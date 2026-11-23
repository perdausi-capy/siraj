const { chromium } = require('playwright-core');
const out = process.env.HOME + '/siraj-local/design/shots';
require('fs').mkdirSync(out, { recursive: true });
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  for (const [w, h, tag, full] of [[1440, 900, 'desktop', true], [390, 844, 'phone', false]]) {
    const ctx = await b.newContext({ viewport: { width: w, height: h }, deviceScaleFactor: tag === 'phone' ? 2 : 1 });
    const p = await ctx.newPage();
    for (const s of ['login', 'dash', 'course']) for (const t of ['light', 'dark']) {
      if (tag === 'phone' && t === 'dark' && s !== 'dash') continue;
      await p.goto(`file://${process.env.HOME}/siraj-local/design/index.html?screen=${s}&theme=${t}`, { waitUntil: 'networkidle' });
      await p.waitForTimeout(600);
      await p.screenshot({ path: `${out}/${tag}-${s}-${t}.png`, fullPage: full && s !== 'login' });
    }
    await ctx.close();
  }
  await b.close(); console.log('ok');
})();
