// Phase 2 screenshots + error check: My team, person, Reports, Compliance (as omar, the line manager),
// Reports everyone, Admin studio, Compliance rules (as admin), certificate check (signed out). Light + dark, desktop + phone.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const OUT = __dirname + '/../screenshots/phase2'; fs.mkdirSync(OUT, { recursive: true });
const only = process.argv[2];
async function login(p, user) {
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
}
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const sets = [
    ['omar', ['team', '/local/siraj/team.php'], ['person', '/local/siraj/person.php?id=PERSON'], ['reports', '/local/siraj/reports.php'], ['matrix', '/local/siraj/matrix.php']],
    ['admin', ['reports-all', '/local/siraj/reports.php?scope=all'], ['studio', '/local/siraj/studio.php'], ['matrix-admin', '/local/siraj/matrix.php?scope=all']],
    [null, ['verify', '/local/siraj/verify.php']],
  ];
  let problems = 0;
  for (const [vw, vh, tag] of [[1440, 900, 'desktop'], [390, 844, 'phone']]) {
    for (const [user, ...pages] of sets) {
      for (const theme of ['light', 'dark']) {
        const ctx = await b.newContext({ viewport: { width: vw, height: vh }, isMobile: vw < 500, hasTouch: vw < 500, deviceScaleFactor: vw < 500 ? 2 : 1 });
        const p = await ctx.newPage(); const errs = [];
        p.on('console', m => { if (m.type() === 'error') errs.push(m.text()); }); p.on('pageerror', e => errs.push(e.message));
        if (user) await login(p, user);
        for (let [name, url] of pages) {
          if (only && !name.startsWith(only)) continue;
          if (url.includes('PERSON')) url = url.replace('PERSON', await p.evaluate(async () => (await (await fetch('/local/siraj/team.php')).text()).match(/person\.php\?id=(\d+)/)[1]));
          await p.goto(BASE + url, { waitUntil: 'networkidle' });
          await p.evaluate(t => { document.cookie = 'siraj-theme=' + t + '; path=/'; document.documentElement.setAttribute('data-bs-theme', t); }, theme);
          await p.waitForTimeout(700);
          const r = await p.evaluate(() => ({ sideways: document.documentElement.scrollWidth - document.documentElement.clientWidth,
            bad: (document.body.innerText.match(/(Exception|Coding error|Warning:|Notice:|Deprecated:|Error reading from database|\[\[[a-z_]+\]\])/) || [])[0] || '' }));
          if (r.sideways > 0 || r.bad || errs.length) { problems++; console.log(`PROBLEM ${tag} ${theme} ${user} ${name}: sideways ${r.sideways} ${r.bad} ${errs.join(' | ')}`); errs.length = 0; }
          await p.screenshot({ path: `${OUT}/${name}-${tag}-${theme}.png`, fullPage: true });
        }
        await ctx.close();
      }
    }
  }
  console.log(problems ? `${problems} problems` : 'no problems'); await b.close();
})();
