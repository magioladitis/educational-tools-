'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs');
const vm=require('node:vm');
const path=require('node:path');
const root=path.resolve(__dirname,'..');
const code=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
const css=fs.readFileSync(path.join(root,'assets/common.css'),'utf8');
const map=Object.create(null);
function field(id){return map[id]||(map[id]={value:'region',selectedOptions:[{textContent:'Μετάθεση από περιοχή σε περιοχή'}]});}
const sandbox={window:{},document:{getElementById:field,addEventListener(){}}};
vm.runInNewContext(code,sandbox);
const api=sandbox.window.EducationTransferDocuments;
const types=['region','local','smeae','eae_sensory','kedasy','music','art','intercultural'];
const special=new Set(types.slice(2));
const personal={name:'Δοκιμή Δοκιμαστικός',branch:'ΠΕ03',protocol:'Αρ. Πρωτ. 2026',registry:'123456',home:'ΔΔΕ Κέρκυρας'};
for(const type of types){
 const blank=api.submissionMessageTemplate(type);
 const result=api.submissionMessageTemplate(type,personal);
 assert.ok(blank.subject.includes('[Αριθμός μητρώου]'),type+' legacy placeholders kept');
 assert.ok(result.subject.includes('123456')&&result.subject.includes('Αρ. Πρωτ. 2026'),type+' subject populated');
 assert.ok(result.body.includes('Δοκιμή Δοκιμαστικός')&&result.body.includes('ΠΕ03'),type+' body populated');
 assert.equal(result.general,!special.has(type));
 if(special.has(type)){
  assert.ok(result.subject.startsWith('ΔΕ, ')&&result.body.endsWith('ΔΔΕ Κέρκυρας'),type+' special circular format');
  assert.equal(result.subject.split(', ').length,5);
  assert.equal(result.body.split(', ').length,6);
 }else{
  assert.ok(!result.subject.startsWith('ΔΕ, ')&&!result.body.includes('ΔΔΕ Κέρκυρας'),type+' general circular format');
  assert.equal(result.subject.split(', ').length,3);
  assert.equal(result.body.split(', ').length,5);
 }
 const missing=api.submissionMessageTemplate(type,{name:'',branch:'ΠΕ03'});
 assert.ok(missing.subject.includes('[Αριθμός πρωτοκόλλου')&&missing.body.includes('[Ονοματεπώνυμο]'),type+' optional fields');
}
assert.ok(code.includes('data-transfer-personalize')&&code.includes('data-transfer-personal-field'), 'optional disclosure and fields');
assert.ok(code.includes('if(onSubmissionPersonalInput(e))return'), 'generic rerender must not interrupt editing');
assert.ok(!code.includes('localStorage.')&&!code.includes('sessionStorage.')&&!code.includes('fetch('), 'no storage/network operations');
assert.ok(css.includes('transfer-submission-template__fields'),'responsive fields');
console.log('PASS v3.22.131 8 routes, exact circular ordering, optional fields, privacy, no rerender while typing');
