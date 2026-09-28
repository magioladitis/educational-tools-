from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'service-timeline-data.php').read_text(encoding='utf-8')
CONFIG = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
SW = (ROOT / 'service-worker.js').read_text(encoding='utf-8')
README = (ROOT / 'README.md').read_text(encoding='utf-8')
AUDIT = (ROOT / 'docs' / 'audits' / 'SERVICE-TIMELINE-HISTORICAL-SOURCE-AUDIT-2026-09-28.md').read_text(encoding='utf-8')
SOURCES = (ROOT / 'docs' / 'audits' / 'SERVICE-TIMELINE-SOURCES-2026-09-28.md').read_text(encoding='utf-8')

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

bodies = event_block('first-bodies-detachments')
check('bodies detachments all years verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in bodies)
check('2022 first bodies date corrected to 16 June', '16/06/2022' in bodies)
check('stale 17 June 2022 removed from bodies block', '17/06/2022' not in bodies)
for value in ['22/06/2020','09/07/2021','07/07/2023','27/05/2024','06/06/2025','15/06/2026']:
    check('bodies history retains ' + value, value in bodies)
for marker in ['45452-22-06-20', '49419-09-07-21', '52433-16-06-22', '55900-07-07-23', '58427-27-05-24', '61733-06-06-25']:
    check('bodies historical source ' + marker, marker in bodies)
check('bodies semantics explicitly first found official posting', 'πρώτη επίσημη ανάρτηση που εντοπίζεται' in bodies)

circ = event_block('metatakseis-circular')
apps = event_block('metatakseis-applications')
withdraw = event_block('metatakseis-application-withdrawal')
for name, block in [('circular', circ), ('applications', apps), ('withdrawal', withdraw)]:
    check('metatakseis ' + name + ' all years verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in block)
    for protocol in ['59720/Ε2','50923/Ε2','55292/Ε2','36165/Ε2','41904/Ε2','46071/Ε2']:
        check(name + ' historical protocol ' + protocol, protocol in block)

for value in ['21–29/05/2020','10–17/05/2021','17–26/05/2022','29/03–11/04/2023','22/04–01/05/2024','30/04–12/05/2025','04–15/05/2026']:
    check('application period ' + value, value in apps)
for value in ['15/06/2020','31/05/2021','10/06/2022','28/04/2023','15/05/2024','23/05/2025','27/05/2026']:
    check('withdrawal deadline ' + value, value in withdraw)

check('version is 3.22.38 or newer', "EDU_TOOLS_VERSION', '3.22." in CONFIG)
check('cache remains release-scoped', "CACHE_PREFIX + '3.22." in SW)
check('readme documents v3.22.38', 'v3.22.38' in README and 'Bodies detachments + metatakseis applications/withdrawals' in README)
check('historical audit documents correction and full metatakseis coverage', '17/06/2022 → 16/06/2022' in AUDIT and 'Αιτήσεις και ανακλήσεις μετατάξεων 2020–2026' in AUDIT)
check('source audit documents v3.22.38', 'Συμπλήρωση ιστορικών πηγών v3.22.38' in SOURCES)
