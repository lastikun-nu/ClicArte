# Reflujo a 320 px, nombres accesibles (árbol de accesibilidad de Chromium) y teclado en gestión de incidencias.
import sys, json, asyncio, pathlib
from playwright.async_api import async_playwright
ROOT=pathlib.Path(sys.argv[1]).resolve(); OUT=sys.argv[2]
PAGES=["comun/index.html","comun/dashboard.html","comun/verTicket.html","alumno/misIncidencias.html","alumno/formularioTicket.html","alumno/misEquipos.html","profe/profesorEquipos.html","profe/profesorIncidencias.html","profe/gestionAlumnos.html","profe/gestionAulas.html","tecnico/tecnicoIncidencias.html","tecnico/tecnicoEquipos.html","tecnico/editarTicket.html"]
ROLES={'button','link','textbox','combobox','searchbox','checkbox','radio','region','navigation','main','banner','complementary','table','DisclosureTriangle'}
async def run():
    res={'reflow320':{},'nombres':{},'landmarks':{}}
    async with async_playwright() as p:
        b=await p.chromium.launch()
        ctx=await b.new_context(viewport={'width':320,'height':640}); await ctx.route("http*://**", lambda r: r.abort()); pg=await ctx.new_page()
        for x in PAGES:
            await pg.goto((ROOT/x).as_uri()); await pg.wait_for_timeout(150)
            res['reflow320'][x]=await pg.evaluate("document.documentElement.scrollWidth")
        await ctx.close()
        ctx=await b.new_context(viewport={'width':1366,'height':800}); await ctx.route("http*://**", lambda r: r.abort()); pg=await ctx.new_page()
        cdp=await ctx.new_cdp_session(pg)
        for x in PAGES:
            await pg.goto((ROOT/x).as_uri()); await pg.wait_for_timeout(200)
            tree=(await cdp.send('Accessibility.getFullAXTree'))['nodes']
            sinNombre=[]; total=0; lm=[]
            for n in tree:
                if n.get('ignored'): continue
                role=(n.get('role') or {}).get('value'); name=((n.get('name') or {}).get('value') or '').strip()
                if role in ('button','link','textbox','combobox','searchbox','ComboBoxSelect','DisclosureTriangle'):
                    total+=1
                    if not name: sinNombre.append(role)
                if role in ('navigation','main','banner','complementary','region','contentinfo'): lm.append(f"{role}:{name}" if name else role)
            res['nombres'][x]={'controles':total,'sin_nombre':sinNombre}; res['landmarks'][x]=lm
        # teclado en gestión de incidencias (técnico)
        await pg.goto((ROOT/"tecnico/tecnicoIncidencias.html").as_uri()); await pg.wait_for_timeout(200)
        seq=[]
        for i in range(30):
            await pg.keyboard.press('Tab')
            f=await pg.evaluate("()=>{const e=document.activeElement;const c=getComputedStyle(e);return {t:(e.className||e.tagName)+'',n:(e.getAttribute('aria-label')||e.textContent||'').trim().slice(0,30),ring:c.outlineStyle!=='none'}}")
            seq.append(f)
            if 'btn-consultar' in f['t']: break
        res['teclado_gestion']={'tabs_hasta_gestionar':len(seq) if 'btn-consultar' in seq[-1]['t'] else None,'sin_anillo':[s['t'] for s in seq if not s['ring']],'secuencia':[s['n'] or s['t'] for s in seq]}
        await b.close()
    json.dump(res,open(OUT,'w'),ensure_ascii=False,indent=1)
    print('reflow320',res['reflow320']); 
    for k,v in res['nombres'].items(): print(k,v,res['landmarks'][k])
    print({k:v for k,v in res['teclado_gestion'].items()})
asyncio.run(run())
