'use strict';
function check(name, condition) {
  if (!condition) { console.error('FAIL | ' + name); process.exitCode = 1; }
  else console.log('PASS | ' + name);
}

global.window = global;
global.PedagogicalCompetenceReference = { appendix_named_programs: {
  '14': { section: 'postgraduate_prior', title: 'Πρόγραμμα Μεταπτυχιακών Σπουδών «Σπουδές στην Εκπαίδευση»', provider: 'Ελληνικό Ανοικτό Πανεπιστήμιο', legal_basis: 'ν. 4186/2013', fek: '193 Α΄/17-9-2013' },
  '39': { section: 'special_program', title: 'Πρόγραμμα Σπουδών στις Επιστήμες της Αγωγής και της Εκπαίδευσης', provider: 'Οικονομικό Πανεπιστήμιο Αθηνών (ΟΠΑ)', legal_basis: '39460/Γ2/21-3-2013', fek: '689 Β΄/26-3-2013' }
} };
global.document = {
  readyState: 'loading',
  getElementById: () => null,
  addEventListener: () => {}
};
require('../includes/pedagogical-competence-ui.js');
const UI = global.PedagogicalCompetenceUI;

const lateProfessor = UI.evaluateCredentialValues({
  proofType: 'professor_school', entryYear: 'from_2015', graduationYear: 'from_2018'
});
const eppaik = UI.evaluateCredentialValues({ proofType: 'aspaite_eppaik' });
const overall = UI.aggregateEvaluations([lateProfessor, eppaik]);
check('second independent credential prevents false negative', overall.status === 'positive' && overall.positives.length === 1);

const secondDegree = UI.evaluateCredentialValues({ proofType: 'pedagogical_department', pedagogicalDepartmentType: 'pte70' });
check('late professor-school degree does not invalidate a second degree route', UI.aggregateEvaluations([lateProfessor, secondDegree]).status === 'positive');

const foreignUnknown = UI.evaluateCredentialValues({ proofType: 'education_msc_phd', educationDegreeOrigin: 'foreign', foreignEducationEvidence: 'unknown' });
check('foreign education title is not auto-positive without evidence', foreignUnknown.status === 'unknown');

const foreignConfirmed = UI.evaluateCredentialValues({ proofType: 'education_msc_phd', educationDegreeOrigin: 'foreign', foreignEducationEvidence: 'yes' });
check('foreign education title becomes positive when evidence is confirmed', foreignConfirmed.status === 'positive');

const namedPending = UI.evaluateCredentialValues({ proofType: 'aei_certificate', aeiCertificateSubtype: 'named_special_program', namedSpecialProgramRow: '39', namedSpecialProgramExactMatch: 'unknown' });
check('named special-program row requires exact-title verification before positive result', namedPending.status === 'unknown');

const namedConfirmed = UI.evaluateCredentialValues({ proofType: 'aei_certificate', aeiCertificateSubtype: 'named_special_program', namedSpecialProgramRow: '39', namedSpecialProgramExactMatch: 'yes' });
check('named special-program row becomes positive only after exact title/provider confirmation', namedConfirmed.status === 'positive' && namedConfirmed.detail.includes('Οικονομικό Πανεπιστήμιο Αθηνών'));

const domesticConfirmed = UI.evaluateCredentialValues({ proofType: 'education_msc_phd', educationDegreeOrigin: 'domestic', domesticEducationEvidence: 'yes' });
check('domestic education degree is positive without opening the old named list when evidence is already clear', domesticConfirmed.status === 'positive');

const namedPostgraduatePending = UI.evaluateCredentialValues({ proofType: 'education_msc_phd', educationDegreeOrigin: 'domestic', domesticEducationEvidence: 'unknown', namedPostgraduateRow: '14', namedPostgraduateExactMatch: 'unknown' });
check('named postgraduate lookup requires exact title/provider confirmation', namedPostgraduatePending.status === 'unknown');

const namedPostgraduate = UI.evaluateCredentialValues({ proofType: 'education_msc_phd', educationDegreeOrigin: 'domestic', domesticEducationEvidence: 'unknown', namedPostgraduateRow: '14', namedPostgraduateExactMatch: 'yes' });
check('named postgraduate lookup becomes positive after exact match', namedPostgraduate.status === 'positive' && namedPostgraduate.detail.includes('Ελληνικό Ανοικτό Πανεπιστήμιο'));

const guidedAei = UI.evaluateCredentialValues({ proofType: 'aei_certificate', aeiCertificateSubtype: 'standard', aeiEntryPeriod: 'up_to_2026', aeiCertifiedAtEntry: 'yes' });
check('guided AEI transition route requires both sequential conditions', guidedAei.status === 'positive');

if (process.exitCode) process.exit(process.exitCode);
console.log('RESULT: 10/10 PASS');
