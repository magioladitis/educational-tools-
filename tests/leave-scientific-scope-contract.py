#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
perm = (ROOT / 'includes/permanent-leaves-data.php').read_text(encoding='utf-8')
sub = (ROOT / 'includes/substitute-leaves-data.php').read_text(encoding='utf-8')
compare = (ROOT / 'adeies-sygkrisi.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes/leave-comparison-ui.js').read_text(encoding='utf-8')

checks = []
def check(ok, label):
    checks.append((bool(ok), label))

check("'scope' => array('key' => 'domestic_or_abroad', 'label' => 'Εσωτερικό ή εξωτερικό')" in perm,
      'permanent scientific leave scope includes domestic or abroad')
check('στο εσωτερικό ή το εξωτερικό' in perm,
      'permanent conditions explicitly mention interior or abroad')
check('αναγκαίες ημέρες μετάβασης και επιστροφής' in perm,
      'permanent conditions include travel days')
check("'scope' => array('key' => 'domestic_only', 'label' => 'Μόνο στο εσωτερικό')" in sub,
      'substitute scientific leave scope is domestic only')
check('μόνο στο εσωτερικό' in sub,
      'substitute conditions explicitly state domestic-only restriction')
check('αναγκαίες ημέρες μετάβασης και επιστροφής' in sub,
      'substitute conditions include travel days')
check("scope'" in compare or "comparison" in compare,
      'comparison payload carries comparison metadata')
check("Πεδίο δικαιώματος" in ui,
      'comparison UI renders scope differences')

failed = [label for ok, label in checks if not ok]
for ok, label in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + label)
if failed:
    raise SystemExit(f'{len(failed)} checks failed')
print(f'RESULT: {len(checks)}/{len(checks)} PASS')
