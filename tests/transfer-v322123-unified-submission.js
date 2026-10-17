'use strict';
const fs=require('node:fs'), vm=require('node:vm'),assert=require('node:assert/strict'),path=require('node:path');
const dir=path.resolve(__dirname,'..');
const page=fs.readFileSync(path.join(dir,'ypologismos-morion-metathesis.php'),'utf8');
const ui=fs.readFileSync(path.join(dir,'includes/transfer-ui.js'),'utf8');
const docs=fs.readFileSync(path.join(dir,'includes/transfer-documents.js'),'utf8');
const eligibility=fs.readFileSync(path.join(dir,'includes/transfer-eligibility.js'),'utf8');
const checks=[];function ok(condition,label){assert.ok(condition,label);checks.push(label);}
ok(page.includes("'summary' => 'Πού αποστέλλονται τα δικαιολογητικά; — Οδηγίες και υπόδειγμα'"),'single routing disclosure remains');
ok(page.indexOf("'id' => 'transferSubmissionGuidance'")>page.indexOf('<section id="transferDocumentsPanel"'),'disclosure sits after personalized checklist');
ok(!page.includes("'id' => 'calculateBtn'"),'manual calculator button removed');
ok(!ui.includes("byId('calculateBtn')"),'no event handler to removed button');
for(const name of ['transferEaeAreaYears','transferGeneralEaeAreaYears']){
  ok(new RegExp('id="'+name+'" type="number" min="0" max="45"').test(page),name+' HTML capped 45');
  ok(ui.includes("target.id === '"+name+"'"),name+' client-side clamping');
}
ok(ui.includes('clampAreaYears(target)'),'decimal-safe limit applied');
ok(ui.includes('function calculate()'),'automatic calculator retained');
const fields=Object.create(null);
const makeField=(id)=>fields[id]||(fields[id]={value:'',checked:false,selectedOptions:[{textContent:'Επιλογή'}]});
const dom={getElementById:id=>makeField(id),querySelectorAll:()=>[]};
const sandbox={window:{},document:dom};
vm.runInNewContext(eligibility,sandbox);
vm.runInNewContext(docs,sandbox);
const guide=sandbox.window.EducationTransferDocuments;
for(const mode of ['region','local','smeae','music','art','intercultural']){
  makeField('mode').value=mode;
  const route=guide.submissionGuideData();
  ok(route.lines.length>=2,'routing instructions exist in '+mode);
  ok(!guide.build().some(x=>['general-doc-channel','eae-routing','art-submit','ic-submit'].includes(x.id)),'no obsolete routing checkbox in '+mode);
}
makeField('mode').value='smeae';
const eae=guide.submissionGuideData().lines.join(' ');
ok(eae.includes('ΔΔΕ οργανικής')&&eae.includes('mydocs.minedu.gov.gr'),'EAE submission channels preserved');
ok(eae.includes('κάθε χρόνο'),'annual EAE application disclaimer preserved');
makeField('mode').value='art';
ok(guide.submissionGuideData().lines.join(' ').includes('50 MB'),'art-specific file instructions retained');
makeField('mode').value='intercultural';
ok(guide.submissionGuideData().lines.join(' ').includes('μία αποστολή'),'intercultural one-submission constraint retained');
const result=sandbox.window.EducationTransferEligibility;
ok(result.eae({qualification:'masters',origin:'general',newly:true,teachingYears:5,areaYears:46}).advice.some(x=>x.includes('0 έως 45')),'46 area years rejected by eligibility');
ok(result.returnToGeneral({origin:'appointed_eae',eaeYears:5,areaYears:46}).needsReview,'46 area years rejected by return-to-general eligibility');
ok(result.eae({qualification:'masters',origin:'general',newly:true,teachingYears:5,areaYears:1.5}).advice.some(x=>x.includes('δύο έτη')),'1.5 years still numeric and below 2');
console.log('PASS '+checks.length+' v3.22.123 unified submission / auto score / area years checks');
