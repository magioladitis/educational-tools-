#!/usr/bin/env python3
from pathlib import Path
import subprocess
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
checks=[]
def check(name, cond): checks.append((name, bool(cond)))
page=PAGE.read_text(encoding='utf-8')
html=subprocess.run(['php',str(PAGE)],cwd=ROOT,text=True,capture_output=True).stdout
check('lower-priority technical warning is not rendered as a user label', "'χαμηλότερη προτεραιότητα'" not in page)
check('B assignment remains a visible assignment label', "staffingUiPriorityLabel($rowResult['priority']) . ' ανάθεση'" in page)
failed=[name for name,ok in checks if not ok]
for name,ok in checks: print(('PASS' if ok else 'FAIL')+': '+name)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
