# Content seed — Phase 2 pages on a fresh site (staging)

Everything below was run against Local on November 2 2026. The theme (with
`acf-json/`) and ACF Pro must be active first. Scripts save ACF values **by
field key** — `update_field()` by name silently stores raw meta when the field
group isn't resolved yet (see v27.0.12 fix).

| Order | What | Command |
|---|---|---|
| 1 | Blog posts (72) | `tools/blog-snapshot/README.md` |
| 2 | Podcast episodes (37) | `tools/podcast-import/README.md` |
| 3 | Legal pages under `/support/` | `tools/legal-import/README.md` |
| 4 | Case studies (2 from live + Valencia draft) | export live `work` posts to `work.json` (see v27.0.12 CHANGELOG), register images with `register-missing.php`, then `STARK_JSON=work.json wp eval-file seed-portfolio.php` and `STARK_GROUP=…/group_stark_portfolio.json wp eval-file fix-portfolio-fields.php` |
| 5 | Service pages (7) + draft copy | `wp eval-file seed-service-copy.php` (creates missing pages) |
| 6 | Services hub, About, Contact, Service Areas, Home, Blog | create pages with slugs `services`, `about`, `contact`, `service-areas`, `home`, `blog`; Settings → Reading: static front page = Home, posts page = Blog |
| 7 | Author profiles + About team | `wp eval-file seed-authors.php` (user IDs are Local's — adjust: Nathan = 1, Deanna = 3) |
| 8 | Homepage stats + FAQ | `wp eval-file seed-home.php` |
| 9 | Password generator | page `password-generator` under Support, content `[password_generator]` |

Attachment IDs in the seeds (headshots 463 / 269, case-study images
1775–1789, Valencia 500) are **live's IDs** — register them first with
`register-missing.php` so they exist at the same IDs.

Copy marked `[DRAFT - needs Nathan review]` / `[SAMPLE]` is placeholder.
