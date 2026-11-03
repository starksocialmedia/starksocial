# Logo outline (superseded in v27.5.0)

**No longer used.** The theme now uses the real Stark logo files (`stark-social/img/logos/`,
see `tools/brand-assets/LOGOS.md`). The two outlined text logos this script made
(`img/stark-logo.svg`, `img/stark-logo-light.svg`) were removed in v27.5.0.


Regenerates `stark-social/img/stark-logo.svg` (fill `currentColor`) and
`stark-logo-light.svg` (white) with the text converted to paths, using the
theme's own Barlow WOFF2 files. Same viewBox, baselines, sizes and tracking as
the text versions — no font dependency when the SVG is used as an `<img>`.


```bash
python3 -m venv /tmp/logo-venv && /tmp/logo-venv/bin/pip install fonttools brotli
/tmp/logo-venv/bin/python tools/logo-outline/outline_logo.py stark-social/fonts stark-social/img
```

No GPOS pair kerning is applied (tracking only), so spacing can differ by a
fraction of a pixel from browser-rendered text.
