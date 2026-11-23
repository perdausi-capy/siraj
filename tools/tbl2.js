const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  for (const u of ['/mod/assign/view.php?id=29&action=grading', '/report/progress/index.php?course=7', '/mod/quiz/report.php?id=30&mode=overview', '/grade/report/grader/index.php?id=7']) {
    await p.goto(BASE + u, { waitUntil: 'networkidle' });
    console.log(u, await p.evaluate(() => { const t = document.querySelector('#region-main table.flexible, #region-main table.generaltable, #region-main table'); if (!t) return 'no table';
      const chain = []; let a = t.parentElement; for (let i = 0; i < 4 && a; i++) { chain.push(a.tagName + '.' + [...a.classList].slice(0, 2).join('.') + ' id=' + a.id + ' ox=' + getComputedStyle(a).overflowX + ' w=' + Math.round(a.getBoundingClientRect().width)); a = a.parentElement; }
      const box = t.parentElement; const r = box.getBoundingClientRect(); const main = document.querySelector('#region-main').getBoundingClientRect();
      return `table ${Math.round(t.scrollWidth)}px, box ${Math.round(r.width)}px scrolls=${box.scrollWidth > box.clientWidth} inside-main=${r.right <= main.right + 1} | ${chain.join(' > ')}`; }));
  }
  await p.goto(BASE + '/mod/assign/view.php?id=29&action=grading', { waitUntil: 'networkidle' });
  const menus = p.locator('#region-main table .dropdown-toggle, #region-main table [data-bs-toggle="dropdown"]');
  const n = await menus.count(); console.log('row menus:', n);
  if (n) { await menus.nth(n - 1).click(); await p.waitForTimeout(500);
    console.log('last row menu:', await p.evaluate(() => { const m = document.querySelector('#region-main table .dropdown-menu.show'); if (!m) return 'not open';
      const r = m.getBoundingClientRect(); let a = m.parentElement, clip = null; while (a && a.id !== 'region-main') { const c = getComputedStyle(a); if (/(auto|hidden|scroll)/.test(c.overflowX + c.overflowY)) { const ar = a.getBoundingClientRect(); clip = r.bottom > ar.bottom + 1 || r.top < ar.top - 1 ? 'CLIPPED by ' + a.tagName : 'fits'; break; } a = a.parentElement; }
      return `menu ${Math.round(r.top)}-${Math.round(r.bottom)} ${clip || 'no scroll parent'}`; }));
    await p.screenshot({ path: '/tmp/claude-1000/-home-perdausi/662c294c-a995-49e4-a10d-29b6cebe747d/scratchpad/menu.png' }); }
  await b.close();
})();
