'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.join(__dirname,'..');
const calc=require('../includes/transfer-calculations.js');
const ui=fs.readFileSync(path.join(root,'includes/transfer-ui.js'),'utf8');
const html=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis.php'),'utf8');
const docsCode=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
let total=0;
function eq(actual,expected,msg){assert.equal(actual,expected,msg);total++;}
function ok(cond,msg){assert.ok(cond,msg);total++;}
const service=[{years:'2',months:'6',days:'10'},{years:'1',months:'7',days:'8'}];
let result=calc.calculate({serviceMode:'periods',servicePeriods:service,serviceYears:35,msdMode:'direct',msdDirectPoints:'10'});
eq(result.serviceInputValid,true,'two non-overlapping periods valid');
eq(result.serviceDuration.years,4,'years combined');
eq(result.serviceDuration.months,2,'15 cumulative days rounded to month');
eq(result.servicePoints,calc.servicePoints(4,2,0).points,'service points computed from periods');
eq(result.total,calc.truncate2(result.servicePoints+10),'direct service must not be double counted');
result=calc.calculate({serviceMode:'direct',serviceYears:5,serviceMonths:2,serviceDays:15,servicePeriods:service,msdMode:'direct',msdDirectPoints:'0'});
eq(result.serviceDuration.years,5,'direct overrides periods');
eq(result.serviceDuration.months,3,'direct rounds 15 days');

result=calc.calculate({serviceMode:'points',serviceDirectPoints:'21,04',serviceYears:40,serviceMonths:0,serviceDays:0,msdMode:'direct',msdDirectPoints:'0'});
eq(result.serviceInputValid,true,'direct service points valid');
eq(result.serviceDurationKnown,false,'direct service points do not infer duration');
eq(result.servicePoints,21.04,'direct service points preserved');
for (const input of [[{years:49,months:11,days:0},{years:1,months:1,days:0}],[{years:1,months:'',days:0}],[{years:1.2,months:0,days:0}]]){
 const r=calc.calculate({serviceMode:'periods',servicePeriods:input,msdMode:'direct',msdDirectPoints:'0'});
 eq(r.serviceInputValid,false,'invalid period refused'); eq(r.total,null,'invalid period suppresses total');
}
let r=calc.calculate({serviceMode:'periods',servicePeriods:[{years:2,months:0,days:0}],msdMode:'periods',msdPeriods:[{type:'school',category:'D',years:2,months:0,days:0}]});
eq(r.servicePoints,5,'service direct period score');eq(r.msdPoints,8,'MSD independent period score');eq(r.total,13,'independent total');
ok(html.includes('<option value="points" selected>Απευθείας καταχώριση μορίων προϋπηρεσίας'),'direct service-points initial state');
ok(html.includes('id="servicePointsDirectPanel"'),'service direct-points panel exists');
ok(html.includes('id="servicePeriodsPanel" style="display:none"'),'period panel hidden by default');
ok(html.includes('id="transferSpecialCase"'),'conditional health route selector');
ok(html.includes('ΨΚΩΖ46ΝΚΠΔ-63Κ'),'ADA matches official PDF');
ok(html.includes('31/12/2026, ώρα 15:00'),'withdrawal deadline');
ok(!html.includes('ΨΚΩΦ46ΝΚΠΔ-63Κ'),'old incorrect ADA removed');
ok(ui.includes("var needsReview=choice==='unspecified' || choice==='other'"),'pending spouse no points');
ok(ui.includes('serviceRows.innerHTML=\'\''),'reset resets period rows');
ok(docsCode.includes('ΚΛΕΙΣΤΟ φάκελο'),'health sealed hardcopy');
ok(docsCode.includes('γνωμάτευση ΚΕΠΑ ΔΕΝ αποστέλλεται'),'KEPA transmitted as metadata only');
ok(docsCode.includes('περιφέρεια αρμοδιότητας'),'judicial spouse document package');
console.log('PASS',total,'new 3.22.139 service-audit regression checks');
