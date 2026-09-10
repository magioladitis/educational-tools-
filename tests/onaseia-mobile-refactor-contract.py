from pathlib import Path
import re
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'ypologismos-morion-onaseia.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'onaseia-ui.js').read_text(encoding='utf-8')
common = (ROOT / 'assets' / 'common.js').read_text(encoding='utf-8')

checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))
    print(('PASS' if cond else 'FAIL') + ': ' + name)

check('Onaseia UI controller extracted', "edu_asset_url('includes/onaseia-ui.js')" in page)
check('no inline JS block remains', re.search(r'<script(?![^>]*\bsrc=)[^>]*>\s*\S', page, re.I|re.S) is None)
check('no inline action handlers remain', 'onclick' not in page.lower() and 'onclick=' not in ui.lower())
check('UI loads after calculation module', page.find('onaseia-calculations.js') < page.find('onaseia-ui.js'))
check('UI loads after vacancy data module', page.find('onaseia-vacancies-2026.js') < page.find('onaseia-ui.js'))
check('UI uses manual academic validator', 'OnaseiaAcademic.validateManualAcademicPoints' in ui)
check('UI uses shared PE academic controller', 'AsepPeAcademic.calculate("asepPeAcademic")' in ui)
check('UI uses vacancy dataset', 'OnaseiaVacancies2026.selection' in ui)
check('UI auto-initializes', 'DOMContentLoaded", init' in ui and 'else init();' in ui)
check('UI exports initializer', 'OnaseiaUI = Object.freeze({ init })' in ui)
check('canonical add-row button', 'id="addServiceRowBtn"' in page and 'getElementById("addServiceRowBtn")' in ui)
check('canonical calculate button', "'id' => 'calculateBtn'" in page and 'getElementById("calculateBtn")' in ui)
check('canonical reset button', "'id' => 'resetBtn'" in page and 'getElementById("resetBtn")' in ui)
check('dynamic row removal uses event delegation', 'handleServiceRowClick' in ui and 'closest(".remove-row")' in ui)
check('service explanation mobile collapse', 'Πώς προσμετράται η ειδική προϋπηρεσία;' in page and "'data-mobile-collapsed' => 'true'" in page)
check('generic mobile disclosure support remains', '.edu-disclosure[data-mobile-collapsed="true"]' in common)
check('source card uses global responsive default', 'sourceCardStart();' in page and 'sourceCardDisclosureStart' not in page)

proc = subprocess.run(['php', 'ypologismos-morion-onaseia.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    rendered = proc.stdout
    doc = html.document_fromstring(rendered)
    ids = doc.xpath('//*[@id]/@id')
    check('render has no duplicate ids', len(ids) == len(set(ids)))
    check('render contains external UI controller', 'includes/onaseia-ui.js' in rendered)
    check('rendered service disclosure defaults open on desktop', 'data-mobile-collapsed="true" open class="edu-disclosure"' in rendered)

failed=[n for n,ok in checks if not ok]
print(f"RESULT: {len(checks)-len(failed)}/{len(checks)} PASS")
raise SystemExit(1 if failed else 0)
