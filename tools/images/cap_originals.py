"""Downscale oversized original images in wp-content/uploads (v27.3.5).
Originals over 2560px on the long edge (or over 3 MB) and at least 1.5 MB are resized to
2560px, quality 82, same format and filename; originals are copied to BACKUP_DIR first.
Animated images, GIFs and SVGs are left alone. Live keeps the untouched originals.
Usage: python3 cap_originals.py <uploads dir> <backup dir>
Then: wp eval-file sync-original-dimensions.php"""
import os, sys, shutil
from PIL import Image

uploads, backup = sys.argv[1], sys.argv[2]
MAX, SKIP = 2560, ('fw-backup', 'wp-migrate-db', 'wp-defender', 'wpo', 'mainwp', 'breeze')
os.chdir(uploads)
saved = 0
for root, dirs, files in os.walk('.'):
    if root[2:].split('/')[0] in SKIP:
        continue
    for f in files:
        ext = f.lower().rsplit('.', 1)[-1]
        if ext not in ('webp', 'jpg', 'jpeg', 'png'):
            continue
        p = os.path.join(root, f)
        size = os.path.getsize(p)
        if size < 1_500_000:
            continue
        im = Image.open(p)
        if getattr(im, 'is_animated', False) or (max(im.size) <= MAX and size < 3_000_000):
            continue
        dst = os.path.join(backup, p[2:])
        os.makedirs(os.path.dirname(dst), exist_ok=True)
        shutil.copy2(p, dst)
        if max(im.size) > MAX:
            r = MAX / max(im.size)
            im = im.resize((round(im.size[0] * r), round(im.size[1] * r)), Image.LANCZOS)
        fmt = {'jpg': 'JPEG', 'jpeg': 'JPEG', 'webp': 'WEBP', 'png': 'PNG'}[ext]
        if fmt == 'JPEG' and im.mode != 'RGB':
            im = im.convert('RGB')
        im.save(p, fmt, **({'quality': 82} if fmt != 'PNG' else {'optimize': True}))
        saved += size - os.path.getsize(p)
        print(p, '%d KB → %d KB' % (size // 1024, os.path.getsize(p) // 1024))
print('saved %.1f MB' % (saved / 1e6))
