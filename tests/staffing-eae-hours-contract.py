#!/usr/bin/env python3
from pathlib import Path
import subprocess, json, re, html
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
SPEC=ROOT/'includes'/'teacher-specialties.php'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

# Shared registry / workload recognition for .50 EAE codes.
php=r'''<?php
require %s;
require %s;
$codes=array('ΠΕ02.50','ΠΕ03.50','ΠΕ04.50');
$out=array();
foreach($codes as $c){$out[$c]=array('label'=>teacherSpecialtyLabel($c),'eae'=>teacherSpecialtyIsEaeCode($c));}
$p=personnelWorkloadNormalizePerson(array(
 'person_id'=>'eae-1','display_name'=>'ΜΠΟΥΝΙΑΣ ΓΕΩΡΓΙΟΣ','specialty_code'=>'ΠΕ03.50','role'=>'teacher',
 'required_teaching_hours'=>'23','assigned_external_hours'=>0,'obligation_source'=>'myschool_stat4_8',
 'source_base_required_hours'=>23,'source_reduction_hours'=>0,'source_hours_at_unit'=>23
));
$out['person']=$p;
echo json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % (repr(str(SPEC)),repr(str(ROOT/'includes'/'personnel-workload.php')))
r=subprocess.run(['php'],cwd=ROOT,text=True,input=php,capture_output=True)
try: obj=json.loads(r.stdout)
except Exception: obj={}
check('.50 labels are registered', all(obj.get(c,{}).get('label') for c in ['ΠΕ02.50','ΠΕ03.50','ΠΕ04.50']))
check('.50 codes are classified as EAE', all(obj.get(c,{}).get('eae') is True for c in ['ΠΕ02.50','ΠΕ03.50','ΠΕ04.50']))
p=obj.get('person',{})
check('PE03.50 myschool row resolves', p.get('status')=='resolved' and p.get('obligation',{}).get('valid') is True)
check('PE03.50 keeps 23 available hours', p.get('remaining_before_profile_hours')==23 and p.get('required_teaching_hours')==23)

php_leaf=r'''<?php
require %s;
$codes=schoolProfileWorkloadStaffingCodes();
echo json_encode($codes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % repr(str(ROOT/'includes'/'school-profile-workload.php'))
rl=subprocess.run(['php'],cwd=ROOT,text=True,input=php_leaf,capture_output=True)
try: leaf=json.loads(rl.stdout)
except Exception: leaf=[]
check('general PE03 remains a workload leaf', 'ΠΕ03' in leaf)
check('PE03.50 is not a General workload leaf', 'ΠΕ03.50' not in leaf)

# Render a realistic gymnasium state with one general PE03 and the user's PE03.50 EAE row.
personnel={
 'personnel_person_id':['gen-1','myschool-2401010-eae'],
 'personnel_display_name':['ΓΕΝΙΚΗΣ ΜΑΘΗΜΑΤΙΚΟΣ','ΜΠΟΥΝΙΑΣ ΓΕΩΡΓΙΟΣ'],
 'personnel_specialty_code':['ΠΕ03','ΠΕ03.50'],
 'personnel_secondary_specialty_code':['',''],
 'personnel_required_teaching_hours':['21','23'],
 'personnel_service_years':['0','0'],'personnel_service_months':['0','0'],'personnel_service_days':['0','0'],
 'personnel_role':['teacher','teacher'],
 'personnel_assigned_external_hours':['0','0'],
 'personnel_director_sections_band':['',''],'personnel_hours_branch':['',''],
 'personnel_obligation_source':['manual','myschool_stat4_8'],
 'personnel_source_base_required_hours':['','23'],
 'personnel_source_reduction_hours':['','0'],
 'personnel_source_hours_at_unit':['','23'],
}
post={
 'school_type':'gymnasio','school_name':'1ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ','school_code':'2401010',
 'gym_general_a':1,'gym_general_b':1,'gym_general_c':1,
 'gym_lang_a_fr':1,'gym_lang_a_de':0,'gym_lang_a_it':0,
 'gym_lang_b_fr':1,'gym_lang_b_de':0,'gym_lang_b_it':0,
 'gym_lang_c_fr':1,'gym_lang_c_de':0,'gym_lang_c_it':0,
 'staffing_action':'allocation','active_panel':'allocation',
 'personnel_payload_json':json.dumps(personnel,ensure_ascii=False),
 'allocation_payload_json':json.dumps({'allocation_person_id':[],'allocation_slot_id':[],'allocation_hours':[]},ensure_ascii=False),
}
payload=json.dumps(post,ensure_ascii=False)
php_render='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+',true); include '+repr(str(PAGE))+';'
rr=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=php_render,capture_output=True,timeout=30)
out=rr.stdout
check('EAE hours chip is rendered', 'Διαθέσιμες ώρες ΕΑΕ' in out and re.search(r'<strong>23</strong><span>Διαθέσιμες ώρες ΕΑΕ',out) is not None)
check('EAE code no longer unresolved', re.search(r'<strong>0</strong><span>εγγραφές που χρειάζονται συμπλήρωση</span>',out) is not None)
check('EAE specialty remains selected after POST', 'value="ΠΕ03.50" selected' in out)
check('UI explains EAE exclusion from General allocation', 'δεν συμμετέχουν στην αυτόματη κατανομή μαθημάτων Γενικής Εκπαίδευσης' in out)

m=re.search(r'<script type="application/json" id="staffingRuntimeConfig">(.*?)</script>',out,re.S)
config={}
if m:
    try: config=json.loads(html.unescape(m.group(1)))
    except Exception: config={}
people=config.get('allocationPeople',{})
# Runtime may serialize the PHP associative array as object.
check('general PE03 remains available to allocator', 'gen-1' in people)
check('PE03.50 EAE is excluded from General allocator', 'myschool-2401010-eae' not in people)

# Exact regression for the reported path: automatic allocation after a stat4_8
# roster containing PE03.50 and an untouched blank allocation row.
auto_post=dict(post)
auto_post['staffing_action']='allocation_auto'
auto_post['allocation_payload_json']=json.dumps({
    'allocation_person_id':[''],
    'allocation_slot_id':[''],
    'allocation_hours':['0'],
},ensure_ascii=False)
auto_payload=json.dumps(auto_post,ensure_ascii=False)
auto_php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(auto_payload,ensure_ascii=False)+',true); include '+repr(str(PAGE))+';'
auto_r=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=auto_php,capture_output=True,timeout=30)
auto_out=auto_r.stdout
check('automatic allocation with PE03.50 does not create invalid blank slot error', 'Δεν έχει επιλεγεί έγκυρο τμήμα / ομάδα και μάθημα.' not in auto_out)
check('automatic allocation with PE03.50 completes', auto_r.returncode==0 and 'Αυτόματη πρόταση' in auto_out)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
