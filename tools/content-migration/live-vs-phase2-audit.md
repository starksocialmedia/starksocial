# Live vs Phase 2 — content audit

Taken November 2 2026 from starksocial.com (`ssh stark-live`, read-only) and Local.

## Inventory

| Type | Live | Phase 2 (Local) | Notes |
|---|---|---|---|
| Pages (published) | 22 | 22 | Mapping below |
| Blog posts | 69 published (72 total) | 70 | Snapshot from v27.0.9; re-sync at cutover |
| Podcast episodes | 37 | 37 | Episode data in Podlove tables on live (see `tools/podlove-migration/`) |
| Knowledgebase docs | 27 | 27 | v27.1.2 |
| Case studies | 2 (`work` CPT, archive `/work/` is a **404** on live) | 3 (`portfolio`) | Valencia Jazz & Blues is Phase 2 only, built from the 2017 award post |
| BetterDocs FAQs | 7 | 0 | Not used by any live page template; not migrated |
| Pretty Links | 37 | 0 | Short links (`/37` episode links etc.) — **migrate at cutover** (plugin + table) |
| Media | 255 attachments (2022: 4, 2024: 154, 2025: 3, 2026: 94) | 148 registered + files | Phase 2 registers only what it uses; all files are in uploads |
| Users | starksocialmedia (admin), nateimhoff, deannaleigh (editors) | same three (Local admin login differs) | Author slugs match live |
| Taxonomies | category, post_tag (posts + podcast), doc_category/doc_tag, industry/service/platform (`work`) | same, except `work` taxonomies → ACF fields on `portfolio` | |
| Menus | Main Menu (primary), Services (footer), Company, Resources | Theme-built nav + footer | Live "All Service" / "Social Media Mangement" typos not carried |
| Gravity Forms | Form 1 "Contact Form" + Webhooks feed → Perfex | Form 1 rebuilt (v27.3.2) | |

## Page mapping

| Live page | Live URL | Phase 2 | Status |
|---|---|---|---|
| Home (2) | `/` | `/` | **created-with-content** — live copy below the hero (hero kept per instruction) |
| About Stark Social (31) | `/about/` | `/about/` | **created-with-content** (live) |
| Nathan Imhoff (1654) | `/about/nathan-imhoff/` | `/author/nateimhoff/` (301) | created-with-content (bio from live, v27.0.17) |
| Deanna L. Miller (1629) | `/about/deanna-l-miller/` | `/author/deannaleigh/` (301) | created-with-content (v27.0.17) |
| Contact (91) | `/contact/` | `/contact/` | **created-with-content** (live) |
| Services (1049) | `/services/` | `/services/` | **created-with-content** (live) |
| Social Media Management (1081) | `/services/social-media-management/` | `/social-media-management/` (301) | **created-with-content** (live) |
| Paid Social Advertising (1134) | `/services/paid-advertising/` | `/paid-advertising/` (301) | **created-with-content** (live) |
| Content Creation (1169) | `/services/content-creation/` | `/content-creation/` (301) | **created-with-content** (live, CTA flagged) |
| Web Design (1275) | `/services/web-design/` | `/web-design/` (301) | **created-with-content** (live) |
| SEO (1392) | `/services/seo/` | `/seo/` (301) | **created-with-content** (live) |
| Brand Strategy & Identity (1427) | `/services/brand-strategy-identity/` | `/brand-strategy-identity/` (301) | **partial** — live hero/outcomes/problem/CTA; process + FAQ stay draft (live's are other pages' copies) |
| — | — | `/audit-consulting/` | **created-with-draft** — no live page |
| — | — | `/service-areas/`, `/web-design/antelope-valley/` | **created-with-draft** — no live pages |
| Blog (45) | `/blog/` | `/blog/` | created (archive template) |
| Support (314) | `/support/` | `/support/` | created-with-content (v27.1.2) |
| Knowledgebase (135) | `/support/knowledgebase/` | same | created-with-content (live intro) |
| Password Generator (285) | `/support/password-generator/` | same | created-with-content (live FAQ) |
| Privacy, Terms, Cookie, Accessibility | `/support/…/` | same | created-with-content (v27.0.15) |
| 404 Error (1552), Search Results (1530) | `/stark-404/`, `/search-results/` | 404 + search templates | template pages; live copy not reused (see report) |
| — | — | `sample-page` (Local ID 2) | **orphan** — WordPress default, trashed on Local |

## Gaps and orphans

- **Live links to `/estimate/`** ("Get an Estimate" on Web Design, SEO and About CTAs). That page doesn't exist on live (404) or in Phase 2. Phase 2 CTAs point to `/contact/` only.
- **Live `/work/` is a 404**. Phase 2 makes it the real portfolio archive.
- **Pretty Links (37)** and the 7 BetterDocs FAQs aren't in Phase 2.

## Stale on live (pruning candidates)

No analytics access, so this goes by date only — no traffic data was used.
- Blog posts from 2015–2018 (about a third of the archive): several reference Twitter / Snapchat-era features (e.g. "Has Snapchat Jumped the Shark", "Twitter Is Not Dead"). Keep for SEO history unless traffic says otherwise.
- `search-results` and `stark-404` pages exist only because of Cornerstone; Phase 2 doesn't need them.
- The 2022 location SVG and old `fw-backup/` zips (939 MB, excluded from staging).
