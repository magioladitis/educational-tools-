from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
home = (ROOT / 'ergaleia.php').read_text(encoding='utf-8')
catalog = (ROOT / 'includes' / 'tools-catalog.php').read_text(encoding='utf-8')
header = (ROOT / 'includes' / 'header.php').read_text(encoding='utf-8')
deadlines = (ROOT / 'prothesmies.php').read_text(encoding='utf-8')

checks = [
    ('home uses central tool catalogue', "includes/tools-catalog.php" in home),
    ('catalogue contains 32 tool entries', len(re.findall(r"'number'\s*=>\s*\d+", catalog)) == 32),
    ('catalogue defines five primary groups', len(re.findall(r"^\s{8}'[^']+'\s*=>\s*array\($", catalog, flags=re.M)) == 5),
    ('home renders category navigation', 'id="tool-categories"' in home and 'data-directory-filter=' in home),
    ('home renders grouped tool sections', 'class="tool-group"' in home and 'data-tool-group=' in home),
    ('home search remains available', 'id="toolSearch"' in home),
    ('shared header exposes all-tools navigation', 'ergaleia.php#tools-directory' in header),
    ('shared header exposes deadlines navigation', 'href="prothesmies.php"' in header),
    ('shared header exposes category menu', 'edu-tools-global-menu' in header),
    ('deadline page links back to tools', 'href="ergaleia.php"' in deadlines),
]

failed = False
for label, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + label)
    failed = failed or not ok

raise SystemExit(1 if failed else 0)
