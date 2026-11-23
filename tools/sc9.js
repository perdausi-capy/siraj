const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'sara'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.goto(BASE + '/local/siraj/learn.php?cmid=48', { waitUntil: 'networkidle' }); await p.waitForTimeout(3000);
  const f = p.frames().find(fr => fr.url().includes('/mod/scorm/player.php'));
  console.log(await f.evaluate(() => {
    const r = el => el ? (({ top, height }) => `${el.tagName}#${el.id}.${[...el.classList].slice(0,3).join('.')} top=${Math.round(top)} h=${Math.round(height)}`)(el.getBoundingClientRect()) : 'none';
    const out = ['viewport ' + innerWidth + 'x' + innerHeight, 'body class ' + document.body.className.slice(0, 160)];
    for (const sel of ['#page-wrapper', '#page', '#page-content', '#region-main', '#scormpage', '#tocbox', '#toctree', '#scorm_layout', '#scorm_content', '#scorm_object', '.siraj-main', '#topofscroll', 'footer', '#page-footer']) out.push(sel + ': ' + r(document.querySelector(sel)));
    out.push('scrollH ' + document.documentElement.scrollHeight);
    return out.join('\n');
  }));
  await b.close();
})();
