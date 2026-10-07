from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
checks = []

def check(ok, msg):
    checks.append((bool(ok), msg))

registry = (ROOT / 'includes/legal-audit.php').read_text()
for page in [
    'ypologismos-didaktikon-anagkon.php',
    'adeies-monimon.php',
    'adeies-anapliroton.php',
    'adeies-sygkrisi.php',
    'ypologismos-misthologikou-klimakiou.php',
]:
    pattern = re.compile(
        re.escape("'" + page + "' => array(") + r".*?'status'\s*=>\s*'verified'.*?'last_verified'\s*=>\s*'2026-10-07'.*?'version'\s*=>\s*'3\.22\.89'",
        re.S,
    )
    check(bool(pattern.search(registry)), f'{page}: wave-2 full verification recorded in v3.22.89')

substitute = (ROOT / 'includes/substitute-leaves-data.php').read_text()
leave_sources = (ROOT / 'includes/leave-guide-sources.php').read_text()
check('8 για γονέα με 3+ τέκνα' in substitute, 'single-parent substitute leave no longer overstates a blanket minor-child condition in the duration label')
for phrase in ['τρίτα πρόσωπα', 'δικαστική απόφαση', 'έγγραφης συμφωνίας', 'ενηλικίωση', 'βαριάς ή χρόνιας ασθένειας ή αναπηρίας']:
    check(phrase in substitute, f'single-parent current clarification includes: {phrase}')
check("'labor_ministry_leaves'" in substitute and "'labor_inspection_single_parent'" in substitute, 'single-parent leave points to current official labour guidance')
check('ypergasias.gov.gr/ergasiakes-scheseis/atomikes-ergasiakes-sxeseis/adeies-ergazomenon/' in leave_sources, 'official Ministry of Labour leave overview is registered')
check('hli.gov.gr/ergasiakes-scheseis/nomothesia-ergasiakes-scheseis/adeies-ergasiakes-scheseis' in leave_sources, 'official Labour Inspection single-parent source is registered')

salary = (ROOT / 'ypologismos-misthologikou-klimakiou.php').read_text()
scale = (ROOT / 'includes/salary-scale-calculations.js').read_text()
net = (ROOT / 'includes/salary-net-calculations.js').read_text()
check('minfin.gov.gr/wp-content/uploads/2026/10/%CE%A8%CE%957%CE%A8%CE%97-%CE%9A%CE%A7%CE%A7.pdf' in salary, '2026 basic-pay source is the official Ministry of Finance PDF')
check('minfin.gov.gr/dimosionomiki-politiki/egkyklioi/' in salary, 'official Ministry of Finance circular registry is linked')
check('taxheaven.gr/circulars/52538/54692' not in salary and 'taxheaven.gr/circulars/50271/2-97758' not in salary and 'taxheaven.gr/circulars/23568' not in salary, 'core salary circular links no longer use secondary mirrors')
check('1232' in scale and '2294' in scale and '920' in scale and '1436' in scale, '2026 PE/YE basic salary endpoints remain encoded')
check('if (c === 1) return 70' in net and 'if (c === 2) return 120' in net and 'if (c === 3) return 170' in net and 'if (c === 4) return 220' in net, 'family allowance values remain encoded')
check('0.0165' in net and '0.0040' in net, 'health employee contribution remains 1.65% + 0.40%')
check('Έλεγχος νεότερων μισθολογικών εγκυκλίων' in salary and 'ν. 5313/2026' in salary, 'salary page records review of post-June 2026 circulars')

staffing = (ROOT / 'ypologismos-didaktikon-anagkon.php').read_text()
ethics = (ROOT / 'includes/ethics-class-formation.php').read_text()
check('10-09-2026 — Λειτουργία Γυμνασίων / ΓΕΛ 2026–2027' in staffing, 'staffing simulator links the current 2026-2027 operation instructions')
check('όπως ισχύει' in ethics and '102791/ΓΔ4/10-09-2024' in ethics, 'Ethics fallback citation is explicitly current-law aware')

config = (ROOT / 'includes/config.php').read_text()
sw = (ROOT / 'service-worker.js').read_text()
check("3.22.89" in config and "3.22.89" in sw, 'v3.22.89 version/cache bust synchronized')

for ok, msg in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + msg)
raise SystemExit(0 if all(ok for ok, _ in checks) else 1)
