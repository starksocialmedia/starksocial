# Lighthouse — staging, v27.3.13 (November 2 2026)

8 pages × mobile/desktop against https://staging.starksocial.com after the cache-proof asset
fix (the first run measured an August `custom.css` served by Cloudflare). `summary.json` has
every run; the homepage HTML reports are kept as samples.

| Page | Mobile P / A / BP | Desktop P / A / BP | Local (mobile / desktop P) |
|---|---|---|---|
| `/` | 97 / 100 / 100 | 100 / 100 / 100 | 98 / 100 |
| `/services/` | 92 / 100 / 100 | 100 / 100 / 100 | — |
| `/web-design/` | 97 / 100 / 100 | 99 / 100 / 100 | 96 / 100 |
| `/blog/` | 86 / 100 / 100 | 99 / 100 / 100 | 97 / 100 |
| a blog post | 84 / 100 / 82 | 98 / 100 / 81 | — |
| `/portfolio/pathway-escrow/` | 90 / 100 / 82 | 100 / 100 / 81 | — |
| `/contact/` | 90 / 100 / 82 | 100 / 100 / 81 | 96 / 100 |
| podcast episode | **65** / 100 / 82 | **87** / 100 / 81 | — |

SEO is 66 on every staging page **by design**: the staging guard sets noindex.

## Staging-specific findings

- **Accessibility 100 everywhere** (was 96 when stale CSS was served)
- **`server-response-time`** on mobile runs (TTFB 0.6–1.5 s): uncached staging PHP, and Breeze / Object Cache Pro are off on staging. Expect better on live with caching on. Main reason mobile is 5–11 points below Local
- **Best practices 81–82** on pages where the Perfex chat widget loaded: third-party cookies from hub.starksocial.com (US opt-out = loads by default). Fix: `SameSite=None; Secure; Partitioned` on the Perfex server
- **Podcast episodes**: Podlove Web Player costs about 770 ms TBT on mobile and **CLS 0.19** on desktop (the player renders at 900–1,100px tall after load; height depends on the chapter list). Fix: a click-to-load player (native `<audio>` first, Podlove on demand) — Phase 2.1
