'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.resolve(__dirname,'..');
const file=n=>fs.readFileSync(path.join(root,n),'utf8');
const page=file('ypologismos-morion-metathesis.php'), docs=file('includes/transfer-documents.js');
// The qualification/service eligibility logic remains in the dedicated section.
assert.ok(page.includes('id="transferEaePanel"'));
assert.ok(page.includes('id="transferEaeAdvice"'));
assert.ok(page.includes('id="transferEaeTeachingYears"'));
assert.ok(!docs.includes("entry('eae-service'"));
const elems={}; const el=id=>elems[id]||(elems[id]={value:'',checked:false,selectedOptions:[{textContent:''}]});
Object.entries({mode:'smeae',transferEaeOrigin:'general',transferEaeArea:'other',transferEaeDestination:'smeae',transferEaeBranch:'pe03',transferEaeQualification:'masters',transferEaeExperienceYears:'0',transferEaeTeachingYears:'4',transferEaeAreaYears:'2',transferEaePreviousYear:'none',transferCoServiceCase:'unspecified',eligibleChildren:'0',msdMode:'direct',msdDirectPoints:'1'}).forEach(([k,v])=>el(k).value=v);
const window={}; const document={getElementById:el,querySelectorAll(){return [];}};
vm.runInNewContext(file('includes/transfer-eligibility.js'),{window,document});
vm.runInNewContext(docs,{window,document});
let checked=0; const guide=window.EducationTransferDocuments;
for(const [mode,dest,expected] of [['smeae','smeae','eae-masters'],['eae_sensory','deaf','eae-sign'],['eae_sensory','blind','eae-braille'],['kedasy','kedasy','eae-masters']]){
 el('mode').value=mode; el('transferEaeDestination').value=dest;
 const list=guide.build();
 assert.equal(list.some(x=>x.id==='eae-service'),false,`duplicate removed: ${mode}`);checked++;
 assert.ok(list.some(x=>x.id===expected),`essential evidence still available: ${mode}`);checked++;
 assert.ok(list.some(x=>x.id==='eae-masters'),'qualification documents retained');checked++;
 if(mode==='kedasy'){assert.equal(list.some(x=>x.id==='eae-kedasy'),false,'duplicate KEDASY check removed');checked++;}
}
const r=window.EducationTransferEligibility.eae({origin:'general',branch:'pe03',destination:'smeae',qualifications:['masters'],teachingYears:2.9});
assert.equal(r.verdict,'not_established','real three-year gate remains active');checked++;
console.log('PASS',checked,'v3.22.145 EAE checklist dedup / eligibility preservation');
