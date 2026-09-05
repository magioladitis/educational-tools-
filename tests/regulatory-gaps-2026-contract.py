#!/usr/bin/env python3
"""Contract for the confirmed 2026 timetable ↔ assignment regulatory gaps."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]
php = r'''
require "includes/weekly-timetable-data.php";
require "includes/teaching-assignments-data.php";
echo json_encode(array(
  "timetable" => weeklyTimetableRows(),
  "public" => weeklyTimetablePublicRows(),
  "assignments" => teachingAssignmentsData()
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
payload = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))
rows = payload['timetable']
assignments = payload['assignments']
checks = []

def check(name, condition):
    checks.append((name, bool(condition)))

expected_eneegyl = {
    'eneegyl.lykeio.c.agriculture.5', 'eneegyl.lykeio.c.agriculture.6',
    'eneegyl.lykeio.c.admin.5', 'eneegyl.lykeio.c.admin.6',
    'eneegyl.lykeio.c.building.5', 'eneegyl.lykeio.c.building.6',
    'eneegyl.lykeio.c.arts.4', 'eneegyl.lykeio.c.arts.5',
    'eneegyl.lykeio.c.electrical.4', 'eneegyl.lykeio.c.electrical.5',
    'eneegyl.lykeio.c.mechanical.5',
    'eneegyl.lykeio.c.it.4', 'eneegyl.lykeio.c.it.5',
    'eneegyl.lykeio.c.health.6', 'eneegyl.lykeio.c.health.7',
    'eneegyl.lykeio.c.health.fallback.5', 'eneegyl.lykeio.c.health.fallback.6',
}
expected_music_gym = {'mgym.theatro', 'mgym.istoria_texnis'}
expected_music_lyc = {
    'mgel.music.elliniki_paradosiaki',
    'mgel.c.choice.elliniki_paradosiaki',
    'mgel.c.choice.mousiko_keimeno',
    'mgel.c.choice.analysi_partitouras',
    'mgel.c.choice.choral',
    'mgel.c.choice.ixolipsia2',
}
expected_ids = expected_eneegyl | expected_music_gym | expected_music_lyc

gaps = [r for r in rows if r.get('assignment_link_status') == 'regulatory_gap']
check('25 regulatory-gap rows exact', len(gaps) == 25 and {r.get('course_id') for r in gaps} == expected_ids)
check('all gaps explicitly confirmed', all(r.get('assignment_gap_confirmed') is True for r in gaps))
check('all gaps have machine-readable kind', all(bool(r.get('assignment_gap_kind')) for r in gaps))
check('all gaps have timetable source', all(bool(r.get('assignment_gap_timetable_source')) for r in gaps))
check('all gaps have assignment source', all(bool(r.get('assignment_gap_assignment_source')) for r in gaps))
check('all gaps have inference guard', all(bool(r.get('assignment_gap_inference_guard')) for r in gaps))

instances = sum(len(r.get('hours') or {}) for r in gaps)
check('29 regulatory-gap grade instances', instances == 29)

en = [r for r in gaps if r.get('course_id') in expected_eneegyl]
mg = [r for r in gaps if r.get('course_id') in expected_music_gym]
ml = [r for r in gaps if r.get('course_id') in expected_music_lyc]
check('ENEEGYL 17 rows / 17 instances', len(en) == 17 and sum(len(r.get('hours') or {}) for r in en) == 17)
check('Music Gym 2 rows / 4 instances', len(mg) == 2 and sum(len(r.get('hours') or {}) for r in mg) == 4)
check('Music Lyceum 6 rows / 8 instances', len(ml) == 6 and sum(len(r.get('hours') or {}) for r in ml) == 8)

check('ENEEGYL source pair exact', all(
    r.get('assignment_gap_timetable_source') == 'ΦΕΚ Β΄ 2149/2026'
    and r.get('assignment_gap_assignment_source') == 'ΦΕΚ Β΄ 3216/2026'
    and r.get('assignment_gap_inference_guard') == 'no_cross_grade_or_specialty_borrow'
    for r in en
))
check('Music timetable source exact', all(r.get('assignment_gap_timetable_source') == 'ΦΕΚ Β΄ 2107/2026' for r in mg + ml))
check('Music Gym repealed-table guard', all(r.get('assignment_gap_inference_guard') == 'do_not_revive_repealed_2015_assignment' for r in mg))

# Same title in another grade/context is evidence to document, never authority to copy.
for row in en:
    grade = next(iter(row.get('hours') or {}), None)
    same_grade = any(
        a.get('school') == 'eneegyl_lykeio'
        and a.get('subject') == row.get('subject')
        and (a.get('grade') in ('', grade) or grade in (a.get('grades') or []))
        for a in assignments
    )
    check(f'ENEEGYL gap not silently assigned: {row.get("course_id")}', not same_grade)

mousiko_keimeno = next(r for r in ml if r.get('course_id') == 'mgel.c.choice.mousiko_keimeno')
check('Music text processing is cross-grade-only gap',
      mousiko_keimeno.get('assignment_gap_kind') == 'assignment_exists_only_other_grade'
      and mousiko_keimeno.get('assignment_gap_inference_guard') == 'no_cross_grade_borrow')
ixolipsia2 = next(r for r in ml if r.get('course_id') == 'mgel.c.choice.ixolipsia2')
check('Sound recording II is not mapped to I',
      ixolipsia2.get('assignment_gap_kind') == 'new_course_version_without_explicit_assignment'
      and ixolipsia2.get('assignment_gap_inference_guard') == 'do_not_map_version_ii_to_first_course')

# Internal-only safety metadata must never leak to browser data.
check('public payload strips every assignment_* key', all(
    not any(str(k).startswith('assignment_') for k in row)
    for row in payload['public']
))
check('internal payload retains gap metadata', any('assignment_gap_kind' in row for row in rows))

readme = (ROOT / 'README.md').read_text(encoding='utf-8')
check('README classification count current', '2.084/2.084' in readme)
check('README documents confirmed regulatory gaps', '29 επιβεβαιωμένα κανονιστικά `regulatory_gap`' in readme)

audit = ROOT / 'docs' / 'audits' / 'REGULATORY-GAPS-2026-AUDIT-2026-09-05.md'
check('dedicated regulatory-gap audit exists', audit.exists())

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
