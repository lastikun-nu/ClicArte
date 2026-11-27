# Lee del motor de Chromium (protocolo DevTools, CSS.getMatchedStylesForNode) qué declaraciones
# se aplican a un elemento y cuáles quedan anuladas: es la misma información del panel Styles.
import sys, json, asyncio, pathlib
from playwright.async_api import async_playwright
async def matched(page, cdp, selector, props, pseudo=None):
    doc=await cdp.send('DOM.getDocument',{'depth':-1})
    nid=(await cdp.send('DOM.querySelector',{'nodeId':doc['root']['nodeId'],'selector':selector}))['nodeId']
    if pseudo: await cdp.send('CSS.forcePseudoState',{'nodeId':nid,'forcedPseudoClasses':pseudo})
    m=await cdp.send('CSS.getMatchedStylesForNode',{'nodeId':nid})
    decl=[]
    if m.get('inlineStyle'):
        for p in m['inlineStyle']['cssProperties']:
            if p['name'] in props and p.get('text'): decl.append({'origen':'style="" (inline)','selector':'element.style','prop':p['name'],'valor':p['value']})
    for rm in reversed(m['matchedCSSRules']):
        r=rm['rule']
        if r['origin']!='regular': continue
        sel=r['selectorList']['text']; src=r.get('styleSheetId')
        line=r['style'].get('range',{}).get('startLine')
        for p in reversed(r['style']['cssProperties']):  # dentro de una regla gana la última
            if p['name'] in props and p.get('text') is not None and 'range' in p:
                decl.append({'origen':f"línea {p['range']['startLine']+1}",'selector':sel,'prop':p['name'],'valor':p['value']})
    await page.wait_for_timeout(450)
    want=set(props)|{'padding-top','padding-right','padding-bottom','padding-left','background-color'}
    comp={c['name']:c['value'] for c in (await cdp.send('CSS.getComputedStyleForNode',{'nodeId':nid}))['computedStyle'] if c['name'] in want and (c['name'] in props or c['name'].startswith('padding') and 'padding' in props or c['name']=='background-color' and 'background' in props)}
    seen=set()
    for d in decl:   # la primera declaración (más prioritaria) de cada propiedad gana; el resto, tachada
        d['estado']='aplicada' if d['prop'] not in seen else 'tachada'; seen.add(d['prop'])
    return {'elemento':selector,'pseudo':pseudo,'declaraciones':decl,'calculado':comp}
async def main():
    root=pathlib.Path(sys.argv[1]).resolve(); exp=pathlib.Path(sys.argv[2]).resolve(); out={}
    async with async_playwright() as p:
        b=await p.chromium.launch(); ctx=await b.new_context(viewport={'width':1366,'height':800}); await ctx.route("http*://**", lambda r: r.abort())
        pg=await ctx.new_page(); cdp=await ctx.new_cdp_session(pg); await cdp.send('DOM.enable'); await cdp.send('CSS.enable')
        if exp.exists():
            await pg.goto(exp.as_uri()); await pg.wait_for_timeout(200)
            out['b1']=await matched(pg,cdp,'#b1',['background','color','padding'])
            out['b2']=await matched(pg,cdp,'#b2',['background','color','padding'])
            await pg.screenshot(path=sys.argv[4]+'/ce2b_experimento.png')
        await pg.goto((root/'alumno/formularioTicket.html').as_uri()); await pg.wait_for_timeout(200)
        out['btn_submit']=await matched(pg,cdp,'.btn-submit',['padding'])
        await pg.goto((root/'alumno/misIncidencias.html').as_uri()); await pg.wait_for_timeout(200)
        out['button_hover']=await matched(pg,cdp,'a.button',['background-color'],['hover'])
        await pg.evaluate("document.documentElement.dataset.theme='dark'")
        await pg.goto((root/'alumno/formularioTicket.html').as_uri()); await pg.wait_for_timeout(100)
        await pg.evaluate("document.documentElement.dataset.theme='dark'"); await pg.wait_for_timeout(500)
        out['textarea_oscuro']=await matched(pg,cdp,'html',['--color-surface','--color-ink'])
        out['textarea_oscuro_calc']=await pg.evaluate("(()=>{const c=getComputedStyle(document.querySelector('#descripcion'));return {background:c.backgroundColor,color:c.color}})()")
        await b.close()
    json.dump(out,open(sys.argv[3],'w'),ensure_ascii=False,indent=1)
    for k,v in out.items():
        print('==',k)
        if isinstance(v,dict) and 'declaraciones' in v:
            for d in v['declaraciones']: print('  ',d['estado'].ljust(9),d['selector'][:45].ljust(46),d['prop'],d['valor'],d['origen'])
            print('   calculado:',v['calculado'])
        else: print('  ',v)
asyncio.run(main())
