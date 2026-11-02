# SEO-STRATEGY.md — Stark Social Media Agency
**Project:** starksocial.com Phase 2  
**Owner:** Claude — SEO Chat  
**Last Updated:** November 2026  
**Tools:** RankMath Pro, Wincher, Google Search Console, Google Business Profile

---

## Goals

- RankMath SEO score 90+ on every page
- Core Web Vitals all green post-launch
- Schema validation zero errors
- Rank for agency keywords in 4 target markets
- Own digital marketing searches in underserved SoCal suburbs

---

## SEO Stack (Phase 2)

| Tool | Role |
|---|---|
| RankMath Pro | On-page SEO, schema, redirects, local SEO |
| Wincher | Rank tracking, keyword research |
| Google Search Console | Indexing, performance, coverage |
| Google Business Profile | Local presence, service areas |
| Cloudways | Sitemap submission via RankMath |

**Migration note:** Fresh RankMath install on staging. No AIOSEO import needed — new site, new setup. Redirects from old site to be mapped before launch.

---

## Target Markets (Local SEO)

| Market | Region | Notes |
|---|---|---|
| San Fernando Valley | LA County | Closest to base, most competitive |
| Antelope Valley | LA County (north) | Underserved, low competition |
| Ventura County | Ventura | Strong SMB market |
| Bakersfield | Kern County | Very underserved, high opportunity |

### Strategy Per Market
- Dedicated landing page per market (not thin — genuine local value)
- `LocalBusiness` schema with `areaServed` per region
- Local testimonials/case studies referenced per page where possible
- Google Business Profile service areas set to all 4 markets
- Internal linking: service pages → local landing pages

### Local Landing Page URL Structure

**Locked pattern: nested `/{service}/{area}/`** — a location page is a child of its service page. Any page nested under a service page renders with `templates/stark-location.php` (v27.0.5); flat slugs like `/web-design-bakersfield/` do not.

```
/social-media-management/san-fernando-valley/
/social-media-management/antelope-valley/
/web-design/antelope-valley/
/web-design/bakersfield/
/seo/ventura-county/
```

- `{service}` — one of the 7 service slugs: `social-media-management`, `paid-advertising`, `web-design`, `seo`, `content-creation`, `brand-strategy-identity`, `audit-consulting`
- `{area}` — market slug: `santa-clarita`, `san-fernando-valley`, `antelope-valley`, `ventura-county`, `bakersfield`

*(Full URL map to be built in SEO chat)*

---

## Service Page Keyword Targets

*(To be populated from Wincher data — bring export to SEO chat)*

| Page | Primary Keyword | Secondary | Local Variant |
|---|---|---|---|
| Social Media Management | TBD | TBD | TBD |
| Paid Advertising | TBD | TBD | TBD |
| Web Design | TBD | TBD | TBD |
| SEO | TBD | TBD | TBD |
| Content Creation | TBD | TBD | TBD |
| Brand Strategy | TBD | TBD | TBD |
| Audit & Consulting | TBD | TBD | TBD |

---

## Schema Plan

| Page Type | Schema Type |
|---|---|
| Home | `Organization`, `WebSite`, `LocalBusiness` |
| Service pages | `Service`, `LocalBusiness` |
| Local landing pages | `LocalBusiness` with `areaServed` |
| Portfolio / case studies | `CreativeWork` |
| Blog posts | `Article`, `BreadcrumbList` |
| Podcast | `PodcastSeries`, `PodcastEpisode` |
| About | `Person` (Nathan + Deanna) |
| FAQ sections | `FAQPage` |

---

## Redirect Map

*(To be built before launch — maps old URLs to new)*

| Old URL | New URL | Status |
|---|---|---|
| TBD | TBD | — |

---

## RankMath Configuration Checklist

- [ ] Install RankMath Pro on staging
- [ ] Connect Google Search Console
- [ ] Set site type: Local Business
- [ ] Configure local SEO module (address, service areas)
- [ ] Enable schema for all post types
- [ ] Set breadcrumb separator
- [ ] Configure sitemap (include: pages, posts, podcasts, portfolio; exclude: legal, utility)
- [ ] Set robots.txt (block: staging, admin, search results)
- [ ] 404 monitor enabled
- [ ] Redirects module enabled
- [ ] Configure title + description templates per post type

---

## Wincher Keywords

*(Paste Wincher export here or bring to SEO chat)*

---

## Notes

- Antelope Valley and Bakersfield are highest opportunity — minimal agency competition
- Digital Marketing Audit is a strong entry-point service for local SEO — "free audit" angle
- Podcast content should be schema-marked up for potential rich results
