'use strict';
const assert = require('node:assert/strict');
const {calculate:c,evaluation:e} = require('../includes/school-leadership-calculations.js');
let checks=0;
function eq(actual,expected) {assert.equal(actual,expected);checks++;}
const ready={mode:'first',primary:'no',interview:'0',inputsConfirmed:true};
eq(c(ready).total,0);
eq(c({...ready,interview:''}).total,null);
eq(c({...ready,inputsConfirmed:false}).total,null);
eq(c({...ready,mode:''}).total,null);
eq(c({...ready,primary:''}).total,null);
eq(c({...ready,mode:'general'}).total,null);
eq(c({phdOther:2}).details.titles,5);
eq(c({phdRelated:2}).details.titles,11);
eq(c({phdRelated:1,phdOther:1}).details.titles,11);
eq(c({masterOther:2}).details.titles,3);
eq(c({masterRelated:2}).details.titles,7);
eq(c({masterRelated:1,masterOther:1}).details.titles,7);
eq(c({phdRelated:2,masterRelated:2}).details.titles,13);
eq(c({primary:'no',didaskaleio:1}).details.titles,0);
eq(c({primary:'yes',didaskaleio:1}).details.titles,2);
eq(c({secondDegree:'four',thirdDegree:1,esdda:1}).details.titles,5);
eq(c({publicHours:9}).details.training,0);
eq(c({publicHours:19}).details.training,.1);
eq(c({universityTraining:2,publicHours:1000,ekddaHours:1000,ictLevel:'B',majorTraining:1}).details.training,4);
eq(c({ictLevel:'B1'}).details.training,.5);
eq(c({ictLevel:'B'}).details.training,1);
eq(c({language1:'en',languageLevel1:'C2',language2:'en',languageLevel2:'C1'}).details.languages,3);
eq(c({language1:'en',languageLevel1:'B2',language2:'fr',languageLevel2:'C2'}).details.languages,3.5);
eq(c({language1:'en',languageLevel1:'C2',language2:'fr',languageLevel2:'C2'}).details.languages,4);
eq(c({appointmentLanguage:'en',language1:'en',languageLevel1:'C2',language2:'fr',languageLevel2:'C1'}).details.languages,2);
eq(c({bookInternationalTeam:1}).details.books,1);
eq(c({bookInternational:4,articleInternational:4}).details.books,3);
eq(c({bookInternational:4,articleInternational:4}).details.articles,2);
eq(c({higherSemesters:10}).details.higher,2);
eq(c({teachingMonths:96}).details.teaching,0);
eq(c({teachingMonths:98}).details.teaching,0);
eq(c({teachingMonths:99}).details.teaching,.25);
eq(c({teachingMonths:108,modelMonths:12}).details.teaching,1.5);
eq(c({teachingMonths:97,modelMonths:100}).details.teaching,0);
eq(c({teachingMonths:300,modelMonths:200}).details.teaching,9);
eq(c({researchProjects:9,innovationProjects:9}).details.research,4);
eq(c({teachingMonths:300,trainerHours:500,researchProjects:9}).details.teachingGroup,13);
eq(c({adminCschool:48,adminDschool:96}).details.admin,6);
eq(c({adminCschool:48,adminDschool:96,adminCother:48,adminDother:96}).details.admin,10);
eq(c({adminA:2}).details.admin,0);
eq(c({adminA:3}).details.admin,.5);
eq(c({adminA:720,adminB:720,adminE:720,adminF:720}).details.admin,12);
eq(e({mode:'general',evalType:'leader',evalScore:50}),.5);
eq(e({mode:'general',evalType:'leader',evalScore:51}),.5);
eq(e({mode:'general',evalType:'leader',evalScore:100}),25);
eq(e({mode:'general',evalType:'leader',evalScore:49}),null);
eq(e({mode:'general',evalType:'three',evalA1:'excellent',evalA2:'excellent',evalB:'satisfactory'}),20);
eq(e({mode:'general',evalType:'three',evalA1:'good',evalA2:'good',evalB:'satisfactory'}),12.5);
eq(e({mode:'general',evalType:'two',evalA1:'excellent',evalB:'satisfactory'}),15);
eq(e({mode:'general',evalType:'three',evalA1:'excellent',evalA2:'excellent',evalB:'unsatisfactory'}),null);
eq(e({mode:'first',evalType:'leader',evalScore:100}),0);
const maximal={...ready,phdRelated:2,masterRelated:2,universityTraining:2,publicHours:100,ekddaHours:100,
 language1:'en',languageLevel1:'C2',language2:'fr',languageLevel2:'C2',bookInternational:2,articleInternational:2,
 higherSemesters:4,teachingMonths:204,trainerHours:400,guidanceMonths:48,adminA:36,adminB:48,interview:20};
eq(c(maximal).total,75);
eq(c({...maximal,mode:'general',evalType:'three',evalA1:'excellent',evalA2:'excellent',evalB:'excellent'}).total,100);
eq(c({phdRelated:Infinity,publicHours:NaN,adminA:-1}).known,0);
// Every permutation of the descriptive scale must map to the same statutory row.
const grades=['excellent','good','satisfactory'];
for(const a of grades) for(const b of grades) for(const v of grades) {
 eq(e({mode:'general',evalType:'three',evalA1:a,evalA2:b,evalB:v}),e({mode:'general',evalType:'three',evalA1:v,evalA2:a,evalB:b}));
}
console.log(`PASS: ${checks} school-leadership calculations`);
