#!/usr/bin/env python3
"""Contract for the internal bridge between teaching assignments and timetable hours."""
from pathlib import Path
import json
import re
import subprocess
import unicodedata
from collections import Counter, defaultdict

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require "includes/weekly-timetable-data.php";
require "includes/teaching-assignments-data.php";
echo json_encode(array(
  "timetable" => weeklyTimetableRows(),
  "assignments" => teachingAssignmentsData(),
  "voc_specialties" => weeklyTimetableVocationalSpecialties(),
  "eneegyl_specialties" => weeklyTimetableEneegylSpecialties()
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
raw = subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True)
payload = json.loads(raw)
rows = payload['timetable']
assignments = payload['assignments']

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def norm(text):
    text = unicodedata.normalize('NFC', text or '').lower().strip()
    text = text.replace('–', '-').replace('—', '-').replace('−', '-').replace('&', ' και ')
    text = re.sub(r'\s*-\s*', '-', text)
    text = re.sub(r'\s+', ' ', text)
    return text

legacy_ids = {'evening_gymnasio', 'evening_gel', 'evening_epal', 'kallitexniko_lykeio', 'mousiko_lykeio'}
check('no legacy school ids in timetable', not (legacy_ids & {r.get('school') for r in rows}))
check('no legacy school ids in assignments', not (legacy_ids & {r.get('school') for r in assignments}))

canonical_overlap = {
    'gymnasio', 'esperino_gymnasio', 'gel', 'esperino_gel',
    'kallitexniko_gymnasio', 'kallitexniko_gel', 'mousiko_gymnasio', 'mousiko_gel',
    'epal', 'esperino_epal', 'pepal', 'eneegyl_gymnasio', 'eneegyl_lykeio'
}
assignment_schools = {r.get('school') for r in assignments}
timetable_schools = {r.get('school') for r in rows}
check('canonical school ids overlap', canonical_overlap <= assignment_schools and canonical_overlap <= timetable_schools)

# A' EPAL choice slots must expose the real courses, not an aggregate placeholder.
choice_titles = {
    'Αγωγή Υγείας',
    'Αρχές Γραμμικού και Αρχιτεκτονικού Σχεδίου',
    'Αρχές Ηλεκτρολογίας και Ηλεκτρονικής',
    'Αρχές Μηχανολογίας',
    'Αρχές Οικονομίας',
    'Βασικές Αρχές Σύνθεσης',
    'Γεωπονία και Αειφόρος Ανάπτυξη',
    'Ναυτιλιακές Γνώσεις',
}
for school, choice_set in [('epal', 'epal.a.choices'), ('esperino_epal', 'eepal.a.choices')]:
    choices = [r for r in rows if r.get('school') == school and r.get('choice_set_id') == choice_set]
    check(f'{school} A has 8 real choice courses', len(choices) == 8)
    check(f'{school} A choice titles exact', {r.get('subject') for r in choices} == choice_titles)
    check(f'{school} A choices are 2h / choose 3', all(r.get('hours', {}).get('Α΄') == 2 and r.get('choice_count') == 3 for r in choices))
    check(f'{school} A aggregate choice removed', not any(r.get('school') == school and r.get('subject') == 'Μαθήματα Επιλογής (3 από 8)' for r in rows))
    for title in choice_titles:
        check(f'{school} A choice assignment exists: {title}', any(
            a.get('school') == school and a.get('grade') == 'Α΄' and a.get('subject') == title
            for a in assignments
        ))

# Build specialty-label maps for context-aware component checks.
def flatten_specialties(hierarchy):
    out = {}
    for _track, items in hierarchy.items():
        out.update(items)
    return out

voc_labels = flatten_specialties(payload['voc_specialties'])
eneegyl_labels = flatten_specialties(payload['eneegyl_specialties'])

component_rows = [r for r in rows if r.get('assignment_components_by_grade')]
counts = Counter(r.get('school') for r in component_rows)
check('80 combined theory/lab rows bridged', len(component_rows) == 80)
for school in ('epal', 'esperino_epal', 'pepal', 'eneegyl_lykeio'):
    check(f'{school} has 20 split theory/lab bridges', counts[school] == 20)

for row in component_rows:
    school = row['school']
    specialty = row.get('specialty')
    labels = eneegyl_labels if school == 'eneegyl_lykeio' else voc_labels
    specialty_label = labels.get(specialty, '')
    check(f'component row specialty known: {row.get("course_id")}', bool(specialty_label))

    for grade, components in row['assignment_components_by_grade'].items():
        check(f'component count 2: {row.get("course_id")} / {grade}', len(components) == 2)
        check(f'component kinds theory+lab: {row.get("course_id")} / {grade}', {c.get('kind') for c in components} == {'theory', 'lab'})
        check(f'component hours sum: {row.get("course_id")} / {grade}', sum(int(c.get('hours', 0)) for c in components) == int(row.get('hours', {}).get(grade, -1)))
        for component in components:
            subject = component.get('subject')
            exists = any(
                a.get('school') == school
                and a.get('grade') == grade
                and a.get('subject') == subject
                and specialty_label in a.get('section', '')
                for a in assignments
            )
            check(f'assignment component exists: {row.get("course_id")} / {subject}', exists)

# Every declared safe alias must point to a real assignment row in its school/grade context.
alias_rows = [r for r in rows if r.get('assignment_subject_alias')]
check('safe aliases present', len(alias_rows) >= 7)
for row in alias_rows:
    alias = row['assignment_subject_alias']
    for grade in row.get('hours', {}):
        specialty_label = ''
        if row.get('specialty'):
            labels = eneegyl_labels if row.get('school') == 'eneegyl_lykeio' else voc_labels
            specialty_label = labels.get(row.get('specialty'), '')
        exists = any(
            a.get('school') == row.get('school')
            and (a.get('grade') in ('', grade) or grade in a.get('grades', []))
            and a.get('subject') == alias
            and (not specialty_label or specialty_label in a.get('section', ''))
            for a in assignments
        )
        check(f'safe alias resolves: {row.get("course_id")} / {grade}', exists)

# P.P.EPAL A orientation blocks are intentionally NOT force-mapped: assignment data
# is finer-grained than timetable hours, so inventing per-topic hours would be unsafe.
pepal_a_blocks = [r for r in rows if r.get('school') == 'pepal' and r.get('group') == 'Μαθήματα Επαγγελματικής Κατεύθυνσης Προσανατολιστικού Χαρακτήρα']
check('PEPAL A orientation blocks remain six', len(pepal_a_blocks) == 6)
check('PEPAL A blocks remain structurally unresolved', all('assignment_subject_alias' not in r and 'assignment_components_by_grade' not in r for r in pepal_a_blocks))

# Non-regression metric: normalized public titles that already map directly to assignments.
assignment_index = defaultdict(set)
for a in assignments:
    assignment_index[(a.get('school'), a.get('grade', ''))].add(norm(a.get('subject')))
    for g in a.get('grades', []) or []:
        assignment_index[(a.get('school'), g)].add(norm(a.get('subject')))

total_instances = 0
direct_matches = 0
resolved_instances = 0
status_instances = Counter()
for row in rows:
    for grade in row.get('hours', {}):
        total_instances += 1
        status = row.get('assignment_link_status')
        if status:
            status_instances[status] += 1
        candidates = assignment_index[(row.get('school'), grade)] | assignment_index[(row.get('school'), '')]
        direct = norm(row.get('subject')) in candidates
        if direct:
            direct_matches += 1
            resolved_instances += 1
            continue
        if row.get('assignment_subject_alias') and norm(row['assignment_subject_alias']) in candidates:
            resolved_instances += 1
            continue
        if grade in (row.get('assignment_components_by_grade') or {}):
            resolved_instances += 1

check('direct normalized linkage non-regression', direct_matches >= 1700)
check('direct+audited bridge linkage non-regression', resolved_instances >= 1820)
# Choice-dependent slots are intentional structural bridges, not failed aliases.
choice_by_school = Counter()
choice_rows = []
for row in rows:
    if row.get('assignment_link_status') != 'choice_dependent':
        continue
    for _grade in row.get('hours', {}):
        choice_by_school[row.get('school')] += 1
    choice_rows.append(row)

check('ENEEGYL choice-dependent instances classified', choice_by_school['eneegyl_lykeio'] == 7)
check('EPAL choice-dependent instances classified', choice_by_school['epal'] == 3)
check('evening EPAL choice-dependent instances classified', choice_by_school['esperino_epal'] == 3)
check('PEPAL choice-dependent instances classified', choice_by_school['pepal'] == 3)
check('all choice-dependent instances classified', status_instances['choice_dependent'] == 16)
check('ENEEGYL regulatory-gap instances classified', status_instances['regulatory_gap'] == 17)

# Every vocational choice target must resolve to a real assignment row in the same
# school/grade context. This protects the bridge against title drift in either dataset.
for row in choice_rows:
    if row.get('school') not in {'epal', 'esperino_epal', 'pepal'}:
        continue
    options = row.get('assignment_choice_options') or []
    check(f'vocational choice options present: {row.get("course_id")}', bool(options))
    for grade in row.get('hours', {}):
        for option in options:
            targets = []
            if option.get('subject'):
                targets.append(option['subject'])
            targets.extend(option.get('components') or [])
            check(f'vocational choice target list nonempty: {row.get("course_id")} / {option.get("label")}', bool(targets))
            for target in targets:
                exists = any(
                    a.get('school') == row.get('school')
                    and (a.get('grade') in ('', grade) or grade in (a.get('grades') or []))
                    and a.get('subject') == target
                    for a in assignments
                )
                check(f'vocational choice target resolves: {row.get("course_id")} / {target}', exists)

classified_instances = resolved_instances + status_instances['choice_dependent'] + status_instances['regulatory_gap']
check('classified linkage non-regression', classified_instances >= 1989)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'AUDIT direct={direct_matches}/{total_instances} resolved={resolved_instances}/{total_instances} choice={status_instances["choice_dependent"]} regulatory_gap={status_instances["regulatory_gap"]} classified={classified_instances}/{total_instances} component_rows={len(component_rows)} aliases={len(alias_rows)}')
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
