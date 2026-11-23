const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage(); const errs = [];
  p.on('pageerror', e => errs.push(p.url().replace(BASE, '') + ': ' + e.message));
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'hana'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  const s = await p.evaluate(() => M.cfg.sesskey);
  await p.goto(BASE + '/course/view.php?id=7&edit=on&sesskey=' + s, { waitUntil: 'networkidle' }); await p.waitForTimeout(2000);
  await p.goto(BASE + '/course/modedit.php?update=29', { waitUntil: 'networkidle' }); await p.waitForTimeout(3000);
  console.log('errors after one calm load:', errs.length ? errs : 'none');
  await p.fill('#id_name', 'Reflection: apply it to your role');
  await Promise.all([p.waitForNavigation(), p.click('#id_submitbutton2')]);
  console.log('after save:', p.url().replace(BASE, ''), (await p.title()).slice(0, 60));
  console.log('all errors:', errs.length ? errs : 'none');
  await p.goto(BASE + '/course/view.php?id=7&edit=off&sesskey=' + s);
  await b.close();
})();
