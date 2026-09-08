#!/usr/bin/env python3
from pathlib import Path
import csv, json, subprocess

ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
JS=ROOT/'includes/school-profile-csv-import.js'
CSV_FILE=ROOT/'data/school_registry_v1-dde-kerkyras-2026-2027-full.csv'
COMPAT_CSV=ROOT/'data/school_registry_v1-dde-kerkyras-2026.csv'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

page=PAGE.read_text(encoding='utf-8')
js=JS.read_text(encoding='utf-8')
check('Corfu directory button exists', 'id="loadCorfuSchoolDirectory"' in page)
check('Corfu CSV download button exists', 'id="downloadCorfuSchoolDirectory"' in page)
check('registry search exists', 'id="schoolRegistrySearch"' in page)
check('directory loads without fetch', "loadBuiltinSchoolDirectory('dde_corfu_2026')" in page and 'fetch(' not in page)
check('full 2026-2027 dataset is embedded', "dataset_version:'2026-2027-full-v2-stat3-10'" in js and "filename:'school_registry_v1-dde-kerkyras-2026-2027-full.csv'" in js)
check('full CSV download uses embedded raw dataset', 'directory.raw_csv' in page and "a.download=directory.filename" in page)
check('richer built-in dataset refreshes old identity-only registry', 'existing.directory_id===directoryId || existing.directory_only===true || schoolCsvTotalSections(existing)===0' in page)
check('directory status explains populated structural data', 'διαθέσιμα στοιχεία βασικών τμημάτων' in page and 'πλήρες μητρώο 2026-2027' in page and 'myschool stat3_10' in page)
check('remaining inactive placeholders exist', all(x in page for x in [
    '<option value="esperino_epal" disabled>Εσπερινό ΕΠΑΛ</option>',
    '<option value="sek" disabled>Εργαστηριακό Κέντρο</option>',
]) and '<option value="gymnasio_lt" disabled>' not in page)
check('school address supported in schema', "school_address:['διευθυνση σχολειου'" in js and "school_address:String(get(row,mapping,'school_address')" in js)
check('specific types normalized before generic gymnasium', "if(t.indexOf('μουσικ')>=0) return 'mousiko';" in js and "return 'gymnasio';" in js)
check('canonical full Corfu registry CSV exists', CSV_FILE.exists())
check('compatibility Corfu CSV also exists', COMPAT_CSV.exists())

node=r'''
const c=require('./includes/school-profile-csv-import.js');
const d=c.getBuiltinDirectory('dde_corfu_2026');
const counts={}; d.schools.forEach(s=>counts[s.school_type]=(counts[s.school_type]||0)+1);
const byCode=Object.fromEntries(d.schools.map(s=>[s.school_code,s]));
console.log(JSON.stringify({
 count:d.schools.length,
 unique:new Set(d.schools.map(s=>s.school_code)).size,
 headers:d.headers.length,
 filename:d.filename,
 rawCsvHasFullHeader:d.raw_csv.includes('Κατάσταση πληρότητας') && d.raw_csv.includes('ΛΤ Γ Οικονομίας Πληροφορικής'),
 supported:d.schools.filter(s=>s.supported).length,
 withSections:d.schools.filter(s=>(s.general_a+s.general_b+s.general_c)>0).length,
 counts,
 code2401020:byCode['2401020'],
 faiakes:byCode['2402050'],
 gel1:byCode['2451010'],
 paxi:byCode['2403010'],
 kassiopi:byCode['2409010'],
 skripero:byCode['2408010'],
 music:byCode['2401065'],
 eneegyl:byCode['2411001'],
 pepal:byCode['2448000'],
 sek:byCode['SEK087'],
 normalize:[c.normalizeSchoolType('ΕΝ.Ε.Ε.ΓΥ.-Λ.'),c.normalizeSchoolType('Ε.Ε.Ε.ΕΚ.'),c.normalizeSchoolType('ΕΣΠΕΡΙΝΟ ΕΠΑ.Λ ΚΕΡΚΥΡΑΣ')]
}));
'''
proc=subprocess.run(['node'],input=node,text=True,capture_output=True,cwd=ROOT)
check('directory JS executes', proc.returncode==0)
data=json.loads(proc.stdout) if proc.returncode==0 else {}
check('Corfu directory has 38 schools', data.get('count')==38)
check('Corfu directory codes are unique', data.get('unique')==38)
check('full registry exposes all 62 CSV columns', data.get('headers')==62)
check('full raw CSV is available to downloader', data.get('rawCsvHasFullHeader') is True and data.get('filename')=='school_registry_v1-dde-kerkyras-2026-2027-full.csv')
check('currently supported Gymnasium/GEL/composite profiles count is 30', data.get('supported')==30)
check('structural section data exists for almost all units', data.get('withSections')==37)
check('type distribution stable', data.get('counts')=={
    'gymnasio':16,'gymnasio_lt':4,'eneegyl':1,'esperino_gymnasio':1,'mousiko':1,'eeeek':1,
    'epal':2,'esperino_epal':1,'pepal':1,'gel':8,'esperino_gel':1,'sek':1
})
check('2nd Gymnasium now loads current 2026-2027 sections', data.get('code2401020',{}).get('general_a')==4 and data.get('code2401020',{}).get('general_b')==4 and data.get('code2401020',{}).get('general_c')==3)
check('2nd Gymnasium tech split is prefilled', data.get('code2401020',{}).get('tech_split_a')==4 and data.get('code2401020',{}).get('tech_split_b')==1 and data.get('code2401020',{}).get('tech_split_c')==1)
check('Faiakes uses newer 3-section A class snapshot', data.get('faiakes',{}).get('general_a')==3 and data.get('faiakes',{}).get('general_b')==2 and data.get('faiakes',{}).get('general_c')==3)
check('GEL orientation groups are prefilled', data.get('gel1',{}).get('gel_b_hum')==2 and data.get('gel1',{}).get('gel_b_sci')==3 and data.get('gel1',{}).get('gel_c_hum')==2 and data.get('gel1',{}).get('gel_c_scihealth')==2 and data.get('gel1',{}).get('gel_c_econit')==2)
check('2nd Gymnasium real language groups are prefilled', data.get('code2401020',{}).get('lang_a_fr')==2 and data.get('code2401020',{}).get('lang_a_de')==2 and data.get('code2401020',{}).get('lang_b_fr')==1 and data.get('code2401020',{}).get('lang_b_de')==3 and data.get('code2401020',{}).get('lang_c_fr')==1 and data.get('code2401020',{}).get('lang_c_de')==2)
check('Gymnasium pending second-language fields are cleared', data.get('code2401020',{}).get('pending_fields','')=='')
check('GEL real A/B language groups are prefilled', data.get('gel1',{}).get('lang_a_fr')==2 and data.get('gel1',{}).get('lang_a_de')==2 and data.get('gel1',{}).get('lang_b_fr')==2 and data.get('gel1',{}).get('lang_b_de')==3)
check('GEL pending metadata now only keeps special C groups', '2ης ξένης γλώσσας' not in data.get('gel1',{}).get('pending_fields','') and 'Γ Μαθηματικά 2ου πεδίου' in data.get('gel1',{}).get('pending_fields',''))
check('Paxoi classified as Gymnasium with Lyceum classes', data.get('paxi',{}).get('school_type')=='gymnasio_lt')
check('Kassiopi classified as Gymnasium with Lyceum classes', data.get('kassiopi',{}).get('school_type')=='gymnasio_lt')
check('Skripero classified as Gymnasium with Lyceum classes', data.get('skripero',{}).get('school_type')=='gymnasio_lt')
check('Music school is not generic Gymnasium', data.get('music',{}).get('school_type')=='mousiko')
check('ENEEGYL is not generic Gymnasium', data.get('eneegyl',{}).get('school_type')=='eneegyl')
check('PEPAL classified separately', data.get('pepal',{}).get('school_type')=='pepal')
check('laboratory center classified separately', data.get('sek',{}).get('school_type')=='sek')
check('punctuated Greek acronyms normalize', data.get('normalize')==['eneegyl','eeeek','esperino_epal'])

for file_path,label in [(CSV_FILE,'canonical'),(COMPAT_CSV,'compatibility')]:
    if file_path.exists():
        with file_path.open('r',encoding='utf-8-sig',newline='') as f:
            reader=csv.DictReader(f,delimiter=';')
            rows=list(reader)
            headers=reader.fieldnames or []
        check(f'{label} CSV has 38 rows', len(rows)==38)
        check(f'{label} CSV has 62 columns', len(headers)==62)
        codes=[r.get('Κωδικός Υπουργείου','') for r in rows]
        check(f'{label} CSV codes unique', len(codes)==len(set(codes))==38)
        by_code={r['Κωδικός Υπουργείου']:r for r in rows}
        check(f'{label} CSV contains current Faiakes A sections', by_code.get('2402050',{}).get('Α τμήματα')=='3')
        check(f'{label} CSV contains GEL orientation groups', by_code.get('2451010',{}).get('Β ομάδες Ανθρωπιστικών')=='2' and by_code.get('2451010',{}).get('Γ ομάδες Οικονομίας Πληροφορικής')=='2')

failed=[name for name,ok in checks if not ok]
for name,ok in checks: print(('PASS' if ok else 'FAIL')+': '+name)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
