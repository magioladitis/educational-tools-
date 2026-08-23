'use strict';
const assert = require('assert');
require('../includes/language-calculations.js');
const L = global.EducationLanguages;
let passed = 0;
function eq(actual, expected, label) { assert.strictEqual(actual, expected, `${label}: expected ${expected}, got ${actual}`); passed++; }
function ok(value, label) { assert.ok(value, label); passed++; }

let r = L.calculatePair([
  {language:'en', points:4},
  {language:'en', points:8}
]);
eq(r.points, 8, 'same language counts highest only');
ok(r.warnings.some(x=>x.includes('Αγγλική')), 'duplicate warning names language');

r = L.calculatePair([
  {language:'en', points:8},
  {language:'fr', points:6}
]);
eq(r.points, 14, 'two different languages both count');

r = L.calculatePair([
  {language:'en', points:7},
  {language:'fr', points:5}
], {excluded:['en']});
eq(r.points, 5, 'appointment language excluded');
ok(r.warnings.some(x=>x.includes('προσόν διορισμού')), 'appointment-language warning');

r = L.calculatePair([{language:'', points:7}]);
eq(r.points, 0, 'level without language scores zero');
ok(r.missingLanguage.length === 1, 'missing language flagged');

r = L.calculatePair([
  {language:'other', otherText:'Πορτογαλική', points:3},
  {language:'other', otherText:'  πορτογαλικη ', points:7}
]);
eq(r.points, 7, 'custom other language normalized and deduplicated');

r = L.calculatePair([
  {language:'en', points:3},
  {language:'de', points:3}
], {cap:5});
eq(r.raw, 6, 'DIMOS language raw before cap');
eq(r.points, 5, 'DIMOS language cap 5');

r = L.calculatePair([
  {language:'en', points:3},
  {language:'de', points:2}
], {excluded:['en'], cap:5});
eq(r.points, 2, 'DIMOS own appointment language excluded');

console.log(`PASS ${passed} language-rule v3.17 regression checks`);
