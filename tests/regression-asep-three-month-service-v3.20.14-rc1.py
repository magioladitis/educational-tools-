#!/usr/bin/env python3
from pathlib import Path
from html.parser import HTMLParser
import subprocess, sys, re

root=Path(sys.argv[1] if len(sys.argv)>1 else '.').resolve()
component=root/'includes/components/asep-three-month-service.php'
service_js=root/'includes/service-calculations.js'

targets={
    'ypologismos-morion.php': ('threeMonthMonths2020','threeMonthDifficultMonths2020','threeMonthMonths2021','threeMonthDifficultMonths2021'),
    'ypologismos-morion-1gt-2024.php': ('covid20Regular','covid20Difficult','covid21Regular','covid21Difficult'),
    'ypologismos-morion-1ea-2025.php': ('covid2020','covidHard2020','covid2021','covidHard2021'),
    'ypologismos-morion-2ea-2025.php': ('covid2020','covidHard2020','covid2021','covidHard2021'),
    'ypologismos-morion-3ea-2025.php': ('covid2020Months','covidHard2020Months','covid2021Months','covidHard2021Months'),
    'ypologismos-morion-4ea-2025.php': ('covid20Regular','covid20Difficult','covid21Regular','covid21Difficult'),
}

passed=0; failed=0

def check(cond, name):
    global passed, failed
    if cond:
        print('✓',name); passed+=1
    else:
        print('✗',name); failed+=1

class P(HTMLParser):
    def __init__(self):
        super().__init__(); self.inputs={}; self.data_components=0
    def handle_starttag(self, tag, attrs):
        d=dict(attrs)
        if d.get('data-component')=='asep-three-month-service': self.data_components+=1
        if tag=='input' and d.get('id'): self.inputs[d['id']]=d

ct=component.read_text(encoding='utf-8')
check('function renderAsepThreeMonthService' in ct,'component renderer exists')
check('max="8"' in ct and 'max="7"' in ct,'component exposes historical month limits')
check('έως 8 μήνες' in ct and 'έως 7 μήνες' in ct,'component labels expose 8/7-month limits')
check('έως 10 μόρια' in ct and 'έως 20 μόρια' in ct,'component labels expose annual point caps')
for forbidden in ['300', '400', 'ΕΑΕ', 'ΣΔΕ', 'Ωνάσει']:
    check(forbidden not in ct, f'component does not mix unrelated rule: {forbidden}')

for name,ids in targets.items():
    text=(root/name).read_text(encoding='utf-8')
    check('asep-three-month-service.php' in text,f'{name}: requires shared component')
    check('renderAsepThreeMonthService' in text,f'{name}: renders shared component')
    r=subprocess.run(['php',str(root/name)],cwd=root,capture_output=True,text=True)
    check(r.returncode==0,f'{name}: PHP render succeeds')
    p=P(); p.feed(r.stdout)
    check(p.data_components==1,f'{name}: exactly one three-month component rendered')
    expected=(8,8,7,7)
    for field,maxv in zip(ids,expected):
        a=p.inputs.get(field,{})
        check(a.get('max')==str(maxv),f'{name}: {field} max={maxv}')
        check(a.get('step')=='1',f'{name}: {field} integer step')

for name in ['ypologismos-morion-1ea-2025.php','ypologismos-morion-2ea-2025.php']:
    text=(root/name).read_text(encoding='utf-8')
    check('const tri=' not in text,f'{name}: old local trimester calculator removed')
    for fn in ['threeMonthRegular2020','threeMonthRegular2021','threeMonthDifficult2020','threeMonthDifficult2021']:
        check(('EducationService.'+fn) in text,f'{name}: uses shared {fn}')

# Direct calculation lock: excessive input must clamp both months and points.
node=r'''
const fs=require('fs'),vm=require('vm'); const c={}; c.window=c; vm.createContext(c);
vm.runInContext(fs.readFileSync(process.argv[1],'utf8'),c);
const s=c.EducationService;
const got=[s.threeMonthRegular2020(99),s.threeMonthDifficult2020(99),s.threeMonthRegular2021(99),s.threeMonthDifficult2021(99)];
const exp=[[8,10],[8,20],[7,10],[7,20]];
for(let i=0;i<got.length;i++){ if(got[i].months!==exp[i][0]||got[i].points!==exp[i][1]) process.exit(2); }
'''
r=subprocess.run(['node','-e',node,str(service_js)],capture_output=True,text=True)
check(r.returncode==0,'shared JS clamps 2020/21 months and annual points')

# Scope lock: the component belongs only to the six ASEP proclamation calculators.
for pth in root.glob('*.php'):
    if pth.name in targets: continue
    text=pth.read_text(encoding='utf-8')
    check('asep-three-month-service.php' not in text,f'{pth.name}: remains outside ASEP trimester component')

print(f'\nASEP three-month component: PASS {passed} / FAIL {failed}')
sys.exit(1 if failed else 0)
