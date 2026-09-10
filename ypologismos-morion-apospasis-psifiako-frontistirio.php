<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/teacher-specialties.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μοριοδότησης για απόσπαση μονίμων εκπαιδευτικών στο Ψηφιακό Φροντιστήριο 2026-2027.">
  <title>Υπολογισμός μορίων απόσπασης στο Ψηφιακό Φροντιστήριο</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-page-digital-tutoring">
  <main class="page-shell">
    <?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

    <?php calculatorHero(array(
      'title' => 'Υπολογισμός μορίων απόσπασης στο Ψηφιακό Φροντιστήριο',
      'intro' => 'Ενδεικτικός υπολογισμός για την πρόσκληση αποσπάσεων μονίμων εκπαιδευτικών για το σχολικό έτος 2026–2027.',
      'meta_class' => 'hero-meta',
      'badges' => array('Σύνολο: 100 μονάδες', 'Β + Γ πριν από τη συνέντευξη: έως 65', 'Βιντεοσκοπημένο μάθημα: βάση 20/35')
    )); ?>

    <?php calculatorColumnsStart(); ?>
      <?php calculatorMainStart(); ?>
        <?php calculatorCardStart(); ?>
          <h2>Ειδικότητα &amp; προϋποθέσεις</h2>
          <p class="subtitle">Επίλεξε τον κλάδο σου για να εμφανιστούν τα μαθήματα/θέσεις του Παραρτήματος Ι που μπορείς να δηλώσεις. Οι υπόλοιπες προϋποθέσεις δεν μοριοδοτούνται, αλλά επηρεάζουν το παραδεκτό ή τη συνέχεια της διαδικασίας.</p>
          <div class="field-grid">
            <div class="field">
              <label for="specialty">Κλάδος / ειδικότητα</label>
              <select id="specialty">
                <option value="">— Επιλογή —</option>
<?php
$digitalTutoringSpecialties = array(
    'ΠΕ02', 'ΠΕ03', 'ΠΕ04.01', 'ΠΕ04.02', 'ΠΕ04.03', 'ΠΕ04.04', 'ΠΕ04.05', 'ΠΕ07',
    'ΠΕ34', 'ΠΕ40', 'ΠΕ80', 'ΠΕ81', 'ΠΕ82', 'ΠΕ83', 'ΠΕ85', 'ΠΕ86',
    'ΠΕ87.01', 'ΠΕ87.02', 'ΠΕ87.03', 'ΠΕ87.04', 'ΠΕ87.05', 'ΠΕ87.06', 'ΠΕ87.07', 'ΠΕ87.08', 'ΠΕ87.09', 'ΠΕ87.10',
    'ΠΕ88.01', 'ΠΕ88.02', 'ΠΕ88.03', 'ΠΕ88.04', 'ΠΕ88.05', 'ΠΕ89.01', 'ΠΕ90'
);
foreach ($digitalTutoringSpecialties as $code) {
    echo '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8') . '</option>';
}
?>
              </select>
            </div>
            <div class="field">
              <label for="requiredExperience">Έχεις τουλάχιστον 2 έτη διδακτικής προϋπηρεσίας στο σχετικό μάθημα της Γ΄ Λυκείου;</label>
              <select id="requiredExperience">
                <option value="">— Επίλεξε —</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
              </select>
            </div>
            <div class="field">
              <label for="videoFace">Εμφανίζεται το πρόσωπό σου στο βιντεοσκοπημένο μάθημα;</label>
              <select id="videoFace">
                <option value="">— Επίλεξε —</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
              </select>
            </div>
            <div class="field">
              <label for="videoDuration">Η διάρκεια του βίντεο είναι 4–7 λεπτά;</label>
              <select id="videoDuration">
                <option value="">— Επίλεξε —</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
              </select>
            </div>
            <div class="field">
              <label for="eaePosition">Υποβάλλεις αίτηση για θέση Ειδικής Αγωγής (ΕΑΕ);</label>
              <select id="eaePosition">
                <option value="no">Όχι</option>
                <option value="yes">Ναι</option>
              </select>
            </div>
            <div class="field full hidden" id="eaeSpecializationWrap">
              <label for="eaeSpecialization">Διαθέτεις την απαιτούμενη εξειδίκευση στην ΕΑΕ;</label>
              <select id="eaeSpecialization">
                <option value="">— Επίλεξε —</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
              </select>
            </div>
          </div>
          <div id="assignmentBox" class="info">Επίλεξε ειδικότητα για να δεις τα διαθέσιμα μαθήματα/θέσεις του Παραρτήματος Ι.</div>
          <details>
            <summary>Προβολή όλων των θέσεων του Παραρτήματος Ι</summary>
            <div class="mapping-wrap">
              <table class="mapping-table">
                <thead><tr><th>Κατηγορία</th><th>Μάθημα</th><th>Κλάδος</th><th>Θέσεις</th></tr></thead>
                <tbody>
                  <tr><td>ΓΕΛ</td><td>Αρχαία Ελληνικά</td><td>ΠΕ02</td><td>2</td></tr>
                  <tr><td>ΓΕΛ</td><td>Λατινικά</td><td>ΠΕ02</td><td>2</td></tr>
                  <tr><td>ΓΕΛ</td><td>Ιστορία</td><td>ΠΕ02</td><td>2</td></tr>
                  <tr><td>ΓΕΛ</td><td>Μαθηματικά</td><td>ΠΕ03</td><td>2</td></tr>
                  <tr><td>ΓΕΛ</td><td>Χημεία</td><td>ΠΕ04.02, ΠΕ85</td><td>4</td></tr>
                  <tr><td>ΓΕΛ</td><td>Φυσική</td><td>ΠΕ04.01</td><td>2</td></tr>
                  <tr><td>ΓΕΛ</td><td>Βιολογία</td><td>ΠΕ04.04, ΠΕ04.03</td><td>4</td></tr>
                  <tr><td>ΓΕΛ</td><td>Πληροφορική</td><td>ΠΕ86</td><td>4</td></tr>
                  <tr><td>ΓΕΛ</td><td>Οικονομία</td><td>ΠΕ80</td><td>2</td></tr>
                  <tr><td>ΓΕΛ</td><td>Γερμανικά</td><td>ΠΕ07</td><td>1</td></tr>
                  <tr><td>ΓΕΛ</td><td>Γραμμικό Σχέδιο</td><td>ΠΕ89.01, ΠΕ81</td><td>1</td></tr>
                  <tr><td>ΓΕΛ</td><td>Ισπανικά</td><td>ΠΕ40</td><td>2</td></tr>
                  <tr><td>ΓΕΛ</td><td>Ιταλικά</td><td>ΠΕ34</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Αρχές Οικονομικής Θεωρίας</td><td>ΠΕ80</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Αρχές Οργάνωσης και Διοίκησης</td><td>ΠΕ80</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Αρχιτεκτονικό Σχέδιο</td><td>ΠΕ81</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Τεχνολογία Υλικών</td><td>ΠΕ89.01</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Στοιχεία ψύξης – Κλιματισμού</td><td>ΠΕ82</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Στοιχεία Σχεδιασμού Κεντρικών Θερμάνσεων</td><td>ΠΕ82</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Κινητήρες Αεροσκαφών</td><td>ΠΕ82</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Ναυτικές Μηχανές</td><td>ΠΕ82</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Ναυσιπλοΐα ΙΙ</td><td>ΠΕ90</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Προγραμματισμός Υπολογιστών</td><td>ΠΕ86</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Δίκτυα Υπολογιστών</td><td>ΠΕ86</td><td>1</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Υγιεινή</td><td>ΠΕ87</td><td>2</td></tr>
                  <tr><td>ΕΠΑΛ</td><td>Αρχές Βιολογικής Γεωργίας</td><td>ΠΕ88</td><td>1</td></tr>
                  <tr><td>ΕΑΕ</td><td>Μαθήματα Α΄ ανάθεσης ΠΕ02</td><td>ΠΕ02 ΕΑΕ</td><td>1</td></tr>
                  <tr><td>ΕΑΕ</td><td>Μαθήματα Φυσικών Επιστημών</td><td>ΠΕ04 ΕΑΕ</td><td>1</td></tr>
                  <tr><td>ΕΑΕ</td><td>Μαθήματα Α΄ ανάθεσης ΠΕ80</td><td>ΠΕ80 ΕΑΕ</td><td>1</td></tr>
                  <tr><td>ΕΑΕ</td><td>Μαθήματα Α΄ ανάθεσης ΠΕ82</td><td>ΠΕ82 ΕΑΕ</td><td>1</td></tr>
                  <tr><td>ΕΑΕ</td><td>Μαθήματα Α΄ ανάθεσης ΠΕ83</td><td>ΠΕ83 ΕΑΕ</td><td>1</td></tr>
                  <tr><td>ΕΑΕ</td><td>Μαθήματα Α΄ ανάθεσης ΠΕ87</td><td>ΠΕ87 ΕΑΕ</td><td>1</td></tr>
                  <tr><td>ΕΑΕ</td><td>Μαθήματα Α΄ ανάθεσης ΠΕ88</td><td>ΠΕ88 ΕΑΕ</td><td>1</td></tr>
                </tbody>
              </table>
            </div>
          </details>
          <div class="warning">Η πρόσκληση προβλέπει και επιπλέον κωλύματα/προϋποθέσεις απόσπασης. Το παρόν εργαλείο δεν αντικαθιστά τον πλήρη έλεγχο της πρόσκλησης και του ΠΥΜ.</div>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(); ?>
          <div class="criterion-head">
            <div>
              <h2>Α. Γενική παρουσία</h2>
              <p class="subtitle">Αποτιμάται στη διά ζώσης συνέντευξη.</p>
            </div>
            <div class="max">έως 35</div>
          </div>
          <div class="score-row">
            <label for="a1"><strong>Α1. Συγκρότηση σκέψης – λόγου</strong><small>Μέγιστο 20 · βάση επιλογής 12</small></label>
            <input type="number" id="a1" min="0" max="20" step="0.1" value="0">
          </div>
          <div class="score-row">
            <label for="a2"><strong>Α2. Επικοινωνιακές δεξιότητες</strong><small>Μέγιστο 15 · βάση επιλογής 8</small></label>
            <input type="number" id="a2" min="0" max="15" step="0.1" value="0">
          </div>
          <div class="info">Αν δεν έχει πραγματοποιηθεί ακόμη η συνέντευξη, άφησε τα Α1 και Α2 στο 0. Το εργαλείο θα σου δείξει ξεχωριστά τη βαθμολογία Β + Γ που χρησιμοποιείται πριν από τη συνέντευξη.</div>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(); ?>
          <div class="criterion-head">
            <div>
              <h2>Β. Επιστημονική κατάρτιση – εμπειρία</h2>
              <p class="subtitle">Τα επιμέρους κριτήρια αθροίζουν έως 30 μονάδες.</p>
            </div>
            <div class="max">έως 30</div>
          </div>

          <div class="field-grid">
            <div class="field">
              <label for="phd">Συναφές διδακτορικό δίπλωμα <small>+12 μονάδες</small></label>
              <select id="phd"><option value="0">Όχι</option><option value="12">Ναι</option></select>
            </div>
            <div class="field">
              <label for="master">Συναφής μεταπτυχιακός τίτλος <small>+8 μονάδες</small></label>
              <select id="master"><option value="0">Όχι</option><option value="8">Ναι</option></select>
            </div>
            <div class="field">
              <label for="examExperience">Εμπειρία σε Πανελλαδικές Εξετάσεις <small>Θεματοδότης ή βαθμολογητής ή υπεύθυνος μαθήματος σε Βαθμολογικό Κέντρο · +2</small></label>
              <select id="examExperience"><option value="0">Όχι</option><option value="2">Ναι</option></select>
            </div>
          </div>

          <div class="score-row">
            <div>
              <strong>Β4. Πρόσθετη διδακτική προϋπηρεσία στο πανελλαδικώς εξεταζόμενο μάθημα</strong>
              <small>Μόνο πέραν των απαιτούμενων 2 ετών και σε πανελλαδικώς εξεταζόμενο μάθημα του ίδιου κλάδου με την προκηρυσσόμενη θέση, σε δημόσιο ή ιδιωτικό σχολείο ή στο Ψηφιακό Φροντιστήριο. 2 μονάδες ανά πλήρες διδακτικό έτος, 1 μονάδα ανά τετράμηνο, έως 6. Χρόνος μικρότερος του τετραμήνου δεν υπολογίζεται.</small>
            </div>
            <div>
              <label for="extraYears" class="edu-font-13">Πλήρη επιπλέον έτη</label>
              <input type="number" id="extraYears" min="0" max="48" step="1" value="0">
              <label for="extraMonths" class="edu-font-13 edu-mt-8">Υπόλοιπο μηνών (0–11)</label>
              <input type="number" id="extraMonths" min="0" max="11" step="1" value="0">
            </div>
          </div>

          <div class="score-row">
            <label for="ict"><strong>Β5. Πιστοποιημένη γνώση Τ.Π.Ε.</strong><small>Αν υπάρχουν περισσότερες από μία πιστοποιήσεις, βαθμολογείται μόνο η ανώτερη.</small></label>
            <select id="ict">
              <option value="0">Καμία</option>
              <option value="1">Α΄ επίπεδο / άρθρο 9 π.δ. 85/2022 — 1</option>
              <option value="1.5">Β1 — 1,5</option>
              <option value="2">Β ή Β2 — 2</option>
            </select>
          </div>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(); ?>
          <div class="criterion-head">
            <div>
              <h2>Γ. Βιντεοσκοπημένο μάθημα</h2>
              <p class="subtitle">Ολοκληρωμένη εξ αποστάσεως διδασκαλία διάρκειας 4–7 λεπτών.</p>
            </div>
            <div class="max">έως 35</div>
          </div>
          <div class="score-row">
            <label for="videoScore"><strong>Γ1. Βαθμολογία βιντεοσκοπημένου μαθήματος</strong><small>Μέγιστο 35 · βάση επιλογής 20</small></label>
            <input type="number" id="videoScore" min="0" max="35" step="0.1" value="" placeholder="0–35">
          </div>
          <div class="note">Για την κλήση σε συνέντευξη λαμβάνονται υπόψη οι περισσότεροι βαθμοί στις κατηγορίες <strong>Β + Γ</strong>, με απαραίτητη βάση τουλάχιστον <strong>20 μονάδων στη Γ</strong>. Η πρόσκληση προβλέπει κλήση κατά ανώτατο όριο του τριπλάσιου αριθμού υποψηφίων σε σχέση με τις θέσεις.</div>
        <?php calculatorCardEnd(); ?>
      <?php calculatorMainEnd(); ?>

      <?php calculatorResultsStart(array('class' => 'results', 'attrs' => array('aria-live' => 'polite'))); ?>
        <?php calculatorCardStart(); ?>
          <?php calculatorScoreHeader(array(
            'variant' => 'capped',
            'class' => 'big-total',
            'value_id' => 'totalScore',
            'value_html' => '0',
            'value_class' => 'number',
            'cap_html' => '/ 100 μονάδες',
            'cap_class' => 'outof'
          )); ?>
          <div class="bar"><div id="totalBar"></div></div>

          <?php calculatorResultRow(array('label_html' => 'Α. Γενική παρουσία', 'value_html' => '0 / 35', 'value_id' => 'aTotal')); ?>
          <?php calculatorResultRow(array('label_html' => 'Β. Κατάρτιση – εμπειρία', 'value_html' => '0 / 30', 'value_id' => 'bTotal')); ?>
          <?php calculatorResultRow(array('label_html' => 'Γ. Βιντεοσκοπημένο μάθημα', 'value_html' => '0 / 35', 'value_id' => 'cTotal')); ?>

          <div class="pre-interview">
            <div>Β + Γ πριν από τη συνέντευξη</div>
            <strong id="preInterview">0 / 65</strong>
          </div>

          <div id="statusBox" class="status-box" role="status" aria-live="polite"></div>

          <?php calculatorActions(array(array('attrs' => array('class' => 'secondary', 'type' => 'button', 'id' => 'resetBtn'), 'html' => 'Καθαρισμός'))); ?>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(); ?>
          <h2>Ανάλυση Β</h2>
          <?php calculatorResultRow(array('label_html' => 'Β1. Διδακτορικό', 'value_html' => '0', 'value_id' => 'b1Result')); ?>
          <?php calculatorResultRow(array('label_html' => 'Β2. Μεταπτυχιακό', 'value_html' => '0', 'value_id' => 'b2Result')); ?>
          <?php calculatorResultRow(array('label_html' => 'Β3. Πανελλαδικές', 'value_html' => '0', 'value_id' => 'b3Result')); ?>
          <?php calculatorResultRow(array('label_html' => 'Β4. Πρόσθετη προϋπηρεσία', 'value_html' => '0', 'value_id' => 'b4Result')); ?>
          <?php calculatorResultRow(array('label_html' => 'Β5. Τ.Π.Ε.', 'value_html' => '0', 'value_id' => 'b5Result')); ?>
        <?php calculatorCardEnd(); ?>
      <?php calculatorResultsEnd(); ?>
    <?php calculatorColumnsEnd(); ?>

    <?php sourceCardStart(); ?><p><strong>Πηγή:</strong> Πρόσκληση 86300/Δ7/29.06.2026 για αποσπάσεις μονίμων εκπαιδευτικών στο Ψηφιακό Φροντιστήριο για το σχολικό έτος 2026–2027.</p><?php sourceCardLinksStart(); ?><?php sourceCardLink('https://diavgeia.gov.gr/doc/934%CE%9946%CE%9D%CE%9A%CE%A0%CE%94-80%CE%9C?inline=true', 'Πρόσκληση Ψηφιακού Φροντιστηρίου — Διαύγεια (ΑΔΑ 934Ι46ΝΚΠΔ-80Μ) ↗'); ?><?php sourceCardLinksEnd(); ?><?php sourceCardDisclaimerStart(); ?>Το εργαλείο παρέχει ενδεικτικό υπολογισμό και δεν αντικαθιστά την επίσημη πρόσκληση, τα δικαιολογητικά και την κρίση της αρμόδιας επιτροπής.<?php sourceCardDisclaimerEnd(); ?><?php sourceCardEnd(); ?>
</main>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/digital-tutoring-detachment-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
