// node render.js <scene> <w> <h> <fps> <outdir>: frames of one seamless loop.
const { chromium } = require('/home/perdausi/siraj-local/tools/node_modules/playwright-core');
const [scene, w, h, fps, out] = [process.argv[2], +process.argv[3], +process.argv[4], +process.argv[5], process.argv[6]];
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: w, height: h } })).newPage();
  await p.goto('file://' + __dirname + '/scenes.html'); await p.evaluate(([w, h]) => size(w, h), [w, h]);
  const T = await p.evaluate(() => T), n = Math.round(T * fps);
  require('fs').mkdirSync(out, { recursive: true });
  for (let i = 0; i < n; i++) {
    await p.evaluate(([s, t]) => render(s, t), [scene, i / fps]);
    await p.screenshot({ path: `${out}/f${String(i).padStart(4, '0')}.png` });
  }
  await b.close(); console.log(scene, n, 'frames');
})();
