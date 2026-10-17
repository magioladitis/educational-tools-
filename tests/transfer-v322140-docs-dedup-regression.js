'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.resolve(__dirname,'..');
const page=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis.php'),'utf8');
const docs=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
const css=fs.readFileSync(path.join(root,'assets/common.css'),'utf8');
let checks=0; const ok=(value,name)=>{assert.ok(value,name);checks++;};
ok(!docs.includes("['submission','Οριστικοποίηση ηλεκτρονικής αίτησης'"),'submission no longer a checklist item');
ok(!docs.includes('SUMMARIES={\n submission:'),'submission summary not maintained');
ok(page.includes('Σημαντικές προθεσμίες:') && /η προσωρινή αποθήκευση δεν είναι οριστικοποίηση/i.test(page) && page.includes('PDF της οριστικοποιημένης αίτησης'), 'deadline and critical finalization warning preserved once');
ok(page.includes("'summary' => 'Πού αποστέλλονται τα δικαιολογητικά; — Οδηγίες και υπόδειγμα'"),'submission guidance disclosure retained');
ok(!docs.includes('<strong>1. Αίτηση:</strong>') && !docs.includes('<strong>2. Υποβολή εγγράφων:</strong>') && !docs.includes('<strong>3. Τι χρειάζεται από εσάς:</strong>'),'triple summary removed');
ok(!page.includes('Εξατομικευμένες οδηγίες προετοιμασίας με βάση τα δηλωμένα στοιχεία'),'redundant counselor preface removed');
ok(page.includes('id="transferDocumentActionSummary"') && page.includes('class="transfer-doc-action-summary" role="note" hidden'),'special warning hidden by default');
ok(css.includes('.transfer-doc-action-summary[hidden] {display:none!important}'),'hidden warning does not occupy space');
let fields={};function el(id){return fields[id]||(fields[id]={value:'',checked:false,selectedOptions:[{textContent:'Επιλογή'}]});}
Object.entries({mode:'region',serviceMode:'points',msdMode:'direct',eligibleChildren:'0',transferGeneralOrigin:'general',transferGeneralEaeYears:'0',transferGeneralEaeAreaYears:'0',transferGeneralEaeSinceTransfer:'0',transferEaeOrigin:'general',transferEaeArea:'other',transferEaeDestination:'smeae',transferEaeBranch:'pe03',transferEaeQualification:'masters',transferEaeTeachingYears:'5',transferEaeExperienceYears:'0',transferEaeAreaYears:'2',transferEaePreviousYear:'none',transferEducationType:'general',transferMusicSubject:'unspecified',transferMusicTeachingYears:'5',transferMusicException:'none',transferMusicPreviousYear:'none',transferArtBranch:'general',transferArtOrientation:'',transferArtTeachingYears:'5',transferArtException:'none',transferArtPreviousYear:'none',transferInterculturalBranch:'',transferInterculturalCurrent:'no',transferInterculturalOrganic:'unknown',transferInterculturalVacancy:'unknown',transferInterculturalLanguageCase:'unknown',transferInterculturalPreviousYear:'none',transferCoServiceCase:'unspecified',transferSpecialCase:'none'}).forEach(([id,value])=>el(id).value=value);
const document={getElementById:el,querySelectorAll(){return[];},addEventListener(){}};let win={};vm.runInNewContext(fs.readFileSync(path.join(root,'includes/transfer-eligibility.js'),'utf8'),{document,window:win});vm.runInNewContext(docs,{document,window:win});
const api=win.EducationTransferDocuments;
for(const mode of ['region','local','smeae','eae_sensory','kedasy','music','art','intercultural']){
 el('mode').value=mode;
 const rows=api.build();
 ok(rows.every(x=>x.id!=='submission'),mode+' excludes application finalization');
 ok(api.submissionGuideData().lines.length>=2,mode+' retains submission instructions');
}
el('mode').value='region';
ok(api.build().some(x=>x.id==='record'),'registry checks preserved');
ok(api.build().find(x=>x.id==='record').detail.includes('επίσημα μόρια συνολικής υπηρεσίας'),'new points mode gets appropriate registry guidance');
el('serviceMode').value='periods';
ok(api.build().find(x=>x.id==='record').detail.includes('μη επικαλυπτόμενες περιόδους'),'service-period guidance preserved');
el('transferSpecialCase').value='health_committee';
ok(api.build().some(x=>x.id==='health-committee'),'special health procedure preserved');
el('transferSpecialCase').value='health_kepa';
ok(api.build().some(x=>x.id==='health-kepa'),'KEPA special procedure preserved');
ok(docs.includes('var rows=build(),mode=') && docs.includes('rows.map(function(d){return'),'printing uses deduplicated build rows');
console.log('PASS',checks,'transfer v3.22.140 documents dedup regression checks');
