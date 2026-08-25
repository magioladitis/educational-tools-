from pathlib import Path
import re
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
PAGES = sorted(ROOT.glob('ypologismos-morion*.php'))
LAYOUT = ROOT / 'includes/components/calculator-layout.php'

passes = 0
fails = 0

def check(name, cond):
    global passes, fails
    if cond:
        print('PASS', name)
        passes += 1
    else:
        print('FAIL', name)
        fails += 1

layout = LAYOUT.read_text()
check('layout defines calculatorResultRow', "function calculatorResultRow" in layout)
check('layout supports subtitle_attrs', "subtitle_attrs" in layout)
check('layout supports cap_attrs', "cap_attrs" in layout)
check('layout result helper presentation-only', 'EducationAcademic' not in layout and 'EducationService' not in layout and 'EducationSocial' not in layout)

source = '\n'.join(p.read_text() for p in PAGES)
section_head_count = len(re.findall(r"['\"]header_variant['\"]\s*=>\s*['\"]section-head['\"]", source))
check('21 shared section-head configs', section_head_count == 21)
check('0 literal section-head blocks', '<div class="section-head">' not in source)
check('18 page action helper calls', source.count('calculatorActions(') == 18)
check('0 literal page action blocks', '<div class="actions">' not in source)
check('at least 77 static result-row helper calls', source.count('calculatorResultRow(') >= 77)
check('only runtime JS result-row literal remains', source.count('<div class="result-row') == 1)
check('runtime JS result-row remains in SDE breakdown', "html += '<div class=\"result-row\"><span>'" in (ROOT/'ypologismos-morion-apospasis-sde.php').read_text())

# Dynamic hooks that must remain present in source configurations.
for hook in ('teachingMax', 'adminMax', 'experienceSubtitle', 'primaryResultLabel', 'criteriaRow', 'interviewRow'):
    check('hook preserved: ' + hook, hook in source)

# Render every calculator and verify IDs stay unique.
for p in PAGES:
    proc = subprocess.run(['php', p.name], cwd=ROOT, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
    check(p.name + ' PHP render', proc.returncode == 0)
    if proc.returncode != 0:
        continue
    doc = html.document_fromstring(proc.stdout)
    ids = doc.xpath('//*[@id]/@id')
    check(p.name + ' no duplicate ids', len(ids) == len(set(ids)))

print(f'RESULT {passes} PASS / {fails} FAIL')
raise SystemExit(1 if fails else 0)
