#!/usr/bin/env python3
"""Focused contract for EN.E.E.GY.-L. timetable ↔ assignment crosswalk (2026-2027)."""
from pathlib import Path
import json
import subprocess
import re
import unicodedata

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
rows = payload['timetable']
assignments = payload['assignments']

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def norm(text):
    text = unicodedata.normalize('NFC', text or '').lower().strip()
    text = text.replace('–', '-').replace('—', '-').replace('−', '-').replace('&', ' και ')
    text = re.sub(r'\s*-\s*', '-', text)
    text = re.sub(r'\s+', ' ', text)
    return text

ene = [r for r in rows if r.get('school') == 'eneegyl_lykeio']

# Safe title corrections verified against FEK B 2149/2026 and operational myschool naming.
check('no EXCELL typo in ENEEGYL timetable', not any('EXCELL' in (r.get('subject') or '') for r in ene))
excel = next((r for r in ene if r.get('course_id') == 'eneegyl.lykeio.c.admin.3'), None)
check('EXCEL title exact', excel is not None and excel.get('subject') == 'Χρηματοπιστωτικές Συναλλαγές - Λογιστικά Φύλλα (EXCEL)')

tourism = next((r for r in ene if r.get('course_id') == 'eneegyl.lykeio.d.tourism.8'), None)
check('D tourism language is French/German only', tourism is not None and tourism.get('subject') == 'Γαλλικά ή Γερμανικά')
check('D tourism language assignment exists', any(
    a.get('school') == 'eneegyl_lykeio' and a.get('grade') == 'Δ΄'
    and a.get('subject') == 'Γαλλικά ή Γερμανικά'
    and set(a.get('A') or []) == {'ΠΕ05', 'ΠΕ07'}
    for a in assignments
))

# Context-scoped aliases: never globalize these differences.
expected_aliases = {
    'eneegyl.lykeio.c.admin.2': 'Στοιχεία Δικαίου (Αστικό-Εμπορικό – Εργατικό-Τουριστικό)',
    'eneegyl.lykeio.b.building.3': 'Σχέδιο Δομικών Έργων με χρήση Η/Υ Ι',
    'eneegyl.lykeio.c.building.4': 'Σχέδιο Δομικών Έργων με χρήση Η/Υ Ι',
}
for course_id, alias in expected_aliases.items():
    row = next((r for r in ene if r.get('course_id') == course_id), None)
    check(f'scoped alias exact: {course_id}', row is not None and row.get('assignment_subject_alias') == alias)

# Structured choice slots are recorded as choices, not falsely counted as direct assignments.
choice_rows = [r for r in ene if r.get('assignment_link_status') == 'choice_dependent']
check('7 choice-dependent ENEEGYL rows', len(choice_rows) == 7)
arts = next((r for r in choice_rows if r.get('course_id') == 'eneegyl.lykeio.c.arts.3'), None)
arts_subjects = {o.get('subject') for o in (arts or {}).get('assignment_choice_options', [])}
check('arts special lab has four concrete options', arts_subjects == {
    'Φωτογραφία και Ηλεκτρονική Επεξεργασία Εικόνας (μάθημα επιλογής)',
    'Τεχνολογία Υφαντικών Υλών (μάθημα επιλογής)',
    'Εργαστήριο Χαρακτικής - Πλαστικής (μάθημα επιλογής)',
    'Εισαγωγή στις Ξύλινες Κατασκευές',
})
for subject in arts_subjects:
    check(f'arts choice assignment exists: {subject}', any(
        a.get('school') == 'eneegyl_lykeio'
        and (a.get('grade') == 'Γ΄' or 'Γ΄' in (a.get('grades') or []))
        and a.get('subject') == subject
        for a in assignments
    ))

health_rows = [r for r in choice_rows if r.get('track') == 'health']
check('6 health special slots classified as choices', len(health_rows) == 6)
check('each health slot exposes nine possible courses', all(len(r.get('assignment_choice_options') or []) == 9 for r in health_rows))

# Explicit regulatory gaps: timetable rows exist, but B/C assignment table has no corresponding row.
expected_gap_ids = {
    'eneegyl.lykeio.c.agriculture.5', 'eneegyl.lykeio.c.agriculture.6',
    'eneegyl.lykeio.c.admin.5', 'eneegyl.lykeio.c.admin.6',
    'eneegyl.lykeio.c.building.5', 'eneegyl.lykeio.c.building.6',
    'eneegyl.lykeio.c.arts.4', 'eneegyl.lykeio.c.arts.5',
    'eneegyl.lykeio.c.electrical.4', 'eneegyl.lykeio.c.electrical.5',
    'eneegyl.lykeio.c.mechanical.5',
    'eneegyl.lykeio.c.it.4', 'eneegyl.lykeio.c.it.5',
    'eneegyl.lykeio.c.health.6', 'eneegyl.lykeio.c.health.7',
    'eneegyl.lykeio.c.health.fallback.5', 'eneegyl.lykeio.c.health.fallback.6',
}
gap_rows = [r for r in ene if r.get('assignment_link_status') == 'regulatory_gap']
check('17 regulatory-gap instances classified', len(gap_rows) == 17)
check('regulatory-gap ids exact', {r.get('course_id') for r in gap_rows} == expected_gap_ids)
for row in gap_rows:
    grade = next(iter(row.get('hours', {})), '')
    direct_exists = any(
        a.get('school') == 'eneegyl_lykeio'
        and (a.get('grade') in ('', grade) or grade in (a.get('grades') or []))
        and norm(a.get('subject')) == norm(row.get('subject'))
        for a in assignments
    )
    check(f'gap not falsely direct-mapped: {row.get("course_id")}', not direct_exists)
    check(f'gap has no forced alias: {row.get("course_id")}', not row.get('assignment_subject_alias'))
    check(f'gap note internal: {row.get("course_id")}', '2149/2026' in (row.get('assignment_link_note') or '') and '3216/2026' in (row.get('assignment_link_note') or ''))

# Public source cards remain FEK-based; myschool is an audit aid, not a normative public source.
assign_page = (ROOT / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')
timetable_page = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
check('assignments source includes FEK 3216/2026', 'ΦΕΚ Β΄ 3216/2026' in assign_page)
check('timetable source includes FEK 2149/2026', 'ΦΕΚ Β΄ 2149/2026' in timetable_page)
check('myschool not exposed as normative source', 'myschool' not in assign_page.lower() and 'myschool' not in timetable_page.lower())

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
