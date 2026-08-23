(function (global) {
  'use strict';

  const LEVEL_POINTS = Object.freeze({
    host: {none:0,b2:2.5,c1:4,c2:5},
    secondWorking: {none:0,b2:1.5,c1:3,c2:4},
    thirdWorking: {none:0,b2:1,c1:2,c2:3},
    otherEU: {none:0,b2:0.5,c1:1,c2:2}
  });

  const LANGUAGE_LABELS = Object.freeze({
    english:'Αγγλικά', french:'Γαλλικά', german:'Γερμανικά', dutch:'Ολλανδικά', luxembourgish:'Λουξεμβουργιανά',
    bulgarian:'Βουλγαρικά', croatian:'Κροατικά', czech:'Τσεχικά', danish:'Δανικά', estonian:'Εσθονικά', finnish:'Φινλανδικά',
    hungarian:'Ουγγρικά', irish:'Ιρλανδικά', italian:'Ιταλικά', latvian:'Λετονικά', lithuanian:'Λιθουανικά', maltese:'Μαλτέζικα',
    polish:'Πολωνικά', portuguese:'Πορτογαλικά', romanian:'Ρουμανικά', slovak:'Σλοβακικά', slovenian:'Σλοβενικά',
    spanish:'Ισπανικά', swedish:'Σουηδικά'
  });

  const WORKING_LANGUAGES = Object.freeze(['english','french','german']);
  const OTHER_EU_LANGUAGES = Object.freeze([
    'bulgarian','croatian','czech','danish','dutch','estonian','finnish','hungarian','irish','italian',
    'latvian','lithuanian','maltese','polish','portuguese','romanian','slovak','slovenian','spanish','swedish'
  ]);
  const HOST_LANGUAGES = Object.freeze({
    belgium:['dutch','french','german'],
    luxembourg:['french','german','luxembourgish']
  });

  const POSITIONS_2026 = Object.freeze({
    '': null,
    pe70_lux2:{label:'ΠΕ70 — Λουξεμβούργο II',branches:['ΠΕ70'],requirement:'working_c2',hostCountry:'luxembourg'},
    pe70_bru3:{label:'ΠΕ70 — Βρυξέλλες III',branches:['ΠΕ70'],requirement:'working_c2',hostCountry:'belgium'},
    pe06_bru1:{label:'ΠΕ06 — Βρυξέλλες I',branches:['ΠΕ06'],requirement:'general',hostCountry:'belgium'},
    pe06_mol:{label:'ΠΕ06 — MOL',branches:['ΠΕ06'],requirement:'general',hostCountry:'belgium'},
    pe02_bru3:{label:'ΠΕ02 — Βρυξέλλες III',branches:['ΠΕ02'],requirement:'working_c2',hostCountry:'belgium'},
    history_bru1:{label:'Ιστορία — Βρυξέλλες I',branches:['ΠΕ02','ΠΕ33'],requirement:'english_c2',hostCountry:'belgium'},
    pe03_bru3:{label:'ΠΕ03 — Βρυξέλλες III',branches:['ΠΕ03'],requirement:'working_c2',hostCountry:'belgium'},
    chemistry_bru3:{label:'Χημεία — Βρυξέλλες III',branches:['ΠΕ04.02','ΠΕ85'],requirement:'working_c2',hostCountry:'belgium'},
    biology_bru3:{label:'ΠΕ04.04 — Βρυξέλλες III',branches:['ΠΕ04.04'],requirement:'working_c2',hostCountry:'belgium'},
    pe08_mol:{label:'ΠΕ08 — MOL',branches:['ΠΕ08'],requirement:'english_c2',hostCountry:'belgium'},
    pe11_bru3:{label:'ΠΕ11 — Βρυξέλλες III',branches:['ΠΕ11'],requirement:'working_c2',hostCountry:'belgium'},
    librarian_lux2:{label:'EL & EN Librarian — Λουξεμβούργο II',branches:['Δευτεροβάθμια Εκπαίδευση'],requirement:'english_c2',hostCountry:'luxembourg',librarian:true}
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
    const raw=annual+uni+ministry+publicAdmin+major+eap;
    return {annual,university:uni,ministry,publicAdmin,major,eap,total:Math.min(4,raw),raw};
  }

  function languagePoints(o={}){
    const declarations=[
      {slot:'hostLanguage', key:o.hostLanguageKey||'', level:o.hostLanguageLevel||'none', points:LEVEL_POINTS.host[o.hostLanguageLevel||'none']||0},
      {slot:'secondWorkingLanguage', key:o.secondWorkingLanguage||'', level:o.secondWorkingLevel||'none', points:LEVEL_POINTS.secondWorking[o.secondWorkingLevel||'none']||0},
      {slot:'thirdWorkingLanguage', key:o.thirdWorkingLanguage||'', level:o.thirdWorkingLevel||'none', points:LEVEL_POINTS.thirdWorking[o.thirdWorkingLevel||'none']||0},
      {slot:'otherEULanguage', key:o.otherEULanguage||'', level:o.otherEULevel||'none', points:LEVEL_POINTS.otherEU[o.otherEULevel||'none']||0}
    ];
    const winners=new Map(), duplicateLanguages=[];
    declarations.forEach((d,index)=>{
      if(!d.key || d.level==='none' || d.points<=0) return;
      const old=winners.get(d.key);
      if(!old || d.points>old.points) winners.set(d.key,{...d,index});
      else if(old) duplicateLanguages.push(d.key);
    });
    const awarded={hostLanguage:0,secondWorkingLanguage:0,thirdWorkingLanguage:0,otherEULanguage:0};
    winners.forEach(w=>{awarded[w.slot]=w.points;});
    const uniqueDuplicates=[...new Set(duplicateLanguages.concat(
      declarations.filter(d=>d.key).map(d=>d.key).filter((key,i,arr)=>arr.indexOf(key)!==i)
    ))];
    return {
      ...awarded,
      total:Object.values(awarded).reduce((a,b)=>a+b,0),
      duplicateLanguages:uniqueDuplicates,
      declarations
    };
  }

  function academicPoints(o={}){
    const titleBreakdown={
      phd:o.phd?5:0,
      master:o.master?3:0,
      secondPhd:o.secondPhd?3:0,
      secondMaster:o.secondMaster?1:0,
      secondDegree:o.secondDegree?2:0,
      retrainingDegree:o.retrainingDegree?2:0
    };
    const titles=Object.values(titleBreakdown).reduce((a,b)=>a+b,0);
    const languages=languagePoints(o);
    const ict=o.ictLevel==='b'?2:0;
    return {
      titles,
      titleBreakdown,
      hostLanguage:languages.hostLanguage,
      secondWorkingLanguage:languages.secondWorkingLanguage,
      thirdWorkingLanguage:languages.thirdWorkingLanguage,
      otherEULanguage:languages.otherEULanguage,
      languages,
      ict,
      total:titles+languages.total+ict
    };
  }

  function serviceDuration(o={}){
    const years=Math.min(global.EducationCore.MAX_SERVICE_YEARS,int(o.teachingYears!==undefined?o.teachingYears:o.totalTeachingYears));
    const months=clamp(int(o.teachingMonths),0,11);
    const days=clamp(int(o.teachingDays),0,31);
    return {years,months,days};
  }

  function servicePoints(o={}){
    const duration=serviceDuration(o);
    const extraTeachingYears=Math.min(9,Math.max(0,duration.years-4));
    const higherEducation=Math.min(2,num(o.higherEducationSemesters)*0.5);
    const innovation=Math.min(3,int(o.innovativePrograms));
    const training=trainingPoints(o);
    return {
      duration,
      extraTeachingYears,
      higherEducation,
      innovation,
      training:training.total,
      trainingBreakdown:training,
      total:extraTeachingYears+higherEducation+innovation+training.total
    };
  }

  function positionEligibility(o={}){
    const position=POSITIONS_2026[o.position||''];
    if(!position) return {answered:false,branchRequired:false,branchAnswered:false,eligible:false,issues:[],warnings:[]};
    const issues=[], warnings=[];
    const branchRequired=!position.librarian && position.branches.length>1;
    const branchAnswered=!branchRequired || !!o.selectedBranch;
    if(branchRequired && o.selectedBranch && !position.branches.includes(o.selectedBranch)) issues.push('Ο επιλεγμένος κλάδος δεν αντιστοιχεί στη συγκεκριμένη θέση.');
    if(position.librarian && o.librarianQualification!=='yes') issues.push('Για τη θέση Librarian απαιτείται το προβλεπόμενο προσόν Βιβλιοθηκονομίας.');
    if(position.requirement==='working_c2' && o.positionLanguageRequirementMet!=='yes') issues.push('Για τη συγκεκριμένη θέση απαιτείται Γ2 σε μία από τις γλώσσες εργασίας (Αγγλικά, Γαλλικά ή Γερμανικά).');
    if(position.requirement==='english_c2' && o.positionLanguageRequirementMet!=='yes') issues.push('Για τη συγκεκριμένη θέση απαιτείται Γ2 στην αγγλική γλώσσα.');
    return {answered:true,branchRequired,branchAnswered,eligible:branchAnswered&&issues.length===0,issues,warnings,position};
  }

  function interviewPoints(o={}){
    const keys=['oralPrerequisiteLanguage','oralWorkingLanguage1','oralWorkingLanguage2','thoughtSpeech','interculturalInnovation','curriculumKnowledge'];
    const complete=keys.every(k=>o[k]!==''&&o[k]!==null&&o[k]!==undefined);
    const anyEntered=keys.some(k=>o[k]!==''&&o[k]!==null&&o[k]!==undefined);
    const prerequisite=clamp(o.oralPrerequisiteLanguage,0,10);
    const prerequisiteEntered=o.oralPrerequisiteLanguage!==''&&o.oralPrerequisiteLanguage!==null&&o.oralPrerequisiteLanguage!==undefined;
    const working1=clamp(o.oralWorkingLanguage1,0,5);
    const working2=clamp(o.oralWorkingLanguage2,0,5);
    const thought=clamp(o.thoughtSpeech,0,5);
    const intercultural=clamp(o.interculturalInnovation,0,5);
    const curriculum=clamp(o.curriculumKnowledge,0,10);
    const languageBasePassed=!prerequisiteEntered || prerequisite>=5;
    return {prerequisite,working1,working2,thought,intercultural,curriculum,total:prerequisite+working1+working2+thought+intercultural+curriculum,complete,anyEntered,prerequisiteEntered,languageBasePassed};
  }

  function documentChecklist(o={}){
    const docs=[
      {key:'registry',text:'Φύλλο Μητρώου Ποιότητας (στο επίσημο υπόδειγμα) και πιστοποιητικό υπηρεσιακών μεταβολών.',mandatory:true},
      {key:'europass',text:'Βιογραφικό σημείωμα τύπου Europass.',mandatory:true},
      {key:'ict',text:'Πιστοποιητικό γνώσης ΤΠΕ τουλάχιστον Α΄ επιπέδου.',mandatory:true},
      {key:'degrees',text:'Βασικό πτυχίο και τυχόν πρόσθετοι τίτλοι σπουδών που δηλώνονται. Για τίτλους αλλοδαπής: η απαιτούμενη πράξη αναγνώρισης ΔΟΑΤΑΠ/ΔΙΚΑΤΣΑ.',mandatory:true}
    ];
    const position=POSITIONS_2026[o.position||''];
    const hasLanguage=!!(o.hostLanguageKey||o.secondWorkingLanguage||o.thirdWorkingLanguage||o.otherEULanguage||(position&&(position.requirement==='working_c2'||position.requirement==='english_c2')));
    if(hasLanguage){
      docs.push({key:'languages',text:'Τίτλοι/πιστοποιητικά ξένων γλωσσών που δηλώνονται. Αν η γλωσσομάθεια αποδεικνύεται από τίτλο ΑΕΙ αλλοδαπής, ελέγχεται και η προβλεπόμενη βεβαίωση γλώσσας σπουδών.',mandatory:false});
      const declaredLanguages=[o.hostLanguageKey,o.secondWorkingLanguage,o.thirdWorkingLanguage,o.otherEULanguage].filter(Boolean);
      if(declaredLanguages.some(k=>!['english','french','german','italian','spanish'].includes(k))) docs.push({key:'languageTranslation',text:'Για τίτλο γλώσσας άλλης από Αγγλικά, Γαλλικά, Γερμανικά, Ιταλικά ή Ισπανικά: επίσημη μετάφραση.',mandatory:false});
    }
    if(num(o.higherEducationSemesters)>0) docs.push({key:'higherEducation',text:'Βεβαίωση αυτοδύναμου διδακτικού έργου σε ΑΕΙ.',mandatory:false});
    if(int(o.innovativePrograms)>0) docs.push({key:'innovation',text:'Βεβαιώσεις συμμετοχής και υλοποίησης των δηλωμένων καινοτόμων/ευρωπαϊκών/διεθνών δράσεων.',mandatory:false});
    if(o.annualTraining||int(o.universityTrainingCount)>0||num(o.ministryTrainingHours)>0||num(o.publicAdminTrainingHours)>0||int(o.eapAnnualUnits)>0||int(o.eapSemesterUnits)>0||o.majorTraining){
      docs.push({key:'training',text:'Βεβαιώσεις/πιστοποιητικά των επιμορφώσεων που δηλώνονται.',mandatory:false});
    }
    if(position&&position.librarian) docs.push({key:'librarian',text:'Τίτλος/δικαιολογητικό που αποδεικνύει το προβλεπόμενο προσόν Βιβλιοθηκονομίας.',mandatory:false});
    return docs;
  }

  function calculate(o={}){
    const academic=academicPoints(o), service=servicePoints(o), interview=interviewPoints(o), position=positionEligibility(o);
    const unanswered=[],issues=[],warnings=[...position.warnings],interviewIssues=[];
    const teachingYearsRaw=o.teachingYears!==undefined?o.teachingYears:o.totalTeachingYears;
    if(teachingYearsRaw===''||teachingYearsRaw===null||teachingYearsRaw===undefined) unanswered.push('συνολική αναγνωρισμένη διδακτική υπηρεσία');
    else if(service.duration.years<4) issues.push('Απαιτούνται τουλάχιστον 4 πλήρη έτη συνολικής και αναγνωρισμένης διδακτικής υπηρεσίας έως 31/08/2026.');
    if(!o.ictLevel) unanswered.push('πιστοποιημένη γνώση ΤΠΕ');
    else if(o.ictLevel==='none') issues.push('Απαιτείται πιστοποιημένη γνώση ΤΠΕ τουλάχιστον Α΄ επιπέδου.');
    if(!o.blockingIssue) unanswered.push('κώλυμα συμμετοχής');
    else if(o.blockingIssue==='yes') issues.push('Δηλώθηκε πιθανό κώλυμα συμμετοχής/επιλογής.');
    if(!o.position) unanswered.push('θέση της πρόσκλησης 2026'); else {if(position.branchRequired&&!position.branchAnswered) unanswered.push('κλάδος για τη συγκεκριμένη θέση');issues.push(...position.issues);}
    if(o.secondPhd&&!o.phd) warnings.push('Έχει δηλωθεί δεύτερο διδακτορικό χωρίς πρώτο διδακτορικό.');
    if(o.secondMaster&&!o.master) warnings.push('Έχει δηλωθεί δεύτερο μεταπτυχιακό χωρίς πρώτο μεταπτυχιακό.');
    if(academic.languages.duplicateLanguages.length){
      warnings.push('Η ίδια γλώσσα δηλώθηκε σε περισσότερες από μία κατηγορίες. Μοριοδοτήθηκε μόνο μία φορά με την υψηλότερη τιμή.');
    }
    if(interview.prerequisiteEntered&&!interview.languageBasePassed){
      interviewIssues.push('Δεν συγκεντρώθηκε η απαιτούμενη βάση 5/10 στην προαπαιτούμενη ξένη γλώσσα. Δεν προκύπτει τελική βαθμολογία /90.');
    }
    const preInterview=academic.total+service.total;
    const finalTotal=interview.complete&&interview.languageBasePassed?preInterview+interview.total:null;
    return {
      academic,service,interview,position,preInterview,finalTotal,
      eligibleBeforeInterview:unanswered.length===0&&issues.length===0,
      unanswered,issues,warnings,interviewIssues,
      documents:documentChecklist(o)
    };
  }

  global.EuropeanSchools=Object.freeze({
    LEVEL_POINTS,LANGUAGE_LABELS,WORKING_LANGUAGES,OTHER_EU_LANGUAGES,HOST_LANGUAGES,POSITIONS_2026,
    trainingPoints,languagePoints,academicPoints,serviceDuration,servicePoints,positionEligibility,interviewPoints,documentChecklist,calculate
  });
})(window);
