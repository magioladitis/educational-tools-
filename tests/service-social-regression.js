const fs = require('fs');
const vm = require('vm');
const path = require('path');
const ROOT = path.resolve(__dirname, '..');

function load(file, sandbox) {
  vm.runInContext(fs.readFileSync(path.join(ROOT, file), 'utf8'), sandbox, {filename:file});
}
function approx(a,b){ return Math.abs(Number(a)-Number(b)) < 1e-9; }
let pass=0, fail=0;
function test(name, condition){ if(condition){console.log('PASS',name);pass++;} else {console.error('FAIL',name);fail++;} }

// Core service engine
const core = { console };
core.window = core;
vm.createContext(core);
load('includes/education-core.js', core);
load('includes/service-calculations.js', core);
load('includes/social-calculations.js', core);

let r = core.EducationService.calculateAsepService({regularMonths:10,difficultMonths:5});
test('regular + difficult', approx(r.raw,20) && approx(r.points,20) && r.months===15);
r = core.EducationService.calculateAsepService({threeMonthRegular2020:8});
test('2020 regular three-month cap 10', approx(r.points,10) && r.parts.threeMonthRegular2020.months===8);
r = core.EducationService.calculateAsepService({threeMonthDifficult2021:7});
test('2021 difficult three-month cap 20', approx(r.points,20) && r.parts.threeMonthDifficult2021.months===7);
r = core.EducationService.calculateAsepService({privateMonths:10});
test('private 0.9', approx(r.points,9));
r = core.EducationService.calculateAsepService({regularMonths:100,difficultMonths:60});
test('service total cap 120', approx(r.raw,220) && approx(r.points,120) && r.capped===true && r.warnings.length===1);
r = core.EducationService.calculateAsepService({regularMonths:5,digitalTutoring:{points:13.5,countedMonths:9,activeYears:[{schoolYear:'2024-2025'}],warnings:['digital-warning']}});
test('digital tutoring included', approx(r.raw,18.5) && r.months===14 && r.warnings[0]==='digital-warning');
r = core.EducationService.calculateAsepService({});
test('missing optional service categories are zero', approx(r.points,0) && r.months===0);

// Social arithmetic unchanged
let s = core.EducationSocial.calculate({children:2,candidateDisability:60,spouseDisability:80,childDisability:70,marriageYears4Plus:true});
test('social children points', approx(s.childrenPoints,6));
test('social highest disability only', s.highestPerson==='spouse' && approx(s.disabilityPoints,32) && approx(s.total,38));
s = core.EducationSocial.calculate({candidateDisability:70,candidateMentalCondition:true});
test('candidate mental-condition exclusion', approx(s.disabilityPoints,0) && s.warnings.length===1);
s = core.EducationSocial.calculate({spouseDisability:70,marriageYears4Plus:false});
test('spouse marriage rule', approx(s.disabilityPoints,0) && s.warnings.length===1);
s = core.EducationSocial.calculate({childDisability:67});
test('67 percent EAE flag preserved', s.childDisability67===true);

// Lightweight DOM contract for service controller.
const roleElements = {
  'regular': {value:'12'},
  'difficult': {value:'3'},
  'three-month-regular-2020': {value:'2'},
  'three-month-regular-2021': {value:'1'},
  'three-month-difficult-2020': {value:'0'},
  'three-month-difficult-2021': {value:'0'},
  'private': {value:'10'}
};
const digitalEl={id:'digitalTutoring'};
const serviceRoot={
  nodeType:1,
  querySelector(sel){
    const m=sel.match(/data-service-role="([^"]+)"/); if(!m) return null;
    if(m[1]==='digital-tutoring') return digitalEl;
    return roleElements[m[1]]||null;
  },
  querySelectorAll(){ return []; },
  getAttribute(){ return ''; },
  dispatchEvent(){}
};
const serviceSandbox={console, EducationService:core.EducationService, AsepDigitalTutoring:{getState(){return {points:1.5,countedMonths:1,activeYears:[{}],warnings:[]}},details(){return ['digital detail']},reset(){}}};
serviceSandbox.window=serviceSandbox;
serviceSandbox.document={getElementById(id){return id==='asepService'?serviceRoot:null},querySelector(){return serviceRoot},querySelectorAll(){return []}};
serviceSandbox.CustomEvent=function(){};
vm.createContext(serviceSandbox);
load('includes/asep-service-controller.js',serviceSandbox);
r=serviceSandbox.AsepServiceController.calculate('asepService');
test('service controller reads data roles', approx(r.points,12+6+3+1.5+9+1.5));

// Lightweight DOM contract for social controller.
const socialEls={
  children:{value:'3'}, candidate:{value:'55'}, spouse:{value:'80'}, child:{value:'67'}, marriage:{checked:true}, mental:{checked:false}
};
const attrMap={
  'data-children-id':'children','data-candidate-id':'candidate','data-spouse-id':'spouse','data-child-id':'child','data-marriage-id':'marriage','data-mental-id':'mental',
  'data-warning-id':'','data-subtotal-id':''
};
const socialRoot={nodeType:1,getAttribute(k){return attrMap[k]||''},dispatchEvent(){}};
const socialSandbox={console,EducationSocial:core.EducationSocial};
socialSandbox.window=socialSandbox;
socialSandbox.document={getElementById(id){if(id==='socialCriteria')return socialRoot;return socialEls[id]||null},querySelector(){return socialRoot},querySelectorAll(){return []}};
socialSandbox.CustomEvent=function(){};
vm.createContext(socialSandbox);
load('includes/asep-social-criteria.js',socialSandbox);
s=socialSandbox.AsepSocialCriteria.calculate('socialCriteria');
test('social controller reads component mapping', s.children===3 && s.highestPerson==='spouse' && approx(s.total,41));

console.log(`RESULT ${pass} PASS / ${fail} FAIL`);
process.exit(fail?1:0);
