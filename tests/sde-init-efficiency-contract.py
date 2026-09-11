from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))

calc=(ROOT/'includes/sde-registry-calculations.js').read_text()
reg=(ROOT/'includes/sde-registry-ui.js').read_text()
det=(ROOT/'includes/sde-detachment-ui.js').read_text()

check('career counselling named max exists', 'const SDE_REGISTRY_CAREER_COUNSELLING_MAX = 7;' in calc)
check('career counselling calculation uses named max', 'SDE_REGISTRY_CAREER_COUNSELLING_MAX' in calc.split("role === 'career'",1)[1])
check('stale max-12 ambiguity comment removed', 'μέγιστο 12' not in calc)
check('registry has direct bootstrap via roleChanged', 'syncLanguageChoices();roleChanged();' in reg)
check('registry roleChanged owns initial calculate', 'function roleChanged()' in reg and 'updateComputerUI();\n  calculate();' in reg)
check('registry reset avoids second psychFpp calculate', "roleChanged();psychFppChanged();" not in reg and "show('psychMasterForFppWrap',false);roleChanged();" in reg)
check('detachment reset avoids duplicate calculate', 'specialtyChanged(); calculate();' not in det)
# bootstrap tail should have specialtyChanged only once and no immediate calculate afterwards
check('detachment bootstrap avoids duplicate calculate', '\n  specialtyChanged();\n  calculate();\n' not in det)

for n,ok in checks:
    print(('PASS' if ok else 'FAIL')+': '+n)
print(f"{sum(ok for _,ok in checks)}/{len(checks)} PASS")
if not all(ok for _,ok in checks): raise SystemExit(1)
