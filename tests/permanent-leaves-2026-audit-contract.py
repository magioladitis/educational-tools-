#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import sys
from urllib.parse import urlparse

ROOT = Path(__file__).resolve().parents[1]
checks = []

def check(name, cond):
    checks.append((name, bool(cond)))
    print(('PASS' if cond else 'FAIL') + ' | ' + name)

php = r'''
$d = require $argv[1];
echo json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
proc = subprocess.run(
    ['php', '-r', php, str(ROOT / 'includes/permanent-leaves-data.php')],
    capture_output=True, text=True
)
check('permanent leave data loads', proc.returncode == 0 and bool(proc.stdout.strip()))
if proc.returncode != 0 or not proc.stdout.strip():
    print(proc.stderr)
    sys.exit(1)

data = json.loads(proc.stdout)
leaves = data.get('leaves', [])
sources = data.get('sources', {})
by_id = {x.get('id'): x for x in leaves if isinstance(x, dict)}
ids = [x.get('id') for x in leaves if isinstance(x, dict)]

check('exactly 37 permanent leave cards', len(leaves) == 37)
check('all permanent leave ids unique', len(ids) == len(set(ids)) == 37)

required_new = {
    'progenitikos-elegxos',
    'epapeiloumeni-kyisi',
    'dysmenes-kairikes',
    'monogoneas-2026',
    'therapeia-syzygou-teknou',
    'yiothesia-anadochi-3mino',
}
check('all post-2021 standalone rights present', required_new.issubset(by_id))

missing_sources = []
for leave in leaves:
    for key in leave.get('sources', []):
        if key not in sources:
            missing_sources.append((leave.get('id'), key))
check('every card source key resolves', not missing_sources)
if missing_sources:
    print('  missing:', missing_sources)

exam = by_id.get('exetaseon', {})
check('exam leave uses N4590/2019', 'n4590_2019' in exam.get('sources', []))
check('exam leave does not use N4589/2019', 'n4589_2019' not in exam.get('sources', []))

gamos = by_id.get('gamos', {})
eklogiko = by_id.get('eklogiko', {})
check('marriage leave uses marriage procedure source', 'mitos_marriage' in gamos.get('sources', []))
check('marriage leave is not wired to electoral source', 'ypes_electoral_right' not in gamos.get('sources', []))
check('electoral leave uses electoral source', 'ypes_electoral_right' in eklogiko.get('sources', []))

diki = by_id.get('diki', {})
diki_conditions = ' '.join(diki.get('conditions', []))
check('trial leave states 200–400 km = 1 working day', '200–400 χλμ.' in diki_conditions and '1 εργάσιμη ημέρα' in diki_conditions)
check('trial leave states >401 km road = 2 working days', 'πάνω από 401 χλμ.' in diki_conditions and 'εξ ολοκλήρου οδικώς' in diki_conditions and '2 εργάσιμες ημέρες' in diki_conditions)
check('trial leave states island travel up to 3 days by Director of Education', 'σε/από νησιά' in diki_conditions and 'έως 3 εργάσιμες ημέρες' in diki_conditions and 'Διευθυντή Εκπαίδευσης' in diki_conditions)
check('trial leave has structured 1–3 working-day duration', diki.get('comparison', {}).get('duration', {}).get('kind') == 'range' and diki.get('comparison', {}).get('duration', {}).get('min') == 1 and diki.get('comparison', {}).get('duration', {}).get('max') == 3)

for leave_id in ('meiwmeno-anatrofis', 'enneamini-anatrofis'):
    leave = by_id.get(leave_id, {})
    check(leave_id + ' has educator-specific N2721/1999 source', 'n2721_1999' in leave.get('sources', []))
    check(leave_id + ' has YPES 2022 educator clarification', 'ypes_2022_new_parental' in leave.get('sources', []))

allowed_hosts = {
    'www.minedu.gov.gr',
    'minedu.gov.gr',
    'www.ypes.gr',
    'ypes.gr',
    'api.et.gr',
    'ia37rg02wpsa01.blob.core.windows.net',
    'mitos.gov.gr',
    'diavgeia.gov.gr',
    'www2.dypa.gov.gr',
    'dypa.gov.gr',
    'www.dypa.gov.gr',
}
used_keys = {k for leave in leaves for k in leave.get('sources', [])}
nonofficial = []
for key in sorted(used_keys):
    entry = sources.get(key, {})
    host = (urlparse(entry.get('url', '')).hostname or '').lower()
    if host not in allowed_hosts:
        nonofficial.append((key, host, entry.get('url', '')))
check('all used leave sources are on official allowlisted hosts', not nonofficial)
if nonofficial:
    for item in nonofficial:
        print('  nonofficial:', item)

page = subprocess.run(['php', str(ROOT / 'adeies-monimon.php')], capture_output=True, text=True)
html = page.stdout
check('permanent guide renders', page.returncode == 0 and '<html' in html)
check('rendered permanent guide has 37 cards', html.count('class="leave-card"') == 37)
public_lower = html.lower()
check('public page does not expose internal audit jargon', 'νομικό audit' not in public_lower and '>audit<' not in public_lower)
check('public page uses end-user update wording', 'Επικαιροποίηση και επίσημες πηγές' in html)

failed = [name for name, ok in checks if not ok]
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
sys.exit(1 if failed else 0)
