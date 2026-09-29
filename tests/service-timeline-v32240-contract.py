from pathlib import Path
import subprocess

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'service-timeline-data.php').read_text(encoding='utf-8')
PAGE = (ROOT / 'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
CONFIG = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
SW = (ROOT / 'service-worker.js').read_text(encoding='utf-8')
README = (ROOT / 'README.md').read_text(encoding='utf-8')
AUDIT = (ROOT / 'docs' / 'audits' / 'SERVICE-TIMELINE-HISTORICAL-SOURCE-AUDIT-2026-09-28.md').read_text(encoding='utf-8')


def check(label, cond):
    if not cond:
        raise AssertionError(label)
    print('OK:', label)


def block(event_id):
    anchor = "'id' => '" + event_id + "'"
    start = DATA.find(anchor)
    check(event_id + ' exists', start >= 0)
    nxt = DATA.find("\n        array(\n            'id' =>", start + len(anchor))
    return DATA[start:nxt if nxt >= 0 else len(DATA)]

check('non-standard transfer objection timeline row removed', "'id' => 'transfer-objections'" not in DATA)
check('legacy merged mutation result row removed', "'id' => 'metatakseis-results-history'" not in DATA)

pe = block('metatakseis-to-primary')
de = block('metatakseis-to-secondary')
eep = block('metatakseis-to-eep')
check('PE mutations verified 2020-2026', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in pe)
check('DE mutations verified 2020-2026', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in de)
check('EEP mutations verified where sourced 2022-2026', "'verified_history_indices' => array(2,3,4,5,6)" in eep)
check('2020 mutation directions no longer conflated', "'15/09/2020'" in pe and "'10/09/2020'" in de)
check('2021 mutation directions no longer conflated', "'09/08/2021'" in pe and "'04/08/2021'" in de)
check('2023 mutation directions no longer conflated', "'16/08/2023'" in pe and "'11/08/2023'" in de)
check('2024 merged date corrected to official 02 August', "'02/08/2024'" in pe and "'02/08/2024'" in de and '01/08/2024' not in pe + de)
check('2025 mutation directions split correctly', "'02/09/2025'" in pe and "'03/09/2025'" in de)
for marker in ['29/07/2022','11/08/2023','06/08/2024','05/09/2025','19/08/2026']:
    check('EEP first-announcement history ' + marker, marker in eep)
check('old late EEP 08/10/2025 no longer used as first announcement', '08/10/2025' not in eep)

# End-user copy, not internal audit/change-log language.
for phrase in ['έκδοση δεδομένων', 'ιστορικό αρχείο εργασίας', 'κατάσταση έρευνας', 'ιστορικός έλεγχος', 'παλιά κοινή ιστορική γραμμή', 'διορθώθηκε']:
    check('public copy excludes internal phrase: ' + phrase, phrase not in PAGE and phrase not in DATA)
check('hero uses end-user language', 'Δείτε πότε πραγματοποιήθηκαν οι βασικές υπηρεσιακές διαδικασίες' in PAGE)
check('current-cycle message is user-facing', 'Όταν δημοσιευτεί, η ημερομηνία θα προστεθεί εδώ.' in PAGE)
check('source labels are user-facing', '<summary>Επίσημες πηγές</summary>' in PAGE and '<summary>Πηγές προηγούμενων ετών</summary>' in PAGE)
check('technical history legend is absent', 'Το ✓ δείχνει' not in PAGE and 'timeline-history-disclaimer' not in PAGE)
check('verified-only filter is conditional', '$hasUnverified' in PAGE and '<?php if ($hasUnverified) { ?>' in PAGE)
if "'id' => 'transfer-results'" in DATA:
    transfer_copy = block('transfer-results')
else:
    transfer_copy = block('transfer-results-educators') + block('transfer-results-eep-evp')
check('transfer result gives actionable category-specific guidance', 'αντίστοιχης ανακοίνωσης' in transfer_copy or 'χωριστά από τις μεταθέσεις εκπαιδευτικών' in transfer_copy)

import re
ver = re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'", CONFIG)
cache = re.search(r"CACHE_PREFIX \+ '([0-9.]+)'", SW)
def version_tuple(v): return tuple(int(x) for x in v.split('.'))
check('version is 3.22.40 or newer', bool(ver) and version_tuple(ver.group(1)) >= (3,22,40))
check('cache is 3.22.40 or newer', bool(cache) and version_tuple(cache.group(1)) >= (3,22,40))
check('README documents v3.22.40', 'v3.22.40' in README and 'end-user copy audit' in README)
check('audit retains removed-row reasoning', 'Η σειρά `transfer-objections` αφαιρέθηκε' in AUDIT and 'Η σειρά `metatakseis-results-history` αφαιρέθηκε' in AUDIT)

proc = subprocess.run(['php', 'xronodiagramma-ypiresiakon-metavolon.php'], cwd=ROOT, text=True, capture_output=True)
check('timeline PHP renders', proc.returncode == 0)
if proc.returncode == 0:
    html = proc.stdout
    check('no verified-only checkbox rendered when all cards verified', 'id="timelineVerifiedOnly"' not in html)
    check('collapsed current source disclosures still render', '<summary>Επίσημες πηγές</summary>' in html)
    check('collapsed historical source disclosures still render', '<summary>Πηγές προηγούμενων ετών</summary>' in html)

print('RESULT: service timeline v3.22.40 contract PASS')
