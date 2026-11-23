// Teacher pages audit: screenshot + checks. node audit11.js [user] [tag]
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const user = process.argv[2] || 'admin', tag = process.argv[3] || 'admin';
const VW = +(process.argv[4] || 1440);
const OUT = __dirname + '/../screenshots/audit/' + tag; fs.mkdirSync(OUT, { recursive: true });
const PAGES = [
  ['admin-search', '/admin/search.php'], ['admin-notifications', '/admin/index.php'], ['users', '/admin/user.php'],
  ['user-add', '/user/editadvanced.php?id=-1'], ['upload-users', '/admin/tool/uploaduser/index.php'], ['bulk-users', '/admin/user/user_bulk.php'],
  ['cohorts', '/cohort/index.php'], ['roles', '/admin/roles/manage.php'], ['role-edit', '/admin/roles/define.php?action=view&roleid=5'],
  ['course-mgmt', '/course/management.php'], ['course-add', '/course/edit.php?category=1'], ['plugins', '/admin/plugins.php'],
  ['theme-settings', '/admin/settings.php?section=themesettingsiraj'], ['siraj-settings', '/admin/settings.php?section=local_siraj'],
  ['lang-packs', '/admin/tool/langimport/index.php'], ['tasks', '/admin/tool/task/scheduledtasks.php'], ['caches', '/cache/admin.php'],
  ['reportbuilder', '/reportbuilder/index.php'], ['logs', '/report/log/index.php?id=1'], ['enrol-plugins', '/admin/settings.php?section=manageenrols'],
  ['security', '/admin/settings.php?section=sitepolicies'], ['frontpage', '/admin/settings.php?section=frontpagesettings'],
  ['studio', '/local/siraj/studio.php'],
];
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: VW, height: VW < 500 ? 844 : 900 }, isMobile: VW < 500, hasTouch: VW < 500 })).newPage(); const errs = [];
  p.on('pageerror', e => errs.push(e.message)); p.on('dialog', d => { errs.push('dialog'); d.dismiss(); });
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', user); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  const sess = await p.evaluate(() => M.cfg.sesskey);
  for (const [n, u] of PAGES) {
    const r = await p.goto(BASE + u.replace('SESS', sess), { waitUntil: 'networkidle' }).catch(() => null); await p.waitForTimeout(500);
    const info = await p.evaluate(() => ({ url: location.pathname + location.search, over: Math.max(document.documentElement.scrollWidth - document.documentElement.clientWidth, (document.getElementById('page') || document.body).scrollWidth - (document.getElementById('page') || document.body).clientWidth),
      err: (document.body.innerText.match(/(Exception|Coding error|Warning:|Notice:|\[\[[a-z_]+\]\])/) || [])[0] || '' }));
    console.log(`${n.padEnd(18)} ${r ? r.status() : 'ERR'} ${info.url.slice(0, 64).padEnd(64)} over=${info.over} ${info.err} ${errs.splice(0).join('|')}`);
    await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: false });
  }
  await b.close();
})();
