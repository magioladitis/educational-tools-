#!/usr/bin/env python3
from pathlib import Path
import re
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def ok(name,cond): checks.append((name,bool(cond)))

digital=(ROOT/'includes/digital-tutoring-detachment-ui.js').read_text(encoding='utf-8')
salary=(ROOT/'includes/salary-ui.js').read_text(encoding='utf-8')
hours=(ROOT/'includes/teaching-hours-ui.js').read_text(encoding='utf-8')

ok('digital has side-effect-free EAE sync helper', 'function syncEaeUI()' in digital)
sync=re.search(r'function syncEaeUI\(\)\s*\{(.*?)\n\s*\}', digital, re.S)
ok('digital EAE sync does not calculate', sync and 'calculate();' not in sync.group(1))
ok('digital EAE sync does not render assignments', sync and 'renderAssignments();' not in sync.group(1))
reset_body=digital.split('function resetForm(){',1)[1].split("\n\n  document.getElementById('specialty')",1)[0]
ok('digital reset uses sync helper', 'syncEaeUI();' in reset_body)
ok('digital reset has one orchestrated specialty refresh', reset_body.count('specialtyChanged();')==1)
ok('digital reset avoids toggleEae double work', 'toggleEae();' not in reset_body)
ok('digital bootstrap avoids toggleEae plus specialtyChanged pair', digital.rstrip().endswith('specialtyChanged();\n}());') and '\n  syncEaeUI();\n  specialtyChanged();\n}());' in digital)

for label,txt in [('salary',salary),('teaching-hours',hours)]:
    ok(label+' chooses one event per control', "const eventName = el.tagName === 'SELECT' || el.type === 'checkbox' || el.type === 'radio' ? 'change' : 'input';" in txt)
    # ensure the old paired listener pattern is gone from init
    ok(label+' has no direct paired input listener', "el.addEventListener('input', () =>" not in txt)
    ok(label+' has no direct paired change listener', "el.addEventListener('change', () =>" not in txt)
    ok(label+' binds selected event exactly once', 'el.addEventListener(eventName, () =>' in txt)

failed=[n for n,p in checks if not p]
for n,p in checks: print(('PASS' if p else 'FAIL')+': '+n)
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
