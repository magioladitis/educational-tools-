from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'ypologismos-morion-apospasis-exoteriko.php').read_text(encoding='utf-8')
engine = (ROOT / 'includes' / 'abroad-calculations.js').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'abroad-ui.js').read_text(encoding='utf-8')
common = (ROOT / 'assets' / 'common.js').read_text(encoding='utf-8')

checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))
    print(('PASS' if cond else 'FAIL') + ': ' + name)

check('abroad UI controller extracted', "edu_asset_url('includes/abroad-ui.js')" in page)
check('no inline JS block remains', re.search(r'<script(?![^>]*\bsrc=)[^>]*>\s*\S', page, re.I|re.S) is None)
check('no inline action handlers remain', 'onclick' not in page.lower())
check('calculation engine loaded before UI', page.find('abroad-calculations.js') < page.find('abroad-ui.js'))
check('UI uses calculation engine', 'AbroadSecondment.calculate(values())' in ui)
check('calculation engine remains DOM-free', 'document.' not in engine and 'getElementById' not in engine and 'querySelector' not in engine)
check('UI has no PHP fragments', '<?php' not in ui and 'json_encode' not in ui)
check('UI reads rendered specialty label', 'selectedSpecialtyLabel' in ui and 'select.options[select.selectedIndex]' in ui)
check('UI auto-initializes', "DOMContentLoaded', init" in ui and 'else init();' in ui)
check('UI exports initializer', 'AbroadSecondmentUI = Object.freeze({ init })' in ui)
check('copy button remains wired', "$('copyBtn')" in ui and "addEventListener('click', copySummary)" in ui)
check('reset button remains wired', "$('resetBtn')" in ui and "addEventListener('click', reset)" in ui)
check('tie-break details mobile collapse', 'Σειρά κριτηρίων ισοβαθμίας' in page and "'data-mobile-collapsed' => 'true'" in page)
check('generic mobile disclosure support remains', '.edu-disclosure[data-mobile-collapsed="true"]' in common)
check('source card uses global responsive default', 'sourceCardStart();' in page and 'sourceCardDisclosureStart' not in page)

proc = subprocess.run(['php', 'ypologismos-morion-apospasis-exoteriko.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    html = proc.stdout
    ids = re.findall(r'\bid="([^"]+)"', html)
    check('render has no duplicate ids', len(ids) == len(set(ids)))
    check('render contains external UI controller', 'includes/abroad-ui.js' in html)
    check('rendered mobile disclosure defaults open on desktop', 'data-mobile-collapsed="true" open class="edu-disclosure"' in html)

failed=[n for n,ok in checks if not ok]
print(f"RESULT: {len(checks)-len(failed)}/{len(checks)} PASS")
raise SystemExit(1 if failed else 0)
