#!/usr/bin/env python3
"""Contract for Γ΄ Π.ΕΠΑ.Λ. assignments (ΦΕΚ 5510/2023) ↔ timetable (ΦΕΚ 5251/2023)."""
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
  "assignments" => teachingAssignmentsData(),
  "specialties" => weeklyTimetableVocationalSpecialties()
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
    return re.sub(r'\s+', ' ', text)

specialty_labels = {}
for _track, items in payload['specialties'].items():
    specialty_labels.update(items)

pepal_g_rows = [r for r in rows if r.get('school') == 'pepal' and 'Γ΄' in (r.get('hours') or {})]
pepal_g_assignments = [a for a in assignments if a.get('school') == 'pepal' and a.get('grade') == 'Γ΄']
row_specialties = {r.get('specialty') for r in pepal_g_rows if r.get('specialty')}
assignment_sections = {a.get('section', '') for a in pepal_g_assignments}

check('PEPAL G timetable has 35 specialties', len(row_specialties) == 35)
check('PEPAL G assignment data covers all 35 specialty labels', all(
    any(label in section for section in assignment_sections)
    for code, label in specialty_labels.items() if code in row_specialties
))
check('PEPAL G timetable has 248 course rows', len(pepal_g_rows) == 248)

# Context-aware linkage: a timetable row must resolve only inside its own specialty,
# either directly, through an audited title alias, or through split Θ/Ε components.
resolved = 0
unresolved = []
for row in pepal_g_rows:
    specialty = row.get('specialty')
    label = specialty_labels.get(specialty, '') if specialty else ''
    candidates = [a for a in pepal_g_assignments if not label or label in a.get('section', '')]

    direct = any(norm(a.get('subject')) == norm(row.get('subject')) for a in candidates)
    alias = row.get('assignment_subject_alias')
    alias_ok = bool(alias) and any(norm(a.get('subject')) == norm(alias) for a in candidates)
    components = (row.get('assignment_components_by_grade') or {}).get('Γ΄', [])
    components_ok = bool(components) and all(
        any(a.get('subject') == component.get('subject') for a in candidates)
        for component in components
    )
    if direct or alias_ok or components_ok:
        resolved += 1
    else:
        unresolved.append((specialty, row.get('subject')))

check('PEPAL G timetable↔assignments resolves 248/248 in specialty context', resolved == 248 and not unresolved)

# Critical corrections found during the zero-base audit of ΦΕΚ 5510/2023.
def find_assignment(label, subject):
    return next((a for a in pepal_g_assignments if label in a.get('section', '') and a.get('subject') == subject), None)

admin = 'Υπάλληλος Διοίκησης και Οικονομικών Υπηρεσιών'
commerce = 'Υπάλληλος Εμπορίας και Διαφήμισης'
warehouse = 'Υπάλληλος Αποθήκης και Συστημάτων Εφοδιασμού'
tourism = 'Υπάλληλος Τουριστικών Επιχειρήσεων'
building = 'Τεχνικός Δομικών Έργων και Γεωπληροφορικής'
graphics = 'Γραφικών Τεχνών'
interior = specialty_labels['interior']
conservation = 'Συντήρησης Έργων Τέχνης - Αποκατάστασης'
silver = 'Αργυροχρυσοχοΐας'
garment = specialty_labels['garment']
furniture = specialty_labels['furniture']
childcare = 'Βοηθός Βρεφονηπιοκόμων'
captain = 'Πλοίαρχος Εμπορικού Ναυτικού'
engineer = 'Μηχανικός Εμπορικού Ναυτικού'

row = find_assignment(admin, 'Εισαγωγή στην Οργανωσιακή Συμπεριφορά και στην Διοίκηση Ανθρώπινων Πόρων')
check('5510 administration organizational behaviour A/B', row and row.get('A') == ['ΠΕ80'] and row.get('B') == ['ΠΕ78'])
check('5510 administration MIS exists', bool(find_assignment(admin, 'Πληροφοριακά Συστήματα Διοίκησης')))
check('5510 commerce Digital Marketing exists', bool(find_assignment(commerce, 'Ψηφιακό Μάρκετινγκ')))
check('5510 commerce Strategic Marketing exists', bool(find_assignment(commerce, 'Στρατηγικό Μάρκετινγκ')))
check('5510 commerce does not inherit Modern Office Environment', not find_assignment(commerce, 'Σύγχρονο Περιβάλλον Γραφείου'))
row = find_assignment(warehouse, 'Εφαρμογές Εφοδιαστικής (Logistics)')
check('5510 warehouse Logistics A/C', row and row.get('A') == ['ΠΕ80'] and row.get('C') == ['ΠΕ82'])
check('5510 warehouse Cost Accounting exists', bool(find_assignment(warehouse, 'Λογιστική Κόστους')))
check('5510 warehouse Project Management exists', bool(find_assignment(warehouse, 'Διοίκηση Έργων (Project Management)')))
check('5510 tourism marketing/customer service exists', bool(find_assignment(tourism, 'Τουριστικό Μάρκετινγκ - Εξυπηρέτηση Πελατών')))
row = find_assignment(tourism, 'Γαλλικά ή Γερμανικά ή Ισπανικά ή Ιταλικά')
check('5510 tourism four-language assignment', row and row.get('A') == ['ΠΕ05', 'ΠΕ07', 'ΠΕ34', 'ΠΕ40'])
row = find_assignment(building, 'Ηλεκτρονική Σχεδίαση Τεχνικών Έργων')
check('5510 building electronic design A/B', row and row.get('A') == ['ΠΕ81'] and row.get('B') == ['ΤΕ02.01'])
check('5510 building sustainable development exists', bool(find_assignment(building, 'Δόμηση και Βιώσιμη Ανάπτυξη - Κλιματική Αλλαγή')))
check('5510 graphics production/prepress exists', bool(find_assignment(graphics, 'Οργάνωση Παραγωγής Γραφικών Τεχνών - Προεκτύπωση')))
check('5510 graphics visual communication exists', bool(find_assignment(graphics, 'Σχεδιασμός Οπτικής Επικοινωνίας στην Ειδικότητα')))
check('5510 interior digital space design exists', bool(find_assignment(interior, 'Ψηφιακός Σχεδιασμός Χώρου')))
check('5510 interior bioclimatic design exists', bool(find_assignment(interior, 'Αειφορικός - Βιοκλιματικός Σχεδιασμός Χώρου')))
check('5510 conservation heritage documentation exists', bool(find_assignment(conservation, 'Τεκμηρίωση Έργων Πολιτιστικής Κληρονομιάς')))
row = find_assignment(silver, 'Σύγχρονο Εικαστικό Κόσμημα')
check('5510 silver contemporary jewel A/B', row and row.get('A') == ['ΤΕ01.25'] and row.get('B') == ['ΠΕ89.01'])
check('5510 garment pattern technology title exists', bool(find_assignment(garment, 'Σχεδιασμός και Τεχνολογία Προτύπων Ενδύματος')))
check('5510 garment fibers title exists', bool(find_assignment(garment, 'Υφασματολογία - Τεχνολογία Ινών')))
check('5510 furniture CAD/CAM exists', bool(find_assignment(furniture, 'Ψηφιακός Σχεδιασμός CAD/CAM')))
row = find_assignment(childcare, 'Δημιουργία και Έκφραση στην Προσχολική Ηλικία ΙΙ')
check('5510 childcare new title A/B', row and row.get('A') == ['ΠΕ87.09', 'ΤΕ01.30'] and row.get('B') == ['ΠΕ87.06'])
row = find_assignment(captain, 'Ν.Η.Ο. - Επικοινωνίες')
check('5510 captain NHO title excludes timetable PA suffix', row and row.get('A') == ['ΠΕ83', 'ΠΕ84', 'ΠΕ90'])
row = find_assignment(engineer, 'Μηχανολογικές Κατασκευές Πλοίου - Σχέδιο με Η/Υ')
check('5510 engineer constructions A/B', row and row.get('A') == ['ΠΕ82'] and row.get('B') == ['ΤΕ02.02'])

# Public source cards must expose both autonomous legal sources, with reciprocal cross-check.
assignment_page = (ROOT / 'anatheseis-mathimaton.php').read_text()
timetable_page = (ROOT / 'orologio-programma-mathimaton.php').read_text()
check('assignment page cites ΦΕΚ 5510/2023', 'ΦΕΚ Β΄ 5510/2023 — Γ΄ Π.ΕΠΑ.Λ.' in assignment_page)
check('assignment page cites ΦΕΚ 5251/2023 cross-check', 'ΦΕΚ Β΄ 5251/2023 — Γ΄ Π.ΕΠΑ.Λ. / Ωρολόγιο (διασταύρωση τίτλων)' in assignment_page)
check('timetable page cites ΦΕΚ 5251/2023', 'ΦΕΚ Β΄ 5251/2023 — Γ΄ Π.ΕΠΑ.Λ.' in timetable_page)
check('timetable page cites ΦΕΚ 5510/2023 cross-check', 'ΦΕΚ Β΄ 5510/2023 — Γ΄ Π.ΕΠΑ.Λ. / Αναθέσεις (διασταύρωση τίτλων)' in timetable_page)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'PEPAL_G_AUDIT rows={len(pepal_g_rows)} resolved={resolved} specialties={len(row_specialties)}')
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
if unresolved:
    for specialty, subject in unresolved:
        print(f'UNRESOLVED: {specialty} / {subject}')
raise SystemExit(1 if failed else 0)
