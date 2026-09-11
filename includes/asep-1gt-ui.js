
(function(){
  "use strict";
  const $=id=>document.getElementById(id);
  const num=id=>Math.max(0,Number($(id)?.value||0));
  const intNum=id=>Math.max(0,Math.floor(Number($(id)?.value||0)));
  const fmt=v=>(Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});


  function socialResult(){return AsepSocialCriteria.getState('socialCriteria',fmt);}
  function serviceResult(){return AsepServiceController.getState('asepService',fmt);}

  function calc(){
    const academic=AsepTeAcademic.getState('asepTeAcademic',fmt);
    const academicResult=academic.result, languages=academic.languages;
    const service=serviceResult(),social=socialResult();
    const total=academicResult.points+service.points+social.points;
    $('grandTotal').textContent=fmt(total);$('resAcademic').textContent=`${fmt(academicResult.points)} / 120`;$('resService').textContent=`${fmt(service.points)} / 120`;$('resSocial').textContent=fmt(social.points);
    $('resDegree').textContent=fmt(academicResult.degreePoints);$('resLanguage').textContent=fmt(languages.points);$('resChildren').textContent=fmt(social.childrenPoints);$('resDisability').textContent=fmt(social.disabilityPoints);


    const ped=$('pedagogical').checked;const pb=$('priorityBox');pb.className='result-message edu-message '+(ped?'result-message--success edu-message--success':'result-message--status edu-message--status');pb.textContent=ped?'ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής & Διδακτικής Επάρκειας':'Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.';
    return{academic:academicResult,service,social,languages,total,ped};
  }

  function languageSummary(v){const item=v.languages.accepted[0];return item?`${item.label} — ${fmt(v.languages.points)} μόρια`:'δεν δηλώθηκε';}
  function summary(v){return[
    'Υπολογισμός μορίων 1ΓΤ/2024',`Σύνολο: ${fmt(v.total)}`,`Ακαδημαϊκά: ${fmt(v.academic.points)} / 120`,`Προϋπηρεσία: ${fmt(v.service.points)} / 120`,
    `Κοινωνικά: ${fmt(v.social.points)}`,`Ξένη γλώσσα: ${languageSummary(v)}`,AsepPedagogicalProof.summary('pedagogical'),
    AsepTeAcademic.trainingSummary('asepTeAcademic'),'','Ενδεικτικός υπολογισμός βάσει της Προκήρυξης ΑΣΕΠ 1ΓΤ/2024.'
  ].filter((x,i,a)=>x!==''||a[i-1]!=='').join('\n');}

  document.addEventListener('input',calc);document.addEventListener('change',calc);
  $('resetBtn').addEventListener('click',()=>{
    document.querySelectorAll('input[type="number"]').forEach(el=>el.value='0');$('degreeGrade').value='';
    document.querySelectorAll('input[type="text"]').forEach(el=>el.value='');document.querySelectorAll('input[type="checkbox"],input[type="radio"]').forEach(el=>el.checked=false);
    $('specialty').value='te01';AsepServiceController.reset('asepService',{silent:true});AsepTeAcademic.reset('asepTeAcademic',{silent:true});AsepPedagogicalProof.reset('pedagogical');calc();
  });
  $('copyBtn').addEventListener('click',async()=>{const text=summary(calc());try{await navigator.clipboard.writeText(text);const old=$('copyBtn').textContent;$('copyBtn').textContent='Αντιγράφηκε ✓';setTimeout(()=>$('copyBtn').textContent=old,1400);}catch(e){alert(text);}});
  AsepTeAcademic.sync('asepTeAcademic');calc();
})();
