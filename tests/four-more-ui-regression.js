'use strict';
function assert(label, ok){ console.log((ok?'PASS':'FAIL')+' | '+label); if(!ok) process.exitCode=1; }
function classList(initial=[]){ const s=new Set(initial); return { add:(...x)=>x.forEach(v=>s.add(v)), remove:(...x)=>x.forEach(v=>s.delete(v)), toggle:(v,force)=>{ if(force===undefined) force=!s.has(v); force?s.add(v):s.delete(v); return force; }, contains:v=>s.has(v) }; }
function element(id, value='', tag='INPUT'){
  const listeners={};
  return { id, value:String(value), tagName:tag, checked:false, disabled:false, selectedIndex:0, options:[], style:{}, innerHTML:'', textContent:'', className:'', min:'', max:'', listeners, classList:classList(),
    addEventListener:(type,fn)=>{ listeners[type]=fn; },
    appendChild(child){ this.options.push(child); },
    getAttribute(name){ if(name==='min') return this.min===''?null:this.min; if(name==='max') return this.max===''?null:this.max; return null; },
    setAttribute(name,val){ this[name]=String(val); }, focus(){}, select(){}
  };
}
function fire(el,type){ if(!el.listeners[type]) throw new Error('missing '+type+' listener for '+el.id); return el.listeners[type].call(el,{currentTarget:el,target:el}); }

// 1) SDE leadership: verify key role/input behavior and external action bindings.
{
  global.window=global; global.scrollTo=()=>{}; global.alert=()=>{};
  const ids=['role','permanentTeacher','educationalServiceYears','tertiaryDegree','assignmentEligible','computerKnowledge','adultEducationExperience','adminQualifications','blockingIssue','phd','master','esdda','secondDegree','language1','languageLevel1','languageAppointment1','language2','languageLevel2','languageAppointment2','sdeTeachingYears','sdeTeachingHours','sdeTransferredYears','adultNonformalHours','schoolTeachingYears','schoolTeachingHours','schoolTransferredYears','sdeDirectorYears','sdeDeputyYears','otherAdminYears','trainingHours','interviewScore','tertiaryDegreeWrap','adminQualificationsWrap','interviewCard','interviewRow','serviceRequirement','assignmentRequirement','teachingMax','adminMax','roleChip','formalScore','teachingScore','adminScore','trainingScore','criteriaScore','interviewResult','criteriaRow','totalScore','totalOutOf','totalContext','totalBar','eligibilityStatus','breakdown','overflowHint','sdeLeadershipCopyBtn','sdeLeadershipResetBtn'];
  const E={}; ids.forEach(id=>E[id]=element(id));
  ['tertiaryDegreeWrap','adminQualificationsWrap','interviewCard','interviewRow'].forEach(id=>E[id].classList=classList(['hidden']));
  E.interviewScore.min='0'; E.interviewScore.max='25';
  const numericIds=['sdeTeachingYears','sdeTeachingHours','sdeTransferredYears','adultNonformalHours','schoolTeachingYears','schoolTeachingHours','schoolTransferredYears','sdeDirectorYears','sdeDeputyYears','otherAdminYears','trainingHours']; numericIds.forEach(id=>E[id].value='0');
  E.phd.value='none'; E.master.value='none'; E.esdda.value='no'; E.secondDegree.value='no'; E.languageAppointment1.value='no'; E.languageAppointment2.value='no';
  let lastData=null, calcCount=0, languageSyncCount=0;
  global.LanguagePairLock={sync:()=>{languageSyncCount++; return true;}};
  global.SDELeadership={
    ROLE:{director:{label:'Διευθυντής',minServiceYears:8,requiredTeachingHours:10,max:{teaching:20,admin:15}},deputy:{label:'Υποδιευθυντής',minServiceYears:6,requiredTeachingHours:15,max:{teaching:25,admin:10}}},
    calculate(data){ calcCount++; lastData=data; const cfg=this.ROLE[data.role]||null; return {role:data.role,config:cfg,formal:{total:0,details:[]},teaching:{total:0,details:[]},admin:{total:0,details:[],overflow:{sdeDirector:0,sdeDeputy:0,other:0}},training:{total:0,details:[]},criteria:0,interviewEntered:data.interviewScore!=='',interview:Number(data.interviewScore||0),final:data.interviewScore!==''?Number(data.interviewScore||0):null,eligibility:{status:'unknown',missing:[],issues:[]},warnings:[]}; }
  };
  global.document={getElementById:id=>E[id]||null,querySelectorAll:sel=>sel.includes('input')?Object.values(E).filter(x=>x.tagName==='INPUT'):sel==='select'?Object.values(E).filter(x=>x.tagName==='SELECT'):[],addEventListener:()=>{}};
  ['role','permanentTeacher','tertiaryDegree','assignmentEligible','computerKnowledge','adultEducationExperience','adminQualifications','blockingIssue','phd','master','esdda','secondDegree','language1','languageLevel1','languageAppointment1','language2','languageLevel2','languageAppointment2'].forEach(id=>E[id].tagName='SELECT');
  delete require.cache[require.resolve('../includes/sde-leadership-ui.js')]; require('../includes/sde-leadership-ui.js');
  assert('leadership role change externally wired', typeof E.role.listeners.change==='function');
  assert('leadership interview input externally wired', typeof E.interviewScore.listeners.input==='function');
  assert('leadership copy externally wired', typeof E.sdeLeadershipCopyBtn.listeners.click==='function');
  assert('leadership reset externally wired', typeof E.sdeLeadershipResetBtn.listeners.click==='function');
  E.role.value='director'; fire(E.role,'change');
  assert('director mode reveals director-only degree', !E.tertiaryDegreeWrap.classList.contains('hidden'));
  assert('director mode reveals interview', !E.interviewCard.classList.contains('hidden'));
  assert('director mode updates service requirement', E.serviceRequirement.textContent.includes('8'));
  assert('leadership calculation receives selected role', lastData && lastData.role==='director');
  E.interviewScore.value='99'; fire(E.interviewScore,'input');
  assert('interview score remains bounded at 25', E.interviewScore.value==='25');
  E.language1.value='english'; fire(E.language1,'change');
  assert('language change still synchronizes language pair', languageSyncCount>=2);
  assert('leadership changes still recalculate', calcCount>=4);
}

// 2) SDE detachment: verify specialty-specific behavior, scoring render, and bindings.
{
  const ids=['specialty','eligibilitySchoolYears','formalEducationYears','mathInfoDegree','formerPE09','formerPE1208','teleEducation','blockingIssue','phd','master','secondDegree','secondPhd','secondMaster','sdeTrainingHours','adultTrainingHours','sdeYears','sdeHourlyHours','adultEducationHours','computer','mathInfoDegreeWrap','formerPE09Wrap','formerPE1208Wrap','totalScore','totalBar','educationScore','experienceScore','otherScore','formalExperiencePreview','assignmentResult','assignmentBox','eligibilityStatus','breakdown','sdeDetachmentResetBtn'];
  const E={}; ids.forEach(id=>E[id]=element(id));
  ['specialty','mathInfoDegree','formerPE09','formerPE1208','teleEducation','blockingIssue','phd','master','secondDegree','secondPhd','secondMaster','computer'].forEach(id=>E[id].tagName='SELECT');
  ['mathInfoDegreeWrap','formerPE09Wrap','formerPE1208Wrap'].forEach(id=>E[id].classList=classList(['hidden']));
  ['sdeTrainingHours','adultTrainingHours','sdeYears','sdeHourlyHours','adultEducationHours'].forEach(id=>E[id].value='0');
  E.eligibilitySchoolYears.value=''; E.formalEducationYears.value='';
  let customEvent=null, lastData=null, calcCount=0;
  global.AsepLanguageSelector={readEntries:()=>[],reset:()=>{}};
  global.SDECalculator={calculateAll(data){ calcCount++; lastData=data; return {total:data.computer?1:0,education:{total:0,details:[],warnings:[]},experience:{total:0,formalPoints:0,details:[]},other:{total:data.computer?1:0,details:[],warnings:[]},assignments:data.specialty==='ΠΕ86'?[{literacy:'Πληροφορικός Γραμματισμός',assignment:'Α΄',note:''}]:[],eligibleByTwoYears:Number(data.eligibilitySchoolYears)>=2}; }};
  global.document={getElementById:id=>E[id]||null,querySelectorAll:()=>[],addEventListener:(type,fn)=>{customEvent={type,fn};}};
  delete require.cache[require.resolve('../includes/sde-detachment-ui.js')]; require('../includes/sde-detachment-ui.js');
  assert('SDE specialty change externally wired', typeof E.specialty.listeners.change==='function');
  assert('SDE service input externally wired', typeof E.eligibilitySchoolYears.listeners.input==='function');
  assert('SDE reset externally wired', typeof E.sdeDetachmentResetBtn.listeners.click==='function');
  assert('SDE custom language event preserved', customEvent && customEvent.type==='asep-language-change');
  E.specialty.value='PE86'; fire(E.specialty,'change');
  assert('PE86 still forces computer knowledge', E.computer.value==='yes' && E.computer.disabled===true);
  assert('PE86 still reveals math/informatics condition', !E.mathInfoDegreeWrap.classList.contains('hidden'));
  assert('SDE controller canonicalizes legacy PE86 to ΠΕ86', lastData && lastData.specialty==='ΠΕ86');
  assert('SDE total render still updates', E.totalScore.textContent==='1');
  E.eligibilitySchoolYears.value='2'; fire(E.eligibilitySchoolYears,'input');
  assert('SDE input changes still recalculate', calcCount>=3);
}

// 3) Paravola: actual one/two-paravolo and duplicate protection behavior.
{
  function select(id, firstText){ const e=element(id,'','SELECT'); e.options=[{value:'',textContent:firstText,disabled:false,dataset:{}}]; return e; }
  const E={specialty1:select('specialty1','-- Επιλογή --'),specialty2:select('specialty2','-- Καμία --'),duplicateWarning:element('duplicateWarning'),result:element('result')};
  global.document={getElementById:id=>E[id]||null,createElement:tag=>({value:'',textContent:'',disabled:false,dataset:{}})};
  delete require.cache[require.resolve('../includes/paravola-ui.js')]; require('../includes/paravola-ui.js');
  assert('paravola specialty 1 change wired', typeof E.specialty1.listeners.change==='function');
  assert('paravola specialty 2 change wired', typeof E.specialty2.listeners.change==='function');
  E.specialty1.value='ΠΕ60'; fire(E.specialty1,'change');
  assert('one group still needs one paravolo', E.result.innerHTML.includes('1 παράβολο') && E.result.innerHTML.includes('15 ευρώ'));
  E.specialty2.value='ΠΕ02'; fire(E.specialty2,'change');
  assert('different groups still need two paravola', E.result.innerHTML.includes('2 παράβολα') && E.result.innerHTML.includes('30 ευρώ'));
  E.specialty2.value='ΠΕ60'; fire(E.specialty2,'change');
  assert('duplicate specialty is still removed', E.specialty2.value==='' && E.duplicateWarning.textContent.includes('δεν μπορεί να επιλεγεί δύο φορές'));
}

// 4) Grade scale converter: actual decimal conversion, validation and print binding.
{
  const ids=['decimalTab','lexicalTab','decimalPanel','lexicalPanel','decimalGrade','lexicalGrade','decimalError','result','sourceSummary','copyStatus','grade10','grade20','int10','num10','int20','num20','points10','points20','copy20','resetBtn','printBtn'];
  const E={}; ids.forEach(id=>E[id]=element(id));
  E.result.classList=classList(['hidden']); E.lexicalPanel.classList=classList(['hidden']); E.decimalTab.classList=classList(['active']);
  E.lexicalGrade.tagName='SELECT'; E.lexicalGrade.options=[{text:''},{text:'ΚΑΛΩΣ (5)'},{text:'ΛΙΑΝ ΚΑΛΩΣ (6,5)'},{text:'ΑΡΙΣΤΑ (8,5)'}];
  let printCount=0; global.window=global; global.window.print=()=>{printCount++;};
  global.document={getElementById:id=>E[id]||null,createElement:()=>element('tmp'),body:{appendChild:()=>{}},execCommand:()=>true};
  delete require.cache[require.resolve('../includes/grade-scale-converter-ui.js')]; require('../includes/grade-scale-converter-ui.js');
  assert('scale decimal input wired', typeof E.decimalGrade.listeners.input==='function');
  assert('scale lexical tab wired', typeof E.lexicalTab.listeners.click==='function');
  assert('scale print externally wired', typeof E.printBtn.listeners.click==='function');
  E.decimalGrade.value='7,34'; fire(E.decimalGrade,'input');
  assert('7,34 converts to 14,68 / 20', E.grade20.textContent.includes('14,68'));
  assert('fraction fields keep 14 and 68', String(E.int20.textContent)==='14' && String(E.num20.textContent)==='68');
  assert('1GE points remain 18,35', E.points10.textContent.includes('18,35'));
  assert('1GT points remain 44,04', E.points20.textContent.includes('44,04'));
  E.decimalGrade.value='11'; fire(E.decimalGrade,'input');
  assert('out-of-range decimal still rejected', E.decimalError.style.display==='block' && E.result.classList.contains('hidden'));
  fire(E.printBtn,'click'); assert('print button still prints via external listener', printCount===1);
}

if(process.exitCode) process.exit(process.exitCode);
console.log('RESULT: 34/34 PASS');
