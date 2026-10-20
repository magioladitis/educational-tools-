'use strict';
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),assert=require('node:assert/strict');
const root=path.join(__dirname,'..');
const php=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis-protovathmia.php'),'utf8');
const js=fs.readFileSync(path.join(root,'includes/transfer-primary.js'),'utf8');
assert(php.includes('id="primaryReturnBranch"'),'branch must be selected within EAE to General precheck');
assert(php.includes('foreach ($primaryTransferSpecialtyLabels as $code => $label)'),'use known Primary branch registry');
assert(php.includes('§1.5.1'),'official exclusion must be cited in the local guidance');
const from=js.indexOf('  function updateReturnFromEae(){');
const to=js.indexOf('  function updateEaeRoute(){',from);
assert(from>0&&to>from,'return evaluator must exist');
const evaluate=js.slice(from,to);
function test(branch,route,years,areaYears,organic,mode='region'){
  const fields={
    primaryMode:{value:mode},primaryReturnFromEae:{hidden:true},
    primaryReturnPath:{value:route},primaryReturnBranch:{value:branch},
    primaryReturnBranchField:{hidden:true},primaryReturnYearsField:{hidden:true},
    primaryReturnAreaYearsField:{hidden:true},primaryReturnYears:{value:String(years)},
    primaryReturnAreaYears:{value:String(areaYears)},primaryOrganic:{value:organic},
    primaryReturnStatus:{textContent:'',dataset:{status:'review'}}
  };
  vm.runInNewContext(evaluate+'\nupdateReturnFromEae();',{$:id=>fields[id]});
  return fields;
}
for(const branch of ['ΠΕ61','ΠΕ71']){
 for(const route of ['appointed','transferred']){
   let r=test(branch,route,30,20,'yes');
   assert.equal(r.primaryReturnStatus.dataset.status,'problem',branch+' / '+route+' must be blocked despite 30 years');
   assert.match(r.primaryReturnStatus.textContent,/Δεν προβλέπεται μετάθεση στη Γενική/);
   assert(r.primaryReturnYearsField.hidden,'blocked branch must not display irrelevant 5/2 input');
   assert(r.primaryReturnAreaYearsField.hidden,'blocked branch must not display irrelevant area input');
 }
}
let r=test('ΠΕ60','appointed',5,2,'unknown');
assert.equal(r.primaryReturnStatus.dataset.status,'positive','PE60 appointed with five years, two in area');
r=test('ΠΕ70','transferred',2,0,'yes');
assert.equal(r.primaryReturnStatus.dataset.status,'positive','PE70 transferred with two years and served organic');
r=test('ΠΕ70','appointed',4.99,2,'yes');
assert.equal(r.primaryReturnStatus.dataset.status,'problem','5-year threshold preserved');
r=test('','appointed',12,10,'yes');
assert.equal(r.primaryReturnStatus.dataset.status,'review','no positive eligibility without selecting branch');
r=test('ΠΕ71','none',30,20,'yes');
assert.equal(r.primaryReturnStatus.textContent,'','no eligibility messages if user did not request return');
r=test('ΠΕ61','appointed',30,20,'yes','eae');
assert(r.primaryReturnFromEae.hidden,'return panel only visible in General transfer');
assert.equal(r.primaryReturnStatus.textContent,'','non-general route does not show return warning');
assert(js.includes('if(id===\'primarySpecialty\')'),'shared email branch stays in sync with eligibility');
assert(js.includes('exclusive?\'Ο κλάδος \''),'blocked branch documented in checklist');
console.log('PASS PE61/PE71 blocked; PE60/PE70 remain eligible on thresholds; unknown requires branch; no effect on other modes');
