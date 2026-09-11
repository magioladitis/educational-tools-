
(function(){
 const $=id=>document.getElementById(id); const num=id=>Math.max(0,Number($(id)?.value||0)); const cap=(v,m)=>Math.min(v,m); const fmt=v=>(Math.round((v+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});
 function calcService(){return AsepServiceController.getState('asepService',fmt).points;}
 function calcSocial(){return AsepSocialCriteria.getState('socialCriteria',fmt);}
 function render(){
   TrainingProof.syncAll();
   const degreeGrade=num('degreeGrade');
   const degreeInvalid=degreeGrade>0 && (degreeGrade<5 || degreeGrade>10);
   $('degreeValidation').classList.toggle('hidden', !degreeInvalid);
   const academic=AsepPeAcademic.calculate('asepPeAcademic'), a=academic.points, b=calcService(), socialResult=calcSocial(), c=socialResult.points, t=a+b+c, e=AsepEaeEligibility.getState('eaeEligibility',{socialResult:socialResult});
   $('grandTotal').textContent=fmt(t); $('resAcademic').textContent=fmt(a)+' / 120'; $('resService').textContent=fmt(b)+' / 120'; $('resSocial').textContent=fmt(c);
   $('tableStatus').className='result-message edu-message '+(e.type==='main'||e.type==='aux'?'result-message--success edu-message--success':e.type==='none'&&e.label!=='Επίλεξε κλάδο'?'result-message--warning edu-message--warning':'result-message--status edu-message--status'); $('tableStatus').textContent=e.label; $('eligibilityWhy').innerHTML='<strong>Έλεγχος ένταξης</strong>'+e.why;
   let p=[]; if($('pedagogical').checked) p.push('Πρόταξη λόγω Παιδαγωγικής & Διδακτικής Επάρκειας'); p=p.concat(EaeSensoryProof.priorityLabels());
   $('priorities').innerHTML=p.map(x=>'<div class="result-message edu-message result-message--success edu-message--success">✓ '+x+'</div>').join('');
   return {a,b,c,t,e,p};
 }
 function summary(v){return ['Υπολογισμός μορίων 3ΕΑ/2025',`Πίνακας: ${v.e.label}`,v.e.why,`Ακαδημαϊκά: ${fmt(v.a)} / 120`,`Προϋπηρεσία: ${fmt(v.b)} / 120`,`Κοινωνικά: ${fmt(v.c)}`,`ΣΥΝΟΛΟ: ${fmt(v.t)}`,AsepDigitalTutoring.summary('digitalTutoring',fmt),v.p.length?'Προτάξεις/προτεραιότητες: '+v.p.join(' · '):'',AsepPedagogicalProof.summary('pedagogical'),EaeSensoryProof.summary(),TrainingProof.summary('trainingProof')].filter(Boolean).join('\n');}
 document.addEventListener('input',render);
 document.addEventListener('change',render);
 $('copyBtn').addEventListener('click',async()=>{const txt=summary(render());try{await navigator.clipboard.writeText(txt);$('copyBtn').textContent='Αντιγράφηκε ✓';setTimeout(()=>$('copyBtn').textContent='Αντιγραφή',1200)}catch(e){alert(txt)}});
 document.addEventListener('asep-digital-tutoring-change',render);
 $('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type=number]').forEach(x=>x.value=0);$('degreeGrade').value='';document.querySelectorAll('input[type=text]').forEach(x=>x.value='');document.querySelectorAll('input[type=checkbox]').forEach(x=>x.checked=false);document.querySelectorAll('input[name="trainingProofDates"]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);AsepPeAcademic.reset('asepPeAcademic',{silent:true});AsepServiceController.reset('asepService',{silent:true});AsepPedagogicalProof.reset('pedagogical');EaeSensoryProof.reset();render();});
 render();
})();
