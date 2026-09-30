from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'paidagogiki-eparkeia.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'pedagogical-competence-ui.js').read_text(encoding='utf-8')
css = (ROOT / 'assets' / 'common.css').read_text(encoding='utf-8')
checks = []

def check(name, condition):
    ok = bool(condition)
    checks.append((name, ok))
    print(('PASS' if ok else 'FAIL') + ': ' + name)

check('user copy states six-credential cap', 'έως <strong>6</strong> αποδεικτικά' in page)
check('controller defines six-credential cap', 'const MAX_CREDENTIALS = 6;' in ui)
check('add guard enforces cap', 'if (count >= MAX_CREDENTIALS)' in ui)
check('add button disables at cap', 'addButton.disabled = atLimit;' in ui)
check('add button re-syncs after removal', 'updateCredentialControls();' in ui and 'card.remove();' in ui)
check('cards are reindexed after mutation', 'function reindexCredentialCards()' in ui and 'heading.textContent = "Αποδεικτικό "' in ui)
check('mobile trash target is 44x44', '@media (max-width:760px)' in css and 'body.edu-ui .ped-remove-credential { width:44px; height:44px; flex:0 0 44px; }' in css)
check('limit is exposed for regression harness', 'maxCredentials: MAX_CREDENTIALS' in ui)

failed = [name for name, ok in checks if not ok]
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
