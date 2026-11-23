const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
  await ctx.addCookies([{ name: 'siraj-theme', value: 'dark', url: BASE }]);
  const p = await ctx.newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  for (const [n, u] of [['teaching', '/local/siraj/teaching.php'], ['grading', '/mod/assign/view.php?id=29&action=grading'], ['gradebook', '/grade/report/grader/index.php?id=7']]) {
    await p.goto(BASE + u, { waitUntil: 'networkidle' }); await p.waitForTimeout(500);
    const light = await p.evaluate(() => { const lum = c => { const m = c.match(/\d+(\.\d+)?/g); if (!m) return 1; const [r, g, bb] = m.map(Number); return (0.2126 * r + 0.7152 * g + 0.0722 * bb) / 255; };
      return [...document.querySelectorAll('#region-main *, .siraj-main *, #sticky-footer')].filter(e => { const s = getComputedStyle(e); const r = e.getBoundingClientRect();
        return r.width > 120 && r.height > 30 && s.backgroundColor !== 'rgba(0, 0, 0, 0)' && lum(s.backgroundColor) > .8; }).slice(0, 6).map(e => e.tagName + '.' + [...e.classList].slice(0, 2).join('.')); });
    console.log(n, 'light surfaces in dark:', light.length ? light.join(' | ') : 'none');
    await p.screenshot({ path: `${__dirname}/../screenshots/teacher/${n}-dark.png` });
  }
  await b.close();
})();
