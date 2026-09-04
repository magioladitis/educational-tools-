#!/usr/bin/env python3
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'teaching-assignments-data.php').read_text(encoding='utf-8')
EPAL = (ROOT / 'includes' / 'teaching-assignments-epal.php').read_text(encoding='utf-8')
PEPAL_G = (ROOT / 'includes' / 'teaching-assignments-pepal-g.php').read_text(encoding='utf-8')
PAGE = (ROOT / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')

checks = []


def check(name, condition):
    checks.append((name, bool(condition)))


def block(text, start, end):
    return text[text.index(start):text.index(end, text.index(start))]


evening_gym = block(DATA, '$eveningGymAllowedSubjects', '$eveningGelExcludedSubjects')
for absent in (
    'Οικιακή Οικονομία',
    '2η Ξένη Γλώσσα',
    'Φυσική Αγωγή',
    'Τεχνολογία',
    "'Μουσική'",
    "'Καλλιτεχνικά'",
):
    check('evening Gym excludes ' + absent, absent not in evening_gym)
for present in (
    'Μαθηματικά', 'Φυσική', 'Χημεία', 'Βιολογία', 'Ιστορία',
    'Κοινωνική και Πολιτική Αγωγή', 'Οικονομικά', 'Ηθική',
    'Πληροφορική', 'Εργαστήρια Δεξιοτήτων',
):
    check('evening Gym includes ' + present, present in evening_gym)

special_gym = block(DATA, '$specialGymGrades', 'foreach ($dayGeneralRows as $row) {')
check('special Gym Chemistry only B/G', "'Χημεία' => array('Β΄', 'Γ΄')" in special_gym)
check('special Gym Geology only A/B', "'Γεωλογία-Γεωγραφία' => array('Α΄', 'Β΄')" in special_gym)
check('special Gym social education only G', "'Κοινωνική και Πολιτική Αγωγή' => array('Γ΄')" in special_gym)
check('special Gym economics only G', "'Οικονομικά' => array('Γ΄')" in special_gym)
check('Art and Music Gym receive general curriculum', "array('kallitexniko_gymnasio', 'mousiko_gymnasio')" in DATA)
check('Music Gym receives general Arts assignment', "$row['subject'] === 'Καλλιτεχνικά'" in DATA)
check('Art and Music Lyceum receive GEL curriculum', "array('kallitexniko_gel', 'mousiko_gel')" in DATA)
check('special Lyceum excludes second foreign language', "strpos($row['subject'], '2η Ξένη Γλώσσα') !== 0" in DATA)

check('evening EPAL B exclusion set', "'Β΄' => array('Θρησκευτικά', 'Ηθική', 'Φυσική Αγωγή')" in EPAL)
check('evening EPAL G exclusion set', "'Γ΄' => array('Χημεία', 'Φυσική Αγωγή')" in EPAL)
check('evening EPAL exclusions affect only general education', "strpos($row['section'], 'Μαθήματα Γενικής Παιδείας') === 0" in EPAL)

for fek in ('453/13-02-2020', '3609/29-08-2020', '418/30-01-2023', '5206/28-08-2023', '1975/23-04-2025', '2625/11-05-2026'):
    check('EPAL source ' + fek, fek in EPAL)

for fek in ('2583/07-05-2026', '2106/09-04-2026', '2102/09-04-2026', '3275/11-06-2026', '3216/05-06-2026'):
    check('page source ' + fek, fek in PAGE)
for fek in ('453/2020', '3609/2020', '418/2023', '5206/2023', '1975/2025', '2625/2026', '2104/09-04-2026', '2107/09-04-2026'):
    check('page source ' + fek, fek in PAGE)

check('umbrella codes hidden from specialty selector data', all(
    code in DATA for code in ("'ΠΕ04'", "'ΠΕ79'", "'ΠΕ87'", "'ΠΕ88'", "'ΠΕ89'", "'ΠΕ91'", "'ΤΕ'")
))
check('duplicate PEPAL assignment removed', '$specialties = $specialties =' not in PEPAL_G)

# Lightweight delimiter check outside quoted strings and comments. This catches
# accidental structural breakage when PHP CLI is unavailable in the test host.
def delimiters_balanced(text):
    stack = []
    pairs = {')': '(', ']': '[', '}': '{'}
    quote = None
    escaped = False
    line_comment = False
    block_comment = False
    i = 0
    while i < len(text):
        char = text[i]
        nxt = text[i + 1] if i + 1 < len(text) else ''
        if line_comment:
            if char == '\n':
                line_comment = False
            i += 1
            continue
        if block_comment:
            if char == '*' and nxt == '/':
                block_comment = False
                i += 2
            else:
                i += 1
            continue
        if quote:
            if escaped:
                escaped = False
            elif char == '\\':
                escaped = True
            elif char == quote:
                quote = None
            i += 1
            continue
        if char in "'\"":
            quote = char
        elif char == '/' and nxt == '/':
            line_comment = True
            i += 2
            continue
        elif char == '/' and nxt == '*':
            block_comment = True
            i += 2
            continue
        elif char in '([{':
            stack.append(char)
        elif char in ')]}':
            if not stack or stack.pop() != pairs[char]:
                return False
        i += 1
    return not stack and quote is None and not block_comment


for path in (
    ROOT / 'includes' / 'teaching-assignments-data.php',
    ROOT / 'includes' / 'teaching-assignments-epal.php',
    ROOT / 'includes' / 'teaching-assignments-pepal-g.php',
):
    check(path.name + ' delimiters balanced', delimiters_balanced(path.read_text(encoding='utf-8')))

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks) - len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
