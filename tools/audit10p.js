// Every Moodle page a learner can reach: screenshot + basic checks. node audit10.js [user] [tag]
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const user = process.argv[2] || 'sara', tag = process.argv[3] || 'en';
const OUT = __dirname + '/../screenshots/audit/' + tag + '-phone'; fs.mkdirSync(OUT, { recursive: true });
const PAGES = [
  ['assign-view', '/mod/assign/view.php?id=29'], ['assign-edit', '/mod/assign/view.php?id=29&action=editsubmission'],
  ['quiz-view', '/mod/quiz/view.php?id=30'], ['grades-course', '/grade/report/user/index.php?id=7'],
  ['grades-all', '/grade/report/overview/index.php'], ['edit-profile', '/user/edit.php'], ['password', '/login/change_password.php'],
  ['preferences', '/user/preferences.php'], ['notif-prefs', '/message/notificationpreferences.php'], ['messages', '/message/index.php'],
  ['calendar-day', '/calendar/view.php?view=day'], ['calendar-upcoming', '/calendar/view.php?view=upcoming'],
  ['course-search', '/course/search.php?search=safety'], ['course-index', '/course/index.php'], ['enrol', '/enrol/index.php?id=8'],
  ['book', '/mod/book/view.php?id=45'], ['scorm-view', '/mod/scorm/view.php?id=48'], ['h5p-view', '/mod/h5pactivity/view.php?id=47'],
  ['page-view', '/mod/page/view.php?id=26'], ['url-view', '/mod/url/view.php?id=28'], ['other-profile', '/user/profile.php?id=3'],
  ['lang-pref', '/user/language.php'], ['forum-search', '/search/index.php?q=project'], ['blog', '/blog/index.php'],
];
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 390, height: 844 }, isMobile: true })).newPage(); const errs = [];
  p.on('pageerror', e => errs.push(e.message)); p.on('dialog', d => { errs.push('dialog'); d.dismiss(); });
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  for (const [n, u] of PAGES) {
    const r = await p.goto(BASE + u, { waitUntil: 'networkidle' }).catch(e => null); await p.waitForTimeout(400);
    const info = await p.evaluate(() => ({ url: location.pathname + location.search, over: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      err: (document.body.innerText.match(/(Exception|Coding error|Warning:|Notice:|\[\[[a-z_]+\]\])/) || [])[0] || '' }));
    console.log(`${n.padEnd(18)} ${r ? r.status() : 'ERR'} ${info.url.slice(0, 60).padEnd(60)} over=${info.over} ${info.err} ${errs.splice(0).join('|')}`);
    await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: false });
  }
  await b.close();
})();
