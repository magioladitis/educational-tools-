<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός μορίων προσωρινής τοποθέτησης νεοδιόριστων εκπαιδευτικών</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-newly-appointed-placement">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorContainerStart(array('class' => 'app')); ?>
  <?php calculatorHero(array(
    'title' => 'Μόρια τοποθέτησης νεοδιόριστων',
    'intro_html' => 'Υπολόγισε τα μόρια για την <strong>προσωρινή τοποθέτηση</strong> σε σχολική μονάδα της περιοχής διορισμού. Όταν την ίδια θέση ζητούν περισσότεροι νεοδιόριστοι, συγκρίνονται με βάση τις μονάδες από <strong>οικογενειακούς λόγους, συνυπηρέτηση και εντοπιότητα</strong>.',
    'badges' => array('Προσωρινή τοποθέτηση', 'Π.Ε. & Δ.Ε.', 'Π.Δ. 154/1996 · Π.Δ. 144/1997')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title' => 'Α. Μόρια τοποθέτησης')); ?>
        <div class="check-row">
          <label>
            <input id="familyStatusEligible" type="checkbox">
            Δικαιούμαι μονάδες οικογενειακής κατάστασης (+4)
            <small>Γάμος ή σύμφωνο συμβίωσης, καθώς και οι προβλεπόμενες περιπτώσεις γονέα με επιμέλεια.</small>
          </label>
        </div>

        <div class="field-grid">
          <div class="field">
            <label for="eligibleChildren">Τέκνα που μοριοδοτούνται</label>
            <input id="eligibleChildren" type="number" min="0" max="20" step="1" value="0" inputmode="numeric">
            <div class="field-hint">1ο: +4 · 2ο: +4 · 3ο: +6 · κάθε επόμενο: +7.</div>
          </div>
        </div>

        <div class="check-row">
          <label>
            <input id="coService" type="checkbox">
            Δικαιούμαι συνυπηρέτηση στον Δήμο του σχολείου (+4)
            <small>Η συνυπηρέτηση εξετάζεται για τον συγκεκριμένο Δήμο όπου βρίσκεται η σχολική μονάδα που ζητείται.</small>
          </label>
        </div>

        <div class="check-row">
          <label>
            <input id="locality" type="checkbox">
            Δικαιούμαι εντοπιότητα στον Δήμο του σχολείου (+4)
            <small>Απαιτείται δημοτικότητα στον συγκεκριμένο Δήμο επί τουλάχιστον δύο έτη· η μόνιμη κατοικία από μόνη της δεν αρκεί.</small>
          </label>
        </div>

        <div class="info-note">
          <strong>Σημαντικό:</strong> συνυπηρέτηση και εντοπιότητα συνδέονται με τον Δήμο του συγκεκριμένου σχολείου. Αν δηλώνεις σχολεία σε διαφορετικούς Δήμους, κάνε ξεχωριστό υπολογισμό για κάθε Δήμο.
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('title' => 'Β. Τι δεν προστίθεται στο βασικό άθροισμα')); ?>
        <p class="small-note">
          Για την αρχική προσωρινή τοποθέτηση των νεοδιόριστων, η συνολική υπηρεσία και οι Μονάδες Συνθηκών Διαβίωσης δεν προστίθενται στο παραπάνω σύνολο. Χρησιμοποιούνται στη σειρά επίλυσης ισοβαθμιών, εφόσον χρειαστεί.
        </p>
        <div class="info-note">
          <strong>Σειρά κριτηρίων σε ισοβαθμία:</strong> συνυπηρέτηση → εντοπιότητα → οικογενειακοί λόγοι → συνολική υπηρεσία → δυσμενείς συνθήκες λειτουργίας σχολείων → ημερομηνία και σειρά δημοσίευσης του διορισμού στο ΦΕΚ.
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('title' => 'Γ. Διετής παραμονή στην περιοχή διορισμού')); ?>
        <div class="warning">
          Οι νεοδιόριστοι υποχρεούνται, κατά κανόνα, να παραμείνουν στην περιοχή διορισμού τους για τουλάχιστον δύο σχολικά έτη. Ο ν. 5128/2024 προβλέπει δυνατότητα απόσπασης για συγκεκριμένες ειδικές κατηγορίες και περιπτώσεις αναπηρίας. Η ρύθμιση αυτή <strong>δεν αποτελεί κριτήριο μοριοδότησης</strong> της προσωρινής τοποθέτησης.
        </div>
        <div class="small-note">
          Σε διορισμό στην Ειδική Αγωγή ισχύει επιπλέον υποχρέωση υπηρεσίας στην Ε.Α.Ε. για τουλάχιστον πέντε έτη. Ειδικές διαδικασίες για Σ.Μ.Ε.Α.Ε., Τμήματα Ένταξης, Πρότυπα ή Πειραματικά σχολεία δεν ελέγχονται από τον παρόντα υπολογιστή.
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorActions(array(
        array('id' => 'calculateBtn', 'class' => 'primary', 'label' => 'Υπολογισμός μορίων'),
        array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
      )); ?>

      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Διευκρίνιση:</strong> η νομικά ακριβέστερη παραπομπή είναι στο άρθρο 3 του π.δ. 154/1996, όπως αντικαταστάθηκε από το άρθρο 2 του π.δ. 144/1997. Η οριστική τοποθέτηση γίνεται αργότερα με τη διαδικασία των μεταθέσεων και διαφορετικό σύνολο κριτηρίων.'
      )); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value' => '0',
        'value_id' => 'totalResult',
        'label' => 'μόρια τοποθέτησης',
        'cap' => 'Για τον συγκεκριμένο Δήμο / σχολείο'
      )); ?>
      <?php calculatorResultRow(array('label' => 'Οικογενειακή κατάσταση', 'value' => '0', 'value_id' => 'familyStatusResult')); ?>
      <?php calculatorResultRow(array('label' => 'Τέκνα', 'value' => '0', 'value_id' => 'childrenResult')); ?>
      <?php calculatorResultRow(array('label' => 'Σύνολο οικογενειακών λόγων', 'value' => '0', 'value_id' => 'familyResult')); ?>
      <?php calculatorResultRow(array('label' => 'Συνυπηρέτηση', 'value' => '0', 'value_id' => 'coServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Εντοπιότητα', 'value' => '0', 'value_id' => 'localityResult')); ?>
      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusResult', 'html' => 'Συμπλήρωσε τα στοιχεία που ισχύουν για το σχολείο ή τον Δήμο που εξετάζεις.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Η διαδικασία της προσωρινής τοποθέτησης βασίζεται στο άρθρο 3 του π.δ. 154/1996, όπως αντικαταστάθηκε από το άρθρο 2 του π.δ. 144/1997. Οι επιμέρους μονάδες οικογενειακών λόγων, συνυπηρέτησης και εντοπιότητας ακολουθούν το άρθρο 16 του π.δ. 50/1996 όπως ισχύει.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://mitos.gov.gr/index.php/%CE%94%CE%94:%CE%A0%CF%81%CE%BF%CF%83%CF%89%CF%81%CE%B9%CE%BD%CE%AE_%CF%84%CE%BF%CF%80%CE%BF%CE%B8%CE%AD%CF%84%CE%B7%CF%83%CE%B7_%CE%B5%CE%BA%CF%80%CE%B1%CE%B9%CE%B4%CE%B5%CF%85%CF%84%CE%B9%CE%BA%CF%8E%CE%BD_%CE%94%CE%B5%CF%85%CF%84%CE%B5%CF%81%CE%BF%CE%B2%CE%AC%CE%B8%CE%BC%CE%B9%CE%B1%CF%82_%CE%95%CE%BA%CF%80%CE%B1%CE%AF%CE%B4%CE%B5%CF%85%CF%83%CE%B7%CF%82_(%CE%BD%CE%B5%CE%BF%CE%B4%CE%B9%CF%8C%CF%81%CE%B9%CF%83%CF%84%CF%89%CE%BD,_%CE%B1%CF%80%CF%8C_%CE%B4%CE%B9%CE%AC%CE%B8%CE%B5%CF%83%CE%B7,_%CE%B1%CF%80%CE%BF%CF%83%CF%80%CE%B1%CF%83%CE%BC%CE%AD%CE%BD%CF%89%CE%BD)', 'Εθνικό Μητρώο Διοικητικών Διαδικασιών — Προσωρινή τοποθέτηση Δ.Ε. ↗'); ?>
    <?php sourceCardLink('https://api.et.gr/apiLAW/2/1996/154/pdf', 'Εθνικό Τυπογραφείο — Π.Δ. 154/1996 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2023/%CE%95%CE%93%CE%9A%CE%A5%CE%9A%CE%9B%CE%99%CE%9F%CE%A3_%CE%9C%CE%95%CE%A4%CE%91%CE%98%CE%95%CE%A3%CE%95%CE%A9%CE%9D_%CE%94%CE%95_2025-2026_6%CE%94%CE%A7%CE%9F46%CE%9D%CE%9A%CE%A0%CE%94-%CE%A4%CE%954.pdf', 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Δ.Ε. 2025–2026 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/kinitikotita/metatheseis-nomothesia', 'ΥΠΑΙΘΑ — Νομοθεσία μεταθέσεων / ν. 5128/2024 ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Το αποτέλεσμα είναι ενημερωτικό. Η οικεία Διεύθυνση Εκπαίδευσης και το αρμόδιο υπηρεσιακό συμβούλιο εφαρμόζουν τα επίσημα στοιχεία και δικαιολογητικά.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/newly-appointed-placement-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function () {
  'use strict';
  var byId = function (id) { return document.getElementById(id); };
  var calc = window.NewlyAppointedPlacementCalculations;

  function formatPoints(value) {
    return Number(value || 0).toLocaleString('el-GR', { maximumFractionDigits: 2 });
  }

  function getInput() {
    return {
      familyStatusEligible: byId('familyStatusEligible').checked,
      eligibleChildren: byId('eligibleChildren').value,
      coService: byId('coService').checked,
      locality: byId('locality').checked
    };
  }

  function render() {
    var result = calc.calculate(getInput());
    byId('totalResult').textContent = formatPoints(result.total);
    byId('familyStatusResult').textContent = formatPoints(result.familyStatusPoints);
    byId('childrenResult').textContent = formatPoints(result.childPoints);
    byId('familyResult').textContent = formatPoints(result.familyPoints);
    byId('coServiceResult').textContent = formatPoints(result.coServicePoints);
    byId('localityResult').textContent = formatPoints(result.localityPoints);
    byId('statusResult').textContent = result.total > 0
      ? 'Το σύνολο αφορά το συγκεκριμένο σχολείο/Δήμο. Έλεγξε ξανά συνυπηρέτηση και εντοπιότητα για κάθε διαφορετικό Δήμο.'
      : 'Δεν έχουν επιλεγεί μοριοδοτούμενα κριτήρια για τον συγκεκριμένο Δήμο.';
  }

  function reset() {
    byId('familyStatusEligible').checked = false;
    byId('eligibleChildren').value = '0';
    byId('coService').checked = false;
    byId('locality').checked = false;
    render();
  }

  byId('eligibleChildren').addEventListener('input', function () {
    var n = Math.floor(Number(this.value || 0));
    if (!Number.isFinite(n)) n = 0;
    this.value = String(Math.max(0, Math.min(20, n)));
  });
  byId('calculateBtn').addEventListener('click', render);
  byId('resetBtn').addEventListener('click', reset);
  render();
}());
</script>
