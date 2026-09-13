#!/usr/bin/env python3
"""Contract for the first PWA layer: manifest metadata and page discovery."""
from pathlib import Path
import json
import re

ROOT = Path(__file__).resolve().parents[1]
manifest_path = ROOT / 'manifest.webmanifest'
failed = []

def check(name, ok):
    print(('PASS' if ok else 'FAIL') + ': ' + name)
    if not ok:
        failed.append(name)

check('manifest.webmanifest exists at app root', manifest_path.is_file())
try:
    manifest = json.loads(manifest_path.read_text(encoding='utf-8'))
    parsed = True
except Exception as exc:
    manifest = {}
    parsed = False
    print('manifest parse error:', exc)
check('manifest is valid JSON', parsed)

required = {
    'id': './',
    'name': 'Εργαλειοθήκη Εκπαιδευτικού',
    'short_name': 'Εργαλειοθήκη',
    'lang': 'el-GR',
    'start_url': './ergaleia.php',
    'scope': './',
    'display': 'standalone',
    'orientation': 'any',
    'background_color': '#f4f7fb',
    'theme_color': '#1f6feb',
}
for key, expected in required.items():
    check(f'manifest {key} is stable', manifest.get(key) == expected)

check('manifest description is non-empty', bool(str(manifest.get('description', '')).strip()))
check('manifest categories include education', 'education' in manifest.get('categories', []))
check('manifest does not prefer another native app', manifest.get('prefer_related_applications') is False)

# Icons deliberately arrive in the next PWA step. Do not publish broken icon URLs.
check('manifest does not reference missing icons yet', 'icons' not in manifest)

public_pages = []
unlinked = []
for path in sorted(ROOT.glob('*.php')):
    text = path.read_text(encoding='utf-8', errors='replace')
    if re.search(r'<head\b', text, re.I):
        public_pages.append(path.name)
        if not re.search(r'<link\b[^>]*\brel=["\']manifest["\'][^>]*\bhref=["\']manifest\.webmanifest["\']', text, re.I):
            unlinked.append(path.name)
check('public PHP page inventory is non-empty', bool(public_pages))
check('all public PHP pages discover the manifest', not unlinked)
if unlinked:
    print('unlinked pages:', unlinked)
index_text = (ROOT / 'index.html').read_text(encoding='utf-8', errors='replace')
check('root redirect page also discovers the manifest', 'rel=\"manifest\" href=\"manifest.webmanifest\"' in index_text)

print(f'RESULT {18-len(failed)} PASS / {len(failed)} FAIL; public_pages={len(public_pages)}')
raise SystemExit(1 if failed else 0)
