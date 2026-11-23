// Phase 4 screenshots + flow checks.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim();
const BASE = 'http://localhost:8000'; const OUT = '../screenshots/phase4'; fs.mkdirSync(OUT, { recursive: true });
const EXE = process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome';
async function login(p, u) { for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', u); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]); }
const shot = async (p, n, full = true) => { await p.waitForTimeout(400); await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: full }); console.log('saved', n); };
(async () => {
  const b = await chromium.launch({ executablePath: EXE, ignoreDefaultArgs: ['--hide-scrollbars'] });
  const ctx = await b.newContext({ viewport: { width: 1600, height: 950 } }); const p = await ctx.newPage();
  const errors = []; p.on('pageerror', e => errors.push(e.message)); p.on('response', r => { if (r.status() >= 400) errors.push(r.status() + ' ' + r.url()); });
  await login(p, 'layla');
  await p.goto(BASE + '/badges/mybadges.php', { waitUntil: 'networkidle' }); console.log('/badges/mybadges.php ->', p.url().replace(BASE, ''));
  await shot(p, '01-achievements');
  const cert = await p.$eval('.siraj-cert-foot a', a => a.href);
  await p.goto(cert, { waitUntil: 'networkidle' }); await shot(p, '02-certificate', false);
  await p.pdf({ path: `${OUT}/certificate.pdf`, landscape: true, format: 'A4', printBackground: true }); console.log('saved certificate.pdf');
  const r = await p.goto(BASE + '/local/siraj/certificate.php?id=3'); console.log('certificate for unfinished course -> HTTP', r.status(), (await p.textContent('body')).includes('becomes available') ? '(blocked with message)' : '');
  await p.goto(BASE + '/message/output/popup/notifications.php', { waitUntil: 'networkidle' }); console.log('Moodle notifications ->', p.url().replace(BASE, ''));
  await shot(p, '03-inbox');
  const unread0 = await p.$$eval('.siraj-note.is-unread', e => e.length);
  await p.click('[role="tab"][data-kind="achievements"]'); console.log('achievements tab shows:', await p.$$eval('.siraj-note:not([hidden])', e => e.length));
  await p.click('[role="tab"][data-kind="all"]');
  await Promise.all([p.waitForNavigation(), p.click('.siraj-note.is-unread .siraj-note-read')]);
  console.log('unread before/after mark-read:', unread0, await p.$$eval('.siraj-note.is-unread', e => e.length));
  // Quiz: answer and submit, then results page.
  await p.goto(BASE + '/mod/quiz/view.php?id=10', { waitUntil: 'networkidle' });
  let btn = await p.$('.quizstartbuttondiv button, button:has-text("Continue your attempt"), button:has-text("Attempt quiz")');
  if (btn) { await Promise.all([p.waitForNavigation().catch(() => {}), btn.click()]); const s2 = await p.$('#id_submitbutton'); if (s2) await Promise.all([p.waitForNavigation().catch(() => {}), s2.click()]); }
  for (let i = 0; i < 4; i++) {
    const radio = await p.$('.que .answer input[type=radio]'); if (radio) await radio.check();
    const txt = await p.$('.que .answer input[type=text]'); if (txt) await txt.fill('My line manager');
    const nextb = await p.$('#mod_quiz-next-nav'); if (!nextb) break;
    await Promise.all([p.waitForNavigation(), nextb.click()]);
    if (p.url().includes('summary.php')) break;
  }
  await shot(p, '04-quiz-summary', false);
  const submit = await p.$('button:has-text("Submit all and finish")');
  if (submit) { await submit.click(); await p.waitForTimeout(600); const conf = await p.$('.modal-footer button.btn-primary'); if (conf) await Promise.all([p.waitForNavigation(), conf.click()]); }
  await p.waitForLoadState('networkidle'); console.log('after submit ->', p.url().replace(BASE, ''));
  await shot(p, '05-quiz-review');
  console.log('errors:', errors.length ? errors : 'none');
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'dark'));
  await p.goto(BASE + '/local/siraj/achievements.php', { waitUntil: 'networkidle' }); await shot(p, '06-achievements-dark', false);
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'light'));
  await ctx.close();
  const m = await (await b.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true })).newPage();
  await login(m, 'layla'); await m.goto(BASE + '/local/siraj/inbox.php', { waitUntil: 'networkidle' }); await shot(m, '07-phone-inbox', false);
  await m.goto(BASE + '/local/siraj/achievements.php', { waitUntil: 'networkidle' }); await shot(m, '08-phone-achievements', false);
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
