const { chromium } = require('playwright-core');
(async()=>{ const b=await chromium.launch({executablePath:process.env.HOME+'/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome'});
const p=await (await b.newContext({viewport:{width:1440,height:1000}})).newPage();
await p.goto('http://localhost:8000/login/index.php',{waitUntil:'networkidle'});
console.log(await p.evaluate(()=>{const m=document.querySelector('.siraj-signin'); const cs=getComputedStyle(m); return JSON.stringify({tag:m.tagName, display:cs.display, cols:cs.gridTemplateColumns, w:m.getBoundingClientRect().width, kids:[...m.children].map(c=>c.className+' w='+Math.round(c.getBoundingClientRect().width)), wrap:getComputedStyle(document.querySelector('.siraj-signin-wrap')).display})}));
await b.close();})();
