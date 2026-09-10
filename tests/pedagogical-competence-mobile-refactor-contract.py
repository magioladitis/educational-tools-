from pathlib import Path
import re
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'paidagogiki-eparkeia.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'pedagogical-competence-ui.js').read_text(encoding='utf-8')
checks = []

def check(name, condition):
    condition = bool(condition)
    checks.append((name, condition))
    print(('PASS' if condition else 'FAIL') + ': ' + name)

check('pedagogy UI controller extracted', "edu_asset_url('includes/pedagogical-competence-ui.js')" in page)
check('no inline JS block remains', re.search(r'<script(?![^>]*\bsrc=)[^>]*>\s*\S', page, re.I | re.S) is None)
check('no inline action handlers remain', re.search(r'\son(?:click|change|input|submit|blur|keyup|keydown)\s*=', page, re.I) is None)
check('canonical proof select has no onchange', 'id="proofType"' in page and 'onchange="updateVisibility()"' not in page)
check('canonical action button', 'id="checkEparkeiaBtn"' in page and 'getElementById("checkEparkeiaBtn")' in ui)
check('change listener wired externally', 'proofType.addEventListener("change", updateVisibility)' in ui)
check('click listener wired externally', 'checkButton.addEventListener("click", checkEparkeia)' in ui)
check('UI auto-initializes', 'DOMContentLoaded", init' in ui and 'else {' in ui and 'init();' in ui)
check('UI exposes stable controller', 'PedagogicalCompetenceUI = Object.freeze' in ui)

proc = subprocess.run(['php', 'paidagogiki-eparkeia.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    doc = html.document_fromstring(proc.stdout)
    ids = doc.xpath('//*[@id]/@id')
    check('render has no duplicate ids', len(ids) == len(set(ids)))
    check('render contains external UI controller', 'includes/pedagogical-competence-ui.js' in proc.stdout)

failed = [name for name, ok in checks if not ok]
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
