from pathlib import Path
import re
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'dikaiologitika-titlon-spoudon.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'study-title-documents-ui.js').read_text(encoding='utf-8')
checks = []

def check(name, condition):
    condition = bool(condition)
    checks.append((name, condition))
    print(('PASS' if condition else 'FAIL') + ': ' + name)

check('study-title UI controller extracted', "edu_asset_url('includes/study-title-documents-ui.js')" in page)
check('no inline JS block remains', re.search(r'<script(?![^>]*\bsrc=)[^>]*>\s*\S', page, re.I | re.S) is None)
check('no inline action handlers remain', re.search(r'\son(?:click|change|input|submit|blur|keyup|keydown)\s*=', page, re.I) is None)
check('title type handler moved', 'onchange="updateQuestions()"' not in page and 'titleType.addEventListener("change", updateQuestions)' in ui)
check('foreign recognition handler moved', 'onchange="updateForeignExemptionQuestion()"' not in page and 'foreignRecognition.addEventListener("change", updateForeignExemptionQuestion)' in ui)
check('canonical action button', 'id="showDocumentsBtn"' in page and 'getElementById("showDocumentsBtn")' in ui)
check('click listener wired externally', 'showButton.addEventListener("click", showDocuments)' in ui)
check('UI auto-initializes', 'DOMContentLoaded", init' in ui and 'init();' in ui)
check('UI exposes stable controller', 'StudyTitleDocumentsUI = Object.freeze' in ui)

proc = subprocess.run(['php', 'dikaiologitika-titlon-spoudon.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    doc = html.document_fromstring(proc.stdout)
    ids = doc.xpath('//*[@id]/@id')
    check('render has no duplicate ids', len(ids) == len(set(ids)))
    check('render contains external UI controller', 'includes/study-title-documents-ui.js' in proc.stdout)

failed = [name for name, ok in checks if not ok]
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
