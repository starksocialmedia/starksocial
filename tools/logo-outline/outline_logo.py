"""Outline the Stark text logos: same viewBox, baselines, sizes, tracking and fills,
with <text> replaced by <path> glyph outlines from the theme's own Barlow WOFF2 files.
Pair kerning from GPOS is applied for the wordmark via fontTools' kern lookup when available."""
import sys
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen

FONTS = sys.argv[1]
def run(font_file, text, size, tracking_em, x, y):
    f = TTFont(f"{FONTS}/{font_file}")
    gs, cmap, hmtx = f.getGlyphSet(), f.getBestCmap(), f['hmtx']
    s = size / f['head'].unitsPerEm
    pen = SVGPathPen(gs, ntos=lambda v: ("%.2f" % v).rstrip("0").rstrip("."))
    cx = x
    for ch in text:
        g = cmap[ord(ch)]
        gs[g].draw(TransformPen(pen, (s, 0, 0, -s, cx, y)))
        cx += hmtx[g][0] * s + tracking_em * size
    return pen.getCommands(), cx

def build(fill_main, fill_sub_attr):
    d1, end1 = run('barlow-v13-latin-700.woff2', 'Stark', 24, 0.02, 0, 30)
    d2, end2 = run('barlow-condensed-v13-latin-500.woff2', 'SOCIAL MEDIA AGENCY', 9, 0.26, 92, 30)
    return f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 48" role="img" aria-label="Stark Social Media Agency">
  <!-- Outlined from Barlow 700 / Barlow Condensed 500 (theme fonts/) — no font dependency. Source text: "Stark" + "SOCIAL MEDIA AGENCY". -->
  <path fill="{fill_main}" d="{d1}"/>
  <path fill="{fill_main}" {fill_sub_attr}d="{d2}"/>
</svg>
''', end1, end2

out = sys.argv[2]
svg, e1, e2 = build('currentColor', 'opacity="0.65" ')
open(f'{out}/stark-logo.svg', 'w').write(svg)
svg, _, _ = build('#ffffff', 'opacity="0.65" ')
open(f'{out}/stark-logo-light.svg', 'w').write(svg)
print(f'wordmark ends x={e1:.1f}, sub ends x={e2:.1f} (viewBox width 320)')
