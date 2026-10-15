'use strict';
const assert = require('node:assert/strict');
const t = require('../includes/transfer-calculations.js');
let checks=0;
const ok=(cond,msg)=>{assert.ok(cond,msg);checks++;};
const eq=(actual,expected,msg)=>{assert.equal(actual,expected,msg);checks++;};
const input={serviceYears:4,serviceMonths:0,msdPeriods:[{type:'school',category:'I',years:2,daysPerWeek:5}],familyStatusEligible:false};
let r=t.calculate(input);
eq(r.servicePoints,10,'legacy service');
eq(r.msdPoints,20,'legacy M.S.D. from 2y I');
eq(r.total,30,'legacy total');
eq(r.msdInputValid,true,'default valid');
for(const [raw,num] of [['48,75',48.75],['48.75',48.75],['0',0],['0,01',0.01],['123',123],[' 3,50 ',3.5]]) {
  const v=t.parseDirectMsd(raw);ok(v.valid,'valid '+raw);eq(v.points,num,raw);
  r=t.calculate({...input,msdMode:'direct',msdDirectPoints:raw});
  eq(r.msdPoints,num,'direct '+raw);
  eq(r.total,10+num,'total only direct '+raw);
  eq(r.msdRows.length,0,'periods ignored '+raw);
}
for(const raw of ['', 'abc','-1','-0.01','1,234','1.234','1.2.3','1,2.3','Infinity', '1e2','  ']) {
 const parsed=t.parseDirectMsd(raw);eq(parsed.valid,false,'invalid '+raw);
 r=t.calculate({...input,msdMode:'direct',msdDirectPoints:raw});
 eq(r.msdPoints,null,'invalid not zero '+raw);
 eq(r.total,null,'invalid total unknown '+raw);
 eq(r.msdInputValid,false,'invalid mark '+raw);
}
r=t.calculate({...input,msdMode:'periods',msdDirectPoints:'500'});
eq(r.msdPoints,20,'unused direct does not contaminate periods');
eq(r.total,30,'periods still alone');
console.log('PASS',checks,'M.S.D. direct vs periods regression assertions');
