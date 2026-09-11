#!/usr/bin/env python3
from pathlib import Path
import re

ROOT=Path(__file__).resolve().parents[1]
ui=(ROOT/'includes/teaching-assignments-ui.js').read_text(encoding='utf-8')
checks=[]
def check(name, cond):
    checks.append((name,bool(cond)))

def body(name):
    m=re.search(r'function\s+'+re.escape(name)+r'\s*\([^)]*\)\s*\{',ui)
    if not m: return ''
    i=m.end(); d=1
    while i<len(ui) and d:
        if ui[i]=='{': d+=1
        elif ui[i]=='}': d-=1
        i+=1
    return ui[m.end():i-1]

check('filter state reader extracted', 'function readFilterState()' in ui)
check('row predicate extracted', 'function rowMatchesFilters(row, state)' in ui)
check('result collection extracted', 'function collectAssignmentResults(state)' in ui)
check('grouping extracted', 'function groupAssignmentResults(found)' in ui)
check('school label gets grade explicitly', 'function schoolLabel(row, selectedGrade)' in ui)
check('school label no hidden grade DOM dependency', 'gradeFilter.value' not in body('schoolLabel'))
check('collector has no DOM access', not re.search(r'document\.|querySelector|getElementById|\.innerHTML|\.textContent|\.classList', body('collectAssignmentResults')))
check('row predicate has no DOM access', not re.search(r'document\.|querySelector|getElementById|\.innerHTML|\.textContent|\.classList', body('rowMatchesFilters')))
check('render delegates collection', 'collectAssignmentResults(state)' in body('render'))
check('render delegates grouping', 'groupAssignmentResults(found)' in body('render'))
check('specialty uses one live listener', ui.count("specialty.addEventListener('input', render)") == 1 and "specialty.addEventListener('change', render)" not in ui)

for name, ok in checks: print(('PASS' if ok else 'FAIL')+': '+name)
failed=[n for n,o in checks if not o]
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
