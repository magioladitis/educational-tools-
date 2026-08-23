'use strict';
const assert = require('assert');
delete global.SDECalculator;
require('../includes/sde-calculations.js');
const C = global.SDECalculator;
let passed = 0;
function eq(actual, expected, label, eps=1e-9) {
  assert.ok(Math.abs(actual-expected) <= eps, `${label}: expected ${expected}, got ${actual}`);
  passed++;
}
function ok(value, label) { assert.ok(value, label); passed++; }

// Corrected caps (FEK B 4199/2026)
eq(C.MAX.formalQualifications, 18, 'formal cap');
eq(C.MAX.education, 22, 'education cap');
eq(C.MAX.teachingExperience, 13, 'experience cap');
eq(C.MAX.otherQualifications, 5, 'other cap');
eq(C.MAX.total, 40, 'total cap');

// Academic titles: max and “other direction = 1 point less”.
eq(C.calculateEducation({phd:'adult'}).formal, 11, 'PhD relevant');
eq(C.calculateEducation({phd:'other'}).formal, 10, 'PhD other');
eq(C.calculateEducation({master:'adult'}).formal, 8, 'Master relevant');
eq(C.calculateEducation({master:'other'}).formal, 7, 'Master other');
eq(C.calculateEducation({secondPhd:'adult'}).formal, 2, '2nd PhD relevant');
eq(C.calculateEducation({secondPhd:'other'}).formal, 1, '2nd PhD other');
eq(C.calculateEducation({secondMaster:'adult'}).formal, 1, '2nd Master relevant');
eq(C.calculateEducation({secondMaster:'other'}).formal, 0, '2nd Master other');
eq(C.calculateEducation({phd:'adult', master:'adult'}).formal, 11, 'PhD suppresses first master');
eq(C.calculateEducation({phd:'adult', secondDegree:true, secondPhd:'adult', secondMaster:'adult'}).formal, 18, 'formal cap reached exactly');

// Training threshold and cap.
eq(C.calculateEducation({sdeTrainingHours:14}).training, 0, '14 training hours = 0');
eq(C.calculateEducation({sdeTrainingHours:15}).training, 0.0375, '15 training hours');
eq(C.calculateEducation({sdeTrainingHours:800, adultTrainingHours:800}).training, 4, 'training cap');
eq(C.calculateEducation({phd:'adult', secondDegree:true, secondPhd:'adult', secondMaster:'adult', sdeTrainingHours:800, adultTrainingHours:800}).total, 22, 'education cap 22');

// Corrected formal education: scores from first year, while eligibility remains >=2.
eq(C.calculateExperience({formalEducationYears:0}).formalPoints, 0, 'formal 0 years');
eq(C.calculateExperience({formalEducationYears:1}).formalPoints, 1, 'formal 1 year');
eq(C.calculateExperience({formalEducationYears:2}).formalPoints, 2, 'formal 2 years');
eq(C.calculateExperience({formalEducationYears:4}).formalPoints, 4, 'formal 4 years');
eq(C.calculateExperience({formalEducationYears:9}).formalPoints, 4, 'formal years cap');
ok(!C.calculateAll({eligibilitySchoolYears:1, formalEducationYears:4}).eligibleByTwoYears, '1 school year not eligible even with tertiary/formal experience');
ok(C.calculateAll({eligibilitySchoolYears:2, formalEducationYears:2}).eligibleByTwoYears, '2 school years eligible');

// Languages + ICT = max 5.
eq(C.calculateOther({specialty:'PE02', language1:'english', languageLevel1:'C2', language2:'french', languageLevel2:'C2', computer:true}).total, 5, 'languages 3 + ICT 2');
eq(C.calculateOther({specialty:'PE02', computer:true}).computerPoints, 2, 'ICT = 2');
eq(C.calculateOther({specialty:'PE86', computer:false}).computerPoints, 2, 'PE86 ICT presumed');
eq(C.calculateOther({specialty:'PE06', language1:'english', languageLevel1:'C2'}).languagePoints, 0, 'PE06 English excluded');
eq(C.calculateOther({specialty:'PE02', language1:'english', languageLevel1:'B2', language2:'english', languageLevel2:'C2'}).languagePoints, 2, 'duplicate language highest only');

// Maximum total is 40.
const max = C.calculateAll({
  specialty:'PE02', phd:'adult', secondDegree:true, secondPhd:'adult', secondMaster:'adult',
  sdeTrainingHours:800, adultTrainingHours:800,
  sdeYears:5, adultEducationHours:800, formalEducationYears:4, eligibilitySchoolYears:2,
  language1:'english', languageLevel1:'C2', language2:'french', languageLevel2:'C2', computer:true
});
eq(max.education.total,22,'max education');
eq(max.experience.total,13,'max experience');
eq(max.other.total,5,'max other');
eq(max.total,40,'max total');

console.log(`PASS ${passed} SDE-apospasis v3.16 regression checks`);
