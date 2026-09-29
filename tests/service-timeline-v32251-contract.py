from pathlib import Path
import json,re,subprocess,io,zipfile
ROOT=Path(__file__).resolve().parents[1]
CONFIG=(ROOT/'includes/config.php').read_text(encoding='utf-8')
SW=(ROOT/'service-worker.js').read_text(encoding='utf-8')
AUDIT=(ROOT/'SERVICE-TIMELINE-EEP-EVP-AUDIT-2026-09-29.md').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:',label)

proc=subprocess.run(['php','-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'],cwd=ROOT,text=True,capture_output=True)
check('timeline data loads',proc.returncode==0)
data=json.loads(proc.stdout); events=data['events']; by={e['id']:e for e in events}
check('EEP audit baseline retained',len(events)>=40)
check('EEP-EVP coverage expanded to 18',sum('eep-evp' in e['levels'] for e in events)==18)
fg=by['eep-evp-detachment-functional-gaps']
check('EEP detachment functional gaps complete',fg['history']==['27/05/2020','20/05/2021','16/06/2022','26/06/2023','06/06/2024','23/06/2025','19/06/2026'])
check('EEP functional gaps all verified',fg['verified_history_indices']==[0,1,2,3,4,5,6] and len(fg['historical_sources'])==7)
for eid in ['metatakseis-to-eep','metatakseis-to-primary','metatakseis-to-secondary']:
    check(eid+' applies to all personnel groups',by[eid]['levels']==['pe','de','eep-evp'])
check('no public EEP services-bodies recurring card',not any('φορείς' in e['title'].lower() and 'eep-evp' in e.get('levels',[]) for e in events))
check('audit records historical services-bodies process', 'ιστορικά' in AUDIT and '2019–2020' in AUDIT and '2023–2024' in AUDIT and 'μη ενεργή' in AUDIT)
proc=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon-export.php'],cwd=ROOT,capture_output=True)
check('xlsx export executes',proc.returncode==0 and proc.stdout.startswith(b'PK'))
with zipfile.ZipFile(io.BytesIO(proc.stdout)) as zf:
    sheet=zf.read('xl/worksheets/sheet1.xml').decode('utf-8')
    src=zf.read('xl/worksheets/sheet2.xml').decode('utf-8')
    check('xlsx contains EEP functional gaps', 'Λειτουργικά κενά ΕΕΠ-ΕΒΠ για αποσπάσεις' in sheet)
    check('xlsx sources include 2026 functional gap letter', '81698/Ε4/19-06-2026' in src)
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version at least 3.22.51',bool(ver) and tuple(map(int,ver.group(1).split('.'))) >= (3,22,51))
check('cache matches release',bool(ver and cache) and ver.group(1)==cache.group(1))
print('RESULT: service timeline v3.22.51 EEP-EVP audit contract PASS')
