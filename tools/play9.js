// Learning mode: book chapters, SCORM framed player, H5P player. node play9.js <bookcmid> <scormcmid> <h5pcmid>
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync((process.env.SIRAJ_PASSFILE || process.env.HOME + '/.config/siraj-local/admin-pass'), 'utf8').trim(); const BASE = process.env.SIRAJ_BASE || 'http://localhost:8000';
const [book, scorm, h5p] = process.argv.slice(2);
const OUT = __dirname + '/../screenshots/player'; fs.mkdirSync(OUT, { recursive: true });
let fails = 0; const ok = (c, m) => { console.log((c ? 'ok   ' : 'FAIL ') + m); if (!c) fails++; };
async function session(b, user, vp) {
  const p = await (await b.newContext({ viewport: vp || { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  return p;
}
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  let p = await session(b, 'admin');
  await p.goto(`${BASE}/local/siraj/learn.php?cmid=${h5p}&siraj=1`, { waitUntil: 'networkidle' }); await p.waitForTimeout(4000);
  for (const [user, tag] of [['layla', 'ar'], ['sara', 'en']]) {
    p = await session(b, user);
    // Book.
    await p.goto(`${BASE}/local/siraj/learn.php?cmid=${book}`, { waitUntil: 'networkidle' });
    ok(await p.locator('.siraj-chapter-title').count() === 1 && /scope/i.test(await p.innerText('.siraj-chapter-title')), `${tag} book: first chapter shown`);
    await p.screenshot({ path: `${OUT}/book-${tag}.png`, fullPage: true });
    for (let i = 0; i < 2; i++) await Promise.all([p.waitForNavigation(), p.click('.siraj-chapter-nav .siraj-btn-flame')]);
    ok(/report/i.test(await p.innerText('.siraj-chapter-title')), `${tag} book: reached last chapter`);
    ok(await p.locator('.siraj-player-top .siraj-chip.green').count() === 1, `${tag} book: complete after last chapter`);
    // SCORM.
    await p.goto(`${BASE}/local/siraj/learn.php?cmid=${scorm}`, { waitUntil: 'networkidle' }); await p.waitForTimeout(4000);
    const fr = p.frameLocator('.siraj-embed iframe');
    const inner = await p.evaluate(() => { const f = document.querySelector('.siraj-embed iframe'); try { return { path: f.contentWindow.location.pathname, api: !!(f.contentWindow.API_1484_11 || f.contentWindow.API), sco: !!f.contentDocument.querySelector('#scorm_object, iframe') }; } catch (e) { return { err: e.message }; } });
    ok(inner.path === '/mod/scorm/player.php' && inner.api && inner.sco, `${tag} scorm: Moodle player framed, SCORM API present, package loaded ${JSON.stringify(inner)}`);
    await p.screenshot({ path: `${OUT}/scorm-${tag}.png`, fullPage: true });
    // H5P.
    await p.goto(`${BASE}/local/siraj/learn.php?cmid=${h5p}`, { waitUntil: 'networkidle' }); await p.waitForTimeout(5000);
    const h = await p.evaluate(() => { const f = document.querySelector('.siraj-h5p iframe'); if (!f) return { none: true }; try { const d = f.contentDocument; const inner = d.querySelector('iframe.h5p-iframe'); const txt = (inner ? inner.contentDocument.body.innerText : d.body.innerText) || ''; return { h: Math.round(f.getBoundingClientRect().height), txt: txt.slice(0, 80).replace(/\s+/g, ' ') }; } catch (e) { return { err: e.message }; } });
    ok(h.h > 150 && h.txt && !/error|not found/i.test(h.txt), `${tag} h5p: player shows content ${JSON.stringify(h)}`);
    await p.screenshot({ path: `${OUT}/h5p-${tag}.png`, fullPage: true });
  }
  // Phone.
  p = await session(b, 'sara', { width: 390, height: 844 });
  for (const [n, c] of [['book', book], ['scorm', scorm], ['h5p', h5p]]) {
    await p.goto(`${BASE}/local/siraj/learn.php?cmid=${c}`, { waitUntil: 'networkidle' }); await p.waitForTimeout(2500);
    const over = await p.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    ok(over <= 0, `phone ${n}: no sideways scroll (${over})`);
    await p.screenshot({ path: `${OUT}/${n}-phone.png`, fullPage: true });
  }
  console.log(fails ? `${fails} FAILED` : 'ALL OK'); await b.close();
})();
