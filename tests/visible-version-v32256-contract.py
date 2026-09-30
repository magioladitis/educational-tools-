from pathlib import Path
import re

root = Path(__file__).resolve().parents[1]

def check(name, ok):
    print(('PASS' if ok else 'FAIL') + ': ' + name)
    if not ok:
        raise SystemExit(1)

config = (root / 'includes/config.php').read_text()
footer = (root / 'includes/footer.php').read_text()
css = (root / 'assets/common.css').read_text()
sw = (root / 'service-worker.js').read_text()

ver = re.search(r"EDU_TOOLS_VERSION',\s*'([^']+)'", config)
cache = re.search(r"CACHE_PREFIX \+ '([^']+)'", sw)
check('version 3.22.56', bool(ver) and ver.group(1) == '3.22.56')
check('cache 3.22.56', bool(cache) and cache.group(1) == '3.22.56')
check('footer renders version from constant', 'Έκδοση <?= htmlspecialchars(EDU_TOOLS_VERSION' in footer)
check('footer data-version from constant', 'data-version="<?= htmlspecialchars(EDU_TOOLS_VERSION' in footer)
check('version badge class exists', 'edu-tools-global-footer__version' in footer and '.edu-tools-global-footer__version' in css)
check('no hard-coded 3.22.56 in footer', '3.22.56' not in footer)
print('RESULT: visible online version v3.22.56 contract PASS')
