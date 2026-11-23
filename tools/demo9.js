const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const OUT = __dirname + '/../screenshots/demo'; fs.mkdirSync(OUT, { recursive: true });
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  for (const [user, pages] of [['sara', [['explore', '/local/siraj/explore.php'], ['course-hse', '/local/siraj/course.php?id=3'], ['lesson', '/local/siraj/learn.php?cmid=27']]],
                                ['mariam', [['home-ar', '/local/siraj/index.php'], ['learning-ar', '/local/siraj/learning.php']]]]) {
    const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage(); const errs = [];
    p.on('pageerror', e => errs.push(e.message));
    for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
    await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
    for (const [n, u] of pages) {
      await p.goto(BASE + u, { waitUntil: 'networkidle' });
      const bad = await p.evaluate(() => (document.body.innerText.match(/(Exception|Coding error|Warning:|Notice:|\[\[[a-z_]+\]\])/) || [])[0] || '');
      console.log(user, n, bad || 'ok', errs.splice(0).join('|'));
      await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: true });
    }
    if (user === 'mariam') {
      const href = await p.locator('a[href*="course.php"]', { hasText: 'أساسيات' }).first().getAttribute('href').catch(() => null);
      console.log('arabic course link on My learning:', !!href);
      if (href) { await p.goto(href, { waitUntil: 'networkidle' }); await p.screenshot({ path: `${OUT}/course-ar.png`, fullPage: true }); }
    }
  }
  await b.close();
})();
