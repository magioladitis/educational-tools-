#!/usr/bin/env python3
from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
common_js = (ROOT / 'assets' / 'common.js').read_text(encoding='utf-8')
common_css = (ROOT / 'assets' / 'common.css').read_text(encoding='utf-8')
ergaleia = (ROOT / 'ergaleia.php').read_text(encoding='utf-8')
config = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
worker = (ROOT / 'service-worker.js').read_text(encoding='utf-8')

checks = []
def check(label, condition):
    checks.append((label, bool(condition)))

check('release version bumped to 3.22.25', "define('EDU_TOOLS_VERSION', '3.22.25')" in config)
check('service-worker cache matches release', "CACHE_NAME = CACHE_PREFIX + '3.22.25'" in worker)
check('common JS syntax', subprocess.run(['node', '--check', str(ROOT / 'assets' / 'common.js')], capture_output=True).returncode == 0)
check('mobile hero info disclosure installed', 'installMobileHeroInfoDisclosures(document)' in common_js)
check('directory is excluded from tool info disclosure', "document.body.classList.contains('edu-tools-directory')" in common_js)
check('hero intro and meta nodes are disclosure content', ".hero-kicker, p, .intro, .subtitle, .meta, .hero-meta, .hero-tags" in common_js)
check('info control has accessible expanded state', "button.setAttribute('aria-expanded', 'false')" in common_js and 'Πληροφορίες εργαλείου' in common_js)
check('mobile hides hero supporting content until opened', "node.setAttribute('hidden', '')" in common_js and 'edu-mobile-hero-info-open' in common_js)
check('desktop restores hero supporting content', "else node.removeAttribute('hidden')" in common_js)
check('info icon preserves 44px touch target', 'width: 44px' in common_css and 'height: 44px' in common_css and '.edu-mobile-hero-info-button' in common_css)
check('old more/less hero UI removed', 'edu-mobile-intro-toggle' not in common_js and 'edu-mobile-intro-clamp' not in common_js and 'Περισσότερα' not in common_js)
check('category cards no longer render descriptions', '<?php echo $h($description); ?>' not in ergaleia)
check('category group headings no longer render description paragraphs', 'tool-group__heading p' not in common_css)

failed = False
for label, ok in checks:
    print(('PASS' if ok else 'FAIL') + ' | ' + label)
    failed = failed or not ok
print(f'RESULT {sum(ok for _, ok in checks)} PASS / {sum(not ok for _, ok in checks)} FAIL')
raise SystemExit(1 if failed else 0)
