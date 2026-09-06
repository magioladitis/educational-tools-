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
$p=schoolProfileBuildDayGymnasium2026(array(
 'profile_id'=>'slot-contract','school'=>array('type'=>'Ημερήσιο Γυμνάσιο','name'=>'Slot Contract'),
 'general_sections'=>array('Α΄'=>2,'Β΄'=>1,'Γ΄'=>1),
 'second_foreign_language_groups'=>array(
   'Α΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>1,'Ιταλικά'=>0),
   'Β΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
   'Γ΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0)
 ),
 'technology_informatics_split_sections'=>array('Α΄'=>1,'Β΄'=>0,'Γ΄'=>0),
 'ethics_by_grade'=>array()
));
$m=schoolProfileWorkloadMatrix($p);
$s=personnelWorkloadAllocationSlots($p,$m);
$people=array(
 array('person_id'=>'m1','display_name'=>'Math One','specialty_code'=>'ΠΕ03','service'=>array('years'=>7),'assigned_external_hours'=>0),
 array('person_id'=>'m2','display_name'=>'Math Two','specialty_code'=>'ΠΕ03','service'=>array('years'=>20),'assigned_external_hours'=>0),
 array('person_id'=>'f1','display_name'=>'Phys','specialty_code'=>'ΠΕ04.01','service'=>array('years'=>7),'assigned_external_hours'=>0),
 array('person_id'=>'l1','display_name'=>'Phil','specialty_code'=>'ΠΕ02','service'=>array('years'=>20),'assigned_external_hours'=>0),
 array('person_id'=>'s1','display_name'=>'IT Math','specialty_code'=>'ΠΕ86','secondary_specialty_code'=>'ΠΕ03','required_teaching_hours'=>21,'role'=>'teacher','assigned_external_hours'=>0),
 array('person_id'=>'t1','display_name'=>'Chem tie','specialty_code'=>'ΠΕ04.02','secondary_specialty_code'=>'ΠΕ85','required_teaching_hours'=>21,'role'=>'teacher','assigned_external_hours'=>0)
);
$ok=personnelWorkloadRosterSlotPlan($p,$people,array(
 array('person_id'=>'m1','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4),
 array('person_id'=>'m2','slot_id'=>'gym.mathimatika@Α΄|whole|section|2','hours'=>4)
));
$over=personnelWorkloadRosterSlotPlan($p,$people,array(
 array('person_id'=>'m1','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4),
 array('person_id'=>'m2','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4)
));
$fallback=personnelWorkloadRosterSlotPlan($p,$people,array(
 array('person_id'=>'f1','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4)
));
$bad=personnelWorkloadRosterSlotPlan($p,$people,array(
 array('person_id'=>'l1','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4)
));
$secondary=personnelWorkloadRosterSlotPlan($p,$people,array(
 array('person_id'=>'s1','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4)
));
$tieSlot=$s['gym.ximeia@Β΄|whole|section|1'];
$tie=personnelWorkloadBestAssignmentForSlot($tieSlot,$people[5]);
$p3=schoolProfileBuildDayGymnasium2026(array(
 'profile_id'=>'slot-b-limit','school'=>array('type'=>'Ημερήσιο Γυμνάσιο','name'=>'B Limit'),
 'general_sections'=>array('Α΄'=>3,'Β΄'=>3,'Γ΄'=>3),
 'second_foreign_language_groups'=>array(
   'Α΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
   'Β΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
   'Γ΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0)
 ),
 'technology_informatics_split_sections'=>array('Α΄'=>0,'Β΄'=>0,'Γ΄'=>0),
 'ethics_by_grade'=>array()
));
$bpeople=array(array('person_id'=>'b1','display_name'=>'Combined B','specialty_code'=>'ΠΕ86','secondary_specialty_code'=>'ΠΕ80','required_teaching_hours'=>23,'role'=>'teacher','assigned_external_hours'=>0));
$blimit=personnelWorkloadRosterSlotPlan($p3,$bpeople,array(
 array('person_id'=>'b1','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4),
 array('person_id'=>'b1','slot_id'=>'gym.kpa@Γ΄|whole|section|1','hours'=>3),
 array('person_id'=>'b1','slot_id'=>'gym.kpa@Γ΄|whole|section|2','hours'=>3),
 array('person_id'=>'b1','slot_id'=>'gym.kpa@Γ΄|whole|section|3','hours'=>3)
));
echo json_encode(array('slots'=>$s,'ok'=>$ok,'over'=>$over,'fallback'=>$fallback,'bad'=>$bad,'secondary'=>$secondary,'tie'=>$tie,'blimit'=>$blimit),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
x=php_json(php)
slots=x['slots']
a1='gym.mathimatika@Α΄|whole|section|1'; a2='gym.mathimatika@Α΄|whole|section|2'
check('A1 math slot exists', a1 in slots and slots[a1]['slot_label']=='Α1' and slots[a1]['capacity_hours']==4)
check('A2 math slot exists', a2 in slots and slots[a2]['slot_label']=='Α2' and slots[a2]['capacity_hours']==4)
split='gym.pliroforiki@Α΄|whole|section|3'
check('split group does not fabricate section identity', split in slots and 'πρόσθετη ομάδα χωρισμού 1' in slots[split]['slot_label'])
fr='gym.deyteri_xeni@Α΄|choice|1|_|section|1'
check('foreign language is explicit group', fr in slots and slots[fr]['slot_label']=='Α΄ · Γαλλικά · Ομάδα 1')
check('slot leaf eligibility includes PE03 A math', 'ΠΕ03' in slots[a1]['eligible_by_priority']['A'])
check('slot capacities preserve aggregate matrix total', sum(v['capacity_hours'] for v in slots.values())==x['ok']['summary']['assignable_unit_hours_total'])
check('two separate A slots valid', x['ok']['valid'] is True and x['ok']['summary']['assigned_slot_hours_total']==8 and x['ok']['slots'][a1]['assigned_hours']==4 and x['ok']['slots'][a2]['assigned_hours']==4)
check('same A1 twice caught despite aggregate unit capacity', x['over']['valid'] is False and x['over']['slots'][a1]['assigned_hours']==8 and x['over']['slots'][a1]['overallocated_hours']==4 and x['over']['summary']['overallocated_slot_hours']==4)
check('overallocated rows marked invalid', x['over']['summary']['invalid_allocation_row_count']==2 and all('slot_overallocated_across_roster' in r['errors'] for r in x['over']['allocation_rows']))
check('lower assignment valid but warned', x['fallback']['valid'] is True and x['fallback']['allocation_rows'][0]['priority']=='B' and 'uses_lower_priority_assignment' in x['fallback']['allocation_rows'][0]['warnings'])
check('ineligible teacher rejected', x['bad']['valid'] is False and 'specialty_not_eligible' in x['bad']['allocation_rows'][0]['errors'])
check('manual semantics explicit', x['ok']['semantics']['manual_allocation_only'] is True and x['ok']['semantics']['slot_capacity_checked_per_section_or_group'] is True and x['ok']['semantics']['automatic_placement'] is False)
check('secondary specialty can provide better A assignment', x['secondary']['valid'] is True and x['secondary']['allocation_rows'][0]['priority']=='A' and x['secondary']['allocation_rows'][0]['used_specialty_code']=='ΠΕ03' and x['secondary']['allocation_rows'][0]['specialty_source']=='secondary')
check('tie between primary and secondary prefers primary', x['tie']['priority']=='A' and x['tie']['used_specialty_code']=='ΠΕ04.02' and x['tie']['specialty_source']=='primary')
check('B hours combine across primary and secondary without hard block', x['blimit']['valid'] is True and x['blimit']['people']['b1']['b_assignment_hours']==13 and x['blimit']['people']['b1']['b_assignment_limit_exceeded'] is True and x['blimit']['summary']['people_over_b_assignment_limit_count']==1)
check('B limit warning is attached to both source types', x['blimit']['allocation_rows'][0]['specialty_source']=='primary' and x['blimit']['allocation_rows'][1]['specialty_source']=='secondary' and all('b_assignment_hours_exceed_10_limit' in r['warnings'] for r in x['blimit']['allocation_rows']))
check('slot semantics declare secondary eligibility and warning-only B cap', x['blimit']['semantics']['secondary_specialty_participates_in_slot_eligibility'] is True and x['blimit']['semantics']['b_assignment_10_hour_limit_is_warning_only'] is True)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
