'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),cp=require('node:child_process');
const root=path.resolve(__dirname,'..');
const read=(f)=>fs.readFileSync(path.join(root,f),'utf8');
const labels=JSON.parse(cp.execFileSync('php',['-r',
 'require '+JSON.stringify(path.join(root,'includes/teacher-specialties.php'))+';'+
 '$a=[];foreach(teacherSpecialties() as $code=>$item){$a[teacherSpecialtyCanonicalCode($code)]=$item["label"];}echo json_encode($a,JSON_UNESCAPED_UNICODE);'
],{encoding:'utf8'}));
const map=Object.create(null),dom={getElementById(id){return map[id]||(map[id]={value:'region'});},addEventListener(){}};
const ctx={window:{EducationTransferTeacherSpecialtyLabels:labels},document:dom,navigator:{},console};
for(const f of ['includes/specialty-code-normalization.js','includes/transfer-artistic-vacancies.js','includes/transfer-eligibility.js','includes/transfer-documents.js'])
 vm.runInNewContext(read(f),ctx,{filename:f});
const stock=ctx.window.EducationArtisticVacancies;
assert.equal(stock.totalVacancies,65);
assert.equal(stock.rows.length,55);
assert.equal(new Set(stock.rows.map(x=>x.school)).size,12);
assert.equal(stock.branches().length,13);
assert.ok(stock.branches().every(x=>x.label));
assert.equal(stock.branches().reduce((n,x)=>n+x.positions,0),65);
assert.equal(stock.branches().find(x=>x.code==='ΠΕ01').label,'Θεολόγοι');
assert.equal(stock.count('PE8','ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΔΡΑΜΑΣ','Ζωγραφική'),2);
assert.equal(stock.count('ΠΕ08','ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΔΡΑΜΑΣ','Γλυπτική'),1);
assert.equal(stock.count('ΠΕ08','ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΔΡΑΜΑΣ','Πιάνο'),0);
assert.equal(stock.hasBranch('ΠΕ70'),false);
const music=stock.branches().find(x=>x.code===stock.musicGroup);
assert.equal(music.positions,6,'A single published pooled vacancy count, never duplicated');
assert.ok(stock.schools(stock.musicGroup).length>=1);
assert.equal(stock.hasBranch('ΠΕ79.01'),false,'do not falsely split pooled musical vacancy');
assert.ok(stock.rows.every(x=>x.specialization!=='7'));
const eligibility=ctx.window.EducationTransferEligibility;
const artBase={branch:'pe08',branchCode:'ΠΕ08',school:'ΚΑΛΛΙΤΕΧΝΙΚΟ ΓΥΜΝΑΣΙΟ ΔΡΑΜΑΣ',specialization:'Ζωγραφική',organic:true,teachingYears:5,orientation:'visual'};
assert.ok(!eligibility.art(artBase).advice.some(x=>x.includes('σχολείο δεν διαθέτει')));
assert.ok(eligibility.art({...artBase,specialization:'Πιάνο'}).advice.some(x=>x.includes('ειδίκευση')));
const doc=ctx.window.EducationTransferDocuments;
for(const mode of ['region','art','intercultural','kedasy']){
  const m=doc.submissionMessageTemplate(mode,{name:'Δοκιμή',branch:'ΠΕ01',registry:'123456789012345678901',protocol:'111',home:'ΔΔΕ Κέρκυρας'});
  assert.ok(m.body.includes(', ΠΕ01, ')&&!m.body.includes('Θεολόγοι'));
  assert.ok(m.body.includes('123456789012345'));
  assert.ok(!m.body.includes('1234567890123456'));
}
assert.ok(read('includes/transfer-documents.js').includes("key==='registry'?15"));
assert.ok(read('includes/transfer-documents.js').includes('<select id="transferPersonal-branch"'));
assert.ok(read('ypologismos-morion-metathesis.php').includes('transfer-artistic-vacancies.js'));
console.log('PASS v3.22.135 artistic vacancies (65/12/13), specialization, pooled Music, shared labels, dropdown, registry max 15');
