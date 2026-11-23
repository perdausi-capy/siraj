// Real transfer sizes (compressed, excluding cache hits) via CDP, plus tiny tap targets on phone.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.argv[3], 'utf8').trim(); const user = process.argv[2];
const BASE = 'http://localhost:8000'; const EXE = process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome';
async function login(page) { for (let i = 0; i < 2; i++) await page.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await page.fill('#username', user); await page.fill('#password', pass); await Promise.all([page.waitForNavigation(), page.click('#loginbtn')]); }
(async () => {
  const b = await chromium.launch({ executablePath: EXE });
  for (const [label, cfg] of [['desktop', { viewport: { width: 1440, height: 900 } }], ['phone', { viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true }]]) {
    const ctx = await b.newContext(cfg); const page = await ctx.newPage(); const cdp = await ctx.newCDPSession(page); await cdp.send('Network.enable');
    const rows = {}; cdp.on('Network.responseReceived', e => { rows[e.requestId] = { url: e.response.url, type: e.type, cache: e.response.fromDiskCache || e.response.fromServiceWorker || e.response.fromPrefetchCache }; });
    cdp.on('Network.requestServedFromCache', e => { if (rows[e.requestId]) rows[e.requestId].cache = true; });
    cdp.on('Network.loadingFinished', e => { if (rows[e.requestId]) rows[e.requestId].bytes = e.encodedDataLength; });
    for (const k in rows) delete rows[k];
    await page.goto(BASE + '/login/index.php', { waitUntil: 'load' }); await page.waitForTimeout(800);
    { const net = Object.values(rows).filter(r => !r.cache && r.bytes); const by = {};
      for (const r of net) { const k = r.url.startsWith(BASE) ? r.type : 'EXTERNAL-' + r.type; by[k] = (by[k] || 0) + r.bytes / 1024; }
      console.log(`${label} FIRST VISIT (empty cache, sign-in page): ${net.length} requests, ${Math.round(net.reduce((a, r) => a + r.bytes, 0) / 1024)} KB [${Object.entries(by).map(([k, v]) => k + ' ' + Math.round(v)).join(', ')}]`); }
    await login(page);
    for (const [name, url] of [['home(first)', '/local/siraj/index.php'], ['home(again)', '/local/siraj/index.php'], ['mylearning', '/local/siraj/learning.php'], ['explore', '/local/siraj/explore.php'], ['achievements', '/local/siraj/achievements.php'], ['inbox', '/local/siraj/inbox.php'], ['overview', '/local/siraj/course.php?id=3'], ['learn', '/local/siraj/learn.php?cmid=6']]) {
      for (const k in rows) delete rows[k];
      await page.goto(BASE + url, { waitUntil: 'load' }); await page.waitForTimeout(800);
      const all = Object.values(rows); const net = all.filter(r => !r.cache && r.bytes);
      const kb = net.reduce((a, r) => a + r.bytes, 0) / 1024; const by = {};
      for (const r of net) { const k = r.url.startsWith(BASE) ? r.type : 'external-' + r.type; by[k] = (by[k] || 0) + r.bytes / 1024; }
      console.log(`${label} ${name.padEnd(12)} ${all.length} requests, ${net.length} over network, ${Math.round(kb)} KB transferred  [${Object.entries(by).map(([k, v]) => k + ' ' + Math.round(v)).join(', ')}]`);
    }
    if (label === 'phone') {
      await page.goto(BASE + '/local/siraj/index.php', { waitUntil: 'networkidle' });
      console.log('phone tiny tap targets on Home:', JSON.stringify(await page.evaluate(() => [...document.querySelectorAll('a, button, input, [role=button]')].filter(el => { const r = el.getBoundingClientRect(); return r.width > 0 && r.height > 0 && (r.height < 32 || r.width < 32) && !el.closest('[hidden], .drawer:not(.show), .dropdown-menu, .visually-hidden'); }).map(el => (el.className || el.tagName).toString().split(' ').slice(0, 2).join('.') + ' ' + Math.round(el.getBoundingClientRect().width) + 'x' + Math.round(el.getBoundingClientRect().height) + ' "' + el.textContent.trim().slice(0, 20) + '"'))));
    }
    await ctx.close();
  }
  await b.close();
})();
