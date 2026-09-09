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
function close(name, actual, expected, tolerance = 0.001) {
  if (Math.abs(actual - expected) > tolerance) throw new Error(`${name}: expected ~${expected}, got ${actual}`);
  checks++;
}

check('money half-up 78.615 -> 78.62', S.roundMoney(78.615), 78.62);
check('permanent payroll has 6 real withholding lines', S.PERMANENT_DEDUCTION_COMPONENTS.length, 6);

// External spreadsheet benchmark: PE MK9 + 2 children + remote-area allowance.
// Gross: 1,704 + 120 + 100 = 1,924. The one-cent reconciliation depends on
// rounding each payroll withholding line before summing.
const gross = 1924;
const r = S.calculate({ grossMonthly: gross, profile: 'permanent', ageGroup: 'over30', children: 2 });
close('line-rounded statutory deductions', r.standardDeductions, 427.52);
close('EFKA pension+supplementary 9.67%', r.deductionComponents[0].amount, 186.05);
close('health in kind 1.65%', r.deductionComponents[1].amount, 31.75);
close('health cash 0.40%', r.deductionComponents[2].amount, 7.70);
close('lump sum 4%', r.deductionComponents[3].amount, 76.96);
close('MTPY 4.5%', r.deductionComponents[4].amount, 86.58);
close('unemployment 2%', r.deductionComponents[5].amount, 38.48);
close('monthly tax', r.monthlyTax, 97.70);
close('external benchmark net', r.estimatedNet, 1398.78);

console.log(`Salary line-rounding contract: PASS ${checks}/${checks}`);
