from pathlib import Path
import re, subprocess, io, zipfile
ROOT=Path(__file__).resolve().parents[1]
DATA=(ROOT/'includes/service-timeline-data.php').read_text(encoding='utf-8')
PAGE=(ROOT/'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
CONFIG=(ROOT/'includes/config.php').read_text(encoding='utf-8')
SW=(ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:',label)

def block(event_id):
    start=DATA.index("'id' => '"+event_id+"'")
    nxt=DATA.find("\n        array(\n            'id' =>",start+10)
    return DATA[start:nxt if nxt>=0 else len(DATA)]

check('combined educator results row removed', "'id' => 'transfer-results-educators'" not in DATA)
pe=block('transfer-results-primary'); de=block('transfer-results-secondary')
check('PE results row', "'title' => 'Ανακοινώσεις μεταθέσεων Π.Ε.'" in pe and "'19/03/2026'" in pe)
check('DE results row', "'title' => 'Ανακοινώσεις μεταθέσεων Δ.Ε.'" in de and "'23/03/2026'" in de)
check('PE history independently stored', all(x in pe for x in ['07/04/2020','24/03/2021','18/03/2022','02/03/2023','27/03/2024','20/03/2025','19/03/2026']))
check('DE history independently stored', all(x in de for x in ['24/04/2020','24/03/2021','18/03/2022','02/03/2023','27/03/2024','20/03/2025','23/03/2026']))
check('level metadata on split results', "'levels' => array('pe')" in pe and "'levels' => array('de')" in de)
check('page exposes data-levels groundwork', 'data-levels=' in PAGE and "implode(',', $event['levels'])" in PAGE)
for eid,level in [('mutual-transfer-applications-pe','pe'),('mutual-transfer-applications-de','de'),('mutual-transfer-primary-result','pe'),('mutual-transfer-secondary-result','de'),('functional-gaps-primary-circular','pe'),('functional-gaps-secondary-circular','de'),('first-primary-detachments','pe'),('first-secondary-detachments','de'),('transfer-results-eep-evp','eep-evp'),('eep-ebp-detachments','eep-evp')]:
    check(eid+' level metadata', "'levels' => array('"+level+"')" in block(eid))
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version 3.22.47 or newer', bool(ver) and tuple(map(int, ver.group(1).split('.'))) >= (3,22,47))
check('cache matches version', bool(cache) and bool(ver) and cache.group(1)==ver.group(1))
proc=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon.php'],cwd=ROOT,text=True,capture_output=True)
check('timeline renders',proc.returncode==0)
if proc.returncode==0:
    html=proc.stdout
    check('split cards render', 'Ανακοινώσεις μεταθέσεων Π.Ε.' in html and 'Ανακοινώσεις μεταθέσεων Δ.Ε.' in html)
    check('level attributes render', 'data-levels="pe"' in html and 'data-levels="de"' in html and 'data-levels="eep-evp"' in html)
proc=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon-export.php'],cwd=ROOT,capture_output=True)
check('xlsx export executes', proc.returncode==0 and proc.stdout.startswith(b'PK'))
if proc.returncode==0 and proc.stdout.startswith(b'PK'):
    with zipfile.ZipFile(io.BytesIO(proc.stdout)) as zf:
        sheet=zf.read('xl/worksheets/sheet1.xml').decode('utf-8')
        check('xlsx contains split educator results', 'Ανακοινώσεις μεταθέσεων Π.Ε.' in sheet and 'Ανακοινώσεις μεταθέσεων Δ.Ε.' in sheet and 'Ανακοινώσεις μεταθέσεων εκπαιδευτικών' not in sheet)
print('RESULT: service timeline v3.22.47 contract PASS')
