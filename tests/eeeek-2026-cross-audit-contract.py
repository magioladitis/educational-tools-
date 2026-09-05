#!/usr/bin/env python3
"""Dedicated EEEEΚ timetable/assignment/workload contract for 2026-2027."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require_once "includes/teaching-workload-model.php";
require_once "includes/eeeek-regulatory-data.php";
$types = weeklyTimetableSchoolTypes();
$rows = array_values(array_filter(weeklyTimetableRows(), function($row) {
    return isset($row['school']) && $row['school'] === 'eeeek';
}));
$assignments = array_values(array_filter(teachingAssignmentsData(), function($row) {
    return isset($row['school']) && $row['school'] === 'eeeek';
}));
$model = array_values(array_filter(teachingWorkloadModel(), function($row) {
    return isset($row['school']) && $row['school'] === 'eeeek';
}));
echo json_encode(array(
    'type' => $types['eeeek'],
    'rows' => $rows,
    'assignments' => $assignments,
    'workshops' => eeeekWorkshopDefinitions(),
    'model' => $model,
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
raw = subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True)
payload = json.loads(raw)
info = payload['type']
rows = payload['rows']
assignments = payload['assignments']
workshops = payload['workshops']
model = payload['model']

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def arow(subject):
    return next((x for x in assignments if x.get('subject') == subject), None)

def mrow(instance_id):
    return next((x for x in model if x.get('instance_id') == instance_id), None)

# School structure and totals.
check('EEEΕΚ school type exists', info.get('label') == 'Εργαστήριο Ειδικής Επαγγελματικής Εκπαίδευσης (Ε.Ε.Ε.ΕΚ.)')
check('EEEΕΚ has six grades A-ST', info.get('grades') == ['Α΄','Β΄','Γ΄','Δ΄','Ε΄','ΣΤ΄'])
expected_parts = {
    'Α΄': {'Εξειδικεύσεις Εργαστηρίων':14, 'Μαθήματα':16},
    'Β΄': {'Εξειδικεύσεις Εργαστηρίων':14, 'Μαθήματα':16},
    'Γ΄': {'Εξειδικεύσεις Εργαστηρίων':15, 'Μαθήματα':15},
    'Δ΄': {'Εξειδικεύσεις Εργαστηρίων':16, 'Μαθήματα':14},
    'Ε΄': {'Εξειδικεύσεις Εργαστηρίων':17, 'Μαθήματα':13},
}
for grade, parts in expected_parts.items():
    p = info['program'][grade]
    check(f'{grade} total 30', p.get('total') == 30)
    check(f'{grade} group totals exact', p.get('parts') == parts)
    exact = sum(int(r.get('hours', {}).get(grade, 0)) for r in rows)
    check(f'{grade} row sum 30', exact == 30)
check('ST is explicitly variable', 'Μεταβλητό' in info['program']['ΣΤ΄'].get('total_display',''))

# Fixed 2002 timetable rows.
check('EEEΕΚ has 11 timetable rows', len(rows) == 11)
main = next(r for r in rows if r.get('course_id') == 'eeeek.lab.main')
second = next(r for r in rows if r.get('course_id') == 'eeeek.lab.second')
third = next(r for r in rows if r.get('course_id') == 'eeeek.lab.third')
check('main workshop hours exact', main.get('hours') == {'Α΄':10,'Β΄':10,'Γ΄':11,'Δ΄':12,'Ε΄':15})
check('second workshop hours exact', second.get('hours') == {'Α΄':2,'Β΄':2,'Γ΄':2,'Δ΄':2,'Ε΄':2})
check('third workshop stops at D', third.get('hours') == {'Α΄':2,'Β΄':2,'Γ΄':2,'Δ΄':2})
check('third workshop reallocation rule preserved', 'κατανέμονται' in third.get('note',''))
st = next(r for r in rows if r.get('course_id') == 'eeeek.st.dynamic_program')
check('ST timetable row is dynamic', st.get('hours_mode') == 'dynamic' and st.get('hours_display',{}).get('ΣΤ΄') == 'Μεταβλητό')
check('ST note references EPE', 'ΕΠΕ' in st.get('note',''))

# 2018 assignments: 7 general + 42 workshops.
check('49 EEEEΚ assignment rows', len(assignments) == 49)
check('7 general assignment rows', len([x for x in assignments if x.get('section') == 'Μαθήματα Γενικής Παιδείας']) == 7)
check('42 workshop assignment rows', len([x for x in assignments if x.get('section') == 'Εργαστήρια']) == 42)
check('42 workshop definitions unique', len(workshops) == 42 and len({x.get('subject') for x in workshops}) == 42)
check('language assignment exact', arow('Γλώσσα').get('A') == ['ΠΕ02','ΠΕ70','ΠΕ71'])
check('math assignment exact', arow('Μαθηματικά').get('A') == ['ΠΕ03','ΠΕ70','ΠΕ71'])
check('music assignment exact', arow('Μουσική').get('A') == ['ΠΕ79.01'] and arow('Μουσική').get('B') == ['ΤΕ16'])
check('aesthetic assignment exact', arow('Αισθητική Αγωγή').get('A') == ['ΠΕ08'] and arow('Αισθητική Αγωγή').get('B') == ['ΠΕ89.01'])
# Protect the official-FEK reading against a known secondary transcription that inserts PE18.27 in B assignment.
build = arow('Οικοδομικής-Μεταλλοτεχνίας')
check('building-metalwork A exact', build.get('A') == ['ΠΕ81','ΠΕ82'])
check('building-metalwork B exact official FEK', build.get('B') == ['ΤΕ02.01','ΤΕ02.02'])
geop = arow('Γεωπονίας-Τροφίμων-Περιβάλλοντος')
check('agri-food-environment A exact', geop.get('A') == ['ΠΕ88.01','ΠΕ88.02','ΠΕ88.03','ΠΕ88.04','ΠΕ88.05'])
check('agri-food-environment B exact', geop.get('B') == ['ΤΕ02.07'])

# Workload bridge: 47 grade instances = 32 direct + 14 choice + 1 thematic.
check('47 EEEEΚ workload instances', len(model) == 47)
status_counts = {}
for x in model:
    status_counts[x.get('resolution_status')] = status_counts.get(x.get('resolution_status'), 0) + 1
check('EEEΕΚ status counts exact', status_counts == {'choice_dependent':14,'direct':32,'thematic_dependent':1})
choices = [x for x in model if x.get('resolution_status') == 'choice_dependent']
check('14 choice-dependent workshop slots', len(choices) == 14)
check('each workshop slot has 42 choices', all(len(x.get('choice_options') or []) == 42 for x in choices))
all_choice_options = [o for x in choices for o in (x.get('choice_options') or [])]
check('588/588 EEEEΚ workshop choices resolved', len(all_choice_options) == 588 and all(o.get('status') == 'resolved' for o in all_choice_options))
check('all EEEEΚ choice targets resolved', all(all(t.get('status') == 'resolved' for t in o.get('targets',[])) for o in all_choice_options))
st_model = mrow('eeeek.st.dynamic_program@ΣΤ΄')
check('ST workload stays thematic', st_model and st_model.get('resolution_status') == 'thematic_dependent')
check('ST workload has 42 eligible workshops', st_model and len(st_model.get('thematic_assignments') or []) == 42)
check('ST workload has no fake fixed total', st_model and st_model.get('hours_mode') == 'dynamic' and 'hours_total' not in st_model)
check('ST thematic hours are not attributed', st_model and st_model.get('component_hours_status') == 'not_fixed_by_regulation')

# Public UI presence, but no school-specific profile is fabricated from the supplied mislabeled snapshot.
assign_page = (ROOT/'anatheseis-mathimaton.php').read_text(encoding='utf-8')
time_page = (ROOT/'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
check('assignments UI exposes EEEEΚ filter', 'schoolEeeek' in assign_page and 'ΦΕΚ Β΄ 1761/2018' in assign_page)
check('timetable UI documents EEEEΚ', 'Ε.Ε.Ε.ΕΚ.' in time_page and '57523/Γ6/2002' in time_page and 'ΣΤ΄ τάξη' in time_page)
check('EEEΕK Corfu conservative school profile available', (ROOT/'includes/school-profile-eeeek-kerkyra-2026.php').exists())

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print('STATUS ' + json.dumps(status_counts, ensure_ascii=False, sort_keys=True))
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
