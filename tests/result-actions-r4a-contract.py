from pathlib import Path
import re, sys
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name, ok, detail=''):
    checks.append((name,bool(ok),detail))

for fname, func in [
    ('ypologismos-morion-apospasis-psifiako-frontistirio.php','calculate()'),
    ('ypologismos-morion-apospasis-sde.php','calculate()'),
]:
    text=(ROOT/fname).read_text(encoding='utf-8')
    # There must be no calculator action button that manually invokes calculate().
    action_calls=re.findall(r"calculatorActions\((.*?)\);", text, flags=re.S)
    action_blob='\n'.join(action_calls)
    check(fname+' no redundant calculate action', "onclick' => 'calculate()" not in action_blob)
    check(fname+' reset action remains', "onclick' => 'resetForm()" in action_blob)
    # Live calculation remains wired in source.
    check(fname+' remains live', len(re.findall(r'on(?:input|change)="[^"]*calculate\(\)', text)) >= 1)

on=(ROOT/'ypologismos-morion-onaseia.php').read_text(encoding='utf-8')
check('Onaseia explicit hybrid calculate label remains', "'Έλεγχος & υπολογισμός'" in on)
check('Onaseia stale DIMOS label absent', 'Υπολόγισε τα μόρια ΔΗΜ.Ω.Σ.' not in on)
check('Onaseia calculate action preserved', "onclick' => 'calculatePoints()" in on)

failed=[x for x in checks if not x[1]]
for name,ok,detail in checks:
    print(('PASS' if ok else 'FAIL')+': '+name+((' — '+detail) if detail else ''))
print(f"\n{len(checks)-len(failed)}/{len(checks)} PASS")
sys.exit(1 if failed else 0)
