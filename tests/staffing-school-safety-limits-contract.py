#!/usr/bin/env python3
from pathlib import Path
import subprocess, json, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

def render(payload):
    php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(json.dumps(payload,ensure_ascii=False))+', true); include '+json.dumps(str(PAGE))+';'
    return subprocess.run(['php'],input=php,text=True,capture_output=True,cwd=ROOT)

page=PAGE.read_text(encoding='utf-8')
check('backend declares 200 basic-section cap', "STAFFING_UI_MAX_BASIC_SECTIONS', 200" in page)
check('frontend aggregates A+B+C for live validation', 'function syncBasicSectionLimit()' in page and "total>200" in page)
get_html=subprocess.run(['php',str(PAGE)],cwd=ROOT,text=True,capture_output=True).stdout
check('all six base section inputs carry native max 200', get_html.count('max="200" step="1" type="number" data-basic-section=')==6)
check('CSV importer rejects oversized rows before registry use', 'schoolRegistryValidationProblems(schoolCsvRegistry)' in page and 'υπέρβαση του ορίου των ' in page)
check('session-restored registries are revalidated', 'const problems=schoolRegistryValidationProblems(parsed);' in page and 'sessionStorage.removeItem(schoolCsvStorageKey);' in page)

base={'staffing_action':'profile','active_panel':'results','school_type':'gymnasio','gym_general_a':100,'gym_general_b':50,'gym_general_c':50}
r=render(base)
check('exact 200 basic sections still calculate', r.returncode==0 and '2. Αποτελέσματα ανά κλάδο' in r.stdout and 'Ο υπολογισμός δεν εκτελέστηκε.' not in r.stdout)

over=dict(base); over['gym_general_c']=51
r2=render(over)
check('201 basic sections are rejected server-side', r2.returncode==0 and 'Ο υπολογισμός δεν εκτελέστηκε.' in r2.stdout and 'υπερβαίνει το τεχνικό όριο ασφαλείας των 200 τμημάτων' in r2.stdout)
check('invalid oversized POST does not unlock result tab', re.search(r'data-staffing-tab="results"[^>]* disabled',r2.stdout) is not None)
check('oversized POST stays on school tab', re.search(r'data-staffing-tab="school"[^>]*aria-selected="true"',r2.stdout) is not None)

failed=[name for name,ok in checks if not ok]
for name,ok in checks: print(('PASS' if ok else 'FAIL')+': '+name)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
