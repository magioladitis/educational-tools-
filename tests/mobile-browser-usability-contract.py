#!/usr/bin/env python3
from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parents[1]
common = (ROOT / 'assets/common.css').read_text(encoding='utf-8')
staffing = (ROOT / 'assets/staffing-simulator.css').read_text(encoding='utf-8')
timetable = (ROOT / 'assets/weekly-timetable.css').read_text(encoding='utf-8')

checks = []
def check(label, condition):
    checks.append((label, bool(condition)))

# Every public PHP document must be phone-scaled and use the shared responsive system.
public_pages = []
for path in sorted(ROOT.glob('*.php')):
    text = path.read_text(encoding='utf-8', errors='ignore')
    if '<html' not in text.lower():
        continue
    public_pages.append(path)
    check(f'{path.name}: viewport meta', 'name="viewport"' in text or "name='viewport'" in text)
    check(f'{path.name}: common.css', 'assets/common.css' in text)

check('public page inventory is non-trivial', len(public_pages) >= 30)

# Shared mobile ergonomics.
check('phone controls use 16px to avoid iOS focus zoom', 'font-size: 16px !important' in common)
check('standard check rows expose 44px touch area', 'body.edu-ui.edu-calc-standard .checkrow,' in common and 'body.edu-ui .check-row,' in common and 'min-height: 44px;' in common)
check('segmented controls expose 44px touch area', 'body.edu-ui .segmented-choice span {' in common and 'min-height: 44px;' in common)
check('mobile table wrappers retain touch scrolling', '-webkit-overflow-scrolling: touch;' in common and 'overscroll-behavior-inline: contain;' in common)
check('CSS does not hide horizontal overflow globally', not re.search(r'overflow-x\s*:\s*hidden', common + '\n' + staffing + '\n' + timetable))

# Main tools directory: reduce vertical fatigue while keeping all information.
check('directory mobile cards remove desktop min-height', 'body.edu-ui.edu-tools-directory .tool-card{min-height:0;padding:17px}' in common)
check('directory mobile grid tightens spacing', 'body.edu-ui.edu-tools-directory .tools-grid{grid-template-columns:1fr;gap:12px}' in common)
check('directory filters become horizontal touch strip', 'flex-wrap:nowrap;overflow-x:auto;max-width:100%;padding:1px 1px 6px;' in common)
check('directory filter chips remain 44px touch targets', '.filter-btn{flex:0 0 auto;min-height:44px;white-space:nowrap' in common)
check('directory hero actions remain 44px touch targets', '.hero-action{min-height:44px;' in common)

# Heavy staffing workflow: tabs scroll instead of consuming multiple lines.
check('staffing mobile tabs are horizontal scroll strip', '.edu-page-staffing-simulator .mode-tabs{' in staffing and 'flex-wrap:nowrap;overflow-x:auto;' in staffing)
check('staffing mobile tabs retain 44px targets', '.edu-page-staffing-simulator .mode-tab{flex:0 0 auto;min-height:44px;white-space:nowrap}' in staffing)
check('staffing wide balance table remains readable via controlled scroll', '.specialty-balance-table{' in staffing and 'min-width:680px;' in staffing)
check('staffing help panel respects phone viewport height', '100dvh - 96px' in staffing)

# Weekly timetable must wrap course names/details rather than forcing page overflow.
check('weekly timetable wraps long mobile text', '.timetable-course-title,' in timetable and 'overflow-wrap:anywhere;' in timetable)

failed = [label for label, ok in checks if not ok]
for label, ok in checks:
    print(('[PASS] ' if ok else '[FAIL] ') + label)
print(f'\n{len(checks)-len(failed)}/{len(checks)} checks passed')
if failed:
    sys.exit(1)
