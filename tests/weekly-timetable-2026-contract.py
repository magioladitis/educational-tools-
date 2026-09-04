#!/usr/bin/env python3
from pathlib import Path
import json
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'weekly-timetable-data.php').read_text(encoding='utf-8')
PAGE = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
TOOLS = (ROOT / 'ergaleia.php').read_text(encoding='utf-8')

checks = []
def check(name, cond): checks.append((name, bool(cond)))

for source in ('2132/09-04-2026', '2106/09-04-2026', '2102/09-04-2026', '2104/09-04-2026', '2107/09-04-2026'):
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
check('public religion ethics row is combined', "publicRow.subject = 'Θρησκευτικά / Ηθική'" in PAGE)
check('public roadmap UI removed', 'class="architecture-note"' not in PAGE and 'Έτοιμο για μελλοντική διασύνδεση με αναθέσεις' not in PAGE)
check('internal course ids stay out of public UI', 'ID: ' not in PAGE and "row.course_id" not in PAGE)
check('page has no inline style block', '<style>' not in PAGE)
check('page uses dedicated stylesheet', 'assets/weekly-timetable.css' in PAGE and (ROOT / 'assets' / 'weekly-timetable.css').exists())
check('tool card added', 'href="orologio-programma-mathimaton.php"' in TOOLS and '<span class="tool-number">31</span>' in TOOLS)
check('tool directory count 31', '31 διαθέσιμα εργαλεία' in TOOLS and 'Εμφανίζονται 31 εργαλεία.' in TOOLS)

# Execute the PHP dataset and verify that the rows themselves add up to the
# declared programme totals. Rows with the same slot_id are alternatives and
# therefore count only once (using the maximum hours for that slot).
php_code = f"require {json.dumps(str(ROOT / 'includes' / 'weekly-timetable-data.php'))}; echo json_encode(array('schools'=>weeklyTimetableSchoolTypes(),'rows'=>weeklyTimetableRows()), JSON_UNESCAPED_UNICODE);"
proc = subprocess.run(['php', '-r', php_code], capture_output=True, text=True, check=True)
payload = json.loads(proc.stdout)

def slot_sum(school, grade, group, track=None):
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

for key, expected in expected_groups.items():
    actual = slot_sum(*key)
    label = ' / '.join(key)
    check('row sum ' + label, actual == expected)

ids = re.findall(r"'course_id'=>'([^']+)'", DATA)
check('course ids unique', len(ids) == len(set(ids)) and len(ids) >= 250)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
