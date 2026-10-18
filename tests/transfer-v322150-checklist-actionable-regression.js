'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),vm=require('node:vm'),path=require('node:path');
const root=path.join(__dirname,'..');
const code=fs.readFileSync(path.join(root,'includes/transfer-documents.js'),'utf8');
const ui=fs.readFileSync(path.join(root,'includes/transfer-ui.js'),'utf8');
const php=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis.php'),'utf8');
const form={};
function el(id){return form[id]||(form[id]={value:'',checked:false,selectedOptions:[{textContent:'Αίτηση'}]});}
for(const [id,v] of Object.entries({mode:'region',eligibleChildren:'2',serviceMode:'points',msdMode:'direct',transferGeneralOrigin:'general',transferArtBranch:'general',transferArtOrientation:'',transferArtPreviousYear:'2025',transferArtException:'none',transferArtTeachingYears:'5',transferMusicPreviousYear:'2025',transferMusicException:'none',transferMusicSubject:'unspecified',transferEducationType:'general',transferMusicTeachingYears:'5',transferInterculturalPreviousYear:'2026',transferInterculturalOrganic:'yes',transferInterculturalCurrent:'no',transferInterculturalYears:'2',transferInterculturalVacancy:'unknown',transferInterculturalLanguageCase:'regular',transferEaeOrigin:'general',transferEaeArea:'other',transferEaeDestination:'smeae',transferEaeBranch:'pe03',transferEaeQualification:'masters',transferEaePreviousYear:'2026',transferEaeTeachingYears:'3',transferEaeExperienceYears:'0',transferEaeAreaYears:'2',transferGeneralEaeYears:'5',transferGeneralEaeAreaYears:'2',transferGeneralEaeSinceTransfer:'2',transferCoServiceCase:'unspecified'}))el(id).value=v;
const win={};const document={getElementById:el,querySelectorAll(){return []},querySelector(){return null}};
vm.runInNewContext(fs.readFileSync(path.join(root,'includes/transfer-eligibility.js'),'utf8'),{window:win,document});
vm.runInNewContext(code,{window:win,document});
const guide=win.EducationTransferDocuments;
let count=0;
function check(cond,msg){assert.ok(cond,msg);count++;}
function checkMode(mode,notThere,keep){el('mode').value=mode;const rows=guide.build(),ids=rows.map(r=>r.id);for(const id of notThere)check(!ids.includes(id),mode+' removed duplicate: '+id);for(const id of keep)check(ids.includes(id),mode+' retained real document: '+id);return rows;}
checkMode('region',['eae-to-general'],['record','family','children-specific']);
checkMode('smeae',['eae-dossier'],['eae-masters']);
checkMode('eae_sensory',['eae-dossier'],['eae-masters']);
checkMode('kedasy',['eae-dossier'],['eae-masters']);
checkMode('music',['music-service','music-dossier','music-warnings'],['music-common']);
checkMode('art',['art-service','art-warnings','art-dossier'],['art-cv','art-inventory','art-degrees']);
checkMode('intercultural',['ic-basis','ic-service-record','ic-none','ic-roma','intercultural-dossier'],['ic-cv']);
for(const id of ['transferEaeDossierAdvice','transferMusicDossierAdvice','transferArtDossierAdvice','transferInterculturalDossierAdvice'])check(php.includes('id="'+id+'"'),'existing dossier advice remains '+id);
for(const id of ['transferMusicAdvice','transferArtAdvice','transferInterculturalAdvice','transferGeneralEaeAdvice'])check(ui.includes(id),'eligibility verdict remains '+id);
check(guide.submissionChannel({id:'family',tag:''}).kind==='automatic','family still automatic');
check(guide.submissionChannel({id:'record',tag:''}).kind==='check','official record still verification');
check(guide.submissionChannel({id:'children-specific',tag:''}).kind==='email','child proofs prepared by applicant where needed');
check(code.includes("if(doc.disabled || channel.kind==='automatic')return 'info'"),'informational entries excluded from actions');
check(code.includes("if(channel.kind==='check')return 'verify'"),'verification entries excluded from actions');
check(code.includes('edu-transfer-doc__info-icon'),'no-checkbox marker exists');
check(fs.readFileSync(path.join(root,'includes/transfer-checklist.js'),'utf8').includes('Δικαιολογητικά/ενέργειες που ετοιμάστηκαν:'),'shared counter explicitly reports preparations');
check(code.includes("documentTaskType(d,submissionChannel(d))==='action'"),'print differentiates user actions and information');
console.log('PASS '+count+' v3.22.150 actionable-checklist/dedup checks');
