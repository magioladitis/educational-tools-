#!/usr/bin/env python3
"""Contract for Πρότυπα Εκκλησιαστικά Σχολεία assignment integration."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]
PAGE = (ROOT / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')
PAGE_UI = (ROOT / 'includes' / 'teaching-assignments-ui.js').read_text(encoding='utf-8')
NEEDS = (ROOT / 'ypologismos-didaktikon-anagkon.php').read_text(encoding='utf-8')
DATA_TEXT = (ROOT / 'includes' / 'teaching-assignments-data.php').read_text(encoding='utf-8')

php = r'''
require "includes/teaching-assignments-data.php";
echo json_encode(teachingAssignmentsData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
rows = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))

GYM = 'protypo_ekklisiastiko_gymnasio'
LYK = 'protypo_ekklisiastiko_lykeio'
pes = [r for r in rows if r.get('school') in {GYM, LYK}]

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def one(school, subject, grade=None):
    candidates = [r for r in pes if r.get('school') == school and r.get('subject') == subject]
    if grade is not None:
        candidates = [r for r in candidates if r.get('grade') == grade or grade in (r.get('grades') or [])]
    return candidates[0] if len(candidates) == 1 else None

check('PES assignment rows total 75', len(pes) == 75)
check('PES Gym assignment rows 24', sum(r.get('school') == GYM for r in pes) == 24)
check('PES Lyceum assignment rows 51', sum(r.get('school') == LYK for r in pes) == 51)
check('PES qualification keys present in source', 'pes_byzantine_music_diploma' in DATA_TEXT and 'pes_iconography_eligibility' in DATA_TEXT)

# Common curriculum inheritance stays priority-preserving.
gym_math = one(GYM, 'Μαθηματικά', 'Α΄')
check('PES Gym Mathematics inherits PE03 A assignment', gym_math and gym_math.get('A') == ['ΠΕ03'])
gym_english = one(GYM, 'Αγγλικά', 'Α΄')
check('PES Gym English inherits PE06 A assignment', gym_english and gym_english.get('A') == ['ΠΕ06'])
lyk_alg = one(LYK, 'Άλγεβρα', 'Α΄')
lyk_geo = one(LYK, 'Γεωμετρία', 'Α΄')
check('PES Lyceum Algebra A inherits PE03', lyk_alg and lyk_alg.get('A') == ['ΠΕ03'])
check('PES Lyceum Geometry A inherits PE03', lyk_geo and lyk_geo.get('A') == ['ΠΕ03'])

# Six theological specialization courses are explicit PES special provisions for PE01.
theological = [
    (GYM, 'Α΄', 'Θέματα από την Αγία Γραφή'),
    (GYM, 'Β΄', 'Λειτουργική Ζωή της Εκκλησίας'),
    (GYM, 'Γ΄', 'Εκκλησιαστική Ιστορία και Πατέρες και Θεολόγοι της Εκκλησίας'),
    (LYK, 'Α΄', 'Στοιχεία Λειτουργικής και Τελετουργικής'),
    (LYK, 'Β΄', 'Θέματα Δογματικής Θεολογίας'),
    (LYK, 'Γ΄', 'Θέματα Χριστιανικής Ηθικής και Ποιμαντικής Θεολογίας'),
]
for school, grade, subject in theological:
    row = one(school, subject, grade)
    check(f'theological PES row exists: {subject}', row is not None)
    check(f'theological PES row special PE01: {subject}', row and row.get('special_codes') == ['ΠΕ01'])
    check(f'theological PES row not general A/B/C: {subject}', row and not any(row.get(k) for k in ('A','B','C')))

# Byzantine Music: explicit priority note + mandatory diploma for both codes.
for school in (GYM, LYK):
    row = one(school, 'Βυζαντινή Μουσική')
    check(f'Byzantine Music row exists: {school}', row is not None)
    check(f'Byzantine Music codes exact: {school}', row and row.get('special_codes') == ['ΠΕ79.01','ΤΕ16'])
    check(f'Byzantine Music all grades: {school}', row and row.get('grades') == ['Α΄','Β΄','Γ΄'])
    check(f'Byzantine Music qualification key: {school}', row and row.get('qualification_key') == 'pes_byzantine_music_diploma')
    check(f'Byzantine Music TE16 only if PE79.01 absent: {school}', row and 'Μόνο ελλείψει ΠΕ79.01' in (row.get('special_notes', {}).get('ΤΕ16') or ''))
    check(f'Byzantine Music diploma visible: {school}', row and 'Δίπλωμα Βυζαντινής Μουσικής' in (row.get('special_note') or ''))

# Iconography: code alone is never sufficient.
for school in (GYM, LYK):
    row = one(school, 'Εικονογραφία')
    check(f'Iconography row exists: {school}', row is not None)
    check(f'Iconography candidate codes PE01/PE08: {school}', row and row.get('special_codes') == ['ΠΕ01','ΠΕ08'])
    check(f'Iconography all grades: {school}', row and row.get('grades') == ['Α΄','Β΄','Γ΄'])
    check(f'Iconography qualification key: {school}', row and row.get('qualification_key') == 'pes_iconography_eligibility')
    check(f'Iconography special legal conditions visible: {school}', row and '71346/Θ2/10-06-2020' in (row.get('special_note') or '') and '4404/Θ2/16-01-2023' in (row.get('special_note') or ''))
    check(f'Iconography simple code explicitly insufficient: {school}', row and 'δεν αρκεί' in (row.get('special_note') or ''))

# Foreign-language bridge: no invented specialty for Russian/Arabic/Turkish.
gym_lang = one(GYM, 'Γαλλικά / Γερμανικά / Ρωσικά / Αραβικά / Τουρκικά')
check('PES Gym extended language row exact PE05/PE07 only', gym_lang and gym_lang.get('A') == ['ΠΕ05','ΠΕ07'])
check('PES Gym extended language row explicitly avoids inference', gym_lang and 'Δεν αποδίδεται κλάδος' in (gym_lang.get('note') or ''))
for grade in ('Α΄','Β΄','Γ΄'):
    row = next((r for r in pes if r.get('school') == LYK and r.get('grade') == grade and r.get('subject') == 'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)'), None)
    check(f'PES Lyceum {grade} foreign language branch exact', row and row.get('A') == ['ΠΕ06','ΠΕ05','ΠΕ07'])

# Public assignments UI exposes both new structures and their filters.
check('PES assignments badge visible', 'Πρότυπα Εκκλησιαστικά' in PAGE)
check('PES assignments school group visible', 'Πρότυπα Εκκλησιαστικά Σχολεία' in PAGE)
check('PES Gym checkbox visible', 'id="schoolEcclesiasticalGym"' in PAGE)
check('PES Lyceum checkbox visible', 'id="schoolEcclesiasticalLykeio"' in PAGE)
check('PES Gym data filter wired', "row.school === 'protypo_ekklisiastiko_gymnasio'" in PAGE_UI)
check('PES Lyceum data filter wired', "row.school === 'protypo_ekklisiastiko_lykeio'" in PAGE_UI)
check('PES special legal source text visible', '71346/Θ2/2020' in PAGE and '4404/Θ2/2023' in PAGE and 'Δίπλωμα Βυζαντινής Μουσικής' in PAGE)

# Didactic-needs calculator gets placeholders only: disabled options, no enabled option/case/profile.
check('PES Gym didactic-needs placeholder disabled', '<option value="protypo_ekklisiastiko_gymnasio" disabled>' in NEEDS)
check('PES Lyceum didactic-needs placeholder disabled', '<option value="protypo_ekklisiastiko_lykeio" disabled>' in NEEDS)
check('exactly two PES references in didactic-needs file', NEEDS.count('protypo_ekklisiastiko_') == 2)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
