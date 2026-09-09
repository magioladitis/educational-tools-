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

check('remote-area allowance monthly amount', S.REMOTE_AREA_ALLOWANCE_MONTHLY, 100);
check('permanent standard rate', S.PROFILES.permanent.deductibleRate, 0.2222);
check('new appointee standard rate', S.PROFILES.newly_appointed.deductibleRate, 0.2222);
check('permanent breakdown includes components', S.PROFILES.permanent.deductionBreakdown, 'Σύνταξη 6,67% · Υγεία 2,05% · Επικουρική 3% · Εφάπαξ 4% · ΜΤΠΥ 4,5% · Ανεργία 2%');
close('new appointee MTPY registration monthly rate', S.PROFILES.newly_appointed.extraCashRate, 1 / 12, 1e-12);
check('substitute KPK 101 rate', S.PROFILES.substitute.deductibleRate, 0.1337);

check('base tax 15k no children', S.grossAnnualTax(15000, 'over30', 0), 1900);
check('base tax 15k one child', S.grossAnnualTax(15000, 'over30', 1), 1800);
check('up to 25 first 20k tax free', S.grossAnnualTax(20000, 'upTo25', 0), 0);
check('age 26-30 second bracket 9%', S.grossAnnualTax(20000, 'age26to30', 0), 1800);
check('four children first 20k tax free', S.grossAnnualTax(20000, 'over30', 4), 0);
close('six children third bracket 14%', S.grossAnnualTax(25000, 'over30', 6), 700);

check('tax credit no children', S.baseTaxCredit(0), 777);
check('tax credit one child', S.baseTaxCredit(1), 900);
check('tax credit two children', S.baseTaxCredit(2), 1120);
check('tax credit three children', S.baseTaxCredit(3), 1340);
check('tax credit four children', S.baseTaxCredit(4), 1580);
check('tax credit five children', S.baseTaxCredit(5), 1780);
check('tax credit six children', S.baseTaxCredit(6), 2000);
check('credit taper at 20k no children', S.taxCredit(20000, 0, 99999), 617);
check('five-child credit does not taper', S.taxCredit(40000, 5, 99999), 1780);

const peMk2 = S.calculate({ grossMonthly: 1291, profile: 'permanent', ageGroup: 'over30', children: 0 });
close('PE MK2 standard deductions', peMk2.standardDeductions, 286.8602, 0.0001);
close('PE MK2 estimated net sanity check', peMk2.estimatedNet, 959.6457, 0.01);
const newHire = S.calculate({ grossMonthly: 1291, profile: 'newly_appointed', ageGroup: 'over30', children: 0 });
close('new hire tax basis same as permanent', newHire.taxableAnnual, peMk2.taxableAnnual, 0.0001);
close('new hire registration installment', newHire.registrationDeduction, 1291 / 12, 0.0001);
close('new hire net lower by registration installment', peMk2.estimatedNet - newHire.estimatedNet, 1291 / 12, 0.0001);
const substitute = S.calculate({ grossMonthly: 1291, profile: 'substitute', ageGroup: 'over30', children: 0 });
close('substitute deductions use 13.37%', substitute.standardDeductions, 1291 * 0.1337, 0.0001);
check('invalid profile falls back to permanent', S.calculate({ grossMonthly: 1000, profile: 'x' }).profile, 'permanent');
check('children capped at 20', S.calculate({ grossMonthly: 1000, children: 999 }).children, 20);
const baseOnly = S.calculate({ grossMonthly: 1232, profile: 'permanent', ageGroup: 'over30', children: 0 });
const withRemote = S.calculate({ grossMonthly: 1232 + S.REMOTE_AREA_ALLOWANCE_MONTHLY, profile: 'permanent', ageGroup: 'over30', children: 0 });
close('remote allowance enters deduction base', withRemote.standardDeductions - baseOnly.standardDeductions, 100 * 0.2222, 0.0001);
if (!(withRemote.estimatedNet > baseOnly.estimatedNet && withRemote.estimatedNet - baseOnly.estimatedNet < 100)) throw new Error('remote allowance net impact sanity failed');
checks++;

console.log(`Salary net/tax 2026 contract: PASS ${checks}/${checks}`);
