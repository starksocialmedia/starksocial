# Logo outline

Regenerates `stark-social/img/stark-logo.svg` (fill `currentColor`) and
`stark-logo-light.svg` (white) with the text converted to paths, using the
theme's own Barlow WOFF2 files. Same viewBox, baselines, sizes and tracking as
the text versions — no font dependency when the SVG is used as an `<img>`.

Interim: Nathan is replacing these with the real wordmark files.

```bash
python3 -m venv /tmp/logo-venv && /tmp/logo-venv/bin/pip install fonttools brotli
/tmp/logo-venv/bin/python tools/logo-outline/outline_logo.py stark-social/fonts stark-social/img
```

No GPOS pair kerning is applied (tracking only), so spacing can differ by a
fraction of a pixel from browser-rendered text.
