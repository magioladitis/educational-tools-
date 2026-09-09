from pathlib import Path
import re

root = Path(__file__).resolve().parents[1]
header = (root / 'includes' / 'header.php').read_text(encoding='utf-8')
css = (root / 'assets' / 'common.css').read_text(encoding='utf-8')

checks = []
def check(label, cond):
    checks.append((label, bool(cond)))
    print(('PASS' if cond else 'FAIL'), label)

check('home navigation keeps an accessible label', 'aria-label="Αρχική Εργαλειοθήκης"' in header)
check('all-tools navigation has a grid icon and accessible label', 'aria-label="Όλα τα εργαλεία"' in header and header.count('edu-tools-global-nav__icon') >= 3)
check('deadlines navigation has an accessible label', 'aria-label="Προθεσμίες"' in header)
check('categories summary has an accessible label', 'summary aria-label="Κατηγορίες"' in header)
check('desktop text labels are preserved', header.count('edu-tools-global-nav__label') == 3 and 'edu-tools-global-header__back-label' in header)
check('mobile breakpoint hides all textual navigation labels', bool(re.search(r'@media \(max-width: 700px\).*?\.edu-tools-global-header__back-label,\s*\.edu-tools-global-nav__label\s*\{\s*display:\s*none;', css, re.S)))
check('mobile navigation uses fixed touch targets', bool(re.search(r'@media \(max-width: 700px\).*?width:\s*40px;.*?height:\s*40px;', css, re.S)))
check('mobile category chevron is hidden', bool(re.search(r'@media \(max-width: 700px\).*?\.edu-tools-global-menu > summary::after\s*\{\s*display:\s*none;', css, re.S)))
check('legacy rule no longer removes first mobile navigation link', '.edu-tools-global-nav__link:first-child { display: none; }' not in css)
check('very narrow screens retain four icon controls', '@media (max-width: 360px)' in css and 'width: 38px;' in css)

failed = [label for label, ok in checks if not ok]
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
