#!/usr/bin/env python3
from pathlib import Path
import subprocess,json,re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
post={
'school_type':'gymnasio','school_name':'CSV Contract Gym','gym_general_a':1,'gym_general_b':1,'gym_general_c':1,
'gym_lang_a_fr':1,'gym_lang_a_de':0,'gym_lang_a_it':0,'gym_lang_b_fr':1,'gym_lang_b_de':0,'gym_lang_b_it':0,'gym_lang_c_fr':1,'gym_lang_c_de':0,'gym_lang_c_it':0,
'staffing_action':'personnel','active_panel':'personnel'
}
payload=json.dumps(post,ensure_ascii=False)
php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
r=subprocess.run(['php'],cwd=ROOT,text=True,input=php,capture_output=True)
if r.returncode: print(r.stderr);raise SystemExit(r.returncode)
out=r.stdout
text=PAGE.read_text(encoding='utf-8')
check('CSV import button', 'id="openPersonnelCsv"' in out and 'Εισαγωγή CSV' in out)
check('CSV template button', 'id="downloadPersonnelCsvTemplate"' in out and 'Λήψη προτύπου CSV' in out)
check('local-only privacy note', 'Το αρχείο διαβάζεται μόνο στον browser σου.' in out and 'Δεν μεταφορτώνεται στον διακομιστή.' in out)
check('CSV file input accepts csv', 'id="personnelCsvFile"' in out and 'accept=".csv,text/csv,text/plain"' in out)
check('column mapping panel', 'id="personnelCsvMappings"' in out and 'Αντιστοίχιση στηλών CSV' in out)
check('preview exists', 'id="personnelCsvPreview"' in out)
check('append and replace modes', 'Προσθήκη στις υπάρχουσες εγγραφές' in out and 'Αντικατάσταση του προσωρινού προσωπικού' in out)
check('import JS asset included', 'includes/personnel-csv-import.js' in out)
check('three delimiters documented', 'semicolon (;)' in out and 'κόμμα ή tab' in out)
check('browser FileReader used', 'new FileReader()' in text and 'readAsArrayBuffer' in text)
check('UTF-8 and windows-1253 fallback', "TextDecoder('utf-8'" in text and "TextDecoder('windows-1253')" in text)
check('unknown specialties guarded', 'δεν αναγνωρίζεται από τις διαθέσιμες αναθέσεις' in text)
check('template is UTF8 BOM semicolon CSV', "\\uFEFFΚλάδος;Ονοματεπώνυμο;Έτη υπηρεσίας" in text)
check('CSV does not require form upload enctype', 'enctype="multipart/form-data"' not in out)
check('CSV does not ask director section band', 'Κλίμακα τμημάτων Διευθυντή/ντριας' not in out and 'Κλίμακα τμημάτων διευθυντή' not in text)
failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
