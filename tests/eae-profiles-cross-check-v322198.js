/* EAE 1-4: cross-profile scoring and eligibility regression. */
const fs=require('fs'),vm=require('vm'),path=require('path'),assert=require('assert');
const root=path.resolve(__dirname,'..');let c={console};c.window=c;c.globalThis=c;vm.createContext(c);
for(const f of ['includes/specialty-code-normalization.js','includes/education-core.js','includes/academic-calculations.js','includes/eae-table-eligibility.js']) vm.runInContext(fs.readFileSync(path.join(root,f),'utf8'),c,{filename:f});
const calc=(cfg)=>c.EducationAcademic.calculate({specialty:'ΠΕ02',degreeGrade:8,languagePoints:0,...cfg});
const elig=(cfg)=>c.EaeTableEligibility.calculate(cfg);
let n=0; function t(name,fn){fn();n++;console.log('PASS',name)}
t('2EA academic EEP degree 8 = 20',()=>assert.equal(calc({profile:'eep'}).degreePoints,20));
t('3EA PE61 special 20 without extra master',()=>assert.equal(calc({profile:'eae',specialty:'ΠΕ61',mscCount:0}).mscPoints,20));
t('3EA PE71 + extra master 28 total',()=>assert.equal(calc({profile:'eae',specialty:'ΠΕ71',mscCount:1}).mscPoints,28));
t('3EA PE11 specialization adds exactly 8',()=>assert.equal(calc({profile:'eae',specialty:'ΠΕ11',eaePe11Specialization:true}).specialProfilePoints,8));
t('3EA PE02 does not get PE11 specialization',()=>assert.equal(calc({profile:'eae',specialty:'ΠΕ02',eaePe11Specialization:true}).specialProfilePoints,0));
t('EEP cannot get PE61/71 EAE-special extra',()=>assert.equal(calc({profile:'eep',specialty:'ΠΕ61',mscCount:0}).mscPoints,0));
t('EEP same master scored 20',()=>assert.equal(calc({profile:'eep',specialty:'ΠΕ61',mscCount:1}).mscPoints,20));
t('EAE PE61 automatic main',()=>assert.equal(elig({profile:'pe',specialty:'ΠΕ61'}).code,'main'));
t('EAE PE71 automatic main',()=>assert.equal(elig({profile:'pe',specialty:'ΠΕ71'}).code,'main'));
t('EAE PE02 10 months auxiliary',()=>assert.equal(elig({profile:'pe',specialty:'ΠΕ02',aux:{eaeMonths:10}}).code,'aux'));
t('EAE PE02 nine months insufficient',()=>assert.equal(elig({profile:'pe',specialty:'ΠΕ02',aux:{eaeMonths:9}}).code,'none'));
t('EAE seminar400 auxiliary',()=>assert.equal(elig({profile:'te',specialty:'ΤΕ16',aux:{seminar400:true}}).code,'aux'));
t('EAE main overrides auxiliary',()=>assert.equal(elig({profile:'pe',specialty:'ΠΕ02',main:{phd:true},aux:{seminar400:true}}).code,'main'));
t('EAE unknown specialty no eligibility',()=>assert.equal(elig({profile:'pe',specialty:'',main:{phd:true}}).code,'none'));
t('3EA computer excluded PE86',()=>assert.equal(calc({profile:'eae',specialty:'ΠΕ86',computer:true}).computerPoints,0));
t('3EA cap 120',()=>assert.equal(calc({profile:'eae',degreeGrade:10,phd:true,mscCount:2,secondDegree:true,computer:true,training:true,languagePoints:30}).points,120));
for(let i=1;i<=4;i++){const s=fs.readFileSync(path.join(root,`ypologismos-morion-${i}ea-2025.php`),'utf8');t(`${i}EA sensory common resource`,()=>assert(s.includes('eae-sensory-proof.js')));}
console.log('RESULT:',n,'PASS');
