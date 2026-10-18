'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const cp = require('node:child_process');
const root = path.resolve(__dirname, '..');
const primary = fs.readFileSync(path.join(root, 'ypologismos-morion-metathesis-protovathmia.php'), 'utf8');
const secondary = fs.readFileSync(path.join(root, 'ypologismos-morion-metathesis.php'), 'utf8');
const pages = [
  ['primary', 'ypologismos-morion-metathesis-protovathmia.php', 'primaryServiceMode', 'primaryMsd'],
  ['secondary', 'ypologismos-morion-metathesis.php', 'serviceMode', 'msdDirectPoints']
];
for (const [level, file, serviceModeId, msdInputId] of pages) {
  const result = cp.spawnSync('php', [file], {cwd: root, encoding: 'utf8', timeout: 12000});
  assert.equal(result.status, 0, `${level}: PHP rendering: ${result.stderr}`);
  const html = result.stdout;
  const label = `<label for="${serviceModeId}">Καταχώριση συνολικής υπηρεσίας</label>`;
  assert.equal(html.split(label).length - 1, 1, `${level}: expected identical service mode label`);
  assert.match(html, new RegExp(`<select id="${serviceModeId}"`));
  assert.match(html, new RegExp(`<input id="${msdInputId}"`));
  assert.ok(!html.includes('Έως δύο δεκαδικά.'), `${level}: redundant inline hint`);
  assert.ok(!html.includes('Δώστε τον ακριβή αριθμό Μ.Σ.Δ.'), `${level}: redundant MSD instruction`);
  assert.ok(html.includes('Οδηγίες') && html.includes('Πού βλέπω τις υπηρετήσεις μου;'), `${level}: shared help remains`);
}
assert.match(secondary, /id="msdDirectPoints"[^>]*aria-describedby="msdDirectStatus"/);
assert.ok(!secondary.includes('id="msdDirectHelp"'));
assert.ok(!secondary.includes('Τρόπος καταχώρισης μορίων προϋπηρεσίας / συνολικής αναγνωρισμένης υπηρεσίας'));
// Do not touch mode or input identifiers that calculation modules bind to.
assert.match(primary, /id="primaryServiceMode"/);
assert.match(primary, /id="primaryMsd"/);
assert.match(secondary, /id="serviceMode"/);
assert.match(secondary, /id="msdDirectPoints"/);
console.log('PASS v3.22.187 primary/secondary service labels and streamlined MSD hints');
