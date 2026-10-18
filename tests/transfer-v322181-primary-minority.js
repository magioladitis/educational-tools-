/* Regression: PE73 minority programme must not borrow EAE procedures or invent codes. */
'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),vm=require('node:vm'),path=require('node:path');
const root=path.resolve(__dirname,'..');
const html=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis-protovathmia.php'),'utf8');
const controller=fs.readFileSync(path.join(root,'includes/transfer-primary.js'),'utf8');
for(const id of ['primaryMinorityFields','primaryMinorityBranch','primaryMinorityRoute','primaryMinorityOrigin','primaryMinorityDestination','primaryMinorityStatus'])
  assert(html.includes('id="'+id+'"'),id);
assert(html.includes('<option value="minority">'),'minority selectable');
assert(!html.includes('value="minority" data-official-code='),'never invent minority official code');
assert(controller.includes("mode!=='minority' && !isPlacement()"),'no EAE qualifications/mandatory mydocs');
assert(controller.includes("if(hideFirst)$('primaryFirst').checked=false"),'within-prefecture improvement should not award area first-preference points');
assert(controller.includes("var general=$('primaryMode').value==='region'||$('primaryMode').value==='minority'"),'ordinary general email format not special EAE');
assert(!html.includes('value="mutual"'),'mutual transfers remain out of scope');
const ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync(path.join(root,'includes/transfer-primary-minority.js'),'utf8'),ctx);
const ev=ctx.EducationTransferPrimaryMinority.evaluate;
assert.equal(ev({branch:'pe73',route:'between',origin:'xanthi',destination:'rodopi',organic:'yes'}).status,'positive');
assert.equal(ev({branch:'pe73',route:'between',origin:'xanthi',destination:'xanthi',organic:'yes'}).status,'review');
assert.equal(ev({branch:'other',route:'within',origin:'rodopi',organic:'yes'}).status,'problem');
assert.equal(ev({branch:'pe73',route:'within',origin:'rodopi',organic:'yes'}).status,'positive');
assert.equal(ev({branch:'pe73',route:'within',origin:'rodopi',organic:'unknown'}).status,'review');
assert.equal(ev({branch:'pe73',route:'between',origin:'evros',destination:'xanthi',organic:'yes'}).body,'ΑΠΥΣΠΕ Ανατολικής Μακεδονίας–Θράκης');
assert.equal(ev({branch:'pe73',route:'within',origin:'evros',organic:'yes'}).body,'Οικείο ΠΥΣΠΕ');
console.log('PASS PE73 minority route and advisory precheck');
