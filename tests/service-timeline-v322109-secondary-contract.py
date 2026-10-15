from pathlib import Path
import json, subprocess, io, zipfile
ROOT=Path(__file__).resolve().parents[1]
def php(*args):
    p=subprocess.run(['php',*args],cwd=ROOT,capture_output=True)
    assert p.returncode==0,p.stderr.decode(errors='replace')
    return p.stdout
records=json.loads(php('-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);').decode())['events']
by={e['id']:e for e in records}
assert len(records)==len(by)==59
expected={'metatheseis-circular':'15/10/2026','metatheseis-applications':'16/10–02/11/2026','secondary-transfer-application-intake':'Έως 20/11/2026','temporary-transfer-points':'23/11/2026','secondary-transfer-points-corrections':'23–27/11/2026','secondary-transfer-record-lock':'Από 18/12/2026','transfer-application-withdrawal':'31/12/2026 · 15:00'}
for id,value in expected.items():
    e=by[id]
    assert e['latest']==value,(id,e['latest'])
    assert e['levels']==['de'] and e['latest_verified']
    assert any('ΨΚΩΦ46ΝΚΠΔ-63Κ' in src['url'] for src in e['sources'])
assert by['metatheseis-circular']['history'][-1]=='15/10/2025'
assert by['metatheseis-applications']['history'][-1]=='16–31/10/2025'
assert by['primary-transfer-applications']['levels']==['pe']
assert by['eep-evp-transfer-applications']['levels']==['eep-evp']
page=php('xronodiagramma-ypiresiakon-metavolon.php').decode()
for txt in ['138014/Ε2','138023/Ε2','137906/Ε4','23–27/11/2026','31/12/2026, 15:00']:
    assert txt in page,txt
deadline=php('prothesmies.php').decode()
for txt in ['Μεταθέσεις Δευτεροβάθμιας 2026–2027','Μεταθέσεις Δ.Ε. — διόρθωση','2026-11-03T00:00:00+02:00','2026-11-28T00:00:00+02:00','2026-12-31T15:00:00+02:00']:
    assert txt in deadline,txt
with zipfile.ZipFile(io.BytesIO(php('xronodiagramma-ypiresiakon-metavolon-export.php'))) as z:
    sheet=z.read('xl/worksheets/sheet1.xml').decode()
    sources=z.read('xl/worksheets/sheet2.xml').decode()
    for value in ['23–27/11/2026','Έως 20/11/2026','Από 18/12/2026']:
        assert value in sheet,value
    assert 'ΨΚΩΦ46ΝΚΠΔ-63Κ' in sources
assert '3.22.115' in (ROOT/'includes/config.php').read_text()
assert '3.22.115' in (ROOT/'service-worker.js').read_text()
print('PASS Secondary 2026-2027 timeline, deadline, historical separation, XLSX and version')
