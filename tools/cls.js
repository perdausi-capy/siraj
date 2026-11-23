const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => { const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.addInitScript(() => { window.__s = []; new PerformanceObserver(l => { for (const e of l.getEntries()) window.__s.push({ v: e.value.toFixed(3), src: e.sources.map(s => (s.node && (s.node.id || s.node.className || s.node.nodeName)) + ` ${s.previousRect.x},${s.previousRect.y}->${s.currentRect.x},${s.currentRect.y}`) }); }).observe({ type: 'layout-shift', buffered: true }); });
  await p.goto(BASE + '/mod/quiz/review.php?attempt=1&cmid=10', { waitUntil: 'load' }); await p.waitForTimeout(1500);
  console.log(JSON.stringify(await p.evaluate(() => window.__s), null, 1).slice(0, 2500)); await b.close(); })();
