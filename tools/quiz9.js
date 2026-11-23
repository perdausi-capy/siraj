const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'adam'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/local/siraj/learn.php?cmid=30', { waitUntil: 'networkidle' });
  const btn = p.locator('.siraj-task-actions form button, .siraj-task-actions a.siraj-btn-flame').first();
  await Promise.all([p.waitForNavigation(), btn.click()]);
  const prefl = p.locator('#id_submitbutton'); if (await prefl.count()) await Promise.all([p.waitForNavigation(), prefl.click()]);
  console.log(p.url().replace(BASE, ''), '|', (await p.locator('.qtext').first().innerText()).trim(), '|', (await p.locator('.answer').first().innerText()).replace(/\s+/g, ' ').trim().slice(0, 160));
  await p.screenshot({ path: __dirname + '/../screenshots/demo/quiz-q1.png' });
  await b.close();
})();
