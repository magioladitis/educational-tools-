#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys
ROOT=Path(__file__).resolve().parents[1]
PASS=FAIL=0

def check(cond,msg):
    global PASS,FAIL
    if cond:
        PASS+=1; print('PASS',msg)
    else:
        FAIL+=1; print('FAIL',msg)

targets={
 'ergaleia.php':'assets/tools-directory.js',
 'ypologismos-morion-apospasis-evropaika-scholeia.php':'includes/european-schools-ui.js',
 'ypologismos-morion-1gt-2024.php':'includes/asep-1gt-ui.js',
 'ypologismos-morion-3ea-2025.php':'includes/asep-3ea-ui.js',
 'ypologismos-morion-2ea-2025.php':'includes/asep-2ea-ui.js',
 'dikaioma-ypodiefthynti-saek.php':'includes/saek-deputy-eligibility-ui.js',
 'ypologismos-morion-1ea-2025.php':'includes/asep-1ea-ui.js',
}

def inline_scripts(text):
    out=[]
    for m in re.finditer(r'<script\b([^>]*)>(.*?)</script>',text,re.S|re.I):
        if re.search(r'\bsrc\s*=',m.group(1),re.I): continue
        if re.search(r'\btype\s*=\s*[\"\']application/json[\"\']',m.group(1),re.I): continue
        if m.group(2).strip(): out.append(m.group(2))
    return out

handler_re=re.compile(r'\bon(?:click|change|input|submit|keyup|keydown|blur|focus)\s*=|["\']on(?:click|change|input|submit|keyup|keydown|blur|focus)["\']\s*=>',re.I)

for php,js in targets.items():
    ps=(ROOT/php).read_text(encoding='utf-8')
    check((ROOT/js).exists(),f'{js} exists')
    check(Path(js).name in ps,f'{php} loads {Path(js).name}')
    check(not inline_scripts(ps),f'{php} has no inline executable script')
    check(handler_re.search(ps) is None,f'{php} has no inline event handler')
    check(subprocess.run(['php','-l',str(ROOT/php)],capture_output=True).returncode==0,f'{php} PHP syntax')
    check(subprocess.run(['node','--check',str(ROOT/js)],capture_output=True).returncode==0,f'{js} JS syntax')

# Dependency/load-order contracts.
checks={
 'ypologismos-morion-apospasis-evropaika-scholeia.php':('european-schools-calculations.js','european-schools-ui.js'),
 'ypologismos-morion-1gt-2024.php':('asep-te-academic.js','asep-1gt-ui.js'),
 'ypologismos-morion-3ea-2025.php':('asep-pe-academic.js','asep-3ea-ui.js'),
 'ypologismos-morion-2ea-2025.php':('asep-pe-academic.js','asep-2ea-ui.js'),
 'ypologismos-morion-1ea-2025.php':('eae-sensory-proof.js','asep-1ea-ui.js'),
}
for php,(dep,ui) in checks.items():
    s=(ROOT/php).read_text(encoding='utf-8')
    check(dep in s and ui in s and s.index(dep)<s.index(ui),f'{php} dependencies load before UI controller')

# Important interaction hooks remain in the external modules.
key_tokens={
 'assets/tools-directory.js':['searchInput.addEventListener(\'input\', updateCards)','setFilter(button.getAttribute(\'data-filter\') || \'all\'','normalizeGreek'],
 'includes/european-schools-ui.js':["ids.forEach(id=>{const e=$(id);if(e){e.addEventListener('input',calculate)","$('copyBtn').addEventListener('click'","$('resetBtn').addEventListener('click'",'updatePositionUI'],
 'includes/asep-1gt-ui.js':["document.addEventListener('input',calc)","$('copyBtn').addEventListener('click'", "AsepTeAcademic.sync('asepTeAcademic')"],
 'includes/asep-3ea-ui.js':["document.addEventListener('input',render)","$('copyBtn').addEventListener('click'",'AsepPeAcademic.calculate'],
 'includes/asep-2ea-ui.js':["document.addEventListener('input',render)","$('copyBtn').addEventListener('click'",'syncEligibilityUI'],
 'includes/asep-1ea-ui.js':["document.addEventListener('input',render)","$('copyBtn').addEventListener('click'",'EducationCore.bindBoundedNumberInput'],
 'includes/saek-deputy-eligibility-ui.js':["checkEligibilityBtn').addEventListener('click',checkEligibility)","resetBtn').addEventListener('click',resetForm)",'updateProgress();'],
}
for js,tokens in key_tokens.items():
    s=(ROOT/js).read_text(encoding='utf-8')
    for token in tokens:
        check(token in s,f'{js} keeps {token[:48]}')

saek=(ROOT/'dikaioma-ypodiefthynti-saek.php').read_text(encoding='utf-8')
check('id="checkEligibilityBtn"' in saek,'SAEK eligibility action has stable id')
check('id="resetBtn"' in saek,'SAEK reset action has stable id')

# After the staffing extraction there should be no root PHP page with executable inline JS.
remaining=[]
for p in ROOT.glob('*.php'):
    text=p.read_text(encoding='utf-8',errors='ignore')
    if inline_scripts(text): remaining.append(p.name)
check(remaining==[],f'no root PHP page retains executable inline JS ({remaining})')

config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
check("define('EDU_TOOLS_VERSION', '3.20.98');" in config,'asset version bumped to 3.20.98')

print(f'RESULT {PASS} PASS / {FAIL} FAIL')
sys.exit(1 if FAIL else 0)
