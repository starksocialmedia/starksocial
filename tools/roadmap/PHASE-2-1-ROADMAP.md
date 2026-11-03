# Phase 2.1 roadmap — after launch

Everything deliberately deferred from Phase 2, plus known tech debt. As of v27.3.x
(November 2 2026). Phase 3 (hosting brand, Brand Sheet) stays in `BUILDPLAN.md`.

## Content

- **Case studies:** 3 now (Pathway Escrow, Schlickart, Valencia draft). Live has no others. Target 6–8, with real results and approved testimonials
- **Testimonials:** none exist on live. Collect 3–5 from current clients; the store is `inc/stark-testimonials.php` and the slots are the homepage proof row, service pages and case studies
- **Client logos / award badges:** `img/clients/`, `img/awards/` (empty; READMEs explain)
- **Location pages:** 1 sample (`/web-design/antelope-valley/`). Build the nested `/{service}/{area}/` map from `SEO-STRATEGY.md` once copy exists per market
- **Audit & Consulting** page copy (no live equivalent)
- **Homepage hero**: decide between the Phase 2 hero and live's "Elevate Your Brand. Build Something That Lasts."
- Old animated GIFs in 2015–2024 posts (2–7 MB each) → MP4 or WebP when those posts are edited
- 2015–2018 posts on dead platforms: review with Search Console data and prune or redirect

## Product / features

- **Site auditor tool.** No spec in the repo (Nathan's earlier spec lives outside it). Suggested shape: a `/tools/site-audit/` page next to the password generator. Visitors enter a URL; a server-side check (Lighthouse API or PageSpeed Insights, plus headers, SSL, meta and schema) feeds into the GF → Perfex lead flow. Spec first, then build
- **Shared design tokens with brands.starksocial.com** (Brand Sheet, BUILDPLAN Phase 3.3). Move the `:root` tokens in `custom.css` (sections 1 and 14) into a standalone `tokens.css` that both sites load or build from. Same names: `--stark-navy`, `--stark-light-blue`, `--stark-accent-dark`, `--stark-blue-ink` …
- **Knowledgebase template:** a Stark template for BetterDocs singles and categories. The plugin template has no `<main>`, two banner landmarks and its own sidebar (the only remaining axe notes)
- **Podlove Web Player theme:** match the player colours and fonts to Phase 2 (it's Podlove's default blue)
- **Visual breadcrumbs on BetterDocs** pages (they keep BetterDocs' own)
- **Contact form:** Perfex fields for `company`, `city` and the source fields need checking on the `lead.php` side; add reCAPTCHA or Turnstile if spam shows up (Complianz already lists reCAPTCHA)
- **Cookie consent:** decide opt-in vs opt-out (currently US opt-out, as on live). Turn on Complianz's cookie blocker (live has it off) once the services list is verified

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
- Pretty Links and AIOSEO redirects become Rank Math redirections (one system)
- Phase 1 leftovers to remove after launch: WPCode snippets ported to `functions.php`, stark-site-toolkit, the Pro theme
