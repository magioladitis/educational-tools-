'use strict';
const fs=require('fs'),vm=require('vm'),assert=require('node:assert/strict'),path=require('path');
const source=fs.readFileSync(path.join(__dirname,'../includes/transfer-eligibility.js'),'utf8');
const window={};vm.runInNewContext(source,{window});const rules=window.EducationTransferEligibility;
const tests=[
 ['EAE 2024 prior filed skip',()=>rules.dossier({kind:'eae',year:'2024'}).skipResubmission],
 ['EAE 2025 prior filed skip',()=>rules.dossier({kind:'eae',year:'2025'}).skipResubmission],
 ['EAE 2023 does NOT skip',()=>!rules.dossier({kind:'eae',year:'2023'}).skipResubmission],
 ['EAE prior with new docs does NOT skip',()=>!rules.dossier({kind:'eae',year:'2025',newElements:true}).skipResubmission],
 ['EAE prior with new docs still needs new proof',()=>rules.dossier({kind:'eae',year:'2025',newElements:true}).needsNewDocuments],
 ...['2021','2022','2023','2024','2025'].map(yr=>['Art dossier retained '+yr,()=>rules.dossier({kind:'art',year:yr}).skipResubmission]),
 ['Art 2020 not retained',()=>!rules.dossier({kind:'art',year:'2020'}).skipResubmission],
 ['Art with new evidence not skipped',()=>!rules.dossier({kind:'art',year:'2024',newElements:true}).skipResubmission],
 ['Music uses arts prior years',()=>rules.dossier({kind:'music',year:'2021'}).skipResubmission],
 ['Art general branch no expert committee',()=>!rules.art({branch:'general',organic:true,teachingYears:5}).specialized],
 ['Art PE08 expert',()=>rules.art({branch:'pe08',organic:true,teachingYears:5}).specialized],
 ['Art PE11 expert',()=>rules.art({branch:'pe11',organic:true,teachingYears:5}).specialized],
 ['Art PE79 expert',()=>rules.art({branch:'pe79',organic:true,teachingYears:5}).specialized],
 ['Art PE91.01 expert',()=>rules.art({branch:'pe9101',organic:true,teachingYears:5}).specialized],
 ['Art PE91.02 expert',()=>rules.art({branch:'pe9102',organic:true,teachingYears:5}).specialized],
 ['Art 4.9 yrs requires 5 years',()=>rules.art({branch:'general',organic:true,teachingYears:4.9}).advice.some(s=>s.includes('5 έτη'))],
 ['Art 5 yrs pass',()=>rules.art({branch:'general',organic:true,teachingYears:5}).advice.length===0],
 ['Art no organic warns',()=>rules.art({branch:'general',organic:false,teachingYears:5}).advice.some(s=>s.includes('οργανικής'))],
 ['Art direct music exception only PE79',()=>rules.art({branch:'pe08',organic:true,teachingYears:0,exceptionType:'direct_music'}).advice.some(s=>s.includes('αποκλειστικά'))],
 ['Art direct music exception for PE79 not falsely rejected',()=>!rules.art({branch:'pe79',organic:true,teachingYears:0,exceptionType:'direct_music',orientation:'other'}).advice.some(s=>s.includes('αποκλειστικά'))],
 ['Art primary transfer exception acknowledged',()=>!rules.art({branch:'general',organic:true,teachingYears:0,exceptionType:'primary_transfer'}).advice.some(s=>s.includes('5 έτη'))],
 ['Art special with no subject warns',()=>rules.art({branch:'pe08',organic:true,teachingYears:5}).advice.some(s=>s.includes('κατεύθυνση'))],
 ['Circular year clearly named',()=>rules.BASELINE_CIRCULAR.schoolYear==='2025–2026']
];
for(const [name,fn] of tests)assert.ok(fn(),name);
console.log('PASS '+tests.length+' art/dossier regression cases');
