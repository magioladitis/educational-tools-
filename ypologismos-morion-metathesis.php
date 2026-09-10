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
            <input id="serviceYears" type="number" min="0" max="50" step="1" value="0" inputmode="numeric">
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

        <?php calculatorDisclosure(array(
          'id' => 'modeNote',
          'summary' => 'Πώς αλλάζουν συνυπηρέτηση, εντοπιότητα και πρώτη προτίμηση;',
          'html' => 'Στη μετάθεση από περιοχή σε περιοχή, συνυπηρέτηση και εντοπιότητα εξετάζονται για την <strong>περιοχή μετάθεσης</strong>. Στην οριστική τοποθέτηση/βελτίωση συνδέονται με τον <strong>συγκεκριμένο Δήμο</strong> όπου βρίσκεται το σχολείο. Η πρώτη προτίμηση μοριοδοτείται μόνο στη μετάθεση από περιοχή σε περιοχή.',
          'open' => true,
          'attrs' => array('data-mobile-collapsed' => 'true')
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('title' => 'Β. Μονάδες Συνθηκών Διαβίωσης (Μ.Σ.Δ.)')); ?>
        <p class="small-note">Πρόσθεσε μία γραμμή για κάθε διαφορετική υπηρέτηση/κατηγορία. Οι ημέρες ≥15 λογίζονται ως πλήρης μήνας. Για παράλληλη υπηρεσία μπορείς να δηλώσεις τις ημέρες ανά εβδομάδα (σύνολο 5/5).</p>
        <div id="msdRows"></div>
        <button class="add-row" id="addMsdRow" type="button">+ Προσθήκη υπηρέτησης</button>

        <?php calculatorDisclosure(array(
          'summary' => 'Πότε διπλασιάζονται οι Μ.Σ.Δ. απομακρυσμένων σχολείων;',
          'html' => '<strong>Διπλασιασμός απομακρυσμένων:</strong> από το 2018–2019 αφορά πραγματική υπηρεσία σε σχολεία κατηγοριών Ι΄, ΙΑ΄, ΙΒ΄ και ΙΓ΄, όταν έχει συμπληρωθεί τουλάχιστον διετής συνεχόμενη υπηρεσία. Στον υπολογιστή ενεργοποίησέ τον μόνο όταν το δικαίωμα έχει πράγματι θεμελιωθεί.',
          'open' => true,
          'attrs' => array('data-mobile-collapsed' => 'true')
        )); ?>
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
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
