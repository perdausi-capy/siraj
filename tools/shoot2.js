// Phase 1 screenshots: node shoot2.js <outdir> <user> <passfile>
const { chromium } = require('playwright-core');
const fs = require('fs');
const [outdir, user, passfile] = process.argv.slice(2);
const pass = fs.readFileSync(passfile, 'utf8').trim();
const BASE = 'http://localhost:8000';
const EXE = process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome';
fs.mkdirSync(outdir, { recursive: true });
async function login(page) {
  await page.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await page.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await page.fill('#username', user); await page.fill('#password', pass);
  await Promise.all([page.waitForNavigation(), page.click('#loginbtn')]);
  await page.waitForLoadState('networkidle');
}
async function snap(page, name, url, full = true) {
  if (url) await page.goto(BASE + url, { waitUntil: 'networkidle' });
  await page.waitForTimeout(700);
  await page.screenshot({ path: `${outdir}/${name}.png`, fullPage: false });
  console.log('saved', name, page.url());
}
(async () => {
  const browser = await chromium.launch({ executablePath: EXE });
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
  const page = await ctx.newPage();
  await page.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' }); await page.waitForTimeout(4000);
  await snap(page, '01-signin', '/login/index.php', false);
  await login(page);
  await snap(page, '02-home', null);
  await snap(page, '03-my-learning', '/my/courses.php');
  const cid = await page.evaluate(() => { const a = [...document.querySelectorAll('a[href*="/course/view.php?id="]')].find(x => /Health/.test(x.textContent)); return a ? new URL(a.href).searchParams.get('id') : 2; });
  await snap(page, '04-course', '/course/view.php?id=' + cid);
  await page.keyboard.press('Control+k'); await page.keyboard.type('fire');
  await snap(page, '05-palette', null, false);
  await page.keyboard.press('Escape');
  await page.evaluate(() => { localStorage.setItem('siraj-theme', 'dark'); });
  await snap(page, '06-home-dark', '/local/siraj/index.php');
  await snap(page, '07-course-dark', '/course/view.php?id=' + cid, false);
  await page.evaluate(() => { localStorage.setItem('siraj-theme', 'light'); });
  await ctx.close();
  const m = await browser.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true });
  const mp = await m.newPage();
  await snap(mp, '08-phone-signin', '/login/index.php', false);
  await login(mp);
  await snap(mp, '09-phone-home', null);
  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
