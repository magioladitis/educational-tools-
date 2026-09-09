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

check('disability none option exists', !!S.DISABILITY_TAX_TREATMENTS.none, true);
check('disability 67-79 option exists', !!S.DISABILITY_TAX_TREATMENTS.disability67_79, true);
check('disability 80+ option exists', !!S.DISABILITY_TAX_TREATMENTS.disability80plus, true);
check('67-79 annual reduction', S.DISABILITY_TAX_TREATMENTS.disability67_79.annualReduction, 200);
check('80+ salary tax exemption flag', S.DISABILITY_TAX_TREATMENTS.disability80plus.salaryTaxExempt, true);

const base = S.calculate({ grossMonthly: 1704, profile: 'permanent', ageGroup: 'over30', children: 0 });
const d67 = S.calculate({ grossMonthly: 1704, profile: 'permanent', ageGroup: 'over30', children: 0, disabilityTaxTreatment: 'disability67_79' });
const d80 = S.calculate({ grossMonthly: 1704, profile: 'permanent', ageGroup: 'over30', children: 0, disabilityTaxTreatment: 'disability80plus' });

close('67-79 keeps insurance deductions unchanged', d67.standardDeductions, base.standardDeductions, 1e-12);
close('67-79 applies 200 annual relief', d67.disabilityTaxRelief, 200, 1e-12);
close('67-79 annual tax lower by 200', base.annualTax - d67.annualTax, 200, 1e-9);
check('67-79 treatment returned', d67.disabilityTaxTreatment, 'disability67_79');

close('80+ keeps insurance deductions unchanged', d80.standardDeductions, base.standardDeductions, 1e-12);
close('80+ annual tax zero', d80.annualTax, 0, 1e-12);
close('80+ monthly withholding zero', d80.monthlyTax, 0, 1e-12);
close('80+ relief equals remaining tax after article 16', d80.disabilityTaxRelief, base.annualTax, 1e-9);
check('80+ exemption flag returned', d80.salaryTaxExemptDueToDisability, true);
close('80+ net rises only by removed monthly tax', d80.estimatedNet - base.estimatedNet, base.monthlyTax, 0.001);

const lowTax = S.calculate({ grossMonthly: 700, profile: 'substitute', ageGroup: 'upTo25', children: 0, disabilityTaxTreatment: 'disability67_79' });
close('67-79 relief cannot create negative tax', lowTax.disabilityTaxRelief, 0, 1e-12);
close('67-79 low-tax annual tax stays zero', lowTax.annualTax, 0, 1e-12);

const invalid = S.calculate({ grossMonthly: 1704, disabilityTaxTreatment: 'invalid' });
check('invalid disability option falls back to none', invalid.disabilityTaxTreatment, 'none');

console.log(`Salary disability tax contract: PASS ${checks}/${checks}`);
