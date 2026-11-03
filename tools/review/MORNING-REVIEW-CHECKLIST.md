# Morning review checklist — for Nathan

November 3 2026, after v27.4.x. Staging: **https://staging.starksocial.com** (noindex; emails
blocked). Work top to bottom: the top section unblocks launch. For the 5-minute items, see
`QUICK-WINS.md`.

## 1. Copy that needs your voice (launch-blocking)

Every page shows a coloured bar until its **Copy status** is Approved (page editor, top).
Yellow = Draft, blue = Live-sourced (your live copy, carried over as is).

| Priority | URL | What to do |
|---|---|---|
| 1 | `/brand-strategy-identity/` | **Write the process steps and FAQ.** Live's were copies of the SEO and Web Design pages. Hero, outcomes and CTA are live copy |
| 2 | `/audit-consulting/` | Whole page is draft (new service, no live page). Also its share image tagline: regenerate after (`tools/images/og/`) |
| 3 | `/content-creation/` | `[NEEDS REVIEW]` on the final CTA: "Ready to turn paid social into a system?" is Paid Advertising's CTA |
| 4 | `/about/` | Two `[NEEDS REVIEW]`: the story ("They're doing meaningful work" has no antecedent; "…strategic ownership. holding it all together" is broken) and the CTA ("fractional marketing partner") |
| 5 | `/`, `/social-media-management/`, `/paid-advertising/`, `/web-design/`, `/seo/`, `/services/`, `/contact/` | Read through your live copy in the new layout; set Approved |
| 6 | `/` hero | Keep the Phase 2 hero, or use live's "Elevate Your Brand. Build Something That Lasts." |
| 7 | `/` FAQ | 8 Phase 2 draft questions (live has no FAQ): approve, edit or remove |
| 8 | `/service-areas/` | New hub. The intro paragraph and 5 market descriptions are `[DRAFT]` placeholders. Check the tone: "earned, not salesy" |
| 9 | `/web-design/antelope-valley/` | Sample location page: write it or set it to draft/noindex for launch |

## 2. Look at these on staging (new since yesterday)

- [ ] `/service-areas/`: map, numbered markets, local pages / case studies / posts per market
- [ ] `/web-design/`: case studies at the bottom (2 since v27.5.1), then "From the blog: Reading on Web Design"
- [ ] Any blog post, e.g. `/planning-notes-santa-clarita-web-design-seo/`: "Related services" row at the end; first mention of a service or market in the text is now a link (one per paragraph, max 2–3 per post). **Tell me if any of these links read badly**; they're automatic and can be tuned
- [ ] `/no-such-page/`: popular pages, latest post and episode, "Report a broken link" (emails you with the URL)
- [ ] `/?s=facebook`: filter chips (Articles / Podcast / Knowledgebase…), highlighted matches
- [ ] `/podcast/does-your-biz-really-need-a-video-app/`: the meerkat GIF is now a 0.3 MB video (was 3.3 MB). Click pauses
- [ ] `/docs/what-is-seo-and-how-long-does-it-take/`: looks unchanged (landmark fix is invisible)
- [ ] Browser tab icon in **Safari and Firefox** (only Chrome was checked); add to iPhone home screen once
- [ ] Share a service URL in Slack or iMessage to see the preview image (live's images on 12 pages; generated ones on SEO, Audit & Consulting, Service Areas, Antelope Valley)

## 3. Decisions (each is a one-liner; see QUICK-WINS.md)

- LinkedIn URL: `/company/stark-social` (live footer) or `/company/stark-social-media-agency` (Phase 2)?
- Classic Editor (live disables the block editor) and "hide all admin notices": keep or drop?
- Episode short links `/1`–`/37`: Phase 2 sends `/N` to episode N (live sends 33 of 37 to the wrong episode). OK?
- Perfex chat: the hub has it switched off (no API key). Turn on for launch, or launch without chat?
- Wordfence and MainWP on Phase 2 after launch?
- Pathway Escrow and Schlickart: are they Santa Clarita clients? If so, set **Market** in each case study (shows them on `/service-areas/`)

## 4. Before launch day (accounts only you can reach)

- Cloudflare **and Cloudways → Application → Cloudflare** (the apex goes through Cloudways' Cloudflare Enterprise): caching level Standard
- Cloudways: renew the **hub** app's SSL certificate (expired Nov 2 on the origin; visitors still OK)
- Cloudways: look at **Redis** on the live server (Object Cache Pro logged ~185k "Redis went away" errors)
- Perfex: delete test lead #126; check `lead.php` maps `company` and `city`
- Legal pages: review (last updated Jan 15 2026)
- Real logo files, when you have them

## Reference

- What's on live and what happens to it: `tools/launch/live-inventory.md`
- Every redirect: `tools/launch/redirect-map.md`
- Status of every launch item: `tools/launch/LAUNCH-RUNBOOK.md` §0
- What changed: `CHANGELOG.md` (v27.4.0–v27.4.11)
