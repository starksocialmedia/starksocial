# Rank Math — Phase 2 configuration

`configure.php` sets everything site-wide and is idempotent. Run it on every
environment after the theme and content are in place, and again after cutover so
the URLs in robots.txt and the schema use the live domain:

```
wp eval-file tools/rankmath/configure.php
```

Theme side (`stark-social/inc/stark-seo.php`):
- **Description fallbacks.** Phase 2 copy lives in ACF, so `%excerpt%` is empty. The fallback order is the card description, then the hero thesis or intro, then the parent service page (for location pages), then the case-study summary, then the blog, podcast and author intros
- **Location page titles:** "Web Design in Antelope Valley, CA | …"
- **`areaServed`** on the business entity: the 5 markets, each linking to its card on `/service-areas/`
- **Schema:** pages drop Rank Math's default Article (they keep WebPage); episodes get **PodcastEpisode** (number, duration, MP3, series)

## What `configure.php` sets

| Area | Setting |
|---|---|
| Knowledge graph | Company, "Stark Social Media Agency" (alt "Stark Social"), Local SEO module on, type **ProfessionalService** |
| Contact | hello@starksocial.com, +1-661-513-3996, Valencia CA 91385 US. **No street address**: the P.O. Box isn't a storefront, and Google wants service-area businesses without one |
| Hours | Mon–Thu 9–5, Fri 9–4 (same as Contact). It was 9–5 seven days a week |
| Social | Facebook, Instagram, LinkedIn, YouTube |
| Share image | `stark-social-media-agency-homepage-1.webp` (1200×630, live's homepage image), large Twitter card |
| Titles | Separator `\|` · Page `%title% \| %sitename%` · Post `%title% \| Stark Social Media Agency Blog` · Portfolio `%title% Case Study \| %sitename%` · Podcast `%title% \| Stark Social Media Agency Podcast` · Docs `%title% \| Stark Social Knowledgebase` · Home `%sitename% \| %sitedesc%` |
| Descriptions | `%excerpt%` everywhere + theme fallbacks; homepage and `/services/` set explicitly from their on-page copy; `/work/` and `/podcast/` archive descriptions |
| Breadcrumbs | On, separator `/`, Home label (BreadcrumbList schema; no visual breadcrumb in the templates yet) |
| robots.txt | Allow all; disallow `/wp-admin/` (except admin-ajax), `/?s=`, `/search/`; sitemap line. **Staging serves `Disallow: /` instead** (staging guard) |
| Sitemap | Posts, pages, portfolio, podcast, docs, categories, authors. **Excluded:** the 4 legal pages (also noindex in Rank Math), tags, attachments, KB categories. 404 and search are never in it |
| Search | noindex search results and empty taxonomies |

Also fixed: the knowledge graph name and website name were `staging.starksocial.com`
(left over from the staging install), and the type was Person.

## Still manual, per page (later)

1. **Focus keywords** — none set. Use `SEO-STRATEGY.md`: one primary keyword per service and location page
2. **Per-page share images** — live has one per service (`stark-social-media-agency-web-design-development.webp` etc.) in uploads. Set each service page's Social tab
3. **Descriptions written for search**: the fallbacks reuse on-page copy, which is still draft. Write real 150-character descriptions when the copy is approved, starting with home, the 7 services, `/work/`, About and Contact
4. **Service schema** on the 7 service pages (Rank Math Schema → Service), with `areaServed` and the price range if you want one
5. **Logo** for the knowledge graph (`knowledgegraph_logo`) — waiting on the real logo files
6. **Visual breadcrumbs** — a design decision. The schema is already output
7. **Redirections** — Rank Math's module isn't on; the theme handles the Phase 1 → Phase 2 301s. Import any live AIOSEO redirects at cutover
8. **Rank Math score 90+** per page (BUILDPLAN 2.7) — after copy approval
