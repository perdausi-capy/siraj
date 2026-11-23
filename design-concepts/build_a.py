# Concept A, polished: same type and spacing scale as E, light and dark themes, working hero pager and theme toggle.
import os, sys
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from _parts import FONTS, SPRITE, BASE_CSS, i, COURSES

EXTRA_ICONS = '<svg width="0" height="0" style="position:absolute" aria-hidden="true"><symbol id="moon" viewBox="0 0 24 24"><path d="M20 14.5A8 8 0 0 1 9.5 4 8 8 0 1 0 20 14.5z"/></symbol></svg>'
CSS = BASE_CSS + '''
:root{--t-display:52px;--t-hero:40px;--t-stat:24px;--t-title:18px;--t-body:14px;--t-meta:12.5px;--t-label:11px;
--s1:8px;--s2:16px;--s3:24px;--s4:32px;--s5:40px;
--bg:#F3F6FA;--surface:#fff;--soft:#EEF3F9;--line:#E4E9F0;--ink:#0E1B2A;--muted:#5B6B7C;--faint:#8A98A8;--track:#E7EDF4;--fill:#12406A;
--side1:#0B2E4F;--side2:#0A2742;--mintc:#10B981;--mintsoft:#DDF7EC;--mintink:#047857;--aitint:#E6F7F1;--shadow:0 1px 2px rgba(14,27,42,.04),0 8px 24px rgba(14,27,42,.06);--accent:#1E5FA8;--iconbg:#EEF3F9;--iconfg:#12406A}
[data-theme="dark"]{--bg:#0A1220;--surface:#111B2B;--soft:#172438;--line:#1F2D42;--ink:#E8EEF5;--muted:#9AAABD;--faint:#6D7E93;--track:#22324A;--fill:#5FB3F0;
--side1:#071424;--side2:#06101D;--mintc:#34D399;--mintsoft:rgba(52,211,153,.14);--mintink:#6EE7B7;--aitint:rgba(52,211,153,.08);--shadow:0 1px 2px rgba(0,0,0,.3),0 10px 30px rgba(0,0,0,.35);--accent:#6CB8F5;--iconbg:#18283F;--iconfg:#8CC8F7;color-scheme:dark}
body{background:var(--bg);color:var(--ink);transition:background .25s,color .25s}
.app{display:grid;grid-template-columns:248px minmax(0,1fr);min-height:100vh}
/* sidebar */
.side{position:sticky;top:0;height:100vh;display:flex;flex-direction:column;padding:var(--s3) var(--s2);color:#fff;
background:radial-gradient(120% 40% at 0% 100%,rgba(30,106,138,.35),transparent 60%),linear-gradient(180deg,var(--side1),var(--side2))}
.side .brand{padding:0 12px var(--s4)}.side .brand small{color:#9FC3D4}
.side nav{display:grid;gap:2px}.side nav a{display:flex;align-items:center;gap:12px;height:42px;padding:0 12px;border-radius:12px;color:#A9BFD1;font:500 var(--t-body) var(--sans);cursor:pointer;transition:background .15s,color .15s}
.side nav a:hover{background:rgba(255,255,255,.06);color:#fff}.side nav a.on{background:rgba(255,255,255,.1);color:#fff;font-weight:600}
.side nav a em{margin-left:auto;min-width:20px;height:20px;border-radius:99px;display:grid;place-items:center;font:600 11px var(--sans);font-style:normal;background:#2FB8F0;color:#06243C}
.navlabel{font:600 var(--t-label) var(--sans);letter-spacing:.18em;text-transform:uppercase;color:#6F8CA4;padding:var(--s3) 12px var(--s1)}
.me{margin-top:auto;display:flex;align-items:center;gap:12px;padding:12px;border-radius:14px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08)}
.me b{display:block;font:600 13px var(--sans)}.me small{color:#A9BFD1;font-size:var(--t-meta)}.me .avatar{background:linear-gradient(135deg,#1E6A8A,#2FB8F0)}
/* top */
.top{display:flex;align-items:center;gap:var(--s2);padding:var(--s3) var(--s5)}
.search{width:min(440px,42vw);background:var(--surface);border-color:var(--line);color:var(--faint)}.search kbd{background:var(--soft);color:var(--muted)}
.tools{margin-left:auto;display:flex;align-items:center;gap:var(--s1)}.iconbtn{color:var(--muted);border:0;background:none;cursor:pointer}.iconbtn:hover{background:var(--soft);color:var(--ink)}
.dot{border-color:var(--bg)}
.thm .m{display:none}[data-theme="dark"] .thm .s{display:none}[data-theme="dark"] .thm .m{display:block}
main{padding:var(--s1) var(--s5) var(--s5);display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:var(--s4) var(--s4);align-items:start}
.colmain{display:grid;gap:var(--s4);min-width:0}.greet{grid-column:1/-1}
.rail{display:grid;gap:var(--s3);position:sticky;top:var(--s3)}
.rcard{background:var(--surface);border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);padding:var(--s3)}
.rhead{display:flex;align-items:center;justify-content:space-between;gap:var(--s1);margin-bottom:var(--s2)}
.rhead h3{margin:0;font:600 var(--t-title)/1.3 var(--sans)}
.badge{display:inline-flex;align-items:center;height:26px;padding:0 12px;border-radius:99px;font:600 var(--t-meta) var(--sans);background:var(--mintsoft);color:var(--mintink)}
.rhythm{display:flex;align-items:center;gap:var(--s2)}
.ring{--p:75;width:76px;height:76px;flex:none;border-radius:50%;display:grid;place-items:center;
background:radial-gradient(closest-side,var(--surface) 79%,transparent 81%),conic-gradient(var(--mintc) calc(var(--p)*1%),var(--track) 0)}
.ring b{font:600 18px var(--sans)}.rhythm h4{margin:0 0 4px;font:600 16px var(--sans)}.rhythm p{margin:0;color:var(--muted);font-size:13.5px;line-height:1.45}
.days{display:grid;grid-template-columns:repeat(7,1fr);gap:6px;margin-top:var(--s2)}
.days span{height:32px;border-radius:9px;display:grid;place-items:center;font:600 12px var(--sans);background:var(--soft);color:var(--faint)}
.days span.on{background:var(--mintsoft);color:var(--mintink)}.days span.today{box-shadow:inset 0 0 0 1.5px var(--mintc)}
.ai{background:linear-gradient(160deg,var(--aitint),var(--surface) 60%)}
.ai .who{display:flex;align-items:center;gap:12px;margin-bottom:var(--s2)}.ai .orb{width:40px;height:40px;border-radius:12px;flex:none;background:linear-gradient(135deg,#00E69A,#33CCFF 55%,#3A70B8)}
.ai .who b{display:block;font:600 15px var(--sans)}.ai .who small{color:var(--muted);font-size:var(--t-meta)}
.ai p{margin:0 0 var(--s2);color:var(--muted);font-size:13.5px;line-height:1.55}
.ai .acts{display:flex;align-items:center;gap:var(--s1);flex-wrap:wrap}
.ai .pill{height:38px;padding:0 16px;background:var(--soft);color:var(--ink);cursor:not-allowed}.ai .soon{font:600 var(--t-meta) var(--sans);color:var(--faint);padding:0 6px}
.up{display:grid}.up a{display:flex;align-items:center;gap:12px;padding:12px 0}.up a + a{border-top:1px solid var(--line)}
.up .d{width:44px;flex:none;text-align:center;border-radius:10px;background:var(--soft);padding:6px 0}.up .d b{display:block;font:600 16px/1 var(--sans)}.up .d small{font:600 10px var(--sans);letter-spacing:.1em;color:var(--muted)}
.up .t{min-width:0}.up .t b{display:block;font:600 13.5px var(--sans);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.up .t small{color:var(--muted);font-size:var(--t-meta)}
.up .t small{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.up .t small.late{color:#C2410C}[data-theme=dark] .up .t small.late{color:#FB923C}.rail{align-items:start}
/* greeting */
.greet{display:flex;align-items:center;justify-content:space-between;gap:var(--s3);padding-top:var(--s1)}
.greet h1{margin:var(--s1) 0 6px;font:400 var(--t-display)/1.02 var(--serif);letter-spacing:-.01em}.greet p{margin:0;color:var(--muted);font-size:16px}
.chip{display:inline-flex;align-items:center;gap:8px;height:34px;padding:0 14px;border-radius:99px;font:600 var(--t-meta) var(--sans);background:var(--surface);border:1px solid var(--line);color:var(--muted)}
.chip i{width:8px;height:8px;border-radius:50%;background:#E5A23B}
/* hero */
.hero{position:relative;overflow:hidden;border-radius:22px;height:340px;color:#fff;background:#0A1E33;box-shadow:var(--shadow)}
.slide{position:absolute;inset:0;display:flex;align-items:flex-end;padding:var(--s5);opacity:0;transition:opacity .6s ease;pointer-events:none;
background-size:cover;background-position:right center}
.slide.on{opacity:1;pointer-events:auto}
.slide::before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(8,24,42,.96) 0%,rgba(8,24,42,.85) 38%,rgba(8,24,42,.25) 72%,rgba(8,24,42,.05) 100%)}
.slide > div{position:relative;max-width:560px}.slide .eyebrow{color:#9FD8F2}
.slide h2{margin:12px 0 var(--s1);font:400 var(--t-hero)/1.08 var(--serif)}.slide p{margin:0 0 var(--s3);color:#C9D9E6;font-size:15px}
.slide .row{display:flex;align-items:center;gap:var(--s3)}.slide .row span{white-space:nowrap;display:inline-flex;align-items:center;gap:8px;color:#DCE8F1;font-size:13px}
.slide .row .ic{width:16px;height:16px}
.hero .pill.white{background:#fff;color:#0B2E4F}
.pager{position:absolute;right:var(--s4);bottom:var(--s4);z-index:2;display:flex;align-items:center;gap:10px;padding:5px 5px 5px 14px;border-radius:99px;background:rgba(8,24,42,.55);backdrop-filter:blur(8px);color:#fff;font:600 12px var(--sans);color:#C9D9E6}
.pager button{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;cursor:pointer;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.22);color:#fff;backdrop-filter:blur(6px)}
.pager button:hover{background:rgba(255,255,255,.22)}.pager .ic{width:15px;height:15px}
.bars{position:absolute;left:var(--s5);top:var(--s4);z-index:2;display:flex;gap:6px}.bars i{width:22px;height:3px;border-radius:3px;background:rgba(255,255,255,.3)}.bars i.on{width:34px;background:#fff}
/* stats */
.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:var(--s2)}
.stat{display:flex;align-items:center;gap:var(--s2);padding:var(--s3);background:var(--surface);border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow)}
.stat .si{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:var(--iconbg);color:var(--iconfg);flex:none}
.stat b{display:block;font:600 var(--t-stat)/1.1 var(--sans)}.stat small{color:var(--muted);font-size:var(--t-meta);white-space:nowrap}
/* courses */
.sechead{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:var(--s2)}
.sechead h3{margin:0;font:600 var(--t-title)/1.3 var(--sans)}.sechead a{display:inline-flex;align-items:center;gap:6px;font:600 var(--t-meta) var(--sans);color:var(--accent)}.sechead .ic{width:14px;height:14px}
.grid3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:var(--s3)}
.ccard{background:var(--surface);border-color:var(--line);box-shadow:var(--shadow)}.ccard img{height:auto;aspect-ratio:16/9}
.ccard .body{padding:var(--s2) 20px 20px}.ccard b{font:600 15px/1.35 var(--sans);margin-bottom:4px}.ccard small{font-size:var(--t-meta)}
.ccard .row{margin-top:var(--s2)}.bar{background:var(--track)}.bar i{background:var(--fill)}.ccard .row em{color:var(--ink);font:600 var(--t-meta) var(--sans);font-style:normal}
.tag{display:inline-block;margin-bottom:10px;font:600 var(--t-label) var(--sans);letter-spacing:.12em;text-transform:uppercase;color:var(--accent)}
[data-theme="dark"] .ccard img,[data-theme="dark"] .hero{filter:saturate(.92)}
.note{right:16px;left:auto}
.colmain .stats{grid-template-columns:repeat(4,minmax(0,1fr))}
@media (max-width:1400px){main{grid-template-columns:1fr}.rail{position:static;grid-template-columns:repeat(3,minmax(0,1fr))}}
@media (max-width:1180px){.rail{grid-template-columns:1fr 1fr}.colmain .stats{grid-template-columns:repeat(2,minmax(0,1fr))}.stats{grid-template-columns:repeat(2,minmax(0,1fr))}.grid3{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:900px){.app{grid-template-columns:1fr}.side{display:none}.top{padding:var(--s2)}.search{flex:1;width:auto;min-width:0;white-space:nowrap;overflow:hidden}.search kbd{display:none}
 main{padding:0 var(--s2) var(--s4);gap:var(--s4)}.greet{flex-direction:column;align-items:flex-start}.greet h1{font-size:40px}
 .hero{height:400px}.slide{padding:var(--s3);background-position:65% center}.slide::before{background:linear-gradient(0deg,rgba(8,24,42,.96) 0%,rgba(8,24,42,.8) 50%,rgba(8,24,42,.2) 100%)}
 .slide h2{font-size:30px}.pager{right:var(--s3);top:var(--s3);bottom:auto}.bars{left:var(--s3);top:var(--s3)+4px}
 .rail{grid-template-columns:1fr}.stats{gap:12px}.stat small{white-space:normal}.slide .row{flex-wrap:wrap;gap:var(--s2)}.slide .row span:last-child{display:none}.bars{display:none}.stat{padding:var(--s2);gap:12px}.stat .si{width:38px;height:38px}.stat b{font-size:20px}.grid3{grid-template-columns:1fr}}
'''
learn=[('home','Home',1),('play','My learning',0),('users','Teaching',0),('compass','Explore',0),('calendar','Calendar',0),('award','Achievements',0),('inbox','Inbox',0)]
manage=[('chart','Reports'),('shield','Compliance'),('trend','Admin studio'),('file','Custom reports')]
nav=''.join(f'<a class="{"on" if on else ""}">{i(ic)}<span>{t}</span>{"<em>3</em>" if t=="Inbox" else ""}</a>' for ic,t,on in learn)
nav2=''.join(f'<a>{i(ic)}<span>{t}</span></a>' for ic,t in manage)
slides=[('tower.jpg','Your next step · Mandatory','Finish Workplace Health &amp; Safety','One activity left: the knowledge check. About 10 minutes.','Resume learning','32 min','Due 21 Nov'),
        ('glass.jpg','Due soon','Continue Data Protection Essentials','Next up: Consent and your rights. Three short lessons to go.','Continue','25 min','Due 25 Nov'),
        ('office.jpg','Recommended for you','Start Leadership for Line Managers','Lead one-to-ones, give feedback and support your team.','Start course','1h 20m','Optional')]
sl=''.join(f'''<article class="slide{' on' if n==0 else ''}" style="background-image:url(img/{img})"><div><div class="eyebrow">{eb}</div><h2>{h}</h2><p>{p}</p>
<div class="row"><a class="pill white">{btn} {i("arrow")}</a><span>{i("clock")}{t}</span><span>{i("calendar")}{d}</span></div></div></article>''' for n,(img,eb,h,p,btn,t,d) in enumerate(slides))
stats=''.join(f'<div class="stat"><span class="si">{i(ic)}</span><span><b>{n}</b><small>{t}</small></span></div>' for ic,n,t in
              [('book','4','Courses in progress'),('check','8','Completed this month'),('star','1,240','Total points'),('clock','24h 30m','Learning time')])
cards=''
for img,name,meta,pct in [COURSES[1],COURSES[2],COURSES[3]]:
    cards+=f'<a class="ccard"><img src="img/{img}" alt=""><div class="body"><span class="tag">Mandatory</span><b>{name}</b><small>{meta}</small><div class="row"><span class="bar"><i style="width:{pct}%"></i></span><em>{pct}%</em></div></div></a>'
days=''.join(f'<span class="{c}">{d}</span>' for d,c in [('Mon','on'),('Tue','on'),('Wed','on today'),('Thu',''),('Fri',''),('Sat',''),('Sun','')])
ups=''.join(f'<a><span class="d"><b>{d}</b><small>NOV</small></span><span class="t"><b>{t}</b><small class="{c}">{m}</small></span></a>' for d,t,m,c in
  [('21','Knowledge check','Quiz closes in 3 days','late'),('25','Declaration','Data Protection · due',''),('29','Reflection','Fire Safety · due','')])
RAIL=f'''<aside class="rail">
<section class="rcard"><div class="rhead"><h3>Your weekly rhythm</h3><span class="badge">Good pace</span></div>
<div class="rhythm"><span class="ring" style="--p:75"><b>3/4</b></span><div><h4>One more day</h4><p>You're one learning day away from your weekly goal.</p></div></div>
<div class="days">{days}</div></section>
<section class="rcard ai"><div class="who"><span class="orb"></span><span><b>A thought from Siraj AI</b><small>Based on your learning</small></span></div>
<p>Want a quick refresher before your next step? The Siraj AI tutor can turn the key points into a 2-minute recap.</p>
<div class="acts"><span class="pill">{i("sparkle") if False else ""}Ask Siraj AI</span><span class="soon">Coming soon</span></div></section>
<section class="rcard"><div class="rhead"><h3>Coming up</h3><a style="font:600 12.5px var(--sans);color:var(--accent)">Calendar</a></div><div class="up">{ups}</div></section>
</aside>'''
BODY=f'''{EXTRA_ICONS}<div class="app"><aside class="side"><div class="brand"><svg class="flame"><use href="#flame"/></svg><span><b>SIRAJ</b><small>LEARNING</small></span></div>
<nav>{nav}</nav><div class="navlabel">Management</div><nav>{nav2}</nav>
<div class="me"><span class="avatar">LH</span><span><b>Layla Hassan</b><small>Engineering · Dubai</small></span></div></aside>
<div><header class="top"><div class="search">{i("search")}Search anything…<kbd>Ctrl K</kbd></div>
<div class="tools"><button class="iconbtn thm" data-theme-toggle aria-label="Toggle theme">{i("sun","ic s")}{i("moon","ic m")}</button><span class="iconbtn">{i("bell")}<span class="dot"></span></span><span class="avatar">LH</span></div></header>
<main><section class="greet"><div><div class="eyebrow">Wednesday, 18 November · Dubai</div><h1>Good morning, Layla.</h1><p>Keep learning. Keep growing.</p></div><span class="chip"><i></i>2 due this week</span></section>
<div class="colmain"><section class="hero" data-hero><div class="bars">{''.join(f'<i class="{"on" if n==0 else ""}"></i>' for n in range(3))}</div>{sl}
<div class="pager"><span data-count>1 / 3</span><button data-prev aria-label="Previous">{i("chevl")}</button><button data-next aria-label="Next">{i("chevr")}</button></div></section>
<section class="stats">{stats}</section>
<section><div class="sechead"><h3>Continue exploring</h3><a>View all {i("arrow")}</a></div><div class="grid3">{cards}</div></section></div>{RAIL}</main></div></div>
<div class="note">Concept A, polished · light &amp; dark</div>
<script>
(function(){{var r=document.documentElement,k='siraj-concept-theme',saved=null;try{{saved=localStorage.getItem(k)}}catch(e){{}}
r.dataset.theme=saved||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');
document.querySelector('[data-theme-toggle]').onclick=function(){{r.dataset.theme=r.dataset.theme==='dark'?'light':'dark';try{{localStorage.setItem(k,r.dataset.theme)}}catch(e){{}}}};
var h=document.querySelector('[data-hero]'),s=h.querySelectorAll('.slide'),b=h.querySelectorAll('.bars i'),c=h.querySelector('[data-count]'),n=0,t;
function go(x){{n=(x+s.length)%s.length;s.forEach(function(e,j){{e.classList.toggle('on',j===n)}});b.forEach(function(e,j){{e.classList.toggle('on',j===n)}});c.textContent=(n+1)+' / '+s.length;clearInterval(t);t=setInterval(function(){{go(n+1)}},7000)}}
h.querySelector('[data-prev]').onclick=function(){{go(n-1)}};h.querySelector('[data-next]').onclick=function(){{go(n+1)}};go(0);}})();
</script>'''
html=f'''<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Siraj concept A</title>{FONTS}<style>{CSS}</style></head><body>{SPRITE}{BODY}</body></html>'''
open(os.path.join(os.path.dirname(os.path.abspath(__file__)),'concept-a-polished.html'),'w').write(html)
print('wrote concept-a-polished.html')
