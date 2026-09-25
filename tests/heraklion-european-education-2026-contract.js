const fs=require('fs'), vm=require('vm'), path=require('path');
const ROOT=path.resolve(__dirname,'..');
const source=fs.readFileSync(path.join(ROOT,'includes','heraklion-european-education-calculations.js'),'utf8');
const context={window:{}}; vm.createContext(context); vm.runInContext(source,context);
const H=context.window.HeraklionEuropeanEducation;
let passed=0, failed=0;
function check(name,cond,detail=''){if(cond){passed++;console.log('PASS:',name)}else{failed++;console.error('FAIL:',name,detail)}}
function eq(name,a,b){check(name,a===b,`expected ${b}, got ${a}`)}
const max=H.calculate({
  candidateRoute:'native',teachingQualification:'yes',appointmentObstacle:'no',healthFitness:'yes',
  relevantPhd:true,otherPhd:true,relevantMaster:true,otherMaster:true,
  greekLevel:'very_good',publication:true,secondDegree:true,otherLanguagesCount:3,
  europeanSchoolYears:5,interviewGreek:20,interviewPersonality:10
});
eq('academic maximum is 45',max.academic.total,45);
eq('service maximum is 25',max.service.points,25);
eq('pre-interview maximum is 70',max.preInterview,70);
eq('interview maximum is 30',max.interview.total,30);
eq('final maximum is 100',max.finalTotal,100);

const overlap=H.calculate({
  candidateRoute:'native',teachingQualification:'yes',appointmentObstacle:'no',healthFitness:'yes',
  relevantPhd:true,otherPhd:true,relevantMaster:true,otherMaster:true,
  relevantMasterSameSubjectAsPhd:true,otherMasterSameSubjectAsPhd:true,
  greekLevel:'very_good',publication:true,secondDegree:true,otherLanguagesCount:3
});
eq('same-subject relevant master is suppressed',overlap.academic.breakdown.relevantMaster,0);
eq('same-subject other master is suppressed',overlap.academic.breakdown.otherMaster,0);
eq('overlap reduces academic score by 6',overlap.academic.total,39);

const caps=H.calculate({otherLanguagesCount:9,europeanSchoolYears:12});
eq('other languages capped at three',caps.academic.otherLanguagesCount,3);
eq('other language points capped at 12',caps.academic.breakdown.otherLanguages,12);
eq('European School service capped at five years',caps.service.years,5);
eq('service points cap at 25',caps.service.points,25);

const partial=H.calculate({interviewGreek:17});
check('partial interview has no final total',partial.finalTotal===null);
check('partial interview is detected',partial.interview.anyEntered && !partial.interview.complete);

const nonNativeIncomplete=H.calculate({candidateRoute:'non_native',teachingQualification:'yes',appointmentObstacle:'no',healthFitness:'yes'});
check('non-native route requires language and inspector',nonNativeIncomplete.eligibility.unanswered.includes('άριστη γνώση της απαιτούμενης γλώσσας') && nonNativeIncomplete.eligibility.unanswered.includes('σύμφωνη γνώμη αρμόδιου Εθνικού Επιθεωρητή Ευρωπαϊκών Σχολείων'));
const nonNativeReady=H.calculate({candidateRoute:'non_native',teachingQualification:'yes',appointmentObstacle:'no',healthFitness:'yes',excellentRequiredLanguage:'yes',inspectorAgreement:'yes'});
check('complete non-native route passes basic eligibility',nonNativeReady.eligibility.eligible);
check('non-native route carries separate-table note',nonNativeReady.eligibility.notes.some(x=>x.includes('χωριστό πίνακα')));

console.log(`RESULT: ${passed}/${passed+failed} PASS`);
process.exit(failed?1:0);
