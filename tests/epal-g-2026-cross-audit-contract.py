#!/usr/bin/env python3
"""Γ΄ ΕΠΑ.Λ. 2026-2027: timetable ↔ assignments cross-audit contract.

Covers the 35 regular EPAL specialties for daytime and 3-year evening EPAL,
including audited aliases, split theory/lab assignment bridges and the
four-language Tourism choice.
"""
from pathlib import Path
from collections import Counter, defaultdict
import json
import re
import subprocess
import unicodedata

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require "includes/weekly-timetable-data.php";
require "includes/teaching-assignments-data.php";
echo json_encode(array(
  "rows" => weeklyTimetableRows(),
  "assignments" => teachingAssignmentsData(),
  "specialties" => weeklyTimetableVocationalSpecialties()
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
payload = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))
rows = payload['rows']
assignments = payload['assignments']

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def norm(text):
    text = unicodedata.normalize('NFC', text or '').lower().strip()
    text = text.replace('–', '-').replace('—', '-').replace('−', '-').replace('&', ' και ')
    text = re.sub(r'\s*-\s*', '-', text)
    return re.sub(r'\s+', ' ', text)

def hours_total(row):
    return float((row.get('hours') or {}).get('Γ΄', 0))

specialty_labels = {}
for _track, items in payload['specialties'].items():
    specialty_labels.update(items)
check('EPAL G specialty catalog has exactly 35 specialties', len(specialty_labels) == 35)

for school, expected_general, expected_specialty in (
    ('epal', 12, 23),
    ('esperino_epal', 10, 20),
):
    school_rows = [r for r in rows if r.get('school') == school and 'Γ΄' in (r.get('hours') or {})]
    general_rows = [r for r in school_rows if not r.get('specialty')]
    specialty_rows = [r for r in school_rows if r.get('specialty')]
    by_specialty = defaultdict(float)
    for row in specialty_rows:
        by_specialty[row['specialty']] += hours_total(row)

    check(f'{school} G contains all 35 specialties', set(by_specialty) == set(specialty_labels))
    check(f'{school} G general hours total {expected_general}', sum(map(hours_total, general_rows)) == expected_general)
    check(f'{school} G specialty row count 232', len(specialty_rows) == 232)
    for code in specialty_labels:
        check(f'{school} G {code} specialty hours total {expected_specialty}', by_specialty.get(code) == expected_specialty)

    statuses = Counter(r.get('assignment_link_status', 'direct') for r in school_rows)
    check(f'{school} G has exactly one choice-dependent row', statuses['choice_dependent'] == 1)
    check(f'{school} G has no regulatory gaps', statuses['regulatory_gap'] == 0)
    check(f'{school} G has no thematic-dependent rows', statuses['thematic_dependent'] == 0)
    check(f'{school} G audited title aliases count 18', sum(bool(r.get('assignment_subject_alias')) for r in school_rows) == 18)
    check(f'{school} G split theory/lab rows count 20', sum('Γ΄' in (r.get('assignment_components_by_grade') or {}) for r in school_rows) == 20)

    # Resolve every timetable row in its own specialty context. Choice rows are
    # validated branch-by-branch instead of being treated as a broad direct match.
    resolved = 0
    for row in school_rows:
        specialty = row.get('specialty')
        label = specialty_labels.get(specialty, '') if specialty else ''
        candidates = [
            a for a in assignments
            if a.get('school') == school
            and (a.get('grade') in ('', 'Γ΄') or 'Γ΄' in (a.get('grades') or []))
            and (not label or label in a.get('section', ''))
        ]

        if row.get('assignment_link_status') == 'choice_dependent':
            options = row.get('assignment_choice_options') or []
            option_ok = bool(options)
            for option in options:
                targets = ([option['subject']] if option.get('subject') else []) + (option.get('components') or [])
                option_ok = option_ok and bool(targets)
                for target in targets:
                    matched = next((a for a in candidates if a.get('subject') == target), None)
                    option_ok = option_ok and matched is not None
                    if matched and option.get('codes'):
                        available = set(matched.get('A') or []) | set(matched.get('B') or []) | set(matched.get('C') or [])
                        option_ok = option_ok and set(option['codes']) <= available
            if option_ok:
                resolved += 1
            continue

        direct = any(norm(a.get('subject')) == norm(row.get('subject')) for a in candidates)
        alias = row.get('assignment_subject_alias')
        alias_ok = bool(alias) and any(norm(a.get('subject')) == norm(alias) for a in candidates)
        components = (row.get('assignment_components_by_grade') or {}).get('Γ΄', [])
        components_ok = bool(components) and all(any(a.get('subject') == c.get('subject') for a in candidates) for c in components)
        if direct or alias_ok or components_ok:
            resolved += 1

    check(f'{school} G timetable↔assignments all rows classified', resolved == len(school_rows))

# Exact general-education programmes currently applicable to Γ΄.
def general_subjects(school):
    return {
        r['subject']: (r.get('hours') or {}).get('Γ΄')
        for r in rows
        if r.get('school') == school and 'Γ΄' in (r.get('hours') or {}) and not r.get('specialty')
    }

check('day G general programme exact', general_subjects('epal') == {
    'Νέα Ελληνικά': 3,
    'Άλγεβρα': 2,
    'Γεωμετρία': 1,
    'Φυσική': 2,
    'Χημεία': 1,
    'Ξένη Γλώσσα (Αγγλικά)': 1,
    'Εισαγωγή στις Αρχές της Επιστήμης των Η/Υ': 1,
    'Φυσική Αγωγή': 1,
})
check('3-year evening G general programme exact', general_subjects('esperino_epal') == {
    'Νέα Ελληνικά': 3,
    'Άλγεβρα': 2,
    'Γεωμετρία': 1,
    'Φυσική': 2,
    'Ξένη Γλώσσα (Αγγλικά)': 1,
    'Εισαγωγή στις Αρχές της Επιστήμης των Η/Υ': 1,
})

# Critical timetable amendments / current 3-year evening programme.
def row_by_id(course_id):
    return next((r for r in rows if r.get('course_id') == course_id), None)

expected_day_plant = {
    'epal.g.plant.03': ('Δενδροκομία - Αμπελουργία', '2Θ + 3Ε'),
    'epal.g.plant.04': ('Φυτά Μεγάλης Καλλιέργειας - Κηπευτικές Καλλιέργειες', '2Θ + 3Ε'),
    'epal.g.plant.05': ('Αρδεύσεις Καλλιεργειών', '1Θ + 2Ε'),
    'epal.g.plant.06': ('Φυτοπροστασία', '2Θ + 2Ε'),
}
for course_id, (subject, display) in expected_day_plant.items():
    row = row_by_id(course_id)
    check(f'2072 day plant exact: {course_id}', row and row.get('subject') == subject and (row.get('hours_display') or {}).get('Γ΄') == display)

expected_evening_plant = {
    'esperino_epal.g.plant.03': ('Δενδροκομία - Αμπελουργία', '2Θ + 2Ε'),
    'esperino_epal.g.plant.04': ('Φυτά Μεγάλης Καλλιέργειας - Κηπευτικές Καλλιέργειες', '2Θ + 2Ε'),
    'esperino_epal.g.plant.05': ('Αρδεύσεις Καλλιεργειών', '1Θ + 2Ε'),
    'esperino_epal.g.plant.06': ('Φυτοπροστασία', '1Θ + 2Ε'),
}
for course_id, (subject, display) in expected_evening_plant.items():
    row = row_by_id(course_id)
    check(f'2636 evening plant exact: {course_id}', row and row.get('subject') == subject and (row.get('hours_display') or {}).get('Γ΄') == display)

# B΄ 4373/2018 + its official error corrections in B΄ 4815/2018.
# These ratios are easy to regress because the uncorrected Gazette text contains
# typographical 0/O glyphs where Θ should appear.
for course_id, display in {
    'esperino_epal.g.graphic.01': '3Θ',
    'esperino_epal.g.graphic.02': '2Θ',
    'esperino_epal.g.graphic.05': '1Θ + 2Ε',
    'esperino_epal.g.graphic.08': '1Θ',
    'esperino_epal.g.interior.06': '1Θ + 3Ε',
    'esperino_epal.g.interior.07': '1Θ',
    'epal.g.interior.06': '1Θ + 5Ε',
    'epal.g.interior.07': '2Θ',
}.items():
    row = row_by_id(course_id)
    check(f'4373/4815 applied-arts corrected ratio: {course_id}', row and (row.get('hours_display') or {}).get('Γ΄') == display)

# Tourism language is one actual language, not four simultaneous assignments.
for school in ('epal', 'esperino_epal'):
    row = row_by_id(f'{school}.g.tourism.08')
    options = row.get('assignment_choice_options') if row else None
    got = {o.get('label'): o.get('codes') for o in (options or [])}
    check(f'{school} G tourism language slot is 2Θ', row and (row.get('hours_display') or {}).get('Γ΄') == '2Θ')
    check(f'{school} G tourism language is choice-dependent', row and row.get('assignment_link_status') == 'choice_dependent')
    check(f'{school} G tourism branches map to exact language codes', got == {
        'Γαλλικά': ['ΠΕ05'], 'Γερμανικά': ['ΠΕ07'], 'Ισπανικά': ['ΠΕ40'], 'Ιταλικά': ['ΠΕ34']
    })

# Key assignment amendments in Γ΄ EPAL.
def find_assignment(label_fragment, subject):
    return next((
        a for a in assignments
        if a.get('school') == 'epal' and a.get('grade') == 'Γ΄'
        and label_fragment in a.get('section', '') and a.get('subject') == subject
    ), None)

row = find_assignment('Υπάλληλος Τουριστικών Επιχειρήσεων', 'Γαλλικά ή Γερμανικά ή Ισπανικά ή Ιταλικά')
check('2637 tourism four-language A assignment exact', row and row.get('A') == ['ΠΕ05', 'ΠΕ07', 'ΠΕ34', 'ΠΕ40'])
row = find_assignment('Τεχνικός Δομικών Έργων και Γεωπληροφορικής', 'Αρχιτεκτονικό Σχέδιο')
check('2637 building Architectural Design moved to C assignment for PE89.01/TE02.01', row and row.get('A') == ['ΠΕ81'] and row.get('C') == ['ΠΕ89.01', 'ΤΕ02.01'] and not row.get('B'))
for subject in ('Αρχές Επεξεργασίας Τροφίμων', 'Ασφάλεια Τροφίμων'):
    row = find_assignment('Τεχνικός Τεχνολογίας Τροφίμων και Ποτών', subject)
    check(f'3609 food assignment includes PE88.01 in A: {subject}', row and 'ΠΕ88.01' in (row.get('A') or []))

# All 35 regular EPAL specialty assignment sections must exist.
epal_g_specialty_assignments = [
    a for a in assignments
    if a.get('school') == 'epal' and a.get('grade') == 'Γ΄'
    and not a.get('section', '').startswith('Μαθήματα Γενικής Παιδείας')
]
assignment_sections = {a.get('section', '') for a in epal_g_specialty_assignments}
check('EPAL G assignments expose exactly 35 specialty sections', len(assignment_sections) == 35)
check('EPAL G assignments cover every timetable specialty label', all(any(label in section for section in assignment_sections) for label in specialty_labels.values()))

# Public source cards / internal metadata must expose the complete Γ΄ legal chain.
assignment_page = (ROOT / 'anatheseis-mathimaton.php').read_text()
timetable_page = (ROOT / 'orologio-programma-mathimaton.php').read_text()
assignment_data = (ROOT / 'includes/teaching-assignments-epal.php').read_text()
for fek in ('1664/2018', '2637/2018', '3520/2018', '2779/2019', '3609/2020', '5206/2023'):
    check(f'assignment page/source exposes {fek}', fek in assignment_page or fek in assignment_data)
for fek in ('1426/2017', '2072/2017', '2122/2018', '2636/2018', '3224/2018', '4373/2018', '4815/2018'):
    check(f'timetable page exposes G source {fek}', fek in timetable_page)
check('G evening source note does not misstate 2151 as G amendment', '2636/2018, όπως τροποποιήθηκε με το ΦΕΚ Β΄ 2151/2026' not in assignment_page and '2636/2018, όπως τροποποιήθηκε με το ΦΕΚ Β΄ 2151/2026' not in assignment_data)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'EPAL_G_AUDIT checks={len(checks)} day_rows={len([r for r in rows if r.get("school")=="epal" and "Γ΄" in (r.get("hours") or {})])} evening_rows={len([r for r in rows if r.get("school")=="esperino_epal" and "Γ΄" in (r.get("hours") or {})])} assignment_specialty_rows={len(epal_g_specialty_assignments)}')
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
