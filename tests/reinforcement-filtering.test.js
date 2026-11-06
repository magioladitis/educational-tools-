'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),vm=require('node:vm');
const api=require('../includes/reinforcement-eligibility.js');
let n=0;
function eq(a,b){assert.deepEqual(a,b);n++;}
eq(api.options('', '').branches,[]);
eq(api.options('gym','PE03').boards,['2GE']);
eq(api.options('edem','TE16').boards,['1GT','4EA-main','4EA-aux']);
eq(api.options('edem','PE79.01').boards,['1GE','3EA-main','3EA-aux']);
eq(api.options('edem','PE89.01').subjects,['free','linear']);
eq(api.options('edem','PE05').subjects,['french']);
eq(api.options('gym','PE40').boards,[]);
// Run the actual UI module against a minimal DOM to test dependent selection transitions.
function node(){return{children:[],hidden:false,dataset:{},appendChild(x){this.children.push(x);},replaceChildren(...xs){this.children=xs;},querySelectorAll(){return[];}};}
const values={branch:['','PE02','PE03','PE04.01','PE04.02','PE85','PE06','PE05','PE07','PE34','PE40','PE79.01','TE16','PE08','PE89.01','PE81','other'],board:['','2GE','1GE','3EA-main','3EA-aux','1GT','4EA-main','4EA-aux','none','unknown'],subject:['','french','german','italian','spanish','music','free','linear']};
const fields={program:{value:''}};
for(const [name,vs] of Object.entries(values))fields[name]={name,value:'',disabled:false,dataset:{},options:vs.map(value=>({value,textContent:value})),replaceChildren(fragment){this.options=fragment.children;}};
const listeners={};
const form={elements:{namedItem:k=>fields[k]},addEventListener:(k,v)=>{listeners[k]=v;}};
const groups={};
for(const [id,key] of [['reinforcement-edem','subject'],['reinforcement-chemical','chemical'],['reinforcement-music','musicSpecialty']]){
 if(!fields[key])fields[key]={value:'',disabled:false};
 groups[id]={hidden:true,querySelectorAll:()=>[fields[key]]};
}
const result=node(),periodBox=node();
const document={getElementById(id){return id==='reinforcement-form'?form:id==='reinforcement-result'?result:id==='reinforcement-period'?periodBox:groups[id];},createElement:()=>node(),createDocumentFragment:()=>node(),querySelectorAll:()=>[],querySelector:()=>null};
vm.runInNewContext(fs.readFileSync(require.resolve('../includes/reinforcement-ui.js'),'utf8'),{document,window:{ReinforcementEligibility:api},Intl,Date,setInterval:()=>{},setTimeout:fn=>fn()});
const opts=k=>fields[k].options.map(o=>o.value);
function change(k,v){fields[k].value=v;listeners.change();}
eq(fields.branch.disabled,true);eq(fields.board.disabled,true);eq(fields.subject.disabled,true);
eq(opts('branch'),['']);
change('program','gym');eq(fields.branch.disabled,false);eq(opts('branch').includes('PE03'),true);eq(opts('branch').includes('PE40'),false);eq(fields.board.disabled,true);
change('branch','PE03');eq(fields.board.disabled,false);eq(opts('board'),['','2GE','none','unknown']);
change('board','2GE');change('program','edem');eq(fields.branch.value,'');eq(fields.board.value,'');eq(fields.board.disabled,true);eq(opts('branch').includes('PE03'),false);eq(opts('branch').includes('PE40'),true);
change('branch','PE40');eq(opts('subject'),['','spanish']);eq(fields.subject.disabled,false);
change('subject','spanish');change('board','2GE');change('branch','PE79.01');eq(fields.subject.value,'');eq(fields.board.value,'');eq(opts('subject'),['','music']);eq(opts('board'),['','1GE','3EA-main','3EA-aux','none','unknown']);
change('board','3EA-main');change('branch','PE05');eq(fields.board.value,'3EA-main');eq(opts('subject'),['','french']);
change('branch','TE16');eq(fields.board.value,'');eq(opts('board'),['','1GT','4EA-main','4EA-aux','none','unknown']);eq(groups['reinforcement-music'].hidden,false);
change('branch','PE89.01');eq(opts('subject'),['','free','linear']);eq(groups['reinforcement-music'].hidden,true);
change('program','gym');eq(fields.branch.value,'');eq(fields.subject.value,'');eq(fields.subject.disabled,true);
change('branch','PE85');eq(groups['reinforcement-chemical'].hidden,false);
// Browser restores defaults before the deferred reset callback runs.
fields.program.value='';listeners.reset();eq(fields.branch.disabled,true);eq(fields.board.disabled,true);eq(opts('branch'),['']);
console.log(`PASS: ${n} dependent option and UI transition checks`);
