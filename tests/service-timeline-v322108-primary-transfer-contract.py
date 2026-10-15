from pathlib import Path
import json, subprocess, io, zipfile
ROOT=Path(__file__).resolve().parents[1]
def php(cmd):
    p=subprocess.run(['php']+cmd,cwd=ROOT,capture_output=True)
    assert p.returncode==0,p.stderr.decode()
    return p.stdout
d=json.loads(php(['-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);']).decode())
by={e['id']:e for e in d['events']}
assert len(by)==len(d['events'])==59
pe={'primary-transfer-circular':'15/10/2026', 'primary-transfer-applications':'16/10–02/11/2026', 'primary-transfer-application-intake':'20/11/2026', 'primary-transfer-points-publication':'23/11/2026', 'primary-transfer-points-corrections':'23–27/11/2026', 'primary-transfer-record-lock':'Από 18/12/2026', 'primary-transfer-application-withdrawal':'31/12/2026'}
for k, date in pe.items():
    e=by[k]; assert e['latest']==date,(k,e['latest']); assert e['levels']==['pe'] and e['latest_verified']; assert any('9Ρ7Ν46ΝΚΠΔ-5Κ5' in src['url'] for src in e['sources'])
assert by['metatheseis-circular']['levels']==['de'] and by['metatheseis-circular']['latest']=='15/10/2026'
assert by['metatheseis-applications']['levels']==['de'] and by['metatheseis-applications']['latest']=='16/10–02/11/2026'
assert by['eep-evp-transfer-applications']['latest']=='16/10–02/11/2026'
html=php(['xronodiagramma-ypiresiakon-metavolon.php']).decode()
for marker in ['138023/Ε2','16/10–02/11/2026','23–27/11/2026','138014/Ε2']:
    assert marker in html, marker
html=php(['prothesmies.php']).decode()
for marker in ['2026-11-03T00:00:00+02:00','2026-11-28T00:00:00+02:00','2027-01-01T00:00:00+02:00','Μεταθέσεις Πρωτοβάθμιας 2026']:
    assert marker in html, marker
raw=php(['xronodiagramma-ypiresiakon-metavolon-export.php'])
with zipfile.ZipFile(io.BytesIO(raw)) as z:
    sheet=z.read('xl/worksheets/sheet1.xml').decode()
    assert '23–27/11/2026' in sheet and '20/11/2026' in sheet and '9Ρ7Ν46ΝΚΠΔ-5Κ5' in z.read('xl/worksheets/sheet2.xml').decode()
assert '3.22.109' in (ROOT/'includes/config.php').read_text() and '3.22.109' in (ROOT/'service-worker.js').read_text()
print('PASS: Primary transfers 2026–27 timeline / deadlines / levels / source / Excel')
