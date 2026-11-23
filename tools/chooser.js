const { chromium } = require('playwright-core'); const fs = require('fs');
const pass = fs.readFileSync(process.env.HOME + '/.config/siraj-local/admin-pass', 'utf8').trim(); const BASE = 'http://localhost:8000';
(async () => { const b = await chromium.launch({ executablePath: process.env.HOME + '/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome' });
  const p = await (await b.newContext({ viewport: { width: 1600, height: 950 } })).newPage();
  for (let i = 0; i < 2; i++) await p.goto(BASE + '/login/index.php', { waitUntil: 'networkidle' });
  await p.fill('#username', 'admin'); await p.fill('#password', pass); await Promise.all([p.waitForNavigation(), p.click('#loginbtn')]);
  for (const theme of ['dark', 'light']) {
    await p.evaluate(t => localStorage.setItem('siraj-theme', t), theme);
    await p.goto(BASE + '/course/view.php?id=4&siraj=classic', { waitUntil: 'networkidle' });
    const sw = p.locator('.editmode-switch-form input[type=checkbox]'); if (!(await sw.isChecked())) { await Promise.all([p.waitForNavigation(), sw.click()]); await p.waitForLoadState('networkidle'); }
    const btn = p.locator('.course-section').nth(1).locator('button.add-content, .add-content').first();
    await btn.evaluate(e => e.closest('.divider').scrollIntoView({ block: 'center' }));
    const box = await btn.evaluate(e => { const r = e.closest('.divider').getBoundingClientRect(); return { x: r.left + r.width / 4, y: r.top + r.height / 2 }; });
    await p.mouse.move(box.x, box.y); await p.waitForTimeout(400); await btn.evaluate(e => e.click()); await p.waitForTimeout(800);
    const menu = await p.evaluate(() => [...document.querySelectorAll('.dropdown-menu.show a, .dropdown-menu.show button')].map(a => a.textContent.trim()).join(' | '));
    console.log(theme, 'menu:', menu || '(none, chooser opened directly?)', '| modal:', await p.isVisible('.modchooser.show, .modal.show'));
    if (!(await p.isVisible('.modal.show'))) { await p.evaluate(() => { const a = [...document.querySelectorAll('.dropdown-menu.show a, .dropdown-menu.show button')].find(x => /activity|resource/i.test(x.textContent)); if (a) a.click(); }); await p.waitForTimeout(1500); }
    console.log('chooser visible:', await p.isVisible('.modal.show'));
    await p.screenshot({ path: `../screenshots/polish/${theme}-activity-chooser.png` });
    await p.keyboard.press('Escape'); await p.waitForTimeout(300);
    await Promise.all([p.waitForNavigation(), sw.click()]);
  }
  await b.close(); })();
