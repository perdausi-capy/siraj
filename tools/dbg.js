const { chromium } = require('playwright-core'); const fs=require('fs');
(async()=>{ const b=await chromium.launch({executablePath:process.env.HOME+'/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome'});
const p=await (await b.newContext()).newPage(); await p.goto('http://localhost:8000/login/index.php');
await p.fill('#username',process.argv[3]||'layla'); await p.fill('#password',fs.readFileSync(process.argv[2],'utf8').trim());
await Promise.all([p.waitForNavigation(),p.click('#loginbtn')]); await p.waitForLoadState('networkidle');
console.log(p.url()); console.log((await p.innerText('body')).slice(0,400)); await b.close();})();
