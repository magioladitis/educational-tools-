#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, tempfile, zipfile
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
UI=ROOT/'includes'/'staffing-simulator-ui.js'
MODULE=ROOT/'includes'/'myschool-stat51-import.js'
SRC=(PAGE.read_text(encoding='utf-8')+'\n'+UI.read_text(encoding='utf-8'))
MOD=MODULE.read_text(encoding='utf-8')
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

check('stat5_1 importer module is loaded', "includes/myschool-stat51-import.js" in SRC and 'EducationMySchoolStat51' in MOD)
check('comparison is optional and lives in tab five', 'Προαιρετικός έλεγχος με stat5_1 myschool' in SRC and 'id="stat51ComparePanel"' in SRC)
check('zip and csv input are accepted', 'accept=".zip,.csv,text/csv,application/zip"' in SRC)
check('comparison does not mutate calculations', 'δεν αλλάζει</strong> την κατανομή ή τα κενά του εργαλείου' in SRC)
check('comparison summary exposes ours myschool difference and agreements', all(x in SRC for x in ['data-stat51-ours','data-stat51-myschool','data-stat51-difference','data-stat51-agreements']))
check('comparison table exposes union diagnostics', all(x in SRC for x in ['Μόνο στο εργαλείο','Μόνο στο stat5_1','Συμφωνία']))
check('comparison refreshes from live allocation state', 'stat51RenderComparison(slotAssigned||{});' in SRC and 'function stat51LocalGroups(slotAssigned)' in SRC)
check('school identity uses school_code', 'stat51CurrentSchoolCode' in SRC and 'κωδικό myschool της Καρτέλας 1' in SRC)
check('choice option is exported to client slots for language matching', "'choice_option'=>isset($slot['choice_option']) ? $slot['choice_option'] : ''" in SRC)
check('stat5 snapshot warning is explicit', 'το stat5_1 είναι στιγμιότυπο του myschool' in SRC)
check('legacy windows-1253 decoding exists', "TextDecoder('windows-1253')" in MOD)
check('zip importer explicitly prefers stat5_1', '/stat5[_-]?1/i' in MOD)
check('formula school codes are normalized', 'cleanFormulaText' in MOD and 'normalizeSchoolCode' in MOD)
check('foreign-language level suffix is normalized', 'αρχαριοι|μεσοι|προχωρημενοι' in MOD)

headers=['Περιφέρεια','Διεύθυνση','Περιοχή Μετάθεσης','Ομάδα Σχολείου','Περιφερειακή Ενότητα','Δήμος','Δημοτική Ενότητα','Κοινότητα','Είδος Σχολείου','Τύπος Σχολείου','Κωδικός Μονάδας','Ονομασία Σχολείου','Τηλέφωνο','ΦΑΞ','Email','Αναστολή','Δυσπρόσιτο','Ενισχυτική Διδασκαλία','Τάξη','Τομέας Σπουδών','Μάθημα','Αριθμός Τμημάτων','Μέγιστες Εβδομαδιαίες Ώρες Μαθήματος','Συνολικές Ώρες Τμημάτων','Κάλυψη Αναθέσεων','Εκτίμηση Κενών από myschool','Εκτίμηση Κενών από μονάδα','Σχόλια','Ειδικότητες που έχουν το μάθημα ως Α ανάθεση','Ειδικότητες που έχουν το μάθημα ως Β ανάθεση']
row=['']*30
row[8]='Γυμνάσια'; row[9]='Ημερήσιο Γυμνάσιο'; row[10]='="2401070"'; row[11]='ΔΟΚΙΜΑΣΤΙΚΟ ΓΥΜΝΑΣΙΟ'; row[18]='Γ'; row[19]='Ξένων Γλωσσών Γυμνασίου'; row[20]='Γερμανικά μέσοι'; row[21]='2'; row[22]='2'; row[23]='4'; row[24]='2'; row[25]='2'; row[28]='ΠΕ07'
text=';'.join(headers)+'\r\n'+';'.join(row)+'\r\n'
with tempfile.TemporaryDirectory() as td:
    td=Path(td); csvp=td/'stat5_1_fixture.csv'; zipp=td/'stat5_1_fixture.zip'
    csvp.write_bytes(text.encode('cp1253'))
    with zipfile.ZipFile(zipp,'w',zipfile.ZIP_DEFLATED) as z: z.write(csvp,csvp.name)
    node=r'''
const fs=require('fs');
require(process.argv[1]);
const api=require(process.argv[2]);
(async()=>{
 const b=fs.readFileSync(process.argv[3]);
 const reg=await api.parseArrayBuffer(b.buffer.slice(b.byteOffset,b.byteOffset+b.byteLength),'fixture.zip');
 const rows=api.forSchool(reg,'2401070');
 const local=api.strictKey('gymnasio','Γ΄','Γερμανικά');
 console.log(JSON.stringify({rows:reg.row_count,schools:reg.school_count,mismatch:reg.formula_mismatch_count,schoolRows:rows.length,gap:rows[0].myschool_gap_hours,subject:rows[0].subject_key,structure:rows[0].structure,keyMatches:rows[0].strict_key===local}));
})().catch(e=>{console.error(e);process.exit(1)});
'''
    p=subprocess.run(['node','-e',node,str(ROOT/'includes'/'personnel-csv-import.js'),str(MODULE),str(zipp)],text=True,capture_output=True,cwd=ROOT)
    if p.returncode:
        print(p.stderr)
        data={}
    else:
        data=json.loads(p.stdout.strip())
    check('cp1253 zip fixture parses', data.get('rows')==1 and data.get('schools')==1 and data.get('schoolRows')==1)
    check('myschool formula is validated', data.get('gap')==2 and data.get('mismatch')==0)
    check('gymnasium study area maps to gymnasium structure', data.get('structure')=='gymnasio')
    check('foreign language level matches local language subject', data.get('subject')=='γερμανικα' and data.get('keyMatches') is True)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
