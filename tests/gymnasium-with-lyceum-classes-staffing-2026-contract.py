#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, textwrap, re
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond): checks.append((name,bool(cond)))

php=textwrap.dedent(r'''<?php
require __DIR__ . '/includes/school-profile-general-education.php';
require __DIR__ . '/includes/school-profile-workload.php';
require __DIR__ . '/includes/personnel-workload.php';
require __DIR__ . '/includes/teaching-allocation-engine.php';
$e=array(
 'Α΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
 'Β΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
 'Γ΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
);
$p=schoolProfileBuildGymnasiumWithLyceumClasses2026(array(
 'profile_id'=>'contract-gym-lt',
 'school'=>array('type'=>'Γυμνάσιο με Λυκειακές Τάξεις','name'=>'Contract composite'),
 'gymnasium_general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
 'gymnasium_second_foreign_language_groups'=>array(
   'Α΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
   'Β΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
   'Γ΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
 ),
 'gymnasium_technology_informatics_split_sections'=>array('Α΄'=>0,'Β΄'=>0,'Γ΄'=>0),
 'gymnasium_ethics_by_grade'=>$e,
 'lyceum_general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
 'lyceum_second_foreign_language_groups'=>array(
   'Α΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0),
   'Β΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0),
 ),
 'lyceum_orientation_sections'=>array(
   'Β΄'=>array('humanities'=>1,'science'=>1),
   'Γ΄'=>array('humanities'=>1,'science_health'=>1,'economics_it'=>1),
 ),
 'lyceum_grade_c_science_health_field_groups'=>array('Μαθηματικά'=>1,'Βιολογία'=>0),
 'lyceum_grade_c_conditional_groups'=>array('Μαθηματικά'=>1,'Ιστορία'=>2),
 'lyceum_ethics_by_grade'=>$e,
));
$m=schoolProfileWorkloadMatrix($p);
$slots=personnelWorkloadAllocationSlots($p,$m);
$labels=array_values(array_unique(array_map(function($x){return $x['slot_label'];},$slots)));
$people=array(
 array('person_id'=>'pe03','display_name'=>'Μαθηματικός','specialty_code'=>'ΠΕ03','required_teaching_hours'=>20,'role'=>'teacher','assigned_external_hours'=>0),
 array('person_id'=>'pe02','display_name'=>'Φιλόλογος','specialty_code'=>'ΠΕ02','required_teaching_hours'=>20,'role'=>'teacher','assigned_external_hours'=>0),
);
$proposal=teachingAllocationEngineProposal($p,$people);
echo json_encode(array(
 'profile'=>$p,
 'readiness'=>schoolProfileGeneralEducationReadiness($p),
 'summary'=>$m['summary'],
 'labels'=>$labels,
 'proposal'=>$proposal,
 'total_sections'=>schoolProfileTotalGeneralSections($p),
),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''').replace("__DIR__ . '/includes/", "'"+str(ROOT).replace("'","\\'")+"/includes/")
r=subprocess.run(['php'],cwd=ROOT,text=True,input=php,capture_output=True)
if r.returncode:
    print(r.stderr); raise SystemExit(r.returncode)
data=json.loads(r.stdout)
check('composite contains Gymnasium and GEL structures', set(data['profile']['structures'])=={'gymnasio','gel'})
check('composite declares shared personnel pool', data['profile'].get('composite',{}).get('shared_personnel_pool') is True)
check('six basic classes count as six school sections', data['total_sections']==6)
check('complete composite profile is structurally ready', data['readiness']['ready'] is True)
check('one representative section per all six grades realizes 248 assignment hours', data['summary']['assignment_unit_hours']==248)
check('all composite hours have legal assignment mappings', data['summary']['uncovered_unit_hours']==0)
check('complete composite Ethics has no dependencies', data['summary']['active_dependency_instances']==0)
check('composite has no active regulatory gaps', data['summary']['active_regulatory_gap_instances']==0)
check('allocation labels distinguish Gymnasium A1', 'Γυμν. Α1' in data['labels'])
check('allocation labels distinguish Lyceum A1', 'ΛΤ Α1' in data['labels'])
check('same allocation engine accepts composite profile', data['proposal']['status']=='ok')
check('proposal can include Gymnasium units', any(str(x.get('slot_id','')).startswith('gym.') for x in data['proposal'].get('proposed_allocations',[])))
check('proposal can include GEL units from same personnel pool', any(str(x.get('slot_id','')).startswith('gel.') for x in data['proposal'].get('proposed_allocations',[])))

# Public render contract with full composite profile.
post={
 'staffing_action':'profile','school_type':'gymnasio_lt','school_name':'Contract composite',
 'gym_general_a':1,'gym_general_b':1,'gym_general_c':1,
 'gym_lang_a_fr':1,'gym_lang_a_de':0,'gym_lang_a_it':0,'gym_lang_b_fr':1,'gym_lang_b_de':0,'gym_lang_b_it':0,'gym_lang_c_fr':1,'gym_lang_c_de':0,'gym_lang_c_it':0,
 'gym_tech_split_a':0,'gym_tech_split_b':0,'gym_tech_split_c':0,
 'gel_general_a':1,'gel_general_b':1,'gel_general_c':1,
 'gel_lang_a_fr':1,'gel_lang_a_de':0,'gel_lang_b_fr':1,'gel_lang_b_de':0,
 'gel_b_hum':1,'gel_b_sci':1,'gel_c_hum':1,'gel_c_scihealth':1,'gel_c_econit':1,
 'gel_c_field_math':1,'gel_c_field_bio':0,'gel_c_cond_math':1,'gel_c_cond_history':2,
 'ethics_a_exempt':0,'ethics_a_timely':'1','ethics_b_exempt':0,'ethics_b_timely':'1','ethics_c_exempt':0,'ethics_c_timely':'1',
 'lt_ethics_a_exempt':0,'lt_ethics_a_timely':'1','lt_ethics_b_exempt':0,'lt_ethics_b_timely':'1','lt_ethics_c_exempt':0,'lt_ethics_c_timely':'1',
}
payload=json.dumps(post,ensure_ascii=False)
render='''<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode(%s,true); include %s;''' % (json.dumps(payload,ensure_ascii=False),json.dumps(str(ROOT/'ypologismos-didaktikon-anagkon.php')))
rr=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=render,capture_output=True)
if rr.returncode:
    print(rr.stderr); raise SystemExit(rr.returncode)
out=rr.stdout
check('composite is selectable not placeholder', '<option value="gymnasio_lt" selected>Γυμνάσιο με Λυκειακές Τάξεις</option>' in out and '<option value="gymnasio_lt" disabled>' not in out)
check('both Gymnasium and Lyceum profile blocks visible server-side', re.search(r'id="gymProfileFields"(?![^>]*\shidden)',out) is not None and re.search(r'id="gelProfileFields"(?![^>]*\shidden)',out) is not None)
check('separate composite Ethics panels render', 'id="ethicsPanelGym"' in out and 'id="ethicsPanelLt"' in out and 'name="lt_ethics_a_exempt"' in out)
check('result sidebar names composite structure', '<span>Δομή</span><strong>Γυμνάσιο με Λ.Τ.</strong>' in out)
check('render reports combined 248 assigned hours', '<strong>248</strong><span>ώρες με αντιστοιχισμένη ανάθεση</span>' in out)

# Safety limit is global across all six basic grades.
post120=dict(post); post120.update({'gym_general_a':20,'gym_general_b':20,'gym_general_c':20,'gel_general_a':20,'gel_general_b':20,'gel_general_c':20})
p120=json.dumps(post120,ensure_ascii=False)
r120=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input='''<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode(%s,true); include %s;'''%(json.dumps(p120,ensure_ascii=False),json.dumps(str(ROOT/'ypologismos-didaktikon-anagkon.php'))),capture_output=True)
check('exactly 120 composite basic sections passes hard cap', 'Ο υπολογισμός δεν εκτελέστηκε.' not in r120.stdout)
post121=dict(post120); post121['gel_general_c']=21
p121=json.dumps(post121,ensure_ascii=False)
r121=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input='''<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode(%s,true); include %s;'''%(json.dumps(p121,ensure_ascii=False),json.dumps(str(ROOT/'ypologismos-didaktikon-anagkon.php'))),capture_output=True)
check('121 composite basic sections rejected by backend hard cap', 'Ο υπολογισμός δεν εκτελέστηκε.' in r121.stdout and 'υπερβαίνει το τεχνικό όριο ασφαλείας των 120 τμημάτων' in r121.stdout)

# Built-in Corfu registry: Argyrades composite school is now loadable and maps both structures.
node=r'''
const c=require('./includes/school-profile-csv-import.js');
const d=c.getBuiltinDirectory('dde_corfu_2026');
const r=d.schools.find(x=>x.school_code==='2405010');
const f=c.schoolToFormValues(r);
console.log(JSON.stringify({supported:r.supported,type:r.school_type,total:c.basicSectionTotal(r),gym:[f.gym_general_a,f.gym_general_b,f.gym_general_c],lt:[f.gel_general_a,f.gel_general_b,f.gel_general_c],tracks:[f.gel_b_hum,f.gel_b_sci,f.gel_c_hum,f.gel_c_scihealth,f.gel_c_econit],placeholder:c.placeholderTypes.includes('gymnasio_lt')}));
'''
n=subprocess.run(['node'],cwd=ROOT,text=True,input=node,capture_output=True)
check('Corfu Argyrades composite row parses', n.returncode==0)
nd=json.loads(n.stdout)
check('Argyrades is now supported not placeholder', nd['supported'] is True and nd['type']=='gymnasio_lt' and nd['placeholder'] is False)
check('Argyrades registry maps Gymnasium 2/1/1', nd['gym']==[2,1,1])
check('Argyrades registry maps Lyceum classes 2/1/2', nd['lt']==[2,1,2])
check('Argyrades registry maps Lyceum orientation groups', nd['tracks']==[1,1,1,1,1])
check('Argyrades total basic sections is nine across both structures', nd['total']==9)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
