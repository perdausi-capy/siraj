const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext()).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'omar'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/local/siraj/team.php', { waitUntil: 'networkidle' });
  await p.locator('input[name="users[]"]:not([disabled])').first().check();
  await Promise.all([p.waitForNavigation(), p.click('[data-region="send"]')]);
  const count = async () => JSON.stringify(await p.$$eval('.alert, [role="alert"]', els => els.filter(e => e.offsetParent).map(e => e.innerText.trim().slice(0, 60))));
  console.log('after action, messages shown:', await count());
  await p.goto(BASE + '/local/siraj/reports.php', { waitUntil: 'networkidle' }); console.log('next page, messages shown:', await count());
  await p.goto(BASE + '/local/siraj/team.php', { waitUntil: 'networkidle' }); console.log('back to team, messages shown:', await count());
  await b.close();
})();
