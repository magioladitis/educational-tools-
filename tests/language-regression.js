'use strict';
const fs = require('fs');
const vm = require('vm');
const path = require('path');

const context = { console, globalThis: {} };
context.globalThis = context;
vm.createContext(context);
vm.runInContext(fs.readFileSync(path.join(__dirname, '..', 'includes', 'language-calculations.js'), 'utf8'), context);
const L = context.EducationLanguages;

let pass = 0;
let fail = 0;
function test(name, fn) {
  try {
    fn();
    console.log('✓ ' + name);
    pass++;
  } catch (e) {
    console.error('✗ ' + name + ': ' + e.message);
    fail++;
  }
}
function eq(actual, expected, label) {
  if (actual !== expected) throw new Error((label || 'value') + ` expected ${expected}, got ${actual}`);
}
function ok(value, label) {
  if (!value) throw new Error(label || 'expected truthy');
}
function calc(profile, entries, specialty='') {
  return L.calculate(profile, entries, { specialty });
}

// Profile scoring / limits.
test('PE: excellent=7, very good=5, good=3', () => {
  eq(calc('pe', [{language:'en',level:'excellent'}]).points, 7);
  eq(calc('pe', [{language:'en',level:'very_good'}]).points, 5);
  eq(calc('pe', [{language:'en',level:'good'}]).points, 3);
});
test('PE: two different excellent languages = 14', () => {
  eq(calc('pe', [{language:'en',level:'excellent'},{language:'de',level:'excellent'}]).points, 14);
});
test('EEP: up to two languages at 7/5/3', () => {
  const r = calc('eep', [{language:'en',level:'excellent'},{language:'fr',level:'very_good'}]);
  eq(r.points, 12); eq(r.maxLanguages, 2);
});
test('EBP: up to two languages at 8/6/4', () => {
  const r = calc('ebp', [{language:'en',level:'excellent'},{language:'fr',level:'very_good'}]);
  eq(r.points, 14); eq(r.maxLanguages, 2);
});
test('TE: one language only at 20/15/10', () => {
  eq(calc('te', [{language:'en',level:'excellent'}]).points, 20);
  eq(calc('te', [{language:'en',level:'very_good'}]).points, 15);
  eq(calc('te', [{language:'en',level:'good'}]).points, 10);
  eq(calc('te', [{language:'en',level:'good'},{language:'fr',level:'excellent'}]).points, 20);
});
test('TE: defensive multiple entries keep only the highest language', () => {
  const r = calc('te', [
    {language:'en',level:'good'},
    {language:'fr',level:'excellent'},
    {language:'de',level:'very_good'}
  ]);
  eq(r.points, 20); eq(r.accepted.length, 1); eq(r.accepted[0].key, 'fr');
  eq(r.ignoredByLimit.length, 2);
  ok(r.warnings.some(w => w.includes('μία μόνο ξένη γλώσσα')));
});

// Own-language exclusion for PE foreign-language branches.
const ownCases = [
  ['ΠΕ05','fr','Γαλλική'],
  ['ΠΕ06','en','Αγγλική'],
  ['ΠΕ07','de','Γερμανική'],
  ['ΠΕ34','it','Ιταλική'],
  ['ΠΕ40','es','Ισπανική']
];
ownCases.forEach(([specialty, language, label]) => {
  test(`${specialty}: own ${label} language scores zero`, () => {
    const r = calc('pe', [{language,level:'excellent'}], specialty);
    eq(r.points, 0); eq(r.excludedLanguage, language); eq(r.accepted.length, 0);
    ok(r.warnings.some(w => w.includes('δεν μοριοδοτείται')));
  });
});
test('PE06: another language still scores normally', () => {
  const r = calc('pe', [{language:'en',level:'excellent'},{language:'fr',level:'excellent'}], 'ΠΕ06');
  eq(r.points, 7); eq(r.accepted.length, 1); eq(r.accepted[0].key, 'fr');
});
test('PE06: cannot bypass own-language exclusion via Other = Αγγλικά/English', () => {
  for (const alias of ['Αγγλικά','Αγγλική','English']) {
    const r = calc('pe', [{language:'other',otherText:alias,level:'excellent'}], 'ΠΕ06');
    eq(r.points, 0, alias); eq(r.excludedLanguage, 'en');
  }
});
test('known language typed as Other deduplicates against normal option', () => {
  const r = calc('pe', [
    {language:'en',level:'good'},
    {language:'other',otherText:'Αγγλικά',level:'excellent'}
  ]);
  eq(r.points, 7); eq(r.accepted.length, 1); eq(r.accepted[0].key, 'en'); eq(r.duplicates.length, 1);
});
test('EEP does not inherit PE own-language exclusion', () => {
  eq(calc('eep', [{language:'en',level:'excellent'}], 'ΠΕ06').points, 7);
});

// Duplicates and custom languages.
test('same named language twice: only highest level counts', () => {
  const r = calc('pe', [{language:'en',level:'good'},{language:'en',level:'excellent'}]);
  eq(r.points, 7); eq(r.accepted.length, 1); eq(r.duplicates.length, 1);
});
test('same custom language twice (case/accent-insensitive): only highest counts', () => {
  const r = calc('pe', [
    {language:'other',otherText:'Πορτογαλική',level:'good'},
    {language:'other',otherText:'  πορτογαλικη ',level:'excellent'}
  ]);
  eq(r.points, 7); eq(r.accepted.length, 1); eq(r.duplicates.length, 1);
});
test('two different custom languages both count in two-language profile', () => {
  const r = calc('pe', [
    {language:'other',otherText:'Πορτογαλική',level:'excellent'},
    {language:'other',otherText:'Ρωσική',level:'very_good'}
  ]);
  eq(r.points, 12); eq(r.accepted.length, 2);
});
test('level without named language does not score', () => {
  const r = calc('pe', [{language:'',level:'excellent'}]);
  eq(r.points, 0); eq(r.missingLanguage.length, 1);
});
test('custom Other without name does not score', () => {
  const r = calc('pe', [{language:'other',otherText:'',level:'excellent'}]);
  eq(r.points, 0); eq(r.missingLanguage.length, 1);
});

// calculatePair backward compatibility used by ΔΗΜ.Ω.Σ. secondment.
test('calculatePair compatibility: duplicate highest + cap + exclusion', () => {
  const r = L.calculatePair([
    {language:'en',points:1},
    {language:'en',points:3},
    {language:'fr',points:2},
    {language:'de',points:3}
  ], {excluded:['fr'], cap:5});
  eq(r.points, 5); eq(r.accepted.length, 2); eq(r.duplicates.length, 1); eq(r.excludedEntries.length, 1);
});

console.log(`\nPASS: ${pass}`);
console.log(`FAIL: ${fail}`);
if (fail) process.exit(1);
