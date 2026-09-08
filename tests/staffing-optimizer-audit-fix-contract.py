#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=(ROOT/'ypologismos-didaktikon-anagkon.php').read_text(encoding='utf-8')
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
def php_json(code):
    return json.loads(subprocess.check_output(['php','-d','memory_limit=512M','-r',code],cwd=ROOT,text=True))

php=r'''
require "includes/teaching-allocation-engine.php";
require "includes/school-profile-general-education.php";
function sx($id,$h,$a=array(),$b=array(),$c=array(),$sp=array()){
 return array('slot_id'=>$id,'unit_id'=>$id,'slot_label'=>$id,'grade'=>'Α΄','subject'=>$id,'capacity_hours'=>$h,
 'eligible_by_priority'=>array('A'=>$a,'B'=>$b,'C'=>$c,'SPECIAL'=>$sp),'top_priority'=>!empty($a)?'A':(!empty($sp)?'SPECIAL':(!empty($b)?'B':(!empty($c)?'C':null))));
}
// Audit counterexample: 7 available hours; greedy 6 is not optimal, 3+2+2 is.
$slots=array('s6'=>sx('s6',6,array('ΠΕ03')),'s3'=>sx('s3',3,array('ΠΕ03')),'s2a'=>sx('s2a',2,array('ΠΕ03')),'s2b'=>sx('s2b',2,array('ΠΕ03')));
$people=array(array('person_id'=>'m','specialty_code'=>'ΠΕ03'));
$ps=array('m'=>array('remaining_hours'=>7,'b_assignment_hours'=>0,'b_remaining_hours'=>10));
$ss=array();foreach($slots as $sid=>$sl)$ss[$sid]=array('remaining_hours'=>$sl['capacity_hours']);
$synthetic=teachingAllocationEngineSolveRemaining($slots,$people,$ps,$ss);

// Equal coverage: SPECIAL must beat B after coverage is fixed.
$slots2=array('x'=>sx('x',4,array(),array('ΠΕ02'),array(),array('ΠΕ01')));
$people2=array(array('person_id'=>'sp','specialty_code'=>'ΠΕ01'),array('person_id'=>'b','specialty_code'=>'ΠΕ02'));
$ps2=array('sp'=>array('remaining_hours'=>4,'b_assignment_hours'=>0,'b_remaining_hours'=>10),'b'=>array('remaining_hours'=>4,'b_assignment_hours'=>0,'b_remaining_hours'=>10));
$ss2=array('x'=>array('remaining_hours'=>4));
$special=teachingAllocationEngineSolveRemaining($slots2,$people2,$ps2,$ss2);

$p=schoolProfileBuildDayGel2026(array(
 'profile_id'=>'audit-opt','school'=>array('type'=>'Ημερήσιο ΓΕΛ','name'=>'Audit GEL'),
 'general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
 'second_foreign_language_groups'=>array('Α΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0),'Β΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0)),
 'orientation_sections'=>array('Β΄'=>array('humanities'=>1,'science'=>1),'Γ΄'=>array('humanities'=>1,'science_health'=>1,'economics_it'=>1)),
 'grade_c_science_health_field_groups'=>array('Μαθηματικά'=>1,'Βιολογία'=>1),
 'grade_c_conditional_groups'=>array('Μαθηματικά'=>0,'Ιστορία'=>0),'ethics_by_grade'=>array()
));
$p7=array(array('person_id'=>'m','display_name'=>'M','specialty_code'=>'ΠΕ03','required_teaching_hours'=>7,'role'=>'teacher','assigned_external_hours'=>0));
$p6=array(array('person_id'=>'m','display_name'=>'M','specialty_code'=>'ΠΕ03','required_teaching_hours'=>6,'role'=>'teacher','assigned_external_hours'=>0));
$tab4=teachingAllocationEngineProposal($p,$p7);
$tab6_7=personnelWorkloadAutomaticBalanceProposal($p,$p7);
$tab6_6=personnelWorkloadAutomaticBalanceProposal($p,$p6);
echo json_encode(array('synthetic'=>$synthetic,'special'=>$special,'tab4'=>$tab4,'tab6_7'=>$tab6_7,'tab6_6'=>$tab6_6),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
x=php_json(php)

syn=x['synthetic']
check('optimizer fixes 6-vs-3+2+2 counterexample', syn['summary']['covered_hours']==7 and sorted(a['hours'] for a in syn['allocations'])==[2,2,3])
check('single-teacher optimum is certified', syn['summary']['maximum_coverage_certified'] is True)
check('atomic slots remain whole in optimal combination', all(a['hours'] in (2,3,6) for a in syn['allocations']) and not any(a['slot_id']=='s6' for a in syn['allocations']))
sp=x['special']
check('SPECIAL is preferred to B at equal coverage', sp['summary']['covered_hours']==4 and len(sp['allocations'])==1 and sp['allocations'][0]['person_id']=='sp' and sp['allocations'][0]['priority']=='SPECIAL')
check('real GEL Tab 4 covers all seven available PE03 hours', x['tab4']['summary']['auto_covered_hours']==7 and x['tab4']['optimizer_state']['summary']['maximum_coverage_certified'] is True)
check('Tab 6 shares optimizer and also covers seven', x['tab6_7']['summary']['auto_covered_hours']==7 and x['tab6_7']['semantics']['shared_optimizer_with_tab4'] is True and x['tab6_7']['summary']['maximum_coverage_certified'] is True)
check('Tab 6 fixes six-hour audit counterexample', x['tab6_6']['summary']['auto_covered_hours']==6 and x['tab6_6']['summary']['remaining_personnel_hours']==0)

check('live Tab 6 no longer uses partial Math.min slot filling', 'Math.min(remaining,available)' not in PAGE)
check('live Tab 6 marks partially locked slots atomic-blocked', 'atomic_blocked:assigned>0&&remaining>0' in PAGE)
check('live Tab 6 includes exact optimizer path', 'automatic_live_optimizer_dp' in PAGE and 'automatic_live_optimizer' in PAGE)

m=re.search(r"function schoolGeneralSectionCount\(\)\{.*?\n  \}",PAGE,re.S)
check('director section-count helper found', m is not None)
if m:
    fn=m.group(0)
    js=r'''
const values={}; let currentType='gymnasio';
global.document={
 getElementById:(id)=>id==='school_type'?{value:currentType}:null,
 querySelector:(sel)=>{const m=sel.match(/name="([^"]+)"/);return m&&Object.prototype.hasOwnProperty.call(values,m[1])?{value:String(values[m[1]])}:null;}
};
'''+fn+r'''
function run(type,v){currentType=type;Object.keys(values).forEach(k=>delete values[k]);Object.assign(values,v);return schoolGeneralSectionCount();}
console.log(JSON.stringify({
 egel:run('esperino_gel',{gel_general_a:2,gel_general_b:3,gel_general_c:4,gym_general_a:99,gym_general_b:99,gym_general_c:99}),
 composite:run('gymnasio_lt',{gym_general_a:1,gym_general_b:2,gym_general_c:3,gel_general_a:4,gel_general_b:5,gel_general_c:6}),
 gym:run('gymnasio',{gym_general_a:1,gym_general_b:2,gym_general_c:3,gel_general_a:50,gel_general_b:50,gel_general_c:50})
}));
'''
    vals=json.loads(subprocess.check_output(['node','-e',js],cwd=ROOT,text=True))
    check('Evening GEL director count uses GEL sections', vals['egel']==9)
    check('Gymnasium with Lyceum Classes director count sums both structures', vals['composite']==21)
    check('Gymnasium director count ignores GEL fields', vals['gym']==6)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
