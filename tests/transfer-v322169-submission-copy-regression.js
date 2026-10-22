'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
const root=path.join(__dirname,'..');
const read=p=>fs.readFileSync(path.join(root,p),'utf8');
const primary=read('includes/transfer-primary.js');
const secondary=read('includes/transfer-documents.js');
const shared=read('includes/transfer-email-component.js');
const primaryPhp=read('ypologismos-morion-metathesis-protovathmia.php');
const secondaryPhp=read('ypologismos-morion-metathesis.php');
let checks=0;
function t(condition,msg){assert.ok(condition,msg);checks++;}
for(const s of [shared,primaryPhp,secondaryPhp])t(s.includes('Προετοιμασία μηνύματος υποβολής δικαιολογητικών'),'neutral heading appears in expected source');
t(!shared.includes('Έτοιμο email υποβολής δικαιολογητικών'),'old misleading common title removed');
t(!secondary.includes("title:submissionUiText('emailTitle','Έτοιμο email"),'secondary fallback updated');
t(secondary.includes('Ελέγξτε ότι η αναλυτική περιγραφή του τύπου'),'secondary EAE labels verified against the submitted type');
t(secondary.includes("['smeae','eae_sensory','kedasy'].indexOf(tpl.mode)"),'EAE-only caveat');
t(!secondary.includes('Μέρος 1ο'),'no segmented-mail UI introduced');

const begin=primary.indexOf('  function messageParts(docs) {');
const end=primary.indexOf('  function legacyCopy(',begin);
t(begin>0&&end>begin,'primary functional extraction boundaries');
const fnSrc=primary.slice(begin,end);
function testPrimary(mode,docs){
  const initial={primaryMode:mode,primaryName:'Όνομα Δοκιμή',primarySpecialty:'ΠΕ70',primaryRegistry:'654321',primaryProtocol:'1/2/3',primaryDpe:'ΔΠΕ Κέρκυρας',primaryEaeBranch:'ΠΕ70'};
  const elements={};
  Object.entries(initial).forEach(([key,value])=>elements[key]={value,hidden:false,textContent:'',innerHTML:''});
  for(const key of ['primaryMydocsLink','primaryMydocsNotice','primaryEmailHeading','primaryEmailLead','primaryEmailOpen','primarySubmissionInstruction','primaryTemplateNote','primaryEmailSource','primaryDpeField','primaryEmailSubject','primaryEmailBody','primaryMailAction','primaryOpenMail']){
    elements[key]={hidden:false,textContent:'',innerHTML:'',href:''};
  }
  const sandbox={fields:elements,docs,global:{EducationTransferEmailComponent:{stateCopy(channel){return {title:{email:'Προετοιμασία μηνύματος υποβολής δικαιολογητικών',mydocs:'Έτοιμο υπόδειγμα υποβολής μέσω mydocs',mixed:'Προετοιμασία email και υποβολής μέσω mydocs',optional:'Υπόδειγμα μηνύματος — αν χρειαστεί'}[channel],button:channel};},mailto(tpl){return 'mailto:?subject='+encodeURIComponent(tpl.subject)+'&body='+encodeURIComponent(tpl.body);}}}};
  vm.runInNewContext("var $ = id => fields[id];function primaryEffectiveMode(){return fields.primaryMode.value;}"+fnSrc+";updateMail(docs);",sandbox);
  return elements;
}
const judicial={id:'judicial-spouse',action:true,channel:'mydocs'};
const coservice={id:'coservice',action:true,channel:'email'};
const eaeDoc={id:'eae-masters',action:true,channel:'mydocs'};
let el=testPrimary('region',[judicial]);
t(el.primaryMydocsLink.hidden===false,'judicial link to mydocs visible even when primary mode is general');
t(el.primaryMydocsLink.innerHTML.includes('συζύγων δικαστικών'),'judicial link is accurately labeled');
t(el.primaryMydocsNotice.textContent.includes('πιστοποιητικό οικογενειακής κατάστασης'),'judicial certificate included in mydocs');
t(el.primaryEmailHeading.textContent.includes('σύζυγοι δικαστικών'),'judicial-specific heading');
t(el.primaryEmailLead.textContent.includes('χωρίς να ορίζεται υποχρεωτικό θέμα'),'judicial no mandatory subject/body');
t(el.primaryTemplateNote.textContent.includes('δεν αποτελεί υποχρεωτική μορφή'),'generic template clearly optional for judicial mydocs');
t(el.primaryEmailSource.textContent.includes('§2.2.3')&&el.primaryEmailSource.textContent.includes('§2.2.1'),'cites both distinct legal paths');
t(el.primaryMailAction.hidden===true,'mailto suppressed for mydocs-only special workflow');
t(el.primaryEmailOpen.textContent.includes('οδηγιών υποβολής'),'no suggestion that mandatory mydocs email content exists');
t(el.primaryEmailSubject.textContent.startsWith('Μετάθεση από περιοχή σε περιοχή,'),'existing general subject preserved');
el=testPrimary('region',[judicial,coservice]);
t(el.primaryEmailHeading.textContent.includes('mydocs'),'mixed pathway recognized');
t(el.primaryMailAction.hidden===false,'real DPE email remains available when accompanied by special mydocs');
t(el.primaryEmailLead.textContent.includes('χωριστά μέσω mydocs'),'judicial special attachments kept separate');
el=testPrimary('region',[coservice]);
t(el.primaryMydocsLink.hidden===true,'ordinary general email never shows mydocs link');
t(el.primaryEmailSource.textContent.includes('§2.2.1')&&!el.primaryEmailSource.textContent.includes('§2.2.3'),'general circular reference unchanged');
t(el.primaryEmailHeading.textContent.includes('Προετοιμασία μηνύματος'),'neutral general heading');
el=testPrimary('eae',[eaeDoc]);
t(el.primaryEmailSource.textContent.includes('§2.2.5'),'EAE remains under primary 2.2.5');
t(el.primaryMydocsLink.hidden===false,'EAE mydocs link preserved');
t(el.primaryMailAction.hidden===true,'EAE mydocs only never enables mailto');
el=testPrimary('intercultural',[{id:'inter-1',action:true,channel:'mydocs'}]);
t(el.primarySubmissionInstruction.textContent.includes('Διαπολιτισμικά'),'intercultural guidance preserved');
console.log('PASS',checks,'v3.22.169 copy/legality/channel regression checks');
