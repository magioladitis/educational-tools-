#!/usr/bin/env python3
from pathlib import Path
import re, sys, subprocess
root=Path(__file__).resolve().parents[1]
layout=(root/'includes/components/calculator-layout.php').read_text()
css=(root/'assets/common.css').read_text()
config=(root/'includes/config.php').read_text()
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
check('calculatorResultMessage helper exists', 'function calculatorResultMessage' in layout)
for variant in ['status','success','warning','disclaimer']:
    check('helper supports '+variant, "'"+variant+"'" in layout)
    check('CSS result-message variant '+variant, '.result-message--'+variant in css)
check('neutral status message style exists', '.edu-message--status' in css)
check('central cache version is 3.20.61', "EDU_TOOLS_VERSION', '3.20.61" in config)
standard={
 'ypologismos-morion.php':['pedagogicalPriorityBox','sidebarStatus'],
 'ypologismos-morion-1ea-2025.php':['priorityBox'],
 'ypologismos-morion-1gt-2024.php':['priorityBox'],
 'ypologismos-morion-2ea-2025.php':['priorityBox','specialPriorityBox'],
 'ypologismos-morion-3ea-2025.php':['tableStatus','eligibilityWhy'],
 'ypologismos-morion-4ea-2025.php':['priorityBox'],
 'ypologismos-morion-5ea-2022.php':['priorityBox'],
 'ypologismos-morion-sivitanidios-saek.php':['resultStatus','warningBox','breakdownBox'],
}
for fn, ids in standard.items():
    text=(root/fn).read_text()
    for id_ in ids:
        check(fn+' keeps '+id_, id_ in text)
check('3EA generated priorities use success semantic', 'result-message--success edu-message--success' in (root/'ypologismos-morion-3ea-2025.php').read_text())
check('SDE result messages use semantic status', 'result-message--status edu-message--status' in (root/'ypologismos-morion-apospasis-sde.php').read_text())
check('SDE result messages use semantic warning', 'result-message--warning edu-message--warning' in (root/'ypologismos-morion-apospasis-sde.php').read_text())
check('SDE result messages use semantic success', 'result-message--success edu-message--success' in (root/'ypologismos-morion-apospasis-sde.php').read_text())
check('Digital tutoring result messages use semantic status', 'result-message--status edu-message--status' in (root/'ypologismos-morion-apospasis-psifiako-frontistirio.php').read_text())
check('Digital tutoring result messages use semantic warning', 'result-message--warning edu-message--warning' in (root/'ypologismos-morion-apospasis-psifiako-frontistirio.php').read_text())
check('Digital tutoring result messages use semantic success', 'result-message--success edu-message--success' in (root/'ypologismos-morion-apospasis-psifiako-frontistirio.php').read_text())
for fn in ['ypologismos-morion.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-4ea-2025.php','ypologismos-morion-apospasis-dimos.php']:
    text=(root/fn).read_text()
    check(fn+' has disclaimer semantic', "'variant' => 'disclaimer'" in text)
failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print(f'RESULT-MESSAGE R3: {len(checks)-len(failed)}/{len(checks)} PASS')
if failed: sys.exit(1)
