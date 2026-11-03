#!/bin/bash
# Full Local → staging deploy (database, theme, uploads). See README.md.
# Usage: tools/deploy/deploy-staging.sh            (everything)
#        tools/deploy/deploy-staging.sh theme      (theme only + cache flush)
set -euo pipefail

REPO="$(cd "$(dirname "$0")/../.." && pwd)"
LOCAL="$HOME/Local Sites/stark-social-27/app/public"
LOCAL_RUN="$HOME/Library/Application Support/Local"
SOCK="$LOCAL_RUN/run/4MZB1iDjz/mysql/mysqld.sock"
MYSQLDUMP="$LOCAL_RUN/lightning-services/mysql-8.0.35+4/bin/darwin/bin/mysqldump"
HOST=stark
REMOTE='~/public_html'
STAMP="$(date +%Y%m%d-%H%M)"
SSH="ssh -o ConnectTimeout=30 -o ServerAliveInterval=15 -o ServerAliveCountMax=8"
WORK="$(mktemp -d)"

remote() { $SSH "$HOST" "cd $REMOTE && $*"; }

sync_theme() {
    rsync -a --delete --exclude .DS_Store "$REPO/stark-social/" "$LOCAL/wp-content/themes/stark-social/"
    rsync -az --delete --exclude .DS_Store -e "$SSH" "$LOCAL/wp-content/themes/stark-social/" "$HOST:public_html/wp-content/themes/stark-social/"
}

flush() {
    remote "wp cache flush && wp rewrite flush --hard && rm -rf wp-content/cache/breeze*" 2>&1 | grep -v Deprecated || true
}

echo "== Backups"
$SSH "$HOST" "cd $REMOTE && wp --skip-themes --skip-plugins db export ~/tmp/staging-pre-deploy-$STAMP.sql && md5sum ~/tmp/staging-pre-deploy-$STAMP.sql && cd wp-content/themes && tar czf ~/tmp/staging-pre-deploy-theme-$STAMP.tgz stark-social"
$SSH "$HOST" "cat ~/tmp/staging-pre-deploy-$STAMP.sql" > "$HOME/Downloads/staging-pre-deploy-$STAMP.sql"
BACKUP_MD5="$(md5 -q "$HOME/Downloads/staging-pre-deploy-$STAMP.sql")"
REMOTE_BACKUP_MD5="$($SSH "$HOST" "md5sum ~/tmp/staging-pre-deploy-$STAMP.sql" | cut -d' ' -f1)"
[ "$BACKUP_MD5" = "$REMOTE_BACKUP_MD5" ] || { echo "Backup copy mismatch — aborting"; exit 1; }
# The verified copy is in ~/Downloads; don't leave database dumps on the server.
$SSH "$HOST" "rm -f ~/tmp/staging-pre-deploy-$STAMP.sql"

echo "== Theme"
sync_theme

if [ "${1:-}" = "theme" ]; then flush; echo "Theme deployed."; exit 0; fi

echo "== Database"
"$MYSQLDUMP" --socket="$SOCK" -uroot -proot --single-transaction --set-gtid-purged=OFF --default-character-set=utf8mb4 local > "$WORK/local.sql"
LOCAL_MD5="$(md5 -q "$WORK/local.sql")"
REMOTE_MD5="$($SSH "$HOST" "cat > ~/tmp/local-deploy-$STAMP.sql && md5sum ~/tmp/local-deploy-$STAMP.sql" < "$WORK/local.sql" | cut -d' ' -f1)"
[ "$LOCAL_MD5" = "$REMOTE_MD5" ] || { echo "Checksum mismatch — aborting before import"; exit 1; }
remote "wp --skip-themes --skip-plugins db import ~/tmp/local-deploy-$STAMP.sql"
remote "wp --skip-themes --skip-plugins search-replace 'https://starksocial.local' 'https://staging.starksocial.com' --all-tables --skip-columns=guid --report-changed-only"
remote "wp --skip-themes --skip-plugins search-replace '//starksocial.local' '//staging.starksocial.com' --all-tables --skip-columns=guid --report-changed-only"
remote "rm -f ~/tmp/local-deploy-$STAMP.sql"

echo "== Uploads"
# Old backups and plugin working folders never go to a web-reachable uploads dir.
# fw-backup/ alone is 939 MB of 2020 site-backup zips.
UPLOAD_EXCLUDES="--exclude fw-backup/ --exclude wp-migrate-db/ --exclude wp-defender/ --exclude wpo/ --exclude mainwp/ --exclude breeze/ --exclude *.sql --exclude *.zip"
set -f  # keep the globs above literal
for i in 1 2 3 4 5; do
    rsync -az --partial --timeout=120 --bwlimit=5000 --exclude .DS_Store $UPLOAD_EXCLUDES -e "$SSH" "$LOCAL/wp-content/uploads/" "$HOST:public_html/wp-content/uploads/" && break
    echo "rsync attempt $i failed, retrying in 30s"; sleep 30
done

set +f
echo "== Staging guard"
$SSH "$HOST" "mkdir -p $REMOTE/wp-content/mu-plugins && cat > $REMOTE/wp-content/mu-plugins/stark-staging-guard.php" < "$REPO/tools/deploy/mu-plugins/stark-staging-guard.php"

flush
rm -rf "$WORK"
echo "Deployed. Backup: ~/Downloads/staging-pre-deploy-$STAMP.sql — run the smoke test in README.md."
