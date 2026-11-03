# Launch runbook — Phase 2 (Stark Social 27) → starksocial.com

Written November 2 2026 (v27.3.x). Staging (https://staging.starksocial.com) is the
reference copy. Launch means **pushing staging's Phase 2 site onto the live app**
(`rvqkxhngas`), not a DNS move to a new server: both apps are on Cloudways server 676057.
"DNS cutover" below covers the Cloudflare and domain steps either way.

Owner: Nathan. Notify: Deanna before and after. Hosting is Cloudways (us).

---

## 1. Pre-launch checklist

### Content and copy
- [ ] Every service page, plus Home, About and Contact, has **Copy status = Approved** (Dashboard → "Stark — needs attention" lists what's left)
- [ ] Brand Strategy process and FAQ written (`tools/live-fixes/brand-strategy-1427/` has the context)
- [ ] `[NEEDS REVIEW]` markers removed: Content Creation CTA, About story, About CTA ("fractional")
- [ ] `[SAMPLE]` / `[DRAFT]` gone from the location page and service-areas copy, or those pages set to noindex
- [ ] Homepage FAQ approved (or removed)
- [ ] Legal pages reviewed (last updated January 15 2026)
- [ ] Real logo files in place (Nathan)

### Plugins (Phase 2 vs live)

| Plugin | Live | Phase 2 | Action |
|---|---|---|---|
| ACF Pro | ✓ | ✓ | — |
| GeneratePress + GP Premium + GenerateBlocks | — | ✓ | new |
| Rank Math (+ Pro) | — | ✓ | **replaces AIOSEO**. AIOSEO's 14 redirects are in `inc/stark-redirects.php` (11 carried, 3 already broken on live); Rank Math config in `tools/rankmath/` |
| All in One SEO Pro + add-ons | ✓ | — | deactivate at cutover, **after** exporting redirects |
| Gravity Forms + Webhooks | ✓ | ✓ | form ID 1 rebuilt (`tools/gravity-forms/`) |
| **Mailgun + GF Mailgun add-on** | ✓ | files present, **inactive** (v27.3.18) | **activate at cutover** and copy their settings from live first (options `mailgun` and `gravityformsaddon_gravityformsmailgun_settings`: API key, domain `mg.starksocial.com`). Live sends form email through Mailgun; without it notifications go through PHP mail. **Never activate on staging**: Mailgun replaces `wp_mail()` and would bypass the staging email block (the staging guard now force-unloads both) |
| Podlove Publisher + Web Player | ✓ | ✓ | migrated (`tools/podlove-migration/`) |
| Complianz Premium | ✓ | ✓ | config copied from live (`tools/complianz/`) |
| BetterDocs (+ Pro), SearchWP (+ BetterDocs) | ✓ | ✓ | — |
| Pretty Links (37 episode short links `/1`–`/37`) | ✓ | ✗ | **handled in theme** (`inc/stark-redirects.php`, v27.3.16): `/N` → episode N. Deactivate the plugin at cutover |
| Wordfence, Simple History, Broken Link Checker, Enable Media Replace, Wicked Folders, Query Monitor | ✓ | ✗ | decide; Wordfence is recommended |
| Breeze (cache) | ✓ | inactive | activate after launch, then purge |
| Object Cache Pro | ✓ | inactive | activate after launch |
| Google Site Kit | ✓ | ✗ | **don't add** (no tracking for now, per Nathan) |
| stark-site-toolkit (core tweaks, custom 404, login styling) | ✓ | ✗ | Phase 2 has its own 404 and login; check its "core tweaks" module before removing |
| WPCode Premium | ✓ (19 snippets) | ✓ (0 snippets) | **Audited v27.3.19**: form anti-spam, comments-off, lowercase filenames and SVG uploads are now theme code (`inc/stark-forms.php`, `inc/stark-site-tweaks.php`); portfolio / nav / breadcrumb snippets are replaced by Phase 2. **Your call:** Classic Editor (live disables the block editor), hiding all admin notices, the media filename and alt-field editors |
| FileBird Pro / FacetWP | partly | ✓ | — |

### Settings (verify on staging, then again after cutover)
- [ ] Permalinks `/%postname%/`, category base `blogs/category`, tag base `blogs/tag`
- [ ] Site Title "Stark Social Media Agency", tagline "Digital marketing the right way."
- [ ] Reading: front page = Home, posts page = Blog. **`blog_public` = 1 ("discourage search engines" OFF)**
- [ ] Rank Math: `wp eval-file tools/rankmath/configure.php` **after** the URL switch
- [ ] Complianz banner showing; legal page links point at `/support/…`
- [ ] Contact form: webhook feed active with the secret (see `tools/gravity-forms/README.md`)
- [ ] `wp-content/uploads/stark-assets/` — generated per environment; delete it on live after the push (it regenerates on first page view)
- [ ] `wp-content/mu-plugins/stark-staging-guard.php` — present on staging only; does nothing on live, but delete it after the push anyway
- [ ] `WP_DEBUG_DISPLAY` false on live; HSTS on (theme sends it on production HTTPS)

---

## 2. Final content sync (live → Phase 2), the day of launch

Live keeps changing until cutover. Re-sync, in this order:

1. **Blog posts**: `tools/blog-snapshot/README.md` (new posts since November 2 2026), then `wp eval-file tools/blog-snapshot/fix-inline-images.php apply`
2. **Podcast**: only if episodes were added (`tools/podlove-migration/README.md`)
3. **Knowledgebase**: `tools/kb-import/README.md`
4. **Contact form entries**: export live's GF entries (Forms → Import/Export) and keep the CSV; Phase 2 doesn't need them imported
5. **Media**: rsync any uploads added on live since the snapshot
6. **Redirects**: check for new AIOSEO redirects or Pretty Links added after November 2 2026, and add them to `inc/stark-redirects.php`

Freeze edits on live once the sync starts.

---

## 3. Cutover procedure

1. **Backups**: Cloudways on-demand backup of both apps, plus `wp db export` of live to `~/Downloads/live-pre-phase2-<date>.sql` (md5 check)
2. **Save live's Mailgun settings** (`wp option get mailgun --format=json`, `wp option get gravityformsaddon_gravityformsmailgun_settings --format=json`). The staging push replaces live's database
3. **Put live in maintenance**, so only the final sync runs (5–10 minutes)
4. **Push staging → live** with Cloudways "Push to live" (Database + Files), or the same rsync and DB steps as `tools/deploy/deploy-staging.sh` with the hosts reversed. Afterwards:
   `wp search-replace 'https://staging.starksocial.com' 'https://starksocial.com' --all-tables --skip-columns=guid`
5. **Remove the staging guard**: `wp-content/mu-plugins/stark-staging-guard.php`
6. **Plugins**: restore the Mailgun settings saved in step 2, activate Mailgun (+ GF add-on), Breeze, Object Cache Pro; deactivate AIOSEO and Pretty Links (both replaced by `inc/stark-redirects.php`)
7. **Rebuild caches and settings**: `wp eval-file tools/rankmath/configure.php`, `wp rewrite flush --hard`, `wp cache flush`, purge Breeze and **purge Cloudflare**
8. **Olark goes with the old theme**: live loads `static.olark.com/jsclient/loader.js`, which isn't in any WPCode snippet, so it comes from the Phase 1 theme / Cornerstone global JS. Phase 2 doesn't include it. Confirm after cutover that no request goes to olark.com
9. **DNS / Cloudflare**: no change if staging was pushed into the live app. If the app changes, point `starksocial.com` A/AAAA (proxied) to the new app IP, keep `www` → apex, and leave `podcast.`, `hub.` and `client.` untouched
10. Take the site out of maintenance

---

## 4. Post-cutover verification (first 30 minutes)

```
for u in / /services/ /web-design/ /seo/ /social-media-management/ /paid-advertising/ \
  /content-creation/ /brand-strategy-identity/ /audit-consulting/ /about/ /contact/ \
  /work/ /portfolio/pathway-escrow/ /blog/ /podcast/ /feed/podcast/ /support/ \
  /support/knowledgebase/ /support/privacy-policy/ /sitemap_index.xml /robots.txt; do
  printf "%-36s " "$u"; curl -s -o /dev/null -w "%{http_code}\n" "https://starksocial.com$u"; done
```

- [ ] All 200 (`/404-error-page/` 404). `robots.txt` is the production one (not `Disallow: /`)
- [ ] **Short links:** `/37` → episode 37, `/1` → episode 1. **Old blog slugs:** `/content-is-king` → its post
- [ ] **Old URLs 301:** `/services/web-design/` → `/web-design/` (all 6), `/about/nathan-imhoff/` → `/author/nateimhoff/`, `/about/deanna-l-miller/` → `/author/deannaleigh/`, `/password-generator/` → `/support/password-generator/`
- [ ] **Podcast feed:** `/feed/podcast/` has the same items and GUIDs as before. Validate in Podcasts Connect or castfeedvalidator.com
- [ ] **Contact form:** submit one real test → `/thank-you/`, entry note "Webhook → HTTP 200", lead in Perfex, **email arrives** at nathan@ and deanna@ (Mailgun)
- [ ] Perfex chat appears (US opt-out: visible until a visitor opts out)
- [ ] Rank Math: page titles, `sitemap_index.xml`, schema on home (ProfessionalService) and an episode (PodcastEpisode)
- [ ] Google Search Console: submit the sitemap; watch Coverage for 404s over the first week
- [ ] Lighthouse on the homepage and a service page (staging baseline in `tools/audits/`)
- [ ] Cancel the Olark subscription once Perfex chat is confirmed

---

## 5. Rollback

If anything is broken that can't be fixed in 15 minutes:

1. Maintenance mode on
2. Restore the Cloudways backup from step 1 of the cutover (or `wp db import ~/Downloads/live-pre-phase2-<date>.sql` and restore `wp-content` from the backup)
3. Reactivate AIOSEO, then purge Breeze and Cloudflare
4. Check `/`, `/contact/` (form → Perfex) and `/feed/podcast/`
5. Maintenance off; tell Deanna; log what broke in `ERRORLOG.md`

The Phase 1 theme (Pro / Cornerstone) and its data stay in the backup; nothing in this
runbook deletes them from the server.

---

## 6. Who to notify

| When | Who | What |
|---|---|---|
| Day before | Deanna | Content freeze window, launch time |
| Launch | Deanna | "Live", plus anything to spot-check (her bio, KB) |
| Launch +1 day | Clients (optional) | Only if the client portal or dashboard links changed (they didn't) |
| If rollback | Deanna | What happened, next attempt |
