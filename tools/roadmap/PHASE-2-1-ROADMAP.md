# Phase 2.1 roadmap — after launch

Everything deliberately deferred from Phase 2, plus known tech debt. As of v27.4.x
(November 3 2026). Phase 3 (hosting brand, Brand Sheet) stays in `BUILDPLAN.md`.

## Content

- **Case study depth** (needs Nathan to source): 3 now (Pathway Escrow, Schlickart, Valencia draft). Live has no others. Target 6–8, with real results and approved testimonials. The 2015 award post names sites built in 2014–2015 (Knight, Wilk, SCV Fixit…) as candidates. Set each one's **Market** field (v27.4.9) so it shows on `/service-areas/`; Paid Advertising, Social, Content Creation and Audit & Consulting show no case studies until one lists those services
- **Testimonials collection flow:** none exist on live. Proposed: a short Gravity Forms form (quote, name, title, company, service, permission checkbox) sent to happy clients after a project or a quarterly review. Entries land in Perfex like the contact form; approved ones go into `inc/stark-testimonials.php` (or a CPT once there are more than ~10). Slots: homepage proof row, service pages, case studies
- **Client logos / award badges:** `img/clients/`, `img/awards/` (empty; READMEs explain)
- **Location pages:** 1 sample (`/web-design/antelope-valley/`). Build the nested `/{service}/{area}/` map from `SEO-STRATEGY.md` once copy exists per market. The Service Areas hub (v27.4.9) links each one automatically when it's published
- **Service Areas copy:** the hub's intro and 5 market descriptions are `[DRAFT]` placeholders
- **Audit & Consulting** page copy (no live equivalent)
- **Homepage hero**: decide between the Phase 2 hero and live's "Elevate Your Brand. Build Something That Lasts."
- ~~Old animated GIFs~~ done v27.4.1 (MP4). Run `tools/images/convert-gifs.sh` after uploading new large GIFs; delete the GIF originals a year after launch if nothing links to them (frees 29.6 MB)
- 2015–2018 posts on dead platforms: review with Search Console data and prune or redirect

- **Deanna content pipeline:** a repeatable path from idea to published post: Perfex task or a shared doc → draft in WordPress (author Deanna, one category, featured image ~1600px) → Nathan review → schedule. Rank Math focus keyword per post. Live already runs 3 scheduled posts ahead; `CONTENT-GUIDE.md` covers the editor side. Missing: an editorial calendar view (PublishPress Planner or a Perfex project), and a "ready for review" status (`pending` + the dashboard widget already lists drafts)
- **Search Console keywords** → fill `SEO-STRATEGY.md`'s keyword table (TBD), then replace the v27.4.2 focus keywords
- **Search suggestions** on the empty search page are hand-picked topics; replace with real popular searches once there's data (Rank Math or Search Console)
- **Topic matching** (`inc/stark-topics.php`) drives related services, "From the blog" and in-text links. Tune the phrase lists as posts are added

## Product / features

- **Site auditor tool.** No spec in the repo (Nathan's earlier spec lives outside it). Suggested shape: a `/tools/site-audit/` page next to the password generator. Visitors enter a URL; a server-side check (Lighthouse API or PageSpeed Insights, plus headers, SSL, meta and schema) feeds into the GF → Perfex lead flow. Spec first, then build
- **Shared design tokens with brands.starksocial.com** (Brand Sheet, BUILDPLAN Phase 3.3). Move the `:root` tokens in `custom.css` (sections 1 and 14) into a standalone `tokens.css` that both sites load or build from. Same names: `--stark-navy`, `--stark-light-blue`, `--stark-accent-dark`, `--stark-blue-ink` …
- ~~Knowledgebase landmarks~~ fixed v27.4.0. Still open: a fully Stark-styled BetterDocs template (the plugin's layout and sidebar remain)
- **Process section visuals:** the numbered process steps on service pages and Home are text-only. Commission or draw a small icon / illustration set (one per step type: discover, plan, build, launch, refine) in the brand line style; the step data already has an `icon` slot in the included-items pattern to copy
- **Perfex chat:** widget is disabled on the hub (no API key). Turn on, then apply `tools/perfex/cookie-fix-notes.md` (SameSite=None; Secure; Partitioned)
- **Safari pinned-tab icon:** needs a one-colour SVG of the logo (only a raster exists). Low priority: Safari 12+ uses the favicon
- **Podlove Web Player theme:** match the player colours and fonts to Phase 2 (it's Podlove's default blue)
- **Visual breadcrumbs on BetterDocs** pages (they keep BetterDocs' own)
- **Contact form:** Perfex fields for `company`, `city` and the source fields need checking on the `lead.php` side; add reCAPTCHA or Turnstile if spam shows up (Complianz already lists reCAPTCHA)
- **Cookie consent:** decide opt-in vs opt-out (currently US opt-out, as on live). Turn on Complianz's cookie blocker (live has it off) once the services list is verified

## Ops / security

- **Redis on live** drops connections (most of the 414 MB debug log): fix before turning on Object Cache Pro
- **Login protection:** live returns 403 for `/wp-login.php`; add Wordfence or Cloudways bot protection on Phase 2
- **MainWP child:** reconnect if Nathan manages sites from MainWP
- **Hub origin certificate** (expired November 2 2026): renew in Cloudways
- `WP_DEBUG` is off on live; `debug.log` backups in `~/private_html/debug-log-backups/` (delete after a month)

## Performance

- Critical-CSS split for `custom.css` (120 KB) with a build step (`critical`), only if staging scores drop. Currently desktop 100 / mobile 97–98 on Local
- Minify `custom.css` in a build step; gzip already covers most of it
- Revisit fonts: 4 Barlow weights preloaded (400 / 500 / 700 / 800)
- Breeze + Object Cache Pro on after launch; re-measure on live

## Tech debt

- `custom.css` is still one file for every template (sections and TOC since v27.1.6; `tools/audits/style-fingerprint.mjs` makes refactors safe)
- `passgen/passgen.css` uses `!important` everywhere (shared with Phase 1); rewrite once Phase 1 is retired
- The theme registers the `podcast` post type and Podlove registers it too. Fine today; drop the theme's registration once Podlove is permanent
- Hard-coded copy in templates (homepage sections, hub, support, thank-you, 404) → fields, if Deanna needs to edit them without code
- Menus: the header uses the fallback hard-coded nav (no WordPress menu assigned). Assign a real menu if editors need to change nav items
- Redirects live in theme code (`inc/stark-redirects.php` + 3 smaller maps). Optional: import `tools/launch/redirects.csv` into Rank Math Redirections so editors can manage them (`inc/stark-authors.php`'s map uses its own filter, `stark_legacy_profile_redirects`, since v27.4.11)
- Phase 1 leftovers to remove after launch: WPCode snippets ported to `functions.php`, stark-site-toolkit, the Pro theme
