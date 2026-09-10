from pathlib import Path
root = Path(__file__).resolve().parents[1]
page = (root / 'ypologismos-misthologikou-klimakiou.php').read_text(encoding='utf-8')
ui = (root / 'includes' / 'salary-ui.js').read_text(encoding='utf-8')
app = page + '\n' + ui
net = (root / 'includes' / 'salary-net-calculations.js').read_text(encoding='utf-8')
checks = 0

def check(name, condition):
    global checks
    if not condition:
        raise AssertionError(name)
    checks += 1

check('insured status select', 'id="insuredStatus"' in page)
check('new insured e-EFKA option', 'value="new_efka">Νέος ασφαλισμένος — από 01/01/1993 · επικουρική e-ΕΦΚΑ' in page)
check('new insured TEKA option', 'value="new_teka">Νέος ασφαλισμένος — από 01/01/1993 · επικουρική ΤΕΚΑ' in page)
check('old insured option', 'value="old">Παλαιός ασφαλισμένος — έως 31/12/1992' in page)
check('first-insurance help', 'πρώτη ασφάλιση για κύρια σύνταξη' in page and 'όχι η ημερομηνία διορισμού' in page)
check('insured status details', "'id' => 'insuredStatusInfoPanel'" in page)
check('old TPDY explanation', 'εισφορά ΤΠΔΥ 4% στον <strong>βασικό μισθό</strong>' in page)
check('new pensionable allowances explanation', 'βασικός μισθός + οικογενειακή παροχή + θέση ευθύνης + παραμεθόριο' in page)
check('result insured status row', "'value_id' => 'insuredStatusResult'" in page)
check('result insurance bases row', "'value_id' => 'insuranceBasesResult'" in page)
check('detailed basic passed to engine', 'basicMonthly: result.basicGrossSalary' in app)
check('detailed family passed to engine', 'familyAllowanceMonthly: familyAllowance' in app)
check('insured status passed to engine', "insuredStatus: byId('insuredStatus').value" in app)
check('substitute disables status selector', 'insuredStatusSelect.disabled = !net.insuredStatusApplies' in app)
check('reset restores new e-EFKA status', "byId('insuredStatus').value = 'new_efka'" in app)
check('print includes insured status', '<strong>Ασφαλιστική ιδιότητα:</strong>' in app)
check('print includes contribution bases', '<strong>Βάσεις εισφορών:</strong>' in app)
check('print deduction lines show base', "' · βάση ' + formatEuroCents(component.base)" in app)
check('maternity print uses applicable base', 'maternityPensionContributionBase' in app)
check('engine has old/new statuses', 'const INSURED_STATUSES' in net)
check('engine old TPDY base is basic', 'const lumpSumBase = oldInsured ? earnings.basic : gross' in net)
check('engine old pension base is basic plus position', 'const pensionBase = oldInsured ? basicAndPosition : gross' in net)
check('engine old MTPY mixed base', 'mtpyPrimaryBase * 0.045 + mtpyReducedBase * 0.01' in net)
check('official e-EFKA circular linked', 'Εγκ. 10/2020' in page and 'e-efka.gov.gr' in page)
check('official MTPY Diavgeia circular linked', 'Ψ55ΘΗ-Φ0Α' in page and 'diavgeia.gov.gr/doc/' in page)
check('official MTPY guide linked', 'ODIGOS_KRATISEON_IOYNIOS_2020' in page)
check('official TEKA source linked', 'https://teka.gov.gr/ergodotes' in page and 'ίδιο ποσοστό και ίδια βάση' in page)
check('article 31 linked', '4670/2020/arthro/31' in page)
print(f'Salary old/new insured UI contract: PASS {checks}/{checks}')
