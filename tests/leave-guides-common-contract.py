#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys
ROOT = Path(__file__).resolve().parents[1]
checks=[]
def check(name, ok):
    checks.append((name, bool(ok)))
    print(('PASS' if ok else 'FAIL') + ' | ' + name)

for page, expected in [('adeies-anapliroton.php',25),('adeies-monimon.php',37)]:
    p=subprocess.run(['php', str(ROOT/page)], capture_output=True, text=True)
    html=p.stdout
    check(page+' renders', p.returncode==0 and '<html' in html)
    check(page+' uses shared CSS', 'assets/leave-guide.css' in html)
    check(page+' uses shared JS', 'includes/leave-guide-ui.js' in html)
    check(page+' correct leave count', html.count('class="leave-card"')==expected)
    ids=re.findall(r'data-leave-id="([^"]+)"', html)
    check(page+' unique leave ids', len(ids)==len(set(ids))==expected)
    check(page+' audience switcher', 'leave-audience-switcher' in html)
    check(page+' no public audit jargon', 'Νομικό audit' not in html and '>audit<' not in html.lower())

hub_src=(ROOT/'adeies-ekpaideutikon.php').read_text()
check('hub leave counts are data-driven', '$permanentLeaveCount = count(' in hub_src and '$substituteLeaveCount = count(' in hub_src and '$h($permanentLeaveCount)' in hub_src and '$h($substituteLeaveCount)' in hub_src)

hub=subprocess.run(['php', str(ROOT/'adeies-ekpaideutikon.php')], capture_output=True, text=True)
check('hub renders', hub.returncode==0 and '<html' in hub.stdout)
check('hub links permanent', 'href="adeies-monimon.php"' in hub.stdout)
check('hub links substitutes', 'href="adeies-anapliroton.php"' in hub.stdout)

catalog=(ROOT/'includes/tools-catalog.php').read_text()
check('catalog points to leave hub', "'href' => 'adeies-ekpaideutikon.php'" in catalog)
check('shared source registry exists', (ROOT/'includes/leave-guide-sources.php').exists())
check('shared renderer exists', (ROOT/'includes/leave-guide-page.php').exists())

failed=[n for n,ok in checks if not ok]
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
sys.exit(1 if failed else 0)
