# Live site inventory for cutover (starksocial.com, Phase 1)

Taken November 3 2026, read-only (WP-CLI on the live app, public DNS, TLS handshakes).
Every live URL was requested on Phase 2 (Local, following redirects).
Raw data: `live-inventory/live.json`, per-URL table `live-inventory/live-urls.csv`, media
`live-inventory/live-media.csv`.

**Status key:** **already migrated** (same URL works on Phase 2) · **redirects** (Phase 2
301s it to the new URL) · **will migrate** (moves at the cutover sync) · **will break**
(needs action) · **n/a**

## Summary

| Item | Live | Status |
|---|---|---|
| Pages | 22 published, 1 draft | 12 already migrated · 8 redirect (6 `/services/x/` → `/x/`, 2 team pages → author pages) · **2 will break** (`/stark-404/`, `/search-results/`, now in the redirect map, E2) · draft "Portfolio" (1752) n/a |
| Blog posts | 69 published, **3 scheduled** | 69 already migrated, same URLs. The 3 scheduled posts are in Phase 2 with the same dates (Nov 15, Dec 1, Dec 27 2026) |
| Podcast episodes | 37 | already migrated (`/podcast/{slug}/`, feed `/feed/podcast/` identical, v27.3.1) |
| Case studies (`work`) | 2 | already migrated (`/portfolio/{slug}/` unchanged) |
| Knowledgebase (`docs`) | 27 | already migrated |
| Media library | 255 attachments | **all 255 files present** in Phase 2 uploads. Media added on live after Nov 2 → cutover sync step 5 |
| Users | 3: `starksocialmedia` (administrator, 10 posts), `nateimhoff` (editor, 17), `deannaleigh` (editor, 42) | already migrated (same logins; Phase 2 authors map by login) |
| Comments | 0 approved, 0 pending, 0 spam | n/a (comments are off on both) |
| Scheduled posts | 3 (above) | already migrated. **If launch is after Nov 15**, check the first one published on Phase 2 too (WP cron runs on both) |
| Categories / tags | 9 / 43 | already migrated (bases `blogs/category`, `blogs/tag` kept) |
| Settings | permalinks `/%postname%/`, `blog_public` 1, WP 7.1.4, PHP 8.4.27 | same on Phase 2 |

## Plugins active on live (35)

| Plugin | Cutover |
|---|---|
| ACF Pro, Gravity Forms (+ Webhooks), Complianz Premium, BetterDocs (+ Pro), SearchWP BetterDocs, FacetWP, Podlove Publisher + Web Player | **already migrated** (active on Phase 2) |
| Mailgun, Gravity Forms Mailgun | **will migrate**: files on Phase 2, inactive. Save live's settings before the push, activate after (runbook). **Critical:** `starksocial.com` DMARC is `p=reject` with strict alignment, so form email sent without Mailgun will be rejected |
| Breeze, Object Cache Pro | will migrate: activate after launch. **Redis on live drops connections** (most of the 414 MB debug log, v27.4.0 maintenance note): check before relying on Object Cache Pro |
| All in One SEO Pro + EEAT, Image SEO, IndexNow, Local Business | **will break by design**: replaced by Rank Math. Meta and keyphrases moved (v27.4.2), redirects in the theme (v27.3.16) |
| Pretty Links | replaced: `/1`–`/37` in the theme (v27.3.16) |
| SearchWP (+ Exclude UI, Live Ajax Search) | not on Phase 2. Phase 2 uses WordPress search with facets (v27.4.8); SearchWP BetterDocs stays for KB search. Fine to drop |
| Wordfence | **decide**: recommended on Phase 2 after launch |
| MainWP Child + Child Reports | **decide**: if Nathan manages the site from MainWP, install on Phase 2 after launch and reconnect |
| Simple History, Broken Link Checker, Enable Media Replace, Wicked Folders Pro, Query Monitor, Gravity Forms CLI | optional; not needed for the site to work |
| Google Site Kit | not migrated (no tracking for now, per Nathan) |
| stark-site-toolkit | not migrated: Phase 2 has its own 404, login styling and tweaks |
| WPCode Premium | active on Phase 2 with 0 snippets (see below) |

## WPCode snippets on live (19: 18 active, 1 draft)

| Snippet | Status |
|---|---|
| 816 Lowercase upload filenames · 829 SVG support · 831 Disable comments · 1807 Form validation | **already migrated** to theme code (v27.3.19) |
| 1734 Work CPT · 1735 Taxonomies · 1763 Term list · 1764 Portfolio breadcrumbs · 1766 Results shortcode · 1767 Portfolio nav | replaced by Phase 2's portfolio |
| 827 Copyright shortcode · 828 Reading time shortcode | replaced by templates |
| 1920 / 1924 Blog Player | not used in post content (checked v27.3.19) |
| **852 Disable Gutenberg (Classic Editor) · 908 Admin Tweaks (hides admin notices) · 830 Edit Media Filename · 855 Media Library Alt Fields** | **not ported: Nathan's call** (editorial preferences) |
| 1948 Blog Pagination Fix (draft) | n/a |

## Domain, DNS, SSL, Cloudflare

| Item | Live now | Cutover |
|---|---|---|
| Nameservers | Cloudflare (`brad`, `wanda`) | no change |
| `starksocial.com` | A `162.159.136.54`, `162.159.137.54` (+ AAAA). **Served through Cloudways' Cloudflare Enterprise add-on** (`www` CNAME → `secure.cloudways.cloud`) | no change if staging is pushed into the live app (runbook). **The "caching level" fix is in Cloudways → Application → Cloudflare, not (only) in Nathan's Cloudflare dashboard** |
| `www` | CNAME `secure.cloudways.cloud` | no change |
| `hub`, `podcast`, `brands`, `staging` | proxied by Nathan's Cloudflare zone | untouched. `client` → clientseoreport.com, `updates` → origin directly |
| SSL, apex and www | Google Trust Services (Cloudflare edge), valid to **Dec 31 2026**, auto-renewing | no action |
| SSL, hub origin | Let's Encrypt for `updates.starksocial.com`, **expired Nov 2 2026** (visitors still see Cloudflare's valid cert) | renew in Cloudways (see `tools/perfex/cookie-fix-notes.md`) |
| HSTS | `max-age=31536000; includeSubDomains` | Phase 2 sends the same on production |
| Cache headers | HTML `cf-cache-status: DYNAMIC` + Varnish `x-cache: HIT`; static files `max-age=31536000`, query strings ignored | Phase 2's hashed asset filenames handle it (v27.3.13). Purge Cloudflare + Varnish after the push |
| Email | Google Workspace MX; SPF includes Google + Mailgun; **DMARC `p=reject`, strict**; Mailgun domain `mg.starksocial.com` (MX + SPF present) | no DNS change. Mailgun must be active on Phase 2 (above) |

Not checked: Cloudflare dashboard settings (page rules, WAF, caching level). There's no
Cloudflare or Cloudways API access from here. Nathan checks them in the Cloudflare and
Cloudways dashboards.

## Will break, and the fix

| What | Fix |
|---|---|
| `/stark-404/`, `/search-results/` (Phase 1 utility pages) | 301 in the redirect map (E2, v27.4.10) |
| AIOSEO-only features (IndexNow, image SEO) | Rank Math covers sitemaps. IndexNow is optional (Rank Math has an Instant Indexing module) |
| Pages and posts edited on live after Nov 2 | cutover sync (runbook §2) |
