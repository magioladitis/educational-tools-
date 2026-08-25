#!/usr/bin/env python3
from pathlib import Path
import re, sys
root=Path(__file__).resolve().parents[1]
css=(root/'assets/common.css').read_text()
ea3=(root/'ypologismos-morion-3ea-2025.php').read_text()
config=(root/'includes/config.php').read_text()
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
check('central cache version constant exists', bool(re.search(r"define\('EDU_TOOLS_VERSION',\s*'[^']+'\)", config)))
for token in ['--edu-result-row-gap: 12px','--edu-result-row-pad-y: 9px','--edu-result-row-separator: #edf1f5','--edu-result-section-gap: 14px']:
    check('token '+token, token in css)
for sel in ['body.edu-ui.edu-calc-standard .result-row','body.edu-ui.edu-calc-ea3 .result-row','body.edu-ui.edu-page-dimos-detachment .result-row','body.edu-ui.edu-page-digital-tutoring .result-row','body.edu-ui.edu-page-sde-apospasis .result-row']:
    check('canonical selector '+sel, sel in css)
check('canonical row uses gap token', 'gap:var(--edu-result-row-gap)' in css)
check('canonical row uses padding token', 'padding:var(--edu-result-row-pad-y) 0' in css)
check('canonical row uses separator token', 'border-top:1px solid var(--edu-result-row-separator)' in css)
check('3EA old result table removed', '<table class="table">' not in ea3)
check('3EA obsolete table CSS removed', 'body.edu-calc-ea3 .table{' not in css)
for rid in ['resAcademic','resService','resSocial']:
    check('3EA keeps '+rid, "'value_id' => '"+rid+"'" in ea3)
check('Dimos old row gap removed', 'body.edu-page-dimos-detachment .result-row {display:flex;' not in css)
check('Dimos action gap canonical', 'body.edu-page-dimos-detachment .actions {display:grid;' in css and 'margin-top:var(--edu-result-section-gap);' in css)
failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print(f'RESULT-SUMMARY R2: {len(checks)-len(failed)}/{len(checks)} PASS')
if failed: sys.exit(1)
