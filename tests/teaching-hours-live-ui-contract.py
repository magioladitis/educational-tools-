from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'ypologismos-didaktikou-orariou.php').read_text(encoding='utf-8')

checks = []
def check(name, ok):
    checks.append((name, bool(ok)))
    print(('PASS' if ok else 'FAIL') + ': ' + name)

check('redundant calculate button removed', 'calculateBtn' not in page and 'Υπολογισμός ωραρίου' not in page)
check('clear button retained', "'id' => 'resetBtn'" in page and "'label' => 'Καθαρισμός'" in page)
check('live input calculation retained', "el.addEventListener('input'" in page and 'calculate();' in page)
check('live change calculation retained', "el.addEventListener('change'" in page)
check('initial automatic calculation retained', page.count('calculate();') >= 3)

failed = [name for name, ok in checks if not ok]
print(f"\n{len(checks)-len(failed)}/{len(checks)} checks passed")
if failed:
    raise SystemExit(1)
