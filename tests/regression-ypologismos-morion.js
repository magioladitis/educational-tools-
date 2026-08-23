const fs = require('fs');
const vm = require('vm');
global.window = global;
['academic-calculations.js','service-calculations.js','social-calculations.js'].forEach(f => {
  vm.runInThisContext(fs.readFileSync(__dirname + '/../includes/' + f, 'utf8'), { filename: f });
});

function approx(actual, expected, label) {
  if (Math.abs(actual - expected) > 1e-9) throw new Error(`${label}: expected ${expected}, got ${actual}`);
  console.log('PASS', label, actual);
}

// 1. Plain degree only: 7.50 × 2.5 = 18.75
let a = EducationAcademic.calculate({specialty:'ΠΕ02', degreeGrade:7.5, secondDegree:false, phd:false, mscCount:0, languages:[], computer:false, training:false});
approx(a.points, 18.75, 'degree-only academic');

// 2. Rich academic profile below cap: 8*2.5 +7+40+28+14+4+2 = 115
// (two excellent different languages = 14)
a = EducationAcademic.calculate({specialty:'ΠΕ02', degreeGrade:8, secondDegree:true, phd:true, mscCount:2,
  languages:[{language:'en',level:'excellent'},{language:'fr',level:'excellent'}], computer:true, training:true});
approx(a.points, 115, 'rich academic profile');

// 3. Academic cap: degree 10 + all extras = 120 cap
a = EducationAcademic.calculate({specialty:'ΠΕ02', degreeGrade:10, secondDegree:true, phd:true, mscCount:2,
  languages:[{language:'en',level:'excellent'},{language:'fr',level:'excellent'}], computer:true, training:true});
approx(a.rawPoints, 120, 'academic raw maximum exact');
approx(a.points, 120, 'academic cap maximum');

// 4. PE06 English is excluded as appointment language, French remains.
a = EducationAcademic.calculate({specialty:'ΠΕ06', degreeGrade:8, secondDegree:false, phd:false, mscCount:0,
  languages:[{language:'en',level:'excellent'},{language:'fr',level:'very_good'}], computer:false, training:false});
approx(a.languagePoints, 5, 'PE06 language exclusion');

// 5. PE86 does not get computer points.
a = EducationAcademic.calculate({specialty:'ΠΕ86', degreeGrade:8, secondDegree:false, phd:false, mscCount:0,
  languages:[], computer:true, training:false});
approx(a.computerPoints, 0, 'PE86 computer exclusion');

// 6. Service components and total cap.
const parts = [
  EducationService.regularPublic(100).points,     //100
  EducationService.difficult(20).points,          //40
  EducationService.privateSchool(10).points       //9
];
const s = EducationService.cappedTotal(parts);
approx(s.raw, 149, 'service raw');
approx(s.points, 120, 'service cap');

// 7. COVID three-month annual caps.
approx(EducationService.threeMonthRegular2020(8).points, 10, 'three-month regular 2020 cap');
approx(EducationService.threeMonthDifficult2021(7).points, 20, 'three-month difficult 2021 cap');

// 8. Social: 2 children =6; highest eligible disability 67 child =26.8 =>32.8 total.
const social = EducationSocial.calculate({children:2,candidateDisability:60,spouseDisability:55,childDisability:67,marriageYears4Plus:true,candidateMentalCondition:false});
approx(social.childrenPoints, 6, 'children points');
approx(social.disabilityPoints, 26.8, 'highest disability points');
approx(social.total, 32.8, 'social total');

console.log('\nALL REGRESSION TESTS PASSED');
