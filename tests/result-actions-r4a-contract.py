from pathlib import Path
import re, sys
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name, ok, detail=''):
    checks.append((name,bool(ok),detail))

# Digital tutoring is fully externally wired; SDE still uses its legacy inline wiring.
digital_name='ypologismos-morion-apospasis-psifiako-frontistirio.php'
digital=(ROOT/digital_name).read_text(encoding='utf-8')
digital_ui=(ROOT/'includes/digital-tutoring-detachment-ui.js').read_text(encoding='utf-8')
check(digital_name+' no redundant calculate action', "onclick' => 'calculate()" not in digital)
check(digital_name+' reset action remains', "'id' => 'resetBtn'" in digital and "addEventListener('click', resetForm)" in digital_ui)
check(digital_name+' remains live', "addEventListener('input'" in digital_ui and "addEventListener('change'" in digital_ui and 'calculate();' in digital_ui)

sde_name='ypologismos-morion-apospasis-sde.php'
sde=(ROOT/sde_name).read_text(encoding='utf-8')
action_calls=re.findall(r"calculatorActions\((.*?)\);", sde, flags=re.S)
action_blob='\n'.join(action_calls)
check(sde_name+' no redundant calculate action', "onclick' => 'calculate()" not in action_blob)
check(sde_name+' reset action remains', "onclick' => 'resetForm()" in action_blob)
check(sde_name+' remains live', len(re.findall(r'on(?:input|change)="[^"]*calculate\(\)', sde)) >= 1)

on=(ROOT/'ypologismos-morion-onaseia.php').read_text(encoding='utf-8')
on_ui=(ROOT/'includes/onaseia-ui.js').read_text(encoding='utf-8')
check('Onaseia explicit hybrid calculate label remains', "'Έλεγχος & υπολογισμός'" in on)
check('Onaseia stale DIMOS label absent', 'Υπολόγισε τα μόρια ΔΗΜ.Ω.Σ.' not in on)
check('Onaseia calculate action preserved', "'id' => 'calculateBtn'" in on and 'getElementById("calculateBtn").addEventListener("click", calculatePoints)' in on_ui)

failed=[x for x in checks if not x[1]]
for name,ok,detail in checks:
    print(('PASS' if ok else 'FAIL')+': '+name+((' — '+detail) if detail else ''))
print(f"\n{len(checks)-len(failed)}/{len(checks)} PASS")
sys.exit(1 if failed else 0)
