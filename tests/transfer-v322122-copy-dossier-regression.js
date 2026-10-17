'use strict';
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),assert=require('node:assert/strict');
const root=path.resolve(__dirname,'..');
const php=require('node:child_process').execFileSync('php',[path.join(root,'ypologismos-morion-metathesis.php')],{encoding:'utf8'}) + '\n' + fs.readFileSync(path.join(root,'ypologismos-morion-metathesis.php'),'utf8');
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
for(const [mode,id,statusId] of [['smeae','eae-dossier','transferEaeDossierAdvice'],['music','music-dossier','transferMusicDossierAdvice'],['art','art-dossier','transferArtDossierAdvice'],['intercultural','intercultural-dossier','transferInterculturalDossierAdvice']]){
 el('mode').value=mode;
 guide.render();
 assert.ok(!html.includes('data-doc-card="'+id+'"'),`${id} is not repeated as a checklist item`);
 assert.ok(php.includes('id="'+statusId+'"'),`${id} live status remains by its input`);
 n+=2;
}
assert.ok(js.includes('function print(){var rows=build()') && js.includes('escape(d.detail)'), 'printing not truncated');n++;
assert.match(fs.readFileSync(path.join(root,'includes/config.php'),'utf8'),/EDU_TOOLS_VERSION', '3\.22\.\d+'/);n++;
console.log(`PASS ${n} checks — three labels, four dossier modes and complete details`);
