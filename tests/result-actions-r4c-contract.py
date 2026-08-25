from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))

general=(ROOT/'ypologismos-morion.php').read_text()
apos=(ROOT/'ypologismos-morion-apospasis.php').read_text()
onas=(ROOT/'ypologismos-morion-onaseia.php').read_text()

check('1GE/2GE hybrid button uses explicit validation label', 'Έλεγχος & υπολογισμός</button>' in general)
check('1GE/2GE hybrid button still calls calculatePoints', 'onclick="calculatePoints()">Έλεγχος & υπολογισμός' in general)
check('1GE/2GE live validation remains separate', 'function liveCalculatePoints()' in general and 'AsepPeAcademic.validate' in general)
check('Onaseia hybrid button uses explicit validation label', "'label' => 'Έλεγχος & υπολογισμός'" in onas)
check('Onaseia hybrid button still calls calculatePoints', "'onclick' => 'calculatePoints()'" in onas)
check('Onaseia live mode remains guarded/silent', 'function liveCalculatePoints()' in onas and 'clearLiveResult(); return;' in onas)
check('General secondment button describes check/view role', "'label' => 'Έλεγχος & προβολή αποτελέσματος'" in apos)
check('General secondment button still calls calculatePoints', "'onclick' => 'calculatePoints()'" in apos)
check('General secondment live calculation remains available', 'function liveCalculatePoints()' in apos and 'isLiveCalculation = true;' in apos)
check('Old generic hybrid label removed from target actions', all("'label' => 'Υπολογισμός μορίων'" not in x for x in (apos, onas)))

failed=[n for n,ok in checks if not ok]
for n,ok in checks:
    print(('PASS' if ok else 'FAIL')+': '+n)
print(f'\n{sum(ok for _,ok in checks)}/{len(checks)} PASS')
if failed: raise SystemExit(1)
