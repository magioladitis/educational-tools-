'use strict';
const fs=require('fs'), vm=require('vm');
let pass=0, fail=0;
function ok(name, cond, extra=''){ if(cond){console.log('PASS',name);pass++;}else{console.log('FAIL',name,extra);fail++;} }
class CL{constructor(){this.s=new Set()} toggle(x,on){ if(on)this.s.add(x);else this.s.delete(x);} add(x){this.s.add(x)} remove(x){this.s.delete(x)} contains(x){return this.s.has(x)} }
function el(id, props={}){return Object.assign({id,value:'',checked:false,disabled:false,type:'',dataset:{},classList:new CL(),options:[],textContent:'',placeholder:'',min:'',max:'',listeners:{},addEventListener(t,fn){(this.listeners[t]??=[]).push(fn)},dispatchEvent(){},querySelectorAll(){return[]}},props)}
const els={}; const add=e=>(els[e.id]=e,e);
add(el('specialty',{value:'te01'}));
add(el('gradeScale',{value:'20',dataset:{auto:'on'},options:[el('o20',{value:'20'}),el('o10',{value:'10'}),el('ot',{value:'te16text'})]}));
add(el('degreeGrade',{value:'15',type:'number'}));
add(el('te16TextGrade',{value:'0'}));
add(el('numericGradeWrap')); add(el('te16TextWrap')); add(el('normalizedGradeInfo')); add(el('gradeWarning'));
add(el('secondTitle',{type:'checkbox'})); add(el('secondTitleLabel'));
add(el('computer',{type:'checkbox'})); add(el('training',{type:'checkbox'})); add(el('auxSeminar400',{type:'checkbox'}));
add(el('academicSubtotal')); add(el('trainingProof',{querySelectorAll(){return[]}}));
const root=add(el('asepTeAcademic',{dataset:{specialtyId:'specialty',gradeScaleId:'gradeScale',degreeId:'degreeGrade',textGradeId:'te16TextGrade',normalizedInfoId:'normalizedGradeInfo',gradeWarningId:'gradeWarning',secondTitleId:'secondTitle',secondTitleLabelId:'secondTitleLabel',languageId:'asepLanguages',computerId:'computer',trainingId:'training',trainingProofId:'trainingProof',extraTrainingIds:'',subtotalId:'academicSubtotal',degreePlaceholder20:'π.χ. 15,00'}}));
let langPoints=20;
global.window=global; global.CustomEvent=function(t,o){this.type=t;this.detail=o&&o.detail};
global.document={readyState:'complete',getElementById:id=>els[id]||null,querySelectorAll(sel){return sel==='[data-component="asep-te-academic"]'?[root]:[]},addEventListener(){}};
global.AsepLanguageSelector={sync(){},calculate(){return{points:langPoints,accepted:[{label:'Αγγλική'}],warnings:[]}},reset(){langPoints=0}};
global.AsepComputerProof={syncAll(){}}; global.TrainingProof={syncAll(){},summary(){return 'proof'},warning(){return ''}};
const path=require('path');
const fileRoot=path.resolve(__dirname,'..');
vm.runInThisContext(fs.readFileSync(path.join(fileRoot,'includes/education-core.js'),'utf8'),{filename:'core'});
vm.runInThisContext(fs.readFileSync(path.join(fileRoot,'includes/te-academic-calculations.js'),'utf8'),{filename:'tecalc'});
vm.runInThisContext(fs.readFileSync(path.join(fileRoot,'includes/language-calculations.js'),'utf8'),{filename:'languages'});
vm.runInThisContext(fs.readFileSync(path.join(fileRoot,'includes/asep-te-academic.js'),'utf8'),{filename:'controller'});
function calc(){return global.AsepTeAcademic.getState('asepTeAcademic',x=>Number(x).toFixed(2));}
// TE language profile: one language only, 20/15/10.
let lp=global.EducationLanguages.calculate('te',[{language:'en',level:'excellent'},{language:'fr',level:'excellent'}],{});
ok('TE profile counts one language only',lp.points===20 && lp.ignoredByLimit.length===1,JSON.stringify(lp));
lp=global.EducationLanguages.calculate('te',[{language:'en',level:'good'}],{});
ok('TE good language = 10',lp.points===10,lp.points);
lp=global.EducationLanguages.calculate('te',[{language:'en',level:'good'},{language:'en',level:'excellent'}],{});
ok('TE duplicate language keeps higher level',lp.points===20,lp.points);
// General TE: 15/20 -> 45, plus max extras 60 = 105
els.secondTitle.checked=true; els.computer.checked=true; els.training.checked=true; langPoints=20;
let r=calc(); ok('general total 105',r.result.points===105,JSON.stringify(r.result)); ok('degree 45',r.result.degreePoints===45); ok('scale stays 20',els.gradeScale.value==='20');
// TE16 branch forces scale 10 and musical label
els.specialty.value='te16'; els.gradeScale.dataset.auto='on'; els.degreeGrade.value='7.5'; r=calc();
ok('TE16 auto scale 10',els.gradeScale.value==='10',els.gradeScale.value); ok('TE16 label',els.secondTitleLabel.textContent.includes('μουσική ειδίκευση'));
ok('TE16 numeric 7.5 =>45',r.result.degreePoints===45,r.result.degreePoints);
// descriptive TE16 LIAN KALOS =>13/20 =>39
els.gradeScale.dataset.auto='off'; els.gradeScale.value='te16text'; els.te16TextGrade.value='6.5'; r=calc();
ok('TE16 text normalized 13',r.result.normalizedGrade===13,r.result.normalizedGrade); ok('TE16 text degree 39',r.result.degreePoints===39,r.result.degreePoints);
// Invalid numeric grade returns zero degree and warning
els.specialty.value='te01'; els.gradeScale.dataset.auto='off'; els.gradeScale.value='20'; els.degreeGrade.value='21'; els.secondTitle.checked=false; els.computer.checked=false; els.training.checked=false; langPoints=0; r=calc();
ok('invalid degree =>0',r.result.degreePoints===0,r.result.degreePoints); ok('invalid warning visible',!els.gradeWarning.classList.contains('hidden'));
// 4EA aux 400 acts as training 10 once
root.dataset.extraTrainingIds='auxSeminar400'; els.degreeGrade.value='20'; els.auxSeminar400.checked=true; els.training.checked=false; r=calc();
ok('aux400 gives training 10',r.result.trainingPoints===10,r.result.trainingPoints);
els.training.checked=true; r=calc(); ok('training+aux still 10',r.result.trainingPoints===10,r.result.trainingPoints);
// max academic exactly 120
langPoints=20; els.secondTitle.checked=true; els.computer.checked=true; els.degreeGrade.value='20'; r=calc(); ok('max academic 120',r.result.points===120,r.result.points);
// TE16 text option disabled outside TE16
els.specialty.value='ΤΕ02.01'; els.gradeScale.dataset.auto='on'; r=calc(); ok('text option disabled outside TE16',els.gradeScale.options.find(o=>o.value==='te16text').disabled===true);
els.specialty.value='ΤΕ16'; els.gradeScale.dataset.auto='on'; r=calc(); ok('Greek TE16 recognized',els.gradeScale.value==='10'); ok('text option enabled TE16',els.gradeScale.options.find(o=>o.value==='te16text').disabled===false);
console.log(`RESULT ${pass} PASS / ${fail} FAIL`); if(fail) process.exit(1);
