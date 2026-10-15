'use strict';
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),assert=require('node:assert/strict');
const root=path.resolve(__dirname,'..');
const php=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis.php'),'utf8');
const expected={
 familyStatusEligible:'Γάμος/Σύμφωνο Συμβίωσης/Γονέας με επιμέλεια (+4)',
 coService:'Συνυπηρέτηση στην περιοχή/το δήμο που εξετάζω (+4)',
 locality:'Εντοπιότητα στην περιοχή/το δήμο που εξετάζω (+4)'
};
let n=0;
for (const [id,label] of Object.entries(expected)) {
 const exact=php.match(new RegExp('<input id="'+id+'"[^>]*>\\s*([^<]+)</label>'));
 assert.ok(exact,`label present: ${id}`);
 assert.equal(exact[1].trim(),label);
 n++;
}
const js=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
const elements={mode:{value:'smeae',checked:false}};
const el=(id)=>elements[id]||(elements[id]={value:'',checked:false,selectedOptions:[{textContent:'Απλή επιλογή'}]});
let html='';
const checklist={querySelectorAll(){return [];},set innerHTML(x){html=x;},get innerHTML(){return html;}};
const document={getElementById(id){return id==='transferDocumentChecklist'?checklist:el(id);},querySelectorAll(){return [];}};
const window={};
vm.runInNewContext(js,{window,document});
const guide=window.EducationTransferDocuments;
for(const [mode,id] of [['smeae','eae-dossier'],['music','music-dossier'],['art','art-dossier'],['intercultural','intercultural-dossier']]){
 el('mode').value=mode;
 guide.render();
 const m=html.match(new RegExp('<article class="([^"]+)" data-doc-card="'+id+'">([\\s\\S]*?)<\\/article>'));
 assert.ok(m,`${id} checklist item exists`);
 assert.ok(m[1].includes('is-compact'),`${id} is compact`);
 assert.ok(!m[2].includes('edu-transfer-doc__summary'),`${id} has no outside summary`);
 assert.ok(m[2].includes('<summary>Περισσότερες πληροφορίες</summary>'),`${id} disclosure retained`);
 assert.ok(m[2].includes('edu-transfer-doc__detail'),`${id} detail retained`);
 assert.ok(guide.build().find(x=>x.id===id).detail,`${id} full print data retained`);
 n+=6;
}
assert.ok(js.includes('function print(){var rows=build()') && js.includes('escape(d.detail)'), 'printing not truncated');n++;
assert.ok(fs.readFileSync(path.join(root,'includes/config.php'),'utf8').includes("'3.22.122'"));n++;
console.log(`PASS ${n} checks — three labels, four dossier modes and complete details`);
