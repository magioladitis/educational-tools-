#!/usr/bin/env python3
from pathlib import Path
import subprocess,json,re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
WORKLOAD=ROOT/'includes/personnel-workload.php'
MODULE=ROOT/'includes/myschool-staff-import.js'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
post={
 'school_type':'gymnasio','school_name':'2ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ','school_code':'2401020',
 'gym_general_a':4,'gym_general_b':4,'gym_general_c':3,
 'gym_lang_a_fr':2,'gym_lang_a_de':2,'gym_lang_a_it':0,
 'gym_lang_b_fr':1,'gym_lang_b_de':3,'gym_lang_b_it':0,
 'gym_lang_c_fr':1,'gym_lang_c_de':2,'gym_lang_c_it':0,
 'staffing_action':'personnel','active_panel':'personnel',
 'personnel_person_id':['myschool-2401020-test'],
 'personnel_display_name':['ΔΟΚΙΜΗ ΔΙΕΥΘΥΝΤΗ'],
 'personnel_specialty_code':['ΠΕ02'],'personnel_secondary_specialty_code':[''],
 'personnel_required_teaching_hours':['5'],'personnel_service_years':['0'],'personnel_service_months':['0'],'personnel_service_days':['0'],
 'personnel_role':['director'],'personnel_assigned_external_hours':['0'],'personnel_hours_branch':[''],
 'personnel_obligation_source':['myschool_stat4_8'],'personnel_source_base_required_hours':['18'],
 'personnel_source_reduction_hours':['13'],'personnel_source_hours_at_unit':['18']
}
payload=json.dumps(post,ensure_ascii=False)
php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
r=subprocess.run(['php'],cwd=ROOT,text=True,input=php,capture_output=True)
if r.returncode: print(r.stderr); raise SystemExit(r.returncode)
out=r.stdout;text=PAGE.read_text(encoding='utf-8');work=WORKLOAD.read_text(encoding='utf-8')
check('myschool button exists','id="openMySchoolStaff"' in out and 'myschool stat4_8' in out)
check('ZIP and CSV file input','id="mySchoolStaffFile"' in out and 'accept=".zip,.csv,application/zip,text/csv,text/plain"' in out)
check('direct raw export message','Δεν χρειάζεται προσαρμογή του export.' in out)
check('local-only privacy','μόνο τοπικά στον browser' in out and '<code>sessionStorage</code>' in out)
check('sensitive data explicitly discarded','Α.Μ., Α.Φ.Μ., τηλέφωνα, email, διευθύνσεις και πράξεις τοποθέτησης δεν αποθηκεύονται' in out)
check('myschool asset included','includes/myschool-staff-import.js' in out and MODULE.exists())
check('current school loader exists','id="loadMySchoolStaffForSchool"' in out and 'Φόρτωση προσωπικού τρέχοντος σχολείου' in out)
check('clean export exists','id="downloadCleanMySchoolStaff"' in out and 'Λήψη καθαρισμένου CSV' in out)
check('session clear exists','id="clearMySchoolStaff"' in out and 'Καθαρισμός μητρώου' in out)
check('source hidden fields preserved','name="personnel_obligation_source[]"' in out and 'value="myschool_stat4_8"' in out)
check('source required hours rendered','name="personnel_required_teaching_hours[]"' in out and 'value="5"' in out)
check('source rule shown','Υ.Ω. από myschool stat4_8.' in out and 'Βάση 18 − μείωση 13 = 5 ώρες.' in out)
check('source override backend explicit',"$obligationSource === 'myschool_stat4_8'" in work and "hours_branch_mode'=>'myschool_source_required_hours'" in work)
check('raw archive never uploaded server-side','readAsArrayBuffer(file)' in text and 'enctype="multipart/form-data"' not in out)
check('session registry functions wired','EducationMySchoolStaff.loadSession' in text and 'EducationMySchoolStaff.saveSession' in text and 'EducationMySchoolStaff.forSchool' in text)
check('clean registry uses synthetic ids','myschool-'+'' in MODULE.read_text(encoding='utf-8') and 'source_am' not in MODULE.read_text(encoding='utf-8'))

php2='''<?php
require %s;
$p=personnelWorkloadNormalizePerson(array(
 'person_id'=>'m1','display_name'=>'Imported director','specialty_code'=>'ΠΕ02','role'=>'director',
 'required_teaching_hours'=>'5','assigned_external_hours'=>0,'obligation_source'=>'myschool_stat4_8',
 'source_base_required_hours'=>18,'source_reduction_hours'=>13,'source_hours_at_unit'=>18
));
echo json_encode($p,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % repr(str(WORKLOAD))
r2=subprocess.run(['php'],cwd=ROOT,text=True,input=php2,capture_output=True)
try: obj=json.loads(r2.stdout)
except Exception: obj={}
check('source director resolves without service years/section band',r2.returncode==0 and obj.get('status')=='resolved' and obj.get('required_teaching_hours')==5)
# personnelWorkloadNormalizePerson nests obligation; verify there too
check('source mode recorded',obj.get('obligation',{}).get('hours_branch_mode')=='myschool_source_required_hours')

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
