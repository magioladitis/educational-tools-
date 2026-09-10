#!/usr/bin/env python3
from pathlib import Path
import sys
root=Path(__file__).resolve().parents[1]
page=(root/'ypologismos-misthologikou-klimakiou.php').read_text()
ui=(root/'includes/salary-ui.js').read_text()
app=page+'\n'+ui
net=(root/'includes/salary-net-calculations.js').read_text()
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
check('monthly statutory amount', 'REMOTE_AREA_ALLOWANCE_MONTHLY = 100' in net)
check('optional checkbox', 'id="remoteAreaAllowance" type="checkbox"' in page)
check('eligibility warning', 'Επίλεξέ το μόνο αν υπηρετείς σε περιοχή/μονάδα' in page)
check('included in gross', 'grossForNet = result.basicGrossSalary + familyAllowance + positionAllowance + remoteAllowance' in app)
check('gross passed to net engine', 'grossMonthly: grossForNet' in app)
check('separate result row', 'remoteAllowanceResult' in page)
check('gross subtotal result', 'grossForNetResult' in page)
check('reset clears checkbox', "byId('remoteAreaAllowance').checked = false" in app)
check('official minedu source', 'minedu.gov.gr/site/18335-26-02-16-epidoma-apomakrysmenon-paramethorion-perioxon' in page)
check('MTPY source', 'epidomata-neon-asfalismenon-yper-mtpy' in page)
failed=[n for n,v in checks if not v]
for n,v in checks: print(('PASS' if v else 'FAIL')+': '+n)
print(f'SALARY REMOTE AREA ALLOWANCE: {len(checks)-len(failed)}/{len(checks)} PASS')
sys.exit(1 if failed else 0)
