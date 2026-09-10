const fs = require('fs');
const vm = require('vm');
const path = require('path');

const root = path.resolve(__dirname, '..');
const code = fs.readFileSync(path.join(root, 'includes', 'detachment-calculations.js'), 'utf8');
const win = {};
const context = { window: win, Number, Math, String, console };
vm.createContext(context);
vm.runInContext(code, context);
const calc = win.EducationDetachment;
let checks = 0;
function assert(name, condition) {
  checks++;
  if (!condition) { console.error('FAIL | ' + name); process.exitCode = 1; }
  else console.log('PASS | ' + name);
}
function base(extra) {
  return Object.assign({
    appointmentStatus: 'not_new', serviceYears: 0, serviceMonths: 0, serviceDays: 0,
    coServiceType: 'none', familyStatus: 'none', eligibleChildren: 0,
    healthPerson: 'none', healthSelfFamily: '0', healthParents: '0', studyType: 'none',
    priorityCoServiceCategory: 'none'
  }, extra || {});
}
let r = calc.calculate(base({serviceYears:10}));
assert('10 years service = 10 points', Math.abs(r.service.total - 10) < 1e-9);
r = calc.calculate(base({serviceYears:10, serviceMonths:1}));
assert('month above 10 years uses 1.5/year band', Math.abs(r.service.total - 10.125) < 1e-9);
r = calc.calculate(base({serviceYears:20, serviceDays:15}));
assert('15 days become one month in third band', Math.abs(r.service.total - (25 + 2/12)) < 1e-9);
r = calc.calculate(base({coServiceType:'public_organic'}));
assert('public organic co-service = 10', r.coServicePoints === 10);
r = calc.calculate(base({coServiceType:'private', coServiceOneYearSameArea:true, coServiceWorkedDay:true}));
assert('private co-service with conditions = 10', r.coServicePoints === 10);
r = calc.calculate(base({coServiceType:'private', coServiceOneYearSameArea:true, coServiceWorkedDay:false}));
assert('private co-service without worked day = 0', r.coServicePoints === 0);
r = calc.calculate(base({locality:true}));
assert('locality = 4', r.localityPoints === 4);
r = calc.calculate(base({familyStatus:'married', eligibleChildren:4}));
assert('married + 4 children = 33 family points', r.familyTotal === 33);
r = calc.calculate(base({healthPerson:'self', healthSelfFamily:'20', healthParents:'3', parentLocationEligible:true, siblingHealth:true, ivf:true}));
assert('health categories sum correctly', r.healthTotal === 31);
r = calc.calculate(base({studyType:'eligible', studyDifferentArea:true, studyRequestedArea:true, studyWithinDuration:true}));
assert('eligible studies = 2', r.studiesPoints === 2);
r = calc.calculate(base({appointmentStatus:'after_2024_09_30'}));
assert('post Sep 2024 new appointee obstacle is detected', r.eligibility.obstacleReasons.length === 1);
r = calc.calculate(base({appointmentStatus:'after_2024_09_30', prioritySpecialCategory:true}));
assert('special category enables new-appointee exception', r.eligibility.obstacleReasons.length === 0 && r.eligibility.priorityReasons.length > 0);
r = calc.calculate(base({priorityCoServiceCategory:'uniformed', priorityFirstPreference:false}));
assert('priority co-service needs first preference', r.eligibility.priorityBlockedByFirstChoice === true);
r = calc.calculate(base({serviceMonths:12}));
assert('invalid service months rejected', !!r.error);
r = calc.calculate(base({serviceDays:31}));
assert('invalid service days rejected', !!r.error);

if (!process.exitCode) console.log('RESULT: ' + checks + '/' + checks + ' PASS');
