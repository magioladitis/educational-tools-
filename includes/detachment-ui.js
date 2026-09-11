/*
 * Browser UI controller for ypologismos-morion-apospasis.php.
 * Business rules live in detachment-calculations.js.
 */
(function (global) {
  'use strict';

  var initialized = false;
  var isLiveCalculation = false;
  function byId(id) { return document.getElementById(id); }
  function valueOf(id) { var el = byId(id); return el ? el.value : ''; }
  function numberOf(id) {
    var value = parseFloat(valueOf(id));
    return Number.isFinite(value) ? value : 0;
  }
  function checked(id) { var el = byId(id); return !!(el && el.checked); }

  function formatPoints(value) {
    var truncated = Math.floor((Number(value || 0) + Number.EPSILON) * 100) / 100;
    if (Number.isInteger(truncated)) return truncated.toString();
    return truncated.toFixed(2).replace('.', ',').replace(/0$/, '');
  }

  function formatPointsFixed(value) {
    return new Intl.NumberFormat('el-GR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(Number(value) || 0);
  }

  function escapeHtml(text) {
    return String(text)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function updateSidebarSummary(summary) {
    summary = summary || {};
    var values = {
      grandTotal: summary.total || 0,
      resService: summary.service || 0,
      resCoService: summary.coService || 0,
      resLocality: summary.locality || 0,
      resFamily: summary.family || 0,
      resHealth: summary.health || 0,
      resStudies: summary.studies || 0
    };
    Object.keys(values).forEach(function (id) {
      var el = byId(id);
      if (el) el.textContent = formatPointsFixed(values[id]);
    });

    var statusBox = byId('sidebarStatus');
    if (!statusBox) return;
    var variant = summary.variant || 'status';
    statusBox.className = 'result-message edu-message result-message--' + variant + ' edu-message--' + variant;
    statusBox.textContent = summary.status || 'Συμπλήρωσε τα στοιχεία σου για ζωντανό υπολογισμό.';
  }

  function updateStudyPointsStatus(calc, input) {
    var box = byId('studyPointsStatus');
    if (!box) return;

    input = input || {};
    var studyType = input.studyType || 'none';
    var points = calc && Number(calc.studiesPoints) ? Number(calc.studiesPoints) : 0;
    var message = '';

    if (studyType === 'eligible') {
      var missing = [];
      if (!input.studyDifferentArea) missing.push('η σχολή να βρίσκεται σε διαφορετική περιοχή από την οργανική');
      if (!input.studyRequestedArea) missing.push('ο υπολογισμός να αφορά το ΠΥΣΠΕ/ΠΥΣΔΕ όπου βρίσκεται η σχολή');
      if (!input.studyWithinDuration) missing.push('να βρίσκεσαι μέσα στον προβλεπόμενο χρόνο φοίτησης');

      if (points === 2 && missing.length === 0) {
        message = '<strong>Μόρια σπουδών: 2,00 / 2,00.</strong> Πληρούνται και οι τρεις δηλωμένες προϋποθέσεις.';
      } else {
        message = '<strong>Μόρια σπουδών: 0,00 / 2,00.</strong> Για να δοθούν τα 2 μόρια χρειάζεται ακόμη: ' + escapeHtml(missing.join(' · ')) + '.';
      }
    } else if (studyType === 'eap') {
      message = '<strong>Μόρια σπουδών: 0,00 / 2,00.</strong> Οι σπουδές στο ΕΑΠ δεν μοριοδοτούνται με αυτό το κριτήριο.';
    } else if (studyType === 'phd') {
      message = '<strong>Μόρια σπουδών: 0,00 / 2,00.</strong> Το διδακτορικό δεν μοριοδοτείται με αυτό το κριτήριο.';
    } else {
      message = '<strong>Μόρια σπουδών: 0,00 / 2,00.</strong> Επίλεξε τύπο σπουδών και επιβεβαίωσε τις απαιτούμενες προϋποθέσεις.';
    }

    box.innerHTML = message;
  }

  function showError(message) {
    updateSidebarSummary({ status: message, variant: 'warning' });
    var result = byId('result');
    if (!result) return;
    result.style.display = 'block';
    result.className = 'result error';
    result.textContent = message;
    if (!isLiveCalculation) result.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function showResult(html) {
    var result = byId('result');
    if (!result) return;
    result.style.display = 'block';
    result.className = 'result';
    result.innerHTML = html;
    if (!isLiveCalculation) result.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function readInput() {
    return {
      appointmentStatus: valueOf('appointmentStatus'),
      obstacleMusicExclusive: checked('obstacleMusicExclusive'),
      obstacleLeader: checked('obstacleLeader'),
      obstacleTermDetachment: checked('obstacleTermDetachment'),
      obstacleActiveDetachment: checked('obstacleActiveDetachment'),
      obstacleESK: checked('obstacleESK'),
      obstacleSuspension: checked('obstacleSuspension'),
      obstacleEaeGeneral: checked('obstacleEaeGeneral'),
      prioritySpecialCategory: checked('prioritySpecialCategory'),
      priorityNewSelfSpouse75: checked('priorityNewSelfSpouse75'),
      priorityNewChild67: checked('priorityNewChild67'),
      priorityCoServiceCategory: valueOf('priorityCoServiceCategory'),
      priorityElected: checked('priorityElected'),
      priorityFirstPreference: checked('priorityFirstPreference'),
      requestedArea: valueOf('requestedArea'),
      serviceYears: numberOf('serviceYears'),
      serviceMonths: numberOf('serviceMonths'),
      serviceDays: numberOf('serviceDays'),
      coServiceType: valueOf('coServiceType'),
      coServiceOneYearSameArea: checked('coServiceOneYearSameArea'),
      coServiceWorkedDay: checked('coServiceWorkedDay'),
      locality: checked('locality'),
      familyStatus: valueOf('familyStatus'),
      eligibleChildren: numberOf('eligibleChildren'),
      healthPerson: valueOf('healthPerson'),
      healthSelfFamily: valueOf('healthSelfFamily'),
      healthChildProtected: checked('healthChildProtected'),
      healthParents: valueOf('healthParents'),
      parentLocationEligible: checked('parentLocationEligible'),
      siblingHealth: checked('siblingHealth'),
      ivf: checked('ivf'),
      studyType: valueOf('studyType'),
      studyDifferentArea: checked('studyDifferentArea'),
      studyRequestedArea: checked('studyRequestedArea'),
      studyWithinDuration: checked('studyWithinDuration')
    };
  }

  function renderCalculation(calc) {
    var serviceDetails = [];
    if (calc.service.firstBandPoints > 0) serviceDetails.push('Πρώτη κλίμακα (έως 10 έτη): ' + formatPoints(calc.service.firstBandPoints));
    if (calc.service.secondBandPoints > 0) serviceDetails.push('Δεύτερη κλίμακα (10–20 έτη): ' + formatPoints(calc.service.secondBandPoints));
    if (calc.service.thirdBandPoints > 0) serviceDetails.push('Τρίτη κλίμακα (πάνω από 20 έτη): ' + formatPoints(calc.service.thirdBandPoints));

    var countedServiceText = calc.service.countedYears + ' έτη' + (calc.service.countedMonths ? ' και ' + calc.service.countedMonths + ' μήνες' : '');
    var familyAnalysis = [];
    if (calc.familyBasePoints > 0) familyAnalysis.push(global.EducationDetachment.familyStatusLabel(calc.familyStatus) + ': ' + formatPoints(calc.familyBasePoints) + ' μόρια');
    if (calc.childrenPoints > 0) familyAnalysis.push(calc.children + ' τέκνο/τέκνα: ' + formatPoints(calc.childrenPoints) + ' μόρια');

    var healthAnalysis = [];
    if (calc.selfFamilyHealthPoints > 0) healthAnalysis.push('Εκπαιδευτικός / τέκνο / σύζυγος: ' + calc.selfFamilyHealthPoints + ' μόρια');
    if (calc.parentHealthPoints > 0) healthAnalysis.push('Γονέας εκπαιδευτικού: ' + calc.parentHealthPoints + ' μόρια');
    if (calc.siblingHealthPoints > 0) healthAnalysis.push('Αδελφός/ή: 5 μόρια');
    if (calc.ivfPoints > 0) healthAnalysis.push('Εξωσωματική γονιμοποίηση: 3 μόρια');

    var statusHtml = '';
    if (calc.eligibility.obstacleReasons.length > 0) {
      statusHtml = '<div class="status-box stop">🔴 Πιθανό κώλυμα εξέτασης της αίτησης:<br>' + calc.eligibility.obstacleReasons.map(function (x) { return '• ' + escapeHtml(x); }).join('<br>') + '<br><span class="edu-fw-normal">Τα μόρια εμφανίζονται μόνο πληροφοριακά· απαιτείται έλεγχος από την αρμόδια Διεύθυνση Εκπαίδευσης.</span></div>';
    } else if (calc.eligibility.priorityReasons.length > 0) {
      statusHtml = '<div class="status-box ok">🟢 Πιθανή υπαγωγή σε απόσπαση κατά προτεραιότητα:<br>' + calc.eligibility.priorityReasons.map(function (x) { return '• ' + escapeHtml(x); }).join('<br>') + '<br><span class="edu-fw-normal">Η κατά προτεραιότητα διαδικασία δεν προσθέτει μόρια· αλλάζει τον τρόπο εξέτασης της αίτησης.</span></div>';
    } else if (calc.eligibility.priorityBlockedByFirstChoice) {
      statusHtml = '<div class="status-box warn">🟠 Δηλώθηκε πιθανή κατηγορία κατά προτεραιότητα λόγω συνυπηρέτησης, αλλά δεν επιβεβαιώθηκε η απαιτούμενη 1η προτίμηση.</div>';
    } else {
      statusHtml = '<div class="status-box warn">ℹ️ Με βάση μόνο τις απαντήσεις που δόθηκαν, δεν εντοπίστηκε από το εργαλείο βασικό κώλυμα ή ενεργή κατηγορία κατά προτεραιότητα. Αυτό δεν αποτελεί επίσημη πιστοποίηση.</div>';
    }

    var areaText = calc.requestedArea ? ' για <strong>' + escapeHtml(calc.requestedArea) + '</strong>' : '';
    var html = statusHtml +
      '<div class="score-big">' +
        '<span class="number">' + formatPoints(calc.total) + '</span>' +
        '<div class="caption">ενδεικτικά μόρια απόσπασης' + areaText + '</div>' +
      '</div>' +
      '<h2>Ανάλυση μοριοδότησης</h2>' +
      '<table class="breakdown">' +
        '<tr><th>Κριτήριο</th><th>Μόρια</th><th>Ανάλυση</th></tr>' +
        '<tr><td>Συνολική υπηρεσία</td><td class="points">' + formatPoints(calc.service.total) + '</td><td>' + countedServiceText + (serviceDetails.length ? '<br>' + serviceDetails.join('<br>') : '') + '</td></tr>' +
        '<tr><td>Συνυπηρέτηση</td><td class="points">' + formatPoints(calc.coServicePoints) + '</td><td>' + (calc.coServicePoints ? 'Πληρούνται οι δηλωμένες προϋποθέσεις για τη συγκεκριμένη περιοχή.' : '—') + '</td></tr>' +
        '<tr><td>Εντοπιότητα</td><td class="points">' + formatPoints(calc.localityPoints) + '</td><td>' + (calc.localityPoints ? 'Δηλώθηκε ότι πληρούνται οι προϋποθέσεις εντοπιότητας.' : '—') + '</td></tr>' +
        '<tr><td>Οικογενειακοί λόγοι</td><td class="points">' + formatPoints(calc.familyTotal) + '</td><td>' + (familyAnalysis.length ? familyAnalysis.join('<br>') : '—') + '</td></tr>' +
        '<tr><td>Σοβαροί λόγοι υγείας</td><td class="points">' + formatPoints(calc.healthTotal) + '</td><td>' + (healthAnalysis.length ? healthAnalysis.join('<br>') : '—') + '</td></tr>' +
        '<tr><td>Σπουδές</td><td class="points">' + formatPoints(calc.studiesPoints) + '</td><td>' + (calc.studiesPoints ? 'Πληρούνται οι δηλωμένες προϋποθέσεις: 2 μόρια' : '—') + '</td></tr>' +
        '<tr class="total-row"><td>ΣΥΝΟΛΟ</td><td class="points">' + formatPoints(calc.total) + '</td><td>Ενδεικτικός υπολογισμός</td></tr>' +
      '</table>';

    if (calc.info.length > 0) {
      html += '<div class="subtle-box"><strong>Χρήσιμες επισημάνσεις:</strong><br>' + calc.info.map(function (x) { return '• ' + escapeHtml(x); }).join('<br>') + '</div>';
    }
    if (calc.warnings.length > 0) {
      html += '<div class="warning">Προσοχή:<br>' + calc.warnings.map(function (x) { return '• ' + escapeHtml(x); }).join('<br>') + '</div>';
    }
    return html;
  }

  function calculatePoints() {
    if (!global.EducationDetachment) return;
    var input = readInput();
    var calc = global.EducationDetachment.calculate(input);
    if (calc.error) return showError(calc.error);

    updateStudyPointsStatus(calc, input);
    updateSidebarSummary({
      total: calc.total,
      service: calc.service.total,
      coService: calc.coServicePoints,
      locality: calc.localityPoints,
      family: calc.familyTotal,
      health: calc.healthTotal,
      studies: calc.studiesPoints,
      status: calc.sidebarStatus,
      variant: calc.sidebarVariant
    });
    showResult(renderCalculation(calc));
  }

  function liveCalculatePoints() {
    isLiveCalculation = true;
    try { calculatePoints(); } finally { isLiveCalculation = false; }
  }

  function resetCalculator() {
    document.querySelectorAll('.edu-page-detachment input[type="checkbox"]').forEach(function (el) { el.checked = false; });
    document.querySelectorAll('.edu-page-detachment input[type="number"]').forEach(function (el) { el.value = 0; });
    if (byId('requestedArea')) byId('requestedArea').value = '';

    var defaults = {
      appointmentStatus: '',
      priorityCoServiceCategory: 'none',
      coServiceType: 'none',
      familyStatus: 'none',
      healthPerson: 'none',
      healthSelfFamily: '0',
      healthParents: '0',
      studyType: 'none'
    };
    Object.keys(defaults).forEach(function (id) {
      if (byId(id)) byId(id).value = defaults[id];
    });

    var result = byId('result');
    if (result) {
      result.style.display = 'none';
      result.innerHTML = '';
      result.className = 'result';
    }
    updateStudyPointsStatus(null, { studyType: 'none' });
    updateSidebarSummary();
    global.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function clampIntegerInput(target) {
    if (!target || target.value === '') return;
    var value = Math.max(0, Math.floor(Number(target.value) || 0));
    var max = target.getAttribute('max');
    if (max !== null && max !== '') value = Math.min(value, Number(max));
    target.value = String(value);
  }

  function init() {
    if (initialized) return;
    initialized = true;
    if (!document.querySelector('.edu-page-detachment') || !global.EducationDetachment) return;

    var calculateBtn = byId('calculateBtn');
    var resetBtn = byId('resetBtn');
    if (calculateBtn) calculateBtn.addEventListener('click', calculatePoints);
    if (resetBtn) resetBtn.addEventListener('click', resetCalculator);

    // Initialize the visible study-score diagnostic from one consistent DOM snapshot.
    var initialInput = readInput();
    updateStudyPointsStatus(global.EducationDetachment.calculate(initialInput), initialInput);

    document.addEventListener('input', function (event) {
      var target = event.target;
      if (!target || !target.closest || !target.closest('.edu-page-detachment')) return;
      if (['serviceYears', 'serviceMonths', 'serviceDays', 'eligibleChildren'].indexOf(target.id) !== -1) clampIntegerInput(target);
      if (target.matches('input, select')) liveCalculatePoints();
    });
    document.addEventListener('change', function (event) {
      var target = event.target;
      if (!target || !target.closest || !target.closest('.edu-page-detachment')) return;
      if (target.matches('input, select')) liveCalculatePoints();
    });

  }

  global.EducationDetachmentUI = {
    init: init,
    calculate: calculatePoints,
    reset: resetCalculator
  };

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})(window);
