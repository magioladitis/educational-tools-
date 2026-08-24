const fs = require('fs');
const vm = require('vm');
const path = require('path');
const ROOT = path.resolve(__dirname, '..');
let pass=0, fail=0;
function ok(cond,msg){ if(cond){pass++; console.log('PASS',msg);} else {fail++; console.error('FAIL',msg);} }
function load(files){
  const ctx={window:{}, console}; ctx.window.window=ctx.window; vm.createContext(ctx);
  for(const f of files) vm.runInContext(fs.readFileSync(path.join(ROOT,'includes',f),'utf8'),ctx,{filename:f});
  return ctx.window;
}
const w=load(['education-core.js','language-calculations.js','de-academic-calculations.js','eae-table-eligibility.js']);
let r=w.DEAcademic.calculate({degreePresent:true,degreeGrade:20,workExperienceYears:5,languagePoints:20,computer:true,training:true});
ok(r.rawPoints===120 && r.points===120,'academic maximum = 120');
ok(r.degreePoints===50,'degree 20 x 2.5 = 50');
ok(r.workExperiencePoints===20,'additional experience 5 years = 20');
r=w.DEAcademic.calculate({degreePresent:true,degreeGrade:16.4,workExperienceYears:2,languagePoints:15,computer:true,training:false});
ok(r.degreePoints===41,'degree 16.4 x 2.5 = 41');
ok(r.workExperiencePoints===8,'2 additional years = 8');
ok(r.languagePoints===15 && r.computerPoints===20 && r.trainingPoints===0,'language/computer/training scoring');
r=w.DEAcademic.calculate({degreePresent:true,degreeGrade:9.5,workExperienceYears:8,languagePoints:99,computer:true,training:true});
ok(r.degreePoints===0 && r.degreeValid===false,'invalid grade below 10 scores 0');
ok(r.workExperienceYears===5 && r.workExperiencePoints===20,'work experience clamps at 5');
ok(r.languagePoints===20,'language points clamp at 20');
let lr=w.EducationLanguages.calculate('de',[{language:'english',level:'good'}],{});
ok(lr.points===10 && lr.maxLanguages===1,'DE language B2 = 10, one language');
lr=w.EducationLanguages.calculate('de',[{language:'french',level:'very_good'}],{});
ok(lr.points===15,'DE language C1 = 15');
lr=w.EducationLanguages.calculate('de',[{language:'german',level:'excellent'}],{});
ok(lr.points===20,'DE language C2 = 20');

function e(profile,specialty,main={},aux={}){return w.EaeTableEligibility.calculate({profile,specialty,main,aux});}
ok(e('de','ΔΕ01.05',{msc:true},{}).code==='main','DE main MSc');
ok(e('de','DE02.02',{}, {seminar400:true}).code==='aux','DE auxiliary seminar');
ok(e('de','ΔΕ01.13',{}, {eaeMonths:10}).code==='aux','DE auxiliary 10 EAE months');
ok(e('de','ΔΕ01.14',{}, {childDisability67:true}).code==='aux','DE auxiliary child 67');
ok(e('de','',{},{}).eligible===false,'DE requires specialty');

// Existing PE/TE behavior remains available.
ok(e('pe','ΠΕ61',{},{}).code==='main','PE61 auto-main unchanged');
ok(e('te','ΤΕ01',{msc:true},{}).code==='main','TE main unchanged');
ok(e('te','ΤΕ16',{}, {seminar400:true}).code==='aux','TE auxiliary unchanged');
console.log(`TOTAL PASS=${pass} FAIL=${fail}`);
process.exit(fail?1:0);
