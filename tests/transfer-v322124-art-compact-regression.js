'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const root = path.resolve(__dirname, '..');
const code = fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
const elems = Object.create(null);
function el(id){return elems[id]||(elems[id]={value:'', checked:false, selectedOptions:[{textContent:'Προεπιλογή'}], addEventListener(type,cb){this.handlers=this.handlers||{};this.handlers[type]=cb;}})}
const ids = {
  mode:'art',transferArtBranch:'general',transferArtOrientation:'',transferArtTeachingYears:'5',transferArtException:'none',transferArtPreviousYear:'none',
  transferMusicPreviousYear:'none',transferEducationType:'general',transferMusicException:'none',transferMusicSubject:'unspecified',transferMusicTeachingYears:'5',
  transferEaeOrigin:'general',transferEaeArea:'other',transferEaeDestination:'smeae',transferEaeBranch:'other',transferEaeQualification:'masters',transferEaeTeachingYears:'5',transferEaeExperienceYears:'0',transferEaeAreaYears:'2',transferEaePreviousYear:'none',
  transferGeneralOrigin:'general', transferGeneralEaeYears:'0',transferGeneralEaeAreaYears:'0',transferGeneralEaeSinceTransfer:'0',
  eligibleChildren:'0',msdMode:'direct',msdDirectPoints:'30',serviceYears:'0',serviceMonths:'0',serviceDays:'0',transferCoServiceCase:'unspecified'
};
for(const [k,v] of Object.entries(ids))el(k).value=v;
for(const k of ['transferArtOrganic','transferArtNewDocuments','transferMusicOrganic','transferMusicNewDocuments','transferEaeNewly','transferEaeNewDocuments','transferGeneralEaeOrganic','familyStatusEligible','coService','locality'])el(k).checked=k.endsWith('Organic');
let html='',printed='';
const rootNode={querySelectorAll:()=>[],get innerHTML(){return html;},set innerHTML(v){html=v;}};
const document={getElementById(id){if(id==='transferDocumentChecklist')return rootNode;if(id==='transferSubmissionGuidanceBody')return {innerHTML:''};if(id==='transferDocumentProgress')return {textContent:''};return el(id);},querySelectorAll(){return [];},addEventListener(){}};
const window={open:()=>({document:{write(s){printed=s;},close(){}},focus(){},print(){}})};
vm.runInNewContext(fs.readFileSync(path.join(root,'includes/transfer-eligibility.js'),'utf8'),{document,window});
vm.runInNewContext(code,{document,window});
const guide=window.EducationTransferDocuments;
const idsCompact=['art-cv','art-inventory','art-degrees','art-publications'];
const rows=guide.build();
assert.equal(rows.filter(x=>x.id==='art-no-scoring').length,0,'duplicate art scoring advisory removed as a checklist task');
for(const id of idsCompact){assert.ok(rows.some(x=>x.id===id),'full row still exists '+id);}
guide.render();
for(const id of idsCompact){
 const m=html.match(new RegExp('<article class="([^"]+)" data-doc-card="'+id+'">([\\s\\S]*?)<\\/article>'));
 assert.ok(m,'art checklist row rendered: '+id);
 assert.ok(m[1].includes('is-compact'),'compact card '+id);
 assert.ok(!m[2].includes('edu-transfer-doc__summary'),'no repeated summary outside disclosure: '+id);
 assert.ok(!m[2].includes('edu-transfer-doc__category'),'no redundant category outside disclosure: '+id);
 assert.ok(m[2].includes('<summary>Περισσότερες πληροφορίες</summary>'),'disclosure retained '+id);
 assert.ok(m[2].includes('edu-transfer-doc__detail'),'full detail in disclosure '+id);
}
assert.ok(!html.includes('data-doc-card="art-no-scoring"'),'obsolete checklist not rendered');
// The label must still be clear outside the checklist, and detailed text must remain printable.
guide.init();
assert.ok(el('transferPrintChecklist').handlers.click,'print action installed');
el('transferPrintChecklist').handlers.click();
for(const id of idsCompact){
 const d=rows.find(x=>x.id===id);
 assert.ok(printed.includes(d.title),'print title retained: '+id);
 assert.ok(printed.includes(d.detail.slice(0,40)),'full detail retained in print: '+id);
}
assert.ok(!printed.includes('Αξιολόγηση προσόντων χωρίς μόρια'),'obsolete task not printed');
el('mode').value='region';assert.ok(guide.build().some(x=>x.id==='record'),'general checklist unaffected');
el('mode').value='smeae';assert.ok(!guide.build().some(x=>x.id==='eae-service'),'EAE eligibility handled in dedicated panel, not in checklist');
assert.ok(guide.build().some(x=>x.id==='eae-masters'),'Qualification evidence remains available in EAE checklist');
console.log('PASS 5 compact Artistic School cards, removed redundant task, printing and other routes');
