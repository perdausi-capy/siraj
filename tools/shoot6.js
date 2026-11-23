const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim();
const BASE = 'http://localhost:8000'; const OUT = '../screenshots/finish'; fs.mkdirSync(OUT, { recursive: true });
async function login(p, u) { for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', u); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]); }
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome', ignoreDefaultArgs: ['--hide-scrollbars'] });
  const p = await (await b.newContext({ viewport: { width: 1600, height: 950 } })).newPage();
  const errors = []; p.on('pageerror', e => errors.push(e.message)); p.on('response', r => { if (r.status() >= 400) errors.push(r.status() + ' ' + r.url()); });
  await login(p, 'layla');
  for (const [n, u] of [['quiz-results', '/mod/quiz/review.php?attempt=1&cmid=10'], ['profile', '/user/profile.php'], ['preferences', '/user/preferences.php'], ['calendar', '/calendar/view.php?view=month']]) {
    await p.goto(BASE + u, { waitUntil: 'networkidle' }); await p.waitForTimeout(400);
    console.log(n, '->', p.url().replace(BASE, ''));
    await p.screenshot({ path: `${OUT}/${n}.png` });
  }
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'dark'));
  for (const [n, u] of [['profile-dark', '/local/siraj/profile.php'], ['calendar-dark', '/calendar/view.php?view=month'], ['quiz-results-dark', '/mod/quiz/review.php?attempt=1&cmid=10']]) {
    await p.goto(BASE + u, { waitUntil: 'networkidle' }); await p.waitForTimeout(400); await p.screenshot({ path: `${OUT}/${n}.png` }); }
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'light'));
  console.log('errors:', errors.length ? errors : 'none');
  const m = await (await b.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true })).newPage();
  await login(m, 'layla');
  for (const [n, u] of [['phone-profile', '/local/siraj/profile.php'], ['phone-results', '/mod/quiz/review.php?attempt=1&cmid=10'], ['phone-calendar', '/calendar/view.php?view=month']]) {
    await m.goto(BASE + u, { waitUntil: 'networkidle' }); await m.waitForTimeout(400); await m.screenshot({ path: `${OUT}/${n}.png` }); }
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
