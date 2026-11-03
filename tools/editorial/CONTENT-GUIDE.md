# Content guide — editing the Phase 2 site

For Nathan and Deanna. Everything below is in WP Admin; no code needed.
Your dashboard's **"Stark — needs attention"** box lists new form submissions, drafts
waiting for review and pages whose copy isn't approved yet.

## Copy status — Draft → Live-sourced → Approved

Service pages and the Home page have a **Copy status** field (top of the page editor):

| Status | Meaning | On the site |
|---|---|---|
| **Draft** | Placeholder or first-draft copy | Yellow "[DRAFT - needs Nathan review]" bar |
| **Live-sourced** | Real copy carried over from the old site, still needs a voice pass | Blue "[LIVE-SOURCED…]" bar |
| **Approved** | Final | No bar |

The bars only stop showing once a page is set to **Approved**. Search the site for
`[NEEDS REVIEW]`, `[DRAFT` and `[SAMPLE]` to find copy that's flagged inline.

## Edit a service page

Pages → e.g. *Web Design*. Content is in tabs under the editor. Empty fields and sections are hidden.

| Tab | What it controls |
|---|---|
| **Hero** | Eyebrow (small caps above the headline), **Headline** (the H1; empty = page title), **Thesis** (1–3 sentences), note under the buttons, two buttons, **Card description** (the one line used on `/services/`, the homepage and Related cards) |
| **Outcomes** | "What changes" cards: eyebrow, heading, intro, up to 6 title + description pairs, closing line |
| **Problem** | "The reality" section: heading, body (blank line between paragraphs; the first one is set larger), symptoms (one per line), closing line |
| **What's Included** | Heading, intro, up to 8 items (icon, title, description), closing line |
| **Process** | Eyebrow, heading, intro, up to 4 numbered steps, closing line |
| **Who It's For** | Good-fit and not-a-fit lists (one per line) with their headings |
| **More Sections** | Up to 3 extra card sections (e.g. Paid's "First 30 days") |
| **Case Study** | Pick a featured case study, or leave empty to use the newest one tagged with this service |
| **FAQ** | Heading, intro, questions (also published as FAQ schema for Google) |
| **Related** | Up to 3 related services (empty = automatic) |
| **Final CTA** | Heading, subhead, button, small note |

Location pages (e.g. *Web Design → Antelope Valley*) reuse the service page and add
local fields: market name, local headline, credibility stats, nearby areas, local FAQ.

**SEO:** each page's Rank Math box sets the search title and description. If the description
is empty, the site uses the card description or thesis.

## Add a case study

Portfolio → Add New Case Study.
1. Title = client name. **Featured image** = card image (3:2 works best)
2. Fields: client, industry, year, services (ticks link it to those service pages), website, summary (one sentence for cards)
3. Story: overview, challenge, approach, results summary, **results** (up to 3 number + label stats — real numbers only), hero image, gallery, testimonial (quote, name, role — only with the client's OK)
4. Publish. It appears on `/work/` and on the service pages you ticked

## Publish a blog post

Posts → Add New. Write in the block editor.
- **Featured image**: about 1600px wide (larger images slow pages down; the site keeps originals at 2560px max)
- One **category**; tags are optional
- **Author**: Nathan or Deanna; the house account shows no author box
- Images in the text: use the media library (not links to other sites); add alt text
- Rank Math: set a focus keyword, check the title and description preview

## Add a podcast episode

Podcast → Add New (Podlove).
1. Title, subtitle, summary, cover (1:1 or 16:9)
2. In the **Podcast Episode** box: episode number, the MP3 file slug on podcast.starksocial.com, duration, chapters
3. Verify the media file turns green, then publish. The episode joins `/podcast/` and the RSS feed (`/feed/podcast/`) that Apple Podcasts reads
4. Transcript: Podlove's Transcripts tab (VTT), or paste plain text in the episode content

## Update testimonials, awards and press

They live in `stark-social/inc/stark-testimonials.php` (code), because there are so few. To add one,
send Nathan the quote, name, title, company, which service it's about and the year. Awards
show on the About page under "Recognition". **Only real, approved quotes.**

## The contact form

Forms → *Stark Social Contact*. Entries are under Forms → Entries; each entry has a
note showing whether it reached Perfex ("Webhook → HTTP 200"). Notification emails go to
Nathan and Deanna. Don't change field IDs: the Perfex webhook maps them by number.

## Knowledgebase

Docs → Add New. Pick one category; articles appear on `/support/knowledgebase/` and the Support hub (first 5 per category).
