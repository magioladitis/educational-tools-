'use strict';
const fs=require('fs'),vm=require('vm'),path=require('path');
const root=path.resolve(__dirname,'..');
const ctx={console};ctx.globalThis=ctx;ctx.window=ctx;vm.createContext(ctx);
function load(f){vm.runInContext(fs.readFileSync(path.join(root,f),'utf8'),ctx,{filename:f});}
load('includes/education-core.js');
load('includes/language-calculations.js');
load('includes/academic-calculations.js');
load('includes/service-calculations.js');
load('includes/social-calculations.js');
load('includes/te-academic-calculations.js');
let pass=0,fail=0;function t(name,cond,extra=''){if(cond){console.log('PASS',name);pass++;}else{console.log('FAIL',name,extra);fail++;}}
const C=ctx.EducationCore;
[['PE06','ΠΕ06'],['ΠΕ06','ΠΕ06'],['pe06','ΠΕ06'],['PΕ06','ΠΕ06'],['ΠE06','ΠΕ06'],['PE 04.01','ΠΕ04.01'],['TE16','ΤΕ16'],['ΤΕ16','ΤΕ16'],['te 02.01','ΤΕ02.01'],['DE01','ΔΕ01'],['ΔΕ01','ΔΕ01']].forEach(([a,b])=>t('normalize '+a,C.normalizeSpecialtyCode(a)===b,C.normalizeSpecialtyCode(a)));
t('latin adapter PE86',C.toLatinSpecialtyCode('ΠΕ86')==='PE86',C.toLatinSpecialtyCode('ΠΕ86'));
t('latin adapter TE16',C.toLatinSpecialtyCode('ΤΕ16')==='TE16',C.toLatinSpecialtyCode('ΤΕ16'));
function contract(name,r){t(name+' rawPoints',typeof r.rawPoints==='number');t(name+' points',typeof r.points==='number');t(name+' details',Array.isArray(r.details));t(name+' warnings',Array.isArray(r.warnings));}
let r=ctx.EducationLanguages.calculate('pe',[{language:'en',level:'excellent'}],{specialty:'PE06'});contract('language',r);t('PE06 Latin excludes English',r.points===0);t('language raw alias',r.raw===r.rawPoints);
r=ctx.EducationAcademic.calculate({profile:'general',specialty:'PE86',degreeGrade:8,computer:true});contract('academic',r);t('PE86 Latin computer zero',r.computerPoints===0);t('academic raw alias',r.raw===r.rawPoints);
r=ctx.EducationService.calculateAsepService({regularMonths:100,difficultMonths:60});contract('service',r);t('service cap',r.rawPoints===220&&r.points===120);t('service raw alias',r.raw===r.rawPoints);
r=ctx.EducationSocial.calculate({children:2,spouseDisability:80,marriageYears4Plus:true});contract('social',r);t('social total alias',r.total===r.points);t('social total score',r.points===38);
r=ctx.TEAcademic.calculate({gradeScale:'20',degreeGrade:20,secondTitle:true,languagePoints:20,computer:true,training:true});contract('TE academic',r);t('TE raw alias',r.raw===r.rawPoints);t('TE cap 120',r.points===120);
console.log(`RESULT ${pass} PASS / ${fail} FAIL`);if(fail)process.exit(1);
