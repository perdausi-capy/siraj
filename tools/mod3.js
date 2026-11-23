const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext()).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  const s = await p.evaluate(() => M.cfg.sesskey);
  for (const u of ['/course/modedit.php?update=29', '/course/view.php?id=7&edit=on&sesskey=' + s, '/course/modedit.php?update=29']) {
    await p.goto(BASE + u, { waitUntil: 'networkidle' });
    console.log(u.slice(0, 40), await p.evaluate(() => ({ courseindex: document.querySelectorAll('#courseindex').length,
      msgdrawer: document.querySelectorAll('[data-region="message-drawer"]').length, editing: document.body.classList.contains('editing'),
      pagelayout: [...document.body.classList].find(c => c.startsWith('pagelayout')) })));
  }
  await p.goto(BASE + '/course/view.php?id=7&edit=off&sesskey=' + s);
  await b.close();
})();
