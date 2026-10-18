'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.join(__dirname,'..');
const php=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis-protovathmia.php'),'utf8');
const code=fs.readFileSync(path.join(root,'includes/transfer-primary.js'),'utf8');
const inter=require('../includes/transfer-primary-intercultural.js');
let n=0;function test(condition,label){assert.ok(condition,label);n++;}
function run(data){return inter.evaluate(Object.assign({organic:'yes',current:'no',inclusion:'no',qualifications:['greek-second']},data));}
test(php.includes('value="intercultural" data-official-code="002.ΔΙΑΠ001"'),'official 002 code in dropdown');
test(php.includes('id="primaryInterculturalFields"'),'independent inter section');
test(php.includes('id="primaryScoringCard"')&&code.includes("$('primaryScoringCard').hidden=interculturalMode"),'intercultural no points interface');
test(code.includes("'intercultural'?[]")&&code.includes("mode!=='intercultural' && $('primaryCoservice')"),'no scoring documents');
test(!php.includes('includes/transfer-documents.js')&&!php.includes('includes/transfer-eligibility.js'),'no Secondary rules');
test(php.includes('Δεν προσκομίζεται βιογραφικό σημείωμα'),'no CV explicitly');
test(code.includes('isIntercultural')&&code.includes("$('primaryMailAction').hidden=!hasActions || isIntercultural"),'do not email inter documents');
test(code.includes("intercultural:'μετάθεση σε Διαπολιτισμικό Σχολείο'"),'official template label');
test(run({}).status==='review','positive prerequisites still subject to services');
test(run({organic:'no'}).status==='problem','missing organic year issue');
test(run({current:'yes',currentYears:'1.99'}).status==='problem','served at intercultural <2 not eligible');
test(run({current:'yes',currentYears:'2'}).status==='review','served two years');
test(run({inclusion:'yes',eaeQualification:'no'}).status==='problem','TE not meeting EAE criteria');
test(run({inclusion:'yes',eaeQualification:'yes'}).status==='review','TE EAE credentials need review');
test(run({inclusion:'unknown'}).checks.some(x=>x.includes('Τμήμα Ένταξης')),'unverified destination flagged');
test(inter.years('51')===null&&inter.years('-2')===null&&inter.years('50')===50,'50 year cap');
test(Object.keys(inter.qualificationNames).length===9,'all nine listed criteria');
const begin=code.indexOf('  function messageParts(docs) {'),end=code.indexOf('  function updateMail(docs)',begin);
const fields={primaryMode:'intercultural',primaryName:'Δοκιμή Όνομα',primarySpecialty:'ΠΕ70',primaryRegistry:'345678',primaryProtocol:'1/123/002.ΔΙΑΠ001/01-11-2026',primaryDpe:'ΔΠΕ Κέρκυρας',primaryEaeBranch:'ΠΕ70'};
const fieldsObj=Object.fromEntries(Object.entries(fields).map(([id,value])=>[id,{value}]));
const src='var fields='+JSON.stringify(fieldsObj)+';var $=id=>fields[id];'+code.slice(begin,end)+';result=messageParts([]);'
const context={};vm.createContext(context);vm.runInContext(src,context);
assert.equal(context.result.subject,'ΠΕ, μετάθεση σε Διαπολιτισμικό Σχολείο, 1/123/002.ΔΙΑΠ001/01-11-2026, 345678, ΠΕ70');
assert.equal(context.result.body,'Δοκιμή Όνομα, ΠΕ70, μετάθεση σε Διαπολιτισμικό Σχολείο, 1/123/002.ΔΙΑΠ001/01-11-2026, 345678, ΔΠΕ Κέρκυρας');
n+=2;
console.log('PASS '+n+' primary intercultural legal, isolation, scoring, and document guards');
