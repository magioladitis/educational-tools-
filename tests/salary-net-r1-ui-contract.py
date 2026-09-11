#!/usr/bin/env python3
from pathlib import Path
import sys
root=Path(__file__).resolve().parents[1]
page=(root/'ypologismos-misthologikou-klimakiou.php').read_text()
ui=(root/'includes/salary-ui.js').read_text()
app=page+'\n'+ui
config=(root/'includes/config.php').read_text()
net=(root/'includes/salary-net-calculations.js').read_text()
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
check('live calculator has no redundant calculate button', 'calculateBtn' not in page and "'id' => 'resetBtn'" in page)
check('keeps current calculator layout', "calculatorColumnsStart" in page and "calculatorMainStart" in page and "calculatorResultsStart" in page)
check('optional net section', 'Προαιρετική εκτίμηση καθαρών αποδοχών' in page)
check('payroll profile select', 'id="payrollProfile"' in page and 'newly_appointed' in page and 'substitute' in page)
check('2026 age groups', 'id="ageGroup"' in page and '26–30 ετών' in page and 'Έως 25 ετών' in page)
check('dependent children input', 'id="dependentChildren"' in page and 'max="20"' in page)
check('basic gross remains primary salary row', 'basicSalaryResult' in page)
check('net result subtotal', 'estimatedNetResult' in page and 'Εκτιμώμενο καθαρό' in page)
check('tax detail rows', all(x in page for x in ['taxableAnnualResult','taxBeforeCreditResult','taxCreditResult','annualTaxResult','monthlyTaxResult']))
check('deduction rows', 'standardDeductionsResult' in page and 'deductionBreakdownResult' in page and 'registrationDeductionResult' in page)
check('net engine loaded', 'includes/salary-net-calculations.js' in page)
check('net engine called from total gross', 'EducationSalaryNet.calculate' in app and 'grossMonthly: grossForNet' in app)
check('gross basis disclaimer', 'βασικό μισθό του Μ.Κ.' in page and 'οικογενειακή παροχή' in page and 'επίδομα απομακρυσμένων - παραμεθορίων περιοχών' in page)
check('2026 tax source', 'Φορολογία εισοδήματος 2026' in page and 'Ο.3068/2025' in page)
check('contribution source', 'Υπουργείο Εργασίας — Ασφαλιστικές εισφορές' in page)
check('MTPY source', 'ΜΤΠΥ — Επικαιροποιημένος Οδηγός Κρατήσεων' in page)
check('tax engine defines profiles', all(x in net for x in ['permanent:', 'newly_appointed:', 'substitute:']))
check('tax engine has 2026 bracket cutoffs', all(x in net for x in ['to: 10000','to: 20000','to: 30000','to: 40000','to: 60000']))
check('asset version bumped', "define('EDU_TOOLS_VERSION', '3.20.95')" in config)
check('remote allowance checkbox', 'id="remoteAreaAllowance"' in page and '+100 € μικτά / μήνα' in page)
check('remote allowance result rows', 'remoteAllowanceResult' in page and 'grossForNetResult' in page)
check('remote allowance source', 'ΥΠΑΙΘ — Επίδομα απομακρυσμένων / παραμεθορίων 100 €' in page and 'ΓΓΠΣ — Κράτηση ΜΤΠΥ' in page)
failed=[n for n,v in checks if not v]
for n,v in checks: print(('PASS' if v else 'FAIL')+': '+n)
print(f'SALARY NET R1 UI: {len(checks)-len(failed)}/{len(checks)} PASS')
sys.exit(1 if failed else 0)
