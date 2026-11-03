# Rank Math — per-page status and manual steps (v27.4.2)

November 3 2026. Automated by `import-live-meta.php` (run after `configure.php`; both are
idempotent, and `configure.php` no longer overwrites the home and `/services/` meta):

```
wp eval-file tools/rankmath/configure.php
wp eval-file tools/rankmath/import-live-meta.php      # add "dry" to preview
```

## What's set, per page

Source: **live's AIOSEO** (titles, descriptions, focus keyphrases written for live) and live's
per-page share images. Copy is verbatim; nothing new was written.

| Page | Focus keyword(s) | Description | Share image |
|---|---|---|---|
| Home | social media agency, digital marketing agency | live (145) | live |
| `/services/` | social media marketing | live (137) | live |
| Social Media Management | social media management, + southern california | live (143) | live |
| Paid Advertising | paid advertising services, + southern california | live (135) | live |
| Content Creation | content creation, + southern california | live (155) | live |
| Web Design | web design, + southern california | live (132) | live |
| SEO | SEO Services, + southern california | live (**183, long**) | **none on live**, generated (v27.4.7) |
| Brand Strategy & Identity | Brand Strategy, + southern california | live (144) | live |
| Audit & Consulting | digital marketing audit, marketing audit southern california | **fallback** (card description; draft copy) | generated (v27.4.7) |
| About | Stark Social Media Agency | live (142) | live |
| Contact | — (none on live) | live (116) | live |
| Blog, Support, Knowledgebase, Password generator | live's | live | live |
| Legal pages (noindex) | — | live | homepage image |
| Pathway Escrow, Schlickart | client name (live) | live | featured image |
| Valencia Jazz & Blues (draft) | valencia jazz & blues, jazz festival website | fallback (summary) | featured image |
| `/service-areas/` | digital marketing agency southern california, santa clarita marketing agency | fallback | generated (v27.4.7) |
| `/web-design/antelope-valley/` | web design antelope valley, antelope valley web design | fallback (parent service) | generated (v27.4.7) |

The "+ southern california" secondary keyword on service pages follows the brief; the primary
stays live's. **SEO-STRATEGY.md's keyword table is still TBD (Wincher data)**: when it's
filled in, replace these.

## Schema (theme, `inc/stark-seo.php`)

- **Service** on the 7 service pages (provider = the business, `areaServed` = the 5 markets) and
  on location pages (`areaServed` = that market, name "Web Design in Antelope Valley")
- **CreativeWork** on case studies (client, industry, year, services as keywords, link to the
  client's site), replacing Rank Math's default Article
- **ProfessionalService** (LocalBusiness) with `areaServed` for all 5 markets is on every page
  (v27.1.5). FAQPage on pages with FAQs; PodcastEpisode on episodes

## Not possible yet

- **"5 service area pages"**: only one location page exists (`/web-design/antelope-valley/`,
  sample copy). The other markets have no pages; `/service-areas/` is the hub. Build them when
  there's copy per market (Phase 2.1 roadmap)

## Manual (WP Admin), for Nathan

1. **SEO page description is 183 characters**; Google cuts at about 155–160. Shorten it in the
   page's Rank Math box. The cookie policy's (178) is noindex, so it doesn't matter
2. **Audit & Consulting, `/service-areas/`, the location page:** write descriptions once the copy
   is approved (they use the on-page copy until then)
3. **Contact:** live had no focus keyword. Optional
4. **Author archives** (`/author/nateimhoff/`, `/author/deannaleigh/`): live's team-page
   descriptions are in `live-aioseo-meta.json` (`nathan-imhoff`, `deanna-l-miller`). Rank Math sets
   author descriptions in Users → Profile → Rank Math SEO; the theme falls back to the user bio
5. After launch: connect Search Console in Rank Math (General Settings → Search Console), which
   needs a Google login
