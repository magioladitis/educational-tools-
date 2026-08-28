<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός Μισθολογικού Κλιμακίου (Μ.Κ.) εκπαιδευτικού</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorContainerStart(array('class' => 'app')); ?>
  <?php calculatorHero(array(
    'title' => 'Υπολογισμός Μισθολογικού Κλιμακίου (Μ.Κ.)',
    'intro_html' => 'Βρες ενδεικτικά το Μισθολογικό Κλιμάκιο με βάση την κατηγορία, τον <strong>ήδη αναγνωρισμένο μισθολογικό χρόνο</strong> και τον ανώτερο τίτλο που έχει ήδη αναγνωριστεί για μισθολογική προώθηση.',
    'badges' => array('ΠΕ / ΤΕ', 'ΔΕ / ΥΕ', 'Ν. 4354/2015')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title' => 'Στοιχεία μισθολογικής κατάταξης')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="category">Κατηγορία</label>
            <select id="category">
              <option value="PE">ΠΕ — Πανεπιστημιακής Εκπαίδευσης</option>
              <option value="TE">ΤΕ — Τεχνολογικής Εκπαίδευσης</option>
              <option value="DE">ΔΕ — Δευτεροβάθμιας Εκπαίδευσης</option>
              <option value="YE">ΥΕ — Υποχρεωτικής Εκπαίδευσης</option>
            </select>
          </div>
          <div class="field">
            <label for="qualification">Ανώτερο προσόν που έχει ήδη αναγνωριστεί για μισθολογική προώθηση από την υπηρεσία σας</label>
            <select id="qualification">
              <option value="none">Χωρίς αναγνωρισμένη προώθηση</option>
              <option value="master">Αναγνωρισμένο συναφές μεταπτυχιακό — +2 Μ.Κ.</option>
              <option value="integrated">Integrated Master ελληνικού Α.Ε.Ι. που πληροί τις προϋποθέσεις — +2 Μ.Κ. από 01-01-2026</option>
              <option value="phd">Αναγνωρισμένο συναφές διδακτορικό — +6 Μ.Κ.</option>
            </select>
          </div>
          <div class="field">
            <label for="serviceYears">Συνολικά αναγνωρισμένα έτη υπηρεσίας</label>
            <input id="serviceYears" type="number" min="0" max="50" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceMonths">Επιπλέον αναγνωρισμένοι μήνες</label>
            <input id="serviceMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
          </div>
          <div class="edu-field--full">
            <div class="info-note"><strong>Υπηρεσία στη διετία 01-01-2016 έως 31-12-2017</strong><br>Δήλωσε πόση από τη συνολική αναγνωρισμένη υπηρεσία διανύθηκε στη διετία που δεν λαμβάνεται υπόψη για μισθολογική εξέλιξη.</div>
            <div class="field-grid">
              <div class="field">
                <label for="suspendedYears">Έτη υπηρεσίας στη διετία</label>
                <input id="suspendedYears" type="number" min="0" max="2" step="1" value="0" inputmode="numeric">
              </div>
              <div class="field">
                <label for="suspendedMonths">Επιπλέον μήνες στη διετία</label>
                <input id="suspendedMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
              </div>
            </div>
          </div>
        </div>

        <div class="info-note">
          <strong>Αναστολή μισθολογικής εξέλιξης 2016–2017:</strong> ο δηλωμένος χρόνος της συγκεκριμένης διετίας (έως 24 μήνες) <strong>δεν λαμβάνεται υπόψη για μισθολογική εξέλιξη</strong> και αφαιρείται αυτόματα από τον υπολογισμό του Μ.Κ. Αν δεν έχεις υπηρεσία μέσα στη διετία, άφησε Έτη και Μήνες στο 0.
        </div>
        <div class="info-note">
          Καταχώρισε <strong>μόνο</strong> χρόνο προϋπηρεσίας και τίτλο που έχουν ήδη αναγνωριστεί μισθολογικά από την αρμόδια υπηρεσία. Το εργαλείο δεν κρίνει αν μια προϋπηρεσία είναι αναγνωρίσιμη ούτε αν ένας τίτλος είναι συναφής.
        </div>
        <div class="info-note">
          Από <strong>01-01-2026</strong>, η ειδική προώθηση κατά <strong>2 Μ.Κ.</strong> για Integrated Master αφορά ενιαίο και αδιάσπαστο τίτλο <strong>ελληνικού Α.Ε.Ι.</strong> που εμπίπτει στο άρθρο 46 του ν. 4485/2017 ή στο άρθρο 78 του ν. 4957/2022. <strong>Integrated Master αλλοδαπής δεν καλύπτεται από αυτή την ειδική ρύθμιση.</strong> Επίσης δεν χορηγείται δεύτερη προώθηση όταν έχει ήδη δοθεί προώθηση λόγω διακριτού μεταπτυχιακού τίτλου.
        </div>
        <div class="info-note">
          <strong>Ειδικές περιπτώσεις:</strong> η δυνατότητα μισθολογικής προώθησης δεν προκύπτει μόνο από την κατηγορία ΠΕ/ΤΕ/ΔΕ ή από την απλή κατοχή ενός τίτλου. Για μεταπτυχιακό ή διδακτορικό απαιτούνται οι νόμιμες προϋποθέσεις και, όπου απαιτείται, αναγνώριση συνάφειας από το αρμόδιο υπηρεσιακό όργανο. Η κατηγορία <strong>ΔΕ</strong> δεν αποκλείεται αυτομάτως, αλλά η αναγνώριση πρέπει να έχει προηγηθεί υπηρεσιακά. Ιδιαίτερη προσοχή απαιτείται σε περιπτώσεις όπως ο κλάδος <strong>ΤΕ16</strong>, όπου έχουν τεθεί ειδικά ζητήματα ως προς τον βασικό τίτλο διορισμού και τη μισθολογική αναγνώριση μεταπτυχιακών τίτλων. <strong>Μην επιλέγεις +2 ή +6 Μ.Κ. μόνο επειδή κατέχεις τον τίτλο· επίλεξέ το μόνο αν η αντίστοιχη μισθολογική προώθηση έχει ήδη αναγνωριστεί από την υπηρεσία σου.</strong>
        </div>

        <?php calculatorActions(array(
          array('id' => 'calculateBtn', 'class' => 'primary', 'label' => 'Υπολογισμός Μ.Κ.'),
          array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Δεν υπολογίζεται μισθός.</strong> Το εργαλείο προσδιορίζει μόνο το ενδεικτικό Μ.Κ. Η πραγματική μισθολογική κατάταξη προκύπτει από την υπηρεσιακή πράξη και μπορεί να επηρεάζεται από ειδικές διατάξεις ή μεταβατικούς χρόνους.'
      )); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value' => 'Μ.Κ. 1',
        'value_id' => 'finalMkResult',
        'label' => 'ενδεικτικό Μισθολογικό Κλιμάκιο',
        'cap' => 'Με βάση τα αναγνωρισμένα στοιχεία'
      )); ?>
      <?php calculatorResultRow(array('label' => 'Κατηγορία', 'value' => 'ΠΕ', 'value_id' => 'categoryResult')); ?>
      <?php calculatorResultRow(array('label' => 'Αφαιρούμενος χρόνος 2016–2017', 'value' => '0 μήνες', 'value_id' => 'suspendedServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μετρήσιμος χρόνος για Μ.Κ.', 'value' => '0 μήνες', 'value_id' => 'countableServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μ.Κ. από υπηρεσία', 'value' => 'Μ.Κ. 1', 'value_id' => 'baseMkResult')); ?>
      <?php calculatorResultRow(array('label' => 'Προώθηση τίτλου', 'value' => '0 Μ.Κ.', 'value_id' => 'promotionResult')); ?>
      <?php calculatorResultRow(array('label' => 'Χρόνος προς επόμενο Μ.Κ.', 'value' => '24 μήνες', 'value_id' => 'nextMkResult')); ?>
      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusResult', 'html' => 'Συμπλήρωσε τα αναγνωρισμένα στοιχεία για να δεις το Μ.Κ.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Ο ν. 4354/2015 προβλέπει 19 Μ.Κ. για ΠΕ/ΤΕ και 13 για ΔΕ/ΥΕ. Για τη συνήθη μισθολογική εξέλιξη απαιτούνται δύο έτη ανά Μ.Κ. για ΠΕ/ΤΕ και τρία έτη για ΔΕ/ΥΕ. Αναγνωρισμένος συναφής μεταπτυχιακός τίτλος προωθεί κατά 2 Μ.Κ. και διδακτορικό κατά 6 Μ.Κ. στην κατηγορία όπου ανήκει ο υπάλληλος. Από 01-01-2026, ο ν. 5246/2025 προσθέτει ειδική προώθηση +2 Μ.Κ. για Integrated Master που εμπίπτει στις διατάξεις των άρθρων 46 ν. 4485/2017 και 78 ν. 4957/2022.</p>
  <p>Με το άρθρο 26 παρ. 2 του ν. 4354/2015 η μισθολογική εξέλιξη ανεστάλη έως 31-12-2017. Από 01-01-2018 ενεργοποιήθηκε εκ νέου, χωρίς να λαμβάνεται υπόψη για την εξέλιξη το χρονικό διάστημα 01-01-2016 έως 31-12-2017.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-demosion-upallelon/nomos-4354-2015.html', 'Ν. 4354/2015 — Μισθολογικά κλιμάκια & άρθρο 26 ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/circulars/23568/ar-prwt-2-31029-dep-6-5-2016', 'Εγκύκλιος ΓΛΚ 2/31029/ΔΕΠ/06-05-2016 ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/index.php/n-5246-2025.html', 'Ν. 5246/2025 — Integrated Master ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Ο υπολογιστής δεν αποφαίνεται αν ένας τίτλος θεμελιώνει δικαίωμα προώθησης. Για τη συνάφεια τίτλου, την αναγνώριση προϋπηρεσίας, ειδικές περιπτώσεις όπως ΤΕ16 και την ημερομηνία οικονομικών αποτελεσμάτων υπερισχύει η ισχύουσα απόφαση του αρμόδιου υπηρεσιακού οργάνου.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/salary-scale-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function () {
  'use strict';
  const byId = id => document.getElementById(id);
  function integer(id, max) {
    const n = Math.max(0, Math.floor(Number(byId(id).value) || 0));
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

  function calculate() {
    const result = window.EducationSalaryScale.calculate({
      category: byId('category').value,
      years: integer('serviceYears', 50),
      months: integer('serviceMonths', 11),
      qualification: byId('qualification').value,
      suspendedYears: integer('suspendedYears', 2),
      suspendedMonths: integer('suspendedMonths', 11)
    });

    byId('finalMkResult').textContent = 'Μ.Κ. ' + result.finalMK;
    const categoryLabels = { PE: 'ΠΕ', TE: 'ΤΕ', DE: 'ΔΕ', YE: 'ΥΕ' };
    byId('categoryResult').textContent = categoryLabels[result.categoryCode] || categoryLabels[byId('category').value] || result.category || 'ΠΕ';
    byId('suspendedServiceResult').textContent = formatServiceMonths(result.suspendedServiceMonths);
    byId('countableServiceResult').textContent = formatServiceMonths(result.countableServiceMonths);
    byId('baseMkResult').textContent = 'Μ.Κ. ' + result.baseMK;
    byId('promotionResult').textContent = result.promotionMK + ' Μ.Κ.';
    byId('nextMkResult').textContent = result.capped ? 'Καταληκτικό Μ.Κ.' : result.monthsToNext + ' μήνες';

    const status = byId('statusResult');
    if (result.suspendedServiceAdjusted) {
      status.textContent = 'Ο δηλωμένος χρόνος της διετίας 2016–2017 υπερέβαινε τη συνολική υπηρεσία και περιορίστηκε στον διαθέσιμο χρόνο.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
    } else if (result.promotionCapped) {
      status.textContent = 'Η προώθηση περιορίζεται στο καταληκτικό Μ.Κ. ' + result.maxMK + ' της κατηγορίας.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
    } else if (result.qualification !== 'none') {
      status.textContent = result.qualificationLabel + '. Ο χρόνος 2016–2017 που δηλώθηκε έχει αφαιρεθεί από τη μισθολογική εξέλιξη. Χρησιμοποίησε την επιλογή τίτλου μόνο αν η προώθηση έχει αναγνωριστεί υπηρεσιακά.';
      status.className = 'result-message edu-message result-message--success edu-message--success';
    } else {
      status.textContent = result.suspendedServiceMonths > 0
        ? 'Ο χρόνος 2016–2017 αφαιρέθηκε. Ο υπολογισμός γίνεται με τον υπόλοιπο μισθολογικά μετρήσιμο χρόνο.'
        : 'Υπολογισμός με βάση τον αναγνωρισμένο μισθολογικό χρόνο.';
      status.className = 'result-message edu-message result-message--status edu-message--status';
    }
  }

  function formatServiceMonths(totalMonths) {
    const total = Math.max(0, Math.floor(Number(totalMonths) || 0));
    const years = Math.floor(total / 12);
    const months = total % 12;
    if (years && months) return years + ' έτη ' + months + ' μήνες';
    if (years) return years + (years === 1 ? ' έτος' : ' έτη');
    return months + (months === 1 ? ' μήνας' : ' μήνες');
  }

  function clampSuspendedInputs() {
    const yearsEl = byId('suspendedYears');
    const monthsEl = byId('suspendedMonths');
    clampBoundedIntegerInput(yearsEl, 2);
    clampBoundedIntegerInput(monthsEl, 11);
    if (Number(yearsEl.value) >= 2 && Number(monthsEl.value) > 0) monthsEl.value = '0';
  }

  function reset() {
    byId('category').value = 'PE';
    byId('serviceYears').value = '0';
    byId('serviceMonths').value = '0';
    byId('suspendedYears').value = '0';
    byId('suspendedMonths').value = '0';
    byId('qualification').value = 'none';
    calculate();
  }

  document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      if (el.id === 'suspendedYears' || el.id === 'suspendedMonths') clampSuspendedInputs();
      calculate();
    });
    el.addEventListener('change', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      if (el.id === 'suspendedYears' || el.id === 'suspendedMonths') clampSuspendedInputs();
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
