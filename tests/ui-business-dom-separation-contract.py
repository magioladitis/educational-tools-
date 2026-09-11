#!/usr/bin/env python3
from pathlib import Path
import re
ROOT=Path(__file__).resolve().parents[1]
assign=(ROOT/'includes/teaching-assignments-ui.js').read_text(encoding='utf-8')
onaseia=(ROOT/'includes/onaseia-ui.js').read_text(encoding='utf-8')
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
def body(txt,name):
    m=re.search(r'function\s+'+re.escape(name)+r'\s*\([^)]*\)\s*\{',txt)
    if not m:return ''
    i=m.end();d=1
    while i<len(txt) and d:
        if txt[i]=='{':d+=1
        elif txt[i]=='}':d-=1
        i+=1
    return txt[m.end():i-1]

check('assignment filter state extracted','function readFilterState()' in assign)
check('assignment row predicate extracted','function rowMatchesFilters(row, state)' in assign)
check('assignment collector extracted','function collectAssignmentResults(state)' in assign)
check('assignment grouping extracted','function groupAssignmentResults(found)' in assign)
check('assignment predicate DOM-free',not re.search(r'document\.|querySelector|getElementById|\.innerHTML|\.textContent|\.classList',body(assign,'rowMatchesFilters')))
check('assignment collector DOM-free',not re.search(r'document\.|querySelector|getElementById|\.innerHTML|\.textContent|\.classList',body(assign,'collectAssignmentResults')))
check('school label grade explicit','function schoolLabel(row, selectedGrade)' in assign and 'gradeFilter.value' not in body(assign,'schoolLabel'))
check('single specialty render listener',assign.count("specialty.addEventListener('input', render)")==1 and "specialty.addEventListener('change', render)" not in assign)

check('Onaseia DOM reader extracted','function readServiceEntries()' in onaseia)
check('Onaseia service calculation extracted','function calculateServiceEntries(entries, warnings)' in onaseia)
calcbody=body(onaseia,'calculateServiceEntries')
check('Onaseia service calculation DOM-free',not re.search(r'document\.|querySelector|getElementById|\.innerHTML|\.textContent|\.classList',calcbody))
check('Onaseia adapter is thin','return calculateServiceEntries(readServiceEntries(), warnings);' in body(onaseia,'calculateService'))
check('Onaseia yearly cap retained','Math.min(10, enteredMonths)' in calcbody)
check('Onaseia points rate retained','const points = months * 1.5;' in calcbody)
check('Onaseia minimum year validation retained','startYear < MIN_SPECIAL_SERVICE_YEAR' in calcbody)

for name,ok in checks: print(('PASS' if ok else 'FAIL')+': '+name)
failed=[n for n,o in checks if not o]
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
