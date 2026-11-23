const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const OUT = '../screenshots/layout'; fs.mkdirSync(OUT, { recursive: true });
(async () => { const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome', ignoreDefaultArgs: ['--hide-scrollbars'] });
  const p = await (await b.newContext({ viewport: { width: 1880, height: 940 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  for (const [n, u] of [['home', '/local/siraj/index.php'], ['course', '/local/siraj/course.php?id=3'], ['learn', '/local/siraj/learn.php?cmid=6'], ['mylearning', '/my/courses.php'], ['explore', '/course/index.php'], ['calendar', '/calendar/view.php?view=month'], ['profile', '/user/profile.php']]) {
    await p.goto(BASE + u, { waitUntil: 'networkidle' }); await p.waitForTimeout(400);
    await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: true }); }
  await b.close(); console.log('ok'); })();
