#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
PAGE = (ROOT / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')
EPAL_G = (ROOT / 'includes' / 'teaching-assignments-epal-g.php').read_text(encoding='utf-8')
PEPAL_G = (ROOT / 'includes' / 'teaching-assignments-pepal-g.php').read_text(encoding='utf-8')
ENEEGYL = (ROOT / 'includes' / 'teaching-assignments-eneegyl.php').read_text(encoding='utf-8')
ENEEGYL_D = (ROOT / 'includes' / 'teaching-assignments-eneegyl-d.php').read_text(encoding='utf-8')
EEEEEK = (ROOT / 'includes' / 'teaching-assignments-eeeek.php').read_text(encoding='utf-8')
LEGAL = (ROOT / 'includes' / 'legal-sources.php').read_text(encoding='utf-8')
CONFIG = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
SW = (ROOT / 'service-worker.js').read_text(encoding='utf-8')

checks=[]
def check(name, cond): checks.append((name, bool(cond)))

check('EPAL G childcare Music Movement includes TE16 in A assignment',
      "'subject'=>'Μουσικοκινητική Αγωγή','A'=>array('ΠΕ79.01','ΠΕ87.09','ΤΕ01.30','ΤΕ16')" in EPAL_G)
check('PEPAL G childcare Music Movement includes TE16 in A assignment',
      "'subject'=>'Μουσικοκινητική Αγωγή','A'=>array('ΠΕ79.01','ΠΕ87.09','ΤΕ01.30','ΤΕ16')" in PEPAL_G)
check('ENEEGYL Gym music includes TE16 in A assignment',
      "'subject'=>'Μουσική/Θεατρική Αγωγή','A'=>array('ΠΕ79.01','ΠΕ91','ΤΕ16')" in ENEEGYL)
check('ENEEGYL Lyceum D childcare Music Movement includes TE16 in A assignment',
      "'subject'=>'Μουσικοκινητική Αγωγή','A'=>array('ΠΕ79.01','ΠΕ87.09','ΤΕ01.30','ΤΕ16')" in ENEEGYL_D)
check('EEEEΚ Music is PE79.01 + TE16 A assignment',
      "'subject'=>'Μουσική','A'=>array('ΠΕ79.01','ΤΕ16')" in EEEEEK)

for needle in (
    'Υ.Α. Φ22/119408/Δ4/11-09-2026',
    'Υ.Α. Φ9/119412/Δ4/11-09-2026',
    'Υ.Α. 121386/Δ3/16-09-2026',
    'Υ.Α. 121314/Δ3/16-09-2026',
    'ΦΕΚ Β΄ 5710/22-09-2026',
    'ΦΕΚ Β΄ 5733/22-09-2026',
):
    check('public source prose: '+needle, needle in PAGE)

check('FEK 5710 direct National Printing Office record', 'https://search.et.gr/el/fek/?fekId=805702' in LEGAL)
check('FEK 5733 direct National Printing Office record', 'https://search.et.gr/el/fek/?fekId=805734' in LEGAL)
check('version bumped to 3.22.84', "EDU_TOOLS_VERSION', '3.22.84'" in CONFIG)
check('service worker cache bumped to 3.22.84', "CACHE_PREFIX + '3.22.84'" in SW)

failed=[n for n,ok in checks if not ok]
for n,ok in checks:
    print(('PASS' if ok else 'FAIL')+': '+n)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
