'use strict';
const fs=require('node:fs'),path=require('node:path'),assert=require('node:assert/strict');
const root=path.join(__dirname,'..');
const php=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis-protovathmia.php'),'utf8');
const js=fs.readFileSync(path.join(root,'includes/transfer-primary.js'),'utf8');
for(const option of ['value="placement-general" data-official-code="003.ΠΕΒ01"','value="placement-eae" data-official-code="003.ΠΕΒ01.ΕΙΔ"',
  'id="primaryReturnPath"','id="primaryReturnYears"','id="primaryReturnAreaYears"','id="primaryReturnStatus"','id="primaryPlacementNotice"'])
  assert(php.includes(option),'Missing placement/return control: '+option);
assert(js.includes('function updateReturnFromEae()'),'return evaluation must be independent of scoring');
assert(js.includes("y<2?"),'return transferred EAE requires >=2');
assert(js.includes("y<5||a<2?"),'appointed EAE requires 5 + 2');
assert(js.includes("organic!=='yes'?"),'transferred needs organic service');
assert(js.includes("if(interculturalMode||isPlacement())$('primaryFirst').checked=false"),'no first-preference area points on within-area placement');
assert(js.includes("'placement-general':'Βελτίωση / Οριστική τοποθέτηση Γενικής Εκπαίδευσης'"),'new type in message template');
assert(js.includes("'placement-eae':'Βελτίωση / Οριστική τοποθέτηση Ειδικής Αγωγής'"),'new EAE type in message template');
assert(js.includes("if(mode!=='region' && !isPlacement())"),'placement must not enter EAE qualifications flow');
assert(!php.includes('value="mutual"'),'do not add Primary mutual transfer');
console.log('PASS Primary placement and EAE return controls isolated; no mutual transfer');
