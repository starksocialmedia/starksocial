# Lighthouse — staging, v27.3.20 (November 2 2026, 22:50)

8 pages × mobile / desktop against https://staging.starksocial.com. `summary.json` has every run;
the homepage HTML reports are kept as samples. Earlier runs this session measured an August
`custom.css` (Cloudflare, fixed v27.3.13) and then fonts blocked by CORS (fixed v27.3.20).
These are the clean numbers.

| Page | Mobile P / A / BP | Desktop P / A / BP |
|---|---|---|
| `/` | 97 / 100 / 100 | 100 / 100 / 100 |
| `/services/` | 92 / 100 / 100 | 100 / 100 / 100 |
| `/web-design/` | 88 / 100 / 100 | 99 / 100 / 100 |
| `/blog/` | 96 / 100 / 100 | 99 / 100 / 100 |
| a blog post | 83 / 100 / 100 | 98 / 100 / 100 |
| `/portfolio/pathway-escrow/` | 95 / 100 / 100 | 98 / 100 / 100 |
| `/contact/` | 86 / 100 / 100 | 99 / 100 / 100 |
| podcast episode | 91 / 100 / 100 | 98 / 100 / 100 |

**Targets (desktop 95+, mobile 85+): met everywhere except one mobile blog post (83).**
Accessibility and best practices are 100 everywhere. SEO reads 58–66 only because staging is
noindex with `Disallow: /` by design. Expect 100 on live (Local: 100).

## Notes

- Mobile spread (83–97) is mostly **server response time**: staging runs without Breeze or
  Object Cache Pro, and the first hit of a page is uncached PHP. Live will have both
- The blog post (83) has the largest LCP image; its featured image is a 2560px original
  (capped in v27.3.5), served at `large` size
- Podcast episode: mobile 65 → 91, desktop 87 → 98 after the click-to-load player (v27.3.17)
