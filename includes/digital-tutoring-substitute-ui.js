/*
 * Browser UI controller for ypologismos-morion-anapliroti-psifiako-frontistirio.php.
 * Owns vacancy matching, basic eligibility checks, live scoring and service-point display.
 */
(function(){
  'use strict';

  const positions = [
    {category:'ΓΕΛ', course:'Φυσική', branches:['ΠΕ04.01'], seats:1},
    {category:'ΓΕΛ', course:'Χημεία', branches:['ΠΕ04.02','ΠΕ85'], seats:4, note:'Για ΠΕ85 απαιτείται πτυχίο Χημικών Μηχανικών.'},
    {category:'ΓΕΛ', course:'Ισπανικά', branches:['ΠΕ40'], seats:1},
    {category:'ΕΠΑΛ', course:'Αρχές Οικονομικής Θεωρίας', branches:['ΠΕ80'], seats:1, note:'Κατά προτεραιότητα εκπαιδευτικοί με πτυχία που αντιστοιχούν στον πρώην κλάδο ΠΕ09.'},
    {category:'ΕΠΑΛ', course:'Αρχές Οργάνωσης και Διοίκησης', branches:['ΠΕ80'], seats:1, note:'Κατά προτεραιότητα εκπαιδευτικοί με πτυχία που αντιστοιχούν στους πρώην κλάδους ΠΕ09, ΠΕ18.02.'},
    {category:'ΕΠΑΛ', course:'Αρχιτεκτονικό Σχέδιο', branches:['ΠΕ81'], seats:1, note:'Κατά προτεραιότητα πτυχία πρώην ΠΕ12.01, ΠΕ12.02, ΠΕ17.01, ΠΕ17.05.'},
    {category:'ΕΠΑΛ', course:'Τεχνολογία Υλικών', branches:['ΠΕ89.01'], seats:1},
    {category:'ΕΠΑΛ', course:'Στοιχεία ψύξης – Κλιματισμού', branches:['ΠΕ82'], seats:1, note:'Κατά προτεραιότητα πτυχία πρώην ΠΕ12.04, ΠΕ17.02, ΠΕ17.06.'},
    {category:'ΕΠΑΛ', course:'Στοιχεία Σχεδιασμού Κεντρικών Θερμάνσεων', branches:['ΠΕ82'], seats:1, note:'Κατά προτεραιότητα πτυχία πρώην ΠΕ12.04, ΠΕ17.02, ΠΕ17.06.'},
    {category:'ΕΠΑΛ', course:'Κινητήρες Αεροσκαφών', branches:['ΠΕ82'], seats:1, note:'Κατά προτεραιότητα πτυχία πρώην ΠΕ12.04, ΠΕ17.02, ΠΕ17.06, ΠΕ18.18, ΠΕ18.31, ΠΕ18.32.'},
    {category:'ΕΠΑΛ', course:'Ναυτικές Μηχανές', branches:['ΠΕ82'], seats:1, note:'Κατά προτεραιότητα πτυχία πρώην ΠΕ18.31.'},
    {category:'ΕΠΑΛ', course:'Προγραμματισμός Υπολογιστών', branches:['ΠΕ86'], seats:1},
    {category:'ΕΠΑΛ', course:'Δίκτυα Υπολογιστών', branches:['ΠΕ86'], seats:1},
    {category:'ΕΑΕ', course:'Μαθήματα Α΄ ανάθεσης ΠΕ02', branches:['ΠΕ02'], seats:1, note:'Νεοελληνική Γλώσσα και Λογοτεχνία, Νέα Ελληνικά, Αρχαία Ελληνικά, Λατινικά, Ιστορία.'},
    {category:'ΕΑΕ', course:'Μαθήματα Α΄ ανάθεσης ΠΕ80', branches:['ΠΕ80'], seats:1, note:'Κατά προτεραιότητα πρώην ΠΕ09.'},
    {category:'ΕΑΕ', course:'Μαθήματα Α΄ ανάθεσης ΠΕ82', branches:['ΠΕ82'], seats:1},
    {category:'ΕΑΕ', course:'Μαθήματα Α΄ ανάθεσης ΠΕ83', branches:['ΠΕ83'], seats:1},
    {category:'ΕΑΕ', course:'Μαθήματα Α΄ ανάθεσης ΠΕ87', branches:['ΠΕ87'], seats:1},
    {category:'ΕΑΕ', course:'Μαθήματα Α΄ ανάθεσης ΠΕ88', branches:['ΠΕ88'], seats:1}
  ];

  function branchMatches(rule, specialty){
    return rule === specialty ||
      (rule === 'ΠΕ87' && specialty.indexOf('ΠΕ87.') === 0) ||
      (rule === 'ΠΕ88' && specialty.indexOf('ΠΕ88.') === 0);
  }

  const specialtySelect = document.getElementById('specialty');
  const specialtyCatalog = Array.from(specialtySelect.options).map(option => ({
    value: option.value,
    label: option.textContent
  }));

  function specialtyAvailable(specialty, eae){
    if(!specialty) return true;
    return positions.some(item =>
      (eae ? item.category === 'ΕΑΕ' : item.category !== 'ΕΑΕ') &&
      item.branches.some(rule => branchMatches(rule, specialty))
    );
  }

  function syncSpecialtyOptions(){
    const eae = document.getElementById('eaePosition').value === 'yes';
    const previous = specialtySelect.value;
    specialtySelect.innerHTML = '';

    specialtyCatalog.forEach(item => {
      if(!specialtyAvailable(item.value, eae)) return;
      const option = document.createElement('option');
      option.value = item.value;
      option.textContent = item.label;
      specialtySelect.appendChild(option);
    });

    specialtySelect.value = specialtyAvailable(previous, eae) ? previous : '';
  }

  function matchingPositions(){
    const specialty = document.getElementById('specialty').value;
    if(!specialty) return [];
    const eae = document.getElementById('eaePosition').value === 'yes';
    return positions.filter(item =>
      (eae ? item.category === 'ΕΑΕ' : item.category !== 'ΕΑΕ') &&
      item.branches.some(rule => branchMatches(rule, specialty))
    );
  }

  function renderAssignments(){
    const box = document.getElementById('assignmentBox');
    const specialty = document.getElementById('specialty').value;
    if(!specialty){
      box.className = 'info';
      box.innerHTML = 'Επίλεξε ειδικότητα για να δεις τις θέσεις του Παραρτήματος Ι που αντιστοιχούν στον κλάδο σου.';
      return;
    }
    const matched = matchingPositions();
    if(!matched.length){
      box.className = 'danger';
      box.innerHTML = 'Δεν εντοπίζεται θέση της πρόσκλησης για την επιλεγμένη ειδικότητα' + (document.getElementById('eaePosition').value === 'yes' ? ' στην ΕΑΕ.' : '.');
      return;
    }
    box.className = 'success';
    box.innerHTML = matched.map(item => '<div class="assignment"><strong>' + item.category + ' · ' + item.course + '</strong> <span class="badge">' + item.seats + (item.seats === 1 ? ' θέση' : ' θέσεις') + '</span>' + (item.note ? '<small>' + item.note + '</small>' : '') + '</div>').join('');
  }

  function n(id){
    const value = Number(document.getElementById(id).value || 0);
    return Number.isFinite(value) ? value : 0;
  }

  function clamp(value,min,max){ return Math.min(Math.max(value,min),max); }

  function format(value){
    const rounded = Math.round(value * 10) / 10;
    return Number.isInteger(rounded) ? String(rounded) : rounded.toFixed(1).replace('.', ',');
  }

  function normalizeInteger(input,max){
    let value = Math.floor(Number(input.value || 0));
    value = Math.max(0,value);
    if(typeof max === 'number') value = Math.min(max,value);
    input.value = value;
  }

  function normalizeBoundedNumber(input){
    if(input.value === '') return;
    let value = Number(input.value);
    if(!Number.isFinite(value)){
      input.value = '';
      return;
    }
    if(input.min !== '') value = Math.max(Number(input.min), value);
    if(input.max !== '') value = Math.min(Number(input.max), value);
    if(input.step === '0.1') value = Math.round(value * 10) / 10;
    input.value = value;
  }

  function syncEaeUI(){
    const eae = document.getElementById('eaePosition').value === 'yes';
    document.getElementById('eaeTableWrap').classList.toggle('hidden', !eae);
  }

  function calculate(){
    const a1 = clamp(n('a1'),0,20);
    const a2 = clamp(n('a2'),0,15);
    const a = a1 + a2;

    const b1 = n('phd');
    const b2 = n('master');
    const b3 = n('examExperience');
    const years = Math.max(0, Math.floor(n('relevantYears')));
    const months = clamp(Math.floor(n('relevantMonths')),0,11);
    const b4 = Math.min(6, years * 2 + Math.floor(months / 4));

    let b5 = n('ict');
    if(document.getElementById('specialty').value === 'ΠΕ86' && b5 < 1) b5 = 1;

    const b = Math.min(30,b1+b2+b3+b4+b5);
    const c = clamp(n('videoScore'),0,35);
    const pre = b + c;
    const total = a + b + c;

    const serviceMonths = clamp(Math.floor(n('serviceMonths')),0,12);
    const servicePoints = Math.min(15, serviceMonths * 1.5);

    document.getElementById('aTotal').textContent = format(a) + ' / 35';
    document.getElementById('bTotal').textContent = format(b) + ' / 30';
    document.getElementById('cTotal').textContent = format(c) + ' / 35';
    document.getElementById('preInterview').textContent = format(pre) + ' / 65';
    document.getElementById('totalScore').textContent = format(total);
    document.getElementById('totalBar').style.width = clamp(total,0,100) + '%';
    document.getElementById('servicePoints').textContent = format(servicePoints) + ' / 15';

    document.getElementById('b1Result').textContent = format(b1);
    document.getElementById('b2Result').textContent = format(b2);
    document.getElementById('b3Result').textContent = format(b3);
    document.getElementById('b4Result').textContent = format(b4);
    document.getElementById('b5Result').textContent = format(b5);

    const issues = [];
    const unanswered = [];
    const specialty = document.getElementById('specialty').value;
    const fiveYears = document.getElementById('fiveYears').value;
    const evaluationTable = document.getElementById('evaluationTable').value;
    const videoFace = document.getElementById('videoFace').value;
    const videoDuration = document.getElementById('videoDuration').value;
    const eae = document.getElementById('eaePosition').value === 'yes';
    const eaeTable = document.getElementById('eaeTable').value;

    if(!specialty) unanswered.push('κλάδος / ειδικότητα');
    else if(!matchingPositions().length) issues.push('Δεν προβλέπεται θέση του Παραρτήματος Ι για την επιλεγμένη ειδικότητα και κατηγορία.');

    if(fiveYears === '') unanswered.push('πενταετής διδακτική εμπειρία');
    else if(fiveYears === 'no') issues.push('Η πρόσκληση θέτει πρόσθετη προϋπόθεση πενταετούς διδακτικής εμπειρίας.');

    if(evaluationTable === '') unanswered.push('ένταξη σε αξιολογικό πίνακα');
    else if(evaluationTable === 'no') issues.push('Απαιτείται ο/η υποψήφιος/α να περιλαμβάνεται στους προβλεπόμενους αξιολογικούς πίνακες.');

    if(videoFace === '') unanswered.push('εμφάνιση προσώπου στο βίντεο');
    else if(videoFace === 'no') issues.push('Αν δεν εμφανίζεται το πρόσωπο του/της εκπαιδευτικού στο βίντεο, το βίντεο δεν λαμβάνεται υπόψη και ο/η υποψήφιος/α αποκλείεται.');

    if(videoDuration === '') unanswered.push('διάρκεια βίντεο');
    else if(videoDuration === 'no') issues.push('Το βιντεοσκοπημένο μάθημα πρέπει να έχει διάρκεια 4–7 λεπτά.');

    if(eae){
      if(eaeTable === '') unanswered.push('αξιολογικός πίνακας ΕΑΕ');
      else if(eaeTable === 'no') issues.push('Για θέση ΕΑΕ πρέπει να ελέγξεις ότι περιλαμβάνεσαι στον αντίστοιχο αξιολογικό πίνακα ΕΑΕ.');
    }

    const status = [];
    const videoScoreFilled = document.getElementById('videoScore').value !== '';
    if(!videoScoreFilled){
      status.push('<div class="result-message edu-message result-message--status edu-message--status"><strong>Βαθμολογία βίντεο:</strong> συμπλήρωσε τη βαθμολογία Γ όταν είναι διαθέσιμη.</div>');
    } else if(c < 20){
      status.push('<div class="result-message edu-message result-message--warning edu-message--warning"><strong>Δεν καλύπτεται η βάση του βιντεοσκοπημένου μαθήματος:</strong> απαιτούνται τουλάχιστον 20/35.</div>');
    } else {
      status.push('<div class="result-message edu-message result-message--success edu-message--success"><strong>Καλύπτεται η βάση Γ:</strong> ' + format(c) + '/35. Η κλήση σε συνέντευξη εξαρτάται και από τη σχετική κατάταξη Β+Γ.</div>');
    }

    if((a1 > 0 || a2 > 0) && (a1 < 12 || a2 < 8)){
      const missing = [];
      if(a1 < 12) missing.push('Α1 κάτω από 12');
      if(a2 < 8) missing.push('Α2 κάτω από 8');
      status.push('<div class="result-message edu-message result-message--warning edu-message--warning"><strong>Βάσεις συνέντευξης:</strong> ' + missing.join(' · ') + '.</div>');
    } else if(a1 >= 12 && a2 >= 8){
      status.push('<div class="result-message edu-message result-message--success edu-message--success"><strong>Καλύπτονται και οι δύο βάσεις της συνέντευξης.</strong></div>');
    }

    if(unanswered.length){
      status.push('<div class="result-message edu-message result-message--warning edu-message--warning"><strong>Συμπλήρωσε τις προϋποθέσεις:</strong> ' + unanswered.join(' · ') + '.</div>');
    }
    if(issues.length){
      status.push('<div class="result-message edu-message result-message--warning edu-message--warning"><strong>Έλεγχος προϋποθέσεων:</strong><ul class="edu-list-compact"><li>' + issues.join('</li><li>') + '</li></ul></div>');
    }

    document.getElementById('statusBox').innerHTML = status.join('');
  }

  function specialtyChanged(){
    renderAssignments();
    calculate();
  }

  function eaeChanged(){
    syncEaeUI();
    syncSpecialtyOptions();
    renderAssignments();
    calculate();
  }

  function resetForm(){
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = 0);
    document.getElementById('videoScore').value = '';
    document.querySelectorAll('select').forEach(el => {
      if(['fiveYears','evaluationTable','videoFace','videoDuration','eaeTable'].includes(el.id)) el.value = '';
      else el.selectedIndex = 0;
    });
    syncEaeUI();
    syncSpecialtyOptions();
    specialtyChanged();
    window.scrollTo({top:0,behavior:'smooth'});
  }

  document.getElementById('specialty').addEventListener('change', specialtyChanged);
  document.getElementById('eaePosition').addEventListener('change', eaeChanged);

  ['a1','a2','videoScore'].forEach(id => {
    document.getElementById(id).addEventListener('input', function(){
      normalizeBoundedNumber(this);
      calculate();
    });
  });

  document.getElementById('relevantYears').addEventListener('input', function(){
    normalizeInteger(this,20);
    calculate();
  });
  document.getElementById('relevantMonths').addEventListener('input', function(){
    normalizeInteger(this,11);
    calculate();
  });
  document.getElementById('serviceMonths').addEventListener('input', function(){
    normalizeInteger(this,12);
    calculate();
  });

  ['phd','master','examExperience','ict','fiveYears','evaluationTable','videoFace','videoDuration','eaeTable'].forEach(id => {
    document.getElementById(id).addEventListener('change', calculate);
  });

  document.getElementById('resetBtn').addEventListener('click', resetForm);

  syncEaeUI();
  syncSpecialtyOptions();
  specialtyChanged();
}());
