#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

GENERIC = [
    'posa-paravola.php','ypologismos-morion.php','ypologismos-morion-1gt-2024.php',
    'ypologismos-morion-onaseia.php','ypologismos-morion-apospasis-dimos.php',
    'ypologismos-morion-apospasis.php','ypologismos-morion-1ea-2025.php',
    'ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php',
    'ypologismos-morion-4ea-2025.php','ypologismos-morion-5ea-2022.php',
    'ypologismos-morion-apospasis-psifiako-frontistirio.php',
    'ypologismos-morion-apospasis-sde.php','ypologismos-morion-apospasis-exoteriko.php',
    'ypologismos-morion-apospasis-evropaika-scholeia.php',
    'ypologismos-morion-diefthynton-ypodiefthynton-sde.php','ypologismos-morion-mitroo-sde.php',
    'ypologismos-morion-sivitanidios-saek.php','ypologismos-didaktikou-orariou.php',
    'ypologismos-morion-metathesis.php','ypologismos-morion-topothetisis-neodioriston.php',
    'anatheseis-mathimaton.php','orologio-programma-mathimaton.php'
]

helper=(ROOT/'includes/education-print.js').read_text(encoding='utf-8')
header=(ROOT/'includes/header.php').read_text(encoding='utf-8')
css=(ROOT/'assets/common.css').read_text(encoding='utf-8')

checks=[]
def ok(name, cond):
    checks.append((name, bool(cond)))

ok('shared helper loaded from header', 'includes/education-print.js' in header)
ok('generic print sheet css exists', '.edu-generic-print-sheet' in css and 'edu-generic-printing' in css)
ok('generic helper preserves dedicated print pages by registration', 'ypologismos-misthologikou-klimakiou.php' not in helper and 'ypologismos-didaktikon-anagkon.php' not in helper and 'metatropi-klimakas.php' not in helper)
for f in GENERIC:
    ok('registered: '+f, "'"+f+"'" in helper)

salary=(ROOT/'ypologismos-misthologikou-klimakiou.php').read_text(encoding='utf-8')
salary_ui=(ROOT/'includes/salary-ui.js').read_text(encoding='utf-8')
staff=(ROOT/'ypologismos-didaktikon-anagkon.php').read_text(encoding='utf-8')
grade=(ROOT/'metatropi-klimakas.php').read_text(encoding='utf-8')
ok('salary dedicated print preserved', 'id="payrollPrintSheet"' in salary and 'id' + "' => 'printBtn'" in salary and 'window.print()' in salary_ui)
ok('staffing dedicated print preserved', 'id="staffingPrintReport"' in staff and 'id="staffingPrintButton"' in staff and 'window.print()' in staff)
ok('grade converter print preserved', 'window.print()' in grade and 'Εκτύπωση' in grade)

main=(ROOT/'ypologismos-morion.php').read_text(encoding='utf-8')
ok('1GE/2GE redundant calculate button removed', 'class="calculate-primary" onclick="calculatePoints()"' not in main)
ok('1GE/2GE live calculation preserved', "addEventListener('input', liveCalculatePoints)" in main and "addEventListener('change', liveCalculatePoints)" in main)

php_files=list(ROOT.glob('*.php'))
ok('reset wording normalized', all('Μηδενισμός' not in p.read_text(encoding='utf-8') for p in php_files))

failed=[name for name,result in checks if not result]
for name,result in checks:
    print(('PASS' if result else 'FAIL') + ' - ' + name)
print('%d/%d checks passed' % (len(checks)-len(failed), len(checks)))
raise SystemExit(1 if failed else 0)
