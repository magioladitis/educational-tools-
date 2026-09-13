const fs = require('fs');
const vm = require('vm');
const path = require('path');
const root = path.resolve(__dirname, '..');
const engine = fs.readFileSync(path.join(root, 'includes', 'salary-net-calculations.js'), 'utf8');
const page = fs.readFileSync(path.join(root, 'ypologismos-misthologikou-klimakiou.php'), 'utf8');
const ui = fs.readFileSync(path.join(root, 'includes', 'salary-ui.js'), 'utf8');
const context = { window: {} };
vm.createContext(context);
vm.runInContext(engine, context);
const S = context.window.EducationSalaryNet;
let checks = 0;
function check(name, condition) { if (!condition) throw new Error(name); checks++; }
function close(name, actual, expected, tolerance = 0.001) {
  if (Math.abs(actual - expected) > tolerance) throw new Error(`${name}: expected ~${expected}, got ${actual}`);
  checks++;
}

check('personal difference input exists', page.includes('id="personalDifference"'));
check('personal difference result exists', page.includes('personalDifferenceResult'));
check('zero print is conditional', ui.includes("payroll.personalDifference > 0 ? printAmountRow('Προσωπική διαφορά'"));
check('zero screen row is conditional', ui.includes("setResultRowVisible('personalDifferenceResult', personalDifference > 0)"));
check('reset returns to zero', ui.includes("byId('personalDifference').value = '0'"));
check('engine receives personal difference', ui.includes('personalDifferenceMonthly: personalDifference'));

const baseOptions = {
  basicMonthly: 1468,
  familyAllowanceMonthly: 120,
  positionAllowanceMonthly: 0,
  remoteAllowanceMonthly: 0,
  profile: 'permanent',
  insuredStatus: 'new_efka',
  ageGroup: 'over30',
  children: 2
};
const base = S.calculate(baseOptions);
const pd = S.calculate({ ...baseOptions, personalDifferenceMonthly: 100 });
close('gross rises by personal difference', pd.grossMonthly - base.grossMonthly, 100);
close('pension base unchanged', pd.pensionContributionBase, base.pensionContributionBase);
close('lump-sum base unchanged', pd.lumpSumContributionBase, base.lumpSumContributionBase);
close('health base unchanged', pd.healthContributionBase, base.healthContributionBase);
close('unemployment base rises by personal difference', pd.unemploymentContributionBase - base.unemploymentContributionBase, 100);
const mtpyPd = pd.deductionComponents.find(x => x.key === 'mtpyPersonalDifference');
check('MTPY personal-difference component exists', !!mtpyPd);
close('MTPY personal difference is 2%', mtpyPd.amount, 2.00);
close('unemployment adds 2%', pd.deductionComponents.find(x => x.key === 'unemployment').amount - base.deductionComponents.find(x => x.key === 'unemployment').amount, 2.00);
check('taxable annual rises', pd.taxableAnnual > base.taxableAnnual);
check('net rises but by less than gross addition', pd.estimatedNet > base.estimatedNet && pd.estimatedNet - base.estimatedNet < 100);

const substitute = S.calculate({
  basicMonthly: 1468,
  personalDifferenceMonthly: 100,
  profile: 'substitute',
  ageGroup: 'over30',
  children: 0
});
close('substitute KPK base excludes personal difference', substitute.deductionComponents[0].base, 1468);

console.log(`Salary personal difference: PASS ${checks}/${checks}`);
