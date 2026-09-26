#!/usr/bin/env python3
"""Contract for the PWA manifest, installability icons, and page discovery."""
from pathlib import Path
import json
import re
import struct

ROOT = Path(__file__).resolve().parents[1]
manifest_path = ROOT / 'manifest.webmanifest'
checks = []


def check(name, ok):
    ok = bool(ok)
    checks.append((name, ok))
    print(('PASS' if ok else 'FAIL') + ': ' + name)


def png_dimensions(path):
    """Read PNG dimensions from IHDR without third-party dependencies."""
    try:
        with path.open('rb') as fh:
            if fh.read(8) != b'\x89PNG\r\n\x1a\n':
                return None
            length = struct.unpack('>I', fh.read(4))[0]
            chunk_type = fh.read(4)
            if chunk_type != b'IHDR' or length < 8:
                return None
            width, height = struct.unpack('>II', fh.read(8))
            return width, height
    except OSError:
        return None


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

icons = manifest.get('icons', []) if isinstance(manifest.get('icons', []), list) else []

def icon_matches(size, purpose=None):
    matches = []
    for icon in icons:
        if not isinstance(icon, dict) or icon.get('sizes') != size or icon.get('type') != 'image/png':
            continue
        purposes = set(str(icon.get('purpose', 'any')).split())
        if purpose is None or purpose in purposes:
            matches.append(icon)
    return matches

icon_192 = icon_matches('192x192', 'any')
icon_512 = icon_matches('512x512', 'any')
maskable_512 = icon_matches('512x512', 'maskable')
check('manifest declares a 192x192 PNG app icon', bool(icon_192))
check('manifest declares a 512x512 PNG app icon', bool(icon_512))
check('manifest declares a 512x512 maskable PNG icon', bool(maskable_512))

for label, entries, expected_size in [
    ('192x192 app icon', icon_192, (192, 192)),
    ('512x512 app icon', icon_512, (512, 512)),
    ('512x512 maskable icon', maskable_512, (512, 512)),
]:
    src = entries[0].get('src', '') if entries else ''
    path = ROOT / src if src else None
    check(f'{label} file exists', bool(path and path.is_file()))
    check(f'{label} file has declared dimensions', bool(path and png_dimensions(path) == expected_size))

shortcuts = manifest.get('shortcuts', []) if isinstance(manifest.get('shortcuts', []), list) else []
check('manifest includes useful app shortcuts', len(shortcuts) >= 3)
shortcut_targets = []
for shortcut in shortcuts:
    if not isinstance(shortcut, dict):
        continue
    url = str(shortcut.get('url', ''))
    target = url[2:] if url.startswith('./') else url
    if target and not re.match(r'^[a-z]+://', target, re.I):
        shortcut_targets.append(target)
check('all local shortcut targets exist', bool(shortcut_targets) and all((ROOT / target).is_file() for target in shortcut_targets))

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
check('root redirect page also discovers the manifest', 'rel="manifest" href="manifest.webmanifest"' in index_text)

failed = [name for name, passed in checks if not passed]
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL; public_pages={len(public_pages)}')
raise SystemExit(1 if failed else 0)
