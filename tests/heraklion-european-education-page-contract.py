#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys
ROOT=Path(__file__).resolve().parents[1]
page=(ROOT/'ypologismos-morion-scholeio-evropaikis-paideias-irakleiou.php').read_text(encoding='utf-8')
catalog=(ROOT/'includes'/'tools-catalog.php').read_text(encoding='utf-8')
calc=(ROOT/'includes'/'heraklion-european-education-calculations.js').read_text(encoding='utf-8')
ui=(ROOT/'includes'/'heraklion-european-education-ui.js').read_text(encoding='utf-8')
checks=[]
def check(name,ok):
    checks.append(bool(ok)); print(('PASS' if ok else 'FAIL')+': '+name)
check('page uses shared common stylesheet', "edu_asset_url('assets/common.css')" in page)
check('page uses shared calculator layout', 'includes/components/calculator-layout.php' in page)
check('page uses separate calculation and UI modules', 'heraklion-european-education-calculations.js' in page and 'heraklion-european-education-ui.js' in page)
check('no inline script block', '<script>' not in page)
check('official invitation is linked', '69163/Η2/28-05-2026' in page and 'minedu.gov.gr/publications/docs2023/' in page)
check('score maxima are visible', all(x in page for x in ['/ 45','/ 25','/ 30','/ 70']) and 'Σύνολο: 100' in page)
check('same-subject master guard exists', 'relevantMasterSameSubjectAsPhd' in page and 'otherMasterSameSubjectAsPhd' in page and 'Suppressed' in calc)
check('three-language cap exists', 'max="3"' in page and 'Math.min(3' in calc)
check('five-year cap exists', 'max="5"' in page and 'Math.min(5' in calc)
check('non-native separate route exists', 'non_native' in page and 'χωριστό πίνακα' in calc)
check('catalog contains tool #35', "'number' => 35" in catalog and 'ypologismos-morion-scholeio-evropaikis-paideias-irakleiou.php' in catalog)
nums=[int(x) for x in re.findall(r"'number'\s*=>\s*(\d+)",catalog)]
check('catalog numbers remain sequential', nums==list(range(1,len(nums)+1)))
ids=re.findall(r'\bid="([^"]+)"',page)
check('no duplicate literal ids', len(ids)==len(set(ids)))
proc=subprocess.run(['php','-l',str(ROOT/'ypologismos-morion-scholeio-evropaikis-paideias-irakleiou.php')],capture_output=True,text=True)
check('PHP lint passes',proc.returncode==0)
render=subprocess.run(['php',str(ROOT/'ypologismos-morion-scholeio-evropaikis-paideias-irakleiou.php')],cwd=ROOT,capture_output=True,text=True)
check('page renders under PHP CLI',render.returncode==0 and 'Μόρια — Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου' in render.stdout)
print(f'RESULT: {sum(checks)}/{len(checks)} PASS')
sys.exit(0 if all(checks) else 1)
