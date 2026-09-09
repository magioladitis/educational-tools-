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
  if (Math.abs(actual - expected) > tolerance) {
    throw new Error(`${name}: expected ~${expected}, got ${actual}`);
  }
  checks += 1;
}
function check(name, actual, expected) {
  if (actual !== expected) {
    throw new Error(`${name}: expected ${expected}, got ${actual}`);
  }
  checks += 1;
}

/*
 * Golden payroll reference — September 2026 (anonymised).
 * Real public-school payroll statement supplied by the user.
 *
 * Reference facts used by this test only:
 *   PE MK7 basic gross salary: EUR 1,586.00
 *   Statutory employee deductions represented by the permanent profile: EUR 352.41
 *   Monthly income-tax withholding: EUR 94.97
 *   Small payroll-specific deductions outside the core engine:
 *     ADEDY EUR 0.25 + federation EUR 1.00 + local association EUR 0.65 = EUR 1.90
 *   Actual paid amount: EUR 1,136.72
 *
 * No personal identifiers from the source statement are stored in this test.
 */
const gross = 1586;
const payrollSpecificDeductions = 0.25 + 1.00 + 0.65;
const result = S.calculate({
  grossMonthly: gross,
  profile: 'permanent',
  ageGroup: 'over30',
  children: 0,
  otherDeductions: payrollSpecificDeductions
});

check('golden profile', result.profile, 'permanent');
close('golden gross salary', result.grossMonthly, 1586, 0.0001);
close('golden statutory deductions', result.standardDeductions, 352.41, 0.01);
close('golden monthly taxable before tax', result.taxableMonthly, 1233.59, 0.01);
close('golden monthly income tax', result.monthlyTax, 94.97, 0.01);
close('golden net before payroll-specific small deductions', result.netBeforeOtherDeductions, 1138.62, 0.01);
close('golden payroll-specific deductions', result.otherDeductions, 1.90, 0.0001);
close('golden actual paid amount reconciliation', result.estimatedNet, 1136.72, 0.01);

// Component checks visible on the real statement.
close('golden unemployment 2%', gross * 0.02, 31.72, 0.001);
close('golden MTPY 4.5%', gross * 0.045, 71.37, 0.001);
close('golden TPDY/lump-sum 4%', gross * 0.04, 63.44, 0.001);
close('golden health 2.05%', gross * 0.0205, 32.51, 0.01);
close('golden pension plus supplementary 9.67%', gross * 0.0967, 153.37, 0.01);
close('golden component sum',
  gross * (0.02 + 0.045 + 0.04 + 0.0205 + 0.0967),
  352.41,
  0.01
);

console.log(`Salary payroll golden Sep-2026: PASS ${checks}/${checks}`);
