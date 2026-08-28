from pathlib import Path
import subprocess
import re
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
LAYOUT = ROOT / 'includes/components/calculator-layout.php'
PAGES = sorted(ROOT.glob('ypologismos-morion*.php'))

passes = 0
fails = 0

def check(name, condition):
    global passes, fails
    if condition:
        print('PASS', name)
        passes += 1
    else:
        print('FAIL', name)
        fails += 1

layout = LAYOUT.read_text(encoding='utf-8')
source_by_page = {p.name: p.read_text(encoding='utf-8') for p in PAGES}
source = '\n'.join(source_by_page.values())

check('layout defines compatibility calculatorTotalBlock', 'function calculatorTotalBlock' in layout)
check('layout defines canonical calculatorScoreHeader', 'function calculatorScoreHeader' in layout)
check('layout defines calculatorSubtotalRow', 'function calculatorSubtotalRow' in layout)
check('hero helper supports intro_attrs', "intro_attrs" in layout)
check('18 declarative calculatorHero calls', source.count('calculatorHero(') == 18)
check('only 1 manual calculatorHeroStart remains', source.count('calculatorHeroStart(') == 1)
check('manual hero remains only on DIMOS special two-paragraph hero', 'calculatorHeroStart(' in source_by_page['ypologismos-morion-apospasis-dimos.php'])
for name, text in source_by_page.items():
    if name != 'ypologismos-morion-apospasis-dimos.php':
        check(name + ' no manual hero wrapper', 'calculatorHeroStart(' not in text)
check('at least 14 canonical score headers', source.count('calculatorScoreHeader(') >= 14)
check('0 production uses of legacy total helper', source.count('calculatorTotalBlock(') == 0)
check('14 shared subtotal rows', source.count('calculatorSubtotalRow(') == 14)
check('0 literal subtotal blocks', '<div class="subtot">' not in source)

# R8: all calculator score headers, including DIMOS, are component-backed.
check('0 literal total wrappers remain', source.count('<div class="total">') == 0)
for name in ['ypologismos-morion-apospasis-dimos.php','ypologismos-morion-apospasis-evropaika-scholeia.php','ypologismos-morion-apospasis-psifiako-frontistirio.php','ypologismos-morion-apospasis-sde.php']:
    check(name + ' uses canonical score header', 'calculatorScoreHeader(' in source_by_page[name])

# Presentation component must remain free of scoring/business engines.
for token in ['EducationAcademic', 'EducationService', 'EducationSocial', 'TEAcademic', 'EducationLanguages', 'calculateAsepService']:
    check('layout excludes business token ' + token, token not in layout)

for page in PAGES:
    proc = subprocess.run(['php', page.name], cwd=ROOT, capture_output=True, text=True)
    check(page.name + ' PHP render', proc.returncode == 0)
    if proc.returncode != 0:
        continue
    doc = html.document_fromstring(proc.stdout)
    ids = doc.xpath('//*[@id]/@id')
    check(page.name + ' no duplicate ids', len(ids) == len(set(ids)))
    check(page.name + ' has one hero', len(doc.xpath("//*[contains(concat(' ', normalize-space(@class), ' '), ' hero ')]")) == 1)

print(f'RESULT {passes} PASS / {fails} FAIL')
raise SystemExit(1 if fails else 0)
