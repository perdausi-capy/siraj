// Phase 2 flows: reminders, notes, export, rules, enrol missing, certificate check, and access control.
const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync((process.env.SIRAJ_PASSFILE || process.env.HOME + '/.config/siraj-local/admin-pass'), 'utf8').trim(); const BASE = process.env.SIRAJ_BASE || 'http://localhost:8000';
let fails = 0; const ok = (c, m) => { console.log((c ? 'ok   ' : 'FAIL ') + m); if (!c) fails++; };
async function session(b, user) {
  const ctx = await b.newContext({ acceptDownloads: true }); const p = await ctx.newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  if (user) { await p.fill('#username', user); await p.fill('#password', user === 'admin' && process.env.SIRAJ_ADMINPASSFILE ? fs.readFileSync(process.env.SIRAJ_ADMINPASSFILE, 'utf8').trim() : pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
    await p.goto(BASE + '/local/siraj/index.php?lang=en'); }  // the checks read English text; some test users are set to Arabic
  return p;
}
(async () => {
  const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  // Manager.
  let p = await session(b, 'omar');
  await p.goto(BASE + '/local/siraj/team.php', { waitUntil: 'networkidle' });
  ok(await p.locator('.siraj-person').count() === 12, 'My team lists 12 people');
  const nover = +(await p.innerText('.siraj-filter[data-status="overdue"]')).replace(/\D+/g, ' ').trim().split(' ').pop();
  await p.click('.siraj-filter[data-status="overdue"]'); ok(nover > 0 && await p.locator('.siraj-person:visible').count() === nover, `Overdue filter shows its ${nover}`);
  await p.click('.siraj-filter[data-status="all"]'); await p.fill('[data-region="q"]', 'finance'); ok(await p.locator('.siraj-person:visible').count() === 2, 'search "finance" shows 2');
  await p.fill('[data-region="q"]', '');
  const need = +(await p.innerText('.siraj-filter[data-status="attention"]')).replace(/\D+/g, ' ').trim().split(' ').pop();
  await p.click('[data-action="select-attention"]'); const sel = await p.locator('input[name="users[]"]:checked').count();
  ok(sel === need, `select-attention ticks everyone who needs attention (${sel} of ${need})`);
  await Promise.all([p.waitForNavigation(), p.click('[data-region="send"]')]);
  const msg = await p.locator('.alert, .toast-message, [role="alert"]').first().innerText().catch(() => '');
  ok(/Reminder sent to \d+/.test(msg), 'reminders sent (or skipped if sent in the last day): ' + msg.replace(/\s+/g, ' ').slice(0, 90));
  await p.click('[data-action="select-attention"]'); await Promise.all([p.waitForNavigation(), p.click('[data-region="send"]')]);
  const msg2 = await p.locator('.alert, [role="alert"]').first().innerText().catch(() => '');
  ok(/skipped/.test(msg2), 'second send within a day is skipped: ' + msg2.replace(/\s+/g, ' ').slice(0, 90));
  const href = await p.locator('.siraj-person-who').first().getAttribute('href');
  await p.goto(href, { waitUntil: 'networkidle' });
  await p.fill('#siraj-note', 'Agreed to finish Anti-Bribery by Thursday'); await Promise.all([p.waitForNavigation(), p.click('.siraj-noteform button')]);
  ok(await p.locator('.siraj-timeline li', { hasText: 'Agreed to finish' }).count() >= 1, 'note saved and shown');
  ok(await p.locator('.siraj-timeline li.is-reminder').count() >= 1, 'reminder shows in timeline');
  await p.goto(BASE + '/local/siraj/reports.php', { waitUntil: 'networkidle' });
  ok(await p.locator('canvas[data-siraj-chart]').evaluate(c => c.dataset.drawn === '1'), 'weekly chart drawn');
  await p.goto(BASE + '/local/siraj/reports.php', { waitUntil: 'networkidle' });
  const links = await p.$$eval('.siraj-head-actions .dropdown-menu a', as => as.map(a => a.href));
  const res = await p.evaluate(async urls => Promise.all(urls.map(async u => { const r = await fetch(u); const buf = await r.arrayBuffer();
    return { type: r.headers.get('content-type'), disp: r.headers.get('content-disposition'), size: buf.byteLength,
      text: new TextDecoder().decode(buf.slice(0, 4000)) }; })), links);
  for (const r of res) console.log('     ', r.type, r.disp, r.size);
  ok(res.some(r => /csv/.test(r.type || '') && /Person,Email,Department/.test(r.text) && r.size > 3000), 'CSV export');
  ok(res.some(r => /spreadsheet|excel/.test(r.type || '') && r.size > 3000), 'Excel export');
  await p.goto(BASE + '/local/siraj/matrix.php', { waitUntil: 'networkidle' });
  ok(await p.locator('.siraj-mx-enrol').count() === 0, 'manager cannot enrol or edit rules');
  await p.goto(BASE + '/local/siraj/studio.php'); ok(/permission/i.test(await p.innerText('body')), 'manager blocked from Admin studio');
  // A person outside the team.
  const adminid = 2; await p.goto(BASE + '/local/siraj/person.php?id=' + adminid); ok(/permission/i.test(await p.innerText('body')), 'manager cannot open someone outside the team');
  // Learner.
  p = await session(b, 'layla');
  for (const u of ['team', 'reports', 'matrix', 'studio']) { await p.goto(BASE + `/local/siraj/${u}.php`); const c = await p.content(); ok(/permission|Nobody reports to you/i.test(c), `learner blocked or empty on ${u}`); }
  ok(await p.locator('.siraj-sidebar a[href*="team.php"]').count() === 0, 'learner sidebar has no Management items');
  const nmsg = await p.evaluate(async () => (await (await fetch('/local/siraj/inbox.php')).text()).includes('sent you a reminder'));
  ok(nmsg, 'Layla got the reminder in Inbox');
  await p.goto(BASE + '/local/siraj/achievements.php', { waitUntil: 'networkidle' });
  // Admin.
  p = await session(b, 'admin');
  await p.goto(BASE + '/local/siraj/matrix.php?scope=all', { waitUntil: 'networkidle' });
  const missing = await p.locator('.siraj-mxpill.mx-missing').count();
  if (await p.locator('.siraj-mx-enrol').count()) await Promise.all([p.waitForNavigation(), p.locator('.siraj-mx-enrol').first().click()]);
  else console.log('     (nobody missing any more: enrolled on an earlier run)');
  ok(missing === 0 || await p.locator('.siraj-mxpill.mx-missing').count() < missing, `enrol missing: ${missing} -> ${await p.locator('.siraj-mxpill.mx-missing').count()}`);
  await p.fill('input[name="newrole"]', 'Warehouse'); await p.locator('input[name="newcourses[]"]').first().check();
  await Promise.all([p.waitForNavigation(), p.click('text=Save rules')]);
  ok(await p.locator('.siraj-rules th', { hasText: 'Warehouse' }).count() === 1, 'new job role rule saved');
  await p.goto(BASE + '/local/siraj/studio.php', { waitUntil: 'networkidle' });
  ok(await p.locator('canvas[data-siraj-chart]').evaluate(c => c.dataset.drawn === '1'), 'studio chart drawn');
  await p.goto(BASE + '/local/siraj/reports.php?scope=all&dept=Finance', { waitUntil: 'networkidle' });
  ok(/Finance: 2 people/.test(await p.innerText('.siraj-pagehead')), 'department filter');
  // Certificate check, signed out.
  p = await session(b, null);
  await p.goto(BASE + '/local/siraj/verify.php?code=0000-0000-0000'); ok(await p.locator('.siraj-verify-result.bad').count() === 1, 'unknown code rejected');
  const real = require('child_process').execFileSync('docker', ['exec', 'siraj-db-1', 'sh', '-c',
    'mariadb -N -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "select code from m_local_siraj_cert limit 1"']).toString().trim();
  if (real) { await p.goto(BASE + '/local/siraj/verify.php?code=' + real.toLowerCase().replace(/-/g, '')); ok(await p.locator('.siraj-verify-result.ok').count() === 1, 'real code verified (typed without dashes, lower case)'); }
  else ok(false, 'could not read a code from the DB');
  console.log(fails ? `${fails} FAILED` : 'ALL FLOWS OK'); await b.close();
})();
