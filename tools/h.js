const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => { const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  for (const w of [1280, 1440, 1920]) { const p = await (await b.newContext({ viewport: { width: w, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/mod/quiz/review.php?attempt=1&cmid=10', { waitUntil: 'networkidle' });
  console.log(w, await p.$eval('.siraj-results', e => Math.round(e.getBoundingClientRect().height))); await p.context().close(); }
  await b.close(); })();
