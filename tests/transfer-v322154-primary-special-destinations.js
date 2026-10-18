'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const source=fs.readFileSync(path.join(__dirname,'..','ypologismos-morion-metathesis-protovathmia.php'),'utf8');
const controller=fs.readFileSync(path.join(__dirname,'..','includes/transfer-primary.js'),'utf8');
const rules=require('../includes/transfer-primary-eae.js');
let count=0;
function t(cond,label){ assert.ok(cond,label); count++; }
const valid={origin:'general',generalYears:4,organic:'yes',qualifications:{masters:true}};
for(const mode of ['eeeek','deaf','blind']) t(controller.includes("['"+mode+"',") || controller.includes(",'"+mode+"'"), 'retains effective precheck '+mode);
t(source.includes('id="primaryDestination"')&&source.includes('value="sensory"'),'destinations nested under official type');
for(const field of ['primaryEaeCertification','primaryEaeCertificationDossier','primaryEaeCertificationField','primaryEaeCertificationDossierField'])t(source.includes('id="'+field+'"'),field);
for(const branch of ['ΠΕ70','ΠΕ71'])t(rules.permittedEeeek.includes(branch),'EEEΕK branch allowed '+branch);
for(const branch of ['ΠΕ60','ΠΕ61','ΠΕ06','ΠΕ11','ΠΕ86']){
 const r=rules.evaluate({...valid,mode:'eeeek',branch});
 t(r.status==='problem','EEEΕK rejects '+branch);
}
for(const branch of ['ΠΕ70','ΠΕ71']){
 const r=rules.evaluate({...valid,mode:'eeeek',branch});
 t(r.status!=='problem','EEEΕK accepts eligible '+branch+' for service review');
}
const pe71=rules.evaluate({mode:'eeeek',branch:'ΠΕ71',origin:'general',generalYears:4,organic:'yes',qualifications:{}});
t(pe71.status!=='problem'&&!pe71.checks.some(x=>x.includes('Δεν έχει δηλωθεί ακόμη προσόν')),'PE71 special qualification not forced');
const pe70empty=rules.evaluate({mode:'eeeek',branch:'ΠΕ70',origin:'appointed-eae',appointmentAreaYears:2,organic:'yes',qualifications:{}});
t(pe70empty.status==='review'&&pe70empty.checks.some(x=>x.includes('εξειδίκευση')),'PE70 EEEEK requires evidence');
const pe70five=rules.evaluate({mode:'eeeek',branch:'ΠΕ70',origin:'general',generalYears:4,organic:'yes',qualifications:{experience:true},eaeYears:4.5});
t(pe70five.status==='problem','PE70 insufficient experience only');
const pe70dual=rules.evaluate({mode:'eeeek',branch:'ΠΕ70',origin:'general',generalYears:4,organic:'yes',qualifications:{experience:true,masters:true},eaeYears:4.5});
t(pe70dual.status!=='problem','PE70 masters protects from incomplete second route');
for(const [mode,cert] of [['deaf','no'],['blind','no']]){
 const r=rules.evaluate({...valid,mode,branch:'ΠΕ70',certification:cert});
 t(r.status==='problem'&&r.problems.some(x=>x.includes('πιστοποιημένη')),'certification absent flagged '+mode);
}
for(const mode of ['deaf','blind']){
 const r=rules.evaluate({...valid,mode,branch:'ΠΕ70',certification:'unknown'});
 t(r.status==='review','unknown certification warns '+mode);
 const yes=rules.evaluate({...valid,mode,branch:'ΠΕ70',certification:'yes'});
 t(yes.status!=='problem','certification supported '+mode);
}
t(controller.includes("certificate==='yes' && certificateDossier==='new'") && controller.includes("mydocs('eae-certificate-"), 'certificate documents use mydocs only with new dossier');
t(controller.includes("deaf:'μετάθεση με εξειδίκευση στην ΕΝΓ'")&&controller.includes("blind:'μετάθεση με εξειδίκευση στη γραφή Braille'"),'special destination labels align with official application type');
t(!source.includes('Οι κωδικοί εμφανίζονται δίπλα στον τύπο') && source.includes('value="eae"') && controller.includes("['eeeek','ΕΕΕΕΚ']"),'obsolete explanatory copy removed in v3.22.168; prechecks remain');
t(!source.includes('includes/transfer-eligibility.js')&&!source.includes('includes/transfer-documents.js')&&!source.includes('includes/transfer-ui.js'),'secondary rules isolated');
console.log('PASS '+count+' primary special destination assertions');
