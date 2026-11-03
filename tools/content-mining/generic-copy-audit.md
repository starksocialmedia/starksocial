# Generic copy audit

November 3 2026 (v27.5.7). Every published page fetched from Phase 2 and its `<main>` text
scanned for placeholder markers (`[DRAFT]`, `[SAMPLE]`, `[NEEDS REVIEW]`), WordPress stock
text, lorem ipsum, a list of agency stock phrases ("passionate about", "next level",
"cutting-edge", "leverage", "elevate", "seamless", "solutions"…) and repeated buzzwords.
Then the draft-status service pages were read in full.

**Bottom line:** no lorem ipsum, no WordPress stock text, almost none of the classic stock
phrases (one "Leverage", one "elevated"). What reads generic is **repetition**: the same
handful of abstract words on every page, and hero taglines that are interchangeable between
services. That's what Nathan is reacting to as much as the visuals.

Priority: **P1** = fix before launch, **P2** = soon after, **P3** = polish.

## 1. Placeholder markers still on the site

| Page | Section | Current | Why | Priority |
|---|---|---|---|---|
| `/web-design/antelope-valley/` | Whole page | 6 × `[SAMPLE]`: "Rooted in the Antelope Valley", "Local credibility copy.", stats "40+ local clients", "10 yrs in the region", FAQ "Do you meet in person in Lancaster? Yes." | Sample page. The stats are invented placeholders and must not go live | **P1** (write it, or draft/noindex it: quick win #15) |
| `/service-areas/` | Intro + 5 market descriptions | 6 × `[DRAFT]` (written in v27.4.9) | Placeholder copy awaiting Nathan's voice | **P1** |
| `/` | FAQ heading | "Questions, answered [DRAFT — not on live]" + 8 draft Q&As | Not on live; Nathan keeps, trims or removes | **P1** (quick win #12) |
| `/about/` | Story | `[NEEDS REVIEW]`: "They're doing meaningful work" (no antecedent), "…strategic ownership. holding it all together" | Broken sentences, as on live | **P1**: syntactic fix done in v27.5.10 (Block G) if not reverted |
| `/about/` | CTA | `[NEEDS REVIEW]`: "fractional" | Fractional CMO isn't a service any more | P1 (Nathan's call, quick win #5) |
| `/content-creation/` | Final CTA | `[NEEDS REVIEW]` "Ready to turn paid social into a system?" | Copied from Paid Advertising on live | **P1**: replaced in v27.5.10 (Block G) |

## 2. Reads like a template

| Page | Section | Current copy | Why it reads generic | Priority |
|---|---|---|---|---|
| All service pages | Hero note (small line under the CTAs) | "Strategy-led. Fully in-house. Built to last." (Services, Social), "Strategy-led. Collaborative. Built to last." (Brand), "Strategy-led. Fully in-house. No black box." (Paid) | Same three-beat formula on four pages; the reader sees it repeat while clicking between services | **P2**: one line per service that only that service could say, or drop the line |
| `/seo/` | Hero note | "Trusted by organizations that value clarity, consistency, and long-term thinking." | A "trusted by" line with nobody named. Reads as filler social proof | **P1**: replace with a real proof point (an award, a client name) or remove |
| `/web-design/` (+ Antelope Valley copy) | Hero note | "Trusted by growing brands who need their website to actually work — not just look good." | Same unnamed "trusted by" pattern | P1, same fix |
| Site-wide | Body copy | "clarity" 48×, "clear" 43×, "consistent/consistency" 40×, "system" 34×, "trust" 28× across 22 pages. Web Design alone says "clarity" 10 times; Social says "consistent" 10 times | The ideas are right, but the same abstract nouns everywhere make every page sound alike. Swap half for concrete specifics: what's delivered, how often, who does it | **P2** |
| `/content-creation/` | Hero + formats + process | "test fast, learn faster", "creative systems", "repeatable content engine", "Every piece has a job. Every format has a reason." | Performance-agency language (UGC, testing sets) that doesn't match the homepage card ("Photo and video that looks sharp…") or the rest of the site's voice | **P2**: decide what Content Creation actually is at Stark, then rewrite |
| `/brand-strategy-identity/` | Hero + process heading | "Clarity before creativity." appears twice (hero H1 and process heading "Clarity before creativity. Structure before scale.") | Repeated slogan; the second use reads as filler | P2 |
| `/brand-strategy-identity/` | Outcomes | "Alignment / Clarity / Confidence" | Abstract triad; could sit on any agency's site | P3 |
| `/` | Hero | "Marketing that moves the needle." | Stock idiom. **Not changed** (hero is Nathan's call: quick win #3 offers live's "Elevate Your Brand. Build Something That Lasts.", which is also generic) | P2 (Nathan) |
| `/` | Why Stark footnote | "Based in Southern California (Los Angeles + Santa Clarita)" | Everywhere else says Santa Clarita; "Los Angeles" here is inconsistent | P3 |
| `/seo/` | Outcomes | "Leverage: SEO supports every other channel…" | The one stock verb on the site | P3 |
| `/portfolio/schlickart/` | Challenge | "a refined visual identity that elevated the brand" | "Elevated the brand" is stock | P3 |

## 3. Typos and grammar (fixed in v27.5.10 unless noted)

| Page | Current | Fix |
|---|---|---|
| `/content-creation/` | Section heading "How it fit together" | "How it fits together" |
| `/` and `/services/` cards | Content Creation card: "Photo and video that looks sharp and performs across channels." | "…that look sharp and perform…" (plural subject) |
| `/about/` | The two `[NEEDS REVIEW]` sentences above | Syntactic fix, meaning kept |

## 4. Clean

No lorem ipsum, "Hello world!" (drafted in v27.4.x), "Sample Page", "Just another WordPress
site", "passionate about", "one-stop shop", "cutting-edge", "world-class", "synergy",
"game-changer", "results-driven" anywhere in published page content.
