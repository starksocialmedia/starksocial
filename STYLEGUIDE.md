# STYLEGUIDE.md — Stark Social Media Agency
**Project:** starksocial.com
**Last Updated:** April 29, 2026
**Scope:** Brand, visual design, CSS conventions, code standards, copy voice

---

## Brand Colors

Stark's palette is six colors. Every one earns its place — there's nothing here that doesn't get used. Each color carries a dual name: an evocative half (the personality) and a functional half (the role).

### Phase 2 naming convention (canonical, adopted April 29 2026)

| Hex | Name | Variable | Role |
|---|---|---|---|
| `#004C97` | **Midnight Anchor** | `--stark-anchor` | Foundation. Headlines, navigation, CTA backgrounds. |
| `#F93822` | **Ember Pulse** | `--stark-pulse` | Heat. Inline accents, error states, signature CTAs. Forms the Stark logo gradient. |
| `#307FE2` | **Sky Signal** | `--stark-signal` | Current. Interactive elements, links, scroll progress, seasonal accent base. |
| `#FEDD00` | **Caution Beacon** | `--stark-beacon` | Attention. Focus rings (WCAG-compliant), accents on dark surfaces. |
| `#00B140` | **Moss Affirm** | `--stark-affirm` | Yes. Success states, positive metrics, confirmations. |
| `#E31C79` | **Pop Spark** | `--stark-spark` | Conversion. Hero CTAs, contact buttons. |

### Why dual names

The evocative half ("Midnight," "Ember," "Sky") gives the brand sheet personality and helps marketing communicate. The functional half ("Anchor," "Pulse," "Signal") tells developers what a color is *for* — the variable name reflects the role, not the appearance. A color named only "Midnight" might end up as an accent and the name would lie. A variable named `--stark-anchor` makes the role explicit and stays accurate as usage evolves.

Use the full name ("Midnight Anchor") in client-facing brand documents and the variable (`--stark-anchor`) in code.

### CSS variables (Phase 2)

```css
:root {
  /* Primary */
  --stark-anchor:   #004C97;  /* Midnight Anchor */
  --stark-pulse:    #F93822;  /* Ember Pulse */
  --stark-signal:   #307FE2;  /* Sky Signal */

  /* Secondary */
  --stark-beacon:   #FEDD00;  /* Caution Beacon */
  --stark-affirm:   #00B140;  /* Moss Affirm */
  --stark-spark:    #E31C79;  /* Pop Spark */
}
```

### Pantone, RGB, CMYK

Canonical print values per Stark's brand standards:

| Color | Pantone | RGB | CMYK |
|---|---|---|---|
| Midnight Anchor | 2945 C | 0, 76, 151 | 100, 53, 2, 16 |
| Ember Pulse | Bright Red C | 249, 56, 34 | 0, 78, 74, 0 |
| Sky Signal | 2727 C | 48, 127, 226 | 70, 47, 0, 0 |
| Caution Beacon | Yellow C | 254, 221, 0 | 0, 1, 100, 0 |
| Moss Affirm | 354 C | 0, 177, 64 | 81, 0, 92, 0 |
| Pop Spark | 213 C | 227, 28, 121 | 0, 92, 18, 0 |

### Phase 1 → Phase 2 rename mapping

Phase 1 (current production) uses different variable names on the marketing site versus the Hub. Phase 2 unifies under the new namespace at cutover. Until then, both production systems keep their existing names — see mapping below.

| Phase 2 (canonical) | Phase 1 — starksocial.com | Phase 1 — hub.starksocial.com |
|---|---|---|
| `--stark-anchor` | `--stark-dark-blue` | `--ss-blue-dark` |
| `--stark-pulse` | `--stark-primary-red` | `--ss-red` |
| `--stark-signal` | `--stark-primary-light-blue` | `--ss-blue-light` |
| `--stark-beacon` | `--stark-secondary-yellow` | `--ss-yellow` |
| `--stark-affirm` | `--stark-secondary-green` | `--ss-green` |
| `--stark-spark` | `--stark-secondary-magenta` | *(not used in Hub)* |

**Cutover policy:** New work in Phase 2 child theme uses the canonical names from day one. Phase 1 marketing site and the Hub keep their existing names until Phase 2 launches. The brand sheet (`brand-sheet/stark/`) reflects Phase 2 conventions because it's the forward-looking authority.

### Tints and shades

Modern design tools generate tints and shades algorithmically from canonical color values. The brand sheet doesn't pre-compute them — print designers and devs both work from the canonical Pantone/CMYK/hex above. Stark's older brand standards PDF documents two tints and two shades per color for press reference if needed; that document remains the source of truth for print families and lives in Drive at `Stark Social Media Agency → Tech → Reference/`.

---

## Gradients

```css
/* UI / CSS gradients — components, buttons, sections */
--gradient-anchor: linear-gradient(135deg, #0C4E97 0%, #1D65B8 100%);
--gradient-pulse:  linear-gradient(135deg, #F93822 0%, #DA291C 100%);
--gradient-signal: linear-gradient(135deg, #307FE2 0%, #539AEF 100%);

/* Hover variants reverse direction (suffix --hover) */
```

Logo composition gradients live separately — they're print specs for the Stark mark, not UI primitives:

- **Logo Background Gradient**: `linear-gradient(135deg, #418FDE 0%, #FFFFFF 100%)` — PMS 279 C → white
- **Logo Stark Gradient**: `linear-gradient(135deg, #F93822 0%, #DA291C 100%)` — Bright Red C → PMS 485 C (same hex stops as the UI Pulse gradient; keep names distinct in handoff specs)

---

## Typography

**Font:** Barlow + Barlow Condensed (self-hosted WOFF2 in child theme `/fonts/`)
**Base:** `18px` / `line-height: 1.65`
**Smoothing:** `-webkit-font-smoothing: antialiased`

### Type Scale

| Element | Size | Weight | Letter-spacing |
|---|---|---|---|
| h1 | `clamp(2.6rem, 4vw, 3.6rem)` | 800 | -0.03em |
| h2 | `clamp(1.85rem, 2.4vw, 2.45rem)` | 800 | -0.02em |
| h3 | `clamp(1.25rem, 1.6vw, 1.55rem)` | 800 | -0.015em |
| h4 | `1.1rem` | 750 | -0.01em |
| h5 | `1rem` | 700 | -0.005em |
| h6 | `0.95rem` | 700 | 0 |
| Body | `1rem` / `18px` | 400 | — |
| Eyebrow | `0.78rem` | 700 | `0.14em` uppercase |
| Small/meta | `0.92rem` | 400 | — |

---

## UI Tokens

**Source of truth.** When token values change, edit this section first, then update `brand-sheet/stark/config.php` to match. They should never drift.

```css
/* Text */
--stark-text:       #111;
--stark-muted:      rgba(17,17,17,.62);
--stark-muted-2:    rgba(17,17,17,.48);

/* Surfaces */
--stark-surface:    #fff;
--stark-bg:         #0b0f1a;        /* Reserved for dark surfaces — photo overlays, future dark mode */
--stark-rule:       rgba(17,17,17,.14);
--stark-rule-soft:  rgba(17,17,17,.08);

/* Accent (dynamic, seasonal — defaults to Sky Signal at 0.85 alpha) */
--stark-accent:          rgba(48,127,226,0.85);
--stark-accent-soft:     rgba(48,127,226,0.14);
--stark-accent-glow:     rgba(48,127,226,0.35);

/* Border radii */
--stark-radius-card: 14px;
--stark-radius-ui:   16px;
--stark-radius-field:14px;
--stark-radius-btn:  12px;

/* Layout widths */
--stark-max-wide:    1400px;
--stark-max-search:  1040px;
--stark-max-article: 960px;
```

### Naming policy

Functional UI tokens (`--stark-text`, `--stark-muted`, `--stark-rule`) keep their plain descriptive names. The dual-name pattern earns its keep on brand colors that marketing people see. Tokens devs touch a hundred times a day stay functional. Don't rename `--stark-text` to `--stark-ink` or similar — the cuteness has diminishing returns at this layer.

---

## Seasonal Accents

The accent color shifts subtly per season via `data-stark-season` on `<html>`:

| Season | Months | `--stark-accent` |
|---|---|---|
| Spring | Mar–May | `rgba(68,149,255,.90)` |
| Summer | Jun–Aug | `rgba(33,165,255,.88)` |
| Fall | Sep–Nov | `rgba(30,92,195,.88)` |
| Winter | Dec–Feb | `rgba(92,178,255,.82)` |

Set inline in `<head>` via inline JS snippet to prevent flash.

---

## Layout System

### Sections
`.stark-section` — base padding: `clamp(5rem, 12vh, 9rem) 0`
`.stark-section-tight` — `clamp(3.75rem, 9vh, 6.5rem) 0`
`.stark-section-loose` — `clamp(6.5rem, 14vh, 11rem) 0`

### Containers
`.stark-inner` — max-width `1400px`, horizontal padding `clamp(20px, 3vw, 56px)`
`.stark-inner-narrow` — max-width `960px`
`.stark-inner-medium` — max-width `1040px`

### Breakpoints
- Mobile: `≤ 599px`
- Tablet: `600px – 899px`
- Desktop: `≥ 900px`

---

## Components

### Buttons

**Base class:** `.x-anchor-button.stark-btn`
All buttons use glass morphism base: `backdrop-filter: blur(10px)`, `border: 1px solid rgba(...)`, lift on hover (`translateY(-2px)`).

| Class | Description |
|---|---|
| `.stark-btn` | Default glass/white button |
| `.blue-btn` | Anchor gradient, white text |
| `.magenta-btn` | Spark gradient, white text |
| `.stark-glass-magenta` | Glass spark, semi-transparent |
| `.white-trns-btn` | Transparent white glass (for dark backgrounds) |
| `.ghost-btn` | No background, no border, text-only |
| `.stark-glass-accent` | Accent-tinted glass |
| `.spotify-btn` | Spotify green glass (podcast use only) |

All buttons: `min-height: 52px`, `border-radius: var(--stark-radius-btn)`, `font-weight: 750`.
Mobile: `min-height: 46px`, `border-radius: 14px`.
Focus: `outline: 3px solid var(--stark-beacon)` (Caution Beacon — accessibility compliant).

### Cards

**`.stark-card`** — Glass morphism card with radial gradient overlay:
- `background: rgba(255,255,255,0.78)`, `backdrop-filter: blur(14px)`
- `border: 1px solid rgba(0,0,0,0.06)`, `border-radius: 14px`
- Hover: `translateY(-2px)`, deeper shadow

**`.stark-halo-card`** — Editorial signal rows (not interactive):
- No background, no border, no box-shadow
- Left dot: `5px` circle, Sky Signal at `0.55` alpha with halo ring
- Hairline divider between items
- `cursor: default` — explicitly non-interactive

**`.stark-process-card`** — Process/steps:
- White background, subtle border, numbered step badge

**`.stark-outcome`** — Results/outcomes:
- White, hover border accent (Anchor at `0.18` alpha)

### Navigation

**`.stark-nav`** — Hero overlay nav (white text)
**`.stark-sticky-nav`** — Sticky nav, appears on scroll:
- `background: rgba(255,255,255,.90)`, `backdrop-filter: blur(10px)`
- Scroll progress bar via `--stark-scroll` CSS variable
- Velocity glow effect via `--stark-vel`

**Off-canvas menu** (mobile): Dark glass, `rgba(10,56,120,0.58)` → `rgba(5,34,84,0.78)` gradient, `blur(14px)`.
Menu items: `border-radius: 14px`, hover shifts `translateX(2px)`.

### CTA Section

`.stark-cta` — Anchor gradient background
Two-column grid: copy left, actions right (stacks on mobile).
Primary button: Pulse gradient. Secondary: transparent with white border.

### FAQ

`.stark-faq-item` — List items with left dot, hover background tint.
Dot animates on hover: Sky Signal at `0.7` alpha fill.

---

## Signature Features — DO NOT REMOVE

These are defining UI moments that must survive the Phase 2 migration exactly as they work today. They are non-negotiable design decisions.

### 1. Scroll Progress Bar
A thin blue line that runs edge-to-edge along the bottom of the sticky nav, growing from left to right as the user scrolls down the page.

- Only visible when the sticky nav is in frosted glass state (scrolled)
- Color: `color-mix(in srgb, var(--stark-accent) 78%, white 0%)`
- Height: `2px` desktop / `3px` at `≥ 900px`
- Driven by `--stark-scroll` CSS variable (0 to 1) set via scroll event listener
- Velocity glow: as scroll speed increases, the bar glows via `--stark-vel` variable
- Transition: `120ms linear` — feels live, not laggy
- Implementation: CSS `scaleX(var(--stark-scroll))` on `::after` pseudo-element of `.x-bar-content`
- Reduced motion: transition disabled, bar still shows statically

### 2. A11y Button + Back-to-Top Choreography
Two fixed-position utility buttons that perform a coordinated entrance as the user scrolls.

**A11y button (`.stark-a11y-btn`):**
- Labeled "A11y" — accessibility settings shortcut
- Fixed to bottom-left, starts at `left: 10px`
- As user scrolls to 50% of page, it smoothly slides right to `left: 64px` — making room for the back-to-top button to appear beside it
- Movement eased with `cubic-bezier(.22,.61,.36,1)`
- Icon: accessibility symbol

**Back-to-top button (`.x-scroll-top`):**
- Fixed bottom-right
- Hidden off-screen (`translateX(-18px)`, `opacity: 0`) until user scrolls
- Slides in from the left with `opacity: 1` when triggered
- Pure CSS chevron pointing up (no icon font dependency)
- Subtle `starkChevronNudge` animation pulses every 4.2s to hint at the action
- Hover: chevron turns Sky Signal, button lifts `translateY(-2px)`
- Both buttons share the same glass morphism style: `rgba(255,255,255,.62)`, `backdrop-filter: blur(10px)`, `border-radius: 14px`

**The choreography:** A11y slides left → gap opens → back-to-top slides into the gap. Feels intentional, not accidental.

---

## Interaction Patterns

- **Hover lift:** `translateY(-2px)` — universal for cards, buttons
- **Hover press:** `translateY(0) scale(0.99)` — button active state
- **Transition timing:** `180ms cubic-bezier(.2,.8,.2,1)` — standard
- **Fast transitions:** `160ms ease` — buttons, menu items
- **Reveal animation:** `.stark-reveal` + IntersectionObserver, `threshold: 0.15`
- **Tilt effect:** `.stark-tilt` — `rotateX/Y(±3deg)` on mousemove, clamped
- **Always:** `@media (prefers-reduced-motion: reduce)` — disable all motion, `transition: none`

---

## Design Principles (Nathan's Aesthetic)

- **Apple-minimal:** Clean hairlines, generous whitespace, gray secondary text
- **No bold color bars** as decorative elements
- Glass morphism is used for depth, not decoration — always with purpose
- Radial gradient overlays on cards create material quality without complexity
- Typography does the work: tight letter-spacing, large weight contrast (400 body / 800 headings)
- Seasonal accents: subtle shifts, never jarring, always within the blue family
- CTA sections are the exception: full Anchor gradient, this is intentional contrast

---

## Code Standards

### PHP
- All functions prefixed `stark_`
- Hooks use anonymous functions for single-use; named functions for anything hooked in multiple places
- Transient caching for any DB queries in frontend output (key: `stark_{descriptor}_{id}`)
- Never store API keys in `functions.php` — use `wp-config.php` constants
- All DB queries use `$wpdb->prepare()`
- File includes in `~/public_html/` subdirectories only (not root — CodeIgniter routing blocks root PHP)

### CSS
- All custom properties on `:root`; seasonal overrides via `html[data-stark-season]`
- `!important` is acceptable in child theme overrides of Theme.co — will be removed in Phase 2
- Prefix all component classes with `stark-`
- BEM-lite: `.stark-component__element` for sub-elements when needed
- Never override WordPress core admin styles from frontend CSS
- **Phase 2 variable names** are canonical: `--stark-anchor`, `--stark-pulse`, `--stark-signal`, etc. Phase 1 names remain in production WPCode CSS until cutover.

### JavaScript
- All JS wrapped in IIFE `(function(){})()` or arrow IIFE
- Check `prefers-reduced-motion` before any animation
- Use `{ passive: true }` on all scroll/touch event listeners
- IntersectionObserver preferred over scroll listeners for reveal
- Builder/preview detection: check for `.cs-preview` or `#cs-content` before running motion JS

### WordPress
- All CPTs registered in `functions.php` (not plugins) unless complex enough to warrant a plugin
- Conditionally enqueue scripts/styles — nothing global unless truly needed everywhere
- WPCode for global CSS/JS injection (Phase 1); child theme enqueue (Phase 2)

---

## Email / Notification Design

- **Layout:** Div-based, no tables (avoids `wpautop` injection)
- **Dark mode:** Tested in Apple Mail dark mode — no table cells that invert
- **Spark:** Tested — no background-color conflicts
- **Logo path:** `hub.starksocial.com/media/public/custom/emails/stark-logo-light.png`
- **Footer bar:** `hub.starksocial.com/media/public/custom/emails/email-footer-bar.png`
- Match Perfex Hub template styling: Anchor header, white body, footer bar

---

## Voice & Tone

- **Direct:** No filler, no "we're passionate about..." — say what you do
- **Confident without ego:** State outcomes, not superlatives
- **Human:** Write like a smart person talking, not a brochure
- **Agency, not freelancer:** "We" not "I" — team language
- **Proof over promises:** Lead with results where possible ("clients sending emails")
- **Accessible:** Plain English — no jargon unless the audience expects it

### Copy Patterns
- Eyebrows: SHORT CAPS, 2–4 words, no period
- Headlines: Sentence case, no period, tight letter-spacing
- Body: 2–3 sentences max per paragraph on marketing pages
- CTAs: Action verb first — "Get a proposal", "See our work", "Start here"

---

## Accessibility

- WCAG 2.1 AA target (currently scoring 95 on PageSpeed)
- Focus ring: `3px solid var(--stark-beacon)` (Caution Beacon) — consistent site-wide
- All interactive elements: `min-height: 44px`, `min-width: 44px` on mobile
- All images: descriptive `alt` text
- Heading hierarchy: never skip levels
- Color contrast: all text on backgrounds meets 4.5:1 minimum
- Reduced motion: all animations gated on `prefers-reduced-motion`

---

## File Structure (Phase 2 Child Theme)

```
starksocial/
├── style.css           ← Theme declaration only
├── functions.php       ← All PHP hooks, CPTs, shortcodes
├── custom.css          ← Global design system CSS (ported from WPCode)
├── fonts/              ← Barlow WOFF2 files
│   ├── barlow-v13-latin-regular.woff2
│   ├── barlow-v13-latin-500.woff2
│   ├── barlow-v13-latin-600.woff2
│   ├── barlow-v13-latin-700.woff2
│   ├── barlow-v13-latin-800.woff2
│   ├── barlow-condensed-v13-latin-regular.woff2
│   ├── barlow-condensed-v13-latin-500.woff2
│   ├── barlow-condensed-v13-latin-600.woff2
│   └── barlow-condensed-v13-latin-700.woff2
├── js/
│   ├── stark-global.js ← Season, scroll progress, velocity
│   ├── stark-reveal.js ← IntersectionObserver reveal + tilt
│   └── author-box.js   ← Author login attribute
├── img/                ← Theme-specific SVGs
├── passgen/            ← Password generator (rebuild in Phase 2)
└── framework/          ← Custom template views
    └── views/custom/
        └── wp-404.php
```

---

## Feature Decisions & Scope

### 1. Password Generator (Rebuild in Phase 2)
**Current:** Pill-shaped form, doesn't match site design
**Decision:** Rebuild to match Stark design system — `stark-card` container, `--stark-radius-field` inputs, Barlow font, brand colors. Remove pill shapes. Should feel like a native part of the site, not a widget drop-in.
**Location:** `/password-generator/` — keep as shortcode `[password_generator]`, just rebuild the CSS
**Owner:** Claude — Build

---

### 2. Live Chat (Olark → Evaluate)
**Current:** Olark free tier — button style is good, service is adequate. Also integrated with Perfex Hub.
**Decision:** Evaluate before committing to Olark Pro.

**Options to consider:**
| Service | Pro | Con |
|---|---|---|
| **Olark Pro** | Already integrated with Hub, familiar | Older product, limited AI features |
| **Crisp** | Modern, free tier generous, good design | No native Perfex integration |
| **Tidio** | AI features, clean UI | Can get expensive |
| **Tawk.to** | Free forever | Design less polished |

**Recommendation:** If Hub integration is important, pay for Olark Pro. If Hub integration isn't critical for chat, Crisp is the better modern product.
**Keep current Olark styling** from global CSS regardless of which service is chosen — the button style is already on-brand.
**Decision needed by:** Before Phase 2 launch

---

### 3. Podcast (Podlove → Evaluate)
**Current:** Podlove Podcasting Plugin — hasn't been updated recently. Audio files on AWS S3.
**Status:** Podcast on pause, considering relaunch.

**Options:**
| Option | Pro | Con |
|---|---|---|
| **Keep Podlove** | Already set up, AWS connected, CPT working | Plugin aging, less active development |
| **Seriously Simple Podcasting** | Active development, clean UI, good RSS | Migration work |
| **Castos** | Hosted platform, great analytics, WordPress plugin | Monthly cost, moves files off AWS |
| **Buzzsprout embed** | Simple, reliable | Loses on-site player control |

**Recommendation:** Keep Podlove for now since podcast is on pause. When relaunching evaluate Seriously Simple Podcasting — active development, better WordPress integration. Audio stays on AWS either way.
**AWS S3 bucket:** Already configured, keep regardless of plugin choice.

---

### 4. Blog Audio Player — ElevenLabs AI Voice (Phase 2 Feature)
**Current:** Audio files manually created in Speechify, hosted on AWS S3, embedded in blog posts. Labor intensive.
**Decision:** Replace manual Speechify workflow with ElevenLabs API — auto-generate audio from blog post content using Nathan or Deanna's cloned voice.

**Proposed workflow:**
1. Blog post published/updated in WordPress
2. WordPress hook triggers ElevenLabs API call with post content
3. ElevenLabs generates MP3 using cloned voice
4. MP3 uploaded to existing AWS S3 bucket automatically
5. Player on blog post pulls from S3 URL (same as today)

**Voice cloning:** ElevenLabs Professional Voice Clone — requires ~30 min of clean audio. Podcast episodes are ideal source material for both Nathan and Deanna.
**ElevenLabs plan needed:** Creator ($22/mo) minimum for voice cloning
**AWS:** Keep existing bucket and structure
**Owner:** Claude — Build (WordPress hook + API integration)
**Priority:** Phase 2 — high value, replaces manual labor entirely

---

### 5. Unified Audio Player (Blog + Podcast)
**Decision:** Both blog and podcast use the same base player component with different feature sets.

**Blog player (simple):**
- Play/pause, progress bar, speed control, volume
- "Listen to this post" label
- Matches `.stark-card` glass morphism style

**Podcast player (full):**
- All blog player features PLUS
- Chapter markers
- Transcript toggle (collapsible below player)
- Episode number + duration display
- Download button
- Subscribe links (Spotify, Apple, etc.)

**Design:** Same visual language — dark glass `rgba(10,16,28,.92)`, Sky Signal progress bar, Barlow font, `border-radius: 18px`. Feels like one product at two feature levels.
**Owner:** Claude — Build
**Note:** If switching from Podlove, verify chapter + transcript support in new plugin before committing.

---

### 6. Support Ticket Button → Perfex Hub
**Decision:** Add "Open a Support Ticket" as a utility action that redirects to `hub.starksocial.com/clients/open_ticket`

**Behavior:**
- Clicking opens `https://hub.starksocial.com/clients/open_ticket` in a new tab
- Perfex Hub handles authentication — existing clients log in, non-clients see login/register
- No custom auth needed on the WordPress side

**Placement:**
- Support page (`/support/`) — primary CTA
- Knowledgebase pages — sidebar or footer of articles
- Utility nav (top right alongside Client Portal)
- Mobile menu — utility section at bottom

**Button style:** `.stark-btn.blue-btn` — matches Client Portal button treatment
**Label:** "Open a Support Ticket"
**Icon:** ticket or message icon
**Owner:** Claude — Build

---

## Page-by-Page Visual Reference (From Live Site Screenshots — April 2026)

### Homepage Hero
- Full-bleed cloud photo background
- All-caps white headline (keep weight and drama)
- Yellow all-caps subtitle — **Phase 2: replace with white/muted subtitle, sentence case** — yellow all-caps conflicts with Apple-minimal direction
- Single Spark CTA button — keep color, **replace pill shape with `--stark-radius-btn` (12px)**
- Transparent nav state on load, logo top-left, burger top-right

### Homepage — Who We're For Section
- Two-column: copy + H2 left, halo bullet points right
- Halo dots: Sky Signal at `0.55` alpha with ring — keep exactly
- Service cards: 2×2 grid, icon + title + description — this is the reference layout for Phase 2 service card pattern

### Scroll Progress Bar (Confirmed Live)
- Thin blue line visible under sticky nav when scrolled
- Runs full width edge to edge ✅
- Must be pixel-perfect match in Phase 2

### Blog Single — Audio Player
**Current state:**
- White card container, subtle border
- Label: "Listen to this article"
- Timestamps left (current) and right (total, negative format e.g. -5:53)
- Large play button center
- ±15 second skip buttons
- Speed control pill (1x dropdown)
- Download audio link with download icon

**Phase 2 upgrade:**
- Replace white card with dark glass: `rgba(10,16,28,.92)`, `backdrop-filter: blur(22px)`
- Sky Signal progress bar
- Keep same controls, same layout
- Remove pill shape from speed control — use `--stark-radius-btn`
- "Listen to this article" label in eyebrow style (small caps, muted)
- ElevenLabs AI voice integration replaces manual Speechify workflow

### Podcast Single — Podlove Player
**Current state:**
- Episode art square left
- Episode title + podcast name
- Blue pill "Play Podcast" button
- Progress bar with timestamps
- Chapters button, download button, share button (icon row)
- Chapters panel: numbered list, chapter name, timestamp, X to close

**Phase 2 upgrade:**
- Same feature set, unified visual language with blog player
- Dark glass container matching blog player
- **Replace blue pill Play button** with `stark-btn blue-btn` style
- Keep chapters panel — it's a strong feature, just restyle to dark glass
- Transcript toggle to be added alongside chapters button

### Password Generator
**Current state (keep layout, fix styles):**
- Two-column: copy/tips left, widget right — **keep this layout**
- Widget: COPY PASSWORD button (top-right), password output area, length slider, toggle switches, GENERATE PASSWORD button

**What's wrong:**
- Pill-shaped iOS-style toggle switches — replace with Stark-styled checkboxes or squared toggles with `--stark-radius-ui`
- COPY PASSWORD and GENERATE PASSWORD buttons are pill-shaped — replace with `stark-btn` style
- Widget background too plain — upgrade to `stark-card` glass morphism

**Phase 2 target:**
- `stark-card` container for the widget
- Slider: Sky Signal `--stark-accent` thumb and fill
- Toggles: replace pill iOS style — use styled checkboxes or small `--stark-radius-btn` toggles in Anchor
- Buttons: `.stark-btn.blue-btn` for Generate, ghost/secondary for Copy
- Generated password output: monospace font, Pulse to match existing `code` element style

### Footer Structure (Current → Phase 2)
**Current columns:** Logo+tagline | SERVICES | COMPANY | RESOURCES

**Phase 2 columns:** Logo+tagline | SERVICES (all 7) | COMPANY | RESOURCES + Support Ticket

**SERVICES column update (add new services):**
- Social Media Management
- Paid Advertising
- Web Design
- SEO
- Content Creation
- Brand Strategy & Identity
- Audit & Consulting ← NEW

**RESOURCES column update:**
- Knowledgebase
- Password Generator
- Client Portal
- Blog
- Podcast
- Open a Support Ticket ← NEW (links to `hub.starksocial.com/clients/open_ticket`)

**Social icons:** Facebook, Instagram, LinkedIn, YouTube, Apple Podcasts — keep all five

### CTA Section (Global)
- Anchor gradient background — keep
- "Start a conversation" headline — good, keep
- Subtext: "Tell us what you're working on and we'll help you think it through. No pressure. If we're not a fit, we'll tell you." — keep this copy, it's very on-brand
- Spark Contact Us button — keep color, fix pill shape to `--stark-radius-btn`

---

## Annotated Feature Notes (From Nathan — April 2026)
*These notes come directly from annotated screenshots and override any earlier general descriptions.*

### Password Generator — Exact Notes
- **Bigger widget** — the right-column widget needs to be larger and fill more space
- **More spacing, more intentional** — generous padding inside the widget, don't crowd the controls
- **Copy button + form = one unified thing** — COPY PASSWORD button should be integrated into the password output field, not floating above it separately. Think: output field with copy icon/button embedded on the right end — one component
- **No pill shapes anywhere** — toggles, buttons, all of it. Glass feel throughout
- **Glass buttons** — `.stark-btn` glass morphism style for Generate and Copy. Not flat, not pill — frosted glass with subtle border and depth
- **Toggles** — replace iOS pill toggles with Stark-styled checkbox buttons or small glass toggle chips using `--stark-radius-btn`

### Blog Audio Player — Exact Notes
- **Love this look** — use the current blog player visual as the reference for the unified player design across the site
- **AI voice via ElevenLabs** — no more manual Speechify. Auto-generate on publish
- **Voice selection logic: based on post author**
  - Nathan wrote the post → Nathan's cloned voice
  - Deanna wrote the post → Deanna's cloned voice
  - Override field in post meta for edge cases
- **Avatar for each voice** — small circular avatar appears in the player next to "Listen to this article"
  - Nathan's avatar: his photo/headshot cropped to circle
  - Deanna's avatar: her photo/headshot cropped to circle
  - Sits left of the label, gives the player a human, personal feel
- **Label update:** "Listen to this article · Nathan" or "Listen to this article · Deanna" — identifies whose voice is reading

### Podcast Player — Exact Notes
- **Chapters: collapsible, not auto-open** — chapters panel should be closed by default, opened on tap/click. Currently opens automatically which takes up too much space on load
- **Play button: consistent site-wide button style** — the Anchor pill "Play Podcast" button needs to become `.stark-btn.blue-btn` to match every other button on the site. This is the site-wide button standardization note — applies everywhere
- **Sidebar search = site-wide form standard** — the sidebar search box style (white glass, rounded, focus ring) is the reference style for ALL forms: password generator, contact form, gravity forms, any input anywhere on the site
- **Categories sidebar widget** — should adopt the blog player's visual language: darker background treatment, cleaner typography. Currently too plain with grey counts

### Scroll Progress Bar — Exact Behavior (Confirmed)
- Grows from left edge → right edge as user scrolls
- **Touches both edges** — full viewport width, edge to edge, no padding/margin
- Moves in real time as user scrolls
- At 100% scroll = fully touches right edge

### A11y + Back-to-Top Choreography — Exact Behavior (Confirmed)
**Starting state (page load):**
- A11y button: far bottom-left corner
- Back-to-top: hidden

**As user scrolls:**
- A11y button slowly slides right
- Scroll progress bar grows left to right

**Arriving state (mid-to-bottom of page):**
- Back-to-top button slides IN from the right into position
- Back-to-top button looks identical to A11y button — same glass style, same size, same border radius
- Both buttons sit bottom-left together: [↑] [A11y]
- Back-to-top is leftmost, A11y is to its right

**Both buttons share identical styling:**
- `background: rgba(255,255,255,.62)`
- `backdrop-filter: blur(10px) saturate(140%)`
- `border: 1px solid rgba(17,17,17,.10)`
- `border-radius: 14px`
- `width: 44px` / `height: 44px`
- `box-shadow: 0 16px 34px rgba(17,17,17,.14)`

### Site-Wide Form Standard (From Sidebar Search Reference)
All forms, inputs, and search fields across the site use this style:
- `background: rgba(255,255,255,0.92)`
- `border: 1px solid rgba(0,0,0,0.10)`
- `border-radius: var(--stark-radius-field)` (14px)
- `box-shadow: 0 0.75rem 1.75rem rgba(0,0,0,0.08)`
- Focus: `border-color: rgba(48,127,226,0.55)`, `box-shadow: 0 0 0 0.25rem rgba(48,127,226,0.18)`
- Applies to: contact form, password generator inputs, sidebar search, all Gravity Forms inputs, knowledgebase search

---

## Bottom UI Cluster — Locked Behavior (v2.0.1, April 24 2026)

Three bottom-fixed elements. Two form a pair on the left, one stands alone on the right.

### A11y button (bottom-left, always visible)
- Rounded-square glass pill, 44×44, radius 14
- Rest: `left: 10px`, bottom `clamp(14px, 2vw, 22px)`
- Active (past 50% scroll): `left: 64px`
- Transition: 360ms cubic-bezier(.2, .8, .2, 1)
- Links to `/support/accessibility-statement/`
- Label: "A11y" in 11px/600/letter-spacing 0.06em

### Back-to-top button (bottom-left, appears at 50% scroll)
- Identical glass pill to A11y (same size, radius, glass)
- Hidden at rest: `opacity: 0`, `translateY(18px)`, `pointer-events: none`
- Active: slides up from below into `left: 10px`
- Shows when scroll ≥ 50%; hides below 40% (10% hysteresis)
- CSS-drawn chevron-up glyph (no SVG asset)
- Click: interrupt-safe scroll to top
  - Reduced motion: instant
  - Desktop: native smooth
  - Touch: custom RAF ease-out, aborts on touchstart or wheel

### Chat bot (Perfex PRChat, bottom-right, always visible)
- **Squircle** (asymmetric border-radius: `42% 58% 52% 48% / 45% 45% 55% 55%`) — intentionally distinct from A11y/back-to-top's rounded squares
- 58×58 desktop, 52×52 mobile ≤480px
- Sky Signal fill (`#307FE2`), white icon
- Entrance: slide from right 400ms after page load, 620ms duration
- Attention: single pulse ring (2px Sky Signal border, scale 1→1.8, opacity fade) at 3020ms. Never repeats.
- Pulse suppressed when `.prchat-widget-open` or `[aria-expanded="true"]` set
- Position: `right: 16px`

### State coordination
- JS file: `js/stark-bottom-ui.js`
- Sets `data-stark-cluster="rest|active"` on `<html>` based on scroll
- Writes `--stark-progress` (0..1) as CSS custom property for any other consumer
- Reduced motion: all transitions and animations suppressed; state changes instant

### Mobile scroll guidance (site-wide)
- `html { scroll-behavior: auto; }` — no CSS smooth scroll anywhere
- Any programmatic scroll (back-to-top, anchor jumps, etc.) must go through JS that:
  1. Instant-jumps if `prefers-reduced-motion`
  2. Uses native `scrollTo({behavior: 'smooth'})` on non-touch devices
  3. Uses RAF ease-out scroll on touch devices, aborting on `touchstart` or `wheel`
- This policy prevents the iOS momentum-vs-programmatic-smooth fight that causes jank
- Touch detection: `matchMedia('(hover: none) and (pointer: coarse)')`

### What this replaces from Phase 1
- Themeco Pro's `.x-scroll-top.left` → `.stark-back-to-top`
- Olark's `.olark-launch-button` → Perfex PRChat injected via embed
- A11y's inline `style="left: 64px"` written by legacy JS → CSS-driven via `data-stark-cluster`
