'use strict';
const fs=require('fs'),vm=require('vm'),path=require('path');const rootPath=path.resolve(__dirname,'..');
class CL{constructor(){this.s=new Set()}toggle(x,on){if(on)this.s.add(x);else this.s.delete(x)}add(x){this.s.add(x)}remove(x){this.s.delete(x)}contains(x){return this.s.has(x)}}
function E(id,p={}){return Object.assign({id,value:'',checked:false,disabled:false,type:'',tagName:'INPUT',dataset:{},classList:new CL(),selectedIndex:0,querySelectorAll(){return[]},addEventListener(){},dispatchEvent(){}},p)}
const els={};const add=e=>(els[e.id]=e,e);
add(E('branch',{value:'PE86',tagName:'SELECT'}));add(E('degreeGrade',{value:'8'}));add(E('secondDegree',{type:'checkbox'}));add(E('phd',{type:'checkbox'}));add(E('mscCount',{value:'0',tagName:'SELECT'}));add(E('computer',{type:'checkbox',checked:true}));add(E('training',{type:'checkbox'}));
const component=add(E('asepPeAcademic',{dataset:{profile:'eep',degreeRequired:'false',specialtyId:'branch',degreeId:'degreeGrade',secondDegreeId:'secondDegree',phdId:'phd',mscId:'mscCount',languageId:'asepLanguages',computerId:'computer',trainingId:'training'},getAttribute(){return null}}));
global.window=global;global.globalThis=global;global.document={readyState:'complete',getElementById:id=>els[id]||null,querySelectorAll:s=>s==='[data-component="asep-pe-academic"]'?[component]:[],addEventListener(){}};global.CustomEvent=function(){};
global.AsepLanguageSelector={sync(){},calculate(){return{points:0,details:[],warnings:[]}},reset(){}};global.AsepComputerProof={syncAll(){}};global.TrainingProof={syncAll(){},summary(){return''},warning(){return''}};
function load(f){vm.runInThisContext(fs.readFileSync(path.join(rootPath,f),'utf8'),{filename:f});}
load('includes/education-core.js');load('includes/academic-calculations.js');load('includes/asep-pe-academic.js');
let pass=0,fail=0;function t(n,c,x=''){if(c){console.log('PASS',n);pass++;}else{console.log('FAIL',n,x);fail++;}}
global.AsepPeAcademic.sync('asepPeAcademic');t('Latin PE86 disables computer control',els.computer.disabled===true);t('disabled computer is unchecked',els.computer.checked===false);let r=global.AsepPeAcademic.calculate('asepPeAcademic');t('Latin PE86 canonical state',global.AsepPeAcademic.getState('asepPeAcademic').specialty==='ΠΕ86',global.AsepPeAcademic.getState('asepPeAcademic').specialty);t('PE86 computer points remain zero',r.computerPoints===0,r.computerPoints);
els.branch.value='PE61';global.AsepPeAcademic.sync('asepPeAcademic');t('Latin PE61 canonical state',global.AsepPeAcademic.getState('asepPeAcademic').specialty==='ΠΕ61');
console.log(`RESULT ${pass} PASS / ${fail} FAIL`);if(fail)process.exit(1);
