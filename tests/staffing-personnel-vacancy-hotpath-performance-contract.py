#!/usr/bin/env python3
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
SRC=(ROOT/'includes'/'staffing-simulator-ui.js').read_text(encoding='utf-8')
checks=[]
def check(name, cond): checks.append((name,bool(cond)))

check('personnel input updates only edited row', "updatePersonnelRow(row); markPersonnelDirty();" in SRC)
check('personnel input no longer rescans director constraints', "updatePersonnelRow(row); refreshDirectorRoleConstraints(); markPersonnelDirty();" not in SRC)
check('director constraints refresh only on role select change', "if(event.target.matches('.personnel-role')) refreshDirectorRoleConstraints();" in SRC)
check('section changes use director-only refresh', 'function refreshDirectorPersonnelRows()' in SRC)
check('section changes no longer refresh every personnel row', 'function refreshPersonnelRows(){' not in SRC)
check('director-only refresh filters by director role', "if(role.value==='director')updatePersonnelRow(role.closest('[data-personnel-row]'));" in SRC)
check('vacancy eligibility cache exists', 'const vacancyEligiblePeopleCache={};' in SRC)
check('vacancy eligibility cache is lazy', 'Object.prototype.hasOwnProperty.call(vacancyEligiblePeopleCache,sid)' in SRC)
check('vacancy eligibility cache stores assignment priority', "candidates.push({pid:pid,priority:match.priority});" in SRC)
check('vacancy refresh iterates cached eligible candidates', 'vacancyEligiblePeopleForSlot(sid,slot).forEach(function(candidate)' in SRC)
start=SRC.index('function vacancyEligiblePeopleAvailability(sid,slot,personAssigned,personPriority)')
end=SRC.index('function allocationCollectState()',start)
vacancy_fn=SRC[start:end]
check('vacancy refresh no longer scans all people directly', 'Object.keys(allocationPeopleData||{})' not in vacancy_fn and 'vacancyEligiblePeopleForSlot(sid,slot).forEach(function(candidate)' in vacancy_fn)
check('vacancy row DOM cache exists', 'const vacancyRowCache={};' in SRC)
check('vacancy row cache stores hours/status nodes', "hours:row.querySelector('[data-vacancy-hours]')" in SRC and "status:row.querySelector('[data-vacancy-status]')" in SRC)
check('vacancy row cache stores normalized search text', "search:(row.getAttribute('data-search')||'').toLocaleLowerCase('el-GR')" in SRC)
check('vacancy print nodes cached', "printHours:printRow?printRow.querySelector('[data-print-vacancy-hours]'):null" in SRC)
check('vacancy static wrappers cached', "const vacancyTableWrap=document.getElementById('vacancyTableWrap');" in SRC and "const printVacancyEmpty=document.getElementById('printVacancyEmpty');" in SRC)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+' '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
