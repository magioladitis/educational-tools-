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
check('position selector', 'id="positionAllowance"' in page)
check('no position default', '<option value="none">Χωρίς θέση ευθύνης</option>' in page)
check('lyceum amounts', '— 429 €' in page and '— 501 €' in page)
check('gymnasium amounts', '— 358 €' in page and 'gymnasium_director_large' in page)
check('vice director 195', 'vice_director' in page and '— 195 €' in page)
check('small school head 215', 'small_school_head' in page and '— 215 €' in page)
check('gross includes position', 'result.basicGrossSalary + familyAllowance + positionAllowance + remoteAllowance' in app)
check('separate result row', 'positionAllowanceResult' in page)
check('reset clears position', "byId('positionAllowance').value = 'none'" in app)
check('single-position rule note', 'Σε συρροή θέσεων επιλέγεται μόνο η ανώτερη θέση' in page)
check('30 percent legal note', 'αυξήθηκαν κατά <strong>30%</strong> από 01-01-2024' in page)
check('2023 circular source', '2/97758/ΔΕΠ/19-10-2023' in page)
check('GSIS source', '27-5-2024_Odigies_Simplirosis_e-DAYK.pdf' in page)
check('tax article disambiguated', 'Μείωση φόρου άρθρου 16 ΚΦΕ' in page)
check('engine exported lookup', 'positionAllowanceMonthly: positionAllowanceMonthly' in net)
failed=[n for n,v in checks if not v]
for n,v in checks: print(('PASS' if v else 'FAIL')+': '+n)
print(f'SALARY POSITION ALLOWANCE UI: {len(checks)-len(failed)}/{len(checks)} PASS')
sys.exit(1 if failed else 0)
