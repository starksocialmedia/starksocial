# CHANGELOG.md — Stark Social Media Agency
**Project:** starksocial.com
**Format:** newest first

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
