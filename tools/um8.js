const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1870, height: 980 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/local/siraj/index.php', { waitUntil: 'networkidle' });
  await p.click('#user-menu-toggle'); await p.waitForTimeout(500);
  const lang = p.locator('.usermenu .dropdown-menu a', { hasText: 'اللغة' });
  console.log('language item:', await lang.count());
  if (await lang.count()) { await lang.first().click(); await p.waitForTimeout(500); }
  const r = await p.evaluate(() => [...document.querySelectorAll('.usermenu .dropdown-menu')].filter(m => m.offsetParent).map(m => { const b = m.getBoundingClientRect(); return `menu x ${Math.round(b.left)}..${Math.round(b.right)} of ${innerWidth}`; }));
  console.log(r.join('\n'));
  await p.screenshot({ path: '/tmp/claude-1000/-home-perdausi/662c294c-a995-49e4-a10d-29b6cebe747d/scratchpad/um.png', clip: { x: 0, y: 0, width: 700, height: 520 } });
  await b.close();
})();
