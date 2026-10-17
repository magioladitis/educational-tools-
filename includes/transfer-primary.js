/* Isolated PRIMARY general, EAE and KEDASY transfer UI. Shared scoring module is unchanged. */
(function (global) {
  'use strict';
  var scoring = global.EducationTransfer;
  var eae = global.EducationTransferPrimaryEae;
  if (!scoring || !eae) return;
  var $ = function (id) { return document.getElementById(id); };
  if (!$('primaryMode')) return;
  var checkedDocs = {};
  function escapeText(s) { return String(s).replace(/[&<>"']/g, function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }
  function format(n) { return Number(n || 0).toLocaleString('el-GR', { maximumFractionDigits: 2 }); }
  function action(id,title,desc) { return {id:id, title:title,desc:desc,channel:'email',action:true}; }
  function info(id,title,desc) { return {id:id, title:title,desc:desc,channel:'check',action:false}; }
  function mydocs(id,title,desc) { return {id:id, title:title,desc:desc,channel:'mydocs',action:true}; }
  function buildDocuments() {
    var mode=$('primaryMode').value;
    var docs=[info('registry','Υπηρεσιακό μητρώο και μονάδες','Έλεγχος στο επίσημο μητρώο — χωρίς αποστολή')];
    if (Number($('primaryChildren').value)>0 || $('primaryFamily').checked) {
      docs.push(info('family','Πιστοποιητικό οικογενειακής κατάστασης','Αυτεπάγγελτα από ΔΠΕ — δεν το προσκομίζετε'));
    }
    if ($('primaryChildrenExtra').checked && Number($('primaryChildren').value)>0) docs.push(action('children','Συμπληρωματικά στοιχεία τέκνων','Email στη ΔΠΕ οργανικής — μόνο όπου δεν επαρκεί η αυτεπάγγελτη αναζήτηση / απαιτείται ειδικό αποδεικτικό'));
    if ($('primaryCoservice').checked) docs.push(action('coservice','Δικαιολογητικά συνυπηρέτησης','Αποστολή με email στη ΔΠΕ οργανικής — όπου απαιτούνται αποδεικτικά'));
    if ($('primaryLocality').checked) docs.push(info('locality','Εντοπιότητα','Αυτεπάγγελτη αναζήτηση πιστοποιητικού δημοτικότητας/γέννησης από ΔΠΕ, όπου περιέχεται η ημερομηνία κτήσης δημοτικότητας· επιβεβαιώστε τα στοιχεία'));
    if(mode!=='region') {
      var origin=$('primaryEaeOrigin').value;
      var dossier=$('primaryEaeDossier').value;
      var options=[['phd','primaryEaePhd'],['masters','primaryEaeMasters'],['retraining','primaryEaeRetraining'],['experience','primaryEaeExperience'],['pe11','primaryEaePe11']];
      var chosen=options.filter(function(pair){return $(pair[1]).checked;});
      var labels={phd:'Διδακτορικός τίτλος ΕΑΕ και τεκμηρίωση συνάφειας',masters:'Μεταπτυχιακός τίτλος ΕΑΕ και τεκμηρίωση συνάφειας',retraining:'Πτυχίο διετούς μετεκπαίδευσης ΕΑΕ',pe11:'Πτυχίο ΤΕΦΑΑ με κύρια ειδικότητα ΕΑΕ'};
      if(origin==='appointed-eae') {
        docs.push(info('appointed-eae','Διορισμός στην ΕΑΕ — υπηρεσιακή διετία','Έλεγχος από ΔΠΕ / υπηρεσιακό φάκελο — όχι αυτοτελής αποστολή'));
      }
      if(origin==='general' || origin==='transferred-eae') {
        if(chosen.length && dossier==='unknown') {
          docs.push(info('eae-dossier','Προηγούμενος φάκελος προσόντων ΕΑΕ','Ελέγξτε αν υπάρχει φάκελος μετάθεσης 2025–26 ή απόσπασης ΕΑΕ 2026· επανυποβάλλονται μόνο νέα/συμπληρωματικά στοιχεία.'));
        }
        if(chosen.length && dossier==='previous') {
          docs.push(info('eae-previous-dossier','Υφιστάμενος φάκελος προσόντων ΕΑΕ','Δεν απαιτείται νέα υποβολή ήδη κατατεθειμένων δικαιολογητικών χωρίς νέα στοιχεία.'));
        }
        chosen.forEach(function(pair){
          var key=pair[0];
          if(key==='experience'){
            docs.push(info('eae-experience','Πενταετής διδακτική υπηρεσία ΕΑΕ','Διαπίστωση υπηρεσίας από το αρμόδιο τμήμα του ΥΠΑΙΘΑ βάσει αποδεικτικών στοιχείων· όχι αυτόματη αποστολή με email.'));
            return;
          }
          if(dossier==='new') {
            var note= key==='phd'?'Για μη ήδη αναγνωρισμένη συνάφεια ενδέχεται να χρειάζεται περίληψη διατριβής.':key==='masters'?'Για μη ήδη αναγνωρισμένη συνάφεια ενδέχεται να απαιτείται αναλυτικό πρόγραμμα και περίληψη εργασίας.':'Ελέγξτε τυχόν απαιτούμενη αναγνώριση ξένου τίτλου.';
            docs.push(mydocs('eae-'+key, labels[key], 'Υποβολή μέσω mydocs — πρώτη υποβολή ή νέα/συμπληρωματικά στοιχεία. '+note));
          }
        });
      }
      docs.push(info('eae-review','Υπηρεσιακή επαλήθευση ΕΑΕ / ΚΕΔΑΣΥ','Οι προϋποθέσεις και τα πραγματικά οργανικά κενά ελέγχονται από τα αρμόδια υπηρεσιακά όργανα.'));
    }
    return docs;
  }
  function updateCheckList(docs) {
    var checklist=$('primaryChecklist');
    checklist.innerHTML=docs.map(function(d){return '<div class="primary-doc-row" data-primary-doc="'+escapeText(d.id)+'">' +
      (d.action ? '<label><input type="checkbox" data-primary-check="'+escapeText(d.id)+'"'+(checkedDocs[d.id]?' checked':'')+'><strong>'+escapeText(d.title)+'</strong></label>' : '<div><strong>ⓘ '+escapeText(d.title)+'</strong></div>') +
      '<p class="primary-doc-kind">'+escapeText(d.desc)+'</p></div>';}).join('');
    var actionable=docs.filter(function(d){return d.action;});
    Object.keys(checkedDocs).forEach(function(k){ if (!actionable.some(function(d){return d.id===k;})) delete checkedDocs[k]; });
    var completed=actionable.filter(function(d){return checkedDocs[d.id];}).length;
    $('primaryChecklistProgress').textContent= completed+' από '+actionable.length+' ενέργειες δικαιολογητικών έχουν ολοκληρωθεί';
    updateMail(docs);
  }
  function numeric(el,max){
    var raw=el.value;
    if(raw==='')return 0;
    var n=Number(raw);
    if(!Number.isFinite(n)|| n<0 || n>max || !Number.isInteger(n))return null;
    return n;
  }
  function qualifications(){
    return {phd:$('primaryEaePhd').checked,masters:$('primaryEaeMasters').checked,
      retraining:$('primaryEaeRetraining').checked,experience:$('primaryEaeExperience').checked,pe11:$('primaryEaePe11').checked};
  }
  function updateEaePrecheck(){
    var mode=$('primaryMode').value;
    $('primaryEaeFields').hidden=mode==='region';
    $('primaryGeneralOrganicField').hidden=mode!=='region';
    $('primaryEligibility').hidden=mode!=='region';
    $('primaryKedasyGeoNote').hidden=mode!=='kedasy';
    if(mode==='region')return;
    var origin=$('primaryEaeOrigin').value;
    $('primaryEaeGeneralYearsField').hidden=origin!=='general';
    $('primaryEaeAppointmentYearsField').hidden=origin!=='appointed-eae';
    $('primaryEaeYearsField').hidden=!$('primaryEaeExperience').checked;
    $('primaryPreviousDossierField').hidden=origin==='appointed-eae';
    var data={mode:mode,branch:$('primaryEaeBranch').value,origin:origin,
      organic:$('primaryEaeOrganic').value,generalYears:$('primaryEaeGeneralYears').value,
      appointmentAreaYears:$('primaryEaeAppointmentYears').value,eaeYears:$('primaryEaeYears').value,
      qualifications:qualifications()};
    var outcome=eae.evaluate(data);
    var labels={problem:'⚠ Δεν πληρούται ελεγχόμενη προϋπόθεση',review:'ⓘ Απαιτούνται στοιχεία ή υπηρεσιακή επαλήθευση',positive:'✓ Θετικός προκαταρκτικός έλεγχος'};
    $('primaryEaeEligibility').setAttribute('data-status',outcome.status);
    var list=outcome.problems.concat(outcome.checks);
    if(!list.length)list=outcome.positive;
    $('primaryEaeEligibility').innerHTML='<strong>'+escapeText(labels[outcome.status])+'</strong>'+
      (list.length?'<ul>'+list.map(function(x){return '<li>'+escapeText(x)+'</li>';}).join('')+'</ul>':'');
  }
  function update() {
    updateEaePrecheck();
    var mode=$('primaryServiceMode').value;
    $('primaryServicePointsPanel').hidden=mode!=='points';
    $('primaryServiceDurationPanel').hidden=mode!=='direct';
    var children=numeric($('primaryChildren'),20),years=numeric($('primaryYears'),50), months=numeric($('primaryMonths'),11), days=numeric($('primaryDays'),29);
    if (children === 0) { $('primaryChildrenExtra').checked = false; $('primaryChildrenExtra').disabled = true; }
    else $('primaryChildrenExtra').disabled = false;
    var numericValid=[children,years,months,days].every(function(v){return v!==null;});
    var raw={mode:'region',serviceMode:mode,serviceDirectPoints:$('primaryServicePoints').value,
      serviceYears:years,serviceMonths:months,serviceDays:days,msdMode:'direct',msdDirectPoints:$('primaryMsd').value,
      eligibleChildren:children,familyStatusEligible:$('primaryFamily').checked,coService:$('primaryCoservice').checked,
      locality:$('primaryLocality').checked,firstPreference:$('primaryFirst').checked};
    // No empty-as-zero interpretation for official-register inputs.
    if (!$('primaryMsd').value.trim() || (mode==='points' && !$('primaryServicePoints').value.trim())) numericValid=false;
    var result=numericValid?scoring.calculate(raw):null;
    var valid=!!result && result.serviceInputValid && result.msdInputValid;
    $('primaryTotal').textContent=valid?format(result.total):'—';
    $('primaryTotalStatus').textContent=valid?'Πληροφοριακό αποτέλεσμα — ελέγξτε τα κριτήρια και το επίσημο μητρώο.':'Συμπληρώστε έγκυρα στοιχεία (έως 2 δεκαδικά, Μ.Σ.Δ. έως 1.000 και προϋπηρεσία έως 125).';
    $('primaryValidation').textContent=!valid?'Δεν εμφανίζεται σύνολο όσο λείπουν ή είναι άκυρα βασικά αριθμητικά πεδία.':'';
    $('primaryBreakdown').innerHTML=valid?[
      ['Συνολική υπηρεσία',result.servicePoints],['Μονάδες συνθηκών διαβίωσης',result.msdPoints],
      ['Συνυπηρέτηση',result.coServicePoints],['Οικογενειακοί λόγοι',result.familyPoints],
      ['Εντοπιότητα',result.localityPoints],['Πρώτη προτίμηση',result.firstPreferencePoints]
    ].map(function(x){return '<p>'+escapeText(x[0])+': <strong>'+escapeText(format(x[1]))+'</strong></p>';}).join(''):'';
    var state=$('primaryOrganic').value;
    $('primaryEligibility').textContent=state==='yes'
      ? '✓ Δηλώθηκε συμπλήρωση έτους οργανικής. Ο οριστικός έλεγχος και οι ειδικές εξαιρέσεις ανήκουν στη ΔΠΕ.'
      : state==='no'
        ? '⚠ Η μη συμπλήρωση έτους οργανικής απαιτεί ειδικό υπηρεσιακό έλεγχο· ενδέχεται να ισχύουν νόμιμες εξαιρέσεις. Μην θεωρείτε την αίτηση αυτομάτως επιλέξιμη.'
        : 'ⓘ Ελέγξτε την υπηρεσία στην οργανική έως 31/08/2027 και τις ενδεχόμενες εξαιρέσεις της εγκυκλίου.';
    updateCheckList(buildDocuments());
  }
  function messageParts(docs) {
    var actions=docs.filter(function(d){return d.action && d.channel==='email';});
    var name=$('primaryName').value.trim()||'[Ονοματεπώνυμο]';
    var specialty=$('primarySpecialty').value.trim()||($('primaryMode').value!=='region'&&$('primaryEaeBranch').value)||'[Κλάδος/ειδικότητα]';
    var registry=$('primaryRegistry').value.trim()||'[Αριθμός μητρώου]';
    var protocol=$('primaryProtocol').value.trim()||'[Αριθμός πρωτοκόλλου]';
    var dpe=$('primaryDpe').value.trim()||'[ΔΠΕ οργανικής]';
    var transferType=$('primaryMode').value==='kedasy'?'Μετάθεση σε ΚΕΔΑΣΥ':$('primaryMode').value==='eae'?'Μετάθεση σε περιοχή ΕΑΕ':'Μετάθεση από περιοχή σε περιοχή';
    var subject='ΠΕ, '+transferType+', '+protocol+', '+registry+', '+specialty;
    var body='Προς '+dpe+'\n\nΣας αποστέλλω τα απαιτούμενα δικαιολογητικά για την αίτηση γενικής μετάθεσης Πρωτοβάθμιας Εκπαίδευσης.\n\n'+
      'Ονοματεπώνυμο: '+name+'\nΚλάδος/ειδικότητα: '+specialty+'\nΤύπος αίτησης: '+transferType+'\n'+
      'Αριθμός πρωτοκόλλου: '+protocol+'\nΑριθμός μητρώου: '+registry+'\nΔΠΕ οργανικής: '+dpe+'\n\n'+
      'Επισυναπτόμενα (ελέγξτε ότι τα αντίστοιχα αρχεία έχουν πράγματι επισυναφθεί):\n'+
      actions.map(function(d){return '• '+d.title;}).join('\n')+'\n\nΜε εκτίμηση,\n'+name;
    return {subject:subject,body:body,actions:actions};
  }
  function updateMail(docs){
    var email=messageParts(docs);
    var hasActions=email.actions.length>0;
    var mydocActions=docs.filter(function(d){return d.action && d.channel==='mydocs';});
    $('primaryMydocsLink').hidden=$('primaryMode').value==='region';
    $('primaryEmailHeading').textContent=(hasActions || $('primaryMode').value==='region')?'Έτοιμο email υποβολής δικαιολογητικών':'Πού υποβάλλονται τα δικαιολογητικά ΕΑΕ';
    var hint=$('primaryMydocsNotice');
    hint.hidden=!mydocActions.length;
    hint.textContent=mydocActions.length?'Έχετε '+mydocActions.length+' δικαιολογητικό/ά προσόντων ΕΑΕ για υποβολή στο mydocs.minedu.gov.gr (όχι με email). Δεν αποστέλλονται εκ νέου ήδη κατατεθειμένα στοιχεία.':'';
    $('primaryEmailOpen').hidden=($('primaryMode').value!=='region' && !hasActions);
    if($('primaryEmailOpen').hidden){ $('primaryEmailDetails').hidden=true; $('primaryEmailOpen').setAttribute('aria-expanded','false'); }
    $('primaryEmailLead').textContent=hasActions?'Έχετε δικαιολογητικά προς πιθανή αποστολή με email στη ΔΠΕ οργανικής. Ετοιμάστε θέμα και κείμενο, ελέγχοντας τα απαραίτητα συνημμένα.':$('primaryMode').value==='region'?'Δεν δηλώθηκαν δικαιολογητικά προς αποστολή. Η επιλογή δημιουργίας μηνύματος παραμένει διαθέσιμη για προεπισκόπηση, αλλά δεν προτείνεται αποστολή email.':'Δεν δηλώθηκαν δικαιολογητικά μοριοδότησης προς email. Για προσόντα ΕΑΕ ακολουθήστε τη διαδικασία mydocs, εφόσον απαιτείται νέος ή συμπληρωματικός φάκελος.';
    $('primaryEmailSubject').value=email.subject;
    $('primaryEmailBody').value=email.body;
    $('primaryOpenMail').style.display=hasActions?'inline-flex':'none';
    $('primaryOpenMail').href=hasActions?'mailto:?subject='+encodeURIComponent(email.subject)+'&body='+encodeURIComponent(email.body):'#';
  }
  $('primaryChecklist').addEventListener('change',function(e){
    if(e.target && e.target.matches('[data-primary-check]')){
      checkedDocs[e.target.getAttribute('data-primary-check')]=e.target.checked;
      update();
    }
  });
  document.querySelectorAll('input,select').forEach(function(el){
    if(el.closest('#primaryEmailDetails'))return;
    el.addEventListener('input',update);
    el.addEventListener('change',update);
  });
  ['primaryName','primarySpecialty','primaryRegistry','primaryProtocol','primaryDpe'].forEach(function(id){
    $(id).addEventListener('input',function(){updateMail(buildDocuments());});
  });
  $('primaryEmailOpen').addEventListener('click',function(){
    var panel=$('primaryEmailDetails'); panel.hidden=!panel.hidden;
    this.setAttribute('aria-expanded',String(!panel.hidden));
    this.textContent=panel.hidden?'Δημιουργία μηνύματος υποβολής ↓':'Απόκρυψη μηνύματος ↑';
  });
  $('primaryCopyEmail').addEventListener('click',function(){
    var text=$('primaryEmailSubject').value+'\n\n'+$('primaryEmailBody').value;
    if(navigator.clipboard&&navigator.clipboard.writeText) navigator.clipboard.writeText(text).then(function(){ $('primaryCopyEmail').textContent='Αντιγράφηκε ✓';},function(){$('primaryCopyEmail').textContent='Αδυναμία αντιγραφής';});
    else {$('primaryEmailBody').focus();$('primaryEmailBody').select();$('primaryCopyEmail').textContent='Επιλέχθηκε το κείμενο — αντιγράψτε το';}
  });
  update();
}(typeof window!=='undefined'?window:this));
