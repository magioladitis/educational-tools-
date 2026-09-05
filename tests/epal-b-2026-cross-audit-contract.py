#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]
DATA = (ROOT / 'includes' / 'weekly-timetable-data.php').read_text(encoding='utf-8')
CROSS = (ROOT / 'includes' / 'teaching-timetable-crosswalk.php').read_text(encoding='utf-8')
ASSIGN = (ROOT / 'includes' / 'teaching-assignments-epal.php').read_text(encoding='utf-8')
PAGE = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
ASSIGN_PAGE = (ROOT / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')

checks = []
def check(name, cond):
    checks.append((name, bool(cond)))

php_code = (
    f"require {json.dumps(str(ROOT / 'includes' / 'weekly-timetable-data.php'))}; "
    "echo json_encode(array('schools'=>weeklyTimetableSchoolTypes(),'rows'=>weeklyTimetableRows()), JSON_UNESCAPED_UNICODE);"
)
payload = json.loads(subprocess.run(['php', '-r', php_code], capture_output=True, text=True, check=True).stdout)
rows = payload['rows']
by_id = {r['course_id']: r for r in rows}

# --- Sources / legal chain -------------------------------------------------
for token in ('2187/12-06-2018', '2636/05-07-2018', '3224/07-08-2018', '2151/16-04-2026'):
    check('timetable source ' + token, token in DATA)
for token in ('1664/15-05-2018', '1975/2025', '2625/2026'):
    check('assignment source ' + token, token in ASSIGN_PAGE or token in ASSIGN)
check('2026 Ethics decision source link', 'ΨΩ0Α46ΝΚΠΔ-Α4Υ' in ASSIGN_PAGE)

# --- General programme ----------------------------------------------------
def slot_sum(school, group, track=None, variant=None):
    total = 0.0
    slots = {}
    for row in rows:
        if row.get('school') != school or row.get('group') != group:
            continue
        if track is None:
            if row.get('track') is not None:
                continue
        elif row.get('track') not in (None, track):
            continue
        rv = row.get('variant')
        if variant is None:
            if rv is not None:
                continue
        elif rv not in (None, variant):
            continue
        h = row.get('hours', {}).get('Β΄')
        if h is None:
            continue
        sid = row.get('slot_id')
        if sid:
            slots[sid] = max(slots.get(sid, 0.0), float(h))
        else:
            total += float(h)
    return total + sum(slots.values())

check('day B general total 12', slot_sum('epal', 'Μαθήματα Γενικής Παιδείας') == 12)
check('evening B general total 10', slot_sum('esperino_epal', 'Μαθήματα Γενικής Παιδείας') == 10)
check('day B religion/ethics alternative slot',
      by_id['epal.b.general.thriskeftika'].get('slot_id') == 'epal.b.religion_ethics'
      and by_id['epal.b.general.ithiki'].get('slot_id') == 'epal.b.religion_ethics'
      and by_id['epal.b.general.thriskeftika'].get('mode') == 'alternative'
      and by_id['epal.b.general.ithiki'].get('mode') == 'alternative')
check('day B Ethics assignment PE01 / PE02 PE33 PE78',
      "'grade'=>'Β΄'" in ASSIGN and "'subject'=>'Ηθική'" in ASSIGN
      and "'A'=>array('ΠΕ01')" in ASSIGN and "'B'=>array('ΠΕ02','ΠΕ33','ΠΕ78')" in ASSIGN)
check('evening B has no religion/ethics row',
      all(r.get('subject') not in ('Θρησκευτικά', 'Ηθική')
          for r in rows if r.get('school') == 'esperino_epal' and 'Β΄' in r.get('hours', {})
          and r.get('group') == 'Μαθήματα Γενικής Παιδείας'))

# --- Nine sectors and totals ---------------------------------------------
for school, expected in (('epal', 23), ('esperino_epal', 20)):
    tracks = payload['schools'][school]['tracks_by_grade']['Β΄']
    check(school + ' B has 9 sectors', len(tracks) == 9)
    for code, label in tracks.items():
        group = 'Μαθήματα Τομέα · ' + label
        variants = payload['schools'][school].get('variants_by_grade_track', {}).get('Β΄', {}).get(code)
        if variants:
            for variant in variants:
                check(f'{school} B {code} / {variant} total {expected}', slot_sum(school, group, code, variant) == expected)
        else:
            check(f'{school} B {code} total {expected}', slot_sum(school, group, code) == expected)

# --- Applied Arts exact choice semantics ---------------------------------
for cid, hours in (('epal.b.arts.eidiko_ergastiriako', 5), ('eepal.b.arts.eidiko_ergastiriako', 4)):
    row = by_id[cid]
    labels = [o['label'] for o in row.get('assignment_choice_options', [])]
    check(cid + ' lab hours', row['hours']['Β΄'] == hours and row['hours_display']['Β΄'] == f'{hours}Ε')
    check(cid + ' four legal choices', labels == [
        'Φωτογραφία και Ηλεκτρονική Επεξεργασία Εικόνας',
        'Τεχνολογία Υφαντικών Υλών',
        'Εργαστήριο Χαρακτικής - Πλαστικής',
        'Εισαγωγή στις Ξύλινες Κατασκευές',
    ])
    check(cid + ' furniture restriction explicit',
          'μόνο όταν στη Γ΄ τάξη λειτουργεί η ειδικότητα «Επιπλοποιίας - Ξυλογλυπτική»' in row.get('note', ''))
    check(cid + ' choice-dependent crosswalk', row.get('assignment_link_status') == 'choice_dependent')

# --- Health: selectable legal timetable variants -------------------------
for school in ('epal', 'esperino_epal'):
    info = payload['schools'][school]
    variants = info.get('variants_by_grade_track', {}).get('Β΄', {}).get('health', {})
    check(school + ' health variant label', info.get('variant_label_by_grade_track', {}).get('Β΄', {}).get('health') == 'Περίπτωση διδασκαλίας')
    check(school + ' health exposes two variants', variants == {
        'two_specials': 'Διδάσκονται δύο Ειδικά Μαθήματα',
        'one_special': 'Δεν είναι δυνατή η διδασκαλία δεύτερου Ειδικού Μαθήματος',
    })

DAY_NORMAL = {
    'epal.b.health.anatomia': (3, '3Θ'),
    'epal.b.health.protes_voitheies': (2, '2Ε'),
    'epal.b.health.ygeia_diatrofi': (2, '2Θ'),
    'epal.b.health.diaprosopikes': (2, '2Θ'),
    'epal.b.health.ergasiako': (2, '2Ε'),
    'epal.b.health.agglika_tomea': (2, '2Θ'),
    'epal.b.health.eidiko_a': (5, '5 (Θ+Ε)'),
    'epal.b.health.eidiko_b': (5, '5 (Θ+Ε)'),
}
DAY_FALLBACK = {
    'epal.b.health.fallback.anatomia': (4, '4Θ'),
    'epal.b.health.fallback.protes_voitheies': (3, '3Ε'),
    'epal.b.health.fallback.ygeia_diatrofi': (2, '2Θ'),
    'epal.b.health.fallback.diaprosopikes': (2, '2Θ'),
    'epal.b.health.fallback.ergasiako': (3, '3Ε'),
    'epal.b.health.fallback.agglika_tomea': (2, '2Θ'),
    'epal.b.health.fallback.eidiko_a': (7, '7 (Θ+Ε)'),
}
EVENING_NORMAL = {
    'eepal.b.health.anatomia': (3, '3Θ'),
    'eepal.b.health.protes_voitheies': (2, '2Ε'),
    'eepal.b.health.ygeia_diatrofi': (2, '2Θ'),
    'eepal.b.health.diaprosopikes': (1, '1Θ'),
    'eepal.b.health.ergasiako': (2, '2Ε'),
    'eepal.b.health.agglika_tomea': (2, '2Θ'),
    'eepal.b.health.eidiko_a': (4, '4 (Θ+Ε)'),
    'eepal.b.health.eidiko_b': (4, '4 (Θ+Ε)'),
}
EVENING_FALLBACK = {
    'eepal.b.health.fallback.anatomia': (4, '4Θ'),
    'eepal.b.health.fallback.protes_voitheies': (3, '3Ε'),
    'eepal.b.health.fallback.ygeia_diatrofi': (2, '2Θ'),
    'eepal.b.health.fallback.diaprosopikes': (1, '1Θ'),
    'eepal.b.health.fallback.ergasiako': (3, '3Ε'),
    'eepal.b.health.fallback.agglika_tomea': (2, '2Θ'),
    'eepal.b.health.fallback.eidiko_a': (5, '5 (Θ+Ε)'),
}
for label, expected, variant in (
    ('day normal', DAY_NORMAL, 'two_specials'),
    ('day fallback', DAY_FALLBACK, 'one_special'),
    ('evening normal', EVENING_NORMAL, 'two_specials'),
    ('evening fallback', EVENING_FALLBACK, 'one_special'),
):
    for cid, (hours, display) in expected.items():
        row = by_id[cid]
        check(label + ' ' + cid + ' exact', row['hours']['Β΄'] == hours and row['hours_display']['Β΄'] == display and row.get('variant') == variant)

check('day fallback omits Special B', not any(r.get('course_id', '').startswith('epal.b.health.fallback.') and r.get('subject') == 'Ειδικό Μάθημα Β' for r in rows))
check('evening fallback omits Special B', not any(r.get('course_id', '').startswith('eepal.b.health.fallback.') and r.get('subject') == 'Ειδικό Μάθημα Β' for r in rows))
check('day fallback redistribution note exact', '+1Θ στην Ανατομία - Φυσιολογία Ι' in by_id['epal.b.health.fallback.eidiko_a'].get('note','') and '+2 ώρες (1Θ+1Ε)' in by_id['epal.b.health.fallback.eidiko_a'].get('note',''))
check('evening fallback redistribution note exact', '+1Θ στην Ανατομία - Φυσιολογία Ι' in by_id['eepal.b.health.fallback.eidiko_a'].get('note','') and '+1Ε στο Ειδικό Μάθημα Α' in by_id['eepal.b.health.fallback.eidiko_a'].get('note',''))

# Every real health Special slot, including fallback, must remain tied to the
# nine actual choices rather than inheriting one arbitrary assignment.
for cid in (
    'epal.b.health.eidiko_a', 'epal.b.health.eidiko_b', 'epal.b.health.fallback.eidiko_a',
    'eepal.b.health.eidiko_a', 'eepal.b.health.eidiko_b', 'eepal.b.health.fallback.eidiko_a',
):
    row = by_id[cid]
    check(cid + ' choice-dependent', row.get('assignment_link_status') == 'choice_dependent')
    check(cid + ' nine real choices', len(row.get('assignment_choice_options', [])) == 9)

# --- UI contract -----------------------------------------------------------
check('page renders variant selector', 'id="variantField"' in PAGE and 'id="variant"' in PAGE)
check('page filters rows by selected variant', 'row.variant && row.variant !== variant' in PAGE)
check('page names selected variant in summary', 'currentVariantLabel(school, grade, track)' in PAGE)

for name, ok in checks:
    print(('PASS: ' if ok else 'FAIL: ') + name)
failed = sum(not ok for _, ok in checks)
print(f'RESULT {len(checks)-failed} PASS / {failed} FAIL')
raise SystemExit(1 if failed else 0)
