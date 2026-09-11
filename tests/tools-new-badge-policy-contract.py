#!/usr/bin/env python3
from pathlib import Path
import subprocess, json, re, sys

ROOT = Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond, detail=''):
    checks.append(bool(cond))
    print(('PASS' if cond else 'FAIL') + ': ' + name + ((' — '+detail) if detail else ''))

php = r'''
$catalog = require 'includes/tools-catalog.php';
echo json_encode($catalog['tools'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
r=subprocess.run(['php','-r',php],cwd=ROOT,text=True,capture_output=True)
check('catalog loads', r.returncode==0, r.stderr.strip())
tools=json.loads(r.stdout) if r.returncode==0 else []
old_new=[t.get('number') for t in tools if int(t.get('number',0))<30 and t.get('new')]
current_new=[int(t['number']) for t in tools if t.get('new')]
check('no NEW badge before tool 30', old_new==[], str(old_new))
check('current NEW tools are 30-32', current_new==[30,31,32], str(current_new))

render=subprocess.run(['php','ergaleia.php'],cwd=ROOT,text=True,capture_output=True)
check('directory renders', render.returncode==0, render.stderr.strip())
html=render.stdout
badge_count=len(re.findall(r'class="new-badge"[^>]*>\s*ΝΕΟ\s*</span>',html))
check('rendered directory has exactly 3 NEW badges', badge_count==3, str(badge_count))
cards=re.findall(r'<a[^>]*class="[^"]*tool-card[^"]*"[^>]*>.*?</a>', html, re.S)
for n in (30,31,32):
    card=next((c for c in cards if '<span class="tool-number">%d</span>' % n in c), '')
    check('tool #%d renders NEW badge'%n, bool(card and 'class="new-badge"' in card))

print('\n%d/%d PASS' % (sum(checks), len(checks)))
sys.exit(0 if all(checks) else 1)
