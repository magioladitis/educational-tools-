const fs = require('fs');
const vm = require('vm');
const path = require('path');
const root = path.resolve(__dirname, '..');
const code = fs.readFileSync(path.join(root, 'includes', 'salary-net-calculations.js'), 'utf8');
const context = { window: {} };
vm.createContext(context);
vm.runInContext(code, context);
const S = context.window.EducationSalaryNet;

let checks = 0;
function check(name, actual, expected) {
  if (actual !== expected) throw new Error(`${name}: expected ${expected}, got ${actual}`);
  checks++;
}
function close(name, actual, expected, tolerance = 0.01) {
  if (Math.abs(actual - expected) > tolerance) throw new Error(`${name}: expected ~${expected}, got ${actual}`);
  checks++;
}

check('TEKA status exported', S.INSURED_STATUSES.new_teka.supplementaryFund, 'teka');
check('e-EFKA status exported', S.INSURED_STATUSES.new_efka.supplementaryFund, 'efka');

const common = {
  basicMonthly: 1527,
  familyAllowanceMonthly: 120,
  positionAllowanceMonthly: 195,
  remoteAllowanceMonthly: 100,
  grossMonthly: 1942,
  profile: 'permanent',
  ageGroup: 'over30',
  children: 2
};

const efka = S.calculate({ ...common, insuredStatus: 'new_efka' });
const teka = S.calculate({ ...common, insuredStatus: 'new_teka' });

check('e-EFKA normalized insurance era', efka.insuredStatus, 'new');
check('TEKA normalized insurance era', teka.insuredStatus, 'new');
check('e-EFKA supplementary fund', efka.supplementaryFund, 'efka');
check('TEKA supplementary fund', teka.supplementaryFund, 'teka');

close('same pension/supplementary base', teka.pensionContributionBase, efka.pensionContributionBase);
close('same pension/supplementary amount', teka.deductionComponents[0].amount, efka.deductionComponents[0].amount);
close('same total standard deductions', teka.standardDeductions, efka.standardDeductions);
close('same estimated net', teka.estimatedNet, efka.estimatedNet);

check('TEKA line names TEKA', teka.deductionComponents[0].label.includes('ΤΕΚΑ'), true);
check('TEKA line states 3%', teka.deductionComponents[0].note.includes('3% επικουρική ΤΕΚΑ'), true);
check('e-EFKA line names e-EFKA', efka.deductionComponents[0].note.includes('3% επικουρική e-ΕΦΚΑ'), true);
check('TEKA bases label identifies fund', teka.insuranceBasesLabel.includes('(ΤΕΚΑ)'), true);
check('TEKA status label identifies fund', teka.insuredStatusLabel.includes('ΤΕΚΑ'), true);

// Legacy API compatibility: "new" remains equivalent to the explicit e-EFKA selection.
const legacyNew = S.calculate({ ...common, insuredStatus: 'new' });
close('legacy new same standard deductions', legacyNew.standardDeductions, efka.standardDeductions);
check('legacy new maps to e-EFKA', legacyNew.supplementaryFund, 'efka');

console.log(`Salary TEKA contract: PASS ${checks}/${checks}`);
