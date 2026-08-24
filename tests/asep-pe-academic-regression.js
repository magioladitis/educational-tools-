const fs = require('fs');
const vm = require('vm');
const path = require('path');
const root = path.resolve(__dirname, '..');
const context = { console };
context.globalThis = context;
vm.createContext(context);
for (const file of ['includes/education-core.js','includes/language-calculations.js','includes/academic-calculations.js','includes/onaseia-calculations.js']) {
  vm.runInContext(fs.readFileSync(path.join(root,file),'utf8'), context, { filename:file });
}
const L=context.EducationLanguages, A=context.EducationAcademic, O=context.OnaseiaAcademic;
let pass=0, fail=0;
function test(name, fn){try{fn(); console.log('✓ '+name); pass++;}catch(e){console.log('✗ '+name+' — '+e.message); fail++;}}
function eq(a,b,msg){if(a!==b) throw new Error((msg?msg+': ':'')+`${a} !== ${b}`)}
function ok(v,msg){if(!v) throw new Error(msg||'expected truthy')}
function lang(profile, specialty, entries){return L.calculate(profile,entries,{specialty});}
function academic(cfg){return A.calculate(Object.assign({specialty:'ΠΕ02',degreeGrade:5,secondDegree:false,phd:false,mscCount:0,languagePoints:0,languageDetails:[],languageWarnings:[],computer:false,training:false},cfg));}

test('base degree 7.50 × 2.5 = 18.75',()=>eq(academic({degreeGrade:7.5}).points,18.75));
test('second degree = 7',()=>eq(academic({secondDegree:true}).points,19.5));
test('PhD = 40',()=>eq(academic({phd:true}).points,52.5));
test('one MSc = 20',()=>eq(academic({mscCount:1}).points,32.5));
test('two MSc = 28 total',()=>eq(academic({mscCount:2}).points,40.5));
test('PE06 English excluded',()=>{const r=lang('pe','ΠΕ06',[{language:'en',level:'excellent'}]);eq(r.points,0);ok(r.warnings.length);});
test('PE06 cannot bypass exclusion through Other=English',()=>{const r=lang('pe','ΠΕ06',[{language:'other',otherText:'English',level:'excellent'}]);eq(r.points,0);ok(r.warnings.length);});
test('PE05 French excluded',()=>eq(lang('pe','ΠΕ05',[{language:'fr',level:'excellent'}]).points,0));
test('PE07 German excluded',()=>eq(lang('pe','ΠΕ07',[{language:'de',level:'excellent'}]).points,0));
test('PE34 Italian excluded',()=>eq(lang('pe','ΠΕ34',[{language:'it',level:'excellent'}]).points,0));
test('PE40 Spanish excluded',()=>eq(lang('pe','ΠΕ40',[{language:'es',level:'excellent'}]).points,0));
test('two distinct excellent languages = 14',()=>eq(lang('pe','ΠΕ02',[{language:'en',level:'excellent'},{language:'fr',level:'excellent'}]).points,14));
test('duplicate language counted once at higher level',()=>eq(lang('pe','ΠΕ02',[{language:'en',level:'good'},{language:'en',level:'excellent'}]).points,7));
test('PE86 computer gives zero and warning',()=>{const r=academic({specialty:'ΠΕ86',computer:true});eq(r.computerPoints,0);ok(r.warnings.some(x=>x.includes('ΠΕ86')));});
test('non-PE86 computer = 4',()=>eq(academic({computer:true}).computerPoints,4));
test('training = 2',()=>eq(academic({training:true}).trainingPoints,2));
test('maximum normal PE academic combination = 120',()=>{const l=lang('pe','ΠΕ02',[{language:'en',level:'excellent'},{language:'fr',level:'excellent'}]);const r=academic({degreeGrade:10,secondDegree:true,phd:true,mscCount:2,languagePoints:l.points,languageDetails:l.details,computer:true,training:true});eq(r.rawPoints,120);eq(r.points,120);});
test('manual PE minimum 12.50 preserved',()=>{eq(O.validateManualAcademicPoints('12,49','ΠΕ02').valid,false);eq(O.validateManualAcademicPoints('12,50','ΠΕ02').valid,true);});
test('manual TE16 minimum 30 preserved',()=>{eq(O.validateManualAcademicPoints('29,99','ΤΕ16').valid,false);eq(O.validateManualAcademicPoints('30','ΤΕ16').valid,true);});
test('manual academic maximum 120 preserved',()=>{eq(O.validateManualAcademicPoints('120','ΠΕ02').valid,true);eq(O.validateManualAcademicPoints('120,01','ΠΕ02').valid,false);});
console.log(`\nPASS: ${pass}`); console.log(`FAIL: ${fail}`); if(fail) process.exit(1);
