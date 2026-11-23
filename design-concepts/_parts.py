# Shared pieces for the four concept previews (built into standalone HTML files by build.py).
FONTS = '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">'
SPRITE = '''<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
<linearGradient id="fl" x1=".2" y1="0" x2=".8" y2="1"><stop offset="0" stop-color="#00E69A"/><stop offset=".5" stop-color="#33CCFF"/><stop offset="1" stop-color="#3A70B8"/></linearGradient></defs>
<symbol id="flame" viewBox="0 0 30 36"><path fill="url(#fl)" d="M17 0C10 5 6 11 8.5 16.5c1.3 2.8 4.6 4.3 7.6 5.3 4.4 1.5 6.2 4.6 3.4 9.1-1 1.7-2.6 3.3-4.5 5.1 6.3-2.6 11.4-7.6 12.3-13 .8-4.8-2-8.6-7.7-10.3-3.4-1-6-2.6-6.3-5.4C13.1 5 14.5 2.6 17 0Z"/></symbol>
<symbol id="home" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></symbol>
<symbol id="book" viewBox="0 0 24 24"><path d="M4 19.5V5a2 2 0 0 1 2-2h14v16H6.5A2.5 2.5 0 0 0 4 21.5 2.5 2.5 0 0 0 6.5 24"/><path d="M8 7h8"/></symbol>
<symbol id="play" viewBox="0 0 24 24"><path d="m7 4 13 8-13 8z"/></symbol>
<symbol id="users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18.5 20a6.5 6.5 0 0 0-3-5.5"/></symbol>
<symbol id="compass" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/></symbol>
<symbol id="calendar" viewBox="0 0 24 24"><rect x="3" y="4.5" width="18" height="16" rx="2"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/></symbol>
<symbol id="award" viewBox="0 0 24 24"><circle cx="12" cy="9" r="6"/><path d="m8.5 14 -1.5 8 5-3 5 3-1.5-8"/></symbol>
<symbol id="inbox" viewBox="0 0 24 24"><path d="M3 13h5l1.5 3h5L16 13h5"/><path d="M5.5 5h13L21 13v6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-6z"/></symbol>
<symbol id="chart" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 6-7"/></symbol>
<symbol id="shield" viewBox="0 0 24 24"><path d="M12 3 4.5 6v6c0 4.5 3.2 8 7.5 9 4.3-1 7.5-4.5 7.5-9V6z"/></symbol>
<symbol id="trend" viewBox="0 0 24 24"><path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/></symbol>
<symbol id="file" viewBox="0 0 24 24"><path d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></symbol>
<symbol id="search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></symbol>
<symbol id="sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></symbol>
<symbol id="bell" viewBox="0 0 24 24"><path d="M6 9a6 6 0 0 1 12 0c0 6 2.5 7.5 2.5 7.5h-17S6 15 6 9"/><path d="M10 20a2 2 0 0 0 4 0"/></symbol>
<symbol id="arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
<symbol id="chevl" viewBox="0 0 24 24"><path d="m15 6-6 6 6 6"/></symbol>
<symbol id="chevr" viewBox="0 0 24 24"><path d="m9 6 6 6-6 6"/></symbol>
<symbol id="clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
<symbol id="check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></symbol>
<symbol id="star" viewBox="0 0 24 24"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/></symbol>
<symbol id="target" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></symbol>
<symbol id="help" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6V14M12 17.5h.01"/></symbol>
<symbol id="layers" viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/></symbol>
<symbol id="message" viewBox="0 0 24 24"><path d="M4 5h16v11H8l-4 4z"/></symbol>
</svg>'''
def i(name, cls='ic'):
    return f'<svg class="{cls}"><use href="#{name}"/></svg>'
BASE_CSS = '''
:root{--navy:#0B2E4F;--navy2:#12406A;--ink:#0E1B2A;--muted:#5B6B7C;--faint:#8A98A8;--line:#E4E9F0;--bg:#F3F6FA;--surface:#fff;
--mint:#00C98C;--sky:#2FB8F0;--accent:#1E5FA8;--soft:#EEF3F9;--radius:18px;--serif:"Instrument Serif",Georgia,serif;--sans:"Inter",system-ui,sans-serif;
--shadow:0 1px 2px rgba(14,27,42,.04),0 8px 24px rgba(14,27,42,.06)}
*{box-sizing:border-box}html,body{margin:0}body{font:400 14px/1.5 var(--sans);color:var(--ink);background:var(--bg);-webkit-font-smoothing:antialiased}
a{color:inherit;text-decoration:none}.ic{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex:none}
.flame{width:22px;height:27px}.brand{display:flex;align-items:center;gap:10px}.brand b{font:700 15px/1 var(--sans);letter-spacing:.22em}.brand small{display:block;font:600 7.5px/1 var(--sans);letter-spacing:.42em;margin-top:5px;opacity:.7}
.serif{font-family:var(--serif);font-weight:400;letter-spacing:-.01em}
.eyebrow{font:600 11px/1 var(--sans);letter-spacing:.22em;text-transform:uppercase;color:var(--muted)}
.pill{display:inline-flex;align-items:center;gap:8px;height:42px;padding:0 20px;border-radius:999px;font:600 13.5px var(--sans);border:1px solid transparent;cursor:pointer;white-space:nowrap}
.pill .ic{width:16px;height:16px}.pill.navy{background:var(--navy);color:#fff}.pill.white{background:#fff;color:var(--navy)}.pill.ghost{background:transparent;color:var(--navy);border-color:var(--line)}
.roundbtn{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;border:1px solid var(--line);background:#fff;color:var(--navy);flex:none}.roundbtn .ic{width:15px;height:15px}
.roundbtn.dark{background:var(--navy);color:#fff;border-color:var(--navy)}
.card{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow)}
.bar{height:4px;border-radius:9px;background:#E7EDF4;overflow:hidden}.bar i{display:block;height:100%;border-radius:9px;background:var(--navy2)}
.iconbtn{width:38px;height:38px;border-radius:50%;display:grid;place-items:center;color:var(--muted);position:relative}.iconbtn:hover{background:var(--soft)}
.dot{position:absolute;top:8px;right:9px;width:7px;height:7px;border-radius:50%;background:#E5484D;border:2px solid #fff}
.avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;font:600 12px var(--sans);color:#fff;background:linear-gradient(135deg,#123E66,#1E6A8A)}
.search{display:flex;align-items:center;gap:10px;height:40px;padding:0 14px;border-radius:999px;background:#fff;border:1px solid var(--line);color:var(--faint);font-size:13px}
.search kbd{margin-left:auto;font:600 10px var(--sans);color:var(--muted);background:var(--soft);border-radius:6px;padding:3px 6px}
.ccard{display:block;border-radius:16px;overflow:hidden;background:#fff;border:1px solid var(--line);box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s}
.ccard:hover{transform:translateY(-3px);box-shadow:0 16px 40px rgba(14,27,42,.1)}
.ccard img{display:block;width:100%;height:150px;object-fit:cover}.ccard .body{padding:14px 16px 16px}.ccard b{display:block;font-size:14.5px;margin-bottom:4px}
.ccard small{color:var(--muted);font-size:12.5px}.ccard .row{display:flex;align-items:center;gap:10px;margin-top:12px}.ccard .row .bar{flex:1}.ccard .row em{font:600 12px var(--sans);font-style:normal;color:var(--muted)}
.note{position:fixed;right:16px;bottom:16px;z-index:50;background:#0E1B2A;color:#fff;font:500 12px var(--sans);padding:8px 12px;border-radius:999px;opacity:.85}
@media (max-width:900px){.hide-sm{display:none!important}}
'''
COURSES = [('hse.jpg','Workplace Health & Safety','32 min · 5 activities',60),('infosec.jpg','Information Security Essentials','28 min · 4 activities',40),
           ('conduct.jpg','Code of Conduct & Ethics','35 min · 5 activities',80),('dpe.jpg','Data Protection Essentials','25 min · 4 activities',25)]
def page(title, css, body, note):
    return f'''<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{title}</title>{FONTS}<style>{BASE_CSS}{css}</style></head><body>{SPRITE}{body}<div class="note">{note}</div></body></html>'''
