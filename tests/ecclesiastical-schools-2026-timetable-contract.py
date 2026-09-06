#!/usr/bin/env python3
"""Contract for Πρότυπα Εκκλησιαστικά Σχολεία timetable integration."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'weekly-timetable-data.php').read_text(encoding='utf-8')
EDATA = (ROOT / 'includes' / 'weekly-timetable-ecclesiastical-data.php').read_text(encoding='utf-8')
PAGE = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')

php = r'''
require "includes/weekly-timetable-data.php";
echo json_encode(array(
  "schools" => weeklyTimetableSchoolTypes(),
  "rows" => weeklyTimetableRows(),
  "public_rows" => weeklyTimetablePublicRows()
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
payload = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))
schools = payload['schools']
rows = payload['rows']
public_rows = payload['public_rows']

checks = []
def check(name, cond):
    checks.append((name, bool(cond)))

def group_total(school, grade, group):
    ordinary = 0.0
    slots = {}
    for row in rows:
        if row.get('school') != school or row.get('group') != group:
            continue
        hours = row.get('hours', {}).get(grade)
        if hours is None:
            continue
        slot = row.get('slot_id')
        if slot:
            slots[slot] = max(slots.get(slot, 0.0), float(hours))
        else:
            ordinary += float(hours)
    return ordinary + sum(slots.values())

GYM = 'protypo_ekklisiastiko_gymnasio'
LYK = 'protypo_ekklisiastiko_lykeio'
PES = {GYM, LYK}

check('ecclesiastical data file required', "weekly-timetable-ecclesiastical-data.php" in DATA)
check('source base FEK present', '4438/25-09-2021' in DATA and '4438/25-09-2021' in EDATA)
check('source correction 4569 present', '4569/02-10-2021' in DATA and '4569/02-10-2021' in EDATA)
check('source correction 4728 present', '4728/12-10-2021' in DATA and '4728/12-10-2021' in EDATA)
check('source amendment 2781 present', '2781/03-06-2022' in DATA and '2781/03-06-2022' in EDATA)
check('source amendment 4881 present', '4881/15-09-2025' in DATA and '4881/15-09-2025' in EDATA)

check('PES Gym school type exists', GYM in schools)
check('PES Lyceum school type exists', LYK in schools)
check('PES Gym label exact', schools[GYM]['label'] == 'Πρότυπο Εκκλησιαστικό Γυμνάσιο')
check('PES Lyceum label exact', schools[LYK]['label'] == 'Πρότυπο Εκκλησιαστικό Λύκειο')
check('PES Gym grades A-B-G', schools[GYM]['grades'] == ['Α΄','Β΄','Γ΄'])
check('PES Lyceum grades A-B-G', schools[LYK]['grades'] == ['Α΄','Β΄','Γ΄'])

for grade in ('Α΄','Β΄','Γ΄'):
    info = schools[GYM]['program'][grade]
    check(f'PES Gym {grade} total 36', info['total'] == 36)
    check(f'PES Gym {grade} parts 30+6', info['parts'] == {'Κοινό πρόγραμμα':30, 'Θρησκευτική Εξειδίκευση':6})
    check(f'PES Gym {grade} common row sum 30', group_total(GYM, grade, 'Κοινό πρόγραμμα') == 30)
    check(f'PES Gym {grade} specialization row sum 6', group_total(GYM, grade, 'Μαθήματα Θρησκευτικής Εξειδίκευσης') == 6)

expected_lyceum = {
    'Α΄': ({'Γενική Παιδεία':29, 'Θρησκευτική Εξειδίκευση':6}, 29, 6),
    'Β΄': ({'Γενική Παιδεία':24, 'Θρησκευτική Εξειδίκευση':6, 'Ομάδα Προσανατολισμού':5}, 24, 6),
    'Γ΄': ({'Γενική Παιδεία':11, 'Θρησκευτική Εξειδίκευση':6, 'Ομάδα Προσανατολισμού':18}, 11, 6),
}
for grade, (parts, general, specialization) in expected_lyceum.items():
    info = schools[LYK]['program'][grade]
    check(f'PES Lyceum {grade} total 35', info['total'] == 35)
    check(f'PES Lyceum {grade} program parts exact', info['parts'] == parts)
    check(f'PES Lyceum {grade} general row sum', group_total(LYK, grade, 'Μαθήματα Γενικής Παιδείας') == general)
    check(f'PES Lyceum {grade} specialization row sum', group_total(LYK, grade, 'Μαθήματα Θρησκευτικής Εξειδίκευσης') == specialization)

check('PES Lyceum B humanities orientation 5', group_total(LYK, 'Β΄', 'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών') == 5)
check('PES Lyceum B science orientation 5', group_total(LYK, 'Β΄', 'Ομάδα Προσανατολισμού Θετικών Σπουδών') == 5)
check('PES Lyceum G humanities orientation 18', group_total(LYK, 'Γ΄', 'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών') == 18)
check('PES Lyceum G science-health orientation 18', group_total(LYK, 'Γ΄', 'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας') == 18)
check('PES Lyceum G economics-informatics orientation 18', group_total(LYK, 'Γ΄', 'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής') == 18)

pes_rows = [r for r in rows if r.get('school') in PES]
check('PES rows exist', len(pes_rows) > 0)
check('PES course ids unique', len({r['course_id'] for r in pes_rows}) == len(pes_rows))
check('PES assignment bridge active', not any(r.get('assignment_link_status') == 'pending_assignment_integration' for r in pes_rows))
check('PES Gym extended language row is explicit regulatory gap', any(
    r.get('course_id') == 'pes.gym.deuteri_xeni' and r.get('assignment_link_status') == 'regulatory_gap'
    for r in pes_rows
))
check('PES Lyceum foreign-language rows are choice-dependent', sum(
    len(r.get('hours', {})) for r in pes_rows
    if r.get('school') == LYK and r.get('assignment_link_status') == 'choice_dependent'
) == 3)
check('internal assignment metadata hidden from public rows', all(not any(k.startswith('assignment_') for k in r) for r in public_rows if r.get('school') in PES))

# 2025 amendment: Γ΄ Πρότυπου Εκκλησιαστικού Γυμνασίου = 1 ώρα ΚΠΑ + 1 ώρα Οικονομικά.
def exact_hour(school, grade, subject):
    found = [r for r in rows if r.get('school') == school and r.get('subject') == subject and grade in r.get('hours', {})]
    return len(found) == 1 and found[0]['hours'][grade]
check('PES Gym G social-political education 1h', exact_hour(GYM, 'Γ΄', 'Κοινωνική και Πολιτική Αγωγή') == 1)
check('PES Gym G Economics 1h', exact_hour(GYM, 'Γ΄', 'Οικονομικά') == 1)
check('PES Gym no active Technology row', not any(r.get('school') == GYM and r.get('subject') == 'Τεχνολογία' for r in rows))
check('PES Gym expanded second-language set', any(r.get('school') == GYM and r.get('subject') == 'Γαλλικά / Γερμανικά / Ρωσικά / Αραβικά / Τουρκικά' and r.get('hours') == {'Α΄':2,'Β΄':2,'Γ΄':2} for r in rows))

# 2022 amendment: Γ΄ Εκκλησιαστικού Λυκείου public orientation title is «Αρχαία Ελληνικά».
check('PES Lyceum G exact Ancient Greek title', any(r.get('course_id') == 'pes.lykeio.g.hum.archaia' and r.get('subject') == 'Αρχαία Ελληνικά' and r.get('hours',{}).get('Γ΄') == 6 for r in rows))
check('PES Lyceum G general Math/History share slot', {r.get('subject') for r in rows if r.get('slot_id') == 'pes.lykeio.g.general.choice'} == {'Μαθηματικά','Ιστορία'})
check('PES Lyceum G Math/Biology science choice share slot', {r.get('subject') for r in rows if r.get('slot_id') == 'pes.lykeio.g.science_field_choice'} == {'Μαθηματικά','Βιολογία'})

# Public UI exposure / source card.
check('PES badge on page', 'Πρότυπα Εκκλησιαστικά' in PAGE)
check('PES disclaimer on page', 'Πρότυπο Εκκλησιαστικό Γυμνάσιο και Πρότυπο Εκκλησιαστικό Λύκειο' in PAGE)
check('PES 4881 source link on page', 'ΦΕΚ Β΄ 4881/2025 — Πρότυπο Εκκλησιαστικό Γυμνάσιο' in PAGE)
check('PES 4438 source link on page', 'ΦΕΚ Β΄ 4438/2021 · Εκκλησιαστικό Γυμνάσιο/Λύκειο' in PAGE)
check('PES 2781 source link on page', 'ΦΕΚ Β΄ 2781/2022 — τροποποίηση Γ΄ Εκκλησιαστικού Λυκείου' in PAGE)
check('PES Ethics auto-extension guarded', 'Σε Πρότυπα Εκκλησιαστικά Σχολεία, ΕΠΑ.Λ., Π.ΕΠΑ.Λ. και ΕΝ.Ε.Ε.ΓΥ.-Λ. δεν επεκτείνουμε αυτόματα τον κανόνα' in PAGE)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
