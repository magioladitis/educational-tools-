from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
production = [
    p for p in ROOT.rglob('*')
    if p.suffix in {'.php', '.js'}
    and 'tests' not in p.parts
    and 'docs' not in p.parts
]

checks = []

def check(name, ok, detail=''):
    checks.append((name, bool(ok), detail))

# Greek-facing UI must never initialize score displays with an English decimal point.
# Match a standalone 0.00 token only. A raw substring search also matches perfectly
# valid numeric constants such as 0.0040 (0.40%) and tolerances such as 0.001.
dot_zero_hits = []
standalone_dot_zero = re.compile(r'(?<![\d.])0\.00(?!\d)')
check('decimal scanner ignores numeric coefficient 0.0040', not standalone_dot_zero.search('rate: 0.0040'))
check('decimal scanner ignores numeric tolerance 0.001', not standalone_dot_zero.search('diff > 0.001'))
check('decimal scanner catches standalone UI 0.00', bool(standalone_dot_zero.search('value = \"0.00\"')))
for path in production:
    for lineno, line in enumerate(path.read_text(encoding='utf-8').splitlines(), 1):
        if standalone_dot_zero.search(line):
            dot_zero_hits.append(f'{path.relative_to(ROOT)}:{lineno}')
check('no production UI literal 0.00', not dot_zero_hits, ', '.join(dot_zero_hits))

# A bare toFixed(2) is an easy way to reintroduce a decimal point in displayed scores.
bare_tofixed = []
for path in production:
    for lineno, line in enumerate(path.read_text(encoding='utf-8').splitlines(), 1):
        if '.toFixed(2)' not in line:
            continue
        normalized = line.replace(' ', '')
        if ".replace('.',',')" not in normalized and '.replace(".",",")' not in normalized:
            bare_tofixed.append(f'{path.relative_to(ROOT)}:{lineno}')
check('no bare toFixed(2) in production', not bare_tofixed, ', '.join(bare_tofixed))

expected = {
    'ypologismos-morion-apospasis-dimos.php': [
        '0,00 / 29',
        "toLocaleString('el-GR', {minimumFractionDigits:2, maximumFractionDigits:2})",
    ],
    'ypologismos-morion-3ea-2025.php': [
        "calculatorScoreHeader(array('value_id' => 'grandTotal', 'value_html' => '0,00'",
        "toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2})",
    ],
}
for filename, needles in expected.items():
    text = (ROOT / filename).read_text(encoding='utf-8')
    ui_modules = {
        'ypologismos-morion-apospasis-dimos.php': ROOT / 'includes' / 'dimos-detachment-ui.js',
        'ypologismos-morion-3ea-2025.php': ROOT / 'includes' / 'asep-3ea-ui.js',
    }
    if filename in ui_modules:
        text += '\n' + ui_modules[filename].read_text(encoding='utf-8')
    for needle in needles:
        check(f'{filename} contains {needle}', needle in text)

failed = [item for item in checks if not item[1]]
for name, ok, detail in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name + (f' [{detail}]' if detail else ''))
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
