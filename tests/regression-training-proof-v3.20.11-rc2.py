from pathlib import Path
from bs4 import BeautifulSoup, NavigableString
import subprocess, re, hashlib, sys

BASE=Path('/mnt/data/asep-toolkit-v3.20.10-production')
NEW=Path('/mnt/data/asep-toolkit-v3.20.11-rc2-production')
FILES=[
'ypologismos-morion.php','ypologismos-morion-1gt-2024.php','ypologismos-morion-1ea-2025.php',
'ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-4ea-2025.php']
checks=[]
def ok(name, cond, detail=''):
    checks.append((name,bool(cond),detail))

def inline_scripts(s):
    return re.findall(r'<script(?:\s[^>]*)?>(.*?)</script>',s,re.S|re.I)

def render(path):
    r=subprocess.run(['php',str(path)],capture_output=True,text=True,encoding='utf-8')
    if r.returncode:
        raise RuntimeError(f'{path}: {r.stderr}')
    return r.stdout

def norm_text(html):
    soup=BeautifulSoup(html,'html.parser')
    return ' '.join(soup.get_text(' ',strip=True).split())

def proof_fingerprint(html):
    soup=BeautifulSoup(html,'html.parser')
    node=soup.find(id='trainingProof')
    if not node: return None
    # data-training-context is intentionally new and non-behavioral.
    if node.has_attr('data-training-context'): del node.attrs['data-training-context']
    def walk(x):
        if isinstance(x,NavigableString):
            t=' '.join(str(x).split())
            return ('#text',t) if t else None
        attrs=[]
        for k,v in sorted(x.attrs.items()):
            if k=='class' and isinstance(v,list): v=tuple(v)
            attrs.append((k,tuple(v) if isinstance(v,list) else v))
        kids=[]
        for c in x.children:
            z=walk(c)
            if z is not None:kids.append(z)
        return (x.name,tuple(attrs),tuple(kids))
    return walk(node)

partial=(NEW/'includes/components/training-proof.php').read_text(encoding='utf-8')
ok('partial has no PHP 7.4 arrow function', re.search(r'\bfn\s*\(', partial) is None)
ok('partial has no return type declaration', re.search(r'\)\s*:\s*(?:void|string|int|bool|float|array)\b', partial) is None)
for forbidden in ['300','400','7 μην','μόρια','seminar400','auxSeminar400','Ε.Α.Ε.','1ΓΕ','2ΓΕ','1ΓΤ']:
    ok(f'partial has no rule token: {forbidden}', forbidden not in partial)
ok('partial defines renderer', 'function renderTrainingProof' in partial)
ok('partial is presentation-only documented', 'contains no eligibility, scoring, hours, duration' in partial)

for fn in FILES:
    old=(BASE/fn).read_text(encoding='utf-8')
    new=(NEW/fn).read_text(encoding='utf-8')
    ok(f'{fn}: literal training-proof removed', '<div class="training-proof hidden" id="trainingProof">' not in new)
    ok(f'{fn}: component required', "includes/components/training-proof.php" in new)
    ok(f'{fn}: renderer called', 'renderTrainingProof([' in new)
    ok(f'{fn}: inline JS byte-identical', inline_scripts(old)==inline_scripts(new))
    old_html=render(BASE/fn); new_html=render(NEW/fn)
    ok(f'{fn}: visible text parity', norm_text(old_html)==norm_text(new_html))
    ok(f'{fn}: training proof DOM parity', proof_fingerprint(old_html)==proof_fingerprint(new_html))

# Explicitly guard EAE behavior/copy so the generic 300h proof does not erase 400h logic.
s3=(NEW/'ypologismos-morion-3ea-2025.php').read_text(encoding='utf-8')
ok('3EA keeps 400h EAE criterion', 'id="seminar400"' in s3 and '≥400 ωρών και ≥7 μηνών' in s3)
ok('3EA proof still activates from training OR 400h', "$('training').checked||$('seminar400').checked" in s3.replace(' ',''))
ok('3EA has explicit mixed context', "3ea-2025-general-300h-or-eae-400h-7m" in s3)
s4=(NEW/'ypologismos-morion-4ea-2025.php').read_text(encoding='utf-8')
ok('4EA keeps 400h EAE criterion', 'id="auxSeminar400"' in s4 and '≥400 ωρών και διάρκειας ≥7 μηνών' in s4)
ok('4EA proof still activates from training OR 400h', "return $('training').checked || $('auxSeminar400').checked;" in s4)
ok('4EA has explicit mixed context', "4ea-2025-general-300h-or-eae-400h-7m" in s4)

# The SDE registry proof is a different UI/rule and must stay outside this component.
sde=(NEW/'ypologismos-morion-mitroo-sde.php').read_text(encoding='utf-8')
ok('SDE registry keeps separate educatorTrainingProof', 'id="educatorTrainingProof"' in sde)
ok('SDE registry does not require generic training-proof component', 'components/training-proof.php' not in sde)

# Shared browser assets are intentionally unchanged.
for asset in ['assets/common.css','assets/common.js']:
    ok(f'{asset}: byte-identical', (BASE/asset).read_bytes()==(NEW/asset).read_bytes())

# All production PHP files remain free of local CSS and have balanced HTML comments.
for p in sorted(NEW.glob('*.php')):
    s=p.read_text(encoding='utf-8')
    ok(f'{p.name}: no style block', '<style' not in s.lower())
    ok(f'{p.name}: balanced HTML comments', s.count('<!--')==s.count('-->'))

passed=sum(x[1] for x in checks)
for name,status,detail in checks:
    print(('PASS' if status else 'FAIL')+' | '+name+((' | '+detail) if detail else ''))
print(f'\nRESULT: {passed}/{len(checks)} PASS')
if passed!=len(checks): sys.exit(1)
