(function(){
  "use strict";
  const $ = id => document.getElementById(id);
  const fmt = EducationCore.formatPoints;

  function branchFamily(){
    const value = $('specialty').value;
    if(value === 'ΤΕ16') return 'ΤΕ16';
    if(value.startsWith('ΤΕ01')) return 'ΤΕ01';
    if(value.startsWith('ΤΕ02')) return 'ΤΕ02';
    return '';
  }

  function selectedBranchLabel(){
    const option = $('specialty').selectedOptions[0];
    return option && option.value ? option.textContent.trim() : 'κλάδος/ειδικότητα μη επιλεγμένος/η';
  }


  function socialResult(){return AsepSocialCriteria.getState('socialCriteria',fmt);}

  function serviceResult(){return AsepServiceController.getState('asepService',fmt);}

  function calc(){
    const academic=AsepTeAcademic.getState('asepTeAcademic',fmt);
    const academicResult=academic.result, languages=academic.languages;
    const service=serviceResult();
    const social=socialResult();

    const eligibility=AsepEaeEligibility.getState('eaeEligibility',{socialResult:social});
    const tableCode=eligibility.code, tableLabel=eligibility.label, why=eligibility.why;

    const total = academicResult.points + service.points + social.points;

    $('grandTotal').textContent=fmt(total);
    $('resAcademic').textContent=`${fmt(academicResult.points)} / 120`;
    $('resService').textContent=`${fmt(service.points)} / 120`;
    $('resSocial').textContent=fmt(social.points);
    $('resDegree').textContent=fmt(academicResult.degreePoints);
    $('resLanguage').textContent=fmt(languages.points);
    $('resChildren').textContent=fmt(social.childrenPoints);
    $('resDisability').textContent=fmt(social.disabilityPoints);
    $('resTable').textContent=tableLabel;


    $('tableStatus').classList.toggle('yes',tableCode==='main'||tableCode==='aux');
    $('tableStatus').textContent=tableLabel;
    $('eligibilityWhy').textContent=why;

    const ped=$('pedagogical').checked;
    const priorities=[];
    if(ped) priorities.push('ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής και Διδακτικής Επάρκειας');
    priorities.push.apply(priorities,EaeSensoryProof.priorityLabels());
    $('priorityBox').className='result-message edu-message '+(priorities.length?'result-message--success edu-message--success':'result-message--status edu-message--status');
    $('priorityBox').textContent=priorities.length?priorities.join(' · '):'Χωρίς δηλωμένη ειδική πρόταξη / προτεραιότητα';

    return {academicResult,languages,service,social,total,ped,tableCode,tableLabel,why,priorities};
  }

  function summary(v){
    return EducationCore.summaryLines([
      `Υπολογισμός μορίων 4ΕΑ/2025 — ${selectedBranchLabel()}`,
      `Σύνολο: ${fmt(v.total)}`,
      `Ακαδημαϊκά: ${fmt(v.academicResult.points)} / 120`,
      `Ξένη γλώσσα: ${fmt(v.languages.points)}`,
      `Προϋπηρεσία: ${fmt(v.service.points)} / 120`,
      `Κοινωνικά: ${fmt(v.social.points)}`,
      `Πίνακας Ε.Α.Ε.: ${v.tableLabel}`,
      v.why,
      AsepPedagogicalProof.summary('pedagogical'),
      v.priorities.length?'Προτεραιότητες: '+v.priorities.join(' · '):'',
      EaeSensoryProof.summary(),
      AsepTeAcademic.trainingSummary('asepTeAcademic')
    ]);
  }

  function sanitizeIntegerInput(el){
    if(!el) return;
    const ids=['eaeMonths','children'];
    if(!ids.includes(el.id) || el.value==='') return;
    let value=Math.max(0,Math.floor(Number(el.value)||0));
    const max=el.getAttribute('max');
    if(max!==null && max!=='') value=Math.min(value,Number(max));
    el.value=String(value);
  }

  document.addEventListener('input',e=>{sanitizeIntegerInput(e.target);calc();});
  document.addEventListener('change',e=>{
    sanitizeIntegerInput(e.target);
    calc();
  });


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
    AsepTeAcademic.reset('asepTeAcademic',{silent:true});
    AsepPedagogicalProof.reset('pedagogical');
    EaeSensoryProof.reset();
    calc();
  });

  AsepTeAcademic.sync('asepTeAcademic');
  calc();
})();
