from pathlib import Path
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
PAGE = ROOT / 'ypologismos-morion-onaseia.php'
CSS = ROOT / 'assets/common.css'

checks = []
def check(name, ok):
    checks.append(bool(ok))
    print(('PASS ' if ok else 'FAIL ') + name)

source = PAGE.read_text(encoding='utf-8')
css = CSS.read_text(encoding='utf-8')

check('Onaseia uses shared columns', 'calculatorColumnsStart(' in source)
check('Onaseia uses shared main', 'calculatorMainStart(' in source)
check('Onaseia uses shared results', 'calculatorResultsStart(' in source)
check('Onaseia uses canonical score header', source.count('calculatorScoreHeader(') == 1)
check('Onaseia has academic summary row', "'value_id' => 'resAcademic'" in source)
check('Onaseia has service summary row', "'value_id' => 'resService'" in source)
check('Onaseia has status message', "'id' => 'sidebarStatus'" in source)
check('Onaseia sidebar updates from computed total', 'updateSidebarSummary(' in source and 'academicPoints' in source and 'service.points' in source)
check('Onaseia layout CSS exists', 'body.edu-page-onaseia .layout{display:grid' in css)
check('Onaseia sticky results CSS exists', 'body.edu-page-onaseia .results{position:sticky' in css)
check('Onaseia responsive single-column CSS exists', '@media(max-width:900px){body.edu-page-onaseia .layout{grid-template-columns:1fr}' in css)

proc = subprocess.run(['php', PAGE.name], cwd=ROOT, capture_output=True, text=True)
check('Onaseia PHP render', proc.returncode == 0)
if proc.returncode == 0:
    doc = html.document_fromstring(proc.stdout)
    check('renders one result aside', len(doc.xpath('//aside[contains(concat(" ", normalize-space(@class), " "), " results ")]')) == 1)
    check('renders one canonical score header', len(doc.xpath('//*[contains(concat(" ", normalize-space(@class), " "), " result-score-header ")]')) == 1)
    ids = doc.xpath('//*[@id]/@id')
    check('keeps grandTotal id', 'grandTotal' in ids)
    check('keeps resAcademic id', 'resAcademic' in ids)
    check('keeps resService id', 'resService' in ids)
    check('keeps result id', 'result' in ids)
    check('no duplicate ids', len(ids) == len(set(ids)))

passed = sum(checks)
failed = len(checks) - passed
print(f'RESULT {passed} PASS / {failed} FAIL')
raise SystemExit(1 if failed else 0)
