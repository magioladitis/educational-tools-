'use strict';
function check(name, condition) {
  if (!condition) { console.error('FAIL | ' + name); process.exitCode = 1; }
  else console.log('PASS | ' + name);
}

global.window = global;
global.PedagogicalCompetenceReference = { appendix_named_programs: {
  '39': { title: 'Πρόγραμμα Σπουδών στις Επιστήμες της Αγωγής και της Εκπαίδευσης', provider: 'Οικονομικό Πανεπιστήμιο Αθηνών (ΟΠΑ)', legal_basis: '39460/Γ2/21-3-2013', fek: '689 Β΄/26-3-2013' }
} };
global.document = {
  readyState: 'loading',
  getElementById: () => null,
  addEventListener: () => {}
};
require('../includes/pedagogical-competence-ui.js');
const UI = global.PedagogicalCompetenceUI;

const lateProfessor = UI.evaluateCredentialValues({
  proofType: 'professor_school', professorSchoolMatch: 'yes',
  entryYear: 'from_2015', graduationYear: 'from_2018'
});
const eppaik = UI.evaluateCredentialValues({ proofType: 'aspaite_eppaik' });
const overall = UI.aggregateEvaluations([lateProfessor, eppaik]);
check('second independent credential prevents false negative', overall.status === 'positive' && overall.positives.length === 1);

const wrongDegree = UI.evaluateCredentialValues({ proofType: 'professor_school', professorSchoolMatch: 'no' });
const secondDegree = UI.evaluateCredentialValues({ proofType: 'pedagogical_department', pedagogicalDepartmentType: 'pte70' });
check('target specialty does not invalidate a second degree route', UI.aggregateEvaluations([wrongDegree, secondDegree]).status === 'positive');

const foreignUnknown = UI.evaluateCredentialValues({ proofType: 'education_msc_phd', educationDegreeOrigin: 'foreign', foreignEducationEvidence: 'unknown' });
check('foreign education title is not auto-positive without evidence', foreignUnknown.status === 'unknown');

const foreignConfirmed = UI.evaluateCredentialValues({ proofType: 'education_msc_phd', educationDegreeOrigin: 'foreign', foreignEducationEvidence: 'yes' });
check('foreign education title becomes positive when evidence is confirmed', foreignConfirmed.status === 'positive');

const namedPending = UI.evaluateCredentialValues({ proofType: 'appendix_named_program', appendixRow: '39', appendixExactMatch: 'unknown' });
check('named Appendix E row requires exact-title verification before positive result', namedPending.status === 'unknown');

const namedConfirmed = UI.evaluateCredentialValues({ proofType: 'appendix_named_program', appendixRow: '39', appendixExactMatch: 'yes' });
check('named Appendix E row becomes positive only after exact title/provider confirmation', namedConfirmed.status === 'positive' && namedConfirmed.detail.includes('Οικονομικό Πανεπιστήμιο Αθηνών'));

if (process.exitCode) process.exit(process.exitCode);
console.log('RESULT: 6/6 PASS');
