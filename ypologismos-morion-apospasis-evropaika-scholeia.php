<?php require_once __DIR__ . '/includes/config.php'; ?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Υπολογισμός μορίων απόσπασης εκπαιδευτικών στα Ευρωπαϊκά Σχολεία με βάση την πρόσκληση 33598/Η2/18-3-2026.">
<title>Μόρια Απόσπασης σε Ευρωπαϊκά Σχολεία</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-page-european-schools">
<main class="page-shell">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php calculatorHero(array(
    'title' => 'Μόρια Απόσπασης σε Ευρωπαϊκά Σχολεία',
    'intro' => 'Υπολόγισε τα μόρια τυπικών προσόντων και διδακτικής εμπειρίας πριν από τη συνέντευξη και, όταν είναι διαθέσιμες οι βαθμολογίες της προφορικής διαδικασίας, το τελικό σύνολο.',
    'meta_class' => 'hero-meta',
    'badges' => array('Α + Β: έως 50', 'Συνέντευξη: έως 40', 'Τελικό: έως 90', 'Πρόσκληση 33598/Η2/18-3-2026')
)); ?>
<?php calculatorColumnsStart(); ?><?php calculatorMainStart(); ?>
<?php calculatorCardStart(); ?>
<h2>1. Θέση & βασικές προϋποθέσεις</h2>
<p class="subtitle">Επίλεξε τη θέση που σε ενδιαφέρει. Ο κλάδος προκύπτει αυτόματα· μόνο όταν μία θέση δέχεται περισσότερους κλάδους θα σου ζητηθεί να επιλέξεις μεταξύ αυτών.</p>
<div class="field-grid">
<div class="field full"><label for="position">Θέση της πρόσκλησης 2026<small>Η θέση καθορίζει αυτόματα τον κλάδο. Αν προβλέπονται περισσότεροι κλάδοι, εμφανίζεται μόνο τότε μια σύντομη επιλογή μεταξύ αυτών.</small></label><select id="position"><option value="">— Επίλεξε θέση —</option><option value="pe70_lux2">ΠΕ70 — Ε.Σ. Λουξεμβούργο II</option><option value="pe70_bru3">ΠΕ70 — Ε.Σ. Βρυξέλλες III</option><option value="pe06_bru1">ΠΕ06 — Ε.Σ. Βρυξέλλες I</option><option value="pe06_mol">ΠΕ06 — Ε.Σ. MOL</option><option value="pe02_bru3">ΠΕ02 — Ε.Σ. Βρυξέλλες III</option><option value="history_bru1">Ιστορία (ΠΕ02 ή ΠΕ33) — Ε.Σ. Βρυξέλλες I</option><option value="pe03_bru3">ΠΕ03 — Ε.Σ. Βρυξέλλες III</option><option value="chemistry_bru3">Χημεία (ΠΕ04.02 ή ΠΕ85) — Ε.Σ. Βρυξέλλες III</option><option value="biology_bru3">ΠΕ04.04 — Ε.Σ. Βρυξέλλες III</option><option value="pe08_mol">ΠΕ08 — Ε.Σ. MOL</option><option value="pe11_bru3">ΠΕ11 — Ε.Σ. Βρυξέλλες III</option><option value="librarian_lux2">EL & EN Librarian — Ε.Σ. Λουξεμβούργο II</option></select><div id="positionBranchInfo" class="info hidden edu-mt-10"></div><div id="branchChoiceWrap" class="hidden edu-mt-12"><label for="selectedBranch">Ο κλάδος μου<small>Η θέση δέχεται περισσότερους από έναν κλάδους. Επίλεξε μόνο μεταξύ των κλάδων που προβλέπει η πρόσκληση.</small></label><select id="selectedBranch"><option value="">— Επίλεξε κλάδο —</option></select></div></div>
<div class="field"><label>Συνολική αναγνωρισμένη διδακτική υπηρεσία<small>Όπως στο Φύλλο Μητρώου: έτη, μήνες και ημέρες. Απαιτούνται τουλάχιστον 4 πλήρη έτη· κάθε πλήρες επιπλέον έτος δίνει 1 μονάδα, έως 9.</small></label><div class="duration-grid"><div><span class="mini-label">Έτη</span><input type="number" id="teachingYears" aria-label="Έτη συνολικής αναγνωρισμένης διδακτικής υπηρεσίας" min="0" max="50" step="1" inputmode="numeric" value="" placeholder="π.χ. 12"></div><div><span class="mini-label">Μήνες</span><input type="number" id="teachingMonths" aria-label="Μήνες συνολικής αναγνωρισμένης διδακτικής υπηρεσίας" min="0" max="11" step="1" inputmode="numeric" value="" placeholder="0–11"></div><div><span class="mini-label">Ημέρες</span><input type="number" id="teachingDays" aria-label="Ημέρες συνολικής αναγνωρισμένης διδακτικής υπηρεσίας" min="0" max="31" step="1" inputmode="numeric" value="" placeholder="0–31"></div></div><div id="teachingDurationHint" class="subtitle edu-m-8-0-0"></div></div>
<div class="field"><label for="ictLevel">Πιστοποιημένη γνώση ΤΠΕ<small>Απαιτείται τουλάχιστον Α΄ επίπεδο. Το Β΄ επίπεδο δίνει 2 μονάδες.</small></label><select id="ictLevel"><option value="">— Επίλεξε —</option><option value="none">Δεν διαθέτω Α΄ επίπεδο</option><option value="a">Α΄ επίπεδο — προϋπόθεση, 0 μόρια</option><option value="b">Β΄ επίπεδο — 2 μόρια</option></select></div>
<div class="field"><label for="blockingIssue">Κώλυμα συμμετοχής / επιλογής</label><select id="blockingIssue"><option value="">— Επίλεξε —</option><option value="no">Όχι</option><option value="yes">Ναι / πιθανόν</option></select></div>
</div>
<div id="positionRequirementWrap" class="field hidden edu-mt-13"><label for="positionLanguageRequirementMet" id="positionRequirementLabel">Γλωσσική προϋπόθεση θέσης</label><select id="positionLanguageRequirementMet"><option value="">— Επίλεξε —</option><option value="yes">Ναι, την καλύπτω</option><option value="no">Όχι</option></select></div>
<div id="librarianWrap" class="field hidden edu-mt-13"><label for="librarianQualification">Διαθέτω το προβλεπόμενο προσόν Βιβλιοθηκονομίας;</label><select id="librarianQualification"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
<?php calculatorDisclosureStart(array('summary' => 'Ειδικές γλωσσικές απαιτήσεις θέσεων 2026', 'open' => true, 'attrs' => array('data-mobile-collapsed' => 'true'))); ?><ul class="criteria"><li>ΠΕ70 Λουξεμβούργο II / Βρυξέλλες III, ΠΕ02 Βρυξέλλες III, ΠΕ03, Χημεία, Βιολογία και ΠΕ11 Βρυξέλλες III: Γ2 σε Αγγλικά, Γαλλικά ή Γερμανικά.</li><li>Ιστορία Βρυξέλλες I και ΠΕ08 MOL: Γ2 Αγγλικά.</li><li>Librarian Λουξεμβούργο II: προσόν Βιβλιοθηκονομίας και Γ2 Αγγλικά.</li></ul><?php calculatorDisclosureEnd(); ?>
<?php calculatorCardEnd(); ?>
<?php calculatorCardStart(); ?>
<h2>2. Α. Επιστημονική & παιδαγωγική κατάρτιση</h2>
<div class="check"><input type="checkbox" id="phd"><label for="phd">Διδακτορικό δίπλωμα <small>5 μονάδες</small></label></div><div class="check"><input type="checkbox" id="master"><label for="master">Μεταπτυχιακό δίπλωμα ειδίκευσης <small>3 μονάδες</small></label></div><div class="check"><input type="checkbox" id="secondPhd"><label for="secondPhd">Δεύτερο διδακτορικό <small>3 μονάδες</small></label></div><div class="check"><input type="checkbox" id="secondMaster"><label for="secondMaster">Δεύτερο μεταπτυχιακό <small>1 μονάδα</small></label></div><div class="check"><input type="checkbox" id="secondDegree"><label for="secondDegree">Δεύτερο πανεπιστημιακό πτυχίο <small>2 μονάδες</small></label></div><div class="check"><input type="checkbox" id="retrainingDegree"><label for="retrainingDegree">Τίτλος Διδασκαλείου μετεκπαίδευσης <small>2 μονάδες</small></label></div>
<h3>Γλωσσομάθεια</h3>
<div class="field-grid">
<div class="field"><label for="hostLanguageKey">Επίσημη γλώσσα χώρας έδρας<small id="hostLanguageHint">Επίλεξε πρώτα θέση για να εμφανιστούν μόνο οι επίσημες γλώσσες της χώρας.</small></label><select id="hostLanguageKey"><option value="">— Επίλεξε θέση πρώτα —</option></select></div>
<div class="field"><label for="hostLanguageLevel">Επίπεδο επίσημης γλώσσας<small>Γ2: 5 · Γ1: 4 · Β2: 2,5</small></label><select id="hostLanguageLevel"><option value="none">Δεν δηλώνω</option><option value="b2">Β2 — 2,5</option><option value="c1">Γ1 — 4</option><option value="c2">Γ2 — 5</option></select></div>
<div class="field"><label for="secondWorkingLanguage">Δεύτερη γλώσσα εργασίας<small>Μόνο Αγγλικά / Γαλλικά / Γερμανικά και όχι γλώσσα που έχει ήδη δηλωθεί.</small></label><select id="secondWorkingLanguage"><option value="">Δεν δηλώνω</option></select></div>
<div class="field"><label for="secondWorkingLevel">Επίπεδο δεύτερης γλώσσας<small>Γ2: 4 · Γ1: 3 · Β2: 1,5</small></label><select id="secondWorkingLevel"><option value="none">Δεν δηλώνω</option><option value="b2">Β2 — 1,5</option><option value="c1">Γ1 — 3</option><option value="c2">Γ2 — 4</option></select></div>
<div class="field"><label for="thirdWorkingLanguage">Τρίτη γλώσσα εργασίας<small>Μόνο Αγγλικά / Γαλλικά / Γερμανικά και διαφορετική από τις προηγούμενες.</small></label><select id="thirdWorkingLanguage"><option value="">Δεν δηλώνω</option></select></div>
<div class="field"><label for="thirdWorkingLevel">Επίπεδο τρίτης γλώσσας<small>Γ2: 3 · Γ1: 2 · Β2: 1</small></label><select id="thirdWorkingLevel"><option value="none">Δεν δηλώνω</option><option value="b2">Β2 — 1</option><option value="c1">Γ1 — 2</option><option value="c2">Γ2 — 3</option></select></div>
<div class="field"><label for="otherEULanguage">Άλλη γλώσσα της Ε.Ε.<small>Επιλέγονται μόνο λοιπές επίσημες γλώσσες της Ε.Ε. και αποκλείεται αυτόματα όποια έχει δηλωθεί ήδη.</small></label><select id="otherEULanguage"><option value="">Δεν δηλώνω</option></select></div><div class="field"><label for="otherEULevel">Επίπεδο άλλης γλώσσας Ε.Ε.<small>Γ2: 2 · Γ1: 1 · Β2: 0,5</small></label><select id="otherEULevel"><option value="none">Δεν δηλώνω</option><option value="b2">Β2 — 0,5</option><option value="c1">Γ1 — 1</option><option value="c2">Γ2 — 2</option></select></div>
</div><div class="note"><strong>Αυτόματος έλεγχος γλωσσών:</strong> η χώρα της θέσης περιορίζει τις διαθέσιμες επίσημες γλώσσες και η ίδια γλώσσα δεν μπορεί να μοριοδοτηθεί δεύτερη φορά σε άλλη κατηγορία.</div><?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Α', 'value_id' => 'academicSubtotal', 'value_html' => '0 / 32')); ?>
<?php calculatorCardEnd(); ?>
<?php calculatorCardStart(); ?>
<h2>3. Β. Υπηρεσιακή κατάσταση & διδακτική εμπειρία</h2>
<div class="field-grid"><div class="field"><label for="higherEducationSemesters">Ακαδημαϊκά εξάμηνα αυτοδύναμου διδακτικού έργου σε ΑΕΙ<small>0,50 ανά εξάμηνο · έως 2.</small></label><input type="number" id="higherEducationSemesters" min="0" step="1" value="0"></div><div class="field"><label for="innovativePrograms">Καινοτόμα ευρωπαϊκά / διεθνή προγράμματα<small>1 ανά πρόγραμμα · έως 3.</small></label><input type="number" id="innovativePrograms" min="0" step="1" value="0"></div></div>
<h3>Επιμόρφωση — έως 4 μονάδες</h3><div class="check"><input type="checkbox" id="annualTraining"><label for="annualTraining">Ετήσια επιμόρφωση ΣΕΛΜΕ/ΣΕΛΔΕ/ΑΣΠΑΙΤΕ/ΣΕΛΕΤΕ, εφόσον δεν αποτέλεσε προσόν διορισμού <small>1 μονάδα</small></label></div>
<div class="field-grid edu-mt-10"><div class="field"><label for="universityTrainingCount">Προγράμματα ΑΕΙ ≥300 ωρών ή ≥9 μηνών<small>1 ανά πρόγραμμα · έως 2.</small></label><input type="number" id="universityTrainingCount" min="0" step="1" value="0"></div><div class="field"><label for="ministryTrainingHours">Ώρες ΠΕΚΕΣ/ΠΕΚ/ΙΕΠ/ΠΙ/ΟΕΠΕΚ/ΥΠΑΙΘ<small>0,10 ανά 10 ώρες · έως 1.</small></label><input type="number" id="ministryTrainingHours" min="0" step="1" value="0"></div><div class="field"><label for="publicAdminTrainingHours">Ώρες ΕΚΔΔΑ / ΙΠΕΜ-ΔΟΕ / ΚΕΜΕΤΕ-ΟΛΜΕ<small>0,10 ανά 10 ώρες · έως 1.</small></label><input type="number" id="publicAdminTrainingHours" min="0" step="1" value="0"></div><div class="field"><label for="eapAnnualUnits">Ετήσιες θεματικές ενότητες ΕΑΠ</label><input type="number" id="eapAnnualUnits" min="0" step="1" value="0"></div><div class="field"><label for="eapSemesterUnits">Εξαμηνιαίες θεματικές ενότητες ΕΑΠ</label><input type="number" id="eapSemesterUnits" min="0" step="1" value="0"></div></div><div class="check"><input type="checkbox" id="majorTraining"><label for="majorTraining">Μείζον Πρόγραμμα Επιμόρφωσης ή κατάλογος επιμορφωτών Α΄/Β΄ επιπέδου <small>1 μονάδα</small></label></div><?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Β', 'value_id' => 'serviceSubtotal', 'value_html' => '0 / 18')); ?>
<?php calculatorCardEnd(); ?>
<?php calculatorCardStart(); ?>
<h2>4. Δυναμικό checklist δικαιολογητικών</h2><p class="subtitle">Η λίστα προσαρμόζεται στα προσόντα και στις επιλογές που δηλώνεις παραπάνω. Δεν υποκαθιστά τον τελικό έλεγχο της επίσημης πρόσκλησης.</p><ul id="documentsChecklist" class="document-checklist"></ul><div class="note">Η πρόσκληση απαιτεί τα δηλούμενα δικαιολογητικά να έχουν αναρτηθεί πριν από την οριστική καταχώριση της αίτησης.</div>
<?php calculatorCardEnd(); ?>
<?php calculatorCardStart(); ?>
<h2>5. Γ. Προφορική εξέταση & συνέντευξη</h2><p class="subtitle">Συμπλήρωσέ τα μόνο όταν γνωρίζεις τις βαθμολογίες. Η βάση στην προαπαιτούμενη ξένη γλώσσα είναι 5/10.</p>
<div class="field-grid"><div class="field"><label for="oralPrerequisiteLanguage">Προαπαιτούμενη ξένη γλώσσα — προφορικός λόγος<small>0–10 · βάση 5.</small></label><input type="number" id="oralPrerequisiteLanguage" min="0" max="10" step="0.1" value="" placeholder="0–10"></div><div class="field"><label for="oralWorkingLanguage1">Άλλη γλώσσα εργασίας 1<small>0–5.</small></label><input type="number" id="oralWorkingLanguage1" min="0" max="5" step="0.1" value="" placeholder="0–5"></div><div class="field"><label for="oralWorkingLanguage2">Άλλη γλώσσα εργασίας 2<small>0–5.</small></label><input type="number" id="oralWorkingLanguage2" min="0" max="5" step="0.1" value="" placeholder="0–5"></div><div class="field"><label for="thoughtSpeech">Συγκρότηση σκέψης & λόγου<small>0–5.</small></label><input type="number" id="thoughtSpeech" min="0" max="5" step="0.1" value="" placeholder="0–5"></div><div class="field"><label for="interculturalInnovation">Έκφραση, διαπολιτισμική επικοινωνία & καινοτόμες δράσεις<small>0–5.</small></label><input type="number" id="interculturalInnovation" min="0" max="5" step="0.1" value="" placeholder="0–5"></div><div class="field"><label for="curriculumKnowledge">Ενημέρωση & γνώση προγράμματος σπουδών Ευρωπαϊκών Σχολείων<small>0–10.</small></label><input type="number" id="curriculumKnowledge" min="0" max="10" step="0.1" value="" placeholder="0–10"></div></div>
<div class="note">Στη συνέντευξη καλείται το 50% των υποψηφίων με τα υψηλότερα μόρια του 1ου σταδίου, με στρογγυλοποίηση στην πλησιέστερη ακέραιη μονάδα και αριθμό τουλάχιστον τριπλάσιο των θέσεων. Δεν υπάρχει σταθερό όριο μορίων.</div><?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Γ', 'value_id' => 'interviewSubtotal', 'value_html' => '— / 40')); ?>
<?php calculatorCardEnd(); ?>
<?php calculatorMainEnd(); ?>
<?php calculatorResultsStart(array('class' => 'card results', 'aria_live' => 'polite')); ?>
<?php calculatorScoreHeader(array(
  'variant' => 'staged',
  'class' => 'stage',
  'context_html' => '1ο στάδιο · πριν τη συνέντευξη',
  'context_attrs' => array('class' => 'stage-label'),
  'value_html' => '<span id="preInterviewTotal">0</span> <small class="edu-stage-suffix">/ 50</small>',
  'value_class' => 'stage-number'
)); ?>
<?php calculatorResultRow(array('label_html' => 'Α. Κατάρτιση', 'value_html' => '0 / 32', 'value_id' => 'academicResult')); ?><?php calculatorResultRow(array('label_html' => 'Β. Υπηρεσία / εμπειρία', 'value_html' => '0 / 18', 'value_id' => 'serviceResult')); ?><?php calculatorResultRow(array('label_html' => 'Γ. Συνέντευξη', 'value_html' => '— / 40', 'value_id' => 'interviewResult')); ?><?php calculatorDisclosureStart(array('summary' => 'Αναλυτική κατανομή μορίων', 'class' => 'breakdown-box edu-result-disclosure')); ?><div id="academicBreakdown" class="breakdown-list"></div><div id="serviceBreakdown" class="breakdown-list"></div><?php calculatorDisclosureEnd(); ?>
<?php calculatorScoreHeader(array(
  'variant' => 'final',
  'class' => 'stage',
  'context_html' => 'Τελική βαθμολογία',
  'context_attrs' => array('class' => 'stage-label'),
  'value_id' => 'finalTotal',
  'value_html' => '—',
  'value_class' => 'stage-number final',
  'cap_id' => 'finalHelp',
  'cap_html' => 'Συμπλήρωσε όλα τα πεδία της συνέντευξης για τελικό /90.',
  'cap_class' => 'edu-small-muted'
)); ?>
<div id="eligibilityStatus" role="status" aria-live="polite"></div><?php calculatorActions(array(array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Καθαρισμός'))); ?><?php calculatorResultsEnd(); ?>
<?php calculatorColumnsEnd(); ?>
<?php sourceCardStart(); ?><p><strong>Πηγή:</strong> Πρόσκληση 33598/Η2/18-03-2026 για απόσπαση εκπαιδευτικών στα Ευρωπαϊκά Σχολεία και η αναφερόμενη Υ.Α. 26754/Η2/10-03-2022 (Β΄1165, διόρθωση Β΄1300).</p><?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι ενημερωτικό.<?php sourceCardDisclaimerEnd(); ?><?php sourceCardEnd(); ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/european-schools-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/european-schools-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
