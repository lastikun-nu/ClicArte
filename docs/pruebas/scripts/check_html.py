import sys, pathlib, collections, json
from bs4 import BeautifulSoup
ROOT=pathlib.Path(sys.argv[1]); rows=[]; titles=collections.Counter()
for f in sorted(ROOT.glob('*/*.html')):
    s=BeautifulSoup(f.read_text(encoding='utf-8'),'lxml'); rel=f.relative_to(ROOT).as_posix(); prob=[]
    css=[l['href'] for l in s.find_all('link',rel='stylesheet') if not l['href'].startswith('http')]
    css_ok=all((f.parent/h).resolve().exists() for h in css) and any(h.endswith('css/main.css') for h in css)
    for t in s.find_all(['script','img']):
        src=t.get('src')
        if src and not src.startswith('http') and not (f.parent/src).resolve().exists(): prob.append('no existe '+src)
    for a in s.find_all('a',href=True):
        h=a['href'].split('#')[0]
        if h and not h.startswith('http') and not (f.parent/h).resolve().exists(): prob.append('enlace roto '+h)
    ids=collections.Counter(t['id'] for t in s.find_all(id=True)); prob+=['id duplicado '+i for i,c in ids.items() if c>1]
    for l in s.find_all('label'):
        if l.get('for') and not s.find(id=l['for']): prob.append('label sin campo '+l['for'])
        if not l.get('for') and not l.find(['input','select','textarea']): prob.append('label sin control')
    hs=[int(h.name[1]) for h in s.find_all(['h1','h2','h3','h4','h5','h6'])]
    if hs.count(1)!=1: prob.append(f'{hs.count(1)} h1')
    for a,b in zip(hs,hs[1:]):
        if b>a+1: prob.append(f'salto de encabezado h{a}->h{b}')
    for th in s.find_all('th'):
        if not th.get('scope'): prob.append('th sin scope')
    for tb in s.find_all('table'):
        if not tb.find('caption'): prob.append('tabla sin caption')
    st=[t for t in s.find_all(style=True)]; prob+=['atributo style']*len(st)
    divs=len(s.find_all('div'))
    title=s.title.string.strip(); titles[title]+=1
    rows.append({'pagina':rel,'title':title,'css':', '.join(css),'css_ok':css_ok,'divs':divs,'problemas':prob,
      'robots':(s.find('meta',attrs={'name':'robots'}) or {}).get('content'),'description':bool(s.find('meta',attrs={'name':'description'}))})
for r in rows:
    r['title_unico']=titles[r['title']]==1
    print(r['pagina'].ljust(32), 'css_ok' if r['css_ok'] else 'CSS!!', 'divs=',r['divs'], 'unico' if r['title_unico'] else 'DUP', r['problemas'] or 'OK')
json.dump(rows,open(sys.argv[2],'w'),ensure_ascii=False,indent=1)
