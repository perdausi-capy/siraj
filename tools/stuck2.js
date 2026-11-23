const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext()).newPage(); const started = new Map();
  p.on('request', r => started.set(r, Date.now()));
  p.on('requestfinished', r => { const t = Date.now() - (started.get(r) || Date.now()); if (t > 1000) console.log(`   slow ${t}ms ${r.url().replace(BASE, '').slice(0, 120)}`); });
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'sara'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  for (let i = 0; i < 3; i++) { const t0 = Date.now(); await p.goto(`${BASE}/local/siraj/learn.php?cmid=47`, { waitUntil: 'load' }); console.log(`h5p visit ${i + 1}: ${Date.now() - t0}ms`); }
  await b.close();
})();
