'use strict';
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
const assert=require('node:assert/strict');
const cp=require('node:child_process');
const root=path.join(__dirname,'..');
const read=f=>fs.readFileSync(path.join(root,f),'utf8');
const context={window:{},String,Object,Array,encodeURIComponent};
vm.createContext(context);
vm.runInContext(read('includes/transfer-email-component.js'),context);
const shared=context.window.EducationTransferEmailComponent;
assert.equal(typeof shared.specialtyOptions,'function');
assert.equal(typeof shared.fillSpecialtyDropdown,'function');
const raw=cp.execFileSync('php',['-r',
 'require '+JSON.stringify(path.join(root,'includes/teacher-specialties.php'))+';'+
 '$r=array();foreach(teacherSpecialties() as $c=>$info){$r[teacherSpecialtyCanonicalCode($c)]=$info["label"];}echo json_encode($r,JSON_UNESCAPED_UNICODE);'
],{encoding:'utf8'});
const all=JSON.parse(raw);
for(const [code,label] of Object.entries({'ΠΕ03':'Μαθηματικοί','ΠΕ60':'Νηπιαγωγοί','ΠΕ70':'Δάσκαλοι','ΠΕ71':'Δάσκαλοι Ειδικής Αγωγής και Εκπαίδευσης'}))
 assert.equal(all[code],label);
const secondaryHtml=shared.specialtyOptions(all,'ΠΕ03');
assert(secondaryHtml.includes('<option value="ΠΕ03" selected>ΠΕ03 — Μαθηματικοί</option>'));
assert(secondaryHtml.includes('<option value="ΠΕ70">ΠΕ70 — Δάσκαλοι</option>'));
const primaryPhp=read('ypologismos-morion-metathesis-protovathmia.php');
const secPhp=read('ypologismos-morion-metathesis.php');
const priJs=read('includes/transfer-primary.js');
const secJs=read('includes/transfer-documents.js');
assert(primaryPhp.includes("require_once __DIR__ . '/includes/teacher-specialties.php'"));
assert(primaryPhp.includes('EducationTransferPrimarySpecialtyLabels'));
assert(secPhp.includes('EducationTransferTeacherSpecialtyLabels'));
assert(primaryPhp.includes('<select id="primarySpecialty"'));
assert(!primaryPhp.includes('id="primaryBranchHints"'));
assert(priJs.includes('fillSpecialtyDropdown('));
assert(secJs.includes('shared.specialtyOptions(labels,submissionPersonal.branch)'));
const sample={'ΠΕ60':'Νηπιαγωγοί','ΠΕ70':'Δάσκαλοι','ΠΕ71':'Δάσκαλοι Ειδικής Αγωγής και Εκπαίδευσης'};
const opts=shared.specialtyOptions(sample,'');
const fake={innerHTML:''};
shared.fillSpecialtyDropdown(fake,sample,'ΠΕ70');
assert(fake.innerHTML.startsWith('<option value="">— Επιλέξτε ειδικότητα —</option>'));
assert(fake.innerHTML.includes('<option value="ΠΕ70" selected>ΠΕ70 — Δάσκαλοι</option>'));
assert(opts.indexOf('ΠΕ60')<opts.indexOf('ΠΕ70'));
// User-provided/unsafe labels must never render as HTML in shared dropdowns.
assert(shared.specialtyOptions({'ΠΕ70':'Δάσκαλοι <script>alert(1)</script>'},'').includes('&lt;script&gt;'));
// Same UI helper cannot contain legal formulas or app-specific service rules.
for(const forbidden of ['138014/Ε2','138023/Ε2','ΔΔΕ οργανικής','ΔΠΕ οργανικής'])
 assert(!read('includes/transfer-email-component.js').includes(forbidden));
console.log('PASS shared teacher specialty dropdown, Primary filtered branch registry and isolated official email templates');
