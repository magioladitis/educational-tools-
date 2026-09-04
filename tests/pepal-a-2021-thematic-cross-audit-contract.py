#!/usr/bin/env python3
"""Contract for the six integrated A' P.P.EPAL orientation blocks."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require "includes/weekly-timetable-data.php";
require "includes/teaching-assignments-data.php";
echo json_encode(array(
  "internal" => weeklyTimetableRows(),
  "public" => weeklyTimetablePublicRows(),
  "assignments" => teachingAssignmentsData()
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
payload = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))
rows = payload['internal']
public_rows = payload['public']
assignments = payload['assignments']

checks = []
def check(name, cond):
    checks.append((name, bool(cond)))

expected = {
    'Οικονομία, Διοίκηση': (2, '2Ε', 3),
    'Κατασκευές, Παραγωγή και Βιομηχανία': (3, '3Ε', 15),
    'Τέχνες και Πολιτισμός': (2, '2Ε', 9),
    'Υγεία και Ευεξία': (2, '2Ε', 6),
    'Γεωργία, Τρόφιμα και Περιβάλλον': (2, '2Ε', 8),
    'Ενέργεια, Μεταφορές και Επικοινωνίες': (2, '2Ε', 10),
}

group = 'Μαθήματα Επαγγελματικής Κατεύθυνσης Προσανατολιστικού Χαρακτήρα'
blocks = [r for r in rows if r.get('school') == 'pepal' and r.get('group') == group and 'Α΄' in (r.get('hours') or {})]
check('exactly six A PEPAL orientation blocks', len(blocks) == 6)
check('A PEPAL orientation titles exact', {r.get('subject') for r in blocks} == set(expected))
check('A PEPAL orientation total 13h', sum(r['hours']['Α΄'] for r in blocks) == 13)

for row in blocks:
    subject = row['subject']
    hours, display, count = expected[subject]
    section = 'Επαγγελματική Κατεύθυνση · ' + subject
    check(f'{subject}: official block hours', row.get('hours', {}).get('Α΄') == hours)
    check(f'{subject}: official block display', row.get('hours_display', {}).get('Α΄') == display)
    check(f'{subject}: thematic-dependent status', row.get('assignment_link_status') == 'thematic_dependent')
    check(f'{subject}: exact assignment section', row.get('assignment_section') == section)
    check(f'{subject}: no fake alias', not row.get('assignment_subject_alias'))
    check(f'{subject}: no invented hourly components', not row.get('assignment_components_by_grade'))
    linked = [a for a in assignments if a.get('school') == 'pepal' and a.get('grade') == 'Α΄' and a.get('section') == section]
    check(f'{subject}: thematic row count', len(linked) == count)
    check(f'{subject}: every thematic row has codes', all(a.get('special_codes') for a in linked))

# Internal audit metadata must never leak into the public browser payload.
public_blocks = [r for r in public_rows if r.get('school') == 'pepal' and r.get('group') == group and 'Α΄' in (r.get('hours') or {})]
check('six public PEPAL A blocks remain visible', len(public_blocks) == 6)
check('public PEPAL A payload has no assignment metadata', all(not any(k.startswith('assignment_') for k in r) for r in public_blocks))

weekly_page = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
assign_page = (ROOT / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')
crosswalk = (ROOT / 'includes' / 'teaching-timetable-crosswalk.php').read_text(encoding='utf-8')
check('weekly sources include FEK 3470/2021', 'ΦΕΚ Β΄ 3470/2021 — Α΄ Π.ΕΠΑ.Λ.' in weekly_page)
check('weekly sources include FEK 4367/2021', 'ΦΕΚ Β΄ 4367/2021 — Α΄ Π.ΕΠΑ.Λ. / Αναθέσεις ανά θεματική ενότητα' in weekly_page)
check('weekly sources include FEK 7403/2023', 'ΦΕΚ Β΄ 7403/2023 — Α΄ Π.ΕΠΑ.Λ. / κανόνας ανάθεσης & συνδιδασκαλία' in weekly_page)
check('assignments source text explains thematic rule', 'οι αναθέσεις γίνονται με βάση τις επιμέρους ενότητες' in assign_page)
check('crosswalk documents no invented per-topic hours', 'δεν επινοούμε per-topic ώρες' in crosswalk)
check('crosswalk documents Teachers Association rule', 'Σύλλογο' in crosswalk and 'Διδασκόντων' in crosswalk)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
