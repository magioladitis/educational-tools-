#!/usr/bin/env python3
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
js = (ROOT / 'includes/weekly-timetable-ui.js').read_text(encoding='utf-8')
checks=[]
def check(name, cond): checks.append((name, bool(cond)))

def body(name):
    m=re.search(r'function\s+'+re.escape(name)+r'\s*\([^)]*\)\s*\{', js)
    if not m: return ''
    start=m.end(); depth=1; i=start
    while i < len(js) and depth:
        if js[i]=='{': depth+=1
        elif js[i]=='}': depth-=1
        i+=1
    return js[start:i-1]

for helper in ('refreshVariants','refreshSpecialties','refreshTracks'):
    b=body(helper)
    check(helper+' exists', bool(b))
    check(helper+' has no render side effect', not re.search(r'\brender\s*\(', b))

rg=body('refreshGrades')
check('refreshGrades exists', bool(rg))
check('refreshGrades refreshes tracks', 'refreshTracks();' in rg)
check('refreshGrades renders exactly once', len(re.findall(r'\brender\s*\(', rg)) == 1)
check('refreshTracks delegates specialty refresh', 'refreshSpecialties();' in body('refreshTracks'))
check('refreshTracks delegates variant refresh', 'refreshVariants();' in body('refreshTracks'))

failed=[n for n,p in checks if not p]
for n,p in checks: print(('PASS' if p else 'FAIL')+': '+n)
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
