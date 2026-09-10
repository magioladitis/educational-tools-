from pathlib import Path
import subprocess

ROOT = Path(__file__).resolve().parents[1]
source = (ROOT / 'includes/components/source-card.php').read_text(encoding='utf-8')
common = (ROOT / 'assets/common.js').read_text(encoding='utf-8')
printer = (ROOT / 'includes/education-print.js').read_text(encoding='utf-8')

checks=[]
def check(name, ok):
    if not ok:
        raise AssertionError(name)
    checks.append(name)
    print('PASS:', name)

check('legacy sourceCardStart delegates to responsive disclosure', 'sourceCardDisclosureStart($config);' in source)
check('legacy sourceCardEnd delegates to responsive disclosure close', 'sourceCardDisclosureEnd();' in source)
check('legacy source cards default mobile collapsed', "$config['mobile_collapsed'] = true" in source)
check('legacy source cards default desktop open', "$config['open'] = true" in source)
check('mobile collapse hook supports source cards', '.edu-source-card[data-mobile-collapsed="true"]' in common)
check('print clone expands details', "querySelectorAll('details').forEach" in printer and "setAttribute('open', '')" in printer)

legacy_pages=[]
explicit_pages=[]
for f in ROOT.glob('*.php'):
    text=f.read_text(encoding='utf-8', errors='ignore')
    if 'sourceCardStart(' in text:
        legacy_pages.append(f)
    if 'sourceCardDisclosureStart(' in text:
        explicit_pages.append(f)

check('broad legacy adoption remains', len(legacy_pages) >= 25)
check('pilot pages no longer carry explicit responsive source overrides', len(explicit_pages) == 0)

# Render a representative cross-section: simple guide, calculator, complex tool.
for name in ['dikaiologitika-tekna-anapiria.php', 'ypologismos-morion-apospasis.php', 'anatheseis-mathimaton.php']:
    proc=subprocess.run(['php', name], cwd=ROOT, text=True, capture_output=True)
    check(name + ' renders', proc.returncode == 0)
    html=proc.stdout
    check(name + ' responsive source card', 'edu-source-card--responsive' in html and 'data-mobile-collapsed="true"' in html)
    check(name + ' source details default open', 'edu-source-card__details" open' in html)

print(f'RESULT {len(checks)} PASS / 0 FAIL')
