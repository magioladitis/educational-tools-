'use strict';
const assert=require('node:assert/strict'),fs=require('fs'),path=require('path'),vm=require('vm');
const root=path.join(__dirname,'..');
const source=p=>fs.readFileSync(path.join(root,p),'utf8');
const context={window:{}};
vm.runInNewContext(source('includes/transfer-children-documents.js'),context);
const shared=context.window.EducationTransferChildrenDocuments;
assert.ok(shared,'shared engine created');
for(const grade of ['primary','secondary']){
 const text=shared.detail(grade),html=shared.render(grade);
 for(const token of ['01/01/2009','01/01/2002','βεβαίωση σπουδών','ΣΑΕΚ/Κολλέγια','επιμέλειας','ΠΟΛΥΤΕΚΝΙΑ','67%','ΝΕΟΓΕΝΝΗΤΟ','31/12/2026','τελευταίου τριμήνου','αυτεπαγγέλτως']){
  assert.ok(text.includes(token),grade+' missing '+token);
 }
 assert.ok(html.includes('class="transfer-children-guide"') && html.includes('ΠΟΛΥΤΕΚΝΙΑ'));
 assert.ok(text.includes(grade==='primary'?'138023/Ε2':'138014/Ε2'));
}
assert.ok(shared.summary().includes('Σπουδάζοντα'));
const sec=source('includes/transfer-documents.js'),prim=source('includes/transfer-primary.js');
assert.match(sec,/EducationTransferChildrenDocuments\.detail\('secondary'\)/);
assert.match(prim,/EducationTransferChildrenDocuments\.render\('primary'\)/);
assert.match(prim,/info\('children-guide'/);
assert.match(prim,/primaryChildrenExtra/);
assert.ok(!sec.includes('ΑΝΗΛΙΚΑ (γέννηση από 01/01/2009)'),'no duplicate canon in secondary');
for(const file of ['ypologismos-morion-metathesis-protovathmia.php','ypologismos-morion-metathesis.php']){
 const page=source(file),a=page.indexOf('transfer-children-documents.js');
 assert.ok(a>=0,`${file}: missing shared module`);
 assert.ok(a<page.indexOf(file.includes('protovathmia')?'transfer-primary.js':'transfer-documents.js'));
}
console.log('PASS v3.22.175 common children evidence in both grades (same source, distinct refs)');
