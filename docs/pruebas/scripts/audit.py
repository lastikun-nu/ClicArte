import sys, json, asyncio, pathlib
from playwright.async_api import async_playwright

ROOT = pathlib.Path(sys.argv[1]).resolve()
OUT = pathlib.Path(sys.argv[2]); OUT.mkdir(parents=True, exist_ok=True)
SHOTS = sys.argv[3] if len(sys.argv) > 3 else ""
PAGES = ["comun/index.html","comun/dashboard.html","comun/verTicket.html",
 "alumno/misIncidencias.html","alumno/formularioTicket.html","alumno/misEquipos.html",
 "profe/profesorEquipos.html","profe/profesorIncidencias.html","profe/gestionAlumnos.html","profe/gestionAulas.html",
 "tecnico/tecnicoIncidencias.html","tecnico/tecnicoEquipos.html","tecnico/editarTicket.html"]
WIDTHS = [375, 768, 1366]

JS_CHECK = r"""
() => {
 function parse(c){const m=c.match(/rgba?\(([^)]+)\)/); if(!m) return null; const p=m[1].split(',').map(s=>parseFloat(s)); return {r:p[0],g:p[1],b:p[2],a:p.length>3?p[3]:1};}
 function lum(c){const f=v=>{v/=255;return v<=0.03928?v/12.92:Math.pow((v+0.055)/1.055,2.4)}; return 0.2126*f(c.r)+0.7152*f(c.g)+0.0722*f(c.b);}
 function blend(top,bot){const a=top.a; return {r:top.r*a+bot.r*(1-a),g:top.g*a+bot.g*(1-a),b:top.b*a+bot.b*(1-a),a:1};}
 function bgOf(el){let layers=[];let e=el;while(e){const cs=getComputedStyle(e);const c=parse(cs.backgroundColor);if(c&&c.a>0){layers.push(c); if(c.a>=1) break;} if(cs.backgroundImage!=='none' && e!==document.documentElement){/* image bg */} e=e.parentElement;}
   let base={r:255,g:255,b:255,a:1}; for(let i=layers.length-1;i>=0;i--) base=blend(layers[i],base); return base;}
 function ratio(a,b){const l1=lum(a),l2=lum(b);return (Math.max(l1,l2)+0.05)/(Math.min(l1,l2)+0.05);}
 function desc(el){let s=el.tagName.toLowerCase(); if(el.id) s+='#'+el.id; if(el.classList.length) s+='.'+[...el.classList].join('.'); return s;}
 const res=[]; const seen=new Set();
 document.querySelectorAll('body *').forEach(el=>{
   const txt=[...el.childNodes].filter(n=>n.nodeType===3).map(n=>n.textContent.trim()).join(' ').trim();
   if(!txt) return; const cs=getComputedStyle(el); if(cs.visibility==='hidden'||cs.display==='none') return;
   const r=el.getBoundingClientRect(); if(r.width<2||r.height<2) return;
   if(el.closest('.sr-only,[aria-hidden=true],.pantalla-carga')) return;
   const fg=parse(cs.color); const bg=bgOf(el); const fgb=blend(fg,bg); const rt=ratio(fgb,bg);
   const size=parseFloat(cs.fontSize); const bold=parseInt(cs.fontWeight)>=700; const large=size>=24||(bold&&size>=18.66);
   const need=large?3:4.5; const key=desc(el)+'|'+txt.slice(0,30);
   if(seen.has(key)) return; seen.add(key);
   res.push({el:desc(el),text:txt.slice(0,40),ratio:Math.round(rt*100)/100,need,ok:rt>=need,fg:cs.color,size});
 });
 const vw=document.documentElement.clientWidth;
 const over=[];
 document.querySelectorAll('body *').forEach(el=>{const r=el.getBoundingClientRect(); if(r.width===0) return; if(r.right>vw+1){
   // ignore if inside a scroll container
   let p=el.parentElement, inside=false; while(p&&p!==document.body){const o=getComputedStyle(p).overflowX; if(o==='auto'||o==='scroll'||o==='hidden'){inside=true;break;} p=p.parentElement;}
   if(!inside) over.push({el:desc(el),right:Math.round(r.right)});}});
 return {contrast:res, overflow:over.slice(0,8), scrollW:document.documentElement.scrollWidth, bodyScrollW:document.body.scrollWidth, vw,
   theme:document.documentElement.dataset.theme||'light', title:document.title, h1:[...document.querySelectorAll('h1')].map(h=>h.textContent.trim())};
}
"""

async def main():
    report = {}
    async with async_playwright() as p:
        b = await p.chromium.launch()
        ver = b.version
        for w in WIDTHS:
            for theme in ["light","dark"]:
                ctx = await b.new_context(viewport={"width": w, "height": 800})
                await ctx.route("http*://**", lambda r: r.abort())
                page = await ctx.new_page()
                errors=[]
                page.on("pageerror", lambda e: errors.append(str(e)))
                for pg in PAGES:
                    await page.goto((ROOT/pg).as_uri())
                    await page.wait_for_timeout(150)
                    if theme=="dark":
                        await page.evaluate("document.documentElement.dataset.theme='dark'")
                        await page.wait_for_timeout(600)
                    else:
                        await page.evaluate("document.documentElement.removeAttribute('data-theme')")
                    r = await page.evaluate(JS_CHECK)
                    r["errors"]=list(errors); errors.clear()
                    report[f"{pg}|{w}|{theme}"] = r
                    if SHOTS:
                        name = pg.replace('/','_').replace('.html','')
                        await page.screenshot(path=f"{SHOTS}/{name}_{w}_{theme}.png", full_page=True)
                await ctx.close()
        await b.close()
    report["_browser"]=ver
    (OUT/"report.json").write_text(json.dumps(report, ensure_ascii=False, indent=1))
    print("chromium", ver)
asyncio.run(main())
