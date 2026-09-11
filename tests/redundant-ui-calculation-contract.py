#!/usr/bin/env python3
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
checks = []

def ok(name, cond):
    checks.append((name, bool(cond)))

abroad = (ROOT/'includes/abroad-ui.js').read_text(encoding='utf-8')
transfer = (ROOT/'includes/transfer-ui.js').read_text(encoding='utf-8')

# Abroad calculate owns updateUI; callers must not pre-run it.
calc = re.search(r'function calculate\(\)\s*\{(.*?)\n\s*\}', abroad, re.S)
ok('abroad calculate refreshes UI', calc and 'updateUI();' in calc.group(1))
reset = re.search(r'function reset\(\)\s*\{(.*?)\n\s*\}', abroad, re.S)
init = re.search(r'function init\(\)\s*\{(.*?)(?:\n\s*\}\n\n\s*global\.AbroadSecondmentUI)', abroad, re.S)
ok('abroad reset has one calculate', reset and reset.group(1).count('calculate();') == 1)
ok('abroad reset has no redundant updateUI', reset and 'updateUI();' not in reset.group(1))
ok('abroad init has one calculate', init and init.group(1).count('calculate();') == 1)
ok('abroad init has no redundant updateUI', init and 'updateUI();' not in init.group(1))

# Transfer addRow calculates by default, but batch callers can suppress it.
ok('transfer addRow supports options', 'function addRow(initial, options)' in transfer)
ok('transfer addRow supports silent calculation', 'if (!options.silent) calculate();' in transfer)
ok('transfer reset adds row silently', "addRow({ type: 'school', category: 'A' }, { silent: true });\n    syncMode();" in transfer)
ok('transfer init adds row silently', transfer.count("addRow({ type: 'school', category: 'A' }, { silent: true });\n    syncMode();") == 2)
ok('transfer last-row replacement is silent', "addRow(null, { silent: true });\n      calculate();" in transfer)

failed = [name for name, passed in checks if not passed]
for name, passed in checks:
    print(('PASS' if passed else 'FAIL') + ': ' + name)
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
