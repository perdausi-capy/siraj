# Abstract course cover images (no real photos, no client imagery).
import math, sys
from PIL import Image, ImageDraw, ImageFilter
covers = {
  'hse':      ((14,138,95),  (51,204,255)),
  'conduct':  ((15,63,102),  (58,112,184)),
  'infosec':  ((10,44,72),   (0,230,154)),
  'fire':     ((200,72,40),  (245,166,35)),
  'pm':       ((58,112,184), (51,204,255)),
  'lead':     ((92,60,160),  (31,143,214)),
  'abc':      ((120,40,70),  (233,162,59)),
  'dpe':      ((18,80,110),  (110,199,160)),
  'ar-safety':((14,102,94),  (0,201,140)),
  'ar-service':((84,76,139), (142,158,217)),
}
only = sys.argv[1:]
W,H=1200,600
for name,(a,b) in covers.items():
    if only and name not in only:
        continue
    im=Image.new('RGB',(W,H))
    px=im.load()
    for y in range(H):
        for x in range(W):
            t=(x/W*0.7+y/H*0.3)
            px[x,y]=tuple(int(a[i]+(b[i]-a[i])*t) for i in range(3))
    ov=Image.new('RGBA',(W,H),(0,0,0,0)); d=ImageDraw.Draw(ov)
    seed=sum(map(ord,name))
    for k in range(5):
        r=180+((seed*(k+3))%220); cx=(seed*37*(k+1))%W; cy=(seed*53*(k+2))%H
        d.ellipse([cx-r,cy-r,cx+r,cy+r],fill=(255,255,255,18+k*6))
    ov=ov.filter(ImageFilter.GaussianBlur(4))
    im=Image.alpha_composite(im.convert('RGBA'),ov).convert('RGB')
    im.save(f'img/{name}.png',optimize=True)
print('done')
