'use strict';

global.window = global;

function makeElement(id, value, checked) {
  return {
    id: id,
    value: value == null ? '' : String(value),
    checked: !!checked,
    textContent: '',
    innerHTML: '',
    className: '',
    style: {},
    addEventListener: function () {},
    scrollIntoView: function () {},
    getAttribute: function () { return null; }
  };
}

var ids = [
  'appointmentStatus','obstacleMusicExclusive','obstacleLeader','obstacleTermDetachment','obstacleActiveDetachment',
  'obstacleESK','obstacleSuspension','obstacleEaeGeneral','prioritySpecialCategory','priorityNewSelfSpouse75',
  'priorityNewChild67','priorityCoServiceCategory','priorityElected','priorityFirstPreference','requestedArea',
  'serviceYears','serviceMonths','serviceDays','coServiceType','coServiceOneYearSameArea','coServiceWorkedDay',
  'locality','familyStatus','eligibleChildren','healthPerson','healthSelfFamily','healthChildProtected','healthParents',
  'parentLocationEligible','siblingHealth','ivf','studyType','studyDifferentArea','studyRequestedArea','studyWithinDuration',
  'calculateBtn','resetBtn','result','grandTotal','resService','resCoService','resLocality','resFamily','resHealth','resStudies',
  'sidebarStatus','studyPointsStatus'
];

var elements = {};
ids.forEach(function (id) { elements[id] = makeElement(id); });
elements.appointmentStatus.value = 'not_new';
elements.priorityCoServiceCategory.value = 'none';
elements.coServiceType.value = 'none';
elements.familyStatus.value = 'none';
elements.healthPerson.value = 'none';
elements.healthSelfFamily.value = '0';
elements.healthParents.value = '0';
elements.studyType.value = 'none';

global.document = {
  readyState: 'complete',
  getElementById: function (id) { return elements[id] || null; },
  querySelector: function (selector) { return selector === '.edu-page-detachment' ? {} : null; },
  querySelectorAll: function () { return []; },
  addEventListener: function () {}
};
global.scrollTo = function () {};

require('../includes/detachment-calculations.js');
require('../includes/detachment-ui.js');

function assert(label, condition) {
  if (!condition) {
    console.error('FAIL | ' + label);
    process.exitCode = 1;
  } else {
    console.log('PASS | ' + label);
  }
}

// Eligible studies without all confirmations must remain at zero and explain why.
elements.studyType.value = 'eligible';
elements.studyDifferentArea.checked = true;
elements.studyRequestedArea.checked = true;
elements.studyWithinDuration.checked = false;
global.EducationDetachmentUI.calculate();
assert('incomplete studies remain 0', elements.resStudies.textContent === '0,00');
assert('missing prerequisite is visible', elements.studyPointsStatus.innerHTML.indexOf('προβλεπόμενο χρόνο φοίτησης') !== -1);

// With all three official prerequisites, the 2 study points must appear in both subtotal and grand total.
elements.studyWithinDuration.checked = true;
global.EducationDetachmentUI.calculate();
assert('eligible studies show 2 in sidebar', elements.resStudies.textContent === '2,00');
assert('eligible studies are added to grand total', elements.grandTotal.textContent === '2,00');
assert('study diagnostic confirms 2/2', elements.studyPointsStatus.innerHTML.indexOf('2,00 / 2,00') !== -1);
assert('detailed breakdown shows study points', elements.result.innerHTML.indexOf('<tr><td>Σπουδές</td><td class="points">2</td>') !== -1);

if (process.exitCode) process.exit(process.exitCode);
console.log('RESULT: 6/6 PASS');
