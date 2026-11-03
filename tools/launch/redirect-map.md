# Redirect map: live (Phase 1) → Phase 2

November 3 2026 (v27.4.10). Built from every live URL (`live-inventory/live-urls.csv`, 161
pages, posts, episodes, case studies and KB articles) plus a pattern test of old URL shapes,
run against live and Phase 2 side by side. Every rule fires **only on a 404**, so none can
shadow a real page. Machine-readable list of all one-to-one rules: `redirects.csv`.

**Where the rules live:** theme code (`inc/stark-redirects.php`, `inc/stark-services.php`,
`inc/stark-authors.php`, `inc/stark-blog.php`), not Rank Math's Redirections module. This was
decided in v27.3.16: the rules ship with the theme, so they're in place the moment staging is
pushed, they're in git, and nothing has to be re-entered in the admin at cutover. Rank Math's
404 monitor is still the place to catch anything missed after launch. To move them into Rank
Math later, `redirects.csv` has the full list.

## What moves

| Old (live) | New (Phase 2) | Rule | Tested |
|---|---|---|---|
| `/services/{service}/` (6 services) | `/{service}/` | pattern, `stark_redirect_legacy_service_urls()` | ✓ 301 |
| `/services/{service}/{area}/` | `/{service}/{area}/` | same pattern | ✓ |
| `/services/search-engine-optimization-seo/` | `/seo/` | map (AIOSEO) | ✓ 301 (live chained through `/services/seo/`) |
| `/about/nathan-imhoff/`, `/about/deanna-l-miller/` | `/author/nateimhoff/`, `/author/deannaleigh/` | `inc/stark-authors.php` | ✓ 301 |
| `/password-generator/` | `/support/password-generator/` | map (AIOSEO) | ✓ 301, same as live |
| `/privacy-policy/`, `/terms-of-service/`, `/cookie-policy/`, `/accessibility-statement/` | `/support/…` | map (v27.4.10; before, only WordPress's slug guess) | ✓ 301, same as live |
| `/knowledgebase/` | `/support/knowledgebase/` | map (v27.4.10; before, slug guess) | ✓ 301, same as live |
| `/blog/category/{x}/`, `/blog/tag/{x}/` | `/blogs/category/{x}/`, `/blogs/tag/{x}/` | `inc/stark-blog.php` (v27.3.8) | ✓ 301 (404 on live) |
| **`/category/{x}/`, `/tag/{x}/`** (WordPress default bases) | `/blogs/category/{x}/`, `/blogs/tag/{x}/` | **new v27.4.10**, pattern | ✓ 301. **Live sends these to an unrelated post today** (WordPress guessing a similar slug: `/category/social-media/` lands on a podcast episode) |
| `/service/{x}/` (live's Work "Service" taxonomy archive) | `/{x}/` | WordPress slug guess | ✓ 301 to the matching service page |
| 9 old blog slugs (`/content-is-king`, `/twitter-3-0-webinar` …) | current posts | map (AIOSEO) | ✓ 301 |
| `/should-your-small-business-have-a-blog-…` | KB article | map (AIOSEO) | ✓ |
| `/1` … `/37` (Pretty Links, episode short links) | episode with that number | pattern | ✓ 301. **Intentional difference:** on live 33 of 37 go to the wrong episode; Phase 2 follows the number printed on the cover art |
| **`/sitemap.xml`** (AIOSEO's sitemap) | `/sitemap_index.xml` (Rank Math) | **new v27.4.10** | ✓ 301. Was a 404 on Phase 2, and it's the URL Search Console most likely has |
| **`/search-results/`** (Phase 1 search page) | `/?s=` | **new v27.4.10** | ✓ 301 |
| **`/stark-404/`** (Phase 1 404 page) | `/` | **new v27.4.10** | ✓ 301 |

## What doesn't move (same URL on both)

All 69 blog posts (`/{slug}/`, no date-based URLs exist on live: `/2016/01/…` is a 404 on
both), 37 episodes (`/podcast/{slug}/`), 2 case studies (`/portfolio/{slug}/`), 27 KB articles
(`/docs/{slug}/`, `/docs-category/{x}/`), `/blogs/category/…`, `/blogs/tag/…`, author archives,
`/feed/`, **`/feed/podcast/`** (Apple Podcasts), `/blog/` and its pages, `/podcast/`, Home,
About, Contact, Services, Support.

## New in Phase 2 (no old URL)

`/work/` (case study archive; `/portfolio/` was a 404 on live and stays one), `/service-areas/`,
`/audit-consulting/`, `/web-design/antelope-valley/`, `/thank-you/` (noindex), `/support/`
sub-pages that already existed.

## Should 410 / go home

| URL | Decision |
|---|---|
| `/stark-404/`, `/search-results/` | 301 (above). 410 would also be valid; 301 keeps any stray links working |
| Live draft "Portfolio" page (`?page_id=1752`) | n/a: never public |
| `/estimate/` (linked from old Cornerstone buttons) | 404 on both; the buttons weren't carried over. No rule |
| Work taxonomies `/industry/…`, `/platform/…` | 404 on both. No rule |

## Not redirects, but related

- `/wp-login.php` returns **403 on live** (a Cloudways or Wordfence rule) and 200 on Phase 2. After
  launch, decide on login protection (Wordfence, or Cloudways' bot protection)
- After cutover, watch Rank Math → 404 Monitor and Search Console → Pages → Not found for a
  week. Add anything real to `stark_legacy_redirect_map()`
