/* Isolated PRIMARY general, EAE and special structure transfer UI. Shared scoring module is unchanged. */
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
      if(mode==='deaf' || mode==='blind') {
        var certificate = $('primaryEaeCertification').value;
        var certificateDossier = $('primaryEaeCertificationDossier').value;
        var certName = mode==='deaf'?'Ελληνικής Νοηματικής Γλώσσας (ΕΝΓ)':'γραφής Braille';
        if(certificate==='yes' && certificateDossier==='new') {
          docs.push(mydocs('eae-certificate-'+mode, 'Αποδεικτικό πιστοποιημένης γνώσης '+certName, 'Υποβολή μέσω mydocs — μόνο αν απαιτείται νέος ή συμπληρωματικός φάκελος.'));
        } else if(certificate==='yes' && certificateDossier==='unknown') {
          docs.push(info('eae-cert-check-'+mode, 'Αποδεικτικό γνώσης '+certName, 'Ελέγξτε αν το έγκυρο αποδεικτικό έχει ήδη υποβληθεί· όχι αυτόματη επανυποβολή.'));
        }
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
    var isCertificate=mode==='deaf'||mode==='blind';
    $('primaryEaeCertificationField').hidden=!isCertificate;
    $('primaryEaeCertificationDossierField').hidden=!isCertificate || $('primaryEaeCertification').value!=='yes';
    $('primaryEaeCertificationLabel').textContent=mode==='deaf'?'Πιστοποιημένη γνώση Ελληνικής Νοηματικής Γλώσσας (ΕΝΓ)':'Πιστοποιημένη γνώση γραφής Braille';
    if(mode==='region')return;
    var origin=$('primaryEaeOrigin').value;
    $('primaryEaeGeneralYearsField').hidden=origin!=='general';
    $('primaryEaeAppointmentYearsField').hidden=origin!=='appointed-eae';
    $('primaryEaeYearsField').hidden=!$('primaryEaeExperience').checked;
    $('primaryPreviousDossierField').hidden=origin==='appointed-eae';
    var data={mode:mode,branch:$('primaryEaeBranch').value,origin:origin,
      organic:$('primaryEaeOrganic').value,generalYears:$('primaryEaeGeneralYears').value,
      appointmentAreaYears:$('primaryEaeAppointmentYears').value,eaeYears:$('primaryEaeYears').value,
      qualifications:qualifications(),certification:$('primaryEaeCertification').value};
    var outcome=eae.evaluate(data);
    var labels={problem:'⚠ Δεν πληρούται ελεγχόμενη προϋπόθεση',review:'ⓘ Απαιτούνται στοιχεία ή υπηρεσιακή επαλήθευση',positive:'✓ Θετικός προκαταρκτικός έλεγχος'};
    $('primaryEaeEligibility').setAttribute('data-status',outcome.status);
    var list=outcome.problems.concat(outcome.checks);
    if(!list.length)list=outcome.positive;
    $('primaryEaeEligibility').innerHTML='<strong>'+escapeText(labels[outcome.status])+'</strong>'+
      (list.length?'<ul>'+list.map(function(x){return '<li>'+escapeText(x)+'</li>';}).join('')+'</ul>':'');
  }
  function update() {
    // Official application codes from the Primary Education application list.
    // EEEEK shares the SΜΚΔ application with SΜΕΑΕ; Deaf/Blind shares the language-specialization application.
    // This is a UI reference, NOT an alternate electronic filing path.
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
  // Section 2.2.1 (general) and 2.2.5 (EAE) prescribe distinct, exact
  // comma-separated subject/body field orders. The secondary composer uses
  // the same visual component, but PRIMARY legal wording is defined here.
  function messageParts(docs) {
    var actions=docs.filter(function(d){return d.action && d.channel==='email';});
    var general=$('primaryMode').value==='region';
    var name=$('primaryName').value.trim()||'[Ονοματεπώνυμο]';
    var branch=$('primarySpecialty').value.trim()||($('primaryMode').value!=='region'&&$('primaryEaeBranch').value)||'[Κλάδος/ειδικότητα]';
    var registry=$('primaryRegistry').value.trim()||'[Αριθμός μητρώου]';
    var protocol=$('primaryProtocol').value.trim()||'[Αριθμός πρωτοκόλλου οριστικοποιημένης αίτησης]';
    var dpe=$('primaryDpe').value.trim()||'[ΔΠΕ οργανικής]';
    var labels={
      region:'Μετάθεση από περιοχή σε περιοχή',
      eae:'μετάθεση σε ΣΜΕΑΕ–ΕΕΕΕΚ',
      kedasy:'μετάθεση σε ΚΕΔΑΣΥ',
      eeeek:'μετάθεση σε ΣΜΕΑΕ–ΕΕΕΕΚ',
      deaf:'μετάθεση με εξειδίκευση στην ΕΝΓ',
      blind:'μετάθεση με εξειδίκευση στη γραφή Braille'
    };
    var typ=labels[$('primaryMode').value]||labels.region;
    return {
      subject:general?[typ,protocol,registry].join(', '):['ΠΕ',typ,protocol,registry,branch].join(', '),
      body:general?[name,branch,typ,protocol,registry].join(', '):[name,branch,typ,protocol,registry,dpe].join(', '),
      actions:actions,general:general
    };
  }
  function uiText(key,defaultText){return global.EducationTransferEmailComponent&&global.EducationTransferEmailComponent.label(key)||defaultText;}
  function updateMail(docs){
    var email=messageParts(docs);
    var hasActions=email.actions.length>0;
    var mydocActions=docs.filter(function(d){return d.action && d.channel==='mydocs';});
    var special=!email.general;
    var hasMydocs=mydocActions.length>0;
    $('primaryMydocsLink').hidden=!special;
    $('primaryMydocsNotice').hidden=!hasMydocs;
    $('primaryMydocsNotice').textContent=hasMydocs?'Έχετε '+mydocActions.length+' δικαιολογητικό/ά προσόντων ΕΑΕ για υποβολή στο mydocs.minedu.gov.gr (όχι με email). Δεν επανυποβάλλονται ήδη κατατεθειμένα στοιχεία.':'';
    var sharedState=global.EducationTransferEmailComponent?global.EducationTransferEmailComponent.stateCopy(hasActions?'email':hasMydocs?'mydocs':'optional'):null;
    $('primaryEmailHeading').textContent=sharedState?sharedState.title:hasActions?'Έτοιμο email υποβολής δικαιολογητικών':hasMydocs?'Έτοιμο υπόδειγμα υποβολής μέσω mydocs':'Υπόδειγμα μηνύματος — αν χρειαστεί';
    $('primaryEmailLead').textContent=hasActions?'Δημιουργήστε θέμα και σώμα μηνύματος για τα δικαιολογητικά μοριοδότησης που αποστέλλονται στη ΔΠΕ οργανικής.':hasMydocs?'Τα προσόντα ΕΑΕ υποβάλλονται μέσω mydocs όταν απαιτείται νέος ή συμπληρωματικός φάκελος — όχι με email.':'Δεν προκύπτει αυτή τη στιγμή δικαιολογητικό για αποστολή με email. Το υπόδειγμα παραμένει διαθέσιμο για προεπισκόπηση.';
    $('primaryEmailOpen').textContent=sharedState?sharedState.button:hasActions?'Δημιουργία email υποβολής':hasMydocs?'Προετοιμασία κειμένου για mydocs':'Προβολή προαιρετικού υποδείγματος';
    $('primarySubmissionInstruction').textContent=email.general?'Γενική μετάθεση: δικαιολογητικά μέσω email στη ΔΠΕ οργανικής, εφόσον απαιτούνται.':'ΕΑΕ: τα αποδεικτικά μοριοδότησης αποστέλλονται με email στη ΔΠΕ οργανικής, ενώ τα αποδεικτικά προσόντων υποβάλλονται στο mydocs μόνο όπου απαιτείται νέα/συμπληρωματική υποβολή.';
    $('primaryTemplateNote').textContent=email.general?'Το θέμα και το σώμα τηρούν ακριβώς τη σειρά πεδίων της §2.2.1.':'Το θέμα και το σώμα τηρούν ακριβώς τη σειρά πεδίων της §2.2.5. Στο mydocs το σώμα μπορεί να χρησιμοποιηθεί ως κείμενο «Αιτούμαι».';
    $('primaryEmailSource').textContent='Πηγή: εγκύκλιος μεταθέσεων Π.Ε. 138023/Ε2/15-10-2026, §'+(email.general?'2.2.1':'2.2.5')+'. Το υπόδειγμα δεν αντικαθιστά ειδικές διαδικασίες ή προηγούμενο φάκελο.';
    $('primaryDpeField').hidden=email.general;
    $('primaryEmailSubject').textContent=email.subject;
    $('primaryEmailBody').textContent=email.body;
    $('primaryMailAction').hidden=!hasActions;
    $('primaryOpenMail').href=hasActions?(global.EducationTransferEmailComponent?global.EducationTransferEmailComponent.mailto(email):'mailto:?subject='+encodeURIComponent(email.subject)+'&body='+encodeURIComponent(email.body)):'#';
  }
  function legacyCopy(text){
    var node=document.createElement('textarea');node.value=text;node.setAttribute('readonly','');
    node.style.position='fixed';node.style.left='-9999px';document.body.appendChild(node);
    node.select();var success=false;
    try{success=!!document.execCommand('copy');}catch(err){}
    document.body.removeChild(node);return success;
  }
  function copyPart(which){
    var content=$('primaryEmail'+which).textContent;
    var feedback=$('primaryCopyFeedback');
    function done(ok){feedback.textContent=ok?(/\[[^\]]+\]/.test(content)?'Αντιγράφηκε. Συμπληρώστε όσα πεδία παραμένουν σε αγκύλες.':'Το κείμενο αντιγράφηκε.'):'Δεν ήταν δυνατή η αντιγραφή. Επιλέξτε το κείμενο χειροκίνητα.';}
    if(global.EducationTransferEmailComponent){
      global.EducationTransferEmailComponent.copy(content,{
        nativeSuccess:uiText('copySuccess','Το κείμενο αντιγράφηκε.'),
        nativeBrackets:uiText('copyBrackets','Αντιγράφηκε. Συμπληρώστε τα πεδία που απομένουν στις αγκύλες.'),
        fallbackSuccess:uiText('copySuccess','Το κείμενο αντιγράφηκε.'),
        fallbackBrackets:uiText('copyBrackets','Αντιγράφηκε. Συμπληρώστε τα πεδία που απομένουν στις αγκύλες.'),
        fallbackFailure:uiText('copyNativeFailure','Δεν ήταν δυνατή η αντιγραφή. Επιλέξτε το κείμενο χειροκίνητα.')
      },feedback);
    }else if(navigator.clipboard && navigator.clipboard.writeText){navigator.clipboard.writeText(content).then(function(){done(true);},function(){done(legacyCopy(content));});}
    else done(legacyCopy(content));
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
  if(global.EducationTransferEmailComponent&&global.EducationTransferEmailComponent.fillSpecialtyDropdown){
    global.EducationTransferEmailComponent.fillSpecialtyDropdown(
      $('primarySpecialty'),global.EducationTransferPrimarySpecialtyLabels||{},'');
  }
  ['primaryName','primarySpecialty','primaryRegistry','primaryProtocol','primaryDpe'].forEach(function(id){
    $(id).addEventListener('input',function(){updateMail(buildDocuments());});
  });
  $('primaryEmailOpen').addEventListener('click',function(){
    if(global.EducationTransferEmailComponent){
      global.EducationTransferEmailComponent.open($('primaryEmailDetails'),this,'#primaryPersonalize');
    }else{
      $('primaryEmailDetails').open=true;
      $('primaryPersonalize').open=true;
      this.setAttribute('aria-expanded','true');
      if($('primaryEmailDetails').scrollIntoView)$('primaryEmailDetails').scrollIntoView({block:'start',behavior:'smooth'});
    }
  });
  if(global.EducationTransferEmailComponent){
    global.EducationTransferEmailComponent.bindState($('primaryEmailDetails'),$('primaryEmailOpen'));
  }else $('primaryEmailDetails').addEventListener('toggle',function(){
    $('primaryEmailOpen').setAttribute('aria-expanded',String(this.open));
  });
  $('primaryClearEmail').addEventListener('click',function(){
    ['primaryName','primarySpecialty','primaryRegistry','primaryProtocol','primaryDpe'].forEach(function(id){$(id).value='';});
    $('primaryCopyFeedback').textContent='';updateMail(buildDocuments());
  });
  $('primaryCopySubject').addEventListener('click',function(){copyPart('Subject');});
  $('primaryCopyBody').addEventListener('click',function(){copyPart('Body');});
  if(global.EducationTransferEmailComponent){
    global.EducationTransferEmailComponent.syncPrimaryStatic($('primaryEmailFeature'));
  }
  update();
}(typeof window!=='undefined'?window:this));
