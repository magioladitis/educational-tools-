from pathlib import Path
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'paidagogiki-eparkeia.php').read_text(encoding='utf-8')
css = (ROOT / 'assets' / 'common.css').read_text(encoding='utf-8')
checks = []

def check(name, condition):
    ok = bool(condition)
    checks.append((name, ok))
    print(('PASS' if ok else 'FAIL') + ': ' + name)

check('legal help helper exists', "function paidagogikiProfessorSchoolHelp()" in page)
check('strict legal definition is named', 'ν. 3194/2003, άρθρο 8 παρ. 2' in page)
check('definition focuses on the specific degree', 'δεν αρκεί ότι ένα πτυχίο οδηγεί σε εκπαιδευτικό κλάδο' in page)
check('definition mentions no extra pedagogical credential', 'χωρίς να απαιτείται πρόσθετο πτυχίο ή πιστοποιητικό παιδαγωγικής κατάρτισης' in page)
check('help is rendered in initial and template professor-school flows', page.count('<?php paidagogikiProfessorSchoolHelp(); ?>') == 2)
check('source link to law exists', 'n3194_03.htm' in page)
check('mobile disclosure has 44px touch target', 'ped-professor-school-help > summary' in css and 'min-height: 44px' in css)

proc = subprocess.run(['php', 'paidagogiki-eparkeia.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    doc = html.document_fromstring(proc.stdout)
    details = doc.xpath('//details[contains(concat(" ", normalize-space(@class), " "), " ped-professor-school-help ")]')
    check('render contains help in initial card and template', len(details) == 2)
    summaries = doc.xpath('//details[contains(concat(" ", normalize-space(@class), " "), " ped-professor-school-help ")]/summary')
    check('summary copy is concise and consistent', len(summaries) == 2 and all('Τι σημαίνει «καθηγητική σχολή»;' in x.text_content() for x in summaries))

failed = [name for name, ok in checks if not ok]
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
