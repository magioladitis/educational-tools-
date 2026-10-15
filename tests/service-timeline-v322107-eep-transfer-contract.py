from pathlib import Path
import json, subprocess, zipfile, io
ROOT=Path(__file__).resolve().parents[1]
proc=subprocess.run(['php','-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'],cwd=ROOT,text=True,capture_output=True)
assert proc.returncode==0,proc.stderr
d=json.loads(proc.stdout); events=d['events']; by={e['id']:e for e in events}
assert d['current_cycle']=='2026-2027' and d['updated_at']=='15/10/2026'
assert len(events)==59 and len(by)==len(events), 'event IDs unique'
source='6ΑΒΑ46ΝΚΠΔ-ΠΡΘ'
expected={
'eep-evp-transfer-circular':'15/10/2026',
'eep-evp-transfer-applications':'16/10–02/11/2026',
'temporary-transfer-points-eep-evp':'27/11/2026',
'transfer-application-withdrawal-eep-evp':'31/12/2026 · 15:00',
'eep-evp-application-intake':'25/11/2026',
'eep-evp-exceptional-documents':'27/11/2026',
'eep-evp-points-corrections':'27/11–04/12/2026',
}
for k,v in expected.items():
    e=by[k]
    assert e['latest']==v,(k,e['latest'])
    assert e['levels']==['eep-evp'] and e['latest_verified']
    assert any(source in s['url'] for s in e['sources']),(k,e['sources'])
assert by['metatheseis-circular']['latest']=='15/10/2026','Educator circular updated from verified 138014/E2'
assert by['metatheseis-applications']['latest']=='16/10–02/11/2026'
assert by['eep-evp-transfer-applications']['history'][-1]=='16–31/10/2025','Historical year retained'
page=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon.php'],cwd=ROOT,capture_output=True,text=True)
assert page.returncode==0,page.stderr
for phrase in ['16/10–02/11/2026','31/12/2026 · 15:00','27/11–04/12/2026','138014/Ε2']:
    assert phrase in page.stdout,phrase
assert 'https://teachers.minedu.gov.gr/' in page.stdout
# Deadlines, use explicit offset-aware events, October DST and November standard time.
deadline=subprocess.run(['php','prothesmies.php'],cwd=ROOT,text=True,capture_output=True)
assert deadline.returncode==0,deadline.stderr
for phrase in ['2026-11-03T00:00:00+02:00','2026-12-05T00:00:00+02:00','2026-12-31T15:00:00+02:00']:
    assert phrase in deadline.stdout,phrase
export=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon-export.php'],cwd=ROOT,capture_output=True)
assert export.returncode==0 and export.stdout.startswith(b'PK'),export.stderr
with zipfile.ZipFile(io.BytesIO(export.stdout)) as z:
    xml=z.read('xl/worksheets/sheet1.xml').decode()
    assert '27/11–04/12/2026' in xml and '16/10–02/11/2026' in xml
assert '3.22.115' in (ROOT/'includes/config.php').read_text()
print('PASS: ΕΕΠ–ΕΒΠ timeline 2026–2027, deadlines, historical protection, Excel')
