from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
php = (ROOT / 'ypologismos-morion-apospasis.php').read_text(encoding='utf-8')
engine = (ROOT / 'includes' / 'detachment-calculations.js').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'detachment-ui.js').read_text(encoding='utf-8')
common = (ROOT / 'assets' / 'common.js').read_text(encoding='utf-8')

checks=[]
def check(name, cond): checks.append((name, bool(cond)))

check('no inline JS block remains', re.search(r'<script(?![^>]*\bsrc=)[^>]*>\s*\S', php, re.I|re.S) is None)
check('no inline action handlers remain', 'onclick' not in php.lower())
check('calculation engine loaded before UI', php.find('detachment-calculations.js') < php.find('detachment-ui.js'))
check('UI uses engine', 'EducationDetachment.calculate(readInput())' in ui)
check('calculation engine has no DOM dependency', 'document.' not in engine and 'getElementById' not in engine and 'querySelector' not in engine)
check('UI auto-initializes', "DOMContentLoaded', init" in ui and 'else init();' in ui)
check('canonical calculate button', "'id' => 'calculateBtn'" in php and "byId('calculateBtn')" in ui)
check('canonical reset button', "'id' => 'resetBtn'" in php and "byId('resetBtn')" in ui)
check('responsive disclosures exist', php.count("'data-mobile-collapsed' => 'true'") >= 5)
check('priority explanation disclosure', 'Πώς λειτουργεί η κατά προτεραιότητα απόσπαση;' in php)
check('service scale disclosure', 'Κλίμακα μοριοδότησης συνολικής υπηρεσίας' in php)
check('children disclosure', 'Πώς μοριοδοτούνται τα τέκνα;' in php)
check('health disclosure', 'Προϋποθέσεις μοριοδότησης σοβαρών λόγων υγείας' in php)
check('study disclosure', 'Προϋποθέσεις μοριοδότησης σπουδών' in php)
check('generic mobile disclosure support remains', '.edu-disclosure[data-mobile-collapsed="true"]' in common)
check('source card uses global default', 'sourceCardStart();' in php and 'sourceCardDisclosureStart' not in php)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+' | '+n)
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
if failed: raise SystemExit(1)
