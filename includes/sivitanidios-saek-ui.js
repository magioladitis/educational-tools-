(function(){
'use strict';
const $=id=>document.getElementById(id);
const fmt=v=>(Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});
function data(){return {
  candidateType:$('candidateType').value, qualifyingTitle:$('qualifyingTitle').value, relatedPostgrad:$('relatedPostgrad').value,
  adultEducationPostgrad:$('adultEducationPostgrad').value, trainingSubjectHours:$('trainingSubjectHours').value, trainingVetHours:$('trainingVetHours').value, trainingAdultHours:$('trainingAdultHours').value,
  saekOutsideHours:$('saekOutsideHours').value, saekSivitanidiosHours:$('saekSivitanidiosHours').value, tertiaryTeachingYears:$('tertiaryTeachingYears').value, primarySecondaryTeachingYears:$('primarySecondaryTeachingYears').value, otherNonFormalHours:$('otherNonFormalHours').value,
  workMonths:$('workMonths').value, languageName:$('languageName').value, languageLevel:$('languageLevel').value, languageTeachingExcluded:$('languageTeachingExcluded').checked, languageArticle28Status:$('languageArticle28Status').value, languageArticle28:$('languageArticle28Status').value==='yes', languageTitleRegistered:$('languageTitleRegistered').checked, languageOfficialTranslation:$('languageOfficialTranslation').checked, computer:$('computer').checked, pe86:$('pe86').checked, adultTrainer:$('adultTrainer').checked,
  unemploymentBand:$('unemploymentBand').value, threeChildren:$('threeChildren').checked, manyChildren:$('manyChildren').checked, singleParent:$('singleParent').checked, disabilityCategory:$('disabilityCategory').checked
};}
function syncLanguageProof(d){
  const hasLevel=d.languageLevel && d.languageLevel!=='none';
  const hasLanguage=d.languageName && d.languageName!=='none';
  const named=['english','french','german','italian','spanish'].includes(d.languageName);
  $('languageProofBox').classList.toggle('hidden',!hasLevel);
  $('article28Row').classList.toggle('hidden',!(hasLevel&&hasLanguage&&named));
  const article28Status=d.languageArticle28Status||'';
  const translationRequired=hasLevel&&hasLanguage&&(!named||(named&&article28Status==='no'));
  $('translationChecks').classList.toggle('hidden',!translationRequired);
  let text='Επίλεξε τη γλώσσα για να ελεγχθούν τα δικαιολογητικά.', ok=false;
  if(hasLevel&&hasLanguage){
    if(named&&article28Status===''){text='ℹ Δήλωσε αν ο τίτλος πληροί αποκλειστικά τους όρους του άρθρου 28 του Π.Δ. 50/2001.';}
    else if(named&&article28Status==='yes'){text='✓ Δεν απαιτείται μετάφραση του τίτλου.';ok=true;}
    else if(d.languageTitleRegistered&&d.languageOfficialTranslation){text='✓ Έχουν δηλωθεί ο ξενόγλωσσος τίτλος και η επίσημη μετάφραση.';ok=true;}
    else{text='⚠ Απαιτούνται καταχώρηση του ξενόγλωσσου τίτλου και επίσημη μετάφραση.';}
  }
  $('languageProofStatus').textContent=text;
  $('languageProofStatus').classList.toggle('yes',ok);
}
function calc(){
  if(typeof window.SivitanidiosSaekCalc==='undefined'){
    const box=$('warningBox');
    if(box){box.classList.remove('hidden');box.textContent='⚠ Δεν φορτώθηκε ο υπολογιστικός μηχανισμός. Κάνε ανανέωση της σελίδας (Ctrl+F5) και, αν επιμένει, έλεγξε ότι έχει ανέβει το includes/sivitanidios-saek-calculations.js.';}
    return null;
  }
  const d=data(), r=SivitanidiosSaekCalc.calculateAll(d), graduate=d.candidateType==='graduate';
  syncLanguageProof(d);
  $('graduateOnly').classList.toggle('hidden',!graduate);
  $('craftNote').classList.toggle('hidden',d.candidateType!=='craft');
  $('workRuleNote').innerHTML=d.candidateType==='craft'?'Για εμπειροτέχνη τα πρώτα <strong>36 μήνες</strong> είναι προαπαιτούμενο και δεν μοριοδοτούνται. Δηλώνεται η συνολική εμπειρία· ο calculator αφαιρεί αυτόματα την τριετία.':'Για πτυχιούχο μοριοδοτείται η συναφής εργασιακή εμπειρία έως 10 έτη.';
  $('finalTotal').textContent=fmt(r.final); $('resBase').textContent=fmt(r.base)+(graduate?' / 60':' / 16');
  $('resEducation').textContent=fmt(r.education.points)+' / 23'; $('resTeaching').textContent=fmt(r.teaching.points)+' / 21'; $('resWork').textContent=fmt(r.work.points)+' / 10'; $('resOther').textContent=fmt(r.other.points)+' / 6';
  $('resSocial').textContent='+'+fmt(r.social.increase); $('resSocialPercent').textContent=fmt(r.social.totalPercent).replace(',00','')+'%';
  const st=r.eligibility; $('eligibilityBox').classList.toggle('yes',st.eligible); $('eligibilityBox').textContent=st.blockers.length?'⚠ '+st.blockers.join(' '):st.pending.length?'ℹ '+st.pending.join(' '):'✓ Τα βασικά στοιχεία της επιλεγμένης κατηγορίας έχουν επιβεβαιωθεί.';
  $('resultStatus').className='result-message edu-message '+(st.eligible?'result-message--success edu-message--success':'result-message--status edu-message--status'); $('resultStatus').textContent=st.eligible?'Υπολογισμός ολοκληρωμένος — έλεγξε και το προσοντολόγιο του μαθήματος.':'Ο υπολογισμός είναι προσωρινός μέχρι να συμπληρωθούν τα βασικά στοιχεία.';
  const warnings=(r.other.warnings||[]).slice(); if(d.candidateType==='craft'&&Number(d.workMonths||0)<36) warnings.push('Ο εμπειροτέχνης δεν συμπληρώνει την απαιτούμενη τριετή εμπειρία.');
  $('warningBox').classList.toggle('hidden',warnings.length===0); $('warningBox').textContent=warnings.join(' ');
  const lines=[]; r.education.details.forEach(x=>lines.push(x.label+': '+fmt(x.points))); r.teaching.details.forEach(x=>lines.push(x.label+': '+fmt(x.points)));
  if(r.work.points) lines.push('Εργασιακή εμπειρία: '+fmt(r.work.points)); if(r.other.languagePoints) lines.push('Ξένη γλώσσα: '+fmt(r.other.languagePoints)); if(r.other.computerPoints) lines.push('Η/Υ: 2,00'); if(r.other.adultTrainerPoints) lines.push('Πιστοποιημένος Εκπαιδευτής Ενηλίκων: 2,00');
  if(r.social.totalPercent) lines.push('Κοινωνική προσαύξηση: '+fmt(r.social.totalPercent).replace(',00','')+'% = +'+fmt(r.social.increase)); $('breakdownBox').innerHTML=lines.length?lines.map(x=>'<div>'+x.replace(/</g,'&lt;')+'</div>').join(''):'Δεν έχουν δηλωθεί ακόμη μοριοδοτούμενα κριτήρια.';
  return r;
}
$('languageName').addEventListener('change',()=>{
  $('languageArticle28Status').value='';
  $('languageTitleRegistered').checked=false;
  $('languageOfficialTranslation').checked=false;
});
function normalizeTrainingHours(el){
  const raw=String(el.value||'').trim();
  if(raw==='') return false;
  const value=Number(raw.replace(',','.'));
  if(Number.isFinite(value) && value>0 && value<25){
    el.value='';
    return true;
  }
  return false;
}
const scoreControls=Array.from(document.querySelectorAll('.app input, .app select'));
scoreControls.forEach(el=>{
  el.addEventListener('input',calc);
  el.addEventListener('change',()=>{
    let rejected=false;
    if(el.matches('[data-training-hours]')) rejected=normalizeTrainingHours(el);
    const warn=$('trainingHoursWarning');
    if(warn){
      warn.classList.toggle('hidden',!rejected);
      if(rejected) window.setTimeout(()=>warn.classList.add('hidden'),3500);
    }
    calc();
  });
});
$('copyBtn').addEventListener('click',async()=>{const r=calc(), text=['ΣΑΕΚ Σιβιτανιδείου 2026–2027','Βασική βαθμολογία: '+fmt(r.base),'Κοινωνική προσαύξηση: +'+fmt(r.social.increase)+' ('+fmt(r.social.totalPercent).replace(',00','')+'%)','Τελική βαθμολογία: '+fmt(r.final)].join('\n'); try{await navigator.clipboard.writeText(text); const old=$('copyBtn').textContent;$('copyBtn').textContent='Αντιγράφηκε ✓';setTimeout(()=>$('copyBtn').textContent=old,1400);}catch(e){alert(text);}});
$('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type="number"]').forEach(el=>el.value=el.matches('[data-training-hours]')?'':'0');document.querySelectorAll('input[type="checkbox"]').forEach(el=>el.checked=false);document.querySelectorAll('select').forEach(el=>el.selectedIndex=0);$('trainingHoursWarning').classList.add('hidden');calc();});
calc();
})();
