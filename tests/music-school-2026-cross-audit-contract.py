#!/usr/bin/env python3
"""Focused contract for the 2026 Music School timetable ↔ assignment cross-audit."""
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

def assignment_exists(school, subject, grade=None):
    return any(
        a.get('school') == school
        and a.get('subject') == subject
        and (grade is None or a.get('grade') in ('', grade) or grade in (a.get('grades') or []))
        for a in assignments
    )

# Safe, explicit title bridges from FEK B 2107/2026 to FEK B 4202/2018.
aliases = {
    'mgym.music.mousiko_synolo': 'Μουσικά Σύνολα: Οργανοχρησίας ή άλλο',
    'mgym.music.piano': 'Υποχρεωτικό ατομικό μουσικό όργανο - Πιάνο',
    'mgym.music.tambouras': 'Υποχρεωτικό ατομικό μουσικό όργανο - Ταμπουράς ή άλλο παραδοσιακό όργανο αναφοράς',
    'mgel.music.akoustikes_c': 'Ανάπτυξη Ακουστικών Ικανοτήτων',
}
for course_id, alias in aliases.items():
    row = rows.get(course_id, {})
    check(f'{course_id} exists', bool(row))
    check(f'{course_id} alias exact', row.get('assignment_subject_alias') == alias)
    for grade in row.get('hours', {}):
        check(f'{course_id} alias resolves {grade}', assignment_exists(row.get('school'), alias, grade))

# FEK B 2107/2026 does not include Skills Workshops in Music Gymnasium.
# Keep both timetable and inherited general-assignment whitelist guarded.
music_skills_timetable = [
    r for r in rows.values()
    if r.get('school') == 'mousiko_gymnasio' and r.get('subject') == 'Εργαστήρια Δεξιοτήτων'
]
music_skills_assignments = [
    a for a in assignments
    if a.get('school') == 'mousiko_gymnasio' and a.get('subject') == 'Εργαστήρια Δεξιοτήτων'
]
check('music gym has no Skills Workshops in timetable', len(music_skills_timetable) == 0)
check('music gym has no Skills Workshops assignment row', len(music_skills_assignments) == 0)

# The Music Gymnasium second foreign language is a real branch choice: the 2026
# timetable offers French/German, while the assignment table is broader.
foreign = rows.get('mgym.deyteri_xeni', {})
check('music gym foreign language choice status', foreign.get('assignment_link_status') == 'choice_dependent')
options = foreign.get('assignment_choice_options') or []
check('music gym foreign language exactly two choices', [o.get('label') for o in options] == ['Γαλλικά', 'Γερμανικά'])
check('French branch PE05', options[0].get('codes') == ['ΠΕ05'] if len(options) > 0 else False)
check('German branch PE07', options[1].get('codes') == ['ΠΕ07'] if len(options) > 1 else False)
for option in options:
    check(
        f'foreign language target exists {option.get("label")}',
        assignment_exists('mousiko_gymnasio', option.get('subject'), 'Α΄'),
    )

# Do not revive the repealed 2015 assignment table for Theatre / Art History.
gym_gaps = {'mgym.theatro', 'mgym.istoria_texnis'}
for course_id in gym_gaps:
    row = rows.get(course_id, {})
    check(f'{course_id} regulatory gap', row.get('assignment_link_status') == 'regulatory_gap')
    check(f'{course_id} explanatory note', '4202/2018' in row.get('assignment_link_note', ''))

# New/current Lyceum subjects that do not have a same-grade explicit assignment
# in FEK B 4202/2018 stay gaps rather than borrowing another grade's assignment.
lyc_gap_ids = {
    'mgel.music.elliniki_paradosiaki',
    'mgel.c.choice.elliniki_paradosiaki',
    'mgel.c.choice.mousiko_keimeno',
    'mgel.c.choice.analysi_partitouras',
    'mgel.c.choice.choral',
    'mgel.c.choice.ixolipsia2',
}
for course_id in lyc_gap_ids:
    row = rows.get(course_id, {})
    check(f'{course_id} regulatory gap', row.get('assignment_link_status') == 'regulatory_gap')

# Instance counts are intentionally grade-aware: 4 Gymnasium gaps (Theatre x3 +
# Art History x1) and 8 Lyceum gaps (Greek Traditional x3 + five C choices).
music_gym_gap_instances = sum(
    len(r.get('hours', {})) for r in rows.values()
    if r.get('school') == 'mousiko_gymnasio' and r.get('assignment_link_status') == 'regulatory_gap'
)
music_lyc_gap_instances = sum(
    len(r.get('hours', {})) for r in rows.values()
    if r.get('school') == 'mousiko_gel' and r.get('assignment_link_status') == 'regulatory_gap'
)
check('music gym gap instance count 4', music_gym_gap_instances == 4)
check('music lyceum gap instance count 8', music_lyc_gap_instances == 8)

# Source transparency: timetable page must link both the current timetable FEK and
# the assignment FEK used for title cross-checking. Assignment page already does too.
timetable_page = (ROOT / 'orologio-programma-mathimaton.php').read_text()
assignment_page = (ROOT / 'anatheseis-mathimaton.php').read_text()
for label, text in [('timetable page', timetable_page), ('assignment page', assignment_page)]:
    check(f'{label} cites FEK 2107/2026', '2107/2026' in text)
    check(f'{label} cites FEK 4202/2018', '4202/2018' in text)

check('timetable page uses browser-safe public rows', 'weeklyTimetablePublicRows()' in timetable_page)
public_php = r'''require "includes/weekly-timetable-data.php"; echo json_encode(weeklyTimetablePublicRows(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);'''
public_rows = json.loads(subprocess.check_output(['php', '-r', public_php], cwd=ROOT, text=True))
check(
    'public timetable payload strips assignment metadata',
    all(not any(str(k).startswith('assignment_') for k in row) for row in public_rows),
)
check(
    'internal timetable retains assignment metadata',
    any(any(str(k).startswith('assignment_') for k in row) for row in payload['timetable']),
)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
