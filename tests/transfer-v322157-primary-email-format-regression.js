'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.join(__dirname,'..');
const php=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis-protovathmia.php'),'utf8');
const code=fs.readFileSync(path.join(root,'includes/transfer-primary.js'),'utf8');
const secondary=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
assert(php.includes('class="transfer-email-feature"'),'same feature container as secondary');
for(const x of ['transfer-submission-template__personalize','transfer-submission-template__item','transfer-submission-template__bar','transfer-submission-template__source','primaryCopySubject','primaryCopyBody','primaryPersonalize'])assert(php.includes(x),'same style element: '+x);
assert(!php.includes('id="primaryOfficialApplication"'),'no code repeated below dropdown');
assert(!code.includes('Επισυναπτόμενα (ελέγξτε'),'no undocumented inventory inside mandatory message body');
assert(!code.includes('Με εκτίμηση'),'no prose signature in official comma-separated template');
assert(code.includes("(email.general?'2.2.1':'2.2.5')"),'legal provision per route');
assert(secondary.includes('transfer-submission-template__item'),'Secondary styles remain shared');
// Exercise actual messageParts function rather than relying only on source snippets.
const start=code.indexOf('  function messageParts(docs) {');
const end=code.indexOf('  function updateMail(docs)',start);
assert(start>0&&end>start);
const fnSource=code.slice(start,end);
function verify(mode,expectedSubject,expectedBody){
  const field={primaryMode:mode,primaryName:'Δοκιμή Όνομα',primarySpecialty:'ΠΕ70',primaryRegistry:'345678',primaryProtocol:'1/123/001.ΠΕ001/01-11-2026',primaryDpe:'ΔΠΕ Κέρκυρας',primaryEaeBranch:'ΠΕ70'};
  const fields=Object.fromEntries(Object.entries(field).map(([k,v])=>[k,{value:v}]));
  const scope={};vm.createContext(scope);
  vm.runInContext('var $=id=>fields[id];var fields='+JSON.stringify(fields)+';'+fnSource+';result=messageParts([]);',scope);
  assert.equal(scope.result.subject,expectedSubject);
  assert.equal(scope.result.body,expectedBody);
}
verify('region','Μετάθεση από περιοχή σε περιοχή, 1/123/001.ΠΕ001/01-11-2026, 345678','Δοκιμή Όνομα, ΠΕ70, Μετάθεση από περιοχή σε περιοχή, 1/123/001.ΠΕ001/01-11-2026, 345678');
verify('eae','ΠΕ, μετάθεση σε ΣΜΕΑΕ–ΕΕΕΕΚ, 1/123/001.ΠΕ001/01-11-2026, 345678, ΠΕ70','Δοκιμή Όνομα, ΠΕ70, μετάθεση σε ΣΜΕΑΕ–ΕΕΕΕΚ, 1/123/001.ΠΕ001/01-11-2026, 345678, ΔΠΕ Κέρκυρας');
console.log('PASS strict Primary circular §2.2.1/§2.2.5 subject/body format, styled controls, codes inside dropdown');
