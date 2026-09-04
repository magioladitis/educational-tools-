#!/usr/bin/env python3
"""Regression contract after 2026-09 myschool cross-check of core school structures.

The myschool snapshots are operational evidence only. The assertions below protect
legal/timetable semantics already verified against the cited FEKs.
"""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]
php = r'''
require "includes/weekly-timetable-data.php";
require "includes/teaching-assignments-data.php";
echo json_encode(array(
  "rows" => weeklyTimetableRows(),
  "public_rows" => weeklyTimetablePublicRows(),
  "assignments" => teachingAssignmentsData()
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
payload = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))
rows = payload['rows']
public_rows = payload['public_rows']
assignments = payload['assignments']

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def row_by_id(course_id):
    return next((r for r in rows if r.get('course_id') == course_id), None)

def option_map(row):
    return {o.get('label'): o for o in (row.get('assignment_choice_options') or [])}

# Ημερήσιο Γυμνάσιο — the operational school may offer only some languages,
# but the legal nationwide timetable keeps French/German/Italian and PE34 condition.
gym = row_by_id('gym.deyteri_xeni')
check('day Gym second language row exists', gym is not None)
check('day Gym second language hours 2/2/2', gym and gym.get('hours') == {'Α΄': 2, 'Β΄': 2, 'Γ΄': 2})
check('day Gym second language is choice-dependent', gym and gym.get('assignment_link_status') == 'choice_dependent')
gym_opts = option_map(gym or {})
check('day Gym language choices exact', set(gym_opts) == {'Γαλλικά', 'Γερμανικά', 'Ιταλικά'})
check('day Gym French -> PE05', gym_opts.get('Γαλλικά', {}).get('codes') == ['ΠΕ05'])
check('day Gym German -> PE07', gym_opts.get('Γερμανικά', {}).get('codes') == ['ΠΕ07'])
check('day Gym Italian -> PE34', gym_opts.get('Ιταλικά', {}).get('codes') == ['ΠΕ34'])
check('day Gym Italian retains placement condition', bool(gym_opts.get('Ιταλικά', {}).get('condition')) and '2016-2017' in gym_opts['Ιταλικά']['condition'])

# Ημερήσιο ΓΕΛ — myschool exposes French/German as distinct operational courses.
gel = row_by_id('gel.general.deyteri_xeni')
check('day GEL second language row exists', gel is not None)
check('day GEL second language hours A2 B1', gel and gel.get('hours') == {'Α΄': 2, 'Β΄': 1})
check('day GEL second language is choice-dependent', gel and gel.get('assignment_link_status') == 'choice_dependent')
gel_opts = option_map(gel or {})
check('day GEL language choices exact', set(gel_opts) == {'Γαλλικά', 'Γερμανικά'})
check('day GEL French -> PE05', gel_opts.get('Γαλλικά', {}).get('codes') == ['ΠΕ05'])
check('day GEL German -> PE07', gel_opts.get('Γερμανικά', {}).get('codes') == ['ΠΕ07'])

# Εσπερινό Γυμνάσιο — the local myschool snapshot lacks English, but FEK 2106/2026
# still requires it. Protect against deleting a legally valid course from local absence.
egym_en = row_by_id('egym.agglika')
check('evening Gym English retained', egym_en is not None)
check('evening Gym English hours 2/2/2', egym_en and egym_en.get('hours') == {'Α΄': 2, 'Β΄': 2, 'Γ΄': 2})

# Εσπερινό ΓΕΛ — semester asymmetry is preserved exactly, not averaged.
egel_chem = row_by_id('egel.general.ximeia')
egel_bio = row_by_id('egel.general.viologia')
check('evening GEL Chemistry semester display 1/2', egel_chem and egel_chem.get('hours_display', {}).get('Β΄') == '1 / 2')
check('evening GEL Chemistry semester hours exact', egel_chem and egel_chem.get('period_hours', {}).get('Β΄') == {'Α΄ τετράμηνο': 1, 'Β΄ τετράμηνο': 2})
check('evening GEL Biology semester display 2/1', egel_bio and egel_bio.get('hours_display', {}).get('Β΄') == '2 / 1')
check('evening GEL Biology semester hours exact', egel_bio and egel_bio.get('period_hours', {}).get('Β΄') == {'Α΄ τετράμηνο': 2, 'Β΄ τετράμηνο': 1})
check('evening GEL has no second foreign language', not any(r.get('school') == 'esperino_gel' and str(r.get('subject', '')).startswith('2η Ξένη Γλώσσα') for r in rows))
check('evening GEL G has no English', not any(r.get('school') == 'esperino_gel' and r.get('subject') == 'Αγγλικά' and 'Γ΄' in (r.get('hours') or {}) for r in rows))
check('evening GEL G has no Physical Education', not any(r.get('school') == 'esperino_gel' and r.get('subject') == 'Φυσική Αγωγή' and 'Γ΄' in (r.get('hours') or {}) for r in rows))

# Εσπερινό ΕΠΑ.Λ. — the local school currently exposes French as the concrete
# course, while FEK 2122/2018 permits one of four languages. Keep all four legal
# branches internally and bind each to the correct specialty code.
tourism = row_by_id('esperino_epal.g.tourism.08')
check('evening EPAL tourism language row exists', tourism is not None)
check('evening EPAL tourism language 2 theory hours', tourism and tourism.get('hours_display', {}).get('Γ΄') == '2Θ')
check('evening EPAL tourism language is choice-dependent', tourism and tourism.get('assignment_link_status') == 'choice_dependent')
tourism_opts = option_map(tourism or {})
check('evening EPAL tourism language choices exact', set(tourism_opts) == {'Γαλλικά', 'Γερμανικά', 'Ισπανικά', 'Ιταλικά'})
for label, code in [('Γαλλικά', 'ΠΕ05'), ('Γερμανικά', 'ΠΕ07'), ('Ισπανικά', 'ΠΕ40'), ('Ιταλικά', 'ΠΕ34')]:
    check(f'evening EPAL tourism {label} -> {code}', tourism_opts.get(label, {}).get('codes') == [code])

# Every choice branch resolves into the broad official assignment row and code set.
for row in (gym, gel, tourism):
    if not row:
        continue
    for grade in row.get('hours', {}):
        target_subject = row.get('subject')
        assignment = next((a for a in assignments
                           if a.get('school') == row.get('school')
                           and (a.get('grade') in ('', grade) or grade in (a.get('grades') or []))
                           and a.get('subject') == target_subject), None)
        check(f'broad assignment exists: {row.get("course_id")} / {grade}', assignment is not None)
        if assignment:
            broad_codes = set(assignment.get('A') or []) | set(assignment.get('B') or []) | set(assignment.get('C') or [])
            for option in row.get('assignment_choice_options') or []:
                check(f'branch code is legal: {row.get("course_id")} / {option.get("label")}', set(option.get('codes') or []) <= broad_codes)

# Crosswalk metadata is internal only and must not leak into the browser payload.
check('public timetable strips assignment_link_status', all('assignment_link_status' not in r for r in public_rows))
check('public timetable strips assignment_choice_options', all('assignment_choice_options' not in r for r in public_rows))
check('public timetable strips assignment_subject_alias', all('assignment_subject_alias' not in r for r in public_rows))

# Public source cards must expose the corresponding legal sources, never myschool.
timetable_page = (ROOT / 'orologio-programma-mathimaton.php').read_text()
assignments_page = (ROOT / 'anatheseis-mathimaton.php').read_text()
for fek in ('2132/2026', '2106/2026', '2102/2026', '2636/2018', '2151/2026', '2122/2018'):
    check(f'timetable sources include FEK B {fek}', fek in timetable_page)
for fek in ('2583/2026', '1664/2018', '2625/2026', '2151/2026', '2122/2018'):
    check(f'assignment sources include FEK B {fek}', fek in assignments_page)
check('myschool not presented as public legal source timetable', 'myschool' not in timetable_page.lower())
check('myschool not presented as public legal source assignments', 'myschool' not in assignments_page.lower())

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
