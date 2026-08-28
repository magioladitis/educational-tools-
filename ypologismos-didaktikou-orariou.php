<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός υποχρεωτικού ωραρίου εκπαιδευτικών, ΕΕΠ και ΕΒΠ</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorContainerStart(array('class' => 'app')); ?>
  <?php calculatorHero(array(
    'title' => 'Υπολογισμός υποχρεωτικού ωραρίου',
    'intro_html' => 'Υπολόγισε το εβδομαδιαίο <strong>υποχρεωτικό ωράριο</strong> εκπαιδευτικών σε Νηπιαγωγείο, Δημοτικό ή Δευτεροβάθμια Εκπαίδευση, καθώς και το υποχρεωτικό ωράριο υποστηρικτικού έργου ΕΕΠ και ΕΒΠ.',
    'badges' => array('Πρωτοβάθμια', 'Δευτεροβάθμια', 'ΕΕΠ', 'ΕΒΠ', 'Ώρες / εβδομάδα')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title' => 'Στοιχεία προσωπικού')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="level">Κατηγορία προσωπικού / βαθμίδα</label>
            <select id="level">
              <option value="primary">Πρωτοβάθμια Εκπαίδευση</option>
              <option value="secondary">Δευτεροβάθμια Εκπαίδευση</option>
              <option value="eep">Ειδικό Εκπαιδευτικό Προσωπικό (ΕΕΠ)</option>
              <option value="ebp">Ειδικό Βοηθητικό Προσωπικό (ΕΒΠ)</option>
            </select>
          </div>
          <div class="field" id="serviceYearsField">
            <label for="serviceYears">Συμπληρωμένα έτη υπηρεσίας</label>
            <input id="serviceYears" type="number" min="0" max="50" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field" id="serviceMonthsField">
            <label for="serviceMonths">Επιπλέον μήνες υπηρεσίας</label>
            <input id="serviceMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field" id="serviceDaysField">
            <label for="serviceDays">Επιπλέον ημέρες υπηρεσίας</label>
            <input id="serviceDays" type="number" min="0" max="29" step="1" value="0" inputmode="numeric">
          </div>
        </div>

        <div class="info-note" id="serviceInfo">
          Για τη μείωση του ωραρίου λαμβάνεται υπόψη ο <strong>αναγνωρισμένος χρόνος υπηρεσίας</strong> που προβλέπεται από τις σχετικές διατάξεις. Για τους εκπαιδευτικούς περιλαμβάνονται και οι προσφερθείσες υπηρεσίες ως αναπληρωτή/ωρομίσθιου κατόπιν πρόσληψης από το ΥΠΑΙΘ, όπως π.χ. σε ΚΕΣΥ/ΚΕΔΑΣΥ. Καταχώρισε το υπόλοιπο ημερών όπως προκύπτει από την αναγνωρισμένη υπηρεσία (0–29 ημέρες). <a href="https://diavgeia.gov.gr/doc/4%CE%99%CE%9949-%CE%9F9?inline=true" target="_blank" rel="noopener noreferrer">Δες ποια προϋπηρεσία προσμετράται ↗</a>
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
            Στη Δευτεροβάθμια τα όρια εφαρμόζονται με ακρίβεια ημέρας. Για εκπαιδευτικούς <strong>ΠΕ</strong>: <strong>έως 6 έτη: 23 ώρες</strong>, από <strong>6 έτη και 1 ημέρα έως 12 έτη: 21 ώρες</strong> και από <strong>12 έτη και 1 ημέρα: 20 ώρες</strong>. Για την κλίμακα <strong>ΤΕ01</strong>: <strong>έως 7 έτη: 24 ώρες</strong>, από <strong>7 έτη και 1 ημέρα έως 13 έτη: 21 ώρες</strong> και από <strong>13 έτη και 1 ημέρα: 20 ώρες</strong>. Με τη διατηρούμενη ρύθμιση του ν. 2413/1996, στα <strong>20 συμπληρωμένα έτη</strong> εφαρμόζεται επιπλέον μείωση δύο ωρών. Η εγκύκλιος 141076/Ε3/04-11-2021 επισημαίνει ότι στη Δευτεροβάθμια η μείωση ισχύει <strong>από τη συμπλήρωση του απαιτούμενου χρόνου υπηρεσίας</strong>.
          </div>
          <div class="info-note">
            Για υπευθύνους εργαστηρίων τομέων/ειδικοτήτων Ε.Κ. ή ΕΠΑ.Λ. εφαρμόζεται η ειδική μείωση δύο ωρών με κατώτερο όριο 18, όπου συντρέχουν οι νόμιμες προϋποθέσεις. Δεν εφαρμόζεται ως μείωση για ευθύνη εργαστηρίου Πληροφορικής ή Φυσικών Επιστημών στις περιπτώσεις που ο νόμος ορίζει ως εξωδιδακτική απασχόληση.
          </div>
        </div>

        <div id="eepFields" class="hidden">
          <div class="info-note">
            Για το <strong>Ειδικό Εκπαιδευτικό Προσωπικό (ΕΕΠ)</strong> που εργάζεται στις σχολικές μονάδες που καλύπτει η Υ.Α. 66079/Δ3/2018, το εβδομαδιαίο υποχρεωτικό ωράριο <strong>υποστηρικτικού έργου</strong> είναι: <strong>25 ώρες έως και 5 έτη</strong>, <strong>24 ώρες πάνω από 5 έως και 10 έτη</strong>, <strong>23 ώρες πάνω από 10 έως και 15 έτη</strong>, <strong>22 ώρες πάνω από 15 έως και 20 έτη</strong> και <strong>21 ώρες πάνω από 20 έτη</strong> υπηρεσίας. Οι ώρες αντιστοιχούν σε διδακτικές ώρες και υλοποιούνται παράλληλα με το διδακτικό ωρολόγιο πρόγραμμα της σχολικής μονάδας.
          </div>
        </div>

        <div id="ebpFields" class="hidden">
          <div class="info-note">
            Για το <strong>Ειδικό Βοηθητικό Προσωπικό (ΕΒΠ)</strong> το εβδομαδιαίο υποχρεωτικό ωράριο <strong>υποστηρικτικού έργου</strong> είναι <strong>30 ώρες</strong>. Δεν πρόκειται για διδακτικό ωράριο και δεν μειώνεται με βάση τα έτη υπηρεσίας.
          </div>
        </div>

        <div class="info-note">
          <strong>Ειδικές περιπτώσεις:</strong> Σε Δημοτικό που έχει ενταχθεί στο Σύστημα Δικτύου Σχολικών Βιβλιοθηκών, οι <strong>3 ώρες</strong> του/της ορισμένου/ης Υπευθύνου λειτουργίας θεωρούνται διδακτικές· στη Δευτεροβάθμια δεν προβλέπεται αντίστοιχη μείωση. Επίσης, ειδικές ρυθμίσεις Προτύπων/Πειραματικών ή άλλες υπηρεσιακές διευκολύνσεις δεν εφαρμόζονται αυτόματα από τον παρόντα βασικό υπολογισμό.
        </div>

        <?php calculatorActions(array(
          array('id' => 'calculateBtn', 'class' => 'primary', 'label' => 'Υπολογισμός ωραρίου'),
          array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Υποχρεωτικό έργο ≠ συνολική παραμονή:</strong> Για τους εκπαιδευτικούς το εργαλείο υπολογίζει το <em>διδακτικό</em> ωράριο· για ΕΕΠ/ΕΒΠ το υποχρεωτικό ωράριο <em>υποστηρικτικού έργου</em>. Η συνολική υποχρεωτική παραμονή στο σχολείο, πέρα από τις ώρες διδασκαλίας ή υποστηρικτικού έργου, δεν υπερβαίνει τις <strong>6 ώρες ημερησίως ή 30 ώρες εβδομαδιαίως</strong>, σύμφωνα με τις εφαρμοζόμενες διατάξεις. Για τους εκπαιδευτικούς, η εξωδιδακτική απασχόληση δεν αποτελεί από μόνη της μείωση του υποχρεωτικού διδακτικού ωραρίου.'
      )); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value' => '24',
        'value_id' => 'hoursResult',
        'label' => 'ώρες / εβδομάδα',
        'cap' => 'Υποχρεωτικό διδακτικό ωράριο',
        'cap_id' => 'hoursCap'
      )); ?>
      <?php calculatorResultRow(array('label' => 'Κατηγορία / βαθμίδα', 'value' => 'Πρωτοβάθμια', 'value_id' => 'levelResult')); ?>
      <?php calculatorResultRow(array('label' => 'Υπηρεσία', 'value' => '0 έτη', 'value_id' => 'serviceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Χρόνος μέχρι την επόμενη μείωση ωραρίου', 'value' => '—', 'value_id' => 'nextReductionResult')); ?>
      <?php calculatorResultRow(array('label' => 'Ιδιότητα / κανόνας', 'value' => '—', 'value_id' => 'ruleResult')); ?>
      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusResult', 'html' => 'Συμπλήρωσε τα στοιχεία για να δεις το ωράριο.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Ο υπολογισμός βασίζεται στις ισχύουσες ρυθμίσεις για το εβδομαδιαίο υποχρεωτικό διδακτικό ωράριο των εκπαιδευτικών και για το εβδομαδιαίο υποχρεωτικό ωράριο υποστηρικτικού έργου ΕΕΠ/ΕΒΠ. Στην Πρωτοβάθμια λαμβάνονται υπόψη η οργανικότητα, η ιδιότητα και ο χρόνος υπηρεσίας· στη Δευτεροβάθμια ο κλάδος, ο χρόνος υπηρεσίας και η διοικητική θέση· στο ΕΕΠ ο χρόνος υπηρεσίας.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-ekpaideuse/deuterobathmia-ekpaideuse/egkuklios-upaith-141076-e3-4-11-2021.html', 'ΥΠΑΙΘ — Εγκύκλιος 141076/Ε3/04-11-2021 — ωράριο και υπερωριακή διδασκαλία ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/news/17496-25-01-16-orario-ekpaideftikon-se-protovathmia-kai-defterovathmia-ekpaidefsi', 'ΥΠΑΙΘΑ — Ωράριο εκπαιδευτικών ↗'); ?>
    <?php sourceCardLink('https://diavgeia.gov.gr/doc/4%CE%99%CE%9949-%CE%9F9?inline=true', 'ΥΠΑΙΘ — Εγκύκλιος Φ.361.23/12/123995/Δ1 (20-12-2010) — Προϋπηρεσία για μείωση ωραρίου ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2016/leitourgia__nhpio.pdf', 'ΥΠΑΙΘΑ — Ωράριο Νηπιαγωγών / λειτουργία Νηπιαγωγείων ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2018/EPAL_N_4547_FEK_102A_12-06-2018.pdf', 'Ν. 4547/2018, άρθρο 49 — Δημοτικά / Υποδιευθυντές ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-oikonomia/n-4152-2013.html', 'Ν. 4152/2013 — Δευτεροβάθμια ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/site/17496-25-01-16-orario-ekpaideftikon-se-protovathmia-kai-defterovathmia-ekpaidefsi', 'ΥΠΑΙΘ — Διευκρινίσεις για την εγκύκλιο 123948/Δ2/06-09-2013 ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-ekpaideuse/n-2413-1996.html', 'Ν. 2413/1996, άρθρο 48 παρ. 3 — μείωση στα 20 έτη ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-ekpaideuse/deuterobathmia-ekpaideuse/upourgike-apophase-66079-d3-2018.html', 'Υ.Α. 66079/Δ3/2018 (Β΄ 1585) — ωράριο ΕΕΠ / ΕΒΠ ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-ekpaideuse/nomos-4386-2016-phek-83a-11-5-2016-ruthmiseis-gia-thn-ereyna.html', 'Ν. 4386/2016 — Εργαστήρια Ε.Κ./ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://edu.klimaka.gr/ekpaideytikoi/wrario-anatheseis/3687-orario-ypeythynoi-ergastiriwn-kai-sxolikwn-biblithikwn', 'ΥΠΑΙΘΑ — 132906/Ε3/06-11-2024 — εργαστήρια και σχολικές βιβλιοθήκες ↗'); ?>
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
  const eepFields = byId('eepFields');
  const ebpFields = byId('ebpFields');
  const serviceInfo = byId('serviceInfo');
  const serviceYearField = byId('serviceYearsField');
  const serviceMonthField = byId('serviceMonthsField');
  const serviceDayField = byId('serviceDaysField');
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
    const isEep = level.value === 'eep';
    const isEbp = level.value === 'ebp';
    primaryFields.classList.toggle('hidden', isSecondary || isEep || isEbp);
    secondaryFields.classList.toggle('hidden', !isSecondary);
    eepFields.classList.toggle('hidden', !isEep);
    ebpFields.classList.toggle('hidden', !isEbp);
    [serviceYearField, serviceMonthField, serviceDayField, serviceInfo].forEach(el => el.classList.toggle('hidden', isEbp));
    const role = secondaryRole.value;
    sectionsField.classList.toggle('hidden', !isSecondary || role !== 'director');
    branchField.classList.toggle('hidden', !isSecondary || ['director', 'lab_director', 'vice_or_sector'].includes(role));
  }

  function calculate() {
    syncFields();
    const options = {
      level: level.value,
      years: numberValue('serviceYears', 50),
      months: numberValue('serviceMonths', 11),
      days: numberValue('serviceDays', 29)
    };
    if (level.value === 'primary') {
      options.schoolType = value('schoolType');
      options.role = value('primaryRole');
      options.organicity = numberValue('organicity', 30);
    } else if (level.value === 'secondary') {
      options.role = value('secondaryRole');
      options.branch = value('hourCategory');
      options.sections = value('sections');
    }

    const result = window.EducationTeachingHours.calculate(options);
    const status = byId('statusResult');
    byId('hoursCap').textContent = ['eep', 'ebp'].includes(level.value) ? 'Υποχρεωτικό ωράριο υποστηρικτικού έργου' : 'Υποχρεωτικό διδακτικό ωράριο';
    if (!result.valid) {
      byId('hoursResult').textContent = '—';
      byId('levelResult').textContent = level.value === 'secondary' ? 'Δευτεροβάθμια' : (level.value === 'eep' ? 'ΕΕΠ' : (level.value === 'ebp' ? 'ΕΒΠ' : 'Πρωτοβάθμια'));
      byId('serviceResult').textContent = level.value === 'ebp' ? 'Δεν εφαρμόζεται' : window.EducationTeachingHours.serviceLabel(window.EducationTeachingHours.serviceMonths(options.years, options.months, options.days));
      byId('nextReductionResult').textContent = '—';
      byId('ruleResult').textContent = '—';
      status.textContent = result.error || 'Δεν είναι δυνατός ο υπολογισμός.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
      return;
    }

    byId('hoursResult').textContent = result.hours;
    byId('levelResult').textContent = result.level === 'eep' ? 'ΕΕΠ' : (result.level === 'ebp' ? 'ΕΒΠ' : (result.level === 'secondary' ? 'Δευτεροβάθμια' : (result.schoolType === 'kindergarten' ? 'Νηπιαγωγείο' : 'Δημοτικό')));
    byId('serviceResult').textContent = result.serviceLabel;
    byId('nextReductionResult').textContent = result.nextReductionLabel || 'Δεν προβλέπεται περαιτέρω μείωση';
    byId('ruleResult').textContent = result.rule;
    status.textContent = 'Υπολογισμός σύμφωνα με τα δηλωμένα στοιχεία.';
    status.className = 'result-message edu-message result-message--success edu-message--success';
  }

  function reset() {
    level.value = 'primary';
    byId('serviceYears').value = '0';
    byId('serviceMonths').value = '0';
    byId('serviceDays').value = '0';
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
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      if (el.id === 'serviceDays') clampBoundedIntegerInput(el, 29);
      calculate();
    });
    el.addEventListener('change', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      if (el.id === 'serviceDays') clampBoundedIntegerInput(el, 29);
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
