from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
checks = []

def check(ok, msg):
    checks.append((bool(ok), msg))

registry = (ROOT / 'includes/legal-audit.php').read_text()
priority_pages = [
    'ypologismos-didaktikou-orariou.php',
    'ypologismos-morion-metathesis.php',
    'ypologismos-morion-apospasis.php',
    'ypologismos-morion-topothetisis-neodioriston.php',
    'dikaioma-symmetoxis.php',
]
for page in priority_pages:
    pattern = re.compile(
        re.escape("'" + page + "' => array(") + r".*?'status'\s*=>\s*'verified'.*?'last_verified'\s*=>\s*'2026-10-07'.*?'version'\s*=>\s*'3\.22\.87'",
        re.S,
    )
    check(bool(pattern.search(registry)), f'{page}: verified 07/10/2026 in v3.22.87')

points_pattern = re.compile(re.escape("'ypologismos-morion.php' => array(") + r".*?'status'\s*=>\s*'verified'.*?'last_verified'\s*=>\s*'2026-10-07'.*?'version'\s*=>\s*'3\.22\.88'", re.S)
check(bool(points_pattern.search(registry)), 'ypologismos-morion.php: historical three-month cap correction recorded in v3.22.88')

three = (ROOT / 'includes/components/asep-three-month-service.php').read_text()
service = (ROOT / 'includes/service-calculations.js').read_text()
check('ιστορικά έως 8 πλήρεις μήνες' in three and 'ιστορικά έως 7 πλήρεις μήνες' in three, 'three-month UI explains the historical 8/7-month maxima')
check('max="8"' in three and 'max="7"' in three, 'three-month inputs defensively clamp to the historically possible months')
check('threeMonth2020MaxMonths: 8' in service and 'threeMonth2021MaxMonths: 7' in service, 'service engine restores historical 8/7-month maxima')
check('threeMonthRegularMaxPoints: 10' in service and 'threeMonthDifficultMaxPoints: 20' in service, 'statutory 10/20 point caps remain')

points = (ROOT / 'ypologismos-morion.php').read_text()
check('γονική μέριμνα και επιμέλεια' in points and 'κάτω των 25 ετών' in points, '1GE/2GE child eligibility is explained')

hours = (ROOT / 'ypologismos-didaktikou-orariou.php').read_text()
check('ΩΜΥΨ46ΜΤΛΗ-Ψ14' in hours and '6ΔΙ246ΝΚΠΔ-29Τ' in hours, 'teaching-hours source card uses official Diavgeia ADAs')
check('e-nomothesia.gr/kat-ekpaideuse/deuterobathmia-ekpaideuse/egkuklios-upaith-141076' not in hours, '141076 mirror removed')
check('edu.klimaka.gr/ekpaideytikoi/wrario-anatheseis/3687' not in hours, '132906 mirror removed')

placement = (ROOT / 'ypologismos-morion-topothetisis-neodioriston.php').read_text()
check('Προσωρινή τοποθέτηση Π.Ε.' in placement and 'Προσωρινή τοποθέτηση Δ.Ε.' in placement, 'new-appointee sources cover both P.E. and D.E. Mitos procedures')

detachment = (ROOT / 'ypologismos-morion-apospasis.php').read_text()
check('41297/Ε2/02-04-2026 (PDF)' in detachment, 'detachment source card includes direct official circular PDF')

eligibility = (ROOT / 'dikaioma-symmetoxis.php').read_text()
check('Κρίσιμος χρόνος:' in eligibility and 'ούτε κατά τη λήξη της προθεσμίας ούτε κατά τον διορισμό' in eligibility, 'eligibility source card explains critical-time rule')

config = (ROOT / 'includes/config.php').read_text()
sw = (ROOT / 'service-worker.js').read_text()
check("3.22.88" in config and "3.22.88" in sw, 'version and service-worker cache are synchronized')

for ok, msg in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + msg)
raise SystemExit(0 if all(ok for ok, _ in checks) else 1)
