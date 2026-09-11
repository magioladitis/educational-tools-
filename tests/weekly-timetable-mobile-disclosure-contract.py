#!/usr/bin/env python3
from pathlib import Path
import re, subprocess

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
common = (ROOT / 'assets/common.js').read_text(encoding='utf-8')
config = (ROOT / 'includes/config.php').read_text(encoding='utf-8')
checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))

check('hours help uses central disclosure component', 'calculatorDisclosureStart(array(' in page)
check('hours help is disclosure summary', "'summary' => 'Τι σημαίνει «ώρες»'" in page)
check('hours help open on desktop', "'open' => true" in page)
check('hours help marked mobile-collapsible', "'attrs' => array('data-mobile-collapsed' => 'true')" in page)
check('hours help closes central disclosure', 'calculatorDisclosureEnd();' in page)
check('legacy static h2 removed', '<h2>Τι σημαίνει «ώρες»</h2>' not in page)
check('shared mobile collapse handles disclosures', '.edu-disclosure[data-mobile-collapsed="true"]' in common and "details.removeAttribute('open')" in common)
check('release version 3.21.08', "define('EDU_TOOLS_VERSION', '3.21.08');" in config)

proc=subprocess.run(['php', str(ROOT/'orologio-programma-mathimaton.php')], cwd=str(ROOT), capture_output=True, text=True)
html=proc.stdout
check('weekly timetable page renders', proc.returncode == 0 and '<html' in html)
m = re.search(r'<details[^>]*data-mobile-collapsed="true"[^>]*>', html)
check('rendered disclosure has mobile marker', bool(m and 'edu-disclosure' in m.group(0) and 'open' in m.group(0)))
check('rendered summary retained', 'Τι σημαίνει «ώρες»' in html)

failed=[n for n,p in checks if not p]
for n,p in checks: print(('PASS' if p else 'FAIL') + ': ' + n)
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
