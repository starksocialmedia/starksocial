# Staging audit: v27.4.10 (November 3 2026)

Full deploy of v27.4.10 to https://staging.starksocial.com, then Lighthouse 12 (mobile + desktop)
and axe-core 4 (390 + 1440 px) on 16 URLs. `summary.json` = Lighthouse, `axe-summary.json` = axe,
three full reports (home, service areas, web design, mobile).

| Page | Mobile perf | Desktop perf | A11y | Best practices | axe |
|---|---|---|---|---|---|
| Home | 97 | 100 | 100 | 82 / 81 | 0 |
| Services | 94 | 100 | 100 | 82 / 81 | 0 |
| Web Design | 96 | 100 | 100 | 82 / 81 | 0 |
| SEO | 97 | 100 | 100 | 82 / 81 | 0 |
| Service Areas (new hub) | 95 | 100 | 100 | 82 / 81 | 0 |
| About | 97 | 99 | 100 | 82 / 81 | 0 |
| Contact | 86 | 99 | 100 | 82 / 81 | 0 |
| Work | 93 | 100 | 100 | 82 / 81 | 0 |
| Pathway Escrow | 89 | 100 | 100 | 82 / 81 | 0 |
| Blog | 94 | 99 | 100 | 82 / 81 | 0 |
| A blog post | 86 | 99 | 100 | 82 / 81 | 0 |
| Podcast | 88 | 99 | 100 | 82 / 81 | 0 |
| Episode (meerkat GIF → MP4) | 91 | 99 | 98 | 82 / 81 | heading-order (old episode content: an h4 after h2) |
| KB article (v27.4.0 landmarks) | 81 | 92 | 100 | 82 / 81 | 0 (was 4 rules) |
| Search (facets) | 97 | 99 | 100 | 82 / 81 | 0 |
| 404 | — | — | — | — | 0 (Lighthouse won't audit a 404 response) |

**SEO** is 58–66 everywhere only because staging is noindex (`is-crawlable`). `/service-areas/`
and search also lacked a meta description: fixed in v27.4.11 (theme deploy).

## Best practices 100 → 81/82 is not the theme

Both "deprecations" warnings come from **Cloudflare's injected bot-detection script**
(`/cdn-cgi/challenge-platform/scripts/jsd/main.js`: FencedFrame, `StorageType.persistent`). It
wasn't on the page in yesterday's run (v27.3.20: 100 everywhere). A Cloudflare bot setting
(Bot Fight Mode / JavaScript detections) is injecting it, possibly triggered by the volume of
headless requests. Nathan: check Security → Bots in Cloudflare (and Cloudways' Cloudflare add-on).
If it's on for live too, live's best-practices score will show the same.

## Compared with v27.3.20 (yesterday)

Mobile performance is within normal variance (home 97 = 97, services 92 → 94, web design 88 → 96,
Pathway 95 → 89 with a 1.3 s uncached server response, contact 86 = 86). Accessibility unchanged
at 100 except the episode's old content heading. No regressions from v27.4.x.

KB article mobile 81: a 1.5 s uncached server response plus BetterDocs' own CSS/JS (first time
measured on staging; not a regression).
