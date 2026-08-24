'use strict';
const fs=require('fs'), vm=require('vm'), path=require('path');
let pass=0, fail=0;
function ok(name,cond,extra=''){if(cond){console.log('PASS',name);pass++;}else{console.error('FAIL',name,extra);fail++;}}
class CL{constructor(items=[]){this.s=new Set(items)} toggle(x,on){if(on)this.s.add(x);else this.s.delete(x)} add(x){this.s.add(x)} remove(x){this.s.delete(x)} contains(x){return this.s.has(x)}}
function el(id,props={}){return Object.assign({id,value:'',checked:false,disabled:false,type:'',dataset:{},classList:new CL(),listeners:{},attrs:{},addEventListener(t,fn){(this.listeners[t]??=[]).push(fn)},setAttribute(k,v){this.attrs[k]=String(v)},removeAttribute(k){delete this.attrs[k]},dispatchEvent(){},querySelector(){return null},querySelectorAll(){return[]}},props)}
const els={}; const add=e=>(els[e.id]=e,e);
add(el('degreeGrade',{value:'20',type:'number'})); add(el('workExperienceYears',{value:'0',type:'number'}));
add(el('computer',{type:'checkbox'})); add(el('training',{type:'checkbox'})); add(el('seminar400',{type:'checkbox'}));
add(el('academicSubtotal')); add(el('academicWarning')); add(el('trainingProof',{querySelectorAll(){return[]}}));
const note=el('trainingInheritedHelp',{classList:new CL(['hidden'])}); add(note);
const root=add(el('asepDeAcademic',{dataset:{degreeId:'degreeGrade',workId:'workExperienceYears',languageId:'asepLanguages',computerId:'computer',trainingId:'training',trainingProofId:'trainingProof',extraTrainingIds:'seminar400',subtotalId:'academicSubtotal',warningId:'academicWarning'},querySelector(sel){return sel==='[data-de-training-inherited-note]'?note:null;}}));
global.window=global; global.CustomEvent=function(t,o){this.type=t;this.detail=o&&o.detail};
global.document={readyState:'complete',getElementById:id=>els[id]||null,querySelectorAll(sel){return sel==='[data-component="asep-de-academic"]'?[root]:[]},addEventListener(){}};
global.AsepLanguageSelector={calculate(){return{points:0,accepted:[],warnings:[]}},reset(){}};
global.TrainingProof={syncAll(){},summary(){return 'proof'}};
const R=path.resolve(__dirname,'..');
vm.runInThisContext(fs.readFileSync(path.join(R,'includes/education-core.js'),'utf8'));
vm.runInThisContext(fs.readFileSync(path.join(R,'includes/de-academic-calculations.js'),'utf8'));
vm.runInThisContext(fs.readFileSync(path.join(R,'includes/asep-de-academic.js'),'utf8'));
let r=global.AsepDeAcademic.getState('asepDeAcademic');
ok('baseline training off',r.result.trainingPoints===0,r.result.trainingPoints);
els.seminar400.checked=true; r=global.AsepDeAcademic.getState('asepDeAcademic');
ok('400h inherits general training points',r.result.trainingPoints===10,r.result.trainingPoints);
ok('general checkbox auto checked',els.training.checked===true);
ok('general checkbox locked while inherited',els.training.disabled===true);
ok('inheritance note shown',!note.classList.contains('hidden'));
els.seminar400.checked=false; r=global.AsepDeAcademic.getState('asepDeAcademic');
ok('auto checkbox clears after 400h removed',els.training.checked===false && els.training.disabled===false);
ok('training returns to zero',r.result.trainingPoints===0,r.result.trainingPoints);
// Preserve a user's pre-existing general training selection.
els.training.checked=true; els.seminar400.checked=true; r=global.AsepDeAcademic.getState('asepDeAcademic');
els.seminar400.checked=false; r=global.AsepDeAcademic.getState('asepDeAcademic');
ok('manual general training preserved',els.training.checked===true && els.training.disabled===false);
ok('manual general training still scores once',r.result.trainingPoints===10,r.result.trainingPoints);
global.AsepDeAcademic.reset('asepDeAcademic',{silent:true});
ok('reset clears both seminars',!els.training.checked && !els.seminar400.checked && !els.training.disabled);
console.log(`RESULT ${pass} PASS / ${fail} FAIL`); if(fail)process.exit(1);
