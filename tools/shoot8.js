// Arabic / right-to-left: screenshots of every Siraj page as layla (learner) and omar (manager), plus sign-in and
// certificate check; reports sideways scroll, html dir, and English words left in the page (outside course names).
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync((process.env.SIRAJ_PASSFILE || process.env.HOME + '/.config/siraj-local/admin-pass'), 'utf8').trim(); const BASE = process.env.SIRAJ_BASE || 'http://localhost:8000';
const OUT = __dirname + '/../screenshots/rtl'; fs.mkdirSync(OUT, { recursive: true });
const SETS = [
  ['layla', ['home', '/local/siraj/index.php'], ['learning', '/local/siraj/learning.php'], ['explore', '/local/siraj/explore.php'],
    ['course', '/local/siraj/course.php?id=3'], ['learn', '/local/siraj/learn.php?cmid=6'], ['achievements', '/local/siraj/achievements.php'],
    ['certificate', '/local/siraj/certificate.php?id=4'], ['inbox', '/local/siraj/inbox.php'], ['profile', '/local/siraj/profile.php'],
    ['calendar', '/calendar/view.php?view=month'], ['quizresult', '/mod/quiz/review.php?attempt=1&cmid=10'], ['prefs', '/user/preferences.php']],
  ['omar', ['team', '/local/siraj/team.php'], ['person', '/local/siraj/person.php?id=10'], ['reports', '/local/siraj/reports.php'], ['matrix', '/local/siraj/matrix.php']],
  [null, ['login', '/login/index.php?lang=ar'], ['verify', '/local/siraj/verify.php?lang=ar']],
];
const only = process.argv[2];
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const english = {};
  let problems = 0;
  for (const [vw, vh, tag, themes] of [[1440, 900, 'desktop', ['light', 'dark']], [390, 844, 'phone', ['light']]]) {
    for (const [user, ...pages] of SETS) {
      for (const theme of themes) {
        const ctx = await b.newContext({ viewport: { width: vw, height: vh }, isMobile: vw < 500, hasTouch: vw < 500, deviceScaleFactor: vw < 500 ? 2 : 1 });
        const p = await ctx.newPage(); const errs = []; p.on('pageerror', e => errs.push(e.message));
        if (user) { for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
          await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]); }
        for (const [name, url] of pages) {
          if (only && !name.startsWith(only)) continue;
          await p.goto(BASE + url, { waitUntil: 'networkidle' });
          await p.evaluate(t => { document.cookie = 'siraj-theme=' + t + '; path=/'; document.documentElement.setAttribute('data-bs-theme', t); }, theme);
          await p.waitForTimeout(600);
          const r = await p.evaluate(() => {
            const vw = document.documentElement.clientWidth;
            // English words in visible text, skipping course/activity names (they are content, not interface).
            const skip = /Workplace|Health|Safety|Code|Conduct|Ethics|Information|Security|Essentials|Fire|Emergency|Response|Project|Management|Fundamentals|Leadership|Line|Managers|Anti|Bribery|Corruption|Data|Protection|Knowledge|check|Reflection|Policy|PDF|Welcome|objectives|rules|practice|Real|situations|Why|matters|Declaration|Introduction|Key|Practice|Assessment|Moodle|Siraj|Learning|Excel|CSV|Compliance|Professional|Development|Layla|Hassan|Omar|Rahman|Dubai|example|com|Khalid|Mansour|Zaid|Hamdan|Faisal|Haddad|Adam|Farouk|Mariam|Aziz|Noura|Saeed|Tariq|Salem|Reem|Abbas|Sara|Khalil|Yousef|Nasser|Huda|Karim|Engineering|Engineer|Operations|Finance|HR|Analyst|Driver|Site|supervisor|officer|Admin|User|Ctrl|Your|The|About|this|course|page|Safety|First|Lighting|way/;
            const main = document.querySelector('#region-main, .siraj-signin, main') || document.body;
            const words = (main.innerText.match(/[A-Za-z]{3,}/g) || []).filter(w => !skip.test(w));
            return { dir: document.documentElement.dir, sideways: document.documentElement.scrollWidth - vw, words: [...new Set(words)].slice(0, 25) };
          });
          if (r.dir !== 'rtl' || r.sideways > 0 || errs.length) { problems++; console.log(`PROBLEM ${tag} ${theme} ${name}: dir=${r.dir} sideways=${r.sideways} ${errs.join(' | ')}`); errs.length = 0; }
          if (r.words.length) english[name] = [...new Set([...(english[name] || []), ...r.words])];
          await p.screenshot({ path: `${OUT}/${name}-${tag}-${theme}.png`, fullPage: true });
        }
        await ctx.close();
      }
    }
  }
  for (const [n, w] of Object.entries(english)) console.log(`english on ${n}: ${w.join(' ')}`);
  console.log(problems ? `${problems} problems` : 'no layout problems'); await b.close();
})();
