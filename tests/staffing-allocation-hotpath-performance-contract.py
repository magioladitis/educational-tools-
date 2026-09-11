from pathlib import Path
import re, sys

root=Path(__file__).resolve().parents[1]
js=(root/'includes/staffing-simulator-ui.js').read_text()
checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))
    print(('PASS' if cond else 'FAIL')+' '+name)

check('allocation hours input is frame-scheduled', "if(event.target.matches('.allocation-hours')) scheduleAllocationSummary();" in js)
check('allocation hours input no longer recomputes synchronously', "if(event.target.matches('.allocation-hours')) updateAllocationSummary();" not in js)
check('scheduler uses requestAnimationFrame', "window.requestAnimationFrame(run)" in js)
check('scheduler deduplicates pending frame', "if(allocationSummaryFrame) return;" in js)
check('scheduler resets frame before recompute', "allocationSummaryFrame=0; updateAllocationSummary();" in js)
check('allocation total capacity is cached', "const allocationTotalCapacity=Object.keys(allocationSlotsData||{}).reduce" in js)
check('coverage uses cached total capacity', "state.basicAssigned/allocationTotalCapacity" in js)
check('allocation summary DOM nodes are cached', "const allocationSummaryEls={" in js)
check('vacancy summary DOM nodes are cached', "const vacancySummaryEls={" in js)
check('person summary lookup cache exists', "const allocationPersonSummaryCache={};" in js)
check('person summary cache is populated lazily', "allocationPersonSummaryCache[pid]=summary" in js)

m=re.search(r'function updateAllocationSummary\(\)\{(.*?)\n  \}\n  function bindAllocationRow', js, re.S)
body=m.group(1) if m else ''
check('updateAllocationSummary does not rescan allocation metric nodes', "document.querySelector('[data-allocation-assigned]')" not in body)
check('updateAllocationSummary does not recompute total slot capacity', "Object.keys(allocationSlotsData||{}).forEach" not in body)
check('updateAllocationSummary does not globally query each person summary every refresh', "document.querySelector('[data-allocation-person-summary='" not in body)

failed=sum(not ok for _,ok in checks)
print(f'RESULT {len(checks)-failed} PASS / {failed} FAIL')
sys.exit(1 if failed else 0)
