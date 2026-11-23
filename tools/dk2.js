const { chromium } = require('playwright-core'); const fs = require('fs'); const { execSync } = require('child_process');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext()).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'sara'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/local/siraj/index.php', { waitUntil: 'networkidle' });
  const before = await p.evaluate(() => document.documentElement.getAttribute('data-bs-theme') || 'light');
  await p.click('[data-siraj-action="theme"]'); await p.waitForTimeout(300);
  execSync('docker exec -w /var/www/html siraj-webserver-1 php admin/cli/purge_caches.php');
  await p.goto(BASE + '/local/siraj/learning.php', { waitUntil: 'networkidle' });
  const after = await p.evaluate(() => document.documentElement.getAttribute('data-bs-theme'));
  console.log(`was ${before}, toggled, caches purged, next page: ${after}`);
  await p.click('[data-siraj-action="theme"]'); await b.close();
})();
