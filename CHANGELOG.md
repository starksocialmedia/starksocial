# CHANGELOG.md — Stark Social Media Agency
**Project:** starksocial.com
**Format:** newest first

## [27.3.14] — November 2 2026

### Added — Polish
- **Site icon** = live's real icon (`cropped-stark-social-media-agency-icon`, live attachment 1590). WordPress prints 32 / 192 favicons, a 270 tile and `/favicon.ico` (302). The **apple-touch icon is a 180px PNG**, since iOS home-screen icons should be PNG (filter in `inc/stark-admin.php`). `tools/content-seed/set-site-icon.php`
- **Footer:** "Sitemap" link → `/sitemap_index.xml` (Rank Math)

### Checked (no change needed)
- Twitter / Open Graph: Rank Math outputs `summary_large_image` with title, description and the 1200×630 default image
- FAQ schema: exactly one FAQPage on every page with an FAQ (home, all service and location pages, password generator), none elsewhere
- robots.txt: production allows all except `/wp-admin/`, `/?s=`, `/search/` (plus sitemap line); staging serves `Disallow: /`
- Icons used in templates are already inline SVG (service icons, social, podcast platforms)

## [Maintenance] — November 2 2026 — Phase 1 live fixes (overnight)

### Fixed on live (typos + broken images only)
- **Main menu typos:** "Social Media Mangement" → "Social Media Management" (label and title attribute, menu item 1094); "All Service" → "All Services" (1106). Breeze purged; homepage has no "Mangement" left (one remains only as an internal Cornerstone layer label, never shown)
- **Broken inline images in 17 live posts:** 12 references to `-400x400` sizes that no longer exist (404 on live) now point at the existing 800×800 or original file; 12 `http://` same-site image URLs → `https://`. Run with `tools/blog-snapshot/fix-inline-images.php` (dry run first; a second dry run shows 0 changes). Original content of the 21 candidate posts saved to `~/Downloads/live-posts-before-inline-image-fix.json`. 2 references can't be resolved (files gone)

### Checked on live, left for Nathan
- Forms → Perfex working (last entry October 10: webhook 200, Mailgun delivered). Knowledgebase search, feeds, CTAs and all homepage assets OK. Olark loads. Comments are closed site-wide (no comments to test)
- **`WP_DEBUG` + `WP_DEBUG_LOG` are on in production; `debug.log` is 414 MB** (not web-readable, 403). Recurring warnings from Mailgun, Podlove and the Phase 1 theme's `functions.php`; 2 out-of-memory fatals on October 2. Recommend `WP_DEBUG` off and rotating the log
- MainWP stream table error (`Unknown column 'blog_id'`) — MainWP child update
- Footer LinkedIn on live is `/company/stark-social`; Phase 2 uses `/company/stark-social-media-agency`. Confirm which is right
- Brand Strategy process + FAQ (still waiting on copy; `tools/live-fixes/brand-strategy-1427/`)

## [27.3.13] — November 2 2026

### Fixed — Staging served an August 2026 `custom.css` (Cloudflare)
- **Found during the staging Lighthouse run:** staging scored accessibility 96 against Local's 100. The page loaded `custom.css?ver=2.0.0`, and Cloudflare returned a **9 KB copy last modified August 4 2026** (`cf-cache-status: HIT`, 1-year max-age); the server file is 127 KB. Cloudflare's caching level **ignores query strings**, so `?ver=` never busts it. **Every staging review so far showed stale CSS**
- `inc/stark-assets.php`: theme CSS and JS are served from content-hashed copies in `uploads/stark-assets/{name}-{hash}.{ext}`, created on the first request after a deploy (old copies removed, relative `url(fonts/…)` rewritten). A changed file gets a new URL, so no cache can serve it stale. `STARK_CHILD_VERSION` is now the newest asset file time instead of a fixed 2.0.0
- Checked with the computed-style fingerprint: 41/42 page-width combinations identical with and without hashed assets (the one difference is cookie-banner timing on the privacy page)
- `tools/deploy/deploy-staging.sh` also **purges Cloudways Varnish** (it held whole pages with the old asset URLs)
- Staging after the fix: `custom.css` 767 rules (was 27), **axe 0 violations** on home, web design, contact, a post and an episode
- **Nathan:** set Cloudflare Caching Level to **Standard** (respects query strings) on starksocial.com; the hashed URLs make it work either way

## [27.3.12] — November 2 2026

### Added — Documentation
- `tools/launch/LAUNCH-RUNBOOK.md` — pre-launch checklist with a **plugin parity table** (found: live sends form email through **Mailgun**, missing on Phase 2; 37 **Pretty Links** to migrate; AIOSEO → Rank Math redirects; stark-site-toolkit), settings, final content sync, cutover, verification, rollback, who to notify. Olark comes from the Phase 1 theme (`static.olark.com`), not a WPCode snippet
- `tools/editorial/CONTENT-GUIDE.md` — editing service pages (every ACF tab), case studies, blog posts, podcast episodes, testimonials, the contact form, the knowledgebase; the Copy status workflow (Draft → Live-sourced → Approved)
- `tools/roadmap/PHASE-2-1-ROADMAP.md` — deferred content, site auditor tool (needs a spec), shared tokens with the Brand Sheet, KB template, performance, tech debt

## [Deploy] — November 2 2026, 22:12 — v27.3.0–v27.3.11 on staging

- `tools/deploy/deploy-staging.sh` now syncs the plugins Phase 2 added (Podlove Publisher, Podlove Web Player, GF Webhooks) before the database import
- Full deploy: exit 0; 26 URLs checked (200, 404 for the 404 test, no PHP warnings); `/feed/podcast/` 10 items; Complianz banner and review banners present. **Staging is now the reference copy for review**
- Webhook feed activated on staging with the live secret (streamed to a temp file, deleted after)
- **End-to-end contact form test on staging:** submitted → `/thank-you/` → webhook HTTP 200, Perfex `{"status":true,"message":"Lead created","lead_id":126}`. **Lead #126 is the test** ("Stark Test Lead (automated, please delete)"). Staging sent no email (staging guard)

## [27.3.11] — November 2 2026

### Added — Admin and editorial experience (`inc/stark-admin.php`)
- **Login page:** navy background, the existing light logo file (no logo swap), branded button and focus colours, logo links to the site
- **Admin footer:** "Stark Social 27 · starksocial.com"
- **"Stark" admin colour scheme** (navy menu, brand-blue current item and buttons). Applied only to users who never picked a scheme; any choice in Profile wins
- **Dashboard widget "Stark — needs attention"**, first on the dashboard (editors and up): 5 latest form submissions with links to the entries, posts / episodes / case studies in draft or pending, and pages whose Copy status isn't Approved, labelled Draft or Live-sourced (service pages with no status count as Draft). No traffic or vanity metrics

## [27.3.8] — November 2 2026

### Changed — Blog category and tag archives
- Category and tag archives already render through `templates/stark-blog-archive.php` (since v27.0.9): hero with the term as H1, filtered grid, category chips, pagination. **No separate `stark-blog-category.php` / `stark-blog-tag.php`** — they would duplicate the archive template
- **URLs stay as on live:** `/blogs/category/{slug}/`, `/blogs/tag/{slug}/` (live's category and tag bases; changing them would break indexed URLs). The `/blog/category/…` and `/blog/tag/…` forms (with `/page/N/`) now **301** to them
- Terms with no description now show "45 articles on Social Media." instead of an empty subhead (none of the 10 categories or 43 tags has a description)
- D2: `/work/` industry and service filters are client-side on the archive (3 case studies; separate filter pages would be thin content). The podcast has no Podlove seasons (0 rows). Nothing else to polish there

## [27.3.7] — November 2 2026

### Changed — Render-blocking trim
- Non-podcast pages went from **7 blocking stylesheets to 3** (GeneratePress, `custom.css`, Complianz banner):
  - `style.css` was enqueued twice (GeneratePress's `generate-child` and the theme's `stark-child`) and contains no rules. Both are dropped; `custom.css` now depends on `generate-style`
  - Podlove's front-end and admin-font CSS, plus the Web Player's `embed.js` / `cache.js` (blocking `<head>` scripts), now load only on podcast pages
- Complianz banner restyled to the Phase 2 tokens (its accept button and links were #307FE2 at 3.99:1)
- **Lighthouse on Local after the change:** home and blog — desktop 100 / mobile 97–98, **accessibility 100**, SEO 100, CLS 0. Summary in `tools/audits/lighthouse-2026-11-02-v27.3.7/`
- **Not done: critical-CSS inline + async `custom.css`.** What's left blocking is about 150 ms on throttled mobile; scores already beat the targets (desktop 95+, mobile 85+); and splitting 120 KB of CSS by hand risks a flash of unstyled content on templates that weren't measured. Revisit with a build step (`critical`) if staging scores drop
- **Fonts:** the preload stays at Barlow 400 / 500 / 700 / 800, not 400 / 600 / 700. 500 is the nav and chip weight; preloading it fixed the 0.28 CLS on `/blog/` (v27.1.1). 800 is the hero H1. 600 is below the fold. All `@font-face` rules already use `font-display: swap`

## [27.3.6] — November 2 2026

### Added — Visible breadcrumbs
- `templates/parts/breadcrumbs.php`: Rank Math's trail directly under the hero (Barlow Condensed 14px, muted ink, `/` separators, "Home" first) on service and location pages, the services hub, portfolio, blog and podcast singles, legal pages, Support and the knowledgebase. BetterDocs articles keep their own breadcrumb. Not on the homepage
- Checked: `/web-design/antelope-valley/` → Home / Web Design / Antelope Valley; posts → Home / Category / Post; case studies → Home / Portfolio / Client

### Fixed
- Location pages showed the parent's Live-sourced banner while their local copy is still `[SAMPLE]`. They now show Draft until approved

## [27.3.5] — November 2 2026

### Changed — Image weight
- **Oversized originals capped** (`tools/images/cap_originals.py`): 14 uploads were stock-photo originals up to 8688px and 9.4 MB. They are now 2560px on the long edge, quality 82, same filename and format: **44.8 MB saved**. Templates already use sized versions, but Rank Math share images, lightbox links and the fallback `full` size served the originals. Originals are backed up outside the repo, and live keeps them. `sync-original-dimensions.php` updates attachment sizes where the file is a registered attachment
- **Podcast single preload** was the full-size SVG cover (up to 1.7 MB) while the page shows the WebP render. It now preloads the WebP `imagesrcset` with `fetchpriority="high"`
- Checked: theme images go through `wp_get_attachment_image()` / `stark_attachment_img()` with width, height and `srcset`; below-the-fold images lazy-load; heroes and the first row are eager. The server serves `.webp` as `image/webp`
- Not changed (punch list): 9 animated GIFs in 2015–2024 posts (2–7 MB each; convert to MP4/WebP when those posts are edited); the SVG episode covers aren't served any more (v27.1.4), so they weren't run through SVGO

## [27.3.4] — November 2 2026

### Changed — Perfex chat behind cookie consent; Complianz configured on Phase 2
- **Found:** Complianz was never configured on Phase 2, so there was no cookie banner. Live's configuration (US opt-out, banner, 374 cookies, 9 services, options and completion flags) was copied to Local read-only; legal page IDs remapped; license key not copied. `tools/complianz/`
- **Perfex PRChat** loads only with `cmplz_has_consent('preferences'|'marketing')` and re-checks on Complianz consent events. It's also registered in `cmplz_known_script_tags` (preferences) and added as a **Complianz service "Perfex Chat"** (Preferences, third party)
- Tested: no Complianz → no chat; opted out → 0 requests to hub.starksocial.com; consent → chat loads. With live's US opt-out setting the chat loads until a visitor opts out
- Chrome's third-party cookie warning still appears when the widget loads. That needs `SameSite=None; Secure; Partitioned` on hub.starksocial.com (punch list)

## [27.3.3] — November 2 2026

### Fixed — Accessibility pass
- **Button contrast:** new `--stark-accent-dark: #2A70CC` (hover `#1F5DAE`). Primary buttons, nav "Client Portal", episode play buttons and the password generator use it, so white text is 4.9:1 (was 3.99:1). `#307FE2` stays for large text, icons and decoration
- **Skip to content** link (hidden until focused) on every Stark template. GeneratePress pages keep their own
- **`aria-current="page"`** on the current nav link (desktop + mobile, fallback menus and the walker), with an underline cue
- Global `:focus-visible` outline for any interactive element without its own (white on dark bands)
- Fixed from the new axe run: "Why Stark" eyebrow on navy, episode transcript hint, legal contact link (small blue text), `/services/` principles heading order (hidden h2), BetterDocs search hint contrast, BetterDocs feedback emoji links (accessible names)
- **axe:** 0 violations on 21 of 22 page/width runs across home, services, 4 service pages, about, contact, blog, a post, portfolio, support, an episode, the KB, legal and thank-you. Remaining: moderate landmark and heading notes inside BetterDocs' own doc template (no `<main>`, two banners). Fixing those needs a Stark template for docs (punch list)
- Form fields all have visible labels (Gravity Forms); icon-only buttons (burger, back-to-top, A11y) have labels

### Added
- `inc/stark-forms.php`: every Perfex webhook call adds a note on the form entry ("Webhook → HTTP 200 …"), so lead delivery can be checked in WordPress

## [27.3.10] — November 2 2026

### Added — Branded email templates (`inc/stark-emails.php`)
- One div-based HTML wrapper (no tables, no `<br>`, per ERR-001; inline styles): navy header, white card, address footer
- WordPress core emails (password reset, new user, comment notification and moderation): plain text becomes HTML in the wrapper
- Gravity Forms notifications on "Stark Social …" forms are wrapped, plus a `{stark_fields}` merge tag (label/value rows, no table) used by the contact form's admin and confirmation emails
- Checked by rendering the captured admin notification

## [27.3.2] — November 2 2026

### Added — Contact form + Perfex webhook (`tools/gravity-forms/`)
- Gravity Form **"Stark Social Contact"**, ID 1: Name, Business Name, Email, Phone, "What are you looking to improve?" (8 options, values are service slugs), city/market, Message, and hidden `source_page_url` / `source_service` / `source_market` / `source_campaign`. `?service=` and `?market=` prefill from the URL
- Notifications to nathan@ + deanna@starksocial.com (same recipients as live), confirmation email to the visitor, redirect to the new **`/thank-you/`** page (`templates/stark-thank-you.php`, noindex)
- **Webhook feed** to `hub.starksocial.com/webhooks/lead.php` with the `X-Webhook-Secret` header. Live's field keys are kept (`name`, `email`, `phonenumber`, `message`, `source`), plus `company`, `city` and the source fields. The secret is passed by env var at import and never committed. Local's feed is inactive
- Webhooks add-on 1.7.0 installed on Local from live's plugins folder
- Tested on Local: submit → `/thank-you/`, both emails delivered to Mailpit

## [27.3.1] — November 2 2026

### Added — Podlove on Phase 2 (launch-critical: podcast RSS)
- Podlove Publisher 4.5.8 + Web Player 5.10.0 on Local, same versions as live
- Live's 10 Podlove data tables (38 episode rows, 294 media files, feeds, templates, seasons, subscribe button) and 28 `podlove%` options copied read-only from live; episode → post IDs remapped by slug (37/37). Download analytics not copied
- `/feed/podcast/` renders on Local with the same 10 items and enclosure URL format as live; episode pages use the Podlove Web Player (with chapters)
- `tools/podlove-migration/` — README with every step, `import-options.php`, `remap-episode-posts.php`

## [27.3.0] — November 2 2026

### Content migration — live copy into Phase 2 (`tools/content-migration/`)
- **17 of 22 live pages fully populated with live content, 2 partial** (Home below the hero; Brand Strategy, whose live process and FAQ are copies of other pages), 3 are templates with no copy to move. See `page-migration-report.md` and `live-vs-phase2-audit.md`
- Service pages: hero headline + lead + tagline, outcomes, problem, what we handle, process, fit lists, extra sections (Paid "First 30 days", SMM "What working together looks like"), FAQ and CTA, all verbatim from live. Card descriptions come from the live `/services/` page. Services hub, Home (hero unchanged), About (story, How we think, founder bios) and Contact also carry live copy
- **New Copy status: Live-sourced** (blue banner, "needs Nathan voice review") on the service and home groups. Brand Strategy stays Draft
- Live copy errors kept verbatim and flagged `[NEEDS REVIEW]` inline: Content Creation CTA (Paid's heading), About story (broken sentence), About CTA ("fractional"). Homepage FAQ labelled `[DRAFT — not on live]`. About's `[SAMPLE]` values removed (live has none)
- Service template: optional sections **outcomes, problem, fit, extra card sections** plus intro and closing-line fields; hero headline and note; CTA note (`acf-json/group_stark_service.json`, `templates/parts/service-{outcomes,problem,fit,extra}.php`). Fit lists reuse the hub's `.stark-fit` component
- Homepage: live "Who We're For", new **The Stark Difference** section, live How We Work steps and CTA
- `inc/stark-testimonials.php` — store for testimonials (none exist on live), awards (2017 Communicator Award, Best of SCV 2015) and press. About shows a **Recognition** section. `img/awards/` and `img/clients/` created (README only: live displays no logos)
- Portfolio: live has only 2 case studies (both migrated in v27.0.12); no others exist, so none were added
- Tools: `extract_live.py` (outline of a rendered Cornerstone page), `build_specs.py` (live → ACF spec, verbatim strings), `import-service-specs.php`, `import-core-pages.php`, `live-outlines/` (source snapshots)
- Local: default "Sample Page" trashed

## [27.1.7] — November 2 2026

### Deployed — staging at Local parity
- Full re-deploy with `tools/deploy/deploy-staging.sh` (its first run; exit 0). Staging now has everything since v27.1.0: homepage process, Support hub + 27 KB docs, WebP podcast covers, repaired inline blog images, Rank Math configuration, Site Title
- Used the full DB push instead of re-running `tools/content-seed/` one script at a time. Staging has no content of its own, so a fresh copy of Local is exact parity and needs no ID remapping. The seeds stay documented for a fresh install
- Smoke test: 18 URLs, 200 everywhere except `/404-error-page/` (404); no PHP warnings. Staging guard checked: `wp_mail()` returns false and logs `[stark-staging-guard] blocked email`; `noindex, nofollow` on the homepage

### Cleanup (Priority 10)
- Staging `~/tmp/`: removed `local-phase2.sql`, `staging-pre-phase2-backup.sql` and `staging-pre-deploy-20261102-2101.sql` after checking each against its `~/Downloads/` copy by md5. The two small theme tarballs stay as rollback points
- `deploy-staging.sh` now deletes the server copy of the DB backup once the `~/Downloads/` copy is verified

## [27.1.6] — November 2 2026

### Changed — `custom.css` reorganised (no visual change)
- 15 sections with a table of contents: tokens · fonts + base type · nav · footer · global UI · shared components + homepage · service + location · services hub + draft banner · about + contact · blog, 404 + search · portfolio · podcast + author · support, KB, legal, password generator · global accessibility overrides · reduced motion (last)
- Version-appended blocks folded into their areas: homepage proof and process into the homepage section, footer clearance and mobile tap targets into the footer, typography into the blog, support and KB together, `@font-face` out of the homepage block into fonts
- Split rules merged: `.stark-prose` p, blockquote, code and figcaption (v27.1.4 overrides folded into the originals)
- Dead code: the empty "PASTE WPCode Global CSS HERE" placeholder removed. No exact-duplicate rules found. The 11 classes not found in theme, DB or rendered HTML are all generated at runtime (Perfex chat widget, `--2`/`--3` modifiers, body classes, caption shortcode), so none were removed
- One order dependency found and fixed: the homepage stats row's `margin-top: 0` relied on coming after `.stark-case-study__stats`. It now has the specificity to win wherever it sits
- **Verified with a computed-style fingerprint** of 15,046 elements (21 pages × 390 / 1440): 0 changed (`tools/audits/style-fingerprint.mjs`)
- Size: 117,929 → 120,475 bytes (+2.5 KB of TOC and section headers; the rules are the same)

## [27.1.5] — November 2 2026

### Added — Rank Math configuration (`tools/rankmath/`)
- `configure.php` (idempotent, run per environment): company knowledge graph with Local SEO as **ProfessionalService** (Valencia CA, phone, email, hours Mon–Thu 9–5 / Fri 9–4, 4 social profiles, no street address for a service-area business), default share image (live's 1200×630 homepage image), title templates per post type (Page / Post / Portfolio "Case Study" / Podcast / Docs, separator `|`), breadcrumbs on with `/`, robots.txt (disallow `/wp-admin/`, `/?s=`, `/search/` + sitemap), sitemaps for posts, pages, portfolio, podcast and docs with the 4 legal pages excluded and noindexed in Rank Math too
- Fixed leftovers: the knowledge graph and website name were `staging.starksocial.com` (type Person); hours said 9–5 seven days a week
- `inc/stark-seo.php`: meta description fallbacks from ACF (Lighthouse "meta-description" on every audited page), location page titles ("Web Design in Antelope Valley, CA"), `areaServed` with the 5 markets, pages drop the default Article entity, podcast episodes get **PodcastEpisode** schema (episode number, ISO duration, MP3, series) plus the site graph Rank Math had dropped for that post type
- `/service-areas/` market cards have ids (`#santa-clarita`…) for the schema links
- `tools/rankmath/README.md`: what's set, and the per-page work left (focus keywords, per-service share images, final descriptions, Service schema, logo, visual breadcrumbs)

## [27.1.4] — November 2 2026

### Changed — Polish pass
- **Podcast covers → WebP.** The 35 SVG episode covers (11.4 MB total; 97 KB to 1.7 MB each) are now rendered in headless Chrome to 1280×720 and 640×360 WebP beside the originals (2.1 MB + 0.9 MB total, about 60 KB per card). `stark_attachment_img()` serves them with `srcset` / `sizes` when an attachment has `_stark_webp_sizes`. The SVGs stay as the source files. Tools: `tools/podcast-import/svg2webp.mjs` + `register-webp-covers.php`
- **Password generator buttons** now use the Phase 2 pattern: light-blue fill, white text, 10px radius, Barlow 500 (Copy is the outline variant). `passgen/passgen.css` is shared with Phase 1 and uses `!important`, so the page-scoped overrides do too. Generator re-tested: 20 characters, symbols on, no JS errors
- **Blog typography** (5 posts audited, 2 of them with blockquotes): classic-editor floated images capped at 38% / 280px. 400px floats had squeezed paragraphs to ~290px. Floats are cleared before headings and stack on mobile. Blockquote reads as a pull quote. Monospace inline code, `text-wrap: pretty/balance`, and an external-link ↗ (decorative)
- **Broken inline images in migrated posts** (also broken on live): 18 posts referenced `http://starksocial.com/...` (mixed content), and 12 of 26 image references pointed at a `-400x400` size that no longer exists. `tools/blog-snapshot/fix-inline-images.php` points each at a file that exists on the site (same file, or the 800×800 / original sibling). 24 fixed, 2 unresolved (files gone everywhere). Applied on Local; run on staging and at cutover
- **Mobile audit (390px, 20 page types):** no horizontal scroll except the password generator (tool grid track 419px wide), now fixed. Footer links are 28px+ tap targets on touch screens (were 20px)

## [Maintenance] — November 2 2026 — Phase 1 live bugs

### Fixed on live — Accessibility Statement duplicate id
- The bug was a duplicate **HTML id**, not a duplicate page. The Accessibility Statement is already its own page (ID 1355, `/support/accessibility-statement/`, parent Support), so no page or menu changes were needed. Its main Cornerstone text element carried `id="privacy-policy"`, copied from the Privacy Policy page, so the live site had the privacy page's anchor on the accessibility page
- Changed that one property in `_cornerstone_data` to `id="accessibility-statement"`: guarded single-occurrence replace via `wp eval`, saved with `wp_slash()`. Verified the stored data is byte-identical to the original apart from that id, and the JSON is valid. Breeze cache purged; the public page has 0 `id="privacy-policy"`. Original data saved to `~/Downloads/live-1355-cornerstone-before-a11y-id-fix.json`

### Prepared, not applied — Brand Strategy & Identity process steps + FAQ
- Confirmed: page 1427 shows the SEO page's 3 process steps and the Web Design page's 6 FAQs. These are Cornerstone looper JSON from duplicating those pages (not ACF)
- Not applied because no brand-specific copy exists (no revisions, nothing in the repo), and the fix would publish new copy on live. `tools/live-fixes/brand-strategy-1427/` has a guarded apply script (dry-run tested on live, no changes), `proposed.json` from the Phase 2 draft, and steps. Takes 2 minutes once the copy is approved

## [27.1.2] — November 2 2026

### Added — Support hub `/support/` and knowledgebase
- `templates/stark-support.php` — hero ("Support" / "We’re here when you need us."), then two columns: BetterDocs live search (`[betterdocs_search_form]`, restyled to the Phase 2 field; plain docs search as fallback) and articles grouped by category, 5 per group with an "All … articles" link | contact card (phone, email, hours — same defaults as Contact), **Open a ticket** → Perfex `hub.starksocial.com/clients/open_ticket`, and quick actions (Password Generator, Accessibility Statement, Client Portal, Client Dashboard — portal URLs as on live)
- `/support/knowledgebase/` uses the same template listing every article, with live's intro copy. Live's page is a Cornerstone layout (search + category grid)
- Getting Started category pinned first; others keep BetterDocs order
- **Knowledgebase imported into Local**: 27 docs, 6 categories and BetterDocs settings from live (`tools/kb-import/`). URLs match live: `/docs/{slug}/`, `/docs-category/{slug}/`
- BetterDocs single doc + category pages restyled to tokens: breadcrumb (was a bulleted list), headings, links, hover-only heading anchors
- Pages without a Stark hero (BetterDocs, plain GP pages) now start below the fixed nav. Content was hidden under it

## [27.1.3] — November 2 2026

### Added — Homepage process section [DRAFT - needs Nathan review]
- 4-step flow between Services and Who we work with: **Discover, Design, Build, Grow**, numbered 01–04, one line each. Same partial and look as the service-page process (`templates/parts/service-process.php`): vertical with connector on mobile, 4-across on desktop
- Home page fields (`acf-json/group_stark_home.json`, new Process tab): heading + up to 4 steps. Hidden when empty
- Placeholder copy seeded on Local (`tools/content-seed/seed-home-process.php`). The homepage Copy status is still Draft, so the review banner shows
- Section backgrounds re-alternate when the process is present (services white → process grey → who white → case grey → FAQ white)

## [27.1.1] — November 2 2026

### Fixed — Lighthouse + axe audit quick wins (`tools/audits/README.md`)
- **Small blue text contrast:** brand `#307FE2` is 3.99:1 on white (AA needs 4.5:1). New `--stark-blue-ink: #2A70CC` (4.9:1) for small blue text on light backgrounds: eyebrows, "Learn more", case-study links, categories, prose links, roles, work-card meta, episode numbers. Large numbers, icons and buttons unchanged
- **A11y button** accessible name now includes its visible text ("A11y: …"); the A11y / back-to-top cluster sits in an `<aside>` landmark (`display: contents`, no layout change)
- **Draft banner** is an `<aside>` landmark
- **Blog archive:** visually hidden `h2` between the hero `h1` and the card `h3`s; first row of cards loads eagerly (first image `fetchpriority="high"`), card `sizes` matches the ~420px slot
- **CLS 0.276 → 0 on `/blog/`:** Barlow 500 (nav, category chips) is now preloaded. Its late swap rewrapped the chips and pushed the grid up. `/blog/` desktop performance 86 → 100
- axe: from ~100 failing elements across 4 rules to button contrast only

### Not fixed — logged in BUILDPLAN Phase 2.7
- Primary button contrast (white on `#307FE2`, 3.99:1) — needs a design decision
- Meta descriptions (Rank Math, v27.1.5), cache TTLs (server), unused / render-blocking CSS, Perfex chat cookie warning

## [27.1.0] — November 2 2026 — Phase 2 on staging

### Deployed — Local → https://staging.starksocial.com
- Staging DB backed up first (`~/Downloads/staging-pre-phase2-backup.sql`, md5-verified; theme tarball on the server)
- Child theme rsynced; Local DB dumped with Local's MySQL 8.0 `mysqldump`, streamed (md5-verified) and imported into MariaDB 10.11; `search-replace` `https://starksocial.local` → `https://staging.starksocial.com` (27 replacements, GUIDs skipped)
- Uploads rsynced, throttled. **Excluded** `fw-backup/` (939 MB of 2020 site-backup zips) and other plugin working folders. Backups don't belong in a web-reachable uploads directory
- Came across with the DB and verified: permalinks `/%postname%/`, `blogs/category`, `blogs/tag`; ACF Pro active (free ACF inactive)
- Site Title **Stark Social Media Agency**, tagline **Digital marketing the right way.** (staging and Local)
- Caches flushed, rewrite rules flushed hard

### Added
- `tools/deploy/mu-plugins/stark-staging-guard.php` — installed on staging. Only acts when the host starts with `staging.`: blocks all outgoing email (`pre_wp_mail` + Gravity Forms notifications, each logged), `noindex, nofollow` (core + Rank Math), `Disallow: /` robots.txt. `blog_public` left at 1 on purpose so a staging → live DB copy can't de-index live
- `tools/deploy/deploy-staging.sh` — repeatable full or theme-only deploy (backups, md5 checks, throttled retried rsync)
- `tools/deploy/README.md` — steps, staging guard, smoke test, rollback

### Smoke test
- 200: `/`, `/services/`, `/web-design/`, `/web-design/antelope-valley/`, `/work/`, `/portfolio/pathway-escrow/`, `/blog/`, `/podcast/`, `/about/`, `/contact/`, `/service-areas/`, `/?s=test`, legal, password generator, author page. 404: `/404-error-page/`. No PHP warnings in output. `wp-content/debug.log` returns 403
- Staging is slow on the first uncached hit (5–15 s); several requests timed out while the uploads rsync was running on the shared server

## [27.0.21] — November 2 2026

### Fixed
- Weekly `nate_prune_db_event` cron (v2.0.0) ran `DELETE` / `OPTIMIZE` on Podlove's download-intent tables and `TRUNCATE` on BetterDocs' search log without checking they exist → database errors in the log on any environment without those plugins (seen in staging's debug.log, October 30). Each block now runs only when its tables exist; behavior unchanged where they do. Verified: cron run on Local, no new log lines

## [27.0.20] — November 2 2026

### Added — Homepage polish (hero unchanged)
- **Social proof row** under the hero: 3 stat tiles from the Home page's new fields. Seeded with facts from live copy only — **14** years in business (since 2012), **20+** years of marketing experience, **Winner** Communicator Award for web design. Live has no client / project counts, so "clients served" and "projects shipped" were not invented
- **Featured case study**: Home page field, or the most recent portfolio item automatically (Pathway Escrow), rendered with the service-page teaser partial
- **FAQ**: 8 questions (who you work with, who you'll work with, cost, timeline, process, voice, contracts, fit) with FAQPage schema. **Draft** — Home page Copy status shows the review banner
- Homepage service cards now show service icons
- `acf-json/group_stark_home.json` — Stark — Homepage (location: front page): copy status, stats, featured case study, FAQ
- `inc/stark-services.php` — `stark_portfolio_teaser()` extracted (shared by service pages and homepage)
- `custom.css` — v27.0.20 block

### Added — tools
- `tools/content-seed/` — every Local content seed from v27.0.12–v27.0.20 (service copy, case studies, authors, homepage) + run order for staging

## [Maintenance] — November 2 2026 — live content fix (Phase 1)

### Fixed on live (starksocial.com)
- **Post 1855 `got-an-seo-audit-email-read-this-first`**: removed 7 pasted ChatGPT citation markers (`:contentReference[oaicite:N]{index=N}`) that were visible to readers. Only post on live containing them
- Method: original content saved first; markers removed with a regex that preserves CRLF line endings; diff confirmed 6 lines changed and nothing else; uploaded via ssh, checksum-verified, `wp post update`; stored content verified byte-identical to original minus the markers (a WordPress revision of the previous version exists). Public page re-checked: 200, 0 markers

### Found while there — Local permalinks didn't match live
- Live: `/%postname%/`, category base `blogs/category`, tag base `blogs/tag`. Local was on `/%year%/%monthnum%/%day%/%postname%/` — every blog URL would have changed at launch. **Local switched to match live**; all Phase 2 URLs re-checked (200). Staging must use the same settings
- Password generator page link to the passwords post updated to the `/%postname%/` URL

## [27.0.19] — November 2 2026

### Fixed — Fixed A11y / back-to-top buttons covering footer content
- Measured in headless Chrome at full scroll: the bottom-left cluster (back-to-top + A11y, 44px, ~14px off the viewport bottom) covered the **Privacy Policy link on mobile (390px — tap-target collision)** and the copyright line on desktop
- `.stark-footer { padding-bottom: 96px }` so the cluster floats over empty space at the end of every page. Re-checked 390 / 768 / 1440 widths on home, a blog post, `/blog/` and `/contact/`: no overlaps
- Mid-page, the cluster overlays content while scrolling (expected for fixed UI); content always scrolls clear of it

## [27.0.18] — November 2 2026

### Added — Password generator page
- `templates/stark-password-generator.php` at **`/support/password-generator/`** (live's URL): hero, tool card overlapping the hero with `[password_generator]` (passgen/ from v2.0.0, unchanged), "Good habits" tips aside linking the passwords blog post, 7 FAQs (FAQ partial → FAQPage schema). Copy from the live page
- Verified in headless Chrome: Generate produces a 20-character password with symbols, no JS errors; passgen CSS/JS still enqueue by slug
- Phase 2 layout for the passgen markup scoped to `.stark-passgen-page__card` (length row, toggle grid, full-width generate). Buttons keep Phase 1's pill style — `passgen.css` sets it with `!important`
- `inc/stark-pages.php` — registry entries can name a `parent` slug (child pages); Local page 1997 under Support

### Added — Legacy redirects
- 301s when nothing else answers: `/about/nathan-imhoff/` → `/author/nateimhoff/`, `/about/deanna-l-miller/` (and `/about/deanna-miller/`) → `/author/deannaleigh/`, `/password-generator/` → `/support/password-generator/` (`stark_legacy_redirects` filter)

### Changed
- Footer: Password Generator link → `/support/password-generator/`

## [27.0.17] — November 2 2026

### Added — Author pages
- `templates/stark-author.php` at `/author/{nicename}/`: hero (profile photo or initials, name, role, highlight chips, 2-paragraph bio, "Work with {first}" + social links), stat row (articles published, podcast episodes, years at Stark), latest 3 posts, latest 3 episodes, CTA. `/author/{nicename}/?view=posts` keeps the paginated article list (blog archive template)
- `inc/stark-authors.php` — routing, `stark_author_profile()`, `stark_author_avatar()` (profile photo, else Gravatar)
- `acf-json/group_stark_author.json` — Stark — Author Profile on the user screen: role, highlights, photo, at Stark since, podcast host (counts every episode), LinkedIn, Instagram. Bio = core Biographical Info
- Blog author box: name links to the profile, shows role, bio trimmed to ~32 words, "View profile and posts →"
- `custom.css` — v27.0.17 block

### Content (Local)
- Nathan (user 1) and Deanna (user 3): bios, roles, highlights from live's `/about/nathan-imhoff/` and `/about/deanna-l-miller/` pages (real copy); headshots registered from existing uploads (463, 269); since 2012 (co-founded per Deanna's live bio); both podcast hosts. Display names → "Nathan Imhoff", "Deanna L. Miller", "Stark Social Media Agency" (as live). **Nathan's slug `nathan` → `nateimhoff` so author URLs match live**
- About page team cards: real headshots + short bios

## [27.0.16] — November 2 2026

### Added — Podcast templates
- `templates/stark-podcast-archive.php` — show hero (The Social Life Podcast, subscribe links), 3-column episode grid (cover art + play badge, episode number, title, duration · date, excerpt), 12 per page with pagination, CTA
- `templates/stark-podcast-single.php` — hero with cover art, title, Podlove subtitle, meta line via **`[starkpodmeta]`**, player; show notes; collapsible full transcript; sticky "Listen and subscribe" aside; 3 related episodes (same category, then recent); CTA
- `templates/parts/podcast-card.php`, `templates/parts/podcast-subscribe.php`
- `inc/stark-podcast.php` — routing, body classes, `stark_podcast_episode()`, **`stark_podcast_player()`: Podlove Web Player (`[podlove-episode-web-player]`) when the plugin is active, native `<audio>` + Download MP3 otherwise**, `stark_podcast_subscribe_links()` (Apple Podcasts, YouTube, RSS — from live; filterable), `stark_attachment_img()` (renders SVG attachments, which `wp_get_attachment_image()` skips — 35 of 37 covers are SVG)
- `functions.php` — `[starkpodmeta]` accepts `id=""` and falls back to `episode_number` / `episode_duration` post meta when Podlove's tables don't exist
- `custom.css` — v27.0.16 block
- `tools/podcast-import/` — README + meta seed script

### Content (Local)
- 37 episodes imported from live (Podlove-managed `podcast` post type), cover art registered against existing uploads, Cornerstone stripped (6 transcript-PDF buttons kept as links), Podlove number / duration / subtitle / MP3 URL (podcast.starksocial.com) and transcripts (37 .txt files) stored as post meta

### Launch-critical
- **Podlove must be active on the Phase 2 site at launch** — it generates the podcast RSS feed (`/feed/podcast/`) that Apple Podcasts subscribers use

## [27.0.15] — November 2 2026

### Added — Legal pages
- `templates/stark-legal.php` — title-only hero, sticky side nav of all legal pages (current marked), "Questions? Contact us", long-form `.stark-prose` content
- `inc/stark-legal.php` — `stark_get_legal_pages()` registry, routing by slug, `stark-has-hero` body class, **noindex, follow** on all four (RankMath's robots filter; core `wp_robots` only when RankMath is inactive — one robots meta either way)
- Local pages under **`/support/`** — same URLs as live: Privacy Policy (reused WP's privacy page, ID 3), Terms of Service (1994), Cookie Policy (1995), Accessibility Statement (1996), parent Support (1992, default template until the Support hub is built). Content from live's rendered pages (headings intact), "Last updated: January 15, 2026" kept as the first line
- `tools/legal-import/` — extractor + seed script + README for staging
- Footer legal links now point at `/support/privacy-policy/`, `/support/terms-of-service/`, `/support/cookie-policy/` (were `/privacy-policy/` etc., which don't exist on live)

### Fixed
- Templates assigned `$page` in global scope (legal nav loop, services hub), clobbering WordPress's `$page` pagination global → `the_content()` fatal on legal pages. Renamed to `$legal_page` / `$svc_page`

### Found on live (not fixed)
- Live accessibility statement's content block reuses `id="privacy-policy"` (Cornerstone copy-paste)

## [27.0.14] — November 2 2026

### Changed — Barlow site-wide
- `custom.css` v27.0.14 block: `--stark-font` / `--stark-font-condensed` tokens; `body, button, input, select, textarea` use Barlow. Nav, footer, forms and GP-rendered pages now match the Phase 2 templates (verified computed font on nav, footer and body: Barlow; files load)

### Changed — Logos outlined
- `img/stark-logo.svg` and `img/stark-logo-light.svg`: text converted to paths with fontTools from the theme's own `fonts/barlow-v13-latin-700.woff2` and `barlow-condensed-v13-latin-500.woff2`. Same viewBox (320×48), baselines, sizes, tracking and fills — they now render in Barlow as `<img>` instead of falling back to system fonts. ~11 KB each. Interim until Nathan's real wordmark files
- `tools/logo-outline/` — script + README to regenerate

### Fixed
- Draft review banner (v27.0.13) is now pinned with the nav, so no gap appears above the nav on scroll; mobile shows the short label only

## [27.0.13] — November 2 2026

### Added — Services hub
- `templates/stark-services-hub.php` at `/services/` (registered in `inc/stark-pages.php`): hero ("Marketing that earns its keep." — brand guide tagline), **featured service** (Web Design, `stark_featured_service` filter) as a large dark card with its latest case study image, **6 secondary cards** in a 3-column grid (service icon, name, pitch from each page's Card description, Learn more), "who we work best with" fit lists (copied from live `/services/`), CTA
- Service registry gains an `icon` per service; related-service cards now show it

### Added — Service page copy (first draft)
- All 7 service pages created / filled on Local (IDs 1985–1990 + Web Design 22): 3-sentence thesis, card description, 5 included items with icons, 4-step process, 5 FAQs, service-specific CTA heading. Based on live `/services/*` copy (Audit & Consulting is new — no live page; built from the blog's audit/agency-access posts). **All marked `[DRAFT - needs Nathan review]`** via a new **Copy status** field (Draft / Approved): Draft shows a yellow review banner at the top of the page
- Case study teaser now falls back to the **most recent portfolio item tagged with that service** (`stark_service_portfolio_teasers()`): Web Design + SEO → Pathway Escrow, Brand Strategy → Schlickart. Services without a tagged case study hide the section
- `hero_thesis` field: 1–3 sentences (maxlength 200 → 360)

### Added — Legacy URL redirects
- Live serves services at `/services/{slug}/`; Phase 2 moves them to `/{slug}/`. `stark_redirect_legacy_service_urls()` 301s `/services/{slug}/` (and `/services/{slug}/{area}/`) to the new URL when nothing else answers at that path

### Found on live (not fixed — Phase 1)
- Live `/services/brand-strategy-identity/` shows SEO's process steps and Web Design's FAQs (copy-paste leftovers)

## [27.0.12] — November 2 2026

### Added — Portfolio system
- `inc/stark-portfolio.php` — `portfolio` post type (Portfolio → Case Studies). **Archive at `/work/`** (matches the nav), **singles at `/portfolio/{slug}/`** — identical to live's case-study URLs, so no redirects needed. Rewrite rules flush once per `STARK_PORTFOLIO_REWRITE_VERSION` (no manual Permalinks save on staging/live). Archive shows all items ordered by project year (items without a year are kept). `stark_portfolio_data()` normalises a case study for cards, the single and teasers; `stark_latest_portfolio_item()` for homepage/service teasers
- `acf-json/group_stark_portfolio.json` — Stark — Case Study: client, industry, project year, services (checkboxes from the 7-service registry), live URL, card summary, overview / challenge / approach / results narrative, 3 result stats, hero image, gallery repeater (image + caption), testimonial (quote, name, role). Featured image = card image
- `templates/stark-work-archive.php` — hero, industry + service filter chips (`js/stark-work-filter.js`, AND logic, `aria-pressed`, live-region count, empty state + reset; no-JS shows everything), case study cards (image, industry · year, client, summary, 2 stats). First card spans the row on desktop until a filter is applied
- `templates/stark-work-single.php` — hero, large image, project strip (client · industry · year · services · website), prominent stat row (2 or 3 columns), overview / challenge / approach / results, gallery, testimonial pull-quote band, prev / all work / next, CTA
- `custom.css` — v27.0.12 block (`.stark-chip`, work cards, case study layout, gallery, `.stark-quote`)

### Content (Local)
- Imported live's 2 case studies (`work` post type on live → `portfolio`): **Pathway Escrow** (1982) and **Schlickart** (1983) — client, outcome, URL, 3 results, story sections parsed from live's Overview / Challenge / Solution / Results markup, project gallery images (live's `project_gallery`, not `gallery`, which held unrelated team photos)
- Added **Valencia Jazz & Blues** (1984) from the June 2017 award post: Communicator Award win, 6,000+ entries, Barbara Myler testimonial. Narrative sections marked `[DRAFT - needs Nathan review]`
- Inferred, needs confirming: industries (Real Estate & Escrow, Photography, Events & Entertainment), services, years (Pathway 2026 = live publish year; Schlickart 2025 = latest refresh; Valencia 2016)

### Fixed
- Seeded ACF values on the About page (and the new case studies) had been stored as raw post meta (`update_field` by name didn't resolve the field) — rendered fine, but repeaters would look empty in wp-admin. Re-saved by field key

## [27.0.11] — November 2 2026

### Fixed — visual QA pass (headless Chrome, 1440 + 390 widths)
- **`.stark-prose` paragraphs had no spacing.** `.stark-prose p { margin: 0 }` out-specified the `> * + *` rhythm rule — now only zeroes `margin-bottom`. Affected blog posts and the About story
- Contact hero uses the compact hero height (was service-page height, mostly empty)
- Checked: home, service, location, about, contact, blog archive, single, 404, search — no horizontal overflow at 390px

## [27.0.10] — November 2 2026

### Added — 404 + search templates
- `templates/stark-404.php` — real 404 status, dark hero "Lost in the hills?" with a Santa Clarita joke, search form, "Popular trails" chips (Home + popular pages), layered SVG hills at the hero base, 3 recent posts below
- `templates/stark-search.php` — hero with the query and result count plus a search form; results list for all post types (type/category label, title, excerpt, path), pagination; empty state (no matches or empty query) with search tips, popular-page chips and 3 recent posts
- `inc/stark-utility.php` — routing, `stark-has-hero`, `stark_popular_links()` (filterable), `stark_search_form()` (light/default variants, labelled input)
- `custom.css` — v27.0.10 block
- 404 and empty-state copy is placeholder for Copy chat review

## [27.0.9] — November 2 2026

### Added — Blog archive + single templates
- `templates/stark-blog-archive.php` — posts index (`/blog/`) plus category, tag, author and date archives: compact hero (archive-aware heading), category filter chips, 1/2/3-column post grid, numbered pagination, CTA band. 12 posts per page so the 3-column grid fills
- `templates/stark-blog-single.php` — dark hero with category eyebrow, H1, meta row (author · date · reading time), featured image overlapping the hero, `.stark-prose` body, author box, 3 related posts (same category, then recent), CTA band
- `templates/parts/blog-card.php` — post card (featured image, category tag, title, excerpt, date, author), whole card clickable; reused by related posts and search
- `inc/stark-blog.php` — routing via `template_include` (not root `home.php`/`single.php`, so attachment pages and other post types keep their templates), `stark-has-hero` body class, `stark_reading_time()` (225 wpm), `stark_primary_category()` (skips Uncategorized), `stark_related_posts()`, `stark_show_author_box()` (hidden for the `starksocialmedia` house account, matching Phase 1 `author-box.js`; `stark_show_author_box` filter)
- `custom.css` — v27.0.9 block

### Local settings
- Settings → Reading switched to a static front page: Home (ID 1980) on front, Blog (ID 1981) as posts page, so the archive lives at `/blog/`. Homepage still renders `stark-homepage.php`

## [27.0.8] — November 2 2026

### Added — Contact page template
- `templates/stark-contact.php` — hero (eyebrow "Get in touch", H1 "Let's talk about your growth.", intro) → two-column band: form card left, sticky contact details right (stacks on mobile, form first)
- **Form:** `[gravityform id="1" title="false" description="false" ajax="true"]`. ID comes from the page's `gf_form_id` field (default **1**). Until that form exists, a dashed placeholder explains what to configure
- Details: phone (`tel:+1…`), email (antispambot-encoded), mailing address, office hours, social icons. Defaults mirror the live Contact page (P.O. Box 55081 Valencia, (661) 513-3996, hello@, Mon–Thu 9–5 / Fri 9–4) and footer socials; every value is overridable in ACF
- `inc/stark-pages.php` — `contact` added to the page registry; `stark_default_social_links()` + `stark_social_icon()` helpers
- `acf-json/group_stark_contact.json` — Stark — Contact Page (tabs: Hero, Form, Contact Details)
- `custom.css` — v27.0.8 block
- Local: Contact page (ID 1979) created; `[SAMPLE]` intro copy

## [27.0.7] — November 2 2026

### Added — About page template
- `templates/stark-about.php` — hero (eyebrow "About Stark Social", H1, intro) → Our Story (long-form, sticky heading on desktop) → Team (photo or initials placeholder, name, role, bio) → Our Values (3–4 numbered principles) → Where We Work (market chips from `stark_get_markets()`, link to `/service-areas/`) → "Work with us" CTA band. Reuses service hero + CTA partials
- `inc/stark-pages.php` — slug → template registry (`stark_get_page_templates()`, filterable) for single-purpose top-level pages, `template_include` routing, `stark-has-hero` + `stark-page-{slug}` body classes, ACF location rule **Stark page**
- `acf-json/group_stark_about.json` — Stark — About Page (tabs: Hero, Our Story, Team, Values, Where We Work, Final CTA)
- `custom.css` — v27.0.7 block: `.stark-prose` shared long-form type scale (headings, links, lists, blockquote, media, tables), team cards, numbered values, where-we-work layout
- Local: About page (ID 1978) seeded with `[SAMPLE]` copy. Names and roles are from the live About page; all other copy is placeholder

## [Data] — November 2 2026 — Blog snapshot live → Local

### Pulled
- **Snapshot of all 72 blog posts from live into Local** (69 published, 3 scheduled for Nov 15, Dec 1, Dec 27 2026). Live keeps publishing — **final sync happens at launch cutover**
- 90 attachment records registered against media already in Local `uploads/` (82 from the export + 8 featured images attached elsewhere on live). All 72 featured images resolve
- Authors: `deannaleigh` (42 posts) and `starksocialmedia` (10) created as editors; `nateimhoff` (18) mapped to the existing Local admin

### Cleaned
- Pro/X + Cornerstone shortcodes stripped from 5 posts (posts only, pages untouched). Spotify `[cs_component]` buttons converted to plain links; other component embeds removed
- Post 498 (award post) was fully Cornerstone-built — text kept as paragraphs; original headings and 3 slider images were only in Cornerstone data and are lost

### Added
- `tools/blog-snapshot/` — runbook + `register-attachments.php` + `strip-cornerstone.php` for the cutover re-run

### Known issue (live)
- `got-an-seo-audit-email-read-this-first` shows 7 ChatGPT citation artifacts (`:contentReference[oaicite:N]{index=N}`) in the live post body. Fix on live

## [27.0.6] — November 2 2026

### Changed — Footer
- Removed the **Markets** column (5 per-market web-design links). Footer is now 3 link columns: Services · Company · Support
- Added **Service Areas** → `/service-areas/` to the Company column
- `custom.css` — `.stark-footer__cols` 4 → 3 columns

### Added — Service areas hub (stub)
- `templates/stark-service-areas.php` — hub for `/service-areas/`: hero, 5 market cards with brief placeholder context, CTA band. Reuses the service hero + CTA partials and homepage persona cards. Full hub (per-market service links, local proof) comes later
- `inc/stark-service-areas.php` — `stark_get_markets()` registry (5 slugs, filterable via `stark_markets`) — the `{area}` segment of `/{service}/{area}/`; `template_include` routing + `stark-has-hero` body class

### Docs — 7 services
- Fractional CMO removed from START-PROMPT-COPY, -SEO, -BLOG, -BUILD, STYLEGUIDE and INSTRUCTIONS; service counts 8 → 7
- Removed `site-content/pages/fractional-cmo/` (empty paste-here boilerplate, no content)

## [27.0.5] — November 2 2026

### Added — Location page template (service + market)
- `templates/stark-location.php` — renders any page nested under a service page (`/web-design/antelope-valley/`). Builds the parent service context, overlays local fields, then renders through `stark-service.php` so both page types share one set of partials
- `inc/stark-locations.php` — `stark_get_location_parent()`, `stark_location_context()`, `template_include` routing, `stark-has-hero` + `stark-location-page` body classes, custom ACF location rule **Stark location page** (any page whose parent is a service page, optionally per service)
- `acf-json/group_stark_location.json` — Stark — Location Page: market name, H1 override (default "{Service} in {Market}"), thesis + primary CTA overrides, local credibility (heading, body, up to 4 stats, areas served, testimonial), up to 3 local case studies, local FAQ, final CTA heading override
- `templates/parts/service-credibility.php` — local credibility block, rendered after the hero on location pages only
- `custom.css` — v27.0.5 block: credibility grid, stat tiles, area chips, testimonial card

### Behavior
- Local case studies replace the parent's featured case study when present; otherwise the parent's shows
- Local FAQ items are appended after the parent's FAQ (and included in FAQPage schema)
- Related cards link to `/{other-service}/{market}/` when that page exists, else the service page

## [27.0.4] — November 2 2026

### Added — Service page template
- `templates/stark-service.php` — standalone document for top-level pages whose slug is a registered service. Sections: hero → what's included (3–5 icon bullets) → process (4 numbered steps) → featured case study teaser → FAQ accordion → related services (3 cards) → final CTA band. Each section renders only when its fields have content
- `templates/parts/service-*.php` — one partial per section, loaded with `get_template_part()` and the page context as `$args`
- `inc/stark-services.php` — `stark_get_services()` registry (7 service slugs, filterable via `stark_services`), `stark_is_service_page()`, `stark_service_context()`, `stark_related_services()` (editor picks, else next 3 in registry order), `stark_service_icon()` (10 inline stroke icons), `template_include` routing, `stark-has-hero` + `stark-service-page` body classes, custom ACF location rule **Stark service page** (matches by slug), Related picker limited to service pages
- `acf-json/group_stark_service.json` — Stark — Service Page, tabbed: Hero, What's Included, Process, Case Study, FAQ, Related, Final CTA. Registered to the 7 service slugs; ACF auto-syncs from the theme's `acf-json/`
- FAQ outputs `FAQPage` JSON-LD (per SEO-STRATEGY schema plan); `stark_faq_schema` filter turns it off
- `custom.css` — v27.0.4 block: `.stark-page` base, `.stark-band` alternating backgrounds, shorter `.stark-hero--service`, included grid, process flow (vertical connector on mobile, horizontal 4-across at 1024px), case study card, `<details>` FAQ with plus/minus, 3-column related grid

### Requires
- **ACF Pro active** (repeaters). Local was running free ACF — switched to Pro 6.8.0.1. Staging and live need the same

## [27.0.3] — November 2 2026

### Added — Homepage shell
- `templates/stark-homepage.php` — standalone front-page document (outside GP containers): hero, 7-card services preview, who-we're-for 2×2 persona grid, CTA band. All copy is placeholder
- `functions.php` — `stark_body_class_front_hero()` adds `stark-has-hero` on `is_front_page()` (nav transparent on load); `stark_front_page_template()` routes the front page via `template_include`; `stark_inline_svg()` helper inlines theme SVGs so they inherit `currentColor`
- `custom.css` — v27.0.3 block: `--stark-navy-bright` + `--stark-hero-bg` tokens, `.stark-btn` primary/ghost, `.stark-eyebrow`, `.stark-section-head`, section styles, mobile-first grids (1 → 2 at 640px → 4 at 1024px), reduced-motion overrides

### Fixed
- **Barlow never loaded.** Fonts were preloaded but no `@font-face` existed — declared all 9 self-hosted WOFF2 files. Applied to `.stark-home` only for now
- **Nav invisible over dark hero.** v27.0.1 forced navy nav text; hero pages now get white logo/links/burger while unscrolled with no menu open. Logo `<img>` whitened via `filter: invert`
- Footer `margin-top` removed after the homepage so the CTA band meets the footer

### Deferred to Phase 2.1
- Social proof row, case study teaser, process steps, FAQ

## [27.0.2] — November 2 2026

### Added — Stark 27 footer
- `templates/stark-footer.php` — dark navy footer: brand block (light logo, tagline, Instagram/LinkedIn/Facebook icons), four link columns (Services · Markets · Company · Support), bottom bar with copyright, location line, and legal links (Privacy · Terms · Cookie)
- `img/stark-logo-light.svg` — white text wordmark for dark backgrounds
- `custom.css` — footer styles (v27.0.2 block): 4-col grid, stacks at 1100px, 2-col links at 740px; uses existing `--stark-navy`, `--stark-light-blue`, `--stark-ease` tokens
- `functions.php` — `stark_render_footer()` on `wp_footer` (priority 1, ahead of the bottom UI cluster at 5); `stark_remove_generate_footer()` unhooks GP's `generate_construct_footer` and `generate_footer_bar`; `.stark-phase-27 .site-footer` hidden as a backstop

## [27.0.1] — November 2 2026

### Fixed — Nav
- **Default to frosted.** Nav is frosted glass with navy text on every page by default; transparent state is now opt-in via `body.stark-has-hero` (transparent while `html[data-stark-scrolled="false"]`)
- **Logo viewBox.** `img/stark-logo.svg` viewBox 220 → 320 wide and text baseline 26 → 30 so "SOCIAL MEDIA AGENCY" no longer clips; nav logo height 30px

### Note
- Committed together with the v27.0.0 drop in `bdc1f47` — 27.0.0 never had its own commit

## [27.0.0] — November 2 2026

### Added — Stark 27 nav (installed from `stark-27-v27.0.0-nav-drop.zip`)
- `templates/stark-header.php` — fixed header rendered on `wp_body_open`; GP's `generate_construct_header` unhooked
- `inc/class-stark-mega-walker.php` — mega menu walker (title + description per item)
- `js/stark-nav.js` — scroll state (`data-stark-scrolled`), mega menu (`data-stark-mega-open`), mobile accordion (`data-stark-mobile-open`), scroll progress bar, reduced-motion aware
- `img/stark-logo.svg` — text wordmark using `currentColor`
- `functions.php` — nav menu locations, deferred nav script, `stark-phase-27` body class, fallback primary + mobile menus (7 services with descriptions)
- `custom.css` — foundational design tokens (`--stark-navy`, `--stark-light-blue`, `--stark-ink`, `--stark-paper`, `--stark-nav-h`, `--stark-ease`, …), transparent → frosted nav shell, mega menu, mobile accordion, scroll progress

## [2.0.16] — April 30 2026

### Fixed — Brand Sheet PDF
- **Chrome phantom blank page eliminated.** Root cause: `100vh` in print evaluates to screen viewport height in Chrome (10+ inches on most displays), not page height (~7.5 inches landscape letter). Closing element was 10in tall on a 7.5in page → forced a 2nd page that rendered with default `@page` template (the phantom). Fixed by replacing all `100vh` with explicit inch values on print-specific elements.
- **Cover page logo position** — north-shifted in PDF only via print-specific padding override; web cover unchanged
- **Color palette** — all 6 colors fit one PDF page (chip 38pt, body padding 5pt 7pt)
- **Logos section** — wordmarks + icons all fit one PDF page (max-based sizing, contained properly)

### Decided — Brand Sheet print recommendation
- **Print from Chrome.** Firefox renders a phantom trailing page in print; Safari ignores `@page { size: letter landscape }`. Both require browser-specific workarounds we're not pursuing. Chrome is the recommended browser, documented in `brand-sheet/README.md`.

### Architectural changes
- `.bs-closing` rendered as `<body>` sibling instead of inside `<main>` — cleaner pagination isolation
- Print CSS uses `body > *:not(.bs-main):not(.bs-closing) { display: none }` to whitelist only main content + closing in print

## [2.0.8] — April 30 2026

### Added — Brand Guide functional product
- **Logo lockups populated.** Four SVG variants exported from a new `stark-logo-master-EDITABLE.ai` Illustrator file: `stark-wordmark-dark.svg`, `stark-wordmark-light.svg`, `stark-icon.svg`, `stark-web-icon.svg`. All live in `brand-sheet/stark/assets/logos/`. Logos section now renders real artwork, each on a background appropriate to its variant (light cards for the dark wordmark, icon, and web icon; dark blue gradient card for the light wordmark).
- **Cover page redesigned.** Logo is the hero element. Red bar at top of page (was bottom). "BRAND GUIDE" yellow label sits above the logo. Tagline "Marketing that earns its keep" in quiet supporting role beneath. Strict left-aligned column. Top bar transparent over the cover, transitions to frosted glass when scrolled past.
- **Closing page** rendered for PDF only — hidden in web view to avoid dead blue space. Footer copyright text moved inside the closing section so it sits on the gradient, not on a separate white block.
- **Site footer restored** for web view: brand questions email link + "Crafted as part of Stark Care Pro" line.
- **Content max-width** bumped to 1400px throughout.

### Decided
- **"Brand Sheet" → "Brand Guide"** throughout. Matches industry vocabulary, scales for clients of any size, avoids confusion with internal `STYLEGUIDE.md`.
- **"Stewarded" → "Crafted"** for the work, "Maintained by" for the ongoing service. ("Stewarded" felt too clergy. "Crafted" matches the Vance brand book pattern and the agency's tone.)
- **Sophistication tiers.** Stark gets the full 9-section guide (cover, identity, colors, gradients, tokens, typography, logos, voice, closing) — engineering-flavored. Standard clients get a simpler 7-section version (no tokens, no gradients, no Phase 1/Phase 2 mapping). Configurable per-client via the `sections` array in `config.php`.

### Fixed
- PDF print layout properly wired: cover full-bleed page 1, content sections flowing pages 2+, closing full-bleed last page.
- Top bar transparency state machine — over-cover and frosted classes swap on scroll, with 240ms transition.

### Parked for next session
- Logo section needs real polish: bigger displays, clear-space diagrams (.5x rule), minimum-size guidance, do/don't usage examples grid (Vance-style).
- `stark-wordmark-light.svg` is currently the colored wordmark, not actually white. Needs proper white variant.
- `stark-icon.svg` and `stark-web-icon.svg` are 760+ KB each because they have embedded raster inside the SVG. Need to be redrawn as proper vector paths.
- The `stark-logo-master-EDITABLE.ai` file was created in Illustrator but not located on disk for moving into the repo. Find it next session.

## [2.0.6] — April 29 2026

### Decided — Color naming convention (Phase 2)
- Adopted dual-name pattern (evocative + functional) for the six brand colors
- Variable names simplified from descriptive (`--stark-dark-blue`) to role-based (`--stark-anchor`)
- New names locked in:
  - `#004C97` → **Midnight Anchor** / `--stark-anchor` (was Dark Blue / `--stark-dark-blue`)
  - `#F93822` → **Ember Pulse** / `--stark-pulse` (was Red / `--stark-primary-red`)
  - `#307FE2` → **Sky Signal** / `--stark-signal` (was Light Blue / `--stark-primary-light-blue`)
  - `#FEDD00` → **Caution Beacon** / `--stark-beacon` (was Yellow / `--stark-secondary-yellow`)
  - `#00B140` → **Moss Affirm** / `--stark-affirm` (was Green / `--stark-secondary-green`)
  - `#E31C79` → **Pop Spark** / `--stark-spark` (was Magenta / `--stark-secondary-magenta`)
- Functional UI tokens (`--stark-text`, `--stark-muted`, `--stark-rule`, etc.) keep plain names — dual naming earns its keep on brand colors only

### Decided — Variable rename strategy (Option 2)
- Phase 2 child theme adopts new variable names from day one
- Phase 1 production CSS (starksocial.com WPCode + hub.starksocial.com `custom.css`) keeps existing names until Phase 2 cutover
- Brand sheet (`brand-sheet/stark/`) reflects Phase 2 conventions — it's the forward-looking authority
- Mapping table for the transition period documented in STYLEGUIDE.md and rendered in the brand sheet's Tokens section
- No live-site changes during Phase 2 build per existing rule (BUILDPLAN policy)

### Added — Brand Sheet v2.0.6 (Phase 3.3)
- `sections/tokens.php` — full CSS variable reference rendered in the brand sheet
  - Functional UI subsection (text, surface, rule colors with alpha values)
  - Dynamic accent subsection (live accent triplet)
  - Seasonal overrides subsection (all four seasons with `data-stark-season` selectors)
  - Layout subsection (radii and max widths)
  - Phase 1 → Phase 2 legacy mapping table (3 columns: canonical, Phase 1 marketing, Phase 1 Hub)
- Color section redesigned: dual name as headline, role as small label, five copyable values per color (HEX/RGB/CMYK/Pantone/Variable), no more tint/shade family chips
- Tints/shades dropped from active rendering — modern design tools generate algorithmically; original brand standards PDF retained in Drive at `Stark Social Media Agency → Tech → Reference/` for press use

### Modified
- `STYLEGUIDE.md` — major rewrite of Brand Colors section: Phase 2 naming table leads, Pantone/RGB/CMYK reference table, "Why dual names" explainer, Phase 1 → Phase 2 rename mapping, naming policy for functional vs evocative tokens. Component descriptions throughout updated to use new vocabulary (Anchor gradient, Sky Signal accent, Spark CTA, etc.)
- `config.php` — color entries restructured with `name` (full evocative+functional), `role` (legacy descriptive), `variable` (CSS var), full token system added
- `sections/colors.php` — renders dual name + role + variable; family chip rendering removed
- `assets/css/brand-sheet.css` — added styles for `.bs-color__head`, `.bs-color__role`, `.bs-token-grid`, `.bs-token`, `.bs-legacy-table`, with print rules for all new components

### Open for next session
- Logos: drop SVG/PNG variants in `assets/logos/`, populate `'logos' => []` in config
- Voice section: short, draws from STYLEGUIDE
- Hosting buildout: `brand.starksocial.com` apex + dispatcher + custom domain support via CNAME
- Email signature generator: port from Hub when ready
- Cover headline letter-spacing fix (Barlow "a A" glyph collision at 36pt on PDF cover)
- Type scale `.bs-scale` page-break-inside refinement

## [2.0.5] — April 29 2026

### Added — Brand Sheet stark/ scaffold + full data layer (Phase 3.3)
- Initial scaffold at `brand-sheet/stark/` per CHANGELOG 2.0.4 architecture: per-client folder, plain PHP, no DB, `config.php` as single source of truth, `cp -r stark/ {client-slug}/` to onboard a new client
- `index.php` orchestrator, `lib/active-check.php` (Perfex hook stub), `inactive.php` fallback for cancelled Care Pro
- Section files: `identity.php`, `colors.php`, `gradients.php`, `typography.php`, `logos.php` (auto-skips when empty)
- 9 self-hosted Barlow + Barlow Condensed woff2 files in `assets/fonts/`, wired via `@font-face`
- `assets/js/brand-sheet.js` — vanilla click-to-copy on any `[data-copy]` element with Clipboard API + textarea fallback

### Added — Canonical brand data from Stark's official standards PDF
- Pantone (PMS) values for every primary + secondary color
- CMYK replaced with brand-calibrated press values (RGB-converted approximations dropped)
- Tint/shade families: each color now has Tint +1, Tint +2, Shade +1, Shade +2 with full PMS/RGB/CMYK/hex
- Tint/shade family chip rows render beneath each True swatch — hover for full spec, click to copy hex, True chip distinguished by thicker border
- Logo composition gradients (Logo Background, Logo Stark) added as a separate subsection from UI/CSS gradients

### Added — PDF / print polish (landscape brand book)
- `@page { size: letter landscape; margin: 0.5in 0.55in 0.6in 0.55in; }` — PDFs default to landscape
- Running headers/footers via CSS Paged Media: "Stark Social Media Agency · Brand Sheet" top-left, page number top-right, "starksocial.com" bottom-right (Chrome-supported; Safari falls back gracefully)
- `@page :first` suppresses running headers on the cover page
- Cover-page treatment for `#identity` in print: 36pt headline, accent-blue tagline, body copy sized for page, `page-break-after: always`
- `print-color-adjust: exact` forces browsers to render color backgrounds (default behavior strips them)
- 3-column color/gradient/logo grids in print
- Family chips hidden in print (don't compress usefully at small sizes)

### Removed
- Purple and Orange from active secondary palette per Nathan (rarely used). Reinstate by adding entries back to `config.php`.

### Decided
- Stark's brand sheet is built first as the master template; client onboarding = `cp -r stark/ {client-slug}/` + edit `config.php`. No multi-tenancy.
- Hosting architecture: `brand.starksocial.com/` = marketing landing; `brand.starksocial.com/{slug}` = each client's sheet (path-based, simpler SSL story than subdomain-based)
- Custom client domains via CNAME → `brand.starksocial.com`, server reads Host header, looks up slug, serves their sheet without redirect (branded URL stays in address bar)
- Implementation deferred — restructure into `clients/` folder with top-level dispatcher, build apex landing page, configure Cloudways for the subdomain + custom-domain routing
- PDF export v1 = browser print stylesheet (no library). Revisit Dompdf only if fidelity demands it.
- Self-hosted fonts: each brand sheet instance carries its own `assets/fonts/`. Clients bring their own fonts when onboarding.

### Fixed
- Cleaned up two transcription typos in source brand PDF: Dark Blue True hex (Page 2 said `0C4E97`, RGB matches `004C97`); Light Blue Shade +2 hex (PDF said `004C97`, RGB matches `004976`)

### Modified
- Identity copy authored by Nathan directly in `config.php`: tagline "Marketing that earns its keep", mission listing services and ending in "The work over the words", description with founder reference and Care Pro stewardship line

### Open for next session
- Cover headline letter-spacing fix (Barlow "a A" glyph collision at 36pt on the cover page)
- Type scale `.bs-scale` page-break-inside to fix dead space on page 5
- Hosting buildout (apex landing + dispatcher + custom domain support)
- Logos drop-in (SVG + PNG variants → `assets/logos/`, entries to config)
- Voice section content from STYLEGUIDE
- Perfex sync direction + auth model
- Email signature generator (port from Hub when ready)

## [2.0.4] — April 29 2026

### Decided — Stark Brand Sheet (Phase 3.3)

- **Concept defined.** Stark Care Pro yearly retainer feature: shareable brand asset page, one URL per client, designed for vendor handoff (printers, media outlets, freelancers). Replaces the brand-PDF and reduces back-and-forth on logo/color/font requests.
- **Stack:** Plain PHP. WordPress, ResourceSpace, Pimcore, and SaaS platforms (Brandfolder/Frontify) all evaluated and rejected. Plain PHP fits the asset volume (~30-50 per client), Nathan's skill set, and the premium positioning best.
- **Architecture:** Per-client folder, `config.php` for all client-specific data, independent installs (no multi-tenant single install). Onboarding a new client = copy folder + drop in assets + edit config.
- **Perfex integration:** Uses same webhook pattern as existing GF→Perfex lead flow. Care Pro cancellation flips `'active' => false` in client config, sheet returns inactive notice. Reactivation reverses.
- **Sales positioning:** Justifies Care Pro retainer through ongoing brand stewardship — most agencies hand off a brand PDF at launch and disappear, Stark keeps the brand sheet current.
- **Timeline:** Parked. Do NOT start until Phase 2 and first Henry Mayo Fitness site are both shipped.

### Added

- **BUILDPLAN.md Phase 3.3 section** — full spec for the Stark Brand Sheet, sections, Definition of Done, decisions log
- **brand-sheet/README.md** — standalone product README capturing architecture, onboarding workflow, Perfex integration, and rejected alternatives. Lives at `~/Projects/stark-phase-2/brand-sheet/README.md` as a placeholder folder for the future product.

### Strategic context

- Original conversation reframed multiple times: design system documentation page → live brand demo → Brandfolder-style portal → DAM productized tier → final framing as "shareable brand sheet for vendor handoff." The vendor-handoff framing is the one that aligns with what clients actually need and what Stark can deliver without building an enterprise platform.
- Henry Mayo Fitness pitch is already submitted, so the brand sheet is no longer gated on that pitch — it's a future Care Pro feature, not a pitch artifact.
- Phase 2 launch is now gated on shipping at least one Henry Mayo Fitness site first. Phase 2 launches with Henry Mayo as the inaugural case study, not before.

## [2.0.3] — April 29 2026

### Repo cleanup
- Archived `Current Child Folder/` (old Phase 1 child theme snapshot) to Drive at `Stark Social Media Agency → Tech → Phase 2 (2026) → Reference/`
- Deleted redundant zip uploads from `starksocial-site/`: `First Pages.zip`, `global.zip`, `Site Features.zip`, `global/Header and Footer Only.zip` — unzipped versions remain, Git history preserves originals
- Deleted generic `START-PROMPT.md` (the five role-specific prompts remain)

### Renamed
- `starksocial-site/` → `site-content/` — disambiguates from `stark-social/` (the active GeneratePress child theme code)
- `pages/Deanna L Miller/` → `pages/team/deanna-miller/`
- `pages/Nathan Imhoff/` → `pages/team/nathan-imhoff/`

### Added
- `NAMING-CONVENTION.md` — locks down where files live (GitHub vs Drive vs Perfex), naming rules for both repo and Drive, folder structures, onboarding checklist for new team members
- INSTRUCTIONS.md updated to reflect new repo structure, new file paths, and to include `NAMING-CONVENTION.md` in `STARK-CONTEXT.md` regeneration command

### Decided
- **Style guide page added to Phase 2 scope.** Single page at `starksocial.com/style-guide`, scoped to Stark's own brand system, components rendered live from production CSS. Dual-purpose — internal design tool during build and sales artifact for Henry Mayo Fitness pitch. Full spec coming to BUILDPLAN.md in next pass.
- Research files (`.docx`) live in Drive at `Stark Social Media Agency` shared drive, not committed to repo by design

### Environment
- Repo location moving from `~/Desktop/starksocial` → `~/Projects/stark-phase-2` (final step of cleanup, executed after this commit)

---

## [2.0.2] — April 24 2026

### Added
- Bottom UI cluster on staging — A11y + back-to-top + Perfex chat widget
  - `js/stark-bottom-ui.js` — scroll choreography, 50% show / 40% hide (hysteresis)
  - Cluster styling appended to `custom.css`
  - `functions.php` — renders markup in `wp_footer`, enqueues JS, injects Perfex embed
- **Interruptible scroll-to-top** — RAF-based custom easing, cancels on any user input (touchstart, wheel, keydown, mousedown). Fixes mobile smooth-scroll fights.
- `scroll-behavior: auto` forced on `html` — disables native smooth-scroll site-wide, eliminating iOS momentum-vs-smooth-scroll conflicts
- Staging chat suppression by default — Perfex widget off on staging via `STARK_PRCHAT_ON_STAGING` constant (no wasted tokens)
- Widget ID constant `STARK_PRCHAT_WIDGET_ID` — overrideable via `wp-config.php`

### Decided
- Chat bot = Perfex PRChat (hub.starksocial.com) replacing Olark at launch
- Chat bot shape = squircle (asymmetric border-radius, Olark-style)
- Chat bot position = bottom-right, solo (NOT part of left cluster)
- Entrance = slide from right after 400ms + single pulse ring at 3s
- A11y + back-to-top = left-side pair (A11y at `left:10px` → slides to `left:64px` at 50% scroll; back-to-top slides up into `left:10px`)
- Themeco Pro's `.x-scroll-top` is NOT ported — rebuilt from scratch as `.stark-back-to-top`
- Olark stays live on production through Phase 2 launch; remove at cutover; do NOT cancel Olark sub until Perfex chat verified in production

### Port pending
- 13 keeper WPCode PHP snippets → `functions.php` (Work CPT, taxonomies, reading time, SVG support, form validation, etc.)
- Phase 1 scroll progress + velocity JS (`header-js.js`) → `js/stark-global.js`
- Full `:root` token system + core components (`.stark-card`, `.stark-btn`, `.stark-section`) in `custom.css`
- AIOSEO podcast breadcrumb filter → RankMath equivalent
- OpenAI API key configuration in Perfex admin

---

## [2.0.1] — April 24 2026

### Environment
- Staging app confirmed as `wcrjhscubc` on server 676057 (INSTRUCTIONS.md had stale `rctcpyewsk`)
- `wp-config.php` constants set on staging:
  - `WP_DEBUG=true`, `WP_DEBUG_DISPLAY=false`, `WP_DEBUG_LOG=true` (BetterDocs PHP 8.4 deprecation output no longer breaks login)
  - `WP_REDIS_DISABLED=true`
  - `STARK_ENV=staging`
- BetterDocs 4.3.11 → 4.3.12
- Plugin inventory confirmed: ACF Pro, GenerateBlocks Pro, GP Premium, Gravity Forms, RankMath Pro, Complianz, FacetWP, FileBird Pro, WPCode Premium, SearchWP
- GeneratePress Premium activated as parent; Stark Social v2.0.0 child uploaded

---

## [2.0.0] — April 24 2026

### Added — Stark Social GeneratePress child theme
- Full Phase 1 `functions.php` port (adapted for GeneratePress):
  - Podcast CPT + activation flush
  - `data-author-login` attribute (transient-cached)
  - Passgen conditional enqueue + `[password_generator]` shortcode
  - BetterDocs + Gravity Forms style dequeues + `gform_disable_css`
  - `stark_seconds_to_hms` + `[starkpodmeta]` (transient-cached, SHOW TABLES guard)
  - Weekly `nate_prune_db_event` — Podlove prune + BetterDocs search log truncate
  - Podcast featured image preload (LCP assist)
  - Security headers (HSTS production-only)
  - Podcast/MediaElement conditional dequeue
  - Blog rewrite rules `/blog/` + `/blog/page/N/`
  - BetterDocs `/docs/` → `/support/knowledgebase/` breadcrumb URL fix
  - GF notification `<br />` strip filter (resolves ERR-001)
- Phase 1 assets carried verbatim: 9 Barlow WOFF2s, `author-box.js`, `excerpt-length.js`, `passgen/` trio (CSS renamed from `1passgen.css`), two arrow SVGs
- New for Phase 2: `STARK_ENV` flag + red staging admin badge, seasonal accent inline bootstrap, Barlow WOFF2 preload (3 weights), `custom.css` enqueue with `:root` tokens

### Removed from Phase 1
- Themeco Pro shims (`x_enqueue_parent_stylesheet`, `x-theme` no-op)
- AIOSEO LLMS filter + Cornerstone `cs_component` regex
- AIOSEO cache truncation from weekly cron
- `Template: pro` → `Template: generatepress`

### Fixed
- Password generator CSS enqueue — Phase 1 referenced `passgen.css` but file was named `1passgen.css`

---

## [1.2.4] — April 2026 (Phase 1 live site)

### Fixed
- Gravity Forms → Perfex CRM webhook rebuilt via custom PHP
- Backup cron `*/5 * * * *` → `0 * * * *` (memory exhaustion)
- Perfex email template image paths (124 templates, MySQL find-and-replace)
- Gravity Forms email templates rebuilt (div-based)

### Infrastructure
- Themesic REST API module permanently deactivated
- Signature generator deployed

---

## [1.2.3] — Early 2026

- Seasonal accent system
- Scroll velocity CSS variable
- Reveal + tilt effects
- Accessibility button with scroll offset
- Back-to-top button redesign
- `[starkpodmeta]` shortcode
- Podlove DB prune cron

---

## [1.1.0] — 2025

- GF Mailgun, FacetWP, SearchWP+BetterDocs, Google Site Kit
- Stark Site Toolkit plugin, WPCode Premium
- Password Generator, Complianz GDPR
- Object Cache Pro, Breeze, Cloudflare Enterprise
- Security headers, generator/XMLRPC removal

---

## [1.0.0] — Initial Build

- Themeco Pro/X + Stark Social child
- Barlow + Barlow Condensed (WOFF2)
- Podcast CPT + Podlove
- Blog at `/blog/`
- AIOSEO Pro + 5 addons
- BetterDocs Pro
- Brand: `#004C97`, `#F93822`, `#307FE2`, `#031B34`
