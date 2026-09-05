#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, tempfile, textwrap
ROOT=Path(__file__).resolve().parents[1]
php=textwrap.dedent(r'''<?php
require __DIR__ . '/includes/school-profile-general-education.php';
require __DIR__ . '/includes/school-profile-workload.php';

$gym=schoolProfileBuildDayGymnasium2026(array(
 'profile_id'=>'contract-gym',
 'general_sections'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1),
 'second_foreign_language_groups'=>array(
  'Α΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>1,'Ιταλικά'=>0),
  'Β΄'=>array('Γαλλικά'=>2,'Γερμανικά'=>0,'Ιταλικά'=>0),
  'Γ΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>1),
 ),
 'ethics_by_grade'=>array(
  'Α΄'=>array('exempt_students'=>12,'within_fifth_day'=>true,'equivalent_ethics_sections'=>0),
  'Β΄'=>array('exempt_students'=>8,'within_fifth_day'=>true),
  'Γ΄'=>array('exempt_students'=>10,'within_fifth_day'=>true),
 ),
));
$gel=schoolProfileBuildDayGel2026(array(
 'profile_id'=>'contract-gel',
 'general_sections'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>3),
 'orientation_sections'=>array(
  'Β΄'=>array('humanities'=>1,'science'=>1),
  'Γ΄'=>array('humanities'=>1,'science_health'=>2,'economics_it'=>1),
 ),
 'second_foreign_language_groups'=>array(
  'Α΄'=>array('Γαλλικά'=>2,'Γερμανικά'=>1),
  'Β΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>1),
 ),
 'grade_c_science_health_field_groups'=>array('Μαθηματικά'=>1,'Βιολογία'=>1),
 'grade_c_conditional_groups'=>array('Μαθηματικά'=>1,'Ιστορία'=>3),
 'ethics_by_grade'=>array(
  'Α΄'=>array('exempt_students'=>12,'within_fifth_day'=>true,'equivalent_ethics_sections'=>1),
  'Β΄'=>array('exempt_students'=>10,'within_fifth_day'=>true,'equivalent_ethics_sections'=>0),
  'Γ΄'=>array('exempt_students'=>5,'within_fifth_day'=>true),
 ),
));
$incomplete=schoolProfileBuildDayGel2026(array(
 'profile_id'=>'incomplete-gel',
 'general_sections'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2),
));
function selectedUnits($matrix) {
 $out=array();
 foreach($matrix['units'] as $u){
  if (!empty($u['choice_option'])
      || strpos($u['instance_id'],'thriskeftika@')!==false
      || strpos($u['instance_id'],'ithiki@')!==false
      || strpos($u['instance_id'],'gel.b.')===0
      || strpos($u['instance_id'],'gel.c.')===0
      || strpos($u['instance_id'],'gel.general.istoria@Γ΄')===0
      || strpos($u['instance_id'],'gel.general.mathimatika_conditional@Γ΄')===0) {
    $out[]=$u;
  }
 }
 return $out;
}
echo json_encode(array(
 'gym_readiness'=>schoolProfileGeneralEducationReadiness($gym),
 'gel_readiness'=>schoolProfileGeneralEducationReadiness($gel),
 'incomplete_readiness'=>schoolProfileGeneralEducationReadiness($incomplete),
 'gym_realized'=>schoolProfileRealize($gym),
 'gel_realized'=>schoolProfileRealize($gel),
 'gym_matrix'=>schoolProfileWorkloadMatrix($gym),
 'gel_matrix'=>schoolProfileWorkloadMatrix($gel),
 'gym_units'=>selectedUnits(schoolProfileWorkloadMatrix($gym)),
 'gel_units'=>selectedUnits(schoolProfileWorkloadMatrix($gel)),
), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''')
# __DIR__ must resolve to repo root for this ephemeral probe.
php=php.replace("__DIR__ . '/includes/", "'"+str(ROOT).replace("'","\\'")+"/includes/")
proc=subprocess.run(['php'],input=php,text=True,capture_output=True,cwd=ROOT)
if proc.returncode:
    print(proc.stderr); raise SystemExit(proc.returncode)
data=json.loads(proc.stdout)
checks=[]
def check(name, cond): checks.append((name,bool(cond)))

def unit(units, instance, option=None):
    for u in units:
        if u['instance_id']==instance and (option is None or u.get('choice_option')==option): return u
    return None

check('gym structural readiness', data['gym_readiness']['ready'])
check('gel structural readiness', data['gel_readiness']['ready'])
check('incomplete GEL is rejected as ready', not data['incomplete_readiness']['ready'])
check('incomplete GEL asks orientation input', any('orientation' in x for x in data['incomplete_readiness']['issues']))
check('incomplete GEL asks language groups', any('second_foreign' in x for x in data['incomplete_readiness']['issues']))

# Exact synthetic totals: importantly, staffing-group hours include parallel Ethics.
check('gym fixed teaching-group hours 171', data['gym_realized']['summary']['fixed_curriculum_hours']==171)
check('gym no unresolved dependencies after complete inputs', data['gym_realized']['summary']['dependency_instances']==0)
check('gel fixed teaching-group hours 293', data['gel_realized']['summary']['fixed_curriculum_hours']==293)
check('gel no unresolved dependencies after complete inputs', data['gel_realized']['summary']['dependency_instances']==0)
check('gym matrix fully covers realized hours', data['gym_matrix']['summary']['assignment_unit_hours']==171 and data['gym_matrix']['summary']['uncovered_unit_hours']==0)
check('gel matrix fully covers realized hours', data['gel_matrix']['summary']['assignment_unit_hours']==293 and data['gel_matrix']['summary']['uncovered_unit_hours']==0)

# Second foreign language becomes real per-language staffing units.
u=unit(data['gym_units'],'gym.deyteri_xeni@Α΄','Γαλλικά'); check('gym A French group is 2 hours PE05', u and u['school_hours']==2 and u['top_codes']==['ΠΕ05'])
u=unit(data['gym_units'],'gym.deyteri_xeni@Α΄','Γερμανικά'); check('gym A German group is 2 hours PE07', u and u['school_hours']==2 and u['top_codes']==['ΠΕ07'])
u=unit(data['gym_units'],'gym.deyteri_xeni@Γ΄','Ιταλικά'); check('gym C Italian group is 2 hours PE34', u and u['school_hours']==2 and u['top_codes']==['ΠΕ34'])
u=unit(data['gel_units'],'gel.general.deyteri_xeni@Α΄','Γαλλικά'); check('GEL A two French groups give 4 PE05 hours', u and u['school_hours']==4 and u['top_codes']==['ΠΕ05'])
u=unit(data['gel_units'],'gel.general.deyteri_xeni@Β΄','Γερμανικά'); check('GEL B German group gives 1 PE07 hour', u and u['school_hours']==1 and u['top_codes']==['ΠΕ07'])

# Orientation groups no longer multiply by general sections.
u=unit(data['gel_units'],'gel.b.humanities.archaia@Β΄'); check('GEL B humanities uses one orientation group', u and u['section_count']==1 and u['school_hours']==3)
u=unit(data['gel_units'],'gel.b.science.mathimatika@Β΄'); check('GEL B science uses one orientation group', u and u['section_count']==1 and u['school_hours']==3)
u=unit(data['gel_units'],'gel.c.humanities.archaia@Γ΄'); check('GEL C humanities uses one orientation group', u and u['section_count']==1 and u['school_hours']==6)
u=unit(data['gel_units'],'gel.c.health.fysiki@Γ΄'); check('GEL C science-health common subjects use two groups', u and u['section_count']==2 and u['school_hours']==12)
u=unit(data['gel_units'],'gel.c.econ.pliroforiki@Γ΄'); check('GEL C economics-IT uses one group', u and u['section_count']==1 and u['school_hours']==6)

# 2nd/3rd field choice is explicitly split rather than both multiplied by health groups.
u=unit(data['gel_units'],'gel.c.health.mathimatika@Γ΄'); check('GEL C 2nd field Math group explicit', u and u['section_count']==1 and u['school_hours']==6 and u['top_codes']==['ΠΕ03'])
u=unit(data['gel_units'],'gel.c.health.viologia@Γ΄'); check('GEL C 3rd field Biology group explicit', u and u['section_count']==1 and u['school_hours']==6 and set(u['top_codes'])=={'ΠΕ04.03','ΠΕ04.04'})

# Conditional general-education groups are explicit school inputs.
u=unit(data['gel_units'],'gel.general.mathimatika_conditional@Γ΄'); check('GEL C conditional Math uses explicit one group', u and u['section_count']==1 and u['school_hours']==2)
u=unit(data['gel_units'],'gel.general.istoria@Γ΄'); check('GEL C conditional History uses explicit three groups', u and u['section_count']==3 and u['school_hours']==6)

# Ethics: formation logic now changes actual teaching groups.
u=unit(data['gym_units'],'gym.ithiki@Α΄'); check('gym A consolidated parallel Ethics adds one group', u and u['section_count']==1 and u['school_hours']==2)
u=unit(data['gym_units'],'gym.thriskeftika@Α΄'); check('gym A consolidated parallel keeps two Religion groups', u and u['section_count']==2 and u['school_hours']==4)
check('gym B no Ethics unit below threshold', unit(data['gym_units'],'gym.ithiki@Β΄') is None)
u=unit(data['gym_units'],'gym.ithiki@Γ΄'); check('gym C single-section parallel Ethics one group', u and u['section_count']==1 and u['school_hours']==2)
u=unit(data['gel_units'],'gel.general.ithiki@Α΄'); check('GEL A dedicated equivalent Ethics one group', u and u['section_count']==1 and u['school_hours']==2)
u=unit(data['gel_units'],'gel.general.thriskeftika@Α΄'); check('GEL A dedicated equivalent reduces Religion to two groups', u and u['section_count']==2 and u['school_hours']==4)
u=unit(data['gel_units'],'gel.general.ithiki@Β΄'); check('GEL B consolidated parallel Ethics one group', u and u['section_count']==1 and u['school_hours']==2)
check('GEL C no Ethics unit below threshold', unit(data['gel_units'],'gel.general.ithiki@Γ΄') is None)

# No public UI behavior change: profile metadata must not leak into public rows.
page=(ROOT/'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
check('general profile layer remains internal', 'school-profile-general-education.php' not in page)
# Verify the public payload helper strips profile_* metadata.
probe=subprocess.run(['php','-r',f'require "{ROOT}/includes/weekly-timetable-data.php"; foreach(weeklyTimetablePublicRows() as $r){{foreach(array_keys($r) as $k){{if(strpos($k,"profile_")===0){{echo $k; exit(1);}}}}}}'],capture_output=True,text=True)
check('profile metadata stripped from public timetable payload', probe.returncode==0 and probe.stdout=='')

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('GYM_SUMMARY '+json.dumps(data['gym_matrix']['summary'],ensure_ascii=False,sort_keys=True))
print('GEL_SUMMARY '+json.dumps(data['gel_matrix']['summary'],ensure_ascii=False,sort_keys=True))
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
