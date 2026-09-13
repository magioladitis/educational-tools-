from pathlib import Path

root = Path(__file__).resolve().parents[1]
common = (root / 'assets' / 'common.css').read_text(encoding='utf-8')
staffing = (root / 'assets' / 'staffing-simulator.css').read_text(encoding='utf-8')
timetable = (root / 'assets' / 'weekly-timetable.css').read_text(encoding='utf-8')
assignments = (root / 'anatheseis-mathimaton.php').read_text(encoding='utf-8')
config = (root / 'includes' / 'config.php').read_text(encoding='utf-8')

checks = []
def check(label, cond):
    checks.append((label, bool(cond)))
    print(('PASS' if cond else 'FAIL'), label)

check('release bumped to v3.21.14', "EDU_TOOLS_VERSION', '3.21.14" in config)
check('mobile controls use 16px to avoid iOS focus zoom', 'font-size: 16px !important;' in common)
check('mobile controls retain 44px touch targets', 'min-height: 44px;' in common)
check('narrow navigation does not shrink below 44px', 'width: 38px;' not in common and common.count('width: 44px;') >= 2)
check('mobile long content can wrap', 'overflow-wrap: anywhere;' in common)
check('horizontal data areas get touch scrolling', '-webkit-overflow-scrolling: touch;' in common)
check('safe-area aware back-to-top control', 'env(safe-area-inset-right)' in common and 'env(safe-area-inset-bottom)' in common)
check('staffing specialty table remains readable', 'min-width:680px;' in staffing)
check('staffing claim list can shrink on phone', '.claim-list' in staffing and 'min-width:0;' in staffing)
check('staffing help respects dynamic viewport height', '100dvh' in staffing)
check('weekly timetable long text wraps', 'Mobile hardening v3.21.14' in timetable and 'overflow-wrap:anywhere;' in timetable)
check('teaching assignments result headings wrap', '#assignmentResults h3' in assignments and 'flex-wrap:wrap;' in assignments)
check('no global overflow-x hiding introduced', 'body.edu-ui {\n  overflow-x: hidden' not in common and 'html {\n  overflow-x: hidden' not in common)

failed = [label for label, ok in checks if not ok]
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
