const { chromium } = require('playwright-core');
const out = process.env.HOME + '/siraj-local/design/proto/shots';
require('fs').mkdirSync(out, { recursive: true });
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  await p.goto('file://' + process.env.HOME + '/siraj-local/design/proto/siraj_learning_prototype.html', { waitUntil: 'networkidle' });
  for (const v of ['home','learning','course','player','quiz','team','admin','signin']) {
    await p.evaluate(v => go(v), v); await p.waitForTimeout(300);
    await p.screenshot({ path: `${out}/${v}.png`, fullPage: true });
  }
  await b.close(); console.log('ok');
})();
