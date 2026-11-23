const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const OUT = __dirname + '/../screenshots/teacher'; fs.mkdirSync(OUT, { recursive: true });
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  for (const [user, vp, tag] of [['hana', { width: 1440, height: 900 }, 'desk'], ['hana', { width: 390, height: 844 }, 'phone'], ['omar', { width: 1440, height: 900 }, 'omar-ar']]) {
    const p = await (await b.newContext({ viewport: vp, isMobile: vp.width < 500 })).newPage(); const errs = [];
    p.on('pageerror', e => errs.push(e.message));
    for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
    await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
    await p.goto(BASE + '/local/siraj/index.php', { waitUntil: 'networkidle' });
    const r = await p.evaluate(() => ({ url: location.pathname, over: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      err: (document.body.innerText.match(/(Exception|Coding error|Warning:|Notice:|\[\[[a-z_]+\]\])/) || [])[0] || '',
      nav: [...document.querySelectorAll('.siraj-sidebar a span, .siraj-sidebar a')].map(a => a.textContent.trim()).filter(Boolean).slice(0, 14).join(', ') }));
    console.log(tag, JSON.stringify(r), errs.join('|'));
    await p.screenshot({ path: `${OUT}/teaching-${tag}.png`, fullPage: true });
    await p.goto(BASE + '/local/siraj/learning.php', { waitUntil: 'networkidle' });
    console.log(tag, 'my learning items:', await p.locator('.siraj-item').count());
  }
  await b.close();
})();
