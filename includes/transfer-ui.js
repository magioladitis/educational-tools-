/*
 * Browser UI controller for ypologismos-morion-metathesis.php.
 * Calculation/business rules stay in transfer-calculations.js so the same
 * engine can be reused by a future mobile client.
 */
(function (global) {
  'use strict';
  var byId = function (id) { return document.getElementById(id); };
  var initialized = false;
  function isEaeMode(mode){ return ['smeae','eae_sensory','kedasy'].indexOf(mode)!==-1; }
  var rowCounter = 0;
  var msdRows = null;

  var TYPE_LABELS = {
    school: 'Σχολική μονάδα / οργανική / προσωρινή τοποθέτηση',
    prison: 'Σχολική μονάδα σε κατάστημα κράτησης (+5/έτος)',
    digital_tutoring: 'Ψηφιακό Φροντιστήριο (+6/έτος)',
    listed_service_bonus2: 'ΥΠΑΙΘΑ / ΠΔΕ / ΔΔΕ / ΙΕΠ κ.ά. (+2/έτος)',
    other_secondment: 'Λοιπή απόσπαση / φορέας — πλησιέστερη σχολική μονάδα',
    recognized_prior: 'Αναγνωρισμένη προϋπηρεσία — πλησιέστερη σχολική μονάδα',
    abroad_europe: 'Απόσπαση εξωτερικού — Ευρώπη (Α΄)',
    abroad_america: 'Απόσπαση εξωτερικού — Αμερική (Β΄)',
    abroad_other: 'Απόσπαση εξωτερικού — λοιπές χώρες (Γ΄)',
    study_leave: 'Άδεια υπηρεσιακής εκπαίδευσης με αποδοχές (Α΄)'
  };

  var CATEGORY_OPTIONS = [
    ['A', 'Α΄ — 1'], ['B', 'Β΄ — 2'], ['G', 'Γ΄ — 3'], ['D', 'Δ΄ — 4'],
    ['E', 'Ε΄ — 5'], ['ST', 'ΣΤ΄ — 6'], ['Z', 'Ζ΄ — 7'], ['H', 'Η΄ — 8'],
    ['TH', 'Θ΄ — 9'], ['I', 'Ι΄ — 10'], ['IA', 'ΙΑ΄ — 11'], ['IB', 'ΙΒ΄ — 12'], ['IG', 'ΙΓ΄ — 14']
  ];

  function esc(text) {
    return String(text).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function formatPoints(value) {
    return Number(value || 0).toLocaleString('el-GR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
  }

  function clampInput(el, max) {
    if (!el || el.value === '') return;
    var n = Number(el.value);
    if (!Number.isFinite(n)) { el.value = ''; return; }
    el.value = String(Math.max(0, Math.min(max, Math.floor(n))));
  }

  // Keep fractional years (0.1 precision); do not use the integer-only clampInput() here.
  function clampAreaYears(el) {
    if (!el || el.value === '') return;
    var n = Number(el.value);
    if (!Number.isFinite(n)) return;
    if (n < 0) el.value = '0';
    else if (n > 45) el.value = '45';
  }

  function categoryOptionsHtml() {
    return CATEGORY_OPTIONS.map(function (x) { return '<option value="' + x[0] + '">' + x[1] + '</option>'; }).join('');
  }

  function typeOptionsHtml() {
    return Object.keys(TYPE_LABELS).map(function (key) { return '<option value="' + key + '">' + esc(TYPE_LABELS[key]) + '</option>'; }).join('');
  }

  function addRow(initial, options) {
    options = options || {};
    rowCounter += 1;
    initial = initial || {};
    var row = document.createElement('section');
    row.className = 'transfer-service-row';
    row.dataset.rowId = String(rowCounter);
    row.innerHTML =
      '<div class="transfer-service-row-head"><strong>Υπηρέτηση #' + rowCounter + '</strong><span class="transfer-row-score">0 μόρια Μ.Σ.Δ.</span></div>' +
      '<div class="field-grid transfer-service-grid">' +
        '<div class="field transfer-type-field"><label>Είδος υπηρέτησης</label><select class="msd-type">' + typeOptionsHtml() + '</select></div>' +
        '<div class="field transfer-category-field"><label>Κατηγορία / πλησιέστερο σχολείο</label><select class="msd-category">' + categoryOptionsHtml() + '</select></div>' +
        '<div class="field"><label>Έτη</label><input class="msd-years" type="number" min="0" max="50" step="1" value="0" inputmode="numeric"></div>' +
        '<div class="field"><label>Μήνες</label><input class="msd-months" type="number" min="0" max="11" step="1" value="0" inputmode="numeric"></div>' +
        '<div class="field"><label>Ημέρες <small>15+ → μήνας</small></label><input class="msd-days" type="number" min="0" max="29" step="1" value="0" inputmode="numeric"></div>' +
        '<div class="field transfer-weekdays-field"><label>Ημέρες / εβδομάδα <small class="msd-weekdays-hint">έως 5/5</small></label><input class="msd-weekdays" type="number" min="0" max="5" step="0.01" value="5" inputmode="decimal"></div>' +
      '</div>' +
      '<div class="check-row transfer-remote-row"><label><input class="msd-remote-double" type="checkbox"> Έχει θεμελιωθεί διπλασιασμός απομακρυσμένης σχολικής μονάδας (Ι΄–ΙΓ΄, διετής συνεχόμενη υπηρεσία)</label></div>' +
      '<div class="transfer-service-actions"><span class="field-hint transfer-row-note"></span><button class="remove-row" type="button">Αφαίρεση</button></div>';

    msdRows.appendChild(row);
    if (initial.type) row.querySelector('.msd-type').value = initial.type;
    if (initial.category) row.querySelector('.msd-category').value = initial.category;
    syncRow(row);
    if (!options.silent) calculate();
  }

  function syncRow(row) {
    var type = row.querySelector('.msd-type').value;
    var categoryField = row.querySelector('.transfer-category-field');
    var remoteRow = row.querySelector('.transfer-remote-row');
    var category = row.querySelector('.msd-category').value;
    var fixedCategory = /^abroad_|^study_leave$/.test(type);
    categoryField.classList.toggle('hidden', fixedCategory);

    var fixedFullWeek = /^abroad_|^study_leave$/.test(type);
    var weekdaysInput = row.querySelector('.msd-weekdays');
    var weekdaysHint = row.querySelector('.msd-weekdays-hint');
    if (fixedFullWeek) {
      weekdaysInput.value = '5';
      weekdaysInput.disabled = true;
      weekdaysHint.textContent = 'σταθερά 5/5';
    } else {
      weekdaysInput.disabled = false;
      weekdaysHint.textContent = 'έως 5/5';
    }

    var remoteEligible = type === 'school' && ['I', 'IA', 'IB', 'IG'].indexOf(category) !== -1;
    remoteRow.classList.toggle('hidden', !remoteEligible);
    if (!remoteEligible) row.querySelector('.msd-remote-double').checked = false;

    var note = '';
    if (type === 'prison') note = 'Πλησιέστερη σχολική μονάδα + 5 μονάδες ανά έτος.';
    else if (type === 'digital_tutoring') note = 'Πλησιέστερη σχολική μονάδα + 6 μονάδες ανά έτος.';
    else if (type === 'listed_service_bonus2') note = 'Ισχύει μόνο για τις υπηρεσίες που απαριθμεί η εγκύκλιος: ΥΠΑΙΘΑ, ΠΔΕ, ΔΠΕ/ΔΔΕ, ΙΕΠ κ.ά.';
    else if (type === 'other_secondment') note = 'Χωρίς πρόσθετη προσαύξηση: μονάδες της πλησιέστερης σχολικής μονάδας.';
    else if (type === 'recognized_prior') note = 'Για προϋπηρεσία που η εγκύκλιος αναγνωρίζει και για Μ.Σ.Δ.';
    else if (type === 'abroad_europe') note = 'Η υπηρεσία θεωρείται Α΄ κατηγορίας και υπολογίζεται υποχρεωτικά ως 5/5 ημέρες την εβδομάδα.';
    else if (type === 'abroad_america') note = 'Η υπηρεσία θεωρείται Β΄ κατηγορίας και υπολογίζεται υποχρεωτικά ως 5/5 ημέρες την εβδομάδα.';
    else if (type === 'abroad_other') note = 'Η υπηρεσία θεωρείται Γ΄ κατηγορίας και υπολογίζεται υποχρεωτικά ως 5/5 ημέρες την εβδομάδα.';
    else if (type === 'study_leave') note = 'Η άδεια υπηρεσιακής εκπαίδευσης με αποδοχές θεωρείται Α΄ κατηγορίας και υπολογίζεται υποχρεωτικά ως 5/5 ημέρες την εβδομάδα.';
    row.querySelector('.transfer-row-note').textContent = note;
  }

  function readRow(row) {
    return {
      type: row.querySelector('.msd-type').value,
      category: row.querySelector('.msd-category').value,
      years: Number(row.querySelector('.msd-years').value) || 0,
      months: Number(row.querySelector('.msd-months').value) || 0,
      days: Number(row.querySelector('.msd-days').value) || 0,
      daysPerWeek: Number(row.querySelector('.msd-weekdays').value) || 0,
      remoteDouble: row.querySelector('.msd-remote-double').checked
    };
  }

  function syncMsdMode() {
    var direct = byId('msdMode').value === 'direct';
    byId('msdPeriodsPanel').style.display = direct ? 'none' : '';
    byId('msdDirectPanel').style.display = direct ? '' : 'none';
  }

  function calculate() {
    if (!global.EducationTransfer) return;
    var rows = Array.prototype.slice.call(msdRows.querySelectorAll('.transfer-service-row'));
    var input = {
      mode: byId('mode').value,
      serviceYears: Number(byId('serviceYears').value) || 0,
      serviceMonths: Number(byId('serviceMonths').value) || 0,
      serviceDays: Number(byId('serviceDays').value) || 0,
      familyStatusEligible: byId('familyStatusEligible').checked,
      eligibleChildren: Number(byId('eligibleChildren').value) || 0,
      coService: byId('coService').checked && !syncCoService(),
      locality: byId('locality').checked,
      firstPreference: byId('firstPreference').checked,
      msdMode: byId('msdMode').value,
      msdDirectPoints: byId('msdDirectPoints').value,
      msdPeriods: rows.map(readRow)
    };
    // Qualification-based procedures do not publish a comparative point ranking.
    if (input.mode === 'music' || input.mode === 'art' || input.mode === 'intercultural') {
      syncEligibilityDetails();
      if (global.EducationTransferDocuments) global.EducationTransferDocuments.render();
      return;
    }
    if (isEaeMode(input.mode)) input.mode = byId('transferEaeArea').value==='same'?'local':'region'; // Arithmetic depends on area; eligibility is separate.
    var result = global.EducationTransfer.calculate(input);

    // When direct M.S.D. is missing, the full result is unknown. Show a clearly
    // labelled provisional subtotal rather than the misleading bare dash.
    // Never treat an absent M.S.D. amount as zero for the final calculation.
    var missingMsd = !result.msdInputValid;
    var provisional = global.EducationTransfer.truncate2(
      result.servicePoints + result.coServicePoints + result.familyPoints +
      result.localityPoints + result.firstPreferencePoints
    );
    byId('totalResult').textContent = formatPoints(missingMsd ? provisional : result.total);
    var totalLabel = byId('transferTotalLabel');
    if (totalLabel) totalLabel.textContent = missingMsd ? 'προσωρινές μονάδες (χωρίς Μ.Σ.Δ.)' : 'συνολικές μονάδες';
    byId('servicePointsResult').textContent = formatPoints(result.servicePoints);
    byId('msdPointsResult').textContent = result.msdPoints === null ? '—' : formatPoints(result.msdPoints);
    // The static field hint already explains the entry format and the no-double-counting rule.
    // Only give an inline message for a genuinely invalid, non-empty value.
    var directValue = String(input.msdDirectPoints || '').trim();
    var directInvalid = input.msdMode === 'direct' && directValue !== '' && !result.msdInputValid;
    byId('msdDirectStatus').textContent = directInvalid ? 'Μη έγκυρη τιμή: δώστε μη αρνητικό αριθμό με έως δύο δεκαδικά.' : '';
    byId('msdDirectPoints').setAttribute('aria-invalid', directInvalid ? 'true' : 'false');
    byId('coServiceResult').textContent = formatPoints(result.coServicePoints);
    byId('familyResult').textContent = formatPoints(result.familyPoints);
    byId('localityResult').textContent = formatPoints(result.localityPoints);
    byId('firstPreferenceResult').textContent = formatPoints(result.firstPreferencePoints);

    rows.forEach(function (row, index) {
      var score = result.msdRows[index] ? result.msdRows[index].points : 0;
      row.querySelector('.transfer-row-score').textContent = formatPoints(score) + ' μόρια Μ.Σ.Δ.';
    });

    var duration = result.serviceDuration;
    var durationText = duration.years + ' έτη, ' + duration.months + ' μήνες';
    if (duration.roundedDays) durationText += ' (οι ' + duration.roundedDays + ' ημέρες λογίστηκαν ως μήνας)';
    var excludedCoService=byId('coService').checked && !input.coService;
    byId('statusResult').textContent = result.msdInputValid ? ('Συνολική υπηρεσία: ' + durationText + '. Οι ειδικές κατηγορίες/προτεραιότητες δεν περιλαμβάνονται στο άθροισμα.'+(excludedCoService?' Συνυπηρέτηση: 0 μόρια λόγω του επιλεγμένου μη επιλέξιμου εργασιακού καθεστώτος.':'')) : 'Το εμφανιζόμενο ποσό είναι προσωρινό και ΔΕΝ περιλαμβάνει Μ.Σ.Δ. Εισαγάγετε τον έγκυρο συνολικό αριθμό Μ.Σ.Δ. ή επιλέξτε υπολογισμό από υπηρετήσεις για την τελική βαθμολογία.';
  }

  function currentGeneralReturnInput(){
    return {origin:byId('transferGeneralOrigin').value,
      eaeYears:byId('transferGeneralEaeYears').value,
      areaYears:byId('transferGeneralEaeAreaYears').value,
      sinceTransferYears:byId('transferGeneralEaeSinceTransfer').value,
      organicServed:byId('transferGeneralEaeOrganic').checked};
  }
  function syncGeneralReturn(){
    var mode=byId('mode').value;
    var origin=byId('transferGeneralOrigin').value;
    byId('transferGeneralOriginField').style.display=mode==='region'?'':'none';
    byId('transferGeneralFromEaePanel').style.display=mode==='region' && origin!=='general'?'':'none';
    byId('transferGeneralEaeAppointed').style.display=mode==='region'&&origin==='appointed_eae'?'':'none';
    byId('transferGeneralEaeTransferred').style.display=mode==='region'&&origin==='transferred_eae'?'':'none';
    var eligibility=global.EducationTransferEligibility;
    if(eligibility&&eligibility.returnToGeneral && mode==='region' && origin!=='general'){
      var result=eligibility.returnToGeneral(currentGeneralReturnInput());
      var advice=byId('transferGeneralEaeAdvice');
      advice.textContent=result.status+(result.advice.length?' '+result.advice.join(' '):'');
      advice.setAttribute('data-eligibility-verdict',result.verdict);
      advice.classList.toggle('transfer-eligibility-failed',result.verdict==='not_established');
      advice.classList.toggle('transfer-eligibility-review',result.verdict==='needs_verification');
      advice.classList.toggle('transfer-eligibility-met',result.verdict==='preliminary_met');
    }
  }
  function syncCoService(){
    var active=byId('coService').checked;
    var field=byId('transferCoServiceCaseField');
    if(field)field.style.display=active?'':'none';
    var choice=byId('transferCoServiceCase').value;
    var docEngine=global.EducationTransferDocuments;
    var mode=byId('mode').value;
    if(isEaeMode(mode) && byId('transferEaeArea').value==='same')mode='local';
    var details=docEngine&&docEngine.coServiceInfo?docEngine.coServiceInfo(choice,mode):null;
    var advice=byId('transferCoServiceAdvice');
    if(advice)advice.textContent=active && details ? details.summary : '';
    return !!(active && details && details.blocked);
  }

  function syncEaeApplicationRoute(mode) {
    var dest=byId('transferEaeDestination'), branch=byId('transferEaeBranch');
    if(!dest || !branch)return;
    var sensory=mode==='eae_sensory', kedasy=mode==='kedasy', smeae=mode==='smeae';
    var field=byId('transferEaeDestinationField'), note=byId('transferEaeRouteNote');
    Array.prototype.forEach.call(dest.options,function(opt){opt.hidden=sensory?(['deaf','blind'].indexOf(opt.value)===-1):!(smeae&&opt.value==='smeae');});
    if(sensory && ['deaf','blind'].indexOf(dest.value)===-1)dest.value='deaf';
    if(kedasy)dest.value='kedasy';
    if(smeae)dest.value='smeae';
    if(field)field.style.display=sensory?'':'none';
    Array.prototype.forEach.call(branch.options,function(opt){opt.hidden=kedasy && ['other','pe02','pe03','pe04'].indexOf(opt.value)===-1;});
    if(kedasy && ['other','pe02','pe03','pe04'].indexOf(branch.value)===-1)branch.value='other';
    if(note)note.textContent=sensory?'Επιλέξτε Σχολείο Κωφών (ΕΝΓ) ή Τυφλών (Braille). Απαιτείται επιπλέον η αντίστοιχη πιστοποιημένη γνώση.':kedasy?'Για ΚΕΔΑΣΥ επιτρέπονται μόνο ΠΕ02, ΠΕ03 ή ΠΕ04, εφόσον προβλέπεται αντίστοιχη οργανική θέση και πληρούνται τα προσόντα ΕΑΕ.':smeae?'Η αίτηση αυτή αφορά ΣΜΕΑΕ/ΕΕΕΕΚ· Κωφών/Τυφλών και ΚΕΔΑΣΥ έχουν ξεχωριστούς κωδικούς αίτησης.':'';
    var pe11Qual=byId('transferEaeQualification').querySelector('option[value="pe11"]');
    if(pe11Qual)pe11Qual.hidden=kedasy;
    if(kedasy && byId('transferEaeQualification').value==='pe11')byId('transferEaeQualification').value='none';
  }

  function syncMode() {
    var mode = byId('mode').value;
    var isLocal = mode === 'local' || (isEaeMode(mode) && byId('transferEaeArea').value==='same');
    var qualifications = mode === 'music' || mode === 'art' || mode === 'intercultural';
    byId('firstPreferenceRow').classList.toggle('hidden', isLocal || qualifications);
    if (isLocal || qualifications) byId('firstPreference').checked = false;
    var guidance = byId('transferModeGuidance');
    var scoreBlock = byId('transferScoringResults');
    if (scoreBlock) scoreBlock.style.display = qualifications ? 'none' : '';
    ['transferNumericalInputs','transferMsdCard'].forEach(function(id){ var e=byId(id); if(e) e.style.display=qualifications?'none':''; });
    var q=byId('transferQualificationPanel'); if(q) q.style.display=qualifications?'':'none';
    var qt=byId('transferQualificationTitle'); if(qt) qt.textContent=mode==='music'?'Μετάθεση σε Μουσικό Σχολείο — Φάκελος προσόντων':mode==='art'?'Μετάθεση σε Καλλιτεχνικό Σχολείο — Φάκελος προσόντων':'Μετάθεση σε Πειραματικό Σχολείο Διαπολιτισμικής Εκπαίδευσης — Φάκελος προσόντων';
    var qi=byId('transferQualificationIntro'); if(qi) qi.textContent=mode==='music'?'Γενική παιδεία: ελέγχονται τα κοινά προσόντα, χωρίς απαίτηση μουσικής εξειδίκευσης. Μουσική παιδεία: απαιτείται πρόσθετη τεκμηρίωση ανά μουσικό αντικείμενο.':mode==='art'?'Για Καλλιτεχνικά: υπηρέτηση οργανικής, πενταετής διδακτική εμπειρία (με εξαιρέσεις), φάκελος προσόντων και ειδική κρίση για ΠΕ08, ΠΕ11, ΠΕ79.01, ΠΕ91.01, ΠΕ91.02. Δεν προβλέπεται αριθμητική κατάταξη.':'Για Πειραματικά Διαπολιτισμικής: θεμελίωση υπηρεσιακού δικαιώματος και συνεκτίμηση συγκεκριμένων προσόντων κατά σειρά προτεραιότητας, χωρίς μοριοδότηση ή ενιαία κατάταξη.';
    var eaePanel=byId('transferEaePanel');if(eaePanel)eaePanel.style.display=isEaeMode(mode)?'':'none';
    syncEaeApplicationRoute(mode);
    var musicPanel=byId('transferMusicEligibility');if(musicPanel)musicPanel.style.display=mode==='music'?'':'none';
    var artPanel=byId('transferArtEligibility');if(artPanel)artPanel.style.display=mode==='art'?'':'none';
    var crossPanel=byId('transferInterculturalEligibility');if(crossPanel)crossPanel.style.display=mode==='intercultural'?'':'none';
    var crossNotes=byId('transferInterculturalNotes');if(crossNotes)crossNotes.style.display=mode==='intercultural'?'':'none';
    var typeField=byId('transferEducationTypeField');if(typeField)typeField.style.display=mode==='music'?'':'none';
    var footnote=byId('transferQualificationFootnote');if(footnote)footnote.style.display=mode==='music'?'':'none';
    var typeLabel=byId('transferEducationType');if(typeLabel){
      var specialtyOption=typeLabel.querySelector('option[value="specialized"]');
      if(specialtyOption) specialtyOption.textContent=mode==='music'?'Μουσική παιδεία (ΠΕ79.01/ΤΕ16)':'Καλλιτεχνική εξειδίκευση';
    }
    syncGeneralReturn();
    syncCoService();
    syncEligibilityDetails();
    if(global.EducationTransferDocuments) global.EducationTransferDocuments.render();
    if (isEaeMode(mode)) {
      guidance.innerHTML = '<strong>ΣΜΕΑΕ / ΕΕΕΕΚ — ενδεικτικά μόρια:</strong> Πληροφοριακή μοριοδότηση με διαχωρισμό ίδιας/άλλης περιοχής. Ο προέλεγχος προσόντων και χρόνου υπηρεσίας <strong>δεν πιστοποιεί</strong> θεμελίωση δικαιώματος. Ελέγξτε τις υπηρεσιακές πράξεις, την εγκύκλιο και τη ΔΔΕ.';
    } else if (mode === 'music') {
      guidance.innerHTML = '<strong>Μουσικά Σχολεία — αξιολόγηση προσόντων, όχι μορίων.</strong> Δεν παρέχεται αριθμητική κατάταξη. Ελέγξτε τίτλους σπουδών, εξειδίκευση (ιδιαίτερα στις μουσικές ειδικότητες), προηγούμενη διδακτική εμπειρία, επιμόρφωση και τα αποδεικτικά που προβλέπει η οικεία εγκύκλιος. Μην υποβάλλετε άσχετα έγγραφα.';
    } else if (mode === 'art') {
      guidance.innerHTML = '<strong>Καλλιτεχνικά Σχολεία — χωρίς πίνακα μορίων.</strong> Ελέγχονται η υπηρέτηση οργανικής, η πενταετία (εκτός ειδικών εξαιρέσεων), οι τίτλοι/εμπειρία και η συνάφεια προσόντων ανά κλάδο. Για ΠΕ08, ΠΕ11, ΠΕ79.01, ΠΕ91.01, ΠΕ91.02 υπάρχει εισήγηση της Επιστημονικής Επιτροπής Καλλιτεχνικής Παιδείας. Βάση: εγκύκλιος 138014/Ε2/15-10-2026, Κεφ. ΣΤ΄, σελ. 41–43.';
    } else if (mode === 'intercultural') {
      guidance.innerHTML = '<strong>Πειραματικά Σχολεία Διαπολιτισμικής Εκπαίδευσης:</strong> ειδική διαδικασία προσόντων χωρίς υπολογισμό μορίων. Ελέγχονται η οργανική θέση έως 31/08/2027, η διετία για ήδη υπηρετούντες, τα οργανικά κενά και τα πρόσθετα προσόντα με τη σειρά της εγκυκλίου 138014/Ε2/2026, Κεφ. Ζ΄. Ο έλεγχος δεν υποκαθιστά απόφαση ΔΔΕ.';
    } else if (isLocal) {
      guidance.innerHTML = '<strong>Οριστική τοποθέτηση / βελτίωση:</strong> εξετάζονται οι δήμοι των συγκεκριμένων σχολείων και δεν ισχύουν μόρια πρώτης προτίμησης.';
    } else {
      guidance.innerHTML = '<strong>Μετάθεση από περιοχή σε περιοχή — Γενική Εκπαίδευση:</strong> περιλαμβάνει τη Γενική/Επαγγελματική Εκπαίδευση, όχι οργανικές θέσεις ΕΑΕ. Συνυπηρέτηση και εντοπιότητα αφορούν την περιοχή μετάθεσης· μόρια πρώτης προτίμησης όπου προβλέπονται.';
    }
    calculate();
  }

  function currentEaeInput() {
    return {origin:byId('transferEaeOrigin').value,area:byId('transferEaeArea').value,destination:byId('transferEaeDestination').value,branch:byId('transferEaeBranch').value,
      qualification:byId('transferEaeQualification').value,eaeYears:byId('transferEaeExperienceYears').value,
      teachingYears:byId('transferEaeTeachingYears').value,newly:byId('transferEaeNewly').checked,
      areaYears:byId('transferEaeAreaYears').value};
  }
  function currentMusicInput() {
    return {educationType:byId('transferEducationType').value,organic:byId('transferMusicOrganic').checked,
      teachingYears:byId('transferMusicTeachingYears').value,exceptionType:byId('transferMusicException').value,
      subject:byId('transferMusicSubject').value};
  }
  function currentArtInput() {
    return {branch:byId('transferArtBranch').value,orientation:byId('transferArtOrientation').value,
      organic:byId('transferArtOrganic').checked,teachingYears:byId('transferArtTeachingYears').value,
      exceptionType:byId('transferArtException').value};
  }
  function currentInterculturalInput(){
    return {organic:byId('transferInterculturalOrganic').value,
      current:byId('transferInterculturalCurrent').value,
      years:byId('transferInterculturalYears').value,
      vacancy:byId('transferInterculturalVacancy').value,
      languageCase:byId('transferInterculturalLanguageCase').value,
      qualifications:Array.prototype.map.call(document.querySelectorAll('[data-intercultural-qual]:checked'),function(el){return el.getAttribute('data-intercultural-qual');})};
  }
  function syncDossier(kind,selectId,changeId,outputId){
    var eligibility=global.EducationTransferEligibility;
    if(!eligibility || !eligibility.dossier)return;
    var r=eligibility.dossier({kind:kind,year:byId(selectId).value,newElements:byId(changeId).checked});
    byId(outputId).textContent=r.status+' '+r.limitation;
  }
  function syncEligibilityDetails() {
    var mode=byId('mode').value, eae=isEaeMode(mode),music=mode==='music';
    var sameArea=eae&&byId('transferEaeArea').value==='same';
    var preferenceRow=byId('firstPreferenceRow');
    if(preferenceRow)preferenceRow.classList.toggle('hidden',mode==='local'||sameArea||mode==='music'||mode==='art'||mode==='intercultural');
    if(sameArea)byId('firstPreference').checked=false;
    var origin=byId('transferEaeOrigin').value, route=byId('transferEaeQualification').value;
    byId('transferEaeExperienceRow').style.display=eae&&route==='experience'?'':'none';
    byId('transferEaeGeneralYearsRow').style.display=eae&&origin==='general'?'':'none';
    byId('transferEaeNewlyRow').style.display=eae&&origin==='general'?'':'none';
    byId('transferEaeAreaYearsRow').style.display=eae&&origin==='general'&&byId('transferEaeNewly').checked?'':'none';
    byId('transferMusicSpecializationPanel').style.display=music&&byId('transferEducationType').value==='specialized'?'':'none';
    var art=mode==='art',artBranch=byId('transferArtBranch').value;
    var isArtSpecialized=['pe08','pe11','pe79','pe9101','pe9102'].indexOf(artBranch)!==-1;
    byId('transferArtOrientationRow').style.display=art&&isArtSpecialized?'':'none';
    var elig=global.EducationTransferEligibility;
    if(elig&&eae){
      var result=elig.eae(currentEaeInput());
      byId('transferEaeAdvice').textContent=result.status+(result.advice.length?' · '+result.advice.join(' '):'');
      syncDossier('eae','transferEaePreviousYear','transferEaeNewDocuments','transferEaeDossierAdvice');
    }
    if(elig&&music){
      var mr=elig.music(currentMusicInput());
      byId('transferMusicAdvice').textContent=mr.status+(mr.advice.length?' · '+mr.advice.join(' '):'');
      syncDossier('music','transferMusicPreviousYear','transferMusicNewDocuments','transferMusicDossierAdvice');
    }
    if(elig&&mode==='intercultural'){
      byId('transferInterculturalYearsRow').style.display=byId('transferInterculturalCurrent').value==='yes'?'':'none';
      var ic=elig.intercultural(currentInterculturalInput());
      var message=byId('transferInterculturalAdvice');
      message.textContent=ic.status+(ic.advice.length?' · '+ic.advice.join(' '):'')+(ic.languageException?' · Σχολείο με πλειοψηφία μαθητών Ρομά: δεν απαιτείται το πρόσθετο προσόν ξένης γλώσσας προέλευσης.':'');
      message.setAttribute('data-eligibility-verdict',ic.verdict);
      message.classList.toggle('transfer-eligibility-failed',ic.verdict==='not_established');
      message.classList.toggle('transfer-eligibility-review',ic.verdict==='needs_verification');
      message.classList.toggle('transfer-eligibility-met',ic.verdict==='preliminary_met');
      syncDossier('intercultural','transferInterculturalPreviousYear','transferInterculturalNewDocuments','transferInterculturalDossierAdvice');
    }
    if(elig&&art){
      var ar=elig.art(currentArtInput());
      byId('transferArtAdvice').textContent=ar.status+(ar.advice.length?' · '+ar.advice.join(' '):'');
      syncDossier('art','transferArtPreviousYear','transferArtNewDocuments','transferArtDossierAdvice');
    }
  }

  function reset() {
    byId('mode').value = 'region';
    byId('transferGeneralOrigin').value='general';
    ['transferGeneralEaeYears','transferGeneralEaeAreaYears','transferGeneralEaeSinceTransfer'].forEach(function(id){byId(id).value='0';});
    byId('transferGeneralEaeOrganic').checked=false;
    byId('serviceYears').value = '0';
    byId('serviceMonths').value = '0';
    byId('serviceDays').value = '0';
    byId('eligibleChildren').value = '0';
    ['familyStatusEligible', 'coService', 'locality', 'firstPreference'].forEach(function (id) { byId(id).checked = false; });
    byId('transferEaeOrigin').value='general';
    byId('transferEaeArea').value='other';
    byId('transferEaeDestination').value='smeae';
    byId('transferEaeBranch').value='other';
    byId('transferEaeQualification').value='none';
    byId('transferEaeExperienceYears').value='0';
    byId('transferEaeTeachingYears').value='0';
    byId('transferEaeNewly').checked=false;
    byId('transferEaeAreaYears').value='0';
    byId('transferEaePreviousYear').value='none';
    byId('transferEaeNewDocuments').checked=false;
    byId('transferEducationType').value='general';
    byId('transferSpecialty').value='';
    byId('transferMusicTeachingYears').value='0';
    byId('transferMusicException').value='none';
    byId('transferMusicOrganic').checked=false;
    byId('transferMusicSubject').value='unspecified';
    byId('transferMusicPreviousYear').value='none';
    byId('transferMusicNewDocuments').checked=false;
    byId('transferArtBranch').value='general';
    byId('transferArtOrientation').value='';
    byId('transferArtTeachingYears').value='0';
    byId('transferArtException').value='none';
    byId('transferArtOrganic').checked=false;
    byId('transferArtPreviousYear').value='none';
    byId('transferArtNewDocuments').checked=false;
    ['transferInterculturalOrganic','transferInterculturalVacancy','transferInterculturalLanguageCase'].forEach(function(id){byId(id).value='unknown';});
    byId('transferInterculturalCurrent').value='no';
    byId('transferInterculturalYears').value='0';
    byId('transferInterculturalPreviousYear').value='none';
    byId('transferInterculturalNewDocuments').checked=false;
    document.querySelectorAll('[data-intercultural-qual]').forEach(function(el){el.checked=false;});
    byId('transferCoServiceCase').value = 'unspecified';
    byId('msdMode').value = 'periods';
    byId('msdDirectPoints').value = '';
    syncMsdMode();
    msdRows.innerHTML = '';
    rowCounter = 0;
    addRow({ type: 'school', category: 'A' }, { silent: true });
    syncMode();
    if(global.EducationTransferDocuments) global.EducationTransferDocuments.render();
  }

  function init() {
    if (initialized) return;
    initialized = true;
    msdRows = byId('msdRows');
    if (!msdRows || !global.EducationTransfer) return;

    byId('addMsdRow').addEventListener('click', function () { addRow(); });
    byId('msdMode').addEventListener('change', function () { syncMsdMode(); calculate(); });
    byId('resetBtn').addEventListener('click', reset);
    byId('mode').addEventListener('change', syncMode);
    if(global.EducationTransferDocuments) global.EducationTransferDocuments.init();

    document.addEventListener('input', function (event) {
      var target = event.target;
      if (!target) return;
      if (target.id === 'serviceYears' || target.classList.contains('msd-years')) clampInput(target, 50);
      if (target.id === 'transferEaeAreaYears' || target.id === 'transferGeneralEaeAreaYears') clampAreaYears(target);
      if (target.id === 'serviceMonths' || target.classList.contains('msd-months')) clampInput(target, 11);
      if (target.id === 'serviceDays' || target.classList.contains('msd-days')) clampInput(target, 29);
      if (target.id === 'eligibleChildren') clampInput(target, 20);
      if (target.classList.contains('msd-weekdays') && target.value !== '') {
        var n = Number(target.value);
        if (Number.isFinite(n)) target.value = String(Math.max(0, Math.min(5, n)));
      }
      if (target.closest && target.closest('.edu-page-transfer')) {syncGeneralReturn();syncEligibilityDetails();calculate();}
    });

    document.addEventListener('change', function (event) {
      var target = event.target;
      if (!target) return;
      if (target.id === 'transferEaeAreaYears' || target.id === 'transferGeneralEaeAreaYears') clampAreaYears(target);
      var row = target.closest ? target.closest('.transfer-service-row') : null;
      if (row && (target.classList.contains('msd-type') || target.classList.contains('msd-category'))) syncRow(row);
      if (target.closest && target.closest('.edu-page-transfer')) {syncGeneralReturn();syncEligibilityDetails();calculate();}
    });

    msdRows.addEventListener('click', function (event) {
      var button = event.target.closest ? event.target.closest('.remove-row') : null;
      if (!button) return;
      var row = button.closest('.transfer-service-row');
      if (row) row.remove();
      if (!msdRows.querySelector('.transfer-service-row')) addRow(null, { silent: true });
      calculate();
    });

    addRow({ type: 'school', category: 'A' }, { silent: true });
    syncMsdMode();
    syncMode();
  }

  global.EducationTransferUI = Object.freeze({
    init: init,
    calculate: calculate,
    reset: reset,
    addRow: addRow,
    syncMode: syncMode
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})(window);
