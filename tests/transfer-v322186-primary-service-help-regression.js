'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),path=require('node:path'),cp=require('node:child_process');
const root=path.resolve(__dirname,'..');
const read=f=>fs.readFileSync(path.join(root,f),'utf8');
const shared=read('includes/components/transfer-service-help.php');
const primary=read('ypologismos-morion-metathesis-protovathmia.php');
const secondary=read('ypologismos-morion-metathesis.php');
const js=read('includes/transfer-primary.js');
assert.equal((primary.match(/Α\. Τύπος διαδικασίας και υπηρεσιακά στοιχεία/g)||[]).length,1);
assert.equal((secondary.match(/Α\. Τύπος διαδικασίας και υπηρεσιακά στοιχεία/g)||[]).length,1);
assert.ok(!primary.includes('Α. Διαδικασία και υπηρεσιακός προέλεγχος'));
for(const level of ['primary','secondary'])for(const kind of ['service','msd']){
  assert.match(level==='primary'?primary:secondary,new RegExp(`transferServiceHelp\\('${level}', '${kind}'\\)`));
}
for(const summary of ['Οδηγίες','Πού βλέπω τις υπηρετήσεις μου;'])assert.ok(shared.includes(summary));
assert.ok(!js.includes('ⓘ Ελέγξτε την υπηρεσία στην οργανική'));
assert.match(js,/var state=\$\('primaryOrganic'\)\.value;/);
assert.ok(js.includes("? '✓ Δηλώθηκε συμπλήρωση έτους οργανικής."));
assert.ok(js.includes("? '⚠ Η μη συμπλήρωση έτους οργανικής"));
assert.match(read('assets/transfer-primary.css'),/#primaryEligibility:empty\s*\{\s*display:none;/);
// Test server-rendered disclosures rather than relying solely on static PHP source.
for(const [file,ids] of [
  ['ypologismos-morion-metathesis-protovathmia.php',['primaryServiceRegistryHelp','primaryMsdRegistryHelp']],
  ['ypologismos-morion-metathesis.php',['serviceRegistryHelp','msdRegistryHelp']]
]){
  const res=cp.spawnSync('php',[file],{cwd:root,encoding:'utf8',timeout:12000});
  assert.equal(res.status,0,res.stderr);
  const html=res.stdout;
  for(const id of ids){
    assert.equal((html.match(new RegExp(`id="${id}"`,'g'))||[]).length,1,`rendered ${id}`);
    assert.match(html,new RegExp(`<details[^>]+id="${id}"[^>]*>`));
  }
  assert.ok(html.includes('teachers.minedu.gov.gr'));
}
console.log('PASS v3.22.186 shared help boxes, common section title, hidden default organic notice');
