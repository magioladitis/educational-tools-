#!/usr/bin/env python3
import json, pathlib, subprocess, sys, re
ROOT=pathlib.Path(__file__).resolve().parents[1]
FAIL=[]
def check(label, ok):
    print(('  ✔ ' if ok else '  ✘ ')+label)
    if not ok: FAIL.append(label)

page=(ROOT/'ypologismos-didaktikon-anagkon.php').read_text(encoding='utf-8')
ui=(ROOT/'includes/staffing-simulator-ui.js').read_text(encoding='utf-8')
module=(ROOT/'includes/personnel-workload-calculations.js').read_text(encoding='utf-8')
check('personnel button is client-first with server submit fallback', 'data-staffing-request-action="personnel" data-staffing-client-action="personnel"' in page and 'type="submit"' in page)
check('allocation shell has a client-unlock gate and workspace', 'id="allocationGateMessage"' in page and 'id="allocationWorkspace"' in page)
check('client transition uses shared normalizeRoster()', 'PersonnelWorkloadCalculations.normalizeRoster' in ui)
check('client transition synchronizes personnel payload for server fallback', 'syncPersonnelPayloadToAllocationFallback' in ui and 'personnel_payload_json' in ui)
check('client transition preserves fallback on module/schema failure', "if(!compatibility.ok)" in ui and "return false;" in ui)
check('pure workload module exports normalizeRoster', 'normalizeRoster: normalizeRoster' in module)

# Render a valid profile with no personnel: allocation DOM must already exist but stay gated.
post={
 'staffing_action':'profile','school_type':'gymnasio','school_name':'Phase4 Gym',
 'gym_general_a':'1','gym_general_b':'1','gym_general_c':'1',
 'gym_lang_a_fr':'1','gym_lang_a_de':'0','gym_lang_a_it':'0',
 'gym_lang_b_fr':'1','gym_lang_b_de':'0','gym_lang_b_it':'0',
 'gym_lang_c_fr':'1','gym_lang_c_de':'0','gym_lang_c_it':'0',
 'ethics_a_exempt':'0','ethics_a_timely':'1','ethics_b_exempt':'0','ethics_b_timely':'1','ethics_c_exempt':'0','ethics_c_timely':'1'
}
php=['<?php','$_SERVER["REQUEST_METHOD"]="POST";','$_POST='+repr(post).replace("'",'"').replace('True','true').replace('False','false')+';']
# Python repr is not PHP syntax for dict. Build PHP array safely.
parts=[]
for k,v in post.items(): parts.append(json.dumps(k,ensure_ascii=False)+'=>'+json.dumps(v,ensure_ascii=False))
code='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=array('+','.join(parts)+'); include '+json.dumps(str(ROOT/'ypologismos-didaktikon-anagkon.php'))+';'
r=subprocess.run(['php'],input=code,text=True,capture_output=True,cwd=ROOT)
if r.returncode:
    print(r.stderr); sys.exit(1)
html=r.stdout
check('profile-only render includes allocation list/template before personnel POST', 'id="allocationList"' in html and 'id="allocationRowTemplate"' in html)
check('profile-only render keeps allocation workspace hidden', re.search(r'id="allocationWorkspace" hidden',html) is not None)
check('profile-only render keeps allocation tab disabled', re.search(r'data-staffing-tab="allocation"[^>]*disabled',html) is not None)

# Progressive fallback remains fully functional: the old personnel POST must still unlock allocation.
fallback_post=dict(post)
fallback_post['staffing_action']='personnel'
fallback_post['personnel_payload_json']=json.dumps({
 'personnel_person_id':['fallback-p1'],'personnel_display_name':['Fallback Teacher'],'personnel_specialty_code':['ΠΕ03'],'personnel_secondary_specialty_code':[''],
 'personnel_required_teaching_hours':['21'],'personnel_service_years':['0'],'personnel_service_months':['0'],'personnel_service_days':['0'],'personnel_role':['teacher'],
 'personnel_assigned_external_hours':['0'],'personnel_director_sections_band':[''],'personnel_hours_branch':[''],'personnel_obligation_source':[''],
 'personnel_source_base_required_hours':[''],'personnel_source_reduction_hours':[''],'personnel_source_hours_at_unit':['']
},ensure_ascii=False)
parts=[]
for k,v in fallback_post.items(): parts.append(json.dumps(k,ensure_ascii=False)+'=>'+json.dumps(v,ensure_ascii=False))
code='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=array('+','.join(parts)+'); include '+json.dumps(str(ROOT/'ypologismos-didaktikon-anagkon.php'))+';'
fr=subprocess.run(['php'],input=code,text=True,capture_output=True,cwd=ROOT)
if fr.returncode: print(fr.stderr); sys.exit(1)
fallback_html=fr.stdout
check('server personnel fallback still unlocks allocation', re.search(r'data-staffing-tab="allocation"[^>]*disabled',fallback_html) is None and re.search(r'id="allocationWorkspace" hidden',fallback_html) is None)
check('server personnel fallback still renders allocation person summary', 'data-allocation-person-summary="fallback-p1"' in fallback_html)

rosters=[
 {'id':'general_plus_eae','sections':3,'people':[
   {'person_id':'p1','display_name':'Γενικός','specialty_code':'ΠΕ03','secondary_specialty_code':'','required_teaching_hours':'21','service':{'years':0,'months':0,'days':0},'role':'teacher','assigned_external_hours':'3'},
   {'person_id':'p2','display_name':'ΕΑΕ','specialty_code':'ΠΕ03.50','secondary_specialty_code':'','required_teaching_hours':'20','service':{'years':0,'months':0,'days':0},'role':'teacher','assigned_external_hours':'0'}]},
 {'id':'duplicate_director','sections':6,'people':[
   {'person_id':'d1','display_name':'Δ1','specialty_code':'ΠΕ03','secondary_specialty_code':'','service':{'years':20,'months':0,'days':0},'role':'director','assigned_external_hours':'0'},
   {'person_id':'d2','display_name':'Δ2','specialty_code':'ΠΕ02','secondary_specialty_code':'','service':{'years':20,'months':0,'days':0},'role':'director','assigned_external_hours':'0'}]},
 {'id':'valid_plus_unresolved','sections':3,'people':[
   {'person_id':'p1','display_name':'Έγκυρος','specialty_code':'ΠΕ02','secondary_specialty_code':'','required_teaching_hours':'20','service':{'years':0,'months':0,'days':0},'role':'teacher','assigned_external_hours':'0'},
   {'person_id':'p2','display_name':'Ελλιπής','specialty_code':'ΠΕ03','secondary_specialty_code':'','required_teaching_hours':'','service':{'years':0,'months':0,'days':0},'role':'teacher','assigned_external_hours':'0'}]},
 {'id':'myschool_and_secondary','sections':4,'people':[
   {'person_id':'p1','display_name':'myschool','specialty_code':'ΠΕ04.01','secondary_specialty_code':'ΠΕ04.02','required_teaching_hours':'18','service':{'years':14,'months':0,'days':0},'role':'teacher','assigned_external_hours':'2','obligation_source':'myschool_stat4_8','source_base_required_hours':'20','source_reduction_hours':'2','source_hours_at_unit':'18'}]}
]

php_code=r'''<?php
require %s;
$rosters=json_decode(%s,true);
$out=array('labels'=>array(),'rosters'=>array());
foreach($rosters as $case){
  $seen=false;$dups=array();$evals=array();
  $summary=array('people_count'=>0,'resolved_count'=>0,'unresolved_count'=>0,'required_hours'=>0,'external_hours'=>0,'available_here_hours'=>0,'general_resolved_count'=>0,'general_available_here_hours'=>0,'eae_people_count'=>0,'eae_available_here_hours'=>0,'by_code'=>array());
  $allocation=array();
  foreach($case['people'] as $idx=>$raw){
    $person=$raw;$duplicate=false;
    if(isset($person['role'])&&$person['role']==='director'){
      if($seen){$duplicate=true;$dups[]=$idx;}else{$seen=true;}
      $person['school_general_section_count']=$case['sections'];
    }
    $code=isset($person['specialty_code'])?teacherSpecialtyCanonicalCode($person['specialty_code']):'';
    if($code!==''&&!isset($out['labels'][$code]))$out['labels'][$code]=teacherSpecialtyLabel($code);
    $sec=isset($person['secondary_specialty_code'])?teacherSpecialtyCanonicalCode($person['secondary_specialty_code']):'';
    if($sec!==''&&!isset($out['labels'][$sec]))$out['labels'][$sec]=teacherSpecialtyLabel($sec);
    $norm=$duplicate?array('status'=>'invalid','valid'=>false,'reason'=>'multiple_directors_not_allowed','specialty_code'=>$code):personnelWorkloadNormalizePerson($person);
    $pid=isset($person['person_id'])?trim((string)$person['person_id']):'';if($pid!=='')$evals[$pid]=$norm;
    $summary['people_count']++;
    if($code!==''&&!isset($summary['by_code'][$code]))$summary['by_code'][$code]=array('code'=>$code,'label'=>teacherSpecialtyLabel($code),'people_count'=>0,'resolved_count'=>0,'required_hours'=>0,'external_hours'=>0,'available_here_hours'=>0);
    if($code!=='')$summary['by_code'][$code]['people_count']++;
    $eae=teacherSpecialtyIsEaeCode($code);if($eae)$summary['eae_people_count']++;
    if(!isset($norm['status'])||$norm['status']!=='resolved'){$summary['unresolved_count']++;continue;}
    $summary['resolved_count']++;$req=(int)$norm['required_teaching_hours'];$ext=(int)$norm['assigned_external_hours'];$avail=(int)$norm['remaining_before_profile_hours'];
    $summary['required_hours']+=$req;$summary['external_hours']+=$ext;$summary['available_here_hours']+=$avail;
    if($eae)$summary['eae_available_here_hours']+=$avail;else{$summary['general_resolved_count']++;$summary['general_available_here_hours']+=$avail;}
    if($code!==''){$summary['by_code'][$code]['resolved_count']++;$summary['by_code'][$code]['required_hours']+=$req;$summary['by_code'][$code]['external_hours']+=$ext;$summary['by_code'][$code]['available_here_hours']+=$avail;}
    if(!$duplicate&&!$eae&&$pid!==''){
      $name=isset($person['display_name'])?trim((string)$person['display_name']):'';$codes=$code.($sec!==''?' / 2η '.$sec:'');
      $allocation[$pid]=array('person_id'=>$pid,'display_name'=>$name,'label'=>trim($codes.' · '.($name!==''?$name:'Χωρίς ονοματεπώνυμο'),' ·'),'specialty_code'=>$code,'secondary_specialty_code'=>$sec,'required_hours'=>$req,'external_hours'=>$ext,'available_here_hours'=>$avail);
    }
  }
  $out['rosters'][$case['id']]=array('evaluations'=>$evals,'duplicate_director_indexes'=>$dups,'summary'=>$summary,'allocation_people'=>$allocation,'allocation_enabled'=>empty($dups)&&$summary['general_resolved_count']>0&&!empty($allocation));
}
echo json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % (json.dumps(str(ROOT/'includes/personnel-workload.php')),json.dumps(json.dumps(rosters,ensure_ascii=False)))
pr=subprocess.run(['php'],input=php_code,text=True,capture_output=True,cwd=ROOT)
if pr.returncode: print(pr.stderr); sys.exit(1)
php_out=json.loads(pr.stdout)

node_script=r'''
const fs=require('fs'),vm=require('vm');global.window=global;
vm.runInThisContext(fs.readFileSync(%s,'utf8'));vm.runInThisContext(fs.readFileSync(%s,'utf8'));
const rosters=%s, labels=%s, W=global.PersonnelWorkloadCalculations, out={};
const opt={specialtyLabels:labels,isKnownSpecialty:(c)=>Object.prototype.hasOwnProperty.call(labels,c)};
rosters.forEach(c=>{const r=W.normalizeRoster(c.people,Object.assign({school_general_section_count:c.sections},opt));out[c.id]={evaluations:r.evaluations,duplicate_director_indexes:r.duplicate_director_indexes,summary:r.summary,allocation_people:r.allocation_people,allocation_enabled:r.allocation_enabled};});
process.stdout.write(JSON.stringify(out));
''' % (json.dumps(str(ROOT/'includes/specialty-code-normalization.js')),json.dumps(str(ROOT/'includes/personnel-workload-calculations.js')),json.dumps(rosters,ensure_ascii=False),json.dumps(php_out['labels'],ensure_ascii=False))
nr=subprocess.run(['node','-e',node_script],text=True,capture_output=True,cwd=ROOT)
if nr.returncode: print(nr.stderr); sys.exit(1)
js_out=json.loads(nr.stdout)
for cid,expected in php_out['rosters'].items():
    check(cid+' full roster result matches PHP',js_out.get(cid)==expected)

if FAIL:
    print('\nPersonnel stage client transition: FAIL (%d)'%len(FAIL))
    for x in FAIL: print(' - '+x)
    sys.exit(1)
print('\nPersonnel stage client transition: PASS (%d checks)'%(11+len(rosters)))
