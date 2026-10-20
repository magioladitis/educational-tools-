/* Behavioral regression of the dynamic SDE advisor eligibility help. */
'use strict';
const fs=require('fs'), vm=require('vm'),assert=require('assert'),path=require('path');
const src=fs.readFileSync(path.resolve(__dirname,'../includes/sde-registry-ui.js'),'utf8');
const begin=src.indexOf('function renderQualificationGuide(');
const end=src.indexOf('function renderTrainingMinimumWarning(',begin);
assert(begin!==-1&&end>begin);
let current={};
const context={show:(id,value)=>{current[id]=!!value;}};
vm.createContext(context);
vm.runInContext(src.slice(begin,end),context);
function check(d,eligible){current={};context.renderQualificationGuide(d,{eligibility:{eligible}});return {...current};}
let c=check({role:'career',tertiaryDegree:null,careerQualification:'none'},false);
assert(c.sdeQualificationGuide&&c.sdeGuideCareerDegree&&c.sdeGuideCareerQualification&&!c.sdeGuideReady);
c=check({role:'career',tertiaryDegree:true,careerQualification:'master'},true);
assert(c.sdeQualificationGuide&&!c.sdeGuideCareerDegree&&!c.sdeGuideCareerQualification&&c.sdeGuideReady);
c=check({role:'psychologist',psychDegree:true,psychLicense:false,fppBefore1993:true,psychMasterForFpp:false},false);
assert(c.sdeGuidePsychLicense&&c.sdeGuidePsychFpp&&!c.sdeGuidePsychDegree&&!c.sdeGuideReady);
c=check({role:'psychologist',psychDegree:true,psychLicense:true,fppBefore1993:true,psychMasterForFpp:true},true);
assert(c.sdeGuideReady&&!c.sdeGuidePsychFpp);
c=check({role:'educator'},true);
assert(!c.sdeQualificationGuide);
console.log('SDE advisor qualifications: 5/5 scenarios PASS');
