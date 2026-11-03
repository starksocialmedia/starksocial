# Audits

Lighthouse 12 (desktop + mobile presets) and axe-core 4 (WCAG 2.0/2.1 A + AA +
best-practice, at 390px and 1440px) against Local, run through headless Chrome.
The runner lives outside the repo (scratchpad `audit/run.mjs`, `lighthouse`,
`axe-core`, `puppeteer-core`); re-create it from the notes below if needed.

## 2026-11-02 — v27.1.1

Pages: `/`, `/web-design/`, `/blog/`, `/portfolio/pathway-escrow/`.

`lighthouse-2026-11-02/` has the HTML reports and `summary.json` (after the
fixes). `summary-before.json` is the baseline. `axe-2026-11-02/` has one JSON per page and width.

### Scores (Perf / A11y / Best practices / SEO)

| Page | Mobile before → after | Desktop before → after |
|---|---|---|
| `/` | 99 / 96 / 96 / 92 → 99 / 96 / 96 / 92 | 100 / 96 / 96 / 92 → 100 / 96 / 96 / 92 |
| `/web-design/` | 97 / 96 / 96 / 92 → 97 / 96 / 96 / 92 | 100 / 96 / 96 / 92 → same |
| `/blog/` | 98 / 94 / 96 / 92 → 98 / 96 / 96 / 92 | **86** / 94 / 96 / 92 → **100** / 96 / 96 / 92 |
| `/portfolio/pathway-escrow/` | 99 / 96 / 96 / 92 → same | 100 / 96 / 96 / 92 → same |

Accessibility stays at 96 only because of button contrast (below). axe went from 4
rule types and about 100 failing elements in total to 1 rule type with 1–3 per page.

### Fixed (quick)

| Issue | Where | Fix |
|---|---|---|
| Small blue text at 3.99:1 (3.76:1 on paper-soft); AA needs 4.5:1 | eyebrows, "Learn more", case-study links, post-card categories, prose links, author / team roles, work-card meta, episode numbers… | New token `--stark-blue-ink: #2A70CC` (4.9:1 / 4.61:1), the closest shade of the brand blue that passes. Small text only — large numbers, icons and buttons keep `#307FE2` |
| `label-content-name-mismatch`: A11y button shows "A11y" but is named "Accessibility Statement and Support" | every page | Name is now "A11y: Accessibility Statement and Support" |
| `region`: A11y / back-to-top cluster and draft banner outside landmarks | every page | Cluster wrapped in `<aside aria-label="Accessibility and page tools">` (`display: contents`, so no layout change); draft banner is an `<aside>` |
| `heading-order`: card `h3`s straight after the hero `h1` | `/blog/` | Visually hidden `h2` "Posts" labels the post list |
| CLS 0.276: Barlow 500 (nav, category chips) wasn't preloaded; on swap the chips rewrapped from 2 lines to 1 and pushed the grid up | `/blog/` desktop | Preload `barlow-v13-latin-500.woff2`. CLS now 0 |
| `lcp-lazy-loaded`: first post card image (the LCP) was `loading="lazy"` | `/blog/` | First row of the main archive loop loads eagerly, first image `fetchpriority="high"` |
| Card images requested at 768px for a ~410px slot | `/blog/` | `sizes="(min-width: 1024px) 420px, (min-width: 640px) 50vw, 100vw"` |

Checked and fine: `lang` attribute, image alt text (decorative card images use `alt=""`,
the title follows), form labels, icon button labels, focus-visible states.

### Not fixed — see BUILDPLAN Phase 2.7

| Issue | Category | Notes |
|---|---|---|
| **White on `#307FE2` buttons = 3.99:1** (primary buttons, nav "Client Portal") | Needs design decision | Fails AA for 16px text. Options: darken the button to `#2A70CC` (4.9:1), or keep the brand blue and make button text 18.66px+ bold (AA large text, 3:1). The only remaining axe failure |
| `meta-description` missing | Priority 7 (Rank Math) | Rank Math templates set descriptions |
| `uses-long-cache-ttl` | Server | Local's nginx. Cloudways + Cloudflare set cache headers on staging/live; recheck there |
| `unused-css-rules` / `unminified-css` (~13 KB / 5 KB) | Medium | `custom.css` is one file for all templates; a minified build or per-template split is post-launch |
| `render-blocking-resources` (~400 ms, mobile) | Medium | GeneratePress + plugin CSS; candidates are Complianz and BetterDocs CSS on pages that don't use them |
| `inspector-issues`: third-party cookie warning | Third party | Perfex chat widget (`hub.starksocial.com`) — needs `SameSite=None; Secure` on that server |
| `/web-design/` mobile CLS 0.1 under Lighthouse throttling | Watch | Not reproducible unthrottled (0). Recheck on staging |

## CSS regression check — `style-fingerprint.mjs`

Hashes the computed style (plus `::before` / `::after` content) of every element
on 21 key pages at 390px and 1440px. Run it before and after a CSS refactor and
compare: any element whose hash changed renders differently. Two consecutive
runs with no change produce identical output (checked November 2 2026), so any
difference is real.

```
# needs headless Chrome on :9333 and puppeteer-core
node style-fingerprint.mjs before.json   # old CSS
node style-fingerprint.mjs after.json    # new CSS
```

Used for v27.1.6 (custom.css reorganisation): 15,046 elements, 0 changed.
