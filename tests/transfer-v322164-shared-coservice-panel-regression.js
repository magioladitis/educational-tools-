'use strict';
// Protect the shared spouse-employment panel without merging grade-specific rules.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const { execFileSync } = require('node:child_process');
const root = path.resolve(__dirname, '..');
const read = p => fs.readFileSync(path.join(root, p), 'utf8');
const script = read('includes/transfer-social-input.js');
const context = { window: {} };
vm.runInNewContext(script, context);
const shared = context.window.EducationTransferSocialInput;
assert.equal(typeof shared.clampChildren, 'function');
assert.equal(typeof shared.syncCoServiceField, 'function');
assert.equal(typeof shared.bindCoServiceField, 'function');
for (const checked of [false, true]) {
  const field = { style: { display: 'none' } };
  const input = { checked, addEventListener(){} };
  const select = { value: 'unspecified', addEventListener(){} };
  assert.equal(shared.syncCoServiceField(input, field, select), checked);
  assert.equal(field.style.display, checked ? 'block' : 'none');
}
const events = {};
function fakeInput(name, props) {
  return { ...props, addEventListener(type, callback) { (events[name] ||= {})[type] = callback; } };
}
const cb = fakeInput('check', { checked: false });
const choice = fakeInput('choice', { value: 'unspecified' });
const field = { style: { display: '' } };
shared.bindCoServiceField(cb, field, choice);
assert.equal(field.style.display, 'none', 'initially hidden');
cb.checked = true; events.check.input();
assert.equal(field.style.display, 'block', 'immediate open on checkbox input');
cb.checked = false; events.check.change();
assert.equal(field.style.display, 'none', 'close when unchecked');
choice.value = 'private_employee'; events.choice.change();
assert.equal(field.style.display, 'block', 'keep visible for existing case');
const primary = read('includes/transfer-primary.js');
const secondary = read('includes/transfer-ui.js');
for (const [grade, js] of [['Primary', primary], ['Secondary', secondary]]) {
  assert.match(js, /EducationTransferSocialInput\.bindCoServiceField\(/, grade+' binds shared field control');
  assert.match(js, /EducationTransferSocialInput\.syncCoServiceField\(/, grade+' uses shared field control');
}
assert.match(primary, /if\(blocked\)check\.checked=false/, 'Primary special constraints retained');
assert.match(secondary, /if\(blocked\)checkbox\.checked=false/, 'Secondary special constraints retained');
const primaryHtml = execFileSync('php',[path.join(root,'ypologismos-morion-metathesis-protovathmia.php')],{encoding:'utf8'});
const secondaryHtml = execFileSync('php',[path.join(root,'ypologismos-morion-metathesis.php')],{encoding:'utf8'});
for(const [name, html, field, checkbox] of [['Primary',primaryHtml,'primaryCoServiceCaseField','primaryCoservice'],['Secondary',secondaryHtml,'transferCoServiceCaseField','coService']]){
  assert.ok(html.includes('id="'+field+'"'), name+' dropdown exists');
  assert.ok(html.includes('id="'+checkbox+'"'), name+' checkbox exists');
  assert.ok(html.includes('transfer-social-input.js'), name+' loads shared handler');
}
console.log('PASS shared spouse panel initial/open/close/reopen, both grade connections and independent eligibility');
