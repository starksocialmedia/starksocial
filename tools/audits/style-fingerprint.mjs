// node styles.mjs <out.json> — computed-style fingerprint of every element on key pages, 390 + 1440
import puppeteer from 'puppeteer-core';
import { writeFileSync } from 'node:fs';
const urls = ['/', '/services/', '/web-design/', '/web-design/antelope-valley/', '/service-areas/', '/work/', '/portfolio/pathway-escrow/', '/blog/', '/avoid-these-5-social-media-snafus/', '/podcast/', '/podcast/the-social-life-episode-37-boost-your-online-business-presence-in-under-1-hour-a-week/', '/about/', '/contact/', '/support/', '/support/knowledgebase/', '/docs/what-we-need-to-get-started-with-seo/', '/support/password-generator/', '/support/privacy-policy/', '/author/nateimhoff/', '/404-error-page/', '/?s=seo'];
const b = await puppeteer.connect({ browserURL: 'http://127.0.0.1:9333', acceptInsecureCerts: true });
const out = {};
for (const u of urls) for (const w of [390, 1440]) {
  const p = await b.newPage();
  await p.setViewport({ width: w, height: 900 });
  await p.goto('https://starksocial.local' + u, { waitUntil: 'networkidle2', timeout: 60000 });
  out[`${u}@${w}`] = await p.evaluate(() => {
    const res = [];
    const walk = (el, path) => {
      const cs = getComputedStyle(el);
      let s = ''; for (let i = 0; i < cs.length; i++) { const k = cs[i]; if (k.startsWith('--') || k.includes('transition') || k.includes('animation')) continue; s += k + ':' + cs.getPropertyValue(k) + ';'; }
      for (const pe of ['::before', '::after']) { const c = getComputedStyle(el, pe); if (c.content && c.content !== 'none' && c.content !== 'normal') s += pe + c.content + c.color + c.width + c.display; }
      let h = 0; for (let i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) | 0;
      res.push(path + '|' + h);
      [...el.children].forEach((c, i) => walk(c, path + '>' + c.tagName + (c.className && typeof c.className === 'string' ? '.' + c.className.trim().split(/\s+/)[0] : '') + i));
    };
    walk(document.body, 'BODY');
    return res;
  });
  await p.close();
}
writeFileSync(process.argv[2], JSON.stringify(out));
console.log('pages', Object.keys(out).length, 'elements', Object.values(out).reduce((a, x) => a + x.length, 0));
b.disconnect();
