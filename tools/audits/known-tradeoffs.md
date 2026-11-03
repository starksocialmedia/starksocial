# Known tradeoffs (accepted for launch)

## Lighthouse Best Practices 81–82 on staging: Cloudflare JavaScript Detections

**What:** every staging page scores 81/82 for Best Practices (100 on Local). Lighthouse flags two
deprecation warnings from `/cdn-cgi/challenge-platform/scripts/jsd/main.js`.

**Where it comes from:** Cloudflare injects that script into every HTML response at the edge.
`jsd` is Cloudflare's **JavaScript Detections** (part of Bot Fight Mode / Bot Management). It isn't
in the theme or WordPress: the origin HTML doesn't contain it (checked November 3 2026: the
staging response through Cloudflare has one `challenge-platform` reference, the theme templates
have none).

**Can Phase 2 fix it?** No, not from the site:
- Removing it needs a Cloudflare setting (Security → Bots: turn off Bot Fight Mode / JavaScript
  Detections), which also turns off that bot protection
- A Content-Security-Policy that blocks it would make Cloudflare's own bot check fail and log a
  console error, which Lighthouse also penalises under Best Practices
- The warnings are about browser APIs Cloudflare's script uses; only Cloudflare can update it

**Production:** the apex goes through **Cloudways' Cloudflare Enterprise** add-on, not Nathan's
zone (see `tools/launch/live-inventory.md`). Whether JS Detections runs there depends on the
Cloudways add-on's bot settings, so the production score may differ from staging. Re-run
Lighthouse on production after launch.

**Decision:** accept. Real visitors aren't affected (no visible change, no measurable
performance cost in the staging runs: performance 92–100 desktop). Nathan can trade bot
protection for the score in Cloudflare if he wants the 100.

## SEO 58–66 on staging

Staging sends `noindex` (by design). Not a production issue; Local scores 100.
