from pathlib import Path
import subprocess

ROOT=Path(__file__).resolve().parents[1]
layout=(ROOT/'includes/components/calculator-layout.php').read_text(encoding='utf-8')
css=(ROOT/'assets/common.css').read_text(encoding='utf-8')
page=(ROOT/'ypologismos-morion-topothetisis-neodioriston.php').read_text(encoding='utf-8')
ui=(ROOT/'includes/newly-appointed-placement-ui.js').read_text(encoding='utf-8')
checks=[]
def check(name, ok):
    checks.append((name,bool(ok)))
    print(('PASS' if ok else 'FAIL')+': '+name)

check('calculatorActions emits canonical edu-actions class', "' edu-actions'" in layout)
check('canonical action class participates in shared gap rule', '.edu-actions,' in css and 'body.edu-ui .actions,' in css)
check('standard calculator actions remain one column on mobile', 'body.edu-ui.edu-calc-standard .actions { grid-template-columns:1fr; }' in css)
check('placement UI extracted', "edu_asset_url('includes/newly-appointed-placement-ui.js')" in page)
check('placement page has no inline UI IIFE', '<script>\n(function () {' not in page)
check('placement UI auto initializes', "DOMContentLoaded', init" in ui and 'var initialized = false;' in ui)
check('placement UI still calls calculation engine', 'NewlyAppointedPlacementCalculations' in ui and 'calc.calculate' in ui)
check('tie-break explanation uses mobile disclosure', "'summary' => 'Σειρά κριτηρίων σε ισοβαθμία'" in page and "'data-mobile-collapsed' => 'true'" in page)
check('important municipality warning remains always visible', '<strong>Σημαντικό:</strong> συνυπηρέτηση και εντοπιότητα' in page)
proc=subprocess.run(['php','ypologismos-morion-topothetisis-neodioriston.php'],cwd=ROOT,capture_output=True,text=True)
check('placement PHP renders', proc.returncode == 0)
if proc.returncode == 0:
    html=proc.stdout
    check('render includes external placement UI', 'includes/newly-appointed-placement-ui.js' in html)
    check('render has responsive action component', 'actions edu-actions' in html)
    check('render has mobile-collapsible tie-break details', 'data-mobile-collapsed="true"' in html and 'Σειρά κριτηρίων σε ισοβαθμία' in html)
failed=[n for n,ok in checks if not ok]
print(f"\n{len(checks)-len(failed)}/{len(checks)} checks passed")
raise SystemExit(1 if failed else 0)
