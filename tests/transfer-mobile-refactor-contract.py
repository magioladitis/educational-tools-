from pathlib import Path
import subprocess
import hashlib

ROOT = Path(__file__).resolve().parents[1]
page_path = ROOT / 'ypologismos-morion-metathesis.php'
ui_path = ROOT / 'includes' / 'transfer-ui.js'
engine_path = ROOT / 'includes' / 'transfer-calculations.js'
layout_path = ROOT / 'includes' / 'components' / 'calculator-layout.php'

page = page_path.read_text(encoding='utf-8')
ui = ui_path.read_text(encoding='utf-8')
engine = engine_path.read_text(encoding='utf-8')
layout = layout_path.read_text(encoding='utf-8')
checks=[]

def check(name, ok):
    checks.append((name, bool(ok)))
    print(('PASS' if ok else 'FAIL') + ': ' + name)

check('transfer UI controller extracted', "edu_asset_url('includes/transfer-ui.js')" in page)
check('page has no inline transfer IIFE', '<script>\n(function () {' not in page)
check('page contains no transfer controller functions', 'function addRow(' not in page and 'function calculate()' not in page and 'function syncRow(' not in page)
check('UI controller auto initializes', "DOMContentLoaded', init" in ui and 'var initialized = false;' in ui)
check('UI exports stable namespace', 'EducationTransferUI' in ui and 'init: init' in ui and 'calculate: calculate' in ui)
check('UI calls independent calculation engine', 'global.EducationTransfer.calculate(input)' in ui)
check('dynamic MSD rows remain in UI controller', 'transfer-service-row' in ui and 'msdRows.appendChild(row)' in ui)
check('service year UI clamp remains 50', 'clampInput(target, 50)' in ui)
check('abroad/study leave fixed 5/5 remains', 'fixedFullWeek' in ui and "weekdaysInput.disabled = true" in ui and 'σταθερά 5/5' in ui)
check('mode explanation uses shared disclosure', "'id' => 'modeNote'" in page and "'data-mobile-collapsed' => 'true'" in page)
check('remote-double explanation uses shared disclosure', 'Πότε διπλασιάζονται οι Μ.Σ.Δ. απομακρυσμένων σχολείων;' in page)
check('critical first-version limitation remains visible', '<strong>Πρώτη έκδοση:</strong>' in page)
check('source card uses global responsive component', 'sourceCardStart();' in page and 'sourceCardDisclosureStart' not in page)
check('shared action component retained', 'calculatorActions(array(' in page)
check('calculation engine still separate', "edu_asset_url('includes/transfer-calculations.js')" in page and 'function calculateMsdPeriod' in engine)
check('shared disclosure helper available', 'function calculatorDisclosure' in layout)

proc = subprocess.run(['php', 'ypologismos-morion-metathesis.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    html = proc.stdout
    check('render includes external transfer UI', 'includes/transfer-ui.js' in html)
    check('render includes mobile-collapsible explanations', html.count('data-mobile-collapsed="true"') >= 3)
    check('render keeps core result ids unique', all(html.count('id="%s"' % i) == 1 for i in ['totalResult','servicePointsResult','msdPointsResult','statusResult']))

jscheck = subprocess.run(['node', '--check', str(ui_path)], capture_output=True, text=True)
check('transfer UI JavaScript syntax', jscheck.returncode == 0)
phpcheck = subprocess.run(['php', '-l', str(page_path)], capture_output=True, text=True)
check('transfer PHP syntax', phpcheck.returncode == 0)

failed=[name for name, ok in checks if not ok]
print(f"\n{len(checks)-len(failed)}/{len(checks)} checks passed")
raise SystemExit(1 if failed else 0)
