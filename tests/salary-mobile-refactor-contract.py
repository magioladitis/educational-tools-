from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'ypologismos-misthologikou-klimakiou.php').read_text(encoding='utf-8')
layout = (ROOT / 'includes/components/calculator-layout.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes/salary-ui.js').read_text(encoding='utf-8')
common = (ROOT / 'assets/common.js').read_text(encoding='utf-8')

checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))
    print(('PASS' if cond else 'FAIL') + ': ' + name)

check('salary page has mobile page class', 'edu-page-salary' in page)
check('salary UI controller extracted', "edu_asset_url('includes/salary-ui.js')" in page)
check('page has no inline salary initializer', 'EducationSalaryUI.init()' not in page)
check('salary UI auto-initializes itself', "DOMContentLoaded', init" in ui and 'let initialized = false;' in ui)
check('large inline controller removed', 'function calculate()' not in page and 'function renderPrintSheet' not in page)
check('UI exports reusable initializer', 'EducationSalaryUI = Object.freeze' in ui and 'init: init' in ui)
check('UI still calls scale engine', 'EducationSalaryScale.calculate' in ui)
check('UI still calls net engine', 'EducationSalaryNet.calculate' in ui)
check('shared streaming disclosure helper exists', 'function calculatorDisclosureStart' in layout and 'function calculatorDisclosureEnd' in layout)
check('salary explanations use shared disclosure', page.count('calculatorDisclosure(') >= 7)
check('legacy large info-note boxes removed from salary page', 'class="info-note"' not in page)
check('result has three mobile-collapsible detail groups', page.count("'data-mobile-collapsed' => 'true'") >= 3 and page.count("'class' => 'edu-result-disclosure'") >= 3)
check('key net figures remain outside detail groups', page.index("value_id' => 'grossForNetResult'") < page.index("'summary' => 'Σύνθεση αποδοχών & ασφαλιστικές βάσεις'") and page.index("value_id' => 'estimatedNetResult'") < page.index("'summary' => 'Σύνθεση αποδοχών & ασφαλιστικές βάσεις'"))
check('source card uses global responsive helper', 'sourceCardStart();' in page and 'sourceCardEnd();' in page and 'sourceCardDisclosureStart' not in page)
check('generic mobile collapse supports result disclosures', '.edu-disclosure[data-mobile-collapsed="true"]' in common)
check('other deductions remain interactive inside disclosure', "'id' => 'otherDeductionsPanel'" in page and 'id="adedYDeduction"' in page)
check('deduction composition one-click detail preserved', 'id="deductionBreakdownToggle"' in page and 'id="deductionBreakdownDetails"' in page)
check('dedicated full print sheet preserved', 'id="payrollPrintSheet"' in page and 'id="payrollPrintContent"' in page)

proc=subprocess.run(['php','ypologismos-misthologikou-klimakiou.php'],cwd=ROOT,capture_output=True,text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    html=proc.stdout
    ids=re.findall(r'\bid="([^"]+)"',html)
    duplicates=sorted({x for x in ids if ids.count(x)>1})
    check('render has no duplicate ids', not duplicates)
    check('render has responsive source card', 'edu-source-card--responsive' in html and 'data-mobile-collapsed="true"' in html)
    check('desktop result detail groups default open', html.count('data-mobile-collapsed="true" open class="edu-disclosure edu-result-disclosure"') >= 3)

failed=[n for n,ok in checks if not ok]
print(f"\n{len(checks)-len(failed)}/{len(checks)} checks passed")
raise SystemExit(1 if failed else 0)
