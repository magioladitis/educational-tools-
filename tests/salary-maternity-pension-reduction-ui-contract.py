from pathlib import Path
root=Path(__file__).resolve().parents[1]
page=(root/'ypologismos-misthologikou-klimakiou.php').read_text()
net=(root/'includes/salary-net-calculations.js').read_text()
checks=0

def check(name, cond):
    global checks
    if not cond:
        raise AssertionError(name)
    checks += 1

check('maternity checkbox exists', 'id="maternityPensionReduction"' in page)
check('maternity checkbox is optional manual control', 'Μειωμένη εισφορά κύριας σύνταξης λόγω μητρότητας' in page and 'Επίλεξέ το μόνο όταν' in page)
check('maternity result row exists', "'value_id' => 'maternityPensionReductionResult'" in page)
check('calculator passes maternity flag', "maternityPensionReduction: byId('maternityPensionReduction').checked" in page)
check('reset clears maternity flag', "byId('maternityPensionReduction').checked = false" in page)
check('3.335 percentage-point explanation', '3,335' in page and '6,67%' in page)
check('e-EFKA source linked', 'meiomenes-eisphores-gia-meteres-misthotes' in page)
check('engine exports maternity reduction rate', 'MATERNITY_MAIN_PENSION_REDUCTION_RATE' in net)
check('engine reduces standard deductions not gross', 'baseStandardDeductions - maternityReduction' in net and 'MATERNITY_MAIN_PENSION_REDUCTION_RATE' in net)
check('tax basis uses reduced actual deductions', 'gross - standardDeductionsExact' in net)
print(f'Salary maternity UI contract: PASS {checks}/{checks}')
