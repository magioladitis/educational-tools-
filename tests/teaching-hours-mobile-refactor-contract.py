from pathlib import Path
import subprocess

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'ypologismos-didaktikou-orariou.php').read_text(encoding='utf-8')
layout = (ROOT / 'includes/components/calculator-layout.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes/teaching-hours-ui.js').read_text(encoding='utf-8')
css = (ROOT / 'assets/common.css').read_text(encoding='utf-8')

checks = []
def check(name, ok):
    checks.append((name, bool(ok)))
    print(('PASS' if ok else 'FAIL') + ': ' + name)

check('page has mobile pilot class', 'edu-page-teaching-hours' in page)
check('shared disclosure helper exists', 'function calculatorDisclosure' in layout)
check('large explanations use shared disclosure', page.count('calculatorDisclosure(') >= 8)
check('legacy teaching-hour info boxes removed', 'class="info-note"' not in page)
check('result rule is progressive disclosure', "'class' => 'edu-result-disclosure'" in page and 'id="ruleResult"' in page)
check('UI controller extracted from PHP', "edu_asset_url('includes/teaching-hours-ui.js')" in page and 'EducationTeachingHoursUI.init()' not in page)
check('UI auto-initializes itself', "DOMContentLoaded', init" in ui and 'let initialized = false;' in ui)
check('PHP page has no controller function definitions', 'function calculate()' not in page and 'function syncFields()' not in page)
check('UI controller exports initializer', 'EducationTeachingHoursUI' in ui and 'init: init' in ui)
check('UI calls independent calculation engine', 'EducationTeachingHours.calculate(options)' in ui)
check('shared disclosure has accessible native details CSS', '.edu-disclosure__summary' in css and '.edu-disclosure[open]' in css)
check('mobile disclosure rules exist', '@media (max-width:650px)' in css and 'edu-page-teaching-hours' in css)
check('disclosure contents print expanded', '.edu-disclosure > .edu-disclosure__body { display: block !important;' in css)

proc = subprocess.run(['php', 'ypologismos-didaktikou-orariou.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    html = proc.stdout
    check('render contains eight or more details', html.count('<details') >= 8)
    check('render contains external UI controller only', 'includes/teaching-hours-ui.js' in html and 'EducationTeachingHoursUI.init()' not in html)
    check('render has one dynamic rule result id', html.count('id="ruleResult"') == 1)

failed = [name for name, ok in checks if not ok]
print(f"\n{len(checks)-len(failed)}/{len(checks)} checks passed")
if failed:
    raise SystemExit(1)
