#!/usr/bin/env python3
from pathlib import Path
import sys
root=Path(__file__).resolve().parents[1]
page=(root/'ypologismos-misthologikou-klimakiou.php').read_text()
config=(root/'includes/config.php').read_text()
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
check('Greek display fallback map', "PE: 'ΠΕ'" in page and "TE: 'ΤΕ'" in page and "DE: 'ΔΕ'" in page and "YE: 'ΥΕ'" in page)
check('category sidebar uses mapped label', "categoryLabels[result.categoryCode]" in page)
check('removed Από τα παραπάνω labels', 'Από τα παραπάνω' not in page)
check('2016-2017 group heading', 'Υπηρεσία στη διετία 01-01-2016 έως 31-12-2017' in page)
check('freeze years concise label', 'Έτη υπηρεσίας στη διετία' in page)
check('freeze months concise label', 'Επιπλέον μήνες στη διετία' in page)
check('freeze fields nested together', '<div class="field-grid">' in page and page.index('id="suspendedYears"') < page.index('id="suspendedMonths"'))
check('central cache version present', "define('EDU_TOOLS_VERSION'" in config and "3.20.76" in config)
check('2026 basic salary result row', 'basicSalaryResult' in page and 'Βασικός μισθός (μικτά) από 01/04/2026' in page)
check('2026 salary circular source', '54692 ΕΞ 2026/03-04-2026' in page and 'ΨΕ7ΨΗ-ΚΧΧ' in page)
check('salary disclaimer distinguishes basic from total pay', 'βασικό μισθό του Μ.Κ.' in page and 'οικογενειακή παροχή' in page and 'επίδομα θέσης ευθύνης' in page and 'Δεν προστίθενται προσωπική διαφορά' in page)
failed=[n for n,v in checks if not v]
for n,v in checks: print(('PASS' if v else 'FAIL')+': '+n)
print(f'SALARY-SCALE R18 UI: {len(checks)-len(failed)}/{len(checks)} PASS')
sys.exit(1 if failed else 0)
