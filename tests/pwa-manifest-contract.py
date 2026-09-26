#!/usr/bin/env python3
"""Contract for PWA manifest, browser icons, shared head metadata and SW setup."""
from pathlib import Path
import json
import re
import struct

ROOT = Path(__file__).resolve().parents[1]
manifest_path = ROOT / 'manifest.webmanifest'
head_partial_path = ROOT / 'includes' / 'head-pwa.php'
pwa_js_path = ROOT / 'assets' / 'pwa.js'
sw_path = ROOT / 'service-worker.js'
htaccess_path = ROOT / '.htaccess'
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


# --- manifest ---------------------------------------------------------------
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

# --- browser icons ----------------------------------------------------------
favicon_ico = ROOT / 'favicon.ico'
favicon_16 = ROOT / 'assets' / 'icons' / 'favicon-16.png'
favicon_32 = ROOT / 'assets' / 'icons' / 'favicon-32.png'
apple_touch = ROOT / 'assets' / 'icons' / 'apple-touch-icon.png'
check('classic favicon.ico exists and is non-empty', favicon_ico.is_file() and favicon_ico.stat().st_size > 0)
check('16x16 browser favicon exists with correct dimensions', png_dimensions(favicon_16) == (16, 16))
check('32x32 browser favicon exists with correct dimensions', png_dimensions(favicon_32) == (32, 32))
check('180x180 Apple touch icon exists with correct dimensions', png_dimensions(apple_touch) == (180, 180))

# --- shared <head> partial --------------------------------------------------
check('shared PWA head partial exists', head_partial_path.is_file())
head_partial = head_partial_path.read_text(encoding='utf-8', errors='replace') if head_partial_path.is_file() else ''
check('shared head discovers manifest', 'rel="manifest" href="manifest.webmanifest"' in head_partial)
check('shared head declares theme-color matching manifest', 'name="theme-color" content="#1f6feb"' in head_partial)
check('shared head exposes 32x32 browser favicon', 'assets/icons/favicon-32.png' in head_partial)
check('shared head exposes 16x16 browser favicon', 'assets/icons/favicon-16.png' in head_partial)
check('shared head exposes Apple touch icon', 'assets/icons/apple-touch-icon.png' in head_partial)
check('shared head loads central PWA bootstrap', "edu_asset_url('assets/pwa.js')" in head_partial)

public_pages = []
missing_partial = []
partial_outside_head = []
duplicated_pwa_markup = []
for path in sorted(ROOT.glob('*.php')):
    text = path.read_text(encoding='utf-8', errors='replace')
    if not re.search(r'<head\b', text, re.I):
        continue
    public_pages.append(path.name)
    marker = "includes/head-pwa.php"
    if marker not in text:
        missing_partial.append(path.name)
        continue
    head_start = text.lower().find('<head')
    head_end = text.lower().find('</head>')
    marker_pos = text.find(marker)
    if head_start < 0 or head_end < 0 or not (head_start < marker_pos < head_end):
        partial_outside_head.append(path.name)
    if re.search(r'<link\b[^>]*\brel=["\']manifest["\']', text, re.I) or 'assets/icons/favicon-32.png' in text or 'apple-touch-icon.png' in text:
        duplicated_pwa_markup.append(path.name)

check('public PHP page inventory is non-empty', bool(public_pages))
check('all public PHP pages include shared PWA head partial', not missing_partial)
check('shared PWA partial is included inside <head>', not partial_outside_head)
check('public PHP pages do not duplicate manifest/favicon markup', not duplicated_pwa_markup)
if missing_partial:
    print('pages missing shared PWA partial:', missing_partial)
if partial_outside_head:
    print('pages with PWA partial outside head:', partial_outside_head)
if duplicated_pwa_markup:
    print('pages duplicating PWA markup:', duplicated_pwa_markup)

# index.html cannot use a PHP include; keep the tiny direct discovery block there.
index_text = (ROOT / 'index.html').read_text(encoding='utf-8', errors='replace')
check('root redirect page also discovers the manifest', 'rel="manifest" href="manifest.webmanifest"' in index_text)
check('root redirect page exposes the browser favicon', 'assets/icons/favicon-32.png' in index_text)
check('root redirect page exposes the Apple touch icon', 'assets/icons/apple-touch-icon.png' in index_text)

# --- SW registration and conservative caching ------------------------------
check('central PWA bootstrap exists', pwa_js_path.is_file())
pwa_js = pwa_js_path.read_text(encoding='utf-8', errors='replace') if pwa_js_path.is_file() else ''
check('PWA bootstrap feature-detects service workers', "'serviceWorker' in navigator" in pwa_js)
check('PWA bootstrap only registers in secure/localhost context', "window.location.protocol !== 'https:'" in pwa_js and 'localhost' in pwa_js)
check('PWA bootstrap registers root service worker with app scope', "register('service-worker.js', { scope: './' })" in pwa_js)

check('root service-worker.js exists', sw_path.is_file())
sw = sw_path.read_text(encoding='utf-8', errors='replace') if sw_path.is_file() else ''
check('service worker has release-scoped static cache', bool(re.search(r"CACHE_NAME\s*=\s*CACHE_PREFIX\s*\+\s*'[^']+'", sw)))
check('service worker removes old app caches on activate', 'caches.keys()' in sw and 'caches.delete(key)' in sw)
check('service worker only handles GET same-origin requests', "request.method !== 'GET'" in sw and 'url.origin !== self.location.origin' in sw)
check('service worker cache strategy is limited to static asset extensions', 'STATIC_ASSET_RE' in sw and '.php' not in re.search(r'STATIC_ASSET_RE\s*=\s*([^;]+);', sw, re.S).group(1).lower() if re.search(r'STATIC_ASSET_RE\s*=\s*([^;]+);', sw, re.S) else False)
check('service worker uses network-first static asset refresh', 'return fetch(request)' in sw and 'cache.put(request, response.clone())' in sw and 'cache.match(request)' in sw)

# --- Apache MIME/cache config ----------------------------------------------
check('.htaccess exists for PWA MIME/cache directives', htaccess_path.is_file())
htaccess = htaccess_path.read_text(encoding='utf-8', errors='replace') if htaccess_path.is_file() else ''
check('.htaccess serves .webmanifest as application/manifest+json', re.search(r'AddType\s+application/manifest\+json\s+\.webmanifest', htaccess, re.I))
check('.htaccess serves JavaScript with JavaScript MIME type', re.search(r'AddType\s+application/javascript\s+\.js', htaccess, re.I))
check('.htaccess forces manifest/service worker revalidation', 'manifest\\.webmanifest|service-worker\\.js' in htaccess and 'no-cache' in htaccess)

# The service-worker cache version should track the release version in config.php.
config = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8', errors='replace')
version_match = re.search(r"EDU_TOOLS_VERSION'\s*,\s*'([^']+)'", config)
release_version = version_match.group(1) if version_match else ''
check('service-worker cache version matches EDU_TOOLS_VERSION', bool(release_version) and ("CACHE_NAME = CACHE_PREFIX + '%s'" % release_version) in sw)

failed = [name for name, passed in checks if not passed]
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL; public_pages={len(public_pages)}')
raise SystemExit(1 if failed else 0)
