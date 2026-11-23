const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext()).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'admin'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'dark'));
  await p.goto(BASE + '/local/siraj/index.php', { waitUntil: 'networkidle' });
  console.log(await p.evaluate(() => [document.documentElement.getAttribute('data-bs-theme'), localStorage.getItem('siraj-theme'), getComputedStyle(document.body).backgroundColor,
     [...document.querySelectorAll('head script')].map(s => s.textContent.slice(0, 90)).filter(t => t.includes('siraj')).join(' || ')]));
  await b.close();
})();
