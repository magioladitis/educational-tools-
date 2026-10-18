'use strict';
// Regression contract for a common SOCIAL VIEW, not merged circular/evidence rules.
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const crypto=require('node:crypto');
const {execFileSync}=require('node:child_process');
const root=path.resolve(__dirname,'..');
function read(p){return fs.readFileSync(path.join(root,p),'utf8');}
function render(page){return execFileSync('php',[path.join(root,page)],{encoding:'utf8'});}
function normalizedSecondarySection(html){
  const start=html.indexOf('<div id="transferSocialCriteriaCard">');
  const end=html.indexOf('<div class="actions edu-actions"',start);
  assert.ok(start!==-1 && end>start,'social section is present before form actions');
  return html.slice(start,end).replace(/>\s+</g,'><').replace(/\s+/g,' ').trim();
}
let checks=0;function check(value,name){assert.ok(value,name);checks++;}
const primary=render('ypologismos-morion-metathesis-protovathmia.php');
const secondary=render('ypologismos-morion-metathesis.php');
const social=read('includes/components/transfer-social-criteria.php');
check(read('ypologismos-morion-metathesis.php').includes("transferSocialCriteriaCard('secondary')"),'Secondary uses common social view');
check(read('ypologismos-morion-metathesis-protovathmia.php').includes("transferSocialCriteriaCard('primary'"),'Primary uses common social view');
const stableSha='e0bdd296d17cb66fc169dca55c15d04026e6fc0404f34ebf9b94fc4146778579';
check(crypto.createHash('sha256').update(normalizedSecondarySection(secondary)).digest('hex')===stableSha,'Secondary social markup unchanged from 3.22.160');
for(const [id,label] of Object.entries({primaryChildren:'Τέκνα που μοριοδοτούνται',primaryFamily:'Γάμος/Σύμφωνο',primaryCoservice:'Συνυπηρέτηση',primaryCoServiceCase:'Καθεστώς εργασίας',primarySpecialCase:'Χρειάζομαι οδηγίες',primaryLocality:'Εντοπιότητα',primaryFirst:'πρώτη προτίμησή'})){
  check(primary.includes('id="'+id+'"') && primary.includes(label),'Primary includes '+id);
  check(primary.split('id="'+id+'"').length===2,'Primary '+id+' unique');
}
check(primary.includes('primaryKedasyGeoNote'),'KEDASY municipality note preserved');
check(!primary.includes('id="transferCoServiceCase"'),'No Secondary input IDs in Primary');
check(secondary.includes('id="transferCoServiceCase"') && !secondary.includes('id="primaryCoServiceCase"'),'Secondary keeps original IDs');
check(social.includes('1ο–2ο: 4') && social.includes('3ο: 6') && social.includes('από το 4ο: 7'),'Children rule shared');
const js=read('includes/transfer-primary.js');
check(js.includes("coService:$('primaryCoservice').checked && !invalidSpouse"),'Unverified spouse excluded from scoring');
check(js.includes('if(blocked)check.checked=false'),'Ineligible spouse checkbox reset');
check(js.includes('municipalDisposal'),'KEDASY disposal handled at municipality level');
check(js.includes("$('primarySocialCriteriaCard').hidden=interculturalMode"),'No social scoring in Intercultural');
console.log('PASS',checks,'shared-social render, statutory scoring safeguards and Secondary baseline assertions');
