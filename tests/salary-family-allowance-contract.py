#!/usr/bin/env python3
from pathlib import Path
import sys
root=Path(__file__).resolve().parents[1]
page=(root/'ypologismos-misthologikou-klimakiou.php').read_text()
net=(root/'includes/salary-net-calculations.js').read_text()
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
check('family allowance function', 'function familyAllowanceMonthly(children)' in net)
check('one child 70', 'if (c === 1) return 70;' in net)
check('two children 120', 'if (c === 2) return 120;' in net)
check('three children 170', 'if (c === 3) return 170;' in net)
check('four children 220', 'if (c === 4) return 220;' in net)
check('additional child 70', '220 + (c - 4) * 70' in net)
check('family allowance result row', 'familyAllowanceResult' in page)
check('gross includes family allowance', 'result.basicGrossSalary + familyAllowance + remoteAllowance' in page)
check('children reused for tax', 'children: children' in page)
check('legal source note', 'άρθρο 15 του ν. 4354/2015' in page and 'ν. 5045/2023' in page)
failed=[n for n,v in checks if not v]
for n,v in checks: print(('PASS' if v else 'FAIL')+': '+n)
print(f'SALARY FAMILY ALLOWANCE: {len(checks)-len(failed)}/{len(checks)} PASS')
sys.exit(1 if failed else 0)
