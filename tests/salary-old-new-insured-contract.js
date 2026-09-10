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

check('new status exported', S.INSURED_STATUSES.new.shortLabel.includes('01/01/1993'), true);
check('old status exported', S.INSURED_STATUSES.old.shortLabel.includes('31/12/1992'), true);

const common = {
  basicMonthly: 1527,
  familyAllowanceMonthly: 120,
  positionAllowanceMonthly: 0,
  remoteAllowanceMonthly: 100,
  grossMonthly: 1747,
  profile: 'permanent',
  ageGroup: 'over30',
  children: 2
};
const newer = S.calculate({ ...common, insuredStatus: 'new' });
const older = S.calculate({ ...common, insuredStatus: 'old' });

close('new pension contribution base', newer.pensionContributionBase, 1747);
close('new lump-sum contribution base', newer.lumpSumContributionBase, 1747);
close('new MTPY primary base', newer.mtpyPrimaryContributionBase, 1747);
close('new standard deductions', newer.standardDeductions, 388.19);
close('new EFKA pension+supplementary line', newer.deductionComponents[0].amount, 168.93);
close('new TPDY line', newer.deductionComponents[3].amount, 69.88);
close('new MTPY line', newer.deductionComponents[4].amount, 78.62);

close('old pension contribution base excludes family/remote', older.pensionContributionBase, 1527);
close('old lump-sum base is basic salary', older.lumpSumContributionBase, 1527);
close('old MTPY 4.5% base', older.mtpyPrimaryContributionBase, 1527);
close('old MTPY 1% base', older.mtpyReducedContributionBase, 220);
close('old EFKA pension+supplementary line', older.deductionComponents[0].amount, 147.66);
close('old health in-kind still uses gross', older.deductionComponents[1].amount, 28.83);
close('old health cash still uses gross', older.deductionComponents[2].amount, 6.99);
close('old TPDY 4% of basic', older.deductionComponents[3].amount, 61.08);
close('old MTPY mixed bases', older.deductionComponents[4].amount, 70.92);
close('old unemployment still uses gross', older.deductionComponents[5].amount, 34.94);
close('old standard deductions reflect mixed bases', older.standardDeductions, 350.42);
check('old effective rate is lower than flat 22.22% when non-pensionable allowances exist', older.standardDeductionRate < 0.2222, true);

const oldWithPosition = S.calculate({ ...common, positionAllowanceMonthly: 195, grossMonthly: 1942, insuredStatus: 'old' });
close('old pension base includes position allowance', oldWithPosition.pensionContributionBase, 1722);
close('old TPDY base still excludes position allowance', oldWithPosition.lumpSumContributionBase, 1527);
close('old MTPY primary base includes position allowance', oldWithPosition.mtpyPrimaryContributionBase, 1722);

const oldMother = S.calculate({ ...common, insuredStatus: 'old', maternityPensionReduction: true });
close('old maternity reduction uses old main-pension base', oldMother.maternityPensionContributionBase, 1527);
close('old maternity reduction amount', oldMother.maternityPensionReductionAmount, 50.93);
const newMother = S.calculate({ ...common, insuredStatus: 'new', maternityPensionReduction: true });
close('new maternity reduction uses new main-pension base', newMother.maternityPensionContributionBase, 1747);
close('new maternity reduction amount', newMother.maternityPensionReductionAmount, 58.26);

const subNew = S.calculate({ grossMonthly: 1520, profile: 'substitute', insuredStatus: 'new', ageGroup: 'over30', children: 0 });
const subOld = S.calculate({ grossMonthly: 1520, profile: 'substitute', insuredStatus: 'old', ageGroup: 'over30', children: 0 });
close('KPK101 unaffected by insured-status selection', subOld.standardDeductions, subNew.standardDeductions);
check('KPK101 reports insured status not applicable', subOld.insuredStatusApplies, false);

console.log(`Salary old/new insured bases contract: PASS ${checks}/${checks}`);
