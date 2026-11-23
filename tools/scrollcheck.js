// Real-scrollbar check: does the document itself scroll (vertical or sideways)? Which element sticks out?
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const PAGES = (process.argv[2] || '/local/siraj/index.php,/my/courses.php,/course/index.php,/calendar/view.php?view=month,/badges/mybadges.php,/message/output/popup/notifications.php,/user/profile.php,/user/preferences.php,/local/siraj/course.php?id=3,/local/siraj/learn.php?cmid=6').split(',');
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome', ignoreDefaultArgs: ['--hide-scrollbars'] });
  for (const w of [1280, 1440, 1880]) {
    const p = await (await b.newContext({ viewport: { width: w, height: 940 } })).newPage();
    for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
    await p.fill('#username', 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
    const out = [];
    for (const url of PAGES) {
      await p.goto(BASE + url, { waitUntil: 'networkidle' });
      const r = await p.evaluate(() => {
        const d = document.documentElement; const vw = d.clientWidth;
        const wide = [...document.querySelectorAll('body *')].filter(e => { const b = e.getBoundingClientRect(); return b.right > vw + 1 && getComputedStyle(e).position !== 'fixed'; }).slice(0, 3).map(e => e.tagName + '.' + [...e.classList].slice(0, 2).join('.') + '@' + Math.round(e.getBoundingClientRect().right));
        const tall = [...document.body.children].map(e => [e.tagName + '#' + e.id + '.' + [...e.classList].slice(0, 2).join('.'), Math.round(e.getBoundingClientRect().bottom)]).filter(x => x[1] > innerHeight + 1);
        const main = document.querySelector('#page.drawers .main-inner, #region-main');
        return { docV: d.scrollHeight - d.clientHeight, docH: d.scrollWidth - d.clientWidth, wide, tall, mainW: main ? Math.round(main.getBoundingClientRect().width) : 0 };
      });
      out.push(`${url.split('?')[0].replace(/\/(index|view)\.php$/, '')}: docScrollV=${r.docV} H=${r.docH} content=${r.mainW}px${r.docH ? ' wide:' + r.wide.join(',') : ''}${r.docV ? ' tall:' + JSON.stringify(r.tall) : ''}`);
    }
    console.log(`== ${w}px\n  ` + out.join('\n  '));
    await p.context().close();
  }
  await b.close();
})();
