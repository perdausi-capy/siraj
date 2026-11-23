const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 320, height: 700 }, isMobile: true })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'omar'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/local/siraj/person.php?id=10', { waitUntil: 'networkidle' });
  console.log(await p.evaluate(() => [...document.querySelectorAll('.siraj-dash-grid, .siraj-dash-grid > *, .siraj-dash-main > section')].map(e => {
    const s = getComputedStyle(e); return e.className + ' w=' + Math.round(e.getBoundingClientRect().width) + ' cols=' + s.gridTemplateColumns + ' minw=' + s.minWidth; }).join('\n')));
  await b.close();
})();
