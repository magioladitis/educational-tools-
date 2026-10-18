'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.resolve(__dirname,'..');
const read=f=>fs.readFileSync(path.join(root,f),'utf8');
const w={};vm.runInNewContext(read('includes/transfer-coservice-resources.js'),{window:w});
const shared=w.EducationTransferCoServiceResources;
const detail=shared.legalDetail('lawyer');
assert.match(detail,/Δικηγορικού Συλλόγου/);
assert.match(detail,/δεν απαιτείται διετής υπηρεσία/);
assert.match(detail,/μία μόνο περιοχή/);
assert.match(shared.evidenceRecency(),/τελευταίου τριμήνου/);
assert.match(shared.legalSource('primary'),/138023\/Ε2/);
assert.match(shared.legalSource('secondary'),/138014\/Ε2/);
assert.match(shared.legalDetail('private_employee'),/ασφαλιστικού φορέα/);
assert.equal(shared.render('lawyer'),'','Do not claim lawyers use private-sector EFKA resources');
const primary=read('includes/transfer-primary.js');
const secondary=read('includes/transfer-documents.js');
assert.match(primary,/resources\.legalDetail\s*\? resources\.legalDetail\(choice\)/,'Primary shows common legal detail');
assert.match(primary,/resources\.legalSource\('primary',choice\)/,'Primary prints and displays its own circular source');
assert.match(primary,/resources\.evidenceRecency\(\)/,'Primary includes three-month evidence rule');
assert.match(primary,/Περισσότερες πληροφορίες/);
assert.match(secondary,/shared\.legalDetail\(value\)/,'Secondary uses same lawyer evidence');
for(const page of ['ypologismos-morion-metathesis-protovathmia.php','ypologismos-morion-metathesis.php']){
 const html=read(page);
 assert.ok(html.indexOf('transfer-coservice-resources.js')<html.indexOf(page.includes('protovathmia')?'transfer-primary.js':'transfer-documents.js'));
}
console.log('PASS v3.22.184 shared lawyer proof, 2 grades, screen and print, correct references');
