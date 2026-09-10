from pathlib import Path
root = Path(__file__).resolve().parents[1]
p = root / 'ypologismos-misthologikou-klimakiou.php'
s = p.read_text(encoding='utf-8')
ui = (root / 'includes' / 'salary-ui.js').read_text(encoding='utf-8')
app = s + '\n' + ui
checks = []
def need(label, needle):
    if needle not in app:
        raise AssertionError(f'{label}: missing {needle!r}')
    checks.append(label)
need('print button', "'id' => 'printBtn'")
need('print label', "'label' => 'Εκτύπωση'")
need('print sheet', 'id="payrollPrintSheet"')
need('print content', 'id="payrollPrintContent"')
need('A4 print rule', '@page { size:A4; margin:12mm; }')
need('browser print action', 'window.print();')
need('print render function', 'function renderPrintSheet(result, net, payroll)')
need('gross detail heading', 'ΑΠΟΔΟΧΕΣ')
need('deductions detail heading', 'ΚΡΑΤΗΣΕΙΣ')
need('net print label', 'ΕΚΤΙΜΩΜΕΝΟ ΠΛΗΡΩΤΕΟ')
need('eligibility details panel', "'id' => 'maternityEligibilityPanel'")
need('eligible salaried mothers explanation', 'μητέρες μισθωτές ασφαλισμένες')
need('lochia timing', 'επιδότηση λόγω λοχείας')
need('next-month timing', '1η του επόμενου μήνα του τοκετού')
need('paid leave counts', 'άδεια <strong>με αποδοχές</strong>')
print(f'Salary print/maternity UI contract: PASS {len(checks)}/{len(checks)}')
