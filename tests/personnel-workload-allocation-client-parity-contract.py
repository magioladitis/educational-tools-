#!/usr/bin/env python3
import json, pathlib, subprocess, sys
ROOT=pathlib.Path(__file__).resolve().parents[1]
FAIL=[]
def check(label, ok):
    if ok: print('  ✔ '+label)
    else: FAIL.append(label); print('  ✘ '+label)

module_path=ROOT/'includes/personnel-workload-calculations.js'
ui_path=ROOT/'includes/staffing-simulator-ui.js'
page_path=ROOT/'ypologismos-didaktikon-anagkon.php'
module=module_path.read_text(encoding='utf-8')
ui=ui_path.read_text(encoding='utf-8')
page=page_path.read_text(encoding='utf-8')
check('shared module exports priorityForSlotCode', 'priorityForSlotCode: priorityForSlotCode' in module)
check('shared module exports priorityRank', 'priorityRank: priorityRank' in module)
check('shared module exports bestAssignmentForSlot', 'bestAssignmentForSlot: bestAssignmentForSlot' in module)
check('shared module exports roster slot validator', 'validateRosterSlotAllocations: validateRosterSlotAllocations' in module)
check('staffing UI delegates best-assignment logic to shared module', 'PersonnelWorkloadCalculations.bestAssignmentForSlot' in ui)
check('staffing UI delegates roster validation to shared module', 'PersonnelWorkloadCalculations.validateRosterSlotAllocations' in ui)
check('staffing UI no longer owns candidate sort implementation', 'candidates.sort(function(a,b)' not in ui[ui.find('function allocationBestAssignment'):ui.find('const vacancyEligiblePeopleCache')])
check('staffing page loads shared module before simulator UI', page.find('includes/personnel-workload-calculations.js') >= 0 and page.find('includes/personnel-workload-calculations.js') < page.find('includes/staffing-simulator-ui.js'))

profile={'structures':{'gymnasio':{'general_sections':{'Α΄':1}}}}
units=[]
codes={}
def add_unit(uid, subject, hours, claims, top='A'):
    units.append({'unit_id':uid,'school':'gymnasio','grade':'Α΄','group':'','subject':subject,'assignment_subject':subject,'section_count':1,'hours_per_section':hours,'school_hours':hours,'top_priority':top,'top_codes':[c for c,p in claims if p==top]})
    for code,priority in claims:
        codes.setdefault(code,{'claims':[]})['claims'].append({'unit_id':uid,'priority':priority,'top_priority':top,'is_top_priority':priority==top,'top_code_count':sum(1 for _c,p in claims if p==top)})
add_unit('u_math','Μαθηματικά',2,[('ΠΕ03','A')])
add_unit('u_hist','Ιστορία',2,[('ΠΕ02','A'),('ΠΕ03','B')])
add_unit('u_econ','Οικονομία',2,[('ΠΕ80','A'),('ΠΕ03','B')])
for i in range(1,7):
    add_unit(f'u_b{i}',f'Β μάθημα {i}',2,[('ΠΕ80','A'),('ΠΕ03','B')])
matrix={'profile_id':'phase2_test','school':{'type':'gymnasio'},'readiness':'ready','summary':{'assignment_unit_hours':sum(u['school_hours'] for u in units),'active_regulatory_gap_curriculum_hours':0,'active_dependency_instances':0},'units':units,'codes':codes}
people=[
 {'person_id':'p1','display_name':'Primary+secondary','specialty_code':'ΠΕ03','secondary_specialty_code':'ΠΕ02','role':'teacher','required_teaching_hours':20,'service':{'years':15}},
 {'person_id':'p2','display_name':'Economics','specialty_code':'ΠΕ80','role':'teacher','required_teaching_hours':20,'service':{'years':15}},
 {'person_id':'p3','display_name':'Philologist','specialty_code':'ΠΕ02','role':'teacher','required_teaching_hours':20,'service':{'years':15}},
]
scenarios={
 'valid_primary_secondary':[
   {'person_id':'p1','slot_id':'u_math|section|1','hours':2},
   {'person_id':'p1','slot_id':'u_hist|section|1','hours':2},
   {'person_id':'p2','slot_id':'u_econ|section|1','hours':2},
 ],
 'atomic_partial_invalid':[{'person_id':'p1','slot_id':'u_math|section|1','hours':1}],
 'ineligible_invalid':[{'person_id':'p3','slot_id':'u_math|section|1','hours':2}],
 'unknown_person_slot':[{'person_id':'missing','slot_id':'missing','hours':0}],
 'overallocated_same_slot':[
   {'person_id':'p1','slot_id':'u_math|section|1','hours':2},
   {'person_id':'p1','slot_id':'u_math|section|1','hours':2},
 ],
 'lower_priority_warning':[{'person_id':'p1','slot_id':'u_econ|section|1','hours':2}],
 'b_limit_warning':[{'person_id':'p1','slot_id':f'u_b{i}|section|1','hours':2} for i in range(1,7)],
}

php_code='''<?php
require %s;
$profile=json_decode(%s,true);$matrix=json_decode(%s,true);$people=json_decode(%s,true);$scenarios=json_decode(%s,true);
$slots=personnelWorkloadAllocationSlots($profile,$matrix);
$out=['slots'=>$slots,'best'=>[],'rank'=>[],'scenarios'=>[]];
foreach(['A','SPECIAL','B','C','X',null] as $p){$out['rank'][$p===null?'NULL':$p]=personnelWorkloadPriorityRank($p);}
$bestVectors=[
 ['slot'=>'u_hist|section|1','person'=>$people[0]],
 ['slot'=>'u_econ|section|1','person'=>$people[0]],
 ['slot'=>'u_math|section|1','person'=>$people[2]],
];
foreach($bestVectors as $i=>$v){$out['best'][(string)$i]=personnelWorkloadBestAssignmentForSlot($slots[$v['slot']],$v['person']);}
foreach($scenarios as $name=>$alloc){
 $r=personnelWorkloadRosterSlotPlan($profile,$people,$alloc,null,$matrix);
 $out['scenarios'][$name]=['allocation_rows'=>$r['allocation_rows'],'summary'=>[
  'assigned_slot_hours_total'=>$r['summary']['assigned_slot_hours_total'],
  'unassigned_slot_hours'=>$r['summary']['unassigned_slot_hours'],
  'overallocated_slot_hours'=>$r['summary']['overallocated_slot_hours'],
  'invalid_allocation_row_count'=>$r['summary']['invalid_allocation_row_count'],
  'people_over_b_assignment_limit_count'=>$r['summary']['people_over_b_assignment_limit_count'],
  'b_assignment_hours_over_limit_total'=>$r['summary']['b_assignment_hours_over_limit_total'],
 ]];
}
echo json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % tuple(json.dumps(x,ensure_ascii=False) for x in [str(ROOT/'includes/personnel-workload.php'),json.dumps(profile,ensure_ascii=False),json.dumps(matrix,ensure_ascii=False),json.dumps(people,ensure_ascii=False),json.dumps(scenarios,ensure_ascii=False)])
php=subprocess.run(['php'],input=php_code,text=True,capture_output=True,cwd=ROOT)
if php.returncode!=0:
    print(php.stderr);sys.exit(1)
php_out=json.loads(php.stdout)

node_script='''const fs=require('fs'),vm=require('vm');global.window=global;vm.runInThisContext(fs.readFileSync(%s,'utf8'));vm.runInThisContext(fs.readFileSync(%s,'utf8'));
const W=global.PersonnelWorkloadCalculations,slots=%s,people=%s,scenarios=%s;
const peopleIndex={};people.forEach(p=>peopleIndex[p.person_id]=p);
const out={rank:{},best:[],scenarios:{}};
['A','SPECIAL','B','C','X',null].forEach(p=>out.rank[p===null?'NULL':p]=W.priorityRank(p));
const bestVectors=[['u_hist|section|1',people[0]],['u_econ|section|1',people[0]],['u_math|section|1',people[2]]];
bestVectors.forEach((v)=>out.best.push(W.bestAssignmentForSlot(slots[v[0]],v[1])));
Object.keys(scenarios).forEach(name=>{const r=W.validateRosterSlotAllocations(slots,peopleIndex,scenarios[name]);out.scenarios[name]={allocation_rows:r.allocation_rows,summary:r.summary};});
process.stdout.write(JSON.stringify(out));
''' % (json.dumps(str(ROOT/'includes/specialty-code-normalization.js')),json.dumps(str(module_path)),json.dumps(php_out['slots'],ensure_ascii=False),json.dumps(people,ensure_ascii=False),json.dumps(scenarios,ensure_ascii=False))
node=subprocess.run(['node','-e',node_script],text=True,capture_output=True,cwd=ROOT)
if node.returncode!=0:
    print(node.stderr);sys.exit(1)
js_out=json.loads(node.stdout)
check('priority ranks match PHP', js_out['rank']==php_out['rank'])
check('best-assignment primary/secondary selection matches PHP', js_out['best']==php_out['best'])
for name in scenarios:
    check(name+' allocation rows match PHP', js_out['scenarios'][name]['allocation_rows']==php_out['scenarios'][name]['allocation_rows'])
    check(name+' slot-level summary matches PHP', js_out['scenarios'][name]['summary']==php_out['scenarios'][name]['summary'])

if FAIL:
    print('\nFAIL:',len(FAIL))
    for x in FAIL: print(' - '+x)
    sys.exit(1)
print('\nPersonnel workload allocation client parity: PASS (%d checks)' % (10+len(scenarios)*2))
