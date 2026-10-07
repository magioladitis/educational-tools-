from pathlib import Path
import re, sys
ROOT = Path(__file__).resolve().parents[1]
errors=[]
def check(cond,msg):
    if not cond: errors.append(msg)
registry=(ROOT/'includes/legal-audit.php').read_text()
pages={
 'ypologismos-morion-1ea-2025.php':'1ΕΑ/2025',
 'ypologismos-morion-2ea-2025.php':'2ΕΑ/2025',
 'ypologismos-morion-3ea-2025.php':'3ΕΑ/2025',
 'ypologismos-morion-4ea-2025.php':'4ΕΑ/2025',
 'ypologismos-morion-5ea-2022.php':'5ΕΑ/2022',
 'ypologismos-morion-1gt-2024.php':'1ΓΤ/2024',
}
for page,label in pages.items():
    pattern=re.compile(r"'"+re.escape(page)+r"'\s*=>\s*array\((?:(?!\n\s*\),).)*?'status'\s*=>\s*'verified'(?:(?!\n\s*\),).)*?'last_verified'\s*=>\s*'2026-10-07'(?:(?!\n\s*\),).)*?'version'\s*=>\s*'3\.22\.90'", re.S)
    check(bool(pattern.search(registry)), f'{label}: verified audit entry v3.22.90')

onegt=(ROOT/'ypologismos-morion-1gt-2024.php').read_text()
check('28/16.07.2024' in onegt, '1ΓΤ/2024: second FEK 28/2024 surfaced')
check('18/06/2025' in onegt, '1ΓΤ/2024: final tables date surfaced')

twoea=(ROOT/'ypologismos-morion-2ea-2025.php').read_text()
check('21/23.05.2025' in twoea and '24/02.06.2025' in twoea, '2ΕΑ/2025: both FEKs surfaced')
check('02/06/2026' in twoea, '2ΕΑ/2025: final tables date surfaced')

five=(ROOT/'ypologismos-morion-5ea-2022.php').read_text()
check('Μέχρι 07/10/2026' in five and 'Μέχρι 24/08/2026' not in five, '5ΕΑ/2022: historical freshness note updated')
check('22/06/2023' in five, '5ΕΑ/2022: final tables date surfaced')

for page,date in [('ypologismos-morion-1ea-2025.php','29/04/2026'),('ypologismos-morion-3ea-2025.php','30/06/2026'),('ypologismos-morion-4ea-2025.php','29/04/2026')]:
    check(date in (ROOT/page).read_text(), f'{page}: final tables date surfaced')

config=(ROOT/'includes/config.php').read_text(); sw=(ROOT/'service-worker.js').read_text()
check("3.22.90" in config and "3.22.90" in sw, 'v3.22.90 version/cache synchronized')

if errors:
    print('\n'.join('FAIL: '+x for x in errors)); sys.exit(1)
print(f'PASS: {len(pages)+9} wave-3 legal-audit checks')
