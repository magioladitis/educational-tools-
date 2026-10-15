'use strict';
const fs=require('fs');
const vm=require('vm');
const assert=require('node:assert/strict');
const path=require('path');
const code=fs.readFileSync(path.resolve(__dirname,'../includes/transfer-eligibility.js'),'utf8');
const window={};
vm.runInNewContext(code,{window}, {filename:'transfer-eligibility.js'});
const x=window.EducationTransferEligibility;
const cases=[
  ['eae basic missing route',()=>x.eae({qualification:'none',origin:'general',teachingYears:0}).advice.some(a=>a.includes('εξειδίκευσης'))],
  ['eae doctorate recognized route',()=>x.eae({qualification:'doctorate',origin:'general',teachingYears:4}).advice.length===0],
  ['eae masters allowed',()=>x.eae({qualification:'masters',origin:'general',teachingYears:3}).advice.length===0],
  ['eae two years route',()=>x.eae({qualification:'two_year',origin:'general',teachingYears:3}).advice.length===0],
  ['eae five-year service route fails at 4.9',()=>x.eae({qualification:'experience',origin:'general',teachingYears:7,eaeYears:4.9}).advice.some(a=>a.includes('πέντε'))],
  ['eae five-year service route passes at 5',()=>x.eae({qualification:'experience',origin:'general',teachingYears:7,eaeYears:5}).advice.length===0],
  ['eae PE11 limited to PE11',()=>x.eae({qualification:'pe11',origin:'general',teachingYears:4,branch:'other'}).advice.some(a=>a.includes('ΠΕ11'))],
  ['eae PE11 allowed with PE11 branch',()=>x.eae({qualification:'pe11',origin:'general',teachingYears:4,branch:'pe11'}).advice.length===0],
  ['eae newly region tenure guard',()=>x.eae({qualification:'masters',origin:'general',teachingYears:3,newly:true,areaYears:1.9}).advice.some(a=>a.includes('δύο έτη'))],
  ['eae already-EAE not 3y general rule',()=>!x.eae({qualification:'masters',origin:'eae',teachingYears:0}).advice.some(a=>a.includes('τριετής'))],
  ['eae deaf ENΓ requirement',()=>x.eae({qualification:'masters',origin:'general',teachingYears:3,destination:'deaf'}).advice.some(a=>a.includes('Νοηματικής'))],
  ['eae blind Braille requirement',()=>x.eae({qualification:'masters',origin:'general',teachingYears:3,destination:'blind'}).advice.some(a=>a.includes('Braille'))],
  ['music general 5y and organic met',()=>x.music({educationType:'general',teachingYears:5,organic:true}).advice.length===0],
  ['music general 4y requires 5y',()=>x.music({educationType:'general',teachingYears:4,organic:true}).advice.some(a=>a.includes('πενταετής'))],
  ['music missing organic warning',()=>x.music({educationType:'general',teachingYears:5,organic:false}).advice.some(a=>a.includes('οργανικής'))],
  ['music direct exception only specialized',()=>x.music({educationType:'general',exceptionType:'direct_music',organic:true}).advice.some(a=>a.includes('μουσικής παιδείας'))],
  ['music direct exception specialized accepted pending verification',()=>!x.music({educationType:'specialized',exceptionType:'direct_music',subject:'instrument',organic:true}).advice.some(a=>a.includes('πενταετής'))],
  ['music transferred exception regardless teaching',()=>!x.music({educationType:'general',exceptionType:'primary_transfer',organic:true,teachingYears:0}).advice.some(a=>a.includes('πενταετής'))],
  ['music subject theory proper title',()=>x.music({educationType:'specialized',teachingYears:5,organic:true,subject:'theory'}).subjectInfo.document.includes('αντίστιξης')],
  ['music subject instruments proper title',()=>x.music({educationType:'specialized',teachingYears:5,organic:true,subject:'instrument'}).subjectInfo.document.includes('οργάνου')],
  ['music non-specialized no mandatory subject',()=>!x.music({educationType:'general',teachingYears:5,organic:true}).advice.some(a=>a.includes('αντικείμενο'))]
];
for(const [name,fn] of cases){assert.ok(fn(),name);}
console.log('PASS '+cases.length+' transfer eligibility cases');
