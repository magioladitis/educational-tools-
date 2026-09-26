#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, tempfile, sys
ROOT=Path(__file__).resolve().parents[1]
src=(ROOT/'includes/personnel-workload-calculations.js').read_text(encoding='utf-8')
FAIL=[]
def check(name,ok):
    print(('  ✔ ' if ok else '  ✘ ')+name)
    if not ok: FAIL.append(name)

def run(mod,scenario):
    with tempfile.NamedTemporaryFile('w',suffix='.js',encoding='utf-8',delete=False) as f:
        f.write(mod); path=f.name
    js=r'''const fs=require('fs'),vm=require('vm');global.window=global;vm.runInThisContext(fs.readFileSync(process.argv[1],'utf8'));vm.runInThisContext(fs.readFileSync(process.argv[2],'utf8'));const s=JSON.parse(fs.readFileSync(0,'utf8'));const r=global.PersonnelWorkloadCalculations.optimizeRemaining(s.slots,s.people,s.person_state,s.slot_state);process.stdout.write(JSON.stringify(r));'''
    p=subprocess.run(['node','-e',js,str(ROOT/'includes'/'specialty-code-normalization.js'),path],input=json.dumps(scenario,ensure_ascii=False),text=True,capture_output=True,cwd=ROOT)
    Path(path).unlink(missing_ok=True)
    if p.returncode: raise RuntimeError(p.stderr)
    return json.loads(p.stdout)

def slot(sid,h,by):
    return {'slot_id':sid,'unit_id':sid,'slot_label':sid,'grade':'Α΄','subject':sid,'capacity_hours':h,'eligible_by_priority':{'A':by.get('A',[]),'SPECIAL':by.get('SPECIAL',[]),'B':by.get('B',[]),'C':by.get('C',[])},'top_priority':next((x for x in ('A','SPECIAL','B','C') if by.get(x)),None)}
# Baseline B-limit scenario.
sc={'slots':{'b1':slot('b1',5,{'B':['ΠΕ78']}),'b2':slot('b2',5,{'B':['ΠΕ78']}),'b3':slot('b3',2,{'B':['ΠΕ78']})},'people':[{'person_id':'b','specialty_code':'ΠΕ78'}],'person_state':{'b':{'remaining_hours':20,'b_assignment_hours':0,'b_remaining_hours':10}},'slot_state':{'b1':{'remaining_hours':5},'b2':{'remaining_hours':5},'b3':{'remaining_hours':2}}}
base=run(src,sc); basecov=sum(x['hours'] for x in base['allocations'])
check('baseline mutation fixture enforces 10h B limit',basecov==10)
needle="b_priority: 'B'"
check('B-priority policy mutation target exists',needle in src)
if needle in src:
    mutant=src.replace(needle,"b_priority: 'X'",1)
    bad=run(mutant,sc); badcov=sum(x['hours'] for x in bad['allocations'])
    check('B-limit mutant is detected by fixture',badcov!=10)

# SPECIAL must outrank B after equal coverage.
sc2={'slots':{'x':slot('x',4,{'SPECIAL':['ΠΕ01'],'B':['ΠΕ02']})},'people':[{'person_id':'sp','specialty_code':'ΠΕ01'},{'person_id':'b','specialty_code':'ΠΕ02'}],'person_state':{'sp':{'remaining_hours':4,'b_assignment_hours':0,'b_remaining_hours':10},'b':{'remaining_hours':4,'b_assignment_hours':0,'b_remaining_hours':10}},'slot_state':{'x':{'remaining_hours':4}}}
base2=run(src,sc2)
check('baseline mutation fixture prefers SPECIAL',base2['allocations'] and base2['allocations'][0]['person_id']=='sp')
# Remove SPECIAL from the canonical optimizer policy. This should make B win the next objective dimension.
needle2="top_priorities: ['A', 'SPECIAL']"
check('SPECIAL policy mutation target exists',needle2 in src)
mutant2=src.replace(needle2,"top_priorities: ['A']",1)
bad2=run(mutant2,sc2)
check('SPECIAL-priority mutant is detected by fixture',bad2['allocations'] and bad2['allocations'][0]['person_id']!='sp')

if FAIL:
    print('\nOptimizer mutation safety: FAIL (%d)'%len(FAIL));sys.exit(1)
print('\nOptimizer mutation safety: PASS')
