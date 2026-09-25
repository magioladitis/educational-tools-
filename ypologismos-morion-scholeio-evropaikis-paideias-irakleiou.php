<?php require_once __DIR__ . '/includes/config.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="manifest" href="manifest.webmanifest">
  <meta name="description" content="Υπολογισμός μορίων για το Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου με βάση την πρόσκληση 69163/Η2/28-05-2026 για το σχολικό έτος 2026-2027.">
  <title>Μόρια — Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-page-heraklion-european-education">
<main class="page-shell">
  <?php require_once __DIR__ . '/includes/header.php'; ?>
  <?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

  <?php calculatorHero(array(
    'title' => 'Μόρια — Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου',
    'intro' => 'Υπολόγισε τη μοριοδότηση της πρόσκλησης 2026–2027 για το Σ.Ε.Π. Ηρακλείου και κάνε έναν βασικό έλεγχο των προϋποθέσεων συμμετοχής.',
    'meta_class' => 'hero-meta',
    'badges' => array('Κατάρτιση: έως 45', 'Εμπειρία: έως 25', 'Συνέντευξη: έως 30', 'Σύνολο: 100', 'Πρόσκληση 69163/Η2/28-05-2026')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>

      <?php calculatorCardStart(); ?>
        <h2>1. Βασικές προϋποθέσεις</h2>
        <p class="subtitle">Η πρόσκληση είναι ανεξάρτητη από τους πίνακες αναπληρωτών και ωρομισθίων. Οι φυσικοί ομιλητές έχουν προτεραιότητα· για μη φυσικούς ομιλητές προβλέπεται χωριστός πίνακας υπό ειδικές προϋποθέσεις.</p>
        <div class="field-grid">
          <div class="field">
            <label for="candidateRoute">Κατηγορία υποψηφίου</label>
            <select id="candidateRoute">
              <option value="">— Επίλεξε —</option>
              <option value="native">Φυσικός ομιλητής της γλώσσας που αφορά η θέση</option>
              <option value="non_native">Μη φυσικός ομιλητής — εξαιρετική διαδρομή</option>
            </select>
          </div>
          <div class="field">
            <label for="targetLanguage">Γλώσσα που αφορά η θέση <small>Για τον έλεγχο/σύνοψη· δεν αλλάζει τη μοριοδότηση.</small></label>
            <select id="targetLanguage">
              <option value="">— Επίλεξε —</option>
              <option value="english">Αγγλικά</option>
              <option value="french">Γαλλικά</option>
              <option value="german">Γερμανικά</option>
              <option value="spanish">Ισπανικά</option>
              <option value="italian">Ιταλικά</option>
            </select>
          </div>
          <div class="field">
            <label for="teachingQualification">Διαθέτω το απαιτούμενο τυπικό προσόν διδασκαλίας;<small id="qualificationHint">Για φυσικό ομιλητή: τίτλος που παρέχει δικαίωμα διδασκαλίας στο αντίστοιχο εκπαιδευτικό σύστημα της γλώσσας/χώρας.</small></label>
            <select id="teachingQualification"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="appointmentObstacle">Υπάρχει κώλυμα διορισμού κατά το άρθρο 8 του ν. 3528/2007;</label>
            <select id="appointmentObstacle"><option value="">— Επίλεξε —</option><option value="no">Όχι</option><option value="yes">Ναι / πιθανόν</option></select>
          </div>
          <div class="field">
            <label for="healthFitness">Διαθέτω την απαιτούμενη υγεία και φυσική καταλληλότητα;</label>
            <select id="healthFitness"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν μπορώ να το δηλώσω</option></select>
          </div>
        </div>
        <div id="nonNativeRequirements" class="field-grid hidden edu-mt-13">
          <div class="field">
            <label for="excellentRequiredLanguage">Άριστη γνώση της γλώσσας που απαιτεί η θέση;</label>
            <select id="excellentRequiredLanguage"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="inspectorAgreement">Υπάρχει η σύμφωνη γνώμη του αρμόδιου Εθνικού Επιθεωρητή Ευρωπαϊκών Σχολείων;</label>
            <select id="inspectorAgreement"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν υπάρχει ακόμη</option></select>
          </div>
        </div>
        <?php calculatorDisclosureStart(array('summary' => 'Πώς λειτουργεί η προτεραιότητα φυσικών ομιλητών;', 'open' => true, 'attrs' => array('data-mobile-collapsed' => 'true'))); ?>
          <p>Η πρόσκληση προβλέπει προτεραιότητα στους φυσικούς ομιλητές. Οι μη φυσικοί ομιλητές μπορούν να ενταχθούν σε χωριστό πίνακα μόνο υπό τις ειδικές προϋποθέσεις της πρόσκλησης και ο πίνακας αυτός ενεργοποιείται μετά την εξάντληση του πίνακα φυσικών ομιλητών.</p>
        <?php calculatorDisclosureEnd(); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>2. Επιστημονική & παιδαγωγική κατάρτιση</h2>
        <p class="subtitle">Μέγιστο 45 μόρια. Κάθε γραμμή αντιστοιχεί σε διακριτό προσόν. Αν master και διδακτορικό είναι στο ίδιο αντικείμενο, το master δεν προσμετράται.</p>
        <div class="check"><input type="checkbox" id="relevantPhd"><label for="relevantPhd">Διδακτορικό συναφές με την ειδικότητα ή στις επιστήμες της αγωγής <small>8 μόρια</small></label></div>
        <div class="check"><input type="checkbox" id="otherPhd"><label for="otherPhd">Διδακτορικό μη συναφές με την ειδικότητα <small>4 μόρια</small></label></div>
        <div class="check"><input type="checkbox" id="relevantMaster"><label for="relevantMaster">Master συναφές με την ειδικότητα ή στις επιστήμες της αγωγής <small>4 μόρια</small></label></div>
        <div id="relevantMasterOverlapWrap" class="check hidden"><input type="checkbox" id="relevantMasterSameSubjectAsPhd"><label for="relevantMasterSameSubjectAsPhd">Το παραπάνω master είναι στο ίδιο αντικείμενο με δηλωμένο διδακτορικό <small>τότε δεν μοριοδοτείται</small></label></div>
        <div class="check"><input type="checkbox" id="otherMaster"><label for="otherMaster">Master μη συναφές με την ειδικότητα <small>2 μόρια</small></label></div>
        <div id="otherMasterOverlapWrap" class="check hidden"><input type="checkbox" id="otherMasterSameSubjectAsPhd"><label for="otherMasterSameSubjectAsPhd">Το παραπάνω master είναι στο ίδιο αντικείμενο με δηλωμένο διδακτορικό <small>τότε δεν μοριοδοτείται</small></label></div>

        <div class="field-grid edu-mt-13">
          <div class="field">
            <label for="greekLevel">Γνώση ελληνικής γλώσσας</label>
            <select id="greekLevel"><option value="none">Δεν δηλώνω μοριοδοτούμενο επίπεδο</option><option value="good">Καλή γνώση — 5 μόρια</option><option value="very_good">Πολύ καλή γνώση — 10 μόρια</option></select>
          </div>
          <div class="field">
            <label for="otherLanguagesCount">Άλλες γλώσσες ≥ Β2 <small>Εκτός μητρικής και ελληνικής· 4 μόρια για καθεμία, έως 3.</small></label>
            <input type="number" id="otherLanguagesCount" min="0" max="3" step="1" inputmode="numeric" value="0">
          </div>
        </div>
        <div class="check"><input type="checkbox" id="publication"><label for="publication">Δημοσιευμένη συγγραφική εργασία <small>3 μόρια</small></label></div>
        <div class="check"><input type="checkbox" id="secondDegree"><label for="secondDegree">Άλλος πανεπιστημιακός τίτλος ειδικότητας ή στις επιστήμες της αγωγής <small>2 μόρια</small></label></div>
        <div class="note"><strong>Χωρίς αυθαίρετη πρόσθετη μοριοδότηση:</strong> η πρόσκληση αναφέρει ότι η γνώση Η/Υ, η επιμόρφωση στην ειδικότητα και τα σεμινάρια συνεκτιμώνται στη διαδικασία επιλογής, χωρίς να τους αποδίδει χωριστές μονάδες στον πίνακα των 100.</div>
        <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο κατάρτισης', 'value_id' => 'academicSubtotal', 'value_html' => '0 / 45')); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>3. Διδακτική εμπειρία σε Ευρωπαϊκό Σχολείο / Σ.Ε.Π.</h2>
        <p class="subtitle">5 μόρια για κάθε έτος υπηρεσίας, έως πέντε έτη.</p>
        <div class="field">
          <label for="europeanSchoolYears">Πλήρη έτη διδακτικής υπηρεσίας <small>Σε Ευρωπαϊκό Σχολείο ή στο Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου.</small></label>
          <input type="number" id="europeanSchoolYears" min="0" max="5" step="1" inputmode="numeric" value="0">
        </div>
        <div class="note">Ο πίνακας της πρόσκλησης ορίζει 5 μόρια «για κάθε έτος» και δεν εξειδικεύει στην ίδια διάταξη αναγωγή υπολοίπου μηνών ή ημερών. Για να μην εισάγεται μη προβλεπόμενος κανόνας, το εργαλείο υπολογίζει πλήρη έτη.</div>
        <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο εμπειρίας', 'value_id' => 'serviceSubtotal', 'value_html' => '0 / 25')); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>4. Συνέντευξη</h2>
        <p class="subtitle">Συμπλήρωσε τα πεδία μόνο όταν είναι γνωστή η επίσημη βαθμολογία της Επιτροπής.</p>
        <div class="field-grid">
          <div class="field"><label for="interviewGreek">Γνώση ελληνικής γλώσσας στον προφορικό λόγο <small>0–20 μόρια.</small></label><input type="number" id="interviewGreek" min="0" max="20" step="0.1" value="" placeholder="0–20"></div>
          <div class="field"><label for="interviewPersonality">Γενική εικόνα και συγκρότηση προσωπικότητας <small>0–10 μόρια.</small></label><input type="number" id="interviewPersonality" min="0" max="10" step="0.1" value="" placeholder="0–10"></div>
        </div>
        <div class="note">Αν ο υποψήφιος έχει αξιολογηθεί για τον ίδιο λόγο και την ίδια θέση σε προηγούμενο έτος, μπορεί κατόπιν αιτήματος να μη συμμετάσχει σε νέα συνέντευξη και να ληφθεί υπόψη η τελευταία βαθμολογία του. Σε ισοβαθμία προηγείται ο υποψήφιος με καλύτερη γνώση της ελληνικής γλώσσας· αν παραμένει η ισοβαθμία, προβλέπεται δημόσια κλήρωση.</div>
        <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο συνέντευξης', 'value_id' => 'interviewSubtotal', 'value_html' => '— / 30')); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>5. Δυναμικό checklist δικαιολογητικών</h2>
        <p class="subtitle">Η λίστα προσαρμόζεται στα προσόντα που δηλώνεις. Είναι βοηθητική και δεν αντικαθιστά το πλήρες κεφάλαιο δικαιολογητικών της πρόσκλησης.</p>
        <ul id="documentsChecklist" class="document-checklist"></ul>
      <?php calculatorCardEnd(); ?>

    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'card results', 'aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'variant' => 'staged',
        'class' => 'stage',
        'context_html' => 'Μόρια πριν από τη συνέντευξη',
        'context_attrs' => array('class' => 'stage-label'),
        'value_html' => '<span id="preInterviewTotal">0</span> <small class="edu-stage-suffix">/ 70</small>',
        'value_class' => 'stage-number'
      )); ?>
      <?php calculatorResultRow(array('label_html' => 'Κατάρτιση', 'value_html' => '0 / 45', 'value_id' => 'academicResult')); ?>
      <?php calculatorResultRow(array('label_html' => 'Εμπειρία', 'value_html' => '0 / 25', 'value_id' => 'serviceResult')); ?>
      <?php calculatorResultRow(array('label_html' => 'Συνέντευξη', 'value_html' => '— / 30', 'value_id' => 'interviewResult')); ?>
      <?php calculatorDisclosureStart(array('summary' => 'Αναλυτική κατανομή μορίων', 'class' => 'breakdown-box edu-result-disclosure')); ?>
        <div id="academicBreakdown" class="breakdown-list"></div>
        <div id="serviceBreakdown" class="breakdown-list"></div>
      <?php calculatorDisclosureEnd(); ?>
      <?php calculatorScoreHeader(array(
        'variant' => 'final',
        'class' => 'stage',
        'context_html' => 'Τελική βαθμολογία',
        'context_attrs' => array('class' => 'stage-label'),
        'value_id' => 'finalTotal',
        'value_html' => '—',
        'value_class' => 'stage-number final',
        'cap_id' => 'finalHelp',
        'cap_html' => 'Η συνέντευξη συμπληρώνεται μόνο όταν είναι γνωστή η επίσημη βαθμολογία.',
        'cap_class' => 'edu-small-muted'
      )); ?>
      <div id="eligibilityStatus" role="status" aria-live="polite"></div>
      <?php calculatorActions(array(
        array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'),
        array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Καθαρισμός')
      )); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>

  <?php sourceCardStart(); ?>
    <p><strong>Βάση υπολογισμού:</strong> Πρόσκληση 69163/Η2/28-05-2026 για την πλήρωση θέσεων διδακτικού προσωπικού στο Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου για το σχολικό έτος 2026–2027. Η προθεσμία αιτήσεων ήταν 02/06/2026–09/06/2026, ώρα 15:00.</p>
    <?php sourceCardLinksStart(); ?>
      <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2023/%CE%B7%CF%81%CE%AC%CE%BA%CE%BB%CE%B5%CE%B9%CE%BF_%CF%80%CF%81%CF%8C%CF%83%CE%BA%CE%BB%CE%B7%CF%83%CE%B7.pdf', 'ΥΠΑΙΘΑ — επίσημη πρόσκληση (PDF) ↗'); ?>
      <?php sourceCardLink('https://sepherapp.pdekritis.gr', 'Ηλεκτρονική εφαρμογή Σ.Ε.Π. Ηρακλείου ↗'); ?>
    <?php sourceCardLinksEnd(); ?>
    <?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι ενημερωτικό. Η επίσημη αναγνώριση προσόντων, η επιλεξιμότητα και η κατάταξη ανήκουν στην αρμόδια Επιτροπή.<?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardEnd(); ?>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/heraklion-european-education-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/heraklion-european-education-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
