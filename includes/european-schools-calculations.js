(function (global) {
  'use strict';

  const LEVEL_POINTS = Object.freeze({
    host: {none:0,b2:2.5,c1:4,c2:5},
    secondWorking: {none:0,b2:1.5,c1:3,c2:4},
    thirdWorking: {none:0,b2:1,c1:2,c2:3},
    otherEU: {none:0,b2:0.5,c1:1,c2:2}
  });

  const POSITIONS_2026 = Object.freeze({
    '': null,
    pe70_lux2:{label:'ΠΕ70 — Λουξεμβούργο II',branches:['PE70'],requirement:'working_c2'},
    pe70_bru3:{label:'ΠΕ70 — Βρυξέλλες III',branches:['PE70'],requirement:'working_c2'},
    pe06_bru1:{label:'ΠΕ06 — Βρυξέλλες I',branches:['PE06'],requirement:'general'},
    pe06_mol:{label:'ΠΕ06 — MOL',branches:['PE06'],requirement:'general'},
    pe02_bru3:{label:'ΠΕ02 — Βρυξέλλες III',branches:['PE02'],requirement:'working_c2'},
    history_bru1:{label:'Ιστορία — Βρυξέλλες I',branches:['PE02','PE33'],requirement:'english_c2'},
    pe03_bru3:{label:'ΠΕ03 — Βρυξέλλες III',branches:['PE03'],requirement:'working_c2'},
    chemistry_bru3:{label:'Χημεία — Βρυξέλλες III',branches:['PE04.02','PE85'],requirement:'working_c2'},
    biology_bru3:{label:'ΠΕ04.04 — Βρυξέλλες III',branches:['PE04.04'],requirement:'working_c2'},
    pe08_mol:{label:'ΠΕ08 — MOL',branches:['PE08'],requirement:'english_c2'},
    pe11_bru3:{label:'ΠΕ11 — Βρυξέλλες III',branches:['PE11'],requirement:'working_c2'},
    librarian_lux2:{label:'EL & EN Librarian — Λουξεμβούργο II',branches:['SECONDARY_ANY'],requirement:'english_c2',librarian:true}
  });

  const num=v=>Number.isFinite(Number(v))?Number(v):0;
  const clamp=(v,min,max)=>Math.min(max,Math.max(min,num(v)));
  const int=v=>Math.max(0,Math.floor(num(v)));

  function trainingPoints(o={}){
    const annual=o.annualTraining?1:0;
    const uni=Math.min(2,int(o.universityTrainingCount));
    const ministry=Math.min(1,Math.floor(num(o.ministryTrainingHours)/10)*0.1);
    const publicAdmin=Math.min(1,Math.floor(num(o.publicAdminTrainingHours)/10)*0.1);
    const major=o.majorTraining?1:0;
    const eap=Math.min(1,int(o.eapAnnualUnits)+(int(o.eapSemesterUnits)*0.5));
    return Math.min(4,annual+uni+ministry+publicAdmin+major+eap);
  }

  function academicPoints(o={}){
    const titles=(o.phd?5:0)+(o.master?3:0)+(o.secondPhd?3:0)+(o.secondMaster?1:0)+(o.secondDegree?2:0)+(o.retrainingDegree?2:0);
    const host=LEVEL_POINTS.host[o.hostLanguageLevel||'none']||0;
    const second=LEVEL_POINTS.secondWorking[o.secondWorkingLevel||'none']||0;
    const third=LEVEL_POINTS.thirdWorking[o.thirdWorkingLevel||'none']||0;
    const other=LEVEL_POINTS.otherEU[o.otherEULevel||'none']||0;
    const ict=o.ictLevel==='b'?2:0;
    return {titles,hostLanguage:host,secondWorkingLanguage:second,thirdWorkingLanguage:third,otherEULanguage:other,ict,total:titles+host+second+third+other+ict};
  }

  function servicePoints(o={}){
    const totalTeachingYears=int(o.totalTeachingYears);
    const extraTeachingYears=Math.min(9,Math.max(0,totalTeachingYears-4));
    const higherEducation=Math.min(2,num(o.higherEducationSemesters)*0.5);
    const innovation=Math.min(3,int(o.innovativePrograms));
    const training=trainingPoints(o);
    return {extraTeachingYears,higherEducation,innovation,training,total:extraTeachingYears+higherEducation+innovation+training};
  }

  function positionEligibility(o={}){
    const position=POSITIONS_2026[o.position||''];
    if(!position) return {answered:false,eligible:false,issues:[],warnings:[]};
    const issues=[], warnings=[];
    const branch=o.branch||'';
    if(!branch) issues.push('Δεν έχει επιλεγεί κλάδος/ειδικότητα.');
    else if(position.librarian){
      if(o.librarianQualification!=='yes') issues.push('Για τη θέση Librarian απαιτείται το προβλεπόμενο προσόν Βιβλιοθηκονομίας.');
      if(branch!=='SECONDARY_ANY') warnings.push('Για τη θέση Librarian μπορούν να υποβάλουν υποψηφιότητα όλες οι ειδικότητες Δευτεροβάθμιας, με το απαιτούμενο προσόν Βιβλιοθηκονομίας.');
    } else if(!position.branches.includes(branch)) issues.push('Ο επιλεγμένος κλάδος δεν αντιστοιχεί στη συγκεκριμένη θέση της πρόσκλησης 2026.');

    if(position.requirement==='working_c2' && o.positionLanguageRequirementMet!=='yes') issues.push('Για τη συγκεκριμένη θέση απαιτείται Γ2 σε μία από τις γλώσσες εργασίας (Αγγλικά, Γαλλικά ή Γερμανικά).');
    if(position.requirement==='english_c2' && o.positionLanguageRequirementMet!=='yes') issues.push('Για τη συγκεκριμένη θέση απαιτείται Γ2 στην αγγλική γλώσσα.');
    return {answered:true,eligible:issues.length===0,issues,warnings,position};
  }

  function interviewPoints(o={}){
    const keys=['oralPrerequisiteLanguage','oralWorkingLanguage1','oralWorkingLanguage2','thoughtSpeech','interculturalInnovation','curriculumKnowledge'];
    const complete=keys.every(k=>o[k]!==''&&o[k]!==null&&o[k]!==undefined);
    const anyEntered=keys.some(k=>o[k]!==''&&o[k]!==null&&o[k]!==undefined);
    const prerequisite=clamp(o.oralPrerequisiteLanguage,0,10);
    const working1=clamp(o.oralWorkingLanguage1,0,5);
    const working2=clamp(o.oralWorkingLanguage2,0,5);
    const thought=clamp(o.thoughtSpeech,0,5);
    const intercultural=clamp(o.interculturalInnovation,0,5);
    const curriculum=clamp(o.curriculumKnowledge,0,10);
    return {prerequisite,working1,working2,thought,intercultural,curriculum,total:prerequisite+working1+working2+thought+intercultural+curriculum,complete,anyEntered,languageBasePassed:!anyEntered||prerequisite>=5};
  }

  function calculate(o={}){
    const academic=academicPoints(o), service=servicePoints(o), interview=interviewPoints(o), position=positionEligibility(o);
    const unanswered=[],issues=[],warnings=[...position.warnings];
    if(o.totalTeachingYears===''||o.totalTeachingYears===null||o.totalTeachingYears===undefined) unanswered.push('συνολική αναγνωρισμένη διδακτική υπηρεσία');
    else if(int(o.totalTeachingYears)<4) issues.push('Απαιτούνται τουλάχιστον 4 έτη συνολικής και αναγνωρισμένης διδακτικής υπηρεσίας.');
    if(!o.ictLevel) unanswered.push('πιστοποιημένη γνώση ΤΠΕ');
    else if(o.ictLevel==='none') issues.push('Απαιτείται πιστοποιημένη γνώση ΤΠΕ τουλάχιστον Α΄ επιπέδου.');
    if(!o.blockingIssue) unanswered.push('κώλυμα συμμετοχής');
    else if(o.blockingIssue==='yes') issues.push('Δηλώθηκε πιθανό κώλυμα συμμετοχής/επιλογής.');
    if(!o.position) unanswered.push('θέση της πρόσκλησης 2026'); else issues.push(...position.issues);
    if(o.secondPhd&&!o.phd) warnings.push('Έχει δηλωθεί δεύτερο διδακτορικό χωρίς πρώτο διδακτορικό.');
    if(o.secondMaster&&!o.master) warnings.push('Έχει δηλωθεί δεύτερο μεταπτυχιακό χωρίς πρώτο μεταπτυχιακό.');
    if(o.secondWorkingLanguage && o.hostLanguageKey && o.secondWorkingLanguage===o.hostLanguageKey) warnings.push('Η δεύτερη γλώσσα εργασίας δεν πρέπει να είναι ίδια με την επίσημη γλώσσα που μοριοδοτήθηκε ήδη.');
    if(o.thirdWorkingLanguage && o.hostLanguageKey && o.thirdWorkingLanguage===o.hostLanguageKey) warnings.push('Η τρίτη γλώσσα εργασίας δεν πρέπει να είναι ίδια με την επίσημη γλώσσα που μοριοδοτήθηκε ήδη.');
    if(o.secondWorkingLanguage && o.thirdWorkingLanguage && o.secondWorkingLanguage===o.thirdWorkingLanguage) warnings.push('Η ίδια γλώσσα δεν μπορεί να δηλωθεί ως δεύτερη και τρίτη γλώσσα εργασίας.');
    if(interview.anyEntered&&!interview.languageBasePassed) issues.push('Στην προφορική εξέταση της προαπαιτούμενης ξένης γλώσσας απαιτούνται τουλάχιστον 5/10 μονάδες.');
    const preInterview=academic.total+service.total;
    return {academic,service,interview,position,preInterview,finalTotal:interview.complete?preInterview+interview.total:null,eligibleBeforeInterview:unanswered.length===0&&issues.length===0,unanswered,issues,warnings};
  }

  global.EuropeanSchools=Object.freeze({LEVEL_POINTS,POSITIONS_2026,trainingPoints,academicPoints,servicePoints,positionEligibility,interviewPoints,calculate});
})(window);
