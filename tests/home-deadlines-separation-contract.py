from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
home = (ROOT / 'ergaleia.php').read_text(encoding='utf-8')
data = (ROOT / 'includes' / 'home-deadlines.php').read_text(encoding='utf-8')

checks = [
    ('central page loads deadline data file', "require __DIR__ . '/includes/home-deadlines.php'" in home),
    ('central page renders loaded deadline config', 'renderDeadlineCard($homeDeadlineConfig);' in home),
    ('deadline data removed from central page', 'Νεοδιόριστοι 2026 — Ορκωμοσία & ανάληψη υπηρεσίας' not in home),
    ('deadline data file returns configuration', 'return array(' in data),
    ('deadline data contains central deadline title', 'Ενεργές & προσεχείς προθεσμίες' in data),
    ('deadline data does not own presentation renderer', 'function renderDeadlineCard' not in data),
]

failed = False
for label, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + label)
    failed = failed or not ok

raise SystemExit(1 if failed else 0)
