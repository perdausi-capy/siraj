// Phase 2 screenshots + flow checks.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim();
const BASE = 'http://localhost:8000'; const OUT = '../screenshots/phase2'; fs.mkdirSync(OUT, { recursive: true });
const EXE = process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome';
async function login(page, user) { for (let i = 0; i < 2; i++) await page.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await page.fill('#username', user); await page.fill('#password', pass); await Promise.all([page.waitForNavigation(), page.click('#loginbtn')]); }
async function snap(page, name, url) { if (url) await page.goto(BASE + url, { waitUntil: 'networkidle' }); await page.waitForTimeout(500);
  await page.screenshot({ path: `${OUT}/${name}.png` }); console.log('saved', name, page.url().replace(BASE, '')); }
(async () => {
  const b = await chromium.launch({ executablePath: EXE });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } }); const p = await ctx.newPage();
  const errors = []; p.on('pageerror', e => errors.push(e.message)); p.on('console', m => { if (m.type() === 'error') errors.push(m.text()); }); p.on('response', r => { if (r.status() >= 400) errors.push(r.status() + ' ' + r.url().replace(BASE, '')); });
  await login(p, 'layla');
  await p.goto(BASE + '/course/view.php?id=3', { waitUntil: 'networkidle' });
  console.log('learner course/view.php ->', p.url().replace(BASE, ''));
  await snap(p, '01-course-overview', null);
  const links = await p.$$eval('.siraj-act', as => as.map(a => ({ href: a.getAttribute('href'), name: a.querySelector('b').textContent })));
  console.log('activities:', links.map(l => l.name).join(' | '));
  const byName = n => links.find(l => l.name.includes(n)).href.replace(BASE, '');
  await snap(p, '02-learn-page', byName('Welcome'));
  await snap(p, '03-learn-url', byName('Policy'));
  await snap(p, '04-learn-quiz', byName('Knowledge'));
  await snap(p, '05-learn-assign', byName('Reflection'));
  // Mark-as-done flow on a not-yet-done page in another course, then undo.
  await p.goto(BASE + '/local/siraj/course.php?id=6', { waitUntil: 'networkidle' });
  const first = await p.$eval('.siraj-act', a => a.getAttribute('href'));
  await p.goto(first, { waitUntil: 'networkidle' });
  const before = await p.textContent('.siraj-complete');
  await Promise.all([p.waitForNavigation(), p.click('.siraj-complete button')]);
  console.log('mark done: before="' + before.trim().replace(/\s+/g, ' ') + '" -> now at', p.url().replace(BASE, ''));
  await p.goto(first, { waitUntil: 'networkidle' });
  const after = await p.textContent('.siraj-player-top'); console.log('after reload top shows:', after.trim().replace(/\s+/g, ' ').slice(-20));
  await Promise.all([p.waitForNavigation(), p.click('.siraj-undo')]);
  console.log('undo ok:', (await p.textContent('.siraj-complete')).includes('Mark as done'));
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'dark'));
  await snap(p, '06-course-dark', '/local/siraj/course.php?id=3');
  await snap(p, '07-learn-dark', byName('Welcome'));
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'light'));
  console.log('JS errors:', errors.length ? errors : 'none');
  await ctx.close();
  // Teacher keeps Moodle's course page.
  const t = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage(); await login(t, 'admin');
  await t.goto(BASE + '/course/view.php?id=3', { waitUntil: 'networkidle' }); console.log('admin course/view.php ->', t.url().replace(BASE, ''));
  // Phone
  const m = await (await b.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true })).newPage();
  await login(m, 'layla'); await snap(m, '08-phone-course', '/local/siraj/course.php?id=3'); await snap(m, '09-phone-learn', byName('Welcome'));
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
