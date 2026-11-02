'use strict';
const fs=require('fs'),vm=require('vm'),path=require('path'),assert=require('assert');
const root=path.resolve(__dirname,'..');
function load(base,shared){
 const c={};c.window=c;c.globalThis=c;vm.createContext(c);
 const files=['specialty-code-normalization.js','education-core.js'];
 if(shared)files.push('eae-qualifications.js');
 files.push('eae-table-eligibility.js','transfer-eligibility.js','transfer-primary-eae.js');
 for(const f of files)vm.runInContext(fs.readFileSync(path.join(base,'includes',f),'utf8'),c);
 return c;
}
const current=load(root,true);
const canonical=current.EaeQualifications;
assert.equal(canonical.canonical('masters'),'msc');
assert.equal(canonical.canonical('doctorate'),'phd');
assert.equal(canonical.canonical('seminar400'),null);
assert.equal(canonical.assess('unlisted-degree').status,'unknown');
assert.equal(canonical.assess('experience',{requireYears:true,years:''}).status,'missing');
assert.equal(canonical.assess('experience',{requireYears:true,years:'4,9'}).status,'invalid');
assert.equal(canonical.assess('experience',{requireYears:true,years:5}).status,'valid');
assert.equal(canonical.assess('pe11',{pe11Allowed:false}).status,'invalid');
assert.equal(current.EaeTableEligibility.calculate({specialty:'ΠΕ03',aux:{seminar400:true}}).code,'aux');
assert.equal(current.EducationTransferEligibility.eae({qualifications:['seminar400'],teachingYears:5}).validQualifications.length,0);
let count=0;
if(process.argv[2]){
 const old=load(path.resolve(process.argv[2]),false);
 function equal(name,input){
  const methods={asep:['EaeTableEligibility','calculate'],secondary:['EducationTransferEligibility','eae'],primary:['EducationTransferPrimaryEae','evaluate']};
  const [obj,fn]=methods[name];
  assert.equal(JSON.stringify(current[obj][fn](input)),JSON.stringify(old[obj][fn](input)),name+JSON.stringify(input));count++;
 }
 for(let bits=0;bits<32;bits++){
  const ks=['phd','msc','retraining','fiveYear','pe11'];const main=Object.fromEntries(ks.map((k,i)=>[k,!!(bits&(1<<i))]));
  for(const profile of ['pe','te','de'])for(const specialty of ['','ΠΕ03','ΠΕ11','ΠΕ61','ΠΕ71','ΤΕ01'])for(const months of [0,9.9,10])
   equal('asep',{profile,specialty,main,aux:{seminar400:bits%2===1,eaeMonths:months,childDisability67:bits%3===0}});
  for(const years of [undefined,'',-1,0,4.9,'4,9',5,50,51])for(const branch of ['other','pe11'])for(const origin of ['general','eae']){
   equal('secondary',{qualifications:['doctorate','masters','two_year','experience','pe11'].filter((k,i)=>bits&(1<<i)),eaeYears:years,branch,origin,teachingYears:3,organic:true});
  }
  for(const years of [undefined,'',-1,4.9,5,51])for(const branch of ['ΠΕ70','ΠΕ71','ΠΕ11'])for(const mode of ['region','eeeek','kedasy','deaf'])for(const origin of ['general','appointed-eae','transferred-eae']){
   equal('primary',{qualifications:Object.fromEntries(['phd','masters','retraining','experience','pe11'].map((k,i)=>[k,!!(bits&(1<<i))])),eaeYears:years,branch,mode,origin,generalYears:3,organic:'yes'});
  }
 }
}
for(const [page,dependency] of [['3ea-2025','eae-table-eligibility'],['4ea-2025','eae-table-eligibility'],['5ea-2022','eae-table-eligibility'],['metathesis','transfer-eligibility'],['metathesis-protovathmia','transfer-primary-eae']]){
 const s=fs.readFileSync(path.join(root,'ypologismos-morion-'+page+'.php'),'utf8');
 assert(s.includes('renderEaeQualifications('));
 assert(s.indexOf("edu_asset_url('includes/eae-qualifications.js')")<s.indexOf("edu_asset_url('includes/"+dependency+".js')"));
}
console.log('PASS shared API, procedure isolation, five page integration contracts; '+count+' baseline comparisons');
