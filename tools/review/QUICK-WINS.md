# Nathan's quick wins: each under 5 minutes

Crank through these in 30–60 minutes. Ordered by what they unblock; ⛔ = launch blocker.

| # | Do this | Where | Unblocks |
|---|---|---|---|
| 1 | ⛔ **Set Cloudflare caching level to Standard** (Caching → Configuration), and check the same in **Cloudways → Application → Cloudflare** (the apex goes through Cloudways' Cloudflare Enterprise) | Cloudflare, Cloudways | stale CSS/JS after deploys; launch |
| 2 | **Delete Perfex test leads #126 and #127** (#127 from the v27.5 staging form test) | hub.starksocial.com → Leads | clean CRM |
| 3 | **Pick the homepage hero**: keep Phase 2's, or use live's "Elevate Your Brand. Build Something That Lasts." | reply in chat, or edit Home → Hero | Home approval |
| 4 | ~~Content Creation CTA~~ **done v27.5.9**: now "Ready to turn your content into a system?". Reword if you like | Pages → Content Creation → Final CTA | — |
| 5 | **About CTA**: "fractional marketing partner" → keep or reword | Pages → About | removes a `[NEEDS REVIEW]` |
| 6 | ~~About story~~ **done v27.5.9**: "The organizations we work with are doing meaningful work." / "…strategic ownership holding it all together." Check you're happy | Pages → About | — |
| 7 | **Pick the LinkedIn URL**: `/company/stark-social` or `/company/stark-social-media-agency` | reply in chat | footer + schema `sameAs` |
| 8 | **Episode short links**: OK that `/N` goes to episode N (live sends 33 of 37 elsewhere)? | reply yes/no | podcast covers |
| 9 | **Classic Editor + hide admin notices** (live WPCode snippets 852, 908): keep or drop? | reply | editor setup |
| 10 | **Approve the pages you're happy with**: set Copy status → Approved (`/web-design/`, `/seo/`, `/social-media-management/`, `/paid-advertising/`, `/services/` are your live copy) | each page editor, top field | ⛔ copy approval |
| 11 | **Pathway Escrow / Schlickart: Santa Clarita clients?** If yes, set **Market** | Portfolio → each → Market | shows them on `/service-areas/` |
| 12 | **Homepage FAQ**: keep, trim or remove (8 draft Qs) | Pages → Home → FAQ | Home approval |
| 13 | **Perfex chat**: on for launch (set the widget API key in the hub's PRChat settings) or launch without it? | hub, or reply | chat; cookie fix |
| 14 | **Renew the hub's SSL certificate** on the origin | Cloudways → hub app → SSL | future "Full (strict)" Cloudflare mode |
| 15 | **Antelope Valley sample page**: draft it for launch, or keep (noindex)? | reply | no `[SAMPLE]` copy at launch |
| 16 | ~~SEO meta description~~ **done v27.5.8** (150 characters) | — | — |
| 17 | **Check the favicon in Safari / Firefox** and add the site to your iPhone home screen | staging | favicon sign-off |

| 18 | **Logo exports** (not in any source, so not made up): one-colour white and navy versions of the wordmark, and a script-only "Stark" SVG for small sizes. From the `.ai` master; see `tools/brand-assets/LOGOS.md` | Illustrator | Safari pinned-tab icon, sharper small header logo |
| 19 | **Ask Pathway Escrow and Schlickart for a two-sentence quote**, and OK reusing Barbara Myler's 2017 Valencia Jazz quote (`tools/content-mining/testimonial-candidates.md`) | email | first testimonials on the site |
| 20 | **Pick case studies from the 2015 client list** (SCV Fixit is the strongest), and political clients on `/work/`: yes or no? (`case-study-candidates.md`) | reply | more than 2 case studies |
| 21 | **Replace the unnamed "Trusted by…" hero lines** on SEO and Web Design with a real proof point, or drop them (`generic-copy-audit.md` §2) | Pages → SEO / Web Design → Hero note | less generic |

Bigger items that aren't quick wins: Brand Strategy process + FAQ, Audit & Consulting page,
Service Areas copy, legal review, Content Creation rewrite (what is it at Stark?), repeated buzzwords across service pages (`tools/content-mining/generic-copy-audit.md`).
