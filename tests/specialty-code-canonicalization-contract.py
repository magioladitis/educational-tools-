from pathlib import Path
import re, subprocess, json

ROOT = Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))
    print(('PASS' if cond else 'FAIL') + ': ' + name)

core=(ROOT/'includes/education-core.js').read_text(encoding='utf-8')
teacher=(ROOT/'includes/teacher-specialties.php').read_text(encoding='utf-8')
abroad_php=(ROOT/'ypologismos-morion-apospasis-exoteriko.php').read_text(encoding='utf-8')
abroad_ui=(ROOT/'includes/abroad-ui.js').read_text(encoding='utf-8')
ea2_php=(ROOT/'ypologismos-morion-2ea-2025.php').read_text(encoding='utf-8')
ea2_ui=(ROOT/'includes/asep-2ea-ui.js').read_text(encoding='utf-8')
eep=(ROOT/'includes/eep-eligibility-calculations.js').read_text(encoding='utf-8')
sde_reg_php=(ROOT/'ypologismos-morion-mitroo-sde.php').read_text(encoding='utf-8')
sde_reg_ui=(ROOT/'includes/sde-registry-ui.js').read_text(encoding='utf-8')
sde_det_php=(ROOT/'ypologismos-morion-apospasis-sde.php').read_text(encoding='utf-8')
sde_det_ui=(ROOT/'includes/sde-detachment-ui.js').read_text(encoding='utf-8')
sde_calc=(ROOT/'includes/sde-calculations.js').read_text(encoding='utf-8')

check('shared JS canonicalizer maps mixed PE/TE/DE prefixes', all(x in core for x in ["PΕ|ΠE|ΠΕ", "TΕ|ΤE|ΤΕ", "DΕ|ΔE|ΔΕ"]))
check('shared PHP canonicalizer accepts mixed PE/TE/DE prefixes', all(x in teacher for x in ["PΕ|ΠE|ΠΕ", "TΕ|ΤE|ΤΕ", "DΕ|ΔE|ΔΕ"]))

# Actual specialty code literals are Greek in the four migrated families.
for label, text in [
    ('abroad PHP',abroad_php),('abroad UI',abroad_ui),('2EA PHP',ea2_php),('2EA UI',ea2_ui),
    ('EEP eligibility',eep),('SDE registry PHP',sde_reg_php),('SDE registry UI',sde_reg_ui),
    ('SDE detachment PHP',sde_det_php),('SDE detachment UI',sde_det_ui),('SDE calculations',sde_calc)
]:
    latin = re.findall(r"(?<![A-Za-zΑ-Ωα-ω])(?:PE|TE|DE)\d+(?:\.\d+)?", text)
    check(label + ' has no Latin actual specialty-code literals', not latin)

check('abroad destinations use Greek canonical specs', 'specs:["ΠΕ70","ΠΕ02"]' in abroad_ui and 'specs:["PE70"' not in abroad_ui)
check('abroad controller normalizes specialty before matching', 'specialty = normalizeSpecialtyCode(specialty);' in abroad_ui and "specialty: $('specialty').value" not in abroad_ui)
check('2EA controller normalizes specialty before branch rules', 'function specialtyCode()' in ea2_ui and "b==='ΠΕ25'" in ea2_ui and "b==='PE25'" not in ea2_ui)
check('EEP requirements keyed canonically', "'ΠΕ25': Object.freeze" in eep and 'PE25: Object.freeze' not in eep)
check('SDE registry PE86 UI rule is canonical Greek', "specialtyCode()==='ΠΕ86'" in sde_reg_ui)
check('SDE detachment specialty UI rules are canonical Greek', "sp === 'ΠΕ86'" in sde_det_ui and "sp.startsWith('ΠΕ04.')" in sde_det_ui)
check('SDE exported specialty map uses Greek keys', "'ΠΕ01': 'ΠΕ01 Θεολόγων'" in sde_calc and "'PE01':" not in sde_calc)

# Rendered specialty option values must also be canonical Greek, not merely their visible labels.
def specialty_values(page):
    proc=subprocess.run(['php',page],cwd=ROOT,capture_output=True,text=True)
    check(page+' renders',proc.returncode==0)
    if proc.returncode!=0: return []
    m=re.search(r'<select id="specialty"[^>]*>(.*?)</select>',proc.stdout,re.S)
    if not m: return []
    return re.findall(r'<option\s+value="([^"]*)"',m.group(1))

for page in ['ypologismos-morion-apospasis-exoteriko.php','ypologismos-morion-2ea-2025.php','ypologismos-morion-mitroo-sde.php','ypologismos-morion-apospasis-sde.php']:
    vals=[v for v in specialty_values(page) if v and v!='OTHER']
    check(page+' exposes Greek specialty values', bool(vals) and all(not re.match(r'^(?:PE|TE|DE)\d',v) for v in vals))
    check(page+' visible/internal specialty values begin ΠΕ/ΤΕ where applicable', all(v.startswith(('ΠΕ','ΤΕ','ΔΕ')) for v in vals))

# PHP mixed-script boundary regression.
php_code=r'''require "includes/teacher-specialties.php"; echo json_encode(array(
 teacherSpecialtyCanonicalCode("PE86"),teacherSpecialtyCanonicalCode("PΕ86"),teacherSpecialtyCanonicalCode("ΠE86"),teacherSpecialtyCanonicalCode("ΠΕ86"),
 teacherSpecialtyCanonicalCode("TE16"),teacherSpecialtyCanonicalCode("TΕ16"),teacherSpecialtyCanonicalCode("DE01.05")
), JSON_UNESCAPED_UNICODE);'''
proc=subprocess.run(['php','-r',php_code],cwd=ROOT,capture_output=True,text=True)
expected=['ΠΕ86','ΠΕ86','ΠΕ86','ΠΕ86','ΤΕ16','ΤΕ16','ΔΕ01.05']
try: got=json.loads(proc.stdout)
except Exception: got=[]
check('PHP mixed-script normalization regression',proc.returncode==0 and got==expected)

# Residual Latin-looking tokens are explicitly limited to non-specialty internal enums.
allowed={
 'ypologismos-didaktikou-orariou.php': {'TE01','DE01_ARCH','DE01_TECH'},
 'includes/personnel-csv-import.js': {'DE01_ARCH','DE01_TECH'},
 'includes/personnel-workload.php': {'TE01','DE01_ARCH','DE01_TECH'},
 'includes/teaching-hours-calculations.js': {'TE01','DE01_ARCH','DE01_TECH'},
 'includes/asep-te-academic.js': {'TE16','TE01','TE02'},
}
unexpected=[]
for p in ROOT.rglob('*'):
    if not p.is_file() or 'tests' in p.parts or p.suffix not in {'.php','.js'}: continue
    rel=str(p.relative_to(ROOT))
    text=p.read_text(encoding='utf-8',errors='ignore')
    tokens=set(re.findall(r"['\"]((?:PE|TE|DE)\d+(?:\.\d+)?(?:_[A-Z]+)?)['\"]",text))
    for token in tokens:
        if token not in allowed.get(rel,set()): unexpected.append(rel+':'+token)
check('Latin-looking production tokens limited to documented internal enums',not unexpected)
if unexpected: print('UNEXPECTED:',unexpected)

failed=[n for n,ok in checks if not ok]
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
