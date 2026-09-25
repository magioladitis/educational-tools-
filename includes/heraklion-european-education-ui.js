(function(){
  'use strict';
  var $ = function(id){ return document.getElementById(id); };
  var fmt = function(n){ return Number(n || 0).toLocaleString('el-GR', {maximumFractionDigits:2}); };
  var ids = [
    'candidateRoute','targetLanguage','teachingQualification','appointmentObstacle','healthFitness','excellentRequiredLanguage','inspectorAgreement',
    'relevantPhd','otherPhd','relevantMaster','otherMaster','relevantMasterSameSubjectAsPhd','otherMasterSameSubjectAsPhd','greekLevel','publication','secondDegree','otherLanguagesCount',
    'europeanSchoolYears','interviewGreek','interviewPersonality'
  ];

  function value(id){ return $(id).value; }
  function checked(id){ return $(id).checked; }
  function escapeHtml(value){
    return String(value == null ? '' : value).replace(/[&<>'"]/g,function(ch){ return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[ch]; });
  }

  function normalize(){
    ['otherLanguagesCount','europeanSchoolYears'].forEach(function(id){
      var el = $(id); if (!el || el.value === '') return;
      var n = Math.max(0, Math.floor(Number(el.value) || 0));
      var max = Number(el.getAttribute('max'));
      if (Number.isFinite(max)) n = Math.min(max,n);
      el.value = String(n);
    });
    var limits = {interviewGreek:20, interviewPersonality:10};
    Object.keys(limits).forEach(function(id){
      var el = $(id); if (!el || el.value === '') return;
      var n = Number(el.value);
      if (!Number.isFinite(n)){ el.value=''; return; }
      n = Math.min(limits[id], Math.max(0,n));
      el.value = String(Math.round(n*10)/10);
    });
  }

  function options(){
    normalize();
    return {
      candidateRoute:value('candidateRoute'),
      targetLanguage:value('targetLanguage'),
      teachingQualification:value('teachingQualification'),
      appointmentObstacle:value('appointmentObstacle'),
      healthFitness:value('healthFitness'),
      excellentRequiredLanguage:value('excellentRequiredLanguage'),
      inspectorAgreement:value('inspectorAgreement'),
      relevantPhd:checked('relevantPhd'),
      otherPhd:checked('otherPhd'),
      relevantMaster:checked('relevantMaster'),
      otherMaster:checked('otherMaster'),
      relevantMasterSameSubjectAsPhd:checked('relevantMasterSameSubjectAsPhd'),
      otherMasterSameSubjectAsPhd:checked('otherMasterSameSubjectAsPhd'),
      greekLevel:value('greekLevel'),
      publication:checked('publication'),
      secondDegree:checked('secondDegree'),
      otherLanguagesCount:value('otherLanguagesCount'),
      europeanSchoolYears:value('europeanSchoolYears'),
      interviewGreek:value('interviewGreek'),
      interviewPersonality:value('interviewPersonality')
    };
  }

  function updateRouteUI(){
    var route = value('candidateRoute');
    var nonNative = route === 'non_native';
    $('nonNativeRequirements').classList.toggle('hidden', !nonNative);
    $('qualificationHint').textContent = nonNative
      ? 'Για τη διαδρομή μη φυσικού ομιλητή: προσόντα που απαιτεί η ελληνική νομοθεσία για τη συγκεκριμένη γλώσσα ή το αντικείμενο.'
      : 'Για φυσικό ομιλητή: τίτλος που παρέχει δικαίωμα διδασκαλίας στο αντίστοιχο εκπαιδευτικό σύστημα της γλώσσας/χώρας.';
    if (!nonNative){ $('excellentRequiredLanguage').value=''; $('inspectorAgreement').value=''; }
  }

  function updateMasterOverlapUI(){
    var hasPhd = checked('relevantPhd') || checked('otherPhd');
    $('relevantMasterOverlapWrap').classList.toggle('hidden', !(hasPhd && checked('relevantMaster')));
    $('otherMasterOverlapWrap').classList.toggle('hidden', !(hasPhd && checked('otherMaster')));
    if (!(hasPhd && checked('relevantMaster'))) $('relevantMasterSameSubjectAsPhd').checked=false;
    if (!(hasPhd && checked('otherMaster'))) $('otherMasterSameSubjectAsPhd').checked=false;
  }

  function rows(items){
    return items.map(function(item){
      return '<div class="break-row"><span>'+escapeHtml(item[0])+'</span><strong>'+fmt(item[1])+'</strong></div>';
    }).join('');
  }

  function renderBreakdown(r){
    var b = r.academic.breakdown;
    var academicRows = [];
    if (checked('relevantPhd')) academicRows.push(['Συναφές διδακτορικό / επιστήμες αγωγής',b.relevantPhd]);
    if (checked('otherPhd')) academicRows.push(['Μη συναφές διδακτορικό',b.otherPhd]);
    if (checked('relevantMaster')) academicRows.push([r.academic.suppressed.relevantMaster ? 'Συναφές master — δεν προσμετράται λόγω ίδιου αντικειμένου με διδακτορικό' : 'Συναφές master',b.relevantMaster]);
    if (checked('otherMaster')) academicRows.push([r.academic.suppressed.otherMaster ? 'Μη συναφές master — δεν προσμετράται λόγω ίδιου αντικειμένου με διδακτορικό' : 'Μη συναφές master',b.otherMaster]);
    if (b.greek) academicRows.push(['Γνώση ελληνικής γλώσσας',b.greek]);
    if (b.publication) academicRows.push(['Δημοσιευμένη συγγραφική εργασία',b.publication]);
    if (b.secondDegree) academicRows.push(['Άλλος πανεπιστημιακός τίτλος',b.secondDegree]);
    if (r.academic.otherLanguagesCount) academicRows.push(['Άλλες γλώσσες × '+r.academic.otherLanguagesCount,b.otherLanguages]);
    if (!academicRows.length) academicRows.push(['Δεν έχουν δηλωθεί μοριοδοτούμενα προσόντα',0]);
    $('academicBreakdown').innerHTML = '<div class="stage-label">Επιστημονική & παιδαγωγική κατάρτιση</div>'+rows(academicRows);
    $('serviceBreakdown').innerHTML = '<div class="stage-label edu-mt-8">Διδακτική εμπειρία</div>'+rows([['Έτη σε Ευρωπαϊκό Σχολείο / Σ.Ε.Π. × '+r.service.years,r.service.points]]);
  }

  function renderDocuments(r){
    $('documentsChecklist').innerHTML = r.documents.map(function(text){ return '<li><span class="doc-mark">✓</span><span>'+escapeHtml(text)+'</span></li>'; }).join('');
  }

  function renderStatus(r){
    var blocks=[];
    if (r.eligibility.unanswered.length) blocks.push('<div class="info"><strong>Χρειάζονται ακόμη στοιχεία:</strong><ul class="edu-list-compact"><li>'+r.eligibility.unanswered.map(escapeHtml).join('</li><li>')+'</li></ul></div>');
    if (r.eligibility.issues.length) blocks.push('<div class="danger"><strong>Έλεγχος βασικών προϋποθέσεων:</strong><ul class="edu-list-compact"><li>'+r.eligibility.issues.map(escapeHtml).join('</li><li>')+'</li></ul></div>');
    else if (r.eligibility.eligible) blocks.push('<div class="success"><strong>Ο βασικός έλεγχος προϋποθέσεων είναι θετικός.</strong> Η τελική κρίση γίνεται από την αρμόδια Επιτροπή με βάση τα επίσημα δικαιολογητικά.</div>');
    if (r.eligibility.notes.length) blocks.push('<div class="note"><strong>Κατάταξη:</strong><ul class="edu-list-compact"><li>'+r.eligibility.notes.map(escapeHtml).join('</li><li>')+'</li></ul></div>');
    if (r.warnings.length) blocks.push('<div class="warning"><strong>Παρατηρήσεις υπολογισμού:</strong><ul class="edu-list-compact"><li>'+r.warnings.map(escapeHtml).join('</li><li>')+'</li></ul></div>');
    $('eligibilityStatus').innerHTML=blocks.join('');
  }

  function calculate(){
    updateRouteUI();
    updateMasterOverlapUI();
    var r = HeraklionEuropeanEducation.calculate(options());
    $('academicSubtotal').textContent=fmt(r.academic.total)+' / 45';
    $('serviceSubtotal').textContent=fmt(r.service.points)+' / 25';
    $('preInterviewTotal').textContent=fmt(r.preInterview);
    $('academicResult').textContent=fmt(r.academic.total)+' / 45';
    $('serviceResult').textContent=fmt(r.service.points)+' / 25';

    if (r.interview.complete){
      $('interviewSubtotal').textContent=fmt(r.interview.total)+' / 30';
      $('interviewResult').textContent=fmt(r.interview.total)+' / 30';
      $('finalTotal').textContent=fmt(r.finalTotal)+' / 100';
      $('finalHelp').textContent='Τελικό άθροισμα κριτηρίων και συνέντευξης.';
    } else if (r.interview.anyEntered){
      $('interviewSubtotal').textContent=fmt(r.interview.total)+' / 30 (μερικό)';
      $('interviewResult').textContent=fmt(r.interview.total)+' / 30 (μερικό)';
      $('finalTotal').textContent='—';
      $('finalHelp').textContent='Συμπλήρωσε και τα δύο πεδία της συνέντευξης για τελικό /100.';
    } else {
      $('interviewSubtotal').textContent='— / 30';
      $('interviewResult').textContent='— / 30';
      $('finalTotal').textContent='—';
      $('finalHelp').textContent='Η συνέντευξη συμπληρώνεται μόνο όταν είναι γνωστή η επίσημη βαθμολογία.';
    }

    renderBreakdown(r);
    renderDocuments(r);
    renderStatus(r);
    return r;
  }

  async function copySummary(){
    var r=calculate();
    var route=value('candidateRoute')==='native'?'Φυσικός ομιλητής':(value('candidateRoute')==='non_native'?'Μη φυσικός ομιλητής — εξαιρετική διαδρομή':'—');
    var language=$('targetLanguage').selectedOptions.length ? $('targetLanguage').selectedOptions[0].textContent : '—';
    var lines=[
      'Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου — υπολογισμός μορίων',
      'Πρόσκληση 69163/Η2/28-05-2026',
      'Κατηγορία: '+route,
      'Γλώσσα θέσης: '+language,
      'Επιστημονική & παιδαγωγική κατάρτιση: '+fmt(r.academic.total)+' / 45',
      'Διδακτική εμπειρία: '+fmt(r.service.points)+' / 25',
      'Μόρια πριν από συνέντευξη: '+fmt(r.preInterview)+' / 70',
      'Συνέντευξη: '+(r.interview.complete?fmt(r.interview.total)+' / 30':'δεν έχει ολοκληρωθεί'),
      'Τελικό: '+(r.finalTotal===null?'—':fmt(r.finalTotal)+' / 100')
    ];
    try {
      await navigator.clipboard.writeText(lines.join('\n'));
      $('copyBtn').textContent='Αντιγράφηκε ✓';
      setTimeout(function(){ $('copyBtn').textContent='Αντιγραφή'; },1400);
    } catch(e){ alert(lines.join('\n')); }
  }

  function reset(){
    document.querySelectorAll('input[type="checkbox"]').forEach(function(el){ el.checked=false; });
    document.querySelectorAll('input[type="number"]').forEach(function(el){ el.value=''; });
    document.querySelectorAll('select').forEach(function(el){ el.selectedIndex=0; });
    $('otherLanguagesCount').value='0';
    $('europeanSchoolYears').value='0';
    calculate();
    $('candidateRoute').focus();
  }

  ids.forEach(function(id){
    var el=$(id); if (!el) return;
    el.addEventListener('input',calculate);
    el.addEventListener('change',calculate);
  });
  $('copyBtn').addEventListener('click',copySummary);
  $('resetBtn').addEventListener('click',reset);
  calculate();
})();
