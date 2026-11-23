const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/mod/assign/view.php?id=29&action=grading', { waitUntil: 'networkidle' });
  console.log(await p.evaluate(() => {
    const t = document.querySelector('table.gradingtable, .gradingtable table, table.flexible'); if (!t) return 'no table';
    const th = t.querySelector('thead'), r1 = t.querySelector('tbody tr');
    const s = getComputedStyle(th); const cs = el => { const c = getComputedStyle(el); return `pos=${c.position} top=${c.top} mt=${c.marginTop} tr=${c.transform}`; };
    const out = ['thead ' + JSON.stringify(th.getBoundingClientRect().toJSON()) + ' ' + cs(th), 'row1 ' + JSON.stringify(r1.getBoundingClientRect().toJSON())];
    let a = t; for (let i = 0; i < 5 && a; i++) { out.push(a.tagName + '.' + [...a.classList].slice(0, 3).join('.') + ' ' + cs(a) + ' h=' + Math.round(a.getBoundingClientRect().height) + ' ov=' + getComputedStyle(a).overflow); a = a.parentElement; }
    t.querySelectorAll('thead th').forEach((x, i) => { if (i < 1) out.push('th ' + cs(x)); });
    return out.join('\n');
  }));
  await b.close();
})();
