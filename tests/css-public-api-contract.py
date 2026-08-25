from pathlib import Path
import re
import tinycss2

ROOT = Path(__file__).resolve().parents[1]
CSS_PATH = ROOT / 'assets/common.css'
JS_PATH = ROOT / 'assets/common.js'
CONFIG = ROOT / 'includes/config.php'
HEADER = ROOT / 'includes/header.php'

css = CSS_PATH.read_text()
js = JS_PATH.read_text()
config = CONFIG.read_text()
header = HEADER.read_text()

passes = fails = 0

def check(name, condition):
    global passes, fails
    print(('PASS' if condition else 'FAIL'), name)
    if condition:
        passes += 1
    else:
        fails += 1

# Parse all qualified rules, including rules nested in media queries.
rules = tinycss2.parse_stylesheet(css, skip_comments=True, skip_whitespace=True)
all_classes = set()
custom_decl = set()
custom_ref = set()

def walk(rule_list):
    for rule in rule_list:
        if rule.type == 'qualified-rule':
            selector = tinycss2.serialize(rule.prelude)
            all_classes.update(re.findall(r'\.([A-Za-z_][A-Za-z0-9_-]*)', selector))
            declarations = tinycss2.parse_declaration_list(rule.content, skip_comments=True, skip_whitespace=True)
            for declaration in declarations:
                if declaration.type != 'declaration':
                    continue
                if declaration.name.startswith('--'):
                    custom_decl.add(declaration.name)
                value = tinycss2.serialize(declaration.value)
                custom_ref.update(re.findall(r'var\(\s*(--[A-Za-z0-9_-]+)', value))
        elif rule.type == 'at-rule' and rule.content is not None:
            try:
                walk(tinycss2.parse_rule_list(rule.content, skip_comments=True, skip_whitespace=True))
            except Exception:
                pass

walk(rules)

production_files = (
    list(ROOT.glob('*.php')) +
    list((ROOT / 'includes').rglob('*.php')) +
    list((ROOT / 'includes').rglob('*.js')) +
    [JS_PATH]
)
production_text = '\n'.join(p.read_text(errors='ignore') for p in production_files)

PUBLIC_UNUSED_CLASSES = {
    'edu-actions',
    'edu-card',
    'edu-field',
    'edu-field--full',
    'edu-field-grid',
    'edu-help',
    'edu-message',
    'edu-message--danger',
    'edu-message--info',
    'edu-message--success',
    'edu-message--warning',
    'result-message--disclaimer',
}

unused_classes = {
    cls for cls in all_classes
    if not re.search(r'(?<![A-Za-z0-9_-])' + re.escape(cls) + r'(?![A-Za-z0-9_-])', production_text)
}

DEAD_LEGACY_CLASSES = {
    'back', 'back-tools', 'calc-actions', 'edu-mt-18', 'edu-source-compact',
    'edu-tools-global-footer__disclaimer', 'edu-tools-global-footer__meta',
    'edu-tools-global-header__brand', 'edu-tools-global-header__brand-text',
    'inline-result', 'official-note', 'result-box', 'sde-language-level', 'source-note',
}

PUBLIC_TOKENS = {
    '--edu-bg', '--edu-surface', '--edu-surface-soft', '--edu-text', '--edu-muted',
    '--edu-border', '--edu-border-strong', '--edu-primary', '--edu-primary-dark',
    '--edu-primary-soft', '--edu-success', '--edu-success-soft', '--edu-success-border',
    '--edu-warning', '--edu-warning-soft', '--edu-warning-border', '--edu-danger',
    '--edu-danger-soft', '--edu-danger-border', '--edu-neutral-soft', '--edu-shadow-sm',
    '--edu-shadow', '--edu-radius-sm', '--edu-radius', '--edu-radius-lg', '--edu-control-height',
}
COMPAT_TOKENS = {'--edu-tools-blue', '--edu-tools-blue-dark', '--edu-tools-muted', '--edu-tools-border'}
INTENTIONALLY_UNCONSUMED_TOKENS = {'--edu-radius-sm', '--edu-radius'} | COMPAT_TOKENS
THEME_HOOKS = {'--edu-page-accent', '--edu-page-accent-dark'}

check('CSS parses without top-level errors', not any(getattr(r, 'type', '') == 'error' for r in rules))
check('central cache version constant is defined', bool(re.search(r"define\('EDU_TOOLS_VERSION',\s*'[^']+'\)", config)))
check('all canonical public design tokens remain declared', PUBLIC_TOKENS <= custom_decl)
check('all backwards-compatible edu-tools token aliases remain declared', COMPAT_TOKENS <= custom_decl)
check('only documented public/compat tokens are declared but not consumed internally', custom_decl - custom_ref == INTENTIONALLY_UNCONSUMED_TOKENS)
check('only page-theme hooks are referenced without a global declaration', custom_ref - custom_decl == THEME_HOOKS)
check('all dead legacy selector classes have been removed', not (DEAD_LEGACY_CLASSES & all_classes))
check('all selector classes unused by current production are documented canonical public API classes', unused_classes <= PUBLIC_UNUSED_CLASSES)
check('canonical button base class exists', 'edu-btn' in all_classes)
check('canonical BEM primary modifier exists', 'edu-btn--primary' in all_classes)
check('canonical BEM secondary modifier exists', 'edu-btn--secondary' in all_classes)
check('common.js emits canonical button base + modifier classes', "button.classList.add('edu-btn', isSecondary ? 'edu-btn--secondary' : 'edu-btn--primary')" in js)
check('common.js still recognizes legacy button modifier aliases', "button.classList.contains('edu-btn-primary')" in js and "button.classList.contains('edu-btn-secondary')" in js)
check('legacy button selector aliases remain in CSS for compatibility', 'button.edu-btn-primary' in css and 'button.edu-btn-secondary' in css)
check('shared header comment no longer carries a stale hard-coded UI version', 'UI v3.20.1' not in header)

print(f'RESULT {passes} PASS / {fails} FAIL')
raise SystemExit(1 if fails else 0)
