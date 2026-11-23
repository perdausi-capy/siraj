const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const OUT = __dirname + '/../screenshots/player'; fs.mkdirSync(OUT, { recursive: true });
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  for (const [user, vp, tag] of [['sara', { width: 1440, height: 900 }, 'desk'], ['layla', { width: 1440, height: 900 }, 'ar'], ['sara', { width: 390, height: 844 }, 'phone']]) {
    const ctx = await b.newContext({ viewport: vp, isMobile: vp.width < 500 }); const p = await ctx.newPage(); const errs = [];
    p.on('pageerror', e => errs.push(e.message)); p.on('dialog', d => { errs.push('DIALOG: ' + d.message()); d.dismiss(); });
    for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
    await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
    for (const [n, cm] of [['assign', 29], ['quiz', 30], ['scorm', 48]]) {
      await p.goto(`${BASE}/local/siraj/learn.php?cmid=${cm}`, { waitUntil: 'networkidle' }); await p.waitForTimeout(n === 'scorm' ? 3000 : 500);
      const bad = await p.evaluate(() => (document.body.innerText.match(/(Exception|Coding error|Warning:|Notice:|\[\[[a-z_]+\]\])/) || [])[0] || '');
      console.log(tag, n, bad || 'ok', errs.splice(0).join(' | '));
      await p.screenshot({ path: `${OUT}/${n}-${tag}.png`, fullPage: true });
    }
    await ctx.close();
  }
  await b.close();
})();
