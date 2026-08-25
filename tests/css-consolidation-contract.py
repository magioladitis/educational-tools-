from pathlib import Path
import tinycss2
import collections

ROOT = Path(__file__).resolve().parents[1]
CSS = ROOT / 'assets/common.css'
TARGET_FAMILIES = (
    'edu-page-european-schools',
    'edu-page-abroad',
    'edu-page-digital-tutoring',
    'edu-calc-sde',
)

passes = fails = 0

def check(name, condition):
    global passes, fails
    print(('PASS' if condition else 'FAIL'), name)
    if condition:
        passes += 1
    else:
        fails += 1

css = CSS.read_text()
rules = tinycss2.parse_stylesheet(css, skip_comments=True, skip_whitespace=True)
check('CSS parses without top-level errors', not any(getattr(r, 'type', '') == 'error' for r in rules))
check('consolidation marker present', 'Consolidated v3.20.50 from identical page-scoped rules only' in css)

by_declarations = collections.defaultdict(list)
qualified_count = 0
for rule in rules:
    if rule.type != 'qualified-rule':
        continue
    qualified_count += 1
    selector = tinycss2.serialize(rule.prelude).strip()
    declarations = []
    for declaration in tinycss2.parse_declaration_list(rule.content, skip_comments=True, skip_whitespace=True):
        if declaration.type == 'declaration':
            declarations.append((
                declaration.name.strip(),
                tinycss2.serialize(declaration.value).strip(),
                bool(declaration.important),
            ))
    if declarations and any(name in selector for name in TARGET_FAMILIES):
        by_declarations[tuple(declarations)].append(selector)

cross_family_duplicates = []
for declarations, selectors in by_declarations.items():
    families = set()
    for selector in selectors:
        for name in TARGET_FAMILIES:
            if name in selector:
                families.add(name)
    if len(families) >= 2 and len(selectors) >= 2 and len(declarations) >= 2:
        cross_family_duplicates.append((declarations, selectors))

check('no remaining exact cross-family duplicate declaration sets', len(cross_family_duplicates) == 0)
check('qualified-rule count remains at or below consolidated baseline 811', qualified_count <= 811)
check('legacy checkrow retained for distinct visual variants', '.checkrow' in css)
check('legacy check-row retained for distinct visual variants', '.check-row' in css)

print(f'RESULT {passes} PASS / {fails} FAIL')
raise SystemExit(1 if fails else 0)
