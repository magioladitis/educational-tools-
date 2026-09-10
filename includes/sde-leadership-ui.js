/* Extracted UI/controller logic from ypologismos-morion-diefthynton-ypodiefthynton-sde.php. */
(function(){
  const $ = id => document.getElementById(id);
  const val = id => $(id).value;
  const num = id => Math.max(0, Number($(id).value || 0));
  const EXPERIENCE_YEAR_MAX = 50;
  const boundedYears = (id, preserveBlank = false) => {
    const el = $(id);
    if (!el || el.value === '') return preserveBlank ? '' : 0;
    let value = Number(el.value);
    if (!Number.isFinite(value)) value = 0;
    value = Math.min(EXPERIENCE_YEAR_MAX, Math.max(0, value));
    if (String(value) !== el.value) el.value = String(value);
    return value;
  };
  const normalizeBoundedScore = el => {
    if (!el || el.value === '') return;
    let value = Number(el.value);
    if (!Number.isFinite(value)) { el.value = ''; return; }
    const min = el.min !== '' ? Number(el.min) : 0;
    const max = el.max !== '' ? Number(el.max) : Infinity;
    value = Math.min(max, Math.max(min, value));
    el.value = String(Math.round(value * 100) / 100);
  };
  const yes = id => val(id) === 'yes';
  const fmt = value => {
    const x = Number(value || 0);
    return x.toLocaleString('el-GR', { maximumFractionDigits: 2, minimumFractionDigits: Number.isInteger(x) ? 0 : 1 });
  };

  function collectData(){
    return {
      role: val('role'), permanentTeacher: val('permanentTeacher'), educationalServiceYears: boundedYears('educationalServiceYears', true),
      tertiaryDegree: val('tertiaryDegree'), assignmentEligible: val('assignmentEligible'),
      computerKnowledge: val('computerKnowledge') === 'pe86' ? 'yes' : val('computerKnowledge'),
      adultEducationExperience: val('adultEducationExperience'), adminQualifications: val('adminQualifications'), blockingIssue: val('blockingIssue'),
      phd: val('phd'), master: val('master'), esdda: yes('esdda'), secondDegree: yes('secondDegree'),
      language1: val('language1'), languageLevel1: val('languageLevel1'), languageAppointment1: yes('languageAppointment1'),
      language2: val('language2'), languageLevel2: val('languageLevel2'), languageAppointment2: yes('languageAppointment2'),
      sdeTeachingYears: boundedYears('sdeTeachingYears'), sdeTeachingHours: num('sdeTeachingHours'), sdeTransferredYears: boundedYears('sdeTransferredYears'),
      adultNonformalHours: num('adultNonformalHours'), schoolTeachingYears: boundedYears('schoolTeachingYears'), schoolTeachingHours: num('schoolTeachingHours'), schoolTransferredYears: boundedYears('schoolTransferredYears'),
      sdeDirectorYears: boundedYears('sdeDirectorYears'), sdeDeputyYears: boundedYears('sdeDeputyYears'), otherAdminYears: boundedYears('otherAdminYears'), trainingHours: num('trainingHours'),
      interviewScore: val('interviewScore')
    };
  }

  function roleChanged(){
    const role = val('role');
    const cfg = SDELeadership.ROLE[role];
    $('tertiaryDegreeWrap').classList.toggle('hidden', role !== 'director');
    $('adminQualificationsWrap').classList.toggle('hidden', role !== 'director');
    $('interviewCard').classList.toggle('hidden', role !== 'director');
    $('interviewRow').classList.toggle('hidden', role !== 'director');
    if (role !== 'director') $('interviewScore').value = '';
    if (cfg){
      $('serviceRequirement').textContent = 'Απαιτούνται τουλάχιστον ' + cfg.minServiceYears + ' έτη.';
      $('assignmentRequirement').textContent = 'Απαιτείται δυνατότητα κάλυψης ' + cfg.requiredTeachingHours + ' ωρών διδακτικού έργου.';
      $('teachingMax').textContent = 'έως ' + cfg.max.teaching;
      $('adminMax').textContent = 'έως ' + cfg.max.admin;
    } else {
      $('serviceRequirement').textContent = 'Επίλεξε θέση για να εμφανιστεί το ελάχιστο.';
      $('assignmentRequirement').textContent = 'Επίλεξε θέση.';
      $('teachingMax').textContent = 'έως —'; $('adminMax').textContent = 'έως —';
    }
    calculate();
  }

  const LANGUAGE_PAIR_OPTIONS = {
    first: 'language1',
    second: 'language2',
    relatedFirst: ['languageLevel1', 'languageAppointment1'],
    relatedSecond: ['languageLevel2', 'languageAppointment2']
  };

  function syncLanguageChoices(){
    return LanguagePairLock.sync(LANGUAGE_PAIR_OPTIONS);
  }

  function languageChanged(){
    syncLanguageChoices();
    calculate();
  }

  function renderEligibility(result){
    const box = $('eligibilityStatus');
    const e = result.eligibility;
    if (!result.role){ box.className='warning'; box.innerHTML='<strong>Επίλεξε θέση υποψηφιότητας.</strong>'; return; }
    if (e.status === 'eligible') { box.className='success'; box.innerHTML='<strong>✅ Οι βασικές προϋποθέσεις φαίνεται να πληρούνται.</strong>'; return; }
    if (e.status === 'not-eligible') { box.className='danger'; box.innerHTML='<strong>⚠️ Έλεγχος βασικών προϋποθέσεων:</strong><ul class="criteria-list">'+e.issues.map(x=>'<li>'+x+'</li>').join('')+'</ul>'; return; }
    box.className='warning'; box.innerHTML='<strong>Χρειάζονται ακόμη στοιχεία:</strong><ul class="criteria-list">'+e.missing.map(x=>'<li>'+x+'</li>').join('')+'</ul>';
  }

  function renderBreakdown(result){
    const groups = [
      ['Τυπικά προσόντα', result.formal.details], ['Διδακτική εμπειρία', result.teaching.details],
      ['Διοικητική εμπειρία', result.admin.details], ['Επιμόρφωση', result.training.details]
    ];
    let html = '';
    groups.forEach(([title, items]) => {
      html += '<h3>'+title+'</h3>';
      if (!items.length) html += '<p class="subtitle">0 μόρια</p>';
      else html += '<ul class="breakdown-list">'+items.map(i=>'<li><span>'+i.label+'</span><strong>'+fmt(i.points)+'</strong></li>').join('')+'</ul>';
    });
    if (result.role === 'director') html += '<h3>Συνέντευξη</h3><p class="subtitle">'+(result.interviewEntered ? fmt(result.interview)+' / 25' : 'Δεν έχει καταχωριστεί ακόμη.')+'</p>';
    if (result.warnings.length) html += '<div class="warning">'+result.warnings.join('<br>')+'</div>';
    $('breakdown').innerHTML = html || 'Συμπλήρωσε τα στοιχεία σου.';
  }

  function renderOverflow(result){
    if (!result.config){ $('overflowHint').textContent='Επίλεξε θέση για να υπολογιστούν τα επιμέρους πλαφόν και τυχόν διοικητικός χρόνος που μένει εκτός μοριοδότησης.'; return; }
    const o = result.admin.overflow;
    const sde = o.sdeDirector + o.sdeDeputy;
    if (!sde && !o.other){
      $('overflowHint').innerHTML='<strong>Δεν προκύπτει διοικητικός χρόνος πάνω από τα επιμέρους πλαφόν.</strong> Θυμήσου ότι ίδια περίοδος δεν δηλώνεται ταυτόχρονα ως διοικητική και διδακτική.';
    } else {
      $('overflowHint').innerHTML='<strong>Πιθανός χρόνος εκτός διοικητικής μοριοδότησης λόγω πλαφόν:</strong><br>ΣΔΕ: '+fmt(sde)+' έτη · Λοιπές δομές: '+fmt(o.other)+' έτη.<br><small>Ο χρόνος δεν μεταφέρεται αυτόματα. Εφόσον πληροί τον κανόνα του ΦΕΚ, δήλωσέ τον μόνο στο αντίστοιχο πεδίο μεταφοράς της διδακτικής εμπειρίας.</small>';
    }
  }

  function calculate(){
    const result = SDELeadership.calculate(collectData());
    const cfg = result.config;
    $('roleChip').textContent = cfg ? cfg.label : 'Επίλεξε θέση';
    $('formalScore').textContent = fmt(result.formal.total) + ' / 25';
    $('teachingScore').textContent = fmt(result.teaching.total) + ' / ' + (cfg ? cfg.max.teaching : '—');
    $('adminScore').textContent = fmt(result.admin.total) + ' / ' + (cfg ? cfg.max.admin : '—');
    $('trainingScore').textContent = fmt(result.training.total) + ' / 5';
    $('criteriaScore').textContent = fmt(result.criteria) + ' / 75';

    let main = result.criteria, denom = 75, context='Μόρια κριτηρίων';
    if (result.role === 'director') {
      $('interviewResult').textContent = result.interviewEntered ? fmt(result.interview) + ' / 25' : '— / 25';
      if (result.interviewEntered) { main = result.final; denom = 100; context='Τελικό σύνολο'; }
      else context='Πριν από τη συνέντευξη';
      $('criteriaRow').classList.remove('hidden');
    } else if (result.role === 'deputy') {
      context='Τελικό σύνολο';
      $('criteriaRow').classList.add('hidden');
    } else {
      $('criteriaRow').classList.remove('hidden');
    }
    $('totalScore').textContent = fmt(main);
    $('totalOutOf').textContent = 'από ' + denom + ' μόρια';
    $('totalContext').textContent = context;
    $('totalBar').style.width = Math.min(100, denom ? (main/denom*100) : 0) + '%';
    renderEligibility(result); renderBreakdown(result); renderOverflow(result);
    return result;
  }

  async function copySummary(button){
    const r = calculate();
    const cfg = r.config;
    const lines = ['Μόρια Διευθυντών / Υποδιευθυντών ΣΔΕ', cfg ? 'Θέση: '+cfg.label : 'Θέση: —', 'Τυπικά προσόντα: '+fmt(r.formal.total)+'/25', 'Διδακτική εμπειρία: '+fmt(r.teaching.total)+'/'+(cfg?cfg.max.teaching:'—'), 'Διοικητική εμπειρία: '+fmt(r.admin.total)+'/'+(cfg?cfg.max.admin:'—'), 'Επιμόρφωση: '+fmt(r.training.total)+'/5', 'Σύνολο κριτηρίων: '+fmt(r.criteria)+'/75'];
    if (r.role === 'director') lines.push('Συνέντευξη: '+(r.interviewEntered ? fmt(r.interview)+'/25' : 'δεν έχει καταχωριστεί'), 'Τελικό: '+(r.final == null ? '— /100' : fmt(r.final)+'/100'));
    else if (r.role === 'deputy') lines.push('Τελικό: '+fmt(r.criteria)+'/75');
    lines.push('Πηγή: Υ.Α. 70621/Κ1, ΦΕΚ Β΄ 3037/19.06.2025');
    try { await navigator.clipboard.writeText(lines.join('\n')); if(button){const old=button.textContent;button.textContent='Αντιγράφηκε ✓';setTimeout(()=>button.textContent=old,1200);} } catch(e){ alert(lines.join('\n')); }
  }

  function resetForm(){
    document.querySelectorAll('input[type="number"]').forEach(i => i.value = (i.id === 'educationalServiceYears' || i.id === 'interviewScore') ? '' : '0');
    document.querySelectorAll('select').forEach(s => {
      if (['phd','master'].includes(s.id)) s.value='none';
      else if (['esdda','secondDegree','languageAppointment1','languageAppointment2'].includes(s.id)) s.value='no';
      else s.selectedIndex=0;
    });
    syncLanguageChoices();
    roleChanged(); window.scrollTo({top:0,behavior:'smooth'});
  }

  syncLanguageChoices();
  roleChanged();

  // External event bindings replacing the former inline onchange/oninput/onclick handlers.
  $('role').addEventListener('change', roleChanged);
  ['permanentTeacher','tertiaryDegree','assignmentEligible','computerKnowledge','adultEducationExperience','adminQualifications','blockingIssue','phd','master','esdda','secondDegree','languageLevel1','languageAppointment1','languageLevel2','languageAppointment2'].forEach(id => $(id).addEventListener('change', calculate));
  ['language1','language2'].forEach(id => $(id).addEventListener('change', languageChanged));
  ['educationalServiceYears','sdeTeachingYears','sdeTeachingHours','sdeTransferredYears','adultNonformalHours','schoolTeachingYears','schoolTeachingHours','schoolTransferredYears','sdeDirectorYears','sdeDeputyYears','otherAdminYears','trainingHours'].forEach(id => $(id).addEventListener('input', calculate));
  $('interviewScore').addEventListener('input', function(){ normalizeBoundedScore(this); calculate(); });
  $('sdeLeadershipCopyBtn').addEventListener('click', function(){ copySummary(this); });
  $('sdeLeadershipResetBtn').addEventListener('click', resetForm);
})();
