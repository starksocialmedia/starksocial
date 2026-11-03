# Breakpoint audit, v27.5.8 (Local)

20 page types × 320 / 390 / 768 / 1024 / 1440. `bp.mjs` (puppeteer-core, headless Chrome on
:9333) checks horizontal overflow, tap targets under 44×44 on touch widths (inline links inside
running text exempt), text under 12px, and images extending past the viewport.

| Check | Before | After |
|---|---|---|
| Horizontal overflow | `/support/password-generator/` at 320 (length display, options grid) | none |
| Text under 12px | none | none |
| Images cut off | none | none |
| Tap targets under 44px tall (touch) | 26–43 per page: footer links (28px), social icons (36px), filter chips (40px), standalone "→" links (18–31px), KB lists (42px), nav at 1024 (41px) | 4–13 per page; what's left is either 44px tall but narrower than 44px (short words like "SEO" in the footer, with full-height rows above and below) or a link inside running text |

`before.json` is the full run; `after-subset.json` re-ran 9 representative pages. Fixes are
`@media (pointer: coarse)` only, so desktop with a mouse looks the same.
