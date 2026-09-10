from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))

general=(ROOT/'ypologismos-morion.php').read_text()
general_ui=(ROOT/'includes/asep-points-ui.js').read_text()
apos=(ROOT/'ypologismos-morion-apospasis.php').read_text()
onas=(ROOT/'ypologismos-morion-onaseia.php').read_text()
apos_ui=(ROOT/'includes/detachment-ui.js').read_text()
onas_ui=(ROOT/'includes/onaseia-ui.js').read_text()

check('1GE/2GE redundant hybrid button removed', 'Έλεγχος & υπολογισμός</button>' not in general)
check('1GE/2GE no manual calculate action remains', 'class="calculate-primary" onclick="calculatePoints()"' not in general)
check('1GE/2GE live validation remains separate', 'function liveCalculatePoints()' in general_ui and 'AsepPeAcademic.validate' in general_ui)
check('Onaseia hybrid button uses explicit validation label', "'label' => 'Έλεγχος & υπολογισμός'" in onas)
check('Onaseia hybrid button still calls calculatePoints', "'id' => 'calculateBtn'" in onas and 'getElementById("calculateBtn").addEventListener("click", calculatePoints)' in onas_ui)
check('Onaseia live mode remains guarded/silent', 'function liveCalculatePoints()' in onas_ui and 'clearLiveResult(); return;' in onas_ui)
check('General secondment button describes check/view role', "'label' => 'Έλεγχος & προβολή αποτελέσματος'" in apos)
check('General secondment button still calls calculatePoints', "'id' => 'calculateBtn'" in apos and "calculateBtn.addEventListener('click', calculatePoints)" in apos_ui)
check('General secondment live calculation remains available', 'function liveCalculatePoints()' in apos_ui and 'isLiveCalculation = true;' in apos_ui)
check('Old generic hybrid label removed from target actions', all("'label' => 'Υπολογισμός μορίων'" not in x for x in (apos, onas)))

failed=[n for n,ok in checks if not ok]
for n,ok in checks:
    print(('PASS' if ok else 'FAIL')+': '+n)
print(f'\n{sum(ok for _,ok in checks)}/{len(checks)} PASS')
if failed: raise SystemExit(1)
