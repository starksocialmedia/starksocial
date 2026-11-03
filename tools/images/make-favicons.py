#!/usr/bin/env python3
"""Favicon suite from live's site icon (v27.4.6).

Source: uploads/2026/01/cropped-stark-social-media-agency-icon.webp (512x512,
live's WordPress site icon). No new artwork: every file is a resize of it.
Usage: python3 tools/images/make-favicons.py <source.png|webp> <out-dir>
"""
import json, sys
from PIL import Image

src, out = sys.argv[1], sys.argv[2].rstrip('/')
im = Image.open(src).convert('RGB')

def save(px, name):
    im.resize((px, px), Image.LANCZOS).save(f'{out}/{name}', optimize=True)

save(16, 'favicon-16x16.png')
save(32, 'favicon-32x32.png')
save(180, 'apple-touch-icon.png')
save(192, 'android-chrome-192x192.png')
save(512, 'android-chrome-512x512.png')
# Windows tile: the icon centred on the tile colour, as Microsoft's template does.
tile = Image.new('RGB', (150, 150), (3, 27, 52))
tile.paste(im.resize((108, 108), Image.LANCZOS), (21, 21))
tile.save(f'{out}/mstile-150x150.png', optimize=True)
im.save(f'{out}/favicon.ico', sizes=[(16, 16), (32, 32), (48, 48)])

json.dump({
    'name': 'Stark Social Media Agency',
    'short_name': 'Stark Social',
    'icons': [
        {'src': 'android-chrome-192x192.png', 'sizes': '192x192', 'type': 'image/png'},
        {'src': 'android-chrome-512x512.png', 'sizes': '512x512', 'type': 'image/png'},
    ],
    'theme_color': '#031B34',
    'background_color': '#031B34',
    'display': 'browser',
    'start_url': '/',
}, open(f'{out}/site.webmanifest', 'w'), indent=2)
open(f'{out}/browserconfig.xml', 'w').write(
    '<?xml version="1.0" encoding="utf-8"?>\n<browserconfig><msapplication><tile>'
    '<square150x150logo src="/wp-content/themes/stark-social/img/favicon/mstile-150x150.png"/>'
    '<TileColor>#031b34</TileColor></tile></msapplication></browserconfig>\n')
print('ok')
