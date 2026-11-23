// Dark-mode audit: light surfaces and low-contrast text on many Moodle pages and popups, as admin.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
const PAGES = [
  ['studio', '/local/siraj/studio.php'], ['reports-all', '/local/siraj/reports.php?scope=all'], ['matrix-all', '/local/siraj/matrix.php?scope=all'],
  ['home', '/local/siraj/index.php'], ['course-edit-mode', '/course/view.php?id=4&siraj=classic', 'edit'], ['course-settings', '/course/edit.php?id=4'],
  ['grader', '/grade/report/grader/index.php?id=4'], ['participants', '/user/index.php?id=4'], ['reportbuilder', '/reportbuilder/index.php'],
  ['admin-search', '/admin/search.php'], ['admin-users', '/admin/user.php'], ['admin-plugins', '/admin/plugins.php'], ['theme-settings', '/admin/settings.php?section=themesettingsiraj'],
  ['quiz-edit', '/mod/quiz/edit.php?cmid=10'], ['quiz-view', '/mod/quiz/view.php?id=10'], ['assign', '/mod/assign/view.php?id=9'], ['calendar', '/calendar/view.php?view=month'],
  ['profile-moodle', '/user/profile.php?id=2&siraj=classic'], ['edit-profile', '/user/edit.php'], ['messages', '/message/index.php'], ['prefs', '/user/preferences.php'],
  ['notif-prefs', '/message/notificationpreferences.php'], ['badges-manage', '/badges/index.php?type=1'], ['course-mgmt', '/course/management.php'],
  ['explore', '/local/siraj/explore.php'], ['learning', '/local/siraj/learning.php'], ['inbox', '/local/siraj/inbox.php'],
];
const POPUPS = [
  ['notif-popover', '/local/siraj/index.php', '.siraj-plugins .popover-region-toggle >> nth=0'],
  ['message-drawer', '/local/siraj/index.php', '.siraj-plugins .popover-region-toggle >> nth=1'],
  ['user-menu', '/local/siraj/index.php', '.siraj-topbar .usermenu .dropdown-toggle'],
  ['filepicker', '/course/edit.php?id=4', '.fp-file >> nth=0'],
  ['reportbuilder-filters', '/reportbuilder/index.php', 'button[data-toggle="dropdown"]:has-text("Filters"), button:has-text("Filters")'],
  ['new-event-modal', '/calendar/view.php?view=month', 'button[data-action="new-event-button"]'],
];
const audit = () => {
  const lum = c => { const m = c.match(/[\d.]+/g); if (!m) return null; const [r, g, b, a = 1] = m.map(Number); if (a < .5) return null;
    const f = v => { v /= 255; return v <= .03928 ? v / 12.92 : Math.pow((v + .055) / 1.055, 2.4); }; return .2126 * f(r) + .7152 * f(g) + .0722 * f(b); };
  const vis = e => { const r = e.getBoundingClientRect(); const s = getComputedStyle(e); return r.width > 8 && r.height > 8 && s.visibility !== 'hidden' && s.display !== 'none' && s.opacity !== '0' && r.bottom > 0 && r.top < innerHeight * 3; };
  const bgOf = e => { while (e) { const L = lum(getComputedStyle(e).backgroundColor); if (L !== null) return L; e = e.parentElement; } return 0; };
  const name = e => e.tagName.toLowerCase() + (e.id ? '#' + e.id : '') + [...e.classList].slice(0, 2).map(c => '.' + c).join('');
  const light = [], low = [];
  document.querySelectorAll('body *').forEach(e => {
    if (!vis(e) || e.closest('.siraj-hero, .siraj-results, .siraj-feature, .siraj-course-cover, .siraj-profile-hero, .siraj-cert-face, img, svg, .siraj-cover')) return;
    const r = e.getBoundingClientRect(); const L = lum(getComputedStyle(e).backgroundColor);
    if (L !== null && L > .55 && r.width * r.height > 900 && !/btn-primary|siraj-btn-flame|badge|bg-success|day-number-circle|siraj-chip|toggle|form-check-input|progress-bar/.test(e.className)) light.push(name(e) + ` ${Math.round(r.width)}x${Math.round(r.height)}`);
    const own = [...e.childNodes].some(n => n.nodeType === 3 && n.textContent.trim().length > 1);
    if (own && !getComputedStyle(e).backgroundImage.includes("gradient")) { const fg = lum(getComputedStyle(e).color); const bg = bgOf(e); if (fg !== null) { const c = (Math.max(fg, bg) + .05) / (Math.min(fg, bg) + .05); if (c < 3) low.push(name(e) + ` "${e.textContent.trim().slice(0, 24)}" ${c.toFixed(1)}`); } }
  });
  return { light: [...new Set(light)], low: [...new Set(low)] };
};
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'admin'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  await p.evaluate(() => document.cookie = 'siraj-theme=dark; path=/');
  let total = 0; const only = process.argv[2];
  for (const [n, u, mode] of PAGES) {
    if (only && !n.includes(only)) continue;
    await p.goto(BASE + u, { waitUntil: 'networkidle' }).catch(() => {});
    if (mode === 'edit') { const sw = await p.$('.editmode-switch-form input'); if (sw && !(await sw.isChecked())) { await Promise.all([p.waitForNavigation().catch(() => {}), sw.click()]); await p.waitForLoadState('networkidle'); } const ba = await p.$('[data-action="bulkedit"], button:has-text("Bulk edit")'); if (ba) { await ba.click().catch(() => {}); await p.waitForTimeout(500); } }
    const r = await p.evaluate(audit); total += r.light.length + r.low.length;
    if (r.light.length || r.low.length) console.log(`## ${n}\n  light: ${r.light.slice(0, 8).join(' | ')}\n  lowcontrast: ${r.low.slice(0, 8).join(' | ')}`);
    else console.log(`ok ${n}`);
  }
  for (const [n, u, sel] of POPUPS) {
    if (only && !n.includes(only)) continue;
    await p.goto(BASE + u, { waitUntil: 'networkidle' }).catch(() => {});
    const el = await p.locator(sel).first().elementHandle({ timeout: 3000 }).catch(() => null); if (!el) { console.log(`?? ${n}: trigger not found`); continue; }
    await el.click().catch(() => {}); await p.waitForTimeout(1200);
    const r = await p.evaluate(audit); total += r.light.length + r.low.length;
    if (r.light.length || r.low.length) console.log(`## ${n} (popup)\n  light: ${r.light.slice(0, 8).join(' | ')}\n  lowcontrast: ${r.low.slice(0, 8).join(' | ')}`);
    else console.log(`ok ${n} (popup)`);
    await p.screenshot({ path: `../screenshots/dark-${n}.png` });
  }
  await p.evaluate(() => document.cookie = 'siraj-theme=light; path=/');
  console.log('TOTAL ISSUES', total); await b.close();
})();
