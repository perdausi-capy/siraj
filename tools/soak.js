// 5-minute realistic browsing: random sidebar/page clicks, idle gaps, quick bursts. Reports slow loads.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync((process.env.SIRAJ_PASSFILE || process.env.HOME + '/.config/siraj-local/admin-pass'), 'utf8').trim(); const BASE = process.env.SIRAJ_BASE || 'http://localhost:8000';
const URLS = process.env.SOAK_URLS ? JSON.parse(process.env.SOAK_URLS) : ['/local/siraj/index.php', '/local/siraj/learning.php', '/local/siraj/explore.php', '/calendar/view.php?view=month', '/local/siraj/achievements.php',
  '/local/siraj/inbox.php', '/local/siraj/profile.php', '/local/siraj/course.php?id=3', '/local/siraj/learn.php?cmid=6', '/local/siraj/course.php?id=7', '/mod/quiz/view.php?id=10'];
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', process.env.SIRAJ_USER || 'layla'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  const end = Date.now() + 5 * 60 * 1000; const times = []; const slow = []; let errors = 0;
  p.on('response', r => { if (r.status() >= 500) errors++; });
  while (Date.now() < end) {
    const burst = Math.random() < 0.3 ? 5 : 1;
    for (let k = 0; k < burst; k++) {
      const u = URLS[Math.floor(Math.random() * URLS.length)];
      const t0 = Date.now();
      const ok = await p.goto(BASE + u, { waitUntil: 'load', timeout: 30000 }).then(() => true).catch(() => false);
      const dt = Date.now() - t0; times.push(dt); if (!ok || dt > 2000) slow.push(`${u} ${ok ? dt + 'ms' : 'TIMEOUT'}`);
      if (burst > 1) await p.waitForTimeout(150);
    }
    await p.waitForTimeout(2000 + Math.random() * 13000);
  }
  times.sort((a, c) => a - c);
  console.log(`loads ${times.length} | median ${times[times.length >> 1]}ms | p95 ${times[Math.floor(times.length * .95)]}ms | max ${times[times.length - 1]}ms | over 2s: ${slow.length} ${slow.join(', ')} | 5xx: ${errors}`);
  await b.close();
})();
