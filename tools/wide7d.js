const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: +process.argv[4] || 390, height: 844 }, isMobile: false })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', process.argv[2]); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + process.argv[3], { waitUntil: 'networkidle' });
  console.log(await p.evaluate(() => { const vw = document.documentElement.clientWidth; const out = [];
    document.querySelectorAll('body *').forEach(el => { const r = el.getBoundingClientRect(); if (r.right > vw + 1 && r.width > 0) {
      let a = el.parentElement, clipped = false; while (a) { const s = getComputedStyle(a); if (/(auto|scroll|hidden|clip)/.test(s.overflowX) && a !== document.body && a !== document.documentElement) { clipped = true; break; } a = a.parentElement; }
      if (!clipped) out.push(el.tagName + '.' + [...el.classList].join('.') + ' right=' + Math.round(r.right)); } });
    return out.slice(0, 15).join('\n'); }));
  await b.close();
})();
