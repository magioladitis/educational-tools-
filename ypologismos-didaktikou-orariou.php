<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός υποχρεωτικού διδακτικού ωραρίου εκπαιδευτικών</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorContainerStart(array('class' => 'app')); ?>
  <?php calculatorHero(array(
    'title' => 'Υπολογισμός υποχρεωτικού διδακτικού ωραρίου',
    'intro_html' => 'Υπολόγισε το εβδομαδιαίο <strong>υποχρεωτικό διδακτικό ωράριο</strong> σε Νηπιαγωγείο, Δημοτικό ή Δευτεροβάθμια Εκπαίδευση, με βάση βαθμίδα, οργανικότητα/θέση, κλάδο και έτη υπηρεσίας.',
    'badges' => array('Πρωτοβάθμια', 'Δευτεροβάθμια', 'Ώρες / εβδομάδα')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title' => 'Στοιχεία εκπαιδευτικού')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="level">Βαθμίδα εκπαίδευσης</label>
            <select id="level">
              <option value="primary">Πρωτοβάθμια Εκπαίδευση</option>
              <option value="secondary">Δευτεροβάθμια Εκπαίδευση</option>
            </select>
          </div>
          <div class="field">
            <label for="serviceYears">Συμπληρωμένα έτη υπηρεσίας</label>
            <input id="serviceYears" type="number" min="0" max="40" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceMonths">Επιπλέον μήνες υπηρεσίας</label>
            <input id="serviceMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
          </div>
        </div>

        <div id="primaryFields">
          <div class="field-grid">
            <div class="field">
              <label for="schoolType">Τύπος σχολικής μονάδας</label>
              <select id="schoolType">
                <option value="primary">Δημοτικό Σχολείο</option>
                <option value="kindergarten">Νηπιαγωγείο</option>
              </select>
            </div>
            <div class="field">
              <label for="primaryRole">Ιδιότητα</label>
              <select id="primaryRole">
                <option value="teacher">Εκπαιδευτικός / Προϊστάμενος ολιγοθέσιου</option>
                <option value="director">Διευθυντής/ντρια</option>
                <option value="vice_director">Υποδιευθυντής/ντρια Δημοτικού</option>
              </select>
            </div>
            <div class="field">
              <label for="organicity">Οργανικότητα σχολικής μονάδας <small>Αριθμός θέσεων</small></label>
              <input id="organicity" type="number" min="1" max="30" step="1" value="6" inputmode="numeric">
            </div>
          </div>
          <div class="info-note">
            Στην Πρωτοβάθμια, όταν το απαιτούμενο όριο υπηρεσίας για μείωση ωραρίου συμπληρώνεται έως τις <strong>31 Δεκεμβρίου</strong>, η μείωση εφαρμόζεται από 1η Σεπτεμβρίου του ίδιου σχολικού έτους. Καταχώρισε τον χρόνο υπηρεσίας που πρέπει να ληφθεί υπόψη για το σχολικό έτος που ελέγχεις.
          </div>
        </div>

        <div id="secondaryFields" class="hidden">
          <div class="field-grid">
            <div class="field">
              <label for="secondaryRole">Ιδιότητα</label>
              <select id="secondaryRole">
                <option value="teacher">Εκπαιδευτικός</option>
                <option value="director">Διευθυντής/ντρια Γυμνασίου / Λυκείου / ΕΠΑ.Λ.</option>
                <option value="lab_director">Διευθυντής/ντρια Εργαστηριακού Κέντρου</option>
                <option value="vice_or_sector">Υποδιευθυντής/ντρια ή Υπεύθυνος/η Τομέα Ε.Κ.</option>
                <option value="lab_responsible">Υπεύθυνος/η Εργαστηρίου</option>
                <option value="epal_ek_lab_sector">Υπεύθυνος/η εργαστηρίου τομέα/ειδικότητας Ε.Κ. ή ΕΠΑ.Λ.</option>
              </select>
            </div>
            <div class="field" id="branchField">
              <label for="hourCategory">Κατηγορία / κλάδος ωραρίου</label>
              <select id="hourCategory">
                <option value="PE">ΠΕ — Πανεπιστημιακής Εκπαίδευσης</option>
                <option value="TE01">ΤΕ εργαστηριακών κλάδων (κλίμακα πρώην ΤΕ01)</option>
                <option value="DE01_ARCH">ΔΕ01 — Αρχιτεχνίτες</option>
                <option value="DE01_TECH">ΔΕ01 — Τεχνίτες</option>
              </select>
            </div>
            <div class="field hidden" id="sectionsField">
              <label for="sections">Αριθμός τμημάτων σχολικής μονάδας</label>
              <select id="sections">
                <option value="3-5">3–5 τμήματα</option>
                <option value="6-9">6–9 τμήματα</option>
                <option value="10-12">10–12 τμήματα</option>
                <option value="13+">Πάνω από 12 τμήματα</option>
              </select>
            </div>
          </div>
          <div class="info-note">
            Για υπευθύνους εργαστηρίων τομέων/ειδικοτήτων Ε.Κ. ή ΕΠΑ.Λ. εφαρμόζεται η ειδική μείωση δύο ωρών με κατώτερο όριο 18, όπου συντρέχουν οι νόμιμες προϋποθέσεις. Δεν εφαρμόζεται ως μείωση για ευθύνη εργαστηρίου Πληροφορικής ή Φυσικών Επιστημών στις περιπτώσεις που ο νόμος ορίζει ως εξωδιδακτική απασχόληση.
          </div>
        </div>

        <?php calculatorActions(array(
          array('id' => 'calculateBtn', 'class' => 'primary', 'label' => 'Υπολογισμός ωραρίου'),
          array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Προσοχή:</strong> Το εργαλείο υπολογίζει το <em>διδακτικό</em> ωράριο και όχι το συνολικό εργασιακό ωράριο/παρουσία στο σχολείο. Ειδικές υπηρεσιακές περιπτώσεις μπορεί να απαιτούν έλεγχο από τη Διεύθυνση Εκπαίδευσης.'
      )); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value' => '24',
        'value_id' => 'hoursResult',
        'label' => 'ώρες / εβδομάδα',
        'cap' => 'Υποχρεωτικό διδακτικό ωράριο'
      )); ?>
      <?php calculatorResultRow(array('label' => 'Βαθμίδα', 'value' => 'Πρωτοβάθμια', 'value_id' => 'levelResult')); ?>
      <?php calculatorResultRow(array('label' => 'Υπηρεσία', 'value' => '0 έτη', 'value_id' => 'serviceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Ιδιότητα / κανόνας', 'value' => '—', 'value_id' => 'ruleResult')); ?>
      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusResult', 'html' => 'Συμπλήρωσε τα στοιχεία για να δεις το ωράριο.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Ο υπολογισμός βασίζεται στις ισχύουσες ρυθμίσεις για το εβδομαδιαίο υποχρεωτικό διδακτικό ωράριο. Στην Πρωτοβάθμια λαμβάνονται υπόψη η οργανικότητα, η ιδιότητα και τα έτη υπηρεσίας· στη Δευτεροβάθμια ο κλάδος, τα έτη υπηρεσίας και η διοικητική θέση.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/news/17496-25-01-16-orario-ekpaideftikon-se-protovathmia-kai-defterovathmia-ekpaidefsi', 'ΥΠΑΙΘΑ — Ωράριο εκπαιδευτικών ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2016/leitourgia__nhpio.pdf', 'ΥΠΑΙΘΑ — Ωράριο Νηπιαγωγών / λειτουργία Νηπιαγωγείων ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2018/EPAL_N_4547_FEK_102A_12-06-2018.pdf', 'Ν. 4547/2018, άρθρο 49 — Δημοτικά / Υποδιευθυντές ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-oikonomia/n-4152-2013.html', 'Ν. 4152/2013 — Δευτεροβάθμια ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-ekpaideuse/nomos-4386-2016-phek-83a-11-5-2016-ruthmiseis-gia-thn-ereyna.html', 'Ν. 4386/2016 — Εργαστήρια Ε.Κ./ΕΠΑ.Λ. ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Το αποτέλεσμα είναι ενημερωτικό και δεν υποκαθιστά υπηρεσιακή πράξη καθορισμού ωραρίου.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/teaching-hours-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function () {
  'use strict';
  const byId = id => document.getElementById(id);
  const level = byId('level');
  const primaryFields = byId('primaryFields');
  const secondaryFields = byId('secondaryFields');
  const secondaryRole = byId('secondaryRole');
  const sectionsField = byId('sectionsField');
  const branchField = byId('branchField');

  function value(id) { return byId(id).value; }
  function numberValue(id, max) {
    const n = Math.max(0, Math.floor(Number(value(id)) || 0));
    return Number.isFinite(max) ? Math.min(max, n) : n;
  }

  function clampBoundedIntegerInput(el, max) {
    if (!el || el.value === '') return;
    const n = Number(el.value);
    if (!Number.isFinite(n)) {
      el.value = '';
      return;
    }
    el.value = String(Math.max(0, Math.min(max, Math.floor(n))));
  }

  function syncFields() {
    const isSecondary = level.value === 'secondary';
    primaryFields.classList.toggle('hidden', isSecondary);
    secondaryFields.classList.toggle('hidden', !isSecondary);
    const role = secondaryRole.value;
    sectionsField.classList.toggle('hidden', !isSecondary || role !== 'director');
    branchField.classList.toggle('hidden', !isSecondary || ['director', 'lab_director', 'vice_or_sector'].includes(role));
  }

  function calculate() {
    syncFields();
    const options = {
      level: level.value,
      years: numberValue('serviceYears', 40),
      months: numberValue('serviceMonths', 11)
    };
    if (level.value === 'primary') {
      options.schoolType = value('schoolType');
      options.role = value('primaryRole');
      options.organicity = numberValue('organicity', 30);
    } else {
      options.role = value('secondaryRole');
      options.branch = value('hourCategory');
      options.sections = value('sections');
    }

    const result = window.EducationTeachingHours.calculate(options);
    const status = byId('statusResult');
    if (!result.valid) {
      byId('hoursResult').textContent = '—';
      byId('levelResult').textContent = level.value === 'secondary' ? 'Δευτεροβάθμια' : 'Πρωτοβάθμια';
      byId('serviceResult').textContent = window.EducationTeachingHours.serviceLabel(window.EducationTeachingHours.serviceMonths(options.years, options.months));
      byId('ruleResult').textContent = '—';
      status.textContent = result.error || 'Δεν είναι δυνατός ο υπολογισμός.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
      return;
    }

    byId('hoursResult').textContent = result.hours;
    byId('levelResult').textContent = result.level === 'secondary' ? 'Δευτεροβάθμια' : (result.schoolType === 'kindergarten' ? 'Νηπιαγωγείο' : 'Δημοτικό');
    byId('serviceResult').textContent = result.serviceLabel;
    byId('ruleResult').textContent = result.rule;
    status.textContent = 'Υπολογισμός σύμφωνα με τα δηλωμένα στοιχεία.';
    status.className = 'result-message edu-message result-message--success edu-message--success';
  }

  function reset() {
    level.value = 'primary';
    byId('serviceYears').value = '0';
    byId('serviceMonths').value = '0';
    byId('schoolType').value = 'primary';
    byId('primaryRole').value = 'teacher';
    byId('organicity').value = '6';
    secondaryRole.value = 'teacher';
    byId('hourCategory').value = 'PE';
    byId('sections').value = '3-5';
    calculate();
  }

  document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 40);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      calculate();
    });
    el.addEventListener('change', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 40);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      calculate();
    });
  });
  byId('calculateBtn').addEventListener('click', calculate);
  byId('resetBtn').addEventListener('click', reset);
  calculate();
})();
</script>
</body>
</html>
