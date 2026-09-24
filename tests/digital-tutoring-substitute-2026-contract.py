from pathlib import Path

root = Path(__file__).resolve().parents[1]
page = (root / 'ypologismos-morion-anapliroti-psifiako-frontistirio.php').read_text(encoding='utf-8')
js = (root / 'includes/digital-tutoring-substitute-ui.js').read_text(encoding='utf-8')
catalog = (root / 'includes/tools-catalog.php').read_text(encoding='utf-8')
config = (root / 'includes/config.php').read_text(encoding='utf-8')

assert '126274/Δ7/24.09.2026' in page
assert 'Διεύθυνση Δευτεροβάθμιας Εκπαίδευσης Β΄ Αθήνας' in page
assert 'τουλάχιστον 5 έτη διδακτικής εμπειρίας' in page
assert '1,5 μονάδα ανά μήνα' in page
assert '05/10/2026, ώρα 17:00' in page
assert 'digital-tutoring-substitute-ui.js' in page
assert "course:'Φυσική'" in js and "branches:['ΠΕ04.01']" in js and 'seats:1' in js
assert "course:'Χημεία'" in js and "branches:['ΠΕ04.02','ΠΕ85']" in js and 'seats:4' in js
assert "course:'Ισπανικά'" in js and "branches:['ΠΕ40']" in js
assert 'Math.min(6, years * 2 + Math.floor(months / 4))' in js
assert 'Math.min(15, serviceMonths * 1.5)' in js
assert 'ypologismos-morion-anapliroti-psifiako-frontistirio.php' in catalog
assert "define('EDU_TOOLS_VERSION', '3.22.3');" in config
print('digital-tutoring-substitute-2026-contract: PASS')
