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
    'intro_html' => 'Βρες ενδεικτικά το Μισθολογικό Κλιμάκιο, τον αντίστοιχο <strong>βασικό μικτό μισθό</strong> και μια <strong>εκτίμηση καθαρών αποδοχών</strong> με τη φορολογία 2026, με βάση την κατηγορία, τον <strong>ήδη αναγνωρισμένο μισθολογικό χρόνο</strong> και τον ανώτερο τίτλο που έχει ήδη αναγνωριστεί για μισθολογική προώθηση.',
    'badges' => array('ΠΕ / ΤΕ', 'ΔΕ / ΥΕ', 'Αποδοχές από 01/04/2026', 'Φορολογία 2026')
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

        <h3>Προαιρετική εκτίμηση καθαρών αποδοχών</h3>
        <div class="field-grid">
          <div class="field">
            <label for="payrollProfile">Προφίλ κρατήσεων</label>
            <select id="payrollProfile">
              <option value="permanent">Μόνιμος δημόσιος υπάλληλος</option>
              <option value="newly_appointed">Νεοδιόριστος — 1ο έτος ΜΤΠΥ</option>
              <option value="substitute">Αναπληρωτής / ΙΔΟΧ — ΚΠΚ 101</option>
            </select>
          </div>
          <div class="field">
            <label for="ageGroup">Ηλικιακή κατηγορία για τη φορολογία 2026</label>
            <select id="ageGroup">
              <option value="over30">Άνω των 30 ετών</option>
              <option value="age26to30">26–30 ετών</option>
              <option value="upTo25">Έως 25 ετών</option>
            </select>
          </div>
          <div class="field">
            <label for="dependentChildren">Εξαρτώμενα τέκνα</label>
            <input id="dependentChildren" type="number" min="0" max="20" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field edu-field--full">
            <label for="positionAllowance">Θέση ευθύνης — άρθρο 16 ν. 4354/2015</label>
            <select id="positionAllowance">
              <option value="none">Χωρίς θέση ευθύνης</option>
              <optgroup label="Στελέχη εκπαίδευσης">
                <option value="regional_director">Περιφερειακός Διευθυντής Εκπαίδευσης — 1.170 €</option>
                <option value="regional_quality_supervisor">Περιφερειακός Επόπτης Ποιότητας — 780 €</option>
                <option value="education_director">Διευθυντής Π/θμιας ή Δ/θμιας Εκπαίδευσης — 715 €</option>
                <option value="quality_supervisor">Επόπτης Ποιότητας της Εκπαίδευσης — 650 €</option>
                <option value="education_counselor">Σύμβουλος Εκπαίδευσης — 455 €</option>
                <option value="kedasy_head">Προϊστάμενος ΚΕ.Δ.Α.Σ.Υ. / Γραφείου Μειονοτικής Εκπαίδευσης — 455 €</option>
                <option value="education_matters_head">Προϊστάμενος Τμήματος Εκπαιδευτικών Θεμάτων — 390 €</option>
              </optgroup>
              <optgroup label="Σχολικές μονάδες / δομές">
                <option value="lyceum_director">Διευθυντής ΓΕΛ / ΕΠΑΛ / αντίστοιχης δομής — 429 €</option>
                <option value="lyceum_director_large">Διευθυντής ΓΕΛ / ΕΠΑΛ / αντίστοιχης δομής ≥120 μαθητές (Σ.Μ.Ε.Α.Ε. ≥30) — 501 €</option>
                <option value="gymnasium_director">Διευθυντής Γυμνασίου / Ε.Κ. / αντίστοιχης δομής — 358 €</option>
                <option value="gymnasium_director_large">Διευθυντής Γυμνασίου / αντίστοιχης δομής ≥120 μαθητές (Σ.Μ.Ε.Α.Ε. ≥30) — 429 €</option>
                <option value="vice_director">Υποδιευθυντής / Υπεύθυνος Τομέα Ε.Κ. / αντίστοιχη θέση — 195 €</option>
                <option value="small_school_head">Προϊστάμενος 1θέσιου–3θέσιου Δημοτικού / Νηπιαγωγείου — 215 €</option>
              </optgroup>
            </select>
            <small>Τα ποσά είναι τα ισχύοντα μετά την αύξηση 30% από 01-01-2024. Σε συρροή θέσεων επιλέγεται μόνο η ανώτερη θέση.</small>
          </div>
          <div class="edu-field--full">
            <div class="checkrow">
              <input id="remoteAreaAllowance" type="checkbox">
              <label for="remoteAreaAllowance">Επίδομα απομακρυσμένων - παραμεθορίων περιοχών <strong>(+100 € μικτά / μήνα)</strong><small>Επίλεξέ το μόνο αν υπηρετείς σε περιοχή/μονάδα που θεμελιώνει δικαίωμα καταβολής του επιδόματος.</small></label>
            </div>
          </div>
        </div>
        <div class="info-note">
          Η εκτίμηση καθαρών γίνεται πάνω στον <strong>βασικό μισθό του Μ.Κ.</strong>, στην <strong>οικογενειακή παροχή</strong> που αντιστοιχεί στον δηλωμένο αριθμό τέκνων, στο τυχόν <strong>επίδομα θέσης ευθύνης</strong> και, αν επιλεγεί, στο <strong>επίδομα απομακρυσμένων - παραμεθορίων περιοχών</strong>, με 12μηνη φορολογική αναγωγή. Δεν προστίθενται προσωπική διαφορά ή άλλες αποδοχές. Αν ο αριθμός τέκνων που λαμβάνεται υπόψη για φορολογία διαφέρει από εκείνον της οικογενειακής παροχής, η εκτίμηση χρειάζεται διοικητικό έλεγχο.
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
          array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Η εκτίμηση καθαρών είναι ενδεικτική.</strong> Υπολογίζεται από τον βασικό μισθό του Μ.Κ., την οικογενειακή παροχή με βάση τον δηλωμένο αριθμό τέκνων, το τυχόν επίδομα θέσης ευθύνης, το προαιρετικό επίδομα απομακρυσμένων - παραμεθορίων περιοχών και το επιλεγμένο τυπικό προφίλ κρατήσεων. Δεν περιλαμβάνει προσωπική διαφορά, ειδικές ατομικές κρατήσεις, αναδρομικά ή άλλες πηγές εισοδήματος. Η πραγματική μισθοδοσία και φορολογική εκκαθάριση μπορεί να διαφέρουν.'
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
      <?php calculatorResultRow(array('label' => 'Βασικός μισθός (μικτά) από 01/04/2026', 'value' => '1.232 €', 'value_id' => 'basicSalaryResult')); ?>
      <?php calculatorResultRow(array('label' => 'Αφαιρούμενος χρόνος 2016–2017', 'value' => '0 μήνες', 'value_id' => 'suspendedServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μετρήσιμος χρόνος για Μ.Κ.', 'value' => '0 μήνες', 'value_id' => 'countableServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μ.Κ. από υπηρεσία', 'value' => 'Μ.Κ. 1', 'value_id' => 'baseMkResult')); ?>
      <?php calculatorResultRow(array('label' => 'Προώθηση τίτλου', 'value' => '0 Μ.Κ.', 'value_id' => 'promotionResult')); ?>
      <?php calculatorResultRow(array('label' => 'Χρόνος προς επόμενο Μ.Κ.', 'value' => '24 μήνες', 'value_id' => 'nextMkResult')); ?>

      <h3>Ενδεικτικές καθαρές αποδοχές</h3>
      <?php calculatorResultRow(array('label' => 'Οικογενειακή παροχή', 'value' => '0,00 €', 'value_id' => 'familyAllowanceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Επίδομα θέσης ευθύνης', 'value' => '0,00 €', 'value_id' => 'positionAllowanceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Επίδομα απομακρυσμένων - παραμεθορίων', 'value' => '0,00 €', 'value_id' => 'remoteAllowanceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Σύνολο μικτών για εκτίμηση', 'value' => '1.232,00 €', 'value_id' => 'grossForNetResult')); ?>
      <?php calculatorResultRow(array('label' => 'Προφίλ κρατήσεων', 'value' => 'Μόνιμος δημόσιος υπάλληλος', 'value_id' => 'payrollProfileResult')); ?>
      <?php calculatorResultRow(array('label' => 'Τακτικές κρατήσεις', 'value' => '0,00 €', 'value_id' => 'standardDeductionsResult')); ?>
      <?php calculatorResultRow(array('label' => 'Σύνθεση κρατήσεων', 'value' => 'Σύνταξη · Υγεία · Επικουρική · Εφάπαξ · ΜΤΠΥ · Ανεργία', 'value_id' => 'deductionBreakdownResult')); ?>
      <?php calculatorResultRow(array('label' => 'Δικαίωμα εγγραφής ΜΤΠΥ', 'value' => '0,00 €', 'value_id' => 'registrationDeductionResult')); ?>
      <?php calculatorResultRow(array('label' => 'Ετήσιο φορολογητέο (12μηνη αναγωγή)', 'value' => '0,00 €', 'value_id' => 'taxableAnnualResult')); ?>
      <?php calculatorResultRow(array('label' => 'Φόρος κλίμακας πριν τη μείωση', 'value' => '0,00 €', 'value_id' => 'taxBeforeCreditResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μείωση φόρου άρθρου 16 ΚΦΕ', 'value' => '0,00 €', 'value_id' => 'taxCreditResult')); ?>
      <?php calculatorResultRow(array('label' => 'Ετήσιος φόρος', 'value' => '0,00 €', 'value_id' => 'annualTaxResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μηνιαία παρακράτηση φόρου', 'value' => '0,00 €', 'value_id' => 'monthlyTaxResult')); ?>
      <?php calculatorSubtotalRow(array('label' => 'Εκτιμώμενο καθαρό', 'value' => '0,00 €', 'value_id' => 'estimatedNetResult')); ?>
      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusResult', 'html' => 'Συμπλήρωσε τα αναγνωρισμένα στοιχεία για να δεις το Μ.Κ.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Ο ν. 4354/2015 προβλέπει 19 Μ.Κ. για ΠΕ/ΤΕ και 13 για ΔΕ/ΥΕ. Για τη συνήθη μισθολογική εξέλιξη απαιτούνται δύο έτη ανά Μ.Κ. για ΠΕ/ΤΕ και τρία έτη για ΔΕ/ΥΕ. Αναγνωρισμένος συναφής μεταπτυχιακός τίτλος προωθεί κατά 2 Μ.Κ. και διδακτορικό κατά 6 Μ.Κ. στην κατηγορία όπου ανήκει ο υπάλληλος. Από 01-01-2026, ο ν. 5246/2025 προσθέτει ειδική προώθηση +2 Μ.Κ. για Integrated Master που εμπίπτει στις διατάξεις των άρθρων 46 ν. 4485/2017 και 78 ν. 4957/2022.</p>
  <p>Με το άρθρο 26 παρ. 2 του ν. 4354/2015 η μισθολογική εξέλιξη ανεστάλη έως 31-12-2017. Από 01-01-2018 ενεργοποιήθηκε εκ νέου, χωρίς να λαμβάνεται υπόψη για την εξέλιξη το χρονικό διάστημα 01-01-2016 έως 31-12-2017.</p>
  <p><strong>Βασικοί μισθοί 2026:</strong> η εγκύκλιος ΥΠΕΘΟΟ <strong>54692 ΕΞ 2026/03-04-2026</strong> (ΑΔΑ: <strong>ΨΕ7ΨΗ-ΚΧΧ</strong>) αναπροσαρμόζει από 01-04-2026 τους βασικούς μισθούς και στο Παράρτημα, Πίνακες 1–4, αποτυπώνει τα ποσά ανά Μ.Κ. για ΠΕ, ΤΕ, ΔΕ και ΥΕ.</p>
  <p><strong>Οικογενειακή παροχή:</strong> το άρθρο 15 του ν. 4354/2015, όπως ισχύει μετά τον ν. 5045/2023, προβλέπει μηνιαία παροχή <strong>70 € για 1 τέκνο, 120 € για 2, 170 € για 3, 220 € για 4 και +70 € για κάθε επιπλέον τέκνο</strong>. Στην εκτίμηση το ποσό προστίθεται στις μικτές αποδοχές πριν από τον υπολογισμό κρατήσεων και φόρου.</p>
  <p><strong>Επίδομα θέσης ευθύνης:</strong> το άρθρο 16 του ν. 4354/2015, όπως ισχύει για τα στελέχη εκπαίδευσης μετά τον ν. 4823/2021, προβλέπει μηνιαίο επίδομα ανά θέση. Με το άρθρο 22 του ν. 5045/2023 τα ποσά αυξήθηκαν κατά <strong>30%</strong> από 01-01-2024 και στρογγυλοποιούνται στην πλησιέστερη μονάδα ευρώ. Ενδεικτικά: Διευθυντής ΓΕΛ/ΕΠΑΛ <strong>429 € ή 501 €</strong>, Διευθυντής Γυμνασίου <strong>358 € ή 429 €</strong>, Υποδιευθυντής <strong>195 €</strong>. Σε συρροή αξιώσεων από δύο βαθμίδες καταβάλλεται μόνο το ποσό της ανώτερης βαθμίδας. Το εργαλείο προσθέτει μία μόνο επιλεγμένη θέση στις μικτές αποδοχές.</p>
  <p><strong>Επίδομα απομακρυσμένων - παραμεθορίων περιοχών:</strong> το άρθρο 19 του ν. 4354/2015 διατηρεί το επίδομα στο ίδιο ύψος και με τις ίδιες προϋποθέσεις· για τους δικαιούχους το ποσό είναι <strong>100 € μικτά τον μήνα</strong>. Η επιλογή στο εργαλείο είναι προαιρετική και δεν ελέγχει αν η συγκεκριμένη περιοχή/υπηρεσία θεμελιώνει δικαίωμα. Στην ενδεικτική εκτίμηση το ποσό προστίθεται στις μικτές αποδοχές και εφαρμόζεται το επιλεγμένο τυπικό προφίλ κρατήσεων· για το ΜΤΠΥ η ισχύουσα κωδικοποίηση προβλέπει τακτική κράτηση 4,5% στο συγκεκριμένο επίδομα για ασφαλισμένους από 01-01-1993 και μετά.</p>
  <p><strong>Φορολογία 2026:</strong> ο ν. 5246/2025 τροποποίησε από το φορολογικό έτος 2026 την κλίμακα μισθωτών, με ειδικούς συντελεστές ανά αριθμό εξαρτώμενων τέκνων και για ηλικίες έως 25 και 26–30 ετών. Εφαρμόζεται επίσης η μείωση φόρου του άρθρου 16 ΚΦΕ.</p>
  <p><strong>Τυπικά προφίλ κρατήσεων:</strong> για μόνιμο δημόσιο υπάλληλο χρησιμοποιείται συνολικό ποσοστό 22,22% επί του βασικού μισθού (κύρια σύνταξη, υγειονομική περίθαλψη, επικουρική, εφάπαξ, ΜΤΠΥ και εισφορά ανεργίας). Για νεοδιόριστο προστίθεται ενδεικτικά το δικαίωμα εγγραφής ΜΤΠΥ — ένας μηνιαίος μισθός σε 12 ισόποσες δόσεις. Για αναπληρωτή/ΙΔΟΧ χρησιμοποιείται το ποσοστό ασφαλισμένου 13,37% του ΚΠΚ 101. Οι πραγματικές κρατήσεις μπορεί να διαφοροποιούνται από ειδικό καθεστώς ή βάση εισφορών.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-demosion-upallelon/nomos-4354-2015.html', 'Ν. 4354/2015 — Μισθολογικά κλιμάκια & άρθρο 26 ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-oikonomia/n-5045-2023.html', 'Ν. 5045/2023 — Αναπροσαρμογή οικογενειακής παροχής ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/circulars/50271/2-97758-dep-19-10-2023', 'ΥΠΕΘΟΟ 2/97758/ΔΕΠ/19-10-2023 — Επίδομα θέσης ευθύνης από 01/01/2024 ↗'); ?>
    <?php sourceCardLink('https://www.gsis.gr/sites/default/files/2024-05/27-5-2024_Odigies_Simplirosis_e-DAYK.pdf', 'ΓΓΠΣ/e-ΔΑΥΚ — Πίνακας επιδομάτων θέσης εκπαιδευτικών ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/circulars/23568/ar-prwt-2-31029-dep-6-5-2016', 'Εγκύκλιος ΓΛΚ 2/31029/ΔΕΠ/06-05-2016 ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/index.php/n-5246-2025.html', 'Ν. 5246/2025 — Integrated Master ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/circulars/52538/54692-ex-03-04-2026', 'ΥΠΕΘΟΟ 54692 ΕΞ 2026 — Βασικοί μισθοί από 01/04/2026 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/site/18335-26-02-16-epidoma-apomakrysmenon-paramethorion-perioxon', 'ΥΠΑΙΘ — Επίδομα απομακρυσμένων / παραμεθορίων 100 € ↗'); ?>
    <?php sourceCardLink('https://www.gsis.gr/polites-epiheiriseis/pliromes-kai-eispraxeis/e-DAYK/e-dayk-announcements/epidomata-neon-asfalismenon-yper-mtpy', 'ΓΓΠΣ — Κράτηση ΜΤΠΥ στα επιδόματα νέων ασφαλισμένων ↗'); ?>
    <?php sourceCardLink('https://minfin.gov.gr/forologiki-politiki/forologikos-odigos/forologia-eisodimatos/', 'ΥΠΕΘΟΟ — Φορολογία εισοδήματος 2026 ↗'); ?>
    <?php sourceCardLink('https://www.aade.gr/egkyklioi-kai-apofaseis/o-3068-18-11-2025', 'ΑΑΔΕ Ο.3068/2025 — Ν. 5246/2025 ↗'); ?>
    <?php sourceCardLink('https://ypergasias.gov.gr/koinoniki-asfalisi/asfalismenoi-eisfores-kai-paroches/asfalistikes-eisfores/', 'Υπουργείο Εργασίας — Ασφαλιστικές εισφορές ↗'); ?>
    <?php sourceCardLink('https://www.mtpy.gr/pliroforisi1/documents/%CE%95%CE%A0%CE%99%CE%9A%CE%91%CE%99%CE%A1%CE%9F%CE%A0%CE%9F%CE%99%CE%97%CE%9C%CE%95%CE%9D%CE%9F%CE%A3%20%CE%9F%CE%94%CE%97%CE%93%CE%9F%CE%A3%20%CE%9A%CE%A1%CE%91%CE%A4%CE%97%CE%A3%CE%95%CE%A9%CE%9D%20%CE%9F%CE%9A%CE%A4%202019.pdf', 'ΜΤΠΥ — Οδηγός κρατήσεων & δικαίωμα εγγραφής ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Ο υπολογιστής δεν αποφαίνεται αν ένας τίτλος θεμελιώνει δικαίωμα προώθησης. Για τη συνάφεια τίτλου, την αναγνώριση προϋπηρεσίας, ειδικές περιπτώσεις όπως ΤΕ16 και την ημερομηνία οικονομικών αποτελεσμάτων υπερισχύει η ισχύουσα απόφαση του αρμόδιου υπηρεσιακού οργάνου.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/salary-scale-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/salary-net-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
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
    byId('basicSalaryResult').textContent = formatEuro(result.basicGrossSalary);

    const children = integer('dependentChildren', 20);
    const familyAllowance = window.EducationSalaryNet.familyAllowanceMonthly(children);
    const positionKey = byId('positionAllowance').value;
    const positionAllowance = window.EducationSalaryNet.positionAllowanceMonthly(positionKey);
    const remoteAllowance = byId('remoteAreaAllowance').checked
      ? window.EducationSalaryNet.REMOTE_AREA_ALLOWANCE_MONTHLY
      : 0;
    const grossForNet = result.basicGrossSalary + familyAllowance + positionAllowance + remoteAllowance;
    const net = window.EducationSalaryNet.calculate({
      grossMonthly: grossForNet,
      profile: byId('payrollProfile').value,
      ageGroup: byId('ageGroup').value,
      children: children
    });
    byId('familyAllowanceResult').textContent = formatEuroCents(familyAllowance);
    byId('positionAllowanceResult').textContent = formatEuroCents(positionAllowance);
    byId('remoteAllowanceResult').textContent = formatEuroCents(remoteAllowance);
    byId('grossForNetResult').textContent = formatEuroCents(grossForNet);
    byId('payrollProfileResult').textContent = net.profileLabel;
    byId('standardDeductionsResult').textContent = formatEuroCents(net.standardDeductions) + ' (' + formatPercent(net.standardDeductionRate) + ')';
    byId('deductionBreakdownResult').textContent = net.deductionBreakdown;
    byId('registrationDeductionResult').textContent = net.registrationDeduction > 0
      ? formatEuroCents(net.registrationDeduction) + ' (1/12 μισθού)'
      : '0,00 €';
    byId('taxableAnnualResult').textContent = formatEuroCents(net.taxableAnnual);
    byId('taxBeforeCreditResult').textContent = formatEuroCents(net.taxBeforeCredit);
    byId('taxCreditResult').textContent = net.taxCredit > 0 ? '−' + formatEuroCents(net.taxCredit) : '0,00 €';
    byId('annualTaxResult').textContent = formatEuroCents(net.annualTax);
    byId('monthlyTaxResult').textContent = formatEuroCents(net.monthlyTax);
    byId('estimatedNetResult').textContent = formatEuroCents(net.estimatedNet);

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

  function formatEuro(value) {
    const amount = Math.max(0, Math.round(Number(value) || 0));
    try {
      return new Intl.NumberFormat('el-GR', { maximumFractionDigits: 0 }).format(amount) + ' €';
    } catch (e) {
      return String(amount).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' €';
    }
  }

  function formatEuroCents(value) {
    const amount = Math.max(0, Number(value) || 0);
    try {
      return new Intl.NumberFormat('el-GR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount) + ' €';
    } catch (e) {
      return amount.toFixed(2).replace('.', ',') + ' €';
    }
  }

  function formatPercent(value) {
    const percentage = Math.max(0, Number(value) || 0) * 100;
    return percentage.toFixed(2).replace('.', ',') + '%';
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
    byId('payrollProfile').value = 'permanent';
    byId('ageGroup').value = 'over30';
    byId('dependentChildren').value = '0';
    byId('positionAllowance').value = 'none';
    byId('remoteAreaAllowance').checked = false;
    calculate();
  }

  document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      if (el.id === 'dependentChildren') clampBoundedIntegerInput(el, 20);
      if (el.id === 'suspendedYears' || el.id === 'suspendedMonths') clampSuspendedInputs();
      calculate();
    });
    el.addEventListener('change', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      if (el.id === 'dependentChildren') clampBoundedIntegerInput(el, 20);
      if (el.id === 'suspendedYears' || el.id === 'suspendedMonths') clampSuspendedInputs();
      calculate();
    });
  });
  byId('resetBtn').addEventListener('click', reset);
  calculate();
})();
</script>
</body>
</html>
