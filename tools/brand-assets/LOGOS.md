# Stark logos: where they came from and where they're used

v27.5.0, November 3 2026. Replaces the interim outlined-text logo (`tools/logo-outline/`).

## Found

| Source | What's there | Used |
|---|---|---|
| **This repo, `brand-sheet/stark/assets/logos/`** (Brand Guide v2.0.8.1) | `stark-wordmark-dark.svg`, `stark-wordmark-light.svg` (all vector, 23 KB), `stark-icon.svg` and `stark-web-icon.svg` (760 KB each: vector + an embedded PNG sky texture) | **Yes**: the two wordmarks, copied unchanged |
| `brand-sheet/source-files/stark-logo-master-EDITABLE.ai` | Illustrator master | No (source only) |
| **Live, `wp-content/uploads/2025/08/stark-social-media-logo-official.svg`** | The cloud speech-bubble lockup, all vector, 25 KB. It's live's header logo | **Yes**: as `stark-mark-color.svg` (viewBox cropped to the artwork, nothing else changed) |
| Perfex hub, `media/public/custom/emails/stark-logo-light.png` (352×204) and `stark-logo-dark.png` | The script "Stark" without the "Social Media Agency" line, raster | No: same artwork as the wordmark SVGs, raster only |
| Live site icon (`cropped-stark-social-media-agency-icon.webp`) | Script wordmark on the blue sky square | Already the source of the favicon suite (v27.4.6), so favicons didn't change |
| Live `stark-assets/` | Doesn't exist on live | — |

All four sources show the same artwork, so there's no doubt about which logo is current.

## In the theme: `stark-social/img/logos/`

| File | What | Where it's used |
|---|---|---|
| `stark-wordmark-dark.svg` | Full-colour wordmark, dark "Social Media Agency" line, blue offset shadow. For light backgrounds | Header (frosted/white nav) |
| `stark-wordmark-light.svg` | Full-colour wordmark, white line, grey shadow. For dark backgrounds | Header over dark heroes, footer, login page, generated share images (`tools/images/og/`) |
| `stark-wordmark-{dark,light}.png`, `@2x.png` | 240 px wide (480 px at 2x), transparent | `@2x` light: email header (`inc/stark-emails.php`; email clients don't render SVG) |
| `stark-mark-color.svg` | Cloud speech-bubble lockup | Source for raster use |
| `stark-mark-color.png`, `@2x.png` | 256 px wide (512 px at 2x) | Organization schema `logo` (`inc/stark-seo.php`; Rank Math's logo setting is empty) |

Other places:
- **Admin bar:** the 32 px favicon (sky square) replaces the WordPress logo menu (`inc/stark-admin.php`).
- **Favicons:** unchanged (`img/favicon/`, already made from live's real icon in v27.4.6).

Rasters were rendered from the SVGs in headless Chrome (transparent background).

## Not available (not invented)

| Wanted | Why it's missing | Needed from Nathan |
|---|---|---|
| **One-colour marks** (`stark-mark-light.svg` all-white, `stark-mark-dark.svg` all-navy) | No one-colour version exists in any source. Making one means redrawing the shadow and outline layers, which is a design decision | Export from the `.ai` master: wordmark in solid white and solid `#004C97`, shadow layers removed |
| **Horizontal lockup** (script + "Social Media Agency" side by side) | Only the stacked lockup exists. In the header the stacked line is ~5 px tall and not readable | Optional: a horizontal lockup, or a script-only "Stark" SVG for small sizes (the hub's PNG is that, but raster) |
| **Safari pinned-tab / mask icon** | Needs a single-colour silhouette (above) | Same export as the one-colour marks |
| **Vector of the cloud icon without the embedded raster** | `stark-icon.svg` embeds a 1080×567 PNG texture | Not needed now: live's vector `official.svg` covers it |

## Swapping in new files later

Drop replacements into `stark-social/img/logos/` with the same names. Header sizing is by height
(`custom.css`: 64 px over the hero, 50 px scrolled; footer 72 px), so a different aspect ratio
only needs the `width`/`height` attributes in `templates/stark-header.php` and
`templates/stark-footer.php` updated. Re-run `node tools/images/og/generate.mjs` for the share
images and re-render the PNGs.
