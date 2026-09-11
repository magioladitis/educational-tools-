/* Extracted UI/controller logic from ypologismos-morion-apospasis-sde.php. */
(function(){
  const $ = id => document.getElementById(id);
  const yes = id => $(id).value === 'yes';
  const value = id => $(id).value;
  function specialtyCode(){
    const raw = value('specialty');
    if(globalThis.EducationCore && typeof globalThis.EducationCore.normalizeSpecialtyCode === 'function'){
      return globalThis.EducationCore.normalizeSpecialtyCode(raw);
    }
    return String(raw || '').trim().toUpperCase()
      .replace(/^(?:PE|PΕ|ΠE|ΠΕ)/, 'ΠΕ')
      .replace(/^(?:TE|TΕ|ΤE|ΤΕ)/, 'ΤΕ')
      .replace(/^(?:DE|DΕ|ΔE|ΔΕ)/, 'ΔΕ');
  }
  const numberValue = id => Math.max(0, Number($(id).value || 0));

  function fmt(n) {
    const rounded = Math.round((Number(n) || 0) * 100) / 100;
    return Number.isInteger(rounded) ? String(rounded) : rounded.toFixed(2).replace('.', ',');
  }

  function normalizeYearField(id, whole) {
    const el = $(id);
    if (!el || el.value === '') return;
    let value = Number(el.value);
    if (!Number.isFinite(value)) value = 0;
    value = Math.max(0, value);
    if (whole) value = Math.floor(value);
    const max = el.getAttribute('max');
    if (max !== null && max !== '') value = Math.min(value, Number(max));
    el.value = String(value);
  }

  function specialtyChanged() {
    const sp = specialtyCode();
    const needsMathCondition = sp === 'ΠΕ86' || sp.startsWith('ΠΕ04.');
    $('mathInfoDegreeWrap').classList.toggle('hidden', !needsMathCondition);
    $('formerPE09Wrap').classList.toggle('hidden', sp !== 'ΠΕ80');
    $('formerPE1208Wrap').classList.toggle('hidden', sp !== 'ΠΕ85');
    if(!needsMathCondition) $('mathInfoDegree').value = 'no';
    if(sp !== 'ΠΕ80') $('formerPE09').value = 'no';
    if(sp !== 'ΠΕ85') $('formerPE1208').value = 'no';
    if (sp === 'ΠΕ86') {
      $('computer').value = 'yes';
      $('computer').disabled = true;
    } else {
      $('computer').disabled = false;
    }
    calculate();
  }

  function getData() {
    return {
      specialty: specialtyCode(),
      phd: value('phd'),
      master: value('master'),
      secondDegree: yes('secondDegree'),
      secondPhd: value('secondPhd'),
      secondMaster: value('secondMaster'),
      sdeTrainingHours: numberValue('sdeTrainingHours'),
      adultTrainingHours: numberValue('adultTrainingHours'),
      sdeYears: numberValue('sdeYears'),
      sdeHourlyHours: numberValue('sdeHourlyHours'),
      adultEducationHours: numberValue('adultEducationHours'),
      formalEducationYears: numberValue('formalEducationYears'),
      eligibilitySchoolYears: numberValue('eligibilitySchoolYears'),
      languages: AsepLanguageSelector.readEntries('sdeLanguages'),
      computer: yes('computer'),
      flags: {
        mathOrInformaticsDegree: yes('mathInfoDegree'),
        formerPE09or15: yes('formerPE09'),
        formerPE1208: yes('formerPE1208')
      }
    };
  }

  function renderAssignments(assignments) {
    if (!value('specialty')) return '<span class="subtitle">Επίλεξε ειδικότητα.</span>';
    if (!assignments.length) return '<div class="danger">Η επιλεγμένη ειδικότητα δεν περιλαμβάνεται στους κλάδους του άρθρου 5 για τα γνωστικά αντικείμενα/τμήματα που αναφέρονται στην απόφαση.</div>';
    return assignments.map(item => '<div class="assignment"><strong>' + item.literacy + '</strong><span class="badge">' + item.assignment + '</span>' + (item.note ? '<small>' + item.note + '</small>' : '') + '</div>').join('');
  }

  function detailRows(title, obj) {
    let html = '<h3>' + title + '</h3>';
    if (!obj.details || !obj.details.length) return html + '<div class="subtitle">—</div>';
    obj.details.forEach(d => { html += '<div class="result-row"><span>' + d.label + '</span><strong>' + fmt(d.points) + '</strong></div>'; });
    return html;
  }

  function calculate() {
    normalizeYearField('eligibilitySchoolYears', false);
    normalizeYearField('formalEducationYears', true);
    normalizeYearField('sdeYears', true);
    const result = SDECalculator.calculateAll(getData());
    $('totalScore').textContent = fmt(result.total);
    $('totalBar').style.width = Math.min(100, result.total / 40 * 100) + '%';
    $('educationScore').textContent = fmt(result.education.total) + ' / 22';
    $('experienceScore').textContent = fmt(result.experience.total) + ' / 13';
    $('otherScore').textContent = fmt(result.other.total) + ' / 5';
    $('formalExperiencePreview').textContent = fmt(result.experience.formalPoints) + ' μόρια';

    const assignmentsHtml = renderAssignments(result.assignments);
    $('assignmentResult').innerHTML = assignmentsHtml;
    $('assignmentBox').innerHTML = assignmentsHtml;
    $('assignmentBox').className = result.assignments.length ? 'success' : (value('specialty') ? 'danger' : 'info');

    const messages = [];
    const specialtySelected = Boolean(value('specialty'));
    const blockingValue = value('blockingIssue');
    const teleValue = value('teleEducation');

    if (!specialtySelected) messages.push('<div class="result-message edu-message result-message--status edu-message--status"><strong>Επίλεξε ειδικότητα</strong> για να ελεγχθούν οι αποδεκτοί γραμματισμοί.</div>');
    const eligibilityYearsAnswered = $('eligibilitySchoolYears').value !== '';
    if (!eligibilityYearsAnswered) {
      messages.push('<div class="result-message edu-message result-message--status edu-message--status"><strong>Δικαίωμα αίτησης:</strong> συμπλήρωσε τη διδακτική υπηρεσία σε σχολεία Πρωτοβάθμιας/Δευτεροβάθμιας.</div>');
    } else if (!result.eligibleByTwoYears) {
      messages.push('<div class="result-message edu-message result-message--warning edu-message--warning"><strong>Δεν συμπληρώνονται τα 2 απαιτούμενα έτη διδακτικής υπηρεσίας.</strong></div>');
    }

    if (blockingValue === '') {
      messages.push('<div class="result-message edu-message result-message--warning edu-message--warning"><strong>Κωλύματα:</strong> δήλωσε αν υπάρχει πιθανό κώλυμα του άρθρου 4.</div>');
    } else if (blockingValue === 'yes') {
      messages.push('<div class="result-message edu-message result-message--warning edu-message--warning"><strong>Δήλωσες πιθανό κώλυμα του άρθρου 4.</strong> Απαιτείται έλεγχος πριν την αίτηση.</div>');
    } else if (specialtySelected && eligibilityYearsAnswered && result.eligibleByTwoYears) {
      messages.push('<div class="result-message edu-message result-message--success edu-message--success"><strong>Ο βασικός έλεγχος των 2 ετών/κωλύματος είναι θετικός.</strong> Έλεγξε πάντως όλες τις προϋποθέσεις της πρόσκλησης.</div>');
    }

    if ($('formalEducationYears').value !== '' && eligibilityYearsAnswered && Math.floor(numberValue('formalEducationYears')) < Math.floor(numberValue('eligibilitySchoolYears'))) {
      messages.push('<div class="result-message edu-message result-message--warning edu-message--warning"><strong>Έλεγχος ετών:</strong> τα συνολικά πλήρη έτη τυπικής εκπαίδευσης που δήλωσες για μοριοδότηση είναι λιγότερα από τα έτη Πρωτοβάθμιας/Δευτεροβάθμιας που δήλωσες για επιλεξιμότητα. Έλεγξε τις καταχωρίσεις.</div>');
    }

    if (specialtySelected && !result.assignments.length) messages.push('<div class="result-message edu-message result-message--warning edu-message--warning">Δεν εντοπίζεται αποδεκτός γραμματισμός για την επιλεγμένη ειδικότητα στο άρθρο 5.</div>');

    if (teleValue === '') {
      messages.push('<div class="result-message edu-message result-message--status edu-message--status">Δήλωσε αν αποδέχεσαι σύγχρονη τηλεκπαίδευση· χρησιμοποιείται ως πρώτο κριτήριο ισοβαθμίας.</div>');
    } else if (teleValue === 'no') {
      messages.push('<div class="result-message edu-message result-message--warning edu-message--warning">Σε ισοβαθμία προηγείται υποψήφιος που έχει αποδεχτεί τη σύγχρονη τηλεκπαίδευση.</div>');
    }
    $('eligibilityStatus').innerHTML = messages.join('');

    let breakdown = detailRows('Εκπαίδευση', result.education) + detailRows('Διδακτική εμπειρία', result.experience) + detailRows('Άλλα προσόντα', result.other);
    [...result.education.warnings, ...result.other.warnings].forEach(w => { breakdown += '<div class="warning">' + w + '</div>'; });
    $('breakdown').innerHTML = breakdown;
  }

  function resetForm() {
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = '0');
    $('formalEducationYears').value = '';
    $('eligibilitySchoolYears').value = '';
    document.querySelectorAll('select').forEach(el => {
      if (el.id === 'teleEducation' || el.id === 'blockingIssue') el.value = '';
      else el.selectedIndex = 0;
    });
    AsepLanguageSelector.reset('sdeLanguages', { silent: true });
    specialtyChanged();
  }

  document.addEventListener('asep-language-change', calculate);

  specialtyChanged();

  // External event bindings replacing the former inline onchange/oninput/onclick handlers.
  $('specialty').addEventListener('change', specialtyChanged);
  ['mathInfoDegree','formerPE09','formerPE1208','teleEducation','blockingIssue','phd','master','secondDegree','secondPhd','secondMaster','computer'].forEach(id => $(id).addEventListener('change', calculate));
  ['eligibilitySchoolYears','formalEducationYears','sdeTrainingHours','adultTrainingHours','sdeYears','sdeHourlyHours','adultEducationHours'].forEach(id => $(id).addEventListener('input', calculate));
  $('sdeDetachmentResetBtn').addEventListener('click', resetForm);
})();
