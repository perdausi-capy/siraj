const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1280, height: 800 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/local/siraj/teaching.php', { waitUntil: 'networkidle' });
  console.log(await p.evaluate(() => ['.siraj-teaching', '.siraj-dash-grid', '.siraj-dash-main', '.siraj-side', '.siraj-kpis', '#region-main', '.main-inner'].map(s => { const e = document.querySelector(s); if (!e) return s + ' none'; const r = e.getBoundingClientRect(); return `${s} ${Math.round(r.left)}-${Math.round(r.right)} cols=${getComputedStyle(e).gridTemplateColumns}`; }).join('\n')));
  await b.close();
})();
