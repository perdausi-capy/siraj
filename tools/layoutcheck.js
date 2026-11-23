// Layout check with real scrollbars: no sideways scroll, top bar stays put when scrolling,
// footer sits at the bottom, content uses the width.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync((process.env.SIRAJ_PASSFILE || process.env.HOME + '/.config/siraj-local/admin-pass'), 'utf8').trim(); const BASE = process.env.SIRAJ_BASE || 'http://localhost:8000';
const PAGES = process.env.PAGES ? JSON.parse(process.env.PAGES) : ['/local/siraj/index.php', '/local/siraj/profile.php', '/local/siraj/learning.php', '/local/siraj/explore.php', '/local/siraj/achievements.php', '/local/siraj/inbox.php', '/local/siraj/certificate.php?id=4', '/mod/quiz/review.php?attempt=1&cmid=10', '/mod/quiz/view.php?id=10', '/my/courses.php', '/course/index.php', '/course/index.php?categoryid=2', '/calendar/view.php?view=month', '/badges/mybadges.php', '/message/output/popup/notifications.php', '/user/profile.php', '/user/preferences.php', '/grade/report/overview/index.php', '/local/siraj/course.php?id=3', '/local/siraj/learn.php?cmid=6'];
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome', ignoreDefaultArgs: ['--hide-scrollbars'] });
  let bad = 0;
  for (const [w, h] of [[1280, 800], [1440, 900], [1880, 940], [2560, 1300], [900, 1000], [390, 844]]) {
    const ctx = await b.newContext({ viewport: { width: w, height: h }, isMobile: w < 500, hasTouch: w < 1000 }); const p = await ctx.newPage();
    for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
    await p.fill('#username', process.env.SIRAJ_USER || 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
    const rows = [];
    for (const url of PAGES) {
      await p.goto(BASE + url, { waitUntil: 'networkidle' });
      const r = await p.evaluate(async () => {
        const d = document.documentElement; const vw = d.clientWidth;
        const main = document.querySelector('#page.drawers .main-inner'); const side = document.querySelector('.siraj-sidebar');
        const avail = vw - (side && getComputedStyle(side).display !== 'none' ? side.getBoundingClientRect().width : 0);
        window.scrollTo({ top: d.scrollHeight, behavior: 'instant' }); await new Promise(r => setTimeout(r, 200));
        const tb = document.querySelector('.siraj-topbar'); const top = tb ? tb.getBoundingClientRect() : { top: 0, left: -1 };
        const ft = document.querySelector('#page-footer'); const foot = ft && !document.body.classList.contains('siraj-certificate-page') ? ft.getBoundingClientRect() : { bottom: innerHeight };
        // Moodle's sticky action bar (gradebook, submissions) legitimately sits below the footer.
        const sticky = document.querySelector('#sticky-footer'); const stickyH = sticky && getComputedStyle(sticky).display !== 'none' ? sticky.getBoundingClientRect().height : 0;
        return { sideways: d.scrollWidth - vw, topbarTop: Math.round(top.top), topbarLeft: Math.round(top.left), footGap: Math.round(innerHeight - foot.bottom - stickyH),
          main: main ? Math.round(main.getBoundingClientRect().width) : 0, avail: Math.round(avail) };
      });
      const ok = r.sideways <= 0 && r.topbarTop === 0 && r.footGap >= -2 && r.footGap <= 2;
      if (!ok) bad++;
      rows.push(`${ok ? 'ok ' : 'BAD'} ${url.replace('/index.php', '').padEnd(42)} content ${r.main}/${r.avail}px  sideways ${r.sideways}  topbar@${r.topbarTop},${r.topbarLeft}  footer gap ${r.footGap}`);
    }
    console.log(`== ${w}x${h}\n  ` + rows.join('\n  ')); await ctx.close();
  }
  console.log(bad ? `${bad} problems` : 'ALL OK'); await b.close();
})();
