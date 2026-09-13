#!/usr/bin/env python3
"""Guard the production PHP -> external JavaScript separation."""
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
php_files = [p for p in ROOT.rglob('*.php') if 'tests' not in p.parts]
script_re = re.compile(r'<script\b([^>]*)>(.*?)</script\s*>', re.I | re.S)
handler_re = re.compile(
    r'\s(on(?:click|change|input|submit|load|error|focus|blur|keydown|keyup|keypress|mouseover|mouseout|touchstart|touchend|pointerdown|pointerup))\s*=\s*["\']',
    re.I,
)
js_api_needles = (
    'addEventListener(', 'querySelector(', 'querySelectorAll(', 'DOMContentLoaded',
    'window.onload', 'document.getElementById(',
)

inline_exec = []
handlers = []
js_urls = []
js_api_refs = []
data_scripts = []

for path in php_files:
    text = path.read_text(encoding='utf-8', errors='replace')
    for match in script_re.finditer(text):
        attrs = match.group(1)
        body = match.group(2)
        if re.search(r'\bsrc\s*=', attrs, re.I):
            continue
        if re.search(r'\btype\s*=\s*["\']application/json["\']', attrs, re.I):
            data_scripts.append(path.relative_to(ROOT).as_posix())
            continue
        if body.strip() or not re.search(r'\bsrc\s*=', attrs, re.I):
            inline_exec.append(path.relative_to(ROOT).as_posix())
    if handler_re.search(text):
        handlers.append(path.relative_to(ROOT).as_posix())
    if re.search(r'javascript\s*:', text, re.I):
        js_urls.append(path.relative_to(ROOT).as_posix())
    if any(needle in text for needle in js_api_needles):
        js_api_refs.append(path.relative_to(ROOT).as_posix())

checks = [
    ('production PHP inventory is non-empty', bool(php_files)),
    ('no executable inline script blocks', not inline_exec),
    ('no inline DOM event handlers', not handlers),
    ('no javascript: URLs', not js_urls),
    ('no DOM/JS API implementation left in PHP', not js_api_refs),
    ('runtime JSON script is the only allowed inline script payload', data_scripts == ['ypologismos-didaktikon-anagkon.php']),
]

failed = []
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
    if not ok:
        failed.append(name)
if inline_exec:
    print('inline_exec:', sorted(set(inline_exec)))
if handlers:
    print('handlers:', sorted(set(handlers)))
if js_urls:
    print('javascript_urls:', sorted(set(js_urls)))
if js_api_refs:
    print('js_api_refs:', sorted(set(js_api_refs)))
if data_scripts:
    print('application_json_scripts:', data_scripts)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL; production_php={len(php_files)}')
raise SystemExit(1 if failed else 0)
