from pathlib import Path
import re, subprocess, io, zipfile
ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT/'includes/service-timeline-data.php').read_text(encoding='utf-8')
CONFIG = (ROOT/'includes/config.php').read_text(encoding='utf-8')
SW = (ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:', label)

def event_block(event_id):
    start = DATA.index("'id' => '" + event_id + "'")
    nxt = DATA.find("'id' => '", start + 10)
    return DATA[start:nxt if nxt != -1 else len(DATA)]

withdrawal = event_block('resignation-withdrawal')
check('resignation withdrawal retains one-month rule', 'μέσα σε έναν μήνα από την ημερομηνία της αίτησης' in withdrawal)
check('resignation withdrawal shows latest possible dates', all(x in withdrawal for x in ['10/05/2020','10/04/2021','11/03/2022','13/03/2023','12/03/2024','11/03/2025','11/03/2026']))
check('resignation withdrawal labels dates as indicative horizon', 'ενδεικτικά η τελευταία δυνατή ανάκληση' in withdrawal)

pe = event_block('mutual-transfer-applications-pe')
de = event_block('mutual-transfer-applications-de')
check('old combined mutual application row removed', "'id' => 'mutual-transfer-applications'" not in DATA)
check('PE mutual application row exists', 'Αμοιβαίες μεταθέσεις Π.Ε. — λήξη αιτήσεων' in pe and "'03/04/2026'" in pe)
check('DE mutual application row exists', 'Αμοιβαίες μεταθέσεις Δ.Ε. — λήξη αιτήσεων' in de and "'07/04/2026'" in de)
check('PE and DE preserve different 2020-2022 histories', all(x in pe for x in ['22/04/2020','08/04/2021','02/04/2022']) and all(x in de for x in ['11/05/2020','09/04/2021','04/04/2022']))
check('PE protocol nuance retained', '14/04/2026' in pe)
check('PE/DE official 2026 sources separated', '64531-23-03-26' in pe and '70026-24-03-26' in de)

ver = re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'", CONFIG)
cache = re.search(r"CACHE_PREFIX \+ '([0-9.]+)'", SW)
check('version 3.22.46', bool(ver) and ver.group(1) == '3.22.46')
check('cache 3.22.46', bool(cache) and cache.group(1) == '3.22.46')

proc = subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon-export.php'], cwd=ROOT, capture_output=True)
check('xlsx export still executes', proc.returncode == 0 and proc.stdout.startswith(b'PK'))
if proc.returncode == 0 and proc.stdout.startswith(b'PK'):
    with zipfile.ZipFile(io.BytesIO(proc.stdout)) as zf:
        sheet = zf.read('xl/worksheets/sheet1.xml').decode('utf-8')
        check('xlsx includes split mutual rows', 'Αμοιβαίες μεταθέσεις Π.Ε. — λήξη αιτήσεων' in sheet and 'Αμοιβαίες μεταθέσεις Δ.Ε. — λήξη αιτήσεων' in sheet)
        check('xlsx includes resignation horizon', '11/03/2026' in sheet and '10/05/2020' in sheet)
print('RESULT: service timeline v3.22.46 contract PASS')
