const fs = require('fs');
const vm = require('vm');
const path = require('path');

const code = fs.readFileSync(path.join(__dirname, '..', 'includes', 'teaching-hours-calculations.js'), 'utf8');
const sandbox = { window: {} };
vm.runInNewContext(code, sandbox, { filename: 'teaching-hours-calculations.js' });
const H = sandbox.window.EducationTeachingHours;
let pass = 0;
function check(name, actual, expected) {
  if (actual !== expected) throw new Error(`${name}: expected ${expected}, got ${actual}`);
  pass++;
}
function hours(opts) { return H.calculate(opts).hours; }

check('primary 2-position always 25', hours({level:'primary', schoolType:'primary', role:'teacher', organicity:2, years:30}), 25);
check('primary 4+ under 10', hours({level:'primary', schoolType:'primary', role:'teacher', organicity:6, years:0}), 24);
check('primary 10 years', hours({level:'primary', schoolType:'primary', role:'teacher', organicity:6, years:10}), 23);
check('primary 9y11m29d next reduction in 1 day', H.calculate({level:'primary', schoolType:'primary', role:'teacher', organicity:6, years:9, months:11, days:29}).nextReductionLabel, '1 ημέρα (→ 23 ώρες)');
check('primary 15 years', hours({level:'primary', schoolType:'primary', role:'teacher', organicity:6, years:15}), 22);
check('primary 20 years', hours({level:'primary', schoolType:'primary', role:'teacher', organicity:6, years:20}), 21);
check('primary director 4-5', hours({level:'primary', schoolType:'primary', role:'director', organicity:5, years:5}), 18);
check('primary director 6-9', hours({level:'primary', schoolType:'primary', role:'director', organicity:6, years:5}), 10);
check('primary director 10-11', hours({level:'primary', schoolType:'primary', role:'director', organicity:10, years:5}), 8);
check('primary director 12+', hours({level:'primary', schoolType:'primary', role:'director', organicity:12, years:5}), 6);
check('primary vice director under10', hours({level:'primary', schoolType:'primary', role:'vice_director', organicity:12, years:5}), 22);
check('primary vice director 20y', hours({level:'primary', schoolType:'primary', role:'vice_director', organicity:12, years:20}), 19);
check('kindergarten director 5', hours({level:'primary', schoolType:'kindergarten', role:'director', organicity:5, years:5}), 20);
check('kindergarten director 6', hours({level:'primary', schoolType:'kindergarten', role:'director', organicity:6, years:5}), 12);

check('secondary PE 0', hours({level:'secondary', role:'teacher', branch:'PE', years:0}), 23);
check('secondary PE exactly 6y', hours({level:'secondary', role:'teacher', branch:'PE', years:6}), 23);
check('secondary PE 6y0m1d', hours({level:'secondary', role:'teacher', branch:'PE', years:6, days:1}), 21);
check('secondary PE rule label Greek', H.calculate({level:'secondary', role:'teacher', branch:'PE', years:6, days:1}).rule.includes('κλάδου ΠΕ'), true);
check('secondary PE exactly 12y', hours({level:'secondary', role:'teacher', branch:'PE', years:12}), 21);
check('secondary PE 12y0m1d', hours({level:'secondary', role:'teacher', branch:'PE', years:12, days:1}), 20);
check('secondary PE 19y11m29d', hours({level:'secondary', role:'teacher', branch:'PE', years:19, months:11, days:29}), 20);
check('secondary PE 20', hours({level:'secondary', role:'teacher', branch:'PE', years:20}), 18);
check('secondary PE 6y next reduction in 1 day', H.calculate({level:'secondary', role:'teacher', branch:'PE', years:6}).nextReductionLabel, '1 ημέρα (→ 21 ώρες)');
check('secondary PE 6y1d next reduction in 6y', H.calculate({level:'secondary', role:'teacher', branch:'PE', years:6, days:1}).nextReductionLabel, '6 έτη (→ 20 ώρες)');
check('secondary PE 20 no next reduction', H.calculate({level:'secondary', role:'teacher', branch:'PE', years:20}).nextReductionLabel, 'Δεν προβλέπεται περαιτέρω μείωση');
check('secondary TE 0', hours({level:'secondary', role:'teacher', branch:'TE01', years:0}), 24);
check('secondary TE exactly 7y', hours({level:'secondary', role:'teacher', branch:'TE01', years:7}), 24);
check('secondary TE 7y0m1d', hours({level:'secondary', role:'teacher', branch:'TE01', years:7, days:1}), 21);
check('secondary TE01 rule label Greek', H.calculate({level:'secondary', role:'teacher', branch:'TE01', years:7, days:1}).rule.includes('κλάδου ΤΕ01'), true);
check('secondary TE exactly 13y', hours({level:'secondary', role:'teacher', branch:'TE01', years:13}), 21);
check('secondary TE 13y0m1d', hours({level:'secondary', role:'teacher', branch:'TE01', years:13, days:1}), 20);
check('secondary TE 20', hours({level:'secondary', role:'teacher', branch:'TE01', years:20}), 18);
check('secondary DE architect 0', hours({level:'secondary', role:'teacher', branch:'DE01_ARCH', years:0}), 28);
check('secondary DE architect 20', hours({level:'secondary', role:'teacher', branch:'DE01_ARCH', years:20}), 26);
check('secondary DE technician 0', hours({level:'secondary', role:'teacher', branch:'DE01_TECH', years:0}), 30);
check('secondary DE technician 20', hours({level:'secondary', role:'teacher', branch:'DE01_TECH', years:20}), 28);

check('secondary director >12 under20', hours({level:'secondary', role:'director', branch:'PE', sections:'13+', years:10}), 5);
check('secondary director >12 20y', hours({level:'secondary', role:'director', branch:'PE', sections:'13+', years:20}), 3);
check('secondary vice under20', hours({level:'secondary', role:'vice_or_sector', branch:'PE', years:10}), 16);
check('secondary vice 20y', hours({level:'secondary', role:'vice_or_sector', branch:'PE', years:20}), 14);
check('lab center director under20', hours({level:'secondary', role:'lab_director', branch:'PE', years:10}), 10);
check('lab center director 20y', hours({level:'secondary', role:'lab_director', branch:'PE', years:20}), 8);
check('lab responsible PE 0', hours({level:'secondary', role:'lab_responsible', branch:'PE', years:0}), 20);
check('lab responsible PE 20', hours({level:'secondary', role:'lab_responsible', branch:'PE', years:20}), 18);
check('lab responsible PE 6y next effective reduction is 20y', H.calculate({level:'secondary', role:'lab_responsible', branch:'PE', years:6}).nextReductionLabel, '14 έτη (→ 18 ώρες)');
check('EPAL/EK lab sector PE 7', hours({level:'secondary', role:'epal_ek_lab_sector', branch:'PE', years:7}), 19);
check('EPAL/EK lab sector PE 20 floor', hours({level:'secondary', role:'epal_ek_lab_sector', branch:'PE', years:20}), 18);

check('service years capped at 50', H.serviceMonths(99, 0), 600);
check('service months capped at 11', H.serviceMonths(1, 99), 23);
check('service days capped at 29', H.serviceMonths(1, 0, 99), 12 + 29/30);
check('service max 50y11m29d', H.serviceMonths(50, 11, 29), 611 + 29/30);
check('service label with days', H.serviceLabel(H.serviceMonths(6, 11, 29)), '6 έτη και 11 μήν. και 29 ημ.');

check('EEP 0 years', hours({level:'eep', years:0}), 25);
check('EEP exactly 5 years', hours({level:'eep', years:5}), 25);
check('EEP exactly 5y next reduction in 1 day', H.calculate({level:'eep', years:5}).nextReductionLabel, '1 ημέρα (→ 24 ώρες)');
check('EEP 5y0m1d', hours({level:'eep', years:5, days:1}), 24);
check('EEP exactly 10 years', hours({level:'eep', years:10}), 24);
check('EEP 10y0m1d', hours({level:'eep', years:10, days:1}), 23);
check('EEP exactly 15 years', hours({level:'eep', years:15}), 23);
check('EEP 15y0m1d', hours({level:'eep', years:15, days:1}), 22);
check('EEP exactly 20 years', hours({level:'eep', years:20}), 22);
check('EEP 20y0m1d', hours({level:'eep', years:20, days:1}), 21);
check('EEP 20y next reduction in 1 day', H.calculate({level:'eep', years:20}).nextReductionLabel, '1 ημέρα (→ 21 ώρες)');
check('EEP 20y1d no next reduction', H.calculate({level:'eep', years:20, days:1}).nextReductionLabel, 'Δεν προβλέπεται περαιτέρω μείωση');
check('EEP rule wording', H.calculate({level:'eep', years:11}).rule.includes('υποστηρικτικού έργου'), true);
check('EBP fixed 30', hours({level:'ebp', years:40, months:11, days:29}), 30);
check('EBP no next reduction', H.calculate({level:'ebp'}).nextReductionLabel, 'Δεν προβλέπεται περαιτέρω μείωση');
check('EBP rule wording', H.calculate({level:'ebp'}).rule.includes('υποστηρικτικού έργου'), true);
const invalid = H.calculate({level:'primary', schoolType:'primary', role:'director', organicity:3, years:2});
check('director small primary invalid', invalid.valid, false);
const invalidViceK = H.calculate({level:'primary', schoolType:'kindergarten', role:'vice_director', organicity:6, years:2});
check('vice director kindergarten invalid', invalidViceK.valid, false);
console.log(`Teaching hours regression: PASS ${pass}`);
