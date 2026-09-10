'use strict';

function assert(label, condition) {
  if (!condition) {
    console.error('FAIL | ' + label);
    process.exitCode = 1;
  } else {
    console.log('PASS | ' + label);
  }
}

function makeElement(id, value) {
  const listeners = {};
  const classes = new Set(['hidden']);
  return {
    id,
    value: value || '',
    style: {},
    innerHTML: '',
    className: '',
    listeners,
    classList: {
      add: c => classes.add(c),
      remove: c => classes.delete(c),
      contains: c => classes.has(c)
    },
    addEventListener: (type, fn) => { listeners[type] = fn; }
  };
}

// Pedagogical competence page.
{
  global.window = global;
  const ids = ['specialty','proofType','opsyd','pedagogicalDepartmentQuestions','epathQuestions','professorSchoolQuestions','checkEparkeiaBtn','result'];
  const elements = {};
  ids.forEach(id => { elements[id] = makeElement(id); });
  elements.specialty.value = 'ΠΕ03';
  elements.proofType.value = 'aei_certificate';
  elements.opsyd.value = 'yes';
  global.document = {
    readyState: 'complete',
    getElementById: id => elements[id] || null,
    addEventListener: () => {}
  };
  delete require.cache[require.resolve('../includes/pedagogical-competence-ui.js')];
  require('../includes/pedagogical-competence-ui.js');
  assert('pedagogy change listener registered', typeof elements.proofType.listeners.change === 'function');
  assert('pedagogy click listener registered', typeof elements.checkEparkeiaBtn.listeners.click === 'function');
  global.PedagogicalCompetenceUI.checkEparkeia();
  assert('pedagogy positive result preserved', elements.result.innerHTML.includes('Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια'));
  elements.proofType.value = 'epath';
  global.PedagogicalCompetenceUI.updateVisibility();
  assert('pedagogy conditional section preserved', !elements.epathQuestions.classList.contains('hidden'));
}

// Study-title documents page.
{
  const ids = ['titleType','foreignRecognition','greekTitleQuestions','integratedMasterQuestions','foreignTitleQuestions','foreignExemptionQuestions','jointMscQuestions','showDocumentsBtn','result'];
  const elements = {};
  ids.forEach(id => { elements[id] = makeElement(id); });
  elements.titleType.value = 'none';
  elements.foreignRecognition.value = '';
  global.document = {
    readyState: 'complete',
    getElementById: id => elements[id] || null,
    addEventListener: () => {}
  };
  delete require.cache[require.resolve('../includes/study-title-documents-ui.js')];
  require('../includes/study-title-documents-ui.js');
  assert('documents title change listener registered', typeof elements.titleType.listeners.change === 'function');
  assert('documents foreign change listener registered', typeof elements.foreignRecognition.listeners.change === 'function');
  assert('documents click listener registered', typeof elements.showDocumentsBtn.listeners.click === 'function');
  global.StudyTitleDocumentsUI.showDocuments();
  assert('documents none-result preserved', elements.result.innerHTML.includes('Δεν απαιτείται πρόσθετο δικαιολογητικό τίτλου'));
  elements.titleType.value = 'msc_foreign';
  global.StudyTitleDocumentsUI.updateQuestions();
  assert('documents foreign section preserved', !elements.foreignTitleQuestions.classList.contains('hidden'));
  elements.foreignRecognition.value = 'exception';
  global.StudyTitleDocumentsUI.updateForeignExemptionQuestion();
  assert('documents exemption section preserved', !elements.foreignExemptionQuestions.classList.contains('hidden'));
}

if (process.exitCode) process.exit(process.exitCode);
console.log('RESULT: 10/10 PASS');
