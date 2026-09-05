#!/usr/bin/env python3
"""Contract for Β΄ Π.ΕΠΑ.Λ. timetable (4578/2022, 4961/2022, 2136/2026) ↔ assignments (4983/2022 + amendments)."""
from pathlib import Path
import json
import re
import subprocess
import unicodedata

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require "includes/weekly-timetable-data.php";
require "includes/teaching-assignments-data.php";
echo json_encode(array(
  "timetable" => weeklyTimetableRows(),
  "public" => weeklyTimetablePublicRows(),
  "assignments" => teachingAssignmentsData(),
  "schools" => weeklyTimetableSchoolTypes()
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
payload = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))
rows = payload['timetable']
public_rows = payload['public']
assignments = payload['assignments']
school = payload['schools']['pepal']

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def norm(text):
    text = unicodedata.normalize('NFC', text or '').lower().strip()
    text = text.replace('–', '-').replace('—', '-').replace('−', '-').replace('&', ' και ')
    text = re.sub(r'\s*-\s*', '-', text)
    return re.sub(r'\s+', ' ', text)

def find_row(course_id):
    return next((r for r in pepal_b_rows if r.get('course_id') == course_id), None)

def find_assignment(subject, section_fragment=None):
    for row in pepal_b_assignments:
        if row.get('subject') != subject:
            continue
        if section_fragment and norm(section_fragment) not in norm(row.get('section')):
            continue
        return row
    return None

pepal_b_rows = [r for r in rows if r.get('school') == 'pepal' and 'Β΄' in (r.get('hours') or {})]
pepal_b_assignments = [a for a in assignments if a.get('school') == 'pepal' and a.get('grade') == 'Β΄']
tracks = school['tracks_by_grade']['Β΄']

check('PEPAL B has 9 sectors', len(tracks) == 9)
check('PEPAL B timetable has 79 rows', len(pepal_b_rows) == 79)
check('PEPAL B assignments have 94 rows', len(pepal_b_assignments) == 94)
check('PEPAL B program total 35', school['program']['Β΄']['total'] == 35)
check('PEPAL B program split 12+23', school['program']['Β΄']['parts'] == {'Γενική Παιδεία': 12, 'Μαθήματα Τομέα': 23})

# General education: 12 real timetable hours, with Religion/Ethics sharing one alternative slot.
general = [r for r in pepal_b_rows if not r.get('track')]
check('PEPAL B general has 10 visible rows incl. Ethics alternative', len(general) == 10)
slot_hours = {}
for row in general:
    key = row.get('slot_id') or row.get('course_id')
    slot_hours[key] = max(slot_hours.get(key, 0), int(row['hours']['Β΄']))
check('PEPAL B general unique-slot total is 12', sum(slot_hours.values()) == 12)
religion = find_row('pepal.b.general.thriskeftika')
ethics = find_row('pepal.b.general.ithiki')
check('PEPAL B Religion/Ethics share one slot', religion and ethics and religion.get('slot_id') == ethics.get('slot_id') == 'pepal.b.religion_ethics')
check('PEPAL B Religion/Ethics are alternatives', religion and ethics and religion.get('mode') == ethics.get('mode') == 'alternative')

# Every sector remains exactly 23h and every ordinary row resolves inside its own sector.
resolved = 0
choice_rows = []
for track, label in tracks.items():
    sector_rows = [r for r in pepal_b_rows if r.get('track') == track]
    check(f'PEPAL B {track} has rows', bool(sector_rows))
    check(f'PEPAL B {track} totals 23h', sum(int(r['hours']['Β΄']) for r in sector_rows) == 23)
    candidates = [a for a in pepal_b_assignments if norm(label) in norm(a.get('section'))]
    check(f'PEPAL B {track} assignment section exists', bool(candidates))

    for row in sector_rows:
        status = row.get('assignment_link_status')
        if status == 'choice_dependent':
            choice_rows.append(row)
            resolved += 1
            continue
        direct = any(norm(a.get('subject')) == norm(row.get('subject')) for a in candidates)
        alias = row.get('assignment_subject_alias')
        alias_ok = bool(alias) and any(norm(a.get('subject')) == norm(alias) for a in candidates)
        check(f'PEPAL B sector-context resolve: {row.get("course_id")}', direct or alias_ok)
        if direct or alias_ok:
            resolved += 1

# General rows resolve in General Education context.
for row in general:
    candidates = [a for a in pepal_b_assignments if norm('Μαθήματα Γενικής Παιδείας') in norm(a.get('section'))]
    direct = any(norm(a.get('subject')) == norm(row.get('subject')) for a in candidates)
    alias = row.get('assignment_subject_alias')
    alias_ok = bool(alias) and any(norm(a.get('subject')) == norm(alias) for a in candidates)
    check(f'PEPAL B general-context resolve: {row.get("course_id")}', direct or alias_ok)
    if direct or alias_ok:
        resolved += 1

check('PEPAL B all 79 timetable rows structurally resolve', resolved == 79)
check('PEPAL B exactly 3 choice-dependent rows', len(choice_rows) == 3)
public_b = [r for r in public_rows if r.get('school') == 'pepal' and 'Β΄' in (r.get('hours') or {})]
check('PEPAL B public payload keeps all 79 rows', len(public_b) == 79)
check('PEPAL B public payload strips internal assignment metadata', all(not any(k.startswith('assignment_') for k in r) for r in public_b))

# 4961/2022 correction: accounting title is the corrected one and keeps 3Θ+3Ε (ΠΑ).
accounting = find_row('pepal.b.admin.logistiki')
check('4961 corrected accounting title', accounting and accounting.get('subject') == 'Εισαγωγή στη Λογιστική')
check('4961 corrected accounting hours', accounting and accounting.get('hours', {}).get('Β΄') == 6 and accounting.get('hours_display', {}).get('Β΄') == '3Θ + 3Ε (ΠΑ)')
check('old Financial Accounting title absent from timetable', not any(r.get('subject') == 'Εισαγωγή στη Χρηματοοικονομική Λογιστική' for r in pepal_b_rows))
check('old Financial Accounting title absent from assignments', not any(a.get('subject') == 'Εισαγωγή στη Χρηματοοικονομική Λογιστική' for a in pepal_b_assignments))

# Practical-training annotations from the sector timetable must not be lost.
pa_rows = [r for r in pepal_b_rows if '(ΠΑ)' in (r.get('hours_display', {}).get('Β΄') or '')]
check('PEPAL B preserves 24 PA-marked rows', len(pa_rows) == 24)
check('agriculture PA example exact', find_row('pepal.b.agriculture.fysikoi_poroi').get('hours_display', {}).get('Β΄') == '2Θ + 2Ε (ΠΑ)')
check('arts PA example exact', find_row('pepal.b.arts.eisagogi_eidikotites').get('hours_display', {}).get('Β΄') == '3Ε (ΠΑ)')
check('IT PA example exact', find_row('pepal.b.it.istotopoi').get('hours_display', {}).get('Β΄') == '4Ε (ΠΑ)')

# Context-only aliases for two electrical titles.
electrical_a = find_row('pepal.b.electrical.ilektrotexnia')
electrical_b = find_row('pepal.b.electrical.ypologistika_diktya')
check('PEPAL B electrical alias 1 exact', electrical_a and electrical_a.get('assignment_subject_alias') == 'Ηλεκτροτεχνία (Κυκλώματα Συνεχούς και Εναλλασσόμενου ρεύματος)')
check('PEPAL B electrical alias 2 exact', electrical_b and electrical_b.get('assignment_subject_alias') == 'Εισαγωγή στα Υπολογιστικά Συστήματα και στα Δίκτυα Επικοινωνιών')

# 418/2023 naval amendments.
nav = 'Τομέας Ναυτιλιακών Επαγγελμάτων'
row = find_assignment('Ναυσιπλοΐα Ι - Ναυτική Μετεωρολογία', nav)
check('418 navigation A/B', row and row.get('A') == ['ΠΕ90'] and row.get('B') == ['ΠΕ03', 'ΠΕ04.01'])
row = find_assignment('Αξιοπλοΐα', nav)
check('418 seaworthiness adds PE82 to A', row and row.get('A') == ['ΠΕ90', 'ΠΕ82'])
row = find_assignment('Ναυπηγικό - Μηχανολογικό - Ηλεκτρολογικό Σχέδιο', nav)
check('418 ship-design A includes PE82/PE90', row and row.get('A') == ['ΠΕ82', 'ΠΕ90'] and row.get('B') == ['ΤΕ02.02'])
row = find_assignment('Περιβάλλον Ναυτιλιακής Εργασίας', nav)
check('418 maritime environment A includes PE78/PE90', row and row.get('A') == ['ΠΕ78', 'ΠΕ90'] and row.get('B') == ['ΠΕ82'])
row = find_assignment('Αντοχή Υλικών - Εφαρμογές', nav)
check('418 strength-of-materials A PE82/PE81/PE85', row and row.get('A') == ['ΠΕ82', 'ΠΕ81', 'ΠΕ85'])

# Naval choice is one of two and is machine-readable.
naval_choice = find_row('pepal.b.naval.eidiko')
check('naval choice is choice-dependent', naval_choice and naval_choice.get('assignment_link_status') == 'choice_dependent')
check('naval choice has exactly 2 options', naval_choice and len(naval_choice.get('assignment_choice_options') or []) == 2)
check('naval choice group id', naval_choice and naval_choice.get('assignment_choice_group_id') == 'pepal.b.naval.special_course')
check('naval choice requires one distinct option', naval_choice and naval_choice.get('assignment_choice_group_required') == 1 and naval_choice.get('assignment_choice_group_distinct') is True)

# Health: two distinct special subjects out of nine, each occupying a 5h slot.
health_a = find_row('pepal.b.health.eidiko_a')
health_b = find_row('pepal.b.health.eidiko_b')
check('health special A/B are 5h each', health_a and health_b and health_a.get('hours', {}).get('Β΄') == 5 and health_b.get('hours', {}).get('Β΄') == 5)
check('health special A/B choice-dependent', health_a and health_b and health_a.get('assignment_link_status') == health_b.get('assignment_link_status') == 'choice_dependent')
check('health special A/B each expose 9 legal options', health_a and health_b and len(health_a.get('assignment_choice_options') or []) == 9 and len(health_b.get('assignment_choice_options') or []) == 9)
check('health special A/B share one choice group', health_a and health_b and health_a.get('assignment_choice_group_id') == health_b.get('assignment_choice_group_id') == 'pepal.b.health.special_courses')
check('health choice group requires two distinct options', health_a and health_b and health_a.get('assignment_choice_group_required') == health_b.get('assignment_choice_group_required') == 2 and health_a.get('assignment_choice_group_distinct') is True and health_b.get('assignment_choice_group_distinct') is True)
health_labels = {o.get('label') for o in health_a.get('assignment_choice_options', [])} if health_a else set()
check('health 9-option labels exact', health_labels == {
    'Μικροβιολογία I', 'Βασικές Κλινικές Δεξιότητες', 'Δημιουργία και Έκφραση στην Προσχολική Ηλικία Ι',
    'Σύγχρονη Αισθητική I', 'Εισαγωγή στη Φυσικοθεραπεία', 'Βασικές Εφαρμογές Κομμωτικής I',
    'Οδοντοτεχνία I', 'Φαρμακευτική Τεχνολογία Ι', 'Ακτινολογία Ι'
})
for row in (health_a, health_b):
    for option in row.get('assignment_choice_options', []):
        targets = list(option.get('components') or [])
        if option.get('subject'):
            targets.append(option['subject'])
        check(f'health choice has target: {row.get("course_id")} / {option.get("label")}', bool(targets))
        for target in targets:
            check(
                f'health choice target resolves in health sector: {target}',
                find_assignment(target, 'Τομέας Υγείας - Πρόνοιας - Ευεξίας') is not None,
            )

# 5206/2023 Chemistry amendment and 2624/2026 Ethics.
chem = find_assignment('Χημεία', 'Μαθήματα Γενικής Παιδείας')
check('5206 chemistry A exact', chem and chem.get('A') == ['ΠΕ04.02', 'ΠΕ85'])
check('5206 chemistry B exact', chem and chem.get('B') == ['ΠΕ04.01', 'ΠΕ04.03', 'ΠΕ04.04', 'ΠΕ04.05', 'ΠΕ87.01', 'ΠΕ88.01'])
check('5206 PE85 chemistry-degree note', chem and chem.get('A_notes', {}).get('ΠΕ85') == 'με πτυχίο Χημικών Μηχανικών')
eth = find_assignment('Ηθική', 'Μαθήματα Γενικής Παιδείας')
check('2624 Ethics A/B exact', eth and eth.get('A') == ['ΠΕ01'] and eth.get('B') == ['ΠΕ02', 'ΠΕ33', 'ΠΕ78'])
check('2624 Ethics effective 2026-2027 note', eth and '2026-2027' in (eth.get('note') or ''))

# Public source cards: current official Ministry/Diavgeia links for key amendments.
assign_page = (ROOT / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')
timetable_page = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
check('assignment page cites 4983/2022', 'ΦΕΚ Β΄ 4983/2022 — Β΄ Π.ΕΠΑ.Λ.' in assign_page)
check('assignment page 4983 uses Diavgeia', 'diavgeia.gov.gr/doc/%CE%A8%CE%A0%CE%A4246%CE%9C%CE%A4%CE%9B%CE%97-%CE%914%CE%97' in assign_page)
check('assignment page 418 uses official Ministry PDF', 'www.minedu.gov.gr/publications/docs2020/2023_01_25_' in assign_page and 'ΦΕΚ Β΄ 418/2023 — Β΄ Π.ΕΠΑ.Λ.' in assign_page)
check('assignment page 5206 uses official Ministry PDF', 'www.minedu.gov.gr/publications/docs2020/2023_08_23_' in assign_page and 'ΦΕΚ Β΄ 5206/2023 — Β΄ Π.ΕΠΑ.Λ.' in assign_page)
check('assignment page 2624 uses Diavgeia', 'diavgeia.gov.gr/doc/9%CE%9C%CE%A4%CE%A146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A6%CE%97%CE%95' in assign_page)
check('timetable page cites 4578/2022', 'ΦΕΚ Β΄ 4578/2022 — Β΄ Π.ΕΠΑ.Λ.' in timetable_page)
check('timetable page cites 4961/2022', 'ΦΕΚ Β΄ 4961/2022 — Διόρθωση Β΄ Π.ΕΠΑ.Λ.' in timetable_page)
check('timetable page cites 2136/2026', 'ΦΕΚ Β΄ 2136/2026 — Α΄/Β΄ Π.ΕΠΑ.Λ.' in timetable_page)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'PEPAL_B_AUDIT timetable={len(pepal_b_rows)} assignments={len(pepal_b_assignments)} sectors={len(tracks)} resolved={resolved} choice={len(choice_rows)} pa={len(pa_rows)}')
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
