#!/usr/bin/env python3
from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
common_js = (ROOT / 'assets' / 'common.js').read_text(encoding='utf-8')
common_css = (ROOT / 'assets' / 'common.css').read_text(encoding='utf-8')
config = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
worker = (ROOT / 'service-worker.js').read_text(encoding='utf-8')
catalog = (ROOT / 'includes' / 'tools-catalog.php').read_text(encoding='utf-8')
deadlines = (ROOT / 'includes' / 'deadlines.php').read_text(encoding='utf-8')
onaseia_page = (ROOT / 'ypologismos-morion-onaseia.php').read_text(encoding='utf-8')
detachment_page = (ROOT / 'ypologismos-morion-apospasis.php').read_text(encoding='utf-8')
disability_page = (ROOT / 'dikaiologitika-tekna-anapiria.php').read_text(encoding='utf-8')
objection_page = (ROOT / 'odigos-enstasis.php').read_text(encoding='utf-8')

checks = []
def check(label, condition):
    checks.append((label, bool(condition)))

version_match = re.search(r"define\('EDU_TOOLS_VERSION', '([0-9.]+)'\)", config)
worker_match = re.search(r"CACHE_NAME = CACHE_PREFIX \+ '([0-9.]+)'", worker)
check('release remains at or beyond mobile UX phase 2', bool(version_match) and tuple(map(int, version_match.group(1).split('.'))) >= (3,22,24))
check('service-worker cache matches release', bool(version_match and worker_match) and version_match.group(1) == worker_match.group(1))
check('common JS syntax', subprocess.run(['node', '--check', str(ROOT / 'assets' / 'common.js')], capture_output=True).returncode == 0)

check('mobile table helper installed', 'installMobileTableAssist(document)' in common_js)
check('wide-table swipe hint exists', 'Σύρε οριζόντια για περισσότερες στήλες' in common_js and '.edu-mobile-table-hint' in common_css)
check('stacked-table opt-in supported', "data-edu-mobile-table') === 'stack'" in common_js and '.edu-mobile-table-stack' in common_css)
check('detachment reference table opts into stacked mobile mode', 'data-edu-mobile-table="stack"' in detachment_page)
check('table overflow region is keyboard accessible', "wrapper.setAttribute('tabindex', '0')" in common_js and "wrapper.setAttribute('role', 'region')" in common_js)

check('numeric inputmode inference exists', "input.setAttribute('inputmode', inputUsesDecimalKeyboard(input) ? 'decimal' : 'numeric')" in common_js)
check('soft keyboard enterkeyhint exists', "input.setAttribute('enterkeyhint'" in common_js)
check('decimal comma assistance uses pending valid number strategy', 'data-edu-pending-decimal' in common_js and "input.value = current + '.' + event.data" in common_js)

check('short-lived local draft persistence exists', 'DRAFT_TTL_MS = 12 * 60 * 60 * 1000' in common_js and 'eduToolsDraftV1:' in common_js)
check('draft persists locally only through localStorage', 'window.localStorage' in common_js and 'fetch(' not in common_js)
check('vacancies and staffing are excluded from drafts', "document.body.matches('.edu-vacancies, .edu-page-staffing-simulator')" in common_js)
check('sensitive page can explicitly disable drafts', 'data-edu-draft-persist="off"' in disability_page)
check('e-paravolo code explicitly excluded from drafts', 'id="paravoloCode" type="text" data-edu-draft="off"' in objection_page)
check('clear/reset removes saved draft', "label.indexOf('καθαρισ')" in common_js and "label.indexOf('μηδεν')" in common_js and 'storage.removeItem(key)' in common_js)

check('catalog uses requested ΔΗΜ.Ω.Σ. title', "'title' => 'Μόρια Αναπληρωτή στα ΔΗΜ.Ω.Σ.'" in catalog)
check('old short tool title removed', 'Μόρια Αναπληρωτή στα Ωνάσεια' not in catalog)
check('onaseia page hero uses requested short title', "'title' => 'Μόρια Αναπληρωτή στα ΔΗΜ.Ω.Σ.'" in onaseia_page)
check('related short tags/deadline labels use ΔΗΜ.Ω.Σ.', "'tag' => 'ΔΗΜ.Ω.Σ.'" in catalog and "'tool_label' => 'ΔΗΜ.Ω.Σ. →'" in deadlines)

failed = False
for label, ok in checks:
    print(('PASS' if ok else 'FAIL') + ' | ' + label)
    failed = failed or not ok
print(f'RESULT {sum(ok for _, ok in checks)} PASS / {sum(not ok for _, ok in checks)} FAIL')
raise SystemExit(1 if failed else 0)
