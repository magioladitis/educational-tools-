from pathlib import Path
import subprocess

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'ypologismos-didaktikou-orariou.php').read_text(encoding='utf-8')
source = (ROOT / 'includes/components/source-card.php').read_text(encoding='utf-8')
css = (ROOT / 'assets/common.css').read_text(encoding='utf-8')
common = (ROOT / 'assets/common.js').read_text(encoding='utf-8')
printer = (ROOT / 'includes/education-print.js').read_text(encoding='utf-8')

checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))
    print(('PASS' if cond else 'FAIL') + ': ' + name)

check('responsive source helper exists', 'function sourceCardDisclosureStart' in source and 'function sourceCardDisclosureEnd' in source)
check('teaching-hours uses global responsive source card', 'sourceCardStart();' in page and 'sourceCardDisclosureStart' not in page)
check('teaching-hours uses global source card close', 'sourceCardEnd();' in page)
check('source card reuses native shared disclosure markup', 'edu-source-card__details' in source and '<summary class="edu-disclosure__summary">' in source)
check('mobile initializes sources collapsed', 'collapseResponsiveSourceCards' in common and "max-width: 650px" in common)
check('desktop default is inherited from shared source component', "$config['open'] = true" in source)
check('source card reuses shared disclosure CSS', '.edu-disclosure__summary' in css and '.edu-disclosure__chevron' in css)
check('generic print expands cloned details', "sourceClone.querySelectorAll('details')" in printer and "details.setAttribute('open', '')" in printer)
check('shared disclosure print CSS forces body visible', '.edu-disclosure > .edu-disclosure__body { display: block !important;' in css)

proc=subprocess.run(['php','ypologismos-didaktikou-orariou.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    html=proc.stdout
    check('rendered source card is responsive', 'edu-source-card--responsive' in html and 'data-mobile-collapsed="true"' in html)
    check('rendered source card defaults open', '<details class="edu-disclosure edu-source-card__details" open>' in html)
    check('all source links preserved', html.count('class="source-links"') == 1 and html.count('target="_blank"') >= 11)

failed=[n for n,ok in checks if not ok]
print(f"\n{len(checks)-len(failed)}/{len(checks)} checks passed")
raise SystemExit(1 if failed else 0)
