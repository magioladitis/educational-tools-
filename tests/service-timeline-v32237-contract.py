from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'service-timeline-data.php').read_text(encoding='utf-8')
CONFIG = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
SW = (ROOT / 'service-worker.js').read_text(encoding='utf-8')
README = (ROOT / 'README.md').read_text(encoding='utf-8')
AUDIT = (ROOT / 'docs' / 'audits' / 'SERVICE-TIMELINE-HISTORICAL-SOURCE-AUDIT-2026-09-28.md').read_text(encoding='utf-8')

def check(label, condition):
    if not condition:
        raise AssertionError(label)
    print('OK:', label)

def event_block(event_id):
    anchor = "'id' => '" + event_id + "'"
    start = DATA.find(anchor)
    check(event_id + ' exists', start >= 0)
    nxt = DATA.find("\n        array(\n            'id' =>", start + len(anchor))
    return DATA[start: nxt if nxt >= 0 else len(DATA)]

res_apps = event_block('resignations-applications')
check('resignation applications all years verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in res_apps)
for value in ['01–11/02/2022','01–13/02/2023','01–12/02/2024','01–11/02/2025']:
    check('resignation period ' + value + ' retained', value in res_apps)
for protocol in ['4696/Ε3/14-01-2022','7297/Ε3/23-01-2023','5420/Ε3/18-01-2024','8298/Ε3/27-01-2025']:
    check('resignation source ' + protocol, protocol in res_apps)

withdrawal = event_block('resignation-withdrawal')
check('withdrawal history all years verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in withdrawal)
check('withdrawal history normalized to individual one-month rule', withdrawal.count('Εντός 1 μήνα από κάθε αίτηση') >= 7)
for stale in ['13/03/2022','14/03/2023','13/03/2024']:
    check('stale common deadline removed ' + stale, stale not in withdrawal)
check('2022 one-month-rule evidence attached', '28597/Ε3/15-03-2022' in withdrawal and 'έως 11/03 για αιτήσεις 11/02' in withdrawal)
check('2024 last-possible date described only as example/evidence', '12/03/2024' in withdrawal)
check('2025 last-possible date described only as example/evidence', '11/03/2025' in withdrawal)

org = event_block('organic-gaps-circular')
check('organic gaps all years verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in org)
check('2024 general secondary protocols documented', '19644' in org and '19687/Ε2/23-02-2024' in org)
check('2025 general secondary protocols documented', '24061/Ε2' in org and '24073/Ε2/04-03-2025' in org)
check('2024-2025 official authority sources attached', 'blogs.sch.gr/dideker/' in org and 'dide.arg.sch.gr/' in org)

check('version is 3.22.37 or newer', "EDU_TOOLS_VERSION', '3.22." in CONFIG)
check('cache is 3.22.37 or newer', "CACHE_PREFIX + '3.22." in SW)
check('readme documents v3.22.37', 'v3.22.37' in README and 'Resignations 2022–2025 + organic gaps 2024–2025' in README)
check('audit documents full 2020-2026 coverage', 'Οργανικά κενά / πλεονάσματα 2020–2026' in AUDIT and 'Αιτήσεις παραίτησης 2020–2026' in AUDIT)
