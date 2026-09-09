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

check('none', S.positionAllowanceMonthly('none'), 0);
check('regional director', S.positionAllowanceMonthly('regional_director'), 1170);
check('regional quality supervisor', S.positionAllowanceMonthly('regional_quality_supervisor'), 780);
check('education director', S.positionAllowanceMonthly('education_director'), 715);
check('quality supervisor', S.positionAllowanceMonthly('quality_supervisor'), 650);
check('education counselor', S.positionAllowanceMonthly('education_counselor'), 455);
check('KEDASY head', S.positionAllowanceMonthly('kedasy_head'), 455);
check('lyceum director', S.positionAllowanceMonthly('lyceum_director'), 429);
check('large lyceum director', S.positionAllowanceMonthly('lyceum_director_large'), 501);
check('education matters head', S.positionAllowanceMonthly('education_matters_head'), 390);
check('gymnasium director', S.positionAllowanceMonthly('gymnasium_director'), 358);
check('large gymnasium director', S.positionAllowanceMonthly('gymnasium_director_large'), 429);
check('vice director', S.positionAllowanceMonthly('vice_director'), 195);
check('small school head', S.positionAllowanceMonthly('small_school_head'), 215);
check('unknown is safe', S.positionAllowanceMonthly('unknown'), 0);

// Sanity check: MK7 + two children + remote + vice-director position.
const gross = 1586 + S.familyAllowanceMonthly(2) + S.REMOTE_AREA_ALLOWANCE_MONTHLY + S.positionAllowanceMonthly('vice_director');
check('gross with vice-director allowance', gross, 2001);
const result = S.calculate({ grossMonthly: gross, profile: 'permanent', ageGroup: 'over30', children: 2 });
close('standard deductions include position allowance', result.standardDeductions, gross * 0.2222, 0.001);
close('MTPY share on gross at 4.5%', gross * 0.045, 90.045, 0.001);
console.log(`Salary position allowance: PASS ${checks}/${checks}`);
