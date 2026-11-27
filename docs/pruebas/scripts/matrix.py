# Matriz de tareas: 3 anchos x 2 temas, mismas 4 tareas en cada combinación (Chromium).
import sys, json, asyncio, pathlib
from playwright.async_api import async_playwright
ROOT=pathlib.Path(sys.argv[1]).resolve(); OUT=sys.argv[2]; SHOT=sys.argv[3] if len(sys.argv)>3 else ""
OVER="()=>({over:document.documentElement.scrollWidth>document.documentElement.clientWidth, sw:document.documentElement.scrollWidth, vw:document.documentElement.clientWidth})"
async def run():
    rows=[]
    async with async_playwright() as p:
        b=await p.chromium.launch()
        for w in [375,768,1366]:
            for theme in ['light','dark']:
                ctx=await b.new_context(viewport={"width":w,"height":800}); await ctx.route("http*://**", lambda r: r.abort())
                pg=await ctx.new_page(); errs=[]; pg.on("pageerror", lambda e: errs.append(str(e)))
                r={'ancho':w,'tema':theme}
                # T1 iniciar sesión (tema elegido con el botón, como haría una persona)
                await pg.goto((ROOT/"comun/index.html").as_uri()); await pg.wait_for_timeout(150)
                if theme=='dark': await pg.click('#theme-toggle'); await pg.wait_for_timeout(400)
                await pg.fill('#correo','alumno@campus.com'); await pg.fill('#pass','1234')
                await pg.click('button[data-rol=alumno]'); await pg.wait_for_timeout(3200)
                r['T1_login']= 'misIncidencias' in pg.url
                r['tema_tras_navegar']=await pg.evaluate("document.documentElement.dataset.theme||'light'")
                o1=await pg.evaluate(OVER)
                # T2 navegar entre secciones con el menú
                tog=await pg.query_selector('.sidebar-toggle')
                if w<=760: await tog.click(); await pg.wait_for_timeout(150)
                await pg.click('.sidebar-nav a[href$="misEquipos.html"]'); await pg.wait_for_timeout(300)
                r['T2_navegar']='misEquipos' in pg.url and await pg.evaluate("!!document.querySelector('.sidebar-nav a.activo[aria-current=page][href$=\"misEquipos.html\"]')")
                o2=await pg.evaluate(OVER)
                # T3 formulario: vacío -> error; completo -> confirmación
                await pg.goto((ROOT/"alumno/formularioTicket.html").as_uri()); await pg.wait_for_timeout(200)
                await pg.click('.btn-submit'); await pg.wait_for_timeout(150)
                err=await pg.evaluate("()=>{const m=document.querySelector('.mensaje.error');return !!m && document.activeElement.id==='equipo'}")
                await pg.select_option('#equipo', index=1); await pg.fill('#descripcion','El monitor no enciende')
                await pg.click('.btn-submit'); await pg.wait_for_timeout(150)
                ok=await pg.evaluate("!!document.querySelector('.mensaje.success')")
                r['T3_formulario']= err and ok
                if SHOT: await pg.screenshot(path=f"{SHOT}/matriz_formulario_{w}_{theme}.png", full_page=True)
                o3=await pg.evaluate(OVER)
                # T4 consultar tabla: la tabla se lee entera (scroll interno) y el enlace lleva al detalle
                await pg.goto((ROOT/"alumno/misIncidencias.html").as_uri()); await pg.wait_for_timeout(200)
                t=await pg.evaluate("()=>{const c=document.querySelector('.table-container');return {cap:!!document.querySelector('caption'),scope:document.querySelectorAll('th[scope=col]').length,scrollInterno:c.scrollWidth>c.clientWidth}}")
                if SHOT: await pg.screenshot(path=f"{SHOT}/matriz_tabla_{w}_{theme}.png", full_page=True)
                o4=await pg.evaluate(OVER)
                await pg.click('.btn-consultar'); await pg.wait_for_timeout(300)
                r['T4_tabla']='verTicket' in pg.url and t['cap'] and t['scope']>0
                r['tabla_scroll_interno']=t['scrollInterno']
                r['desbordamiento_pagina']=any(o['over'] for o in [o1,o2,o3,o4])
                # persistencia del tema tras recargar
                await pg.reload(); await pg.wait_for_timeout(200)
                r['tema_tras_recargar']=await pg.evaluate("document.documentElement.dataset.theme||'light'")
                r['errores_js']=errs
                rows.append(r); await ctx.close()
        await b.close()
    json.dump(rows,open(OUT,'w'),ensure_ascii=False,indent=1)
    for r in rows: print(r)
asyncio.run(run())
