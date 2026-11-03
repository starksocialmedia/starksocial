# Live fix — Brand Strategy & Identity (page 1427) process + FAQ

**Status: prepared and dry-run tested on live (no changes written), NOT applied. Needs Nathan's copy.** Live home dir is read-only; uploads go to `~/tmp/`.

Bug (confirmed November 2 2026): on starksocial.com/services/brand-strategy-identity/
the "How We Work" steps are the SEO page's (Discovery & strategy / Structure &
optimization / Measurement & refinement) and the FAQ is the Web Design page's
("Do you write the website content?", "How long does a typical website project take?"…).

Cause: the page was built by duplicating the SEO and Web Design Cornerstone pages.
The steps and questions live in Cornerstone **looper JSON** inside the
`_cornerstone_data` post meta, not in ACF. The rest of the page (hero, outcomes,
problem, CTA) is brand-specific and correct.

Why it wasn't applied: no brand-specific process or FAQ copy exists anywhere —
no revisions of page 1427, nothing in the repo. Fixing it means publishing new
copy on live, which needs your voice. `proposed.json` is the Phase 2 Local
draft (v27.0.13) in live's format, as a starting point.

## Apply (about 2 minutes)

```
# 1. Edit proposed.json until the copy is right. Live's layout was built for
#    3 steps; the draft has 4. Check the page after, or trim to 3.
# 2. Back up, upload, dry run, apply, purge:
ssh stark-live 'cd ~/public_html && wp --skip-themes post meta get 1427 _cornerstone_data' > ~/Downloads/live-1427-before.json
ssh stark-live 'cat > ~/tmp/brand-1427.json' < tools/live-fixes/brand-strategy-1427/proposed.json
ssh stark-live 'cat > ~/tmp/brand-1427-apply.php' < tools/live-fixes/brand-strategy-1427/apply.php
ssh stark-live 'cd ~/public_html && STARK_JSON=~/tmp/brand-1427.json wp --skip-themes eval-file ~/tmp/brand-1427-apply.php'
ssh stark-live 'cd ~/public_html && STARK_JSON=~/tmp/brand-1427.json wp --skip-themes eval-file ~/tmp/brand-1427-apply.php apply && wp --skip-themes breeze purge --cache=all'
```

Rollback: `wp eval` an `update_post_meta( 1427, '_cornerstone_data', wp_slash( file_get_contents( 'live-1427-before.json' ) ) )`
(not `wp post meta update`, which strips the JSON's backslashes).
