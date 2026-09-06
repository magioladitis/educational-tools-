#!/usr/bin/env python3
from pathlib import Path
import re
import tinycss2

ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
CSS=ROOT/'assets/staffing-simulator.css'
text=PAGE.read_text(encoding='utf-8')
css=CSS.read_text(encoding='utf-8')
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

rules=tinycss2.parse_stylesheet(css,skip_comments=True,skip_whitespace=True)
check('dedicated staffing stylesheet exists', CSS.is_file())
check('page links dedicated staffing stylesheet through asset helper', "edu_asset_url('assets/staffing-simulator.css')" in text)
check('staffing page no longer contains inline style block', '<style>' not in text and '</style>' not in text)
check('staffing stylesheet parses without top-level errors', not any(getattr(r,'type','')=='error' for r in rules))
check('staffing stylesheet remains page scoped', '.edu-page-staffing-simulator ' in css)
check('compact tab 2 layout retained', '#staffingMatrixTable{width:auto;min-width:0}' in css)
check('compact tab 6 layout retained', '.specialty-balance-table{min-width:0;table-layout:fixed}' in css)
check('print rules moved with page stylesheet', '@media print' in css and '@page{size:A4 landscape' in css)
check('shared result rows use calculator helper', text.count('calculatorResultRow(array(') >= 17)
check('raw sidebar result-row markup removed', '<div class="result-row"><span>' not in text)
check('basic section fields use one local renderer', 'function staffingUiRenderBasicSectionFields' in text and text.count('staffingUiRenderBasicSectionFields(\'')==2)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
