/* v3.22.177: direct social IDs + one shared browser HTML escaper. */
'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const {execFileSync}=require('node:child_process');
const root=path.resolve(__dirname,'..');
const read=f=>fs.readFileSync(path.join(root,f),'utf8');
const php=file=>execFileSync('php',[path.join(root,file)],{encoding:'utf8'});
const common=read('includes/components/transfer-social-criteria.php');
assert.doesNotMatch(common,/str_replace\(/);
assert.match(common,/htmlspecialchars\(\$id\(/);
assert.match(common,/primaryChildrenExtra/);
const sec=php('ypologismos-morion-metathesis.php');
const prim=php('ypologismos-morion-metathesis-protovathmia.php');
for (const name of ['eligibleChildren','coService','transferCoServiceCase','transferSpecialCase','locality','firstPreference']){
  assert.match(sec,new RegExp('id="'+name+'"'));
}
for (const name of ['primaryChildren','primaryCoservice','primaryCoServiceCase','primarySpecialCase','primaryLocality','primaryFirst']){
  assert.match(prim,new RegExp('id="'+name+'"'));
  assert.equal(prim.split('id="'+name+'"').length-1,1);
}
assert.match(prim,/id="primaryChildrenExtra"/);
const sandbox={window:{}};
vm.runInNewContext(read('includes/transfer-html-escape.js'),sandbox);
const esc=sandbox.window.EducationTransferHtmlEscape.escapeHtml;
assert.equal(esc('<img src="x" onerror=\'y\'>&'),'&lt;img src=&quot;x&quot; onerror=&#39;y&#39;&gt;&amp;');
for(const file of ['includes/transfer-primary.js','includes/transfer-primary-periods.js','includes/transfer-ui.js']){
  assert.match(read(file),/EducationTransferHtmlEscape\.escapeHtml/,file);
}
for (const file of ['ypologismos-morion-metathesis.php','ypologismos-morion-metathesis-protovathmia.php']) {
  const text=read(file);
  assert.ok(text.indexOf('transfer-html-escape.js')>=0);
  assert.ok(text.indexOf('transfer-html-escape.js')<text.indexOf(file.includes('protovathmia')?'transfer-primary-periods.js':'transfer-ui.js'));
}
console.log('PASS transfer shared social rendered IDs and HTML escaping');
