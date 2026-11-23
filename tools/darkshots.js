const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const OUT = '../screenshots/dark'; fs.mkdirSync(OUT, { recursive: true });
(async () => { const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1600, height: 950 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'admin'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'dark'));
  const go = async (n, u, act) => { await p.goto(BASE + u, { waitUntil: 'networkidle' }); if (act) await act(); await p.waitForTimeout(700); await p.screenshot({ path: `${OUT}/${n}.png` }); console.log('saved', n); };
  await go('grader', '/grade/report/grader/index.php?id=4');
  await go('notifications', '/reportbuilder/index.php', () => p.locator('.siraj-plugins .popover-region-toggle').first().click());
  await go('messages', '/reportbuilder/index.php', () => p.locator('.siraj-plugins .popover-region-toggle').nth(1).click());
  await go('rb-filters', '/reportbuilder/index.php', () => p.locator('button:has-text("Filters")').first().click());
  await go('course-settings', '/course/edit.php?id=4');
  await go('filepicker', '/course/edit.php?id=4', async () => { await p.locator('.fp-file').first().click(); });
  await go('bulk-edit', '/course/view.php?id=4&siraj=classic', async () => { const sw = p.locator('.editmode-switch-form input[type=checkbox]'); if (!(await sw.isChecked())) { await Promise.all([p.waitForNavigation(), sw.click()]); await p.waitForLoadState('networkidle'); } await p.locator('.bulkEnable').first().click(); });
  await p.evaluate(() => localStorage.setItem('siraj-theme', 'light'));
  // switch edit mode back off
  await p.goto(BASE + '/course/view.php?id=4&siraj=classic', { waitUntil: 'networkidle' }); const sw = p.locator('.editmode-switch-form input[type=checkbox]'); if (await sw.isChecked()) { await Promise.all([p.waitForNavigation(), sw.click()]); }
  await b.close(); })().catch(e => { console.error(e.message); process.exit(1); });
