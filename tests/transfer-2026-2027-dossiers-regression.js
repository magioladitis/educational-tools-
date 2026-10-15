const fs=require('fs'),vm=require('vm'),assert=require('assert'),path=require('path');
const source=fs.readFileSync(path.join(__dirname,'../includes/transfer-eligibility.js'),'utf8');
let w={};vm.runInNewContext(source,{window:w});
const e=w.EducationTransferEligibility;
assert.equal(e.BASELINE_CIRCULAR.schoolYear,'2026–2027');
assert.equal(e.BASELINE_CIRCULAR.reference,'138014/Ε2/15-10-2026');
assert.equal(e.dossier({kind:'eae',year:'2025'}).skipResubmission,true);
assert.equal(e.dossier({kind:'eae',year:'2026'}).skipResubmission,true);
assert.equal(e.dossier({kind:'eae',year:'2024'}).skipResubmission,false);
assert.equal(e.dossier({kind:'eae',year:'2026',newElements:true}).skipResubmission,false);
for(let year=2021;year<=2026;year++){
 assert.equal(e.dossier({kind:'music',year:String(year)}).skipResubmission,true);
 assert.equal(e.dossier({kind:'art',year:String(year)}).skipResubmission,true);
}
assert.equal(e.dossier({kind:'art',year:'2020'}).skipResubmission,false);
const page=fs.readFileSync(path.join(__dirname,'../ypologismos-morion-metathesis.php'),'utf8');
assert(page.includes('value="2026">Ναι, το 2026'));
assert(page.includes('31/08/2027'));
assert(page.includes('138014/Ε2/15-10-2026'));
const docs=fs.readFileSync(path.join(__dirname,'../includes/transfer-documents.js'),'utf8');
assert(docs.includes('02/11/2026'));
assert(docs.includes('ΔΔΕ οργανικής'));
console.log('PASS: updated 2026-2027 retained dossiers, service horizon and submission routes');
