'use strict';
const fs=require('fs'),vm=require('vm'),path=require('path');const root=path.resolve(__dirname,'..');
const ctx={console};ctx.globalThis=ctx;ctx.window=ctx;vm.createContext(ctx);function load(f){vm.runInContext(fs.readFileSync(path.join(root,f),'utf8'),ctx,{filename:f});}
['includes/education-core.js','includes/language-calculations.js','includes/academic-calculations.js','includes/eae-table-eligibility.js','includes/onaseia-calculations.js','includes/sde-calculations.js','includes/sde-registry-calculations.js'].forEach(load);
let pass=0,fail=0;function t(n,c,x=''){if(c){console.log('PASS',n);pass++;}else{console.log('FAIL',n,x);fail++;}}
let r=ctx.EducationAcademic.calculate({profile:'eae',specialty:'PE61',degreeGrade:8,mscCount:0});t('EAE PE61 Latin gets 20 specialization',r.mscPoints===20,r.mscPoints);
r=ctx.EducationAcademic.calculate({profile:'eae',specialty:'PE11',degreeGrade:8,eaePe11Specialization:true});t('EAE PE11 Latin gets +8',r.specialProfilePoints===8,r.specialProfilePoints);
r=ctx.EaeTableEligibility.calculate({profile:'pe',specialty:'PE71',main:{},aux:{}});t('Eligibility PE71 Latin auto-main',r.code==='main',r.code);
t('Onaseia TE16 Latin minimum 30',ctx.OnaseiaAcademic.validateManualAcademicPoints('29.99','TE16').valid===false && ctx.OnaseiaAcademic.validateManualAcademicPoints('30','TE16').valid===true);
let a=ctx.SDECalculator.assignmentsFor('PE06',{}),b=ctx.SDECalculator.assignmentsFor('ΠΕ06',{});t('SDE assignments PE06 Latin/Greek parity',JSON.stringify(a)===JSON.stringify(b)&&a.some(x=>x.literacy==='Αγγλική Γλώσσα'));
r=ctx.SDECalculator.calculateOther({specialty:'ΠΕ86',computer:false,languages:[]});t('SDE Greek ΠΕ86 infers computer',r.computerPoints===2,r.computerPoints);
r=ctx.SDECalculator.calculateLanguages({specialty:'ΠΕ06',languages:[{language:'en',level:'excellent'}]});t('SDE Greek ΠΕ06 excludes English',r.points===0,r.points);
a=ctx.SDERegistryCalc.educatorAssignments('TE16');b=ctx.SDERegistryCalc.educatorAssignments('ΤΕ16');t('SDE registry TE16 parity',JSON.stringify(a)===JSON.stringify(b)&&a.length>0);
r=ctx.SDERegistryCalc.calculateOther({role:'educator',specialty:'ΠΕ86',computer:true,language1:'',languageLevel1:'none',language2:'',languageLevel2:'none'});t('SDE registry Greek ΠΕ86 computer excluded',r.computerPoints===0,r.computerPoints);
console.log(`RESULT ${pass} PASS / ${fail} FAIL`);if(fail)process.exit(1);
