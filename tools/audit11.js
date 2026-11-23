// Teacher pages audit: screenshot + checks. node audit11.js [user] [tag]
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const user = process.argv[2] || 'hana', tag = process.argv[3] || 'teacher';
const OUT = __dirname + '/../screenshots/audit/' + tag; fs.mkdirSync(OUT, { recursive: true });
const PAGES = [
  ['course', '/course/view.php?id=7'], ['course-edit', '/course/view.php?id=7&edit=on&sesskey=SESS'],
  ['modedit-page', '/course/modedit.php?add=page&course=7&section=1&return=0'], ['modedit-assign', '/course/modedit.php?update=29'],
  ['assign-teacher', '/mod/assign/view.php?id=29'], ['assign-grading', '/mod/assign/view.php?id=29&action=grading'],
  ['assign-grader', '/mod/assign/view.php?id=29&action=grader'], ['gradebook', '/grade/report/grader/index.php?id=7'],
  ['participants', '/user/index.php?id=7'], ['completion-report', '/report/progress/index.php?course=7'],
  ['quiz-report', '/mod/quiz/report.php?id=30&mode=overview'], ['quiz-edit', '/mod/quiz/edit.php?cmid=30'],
  ['course-settings', '/course/edit.php?id=7'], ['course-completion', '/course/completion.php?id=7'],
  ['gradebook-setup', '/grade/edit/tree/index.php?id=7'], ['groups', '/group/index.php?id=7'], ['home', '/local/siraj/index.php'],
];
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage(); const errs = [];
  p.on('pageerror', e => errs.push(e.message)); p.on('dialog', d => { errs.push('dialog'); d.dismiss(); });
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  const sess = await p.evaluate(() => M.cfg.sesskey);
  for (const [n, u] of PAGES) {
    const r = await p.goto(BASE + u.replace('SESS', sess), { waitUntil: 'networkidle' }).catch(() => null); await p.waitForTimeout(500);
    const info = await p.evaluate(() => ({ url: location.pathname + location.search, over: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      err: (document.body.innerText.match(/(Exception|Coding error|Warning:|Notice:|\[\[[a-z_]+\]\])/) || [])[0] || '' }));
    console.log(`${n.padEnd(18)} ${r ? r.status() : 'ERR'} ${info.url.slice(0, 64).padEnd(64)} over=${info.over} ${info.err} ${errs.splice(0).join('|')}`);
    await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: false });
  }
  await p.goto(BASE + '/course/view.php?id=7&edit=off&sesskey=' + sess);
  await b.close();
})();
