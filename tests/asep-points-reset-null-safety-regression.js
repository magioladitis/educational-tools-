'use strict';

const fs = require('fs');
const vm = require('vm');
const path = require('path');

const root = path.resolve(__dirname, '..');
const source = fs.readFileSync(path.join(root, 'includes', 'asep-points-ui.js'), 'utf8');
let passed = 0;
let failed = 0;
function check(condition, label) {
  if (condition) { console.log('PASS', label); passed++; }
  else { console.error('FAIL', label); failed++; }
}

check(!source.includes('document.getElementById("specialty").value = ""'), 'reset has no unguarded specialty value assignment');
check(source.includes('const specialty = document.getElementById("specialty");'), 'reset caches optional specialty element');
check(source.includes('if (specialty) specialty.value = "";'), 'specialty value reset is null-safe');
check(source.includes('if (specialty) specialty.focus();'), 'specialty focus is null-safe');

const listeners = {};
const numberOther = { id: 'otherNumber', value: 7 };
const degreeGrade = { id: 'degreeGrade', value: 8 };
const checkbox = { checked: true };
const elements = {
  grandTotal: { textContent: '' },
  resAcademic: { textContent: '' },
  resService: { textContent: '' },
  resSocial: { textContent: '' },
  resDegree: { textContent: '' },
  sidebarStatus: { textContent: '' },
  result: { style: {}, innerHTML: 'old', className: 'old' },
  copyResultBtn: {
    disabled: false,
    textContent: 'Αντιγραφή',
    addEventListener(type, fn) { listeners['copy:' + type] = fn; }
  },
  resetCalculatorBtn: {
    addEventListener(type, fn) { listeners['reset:' + type] = fn; }
  }
  // Intentionally no #specialty.
};

let academicReset = 0;
let academicSync = 0;
let serviceReset = 0;
let pedagogicalReset = 0;

const documentStub = {
  getElementById(id) { return Object.prototype.hasOwnProperty.call(elements, id) ? elements[id] : null; },
  querySelectorAll(selector) {
    if (selector === 'input[type="number"]') return [numberOther, degreeGrade];
    if (selector === 'input[type="checkbox"]') return [checkbox];
    if (selector === '.layout input, .layout select') return [];
    return [];
  },
  addEventListener() {},
  createElement() { return { style: {}, select() {}, remove() {} }; },
  body: { appendChild() {} },
  execCommand() { return true; }
};

const context = {
  document: documentStub,
  navigator: { clipboard: { writeText: async () => {} } },
  setTimeout(fn) { fn(); return 1; },
  console,
  AsepPeAcademic: {
    reset() { academicReset++; },
    sync() { academicSync++; }
  },
  AsepServiceController: { reset() { serviceReset++; } },
  AsepPedagogicalProof: { reset() { pedagogicalReset++; } },
  AsepDigitalTutoring: {},
  AsepSocialCriteria: {}
};

try {
  vm.runInNewContext(source, context, { filename: 'asep-points-ui.js' });
  check(typeof listeners['reset:click'] === 'function', 'reset click handler registered without #specialty');
  let threw = false;
  try { listeners['reset:click'](); } catch (error) { threw = true; console.error(error); }
  check(!threw, 'reset completes when #specialty is absent');
  check(numberOther.value === 0, 'other number inputs reset');
  check(degreeGrade.value === 8, 'degreeGrade keeps component-controlled value');
  check(checkbox.checked === false, 'checkbox inputs reset');
  check(elements.result.style.display === 'none' && elements.result.innerHTML === '' && elements.result.className === 'result', 'result panel reset');
  check(elements.copyResultBtn.disabled === true, 'copy button disabled after reset');
  check(academicReset === 1 && serviceReset === 1 && pedagogicalReset === 1, 'component reset hooks still run');
  check(academicSync >= 2, 'academic component is synced on init and after reset');
} catch (error) {
  console.error(error);
  check(false, 'controller loads with #specialty absent');
}

console.log(`RESULT ${passed}/${passed + failed}`);
process.exit(failed ? 1 : 0);
