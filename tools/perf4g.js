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
  console.log('\n== Slow 4G phone (Home, cold) ==');
  { const ctx = await browser.newContext({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true, deviceScaleFactor: 3 }); const page = await ctx.newPage();
    await login(page); await ctx.clearCookies === undefined; const cdp = await ctx.newCDPSession(page);
    await cdp.send('Network.enable'); await cdp.send('Network.setCacheDisabled', { cacheDisabled: true });
    await cdp.send('Network.emulateNetworkConditions', { offline: false, latency: 150, downloadThroughput: 1.6 * 1024 * 1024 / 8, uploadThroughput: 750 * 1024 / 8 });
    await cdp.send('Emulation.setCPUThrottlingRate', { rate: 4 });
    const m = await measure(page, PAGES.home);
    console.log(`home 4G+4xCPU no-cache: TTFB ${f(m.ttfb)}ms FCP ${f(m.fcp)} LCP ${f(m.lcp)} load ${f(m.load)} CLS ${m.cls.toFixed(3)} ${m.requests} req ${f(m.totalKB)}KB`);
    await cdp.send('Network.setCacheDisabled', { cacheDisabled: false });
    await page.goto(BASE + PAGES.home, { waitUntil: 'load' }); const m2 = await measure(page, PAGES.home);
    console.log(`home 4G+4xCPU cached:   FCP ${f(m2.fcp)} LCP ${f(m2.lcp)} load ${f(m2.load)} ${m2.requests} req ${f(m2.totalKB)}KB`);
    await ctx.close(); }

  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
