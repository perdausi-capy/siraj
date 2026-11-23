# Builds four standalone concept previews: concept-a..d.html (images in ./img).
import sys, os
sys.path.insert(0, os.path.dirname(__file__))
from _parts import page, i, COURSES

def sidebar(dark):
    learn=[('home','Home',1),('play','My learning',0),('users','Teaching',0),('compass','Explore',0),('calendar','Calendar',0),('award','Achievements',0),('inbox','Inbox',0)]
    manage=[('chart','Reports'),('shield','Compliance'),('trend','Admin studio'),('file','Custom reports')]
    nav=''.join(f'<a class="{ "on" if on else "" }">{i(ic)}<span>{t}</span>{"<em>3</em>" if t=="Inbox" else ""}</a>' for ic,t,on in learn)
    nav2=''.join(f'<a>{i(ic)}<span>{t}</span></a>' for ic,t in manage)
    return f'''<aside class="side {'dark' if dark else ''}"><div class="brand"><svg class="flame"><use href="#flame"/></svg><span><b>SIRAJ</b><small>LEARNING</small></span></div>
<nav>{nav}</nav><div class="navlabel">Management</div><nav>{nav2}</nav>
<div class="me"><span class="avatar">LH</span><span><b>Layla Hassan</b><small>Learner</small></span></div></aside>'''
SIDE_CSS='''.app{display:grid;grid-template-columns:240px 1fr;min-height:100vh}
.side{position:sticky;top:0;height:100vh;padding:24px 16px;display:flex;flex-direction:column;gap:6px;background:#fff;border-right:1px solid var(--line)}
.side .brand{padding:0 10px 22px}.side nav{display:grid;gap:2px}.side nav a{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:12px;color:var(--muted);font-weight:500;cursor:pointer}
.side nav a:hover{background:var(--soft);color:var(--ink)}.side nav a.on{background:var(--soft);color:var(--navy);font-weight:600}
.side nav a em{margin-left:auto;font:600 11px var(--sans);font-style:normal;background:var(--navy);color:#fff;border-radius:99px;padding:1px 7px}
.navlabel{font:600 10.5px var(--sans);letter-spacing:.18em;text-transform:uppercase;color:var(--faint);padding:20px 12px 8px}
.me{margin-top:auto;display:flex;align-items:center;gap:10px;padding:12px;border-radius:14px}.me b{display:block;font-size:13px}.me small{color:var(--muted);font-size:11.5px}
.side.dark{background:linear-gradient(180deg,#0B2E4F,#0A2742);border-right:0;color:#fff}.side.dark nav a{color:#A8BED0}.side.dark nav a:hover{background:rgba(255,255,255,.06);color:#fff}
.side.dark nav a.on{background:rgba(255,255,255,.1);color:#fff}.side.dark .navlabel{color:#6F8CA4}.side.dark .me small{color:#A8BED0}.side.dark nav a em{background:#2FB8F0;color:#06243C}
.top{display:flex;align-items:center;gap:12px;padding:20px 40px}.top .search{width:min(420px,40vw)}.top .tools{margin-left:auto;display:flex;align-items:center;gap:6px}
main{padding:8px 40px 48px;max-width:1240px}
@media (max-width:900px){.app{grid-template-columns:1fr}.side{display:none}.top,main{padding-left:16px;padding-right:16px}}'''
TOOLS=f'<div class="tools"><span class="iconbtn">{i("sun")}</span><span class="iconbtn">{i("bell")}<span class="dot"></span></span><span class="avatar">LH</span></div>'

def cards(n=3, arrow=False):
    out=''
    for img,name,meta,pct in COURSES[:n]:
        tail=f'<span class="roundbtn">{i("arrow")}</span>' if arrow else f'<em>{pct}%</em>'
        out+=f'<a class="ccard"><img src="img/{img}" alt=""><div class="body"><b>{name}</b><small>{meta}</small><div class="row"><span class="bar"><i style="width:{pct}%"></i></span>{tail}</div></div></a>'
    return out

# ---------- A: dark sidebar, photo hero, stat strip, cards ----------
A_CSS=SIDE_CSS+'''
h1.greet{font-size:44px;line-height:1.05;margin:6px 0 4px}.sub{color:var(--muted);margin:0 0 26px;font-size:15px}
.hero{position:relative;overflow:hidden;border-radius:22px;min-height:300px;color:#fff;display:flex;align-items:flex-end;padding:36px 40px;
background:linear-gradient(90deg,rgba(9,30,52,.96) 0%,rgba(9,30,52,.82) 42%,rgba(9,30,52,.15) 75%),url(img/tower.jpg) right center/cover}
.hero .eyebrow{color:#9FD8F2}.hero h2{font-size:40px;line-height:1.08;margin:10px 0 8px;max-width:520px}.hero p{margin:0 0 22px;color:#C9D9E6;max-width:440px}
.hero .meta{display:flex;align-items:center;gap:18px}.hero .meta span{display:inline-flex;gap:7px;align-items:center;color:#DCE8F1;font-size:13px}
.pager{position:absolute;right:28px;bottom:28px;display:flex;align-items:center;gap:10px;font-size:12px;color:#C9D9E6}.pager .roundbtn{width:30px;height:30px;background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.2);color:#fff}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin:18px 0 34px}.stat{display:flex;align-items:center;gap:14px;padding:16px 18px}
.stat .si{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:var(--soft);color:var(--navy2)}.stat b{display:block;font:600 20px var(--sans)}.stat small{color:var(--muted);font-size:12px}
.sh{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:14px}.sh h3{margin:0;font:600 18px var(--sans)}.sh a{font-weight:600;color:var(--accent);display:inline-flex;gap:6px;align-items:center}.sh .ic{width:15px;height:15px}
.grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
@media (max-width:900px){.stats{grid-template-columns:1fr 1fr}.grid3{grid-template-columns:1fr}.hero{padding:26px;min-height:260px}.hero h2{font-size:30px}h1.greet{font-size:34px}}'''
A=f'''<div class="app">{sidebar(True)}<div><div class="top"><div class="search">{i("search")}Search anything…<kbd>Ctrl K</kbd></div>{TOOLS}</div><main>
<h1 class="greet serif">Good morning, Layla.</h1><p class="sub">Keep learning. Keep growing.</p>
<section class="hero"><div><div class="eyebrow">Your next step · Mandatory</div><h2 class="serif">Finish Workplace Health &amp; Safety</h2>
<p>One activity left: the knowledge check. About 10 minutes.</p><div class="meta"><a class="pill white">Resume learning {i("arrow")}</a><span>{i("clock")}32 min</span></div></div>
<div class="pager">1 / 3 <span class="roundbtn">{i("chevl")}</span><span class="roundbtn">{i("chevr")}</span></div></section>
<section class="stats">{''.join(f'<div class="card stat"><span class="si">{i(ic)}</span><span><b>{n}</b><small>{t}</small></span></div>' for ic,n,t in [('book','4','Courses in progress'),('check','8','Completed this month'),('star','1,240','Total points'),('clock','24h 30m','Learning time')])}</section>
<div class="sh"><h3>Continue exploring</h3><a>View all {i("arrow")}</a></div><div class="grid3">{cards(3)}</div></main></div></div>'''

# ---------- B: top navigation, split hero, at-a-glance, right rail ----------
B_CSS='''.topnav{display:flex;align-items:center;gap:36px;padding:18px 48px;background:#fff;border-bottom:1px solid var(--line)}
.topnav nav{display:flex;gap:28px}.topnav nav a{font-weight:500;color:var(--muted);padding:6px 0;cursor:pointer}.topnav nav a.on{color:var(--navy);font-weight:600;box-shadow:inset 0 -2px 0 var(--navy)}
.topnav .tools{margin-left:auto;display:flex;gap:6px;align-items:center}
.wrap{display:grid;grid-template-columns:1fr 300px;gap:0;max-width:1320px;margin:28px auto;padding:0 40px}
.mainc{padding-right:32px}.hero{display:grid;grid-template-columns:.9fr 1.1fr;gap:28px;align-items:center;margin-bottom:34px}
.hero h1{font-size:62px;line-height:1;margin:14px 0 14px}.hero p{color:var(--muted);font-size:16px;margin:0 0 26px}.hero .acts{display:flex;align-items:center;gap:18px}.hero .acts a.link{font-weight:600;color:var(--navy)}
.hero img{width:100%;height:340px;object-fit:cover;border-radius:24px}
.glance{display:grid;grid-template-columns:1.3fr repeat(3,1fr);align-items:center;padding:22px 24px;margin-bottom:30px}.glance h3{margin:0 0 6px;font:600 16px var(--sans)}
.glance .spark{position:relative}.glance svg.sp{width:100%;height:56px}.glance .tag{position:absolute;right:30px;top:26px;background:#fff;border:1px solid var(--line);border-radius:99px;font:600 11px var(--sans);padding:2px 8px;color:var(--mint)}
.g{padding-left:24px;border-left:1px solid var(--line)}.g .ic{color:var(--muted)}.g b{display:block;font:600 24px var(--sans);margin-top:8px}.g small{color:var(--muted)}
.sh{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:12px}.sh h3{margin:0;font:600 16px var(--sans)}.sh a{font-weight:600;font-size:13px;color:var(--navy)}
.reco{display:flex;align-items:center;gap:24px;padding:14px}.reco img{width:220px;height:120px;object-fit:cover;border-radius:12px}.reco b{font-size:16px}.reco p{margin:6px 0;color:var(--muted)}.reco small{color:var(--faint)}.reco .roundbtn{margin-left:auto}
.rail{border-left:1px solid var(--line);padding-left:28px}.rail section{padding:4px 0 24px;margin-bottom:22px;border-bottom:1px solid var(--line)}.rail h4{margin:0 0 12px;font:600 14px var(--sans);display:flex;justify-content:space-between}
.rail .big{font:400 46px/1 var(--serif)}.rail p{color:var(--muted);margin:8px 0 14px}.rail .links a{display:flex;align-items:center;gap:12px;padding:9px 0;color:var(--ink);font-weight:500}.rail .links .ic{color:var(--muted)}
.rail .target{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:var(--soft);color:var(--accent)}
@media (max-width:900px){.wrap{grid-template-columns:1fr;padding:0 16px}.mainc{padding:0}.hero{grid-template-columns:1fr}.hero h1{font-size:44px}.glance{grid-template-columns:1fr 1fr}.rail{border:0;padding:0}.topnav{padding:14px 16px}.topnav nav{display:none}.reco{flex-direction:column;align-items:flex-start}.reco img{width:100%}}'''
B=f'''<header class="topnav"><div class="brand"><svg class="flame"><use href="#flame"/></svg><span><b>SIRAJ</b><small>LEARNING</small></span></div>
<nav><a class="on">Home</a><a>My learning</a><a>Explore</a><a>Calendar</a></nav>{TOOLS}</header>
<div class="wrap"><div class="mainc"><section class="hero"><div><div class="eyebrow">Learn · Develop · Achieve</div><h1 class="serif">Good morning,<br>Layla.</h1><p>Small steps. Big impact.</p>
<div class="acts"><a class="pill navy">Continue learning {i("arrow")}</a><a class="link">Browse courses</a></div></div><img src="img/desk.jpg" alt=""></section>
<section class="card glance"><div class="spark"><h3>Your learning at a glance</h3><span class="tag">+12%</span><svg class="sp" viewBox="0 0 300 56" preserveAspectRatio="none"><path d="M0 44 C40 40 60 46 100 38 S170 30 200 26 S260 12 300 8" fill="none" stroke="#1E5FA8" stroke-width="2"/><circle cx="250" cy="17" r="4" fill="#1E5FA8"/></svg></div>
{''.join(f'<div class="g">{i(ic)}<b>{n}</b><small>{t}</small></div>' for ic,n,t in [('book','4','In progress'),('check','8','Completed'),('clock','24h 30m','Learning time')])}</section>
<div class="sh"><h3>Recommended for you</h3><a>View all</a></div>
<a class="card reco"><img src="img/lead.jpg" alt=""><div><b>Leadership for Line Managers</b><p>Lead one-to-ones, give feedback and support your team.</p><small>1h 20m · 5 activities</small></div><span class="roundbtn dark">{i("arrow")}</span></a></div>
<aside class="rail"><section><h4>Your weekly goal <span class="target">{i("target")}</span></h4><div class="big">2/4</div><p>Learn on 2 more days to reach your goal.</p><span class="bar"><i style="width:50%"></i></span></section>
<section class="links"><h4>Quick links</h4><a>{i("compass")}Browse courses</a><a>{i("calendar")}View calendar</a><a>{i("award")}My certificates</a><a>{i("help")}Need help?</a></section></aside></div>'''

# ---------- C: light sidebar, serif greeting with ring, course cards with arrows ----------
C_CSS=SIDE_CSS+'''
.welcome{display:grid;grid-template-columns:1.2fr auto 1fr;gap:36px;align-items:center;padding:34px 38px;margin-bottom:30px;position:relative;overflow:hidden}
.welcome::after{content:"";position:absolute;left:30%;bottom:-160px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,#E8F0F8,transparent 70%)}
.welcome h1{font-size:54px;line-height:1.02;margin:12px 0 10px}.welcome p{color:var(--muted);margin:0 0 22px;font-size:15px}
.ring{--p:75;width:170px;height:170px;border-radius:50%;display:grid;place-items:center;background:radial-gradient(closest-side,#fff 82%,transparent 84%),conic-gradient(var(--navy) calc(var(--p)*1%),#E7EDF4 0)}
.ring b{display:block;font:400 40px/1 var(--serif);text-align:center}.ring small{color:var(--muted);font-size:12px}
.todo{border-left:1px solid var(--line);padding-left:30px;display:grid;gap:16px}.todo div{display:flex;gap:12px;align-items:center;font-weight:500}.todo .ic{color:var(--accent)}
.sh{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:14px}.sh h3{margin:0;font:600 18px var(--sans)}.sh a{font-weight:600;color:var(--accent);display:inline-flex;gap:6px;align-items:center}.sh .ic{width:15px;height:15px}
.grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.ccard .row em{display:none}
@media (max-width:900px){.welcome{grid-template-columns:1fr;padding:24px}.todo{border:0;padding:0}.grid3{grid-template-columns:1fr}.welcome h1{font-size:40px}}'''
C=f'''<div class="app">{sidebar(False)}<div><div class="top"><div class="search">{i("search")}Search anything…<kbd>Ctrl K</kbd></div>{TOOLS}</div><main>
<section class="card welcome"><div><div class="eyebrow">Welcome back</div><h1 class="serif">Good morning,<br>Layla.</h1><p>Your learning journey is on track.</p><a class="pill navy">Continue learning {i("arrow")}</a></div>
<div class="ring" style="--p:75"><div><b>75%</b><small>Overall progress</small></div></div>
<div class="todo"><div>{i("check")}2 courses to complete</div><div>{i("check")}1 badge available</div><div>{i("check")}Next due: Fri, 21 Nov</div></div></section>
<div class="sh"><h3>Your courses</h3><a>View all {i("arrow")}</a></div><div class="grid3">{cards(3, True)}</div></main></div></div>'''

# ---------- D: half-screen photo hero with progress panel ----------
D_CSS='''.split{display:grid;grid-template-columns:1.45fr 1fr;min-height:100vh}
.photo{position:relative;color:#fff;padding:44px 52px;display:flex;flex-direction:column;justify-content:space-between;
background:linear-gradient(90deg,rgba(8,25,44,.92) 0%,rgba(8,25,44,.7) 45%,rgba(8,25,44,.2) 100%),url(img/glass.jpg) center/cover}
.photo .brand small{opacity:.8}.photo .eyebrow{color:#9FD8F2}.photo h1{font-size:72px;line-height:.98;margin:16px 0 16px}.photo p{color:#C9D9E6;font-size:16px;margin:0 0 30px;max-width:420px}
.photo nav{display:flex;gap:26px;margin-left:auto}.photo nav a{color:#C9D9E6;font-weight:500}.photo nav a.on{color:#fff}
.photo .head{display:flex;align-items:center}.dots{display:flex;gap:6px}.dots i{width:22px;height:3px;border-radius:3px;background:rgba(255,255,255,.35)}.dots i:first-child{background:#fff;width:34px}
.panel{background:#fff;padding:28px 34px;display:flex;flex-direction:column;gap:26px}.panel .tools{display:flex;justify-content:flex-end;gap:6px;align-items:center}
.panel h3{margin:0 0 16px;font:600 15px var(--sans);display:flex;justify-content:space-between}.panel h3 a{font-size:12.5px;color:var(--accent)}
.prog{display:grid;grid-template-columns:auto 1fr;gap:26px;align-items:center}
.ring{--p:75;width:150px;height:150px;border-radius:50%;display:grid;place-items:center;background:radial-gradient(closest-side,#fff 82%,transparent 84%),conic-gradient(var(--navy) calc(var(--p)*1%),#E7EDF4 0)}
.ring b{display:block;font:400 36px/1 var(--serif);text-align:center}.ring small{color:var(--muted);font-size:12px;display:block;text-align:center}
.nums{border-left:1px solid var(--line);padding-left:22px;display:grid;gap:14px}.nums b{display:block;font:600 22px var(--sans)}.nums small{color:var(--muted)}
.up{display:flex;align-items:center;gap:14px;padding:12px}.up img{width:64px;height:56px;border-radius:10px;object-fit:cover}.up b{display:block}.up small{color:#C2410C;font-weight:600}.up .roundbtn{margin-left:auto}
.mini{display:grid;gap:10px}
@media (max-width:900px){.split{grid-template-columns:1fr}.photo{min-height:520px;padding:24px}.photo h1{font-size:46px}.photo nav{display:none}.panel{padding:22px 16px}}'''
D=f'''<div class="split"><section class="photo"><div class="head"><div class="brand"><svg class="flame"><use href="#flame"/></svg><span><b>SIRAJ</b><small>LEARNING</small></span></div>
<nav><a class="on">Home</a><a>My learning</a><a>Explore</a><a>Calendar</a></nav></div>
<div><div class="eyebrow">Siraj Learning</div><h1 class="serif">Learn today.<br>Lead tomorrow.</h1><p>Build your skills with short, focused courses made for your role.</p><a class="pill white">Continue learning {i("arrow")}</a></div>
<div class="dots"><i></i><i></i><i></i></div></section>
<aside class="panel">{TOOLS.replace('class="tools"','class="tools"')}
<div><h3>Your progress</h3><div class="prog"><div class="ring"><div><b>75%</b><small>Overall</small></div></div><div class="nums"><div><b>4</b><small>In progress</small></div><div><b>8</b><small>Completed</small></div><div><b>24h 30m</b><small>Learning time</small></div></div></div></div>
<div><h3>Upcoming activity <a>View all</a></h3><div class="mini"><a class="card up"><img src="img/dpe.jpg" alt=""><span><b>Data Protection Essentials</b><small>Due in 2 days</small></span><span class="roundbtn">{i("arrow")}</span></a>
<a class="card up"><img src="img/hse.jpg" alt=""><span><b>Workplace Health &amp; Safety</b><small>Due in 4 days</small></span><span class="roundbtn">{i("arrow")}</span></a></div></div>
<div><h3>Continue where you left off</h3><div class="mini"><a class="card up"><img src="img/infosec.jpg" alt=""><span><b>Information Security Essentials</b><small style="color:var(--muted);font-weight:500">40% · 28 min left</small></span><span class="roundbtn">{i("arrow")}</span></a></div></div></aside></div>'''

# ---------- E: concept B's content with the light sidebar, on one type and spacing scale ----------
# Type: display 56 serif · stat 26 · title 18 · body 14 · meta 12.5 · label 11 caps.  Spacing: 8 · 16 · 24 · 32 · 40.
E_CSS=SIDE_CSS+"""
:root{--t-display:56px;--t-stat:26px;--t-title:18px;--t-body:14px;--t-meta:12.5px;--t-label:11px;--s1:8px;--s2:16px;--s3:24px;--s4:32px;--s5:40px}
.top{padding:var(--s3) var(--s5)}main.e{max-width:none;padding:0 var(--s5) var(--s5)}
.e .layout{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:var(--s5);max-width:1560px}
.e .col{display:grid;gap:var(--s5);align-content:start}
.e h2.title{margin:0;font:600 var(--t-title)/1.3 var(--sans);color:var(--ink)}
.e .meta{font-size:var(--t-meta);color:var(--muted)}
.e .sechead{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:var(--s2)}
.e .sechead a{font:600 var(--t-meta) var(--sans);color:var(--accent)}
.e .hero{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.15fr);gap:var(--s5);align-items:center}
.e .hero h1{font:400 var(--t-display)/1.02 var(--serif);margin:var(--s2) 0;letter-spacing:-.01em}
.e .hero p{margin:0 0 var(--s3);font-size:16px;color:var(--muted)}
.e .hero .acts{display:flex;align-items:center;gap:var(--s3)}.e .hero .acts .link{font:600 var(--t-body) var(--sans);color:var(--navy);white-space:nowrap}
.e .hero img{width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:var(--radius);display:block}
.e .glance{display:grid;grid-template-columns:1.4fr repeat(3,1fr);padding:var(--s3)}
.e .glance > div{padding:0 var(--s3)}.e .glance > div:first-child{padding-left:0}.e .glance > div + div{border-left:1px solid var(--line)}
.e .glance svg.sp{display:block;width:100%;height:48px;margin-top:var(--s2)}
.e .glance .trend{display:inline-block;margin-left:var(--s1);font:600 var(--t-meta) var(--sans);color:#0E9F6E}
.e .stat .ic{color:var(--muted)}.e .stat b{display:block;margin:var(--s2) 0 4px;font:600 var(--t-stat)/1 var(--sans)}
.e .reco{display:flex;align-items:center;gap:var(--s3);padding:var(--s2)}
.e .reco img{width:220px;aspect-ratio:16/10;object-fit:cover;border-radius:12px;flex:none}
.e .reco .txt{flex:1;min-width:0;display:grid;gap:4px}.e .reco b{font:600 16px/1.3 var(--sans)}.e .reco p{margin:0;color:var(--muted);font-size:var(--t-body)}
.e .rail{display:grid;gap:var(--s3);align-content:start}.e .rail .card{padding:var(--s3)}
.e .rail .head{display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--s2)}
.e .rail .big{font:400 48px/1 var(--serif);margin-bottom:var(--s1)}.e .rail p{margin:0 0 var(--s2);font-size:var(--t-body);color:var(--muted)}
.e .target{width:36px;height:36px;border-radius:50%;display:grid;place-items:center;background:var(--soft);color:var(--accent)}
.e .links{display:grid}.e .links a{display:flex;align-items:center;gap:12px;padding:10px 0;font:500 var(--t-body) var(--sans);color:var(--ink)}
.e .links a + a{border-top:1px solid var(--line)}.e .links .ic{color:var(--muted)}.e .links .go{margin-left:auto;width:14px;height:14px;color:var(--faint)}
.e .eyebrow{font-size:var(--t-label)}.e .bar{display:block;width:100%}.e .stat .meta{white-space:nowrap}
@media (max-width:1280px){.e .layout{grid-template-columns:1fr}.e .rail{grid-template-columns:1fr 1fr}}
@media (max-width:900px){.top{padding:var(--s2)}main.e{padding:0 var(--s2) var(--s4)}.top .search{flex:1;min-width:0;white-space:nowrap;overflow:hidden}.top .search kbd{display:none}
 .e .hero{grid-template-columns:1fr;gap:var(--s3)}.e .hero h1{font-size:44px}.e .glance{grid-template-columns:1fr 1fr 1fr;row-gap:var(--s3)}.e .glance > div:first-child{grid-column:1/-1;padding:0}
 .e .glance > div:nth-child(2){border-left:0;padding-left:0}.e .glance > div{padding:0 12px}.e .stat b{font-size:20px}.e .reco{flex-direction:column;align-items:stretch}.e .reco img{width:100%}.e .rail{grid-template-columns:1fr}}
"""
E=f"""<div class="app">{sidebar(False)}<div><div class="top"><div class="search">{i("search")}Search anything…<kbd>Ctrl K</kbd></div>{TOOLS}</div>
<main class="e"><div class="layout"><div class="col">
<section class="hero"><div><div class="eyebrow">Learn · Develop · Achieve</div><h1>Good morning,<br>Layla.</h1><p>Small steps. Big impact.</p>
<div class="acts"><a class="pill navy">Continue learning {i("arrow")}</a><a class="link">Browse courses</a></div></div><img src="img/desk.jpg" alt=""></section>
<section class="card glance"><div><h2 class="title">Your learning at a glance</h2><span class="meta">Activities this month</span><span class="trend">+12%</span>
<svg class="sp" viewBox="0 0 300 48" preserveAspectRatio="none"><path d="M0 40 C40 37 70 42 110 34 S180 26 210 22 S265 10 300 6" fill="none" stroke="#1E5FA8" stroke-width="2" vector-effect="non-scaling-stroke"/></svg></div>
{''.join(f'<div class="stat">{i(ic)}<b>{n}</b><span class="meta">{t}</span></div>' for ic,n,t in [('book','4','In progress'),('check','8','Completed'),('clock','24h 30m','Learning time')])}</section>
<section><div class="sechead"><h2 class="title">Recommended for you</h2><a>View all</a></div>
<a class="card reco"><img src="img/lead.jpg" alt=""><div class="txt"><b>Leadership for Line Managers</b><p>Lead one-to-ones, give feedback and support your team.</p><span class="meta">1h 20m · 5 activities</span></div><span class="roundbtn dark">{i("arrow")}</span></a></section>
</div>
<aside class="rail"><section class="card"><div class="head"><h2 class="title">Your weekly goal</h2><span class="target">{i("target")}</span></div>
<div class="big">2/4</div><p>Learn on 2 more days to reach your goal.</p><span class="bar"><i style="width:50%"></i></span></section>
<section class="card"><div class="head"><h2 class="title">Quick links</h2></div><nav class="links">{''.join(f'<a>{i(ic)}{t}{i("chevr","ic go")}</a>' for ic,t in [('compass','Browse courses'),('calendar','View calendar'),('award','My certificates'),('help','Need help?')])}</nav></section></aside>
</div></main></div></div>"""

here=os.path.dirname(os.path.abspath(__file__))
for key,title,css,body,note in [('a','Siraj concept A',A_CSS,A,'Concept A: dark sidebar · photo hero · stat strip'),
        ('b','Siraj concept B',B_CSS,B,'Concept B: top navigation · split hero · at a glance'),
        ('c','Siraj concept C',C_CSS,C,'Concept C: light sidebar · serif greeting · progress ring'),
        ('d','Siraj concept D',D_CSS,D,'Concept D: half-screen photo · progress panel'),
        ('e','Siraj concept E',E_CSS,E,'Concept E: concept B with the sidebar')]:
    open(os.path.join(here,f'concept-{key}.html'),'w').write(page(title,css,body,note))
    print('wrote',f'concept-{key}.html')
