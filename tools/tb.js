const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  for (const user of ['admin', 'sara']) {
    const p = await (await b.newContext({ viewport: { width: 390, height: 844 }, isMobile: true })).newPage();
    for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
    await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
    await p.goto(BASE + '/admin/settings.php?section=frontpagesettings', { waitUntil: 'networkidle' });
    console.log(user, await p.evaluate(() => [...document.querySelector('.siraj-topbar').children].map(c => c.className.split(' ')[0] + ':' + Math.round(c.getBoundingClientRect().width)).join(' ') + ' || actions: ' +
      [...document.querySelector('.siraj-top-actions').children].filter(c => getComputedStyle(c).display !== 'none').map(c => (c.className.split(' ')[0] || c.tagName) + ':' + Math.round(c.getBoundingClientRect().width)).join(' ')));
  }
  await b.close();
})();
