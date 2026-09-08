from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
home = (ROOT / 'ergaleia.php').read_text(encoding='utf-8')
page = (ROOT / 'prothesmies.php').read_text(encoding='utf-8')
data = (ROOT / 'includes' / 'deadlines.php').read_text(encoding='utf-8')
legacy = (ROOT / 'includes' / 'home-deadlines.php').read_text(encoding='utf-8')

checks = [
    ('central tools page no longer loads deadline data', "includes/deadlines.php" not in home and "includes/home-deadlines.php" not in home),
    ('central tools page links to separate deadlines page', 'href="prothesmies.php"' in home),
    ('separate deadlines page loads deadline data', "require __DIR__ . '/includes/deadlines.php'" in page),
    ('separate deadlines page renders deadline config', 'renderDeadlineCard($deadlineConfig);' in page),
    ('deadline data remains outside presentation page', 'return array(' in data),
    ('deadline data contains central deadline title', 'Ενεργές & προσεχείς προθεσμίες' in data),
    ('deadline data does not own presentation renderer', 'function renderDeadlineCard' not in data),
    ('legacy home-deadlines include remains a compatibility alias', "return require __DIR__ . '/deadlines.php';" in legacy),
]

failed = False
for label, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + label)
    failed = failed or not ok

raise SystemExit(1 if failed else 0)
