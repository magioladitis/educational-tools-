#!/usr/bin/env python3
from pathlib import Path
import json, subprocess
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

def php_json(code):
    return json.loads(subprocess.check_output(['php','-d','memory_limit=512M','-r',code],cwd=ROOT,text=True))

php=r'''
require "includes/personnel-workload.php";
require "includes/school-profile-general-education.php";
function profileX(){return schoolProfileBuildDayGymnasium2026(array(
 'profile_id'=>'balance-contract','school'=>array('type'=>'Ημερήσιο Γυμνάσιο','name'=>'Balance Contract'),
 'general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
 'second_foreign_language_groups'=>array(
   'Α΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
   'Β΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
   'Γ΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0)
 ),
 'technology_informatics_split_sections'=>array('Α΄'=>0,'Β΄'=>0,'Γ΄'=>0),
 'ethics_by_grade'=>array()
));}
$p=profileX();
$empty=personnelWorkloadSpecialtyBalanceReport($p,array(),array());
$pe05=array(array('person_id'=>'f1','display_name'=>'French','specialty_code'=>'ΠΕ05','required_teaching_hours'=>23,'role'=>'teacher','assigned_external_hours'=>0));
$frAlloc=array(
 array('person_id'=>'f1','slot_id'=>'gym.deyteri_xeni@Α΄|choice|1|_|section|1','hours'=>2),
 array('person_id'=>'f1','slot_id'=>'gym.deyteri_xeni@Β΄|choice|1|_|section|1','hours'=>2),
 array('person_id'=>'f1','slot_id'=>'gym.deyteri_xeni@Γ΄|choice|1|_|section|1','hours'=>2)
);
$withSurplus=personnelWorkloadSpecialtyBalanceReport($p,$pe05,$frAlloc);
$secondary=array(array('person_id'=>'s1','display_name'=>'IT Math','specialty_code'=>'ΠΕ86','secondary_specialty_code'=>'ΠΕ03','required_teaching_hours'=>12,'role'=>'teacher','assigned_external_hours'=>0));
$secondaryReport=personnelWorkloadSpecialtyBalanceReport($p,$secondary,array());
$legacyPe0403=array(array('person_id'=>'l1','display_name'=>'Legacy PE04.03','specialty_code'=>'ΠΕ04.03','required_teaching_hours'=>23,'role'=>'teacher','assigned_external_hours'=>0));
$legacyReport=personnelWorkloadSpecialtyBalanceReport($p,$legacyPe0403,array());
echo json_encode(array('empty'=>$empty,'surplus'=>$withSurplus,'secondary'=>$secondaryReport,'legacy'=>$legacyReport),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
x=php_json(php)
empty=x['empty']
check('special report semantics are proposal not official act', empty['semantics']['official_vacancy_calculation'] is False and empty['semantics']['existing_staff_auto_balance_is_proposal_only'] is True)
check('smart selection limited to equal best assignments', empty['semantics']['smart_choice_only_among_equal_best_assignment_codes'] is True)
check('legacy PE04.03 is not proposed as new vacancy when current alternative exists', empty['semantics']['pe0403_kept_for_legal_assignment_compatibility_but_not_new_vacancy_preference'] is True)
check('skills reported in dedicated gymnasium bucket', empty['special_reporting_buckets']['GYM_SKILLS']['gap_hours']==3)
check('technology reported in dedicated gymnasium bucket', empty['special_reporting_buckets']['GYM_TECHNOLOGY']['gap_hours']==3)
check('special buckets sum to six hours', empty['summary']['special_reporting_bucket_gap_hours_total']==6)
history=[r for r in empty['vacancy_recommendations'] if r['subject']=='Ιστορία']
check('shared history top assignment uses smart code PE02', len(history)==3 and all(r['selected_code']=='ΠΕ02' for r in history) and all(r['selection_kind']=='smart_shared_top_assignment' for r in history))
bio=[r for r in empty['vacancy_recommendations'] if r['subject']=='Βιολογία']
geo=[r for r in empty['vacancy_recommendations'] if r['subject']=='Γεωλογία - Γεωγραφία']
check('biology vacancy prefers current PE04.04 over legacy PE04.03', bio and all(r['selected_code']=='ΠΕ04.04' for r in bio))
check('geology geography vacancy prefers PE04.05 over legacy PE04.03', geo and all(r['selected_code']=='ΠΕ04.05' for r in geo))
check('PE04.03 remains visible in legal candidates for audit', bio and geo and all('ΠΕ04.03' in r.get('legal_candidate_codes',[]) for r in bio+geo))
check('no new vacancy recommendation selects PE04.03', not any(r['selected_code']=='ΠΕ04.03' for r in empty['vacancy_recommendations']))
check('gap rows use negative signed balance', empty['by_specialty']['ΠΕ02']['gap_hours']>0 and empty['by_specialty']['ΠΕ02']['signed_balance_hours']==-empty['by_specialty']['ΠΕ02']['gap_hours'])

sur=x['surplus']
auto=sur['automatic_balance']
check('existing PE05 automatically covers additional legal B assignments', auto['summary']['auto_covered_hours']>=6 and any(a['priority']=='B' and a['subject']=='Ιστορία' for a in auto['allocations']))
check('auto balance removes history from final vacancy recommendations', not any(r['subject']=='Ιστορία' for r in sur['vacancy_recommendations']))
check('remaining PE05 hours are surplus under primary specialty', sur['by_specialty']['ΠΕ05']['surplus_hours']>0 and sur['semantics']['surplus_reported_under_primary_specialty'] is True)
check('automatic balance respects B ten-hour limit', max([a.get('hours',0) for a in auto['allocations']] or [0])>=0 and auto['people']['f1']['b_assignment_hours']<=10 and auto['semantics']['b_assignment_limit_10_respected_without_exception'] is True)

sec=x['secondary']
sec_alloc=[a for a in sec['automatic_balance']['allocations'] if a['subject']=='Μαθηματικά']
check('secondary specialty participates in internal auto balance', sec_alloc and all(a['used_specialty_code']=='ΠΕ03' and a['specialty_source']=='secondary' for a in sec_alloc))
check('secondary specialty does not change surplus branch identity', sec['automatic_balance']['people']['s1']['primary_code']=='ΠΕ86')

legacy=x['legacy']
legacy_alloc=[a for a in legacy['automatic_balance']['allocations'] if a.get('used_specialty_code')=='ΠΕ04.03']
check('existing PE04.03 personnel remains legally usable in internal allocation', legacy_alloc and any(a['subject']=='Γεωλογία - Γεωγραφία' and a['priority']=='A' for a in legacy_alloc))

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
