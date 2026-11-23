const { chromium } = require('playwright-core');
(async () => { const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext()).newPage(); const seen = [];
  p.on('requestfinished', async r => { if (r.resourceType() === 'font' || /woff2|font\.php/.test(r.url())) { const s = await r.sizes(); seen.push(r.url().replace('http://localhost:8000', '') + ' ' + s.responseBodySize + 'B'); } });
  await p.goto('http://localhost:8000/login/index.php', { waitUntil: 'networkidle' }); await p.waitForTimeout(500);
  console.log(seen.join('\n')); await b.close(); })();
