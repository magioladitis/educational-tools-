#!/usr/bin/env python3
from pathlib import Path
import json
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'weekly-timetable-data.php').read_text(encoding='utf-8')
GDATA = (ROOT / 'includes' / 'weekly-timetable-vocational-g-data.php').read_text(encoding='utf-8')
EDATA = (ROOT / 'includes' / 'weekly-timetable-eneegyl-data.php').read_text(encoding='utf-8')
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
check('ENEEGYL source snapshots', '2259/22-04-2026' in EDATA and '2149/16-04-2026' in EDATA)
check('ENEEGYL independent data file required', "weekly-timetable-eneegyl-data.php" in DATA)
check('ENEEGYL Gym four grades 31', "'eneegyl_gymnasio'" in DATA and DATA.count("'total' => 31") >= 4)
check('ENEEGYL Lyceum programme splits', "'eneegyl_lykeio'" in DATA and "'Α΄' => array('total' => 30, 'parts' => array('Γενική Παιδεία' => 20, 'Προσανατολισμός' => 4, 'Μαθήματα Επιλογής' => 6))" in DATA and "'Β΄' => array('total' => 30, 'parts' => array('Γενική Παιδεία' => 15, 'Μαθήματα Τομέα' => 15))" in DATA and "'Δ΄' => array('total' => 30, 'parts' => array('Γενική Παιδεία' => 10, 'Μαθήματα Ειδικότητας' => 20))" in DATA)
check('ENEEGYL has 8 sectors', EDATA.count("function weeklyTimetableEneegylTrackLabels()") == 1 and "'health' => 'Υγείας - Πρόνοιας - Ευεξίας'" in EDATA and "'naval'" not in EDATA)
check('ENEEGYL D has no naval specialties', "'captain'" not in EDATA and "'engineer' => 'Μηχανικός Εμπορικού Ναυτικού'" not in EDATA)
check('ENEEGYL A choices are seven real courses', EDATA.count("'choice_set_id'=>'eneegyl.lykeio.a.choices'") == 7 and "'choice_count'=>3" in EDATA and "'subject'=>'Μαθήματα Επιλογής (3 από 7)'" not in EDATA)
check('EPAL A choices are eight real courses', DATA.count("'choice_set_id'=>'epal.a.choices'") == 8 and "'subject'=>'Μαθήματα Επιλογής (3 από 8)'" not in DATA)
check('evening EPAL A choices are eight real courses', DATA.count("'choice_set_id'=>'eepal.a.choices'") == 8 and "'subject'=>'Μαθήματα Επιλογής (3 από 8)'" not in DATA)
check('internal timetable-assignment crosswalk loaded', 'teaching-timetable-crosswalk.php' in DATA and 'teachingTimetableEnrichRows' in DATA)
check('ENEEGYL health fallback variant data', 'weeklyTimetableEneegylHealthFallbackProgramData' in EDATA and "'4Θ + 1Ε'" in EDATA and "'1Θ + 4Ε'" in EDATA and "'1Θ + 3Ε'" in EDATA)
check('ENEEGYL health variant selector config', "'variants_by_grade_track'" in DATA and "'two_specials' => 'Διδάσκονται δύο Ειδικά Μαθήματα'" in DATA and "'one_special' => 'Δεν είναι δυνατή η διδασκαλία δεύτερου Ειδικού Μαθήματος'" in DATA)
check('page supports timetable variant selector', 'id="variantField"' in PAGE and 'function currentVariants(school, grade, track)' in PAGE and 'row.variant && row.variant !== variant' in PAGE)
check('ENEEGYL public sources exposed', '2259/2026 — Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ.' in PAGE and '2149/2026 — Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ.' in PAGE)
check('ENEEGYL roadmap remains internal', 'ΕΝ.Ε.Ε.ΓΥ.-Λ.' in PAGE and 'μελλοντική διασύνδεση με αναθέσεις' not in PAGE)
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

def slot_sum(school, grade, group, track=None, specialty=None, variant=None):
    ordinary = 0
    slots = {}
    choice_sets = {}
    choice_counts = {}
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
        row_variant = row.get('variant')
        if variant is None and row_variant is not None:
            continue
        if variant is not None and row_variant not in (None, variant):
            continue
        hours = row.get('hours', {}).get(grade)
        if hours is None:
            continue
        choice_set = row.get('choice_set_id')
        if choice_set:
            choice_sets.setdefault(choice_set, []).append(float(hours))
            choice_counts[choice_set] = int(row.get('choice_count', 1))
            continue
        sid = row.get('slot_id')
        if sid:
            slots[sid] = max(slots.get(sid, 0), float(hours))
        else:
            ordinary += float(hours)
    choices_total = 0
    for choice_set, values in choice_sets.items():
        count = choice_counts.get(choice_set, 1)
        choices_total += sum(sorted(values, reverse=True)[:count])
    return ordinary + sum(slots.values()) + choices_total

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

# EN.E.E.GY.-L.: verify the 2026-2027 four-grade Gymnasium and Lyceum structures.
for grade in ('Α΄', 'Β΄', 'Γ΄', 'Δ΄'):
    check('row sum eneegyl_gymnasio / ' + grade, slot_sum('eneegyl_gymnasio', grade, 'Κοινό πρόγραμμα') == 31)

eneegyl_lykeio = payload['schools']['eneegyl_lykeio']
check('ENEEGYL Lyceum sector count', len(eneegyl_lykeio['tracks_by_grade']['Β΄']) == 8 and len(eneegyl_lykeio['tracks_by_grade']['Γ΄']) == 8 and len(eneegyl_lykeio['tracks_by_grade']['Δ΄']) == 8)
check('ENEEGYL Lyceum A general', slot_sum('eneegyl_lykeio', 'Α΄', 'Μαθήματα Γενικής Παιδείας') == 20)
check('ENEEGYL Lyceum A orientation', slot_sum('eneegyl_lykeio', 'Α΄', 'Μαθήματα Προσανατολισμού') == 4)
check('ENEEGYL Lyceum A choices', slot_sum('eneegyl_lykeio', 'Α΄', 'Μαθήματα Επιλογής') == 6)
eneegyl_a_choices = [row for row in payload['rows'] if row.get('school') == 'eneegyl_lykeio' and row.get('choice_set_id') == 'eneegyl.lykeio.a.choices']
check('ENEEGYL Lyceum A seven choice rows', len(eneegyl_a_choices) == 7 and all(row.get('hours', {}).get('Α΄') == 2 for row in eneegyl_a_choices))
epal_a_choices = [row for row in payload['rows'] if row.get('school') == 'epal' and row.get('choice_set_id') == 'epal.a.choices']
eepal_a_choices = [row for row in payload['rows'] if row.get('school') == 'esperino_epal' and row.get('choice_set_id') == 'eepal.a.choices']
check('EPAL A eight choice rows', len(epal_a_choices) == 8 and all(row.get('hours', {}).get('Α΄') == 2 and row.get('choice_count') == 3 for row in epal_a_choices))
check('evening EPAL A eight choice rows', len(eepal_a_choices) == 8 and all(row.get('hours', {}).get('Α΄') == 2 and row.get('choice_count') == 3 for row in eepal_a_choices))
check('ENEEGYL Lyceum A drawing choice display', any(row.get('subject') == 'Αρχές Γραμμικού και Αρχιτεκτονικού Σχεδίου' and row.get('hours_display', {}).get('Α΄') == '2Σ' for row in eneegyl_a_choices))
for grade in ('Β΄', 'Γ΄'):
    check('ENEEGYL Lyceum ' + grade + ' general', slot_sum('eneegyl_lykeio', grade, 'Μαθήματα Γενικής Παιδείας') == 15)
    for track_code, track_label in eneegyl_lykeio['tracks_by_grade'][grade].items():
        group = 'Μαθήματα Τομέα · ' + track_label
        if track_code == 'health':
            standard = slot_sum('eneegyl_lykeio', grade, group, track_code, variant='two_specials')
            fallback = slot_sum('eneegyl_lykeio', grade, group, track_code, variant='one_special')
            check('ENEEGYL Lyceum ' + grade + ' sector health / two specials', standard == 15)
            check('ENEEGYL Lyceum ' + grade + ' sector health / one special', fallback == 15)
        else:
            actual = slot_sum('eneegyl_lykeio', grade, group, track_code)
            check('ENEEGYL Lyceum ' + grade + ' sector ' + track_code, actual == 15)

check('ENEEGYL Lyceum D general', slot_sum('eneegyl_lykeio', 'Δ΄', 'Μαθήματα Γενικής Παιδείας') == 10)
eneegyl_d = eneegyl_lykeio['specialties_by_grade_track']['Δ΄']
eneegyl_d_count = sum(len(items) for items in eneegyl_d.values())
check('ENEEGYL Lyceum D specialty count', eneegyl_d_count == 33)
for track_code, items in eneegyl_d.items():
    for specialty_code, specialty_label in items.items():
        group = 'Μαθήματα Ειδικότητας · ' + specialty_label
        actual = slot_sum('eneegyl_lykeio', 'Δ΄', group, track_code, specialty_code)
        check('ENEEGYL Lyceum D specialty ' + specialty_code, actual == 20)
        check('ENEEGYL Lyceum D total ' + specialty_code, 10 + actual == 30)

def find_row(course_id):
    return next((row for row in payload['rows'] if row.get('course_id') == course_id), None)

sep = find_row('eneegyl.gym.sep')
check('ENEEGYL Gym SEP B display', sep is not None and sep.get('hours_display', {}).get('Β΄') == '1Θ + 2Ε')
plant = find_row('eneegyl.lykeio.d.plant.6')
check('ENEEGYL D plant phytoprotection', plant is not None and plant.get('subject') == 'Φυτοπροστασία' and plant.get('hours_display', {}).get('Δ΄') == '1Θ + 2Ε')
nurse = find_row('eneegyl.lykeio.d.nurse.3')
check('ENEEGYL D nursing II', nurse is not None and nurse.get('subject') == 'Νοσηλευτική ΙΙ' and nurse.get('hours_display', {}).get('Δ΄') == '2Θ + 8Ε')
graphic = find_row('eneegyl.lykeio.d.graphic.4')
check('ENEEGYL D graphic applications', graphic is not None and graphic.get('subject') == 'Γραφιστικές Εφαρμογές' and graphic.get('hours_display', {}).get('Δ΄') == '3Ε')
health_fallback_b = [row for row in payload['rows'] if row.get('school') == 'eneegyl_lykeio' and row.get('track') == 'health' and row.get('variant') == 'one_special' and row.get('hours', {}).get('Β΄') is not None]
health_fallback_c = [row for row in payload['rows'] if row.get('school') == 'eneegyl_lykeio' and row.get('track') == 'health' and row.get('variant') == 'one_special' and row.get('hours', {}).get('Γ΄') is not None]
check('ENEEGYL health fallback B exact rows', len(health_fallback_b) == 4 and any(row.get('subject') == 'Ειδικό Μάθημα Α' and row.get('hours_display', {}).get('Β΄') == '1Θ + 4Ε' for row in health_fallback_b))
check('ENEEGYL health fallback C exact rows', len(health_fallback_c) == 6 and any(row.get('subject') == 'Πρώτες Βοήθειες' and row.get('hours_display', {}).get('Γ΄') == '1Θ + 2Ε' for row in health_fallback_c))

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
