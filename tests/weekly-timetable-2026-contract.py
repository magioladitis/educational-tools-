#!/usr/bin/env python3
from pathlib import Path
import json
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'weekly-timetable-data.php').read_text(encoding='utf-8')
GDATA = (ROOT / 'includes' / 'weekly-timetable-vocational-g-data.php').read_text(encoding='utf-8')
PAGE = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
TOOLS = (ROOT / 'ergaleia.php').read_text(encoding='utf-8')
CSS = (ROOT / 'assets' / 'weekly-timetable.css').read_text(encoding='utf-8')

checks = []
def check(name, cond): checks.append((name, bool(cond)))

for source in (
    '2132/09-04-2026', '2106/09-04-2026', '2102/09-04-2026',
    '2104/09-04-2026', '2107/09-04-2026', '2151/16-04-2026',
    '2187/12-06-2018', '2636/05-07-2018', '3224/07-08-2018',
    '2136/09-04-2026', '3470/29-07-2021', '4578/30-08-2022',
    '4961/22-09-2022', '1426/26-04-2017', '2072/15-06-2017',
    '2122/08-07-2018', '4373/01-10-2018', '4815/30-10-2018',
    '5251/30-08-2023',
):
    check('source ' + source, source in DATA)

check('stable course ids documented', 'course_id' in DATA and 'σταθερό `course_id`' in DATA)
check('day Gym totals', "'Α΄' => array('total' => 33)" in DATA and "'Γ΄' => array('total' => 35)" in DATA)
check('evening Gym totals', "'Α΄' => array('total' => 24)" in DATA and "'Β΄' => array('total' => 25)" in DATA)
check('day GEL B split 30+5', "'Β΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 30, 'Ομάδα Προσανατολισμού' => 5))" in DATA)
check('day GEL C split 14+18', "'Γ΄' => array('total' => 32, 'parts' => array('Γενική Παιδεία' => 14, 'Ομάδα Προσανατολισμού' => 18))" in DATA)
check('evening GEL B split 20+5', "'Β΄' => array('total' => 25, 'parts' => array('Γενική Παιδεία' => 20, 'Ομάδα Προσανατολισμού' => 5))" in DATA)
check('evening GEL C split 7+18', "'Γ΄' => array('total' => 25, 'parts' => array('Γενική Παιδεία' => 7, 'Ομάδα Προσανατολισμού' => 18))" in DATA)
check('artistic Gym totals 40', "'kallitexniko_gymnasio'" in DATA and "'Α΄' => array('total' => 40, 'parts' => array('Γενική Παιδεία' => 26, 'Καλλιτεχνική Παιδεία' => 14))" in DATA)
check('artistic GEL totals 40', "'kallitexniko_gel'" in DATA and "'Γ΄' => array('total' => 40, 'parts' => array('Γενική Παιδεία' => 11, 'Ομάδα Προσανατολισμού' => 18, 'Καλλιτεχνική Παιδεία' => 11))" in DATA)
check('music Gym totals 42', "'mousiko_gymnasio'" in DATA and "'Γ΄' => array('total' => 42, 'parts' => array('Γενική Παιδεία' => 28, 'Μουσική Παιδεία' => 14))" in DATA)
check('music GEL totals 42', "'mousiko_gel'" in DATA and "'Γ΄' => array('total' => 42, 'parts' => array('Γενική Παιδεία' => 12, 'Ομάδα Προσανατολισμού' => 18, 'Μουσική Παιδεία' => 12))" in DATA)
check('day EPAL totals A 35 B 35 C 35', "'epal' => array(" in DATA and "'Α΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 22, 'Προσανατολισμός' => 7, 'Μαθήματα Επιλογής' => 6))" in DATA and "'Β΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 12, 'Μαθήματα Τομέα' => 23))" in DATA and "'Γ΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 12, 'Μαθήματα Ειδικότητας' => 23))" in DATA)
check('evening EPAL totals A 30 B 30 C 30', "'esperino_epal' => array(" in DATA and "'Α΄' => array('total' => 30, 'parts' => array('Γενική Παιδεία' => 20, 'Προσανατολισμός' => 4, 'Μαθήματα Επιλογής' => 6))" in DATA and "'Β΄' => array('total' => 30, 'parts' => array('Γενική Παιδεία' => 10, 'Μαθήματα Τομέα' => 20))" in DATA and "'Γ΄' => array('total' => 30, 'parts' => array('Γενική Παιδεία' => 10, 'Μαθήματα Ειδικότητας' => 20))" in DATA)
check('PEPAL totals A 35 B 35 C 35', "'pepal' => array(" in DATA and "'Α΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 22, 'Επαγγελματική Κατεύθυνση' => 13))" in DATA and "'Β΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 12, 'Μαθήματα Τομέα' => 23))" in DATA and "'Γ΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 12, 'Μαθήματα Ειδικότητας' => 23))" in DATA)
check('B/C vocational use sector selector', DATA.count("'track_label_by_grade' => array('Β΄' => 'Τομέας', 'Γ΄' => 'Τομέας')") == 3 and DATA.count("'Γ΄' => weeklyTimetableVocationalTrackLabels()") == 3)
check('C vocational uses specialty selector config', DATA.count("'specialty_label_by_grade' => array('Γ΄' => 'Ειδικότητα')") == 3 and DATA.count("'specialties_by_grade_track' => array('Γ΄' => weeklyTimetableVocationalSpecialties())") == 3)
check('C vocational helper has 35 specialties', GDATA.count("function weeklyTimetableVocationalSpecialties()") == 1 and GDATA.count("'captain' => 'Πλοίαρχος Εμπορικού Ναυτικού'") == 1)
check('PEPAL C practical exercise annotations preserved', '(ΠΑ 3Ε)' in GDATA and '2Ε + 2ΠΑ' in GDATA)
check('PEPAL accounting amendment applied', "'subject'=>'Εισαγωγή στη Λογιστική'" in DATA and 'Εισαγωγή στη Χρηματοοικονομική Λογιστική' not in DATA)
check('PEPAL exact automation title', 'Εισαγωγή στον Αυτοματισμό (Αυτοματισμοί και Αισθητήρες)' in DATA)
check('PEPAL exact refrigeration title', 'Βασικές Αρχές Ψύξης - Κλιματισμού, Θερμάνσεων, ΜΕΚ και ΑΠΕ' in DATA)
check('PEPAL health ethics title', 'Εργασιακό Περιβάλλον Τομέα - Δεοντολογία' in DATA)
check('evening GEL preserves 1/2', "'Β΄'=>'1 / 2'" in DATA)
check('evening GEL preserves 2/1', "'Β΄'=>'2 / 1'" in DATA)
check('C GEL conditional general History', 'Μόνο για μαθητές/ήτριες των Ομάδων Θετικών Σπουδών' in DATA)
check('C GEL conditional general Math', 'Μόνο για μαθητές/ήτριες που επιλέγουν την Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών.' in DATA)
check('religion and ethics are separate courses', "'course_id'=>'gym.thriskeftika'" in DATA and "'course_id'=>'gym.ithiki'" in DATA)
check('religion ethics share slot', "'slot_id'=>'gym.religion_ethics'" in DATA and "'mode'=>'alternative'" in DATA)
check('health math and biology are separate courses', "'course_id'=>'gel.c.health.mathimatika'" in DATA and "'course_id'=>'gel.c.health.viologia'" in DATA)
check('health field choice shares slot', DATA.count("'slot_id'=>'gel.c.health.field_choice'") == 2)
check('period hours are explicit', "'period_hours'=>array('Β΄'=>array('Α΄ τετράμηνο'=>1,'Β΄ τετράμηνο'=>2))" in DATA and "'period_hours'=>array('Β΄'=>array('Α΄ τετράμηνο'=>2,'Β΄ τετράμηνο'=>1))" in DATA)
check('page exposes school and grade selectors', 'id="schoolType"' in PAGE and 'id="grade"' in PAGE)
check('artistic direction selector', 'id="trackField"' in PAGE and 'id="track"' in PAGE and 'row.track && row.track !== track' in PAGE)
check('dynamic direction or sector label', 'id="trackLabel"' in PAGE and 'function currentTracks(school, grade)' in PAGE and 'function currentTrackLabel(school, grade)' in PAGE and 'trackLabel.textContent = currentTrackLabel(school, grade)' in PAGE)
check('dynamic specialty selector', 'id="specialtyField"' in PAGE and 'id="specialty"' in PAGE and 'function currentSpecialties(school, grade, track)' in PAGE and 'row.specialty && row.specialty !== specialty' in PAGE)
check('C PEPAL official source exposed', '5251/2023 — Γ΄ Π.ΕΠΑ.Λ.' in PAGE and 'orologio-programma-g-taksi---epaggelmatiki-ekpaidefsi' in PAGE)
check('professional hour badges avoid duplicate unit', 'function hoursBadgeText(row)' in PAGE and "/[ΘΕΣ]|ΠΑ/.test(text) ? text : text + ' ώρ.'" in PAGE)
check('professional legend exposed', '<strong>Θ</strong> = θεωρία' in PAGE and '<strong>ΠΑ</strong> = πρακτική άσκηση' in PAGE)
check('public religion ethics row is combined', "publicRow.subject = 'Θρησκευτικά / Ηθική'" in PAGE)
check('public roadmap UI removed', 'class="architecture-note"' not in PAGE and 'Έτοιμο για μελλοντική διασύνδεση με αναθέσεις' not in PAGE)
check('internal course ids stay out of public UI', 'ID: ' not in PAGE and "row.course_id" not in PAGE)
check('page has no inline style block', '<style>' not in PAGE)
check('page uses semantic timetable row markup', 'timetable-course-row' in PAGE and 'timetable-course-title' in PAGE and 'timetable-course-meta' in PAGE and 'timetable-course-detail' in PAGE)
check('timetable detail labels are explicit', 'Ενότητα:' in PAGE and 'Προϋπόθεση:' in PAGE and 'timetable-detail-label' in PAGE)
check('render resolves specialty explicitly', "var specialty = specialtyField.hidden ? '' : specialtySelect.value;" in PAGE)
check('timetable CSS owns title alignment', '.timetable-course-title' in CSS and 'text-align:left' in CSS)
check('timetable CSS overrides generic result row layout', 'body.edu-ui.edu-calc-standard.edu-page-weekly-timetable .result-row.timetable-course-row' in CSS and 'grid-template-columns:minmax(0,1fr) max-content' in CSS)
check('page uses dedicated stylesheet', 'assets/weekly-timetable.css' in PAGE and (ROOT / 'assets' / 'weekly-timetable.css').exists())
check('tool card added', 'href="orologio-programma-mathimaton.php"' in TOOLS and '<span class="tool-number">31</span>' in TOOLS)
check('tool directory count 31', '31 διαθέσιμα εργαλεία' in TOOLS and 'Εμφανίζονται 31 εργαλεία.' in TOOLS)

# Execute the PHP dataset and verify that the rows themselves add up to the
# declared programme totals. Rows with the same slot_id are alternatives and
# therefore count only once (using the maximum hours for that slot).
php_code = f"require {json.dumps(str(ROOT / 'includes' / 'weekly-timetable-data.php'))}; echo json_encode(array('schools'=>weeklyTimetableSchoolTypes(),'rows'=>weeklyTimetableRows()), JSON_UNESCAPED_UNICODE);"
proc = subprocess.run(['php', '-r', php_code], capture_output=True, text=True, check=True)
payload = json.loads(proc.stdout)

def slot_sum(school, grade, group, track=None, specialty=None):
    ordinary = 0
    slots = {}
    for row in payload['rows']:
        if row.get('school') != school or row.get('group') != group:
            continue
        row_track = row.get('track')
        if track is None and row_track is not None:
            continue
        if track is not None and row_track not in (None, track):
            continue
        row_specialty = row.get('specialty')
        if specialty is None and row_specialty is not None:
            continue
        if specialty is not None and row_specialty not in (None, specialty):
            continue
        hours = row.get('hours', {}).get(grade)
        if hours is None:
            continue
        sid = row.get('slot_id')
        if sid:
            slots[sid] = max(slots.get(sid, 0), float(hours))
        else:
            ordinary += float(hours)
    return ordinary + sum(slots.values())

expected_groups = {
    ('gymnasio','Α΄','Κοινό πρόγραμμα'):33,
    ('gymnasio','Β΄','Κοινό πρόγραμμα'):33,
    ('gymnasio','Γ΄','Κοινό πρόγραμμα'):35,
    ('esperino_gymnasio','Α΄','Κοινό πρόγραμμα'):24,
    ('esperino_gymnasio','Β΄','Κοινό πρόγραμμα'):25,
    ('esperino_gymnasio','Γ΄','Κοινό πρόγραμμα'):25,
    ('gel','Α΄','Μαθήματα Γενικής Παιδείας'):35,
    ('gel','Β΄','Μαθήματα Γενικής Παιδείας'):30,
    ('gel','Γ΄','Μαθήματα Γενικής Παιδείας'):14,
    ('gel','Β΄','Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών'):5,
    ('gel','Β΄','Ομάδα Προσανατολισμού Θετικών Σπουδών'):5,
    ('gel','Γ΄','Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών'):18,
    ('gel','Γ΄','Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας'):18,
    ('gel','Γ΄','Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής'):18,
    ('esperino_gel','Α΄','Μαθήματα Γενικής Παιδείας'):25,
    ('esperino_gel','Β΄','Μαθήματα Γενικής Παιδείας'):20,
    ('esperino_gel','Γ΄','Μαθήματα Γενικής Παιδείας'):7,
    ('esperino_gel','Β΄','Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών'):5,
    ('esperino_gel','Β΄','Ομάδα Προσανατολισμού Θετικών Σπουδών'):5,
    ('esperino_gel','Γ΄','Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών'):18,
    ('esperino_gel','Γ΄','Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας'):18,
    ('esperino_gel','Γ΄','Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής'):18,
}
# Extended structures: track-aware and choice-slot-aware sums.
extended_groups = {
    ('kallitexniko_gymnasio','Α΄','Μαθήματα Γενικής Παιδείας'):26,
    ('kallitexniko_gymnasio','Β΄','Μαθήματα Γενικής Παιδείας'):26,
    ('kallitexniko_gymnasio','Γ΄','Μαθήματα Γενικής Παιδείας'):27,
    ('kallitexniko_gymnasio','Α΄','Μαθήματα Καλλιτεχνικής Παιδείας','visual'):14,
    ('kallitexniko_gymnasio','Β΄','Μαθήματα Καλλιτεχνικής Παιδείας','theatre'):14,
    ('kallitexniko_gymnasio','Γ΄','Μαθήματα Καλλιτεχνικής Παιδείας','dance'):13,
    ('kallitexniko_gel','Α΄','Μαθήματα Γενικής Παιδείας'):30,
    ('kallitexniko_gel','Β΄','Μαθήματα Γενικής Παιδείας'):26,
    ('kallitexniko_gel','Γ΄','Μαθήματα Γενικής Παιδείας'):11,
    ('kallitexniko_gel','Α΄','Μαθήματα Καλλιτεχνικής Παιδείας','visual'):10,
    ('kallitexniko_gel','Β΄','Μαθήματα Καλλιτεχνικής Παιδείας','theatre'):9,
    ('kallitexniko_gel','Γ΄','Μαθήματα Καλλιτεχνικής Παιδείας','dance'):9,
    ('kallitexniko_gel','Γ΄','Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)'):2,
    ('mousiko_gymnasio','Α΄','Μαθήματα Γενικής Παιδείας'):29,
    ('mousiko_gymnasio','Β΄','Μαθήματα Γενικής Παιδείας'):29,
    ('mousiko_gymnasio','Γ΄','Μαθήματα Γενικής Παιδείας'):28,
    ('mousiko_gymnasio','Α΄','Μαθήματα Μουσικής Παιδείας'):13,
    ('mousiko_gymnasio','Β΄','Μαθήματα Μουσικής Παιδείας'):13,
    ('mousiko_gymnasio','Γ΄','Μαθήματα Μουσικής Παιδείας'):14,
    ('mousiko_gel','Α΄','Μαθήματα Γενικής Παιδείας'):29,
    ('mousiko_gel','Β΄','Μαθήματα Γενικής Παιδείας'):26,
    ('mousiko_gel','Γ΄','Μαθήματα Γενικής Παιδείας'):12,
    ('mousiko_gel','Α΄','Μαθήματα Μουσικής Παιδείας'):12,
    ('mousiko_gel','Α΄','Μάθημα Επιλογής Μουσικής Παιδείας (1 από 6)'):1,
    ('mousiko_gel','Β΄','Μαθήματα Μουσικής Παιδείας'):11,
    ('mousiko_gel','Γ΄','Μαθήματα Μουσικής Παιδείας'):10,
    ('mousiko_gel','Γ΄','Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)'):2,
}
expected_groups.update(extended_groups)

# Vocational A/B: verify every declared component and all nine sector programmes.
vocational_common = {
    'epal': {
        'Α΄': [('Μαθήματα Γενικής Παιδείας',22), ('Μαθήματα Προσανατολισμού',7), ('Μαθήματα Επιλογής',6)],
        'Β΄': [('Μαθήματα Γενικής Παιδείας',12)],
        'sector_total': 23,
    },
    'esperino_epal': {
        'Α΄': [('Μαθήματα Γενικής Παιδείας',20), ('Μαθήματα Προσανατολισμού',4), ('Μαθήματα Επιλογής',6)],
        'Β΄': [('Μαθήματα Γενικής Παιδείας',10)],
        'sector_total': 20,
    },
    'pepal': {
        'Α΄': [('Μαθήματα Γενικής Παιδείας',22), ('Μαθήματα Επαγγελματικής Κατεύθυνσης Προσανατολιστικού Χαρακτήρα',13)],
        'Β΄': [('Μαθήματα Γενικής Παιδείας',12)],
        'sector_total': 23,
    },
}
for school, spec in vocational_common.items():
    for grade in ('Α΄', 'Β΄'):
        for group, expected in spec[grade]:
            expected_groups[(school, grade, group)] = expected
    tracks = payload['schools'][school]['tracks_by_grade']['Β΄']
    for code, label in tracks.items():
        actual = slot_sum(school, 'Β΄', 'Μαθήματα Τομέα · ' + label, code)
        check('row sum ' + school + ' / Β΄ / ' + code, actual == spec['sector_total'])

# Vocational Γ: verify all 35 specialties for each school type.
expected_c = {'epal': (12, 23, 35), 'esperino_epal': (10, 20, 30), 'pepal': (12, 23, 35)}
for school, (general_total, specialty_total, overall_total) in expected_c.items():
    check('row sum ' + school + ' / Γ΄ / general', slot_sum(school, 'Γ΄', 'Μαθήματα Γενικής Παιδείας') == general_total)
    hierarchy = payload['schools'][school]['specialties_by_grade_track']['Γ΄']
    count = sum(len(items) for items in hierarchy.values())
    check('specialty count ' + school + ' / Γ΄', count == 35)
    for track_code, items in hierarchy.items():
        for specialty_code, specialty_label in items.items():
            group = 'Μαθήματα Ειδικότητας · ' + specialty_label
            actual = slot_sum(school, 'Γ΄', group, track_code, specialty_code)
            check('row sum ' + school + ' / Γ΄ / ' + specialty_code, actual == specialty_total)
            check('total ' + school + ' / Γ΄ / ' + specialty_code, general_total + actual == overall_total)

for key, expected in expected_groups.items():
    actual = slot_sum(*key)
    label = ' / '.join(key)
    check('row sum ' + label, actual == expected)

ids = [row.get('course_id') for row in payload['rows'] if row.get('course_id')]
check('course ids unique', len(ids) == len(set(ids)) and len(ids) >= 1100)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
