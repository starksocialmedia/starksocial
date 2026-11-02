# Blog snapshot — live → Local

Pulls blog posts from live into a Phase 2 environment. Live keeps publishing;
this is a snapshot. First run: November 2 2026 (72 posts: 69 published, 3 scheduled).
Re-run for the final sync at launch cutover.

## Steps

```bash
# 1. Export on live. --skip-themes is required: Pro/Cornerstone hooks the
#    exporter and fatals on wxr_cdata() under WP-CLI.
ssh stark-live 'cd ~/public_html && wp export --dir=/tmp --post_type=post --with_attachments=false --skip-themes'

# 2. Copy down. scp/SFTP is jailed to the app dir on Cloudways and can't see
#    /tmp — stream over ssh instead, compare checksums, then delete on live.
ssh stark-live 'cat /tmp/<file>.xml' > ~/Downloads/<file>.xml
ssh stark-live 'md5sum /tmp/<file>.xml'; md5 ~/Downloads/<file>.xml
ssh stark-live 'rm -f /tmp/<file>.xml'

# 3. Target site: back up DB, install importer.
wp plugin install wordpress-importer --activate

# 4. Register attachments against files already in uploads/ (keeps live IDs,
#    no re-download, no -1 duplicates). Then import posts without attachments.
STARK_WXR=~/Downloads/<file>.xml wp eval-file tools/blog-snapshot/register-attachments.php
wp import ~/Downloads/<file>.xml --authors=authors.csv --skip=attachment

# 5. Strip Pro/X + Cornerstone shortcodes from posts (dry run, then apply).
wp eval-file tools/blog-snapshot/strip-cornerstone.php
wp eval-file tools/blog-snapshot/strip-cornerstone.php apply
```

`authors.csv` maps live logins to target users:

```
old_user_login,new_user_login
starksocialmedia,starksocialmedia
nateimhoff,nathan@starksocial.com
deannaleigh,deannaleigh
```

## Gotchas from the first run

- The export only includes attachments whose parent is an exported post. 8 featured
  images (IDs 62, 174, 447, 448, 452, 454, 455, 500) are attached elsewhere on live and
  had to be registered separately from `wp post get` / `wp post meta get` output.
  Check: every post's `get_post_thumbnail_id()` must resolve to a URL.
- Post 498 (award post) was fully Cornerstone-built. Its text survives as paragraphs, but
  the original heading tags and 3 slider images lived only in Cornerstone data and are gone.
- `got-an-seo-audit-email-read-this-first` contains 7 pasted ChatGPT citation artifacts
  (`:contentReference[oaicite:N]{index=N}`) — visible on live too. Not stripped; fix at source.
