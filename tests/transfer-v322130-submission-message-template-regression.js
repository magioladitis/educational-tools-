'use strict';
const fs=require('fs'),vm=require('node:vm'),assert=require('node:assert/strict'),path=require('node:path');
const dir=path.resolve(__dirname,'..');
const code=fs.readFileSync(path.join(dir,'includes/transfer-documents.js'),'utf8');
const page=fs.readFileSync(path.join(dir,'ypologismos-morion-metathesis.php'),'utf8');
const fields=Object.create(null);
const mock=(id)=>fields[id]||(fields[id]={value:'',checked:false,selectedOptions:[{textContent:'Τύπος'}]});
const dom={getElementById:(id)=>mock(id),querySelectorAll:()=>[],addEventListener:()=>{}};
const sandbox={window:{},document:dom};
vm.runInNewContext(code,sandbox);
const tool=sandbox.window.EducationTransferDocuments;
const special=['smeae','eae_sensory','kedasy','music','art','intercultural'];
const simple=['region','local'];
const types=simple.concat(special);
const test=(ok,label)=>assert.ok(ok,label);
for (const mode of types){
 mock('mode').value=mode;
 const tpl=tool.submissionMessageTemplate();
 const guide=tool.submissionGuideData();
 test(tpl.mode===mode,'template follows current mode '+mode);
 test(guide.lines.length>=2,'guidance persists '+mode);
 test(tpl.subject.includes('[Αριθμός μητρώου]')&&tpl.subject.includes('[Αριθμός πρωτοκόλλου'),'missing subject placeholders '+mode);
 test(tpl.body.includes('[Ονοματεπώνυμο]')&&tpl.body.includes('[Κλάδος/ειδικότητα]'),'missing body placeholders '+mode);
 if(simple.includes(mode)){
  test(!tpl.subject.startsWith('ΔΕ,')&&!tpl.body.includes('[ΔΔΕ οργανικής]'),'general format per 2.2.1 '+mode);
  test(tpl.circular==='2.2.1','general paragraph source '+mode);
 }else{
  test(tpl.subject.startsWith('ΔΕ,')&&tpl.subject.endsWith('[Κλάδος/ειδικότητα]'),'special format per 2.2.4/2.2.5 '+mode);
  test(tpl.body.endsWith('[ΔΔΕ οργανικής]'),'special body DDE '+mode);
  test(tpl.circular===(special.indexOf(mode)<3?'2.2.4':'2.2.5'),'special paragraph source '+mode);
 }
}
const body=mock('transferSubmissionGuidanceBody');
for(const mode of types){
 mock('mode').value=mode;
 tool.renderSubmissionGuidance();
 test(body.innerHTML.includes('data-transfer-copy-template="subject"')&&body.innerHTML.includes('data-transfer-copy-template="body"'),'copy buttons '+mode);
 test(body.innerHTML.includes('Αριθμός πρωτοκόλλου'),'template injected '+mode);
}
test(page.includes('transferSubmissionGuidance')&&page.includes('Πού αποστέλλονται τα δικαιολογητικά; — Οδηγίες και υπόδειγμα'),'template inside one routing disclosure');
test(code.includes("'<h3>Θέμα και σώμα μηνύματος — υπόδειγμα</h3>"),'print includes template');
test(!page.includes('data-transfer-copy-template'),'template not duplicated statically');
console.log('PASS v3.22.130 8 routes, circular §2.2.1 / §2.2.4 / §2.2.5, dynamic copy templates, printing');
