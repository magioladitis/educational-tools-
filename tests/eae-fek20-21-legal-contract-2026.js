// Focused legal-source contract: ASEP FEK 20/22.05.2025 and 21/23.05.2025.
// This checks selected explicit scoring rules; not a complete legal certification.
const fs=require('fs'),path=require('path'),vm=require('vm'),assert=require('assert');
const root=path.join(__dirname,'..');const read=f=>fs.readFileSync(path.join(root,f),'utf8');
const ea1=read('includes/asep-1ea-ui.js');const p1=read('ypologismos-morion-1ea-2025.php');const p2=read('ypologismos-morion-2ea-2025.php');
let n=0;function check(label,fn){fn();n++;console.log('PASS',label)}
check('1EA upper academic limits 64/96',()=>assert(ea1.includes("type==='postsecondary'?96:64")));
check('1EA grade rate 4/7.2 per point above 10',()=>{assert(ea1.includes("type==='postsecondary'?7.2:4"));assert(ea1.includes('Math.max(0,grade-10)*rate'));});
check('1EA second title +10',()=>assert(ea1.includes("$('secondTitle').checked?10:0")));
check('1EA computer +4 training +2',()=>{assert(ea1.includes("$('computer').checked?4:0"));assert(ea1.includes("$('training').checked?2:0"));});
check('1EA children +3, disability 50 and 0.4',()=>{assert(p1.includes("'child_points' => 3"));assert(p1.includes("'min_disability_percent' => 50"));assert(p1.includes("'disability_rate' => '0,4'"));});
check('2EA grade ×2.5, academic 120',()=>{assert(p2.includes("'data-profile' => 'eep'"));assert(p2.includes('Μέγιστο κατηγορίας Α: 120'));});
check('2EA children +3 disability thresholds',()=>{assert(p2.includes("'child_points' => 3"));assert(p2.includes("'min_disability_percent' => 50"));});
check('2EA no 0.9 private-service carryover',()=>assert(p2.includes('δεν αφορά τα μέλη ΕΕΠ-ΕΒΠ')));
check('1EA and 2EA capped service',()=>{assert(p1.includes('Μέγιστο 120 μόρια'));assert(p2.includes('Μέγιστο 120 μόρια'));});
const context={console};context.window=context;context.globalThis=context;vm.createContext(context);
for(const f of ['includes/specialty-code-normalization.js','includes/education-core.js','includes/academic-calculations.js','includes/social-calculations.js'])vm.runInContext(read(f),context,{filename:f});
check('EEP actual calculation grade 8 -> 20',()=>assert.strictEqual(context.EducationAcademic.calculate({profile:'eep',specialty:'ΠΕ23',degreeGrade:8,languagePoints:0}).degreePoints,20));
check('EEP academic cap 120 actual',()=>assert.strictEqual(context.EducationAcademic.calculate({profile:'eep',specialty:'ΠΕ23',degreeGrade:10,phd:true,mscCount:2,secondDegree:true,computer:true,training:true,languagePoints:50}).points,120));
check('Social 2 children 6 points',()=>assert.strictEqual(context.EducationSocial.calculate({children:2}).childrenPoints,6));
check('Social child disability 50% = 20 points',()=>assert.strictEqual(context.EducationSocial.calculate({childDisability:50}).disabilityPoints,20));
console.log('RESULT:',n,'PASS');
