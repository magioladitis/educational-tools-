#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, textwrap, re
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond): checks.append((name,bool(cond)))

php=textwrap.dedent(r'''<?php
require __DIR__ . '/includes/school-profile-general-education.php';
require __DIR__ . '/includes/school-profile-workload.php';
require __DIR__ . '/includes/teaching-allocation-engine.php';
$complete=schoolProfileBuildEveningGymnasium2026(array(
 'profile_id'=>'contract-evening-gym',
 'general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
 'ethics_by_grade'=>array(
  'Α΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
  'Β΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
  'Γ΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
 ),
));
$pending=schoolProfileBuildEveningGymnasium2026(array(
 'profile_id'=>'contract-evening-gym-pending',
 'general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
));
$incomplete=schoolProfileBuildEveningGymnasium2026(array(
 'profile_id'=>'contract-evening-gym-incomplete',
 'general_sections'=>array('Α΄'=>1,'Β΄'=>0,'Γ΄'=>1),
));
$matrix=schoolProfileWorkloadMatrix($complete);
$pendingMatrix=schoolProfileWorkloadMatrix($pending);
$people=array(array(
 'person_id'=>'pe03','display_name'=>'Μαθηματικός','specialty_code'=>'ΠΕ03',
 'required_teaching_hours'=>20,'role'=>'teacher','assigned_external_hours'=>0,
));
$proposal=teachingAllocationEngineProposal($complete,$people);
$religionPeople=array(array(
 'person_id'=>'pe01','display_name'=>'Θεολόγος','specialty_code'=>'ΠΕ01',
 'required_teaching_hours'=>3,'role'=>'teacher','assigned_external_hours'=>0,
));
$pendingProposal=teachingAllocationEngineProposal($pending,$religionPeople);
echo json_encode(array(
 'complete'=>$complete,
 'complete_readiness'=>schoolProfileGeneralEducationReadiness($complete),
 'incomplete_readiness'=>schoolProfileGeneralEducationReadiness($incomplete),
 'matrix'=>$matrix,
 'pending_matrix'=>$pendingMatrix,
 'proposal'=>$proposal,
 'pending_proposal'=>$pendingProposal,
),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''').replace("__DIR__ . '/includes/", "'"+str(ROOT).replace("'","\\'")+"/includes/")
p=subprocess.run(['php'],cwd=ROOT,text=True,input=php,capture_output=True)
if p.returncode:
    print(p.stderr); raise SystemExit(p.returncode)
data=json.loads(p.stdout)

c=data['complete']
check('builder activates evening gymnasium structure', 'esperino_gymnasio' in c['structures'])
check('builder has no foreign-language choice dependency', not c['structures']['esperino_gymnasio'].get('choice_option_sections'))
check('builder has no day-gym technology split dependency', not c['structures']['esperino_gymnasio'].get('extra_course_sections'))
check('complete structural readiness', data['complete_readiness']['ready'])
check('missing one grade section blocks readiness', not data['incomplete_readiness']['ready'] and any('esperino_gymnasio:Β΄:general_sections_required' in x for x in data['incomplete_readiness']['issues']))
summary=data['matrix']['summary']
check('one section per grade realizes exact 74 weekly hours', summary['assignment_unit_hours']==74)
check('all 74 hours have assignment mapping', summary['uncovered_unit_hours']==0)
check('complete Ethics inputs leave no active dependencies', summary['active_dependency_instances']==0)
check('no regulatory gaps in evening gymnasium', summary['active_regulatory_gap_instances']==0)
pending=data['pending_matrix']['summary']
check('without Ethics inputs Religion remains in baseline 74 hours', pending['assignment_unit_hours']==74)
check('without Ethics inputs only Ethics dependencies remain active', pending['active_dependency_instances']==3)
pending_units=data['pending_matrix']['units']
pending_religion=[u for u in pending_units if u.get('subject')=='Θρησκευτικά']
check('Religion remains assignment-ready without Ethics inputs', len(pending_religion)==3 and all(u.get('school_hours')==1 and 'ΠΕ01' in u.get('top_codes',[]) for u in pending_religion))
units=data['matrix']['units']
def find(instance):
    return next((u for u in units if u.get('instance_id')==instance),None)
for grade in ['Α΄','Β΄','Γ΄']:
    u=find('egym.mathimatika@'+grade)
    check('Math '+grade+' is 4 hours with PE03 top assignment', bool(u and u['school_hours']==4 and 'ΠΕ03' in u.get('top_codes',[])))
check('Evening A total course pattern includes 3-hour Language Teaching', bool(find('egym.glosiki@Α΄') and find('egym.glosiki@Α΄')['school_hours']==3))
check('Evening B KPA is 1 hour', bool(find('egym.kpa@Β΄') and find('egym.kpa@Β΄')['school_hours']==1))
check('Evening C KPA is 2 hours', bool(find('egym.kpa@Γ΄') and find('egym.kpa@Γ΄')['school_hours']==2))
prop=data['proposal']
check('allocation engine accepts evening gymnasium profile', prop['status']=='ok')
check('allocation engine fills PE03 full 20-hour capacity', prop['summary']['auto_covered_hours']==20)
check('allocation engine preserves all 12 Math hours as A assignment', prop['summary']['priority_hours']['A']==12)
check('automatic B hours respect 10-hour cap', prop['summary']['priority_hours']['B']<=10)
rprop=data['pending_proposal']
religion_alloc=[a for a in rprop.get('proposed_allocations',[]) if a.get('subject')=='Θρησκευτικά']
check('PE01 receives Religion even before Ethics inputs', rprop['status']=='ok' and sum(a['hours'] for a in religion_alloc)==3 and all(a['person_id']=='pe01' for a in religion_alloc))

# Render contract: active school type, compact profile fields, no day-only input dependency.
post={
 'staffing_action':'profile','school_type':'esperino_gymnasio','school_name':'Contract Evening Gym',
 'gym_general_a':1,'gym_general_b':1,'gym_general_c':1,
 'ethics_a_exempt':0,'ethics_a_timely':'1','ethics_b_exempt':0,'ethics_b_timely':'1','ethics_c_exempt':0,'ethics_c_timely':'1'
}
payload=json.dumps(post,ensure_ascii=False)
render='''<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode(%s,true); include %s;''' % (json.dumps(payload,ensure_ascii=False),json.dumps(str(ROOT/'ypologismos-didaktikon-anagkon.php')))
r=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=render,capture_output=True)
if r.returncode:
    print(r.stderr); raise SystemExit(r.returncode)
out=r.stdout
check('Evening Gymnasium is selectable, not placeholder', '<option value="esperino_gymnasio" selected>Εσπερινό Γυμνάσιο</option>' in out and '<option value="esperino_gymnasio" disabled>' not in out)
check('result sidebar names Evening Gymnasium', '<span>Δομή</span><strong>Εσπερινό Γυμνάσιο</strong>' in out)
check('render reports 74 assigned hours', '<strong>74</strong><span>ώρες με αντιστοιχισμένη ανάθεση</span>' in out)
check('foreign-language block server-hidden for Evening Gymnasium', re.search(r'id="gymLanguageGroupsSection"[^>]*\shidden',out) is not None)
check('technology split block server-hidden for Evening Gymnasium', re.search(r'id="technologyInformaticsPanel"[^>]*\shidden',out) is not None)
check('source card identifies FEK 2106 for Evening Gymnasium', 'ΦΕΚ Β΄ 2106/2026 — Ημερήσιο ΓΕΛ / Εσπερινό Γυμνάσιο' in out)

# End-to-end automatic allocation through the public page pipeline.
auto=dict(post)
auto.update({
 'staffing_action':'allocation_auto','active_panel':'allocation',
 'personnel_person_id':['pe03'],'personnel_display_name':['Μαθηματικός'],'personnel_specialty_code':['ΠΕ03'],
 'personnel_secondary_specialty_code':[''],'personnel_required_teaching_hours':[20],
 'personnel_service_years':[0],'personnel_service_months':[0],'personnel_service_days':[0],
 'personnel_role':['teacher'],'personnel_assigned_external_hours':[0],'personnel_hours_branch':[''],
 'allocation_person_id':[],'allocation_slot_id':[],'allocation_hours':[]
})
autopayload=json.dumps(auto,ensure_ascii=False)
autorender='''<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode(%s,true); include %s;''' % (json.dumps(autopayload,ensure_ascii=False),json.dumps(str(ROOT/'ypologismos-didaktikon-anagkon.php')))
ar=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=autorender,capture_output=True)
if ar.returncode:
    print(ar.stderr); raise SystemExit(ar.returncode)
aout=ar.stdout
check('public allocation_auto works for Evening Gymnasium', 'Η αυτόματη πρόταση δημιουργήθηκε.' in aout)
check('public allocation_auto assigns PE03 twenty hours', re.search(r'<strong data-allocation-assigned>20</strong>',aout) is not None)
check('public allocation rows use evening course ids', 'egym.mathimatika@Α΄|whole|section|1' in aout)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
