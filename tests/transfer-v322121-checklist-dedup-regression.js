'use strict';
const fs = require('node:fs'), path = require('node:path'), vm = require('node:vm'), assert = require('node:assert/strict');
const dir=path.resolve(__dirname,'..');
const code=fs.readFileSync(path.join(dir,'includes/transfer-documents.js'),'utf8');
const elements={};
function el(id,value='',checked=false){elements[id]={value:String(value),checked,selectedOptions:[{textContent:String(value)}]};}
[
 ['mode','region'],['serviceYears','5'],['serviceMonths','1'],['serviceDays','0'],['eligibleChildren','0'],
 ['msdMode','direct'],['msdDirectPoints','42,50'],['transferGeneralOrigin','general'],
 ['transferGeneralEaeYears','0'],['transferGeneralEaeAreaYears','0'],['transferGeneralEaeSinceTransfer','0'],
 ['transferEaeOrigin','general'],['transferEaeArea','other'],['transferEaeDestination','smeae'],
 ['transferEaeBranch','pe03'],['transferEaeQualification','doctorate'],['transferEaeTeachingYears','3'],
 ['transferEaeAreaYears','2'],['transferEaeExperienceYears','0'],['transferEaePreviousYear','none'],
 ['transferArtBranch','general'],['transferArtOrientation',''],['transferArtTeachingYears','5'],
 ['transferArtException','none'],['transferArtPreviousYear','none'],['transferCoServiceCase','unspecified']
].forEach(([k,v])=>el(k,v));
['familyStatusEligible','coService','locality','transferGeneralEaeOrganic','transferEaeNewly','transferEaeNewDocuments','transferArtOrganic','transferArtNewDocuments'].forEach(id=>el(id,'',false));
let generated='';
const root={querySelectorAll(){return [];},set innerHTML(v){generated=v;},get innerHTML(){return generated;}};
const progress={textContent:''};
const document={getElementById(id){if(id==='transferDocumentChecklist')return root;if(id==='transferDocumentProgress')return progress; assert.ok(elements[id],`missing stub ${id}`);return elements[id];},querySelectorAll(){return [];}};
let printed='';const window={open:()=>({document:{write(s){printed=s;},close(){}},focus(){},print(){}})};
vm.runInNewContext(fs.readFileSync(path.join(dir,'includes/transfer-eligibility.js'),'utf8'),{window,document});
vm.runInNewContext(code,{window,document});
const guide=window.EducationTransferDocuments;
let t=0;function ok(test,name){assert.ok(test,name);t++;}
let rows=guide.build();
ok(rows.some(x=>x.id==='record'), 'record check exists');
ok(!rows.some(x=>x.id==='service'), 'no extra recognition check, even with service years');
ok(rows.filter(x=>x.id==='record').length===1, 'record only once');
ok(rows.find(x=>x.id==='record').detail.includes('πράξεις αναγνώρισης υπηρεσίας'), 'recognition detail preserved');
elements.serviceYears.value='0'; elements.serviceMonths.value='0';
rows=guide.build();
ok(!rows.some(x=>x.id==='service'), 'no extra check with zero years either');
elements.mode.value='smeae';
rows=guide.build();
ok(rows.some(x=>x.id==='eae-service'), 'distinct EAE eligibility still checked');
ok(rows.some(x=>x.id==='eae-routing'), 'distinct EAE submission channels still checked');
const routing=rows.find(x=>x.id==='eae-routing');
ok(!routing.detail.includes('Στον κύκλο'), 'redundant preface removed');
ok(routing.detail.includes('ΔΔΕ οργανικής') && routing.detail.includes('mydocs.minedu.gov.gr'), 'both submission channels retained');
ok(routing.detail.includes('κάθε χρόνο'), 'annual new application retained');
guide.render();
const card=id=>{const m=generated.match(new RegExp('<article class="([^"]+)" data-doc-card="'+id+'">([\\s\\S]*?)<\\/article>'));assert.ok(m,`rendered ${id}`);return m;};
for(const id of ['eae-service','eae-routing']){
 const [_,cls,body]=card(id);
 ok(cls.includes('is-compact'),id+' compact');
 ok(!body.includes('edu-transfer-doc__summary'),id+' no repeated preview');
 ok(body.includes('Περισσότερες πληροφορίες'),id+' disclosure kept');
}
ok(generated.split('Ελέγξτε την τριετή διδακτική υπηρεσία').length===2,'EAE service text rendered once');
ok(generated.split('Δικαιολογητικά μοριοδότησης:').length===2,'EAE submission text rendered once');
ok(!generated.includes('data-doc-card="service"'),'no extra recognition checkbox');
ok(progress.textContent.startsWith('Έλεγχοι που σημειώθηκαν:'),'progress maintained');
elements.mode.value='art';rows=guide.build();
for(const id of ['art-service','art-submit']){
 const d=rows.find(x=>x.id===id);
 ok(d && !d.detail.includes('Για τον κύκλο'),id+' no redundant date preface');
}
// Source supports print path despite collapsed UI; keep printed instructions.
elements.mode.value='smeae';guide.print; // print is intentionally not exported; the full details are in build rows.
ok(rows.length>0 && code.includes('escape(d.detail).replace(/\\n/g'), 'print path still uses complete detail');
console.log(`PASS ${t} transfer 3.22.121 dedup / EAE eligibility / channels / render regression checks`);
