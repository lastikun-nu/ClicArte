import json,sys,collections
r=json.load(open(sys.argv[1]))
fails=collections.defaultdict(set); over={}
for k,v in r.items():
    if k.startswith('_'): continue
    pg,w,t=k.split('|')
    for c in v['contrast']:
        if not c['ok']: fails[(c['el'],c['text'][:20],c['ratio'],c['need'])].add(f"{pg.split('/')[1]}@{t}")
    if v['overflow'] or v['scrollW']>v['vw']: over[k]=(v['scrollW'],v['bodyScrollW'],v['vw'],v['overflow'][:3])
    if v['errors']: print('ERR',k,v['errors'])
print("== contrast fails:",len(fails))
for k,s in sorted(fails.items(), key=lambda x:x[0][2]): print(k, len(s), sorted(s)[:4])
print("== overflow:",len(over))
for k,v in over.items(): print(k,v)
