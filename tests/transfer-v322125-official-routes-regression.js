'use strict';
const fs=require('node:fs');const path=require('node:path');const assert=require('node:assert/strict');const vm=require('node:vm');
const root=path.resolve(__dirname,'..');const read=p=>fs.readFileSync(path.join(root,p),'utf8');
const php=read('ypologismos-morion-metathesis.php'),ui=read('includes/transfer-ui.js'),docs=read('includes/transfer-documents.js');
const modes=['region','music','art','intercultural','local','smeae','eae_sensory','kedasy'];
const codes=['001.ΔΕ001','002.ΜΟΥΣ001','002.ΚΑΛ001','002.ΔΙΑΠ002','003.ΔΕΒ01','001.ΔΕΣΚΕ','001.ΔΕΚΤ','001.ΔΕΣΚΕ.ΚΔΣ'];
for(let i=0;i<modes.length;i++) assert.ok(php.includes(`value="${modes[i]}"`)&&php.includes(codes[i]),`Official route ${modes[i]}`);
assert.equal(((php.match(/<select id="mode">([\s\S]*?)<\/select>/)||[])[1]||'').match(/<option /g).length,8);
assert.ok(ui.includes('function isEaeMode(mode)')&&ui.includes('syncEaeApplicationRoute(mode)'));
assert.ok(php.includes('transferInterculturalNotes')&&php.includes('Αλληλεπίδραση με άλλες αιτήσεις')&&php.includes('Μετάθεση προς Γενική μέσα στην ίδια περιοχή'));
const elems=Object.create(null);
function el(id){return elems[id]||(elems[id]={value:'',checked:false,selectedOptions:[{textContent:'Τρέχουσα διαδικασία'}]})}
const defaults={mode:'intercultural',transferInterculturalOrganic:'yes',transferInterculturalCurrent:'no',transferInterculturalYears:'0',transferInterculturalVacancy:'yes',transferInterculturalLanguageCase:'usual',transferInterculturalPreviousYear:'none',transferCoServiceCase:'unspecified',msdMode:'direct',transferEaeOrigin:'general',transferEaeArea:'other',transferEaeDestination:'smeae',transferEaeBranch:'other',transferEaeQualification:'masters',transferEaeTeachingYears:'5',transferEaeExperienceYears:'0',transferEaeAreaYears:'2',transferEaePreviousYear:'none',transferGeneralOrigin:'general',transferGeneralEaeYears:'0',transferGeneralEaeAreaYears:'0',transferGeneralEaeSinceTransfer:'0',transferMusicPreviousYear:'none',transferArtPreviousYear:'none',transferEducationType:'general',transferMusicException:'none',transferMusicSubject:'unspecified',transferMusicTeachingYears:'5',transferArtBranch:'general',transferArtOrientation:'',transferArtTeachingYears:'5',transferArtException:'none',eligibleChildren:'0'};
for(const [k,v] of Object.entries(defaults))el(k).value=v;
const document={getElementById:el,querySelectorAll(){return [];},addEventListener(){}};
const window={};vm.runInNewContext(read('includes/transfer-eligibility.js'),{document,window});vm.runInNewContext(docs,{document,window});
const engine=window.EducationTransferDocuments;
let rows=engine.build();
for(const id of ['ic-qual-order','ic-order','ic-same-area'])assert.equal(rows.some(x=>x.id===id),false,`${id} must not be a checklist task`);
assert.ok(rows.some(x=>x.id==='ic-cv'),'Core checklist remains');
for(const [mode,destination,expectedId] of [['smeae','smeae',null],['eae_sensory','deaf','eae-sign'],['eae_sensory','blind','eae-braille'],['kedasy','kedasy','eae-kedasy']]){
 el('mode').value=mode;el('transferEaeDestination').value=destination;
 rows=engine.build();if(expectedId)assert.ok(rows.some(x=>x.id===expectedId),`Specialized EAE route ${mode}/${destination}`);
 assert.equal(rows.some(x=>x.id==='eae-service'),false,`No duplicate EAE service checklist for ${mode}/${destination}`);
 if(mode==='smeae')assert.ok(rows.some(x=>x.id==='eae-masters'),'Relevant EAE qualification evidence remains in checklist');
 assert.equal(rows.some(x=>x.id==='ic-qual-order'),false);
 assert.ok(engine.submissionGuideData().title.includes(mode==='kedasy'?'ΚΕΔΑΣΥ':mode==='eae_sensory'?'Κωφών/Τυφλών':'ΣΜΕΑΕ'));
}
const eligibility=window.EducationTransferEligibility;
let bad=eligibility.eae({origin:'general',teachingYears:3,destination:'kedasy',qualification:'masters',branch:'pe11'});
assert.ok(bad.advice.some(x=>x.includes('ΠΕ02, ΠΕ03 ή ΠΕ04')),'KEDASY blocks unsupported branch');
let good=eligibility.eae({origin:'general',teachingYears:3,destination:'kedasy',qualification:'masters',branch:'pe03'});
assert.ok(!good.advice.some(x=>x.includes('Για ΚΕΔΑΣΥ ελέγχεται κλάδος')),'KEDASY allows PE03 branch, pending organizational check');
assert.ok(docs.includes("$('mode').value==='intercultural'"),'Print includes intercultural notes selectively');
console.log('PASS 8 official application types, 3 EAE routes, 2 intercultural notes, submission & branch checks');
