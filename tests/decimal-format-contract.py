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
dot_zero_hits = []
for path in production:
    text = path.read_text(encoding='utf-8')
    if '0.00' in text:
        dot_zero_hits.append(str(path.relative_to(ROOT)))
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
    for needle in needles:
        check(f'{filename} contains {needle}', needle in text)

failed = [item for item in checks if not item[1]]
for name, ok, detail in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name + (f' [{detail}]' if detail else ''))
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
