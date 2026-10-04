#!/usr/bin/env python3
from pathlib import Path
import sys

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'ypologismos-misthologikou-klimakiou.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'salary-ui.js').read_text(encoding='utf-8')

checks = []
def check(name, cond):
    checks.append((name, bool(cond)))
    print(('PASS' if cond else 'FAIL') + ': ' + name)

check('family children input is explicitly labelled', 'Τέκνα για οικογενειακή παροχή' in page and 'id="dependentChildren"' in page)
check('separate tax children opt-in exists', 'id="taxChildrenDifferent"' in page and 'Διαφορετικός αριθμός τέκνων για φορολογία' in page)
check('tax children field starts hidden', 'id="taxDependentChildrenField" hidden' in page and 'id="taxDependentChildren"' in page)
check('tax field only affects tax calculation', "const taxChildren = separateTaxChildren ? integer('taxDependentChildren', 20) : familyChildren;" in ui and 'children: taxChildren' in ui)
check('family allowance uses family child count', 'familyAllowanceMonthly(familyChildren)' in ui)
check('opt-in copies current family count', "byId('taxDependentChildren').value = String(integer('dependentChildren', 20));" in ui)
check('print distinguishes both child counts', 'Τέκνα οικογενειακής παροχής:' in ui and 'Εξαρτώμενα τέκνα φορολογίας:' in ui)
check('results expose tax child count', 'taxChildrenResult' in page and "byId('taxChildrenResult').textContent" in ui)
check('reset restores linked default', "byId('taxChildrenDifferent').checked = false;" in ui and "byId('taxDependentChildrenField').hidden = true;" in ui)

failed = [name for name, ok in checks if not ok]
print(f"\n{len(checks)-len(failed)}/{len(checks)} checks passed")
sys.exit(1 if failed else 0)
