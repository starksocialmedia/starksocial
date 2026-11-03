# Page content migration — live → Phase 2 (v27.3.0)

November 2 2026. The source is the rendered live pages (`live-outlines/*.md`,
made by `extract_live.py`) plus the Cornerstone data, read-only over SSH.
`build_specs.py` maps sections to ACF fields and keeps every string verbatim;
`import-service-specs.php` and `import-core-pages.php` write them by field key.

Re-run on any environment:
```
python3 tools/content-migration/build_specs.py tools/content-migration/live-outlines tools/content-migration/service-specs.json
STARK_JSON=tools/content-migration/service-specs.json wp eval-file tools/content-migration/import-service-specs.php
wp eval-file tools/content-migration/import-core-pages.php
```

**Copy status** has a new value, **Live-sourced**: real copy from starksocial.com that still
needs a voice review. Live-sourced pages show a blue banner; drafts keep the yellow one.

## Result

| | Pages | Share |
|---|---|---|
| Live pages | 22 | — |
| Fully populated with live content | 17 | **77%** |
| Partially populated (live + Phase 2 draft) | 2 (Home, Brand Strategy) | 9% |
| Template-only (no copy to move) | 3 (Blog index, 404, Search Results) | 14% |

**Of the 19 live pages that have real copy to move, 17 are complete and 2 are partial.** By
section across those pages, about 90% of live copy is now in Phase 2. What's left over is
listed below.

## Populated with live content

| Page | Sections carried over |
|---|---|
| `/services/` | Hero (title, lead, tagline), 3 principles, "Our Services" heading, service card descriptions (now each service's **card description**, also used on the homepage and Related cards), fit lists, CTA |
| Social Media Management | Hero, outcomes (3), what we handle (5), process (3), fit lists, "What working together looks like" (what we need from you), FAQ (5), CTA |
| Paid Advertising | Hero, "What paid advertising actually changes" (4), what we manage (Align / Build / Test / Refine), approach (3 steps), **First 30 days** (4 weeks), FAQ (5), CTA |
| Content Creation | Hero, what we create (6 formats), how it fits together (4 steps), FAQ (5), CTA |
| Web Design | Hero, outcomes (3), the problem (lead, body, 4 symptoms), process (3), fit lists, what we handle (5), FAQ (6), CTA |
| SEO | Hero, outcomes (3), the reality (4 symptoms), process (3), what we manage (5), FAQ (5), CTA |
| About | Hero, "Why Stark exists" story, "How we think", leadership heading, both founder bios + roles, CTA |
| Contact | Hero, form heading + intro, address, phone, hours |
| Author pages (Nathan, Deanna) | v27.3.21: full live "Background" (6 paragraphs each), highlights line, role, and "How X works" with 4 focus areas — from `/about/nathan-imhoff/` and `/about/deanna-l-miller/` |
| KB, password generator, 4 legal pages | Done in earlier versions (v27.0.15–v27.1.2) |

## Partial or ambiguous

| Page | Situation |
|---|---|
| **Home** | Hero kept as is, per instruction. Live's hero is "Elevate Your Brand. Build Something That Lasts." with "Strategic design, websites, and social media crafted in Southern California, built for brands nationwide." — **your call** whether to use it. Everything else is live: services intro, How We Work (3 steps), Who We're For, The Stark Difference, CTA. The **FAQ** is Phase 2 draft (live has none) and is labelled `[DRAFT — not on live]`. The stat tiles are facts from live copy |
| **Brand Strategy & Identity** | Hero, outcomes, the reality and CTA are live. **Process and FAQ on live are copies of the SEO and Web Design pages** (Phase 1 bug), so the Phase 2 drafts stay and the page keeps Copy status **Draft**. The "what we handle" list is a Phase 2 draft (live has none). Live fix kit: `tools/live-fixes/brand-strategy-1427/` |

## Flagged `[NEEDS REVIEW]` inline (live copy errors, kept verbatim)

- **Content Creation CTA** — "Ready to turn paid social into a system?" is the Paid Advertising CTA, copy-pasted on live.
- **About story** — "They’re doing meaningful work" has no antecedent, and "…strategic ownership. holding it all together" is a broken sentence.
- **About CTA** — "works as a fractional marketing partner" (Fractional CMO was dropped as a service).

## Kept as Phase 2 draft / sample (no live equivalent)

- `/audit-consulting/` — new service, no live page (Draft).
- `/service-areas/` and the location page `/web-design/antelope-valley/` — no live pages; market descriptions from `inc/stark-service-areas.php`.
- Homepage FAQ (8 questions).
- Support hub microcopy ("Client support", ticket note), `/work/` archive intro, 404 and search microcopy.
- About "Where we work" heading (body is now live copy).

## Lost or not carried (Cornerstone-only or no Phase 2 slot)

- **Homepage "Everything your brand needs…" 4 groupings** (Brand + Content Strategy, Website Design + Refreshes, Social Media Management, Creative + Design Support). Phase 2 shows the 7 services with their live hub descriptions instead.
- **Live section eyebrows** that duplicated the service name ("Paid Social Advertising", "What We Create"); the Phase 2 eyebrow is the service name.
- **Social Media Management "What we handle"** list in the collaboration section — identical to the "What we handle for you" list above it, so shown once.
- **Halo / animated keyword lists** in Cornerstone components (looper data shown as floating chips) — same text as the problem symptoms, which are carried.
- **"Get an Estimate → /estimate/"** buttons — `/estimate/` is a 404 on live.
- **Hero video backgrounds** (`.webm` per service) — Phase 2 heroes are the navy gradient; the videos are in uploads if you want them back.
- **Live 404 / Search Results page copy** ("Well... this is awkward.") — Phase 2 has its own 404 template copy (v27.0.10).

## Live pages without a Phase 2 equivalent

None. All 22 live pages map to a Phase 2 page, a template or a 301.

## Testimonials, awards, press, logos (A4)

- **Testimonials: none on live** (no testimonial content on any page or post; Pathway and Schlickart case studies have none).
- **Awards (2)**, now in `inc/stark-testimonials.php` and shown on About under "Recognition":
  - 2017 — Communicator Award, Excellence in Web Design (23rd Annual, AIVA), for the Valencia Jazz & Blues website.
  - 2015 — Best of SCV, Web Design Company (The Santa Clarita Valley Signal).
- **Press:** none beyond those two announcement posts.
- **Client or award logos: none displayed on live.** `img/awards/` and `img/clients/` contain a README each.

## Portfolio (A3)

Live has **2** case studies (`work` CPT: Pathway Escrow, Schlickart), both migrated in
v27.0.12, plus the Valencia Jazz & Blues draft. No other project, case-study or portfolio content
exists on live (no other CPTs, no pages, no portfolio tags). The 2015 award post names
sites built in 2014–2015 (Congressman Steve Knight, Assemblyman Scott Wilk, SCV Fixit and others), but
there's no case-study material for them. **The 5–8 target isn't reachable without
inventing content**, so Phase 2 stays at 3 until real case studies are written.
