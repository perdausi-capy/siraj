const { chromium } = require('playwright-core');
(async()=>{ const b=await chromium.launch({executablePath:process.env.HOME+'/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome'});
const p=await (await b.newContext({viewport:{width:1440,height:900}})).newPage(); await p.goto('http://localhost:8000/login/index.php',{waitUntil:'networkidle'});
console.log(await p.evaluate(()=>{const w=document.querySelector('#page-wrapper'); const cs=getComputedStyle(w);
 return JSON.stringify({display:cs.display,dir:cs.flexDirection,parentTag:w.parentElement.tagName, kids:[...w.children].map(c=>c.tagName+'.'+c.className+' '+getComputedStyle(c).display+' w='+c.getBoundingClientRect().width)})}));
await b.close();})();
