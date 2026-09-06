#!/usr/bin/env python3
from pathlib import Path
import json, subprocess
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
def php_json(code):
    return json.loads(subprocess.check_output(['php','-d','memory_limit=1024M','-r',code],cwd=ROOT,text=True))

php=r'''
require "includes/teaching-allocation-engine.php";

function slotx($id,$hours,$a,$b=array(),$c=array()) {
  return array(
    'slot_id'=>$id,'unit_id'=>$id,'slot_label'=>$id,'grade'=>'Α΄','subject'=>$id,'capacity_hours'=>$hours,
    'eligible_by_priority'=>array('A'=>$a,'B'=>$b,'C'=>$c,'SPECIAL'=>array()),'top_priority'=>!empty($a)?'A':(!empty($b)?'B':(!empty($c)?'C':null))
  );
}

// 1. Κλασική παγίδα greedy: ο ΠΕ02 χρειάζεται στα Αρχαία, ενώ η Ιστορία
// μπορεί να καλυφθεί από ΠΕ78 με Β΄. Η μέγιστη κάλυψη είναι 6/6.
$slots1=array(
 'anc'=>slotx('anc',4,array('ΠΕ02')),
 'hist'=>slotx('hist',2,array('ΠΕ02'),array('ΠΕ78')),
);
$people1=array(
 array('person_id'=>'phil','specialty_code'=>'ΠΕ02'),
 array('person_id'=>'soc','specialty_code'=>'ΠΕ78'),
);
$ps1=array(
 'phil'=>array('remaining_hours'=>4,'b_assignment_hours'=>0,'b_remaining_hours'=>10),
 'soc'=>array('remaining_hours'=>2,'b_assignment_hours'=>0,'b_remaining_hours'=>10),
);
$ss1=array('anc'=>array('remaining_hours'=>4),'hist'=>array('remaining_hours'=>2));
$r1=teachingAllocationEngineSolveRemaining($slots1,$people1,$ps1,$ss1);

// 2. Ίδια κάλυψη με Α΄ ή Β΄: πρέπει να επιλεγεί Α΄.
$slots2=array('x'=>slotx('x',4,array('ΠΕ03'),array('ΠΕ04.01')));
$people2=array(array('person_id'=>'a','specialty_code'=>'ΠΕ03'),array('person_id'=>'b','specialty_code'=>'ΠΕ04.01'));
$ps2=array('a'=>array('remaining_hours'=>4,'b_assignment_hours'=>0,'b_remaining_hours'=>10),'b'=>array('remaining_hours'=>4,'b_assignment_hours'=>0,'b_remaining_hours'=>10));
$ss2=array('x'=>array('remaining_hours'=>4));
$r2=teachingAllocationEngineSolveRemaining($slots2,$people2,$ps2,$ss2);

// 3. Η αυτόματη πρόταση δεν υπερβαίνει το κανονικό όριο 10 ωρών Β΄.
$slots3=array(
 'b1'=>slotx('b1',5,array(),array('ΠΕ78')),
 'b2'=>slotx('b2',5,array(),array('ΠΕ78')),
 'b3'=>slotx('b3',2,array(),array('ΠΕ78'))
);
$people3=array(array('person_id'=>'b','specialty_code'=>'ΠΕ78'));
$ps3=array('b'=>array('remaining_hours'=>20,'b_assignment_hours'=>0,'b_remaining_hours'=>10));
$ss3=array('b1'=>array('remaining_hours'=>5),'b2'=>array('remaining_hours'=>5),'b3'=>array('remaining_hours'=>2));
$r3=teachingAllocationEngineSolveRemaining($slots3,$people3,$ps3,$ss3);

// 4. Τρία πραγματικά slots της ίδιας curriculum unit παραμένουν αδιαίρετα.
$proto=slotx('s1',4,array('ΠΕ03')); $proto['unit_id']='mathA';
$s2=$proto; $s2['slot_id']='s2'; $s2['slot_label']='A2';
$s3=$proto; $s3['slot_id']='s3'; $s3['slot_label']='A3';
$slots4=array('s1'=>$proto,'s2'=>$s2,'s3'=>$s3);
$people4=array(array('person_id'=>'m1','specialty_code'=>'ΠΕ03'),array('person_id'=>'m2','specialty_code'=>'ΠΕ03'));
$ps4=array('m1'=>array('remaining_hours'=>8,'b_assignment_hours'=>0,'b_remaining_hours'=>10),'m2'=>array('remaining_hours'=>4,'b_assignment_hours'=>0,'b_remaining_hours'=>10));
$ss4=array('s1'=>array('remaining_hours'=>4),'s2'=>array('remaining_hours'=>4),'s3'=>array('remaining_hours'=>4));
$r4=teachingAllocationEngineSolveRemaining($slots4,$people4,$ps4,$ss4);


// 4b. Απαγορεύεται split: 4ωρο μάθημα δεν καλύπτεται από 3+1 ώρες δύο εκπαιδευτικών.
$slots4b=array('m'=>slotx('m',4,array('ΠΕ03')));
$people4b=array(array('person_id'=>'m3','specialty_code'=>'ΠΕ03'),array('person_id'=>'m1','specialty_code'=>'ΠΕ03'));
$ps4b=array('m3'=>array('remaining_hours'=>3,'b_assignment_hours'=>0,'b_remaining_hours'=>10),'m1'=>array('remaining_hours'=>1,'b_assignment_hours'=>0,'b_remaining_hours'=>10));
$ss4b=array('m'=>array('remaining_hours'=>4));
$r4b=teachingAllocationEngineSolveRemaining($slots4b,$people4b,$ps4b,$ss4b);

// 5. Public wrapper: υπάρχουσα γραμμή παραμένει κλειδωμένη και συμπληρώνεται
// μόνο το υπόλοιπο πραγματικού Γυμνασίου.
require "includes/school-profile-general-education.php";
$p=schoolProfileBuildDayGymnasium2026(array(
 'profile_id'=>'engine-contract','school'=>array('type'=>'Ημερήσιο Γυμνάσιο','name'=>'Engine Contract'),
 'general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
 'second_foreign_language_groups'=>array('Α΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0),'Β΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0),'Γ΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0)),
 'technology_informatics_split_sections'=>array('Α΄'=>0,'Β΄'=>0,'Γ΄'=>0),
 'ethics_by_grade'=>array()
));
$people5=array(
 array('person_id'=>'m','display_name'=>'Math','specialty_code'=>'ΠΕ03','required_teaching_hours'=>20,'role'=>'teacher','assigned_external_hours'=>0),
 array('person_id'=>'p','display_name'=>'Phil','specialty_code'=>'ΠΕ02','required_teaching_hours'=>20,'role'=>'teacher','assigned_external_hours'=>0)
);
$locked=array(array('person_id'=>'m','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4));
$r5=teachingAllocationEngineProposal($p,$people5,$locked);
$bad=array(array('person_id'=>'p','slot_id'=>'gym.mathimatika@Α΄|whole|section|1','hours'=>4));
$r6=teachingAllocationEngineProposal($p,$people5,$bad);

echo json_encode(array('r1'=>$r1,'r2'=>$r2,'r3'=>$r3,'r4'=>$r4,'r4b'=>$r4b,'r5'=>$r5,'r6'=>$r6),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
x=php_json(php)

r1=x['r1']
check('max coverage beats greedy trap', r1['summary']['covered_hours']==6 and r1['summary']['remaining_slot_hours']==0)
by1={(a['slot_id'],a['person_id']):(a['hours'],a['priority']) for a in r1['allocations']}
check('scarce PE02 preserved for exclusive Ancient Greek', by1.get(('anc','phil'))==(4,'A'))
check('history falls to eligible B only when it increases total coverage', by1.get(('hist','soc'))==(2,'B'))

r2=x['r2']
check('A assignment wins when total coverage is equal', r2['summary']['covered_hours']==4 and len(r2['allocations'])==1 and r2['allocations'][0]['person_id']=='a' and r2['allocations'][0]['priority']=='A')

r3=x['r3']
check('automatic B assignment respects 10-hour limit', r3['summary']['covered_hours']==10 and r3['summary']['remaining_slot_hours']==2 and sum(a['hours'] for a in r3['allocations'])==10 and all(a['hours'] in (5,5) for a in r3['allocations']))

r4=x['r4']
check('atomic solver marks slot-level optimization', r4['summary']['atomic'] is True and r4['summary']['optimization_group_count']==3)
check('atomic optimization returns real slots', {a['slot_id'] for a in r4['allocations']}=={'s1','s2','s3'} and r4['summary']['covered_hours']==12)
per_slot={sid:0 for sid in ('s1','s2','s3')}
for a in r4['allocations']: per_slot[a['slot_id']]+=a['hours']
check('expanded proposal respects each slot capacity', all(v==4 for v in per_slot.values()))
r4b=x['r4b']
check('one course-section is never split across teachers', r4b['summary']['covered_hours']==0 and r4b['summary']['remaining_slot_hours']==4 and r4b['allocations']==[])

r5=x['r5']
check('public proposal succeeds', r5['status']=='ok')
check('existing manual rows are locked and preserved', r5['combined_allocations'][0]=={'person_id':'m','slot_id':'gym.mathimatika@Α΄|whole|section|1','hours':4} and r5['summary']['locked_hours']==4)
check('proposal adds only to remaining capacity', r5['summary']['auto_covered_hours']>0 and r5['summary']['final_assigned_hours']>=4+r5['summary']['auto_covered_hours'])
check('engine semantics are explicit', r5['semantics']['maximum_coverage_first'] is True and r5['semantics']['a_and_special_before_b_before_c'] is True and r5['semantics']['manual_changes_allowed_after_proposal'] is True and r5['semantics']['course_section_assignment_is_atomic'] is True)

r6=x['r6']
check('invalid locked allocations block optimization', r6['status']=='invalid_locked_allocations' and r6['summary']['auto_covered_hours']==0)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
