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
function close(name, actual, expected, tolerance = 0.01) {
  if (Math.abs(actual - expected) > tolerance) throw new Error(`${name}: expected ~${expected}, got ${actual}`);
  checks++;
}
function check(name, actual, expected) {
  if (actual !== expected) throw new Error(`${name}: expected ${expected}, got ${actual}`);
  checks++;
}

// Golden payroll reference: October 2026, anonymised.
// MK7 basic 1,586 + family allowance (2 children) 120 + remote-area allowance 100 = 1,806 gross.
check('family allowance 0 children', S.familyAllowanceMonthly(0), 0);
check('family allowance 1 child', S.familyAllowanceMonthly(1), 70);
check('family allowance 2 children', S.familyAllowanceMonthly(2), 120);
check('family allowance 3 children', S.familyAllowanceMonthly(3), 170);
check('family allowance 4 children', S.familyAllowanceMonthly(4), 220);
check('family allowance 5 children', S.familyAllowanceMonthly(5), 290);

const basic = 1586;
const family = S.familyAllowanceMonthly(2);
const remote = S.REMOTE_AREA_ALLOWANCE_MONTHLY;
const gross = basic + family + remote;
const result = S.calculate({ grossMonthly: gross, profile: 'permanent', ageGroup: 'over30', children: 2 });

close('golden October gross', gross, 1806, 0.0001);
close('golden October unemployment 2%', gross * 0.02, 36.12, 0.001);
close('golden October MTPY 4.5%', gross * 0.045, 81.27, 0.001);
close('golden October TPDY 4%', gross * 0.04, 72.24, 0.001);
close('golden October health 2.05%', gross * 0.0205, 37.02, 0.01);
close('golden October pension plus supplementary 9.67%', gross * 0.0967, 174.64, 0.01);
close('golden October statutory deductions', result.standardDeductions, 401.29, 0.01);
close('golden October income tax', result.monthlyTax, 81.18, 0.01);
close('golden October paid amount', result.estimatedNet, 1323.53, 0.01);

console.log(`Salary payroll golden Oct-2026: PASS ${checks}/${checks}`);
