#!/usr/bin/env python3
"""Focused contract for the 2026 Artistic Gymnasium timetable ↔ assignment bridge."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require "includes/weekly-timetable-data.php";
require "includes/teaching-assignments-data.php";
echo json_encode(array(
  "timetable" => weeklyTimetableRows(),
  "assignments" => teachingAssignmentsData()
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
payload = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))
rows = {r.get('course_id'): r for r in payload['timetable']}
assignments = payload['assignments']
checks = []

def check(name, condition):
    checks.append((name, bool(condition)))

# FEK B 2104/2026: Artistic Gymnasium has only French/German, 2h in A/B/C.
foreign = rows.get('kgym.deyteri_xeni', {})
check('art gym foreign row exists', bool(foreign))
check('art gym foreign title exact', foreign.get('subject') == 'Γαλλικά / Γερμανικά')
check('art gym foreign hours exact', foreign.get('hours') == {'Α΄': 2, 'Β΄': 2, 'Γ΄': 2})
check('art gym foreign is choice-dependent', foreign.get('assignment_link_status') == 'choice_dependent')
options = foreign.get('assignment_choice_options') or []
check('art gym exactly two language branches', [o.get('label') for o in options] == ['Γαλλικά', 'Γερμανικά'])
check('art gym French branch PE05', len(options) > 0 and options[0].get('codes') == ['ΠΕ05'])
check('art gym German branch PE07', len(options) > 1 and options[1].get('codes') == ['ΠΕ07'])
check('art gym no Italian branch', not any(o.get('label') == 'Ιταλικά' for o in options))

# General Gymnasium FEK B 2583/2026 is broader, but the special timetable narrows
# the actual Artistic/Music Gymnasium subject to French/German only.
art_foreign_assignments = [
    a for a in assignments
    if a.get('school') == 'kallitexniko_gymnasio'
    and ('Ξένη Γλώσσα' in a.get('subject', '') or 'Γαλλικά' in a.get('subject', ''))
]
check('art gym has one foreign assignment row', len(art_foreign_assignments) == 1)
if art_foreign_assignments:
    a = art_foreign_assignments[0]
    check('art gym assignment title restricted', a.get('subject') == 'Γαλλικά / Γερμανικά')
    check('art gym assignment codes restricted', a.get('A') == ['ΠΕ05', 'ΠΕ07'])
    check('art gym assignment covers A/B/C', a.get('grades') == ['Α΄', 'Β΄', 'Γ΄'])
    check('art gym assignment excludes PE34', 'ΠΕ34' not in (a.get('A') or []))
    check('art gym assignment note explains restriction', 'μόνο Γαλλικά ή Γερμανικά' in a.get('note', ''))

regular_foreign = next((
    a for a in assignments
    if a.get('school') == 'gymnasio' and a.get('subject') == '2η Ξένη Γλώσσα (Γαλλικά / Γερμανικά / Ιταλικά)'
), None)
check('regular gym broader foreign row preserved', bool(regular_foreign))
check('regular gym PE34 preserved', regular_foreign is not None and 'ΠΕ34' in (regular_foreign.get('A') or []))

# Same special-timetable restriction also applies to Music Gymnasium; protect the
# cleanup discovered while fixing the shared general-education copy path.
music_foreign = next((
    a for a in assignments
    if a.get('school') == 'mousiko_gymnasio' and a.get('subject') == 'Γαλλικά / Γερμανικά'
), None)
check('music gym restricted row retained', bool(music_foreign))
check('music gym PE34 excluded', music_foreign is not None and 'ΠΕ34' not in (music_foreign.get('A') or []))

# Source transparency on both public tools.
timetable_page = (ROOT / 'orologio-programma-mathimaton.php').read_text()
assignment_page = (ROOT / 'anatheseis-mathimaton.php').read_text()
for label, text in [('timetable page', timetable_page), ('assignment page', assignment_page)]:
    check(f'{label} cites FEK 2104/2026', '2104/2026' in text)
    check(f'{label} cites FEK 2583/2026', '2583/2026' in text)
    check(f'{label} cites FEK 3418/2024', '3418/2024' in text)

# Internal bridge metadata must never leak to the browser payload.
public_php = r'''require "includes/weekly-timetable-data.php"; echo json_encode(weeklyTimetablePublicRows(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);'''
public_rows = json.loads(subprocess.check_output(['php', '-r', public_php], cwd=ROOT, text=True))
check(
    'public timetable payload strips assignment metadata',
    all(not any(str(k).startswith('assignment_') for k in row) for row in public_rows),
)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
