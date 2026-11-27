import sys, json, asyncio, pathlib
from playwright.async_api import async_playwright
ROOT = pathlib.Path(sys.argv[1]).resolve(); OUT=sys.argv[2]; W=int(sys.argv[3]) if len(sys.argv)>3 else 1366
SHOT = sys.argv[4] if len(sys.argv)>4 else ""
FOCUS_JS = r"""() => { const el=document.activeElement; if(!el||el===document.body) return null;
 const cs=getComputedStyle(el); const r=el.getBoundingClientRect();
 let d=el.tagName.toLowerCase(); if(el.id) d+='#'+el.id; if(el.className&&typeof el.className==='string') d+='.'+el.className.trim().split(/\s+/).join('.');
 const name=(el.getAttribute('aria-label')||el.textContent||el.value||el.placeholder||'').trim().replace(/\s+/g,' ').slice(0,40);
 const visibleRing=(cs.outlineStyle!=='none'&&parseFloat(cs.outlineWidth)>0)||(cs.boxShadow&&cs.boxShadow!=='none');
 const inView=r.bottom>0&&r.top<innerHeight&&r.right>0&&r.left<innerWidth&&r.width>0;
 return {el:d,name,ring:visibleRing,outline:cs.outlineStyle+' '+cs.outlineWidth+' '+cs.outlineColor,inView,url:location.pathname.split('/').slice(-2).join('/')};}"""
async def tabs(page, until, maxn=40, key="Tab"):
    seq=[]
    for i in range(maxn):
        await page.keyboard.press(key); f=await page.evaluate(FOCUS_JS); seq.append(f)
        if f and until(f): return i+1, seq
    return None, seq
async def main():
    res={}
    async with async_playwright() as p:
        b=await p.chromium.launch(); ctx=await b.new_context(viewport={"width":W,"height":800})
        await ctx.route("http*://**", lambda r: r.abort()); page=await ctx.new_page()
        # Tarea 1: login con teclado
        await page.goto((ROOT/"comun/index.html").as_uri()); await page.wait_for_timeout(200)
        n,seq=await tabs(page, lambda f: f['el'].startswith('input#correo'))
        await page.keyboard.type("alumno@campus.com")
        await page.keyboard.press("Tab"); await page.keyboard.type("1234")
        n2,seq2=await tabs(page, lambda f: 'btn-login' in f['el'])
        if SHOT: await page.screenshot(path=f"{SHOT}/kbd_login_focus_{W}.png")
        await page.keyboard.press("Enter"); await page.wait_for_timeout(3300)
        res['T1_login']={'tabs_hasta_correo':n,'secuencia':seq,'tabs_hasta_boton':n2,'secuencia2':seq2,'url_final':page.url.split('frontend/')[-1]}
        # Tarea 2: crear incidencia con teclado (desde formularioTicket)
        await page.goto((ROOT/"alumno/formularioTicket.html").as_uri()); await page.wait_for_timeout(200)
        n,seq=await tabs(page, lambda f: f['el'].startswith('select#tipo'))
        if SHOT: await page.screenshot(path=f"{SHOT}/kbd_form_focus_{W}.png")
        await page.keyboard.press("ArrowDown")
        n3,seq3=await tabs(page, lambda f: 'btn-submit' in f['el'])
        before=page.url
        await page.keyboard.press("Enter"); await page.wait_for_timeout(400)
        fb=await page.evaluate("""()=>{const a=document.querySelector('[role=alert],.mensaje');const el=document.activeElement;
          return {mensaje:a?a.textContent.trim().slice(0,120):null, foco:el?(el.tagName+'#'+el.id):null, url:location.href.split('frontend/')[-1]}}""")
        if SHOT: await page.screenshot(path=f"{SHOT}/kbd_form_error_{W}.png")
        res['T2_formulario_vacio']={'tabs_hasta_primer_campo':n,'secuencia':seq,'tabs_campo_a_boton':n3,'resultado_envio':fb}
        # Shift+Tab
        sh=[]
        for i in range(3):
            await page.keyboard.press("Shift+Tab"); sh.append(await page.evaluate(FOCUS_JS))
        res['T2_shift_tab']=sh
        # Tarea 2b: mismo formulario usando el enlace "Saltar al contenido"
        await page.goto((ROOT/"alumno/formularioTicket.html").as_uri()); await page.wait_for_timeout(200)
        await page.keyboard.press("Tab"); primero=await page.evaluate(FOCUS_JS)
        skip=None
        if primero and 'skip-link' in primero['el']:
            await page.keyboard.press("Enter"); await page.wait_for_timeout(100)
            skip,_=await tabs(page, lambda f: f['el'].startswith('select#tipo'))
        res['T2b_con_salto']={'primer_foco':primero,'tabs_tras_saltar':skip}
        # Tarea 4: menú móvil con teclado (abrir, recorrer, Escape)
        await page.goto((ROOT/"alumno/misIncidencias.html").as_uri()); await page.wait_for_timeout(200)
        n,seq=await tabs(page, lambda f: 'sidebar-toggle' in f['el'], 10)
        estado0=await page.evaluate("()=>{const b=document.querySelector('.sidebar-toggle');const n=document.querySelector('.sidebar-nav');return b?{expanded:b.getAttribute('aria-expanded'),label:b.getAttribute('aria-label'),menuVisible:n.offsetParent!==null}:null}")
        await page.keyboard.press("Enter"); await page.wait_for_timeout(100)
        estado1=await page.evaluate("()=>{const b=document.querySelector('.sidebar-toggle');const n=document.querySelector('.sidebar-nav');return {expanded:b.getAttribute('aria-expanded'),label:b.getAttribute('aria-label'),menuVisible:n.offsetParent!==null}}")
        if SHOT: await page.screenshot(path=f"{SHOT}/kbd_menu_abierto_{W}.png")
        await page.keyboard.press("Tab"); f1=await page.evaluate(FOCUS_JS)
        await page.keyboard.press("Escape"); await page.wait_for_timeout(100)
        estado2=await page.evaluate("()=>{const b=document.querySelector('.sidebar-toggle');const n=document.querySelector('.sidebar-nav');return {expanded:b.getAttribute('aria-expanded'),menuVisible:n.offsetParent!==null,foco:document.activeElement.className}}")
        res['T4_menu']={'tabs_hasta_boton_menu':n,'inicial':estado0,'tras_enter':estado1,'siguiente_foco':f1,'tras_escape':estado2}
        # Tarea 3: consultar una incidencia desde la tabla
        await page.goto((ROOT/"alumno/misIncidencias.html").as_uri()); await page.wait_for_timeout(200)
        n,seq=await tabs(page, lambda f: 'btn-consultar' in f['el'])
        await page.keyboard.press("Enter"); await page.wait_for_timeout(300)
        res['T3_consultar']={'tabs_hasta_consultar':n,'secuencia':seq,'url_final':page.url.split('frontend/')[-1]}
        await b.close()
    todos=[f for k,v in res.items() if isinstance(v,dict) for kk,vv in v.items() if kk.startswith('secuencia') for f in vv if f]
    res['_resumen']={'focos_registrados':len(todos),'sin_anillo':[f['el'] for f in todos if not f['ring']],'fuera_de_vista':[f['el'] for f in todos if not f['inView']]}
    json.dump(res,open(OUT,'w'),ensure_ascii=False,indent=1)
    for k,v in res.items():
        if isinstance(v,dict): print(k,{kk:vv for kk,vv in v.items() if not kk.startswith('secuencia')})
        else: print(k,v)
asyncio.run(main())
