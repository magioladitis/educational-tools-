'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.join(__dirname,'..');
const src=fs.readFileSync(path.join(root,'includes/transfer-coservice-resources.js'),'utf8');
const sandbox={window:{}};
vm.runInNewContext(src,sandbox);
const r=sandbox.window.EducationTransferCoServiceResources;
assert.ok(r);
for(const key of ['private_employee','remote_employee','self_employed','private_doctor']){
 assert.match(r.render(key),/e-ΕΦΚΑ/);
 assert.match(r.render(key),/href="https:\/\//);
 assert.match(r.render(key),/τελευταίου τριμήνου/);
 assert.match(r.printText(key),/https:\/\//);
}
assert.match(r.render('private_employee'),/Βεβαίωση προϋπηρεσίας μισθωτών/);
assert.match(r.render('private_employee'),/πληροφοριακός/);
assert.match(r.render('self_employed'),/Δεν αντικαθιστά/);
assert.match(r.render('self_employed'),/για ΑΣΕΠ/);
for(const key of ['unspecified','public_organic','teacher_organic','substitute_teacher','public_contract','lawyer']){
 assert.equal(r.render(key),'',key+' should not be linked to private-sector documents');
}
const secondary=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
const primary=fs.readFileSync(path.join(root,'includes/transfer-primary.js'),'utf8');
assert.match(secondary,/global\.EducationTransferCoServiceResources\.render/);
assert.match(primary,/resources\.render\(choice\)/,'Primary renders common resources through a local reference');
for(const file of ['ypologismos-morion-metathesis.php','ypologismos-morion-metathesis-protovathmia.php']){
 const php=fs.readFileSync(path.join(root,file),'utf8');
 assert.match(php,/transfer-coservice-resources\.js/);
}
console.log('PASS v3.22.174 shared co-service resources and conservative evidence routes');
