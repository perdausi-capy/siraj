// Performance + responsive audit for the local Siraj site. node perf.js <user> <passfile>
const { chromium } = require('playwright-core');
const fs = require('fs');
const [user, passfile] = process.argv.slice(2);
const pass = fs.readFileSync(passfile, 'utf8').trim();
const BASE = process.env.SIRAJ_BASE || 'http://localhost:8000';
const EXE = process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome';
const PAGES = process.env.PAGES ? JSON.parse(process.env.PAGES) : { home: '/local/siraj/index.php', mylearning: '/local/siraj/learning.php', explore2: '/local/siraj/explore.php', achievements: '/local/siraj/achievements.php', inbox: '/local/siraj/inbox.php', profile: '/local/siraj/profile.php', calendar: '/calendar/view.php?view=month', results: '/mod/quiz/review.php?attempt=1&cmid=10', prefs: '/user/preferences.php', overview: '/local/siraj/course.php?id=3', learn: '/local/siraj/learn.php?cmid=6', quizcard: '/local/siraj/learn.php?cmid=10', explore: '/course/index.php' };

async function login(page) {
  await page.goto(BASE + '/login/index.php', { waitUntil: 'load' }); await page.waitForTimeout(800);
  await page.goto(BASE + '/login/index.php', { waitUntil: 'load' }); await page.waitForTimeout(800);
  await page.fill('#username', user); await page.fill('#password', pass);
  await Promise.all([page.waitForNavigation(), page.click('#loginbtn')]);
}
async function measure(page, url) {
  const reqs = []; const onResp = async r => { try { const h = await r.headerValue('content-length'); reqs.push({ url: r.url(), type: r.request().resourceType(), bytes: +h || (await r.body()).length, cached: r.fromServiceWorker() || r.status() === 304 }); } catch (e) {} };
  page.on('response', onResp);
  await page.addInitScript(() => {
    window.__lcp = 0; window.__cls = 0;
    new PerformanceObserver(l => { for (const e of l.getEntries()) window.__lcp = e.startTime; }).observe({ type: 'largest-contentful-paint', buffered: true });
    new PerformanceObserver(l => { for (const e of l.getEntries()) if (!e.hadRecentInput) window.__cls += e.value; }).observe({ type: 'layout-shift', buffered: true });
  });
  await page.goto(BASE + url, { waitUntil: 'load' });
  await page.waitForTimeout(1200);
  const t = await page.evaluate(() => { const n = performance.getEntriesByType('navigation')[0]; const fcp = performance.getEntriesByName('first-contentful-paint')[0];
    return { ttfb: n.responseStart, dcl: n.domContentLoadedEventEnd, load: n.loadEventEnd, fcp: fcp ? fcp.startTime : 0, lcp: window.__lcp, cls: window.__cls, htmlKB: n.transferSize / 1024 }; });
  page.off('response', onResp);
  const by = {}; for (const r of reqs) { by[r.type] = by[r.type] || { n: 0, kb: 0 }; by[r.type].n++; by[r.type].kb += r.bytes / 1024; }
  return { ...t, requests: reqs.length, totalKB: reqs.reduce((a, r) => a + r.bytes, 0) / 1024, by, external: reqs.filter(r => !r.url.startsWith(BASE)).map(r => new URL(r.url).host) };
}
const f = x => Math.round(x);
(async () => {
  const browser = await chromium.launch({ executablePath: EXE });
  console.log('\n== Responsive: horizontal overflow / small tap targets ==');
  const sizes = [[320, 568, 'small phone'], [360, 740, 'Android']];
  for (const [w, h, label] of sizes) {
    const ctx = await browser.newContext({ viewport: { width: w, height: h }, isMobile: w < 800, hasTouch: w < 1100 }); const page = await ctx.newPage();
    await page.goto(BASE + '/login/index.php', { waitUntil: 'load' }); await page.waitForTimeout(800);
    const lo = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    await login(page);
    const res = [];
    for (const [name, url] of Object.entries(PAGES)) {
      await page.goto(BASE + url, { waitUntil: 'load' }); await page.waitForTimeout(800);
      const r = await page.evaluate(() => {
        const vw = window.innerWidth; const over = [];
        document.querySelectorAll('body *').forEach(el => { const b = el.getBoundingClientRect(); const cs = getComputedStyle(el);
          if (b.width && b.right > vw + 1 && cs.position !== 'fixed' && !el.closest('.drawer, .dropdown-menu, [hidden], .siraj-palette-back, .visually-hidden, .sr-only')) over.push((el.className && el.className.baseVal === undefined ? el.className : el.tagName).toString().split(' ')[0]); });
        const small = [...document.querySelectorAll('a, button, input, [role=button]')].filter(el => { const b = el.getBoundingClientRect(); return b.width > 0 && b.height > 0 && (b.height < 32 || b.width < 32) && !el.closest('[hidden], .drawer:not(.show), .dropdown-menu, .visually-hidden'); }).length;
        const pg = document.getElementById('page'); return { docOver: document.documentElement.scrollWidth - vw, pageOver: pg ? pg.scrollWidth - pg.clientWidth : 0, over: [...new Set(over)].slice(0, 4), small };
      });
      res.push(`${name}:${r.docOver > 0 || r.pageOver > 0 ? 'OVERFLOW ' + Math.max(r.docOver, r.pageOver) + 'px ' + r.over.join('/') : 'ok'}${w < 800 ? ' tiny-tap=' + r.small : ''}`);
      if (w === 320 || w === 768 || w === 1024) await page.screenshot({ path: `../screenshots/phase5/rs-${w}-${name}.png` });
    }
    console.log(`${String(w).padStart(4)}x${h} ${label.padEnd(15)} login:${lo > 0 ? 'OVERFLOW ' + lo : 'ok'} ${res.join('  ')}`);
    await ctx.close();
  }
  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
