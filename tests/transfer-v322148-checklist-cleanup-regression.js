'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const root=path.join(__dirname,'..');
const docsCode=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
const eligibilityCode=fs.readFileSync(path.join(root,'includes/transfer-eligibility.js'),'utf8');
const pageCode=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis.php'),'utf8');
const mock={};
const elem=id=>mock[id]||(mock[id]={value:'',checked:false,selectedOptions:[{textContent:'Τρέχουσα αίτηση'}]});
const inputs={mode:'kedasy',transferEaeOrigin:'general',transferEaeArea:'other',transferEaeDestination:'kedasy',
 transferEaeBranch:'pe03',transferEaeQualification:'masters',transferEaeTeachingYears:'3',transferEaeExperienceYears:'0',
 transferEaeAreaYears:'2',transferEaePreviousYear:'none',transferMusicPreviousYear:'none',transferArtPreviousYear:'none',
 transferCoServiceCase:'public_organic',transferGeneralOrigin:'general',eligibleChildren:'0',msdMode:'direct'};
Object.entries(inputs).forEach(([id,value])=>elem(id).value=value);
const document={getElementById:elem,querySelectorAll(){return [];}};
const window={}; vm.runInNewContext(eligibilityCode,{document,window});vm.runInNewContext(docsCode,{document,window});
const guide=window.EducationTransferDocuments;
let checked=0;const ok=(v,msg)=>{assert.ok(v,msg);checked++;};
let rows=guide.build();
ok(!rows.some(d=>d.id==='eae-kedasy'),'KEDASY non-document checklist row removed');
ok(rows.some(d=>d.id==='eae-masters'),'KEDASY qualification evidence preserved');
ok(!rows.some(d=>d.id==='eae-dossier') && pageCode.includes('id="transferEaeDossierAdvice"'),'EAE dossier status shown by input, not duplicated in checklist');
ok(pageCode.includes('transferEaeAdvice'),'EAE eligibility display preserved');
let result=window.EducationTransferEligibility.eae({origin:'general',teachingYears:3,destination:'kedasy',qualification:'masters',branch:'pe11'});
ok(result.advice.some(a=>a.includes('ΠΕ02, ΠΕ03 ή ΠΕ04')),'KEDASY PE11 ineligibility still checked');
result=window.EducationTransferEligibility.eae({origin:'general',teachingYears:3,destination:'kedasy',qualification:'masters',branch:'pe03'});
ok(!result.advice.some(a=>a.includes('Για ΚΕΔΑΣΥ ελέγχεται κλάδος')),'KEDASY PE03 branch still accepted for that gate');
elem('mode').value='region'; elem('coService').checked=true;
rows=guide.build(); let cos=rows.find(d=>d.id==='coservice');
ok(!!cos,'co-service documentation retained');
ok(cos.tag==='','redundant co-service category removed');
ok(guide.submissionChannel(cos).text.includes('email στη ΔΔΕ'),'co-service submission instruction retained');
for (const [id,expected] of [['other','Υπό επιβεβαίωση'],['substitute_teacher','Δεν θεμελιώνεται']]) {
 elem('transferCoServiceCase').value=id;rows=guide.build();cos=rows.find(d=>d.id==='coservice');
 if (id==='substitute_teacher')elem('coService').checked=false;
 ok(!!cos&&cos.tag.includes(expected),`${id} meaningful warning preserved`);
}
ok(!docsCode.includes('Δικαιολογητικά βάσει επιλογής'),'removed obsolete label everywhere, including print view');
ok(!docsCode.includes("entry('eae-kedasy'"),'no KEDASY-only checklist entry');
ok(docsCode.includes("(d.tag && ['family','music-common'].indexOf(d.id)===-1?' <small>("),'print suppresses empty and redundant tags');
console.log('PASS',checked,'v3.22.148 checklist cleanup assertions');
