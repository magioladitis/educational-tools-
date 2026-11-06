'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs');
const vm=require('node:vm');
const engine=require('../includes/school-leadership-calculations.js');
let checks=0;
for(const [v,want] of [['',''],['0',0],['10',10],['11',10],['40000000000000000000000000',10],['-4',0],['3.8',3],['1e2',10],['1e999',0],['abc',0]]) {
 assert.equal(engine.normalizeTitleCount(v),want);checks++;
}
const names=['phdRelated','phdOther','masterRelated','masterOther'];
const fields={};
for(const name of [...names,'mode','evalType','primary','didaskaleio']) fields[name]={name,value:'',disabled:false};
fields.mode.value='first';fields.primary.value='no';
const listeners={};
const form={elements:{namedItem:n=>fields[n]},querySelectorAll:()=>[],addEventListener:(name,fn)=>{listeners[name]=fn;}};
const doc={getElementById:id=>id==='schoolLeadershipForm'?form:{hidden:false,closest:()=>({hidden:false}),addEventListener:()=>{}}};
vm.runInNewContext(fs.readFileSync(require.resolve('../includes/school-leadership-ui.js'),'utf8'),{document:doc,window:{SchoolLeadership:engine},setTimeout});
// Exercise the real input/change/focusout listeners before any calculation has run.
for(const event of ['input','change','focusout']) for(const name of names) {
 fields[name].value='40000000000000000000000';listeners[event]({target:fields[name]});
 assert.equal(String(fields[name].value),'10');checks++;
 fields[name].value='2.7';listeners[event]({target:fields[name]});
 assert.equal(String(fields[name].value),'2');checks++;
 fields[name].value='-3';listeners[event]({target:fields[name]});
 assert.equal(String(fields[name].value),'0');checks++;
 fields[name].value='';listeners[event]({target:fields[name]});
 assert.equal(fields[name].value,'');checks++;
}
assert.equal(engine.calculate({phdRelated:'40000000000000000000000000'}).details.titles,11);checks++;
console.log(`PASS: ${checks} title normalization and UI event checks`);
