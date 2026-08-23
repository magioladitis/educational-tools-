const fs=require('fs'),vm=require('vm'),assert=require('assert');
function load(file,globalName){const src=fs.readFileSync(file,'utf8');const ctx={globalThis:{}};vm.createContext(ctx);vm.runInContext(src,ctx);return ctx.globalThis[globalName];}
const R=load(__dirname+'/../includes/sde-registry-calculations.js','SDERegistryCalc');
const A=load(__dirname+'/../includes/sde-calculations.js','SDECalculator');
const L=load(__dirname+'/../includes/sde-leadership-calculations.js','SDELeadership');
function rb(role){return {role,phd:'none',master:'none',secondDegree:false,secondPhd:false,secondMaster:false,extraCredential:false,trainingSdeHours:0,trainingAdultHours:0,trainingThematicHours:0,expSdeHours:0,expAdultHours:0,expFormalHours:0,expSdeMonths:0,expAdultCounsellingMonths:0,language1:'',languageLevel1:'none',language2:'',languageLevel2:'none',computer:false,unemploymentMonths:0,unemploymentExtraDays:0,threeChildren:false,singleParent:false,manyChildren:false,disability:false};}
let d=rb('educator');d.trainingSdeHours=14;assert.equal(R.calculateAll(d).education.training.total,0);
d=rb('educator');d.trainingSdeHours=15;assert.equal(R.calculateAll(d).education.training.total,0.0375);
let a={phd:'none',master:'none',secondDegree:false,secondPhd:'none',secondMaster:'none',sdeTrainingHours:14,adultTrainingHours:0,sdeYears:0,sdeHourlyHours:0,adultEducationHours:0,formalEducationYears:0,language1:'',languageLevel1:'none',language2:'',languageLevel2:'none',computer:false,specialty:''};
assert.equal(A.calculateEducation(a).training,0);a.sdeTrainingHours=15;assert.equal(A.calculateEducation(a).training,0.0375);
let l={trainingHours:14};assert.equal(L.calculateTraining(l).total,0);l.trainingHours=15;assert.equal(L.calculateTraining(l).total,0.08);
console.log('Training minimum regression: PASS');
