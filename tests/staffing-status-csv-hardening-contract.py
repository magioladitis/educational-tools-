#!/usr/bin/env python3
from pathlib import Path
import subprocess, json
ROOT=Path(__file__).resolve().parents[1]
page=(ROOT/'ypologismos-didaktikon-anagkon.php').read_text()+'\n'+(ROOT/'includes/staffing-simulator-ui.js').read_text()
js=(ROOT/'includes/school-profile-csv-import.js').read_text()
passed=failed=0

def check(label, cond):
    global passed, failed
    if cond:
        print('PASS:',label); passed+=1
    else:
        print('FAIL:',label); failed+=1

check('status bar has stable live allocation ids', 'id="staffingContextAssigned"' in page and 'id="staffingContextUnassigned"' in page)
check('allocation summary refreshes status bar live', "staffingContextAssigned.innerHTML='<strong>'+state.basicAssigned+'</strong> ώρες κατανεμημένες'" in page and "staffingContextUnassigned.innerHTML='<strong>'+state.unassigned+'</strong> ακάλυπτες'" in page)
check('live allocation chips preserve pre-allocation compact state', 'staffingContextInitialAllocation' in page and 'hasAllocationContext=hasAllocationSlots && (staffingContextInitialAllocation || allocationRows().length>0)' in page)
check('tab 6 CSV carries registry id', "['schema_version','school_registry_id','school_code','school_name'" in page and 'schoolRegistryId,schoolCode,schoolName' in page)
check('common spreadsheet formula hardening exists', 'function csvSpreadsheetSafeText(value)' in page and '/^[=+\\-@]/.test(probe)' in page)
check('school registry CSV uses hardening', 'const text=csvSpreadsheetSafeText(value);' in page[page.index('function schoolCsvEscape'):page.index('function downloadSchoolRegistryTemplate')])
check('personnel CSV uses hardening', 'const text=csvSpreadsheetSafeText(value);' in page[page.index('function personnelRegistryCsvEscape'):page.index('function personnelRegistryRoleLabel')])
check('tab 6 CSV uses hardening', 'csvSpreadsheetSafeText(value).replace' in page[page.index('function specialtyCsvCell'):page.index('function downloadSpecialtyBalanceCsv')])
check('short school type options render', '>Ημερήσιο Γενικό Λύκειο</option>' in page and '>Γυμνάσιο με Λ.Τ.</option>' in page and '>Εργαστηριακό Κέντρο</option>' in page)
check('restored school registry also uses canonical compact type label', 'const displaySchoolType=' in page and "[record.school_code||'—',displaySchoolType" in page)
check('CSV importer exposes short presentation labels', "gel:'Ημερήσιο Γενικό Λύκειο'" in js and "gymnasio_lt:'Γυμνάσιο με Λ.Τ.'" in js and "sek:'Εργαστηριακό Κέντρο'" in js)

node="""
const imp=require('./includes/school-profile-csv-import.js');
const out={
 gelOld:imp.normalizeSchoolType('Ημερήσιο Γενικό Λύκειο (ΓΕΛ)'),
 gelNew:imp.normalizeSchoolType('Ημερήσιο Γενικό Λύκειο'),
 compOld:imp.normalizeSchoolType('Γυμνάσιο με Λυκειακές Τάξεις'),
 compNew:imp.normalizeSchoolType('Γυμνάσιο με Λ.Τ.'),
 sekOld:imp.normalizeSchoolType('Εργαστηριακό Κέντρο (Ε.Κ.)'),
 sekNew:imp.normalizeSchoolType('Εργαστηριακό Κέντρο'),
 labels:[imp.typeLabel('gel'),imp.typeLabel('gymnasio_lt'),imp.typeLabel('sek')]
};
console.log(JSON.stringify(out));
"""
r=subprocess.run(['node','-e',node],cwd=ROOT,text=True,capture_output=True)
if r.returncode==0:
    out=json.loads(r.stdout)
    check('old and new GEL labels remain import-compatible', out['gelOld']=='gel' and out['gelNew']=='gel')
    check('old and new composite labels remain import-compatible', out['compOld']=='gymnasio_lt' and out['compNew']=='gymnasio_lt')
    check('old and new laboratory labels remain import-compatible', out['sekOld']=='sek' and out['sekNew']=='sek')
    check('presentation labels are compact', out['labels']==['Ημερήσιο Γενικό Λύκειο','Γυμνάσιο με Λ.Τ.','Εργαστηριακό Κέντρο'])
else:
    print(r.stderr); failed+=4

print(f'RESULT {passed} PASS / {failed} FAIL')
raise SystemExit(1 if failed else 0)
