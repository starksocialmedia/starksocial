// Open Graph images, 1200x630 (v27.4.7). Renders an HTML template in headless
// Chrome (remote debugging on :9333) and writes JPEGs to stark-social/img/og/.
// Only for pages with no share image of their own: live's branded images are
// used everywhere else (tools/rankmath/import-live-meta.php).
//   node tools/images/og/generate.mjs        (needs puppeteer-core)
import puppeteer from 'puppeteer-core';
import { readFileSync, writeFileSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const theme = resolve(here, '../../../stark-social');
const pages = JSON.parse(readFileSync(`${here}/pages.json`, 'utf8'));
const font = w => readFileSync(`${theme}/fonts/barlow-v13-latin-${w}.woff2`).toString('base64');
const cond = readFileSync(`${theme}/fonts/barlow-condensed-v13-latin-600.woff2`).toString('base64');
const logo = readFileSync(`${theme}/img/stark-logo-light.svg`, 'utf8').replace(/role="img"[^>]*?>/, 'aria-hidden="true">');
const esc = s => s.replace(/&/g, '&amp;').replace(/</g, '&lt;');

const html = p => `<!doctype html><html><head><meta charset="utf-8"><style>
@font-face{font-family:B;font-weight:500;src:url(data:font/woff2;base64,${font(500)})}
@font-face{font-family:B;font-weight:700;src:url(data:font/woff2;base64,${font(700)})}
@font-face{font-family:BC;font-weight:600;src:url(data:font/woff2;base64,${cond})}
html,body{margin:0;width:1200px;height:630px}
body{position:relative;overflow:hidden;font-family:B,sans-serif;color:#fff;
 background:radial-gradient(900px 600px at 100% 100%,rgba(42,112,204,.95),rgba(42,112,204,0) 70%),linear-gradient(135deg,#031B34 0%,#0A2C55 55%,#1F5DAE 100%)}
body:after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(135deg,rgba(255,255,255,.035) 0 2px,transparent 2px 22px)}
.logo{position:absolute;left:80px;top:64px;width:400px}
.logo svg{width:100%;height:auto;display:block}
.text{position:absolute;left:80px;right:120px;bottom:96px}
.eyebrow{font-family:BC;font-weight:600;font-size:26px;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.72);margin:0 0 14px}
h1{font-weight:700;font-size:${p.title.length > 16 ? 92 : 112}px;line-height:1;letter-spacing:-.02em;margin:0 0 26px}
.tag{font-weight:500;font-size:34px;line-height:1.3;color:rgba(255,255,255,.88);margin:0;max-width:900px}
.url{position:absolute;right:80px;top:80px;font-family:BC;font-weight:600;font-size:24px;letter-spacing:.1em;color:rgba(255,255,255,.6)}
.bar{position:absolute;left:80px;bottom:64px;width:96px;height:6px;border-radius:3px;background:#5AA2FF}
</style></head><body><div class="logo">${logo}</div><div class="url">STARKSOCIAL.COM</div>
<div class="text"><p class="eyebrow">${esc(p.eyebrow)}</p><h1>${esc(p.title)}</h1><p class="tag">${esc(p.tagline)}</p></div><div class="bar"></div></body></html>`;

const b = await puppeteer.connect({ browserURL: 'http://127.0.0.1:9333' });
const page = await b.newPage();
await page.setViewport({ width: 1200, height: 630, deviceScaleFactor: 1 });
for (const [slug, p] of Object.entries(pages)) {
  await page.setContent(html(p), { waitUntil: 'load' });
  await page.evaluate(() => document.fonts.ready);
  const png = `/tmp/stark-og-${slug}.png`, jpg = `${theme}/img/og/${slug}.jpg`;
  await page.screenshot({ path: png, type: 'png' });
  execFileSync('sips', ['-s', 'format', 'jpeg', '-s', 'formatOptions', '85', png, '--out', jpg], { stdio: 'ignore' });
  console.log(jpg.replace(theme + '/', ''));
}
await page.close(); await b.disconnect();
