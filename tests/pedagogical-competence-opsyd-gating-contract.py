from pathlib import Path
root=Path(__file__).resolve().parents[1]
php=(root/'paidagogiki-eparkeia.php').read_text(encoding='utf-8')
js=(root/'includes/pedagogical-competence-ui.js').read_text(encoding='utf-8')
checks=[
 ('OPSYD question starts hidden','id="opsydQuestion" class="question ped-opsyd-question hidden"' in php),
 ('visibility helper exists','function refreshOpsydVisibility()' in js),
 ('visibility depends on positive routes','aggregate.positives.length > 0' in js),
 ('OPSYD reset when hidden','select.value = ""' in js),
 ('result only renders OPSYD for positive aggregate','if (aggregate.positives.length) html += opsydBlock(opsyd);' in js),
 ('negative routes rendered','Διαδρομές που δεν θεμελιώνουν Π.Δ.Ε. με τα δηλωμένα στοιχεία' in js),
]
for name,ok in checks:
 print(('PASS' if ok else 'FAIL')+': '+name)
if not all(ok for _,ok in checks): raise SystemExit(1)
print(f'{len(checks)}/{len(checks)} OPSYD gating checks passed')
