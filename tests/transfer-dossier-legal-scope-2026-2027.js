'use strict';
const fs=require('fs'),vm=require('vm'),assert=require('node:assert/strict'),path=require('path');
const window={};vm.runInNewContext(fs.readFileSync(path.join(__dirname,'../includes/transfer-eligibility.js'),'utf8'),{window});
const e=window.EducationTransferEligibility;
let checks=0;
function check(ok,label){assert.ok(ok,label);checks++;}
for(const kind of ['art','music','intercultural']){
 for(const year of ['2021','2022','2023','2024','2025','2026']){
  check(e.dossier({kind,year}).skipResubmission,kind+' accepted '+year);
  check(!e.dossier({kind,year,newElements:true}).skipResubmission,kind+' new docs '+year);
 }
 check(!e.dossier({kind,year:'2020'}).skipResubmission,kind+' 2020 excluded');
}
for(const year of ['2025','2026'])check(e.dossier({kind:'eae',year}).skipResubmission,'EAE '+year);
for(const year of ['2021','2022','2023','2024'])check(!e.dossier({kind:'eae',year}).skipResubmission,'EAE invalid '+year);
for(const kind of ['', 'general', 'unknown'])check(!e.dossier({kind,year:'2026'}).skipResubmission,'unsupported kind '+kind);
check(e.dossier({kind:'eae',year:'2025'}).limitation.includes('όχι την ετήσια αίτηση'),'new annual application remains necessary');
const docs=fs.readFileSync(path.join(__dirname,'../includes/transfer-documents.js'),'utf8');
check(docs.includes('σε μία αποστολή'),'single submission instruction');
console.log('PASS '+checks+' legal dossier scope assertions');
