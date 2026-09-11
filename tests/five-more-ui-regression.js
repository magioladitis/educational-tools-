'use strict';
const fs=require('fs'),vm=require('vm'),path=require('path');
const ROOT=path.resolve(__dirname,'..');
let pass=0,fail=0;
function ok(cond,msg,extra=''){if(cond){pass++;console.log('PASS',msg)}else{fail++;console.error('FAIL',msg,extra)}}
function loadInto(ctx,rel){vm.runInContext(fs.readFileSync(path.join(ROOT,rel),'utf8'),ctx,{filename:rel});}
function approx(a,b){return Math.abs(Number(a)-Number(b))<1e-9;}

// 1) Children/disability guide: execute the extracted UI module against a tiny DOM.
class CL{constructor(hidden=false){this.s=new Set(hidden?['hidden']:[])} add(x){this.s.add(x)} remove(x){this.s.delete(x)} toggle(x,on){if(on)this.s.add(x);else this.s.delete(x)} contains(x){return this.s.has(x)}}
function elem(id,{value='',hidden=false}={}){return {id,value,checked:false,style:{display:''},classList:new CL(hidden),innerHTML:'',textContent:'',listeners:{},addEventListener(t,fn){(this.listeners[t]??=[]).push(fn)}}}
const E={};
['criterion','childrenQuestions','disabilityQuestions','childCase','familyCertificateStatus','disabilityPerson','disabilityPercent','spouseMarriageQuestion','marriageYears4Plus','candidateMentalQuestion','candidateMentalCondition','disabilityCertificate','result','showDocumentsBtn'].forEach(id=>E[id]=elem(id,{hidden:['childrenQuestions','disabilityQuestions','spouseMarriageQuestion','candidateMentalQuestion'].includes(id)}));
const guideCtx={console,document:{getElementById:id=>E[id]||null,querySelectorAll(sel){return sel==='input[name="familySpecialCase"]:checked'?[]:[]}},setTimeout,Number,Array,Set};
guideCtx.window=guideCtx;vm.createContext(guideCtx);loadInto(guideCtx,'includes/children-disability-documents-ui.js');
E.criterion.value='children'; guideCtx.updateVisibility();
ok(!E.childrenQuestions.classList.contains('hidden') && E.disabilityQuestions.classList.contains('hidden'),'children guide visibility preserved');
E.childCase.value='under23';E.familyCertificateStatus.value='yes';guideCtx.showDocuments();
ok(E.result.style.display==='block' && E.result.innerHTML.includes('Πιστοποιητικό οικογενειακής κατάστασης'),'children guide produces family-certificate guidance');
E.criterion.value='disability';E.disabilityPerson.value='candidate';E.disabilityPercent.value='40';E.candidateMentalCondition.value='no';E.disabilityCertificate.value='kepa';guideCtx.showDocuments();
ok(E.result.innerHTML.includes('μικρότερο από 50%'),'disability guide keeps under-50 warning');
ok((E.criterion.listeners.change||[]).length===1 && (E.disabilityPerson.listeners.change||[]).length===1 && (E.showDocumentsBtn.listeners.click||[]).length===1,'children/disability handlers are externally bound');

// 2) SDE registry scoring engine regression. Load canonical core/language dependencies.
const sde={console};sde.window=sde;vm.createContext(sde);loadInto(sde,'includes/education-core.js');loadInto(sde,'includes/language-calculations.js');loadInto(sde,'includes/sde-registry-calculations.js');
let r=sde.SDERegistryCalc.calculateAll({role:'educator',specialty:'ΠΕ03',phd:'target',master:'none',trainingSdeHours:100,trainingAdultHours:100,expSdeHours:400,expAdultHours:200,expFormalHours:200,language1:'english',languageLevel1:'C2',language2:'',languageLevel2:'none',computer:true,unemploymentMonths:10,unemploymentExtraDays:15,threeChildren:true});
ok(r.eligibility.eligible===true && r.assignments.some(x=>x.literacy==='Μαθηματικά'),'SDE educator eligibility/assignments preserved');
ok(r.education.total>0 && r.experience.total>0 && r.other.total>0 && r.final>r.base,'SDE educator scoring/social increase preserved',JSON.stringify(r));
r=sde.SDERegistryCalc.calculateAll({role:'educator',specialty:'ΠΕ86',language1:'english',languageLevel1:'C2',computer:true});
ok(r.other.computerPoints===0 && r.other.warnings.some(x=>x.includes('ΠΕ86')),'SDE PE86 computer exclusion preserved');
r=sde.SDERegistryCalc.calculateTraining({role:'educator',trainingSdeHours:14,trainingAdultHours:15});
ok(r.total===0.0375,'SDE training keeps 15-hour minimum rule',JSON.stringify(r));

// 3) Sivitanidios scoring engine regression.
const siv={console};siv.window=siv;vm.createContext(siv);loadInto(siv,'includes/sivitanidios-saek-calculations.js');
r=siv.SivitanidiosSaekCalc.calculateAll({candidateType:'graduate',qualifyingTitle:'aei',relatedPostgrad:'master',adultEducationPostgrad:'master',trainingSubjectHours:50,trainingVetHours:25,trainingAdultHours:25,saekOutsideHours:1000,saekSivitanidiosHours:500,tertiaryTeachingYears:1,primarySecondaryTeachingYears:1,otherNonFormalHours:500,workMonths:60,languageName:'english',languageLevel:'C2',languageArticle28Status:'yes',computer:true,adultTrainer:true,unemploymentBand:'m12_18',threeChildren:true});
ok(r.eligibility.eligible===true && r.base>0 && r.final>r.base,'Sivitanidios graduate scoring/social increase preserved',JSON.stringify(r));
r=siv.SivitanidiosSaekCalc.calculateAll({candidateType:'craft',workMonths:35});
ok(r.eligibility.eligible===false && r.eligibility.blockers.some(x=>x.includes('36')),'Sivitanidios craft 36-month eligibility preserved');
let tr=siv.SivitanidiosSaekCalc.calculateEducation({candidateType:'graduate',qualifyingTitle:'aei',trainingSubjectHours:24,trainingVetHours:25,trainingAdultHours:50});
ok(tr.trainingPoints===0.75,'Sivitanidios 25-hour training increments preserved',JSON.stringify(tr));

console.log(`RESULT ${pass} PASS / ${fail} FAIL`);process.exit(fail?1:0);
