'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.resolve(__dirname,'..');
const code=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
const ctx={window:{},document:{getElementById(){return null;}}};
vm.runInNewContext(code,ctx,{filename:'transfer-documents.js'});
const api=ctx.window.EducationTransferDocuments;
const doc=(id,disabled=false)=>({id,disabled,tag:''});
const cases=[
 ['none',[],false,false,false,/αν χρειαστεί/],
 ['official only',[doc('family'),doc('record')],false,false,false,/αν χρειαστεί/],
 ['email',[doc('locality')],true,false,false,/email|μηνύματος/],
 ['email disabled',[doc('coservice',true)],false,false,false,/αν χρειαστεί/],
 ['mydocs',[doc('eae-masters')],false,true,false,/mydocs/],
 ['both',[doc('locality'),doc('eae-masters')],true,true,false,/mydocs/],
 ['physical',[doc('health-committee')],false,false,true,/Ειδικές/],
 ['kepa',[doc('health-kepa')],false,false,true,/Ειδικές/],
 ['physical and email',[doc('health-committee'),doc('locality')],true,false,false,/email|μηνύματος/],
 ['mydocs and special',[doc('judicial-spouse'),doc('health-committee')],false,true,false,/mydocs/],
 ['mydocs and disabled email',[doc('eae-braille'),doc('coservice',true)],false,true,false,/mydocs/],
 ['all three',[doc('eae-masters'),doc('locality'),doc('health-kepa')],true,true,false,/mydocs/]
];
let checks=0;
for(const [name,rows,email,mydocs,specialOnly,title] of cases){
 const s=api.submissionFeatureState(rows,'region');
 assert.equal(s.email,email,name+' email');checks++;
 assert.equal(!!s.specialOnly,specialOnly,name+' specialOnly');checks++;
 assert.match(s.title,title,name+' title');checks++;
 if(mydocs) {assert.match(s.title+s.lead,/mydocs/,name+' mydocs wording');checks++;}
}
const exceptional=['health-committee','health-kepa','judicial-spouse'];
assert.equal(new Set(exceptional.map(x=>api.submissionChannel(doc(x)).kind)).size,2);checks++;
for(const mode of ['region','local','smeae','eae_sensory','kedasy','music','art','intercultural']){
 const template=api.submissionMessageTemplate(mode,{name:'Δοκιμή',branch:'ΠΕ03',protocol:'42',registry:'123',home:'ΔΔΕ Κέρκυρας'});
 assert.ok(template.subject&&template.body,mode+' valid template');checks++;
 const link=api.submissionMailto(template);
 assert.ok(link.startsWith('mailto:?subject=')&&link.includes('&body='),mode+' recipient-free mailto');checks++;
}
assert.doesNotMatch(code,/(?:localStorage|sessionStorage)\s*\./);checks++;
console.log('PASS',checks,'submission-channel matrix assertions; cases:',cases.length);
