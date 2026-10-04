from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'service-timeline-data.php').read_text(encoding='utf-8')
PAGE = (ROOT / 'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
SOURCE = (ROOT / 'includes' / 'components' / 'source-card.php').read_text(encoding='utf-8')
CSS = (ROOT / 'assets' / 'common.css').read_text(encoding='utf-8')
CONFIG = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
SW = (ROOT / 'service-worker.js').read_text(encoding='utf-8')
README = (ROOT / 'README.md').read_text(encoding='utf-8')
SOURCES = (ROOT / 'docs' / 'audits' / 'SERVICE-TIMELINE-SOURCES-2026-09-28.md').read_text(encoding='utf-8')
HIST = (ROOT / 'docs' / 'audits' / 'SERVICE-TIMELINE-HISTORICAL-SOURCE-AUDIT-2026-09-28.md').read_text(encoding='utf-8')

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

pe = block('functional-gaps-primary-circular')
de = block('functional-gaps-secondary-circular')
check('old common functional-gaps research card removed', "'id' => 'functional-gaps-history'" not in DATA)
def verified_history_indices(event_block):
    match = re.search(r"'verified_history_indices'\s*=>\s*array\(([^)]*)\)", event_block)
    check('verified_history_indices exists', match is not None)
    return {int(value.strip()) for value in match.group(1).split(',') if value.strip()}

# v3.22.39 established that 2021-2026 (indices 1-6) were verified.
# Later releases may legitimately add older verified years (e.g. DE index 0 / 2020),
# so this historical contract must assert the original guarantee as a subset,
# rather than requiring the array to remain byte-for-byte identical forever.
required_2021_2026 = {1, 2, 3, 4, 5, 6}
check('PE historical years 2021-2026 verified', required_2021_2026.issubset(verified_history_indices(pe)))
check('DE historical years 2021-2026 verified', required_2021_2026.issubset(verified_history_indices(de)))
for marker in ['68239/Ε2','62174/Ε2','75625/Ε2','53701/Ε2','66707/Ε2','73719/Ε2']:
    check('PE source marker ' + marker, marker in pe)
for marker in ['68209/Ε2','68172/Ε2','75639/Ε2','54074/Ε2','68355/Ε2','74045/Ε2']:
    check('DE source marker ' + marker, marker in de)
check('2022 PE and DE are no longer conflated', '26/05/2022 · 62174/Ε2' in pe and '02/06/2022 · 68172/Ε2' in de)
check('2025 PE and DE are no longer conflated', '06/06/2025 · 66707/Ε2' in pe and '11/06/2025 · 68355/Ε2' in de)

check('global source cards default collapsed on desktop', "$config['desktop_expanded'] = false" in SOURCE)
check('source cards still render closed initially', "$config['open'] = false" in SOURCE)
check('timeline current sources use native collapsed details', '<details class="timeline-event-sources">' in PAGE and '<summary>Επίσημες πηγές</summary>' in PAGE)
check('timeline historical sources are nested collapsed details', '<details class="timeline-historical-sources">' in PAGE and '<summary>Πηγές προηγούμενων ετών</summary>' in PAGE)
check('timeline source disclosure styling exists', '.timeline-source-links' in CSS and '.timeline-historical-source-links' in CSS)

check('version is 3.22.39 or newer', "EDU_TOOLS_VERSION', '3.22." in CONFIG)
check('cache is 3.22.39 or newer', "CACHE_PREFIX + '3.22." in SW)
check('readme documents v3.22.39', 'v3.22.39' in README and 'functional gaps P.E./D.E.' in README)
check('source audit documents v3.22.39', 'Συμπλήρωση ιστορικών πηγών v3.22.39' in SOURCES)
check('historical audit closes old functional-gaps research row', 'Η παλιά κοινή γραμμή λειτουργικών κενών αποσύρθηκε' in HIST)

proc = subprocess.run(['php', 'xronodiagramma-ypiresiakon-metavolon.php'], cwd=ROOT, text=True, capture_output=True)
check('timeline PHP renders', proc.returncode == 0)
if proc.returncode == 0:
    html = proc.stdout
    check('rendered timeline has collapsed source details', '<details class="timeline-event-sources">' in html and '<details class="timeline-historical-sources">' in html)
    check('global methodology source card remains closed', 'edu-source-card__details" open' not in html)

print('RESULT: service timeline v3.22.39 contract PASS')
