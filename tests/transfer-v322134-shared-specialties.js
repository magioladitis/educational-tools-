'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs'),path=require('node:path'),vm=require('node:vm'),cp=require('node:child_process');
const root=path.resolve(__dirname,'..');
const read=(p)=>fs.readFileSync(path.join(root,p),'utf8');
const php=read('ypologismos-morion-metathesis.php');
assert.ok(php.includes("require_once __DIR__ . '/includes/teacher-specialties.php'"));
assert.ok(php.includes('window.EducationTransferTeacherSpecialtyLabels'));
assert.ok(php.indexOf('window.EducationTransferTeacherSpecialtyLabels') < php.indexOf('transfer-intercultural-vacancies.js'));
assert.ok(php.includes('JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT'));
const labels=JSON.parse(cp.execFileSync('php',['-r',
  'require '+JSON.stringify(path.join(root,'includes/teacher-specialties.php'))+';'+
  '$result=array(); foreach(teacherSpecialties() as $code=>$specialty){ $result[teacherSpecialtyCanonicalCode($code)]=$specialty["label"]; } echo json_encode($result, JSON_UNESCAPED_UNICODE);'
],{encoding:'utf8'}));
const ctx={window:{EducationTransferTeacherSpecialtyLabels:labels},Number,String,Math,Array,Object};
vm.runInNewContext(read('includes/specialty-code-normalization.js'),ctx);
vm.runInNewContext(read('includes/transfer-intercultural-vacancies.js'),ctx);
vm.runInNewContext(read('includes/transfer-eligibility.js'),ctx);
const code=ctx.window.EducationSpecialtyCodes,stock=ctx.window.EducationInterculturalVacancies;
assert.equal(code.schema,'teacher_specialty_code_normalization_v1');
assert.equal(code.normalize('PE3'),'ΠΕ03');
assert.equal(code.normalize('ΠΕ04-02'),'ΠΕ04.02');
assert.equal(stock.rows.length,22);assert.equal(stock.totalVacancies,23);
assert.equal(stock.availableBranches().length,13);
assert.equal(stock.availableBranches().reduce((n,row)=>n+row.positions,0),23);
assert.ok(stock.availableBranches().every(row=>row.label && row.label===labels[row.code]));
assert.ok(stock.rows.every(row=>!Object.prototype.hasOwnProperty.call(row,'name')),
  'No duplicate labels in annual vacancy data');
assert.equal(stock.availableBranches().find(x=>x.code==='ΠΕ03').label,'Μαθηματικοί');
assert.equal(stock.availableBranches().find(x=>x.code==='ΠΕ04.02').label,'Χημικοί');
assert.equal(stock.hasBranch('PE3'),true);
assert.equal(stock.count('ΠΕ03','ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ'),2);
assert.equal(stock.count('PE3','ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ'),2);
assert.equal(stock.hasBranch('ΠΕ70'),false);
assert.equal(stock.hasBranch('ΠΕ04.01'),false);
const base={organic:'yes',current:'no',vacancy:'yes',languageCase:'usual'};
assert.equal(ctx.window.EducationTransferEligibility.intercultural({
  ...base, branch:'ΠΕ03',school:'ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ'
}).verdict,'preliminary_met');
console.log('PASS v3.22.134: shared canonical specialty normalizer + PHP labels + all 13 ΔΕ codes + vacancy validation');
