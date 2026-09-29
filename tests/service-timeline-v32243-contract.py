from pathlib import Path
import re, subprocess
ROOT=Path(__file__).resolve().parents[1]
DATA=(ROOT/'includes/service-timeline-data.php').read_text(encoding='utf-8')
CONFIG=(ROOT/'includes/config.php').read_text(encoding='utf-8')
SW=(ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:', label)

def block(event_id):
    anchor="'id' => '"+event_id+"'"
    start=DATA.find(anchor)
    check(event_id+' exists', start>=0)
    nxt=DATA.find("\n        array(\n            'id' =>", start+len(anchor))
    return DATA[start:nxt if nxt>=0 else len(DATA)]

eep=block('eep-ebp-detachments')
for d in ['19–26/05/2020','20–27/05/2021','03–10/06/2022','06–18/04/2023','22/04–01/05/2024','14–26/05/2025','19/05–02/06/2026']:
    check('EEP-EVP detachment history '+d, d in eep)
check('stale local 2024 range removed', '15–17/07/2024' not in eep)
check('all EEP-EVP years verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in eep)
check('historical EEP-EVP sources attached', "'historical_sources' => array(" in eep)
for token in ['44996-18-05-20','48786-19-05-21','52310-03-06-22','55064-05-04-23','%CE%A8%CE%977846%CE%9D%CE%9A%CE%A0%CE%94-%CE%A3%CE%A76.pdf','61517-14-05-25','64980-19-05-26']:
    check('source '+token, token in eep)
check('public note scopes the process', 'κεντρική πρόσκληση' in eep and 'ΚΕ.Δ.Α.Σ.Υ.' in eep and 'Σ.Δ.Ε.Υ.' in eep)
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG)
cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version is 3.22.43 or newer', bool(ver) and tuple(map(int,ver.group(1).split('.'))) >= (3,22,43))
check('cache is 3.22.43 or newer', bool(cache) and tuple(map(int,cache.group(1).split('.'))) >= (3,22,43))
proc=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon.php'],cwd=ROOT,text=True,capture_output=True)
check('timeline PHP renders',proc.returncode==0)
if proc.returncode==0:
    html=proc.stdout
    check('corrected 2024 value renders', '22/04–01/05/2024' in html)
    check('2020 and 2021 values render', '19–26/05/2020' in html and '20–27/05/2021' in html)
    check('historical source disclosure renders', '<summary>Πηγές προηγούμενων ετών</summary>' in html)
print('RESULT: service timeline v3.22.43 contract PASS')
