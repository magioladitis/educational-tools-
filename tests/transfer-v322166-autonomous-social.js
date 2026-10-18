'use strict';
const assert=require('node:assert/strict');const fs=require('node:fs');const vm=require('node:vm');const path=require('node:path');
const root=path.resolve(__dirname,'..');const code=fs.readFileSync(path.join(root,'includes/transfer-social-input.js'),'utf8');
function scenario(grade){
  const ids=grade==='primary'?['primaryCoservice','primaryCoServiceCaseField','primaryCoServiceCase']:['coService','transferCoServiceCaseField','transferCoServiceCase'];
  const handlers={};
  const check={checked:false,addEventListener(k,cb){handlers['check:'+k]=cb;}};
  const choice={value:'unspecified',addEventListener(k,cb){handlers['choice:'+k]=cb;}};
  const attrs={};const field={style:{display:'none'},setAttribute(k,v){attrs[k]=v;},getAttribute(k){return attrs[k]||null;}};
  const nodes={[ids[0]]:check,[ids[1]]:field,[ids[2]]:choice};
  const document={readyState:'complete',getElementById(id){return nodes[id]||null;}};
  const ctx={window:{},document};vm.runInNewContext(code,ctx);
  assert.equal(attrs['data-social-bound'],'yes',grade+' should bind independently');
  assert.equal(field.style.display,'none');
  check.checked=true;handlers['check:change']();assert.equal(field.style.display,'block',grade+' opens');
  check.checked=false;handlers['check:input']();assert.equal(field.style.display,'none',grade+' closes');
  choice.value='private_employee';handlers['choice:change']();assert.equal(field.style.display,'block',grade+' reopens');
  assert.equal(attrs['aria-hidden'],'false');
  // No grade controller, intercultural assets, or vacancy module was loaded.
}
scenario('primary');scenario('secondary');
const php=fs.readFileSync(path.join(root,'includes/components/transfer-social-criteria.php'),'utf8');
assert.ok(!php.includes("'modeNote' =>"),'dead mapping removed');
assert.ok(!php.includes("'<div class=\"check-row\"><label><input id=\"primaryFamily\"'"),'fragile markup substitution removed');
console.log('PASS autonomously opens both grades without optional dependency modules');
