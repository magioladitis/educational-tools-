from pathlib import Path

root = Path(__file__).resolve().parents[1]
page = (root / 'ypologismos-morion-anapliroti-psifiako-frontistirio.php').read_text(encoding='utf-8')
js = (root / 'includes/digital-tutoring-substitute-ui.js').read_text(encoding='utf-8')
catalog = (root / 'includes/tools-catalog.php').read_text(encoding='utf-8')
config = (root / 'includes/config.php').read_text(encoding='utf-8')
deadlines = (root / 'includes/deadlines.php').read_text(encoding='utf-8')

assert '126274/Δ7/24.09.2026' in page
assert 'Διεύθυνση Δευτεροβάθμιας Εκπαίδευσης Β΄ Αθήνας' in page
assert 'τουλάχιστον 5 έτη διδακτικής εμπειρίας' in page
assert '1,5 μονάδα ανά μήνα' in page
assert '05/10/2026, 17:00' in page
assert 'digital-tutoring-substitute-ui.js' in page
assert "renderDeadlineCard(array(" in page
assert "2026-10-05T17:00:00+03:00" in page
assert "Ψηφιακό Φροντιστήριο — προσωρινοί αναπληρωτές 2026–2027" in deadlines
assert "ypologismos-morion-anapliroti-psifiako-frontistirio.php" in deadlines
assert "course:'Φυσική'" in js and "branches:['ΠΕ04.01']" in js and 'seats:1' in js
assert "course:'Χημεία'" in js and "branches:['ΠΕ04.02','ΠΕ85']" in js and 'seats:4' in js
assert "course:'Ισπανικά'" in js and "branches:['ΠΕ40']" in js
assert 'Math.min(6, years * 2 + Math.floor(months / 4))' in js
assert 'Math.min(15, serviceMonths * 1.5)' in js
assert 'ypologismos-morion-anapliroti-psifiako-frontistirio.php' in catalog
assert "define('EDU_TOOLS_VERSION', '3.22.4');" in config
print('digital-tutoring-substitute-2026-contract: PASS')
