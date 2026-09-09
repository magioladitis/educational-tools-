const fs = require('fs');
const vm = require('vm');
const path = require('path');
const root = path.resolve(__dirname, '..');
const code = fs.readFileSync(path.join(root, 'includes', 'salary-net-calculations.js'), 'utf8');
const page = fs.readFileSync(path.join(root, 'ypologismos-misthologikou-klimakiou.php'), 'utf8');
const context = { window: {} };
vm.createContext(context);
vm.runInContext(code, context);
const S = context.window.EducationSalaryNet;
let checks = 0;
function check(name, condition) { if (!condition) throw new Error(name); checks++; }
function close(name, actual, expected, tolerance = 0.01) {
  if (Math.abs(actual - expected) > tolerance) throw new Error(`${name}: expected ~${expected}, got ${actual}`);
  checks++;
}

check('collapsible panel', page.includes('id="otherDeductionsPanel"') && page.includes('<summary><strong>Λοιπές κρατήσεις</strong>'));
check('ADEDY input', page.includes('id="adedYDeduction"'));
check('federation input', page.includes('id="federationDeduction"') && page.includes('ΟΛΜΕ / ΔΟΕ'));
check('association input', page.includes('id="associationDeduction"'));
check('other amount input', page.includes('id="otherPayrollDeduction"'));
check('all start at zero', (page.match(/type="number" min="0" step="0\.01" value="0"/g) || []).length >= 4);
check('result total row', page.includes('otherDeductionsResult'));
check('result breakdown row', page.includes('otherDeductionsBreakdownResult'));
check('engine receives total', page.includes('otherDeductions: otherDeductions'));
check('tax unaffected note', page.includes('δεν μειώνουν το φορολογητέο εισόδημα'));

const base = S.calculate({ grossMonthly: 1586, profile: 'permanent', ageGroup: 'over30', children: 0 });
const withOther = S.calculate({ grossMonthly: 1586, profile: 'permanent', ageGroup: 'over30', children: 0, otherDeductions: 1.90 });
close('other deductions stored', withOther.otherDeductions, 1.90, 0.0001);
close('net before other deductions unchanged', withOther.netBeforeOtherDeductions, base.estimatedNet, 0.0001);
close('taxable annual unchanged', withOther.taxableAnnual, base.taxableAnnual, 0.0001);
close('monthly tax unchanged', withOther.monthlyTax, base.monthlyTax, 0.0001);
close('net falls exactly by other deductions', base.estimatedNet - withOther.estimatedNet, 1.90, 0.0001);
close('golden Sep final net', withOther.estimatedNet, 1136.72, 0.01);

console.log(`Salary other deductions: PASS ${checks}/${checks}`);
