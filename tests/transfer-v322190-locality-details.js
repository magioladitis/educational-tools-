'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.resolve(__dirname,'..');
const read=f=>fs.readFileSync(path.join(root,f),'utf8');
const w={};vm.runInNewContext(read('includes/transfer-locality-resources.js'),{window:w});
const loc=w.EducationTransferLocalityResources;
for(const grade of ['primary','secondary']){
 const text=loc.detail(grade), src=loc.source(grade);
 for(const phrase of ['πιστοποιητικό γέννησης','πιστοποιητικό οικογενειακής κατάστασης','ημερομηνία κτήσης δημοτικότητας','τουλάχιστον δύο έτη','ημέρα υποβολής','αυτεπαγγέλτως','ίδιας περιοχής'])assert.ok(text.includes(phrase),`${grade} ${phrase}`);
 assert.match(src,/παρ\. 9 άρθρου 16 π\.δ\. 50\/1996/);
 assert.match(src,grade==='primary'?/138023\/Ε2/:/138014\/Ε2/);
 assert.match(text,grade==='primary'?/ΔΠΕ/:/ΔΔΕ/);
 assert.match(loc.summary(grade),/συμπληρωμένη διετία/);
}
const sec=read('includes/transfer-documents.js'), prim=read('includes/transfer-primary.js');
assert.match(sec,/EducationTransferLocalityResources\.detail\('secondary'\)/);
assert.match(sec,/EducationTransferLocalityResources\.source\('secondary'\)/);
assert.match(prim,/EducationTransferLocalityResources\.detail\('primary'\)/);
assert.match(prim,/EducationTransferLocalityResources\.source\('primary'\)/);
assert.match(prim,/d\.id==='locality' && global\.EducationTransferLocalityResources/);
assert.match(prim,/extra=global\.EducationTransferLocalityResources\.detail\('primary'\)/,'Primary printer includes details');
assert.doesNotMatch(sec,/Ελέγξτε βεβαίωση δημοτικότητας, χρόνο κτήσης της/);
for(const page of ['ypologismos-morion-metathesis.php','ypologismos-morion-metathesis-protovathmia.php']){
 const s=read(page);assert.ok(s.indexOf('transfer-locality-resources.js')<s.indexOf(page.includes('protovathmia')?'transfer-primary.js':'transfer-documents.js'),page);
}
console.log('PASS v3.22.190 locality evidence, 2-year rule, grade-specific autepaggelta, screen and print');
