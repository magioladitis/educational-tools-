const fs = require('fs');
const vm = require('vm');
const path = require('path');

const source = fs.readFileSync(path.join(__dirname, '..', 'includes', 'service-calculations.js'), 'utf8');
const sandbox = { window: {} };
vm.createContext(sandbox);
vm.runInContext(source, sandbox);
const S = sandbox.window.EducationService;

let pass = 0, fail = 0;
function check(label, actual, expected) {
  const ok = JSON.stringify(actual) === JSON.stringify(expected);
  console.log((ok ? '✓' : '✗') + ' ' + label + (ok ? '' : ` | got ${JSON.stringify(actual)} expected ${JSON.stringify(expected)}`));
  if (ok) pass++; else fail++;
}

check('2024-2025 limit', S.RULES.digitalSchoolYears['2024-2025'], {maxMonths:9,maxDaysAtMaxMonths:16});
check('2025-2026 limit', S.RULES.digitalSchoolYears['2025-2026'], {maxMonths:8,maxDaysAtMaxMonths:2});
check('9m16d stays 9 scored months', (() => { const r=S.digitalTutoring([{schoolYear:'2024-2025',months:9,days:16}]); return [r.countedMonths,r.remainingDays,r.points]; })(), [9,16,13.5]);
check('8m2d stays 8 scored months', (() => { const r=S.digitalTutoring([{schoolYear:'2025-2026',months:8,days:2}]); return [r.countedMonths,r.remainingDays,r.points]; })(), [8,2,12]);
check('days carry across years', (() => { const r=S.digitalTutoring([{schoolYear:'2024-2025',months:4,days:16},{schoolYear:'2025-2026',months:3,days:14}]); return [r.fullMonths,r.remainderDays,r.extraMonths,r.remainingDays,r.points]; })(), [7,30,1,0,12]);
check('current real maxima together', (() => { const r=S.digitalTutoring([{schoolYear:'2024-2025',months:9,days:16},{schoolYear:'2025-2026',months:8,days:2}]); return [r.countedMonths,r.remainingDays,r.points]; })(), [17,18,25.5]);
check('2024 over-limit clamps', (() => { const r=S.digitalTutoring([{schoolYear:'2024-2025',months:99,days:99}]); return [r.entries[0].months,r.entries[0].days,r.points]; })(), [9,16,13.5]);
check('2025 over-limit clamps', (() => { const r=S.digitalTutoring([{schoolYear:'2025-2026',months:99,days:99}]); return [r.entries[0].months,r.entries[0].days,r.points]; })(), [8,2,12]);
check('duplicate year counted once', (() => { const r=S.digitalTutoring([{schoolYear:'2024-2025',months:2,days:0},{schoolYear:'2024-2025',months:2,days:0}]); return [r.countedMonths,r.points,r.warnings.length>0]; })(), [2,3,true]);
check('legacy digitalPerSchoolYear preserved', S.digitalPerSchoolYear(99), {months:10,points:15});

console.log(`\nPASS: ${pass}`);
console.log(`FAIL: ${fail}`);
process.exitCode = fail ? 1 : 0;
