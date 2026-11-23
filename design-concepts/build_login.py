# Four sign-in page concepts (standalone HTML, light + dark). Same type system as Concept A.
import os, sys
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from _parts import FONTS, SPRITE, i

MOON = '<svg width="0" height="0" style="position:absolute"><symbol id="moon" viewBox="0 0 24 24"><path d="M20 14.5A8 8 0 0 1 9.5 4 8 8 0 1 0 20 14.5z"/></symbol><symbol id="globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></symbol><symbol id="eye" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></symbol></svg>'
CSS = '''
:root{--bg:#F3F6FA;--surface:#fff;--soft:#EEF3F9;--line:#E4E9F0;--ink:#0E1B2A;--muted:#5B6B7C;--faint:#8A98A8;--navy:#0B2E4F;--link:#1E5FA8;
--serif:"Instrument Serif",Georgia,serif;--sans:"Inter",system-ui,sans-serif;--shadow:0 1px 2px rgba(14,27,42,.04),0 18px 50px rgba(14,27,42,.1)}
[data-theme="dark"]{--bg:#0A1220;--surface:#111B2B;--soft:#172438;--line:#1F2D42;--ink:#E8EEF5;--muted:#9AAABD;--faint:#6D7E93;--navy:#5FB3F0;--link:#6CB8F5;--shadow:0 18px 50px rgba(0,0,0,.45);color-scheme:dark}
*{box-sizing:border-box}html,body{margin:0;min-height:100%}body{font:400 14px/1.5 var(--sans);color:var(--ink);background:var(--bg);-webkit-font-smoothing:antialiased;transition:background .25s,color .25s}
a{color:var(--link);text-decoration:none}.ic{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex:none}
.flame{width:24px;height:29px}.brand{display:flex;align-items:center;gap:10px;color:inherit}.brand b{font:700 15px/1 var(--sans);letter-spacing:.22em}.brand small{display:block;font:600 7.5px/1 var(--sans);letter-spacing:.42em;margin-top:5px;opacity:.7}
.serif{font-family:var(--serif);font-weight:400;letter-spacing:-.01em}.eyebrow{font:600 11px/1 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:var(--muted)}
.tools{display:flex;gap:8px;align-items:center}.tbtn{height:38px;min-width:38px;padding:0 12px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--line);background:var(--surface);color:var(--muted);font:600 12.5px var(--sans);cursor:pointer}
.tbtn:hover{color:var(--ink)}.thm .m{display:none}[data-theme="dark"] .thm .s{display:none}[data-theme="dark"] .thm .m{display:block}
.form{width:100%;max-width:380px}.form h2{font:400 40px/1.05 var(--serif);margin:12px 0 8px}.form p.sub{margin:0 0 28px;color:var(--muted)}
label{display:block;font:600 12.5px var(--sans);color:var(--muted);margin:0 0 6px}.field{position:relative;margin-bottom:16px}
input{width:100%;height:50px;border-radius:14px;border:1px solid var(--line);background:var(--surface);color:var(--ink);padding:0 16px;font:400 15px var(--sans);outline:none;transition:border-color .15s,box-shadow .15s}
input:focus{border-color:var(--link);box-shadow:0 0 0 4px color-mix(in srgb,var(--link) 16%,transparent)}.field .eye{position:absolute;right:12px;bottom:13px;color:var(--faint)}
.row{display:flex;justify-content:space-between;align-items:center;margin:4px 0 22px;font-size:13px;color:var(--muted)}.row label{display:inline-flex;gap:8px;align-items:center;margin:0;font-weight:500}
.row input{width:16px;height:16px;accent-color:var(--navy)}
.submit{width:100%;height:52px;border:0;border-radius:999px;background:var(--navy);color:#fff;font:600 15px var(--sans);display:inline-flex;align-items:center;justify-content:center;gap:8px;cursor:pointer}
[data-theme="dark"] .submit{color:#06243C}.submit .ic{width:16px;height:16px}
.or{display:flex;align-items:center;gap:12px;margin:22px 0;color:var(--faint);font:600 11px var(--sans);letter-spacing:.14em;text-transform:uppercase}.or::before,.or::after{content:"";flex:1;height:1px;background:var(--line)}
.sso{width:100%;height:48px;border-radius:999px;border:1px solid var(--line);background:var(--surface);color:var(--ink);font:600 14px var(--sans);display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer}
.ms{width:16px;height:16px;display:grid;grid-template-columns:1fr 1fr;gap:2px}.ms i{display:block}.ms i:nth-child(1){background:#F25022}.ms i:nth-child(2){background:#7FBA00}.ms i:nth-child(3){background:#00A4EF}.ms i:nth-child(4){background:#FFB900}
.foot{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;color:var(--faint);font-size:12.5px}.foot a{color:var(--muted);text-decoration:underline;text-underline-offset:3px}
.note{position:fixed;right:16px;bottom:16px;z-index:50;background:#0E1B2A;color:#fff;font:500 12px var(--sans);padding:8px 12px;border-radius:999px;opacity:.85}
'''
def form(sub='Sign in to continue your learning journey.', eyebrow='Welcome to Siraj Learning', title='Good to have you here.'):
    return f'''<form class="form" onsubmit="return false"><div class="eyebrow">{eyebrow}</div><h2>{title}</h2><p class="sub">{sub}</p>
<div class="field"><label for="u">Username</label><input id="u" autocomplete="username" value="layla"></div>
<div class="field"><label for="p">Password</label><input id="p" type="password" value="password123"><span class="eye">{i("eye")}</span></div>
<div class="row"><label><input type="checkbox" checked> Remember me</label><a>Forgot password?</a></div>
<button class="submit">Sign in {i("arrow")}</button><div class="or">or</div><button class="sso" type="button"><span class="ms"><i></i><i></i><i></i><i></i></span>Continue with Microsoft</button></form>'''
TOOLS = f'<div class="tools"><button class="tbtn" type="button">{i("globe")}English</button><button class="tbtn thm" type="button" data-theme-toggle aria-label="Theme">{i("sun","ic s")}{i("moon","ic m")}</button></div>'
FOOT = '<div class="foot"><span>© 2026 Siraj Learning</span><a>Privacy</a><a>Cookies notice</a><a>Help</a></div>'
BRAND = '<div class="brand"><svg class="flame"><use href="#flame"/></svg><span><b>SIRAJ</b><small>LEARNING</small></span></div>'
JS = '''<script>(function(){var r=document.documentElement,k='siraj-login-theme',s=null;try{s=localStorage.getItem(k)}catch(e){}
r.dataset.theme=s||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');
document.querySelectorAll('[data-theme-toggle]').forEach(function(b){b.onclick=function(){r.dataset.theme=r.dataset.theme==='dark'?'light':'dark';try{localStorage.setItem(k,r.dataset.theme)}catch(e){}}});})();</script>'''

# 1. Photo split: half-screen architecture photo with a serif statement; form on a clean panel.
C1 = '''.wrap{display:grid;grid-template-columns:1.15fr .85fr;min-height:100vh}
.photo{position:relative;color:#fff;padding:44px 52px;display:flex;flex-direction:column;justify-content:space-between;
background:linear-gradient(180deg,rgba(8,24,42,.35) 0%,rgba(8,24,42,.15) 35%,rgba(8,24,42,.92) 100%),url(img/tower.jpg) center/cover}
.photo .eyebrow{color:#9FD8F2}.photo h1{font:400 clamp(48px,5.4vw,84px)/.98 var(--serif);margin:16px 0 18px;letter-spacing:-.015em}.photo p{color:#D2E2EE;font-size:17px;max-width:460px;margin:0}
.quote{display:flex;gap:28px;margin-top:36px;padding-top:24px;border-top:1px solid rgba(255,255,255,.18)}.quote b{display:block;font:400 34px/1 var(--serif)}.quote small{color:#B9D2DF;font-size:12.5px}
.side{display:flex;flex-direction:column;padding:28px clamp(24px,4vw,64px);background:var(--bg)}.side .tools{justify-content:flex-end}.side .center{flex:1;display:grid;place-items:center;padding:24px 0}
@media (max-width:900px){.wrap{grid-template-columns:1fr}.photo{min-height:340px;padding:24px}.photo h1{font-size:44px}.quote{display:none}.side{padding:20px 16px}}'''
P1 = f'''<div class="wrap"><section class="photo">{BRAND}<div><div class="eyebrow">Siraj Learning</div><h1>Learn today.<br>Lead tomorrow.</h1><p>Short, focused courses made for your role, with your next step always ready.</p>
<div class="quote"><div><b>10 min</b><small>Average lesson</small></div><div><b>40+</b><small>Courses in the library</small></div><div><b>EN · عربي</b><small>Learn in your language</small></div></div></div></section>
<section class="side">{TOOLS}<div class="center">{form()}</div>{FOOT}</section></div>'''

# 2. Full-bleed photo with a frosted glass card in the middle.
C2 = '''body{background:#0A1E33}.bg{position:fixed;inset:0;background:linear-gradient(120deg,rgba(8,24,42,.85),rgba(8,24,42,.35)),url(img/glass.jpg) center/cover}
.top{position:relative;z-index:2;display:flex;justify-content:space-between;align-items:center;padding:24px 40px;color:#fff}.top .tbtn{background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);color:#fff}
.mid{position:relative;z-index:2;min-height:calc(100vh - 160px);display:grid;grid-template-columns:1fr auto;align-items:center;gap:48px;padding:0 40px;max-width:1280px;margin:0 auto}
.mid h1{color:#fff;font:400 clamp(44px,5vw,76px)/1 var(--serif);margin:0 0 16px}.mid p.lead{color:#D2E2EE;font-size:17px;max-width:440px;margin:0}
.glass{width:440px;padding:40px;border-radius:28px;background:color-mix(in srgb,var(--surface) 86%,transparent);backdrop-filter:blur(22px) saturate(1.3);border:1px solid rgba(255,255,255,.35);box-shadow:0 30px 80px rgba(0,0,0,.35)}
[data-theme="dark"] .glass{border-color:rgba(255,255,255,.08)}
.bottom{position:relative;z-index:2;padding:20px}.bottom .foot,.bottom .foot a{color:rgba(255,255,255,.75)}
@media (max-width:900px){.mid{grid-template-columns:1fr;padding:0 16px;gap:24px}.mid h1{font-size:40px}.glass{width:100%;padding:24px}.top{padding:16px}}'''
P2 = f'''<div class="bg"></div><header class="top">{BRAND}{TOOLS}</header><main class="mid"><div><div class="eyebrow" style="color:#9FD8F2">Your learning space</div><h1>Learning that<br>lights the way.</h1><p class="lead">A clearer path to knowledge, confidence and better work.</p></div>
<div class="glass">{form()}</div></main><div class="bottom">{FOOT}</div>'''

# 3. Editorial: white page, big serif headline, a strip of course photos, form in a card.
C3 = '''.page{min-height:100vh;display:flex;flex-direction:column;padding:28px 48px}.head{display:flex;justify-content:space-between;align-items:center}
.body{flex:1;display:grid;grid-template-columns:1.1fr .9fr;gap:64px;align-items:center;max-width:1320px;width:100%;margin:0 auto;padding:32px 0}
.body h1{font:400 clamp(52px,6vw,96px)/.95 var(--serif);margin:16px 0 22px;letter-spacing:-.02em}.body h1 em{font-style:italic;color:var(--link)}.body p.lead{font-size:17px;color:var(--muted);max-width:480px;margin:0 0 36px}
.strip{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;max-width:620px}.strip figure{margin:0;border-radius:16px;overflow:hidden;aspect-ratio:3/4;position:relative}
.strip img{width:100%;height:100%;object-fit:cover;display:block}.strip figure:nth-child(even){transform:translateY(24px)}
.strip figcaption{position:absolute;left:10px;right:10px;bottom:10px;color:#fff;font:600 11.5px/1.3 var(--sans);text-shadow:0 1px 8px rgba(0,0,0,.5)}
.card{background:var(--surface);border:1px solid var(--line);border-radius:28px;padding:44px;box-shadow:var(--shadow);justify-self:end;width:100%;max-width:470px}
@media (max-width:1000px){.body{grid-template-columns:1fr;gap:32px}.card{justify-self:stretch;padding:24px}.page{padding:16px}.strip{display:none}.body h1{font-size:46px}}'''
strip = ''.join(f'<figure><img src="img/{f}" alt=""><figcaption>{t}</figcaption></figure>' for f,t in [('hse.jpg','Health &amp; Safety'),('lead.jpg','Leadership'),('infosec.jpg','Information Security'),('wellness.jpg','Wellness')])
P3 = f'''<div class="page"><header class="head">{BRAND}{TOOLS}</header><div class="body"><section><div class="eyebrow">Learn · Develop · Achieve</div><h1>Small steps.<br><em>Big impact.</em></h1>
<p class="lead">Mandatory training, new skills and certificates, all in one calm place, in English and Arabic.</p><div class="strip">{strip}</div></section>
<div class="card">{form()}</div></div>{FOOT}</div>'''

# 4. Product preview: navy panel showing the app itself (next step, progress ring, streak), form on the right.
C4 = '''.wrap{display:grid;grid-template-columns:1.1fr .9fr;min-height:100vh}
.navy{position:relative;overflow:hidden;color:#fff;padding:44px 52px;display:flex;flex-direction:column;background:radial-gradient(70% 60% at 85% 10%,rgba(47,184,240,.28),transparent 70%),radial-gradient(60% 50% at 0% 100%,rgba(0,201,140,.18),transparent 70%),linear-gradient(160deg,#0B2E4F,#071E34)}
.navy h1{font:400 clamp(40px,4.2vw,64px)/1.02 var(--serif);margin:auto 0 14px;max-width:560px}.navy p{color:#C9D9E6;font-size:16px;max-width:440px;margin:0 0 36px}
.mock{position:relative;height:300px;max-width:560px}.m{position:absolute;border-radius:20px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);backdrop-filter:blur(14px);box-shadow:0 20px 50px rgba(0,0,0,.3)}
.m1{left:0;top:0;width:380px;height:190px;overflow:hidden;background:linear-gradient(90deg,rgba(8,24,42,.95) 35%,rgba(8,24,42,.2)),url(img/hse.jpg) right/cover;padding:22px}
.m1 small{font:600 10px var(--sans);letter-spacing:.2em;color:#9FD8F2}.m1 b{display:block;font:400 26px/1.1 var(--serif);margin:8px 0 14px;max-width:230px}.m1 span{display:inline-flex;height:32px;padding:0 14px;border-radius:99px;background:#fff;color:#0B2E4F;font:600 12px var(--sans);align-items:center}
.m2{right:0;top:40px;width:170px;padding:20px;text-align:center}.ring{--p:72;width:96px;height:96px;margin:0 auto 10px;border-radius:50%;display:grid;place-items:center;background:radial-gradient(closest-side,#10324F 80%,transparent 82%),conic-gradient(#5FB3F0 calc(var(--p)*1%),rgba(255,255,255,.15) 0)}
.ring b{font:400 28px var(--serif)}.m2 small{color:#B9D2DF;font-size:12px}
.m3{left:60px;bottom:0;width:330px;padding:16px 18px}.m3 .h{display:flex;justify-content:space-between;font:600 13px var(--sans);margin-bottom:12px}.m3 .h em{font-style:normal;color:#6EE7B7;font-size:11.5px}
.days{display:grid;grid-template-columns:repeat(7,1fr);gap:5px}.days i{height:26px;border-radius:7px;background:rgba(255,255,255,.08);font:600 10px var(--sans);font-style:normal;display:grid;place-items:center;color:#8FA9BE}.days i.on{background:rgba(52,211,153,.22);color:#A7F3D0}
.side{display:flex;flex-direction:column;padding:28px clamp(24px,4vw,64px);background:var(--bg)}.side .tools{justify-content:flex-end}.side .center{flex:1;display:grid;place-items:center;padding:24px 0}
@media (max-width:1000px){.wrap{grid-template-columns:1fr}.navy{padding:24px}.mock{display:none}.navy h1{font-size:36px;margin-top:28px}.navy p{margin:0}.side{padding:20px 16px}}'''
days=''.join(f'<i class="{"on" if n<3 else ""}">{d}</i>' for n,d in enumerate(['M','T','W','T','F','S','S']))
P4 = f'''<div class="wrap"><section class="navy">{BRAND}<h1>Your next step,<br>always ready.</h1><p>Pick up exactly where you left off, on any device.</p>
<div class="mock"><div class="m m1"><small>YOUR NEXT STEP</small><b>Finish Workplace Health &amp; Safety</b><span>Continue learning →</span></div>
<div class="m m2"><div class="ring"><b>72%</b></div><small>Overall progress</small></div>
<div class="m m3"><div class="h">Your weekly rhythm <em>Good pace</em></div><div class="days">{days}</div></div></div></section>
<section class="side">{TOOLS}<div class="center">{form()}</div>{FOOT}</section></div>'''

# 2 with a looping video background (three options to compare).
C2V = C2.replace("body{background:#0A1E33}.bg{position:fixed;inset:0;background:linear-gradient(120deg,rgba(8,24,42,.85),rgba(8,24,42,.35)),url(img/glass.jpg) center/cover}",
 "body{background:#06182B}.bg{position:fixed;inset:0;overflow:hidden}.bg video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}"
 ".bg::after{content:'';position:absolute;inset:0;background:linear-gradient(100deg,rgba(6,20,36,.72) 0%,rgba(6,20,36,.35) 55%,rgba(6,20,36,.15) 100%)}"
 "@media (prefers-reduced-motion:reduce){.bg video{display:none}.bg{background:url(video/siraj-dubai-sunrise.jpg) center/cover}}") + '''
.pick{position:fixed;left:50%;bottom:64px;transform:translateX(-50%);z-index:5;display:flex;gap:6px;padding:5px;border-radius:999px;background:rgba(6,20,36,.6);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.15)}
.pick button{height:32px;padding:0 14px;border-radius:999px;border:0;background:transparent;color:#C9D9E6;font:600 12.5px var(--sans);cursor:pointer}.pick button.on{background:#fff;color:#0B2E4F}'''
P2V = P2.replace('<div class="bg"></div>', '<div class="bg"><video autoplay muted loop playsinline poster="video/siraj-dubai-sunrise.jpg" data-bgvideo><source src="video/siraj-dubai-sunrise.webm" type="video/webm"><source src="video/siraj-dubai-sunrise.mp4" type="video/mp4"></video></div>') + '''
<div class="pick">''' + ''.join(f'<button type="button" data-v="{k}" class="{"on" if k=="dubai-sunrise" else ""}">{t}</button>' for k,t in [('dubai-sunrise','Sunrise'),('dubai-night','Night'),('dubai-dusk','Dusk'),('skyline-clouds','Clouds'),('ribbons','Light'),('aurora','Aurora')]) + '''</div>
<script>document.querySelectorAll('.pick button').forEach(function(b){b.onclick=function(){var v=document.querySelector('[data-bgvideo]'),k=b.dataset.v;
v.poster='video/siraj-'+k+'.jpg';v.innerHTML='<source src="video/siraj-'+k+'.webm" type="video/webm"><source src="video/siraj-'+k+'.mp4" type="video/mp4">';v.load();v.play();
document.querySelectorAll('.pick button').forEach(function(x){x.classList.toggle('on',x===b)});};});</script>'''

here=os.path.dirname(os.path.abspath(__file__))
for n,(t,c,b,note) in enumerate([('Siraj sign in 1',C1,P1,'Login 1: photo split'),('Siraj sign in 2',C2,P2,'Login 2: full photo, glass card'),
        ('Siraj sign in 3',C3,P3,'Login 3: editorial, course photos'),('Siraj sign in 4',C4,P4,'Login 4: product preview')],1):
    open(os.path.join(here,f'login-{n}.html'),'w').write(f'<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{t}</title>{FONTS}<style>{CSS}{c}</style></head><body>{SPRITE}{MOON}{b}<div class="note">{note}</div>{JS}</body></html>')
    print('wrote', f'login-{n}.html')

open(os.path.join(here,'login-2-video.html'),'w').write(f'<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Siraj sign in</title>{FONTS}<style>{CSS}{C2V}</style></head><body>{SPRITE}{MOON}{P2V}<div class="note">Login 2 with video background</div>{JS}</body></html>')
print('wrote login-2-video.html')
