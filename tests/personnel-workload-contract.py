#!/usr/bin/env python3
"""Contract for personnel workload / roster validation layer."""
from pathlib import Path
import json, subprocess

ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

def php_json(code):
    return json.loads(subprocess.check_output(['php','-d','memory_limit=512M','-r',code],cwd=ROOT,text=True))

# 1) Mandatory-hours parity with the existing public JS calculator for representative
# secondary thresholds and roles.
php=r'''
require "includes/personnel-workload.php";
$cases=array(
 array('id'=>'pe0','p'=>array('person_id'=>'p','specialty_code'=>'ΠΕ03','service'=>array('years'=>0))),
 array('id'=>'pe6','p'=>array('person_id'=>'p','specialty_code'=>'ΠΕ03','service'=>array('years'=>6))),
 array('id'=>'pe6d1','p'=>array('person_id'=>'p','specialty_code'=>'ΠΕ03','service'=>array('years'=>6,'days'=>1))),
 array('id'=>'pe12','p'=>array('person_id'=>'p','specialty_code'=>'ΠΕ03','service'=>array('years'=>12))),
 array('id'=>'pe12d1','p'=>array('person_id'=>'p','specialty_code'=>'ΠΕ03','service'=>array('years'=>12,'days'=>1))),
 array('id'=>'pe20','p'=>array('person_id'=>'p','specialty_code'=>'ΠΕ03','service'=>array('years'=>20))),
 array('id'=>'te7','p'=>array('person_id'=>'p','specialty_code'=>'ΤΕ01.04','service'=>array('years'=>7))),
 array('id'=>'te7d1','p'=>array('person_id'=>'p','specialty_code'=>'ΤΕ01.04','service'=>array('years'=>7,'days'=>1))),
 array('id'=>'director','p'=>array('person_id'=>'p','specialty_code'=>'ΠΕ03','service'=>array('years'=>20),'role'=>'director','director_sections_band'=>'6-9')),
 array('id'=>'sector','p'=>array('person_id'=>'p','specialty_code'=>'ΠΕ03','service'=>array('years'=>7),'role'=>'epal_ek_lab_sector'))
);
$out=array(); foreach($cases as $c){$out[$c['id']]=personnelWorkloadSecondaryObligation($c['p']);}
echo json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
ob=php_json(php)
expected={'pe0':23,'pe6':23,'pe6d1':21,'pe12':21,'pe12d1':20,'pe20':18,'te7':24,'te7d1':21,'director':7,'sector':19}
for k,v in expected.items(): check(f'obligation {k} = {v}', ob[k]['valid'] and ob[k]['required_teaching_hours']==v)
check('PE branch inferred', ob['pe0']['hours_branch']=='PE' and ob['pe0']['hours_branch_mode']=='inferred_from_specialty')
check('TE branch inferred', ob['te7']['hours_branch']=='TE01')

php=r'''
require "includes/personnel-workload.php";
echo json_encode(array(
 'de_auto'=>personnelWorkloadSecondaryObligation(array('person_id'=>'d','specialty_code'=>'ΔΕ01.05','service'=>array('years'=>5))),
 'de_explicit'=>personnelWorkloadSecondaryObligation(array('person_id'=>'d','specialty_code'=>'ΔΕ01.05','hours_branch'=>'DE01_ARCH','service'=>array('years'=>5)))
),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
de=php_json(php)
check('DE hours scale not guessed', de['de_auto']['status']=='needs_input' and de['de_auto']['valid'] is False)
check('DE explicit scale resolves', de['de_explicit']['valid'] and de['de_explicit']['required_teaching_hours']==28)

# 2) Person evaluation in the real ENEEGYL Corfu profile.
php=r'''
require "includes/personnel-workload.php";
require "includes/school-profile-eneegyl-kerkyra-2026.php";
$p=array('person_id'=>'math1','display_name'=>'Synthetic Math','specialty_code'=>'ΠΕ03','service'=>array('years'=>7),'assigned_external_hours'=>3);
$a=array(
 array('unit_id'=>'eneegyl.gym.mathimatika@Α΄|whole','hours'=>6),
 array('unit_id'=>'eneegyl.gym.mathimatika@Β΄|whole','hours'=>6),
 array('unit_id'=>'eneegyl.gym.mathimatika@Γ΄|whole','hours'=>6)
);
$good=personnelWorkloadEvaluatePerson(schoolProfileEneegylKerkyra2026(),$p,$a);
$fallback=personnelWorkloadEvaluatePerson(schoolProfileEneegylKerkyra2026(),$p,array(array('unit_id'=>'eneegyl.gym.fysiki@Α΄|whole','hours'=>2)));
$bad=personnelWorkloadEvaluatePerson(schoolProfileEneegylKerkyra2026(),$p,array(array('unit_id'=>'eneegyl.gym.glosiki@Α΄|whole','hours'=>2)));
echo json_encode(array('good'=>$good,'fallback'=>$fallback,'bad'=>$bad),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
ev=php_json(php)
g=ev['good']
check('synthetic PE03 obligation 21', g['required_teaching_hours']==21)
check('external 3 + profile 18 = exact obligation', g['assigned_external_hours']==3 and g['assigned_profile_hours']==18 and g['assigned_total_hours']==21 and g['remaining_hours']==0 and g['hours_status']=='exact_required')
check('math assignments are exclusive top A', g['assigned_top_priority_hours']==18 and g['assigned_exclusive_top_hours']==18 and g['assigned_shared_top_hours']==0 and g['assigned_fallback_hours']==0 and g['assigned_hours_by_priority']['A']==18)
check('valid exact person plan', g['valid'] is True and g['allocation_errors']==[])
check('fallback assignment remains valid but warned', ev['fallback']['valid'] is True and ev['fallback']['assigned_fallback_hours']==2 and any('lower_priority' in w for w in ev['fallback']['allocation_warnings']))
check('ineligible subject rejected', ev['bad']['valid'] is False and any('specialty_not_eligible' in e for e in ev['bad']['allocation_errors']))

# 3) Roster capacity: two teachers cannot jointly cover more than a curriculum unit contains.
php=r'''
require "includes/personnel-workload.php";
require "includes/school-profile-eneegyl-kerkyra-2026.php";
$people=array(
 array('person_id'=>'m1','specialty_code'=>'ΠΕ03','service'=>array('years'=>7)),
 array('person_id'=>'m2','specialty_code'=>'ΠΕ03','service'=>array('years'=>20))
);
$ok=personnelWorkloadRosterPlan(schoolProfileEneegylKerkyra2026(),$people,array(
 array('person_id'=>'m1','unit_id'=>'eneegyl.gym.mathimatika@Α΄|whole','hours'=>3),
 array('person_id'=>'m2','unit_id'=>'eneegyl.gym.mathimatika@Α΄|whole','hours'=>3)
));
$over=personnelWorkloadRosterPlan(schoolProfileEneegylKerkyra2026(),$people,array(
 array('person_id'=>'m1','unit_id'=>'eneegyl.gym.mathimatika@Α΄|whole','hours'=>4),
 array('person_id'=>'m2','unit_id'=>'eneegyl.gym.mathimatika@Α΄|whole','hours'=>4)
));
echo json_encode(array('ok'=>$ok,'over'=>$over),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
ro=php_json(php)
unit='eneegyl.gym.mathimatika@Α΄|whole'
check('split unit can exactly cover six hours', ro['ok']['units'][unit]['assigned_hours']==6 and ro['ok']['units'][unit]['remaining_hours']==0 and ro['ok']['units'][unit]['overallocated_hours']==0)
check('roster detects shared unit over-allocation', ro['over']['valid'] is False and ro['over']['units'][unit]['assigned_hours']==8 and ro['over']['units'][unit]['overallocated_hours']==2 and ro['over']['summary']['overallocated_unit_hours']>=2)
check('roster does not call remaining curriculum hours official vacancies', ro['ok']['semantics']['official_vacancy_calculation'] is False)
check('automatic placement disabled', ro['ok']['semantics']['automatic_placement'] is False)
check('open opportunities explicitly non-additive', ro['ok']['semantics']['open_eligible_units_are_overlapping_opportunities_not_additive_staffing_need'] is True)
check('PE03 open opportunities exposed after assignments', ro['ok']['people']['m1']['open_eligible_unit_count']>0 and ro['ok']['people']['m1']['open_eligible_top_hours']>0)
check('fully covered math A removed from open opportunities', all(x['unit_id']!=unit for x in ro['ok']['people']['m1']['open_eligible_units']))
check('open opportunities carry priority semantics', all('priority' in x and 'is_top_priority' in x and 'open_unit_hours' in x for x in ro['ok']['people']['m1']['open_eligible_units']))

# 4) EEEEK profile must remain structure-only until section counts are known.
php=r'''
require "includes/personnel-workload.php";
require "includes/school-profile-eeeek-kerkyra-2026.php";
$p=array('person_id'=>'g1','specialty_code'=>'ΠΕ88.01','service'=>array('years'=>10));
$x=personnelWorkloadEvaluatePerson(schoolProfileEeeekKerkyra2026(),$p,array());
echo json_encode($x,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
ee=php_json(php)
check('EEEEK refuses fabricated personnel allocation', ee['valid'] is False and ee['matrix_readiness']=='structure_only' and 'school_profile_has_no_assignable_fixed_units' in ee['allocation_errors'])

# 5) Architecture is internal-only and no public UI changes are introduced.
for page in ('orologio-programma-mathimaton.php','anatheseis-mathimaton.php','ergaleia.php'):
    text=(ROOT/page).read_text(encoding='utf-8')
    check(f'personnel layer not loaded by {page}', 'personnel-workload.php' not in text)

# 6) Cross-check mandatory-hour outputs against the existing browser JS module.
node=r'''
const fs=require('fs'), vm=require('vm');
const code=fs.readFileSync('./includes/teaching-hours-calculations.js','utf8');
const sandbox={window:{}}; vm.runInNewContext(code,sandbox,{filename:'teaching-hours-calculations.js'});
const H=sandbox.window.EducationTeachingHours;
const cases={
 pe0:{level:'secondary',role:'teacher',branch:'PE',years:0},
 pe6:{level:'secondary',role:'teacher',branch:'PE',years:6},
 pe6d1:{level:'secondary',role:'teacher',branch:'PE',years:6,days:1},
 pe12:{level:'secondary',role:'teacher',branch:'PE',years:12},
 pe12d1:{level:'secondary',role:'teacher',branch:'PE',years:12,days:1},
 pe20:{level:'secondary',role:'teacher',branch:'PE',years:20},
 te7:{level:'secondary',role:'teacher',branch:'TE01',years:7},
 te7d1:{level:'secondary',role:'teacher',branch:'TE01',years:7,days:1},
 director:{level:'secondary',role:'director',branch:'PE',years:20,sections:'6-9'},
 sector:{level:'secondary',role:'epal_ek_lab_sector',branch:'PE',years:7}
};
const out={}; for (const [k,v] of Object.entries(cases)) out[k]=H.calculate(v).hours;
console.log(JSON.stringify(out));
'''
js=json.loads(subprocess.check_output(['node','-e',node],cwd=ROOT,text=True))
for k,v in expected.items(): check(f'PHP/JS parity {k}', ob[k]['required_teaching_hours']==js[k])

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('GOOD_PERSON '+json.dumps({k:g[k] for k in ('required_teaching_hours','assigned_external_hours','assigned_profile_hours','assigned_total_hours','remaining_hours','hours_status','assigned_exclusive_top_hours','assigned_shared_top_hours','assigned_fallback_hours')},ensure_ascii=False,sort_keys=True))
print('ROSTER_OK '+json.dumps(ro['ok']['summary'],ensure_ascii=False,sort_keys=True))
print('ROSTER_OVER_UNIT '+json.dumps(ro['over']['units'][unit],ensure_ascii=False,sort_keys=True))
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
