const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext()).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  const s = await p.evaluate(() => M.cfg.sesskey); await p.goto(BASE + '/course/view.php?id=7&edit=on&sesskey=' + s, { waitUntil: 'networkidle' });
  const html = await (await p.request.get(BASE + '/course/modedit.php?update=29')).text();
  const lines = html.split('\n'); for (const n of [5536, 5537, 5599, 5600]) console.log(n + 1, (lines[n] || '').trim().slice(0, 200));
  const i = html.indexOf('first.js'); 
  await b.close();
})();
