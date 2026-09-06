#!/usr/bin/env python3
from pathlib import Path
import subprocess, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
JS=ROOT/'includes/school-profile-csv-import.js'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

page=PAGE.read_text(encoding='utf-8')
js=JS.read_text(encoding='utf-8')
render=subprocess.run(['php',str(PAGE)],cwd=ROOT,text=True,capture_output=True)
check('page renders', render.returncode==0)
html=render.stdout
check('school CSV UI exists', 'id="openSchoolCsv"' in html and 'id="schoolCsvFile"' in html and 'id="schoolCsvPreview"' in html)
check('portable school registry schema exposed', 'school_registry_v1' in html and "registrySchemaVersion:'school_registry_v1'" in js)
check('stable school registry id carried by form', 'name="school_registry_id"' in html and "school_registry_id:school.school_id || ''" in js)
check('multiple-school capability declared', 'supportsMultipleSchools:true' in js)
check('current supported school types remain enabled', re.search(r'<option value="gymnasio"[^>]*>Ημερήσιο Γυμνάσιο</option>',html) is not None and re.search(r'<option value="gel"[^>]*>Ημερήσιο Γενικό Λύκειο \(ΓΕΛ\)</option>',html) is not None)
check('future school-type placeholders are disabled', all(fragment in html for fragment in [
    '<option value="esperino_gymnasio" disabled>Εσπερινό Γυμνάσιο</option>',
    '<option value="esperino_gel" disabled>Εσπερινό ΓΕΛ</option>',
    '<option value="epal" disabled>ΕΠΑΛ</option>',
    '<option value="pepal" disabled>Πρότυπο ΕΠΑΛ</option>',
    '<option value="eneegyl" disabled>ΕΝ.Ε.Ε.ΓΥ.-Λ.</option>',
    '<option value="eeeek" disabled>Ε.Ε.Ε.ΕΚ.</option>',
    '<option value="mousiko" disabled>Μουσικό Σχολείο</option>',
    '<option value="kallitexniko" disabled>Καλλιτεχνικό Σχολείο</option>',
]))
check('school CSV importer uses cache-busted asset helper', "edu_asset_url('includes/school-profile-csv-import.js')" in page)
check('school CSV remains client-side only', 'FileReader' in page and 'Δεν γίνεται μεταφόρτωση στον διακομιστή' in html)
check('school registry survives explicit recalculation in same browser tab', 'sessionStorage.setItem(schoolCsvStorageKey' in page and 'restoreSchoolCsvRegistry()' in page)
check('school registry can be cleared explicitly', 'id="clearSchoolCsvRegistry"' in html and 'sessionStorage.removeItem(schoolCsvStorageKey)' in page)
check('school CSV does not add request path', 'fetch(' not in page and 'location.reload' not in page and '.submit()' not in page and page.count('requestSubmit()')==1)

node=r'''
const csv=require('./includes/school-profile-csv-import.js');
const text='Έκδοση μητρώου;Αναγνωριστικό σχολείου;Ονομασία σχολείου;Είδος σχολείου;Α τμήματα;Β τμήματα;Γ τμήματα;Α Γαλλικά ομάδες;Β ομάδες Ανθρωπιστικών\r\nschool_registry_v1;s1;1ο Γυμνάσιο;Ημερήσιο Γυμνάσιο;2;2;1;1;\r\nschool_registry_v1;s2;1ο ΓΕΛ;Ημερήσιο ΓΕΛ;3;2;3;;1\r\nschool_registry_v1;s3;1ο ΕΠΑΛ;ΕΠΑΛ;4;3;3;;\r\n';
const parsed=csv.parse(text);
const map=csv.autoMap(parsed.headers);
const rows=parsed.rows.map((row,i)=>csv.rowToSchool(row,map,i));
const gelForm=csv.schoolToFormValues(rows[1]);
console.log(JSON.stringify({count:rows.length,types:rows.map(r=>r.school_type),supported:rows.map(r=>r.supported),gymA:rows[0].general_a,gelHum:rows[1].gel_b_hum,gelFormType:gelForm.school_type,gelFormA:gelForm.gel_general_a,gelFormId:gelForm.school_registry_id}));
'''
n=subprocess.run(['node'],input=node,text=True,capture_output=True,cwd=ROOT)
check('school CSV parser executes', n.returncode==0)
check('multiple rows parsed', '"count":3' in n.stdout)
check('school types normalize', '"types":["gymnasio","gel","epal"]' in n.stdout)
check('only implemented types loadable', '"supported":[true,true,false]' in n.stdout)
check('general sections preserved', '"gymA":2' in n.stdout and '"gelFormA":3' in n.stdout)
check('stable school id preserved into active form', '"gelFormId":"s2"' in n.stdout)
check('GEL specialist groups preserved', '"gelHum":1' in n.stdout)

failed=[name for name,ok in checks if not ok]
for name,ok in checks: print(('PASS' if ok else 'FAIL')+': '+name)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
