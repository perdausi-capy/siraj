// Phase 3 screenshots + flow checks.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim();
const BASE = 'http://localhost:8000'; const OUT = '../screenshots/phase3'; fs.mkdirSync(OUT, { recursive: true });
const EXE = process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome';
async function login(p, u) { for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', u); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]); }
const shot = async (p, n, full = true) => { await p.waitForTimeout(400); await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: full }); console.log('saved', n); };
const visible = p => p.$$eval('.siraj-view:not([hidden]) .siraj-item:not([hidden])', e => e.length);
(async () => {
  const b = await chromium.launch({ executablePath: EXE, ignoreDefaultArgs: ['--hide-scrollbars'] });
  const ctx = await b.newContext({ viewport: { width: 1600, height: 950 } }); const p = await ctx.newPage();
  const errors = []; p.on('pageerror', e => errors.push(e.message)); p.on('response', r => { if (r.status() >= 400) errors.push(r.status() + ' ' + r.url()); });
  await login(p, 'layla');
  await p.goto(BASE + '/local/siraj/learning.php', { waitUntil: 'networkidle' }); await shot(p, '01-learning-list');
  console.log('list items:', await visible(p));
  await p.click('[data-type="optional"]'); console.log('optional tab:', await visible(p));
  await p.click('[data-type="all"]'); await p.click('[data-status="done"]'); console.log('completed filter:', await visible(p));
  await p.click('[data-status="all"]'); await p.fill('[data-region="q"]', 'safety'); console.log('search "safety":', await visible(p));
  await p.fill('[data-region="q"]', 'zzz'); console.log('no-match message shown:', await p.isVisible('[data-region="nomatch"]'));
  await p.fill('[data-region="q"]', ''); await p.click('[data-view="agenda"]'); await shot(p, '02-learning-agenda');
  await p.click('[data-view="list"]');
  await p.goto(BASE + '/local/siraj/explore.php', { waitUntil: 'networkidle' }); await shot(p, '03-explore');
  await p.fill('[data-region="q"]', 'fire'); console.log('explore search "fire":', await p.$$eval('.siraj-tile:not([hidden])', e => e.map(x => x.querySelector('b').textContent)));
  // Quiz attempt page.
  await p.goto(BASE + '/mod/quiz/view.php?id=10', { waitUntil: 'networkidle' });
  const start = await p.$('.quizstartbuttondiv button, button:has-text("Continue your attempt"), button:has-text("Attempt quiz")');
  if (start) { await Promise.all([p.waitForNavigation().catch(() => {}), start.click()]); const s2 = await p.$('#id_submitbutton'); if (s2) await Promise.all([p.waitForNavigation().catch(() => {}), s2.click()]); }
  await p.waitForLoadState('networkidle'); await shot(p, '04-quiz', false);
  console.log('quiz URL:', p.url().replace(BASE, ''), '| questions on page:', await p.$$eval('.que', e => e.length));
  console.log('errors:', errors.length ? errors : 'none');
  await ctx.close();
  // Not-enrolled learner: preview + enrol.
  const c2 = await b.newContext({ viewport: { width: 1600, height: 950 } }); const q = await c2.newPage(); await login(q, 'adam');
  await q.goto(BASE + '/course/view.php?id=8', { waitUntil: 'networkidle' }); console.log('adam /course/view.php?id=8 ->', q.url().replace(BASE, ''));
  await shot(q, '05-course-preview', false);
  await Promise.all([q.waitForNavigation(), q.click('form button:has-text("Enrol")')]); console.log('after enrol ->', q.url().replace(BASE, ''), '| has Continue/Start:', await q.isVisible('.siraj-course-actions a.siraj-btn-flame'));
  await shot(q, '06-after-enrol', false);
  await c2.close();
  const m = await (await b.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true })).newPage();
  await login(m, 'layla'); await m.goto(BASE + '/local/siraj/learning.php', { waitUntil: 'networkidle' }); await shot(m, '07-phone-learning', false);
  await m.goto(BASE + '/local/siraj/explore.php', { waitUntil: 'networkidle' }); await shot(m, '08-phone-explore', false);
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
