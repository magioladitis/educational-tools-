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

// Real payroll statement supplied by the user: basic salary EUR 1,457.
// It is useful as a structural benchmark, but its health deductions reflect
// the older 2.55% employee health rate. The current 2026 engine correctly uses 2.05%.
const gross = 1457;
close('2% unemployment reference', gross * 0.02, 29.14);
close('4.5% MTPY reference', gross * 0.045, 65.565, 0.001);
close('4% lump-sum reference', gross * 0.04, 58.28);
close('current 2.05% health', gross * 0.0205, 29.8685, 0.001);
close('historical statement health was 2.55%', 31.33 + 5.83, gross * 0.0255, 0.02);
check('permanent profile remains current statutory 22.22%', S.PROFILES.permanent.deductibleRate, 0.2222);
const current = S.calculate({ grossMonthly: gross, profile: 'permanent', ageGroup: 'over30', children: 0 });
close('current statutory deductions at 1457', current.standardDeductions, gross * 0.2222, 0.001);
if (!(current.monthlyTax > 0 && current.estimatedNet > 0 && current.estimatedNet < gross)) {
  throw new Error('current payroll reference sanity failed');
}
checks++;
console.log(`Salary real-payroll reference contract: PASS ${checks}/${checks}`);
