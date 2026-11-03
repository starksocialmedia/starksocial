// Breakpoint audit: overflow, small tap targets (non-inline), small text, clipped images.
import puppeteer from 'puppeteer-core';
import { writeFileSync } from 'node:fs';
const [outdir, ...paths] = process.argv.slice(2);
const widths = [320, 390, 768, 1024, 1440];
const b = await puppeteer.connect({ browserURL: 'http://127.0.0.1:9333', acceptInsecureCerts: true });
const p = await b.newPage();
const report = {};
for (const path of paths) for (const w of widths) {
  await p.setViewport({ width: w, height: 900, isMobile: w < 768, hasTouch: w < 1025 });
  await p.goto('https://starksocial.local' + path, { waitUntil: 'networkidle2' });
  await p.evaluate(() => { document.querySelectorAll('.stark-reveal,[data-reveal]').forEach(e => e.classList.add('is-visible','is-revealed')); document.querySelectorAll('#cmplz-cookiebanner-container,.cmplz-cookiebanner').forEach(e=>e.remove()); });
  const r = await p.evaluate((w) => {
    const vis = e => { const c = getComputedStyle(e); const r = e.getBoundingClientRect(); return c.visibility !== 'hidden' && c.display !== 'none' && r.width > 0 && r.height > 0 && !e.closest('[aria-hidden="true"],.screen-reader-text,.stark-mobile-nav,.stark-mega__panel,#wpadminbar'); };
    const name = e => e.tagName.toLowerCase() + (e.className && typeof e.className === 'string' ? '.' + e.className.trim().split(/\s+/).slice(0,2).join('.') : '') + ' "' + (e.textContent || e.getAttribute('aria-label') || '').trim().slice(0, 30) + '"';
    const overflow = document.documentElement.scrollWidth > w + 1 ? [...document.querySelectorAll('body *')].filter(e => e.getBoundingClientRect().right > w + 1 && vis(e) && getComputedStyle(e).position !== 'fixed').slice(0, 4).map(name) : [];
    const small = w < 1025 ? [...document.querySelectorAll('a[href], button, input:not([type=hidden]), select, summary, [role=button]')].filter(vis).filter(e => {
      const r = e.getBoundingClientRect();
      // inline links inside running text are exempt (WCAG 2.5.8 inline exception)
      if (e.tagName === 'A' && getComputedStyle(e).display === 'inline' && e.closest('p,li,dd,figcaption,td') && e.closest('p,li,dd,figcaption,td').textContent.trim().length > e.textContent.trim().length + 15) return false;
      return r.height < 44 || r.width < 44;
    }).map(e => { const r = e.getBoundingClientRect(); return name(e) + ` ${Math.round(r.width)}x${Math.round(r.height)}`; }) : [];
    const tiny = [...document.querySelectorAll('main *, footer *')].filter(e => e.childElementCount === 0 && e.textContent.trim() && vis(e) && parseFloat(getComputedStyle(e).fontSize) < 12).slice(0, 5).map(e => name(e) + ' ' + getComputedStyle(e).fontSize);
    const imgs = [...document.querySelectorAll('main img')].filter(vis).filter(i => { const r = i.getBoundingClientRect(); return r.left < -1 || r.right > w + 1; }).map(name);
    return { overflow, small: [...new Set(small)].slice(0, 12), smallCount: small.length, tiny, imgs };
  }, w);
  report[`${path} @${w}`] = r;
}
writeFileSync(`${outdir}/bp.json`, JSON.stringify(report, null, 1));
for (const [k, v] of Object.entries(report)) if (v.overflow.length || v.smallCount || v.tiny.length || v.imgs.length) console.log(k, JSON.stringify(v));
await p.close(); await b.disconnect();
