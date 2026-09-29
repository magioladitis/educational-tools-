from pathlib import Path
import json,re,subprocess,io,zipfile
ROOT=Path(__file__).resolve().parents[1]
CONFIG=(ROOT/'includes/config.php').read_text(encoding='utf-8')
SW=(ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:',label)

proc=subprocess.run(['php','-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'],cwd=ROOT,text=True,capture_output=True)
check('timeline data loads',proc.returncode==0)
data=json.loads(proc.stdout); events=data['events']; by={e['id']:e for e in events}
check('at least 39 events after EEP-EVP expansion',len(events)>=39)
check('EEP-EVP coverage expanded to at least 15',sum('eep-evp' in e['levels'] for e in events)>=15)
check('educator transfer applications are PE-DE only',by['metatheseis-applications']['levels']==['pe','de'] and by['metatheseis-applications']['title']=='Αιτήσεις μετάθεσης εκπαιδευτικών')
check('dedicated EEP transfer circular complete',by['eep-evp-transfer-circular']['history']==['03/04/2020','18/03/2021','18/02/2022','14/10/2022','13/10/2023','15/10/2024','15/10/2025'])
check('dedicated EEP transfer applications complete',by['eep-evp-transfer-applications']['history']==['06–10/04/2020','18–26/03/2021','21–28/02/2022','16–31/10/2022','16–31/10/2023','16–31/10/2024','16–31/10/2025'])
mut=by['mutual-transfer-applications-eep-evp']
check('EEP mutual deadline uses 10-day derived horizon',mut['latest']=='13/04/2026' and '10 ημερολογιακές' in mut['note'])
check('EEP detachment results full 2020-2026',by['eep-evp-detachment-results']['history']==['29/06/2020','28/06/2021','21/07/2022','24/07/2023','05/07/2024','11/07/2025','10/07/2026'])
check('newly-appointed EEP-EVP invitation added',by['newly-appointed-eep-evp-detachment-circular']['latest']=='27/08/2026')
check('newly-appointed EEP-EVP applications added',by['newly-appointed-eep-evp-detachment-applications']['history'][4:] == ['23–26/08/2024','29/08–02/09/2025 16:00','27/08–01/09/2026'])
check('newly-appointed EEP-EVP results added',by['newly-appointed-eep-evp-detachment-results']['history'][3:] == ['11/09/2023','10/09/2024','11/09/2025','11/09/2026'])
check('new EEP-EVP events all source-backed', all(e.get('sources') and e.get('verified_history_indices') for k,e in by.items() if k in ['eep-evp-transfer-circular','eep-evp-transfer-applications','mutual-transfer-applications-eep-evp','eep-evp-detachment-results','newly-appointed-eep-evp-detachment-circular','newly-appointed-eep-evp-detachment-applications','newly-appointed-eep-evp-detachment-results']))
# XLS must automatically contain new rows.
proc=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon-export.php'],cwd=ROOT,capture_output=True)
check('xlsx export executes',proc.returncode==0 and proc.stdout.startswith(b'PK'))
with zipfile.ZipFile(io.BytesIO(proc.stdout)) as zf:
    sheet=zf.read('xl/worksheets/sheet1.xml').decode('utf-8')
    src=zf.read('xl/worksheets/sheet2.xml').decode('utf-8')
    check('xlsx contains EEP transfer applications', 'Αιτήσεις μετάθεσης ΕΕΠ-ΕΒΠ' in sheet)
    check('xlsx contains EEP detachment results', 'Ανακοινώσεις αποσπάσεων ΕΕΠ-ΕΒΠ' in sheet)
    check('xlsx sources contain EEP transfer source', '129423/Ε4/15-10-2025' in src)
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version at least 3.22.50',bool(ver) and tuple(map(int,ver.group(1).split('.'))) >= (3,22,50))
check('cache matches release',bool(ver and cache) and ver.group(1)==cache.group(1))
print('RESULT: service timeline v3.22.50 EEP-EVP coverage contract PASS')
