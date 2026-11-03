# Staging audit after v27.5.0–v27.5.9 (November 3 2026, 11:15 PST)

Full staging deploy at 11:09 (`tools/deploy/README.md`), then Lighthouse (mobile + desktop) and axe
(390 + 1440) on 18 pages via `run.mjs` (headless Chrome). `summary.json` / `axe-summary.json` are
the full runs; `summary-rerun.json` re-ran 3 pages after the v27.5.10 fix.

**axe: 0 violations on all 18 pages × 2 widths.** Accessibility 100 and Best Practices 100 everywhere.
SEO 66 is staging's `noindex` (by design). The 404 page reports 0s because Lighthouse doesn't
score 404 responses (axe on it: 0).

| Page | Mobile perf | Desktop perf | A11y | BP | SEO |
|---|---|---|---|---|---|
| `/` | 96 | 100 | 100/100 | 100/100 | 66 |
| `/services/` | 94 | 99 | 100/100 | 100/100 | 66 |
| `/seo/` | 96 | 99 | 100/100 | 100/100 | 66 |
| `/content-creation/` | 96 | 100 | 100/100 | 100/100 | 66 |
| `/about/` | 97 | 100 | 100/100 | 100/100 | 66 |
| `/contact/` | 81 | 99 | 100/100 | 100/100 | 66 |
| `/support/` | 95 | 100 | 100/100 | 100/100 | 66 |
| `/support/knowledgebase/` | 94 | 98 | 100/100 | 100/100 | 66 |
| `/support/password-generator/` | 97 | 99 | 100/100 | 100/100 | 66 |
| `/work/` | 94 | 99 | 100/100 | 100/100 | 66 |
| `/portfolio/pathway-escrow/` | 96 | 99 | 100/100 | 100/100 | 66 |
| `/service-areas/` | 96 | 100 | 100/100 | 100/100 | 66 |
| `/blog/` | 88 | 99 | 100/100 | 100/100 | 66 |
| `/planning-notes-santa-clarita-web-design-seo/` | 90 | 98 | 100/100 | 100/100 | 66 |
| `/stark-social-wins-excellence-in-web-design-award-for-valenc` | 84 | 63 | 100/100 | 100/100 | 66 |
| `/podcast/does-your-biz-really-need-a-video-app/` | 81 | 99 | 100/100 | 100/100 | 66 |
| `/docs/what-is-seo-and-how-long-does-it-take/` | 87 | 94 | 100/100 | 100/100 | 66 |

Best Practices is **100** this run (81–82 in the v27.4.x audit): Cloudflare's JavaScript
Detections script wasn't injected on these requests. It may come back; see
`tools/audits/known-tradeoffs.md`.

## Regression found and fixed (v27.5.10)

The award post (`/stark-social-wins-…-valencia-jazz-blues/`) scored 63 desktop and had CLS 0.194
on mobile:
- **CLS:** the v27.5.8 touch-target rule included `.stark-article__meta a` (hero byline). The
  critical CSS doesn't have it, so when the full stylesheet arrived the byline changed height and
  pushed the featured image (measured with a layout-shift observer: 0.186 from that one change).
  Removed that selector. Re-run: CLS 0
- **Image weight:** the restored screenshots (v27.5.1) had no `sizes`, so browsers fetched the
  1024px file for a ~240px slot (421 KB wasted on desktop). Added `sizes` + lazy loading.
  Re-run: desktop **98**, LCP 1.0 s

## Mobile performance variance

Mobile scores on staging move ±8 between runs of the same code (home: 96 in the full run, 88 in
the re-run) because staging's server response time varies (0.7–1.7 s for the HTML document across runs). Desktop is stable at 94–100. Recurring opportunities,
none new: unused CSS (~27 KB of the single stylesheet), legacy JS (~10 KB, plugins), and
responsive images on blog cards.
