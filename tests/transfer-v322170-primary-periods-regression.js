/* Regression: Primary partial scoring and detailed service/MSD must not fork numeric rules. */
'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path');
const dir=path.join(__dirname,'..');const read=(f)=>fs.readFileSync(path.join(dir,f),'utf8');
const scoring=require('../includes/transfer-calculations.js');
const php=read('ypologismos-morion-metathesis-protovathmia.php');
const js=read('includes/transfer-primary.js');const ui=read('includes/transfer-primary-periods.js');
for(const k of ['primaryServicePeriodsRows','primaryMsdPeriodsRows','primaryServiceMode','primaryMsdMode','primaryScoreMobile','primaryInterculturalScoreNote']){
 assert.ok(php.includes('id="'+k+'"'),k+' exists');
}
assert.ok(php.includes('transfer-primary-periods.js'));
assert.ok(js.includes('γνωστές μονάδες (μερικό άθροισμα)'));
assert.ok(js.includes('serviceValid?f(result.servicePoints)'));
assert.ok(js.includes('msdValid?f(result.msdPoints)'));
assert.ok(js.includes("$('primaryScoreResults').parentElement.hidden=interculturalMode"));
assert.ok(ui.includes('readService:readService,readMsd:readMsd,sync:sync,bind:bind'));
assert.ok(ui.includes('concurrent')||ui.includes('Concurrent'));
const combined=scoring.calculate({mode:'region',serviceMode:'periods',servicePeriods:[{years:8,months:0,days:0},{years:0,months:6,days:0}],
 msdMode:'periods',msdPeriods:[{type:'school',category:'IB',years:2,months:0,days:0,remoteDouble:true,daysPerWeek:5},
 {type:'digital_tutoring',category:'A',years:1,months:0,days:0,daysPerWeek:5}],familyStatusEligible:true,eligibleChildren:2});
assert.equal(combined.servicePoints,21.25);assert.equal(combined.msdPoints,55);assert.equal(combined.familyPoints,12);assert.equal(combined.total,88.25);
assert.equal(scoring.calculate({serviceMode:'points',serviceDirectPoints:'',msdMode:'direct',msdDirectPoints:'30'}).serviceInputValid,false);
assert.equal(scoring.calculate({serviceMode:'points',serviceDirectPoints:'20',msdMode:'direct',msdDirectPoints:''}).msdInputValid,false);
const duration=scoring.serviceFromPeriods([{years:2,months:6,days:10},{years:1,months:7,days:8}]);
assert.equal(duration.duration.totalMonths,duration.totalMonths,'one rounding only');
assert.equal(duration.totalMonths,50);
console.log('PASS Primary 3.22.170 partial results, score periods, no double rounding');
