'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),path=require('node:path');
const root=path.resolve(__dirname,'..');
const read=p=>fs.readFileSync(path.join(root,p),'utf8');
const p=read('ypologismos-morion-metathesis-protovathmia.php');
const s=read('ypologismos-morion-metathesis.php');
const css=read('assets/transfer-level-switch.css');
for(const page of [p,s]) {
  assert.match(page, /<nav class="transfer-level-switch" aria-label="Επιλογή βαθμίδας">/);
  assert.match(page,/edu_asset_url\('assets\/transfer-level-switch.css'\)/);
  assert(!page.includes('Πρωτοβάθμια — γενικές μεταθέσεις'));
  assert(!page.includes('πλήρης 3.22.151'));
  const nav=page.match(/<nav class="transfer-level-switch"[\s\S]*?<\/nav>/)[0];
  assert(nav.indexOf('Πρωτοβάθμια')<nav.indexOf('Δευτεροβάθμια'), 'Same option order');
  assert.equal((nav.match(/aria-current="page"/g)||[]).length,1);
}
assert.match(p,/<strong aria-current="page">Πρωτοβάθμια<\/strong>/);
assert.match(s,/<strong aria-current="page">Δευτεροβάθμια<\/strong>/);
assert.match(p,/<a href="ypologismos-morion-metathesis.php">Δευτεροβάθμια<\/a>/);
assert.match(s,/<a href="ypologismos-morion-metathesis-protovathmia.php">Πρωτοβάθμια<\/a>/);
assert.match(css,/min-height: 44px/);
assert.match(css, /focus-visible/);
assert.match(css, /@media print/);
assert(!read('assets/transfer-primary.css').includes('.primary-transfer-switch'));
console.log('PASS 3.22.156: identical grade navigation on Primary & Secondary');
