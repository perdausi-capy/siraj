const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => { const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  for (const [user, urls] of [['omar', ['/local/siraj/team.php', '/local/siraj/person.php?id=10', '/local/siraj/reports.php', '/local/siraj/matrix.php']], ['admin', ['/local/siraj/studio.php']]]) {
    for (const vp of [{ width: 1440, height: 900 }, { width: 390, height: 844 }]) {
      const p = await (await b.newContext({ viewport: vp })).newPage();
      for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
      await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
      await p.addInitScript(() => { window.__c = 0; new PerformanceObserver(l => { for (const e of l.getEntries()) if (!e.hadRecentInput) window.__c += e.value; }).observe({ type: 'layout-shift', buffered: true }); });
      for (const u of urls) { await p.goto(BASE + u, { waitUntil: 'load' }); await p.evaluate(() => window.scrollTo(0, document.body.scrollHeight)); await p.waitForTimeout(1500);
        console.log(`${vp.width} ${u.padEnd(34)} CLS ${(await p.evaluate(() => window.__c)).toFixed(3)}`); }
    } }
  await b.close(); })();
