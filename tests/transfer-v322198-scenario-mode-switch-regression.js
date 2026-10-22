'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs'),vm=require('node:vm'),path=require('node:path');
const root=path.resolve(__dirname,'..');
const read=p=>fs.readFileSync(path.join(root,p),'utf8');
const elems={};
const el=id=>elems[id]||(elems[id]={value:'',checked:false,selectedOptions:[{textContent:'Αίτηση'}]});
const defaults={mode:'region',eligibleChildren:'2',serviceMode:'points',msdMode:'direct',transferGeneralOrigin:'general',transferArtBranch:'general',transferArtOrientation:'',transferArtPreviousYear:'2025',transferArtException:'none',transferArtTeachingYears:'5',transferMusicPreviousYear:'2025',transferMusicException:'none',transferMusicSubject:'unspecified',transferEducationType:'general',transferMusicTeachingYears:'5',transferInterculturalPreviousYear:'2026',transferInterculturalOrganic:'yes',transferInterculturalCurrent:'no',transferInterculturalYears:'2',transferInterculturalVacancy:'unknown',transferInterculturalLanguageCase:'regular',transferEaeOrigin:'general',transferEaeArea:'other',transferEaeDestination:'smeae',transferEaeBranch:'pe03',transferEaeQualification:'masters',transferEaePreviousYear:'2026',transferEaeTeachingYears:'3',transferEaeExperienceYears:'0',transferEaeAreaYears:'2',transferGeneralEaeYears:'5',transferGeneralEaeAreaYears:'2',transferGeneralEaeSinceTransfer:'2',transferCoServiceCase:'unspecified'};
for(const [id,v] of Object.entries(defaults))el(id).value=v;
const document={getElementById:el,querySelectorAll(){return []},querySelector(){return null}};
const window={};
vm.runInNewContext(read('includes/transfer-eligibility.js'),{window,document});
vm.runInNewContext(read('includes/transfer-documents.js'),{window,document});
const guide=window.EducationTransferDocuments;
const ids=()=>guide.build().map(d=>d.id);
const baseline=ids();assert(baseline.includes('record'));
const modes=['smeae','eae_sensory','kedasy','music','art','intercultural','region'];
let n=0;
for(let round=0;round<3;round++){
 for(const mode of modes){el('mode').value=mode;const first=ids(),second=ids();assert.deepEqual(first,second,`unstable documents for ${mode}`);assert.equal(first.length,new Set(first).size,`duplicate document IDs for ${mode}`);n+=2;
 if(mode==='intercultural')assert(!first.includes('record'),'intercultural must not inherit general record checklist');
 if(mode==='region')assert.deepEqual(first,baseline,'returning to initial application mode should reproduce baseline checklist');
 }
}
for(const row of guide.build()){const channel=guide.submissionChannel(row);assert(channel && typeof channel.kind==='string',`missing channel for ${row.id}`);n++;}
console.log('PASS transfer scenario mode switches: '+modes.length*3+' transitions, '+n+' assertions');
