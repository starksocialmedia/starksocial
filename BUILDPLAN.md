# BUILDPLAN.md — Stark Social Media Agency
**Project:** starksocial.com Phase 2 Rebuild
**Last Updated:** April 24 2026
**Status:** 🟢 Phase 2.0 in progress

---

## Strategic Decision

**Migrating from Themeco Pro/X + Cornerstone → GeneratePress Premium + Gutenberg/GenerateBlocks**

### Why
- Mobile PageSpeed 47, LCP 9.8s — platform ceiling, not tunable
- Desktop 79, hardware masking builder bloat (149KB unused JS, 6,178KB total)
- Cornerstone export broken
- All visual CSS portable; redesign is already planned

### What Stays
- All custom PHP in `functions.php`
- Plugin logic: Gravity Forms → Perfex CRM webhook, SearchWP, BetterDocs, FacetWP
- Portfolio CPT structure + ACF Pro fields
- Barlow font stack

### What Changes
- Theme: Pro/X → GeneratePress Premium
- Builder: Cornerstone → GenerateBlocks + Gutenberg
- SEO: AIOSEO → RankMath Pro (fresh on staging)
- CSS: WPCode global → child theme `custom.css` + page-specific CSS

---

## Stack

| Layer | Phase 2 |
|---|---|
| Theme | GeneratePress Premium |
| Builder | GenerateBlocks + Gutenberg |
| CSS | `custom.css` in child theme |
| SEO | RankMath Pro |
| Forms | Gravity Forms |
| CRM | Perfex Hub |
| Chat | Perfex PRChat (replaces Olark at launch) |
| Caching | Breeze + Object Cache Pro |
| Search | SearchWP |
| Docs | BetterDocs Pro |
| Hosting | Cloudways |

---

## Page Inventory (21 pages + 4 templates)

### Core (Priority 1)
- [ ] Home (`/`)
- [ ] About (`/about/`)
- [ ] Contact (`/contact/`)
- [ ] Services Hub (`/services/`)

### Services (Priority 2)
- [ ] Social Media Management
- [ ] Paid Social Advertising
- [ ] Web Design
- [ ] SEO
- [ ] Content Creation
- [ ] Brand Strategy & Identity
- [ ] Digital Marketing Audit & Consulting — NEW

### Portfolio — NEW in Phase 2
- [ ] Portfolio archive (`/portfolio/`)
- [ ] Portfolio single template
- [ ] Portfolio CPT fields (ACF Pro — already defined)

### Blog & Podcast
- [ ] Blog archive + single
- [ ] Podcast archive + single (Podlove)

### Support
- [ ] Support hub
- [ ] Knowledgebase (`/support/knowledgebase/`)

### Author Pages
- [ ] Nathan Imhoff
- [ ] Deanna L. Miller

### Utility
- [ ] 404
- [ ] Search Results
- [ ] Password Generator

### Legal (copy-forward)
- [ ] Privacy Policy
- [ ] Terms of Service
- [ ] Cookie Policy
- [ ] Accessibility Statement

---

## Phase 2 Build Phases

### Phase 2.0 — Environment Setup
- [x] GitHub repo for project docs
- [x] Migration decision (GeneratePress)
- [x] Child theme v2.0.0 — full Phase 1 port — April 24 2026
- [x] Fresh WordPress on staging (Cloudways app `wcrjhscubc`, server 676057)
- [x] Plugin inventory confirmed active
- [x] GeneratePress parent + child theme uploaded and activated
- [x] `wp-config.php`: `STARK_ENV=staging`, `WP_REDIS_DISABLED=true`, debug flags set
- [x] Bottom UI cluster v2.0.1 — A11y + back-to-top + Perfex chat embed + mobile scroll fix — April 24 2026
- [ ] Paste Phase 1 design tokens + global CSS into `custom.css`
- [ ] Port 13 keeper WPCode snippets into `functions.php`
- [ ] Port Phase 1 scroll progress + velocity JS into `js/stark-global.js`
- [ ] Port AIOSEO podcast breadcrumb filter to RankMath
- [ ] Configure OpenAI API key in Perfex AI Chatbot
- [x] Verify transparent → frosted glass nav — v27.0.1, November 2 2026
- [x] Build mega menu (desktop) + accordion (mobile) — v27.0.1, November 2 2026
- [x] Stark 27 footer v27.0.2 — 4-column links, social, bottom bar, GP footer suppressed — November 2 2026

### Phase 2.1 — Core Pages
- [x] Homepage shell v27.0.3 — hero, services preview, who-we're-for, CTA band — November 2 2026
- [x] Homepage social proof, featured case study, FAQ v27.0.20 — November 2 2026
- [ ] Homepage: real "clients served" / "projects shipped" numbers
- [x] Homepage process section v27.1.3 — Discover / Design / Build / Grow, draft copy — November 2 2026
- [x] About page template v27.0.7 — ACF fields, `[SAMPLE]` copy on Local — November 2 2026
- [x] Contact page template v27.0.8 — GF form ID 1 placeholder, details from live — November 2 2026
- [ ] Create Gravity Forms contact form (ID 1) + Perfex webhook
- [ ] Home · About · Contact · Services hub

### Phase 2.2 — Service Pages (7)
- [x] Service page template v27.0.4 — ACF Pro fields via `acf-json/`, routes by slug — November 2 2026
- [x] Location page template v27.0.5 — `/{service}/{market}/`, local overlay on parent content — November 2 2026
- [ ] Activate ACF Pro on staging + live (Local switched from free ACF)
- [ ] Full local landing URL map (SEO chat) — pattern locked: nested `/{service}/{area}/`
- [x] Service areas hub stub v27.0.6 — `/service-areas/`, linked from footer Company column — November 2 2026
- [ ] Service areas hub full build — per-market service links, local proof
- [x] All 7 service pages — first-draft copy on Local, v27.0.13 — November 2 2026
- [ ] Nathan review of service copy → set Copy status to Approved per page
- [x] Services hub `/services/` v27.0.13
- [x] 301s from live's `/services/{slug}/` URLs v27.0.13
- [ ] New: Audit & Consulting

### Phase 2.3 — Portfolio
- [x] `portfolio` CPT + ACF fields v27.0.12 — archive `/work/`, singles `/portfolio/{slug}/` — November 2 2026
- [x] Archive (filters) + single templates v27.0.12
- [x] Migrate existing case studies — Pathway Escrow, Schlickart (+ Valencia Jazz & Blues draft)
- [ ] More case studies + client testimonials (Pathway, Schlickart have none)
- [ ] Confirm inferred industry / year / services on imported case studies

### Phase 2.4 — Blog & Podcast
- [x] Blog snapshot from live into Local — 72 posts — November 2 2026 (final sync at cutover, see `tools/blog-snapshot/`)
- [x] Blog archive + single templates v27.0.9 — November 2 2026
- [x] Podcast archive + single templates v27.0.16 — 37 episodes on Local — November 2 2026
- [ ] **Launch-critical:** install Podlove Podcasting + Web Player on Phase 2 and migrate Podlove tables (RSS feed for Apple Podcasts)
- [x] SVG episode covers → WebP 1280 / 640 v27.1.4 (11.4 MB → 2.1 MB) — November 2 2026
- [x] Author pages + bios v27.0.17 — November 2 2026
- [x] Redirect live's team pages → `/author/…/` v27.0.18

### Phase 2.5 — Support & Utility
- [x] 404 + search templates v27.0.10 — November 2 2026
- [x] Password generator page `/support/password-generator/` v27.0.18
- [x] Support hub (`/support/`) + knowledgebase (`/support/knowledgebase/`, 27 BetterDocs docs from live) v27.1.2 — November 2 2026

### Phase 2.6 — Legal
- [x] Copy-forward 4 legal pages to `/support/` — v27.0.15, noindex — November 2 2026
- [ ] Legal review of copy (last updated January 15, 2026)

### Phase 2.7 — QA & Launch
- [x] **Staging deployed from Local** v27.1.0 — DB, theme, uploads; permalinks = live; ACF Pro active; mail blocked + noindex via `stark-staging-guard` mu-plugin — November 2 2026 (`tools/deploy/`)
- [x] Lighthouse + axe audit v27.1.1 — `tools/audits/2026-11-02`; quick fixes shipped — November 2 2026
- [ ] **Needs Nathan:** primary button contrast — white on `#307FE2` is 3.99:1 (AA needs 4.5:1). Darken buttons to `#2A70CC`, or larger bold button text. Only remaining axe failure
- [ ] Re-run Lighthouse on staging (real cache headers, Cloudflare); check `/web-design/` mobile CLS (0.1 under throttling)
- [ ] Trim render-blocking CSS (Complianz / BetterDocs on pages that don't use them); minify `custom.css`
- [ ] Perfex chat widget: third-party cookie warning (`SameSite=None; Secure` on hub.starksocial.com)
- [ ] PageSpeed (desktop 95+, mobile 85+)
- [ ] Core Web Vitals green
- [ ] Cross-browser + device
- [ ] Accessibility 98+
- [x] Rank Math site-wide config v27.1.5 — titles, Local SEO, sitemap, robots, schema (`tools/rankmath/`) — November 2 2026
- [ ] Rank Math per-page: focus keywords, share images, final descriptions, Service schema (`tools/rankmath/README.md`)
- [ ] RankMath 90+ per page
- [ ] Schema validation zero errors
- [ ] Local SEO landing pages verified
- [ ] **Remove Olark embed from live before DNS cutover**
- [ ] DNS cutover
- [ ] Post-launch: verify Perfex chat, then cancel Olark subscription

---

## Workstream Ownership

| Chat | Scope |
|---|---|
| `Stark — Build` | GeneratePress, templates, CSS, PHP, components, nav |
| `Stark — SEO` | RankMath, local landing pages, schema, keyword mapping |
| `Stark — Copy` | Voice guide, page copy, service pages, local messaging |
| `Stark — Blog` | Audit, cull, refresh, 2026 content calendar |
| `Stark — QA` | PageSpeed, a11y, schema, cross-browser |

Portfolio: Template (Build) + Copy (Copy) + Schema (SEO).
Nathan: Project Director — decisions, approvals, cross-chat coordination.

---

## Phase 3 (Future)

### Phase 3.1 — Stark Social Ongoing
- Perfex drip campaigns for client services
- MainWP full integration + client site reporting
- Nav refinement based on analytics
- Performance push (mobile 90+)
- Wincher rank tracking review

### Phase 3.2 — Stark Hosting (Separate Site)
- Standalone hosting brand on Cloudways reseller
- Offerings: domain, email, managed + unmanaged WP hosting
- Separate brand, domain, Cloudways app
- Billing via WHMCS or Blesta
- Cross-promoted on starksocial.com
- **Do not start until Phase 2 is live**

---

### Phase 3.3 — Stark Brand Sheet (Care Pro Feature)

**Status:** Concept / parked
**Owner:** TBD when work starts (likely new chat: `Stark — Brand Sheet`)
**Priority:** Post-launch — do NOT start until Phase 2 + first Henry Mayo Fitness site are both shipped
**Last reviewed:** April 29, 2026

---

#### What It Is

A shareable brand asset page — one URL per client. The modern replacement for the brand-PDF every brand-conscious org maintains. Client forwards the URL to printers, media outlets, ad agencies, and any vendor who needs brand assets. No login required for public assets.

Positioned as a **Stark Care Pro yearly retainer feature.** Clients on Care Pro tier get a custom branded sheet hosted by Stark. Cancel Care Pro → sheet deactivates via Perfex webhook.

#### Why It's Valuable

- **For clients:** Send one URL to a printer instead of emailing logos, hex codes, and font files separately. Looks professional. Reduces back-and-forth.
- **For vendors:** Printers, media outlets, designers get a single source. Downloadable files, copy-paste color values, no accounts.
- **For Stark:** Build once per client (~4-8 hours of client-asset prep + minor config). Strong differentiator against agencies that don't offer ongoing brand stewardship. Sticky upsell.

#### Sections (per sheet)

1. Header — client logo, last-updated date, agency contact line
2. **Logos** — each variant downloadable in SVG, PNG (transparent), JPG (white bg), EPS. Clearspace/min-size guidance shown visually.
3. **Colors** — large swatches with hex, RGB, CMYK, Pantone (where applicable). Click any value to copy to clipboard. Usage notes.
4. **Typography** — font names, downloadable .woff2/.otf, sample text in each weight, fallback guidance.
5. **Templates & graphics** — business cards, letterhead, social templates, email signature generator (ports existing Hub code).
6. **Usage guidelines** — visual do's and don'ts.
7. **Print/PDF version** — single button generates a self-contained PDF of the entire sheet for offline reference or attaching to emails. Killer feature for vendor handoff.

#### Architecture (Direction, Not Final)

**Stack:** Plain PHP. No framework, no WordPress, no DAM (ResourceSpace/Pimcore evaluated and rejected as overkill for asset volume per client).

**Per-client folder structure:**
```
brand-sheet/
├── index.php            ← renders entire page
├── config.php           ← client-specific data arrays
├── pdf.php              ← generates downloadable PDF on demand
├── signature.php        ← email signature generator (adapted from Hub)
├── assets/
│   ├── logos/
│   ├── fonts/
│   ├── templates/
│   └── images/
├── css/sheet.css
└── js/sheet.js
```

**Onboarding a new client:** Copy the folder, drop in their assets, edit `config.php`. That's the work.

**Hosting:** Each client gets their own subdomain (`brand.{client-domain}.com`) or path on Stark infrastructure (`{client-slug}.starkbrand.com`). Each install is independent — no shared infrastructure between clients, no multi-tenant database to manage.

**Perfex integration (cancellation flow):**
- Client cancels Care Pro in Perfex
- Perfex webhook fires (same mechanism as existing GF→Perfex lead webhook)
- Custom PHP endpoint flips `'active' => false` in client's `config.php`
- Page returns "Brand sheet for [Client] is currently inactive — contact agency"

#### Why Not Alternatives

- **WordPress:** Heavier than needed, Nathan prefers not to build new product surface in WordPress, no value-add for a static asset page.
- **ResourceSpace / Pimcore:** Built for thousands of assets and complex permissions. Overkill for 30-50 brand assets per client. Generic UI undermines premium positioning. Maintenance burden of running enterprise DAM per client.
- **SaaS like Brandfolder/Frontify:** Wrong economics — paying per-seat for clients defeats the productization play.

#### Definition of Done — v1 (Stark's own sheet)

- [ ] Sheet live at chosen URL (TBD — possibly `brand.starksocial.com` or `starksocial.com/brand/`)
- [ ] All 7 sections built and populated with Stark's actual assets
- [ ] Logos downloadable in SVG, PNG, JPG, EPS
- [ ] Colors with click-to-copy (hex, RGB, CMYK, Pantone)
- [ ] Email signature generator working (adapted from Hub code)
- [ ] PDF export working
- [ ] Mobile responsive
- [ ] WCAG 2.1 AA pass

#### Definition of Done — v2 (productized for clients)

- [ ] Onboarding documented: hand-off process from Stark team to client
- [ ] Perfex webhook → deactivation flow tested end-to-end
- [ ] First paying Care Pro client onboarded (likely Henry Mayo Fitness if they take Care Pro)
- [ ] Pricing locked in for Care Pro tier (Nathan decision)
- [ ] Marketing page on starksocial.com explaining the Care Pro brand sheet feature

#### Out of Scope (Forever or for Later)

- Client-editable interface (clients send updates to Stark, Stark updates the sheet)
- Asset versioning UI (Git history is sufficient)
- Analytics dashboard for clients (could be added later if requested)
- Multi-tenant single install (intentionally rejected — independent installs are simpler)
- AI-powered metadata or search (not needed at this asset volume)

#### Decisions Log

- **April 29, 2026 — Use case clarified:** Primary value is vendor handoff (printers, media outlets) not internal brand documentation. Drives the "shareable URL + PDF export" framing.
- **April 29, 2026 — Stack decision:** Plain PHP. WordPress and DAM platforms (ResourceSpace, Pimcore) evaluated and rejected.
- **April 29, 2026 — Productization model:** Care Pro yearly retainer feature, not a standalone SaaS. Cancellation = deactivation via Perfex webhook.
- **April 29, 2026 — Timing:** Parked. Do not start until Phase 2 + first Henry Mayo Fitness site are shipped.

## Navigation Structure (Locked)

**Desktop:** Transparent on load → slim frosted glass on scroll
**Mobile:** Same transition, burger → full screen dark glass overlay

**Primary nav (5 items):**
- Work (portfolio)
- Services (mega menu, 7 services)
- Blog
- Podcast
- Contact

**Services mega menu:**
```
Social Media Mgmt     Paid Advertising
Web Design            SEO
Content Creation      Brand Strategy
Audit & Consulting
[View All Services →]
```

**Utility (top right):** Support · Client Portal
**Mobile services:** Accordion expand inline, no Back button

---

## Key URLs & Infrastructure

- Live: `starksocial.com` (app `rvqkxhngas`)
- Staging: `staging.starksocial.com` (app `wcrjhscubc`)
- Perfex Hub: `hub.starksocial.com` (app `knaqmwdvju`)
- MainWP: `cpanel.starksocial.com`
- Cloudways server: 676057
- GF → Perfex webhook: `hub.starksocial.com/webhooks/lead.php`
- Perfex chat widget ID: `67d3563318b5547bfab53fa6366ace82`
- GitHub: `github.com/starkweblabs/starksocial`

---

## Notes

- BetterDocs default styles dequeued in `functions.php`; styling in `custom.css`
- Gravity Forms default styles dequeued
- Podcast scripts conditionally loaded only on podcast pages
- Blog rewrites: `/blog/` + `/blog/page/N/` via `add_rewrite_rule`
- Podlove DB tables: `wp_podlove_episode` queried directly in `starkpodmeta`
- Mobile smooth scroll: kill CSS `scroll-behavior: smooth`; JS does programmatic scroll with touch interrupts


## Style Guide Page

**Status:** Not started
**Owner:** `Stark — Style Guide` chat (page rendering, sections), `Stark — Copy` chat (written sections)
**Priority:** Build alongside Phase 2 — used as live design reference during development; pitched to prospects (Henry Mayo Fitness) ASAP
**Scope:** Single page, Stark's own brand system, components rendered from production CSS, deployed in two phases

---

### Purpose

Dual-purpose deliverable:

1. **Internal tool** — single source of truth for design tokens and components. Build chat references it when porting CSS. Copy chat references it for voice/tone consistency.
2. **Sales artifact** — public-facing demonstration of brand rigor. Primary use case: Henry Mayo Fitness pitch. Secondary: linked from portfolio case studies, used in all future pitches. Long-term: prototype for productized client style guide offering (post-Phase 2).

---

### Build Sequence (Two Phases)

This page is built **twice** by design: once on the current site, then ported when Phase 2 launches.

#### Phase A — Current site (Themeco Pro/X + Cornerstone)

- **Live URL:** `starksocial.com/style-guide`
- **Built using:** Cornerstone elements + custom CSS via WPCode + PHP where needed
- **Why first:** Henry Mayo Fitness pitch is imminent. Need a polished live URL to send prospects faster than waiting for Phase 2 launch.
- **Tokens:** Pull from current site's CSS variables wherever possible

#### Phase B — Phase 2 site (GeneratePress + GenerateBlocks)

- **Live URL:** `staging.starksocial.com/style-guide` during dev; replaces Phase A at `starksocial.com/style-guide` at Phase 2 launch
- **Built using:** GenerateBlocks page template (or `page-style-guide.php` in `stark-social/` child theme), pulling from Phase 2's `:root` token system
- **Components:** Embedded as-is from production `stark-social/` theme — no reimplementation
- **Goal:** Feature parity with Phase A, but cleaner architecture using Phase 2 tokens

---

### Lockdown Mechanism

- **STYLEGUIDE.md** in repo = written canonical source (rationale, rules, decisions)
- **`/style-guide/` page** = visual rendering, pulls from live CSS variables via JS at render time
- **Components on the page** = production components, not copies
- **Drift = structurally impossible** because there's one source of CSS

---

### Page Sections (in order, both phases)

1. **Brand foundation**
   - Logo (primary, secondary, monogram if exists)
   - Clearspace and minimum sizing
   - Voice summary (3–4 sentences, links to VOICE-GUIDE.md)

2. **Color system**
   - Swatches rendered from CSS custom properties
   - Each swatch shows: hex, RGB, CSS variable name, usage notes
   - Includes: primary, secondary, neutrals, semantic (success/warning/error), surface tokens

3. **Typography**
   - Live type scale rendered in production font stack (Barlow + Barlow Condensed)
   - Display, H1–H6, body, small, caption
   - Each shows: font family, weight, size, line-height, letter-spacing, CSS variable name
   - Pairing examples (heading + body together)

4. **Spacing & layout**
   - Spacing scale (rendered as visual blocks)
   - Grid system
   - Breakpoints with annotations
   - Container max-widths

5. **Components**
   - Buttons (all variants × all states: default, hover, active, focus, disabled)
   - Form fields (input, textarea, select, checkbox, radio, with all states)
   - Cards (variants used across the site)
   - Navigation states (transparent, frosted, mobile accordion — live demo)
   - Scroll progress bar (live demo in contained section)
   - A11y + back-to-top choreography (live demo)

6. **Motion principles**
   - Easing curves used
   - Duration tokens
   - When to animate, when not to

7. **Code standards**
   - CSS naming conventions
   - File organization
   - Comments and documentation expectations

---

### Definition of Done — Phase A (Current Site)

- [ ] Page live at `starksocial.com/style-guide`
- [ ] All seven sections built and populated
- [ ] All color tokens rendered from CSS custom properties
- [ ] All typography rendered in production Barlow stack with correct tokens
- [ ] All component states represented and interactive where applicable
- [ ] Scroll progress bar and nav transformation working as live demos
- [ ] Page passes WCAG 2.1 AA
- [ ] Page indexed in RankMath/AIOSEO with appropriate schema (coordinate with SEO chat)
- [ ] Linked from portfolio + footer (Nathan decides placement)
- [ ] STYLEGUIDE.md updated to reference page as visual companion
- [ ] Pitch-ready: linkable in proposals to Henry Mayo Fitness and other prospects

### Definition of Done — Phase B (Phase 2 Port)

- [ ] Page rebuilt on staging at `staging.starksocial.com/style-guide`
- [ ] Built using Phase 2 `:root` token system from `stark-social/` child theme
- [ ] All components are production Phase 2 components, not copies
- [ ] Feature parity with Phase A version (or improved)
- [ ] Phase 2 PageSpeed targets met (85+ mobile, 95+ desktop)
- [ ] WCAG 2.1 AA passes on Phase 2 build
- [ ] Replaces Phase A version at `starksocial.com/style-guide` at Phase 2 launch
- [ ] Old Phase A code removed cleanly

---

### Working Workflow

- As `Stark — Build` chat creates each component for Phase 2, the `Stark — Style Guide` chat adds it to the Phase 2 style guide page (Phase B build)
- Style guide page becomes the integration test — if a component looks wrong on the guide, it's wrong on the site
- `Stark — Copy` chat reviews written sections (voice, brand foundation, code standards intros) once components are in place
- `Stark — QA` chat audits the guide page during the Phase 2 QA pass (accessibility, schema, PageSpeed)

---

### Out of Scope for v1 (both phases)

- Client-editable version
- Multi-tenant / per-client style guides (productized offering — separate scope, after Phase 2)
- Downloadable assets (logo zip, font files) — can be added in v1.1 if Henry Mayo conversation requires it
- Versioning UI (v1.0, v1.1 history) — handled in Git commits

---

### Decisions Log

- **April 29, 2026 — Build sequence:** Phase A on current Themeco/Cornerstone site first, Phase B port to GeneratePress before Phase 2 launch. Decided in favor of speed-to-pitch over single-build efficiency.
- **April 29, 2026 — Ownership:** Dedicated `Stark — Style Guide` chat owns this deliverable across both phases. Build chat focuses on Phase 2 site overall.
- **April 29, 2026 — Sales context:** Primary prospect = Henry Mayo Fitness. Style guide is the wedge against an expensive, unresponsive incumbent vertical marketing company.
