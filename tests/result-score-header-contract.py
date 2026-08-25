from pathlib import Path
import re
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
LAYOUT = ROOT / 'includes/components/calculator-layout.php'
CSS = ROOT / 'assets/common.css'
STANDARD = [
    'ypologismos-morion.php',
    'ypologismos-morion-1ea-2025.php',
    'ypologismos-morion-1gt-2024.php',
    'ypologismos-morion-2ea-2025.php',
    'ypologismos-morion-3ea-2025.php',
    'ypologismos-morion-4ea-2025.php',
    'ypologismos-morion-5ea-2022.php',
    'ypologismos-morion-sivitanidios-saek.php',
]
VALUE_IDS = {
    'ypologismos-morion.php': 'grandTotal',
    'ypologismos-morion-1ea-2025.php': 'totalPoints',
    'ypologismos-morion-1gt-2024.php': 'grandTotal',
    'ypologismos-morion-2ea-2025.php': 'totalPoints',
    'ypologismos-morion-3ea-2025.php': 'grandTotal',
    'ypologismos-morion-4ea-2025.php': 'grandTotal',
    'ypologismos-morion-5ea-2022.php': 'grandTotal',
    'ypologismos-morion-sivitanidios-saek.php': 'finalTotal',
}

passes = fails = 0

def check(name, condition):
    global passes, fails
    print(('PASS' if condition else 'FAIL'), name)
    if condition:
        passes += 1
    else:
        fails += 1

layout = LAYOUT.read_text()
css = CSS.read_text()
check('score-header helper exists', 'function calculatorScoreHeader' in layout)
for slot in ('context', 'value_id', 'label', 'cap', 'variant'):
    check('score-header supports ' + slot, slot in layout)
check('canonical score-header CSS exists', 'body.edu-ui .result-score-header {' in css)
check('canonical score value CSS exists', 'body.edu-ui .result-score {' in css)
check('canonical score label CSS exists', 'body.edu-ui .result-score-label' in css)
check('legacy standard total score CSS removed', 'body.edu-ui.edu-calc-standard .total .num' not in css)
check('legacy EA3 score CSS removed', 'body.edu-calc-ea3 .total{' not in css and 'body.edu-calc-ea3 .total-label' not in css)

for name in STANDARD:
    source = (ROOT / name).read_text()
    check(name + ' uses exactly one score header', source.count('calculatorScoreHeader(') == 1)
    check(name + ' does not use legacy total helper', 'calculatorTotalBlock(' not in source)
    check(name + ' has no redundant visible Result heading', not re.search(r'<h2[^>]*>\s*Αποτέλεσμα\s*</h2>', source))

    proc = subprocess.run(['php', name], cwd=ROOT, capture_output=True, text=True)
    check(name + ' PHP render', proc.returncode == 0)
    if proc.returncode != 0:
        continue
    doc = html.document_fromstring(proc.stdout)
    headers = doc.xpath("//*[contains(concat(' ', normalize-space(@class), ' '), ' result-score-header ')]")
    check(name + ' renders exactly one canonical score header', len(headers) == 1)
    if headers:
        check(name + ' score header has accessible group label', headers[0].get('role') == 'group' and headers[0].get('aria-label') == 'Αποτέλεσμα')
        scores = headers[0].xpath(".//*[contains(concat(' ', normalize-space(@class), ' '), ' result-score ')]")
        labels = headers[0].xpath(".//*[contains(concat(' ', normalize-space(@class), ' '), ' result-score-label ')]")
        check(name + ' renders score value slot', len(scores) == 1)
        check(name + ' renders score label slot', len(labels) == 1)
    ids = doc.xpath('//*[@id]/@id')
    check(name + ' keeps score id ' + VALUE_IDS[name], VALUE_IDS[name] in ids)
    check(name + ' no duplicate ids', len(ids) == len(set(ids)))

print(f'RESULT {passes} PASS / {fails} FAIL')
raise SystemExit(1 if fails else 0)
