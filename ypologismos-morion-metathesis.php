<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Μόρια Μετάθεσης</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-transfer">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorContainerStart(array('class' => 'app')); ?>
  <?php calculatorHero(array(
    'title' => 'Μόρια Μετάθεσης',
    'intro_html' => 'Πρώτη έκδοση για εκπαιδευτικούς <strong>Δευτεροβάθμιας Εκπαίδευσης</strong>, με τα έξι βασικά κριτήρια μετάθεσης και αναλυτικό υπολογισμό Μονάδων Συνθηκών Διαβίωσης (Μ.Σ.Δ.).',
    'badges' => array('Δ.Ε. 2025–2026', 'Μ.Σ.Δ.', 'Δυσπρόσιτα / ειδικές υπηρετήσεις')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title' => 'Α. Βασικά κριτήρια μετάθεσης')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="mode">Τύπος διαδικασίας</label>
            <select id="mode">
              <option value="region">Μετάθεση από περιοχή σε περιοχή</option>
              <option value="local">Οριστική τοποθέτηση / βελτίωση εντός περιοχής</option>
            </select>
          </div>
          <div class="field">
            <label for="serviceYears">Συνολική αναγνωρισμένη υπηρεσία — έτη</label>
            <input id="serviceYears" type="number" min="0" max="40" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceMonths">Επιπλέον μήνες</label>
            <input id="serviceMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceDays">Επιπλέον ημέρες <small>15+ → 1 μήνας</small></label>
            <input id="serviceDays" type="number" min="0" max="29" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="eligibleChildren">Τέκνα που μοριοδοτούνται</label>
            <input id="eligibleChildren" type="number" min="0" max="20" step="1" value="0" inputmode="numeric">
            <div class="field-hint">4 + 4 + 6 + 7 μόρια για κάθε επόμενο τέκνο.</div>
          </div>
        </div>

        <div class="check-row"><label><input id="familyStatusEligible" type="checkbox"> Δικαιούμαι τις 4 μονάδες οικογενειακής κατάστασης (γάμος/σύμφωνο συμβίωσης ή προβλεπόμενη περίπτωση γονέα με επιμέλεια)</label></div>
        <div class="check-row"><label><input id="coService" type="checkbox"> Δικαιούμαι συνυπηρέτηση για την περιοχή/τον δήμο που εξετάζω (+4)</label></div>
        <div class="check-row"><label><input id="locality" type="checkbox"> Δικαιούμαι εντοπιότητα για την περιοχή/τον δήμο που εξετάζω (+4)</label></div>
        <div class="check-row" id="firstPreferenceRow"><label><input id="firstPreference" type="checkbox"> Η περιοχή είναι η πρώτη προτίμησή μου (+2)</label></div>

        <div class="info-note" id="modeNote">
          Στη μετάθεση από περιοχή σε περιοχή, συνυπηρέτηση και εντοπιότητα εξετάζονται για την περιοχή μετάθεσης. Στην οριστική τοποθέτηση/βελτίωση συνδέονται με τον συγκεκριμένο δήμο όπου βρίσκεται το σχολείο. Η πρώτη προτίμηση μοριοδοτείται μόνο στη μετάθεση από περιοχή σε περιοχή.
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('title' => 'Β. Μονάδες Συνθηκών Διαβίωσης (Μ.Σ.Δ.)')); ?>
        <p class="small-note">Πρόσθεσε μία γραμμή για κάθε διαφορετική υπηρέτηση/κατηγορία. Οι ημέρες ≥15 λογίζονται ως πλήρης μήνας. Για παράλληλη υπηρεσία μπορείς να δηλώσεις τις ημέρες ανά εβδομάδα (σύνολο 5/5).</p>
        <div id="msdRows"></div>
        <button class="add-row" id="addMsdRow" type="button">+ Προσθήκη υπηρέτησης</button>

        <div class="info-note">
          <strong>Διπλασιασμός απομακρυσμένων:</strong> από το 2018–2019 αφορά πραγματική υπηρεσία σε σχολεία κατηγοριών Ι΄, ΙΑ΄, ΙΒ΄ και ΙΓ΄, όταν έχει συμπληρωθεί τουλάχιστον διετής συνεχόμενη υπηρεσία. Στον υπολογιστή ενεργοποίησέ τον μόνο όταν το δικαίωμα έχει πράγματι θεμελιωθεί.
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorActions(array(
        array('id' => 'calculateBtn', 'class' => 'primary', 'label' => 'Υπολογισμός μορίων'),
        array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
      )); ?>

      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Πρώτη έκδοση:</strong> υπολογίζει τη βαθμολογία των βασικών κριτηρίων και τις συχνότερες ειδικές Μ.Σ.Δ. της εγκυκλίου Δ.Ε. 2025–2026. Δεν ελέγχει ακόμη δικαίωμα μετάθεσης, ειδικές κατηγορίες προτεραιότητας, όλες τις ιστορικές εξαιρέσεις ή σύνθετες υπηρετήσεις που απαιτούν υπηρεσιακή πράξη.'
      )); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value' => '0',
        'value_id' => 'totalResult',
        'label' => 'συνολικές μονάδες',
        'cap' => 'Ενδεικτική βαθμολογία μετάθεσης'
      )); ?>
      <?php calculatorResultRow(array('label' => 'Συνολική υπηρεσία', 'value' => '0', 'value_id' => 'servicePointsResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μ.Σ.Δ.', 'value' => '0', 'value_id' => 'msdPointsResult')); ?>
      <?php calculatorResultRow(array('label' => 'Συνυπηρέτηση', 'value' => '0', 'value_id' => 'coServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Οικογενειακοί λόγοι', 'value' => '0', 'value_id' => 'familyResult')); ?>
      <?php calculatorResultRow(array('label' => 'Εντοπιότητα', 'value' => '0', 'value_id' => 'localityResult')); ?>
      <?php calculatorResultRow(array('label' => 'Πρώτη προτίμηση', 'value' => '0', 'value_id' => 'firstPreferenceResult')); ?>
      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusResult', 'html' => 'Πρόσθεσε τα στοιχεία και τις υπηρετήσεις σου.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Η πρώτη έκδοση βασίζεται στην εγκύκλιο μεταθέσεων εκπαιδευτικών Δευτεροβάθμιας Εκπαίδευσης σχολικού έτους 2025–2026 (129787/Ε2/15-10-2025) και στο π.δ. 50/1996 όπως ισχύει.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2023/%CE%95%CE%93%CE%9A%CE%A5%CE%9A%CE%9B%CE%99%CE%9F%CE%A3_%CE%9C%CE%95%CE%A4%CE%91%CE%98%CE%95%CE%A3%CE%95%CE%A9%CE%9D_%CE%94%CE%95_2025-2026_6%CE%94%CE%A7%CE%9F46%CE%9D%CE%9A%CE%A0%CE%94-%CE%A4%CE%954.pdf', 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Δ.Ε. 2025–2026 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/monimoi-metatakseis-metatheseis-apospaseis', 'ΥΠΑΙΘΑ — Νομοθεσία κινητικότητας / Π.Δ. 50/1996 ↗'); ?>
    <?php sourceCardLink('https://teachers.minedu.gov.gr/', 'Online Σύστημα Μητρώου & Αιτήσεων Μετάθεσης ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Το αποτέλεσμα είναι ενημερωτικό. Οι καταχωρισμένες υπηρετήσεις και οι μονάδες στο επίσημο Μητρώο, καθώς και οι αποφάσεις της αρμόδιας Διεύθυνσης Εκπαίδευσης, υπερισχύουν.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function () {
  'use strict';
  var byId = function (id) { return document.getElementById(id); };
  var rowCounter = 0;
  var msdRows = byId('msdRows');

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

  function categoryOptionsHtml() {
    return CATEGORY_OPTIONS.map(function (x) { return '<option value="' + x[0] + '">' + x[1] + '</option>'; }).join('');
  }

  function typeOptionsHtml() {
    return Object.keys(TYPE_LABELS).map(function (key) { return '<option value="' + key + '">' + esc(TYPE_LABELS[key]) + '</option>'; }).join('');
  }

  function addRow(initial) {
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
        '<div class="field"><label>Έτη</label><input class="msd-years" type="number" min="0" max="40" step="1" value="0" inputmode="numeric"></div>' +
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
    calculate();
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

  function calculate() {
    if (!window.EducationTransfer) return;
    var rows = Array.prototype.slice.call(msdRows.querySelectorAll('.transfer-service-row'));
    var input = {
      mode: byId('mode').value,
      serviceYears: Number(byId('serviceYears').value) || 0,
      serviceMonths: Number(byId('serviceMonths').value) || 0,
      serviceDays: Number(byId('serviceDays').value) || 0,
      familyStatusEligible: byId('familyStatusEligible').checked,
      eligibleChildren: Number(byId('eligibleChildren').value) || 0,
      coService: byId('coService').checked,
      locality: byId('locality').checked,
      firstPreference: byId('firstPreference').checked,
      msdPeriods: rows.map(readRow)
    };
    var result = window.EducationTransfer.calculate(input);

    byId('totalResult').textContent = formatPoints(result.total);
    byId('servicePointsResult').textContent = formatPoints(result.servicePoints);
    byId('msdPointsResult').textContent = formatPoints(result.msdPoints);
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
    byId('statusResult').textContent = 'Συνολική υπηρεσία: ' + durationText + '. Οι ειδικές κατηγορίες/προτεραιότητες δεν περιλαμβάνονται στο άθροισμα.';
  }

  function syncMode() {
    var local = byId('mode').value === 'local';
    byId('firstPreferenceRow').classList.toggle('hidden', local);
    if (local) byId('firstPreference').checked = false;
    calculate();
  }

  function reset() {
    byId('mode').value = 'region';
    byId('serviceYears').value = '0';
    byId('serviceMonths').value = '0';
    byId('serviceDays').value = '0';
    byId('eligibleChildren').value = '0';
    ['familyStatusEligible', 'coService', 'locality', 'firstPreference'].forEach(function (id) { byId(id).checked = false; });
    msdRows.innerHTML = '';
    rowCounter = 0;
    addRow({ type: 'school', category: 'A' });
    syncMode();
  }

  byId('addMsdRow').addEventListener('click', function () { addRow(); });
  byId('calculateBtn').addEventListener('click', calculate);
  byId('resetBtn').addEventListener('click', reset);
  byId('mode').addEventListener('change', syncMode);

  document.addEventListener('input', function (event) {
    var target = event.target;
    if (!target) return;
    if (target.id === 'serviceYears' || target.classList.contains('msd-years')) clampInput(target, 40);
    if (target.id === 'serviceMonths' || target.classList.contains('msd-months')) clampInput(target, 11);
    if (target.id === 'serviceDays' || target.classList.contains('msd-days')) clampInput(target, 29);
    if (target.id === 'eligibleChildren') clampInput(target, 20);
    if (target.classList.contains('msd-weekdays') && target.value !== '') {
      var n = Number(target.value);
      if (Number.isFinite(n)) target.value = String(Math.max(0, Math.min(5, n)));
    }
    if (target.closest && target.closest('.edu-page-transfer')) calculate();
  });

  document.addEventListener('change', function (event) {
    var target = event.target;
    if (!target) return;
    var row = target.closest ? target.closest('.transfer-service-row') : null;
    if (row && (target.classList.contains('msd-type') || target.classList.contains('msd-category'))) syncRow(row);
    if (target.closest && target.closest('.edu-page-transfer')) calculate();
  });

  msdRows.addEventListener('click', function (event) {
    var button = event.target.closest ? event.target.closest('.remove-row') : null;
    if (!button) return;
    var row = button.closest('.transfer-service-row');
    if (row) row.remove();
    if (!msdRows.querySelector('.transfer-service-row')) addRow();
    calculate();
  });

  addRow({ type: 'school', category: 'A' });
  syncMode();
}());
</script>
</body>
</html>
