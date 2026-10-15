from pathlib import Path
from zipfile import ZipFile
from subprocess import check_output
import json
ROOT=Path(__file__).resolve().parents[1]
ORIGINAL=Path('/mnt/data/ergaleiothiki-3.22.114-FULL.zip')
def load_p(src):
    return json.loads(check_output(['php','-r', '$d=require "'+str(src)+'"; echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);']))
d=load_p(ROOT/'includes/service-timeline-data.php')
old = None
if ORIGINAL.exists():
    with ZipFile(ORIGINAL) as z:
        original_text=z.read('includes/service-timeline-data.php').decode()
    orig_path=ROOT/'.old-test-data.php'
    orig_path.write_text(original_text)
    try: old=load_p(orig_path)
    finally:orig_path.unlink()
assert len(d['events'])==59
checks=0
old_by={e['id']:e for e in old['events']} if old else {};new_by={e['id']:e for e in d['events']}
years=d['history_years']
def count_missing(data):
    gaps=[]
    for e in data['events']:
        linked={s['year'] for s in e.get('historical_sources',[]) if s.get('url')}
        for i,v in enumerate(e.get('history',[])):
            if v and v!='—' and years[i] not in linked:gaps.append((e['id'],years[i]))
    return gaps
old_gaps=count_missing(old) if old else [];new_gaps=count_missing(d)
if old:
    assert len(old_gaps)==34,len(old_gaps);checks+=1
assert new_gaps==[('functional-gaps-secondary-circular','2025-2026')],new_gaps;checks+=1
for eid,e in new_by.items():
    if old:
        previous=old_by[eid]
        for key in ('latest','history','levels','group','note','sources','latest_verified'):
            assert e.get(key)==previous.get(key),(eid,key)
    linked=[s['year'] for s in e.get('historical_sources',[])]
    assert len(set(linked))==len(linked),eid
    assert all(str(x.get('url','')).startswith('https://') for x in e.get('historical_sources',[])),eid
    checks+=1
pe=next(x for x in new_by['primary-transfer-circular']['historical_sources'] if x['year']=='2024-2025')
assert '59828' in pe['url'] and 'defterovathmias' not in pe['url'];checks+=1
assert any(s['year']=='2019-2020' and ('2019-2020' in s['label'] or '2019–2020' in s['label']) for s in new_by['transfer-application-withdrawal']['historical_sources']);checks+=1
assert "'3.22.115'" in (ROOT/'includes/config.php').read_text()
assert "'3.22.115'" in (ROOT/'service-worker.js').read_text(); checks+=1
print(f'{checks} source coverage checks PASS; 33 gaps resolved, 1 pending')
