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

// e-EFKA: 50% reduction of the 6.67% employee main-pension contribution.
check('maternity reduction rate', S.MATERNITY_MAIN_PENSION_REDUCTION_RATE, 0.03335);

const julyGross = 1527 + S.familyAllowanceMonthly(1) + S.REMOTE_AREA_ALLOWANCE_MONTHLY;
const july = S.calculate({ grossMonthly: julyGross, profile: 'permanent', ageGroup: 'over30', children: 1 });
close('July reference gross', julyGross, 1697, 0.0001);
close('July full standard rate', july.standardDeductionRate, 0.2222, 1e-12);
close('July no maternity reduction', july.maternityPensionReductionAmount, 0, 1e-12);
close('July payroll tax reference', july.monthlyTax, 93.99, 0.01);

// August 2026 real statement: 1,747 gross and an explicit "Επιδότηση ΙΚΑ Νέας Μητέρας -58,26".
const augustGross = 1747;
const august = S.calculate({
  grossMonthly: augustGross,
  profile: 'permanent',
  ageGroup: 'over30',
  children: 1, // observed withholding behavior in this statement; family allowance is tested separately
  maternityPensionReduction: true
});
close('August effective standard rate', august.standardDeductionRate, 0.18885, 1e-12);
close('August maternity pension reduction amount', august.maternityPensionReductionAmount, 58.26, 0.01);
close('August reduced standard deductions', august.standardDeductions, 329.93, 0.001); // payroll rounds components line-by-line
close('August payroll tax reference', august.monthlyTax, 113.42, 0.01);
close('August paid amount reference', august.estimatedNet, 1303.65, 0.001);
check('maternity flag returned', august.maternityPensionReduction, true);
check('maternity breakdown disclosed', august.deductionBreakdown.includes('Μειωμένη κύρια σύνταξη μητρότητας 50%'), true);

const substitute = S.calculate({ grossMonthly: 1520, profile: 'substitute', ageGroup: 'age26to30', children: 0, maternityPensionReduction: true });
close('maternity reduction applies only to main-pension slice for substitute profile too', substitute.standardDeductionRate, 0.10035, 1e-12);

console.log(`Salary maternity pension reduction contract: PASS ${checks}/${checks}`);
