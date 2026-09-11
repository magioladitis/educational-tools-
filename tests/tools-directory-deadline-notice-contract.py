#!/usr/bin/env python3
from pathlib import Path
import subprocess, re
ROOT=Path(__file__).resolve().parents[1]
page=(ROOT/'ergaleia.php').read_text(encoding='utf-8')
css=(ROOT/'assets/common.css').read_text(encoding='utf-8')
checks=[]
def check(n,c): checks.append((n,bool(c)))
check('obsolete deadline migration heading removed', 'Προθεσμίες σε ξεχωριστή σελίδα' not in page)
check('obsolete deadline migration copy removed', 'Οι ενεργές και προσεχείς ημερομηνίες δεν εμφανίζονται πλέον ανάμεσα στα εργαλεία.' not in page)
check('obsolete deadline side-box link removed', 'Άνοιγμα προθεσμιών →' not in page)
check('primary deadline hero link retained', 'class="hero-action" href="prothesmies.php"' in page)
check('deadline shortcut retained', 'class="deadline-shortcut" href="prothesmies.php"' in page)
check('remaining notice uses single-column grid', 'class="info-grid info-grid-single"' in page and '.info-grid.info-grid-single{grid-template-columns:1fr}' in css)
proc=subprocess.run(['php',str(ROOT/'ergaleia.php')],cwd=str(ROOT),capture_output=True,text=True)
html=proc.stdout
check('directory renders', proc.returncode==0 and '<html' in html)
check('rendered obsolete copy absent', 'Προθεσμίες σε ξεχωριστή σελίδα' not in html and 'Άνοιγμα προθεσμιών →' not in html)
check('rendered page keeps at least two deadline links', len(re.findall(r'href="prothesmies\.php"', html)) >= 2)
failed=[n for n,p in checks if not p]
for n,p in checks: print(('PASS' if p else 'FAIL')+': '+n)
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
