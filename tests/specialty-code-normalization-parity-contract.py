#!/usr/bin/env python3
import json, pathlib, subprocess, sys
ROOT=pathlib.Path(__file__).resolve().parents[1]
FIX=ROOT/'tests/fixtures/specialty-code-normalization-v1.json'
DATA=json.loads(FIX.read_text(encoding='utf-8'))
fail=[]
def check(label, ok, detail=''):
    print(('PASS' if ok else 'FAIL')+': '+label+((' — '+detail) if detail and not ok else ''))
    if not ok: fail.append(label)

# JS canonicalizer is the only browser normalization implementation.
node_script=r'''
const fs=require('fs');
const api=require('./includes/specialty-code-normalization.js');
const data=JSON.parse(fs.readFileSync(process.argv[1],'utf8'));
const out={schema:api.schema,strict:data.strict.map(x=>api.normalize(x[0])),extract:data.extract.map(x=>api.extract(x[0]))};
process.stdout.write(JSON.stringify(out));
'''
r=subprocess.run(['node','-e',node_script,str(FIX)],cwd=ROOT,text=True,capture_output=True)
check('shared JS canonicalizer executes',r.returncode==0,r.stderr.strip())
js=json.loads(r.stdout) if r.returncode==0 else {}
check('JS schema matches fixture',js.get('schema')==DATA['schema'])
check('JS strict vectors',js.get('strict')==[x[1] for x in DATA['strict']],repr(js.get('strict')))
check('JS CSV-extraction vectors',js.get('extract')==[x[1] for x in DATA['extract']],repr(js.get('extract')))

# PHP canonicalizer must match the exact same strict contract.
php_code=r'''<?php
require %s;
$data=json_decode(file_get_contents(%s),true);
$out=array();
foreach($data['strict'] as $row) $out[]=teacherSpecialtyCanonicalCode($row[0]);
echo json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % (json.dumps(str(ROOT/'includes/teacher-specialties.php')),json.dumps(str(FIX)))
r=subprocess.run(['php'],input=php_code,cwd=ROOT,text=True,capture_output=True)
check('PHP canonicalizer executes',r.returncode==0,r.stderr.strip())
php=json.loads(r.stdout) if r.returncode==0 else []
expected=[x[1] for x in DATA['strict']]
check('PHP strict vectors equal shared contract',php==expected,repr(php))
check('PHP and JS strict outputs are identical',php==js.get('strict'))

# Production consumers must delegate, not carry another prefix/padding implementation.
core=(ROOT/'includes/education-core.js').read_text(encoding='utf-8')
work=(ROOT/'includes/personnel-workload-calculations.js').read_text(encoding='utf-8')
csv=(ROOT/'includes/personnel-csv-import.js').read_text(encoding='utf-8')
header=(ROOT/'includes/header.php').read_text(encoding='utf-8')
check('header loads shared specialty canonicalizer before EducationCore',
      'specialty-code-normalization.js' in header and header.find('specialty-code-normalization.js') < header.find('education-core.js'))
check('EducationCore delegates specialty normalization', 'specialtyCodeApi().normalize(value)' in core)
check('personnel workload delegates specialty normalization', 'return specialtyCodeApi().normalize(value);' in work)
check('CSV importer delegates final canonicalization', "return api.extract(value)" in csv and 'canonicalSpecialtyCode:canonicalSpecialtyCode' in csv)
check('personnel workload has no independent PE/TE/DE prefix regex', '/^(?:PE|PΕ|ΠE|ΠΕ)' not in work)

# CSV compatibility: loose input remains accepted but exact canonicalization is shared.
node_csv=r'''
const csv=require('./includes/personnel-csv-import.js');
process.stdout.write(JSON.stringify({
 loose:csv.normalizeSpecialtyCode('PE 3 - Μαθηματικοί'),
 strict:csv.canonicalSpecialtyCode('PE3'),
 strictReject:csv.canonicalSpecialtyCode('PE3 Μαθηματικοί')
}));
'''
r=subprocess.run(['node','-e',node_csv],cwd=ROOT,text=True,capture_output=True)
check('CSV importer executes with shared module',r.returncode==0,r.stderr.strip())
obj=json.loads(r.stdout) if r.returncode==0 else {}
check('CSV loose extractor preserved',obj.get('loose')=='ΠΕ03')
check('CSV exact canonicalizer zero-pads',obj.get('strict')=='ΠΕ03')
check('CSV exact canonicalizer rejects labels',obj.get('strictReject')=='')

print('RESULT: %d PASS / %d FAIL' % (17-len(fail),len(fail)))
raise SystemExit(1 if fail else 0)
