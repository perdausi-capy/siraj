const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage(); const errs = [];
  p.on('pageerror', e => errs.push((e.stack || e.message).split('\n').slice(0, 4).join(' <- ')));
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  for (let i = 0; i < 4; i++) { await p.goto(BASE + '/course/modedit.php?update=29', { waitUntil: 'networkidle' }); await p.waitForTimeout(800); }
  const s = await p.evaluate(() => M.cfg.sesskey); await p.goto(BASE + '/course/view.php?id=7&edit=on&sesskey=' + s, { waitUntil: 'networkidle' }); await p.goto(BASE + '/course/modedit.php?add=page&course=7&section=1&return=0', { waitUntil: 'networkidle' });
  await p.goto(BASE + '/course/modedit.php?update=29', { waitUntil: 'networkidle' }); await p.waitForTimeout(800);
  console.log(errs.length ? errs.join('\n') : 'no errors in 5 loads');
  await b.close();
})();
