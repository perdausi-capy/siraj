const { chromium } = require('playwright-core'); const fs = require('fs');
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim();
  for (let i = 0; i < 2; i++) await p.goto('http://localhost:8000/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.waitForLoadState('networkidle');
  const seen = []; p.on('request', r => { if (r.resourceType() === 'document' || (r.headers()['sec-purpose'] || '').includes('prefetch')) seen.push(r.url().replace('http://localhost:8000', '') + ' ' + (r.headers()['sec-purpose'] || 'navigation')); });
  await p.hover('.siraj-nav a[href*="/my/courses.php"]'); await p.waitForTimeout(800);
  const t0 = Date.now(); await Promise.all([p.waitForNavigation({ waitUntil: 'load' }), p.click('.siraj-nav a[href*="/my/courses.php"]')]);
  const ms = Date.now() - t0;
  const nav = await p.evaluate(() => { const n = performance.getEntriesByType('navigation')[0]; return { deliveryType: n.deliveryType, ttfb: Math.round(n.responseStart), fcp: Math.round((performance.getEntriesByName('first-contentful-paint')[0] || {}).startTime || 0) }; });
  console.log('requests:', seen); console.log('click->load', ms, 'ms', JSON.stringify(nav));
  await b.close();
})();
