#!/usr/bin/env python3
from pathlib import Path
import re
import subprocess
from bs4 import BeautifulSoup

ROOT = Path(__file__).resolve().parents[1]
common_js = (ROOT / 'assets' / 'common.js').read_text(encoding='utf-8')
common_css = (ROOT / 'assets' / 'common.css').read_text(encoding='utf-8')
ergaleia_src = (ROOT / 'ergaleia.php').read_text(encoding='utf-8')
asep_src = (ROOT / 'asep-tools.php').read_text(encoding='utf-8')
deadlines_src = (ROOT / 'prothesmies.php').read_text(encoding='utf-8')
config = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
worker = (ROOT / 'service-worker.js').read_text(encoding='utf-8')

checks=[]
def check(label, condition): checks.append((label, bool(condition)))

version_match=re.search(r"define\('EDU_TOOLS_VERSION', '([0-9.]+)'\)",config)
worker_match=re.search(r"CACHE_NAME = CACHE_PREFIX \+ '([0-9.]+)'",worker)
check('release remains at or beyond consistency audit', bool(version_match) and tuple(map(int,version_match.group(1).split('.'))) >= (3,22,26))
check('service-worker cache matches release', bool(version_match and worker_match) and version_match.group(1)==worker_match.group(1))
check('directory participates in mobile hero info disclosure', "document.body.classList.contains('edu-tools-directory')" not in common_js and 'installMobileHeroInfoDisclosures(document)' in common_js)
check('global mobile hero selectors no longer exclude directory', 'not(.edu-tools-directory)' not in common_css)
check('mobile hero info control adapts to hero foreground colour', 'color: inherit !important' in common_css and 'border: 1px solid currentColor' in common_css)
check('directory personal section has no redundant explainer', 'Τα εργαλεία που χρησιμοποίησες πρόσφατα' not in ergaleia_src)
check('directory no longer keeps unused group descriptions', "$description = isset($groupConfig['description'])" not in ergaleia_src)
check('ASEP hub uses common hero grammar', 'class="hero edu-asep-hub-hero"' in asep_src)
check('ASEP hub duplicate quick-links removed', 'class="quick-links"' not in asep_src and 'edu-asep-tools-hub .quick-links' not in common_css)
check('deadlines removed implementation-oriented hero copy', 'χωρίς να επιβαρύνουν την κεντρική σελίδα' not in deadlines_src and 'ΞΕΧΩΡΙΣΤΗ ΕΝΟΤΗΤΑ' not in deadlines_src)

representative=[
 'ergaleia.php','asep-tools.php','ypologismos-morion.php','ypologismos-morion-1ea-2025.php',
 'ypologismos-morion-onaseia.php','ypologismos-morion-apospasis.php','ypologismos-morion-apospasis-dimos.php',
 'ypologismos-morion-apospasis-sde.php','ypologismos-morion-apospasis-psifiako-frontistirio.php',
 'ypologismos-morion-scholeio-evropaikis-paideias-irakleiou.php','ypologismos-didaktikou-orariou.php',
 'posa-paravola.php','dikaioma-symmetoxis.php','dikaioma-ypodiefthynti-saek.php','paidagogiki-eparkeia.php'
]
for page in representative:
    proc=subprocess.run(['php',page],cwd=ROOT,capture_output=True,text=True,timeout=30)
    check(f'{page}: renders cleanly', proc.returncode==0)
    soup=BeautifulSoup(proc.stdout,'html.parser')
    body=soup.body
    h1=soup.find('h1')
    hero=soup.select_one('.hero, .edu-legacy-hero')
    check(f'{page}: shared edu-ui body', bool(body and 'edu-ui' in (body.get('class') or [])))
    check(f'{page}: one clear H1', len(soup.find_all('h1'))==1)
    check(f'{page}: recognized hero', hero is not None)
    check(f'{page}: H1 belongs to hero', bool(hero and h1 and h1 in hero.descendants))
    check(f'{page}: no inline style regression', len(soup.select('[style]'))==0)

failed=False
for label,ok in checks:
    print(('PASS' if ok else 'FAIL')+' | '+label)
    failed = failed or not ok
print(f'RESULT {sum(ok for _,ok in checks)} PASS / {sum(not ok for _,ok in checks)} FAIL')
raise SystemExit(1 if failed else 0)
