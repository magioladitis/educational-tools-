from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
checks=[]
def check(ok,msg):
    checks.append((bool(ok),msg))

component=(ROOT/'includes/components/asep-three-month-service.php').read_text()
service=(ROOT/'includes/service-calculations.js').read_text()
readme=(ROOT/'README.md').read_text()

check('max="8"' in component and 'max="7"' in component, 'UI enforces historical 8/7 full-month maxima')
check('ιστορικά έως 8 πλήρεις μήνες' in component and 'ιστορικά έως 7 πλήρεις μήνες' in component, 'UI labels maxima as historical, not statutory')
check('δεν αποτελούν αυτοτελές ανώτατο όριο μοριοδότησης' in component, 'UI distinguishes operational duration maxima from statutory point caps')
check('threeMonth2020MaxMonths: 8' in service and 'threeMonth2021MaxMonths: 7' in service, 'engine clamps historical duration defensively')
check('threeMonthRegularMaxPoints: 10' in service and 'threeMonthDifficultMaxPoints: 20' in service, 'statutory 10/20 point caps remain')
check('v3.22.88' in readme and 'ιστορικά μέγιστα' in readme, 'changelog records correction')

for ok,msg in checks:
    print(('PASS' if ok else 'FAIL')+': '+msg)
raise SystemExit(0 if all(ok for ok,_ in checks) else 1)
