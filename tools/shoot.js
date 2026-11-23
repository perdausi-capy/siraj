// Usage: node shoot.js <outdir> <user> <passfile>
// Takes the same set of screenshots for whichever theme/preset is active.
const { chromium } = require('playwright-core');
const fs = require('fs');
const [outdir, user, passfile] = process.argv.slice(2);
const pass = fs.readFileSync(passfile, 'utf8').trim();
const BASE = 'http://localhost:8000';
const EXE = process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome';
fs.mkdirSync(outdir, { recursive: true });

async function login(page) {
  // First visit sets the session cookie; the second gets a login token tied to it.
  await page.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await page.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await page.fill('#username', user);
  await page.fill('#password', pass);
  await Promise.all([page.waitForNavigation(), page.click('#loginbtn')]);
}
async function snap(page, name, url, opts = {}) {
  if (url) await page.goto(BASE + url, { waitUntil: 'networkidle' });
  await page.waitForTimeout(800);
  await page.screenshot({ path: `${outdir}/${name}.png`, fullPage: !!opts.full });
  console.log('saved', name);
}
(async () => {
  const browser = await chromium.launch({ executablePath: EXE });
  // Desktop
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  // Let Moodle finish compiling the theme CSS before the first screenshot.
  await page.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await page.waitForTimeout(4000);
  await page.reload({ waitUntil: 'networkidle' });
  await snap(page, '01-login', '/login/index.php');
  await login(page);
  await snap(page, '02-dashboard', '/my/');
  await snap(page, '03-my-courses', '/my/courses.php');
  const courseid = await page.evaluate(async () => {
    const a = [...document.querySelectorAll('a[href*="/course/view.php?id="]')].find(x => /Health/.test(x.textContent));
    return a ? new URL(a.href).searchParams.get('id') : null;
  });
  await snap(page, '04-course', '/course/view.php?id=' + (courseid || 2));
  const quizurl = await page.evaluate(() => {
    const a = [...document.querySelectorAll('a[href*="/mod/quiz/view.php"]')][0];
    return a ? a.getAttribute('href') : null;
  });
  if (quizurl) {
    await page.goto(quizurl, { waitUntil: 'networkidle' });
    const btn = await page.$('.quizstartbuttondiv button, button:has-text("Attempt quiz"), button:has-text("Continue your attempt")');
    if (btn) { await Promise.all([page.waitForNavigation().catch(()=>{}), btn.click()]); }
    const start = await page.$('#id_submitbutton');
    if (start) { await Promise.all([page.waitForNavigation().catch(()=>{}), start.click()]); }
    await snap(page, '05-quiz', null);
  }
  await snap(page, '06-catalogue', '/course/index.php');
  await ctx.close();
  // Phone
  const m = await browser.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true });
  const mp = await m.newPage();
  await snap(mp, '07-phone-login', '/login/index.php');
  await login(mp);
  await snap(mp, '08-phone-dashboard', '/my/courses.php');
  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
