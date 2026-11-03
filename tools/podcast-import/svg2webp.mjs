// node svg2webp.mjs svgs.tsv  — render each SVG cover in headless Chrome, save 1280x720 + 640x360 WebP beside it
import { readFileSync, writeFileSync, statSync } from 'node:fs';
const rows = readFileSync(process.argv[2], 'utf8').trim().split('\n').map(l => l.split('\t'));
const targets = await (await fetch('http://127.0.0.1:9333/json/list')).json();
const ws = new WebSocket(targets.find(t => t.type === 'page').webSocketDebuggerUrl);
await new Promise(r => ws.addEventListener('open', r, { once: true }));
let id = 0; const p = new Map();
ws.addEventListener('message', e => { const m = JSON.parse(e.data); if (m.id) p.get(m.id)?.(m); });
const send = (method, params = {}) => new Promise(r => { const i = ++id; p.set(i, r); ws.send(JSON.stringify({ id: i, method, params })); });
const sleep = ms => new Promise(r => setTimeout(r, ms));
await send('Emulation.setDeviceMetricsOverride', { width: 1920, height: 1080, deviceScaleFactor: 1, mobile: false });
const out = [];
for (const [att, file] of rows) {
  await send('Page.navigate', { url: 'file://' + file });
  await sleep(1200);
  const res = {};
  for (const w of [1280, 640]) {
    const h = Math.round(w * 9 / 16);
    const shot = await send('Page.captureScreenshot', { format: 'webp', quality: 82, clip: { x: 0, y: 0, width: 1920, height: 1080, scale: w / 1920 } });
    const dest = file.replace(/\.svg$/i, `-${w}x${h}.webp`);
    writeFileSync(dest, Buffer.from(shot.result.data, 'base64'));
    res[w] = { file: dest.split('/').pop(), w, h, bytes: statSync(dest).size };
  }
  out.push({ att, svg_bytes: statSync(file).size, ...res });
  console.log(att, statSync(file).size, res[1280].bytes, res[640].bytes);
}
writeFileSync('webp-covers.json', JSON.stringify(out, null, 1));
process.exit(0);
