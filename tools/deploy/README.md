# Deploy — Local → staging

Staging: **https://staging.starksocial.com** (Cloudways, `ssh stark`, WordPress in
`~/public_html`, DB prefix `wp_`, MariaDB 10.11). The same server hosts live
(`ssh stark-live`), so keep transfers bandwidth-limited and never touch live from
here.

First full deploy: **November 2 2026 (v27.1.0)** — backup of the empty staging install: `~/Downloads/staging-pre-phase2-backup.sql` (server copy removed after verification; theme tarball `staging-pre-phase2-theme.tgz` stays in `~/tmp/` on the server). Local's database, theme and
uploads pushed over the empty staging install.

## What a full deploy does

`deploy-staging.sh` runs these steps; each can also be run by hand.

| # | Step | Notes |
|---|---|---|
| 1 | Back up staging DB → `~/tmp/staging-pre-deploy-<date>.sql` on the server, copied to `~/Downloads/`, checksum-verified, then **deleted from the server** | Rollback: upload the `~/Downloads/` copy, then `wp db import` it |
| 2 | Back up staging child theme → `~/tmp/staging-pre-deploy-theme-<date>.tgz` | |
| 3 | `rsync --delete` the child theme from Local | Repo → Local first, so Local = `main` |
| 3b | `rsync` the plugins Phase 2 added: `podlove-podcasting-plugin-for-wordpress`, `podlove-web-player`, `gravityformswebhooks` | Before the DB import, so plugins marked active exist |
| 4 | Dump Local DB with Local's own `mysqldump` (MySQL 8.0.35, via the site socket) | `wp db export` fails on Local without Local's binaries on `PATH`. Dump uses `utf8mb4_unicode_520_ci`, which MariaDB accepts |
| 5 | Stream the dump to staging (`ssh … 'cat > …'`, md5 compared), `wp db import` | scp works on staging, but streaming is the same method as live |
| 6 | `wp search-replace 'https://starksocial.local' 'https://staging.starksocial.com' --all-tables --skip-columns=guid`, then the same for `//starksocial.local` | |
| 7 | `rsync` uploads, `--bwlimit=5000 --partial --timeout=120`, retried. **Excludes** `fw-backup/` (939 MB of 2020 site-backup zips), `wp-migrate-db/`, `wp-defender/`, `wpo/`, `mainwp/`, `breeze/`, `*.sql`, `*.zip` | About 260 MB after exclusions. Backups must never sit in a web-reachable uploads folder. Unthrottled, the first attempt stalled the server ("server not responding") |
| 8 | Install `mu-plugins/stark-staging-guard.php` | See below |
| 9 | `wp cache flush`, `wp rewrite flush --hard`, clear `wp-content/cache/breeze*`, **purge Varnish** (`PURGE` regex to 127.0.0.1:8080) | Cloudways Varnish caches whole pages; Cloudflare caches theme assets for a year, so asset URLs carry a file-time version (v27.3.12) |
| 10 | Smoke test (list below) | |

Settings that come across with the database and must stay that way:
`/%postname%/`, category base `blogs/category`, tag base `blogs/tag`, ACF Pro active
(free ACF inactive), Site Title **Stark Social Media Agency**, tagline **Digital
marketing the right way.**

## Staging guard (`mu-plugins/stark-staging-guard.php`)

Active only when the site's host starts with `staging.` — a no-op anywhere else.

- Blocks **all** outgoing email (`pre_wp_mail`) and Gravity Forms notifications.
  Each blocked email is written to the PHP error log as `[stark-staging-guard]`.
- `noindex, nofollow` on every page (core and Rank Math) and `Disallow: /` in robots.txt.
- `blog_public` is deliberately left at 1, so a staging → live database copy can't
  carry "discourage search engines" to live.

## Smoke test

```
for u in / /services/ /web-design/ /web-design/antelope-valley/ /work/ \
  /portfolio/pathway-escrow/ /blog/ /podcast/ /about/ /contact/ /service-areas/ \
  /404-error-page/ "/?s=test"; do
  printf "%-32s " "$u"; curl -s -m 60 -o /dev/null -w "%{http_code}\n" "https://staging.starksocial.com$u"
done
```

Expected: 200 everywhere, 404 for `/404-error-page/`.

## Rollback

```
ssh stark 'cat > ~/tmp/restore.sql' < ~/Downloads/staging-pre-deploy-<date>.sql
ssh stark 'cd ~/public_html && wp db import ~/tmp/restore.sql && rm ~/tmp/restore.sql'
ssh stark 'cd ~/public_html/wp-content/themes && rm -rf stark-social && tar xzf ~/tmp/staging-pre-deploy-theme-<date>.tgz'
```

Uploads are only ever added (no `--delete`), so they never need rolling back.

## After a full deploy

- **Webhook secret:** save it before the deploy (read staging's feed into a mode-600 temp file) and pass it on stdin afterwards, then delete the file. The imported DB has Local's feed, which is **inactive** (no secret on Local). Re-run the form importer on staging with the secret to activate it:
  ```
  ssh stark 'cat > ~/tmp/form.json' < tools/gravity-forms/stark-contact-form.json
  ssh stark 'cat > ~/tmp/import-form.php' < tools/gravity-forms/import-contact-form.php
  ssh stark "cd ~/public_html && STARK_JSON=~/tmp/form.json STARK_WEBHOOK_SECRET='…' wp eval-file ~/tmp/import-form.php; rm ~/tmp/form.json ~/tmp/import-form.php"
  ```

## Not covered yet

- **Podlove** isn't installed on Local or staging — the podcast RSS feed
  (`/feed/podcast/`) doesn't exist there. Install it before launch.
- **Gravity Forms** contact form ID 1 and the Perfex webhook.
- Theme-only deploys: run step 3 alone, then step 9.

## Deploy log

| Date | Version | Notes |
|---|---|---|
| November 2 2026, 19:56 | v27.1.0 | First push, by hand. Uploads stalled unthrottled and were re-run with exclusions |
| November 2 2026, 21:01 | v27.1.7 | `deploy-staging.sh` full run (first run of the script): exit 0, 54 URL replacements, all 18 smoke-test URLs as expected. Brings staging to Local parity (KB, process, WebP covers, image fixes, Rank Math) |
| November 2 2026, 22:12 | v27.3.x | Full run with the new plugin step (Podlove ×2, GF Webhooks): exit 0, 74 replacements, 26 smoke-test URLs as expected, `/feed/podcast/` has 10 items. Webhook secret applied after deploy. **End-to-end form test: entry 2 → Perfex "Lead created", lead #126** (test lead, delete in Perfex) |
| November 2 2026, 22:42–22:54 | v27.3.17–v27.3.21 | Two full runs (theme-only runs between them): Varnish purge on, `stark-assets/` excluded, Mailgun plugins synced inactive (staging guard force-unloads them). Webhook secret re-applied after each DB push. Final smoke: 13 URLs OK, `wp_mail()` blocked |
| November 3 2026, 09:14 | v27.4.10 | Full run: exit 0, 124 replacements. Webhook secret re-applied from staging's previous feed via stdin (never on a command line); feed 5 active. Smoke test: 24 URLs as expected. Theme-only run at 09:35 for v27.4.11 |
