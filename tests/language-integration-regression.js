'use strict';
const fs = require('fs');
const path = require('path');
const root = path.resolve(__dirname, '..');

const pages = [
  ['ypologismos-morion.php', 'pe', true],
  ['ypologismos-morion-1gt-2024.php', 'te', false],
  ['ypologismos-morion-1ea-2025.php', 'ebp', false],
  ['ypologismos-morion-2ea-2025.php', 'eep', false],
  ['ypologismos-morion-3ea-2025.php', 'pe', true],
  ['ypologismos-morion-4ea-2025.php', 'te', false],
  ['ypologismos-morion-onaseia.php', 'pe', true]
];

let pass = 0, fail = 0;
function test(name, fn) {
  try { fn(); console.log('✓ ' + name); pass++; }
  catch (e) { console.error('✗ ' + name + ': ' + e.message); fail++; }
}
function ok(v, msg) { if (!v) throw new Error(msg || 'assertion failed'); }
function no(v, msg) { if (v) throw new Error(msg || 'unexpected value'); }
function text(file) { return fs.readFileSync(path.join(root, file), 'utf8'); }

for (const [file, profile, needsSpecialty] of pages) {
  test(`${file}: shared component/controller/profile`, () => {
    const s = text(file);
    ok(s.includes("includes/components/asep-language-selector.php"), 'missing component require');
    ok(s.includes('renderAsepLanguageSelector'), 'missing component render');
    ok(s.includes("'profile' => '" + profile + "'"), 'wrong profile');
    ok(s.includes('includes/language-calculations.js'), 'missing common calculation script');
    ok(s.includes('includes/asep-language-selector.js'), 'missing common UI controller');
    if (needsSpecialty) ok(s.includes("'specialty_id' => 'specialty'"), 'missing specialty link');
  });
  test(`${file}: no legacy local language UI/logic`, () => {
    const s = text(file);
    const forbidden = [
      'languageOwn', 'syncLanguageUI', 'languageResult(', 'calcLanguages(',
      'langName1', 'langName2', 'languageOther1', 'languageOther2',
      'onaseiaNamedLanguages', 'syncOnaseiaLanguageOptions'
    ];
    for (const token of forbidden) no(s.includes(token), 'legacy token remains: ' + token);
  });
}

test('academic-calculations.js has no second language engine', () => {
  const s = text('includes/academic-calculations.js');
  for (const token of ['OWN_LANGUAGE_BY_SPECIALTY','EXCLUDED_LANGUAGE_BY_SPECIALTY','LANGUAGE_NAMES','LEVEL_POINTS','calculateLanguages']) {
    no(s.includes(token), 'duplicate language engine remains: ' + token);
  }
  ok(s.includes('languagePoints'), 'academic core must accept shared language points');
});

test('language-calculations.js is the only ASEP branch-language map', () => {
  const s = text('includes/language-calculations.js');
  for (const [branch, lang] of [['ΠΕ05','fr'],['ΠΕ06','en'],['ΠΕ07','de'],['ΠΕ34','it'],['ΠΕ40','es']]) {
    ok(s.includes("'" + branch + "': '" + lang + "'"), 'missing ' + branch + ' mapping');
  }
});

test('controller disables and clears own branch language', () => {
  const s = text('includes/asep-language-selector.js');
  ok(s.includes("option.disabled = option.value === excludedLanguage || selectedElsewhere.has(option.value)"), 'disabled-option lock missing');
  ok(s.includes("if (excludedLanguage && select.value === excludedLanguage)"), 'stale branch-language clear missing');
  ok(s.includes("select.value = ''"), 'stale selection is not cleared');
  ok(s.includes("level.value = 'none'"), 'stale level is not cleared');
});

test('controller prevents duplicate named/custom languages', () => {
  const s = text('includes/asep-language-selector.js');
  ok(s.includes('selectedElsewhere.has(option.value)'), 'named duplicate lock missing');
  ok(s.includes('preventDuplicateOther'), 'custom-language duplicate guard missing');
  ok(s.includes("Η ίδια ξένη γλώσσα έχει ήδη δηλωθεί"), 'duplicate warning missing');
});

test('one-language pages use TE profile, not a second-row workaround', () => {
  for (const file of ['ypologismos-morion-1gt-2024.php','ypologismos-morion-4ea-2025.php']) {
    const s = text(file);
    ok(s.includes("'profile' => 'te'"), file + ' not using TE profile');
    no(s.includes('2η ξένη γλώσσα'), file + ' still renders local second language');
  }
});

test('ΔΗΜ.Ω.Σ. legacy calculatePair consumer remains wired', () => {
  const s = text('ypologismos-morion-apospasis-dimos.php');
  ok(s.includes('includes/language-calculations.js'), 'missing language-calculations.js');
  ok(s.includes('EducationLanguages.calculatePair'), 'calculatePair compatibility API not used');
});

console.log(`\nPASS: ${pass}`);
console.log(`FAIL: ${fail}`);
if (fail) process.exit(1);
