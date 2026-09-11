#!/usr/bin/env python3
from pathlib import Path
import subprocess, json, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
UI=ROOT/'includes'/'staffing-simulator-ui.js'
WORKLOAD=ROOT/'includes/personnel-workload.php'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

post={
'school_type':'gymnasio','school_name':'Registry Contract Gym',
'gym_general_a':1,'gym_general_b':1,'gym_general_c':1,
'gym_lang_a_fr':1,'gym_lang_a_de':0,'gym_lang_a_it':0,
'gym_lang_b_fr':1,'gym_lang_b_de':0,'gym_lang_b_it':0,
'gym_lang_c_fr':1,'gym_lang_c_de':0,'gym_lang_c_it':0,
'staffing_action':'personnel','active_panel':'personnel',
'personnel_person_id':['registry-1'],
'personnel_display_name':['Δοκιμή Δεύτερης Ειδικότητας'],
'personnel_specialty_code':['ΠΕ03'],
'personnel_secondary_specialty_code':['ΠΕ86'],
'personnel_required_teaching_hours':[20],
'personnel_service_years':[0],
'personnel_service_months':[0],
'personnel_service_days':[0],
'personnel_role':['teacher'],
'personnel_assigned_external_hours':[2],
'personnel_hours_branch':['']
}
payload=json.dumps(post,ensure_ascii=False)
php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
r=subprocess.run(['php'],cwd=ROOT,text=True,input=php,capture_output=True)
if r.returncode:
    print(r.stderr); raise SystemExit(r.returncode)
out=r.stdout
text=(PAGE.read_text(encoding='utf-8')+'\n'+UI.read_text(encoding='utf-8'))

check('secondary specialty field rendered', 'name="personnel_secondary_specialty_code[]"' in out and 'class="personnel-secondary-specialty"' in out)
check('secondary specialty preserved on POST', re.search(r'name="personnel_secondary_specialty_code\[\]"[^>]*>.*?<option value="ΠΕ86" selected',out,re.S) is not None)
check('same person keeps primary and secondary in search metadata', 'data-search="ΠΕ03 ΠΕ86 Δοκιμή Δεύτερης Ειδικότητας"' in out)
check('portable registry export action exists', 'id="exportPersonnelRegistryCsv"' in out and 'type="button"' in out)
check('portable registry has schema marker', "'staff_registry_v1'" in text and "'Έκδοση μητρώου'" in text)
check('portable registry exports secondary specialty', "value('.personnel-secondary-specialty')" in text)
check('portable registry exports stable person id', "value('input[name=\"personnel_person_id[]\"]')" in text)
check('portable registry omits irrelevant personal ids', 'ΑΦΜ' not in text and 'Αριθμός Μητρώου' not in text)
check('secondary specialty participates in personnel filter', "(secondary?secondary.value:'')" in text)
check('secondary specialty participates in allocation eligibility logic', '/ 2η ' in text and 'allocationBestAssignment' in text and 'personnelWorkloadBestAssignmentForSlot' in WORKLOAD.read_text(encoding='utf-8'))

php2='''<?php
require %s;
$p=personnelWorkloadNormalizePerson(array(
  'person_id'=>'r1','display_name'=>'Registry','specialty_code'=>'ΠΕ03','secondary_specialty_code'=>'ΠΕ86',
  'required_teaching_hours'=>'20','role'=>'teacher','assigned_external_hours'=>0
));
echo json_encode($p, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % repr(str(WORKLOAD))
r2=subprocess.run(['php'],cwd=ROOT,text=True,input=php2,capture_output=True)
check('workload normalization executes', r2.returncode==0)
try:
    obj=json.loads(r2.stdout)
except Exception:
    obj={}
check('workload normalization preserves secondary specialty', obj.get('secondary_specialty_code')=='ΠΕ86')

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
