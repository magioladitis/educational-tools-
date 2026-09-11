(function(){
  'use strict';
  const $ = id => document.getElementById(id);
  const fmt = EducationCore.formatPoints;

  function specialtyLabel(){
    const option=$('specialty').selectedOptions[0];
    return option&&option.value?option.textContent.trim():'ειδικότητα μη επιλεγμένη';
  }

  function basicEligible(){return !!$('specialty').value && $('requiredThreeYearExperience').checked;}
  function service(){return AsepServiceController.getState('asepService',fmt);}
  function social(){return AsepSocialCriteria.getState('socialCriteria',fmt);}

  function sanitizeLocalInteger(el){
    if(!el || el.value==='') return;
    if(el.id!=='eaeMonths' && el.id!=='children') return;
    let value=Math.max(0,Math.floor(Number(el.value)||0));
    const max=el.getAttribute('max');
    if(max!==null&&max!=='') value=Math.min(value,Number(max));
    el.value=String(value);
  }

  function calc(){
    TrainingProof.syncAll();
    AsepPedagogicalProof.syncAll();
    EaeSensoryProof.syncAll();
    const academic=AsepDeAcademic.getState('asepDeAcademic',fmt);
    const serviceResult=service();
    const socialResult=social();
    const eligibility=AsepEaeEligibility.getState('eaeEligibility',{socialResult:socialResult});
    const basic=basicEligible();
    const total=academic.result.points+serviceResult.points+socialResult.points;

    $('grandTotal').textContent=fmt(total);
    $('resAcademic').textContent=fmt(academic.result.points)+' / 120';
    $('resDegree').textContent=fmt(academic.result.degreePoints)+' / 50';
    $('resWork').textContent=fmt(academic.result.workExperiencePoints)+' / 20';
    $('resLanguage').textContent=fmt(academic.languages.points)+' / 20';
    $('resService').textContent=fmt(serviceResult.points)+' / 120';
    $('resSocial').textContent=fmt(socialResult.points);
    $('resBasic').textContent=basic?'Επιβεβαιώθηκε':'Δεν επιβεβαιώθηκε';
    $('resTable').textContent=eligibility.label;

    $('basicEligibilityStatus').classList.toggle('yes',basic);
    $('basicEligibilityStatus').textContent=basic
      ?'✓ Δηλώθηκε ειδικότητα και επιβεβαιώθηκε η υποχρεωτική τριετής επαγγελματική πείρα.'
      :'⚠ Για συμμετοχή απαιτούνται επιλεγμένη ειδικότητα και υποχρεωτική τριετής επαγγελματική πείρα μετά την κτήση του τίτλου.';

    $('tableStatus').classList.toggle('yes',eligibility.code==='main'||eligibility.code==='aux');
    $('tableStatus').textContent=eligibility.label;
    $('eligibilityWhy').textContent=eligibility.why;

    const priorities=[];
    if($('pedagogical').checked) priorities.push('ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής και Διδακτικής Επάρκειας');
    priorities.push.apply(priorities,EaeSensoryProof.priorityLabels());
    $('priorityBox').className='result-message edu-message '+(priorities.length?'result-message--success edu-message--success':'result-message--status edu-message--status');
    $('priorityBox').textContent=priorities.length?priorities.join(' · '):'Χωρίς δηλωμένη ειδική πρόταξη / προτεραιότητα';

    return {academic,serviceResult,socialResult,eligibility,basic,total,priorities};
  }

  function summary(v){
    return EducationCore.summaryLines([
      'Υπολογισμός μορίων 5ΕΑ/2022 — '+specialtyLabel(),
      'Σύνολο: '+fmt(v.total),
      'Ακαδημαϊκά: '+fmt(v.academic.result.points)+' / 120',
      'Βαθμός τίτλου: '+fmt(v.academic.result.degreePoints)+' / 50',
      'Πρόσθετη επαγγελματική εμπειρία: '+fmt(v.academic.result.workExperiencePoints)+' / 20',
      'Ξένη γλώσσα: '+fmt(v.academic.languages.points)+' / 20',
      'Προϋπηρεσία: '+fmt(v.serviceResult.points)+' / 120',
      'Κοινωνικά: '+fmt(v.socialResult.points),
      'Βασικό τυπικό προσόν (υποχρεωτική 3ετία): '+(v.basic?'ΝΑΙ':'ΟΧΙ / ΔΕΝ ΕΠΙΒΕΒΑΙΩΘΗΚΕ'),
      'Πίνακας Ε.Α.Ε.: '+v.eligibility.label,
      v.eligibility.why,
      AsepPedagogicalProof.summary('pedagogical'),
      EaeSensoryProof.summary(),
      AsepDeAcademic.trainingSummary('asepDeAcademic')
    ]);
  }

  document.addEventListener('input',e=>{sanitizeLocalInteger(e.target);calc();});
  document.addEventListener('change',e=>{sanitizeLocalInteger(e.target);calc();});

  $('copyBtn').addEventListener('click',async()=>{
    const text=summary(calc());
    try{
      await navigator.clipboard.writeText(text);
      const old=$('copyBtn').textContent;
      $('copyBtn').textContent='Αντιγράφηκε ✓';
      setTimeout(()=>$('copyBtn').textContent=old,1400);
    }catch(e){alert(text);}
  });

  $('resetBtn').addEventListener('click',()=>{
    EducationCore.resetControls(document,{numberValue:'0',textValue:''});
    $('degreeGrade').value='';
    $('specialty').value='';
    $('mainCriterion').value='none';
    AsepServiceController.reset('asepService',{silent:true});
    AsepDeAcademic.reset('asepDeAcademic',{silent:true});
    AsepEaeEligibility.reset('eaeEligibility',{silent:true});
    AsepPedagogicalProof.reset('pedagogical');
    EaeSensoryProof.reset();
    calc();
  });

  calc();
})();
