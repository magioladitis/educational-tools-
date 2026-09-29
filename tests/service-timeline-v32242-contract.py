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

check('legacy merged transfer-results card removed', "'id' => 'transfer-results'" not in DATA)
pe=block('transfer-results-primary')
de=block('transfer-results-secondary')
eep=block('transfer-results-eep-evp')
check('educator results are split by level', "'title' => 'Ανακοινώσεις μεταθέσεων Π.Ε.'" in pe and "'title' => 'Ανακοινώσεις μεταθέσεων Δ.Ε.'" in de)
check('2026 educator dates stay separate', "'19/03/2026'" in pe and "'23/03/2026'" in de and '03/04/2026' not in pe+de)
check('educator histories fully verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in pe and "'verified_history_indices' => array(0,1,2,3,4,5,6)" in de)
check('EEP-EVP card title split', "'title' => 'Ανακοινώσεις μεταθέσεων ΕΕΠ-ΕΒΠ'" in eep)
for d in ['15/05/2020','22/04/2021','12/04/2022','07/03/2023','03/04/2024','01/04/2025','03/04/2026']:
    check('EEP-EVP history '+d, d in eep)
check('EEP-EVP history fully verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in eep)
check('2025 user-supplied official EEP-EVP source present', '61116:01-04-25' in eep)
check('2026 EEP-EVP official source present', '64693-03-04-26' in eep)
check('sources remain collapsed-compatible via data arrays', "'historical_sources' => array(" in pe and "'historical_sources' => array(" in de and "'historical_sources' => array(" in eep)
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG)
cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version is 3.22.42 or newer', bool(ver) and tuple(map(int, ver.group(1).split('.'))) >= (3,22,42))
check('cache is 3.22.42 or newer', bool(cache) and tuple(map(int, cache.group(1).split('.'))) >= (3,22,42))
proc=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon.php'],cwd=ROOT,text=True,capture_output=True)
check('timeline PHP renders',proc.returncode==0)
if proc.returncode==0:
    html=proc.stdout
    check('both split cards render', 'Ανακοινώσεις μεταθέσεων Π.Ε.' in html and 'Ανακοινώσεις μεταθέσεων Δ.Ε.' in html and 'Ανακοινώσεις μεταθέσεων ΕΕΠ-ΕΒΠ' in html)
    check('source disclosures remain collapsed', '<summary>Επίσημες πηγές</summary>' in html and '<summary>Πηγές προηγούμενων ετών</summary>' in html)
print('RESULT: service timeline v3.22.42 contract PASS')
