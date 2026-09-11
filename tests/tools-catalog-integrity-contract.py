#!/usr/bin/env python3
from pathlib import Path
import json
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
CATALOG_PATH = ROOT / 'includes' / 'tools-catalog.php'
DIRECTORY_JS = (ROOT / 'assets' / 'tools-directory.js').read_text(encoding='utf-8')

php = "require " + json.dumps(str(CATALOG_PATH)) + "; echo json_encode($catalog = require " + json.dumps(str(CATALOG_PATH)) + ", JSON_UNESCAPED_UNICODE);"
# Avoid depending on source formatting: read the catalogue through PHP.
proc = subprocess.run(
    ['php', '-r', "echo json_encode(require " + json.dumps(str(CATALOG_PATH)) + ", JSON_UNESCAPED_UNICODE);"],
    capture_output=True, text=True, check=True
)
catalog = json.loads(proc.stdout)
tools = catalog.get('tools', [])
groups = catalog.get('groups', {})

rendered = subprocess.run(
    ['php', str(ROOT / 'ergaleia.php')], cwd=str(ROOT),
    capture_output=True, text=True, check=True
).stdout

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

numbers = [int(tool.get('number', 0)) for tool in tools]
hrefs = [str(tool.get('href', '')) for tool in tools]
rendered_cards = len(re.findall(r'class="tool-card"', rendered))

check('catalog has tools', len(tools) > 0)
check('tool numbers sequential', numbers == list(range(1, len(tools) + 1)))
check('tool numbers unique', len(numbers) == len(set(numbers)))
check('tool hrefs unique and non-empty', all(hrefs) and len(hrefs) == len(set(hrefs)))
check('all tool groups exist', all(tool.get('group') in groups for tool in tools))
check('rendered card count matches catalog', rendered_cards == len(tools))
check('hero tool count matches catalog', f'<span>{len(tools)} διαθέσιμα εργαλεία</span>' in rendered)
check('directory results count is dynamic', "'Εμφανίζονται ' + visible + ' εργαλεία.'" in DIRECTORY_JS)
check('weekly timetable is catalog tool 31', any(tool.get('number') == 31 and tool.get('href') == 'orologio-programma-mathimaton.php' for tool in tools))

failed = [name for name, passed in checks if not passed]
for name, passed in checks:
    print(('PASS' if passed else 'FAIL') + ': ' + name)
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
