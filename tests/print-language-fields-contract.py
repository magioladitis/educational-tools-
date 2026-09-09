from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
helper = (ROOT / 'includes' / 'education-print.js').read_text(encoding='utf-8')

checks = {
    'language selector has component-aware print collector': 'function collectLanguageFields(root, rows, seen)' in helper,
    'language controls are skipped from generic duplicate collector': 'control.closest(\'[data-component="asep-language-selector"]\')' in helper,
    'language and level are combined': "combined += ' · ' + levelText" in helper,
    'single-language fallback is semantic': ": 'Ξένη γλώσσα';" in helper,
    'generic label detection checks sibling field labels': "control.closest('.field, .form-field, .input-group')" in helper,
    'generic fallback no longer emits Πεδίο': "control.name || control.id || ''" in helper and "control.name || control.id || 'Πεδίο'" not in helper,
}
failed = [name for name, ok in checks.items() if not ok]
for name, ok in checks.items():
    print(('PASS' if ok else 'FAIL') + ' - ' + name)
if failed:
    raise SystemExit(f'{len(failed)} failed')
print(f'{len(checks)}/{len(checks)} passed')
