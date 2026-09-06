#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, textwrap, re
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond): checks.append((name,bool(cond)))

def make_php(period='Α΄ τετράμηνο', ethics=True):
    ethics_block = """
 'ethics_by_grade'=>array(
  'Α΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
  'Β΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
  'Γ΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
 ),""" if ethics else ""
    return textwrap.dedent(r'''<?php
require __DIR__ . '/includes/school-profile-general-education.php';
require __DIR__ . '/includes/school-profile-workload.php';
require __DIR__ . '/includes/teaching-allocation-engine.php';
$complete=schoolProfileBuildEveningGel2026(array(
 'profile_id'=>'contract-evening-gel',
 'general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
 'orientation_sections'=>array(
   'Β΄'=>array('humanities'=>1,'science'=>1),
   'Γ΄'=>array('humanities'=>1,'science_health'=>1,'economics_it'=>1),
 ),
 'grade_c_science_health_field_groups'=>array('Μαθηματικά'=>1,'Βιολογία'=>0),
 'grade_c_conditional_groups'=>array('Μαθηματικά'=>1,'Ιστορία'=>2),
 'grade_b_period'=>%s,
 %s
));
$matrix=schoolProfileWorkloadMatrix($complete);
$people=array(array(
 'person_id'=>'pe03','display_name'=>'Μαθηματικός','specialty_code'=>'ΠΕ03',
 'required_teaching_hours'=>20,'role'=>'teacher','assigned_external_hours'=>0,
));
$proposal=teachingAllocationEngineProposal($complete,$people);
echo json_encode(array(
 'profile'=>$complete,
 'readiness'=>schoolProfileGeneralEducationReadiness($complete),
 'matrix'=>$matrix,
 'proposal'=>$proposal,
),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''') % (json.dumps(period,ensure_ascii=False), ethics_block)

php=make_php().replace("__DIR__ . '/includes/", "'"+str(ROOT).replace("'","\\'")+"/includes/")
p=subprocess.run(['php'],cwd=ROOT,text=True,input=php,capture_output=True)
if p.returncode:
    print(p.stderr); raise SystemExit(p.returncode)
data=json.loads(p.stdout)

profile=data['profile']
summary=data['matrix']['summary']
units=data['matrix']['units']

def find(instance):
    return next((u for u in units if u.get('instance_id')==instance),None)

check('builder activates Evening GEL structure', 'esperino_gel' in profile['structures'])
check('complete structural readiness', data['readiness']['ready'])
check('selected B semester stored in profile', profile['structures']['esperino_gel']['period_selection']['Β΄']=='Α΄ τετράμηνο')
check('one representative complete profile realizes 118 staffing hours', summary['assignment_unit_hours']==118)
check('all 118 hours have assignment mapping', summary['uncovered_unit_hours']==0)
check('complete Ethics inputs leave no active dependencies', summary['active_dependency_instances']==0)
check('no regulatory gaps in Evening GEL', summary['active_regulatory_gap_instances']==0)
check('B Chemistry is 1 hour in first semester', bool(find('egel.general.ximeia@Β΄') and find('egel.general.ximeia@Β΄')['school_hours']==1 and find('egel.general.ximeia@Β΄').get('selected_period')=='Α΄ τετράμηνο'))
check('B Biology is 2 hours in first semester', bool(find('egel.general.viologia@Β΄') and find('egel.general.viologia@Β΄')['school_hours']==2))
check('B Humanities orientation uses its own section count', bool(find('egel.b.humanities.archaia@Β΄') and find('egel.b.humanities.archaia@Β΄')['school_hours']==3))
check('B Science orientation uses its own section count', bool(find('egel.b.science.mathimatika@Β΄') and find('egel.b.science.mathimatika@Β΄')['school_hours']==3))
check('C Humanities orientation is 6 hours per subject group', bool(find('egel.c.humanities.archaia@Γ΄') and find('egel.c.humanities.archaia@Γ΄')['school_hours']==6))
check('C Science-Health orientation is 6 hours per subject group', bool(find('egel.c.health.fysiki@Γ΄') and find('egel.c.health.fysiki@Γ΄')['school_hours']==6))
check('C Economics-IT orientation is 6 hours per subject group', bool(find('egel.c.econ.pliroforiki@Γ΄') and find('egel.c.econ.pliroforiki@Γ΄')['school_hours']==6))
check('C Science-Health Math/Biology choice resolves explicitly', bool(find('egel.c.health.mathimatika@Γ΄') and find('egel.c.health.mathimatika@Γ΄')['school_hours']==6 and not find('egel.c.health.viologia@Γ΄')))
check('C conditional Mathematics applies only to Humanities group', bool(find('egel.general.mathimatika_conditional@Γ΄') and find('egel.general.mathimatika_conditional@Γ΄')['school_hours']==1))
check('C conditional History applies to two non-Humanities groups', bool(find('egel.general.istoria@Γ΄') and find('egel.general.istoria@Γ΄')['school_hours']==2))
check('allocation engine accepts Evening GEL profile', data['proposal']['status']=='ok')
check('allocation engine emits Evening GEL course units', any(str(x.get('slot_id','')).startswith('egel.') for x in data['proposal'].get('proposed_allocations',[])))

# Second semester flips Chemistry/Biology but keeps the same total workload.
php2=make_php('Β΄ τετράμηνο').replace("__DIR__ . '/includes/", "'"+str(ROOT).replace("'","\\'")+"/includes/")
p2=subprocess.run(['php'],cwd=ROOT,text=True,input=php2,capture_output=True)
if p2.returncode:
    print(p2.stderr); raise SystemExit(p2.returncode)
d2=json.loads(p2.stdout)
u2=d2['matrix']['units']
def find2(instance): return next((u for u in u2 if u.get('instance_id')==instance),None)
check('second semester keeps total workload at 118 hours', d2['matrix']['summary']['assignment_unit_hours']==118)
check('B Chemistry becomes 2 hours in second semester', bool(find2('egel.general.ximeia@Β΄') and find2('egel.general.ximeia@Β΄')['school_hours']==2))
check('B Biology becomes 1 hour in second semester', bool(find2('egel.general.viologia@Β΄') and find2('egel.general.viologia@Β΄')['school_hours']==1))

# Missing Ethics inputs leaves only religion/Ethics unresolved; periodic hours remain resolved.
php3=make_php('Α΄ τετράμηνο', ethics=False).replace("__DIR__ . '/includes/", "'"+str(ROOT).replace("'","\\'")+"/includes/")
p3=subprocess.run(['php'],cwd=ROOT,text=True,input=php3,capture_output=True)
if p3.returncode:
    print(p3.stderr); raise SystemExit(p3.returncode)
d3=json.loads(p3.stdout)
check('without Ethics inputs Religion remains in baseline 118 hours', d3['matrix']['summary']['assignment_unit_hours']==118)
check('without Ethics inputs only Ethics dependencies remain active', d3['matrix']['summary']['active_dependency_instances']==3)

# Public page render contract.
post={
 'staffing_action':'profile','school_type':'esperino_gel','school_name':'Contract Evening GEL',
 'gel_general_a':1,'gel_general_b':1,'gel_general_c':1,
 'gel_b_hum':1,'gel_b_sci':1,
 'gel_c_hum':1,'gel_c_scihealth':1,'gel_c_econit':1,
 'gel_c_field_math':1,'gel_c_field_bio':0,
 'gel_c_cond_math':1,'gel_c_cond_history':2,
 'egel_b_period':'Α΄ τετράμηνο',
 'ethics_a_exempt':0,'ethics_a_timely':'1','ethics_b_exempt':0,'ethics_b_timely':'1','ethics_c_exempt':0,'ethics_c_timely':'1'
}
payload=json.dumps(post,ensure_ascii=False)
render='''<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode(%s,true); include %s;''' % (json.dumps(payload,ensure_ascii=False),json.dumps(str(ROOT/'ypologismos-didaktikon-anagkon.php')))
r=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=render,capture_output=True)
if r.returncode:
    print(r.stderr); raise SystemExit(r.returncode)
out=r.stdout
check('Evening GEL is selectable, not placeholder', '<option value="esperino_gel" selected>Εσπερινό ΓΕΛ</option>' in out and '<option value="esperino_gel" disabled>' not in out)
check('result sidebar names Evening GEL', '<span>Δομή</span><strong>Εσπερινό ΓΕΛ</strong>' in out)
check('render reports 118 assigned hours', '<strong>118</strong><span>ώρες με αντιστοιχισμένη ανάθεση</span>' in out)
check('day GEL second-language block server-hidden for Evening GEL', re.search(r'id="gelLanguageGroupsSection"[^>]*\shidden',out) is not None)
check('Evening GEL semester selector is visible', re.search(r'id="eveningGelPeriodSection"(?![^>]*\shidden)',out) is not None and 'name="egel_b_period"' in out)
check('source card identifies FEK 2102 for Evening GEL', 'ΦΕΚ Β΄ 2102/2026 — Εσπερινό ΓΕΛ' in out)

# End-to-end public automatic allocation.
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
check('public allocation_auto works for Evening GEL', 'Η αυτόματη πρόταση δημιουργήθηκε.' in aout)
check('public allocation rows use Evening GEL course ids', 'egel.' in aout and 'allocation_slot_id' in aout)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
