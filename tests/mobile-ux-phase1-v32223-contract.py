#!/usr/bin/env python3
from pathlib import Path
import re
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
common_js = (ROOT / 'assets' / 'common.js').read_text(encoding='utf-8')
common_css = (ROOT / 'assets' / 'common.css').read_text(encoding='utf-8')
config = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
worker = (ROOT / 'service-worker.js').read_text(encoding='utf-8')
eligibility_js = (ROOT / 'includes' / 'eligibility-guide-ui.js').read_text(encoding='utf-8')
saek_js = (ROOT / 'includes' / 'saek-deputy-eligibility-ui.js').read_text(encoding='utf-8')

checks = []
def check(label, condition):
    checks.append((label, bool(condition)))

version_match = re.search(r"define\('EDU_TOOLS_VERSION', '([0-9.]+)'\)", config)
worker_match = re.search(r"CACHE_NAME = CACHE_PREFIX \+ '([0-9.]+)'", worker)
check('release remains at or beyond mobile UX phase 1', bool(version_match) and tuple(map(int, version_match.group(1).split('.'))) >= (3,22,23))
check('service-worker cache matches release', bool(version_match and worker_match) and version_match.group(1) == worker_match.group(1))
check('common JS syntax', subprocess.run(['node', '--check', str(ROOT / 'assets' / 'common.js')], capture_output=True).returncode == 0)
check('shared mobile UX class enabled progressively', "document.body.classList.add('edu-ui', 'edu-mobile-ux-enabled')" in common_js)
check('sticky action is explicit opt-in', "querySelectorAll('[data-edu-primary-action=\"true\"]')" in common_js)
check('sticky action uses safe mobile CSS', '.edu-mobile-sticky-action' in common_css and 'env(safe-area-inset-bottom)' in common_css)
check('sticky action avoids field focus/keyboard obstruction', "active.matches('input, select, textarea')" in common_js)
check('result navigation installs edit-data return action', 'edu-mobile-edit-inputs' in common_js and 'Επεξεργασία στοιχείων ↑' in common_js)
check('tool hero supporting copy uses mobile info disclosure', 'installMobileHeroInfoDisclosures(document)' in common_js and 'edu-mobile-hero-info-button' in common_css)
check('directory hero participates in global compact treatment', 'not(.edu-tools-directory)' not in common_css)
check('shared validation API exists', 'reportMissingFields: reportMissingFields' in common_js and 'edu-validation-summary' in common_css)
check('eligibility guide adopts shared validation API', 'window.EduToolsUI.reportMissingFields' in eligibility_js and 'aria-invalid' in eligibility_js)
check('SAEK guide adopts shared validation API', 'global.EduToolsUI.reportMissingFields' in saek_js and 'aria-invalid' in saek_js)

primary_pages = {
    'dikaioma-symmetoxis.php': '#result',
    'dikaioma-ypodiefthynti-saek.php': '#result',
    'dikaiologitika-tekna-anapiria.php': '#result',
    'dikaiologitika-titlon-spoudon.php': '#result',
    'odigos-enstasis.php': '#result',
    'paidagogiki-eparkeia.php': '#result',
}
for page, target in primary_pages.items():
    text = (ROOT / page).read_text(encoding='utf-8')
    check(f'{page}: primary action opt-in', 'data-edu-primary-action="true"' in text)
    check(f'{page}: result target wired', f'data-edu-result-target="{target}"' in text)

# Heavy workflows must remain outside the opt-in sticky system.
for page in ['ypologismos-didaktikon-anagkon.php', 'ergaleia.php', 'kena-sxoleion.php']:
    text = (ROOT / page).read_text(encoding='utf-8')
    check(f'{page}: no accidental sticky primary action', 'data-edu-primary-action="true"' not in text)

failed = False
for label, ok in checks:
    print(('PASS' if ok else 'FAIL') + ' | ' + label)
    failed = failed or not ok
print(f'RESULT {sum(ok for _, ok in checks)} PASS / {sum(not ok for _, ok in checks)} FAIL')
raise SystemExit(1 if failed else 0)
