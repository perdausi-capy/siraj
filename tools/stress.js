// Reproduce: a user hovering and clicking quickly around the sidebar. Measures each click->load.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  let inflight = 0, prefetches = 0; p.on('request', r => { if ((r.headers()['sec-purpose'] || '').includes('prefetch')) prefetches++; });
  const links = ['Home', 'My learning', 'Explore', 'Calendar', 'Achievements', 'Inbox'];
  const times = [];
  for (let round = 0; round < 3; round++) {
    for (const name of links) {
      const sel = `.siraj-nav a:has-text("${name}")`;
      // sweep the mouse across other links first (like a real user), then click
      for (const other of links) { await p.hover(`.siraj-nav a:has-text("${other}")`).catch(() => {}); await p.waitForTimeout(60); }
      const t0 = Date.now();
      await Promise.all([p.waitForNavigation({ waitUntil: 'load', timeout: 30000 }).catch(e => 'timeout'), p.click(sel)]);
      times.push(Date.now() - t0);
    }
  }
  times.sort((a, c) => a - c);
  console.log(`clicks ${times.length} | median ${times[times.length >> 1]}ms | p90 ${times[Math.floor(times.length * .9)]}ms | max ${times[times.length - 1]}ms | prefetch requests ${prefetches}`);
  await b.close();
})();
