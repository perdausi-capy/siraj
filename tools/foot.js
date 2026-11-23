const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage(); const errs = [];
  p.on('pageerror', e => errs.push(e.stack || e.message));
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  for (const u of ['/grade/report/grader/index.php?id=7', '/mod/assign/view.php?id=29&action=grading']) {
    await p.goto(BASE + u, { waitUntil: 'networkidle' });
    console.log(u, await p.evaluate(async () => { window.scrollTo({ top: 1e6, behavior: 'instant' }); await new Promise(r => setTimeout(r, 300));
      const sf = document.querySelector('#sticky-footer, .stickyfooter, [data-region="sticky-footer"]'); const f = document.querySelector('#page-footer');
      const sfx = sf ? sf.innerText.trim().slice(0, 120) + ' | html=' + sf.innerHTML.replace(/\s+/g,' ').slice(0, 300) : ''; const rr = sf.getBoundingClientRect(); return { box: [Math.round(rr.left), Math.round(rr.right), Math.round(rr.top)], sticky: sf ? Math.round(sf.getBoundingClientRect().height) + 'px visible=' + (getComputedStyle(sf).display !== 'none') : 'none',
        footerBottom: f ? Math.round(innerHeight - f.getBoundingClientRect().bottom) : null }; }));
    await p.screenshot({ path: '/tmp/claude-1000/-home-perdausi/662c294c-a995-49e4-a10d-29b6cebe747d/scratchpad/foot' + (u.includes('grader') ? 1 : 2) + '.png', clip: { x: 0, y: 760, width: 1440, height: 140 } });
  }
  await p.goto(BASE + '/course/modedit.php?update=29', { waitUntil: 'networkidle' });
  console.log('modedit errors:', errs.join('\n').slice(0, 900));
  await b.close();
})();
