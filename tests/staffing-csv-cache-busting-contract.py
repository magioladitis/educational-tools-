#!/usr/bin/env python3
from pathlib import Path
import subprocess,re
ROOT=Path(__file__).resolve().parents[1]
CONFIG=ROOT/'includes/config.php'
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
UI=ROOT/'includes'/'staffing-simulator-ui.js'
CSVJS=ROOT/'includes/personnel-csv-import.js'
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
config=CONFIG.read_text(encoding='utf-8')
page=(PAGE.read_text(encoding='utf-8')+'\n'+UI.read_text(encoding='utf-8'))
js=CSVJS.read_text(encoding='utf-8')
check('asset helper uses filemtime cache key', 'filemtime($localPath)' in config and "$version .= '-'" in config)
version_match=re.search(r"define\('EDU_TOOLS_VERSION',\s*'([^']+)'\)",config)
release_version=version_match.group(1) if version_match else ''
check('release version is declared', bool(release_version))
check('CSV importer declares manual-hours capability', 'supportsManualRequiredTeachingHours:true' in js)
check('CSV importer declares secondary-specialty capability', 'supportsSecondarySpecialty:true' in js)
check('CSV importer carries schema version', "schemaVersion:'2026-09-05-staff-registry-v1'" in js and "registrySchemaVersion:'staff_registry_v1'" in js)
check('page guards stale cached importer', 'personnelCsvImporterSupportsRegistry' in page and 'παλαιότερη cached έκδοση του CSV importer' in page)
php_code="<?php require " + repr(str(CONFIG)) + "; echo edu_asset_url('includes/personnel-csv-import.js');"
r=subprocess.run(['php'],input=php_code,text=True,capture_output=True,cwd=ROOT)
check('asset helper executes', r.returncode==0)
url=r.stdout.strip()
expected=str(int(CSVJS.stat().st_mtime))
check('CSV importer URL includes release and file mtime', bool(release_version) and url.startswith('includes/personnel-csv-import.js?v='+release_version+'-') and url.endswith(expected))
node_code=r"""
const csv=require('./includes/personnel-csv-import.js');
const text='Κλάδος;Ονοματεπώνυμο;Υποχρεωτικό ωράριο;Ρόλος;Έτη υπηρεσίας;Μήνες;Ημέρες;Ώρες αλλού\r\nΠΕ03;Μαρία Παπαδοπούλου;20;Εκπαιδευτικός;;;;0\r\nΠΕ02;Γιώργος Διευθυντής;;Διευθυντής;20;0;0;0\r\nΠΕ01;Μαρία Παπαδοπούλου;21;Εκπαιδευτικός;;;;0\r\nΠΕ03;Μαρία Παπαδοπούλου;23;Εκπαιδευτικός;;;;0\r\nΠΕ03;Μαρία Παπαδοπούλου;23;Εκπαιδευτικός;;;;0\r\n';
const parsed=csv.parse(text);
const map=csv.autoMap(parsed.headers);
const rows=parsed.rows.map(r=>csv.rowToPersonnel(r,map));
console.log(JSON.stringify({map:map.required_teaching_hours,hours:rows.map(r=>r.required_teaching_hours),roles:rows.map(r=>r.role)}));
"""
n=subprocess.run(['node'],input=node_code,text=True,capture_output=True,cwd=ROOT)
check('uploaded sample parser executes', n.returncode==0)
check('uploaded sample maps required-hours header', '"map":"Υποχρεωτικό ωράριο"' in n.stdout)
check('uploaded sample keeps 20 blank 21 23 23', '"hours":[20,"",21,23,23]' in n.stdout)
failed=[name for name,ok in checks if not ok]
for name,ok in checks: print(('PASS' if ok else 'FAIL')+': '+name)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
