from pathlib import Path
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'paidagogiki-eparkeia.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'pedagogical-competence-ui.js').read_text(encoding='utf-8')
checks = []

def check(name, condition):
    ok = bool(condition)
    checks.append((name, ok))
    print(('PASS' if ok else 'FAIL') + ': ' + name)

check('AEI transition is split into entry year then certification-at-entry', 'data-role="aeiEntryPeriod"' in page and 'data-role="aeiCertifiedAtEntry"' in page)
check('AEI second question is progressive', 'data-subsection="aeiCertifiedAtEntry"' in page and 'aeiEntryPeriod === "up_to_2026"' in ui)
check('domestic MSc/PhD asks broad evidence before named list', 'data-role="domesticEducationEvidence"' in page and 'shouldOfferNamedPostgraduate' in ui)
check('old postgraduate list appears only for uncertain/non-general route', 'domesticEvidence === "no" || domesticEvidence === "unknown"' in ui)
check('named postgraduate requires exact match', 'data-role="namedPostgraduateExactMatch"' in page and 'namedPostgraduateExactMatch === "yes"' in ui)
check('special-program list has search field', 'data-role="namedSpecialProgramSearch"' in page and 'repopulateProgramSelect(card, "special_program"' in ui)
check('postgraduate list has search field', 'data-role="namedPostgraduateSearch"' in page and 'repopulateProgramSelect(card, "postgraduate_prior"' in ui)
check('professor-school graduation is progressive', 'data-subsection="professorGraduation"' in page and 'entryYear === "from_2015" || entryYear === "unknown"' in ui)
check('EPATH cutoff is surfaced inline', 'data-role="epathGuidance"' in page and '12/06/2018' in ui)
check('guided inline status component exists', 'ped-flow-guidance' in page and 'setGuidance' in ui)

proc = subprocess.run(['php', 'paidagogiki-eparkeia.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    doc = html.document_fromstring(proc.stdout)
    check('render contains progressive controls', bool(doc.xpath('//*[@data-role="aeiEntryPeriod"]')) and bool(doc.xpath('//*[@data-role="domesticEducationEvidence"]')))
    check('render keeps search inputs mobile-native', bool(doc.xpath('//input[@type="search" and @data-role="namedSpecialProgramSearch"]')) and bool(doc.xpath('//input[@type="search" and @data-role="namedPostgraduateSearch"]')))

failed = [name for name, ok in checks if not ok]
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
