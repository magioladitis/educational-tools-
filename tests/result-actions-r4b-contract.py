#!/usr/bin/env python3
from pathlib import Path
import re, sys

ROOT = Path(__file__).resolve().parents[1]
PAGES = [
    'ypologismos-morion.php',
    'ypologismos-morion-1ea-2025.php',
    'ypologismos-morion-1gt-2024.php',
    'ypologismos-morion-2ea-2025.php',
    'ypologismos-morion-3ea-2025.php',
    'ypologismos-morion-4ea-2025.php',
    'ypologismos-morion-5ea-2022.php',
    'ypologismos-morion-apospasis-dimos.php',
    'ypologismos-morion-apospasis-evropaika-scholeia.php',
    'ypologismos-morion-apospasis-exoteriko.php',
    'ypologismos-morion-diefthynton-ypodiefthynton-sde.php',
    'ypologismos-morion-mitroo-sde.php',
    'ypologismos-morion-sivitanidios-saek.php',
]

passes = 0
fails = []

def check(cond, label):
    global passes
    if cond:
        passes += 1
        print('PASS', label)
    else:
        fails.append(label)
        print('FAIL', label)

for page in PAGES:
    text = (ROOT / page).read_text(encoding='utf-8')
    markup = text.split('<script', 1)[0]
    positions = [m.start() for m in re.finditer('Αντιγραφ', markup)]
    check(bool(positions), f'{page}: copy utility action found')
    fragments = [markup[max(0, pos-260):pos+120] for pos in positions]
    check(all('secondary' in frag for frag in fragments), f'{page}: every copy utility is secondary')

# Fixed-cap extra utilities remain secondary.
dimos = (ROOT / 'ypologismos-morion-apospasis-dimos.php').read_text(encoding='utf-8')
for label in ('Εκτύπωση', 'Φόρτωση παραδείγματος', 'Μηδενισμός'):
    pos = dimos.find(label)
    frag = dimos[max(0, pos-220):pos+80] if pos >= 0 else ''
    check(pos >= 0 and 'secondary' in frag, f'DIMOS: {label} remains secondary')

# Hybrid/manual-value actions must retain their distinct hierarchy.
aposp = (ROOT / 'ypologismos-morion-apospasis.php').read_text(encoding='utf-8')
onas = (ROOT / 'ypologismos-morion-onaseia.php').read_text(encoding='utf-8')
check("'Έλεγχος & προβολή αποτελέσματος', 'class' => 'primary-btn'" in aposp, 'General detachment: hybrid action stays primary')
check("'Έλεγχος & υπολογισμός'" in onas and "onclick' => 'calculatePoints()" in onas, 'Onaseia: hybrid calculate action retained')

print(f'\n{passes}/{passes + len(fails)} PASS')
if fails:
    sys.exit(1)
