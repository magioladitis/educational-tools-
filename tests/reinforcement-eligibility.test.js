'use strict';
const assert=require('node:assert/strict');
const {evaluate:e,period}=require('../includes/reinforcement-eligibility.js');
const ready={program:'gym',branch:'PE03',board:'2GE',sanction:'no',ageEligible:'yes',citizenshipEligible:'yes',dutiesEligible:'yes',applicationImpediment:'no',military:'yes',service:'clear'};
let n=0;
function check(data,status){assert.equal(e({...ready,...data}).status,status,JSON.stringify(data));n++;}
check({},'basic_pass');
check({program:''},'review');check({branch:''},'review');check({board:''},'review');
check({board:'unknown'},'review');check({board:'none'},'not_met');
check({sanction:'yes'},'not_met');check({sanction:'unknown'},'review');
for(const key of ['ageEligible','citizenshipEligible','dutiesEligible']){check({[key]:'no'},'not_met');check({[key]:''},'review');check({[key]:'unknown'},'review');}
check({applicationImpediment:'yes'},'not_met');check({applicationImpediment:''},'review');check({applicationImpediment:'unknown'},'review');
// The removed broad answer must not satisfy missing specific prerequisites.
check({ageEligible:'',citizenshipEligible:'',dutiesEligible:'',applicationImpediment:'',general:'yes'},'review');
check({military:'na'},'basic_pass');check({military:'no'},'review');
check({service:'issue'},'basic_pass');assert(e({...ready,service:'issue'}).notes.some(x=>x.includes('κώλυμα')));n++;
for(const branch of ['PE02','PE03','PE04.01','PE04.02','PE06']){
 check({branch},'basic_pass');check({branch,board:'3EA-main'},'not_met');
}
check({branch:'PE85',chemical:'yes'},'basic_pass');
check({branch:'PE85',chemical:'no'},'not_met');check({branch:'PE85'},'review');
for(const branch of ['PE05','PE04.03','PE79.01','TE16','other'])check({branch},'not_met');
const matches={PE05:['french'],PE07:['german'],PE34:['italian'],PE40:['spanish'],'PE79.01':['music'],PE08:['free'],'PE89.01':['free','linear'],PE81:['linear']};
for(const [branch,subjects] of Object.entries(matches))for(const subject of subjects){
 for(const board of [branch==='PE79.01'?'1GE':'2GE','3EA-main','3EA-aux'])check({program:'edem',branch,subject,board},'basic_pass');
 check({program:'edem',branch,subject:'invalid'},'not_met');
 check({program:'edem',branch,subject,board:'1GT'},'not_met');
}
check({program:'edem',branch:'PE79.01',subject:'music',board:'2GE'},'not_met');
check({program:'edem',branch:'PE05',subject:'french',board:'1GE'},'not_met');
check({program:'edem',branch:'PE89.01',subject:'music'},'not_met');
for(const board of ['1GT','4EA-main','4EA-aux']){
 const d={program:'edem',branch:'TE16',subject:'music',board,musicSpecialty:'yes'};
 check(d,'basic_pass');assert(e({...ready,...d}).notes.some(x=>x.includes('εξάντληση')));n++;
 check({...d,musicSpecialty:'no'},'not_met');check({...d,musicSpecialty:''},'review');
}
check({program:'edem',branch:'TE16',subject:'music',board:'3EA-main',musicSpecialty:'yes'},'not_met');
check({program:'edem',branch:'PE79.01',subject:'',board:'1GE'},'review');
// Stale values from hidden fields do not affect a different programme/branch.
check({chemical:'no',musicSpecialty:'no',subject:'music'},'basic_pass');
check({program:'edem',branch:'PE05',subject:'french',chemical:'no',musicSpecialty:'no'},'basic_pass');
const start='2026-11-09T00:00:00+02:00',end='2026-11-14T00:00:00+02:00';
for(const [date,want] of [['2026-11-08T23:59:59+02:00','before'],[start,'open'],['2026-11-13T23:59:59+02:00','open'],[end,'closed']]){
 assert.equal(period(start,end,Date.parse(date)),want);n++;
}
assert.equal(period('','',Date.now()),'unknown');n++;
assert.equal(period(start,start,Date.now()),'unknown');n++;
// Eligibility is deliberately independent of the application window.
check({},'basic_pass');
console.log(`PASS: ${n} reinforcement eligibility, routing and deadline checks`);
