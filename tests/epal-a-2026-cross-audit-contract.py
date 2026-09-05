#!/usr/bin/env python3
"""Focused contract for A' EPAL timetable ↔ assignment cross-audit (2026-2027)."""
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
rows = payload['timetable']
assignments = payload['assignments']
checks = []

def check(name, condition):
    checks.append((name, bool(condition)))

def by_id(course_id):
    return next((r for r in rows if r.get('course_id') == course_id), None)

def a_assignment(subject, school='epal'):
    return next((a for a in assignments if a.get('school') == school and a.get('grade') == 'Α΄' and a.get('subject') == subject), None)

# FEK B 2151/2026 + B 2187/2018: A' daytime EPAL is 22 + 7 + 6 = 35 hours.
info = by_id('epal.a.general.pliroforiki')
research = by_id('epal.a.orientation.erevnitiki')
check('A EPAL program metadata total 35', "'Α΄' => array('total' => 35" in (ROOT / 'includes/weekly-timetable-data.php').read_text(encoding='utf-8'))

general_expected = {
    'Νέα Ελληνικά': 4,
    'Άλγεβρα': 3,
    'Γεωμετρία': 1,
    'Φυσική': 2,
    'Χημεία': 1,
    'Βιολογία': 1,
    'Πολιτική Παιδεία': 2,
    'Ιστορία': 1,
    'Θρησκευτικά': 1,
    'Ηθική': 1,
    'Ξένη Γλώσσα (Αγγλικά)': 2,
    'Φυσική Αγωγή': 2,
    'Πληροφορική': 2,
}
for subject, hours in general_expected.items():
    row = next((r for r in rows if r.get('school') == 'epal' and r.get('group') == 'Μαθήματα Γενικής Παιδείας' and r.get('subject') == subject and (r.get('hours') or {}).get('Α΄') == hours), None)
    check(f'A EPAL general exact: {subject}', row is not None)

religion = by_id('epal.a.general.thriskeftika')
ethics = by_id('epal.a.general.ithiki')
check('A EPAL religion/ethics share alternative slot', religion and ethics and religion.get('slot_id') == ethics.get('slot_id') == 'epal.a.religion_ethics')
check('A EPAL religion alternative', religion and religion.get('mode') == 'alternative')
check('A EPAL ethics alternative', ethics and ethics.get('mode') == 'alternative')
check('A EPAL ethics exemption note', ethics and 'απαλλάσσονται' in (ethics.get('condition') or ethics.get('note') or ''))

orientation_expected = {
    'Ερευνητική Εργασία στην Τεχνολογία': 2,
    'Σχολικός Επαγγελματικός Προσανατολισμός - Ασφάλεια και Υγεία στο χώρο εργασίας': 2,
    'Ζώνη Δημιουργικών Δραστηριοτήτων': 3,
}
for subject, hours in orientation_expected.items():
    row = next((r for r in rows if r.get('school') == 'epal' and r.get('group') == 'Μαθήματα Προσανατολισμού' and r.get('subject') == subject and (r.get('hours') or {}).get('Α΄') == hours), None)
    check(f'A EPAL orientation exact: {subject}', row is not None)

# Critical split rule from FEK B 2187/2018: >16 pupils => two groups alternating ICT/research.
for row, label in ((info, 'ICT'), (research, 'research')):
    note = (row or {}).get('note') or ''
    check(f'A EPAL {label} split threshold 16 documented', 'μεγαλύτερος από 16' in note)
    check(f'A EPAL {label} split into two groups documented', 'δύο ομάδες' in note)
    check(f'A EPAL {label} alternating pair documented', 'εναλλάξ' in note and 'Πληροφορική' in note and 'Ερευνητική Εργασία' in note)

# FEK B 2187/2018: exactly 3 of 8 choice courses, 2h each = 6h. Two are drawing (2Σ).
choice_rows = [r for r in rows if r.get('school') == 'epal' and r.get('group') == 'Μαθήματα Επιλογής' and 'Α΄' in (r.get('hours') or {})]
check('A EPAL exactly 8 offered choice courses', len(choice_rows) == 8)
check('A EPAL all choice rows use one choice set', {r.get('choice_set_id') for r in choice_rows} == {'epal.a.choices'})
check('A EPAL choose exactly 3', {r.get('choice_count') for r in choice_rows} == {3})
check('A EPAL every choice course numeric hours 2', all((r.get('hours') or {}).get('Α΄') == 2 for r in choice_rows))

drawing = by_id('epal.a.choice.grammiko_architektoniko')
composition = by_id('epal.a.choice.synthesi')
check('A EPAL architectural drawing display 2Σ', drawing and (drawing.get('hours_display') or {}).get('Α΄') == '2Σ')
check('A EPAL composition display 2Σ', composition and (composition.get('hours_display') or {}).get('Α΄') == '2Σ')

# Assignment bridge: key 2025/2026 amendments and special all-PE rows.
history = a_assignment('Ιστορία')
ethics_a = a_assignment('Ηθική')
zone = a_assignment('Ζώνη Δημιουργικών Δραστηριοτήτων')
sep = a_assignment('Σχολικός Επαγγελματικός Προσανατολισμός – Ασφάλεια και Υγεία στο χώρο εργασίας')
check('A EPAL History assignment PE02+PE33', history and history.get('A') == ['ΠΕ02', 'ΠΕ33'])
check('A EPAL History B assignment language/social branches', history and set(history.get('B') or []) == {'ΠΕ05','ΠΕ06','ΠΕ07','ΠΕ78','ΠΕ34','ΠΕ40'})
check('A EPAL Ethics A PE01', ethics_a and ethics_a.get('A') == ['ΠΕ01'])
check('A EPAL Ethics B PE02 PE33 PE78', ethics_a and ethics_a.get('B') == ['ΠΕ02', 'ΠΕ33', 'ΠΕ78'])
check('A EPAL creative zone all PE', zone and zone.get('A_all_pe') is True)
check('A EPAL SEP all PE', sep and sep.get('A_all_pe') is True)
check('A EPAL SEP priority note retained', sep and 'σειρά προτεραιότητας' in (sep.get('A_all_pe_note') or ''))

# Same 2Σ + >16 semantics for the current 3-year evening A' EPAL (FEK B 2636/2018, amended by 2151/2026).
e_info = by_id('eepal.a.general.pliroforiki')
e_research = by_id('eepal.a.orientation.erevnitiki')
e_drawing = by_id('eepal.a.choice.grammiko_architektoniko')
e_composition = by_id('eepal.a.choice.synthesi')
check('evening A EPAL ICT split rule retained', e_info and 'μεγαλύτερος από 16' in (e_info.get('note') or ''))
check('evening A EPAL research split rule retained', e_research and 'μεγαλύτερος από 16' in (e_research.get('note') or ''))
check('evening A EPAL architectural drawing display 2Σ', e_drawing and (e_drawing.get('hours_display') or {}).get('Α΄') == '2Σ')
check('evening A EPAL composition display 2Σ', e_composition and (e_composition.get('hours_display') or {}).get('Α΄') == '2Σ')

# Source cards must expose the legal chain used in this audit.
timetable_page = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
assign_page = (ROOT / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')
for fek in ('2151/2026', '2187/2018', '2636/2018'):
    check(f'timetable source includes FEK B {fek}', fek in timetable_page)
for fek in ('1664/2018', '1975/2025', '2625/2026'):
    check(f'assignment source includes FEK B {fek}', fek in assign_page)
check('assignment source has direct 1975 link', '2025_04_10_%CE%95%CE%9E%CE%95_40504' in assign_page)
check('assignment source has official Diavgeia 2625 decision', 'ΨΩ0Α46ΝΚΠΔ-Α4Υ' in assign_page)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
