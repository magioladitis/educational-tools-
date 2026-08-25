<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: page-specific 3EA layout + shared components -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων 3ΕΑ/2025 και ενδεικτικός έλεγχος ένταξης στον Αξιολογικό Πίνακα Β΄ ή στον Επικουρικό Πίνακα Ειδικής Αγωγής.">
<title>Υπολογισμός μορίων 3ΕΑ/2025</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-ea3 edu-page-ea3">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>
<?php require_once __DIR__ . '/includes/components/training-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-language-selector.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-computer-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/eae-sensory-priority.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-digital-tutoring-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pedagogical-proof.php'; ?>
<div class="page">
<?php calculatorHero(array(
    'title' => 'Υπολογισμός μορίων 3ΕΑ/2025',
    'intro_html' => 'Ενδεικτικός υπολογισμός μορίων για εκπαιδευτικούς ΠΕ Ειδικής Αγωγής και ταυτόχρονος έλεγχος ένταξης στον <strong>ΚΥΡΙΟ – Αξιολογικό Πίνακα Β΄</strong> ή στον <strong>ΕΠΙΚΟΥΡΙΚΟ Πίνακα</strong>.',
    'meta_class' => 'hero-tags',
    'badges' => array('Ακαδημαϊκά έως 120', 'Προϋπηρεσία έως 120', 'Κύριος / Επικουρικός', '3ΕΑ/2025')
)); ?>

<?php
renderDeadlineCard(array(
    'title' => '📅 Δήλωση περιοχών αναπληρωτών 2026–2027',
    'intro' => 'Η φετινή πρόσκληση του ΥΠΑΙΘΑ για πρόσληψη αναπληρωτών/ωρομισθίων είναι σε εξέλιξη.',
    'items' => array(array(
        'title' => '3ΕΑ/2025 — Πίνακες Ειδικής Αγωγής',
        'meta_html' => 'Δήλωση περιοχών στο <strong>ΟΠΣΥΔ</strong> από <strong>Παρασκευή 14</strong> έως και <strong>Δευτέρα 24 Αυγούστου 2026</strong>.',
        'start' => '2026-08-14T00:00:00+03:00',
        'end_exclusive' => '2026-08-25T00:00:00+03:00',
        'source_url' => 'https://diavgeia.gov.gr/doc/9%CE%96%CE%A5%CE%A146%CE%9D%CE%9A%CE%A0%CE%94-%CE%93%CE%A8%CE%A9?inline=true',
        'source_label' => 'Επίσημη πρόσκληση — ΑΔΑ 9ΖΥΡ46ΝΚΠΔ-ΓΨΩ ↗'
    )),
    'note_html' => '<strong>Σημείωση ώρας:</strong> η επίσημη πρόσκληση αναφέρει την καταληκτική ημερομηνία 24/08/2026 χωρίς συγκεκριμένη ώρα. Το countdown χρησιμοποιεί τεχνικά το τέλος της ημέρας σε ώρα Ελλάδας· υπερισχύει πάντοτε η επίσημη πρόσκληση και το ΟΠΣΥΔ.'
));
?>

<?php calculatorColumnsStart(array('class' => 'grid')); ?>
<?php calculatorMainStart(array('tag' => 'main')); ?>
<?php calculatorCardStart(); ?>
<h2>1. Κλάδος και ένταξη σε πίνακα</h2>
<p class="cap">Ο κλάδος επηρεάζει ειδικούς κανόνες (ΠΕ61/ΠΕ71, ΠΕ11, ΠΕ86 και κλάδοι ξένων γλωσσών).</p>
<div class="field"><label for="specialty">Κλάδος / ειδικότητα</label><select id="specialty"><option value="">-- Επιλογή --</option>
<option>ΠΕ01</option><option>ΠΕ02</option><option>ΠΕ03</option><option>ΠΕ04.01</option><option>ΠΕ04.02</option><option>ΠΕ04.03</option><option>ΠΕ04.04</option><option>ΠΕ04.05</option><option>ΠΕ05</option><option>ΠΕ06</option><option>ΠΕ07</option><option>ΠΕ08</option><option>ΠΕ11</option><option>ΠΕ33</option><option>ΠΕ34</option><option>ΠΕ40</option><option>ΠΕ41</option><option>ΠΕ60</option><option>ΠΕ61</option><option>ΠΕ70</option><option>ΠΕ71</option><option>ΠΕ78</option><option>ΠΕ79.01</option><option>ΠΕ80</option><option>ΠΕ81</option><option>ΠΕ82</option><option>ΠΕ83</option><option>ΠΕ84</option><option>ΠΕ85</option><option>ΠΕ86</option><option>ΠΕ87</option><option>ΠΕ88</option><option>ΠΕ89</option><option>ΠΕ90</option><option>ΠΕ91</option>
</select></div>
<div class="info">Για ΠΕ61 και ΠΕ71 ο βασικός κλάδος είναι κλάδος Ε.Α.Ε. και οδηγεί στον Αξιολογικό Πίνακα Β΄. Για τους λοιπούς κλάδους απαιτείται προσόν εξειδίκευσης στην Ε.Α.Ε.</div>

<div id="eaeEligibility" data-eae-profile="pe" data-specialty-id="specialty" data-social-id="socialCriteria">
<h3>Προσόντα για ΚΥΡΙΟ – Αξιολογικό Πίνακα Β΄</h3>
<div class="check"><input type="checkbox" id="phdEae" data-eae-main="phd"><label for="phdEae">Διδακτορικό στην Ε.Α.Ε. ή στη Σχολική Ψυχολογία<small>Αποτελεί κριτήριο ένταξης στον κύριο πίνακα και μοριοδοτείται ως διδακτορικό.</small></label></div>
<div class="check"><input type="checkbox" id="masterEae" data-eae-main="msc"><label for="masterEae">Μεταπτυχιακό στην Ε.Α.Ε. ή στη Σχολική Ψυχολογία<small>Αποτελεί κριτήριο ένταξης στον κύριο πίνακα και μοριοδοτείται ως μεταπτυχιακό.</small></label></div>
<div class="check"><input type="checkbox" id="didaskaleio" data-eae-main="retraining"><label for="didaskaleio">Πτυχίο διετούς μετεκπαίδευσης στην Ε.Α.Ε. (Διδασκαλείο)<small>Κριτήριο ένταξης. Η προκήρυξη δεν ορίζει χωριστή πρόσθετη μοριοδότηση γι’ αυτό το πτυχίο στον πίνακα κριτηρίων.</small></label></div>
<div class="check"><input type="checkbox" id="fiveYearEae" data-eae-main="fiveYear"><label for="fiveYearEae">Τουλάχιστον πενταετής αποδεδειγμένη προϋπηρεσία στην Ε.Α.Ε.<small>Χρησιμοποιείται για τον έλεγχο ένταξης. Τους μήνες προϋπηρεσίας τους δηλώνεις χωριστά παρακάτω για τη μοριοδότηση.</small></label></div>
<div class="check hidden" id="pe11QualWrap"><input type="checkbox" id="pe11Qual" data-eae-main="pe11"><label for="pe11Qual">ΠΕ11 με προβλεπόμενη κύρια ειδικότητα Ε.Α.Ε. / Ειδικής Φυσικής Αγωγής κ.λπ.<small>Κριτήριο ένταξης και +8 μόρια.</small></label></div>

<h3>Προσόντα για ΕΠΙΚΟΥΡΙΚΟ πίνακα</h3>
<div class="check"><input type="checkbox" id="seminar400" data-eae-aux="seminar400"><label for="seminar400">Σεμινάριο εξειδίκευσης Ε.Α.Ε. ≥400 ωρών και ≥7 μηνών<small>Κριτήριο ένταξης στον επικουρικό. Καλύπτει παράλληλα και το γενικό κριτήριο επιμόρφωσης ≥300 ωρών / ≥7 μηνών (+2).</small></label></div>
<div class="field"><label for="eaeMonths">Αναγνωρισμένοι μήνες προϋπηρεσίας ειδικά στην Ε.Α.Ε.<small>Μόνο για έλεγχο του ορίου των 10 μηνών του επικουρικού· δεν προστίθενται δεύτερη φορά στα μόρια.</small></label><input id="eaeMonths" class="service-months" data-eae-aux="months" type="number" min="0" max="480" step="1" inputmode="numeric" value="0"></div>
</div>
<?php calculatorCardEnd(); ?>

<?php calculatorCardStart(array('id' => 'asepPeAcademic', 'attrs' => array('data-component' => 'asep-pe-academic', 'data-profile' => 'eae', 'data-degree-required' => 'false', 'data-specialty-id' => 'specialty', 'data-degree-id' => 'degreeGrade', 'data-second-degree-id' => 'secondDegree', 'data-phd-id' => 'phd', 'data-msc-id' => 'mscCount', 'data-language-id' => 'asepLanguages', 'data-computer-id' => 'computer', 'data-training-id' => 'training', 'data-training-proof-id' => 'trainingProof', 'data-phd-overlay-id' => 'phdEae', 'data-msc-overlay-id' => 'masterEae', 'data-training-overlay-id' => 'seminar400', 'data-eae-pe11-special-id' => 'pe11Qual', 'data-pe11-wrap-id' => 'pe11QualWrap', 'data-pe6171-note-id' => 'pe6171Auto'))); ?>
<h2>2. Ακαδημαϊκά προσόντα</h2><p class="cap">Μέγιστο κατηγορίας Α: 120 μόρια.</p><div id="degreeValidation" class="note hidden">Ο βαθμός βασικού πτυχίου πρέπει να είναι από 5,00 έως 10,00.</div>
<div class="field"><label for="degreeGrade">Βαθμός βασικού πτυχίου (5–10)<small>Βαθμός × 2,5 · ανώτατο 25.</small></label><input id="degreeGrade" type="number" min="5" max="10" step="0.01" value="" placeholder="π.χ. 7,50"></div>
<div class="check"><input type="checkbox" id="secondDegree"><label for="secondDegree">Δεύτερο πτυχίο ΑΕΙ <small>+7 μόρια, εφόσον δεν αποτελεί τυπικό προσόν διορισμού.</small></label></div>
<div class="check"><input type="checkbox" id="phd"><label for="phd">Διδακτορικό δίπλωμα <small>+40 μόρια. Αν τσεκάρεις «Διδακτορικό ΕΑΕ» παραπάνω, ενεργοποιείται αυτόματα.</small></label></div>
<div class="field"><label for="mscCount">Μεταπτυχιακοί τίτλοι / integrated master<small>1 τίτλος: 20 · 2 ή περισσότεροι: 28 συνολικά. Για ΠΕ61/ΠΕ71 το βασικό πτυχίο δίνει αυτοδικαίως 20 και με επιπλέον μεταπτυχιακό η σχετική μοριοδότηση γίνεται 28.</small></label><select id="mscCount"><option value="0">Κανένας</option><option value="1">Ένας</option><option value="2">Δύο ή περισσότεροι</option></select></div>
<div id="pe6171Auto" class="info hidden">ΠΕ61/ΠΕ71: προστίθενται αυτοδικαίως 20 μόρια λόγω βασικού πτυχίου Ειδικής Αγωγής· με έναν ή περισσότερους επιπλέον μεταπτυχιακούς, η συγκεκριμένη μοριοδότηση γίνεται 28.</div>

<?php
renderAsepLanguageSelector(array(
    'id' => 'asepLanguages',
    'profile' => 'pe',
    'specialty_id' => 'specialty'
));
?>
<?php
renderAsepComputerProof(array(
    'input_id' => 'computer',
    'control_type' => 'checkbox',
    'points_text' => '4 μόρια',
    'restriction_note' => 'Δεν μοριοδοτείται στον ΠΕ86.'
));
?>
<div class="check"><input type="checkbox" id="training"><label for="training">Επιμόρφωση ≥300 ωρών και ≥7 μηνών <small>+2 μόρια. Μοριοδοτείται μόνο μία επιμόρφωση. Το 400ωρο ΕΑΕ του επικουρικού καλύπτει αυτό το κριτήριο.</small></label></div>
<?php
renderTrainingProof([
    'id' => 'trainingProof',
    'radio_name' => 'trainingProofDates',
    'yes_id' => 'trainingProofDatesYes',
    'no_id' => 'trainingProofDatesNo',
    'status_id' => 'trainingProofDatesStatus',
    'context' => '3ea-2025-general-300h-or-eae-400h-7m',
    'input_ids' => array('training', 'seminar400'),
    'legal_html' => <<<'HTML'
Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Σε κάθε περίπτωση πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών (6 μήνες και 29 ημέρες δεν γίνεται δεκτό).</strong>
HTML
]);
?>
<?php calculatorCardEnd(); ?>

<?php calculatorCardStart(array('id' => 'asepService', 'attrs' => array('data-component' => 'asep-service-criteria'))); ?>
<h2>3. Εκπαιδευτική προϋπηρεσία</h2><p class="cap">Μέγιστο κατηγορίας Β: 120 μόρια. Δήλωσε τους μήνες χωρίς επικάλυψη μεταξύ των ειδικών κατηγοριών.</p>
<div class="note"><strong>Σημείωση 3ΕΑ/2025:</strong> Στις γενικές κατηγορίες λαμβάνεται υπόψη η προϋπηρεσία σε ακέραιους μήνες.</div>
<div class="note">Οι μήνες δυσπρόσιτων, τρίμηνων συμβάσεων και Ψηφιακού Φροντιστηρίου πρέπει να δηλώνονται στις αντίστοιχες ειδικές γραμμές και όχι ξανά ως κανονική δημόσια προϋπηρεσία.</div>
<div class="field"><label for="regularMonths">Κανονική δημόσια προϋπηρεσία<small>1 μόριο ανά μήνα · έως 120 μήνες.</small></label><input id="regularMonths" class="service-months" data-service-role="regular" type="number" min="0" max="120" step="1" inputmode="numeric" value="0"></div>
<div class="field"><label for="difficultMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–21<small>2 μόρια ανά μήνα · έως 60 μήνες.</small></label><input id="difficultMonths" class="service-months" data-service-role="difficult" type="number" min="0" max="60" step="1" inputmode="numeric" value="0"></div>

<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'threeMonthRegular2020',
    'difficult_2020_id' => 'threeMonthDifficult2020',
    'regular_2021_id' => 'threeMonthRegular2021',
    'difficult_2021_id' => 'threeMonthDifficult2021'
));
?>

<div class="field"><label for="privateMonths">Ιδιωτική εκπαιδευτική προϋπηρεσία<small>0,9 μόρια ανά μήνα, εφόσον πληρούνται οι νόμιμες προϋποθέσεις.</small></label><input id="privateMonths" class="service-months" data-service-role="private" type="number" min="0" max="480" step="1" inputmode="numeric" value="0"></div>
<?php renderAsepDigitalTutoringService(array('container_id' => 'digitalTutoring', 'input_class' => 'service-months')); ?>
<?php calculatorCardEnd(); ?>

<?php
renderAsepSocialCriteria(array(
    'title' => '4. Κοινωνικά κριτήρια',
    'children_id' => 'children',
    'candidate_id' => 'candidateDisability',
    'spouse_id' => 'spouseDisability',
    'child_id' => 'childDisability',
    'marriage_id' => 'marriageYears4Plus',
    'mental_id' => 'candidateMentalCondition',
    'input_step' => '1',
    'child_points' => 3,
    'min_disability_percent' => 50,
    'disability_rate' => '0,4',
    'spouse_min_marriage_years' => 4,
    'child_extra_note' => '',
    'child_auxiliary_note' => 'Από 67% και άνω μπορεί να θεμελιώνει και ένταξη στον Επικουρικό Πίνακα.',
    'warning_id' => 'socialWarnings',
    'warning_mode' => 'bullets',
    'subtotal_id' => '',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>

<?php calculatorCardStart(); ?>
<h2>5. Προτάξεις / ειδικές προτεραιότητες</h2>
<?php renderAsepPedagogicalProof(array(
    'context' => '3ea-2025',
    'input_id' => 'pedagogical'
)); ?>
<?php
renderEaeSensoryPriority(array(
    'context' => '3ea-2025',
    'eng_enabled' => true,
    'braille_enabled' => true
));
?>
<?php calculatorCardEnd(); ?>
<?php calculatorMainEnd(); ?>

<?php calculatorResultsStart(array('variant' => 'result-card', 'class' => 'card result-card', 'aria_live' => 'polite')); ?>
<?php calculatorScoreHeader(array('value_id' => 'grandTotal', 'value_html' => '0,00', 'label' => 'συνολικά μόρια')); ?>
<?php calculatorResultMessage(array('id' => 'tableStatus', 'variant' => 'status', 'text' => 'Επίλεξε κλάδο')); ?>
<?php calculatorResultMessage(array('id' => 'eligibilityWhy', 'variant' => 'disclaimer', 'html' => '<strong>Έλεγχος ένταξης</strong>Συμπλήρωσε τα προσόντα σου.')); ?>
<?php calculatorResultRow(array('label_html' => 'Ακαδημαϊκά', 'value_html' => '0,00 / 120', 'value_id' => 'resAcademic')); ?>
<?php calculatorResultRow(array('label_html' => 'Προϋπηρεσία', 'value_html' => '0,00 / 120', 'value_id' => 'resService')); ?>
<?php calculatorResultRow(array('label_html' => 'Κοινωνικά', 'value_html' => '0,00', 'value_id' => 'resSocial')); ?>
<div id="priorities"></div>
<?php calculatorActions(array(array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Μηδενισμός'))); ?>
<?php calculatorResultMessage(array('variant' => 'disclaimer', 'text' => 'Ενημερωτικός υπολογισμός βάσει της 3ΕΑ/2025. Η τελική ένταξη και μοριοδότηση προκύπτει από τον έλεγχο ΑΣΕΠ/ΟΠΣΥΔ και τα επίσημα δικαιολογητικά.')); ?>
<?php calculatorResultsEnd(); ?>
<?php calculatorColumnsEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Προκήρυξη ΑΣΕΠ <strong>3ΕΑ/2025</strong> για εκπαιδευτικούς Ειδικής Αγωγής και Εκπαίδευσης κατηγορίας ΠΕ — <strong>ΦΕΚ 22/23.05.2025/τ. Α.Σ.Ε.Π.</strong> και <strong>ΦΕΚ 25/02.06.2025/τ. Α.Σ.Ε.Π.</strong>, ιδίως τα Κεφάλαια Β΄ και Γ΄.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/76185', 'Επίσημη σελίδα 3ΕΑ/2025 στο ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/sites/default/files/2025-05/3%CE%95%CE%91_2025%20%CE%A4%CF%85%CF%80%CE%B9%CE%BA%CE%AC%20%CE%A0%CF%81%CE%BF%CF%83%CF%8C%CE%BD%CF%84%CE%B1%20%CE%88%CE%BD%CF%84%CE%B1%CE%BE%CE%B7%CF%82.pdf', 'Τυπικά Προσόντα Ένταξης 3ΕΑ/2025 ↗'); ?><?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι ενημερωτικό και δεν υποκαθιστά τον επίσημο έλεγχο της αίτησης, του ΟΠΣΥΔ και των δικαιολογητικών από το ΑΣΕΠ και τα αρμόδια όργανα.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>
</div>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/service-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-service-controller.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-digital-tutoring.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/social-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-social-criteria.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/eae-table-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-eae-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/academic-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pe-academic.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/training-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pedagogical-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/eae-sensory-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function(){
 const $=id=>document.getElementById(id); const num=id=>Math.max(0,Number($(id)?.value||0)); const cap=(v,m)=>Math.min(v,m); const fmt=v=>(Math.round((v+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});
 function calcService(){return AsepServiceController.getState('asepService',fmt).points;}
 function calcSocial(){return AsepSocialCriteria.getState('socialCriteria',fmt);}
 function render(){
   TrainingProof.syncAll();
   const degreeGrade=num('degreeGrade');
   const degreeInvalid=degreeGrade>0 && (degreeGrade<5 || degreeGrade>10);
   $('degreeValidation').classList.toggle('hidden', !degreeInvalid);
   const academic=AsepPeAcademic.calculate('asepPeAcademic'), a=academic.points, b=calcService(), socialResult=calcSocial(), c=socialResult.points, t=a+b+c, e=AsepEaeEligibility.getState('eaeEligibility',{socialResult:socialResult});
   $('grandTotal').textContent=fmt(t); $('resAcademic').textContent=fmt(a)+' / 120'; $('resService').textContent=fmt(b)+' / 120'; $('resSocial').textContent=fmt(c);
   $('tableStatus').className='result-message edu-message '+(e.type==='main'||e.type==='aux'?'result-message--success edu-message--success':e.type==='none'&&e.label!=='Επίλεξε κλάδο'?'result-message--warning edu-message--warning':'result-message--status edu-message--status'); $('tableStatus').textContent=e.label; $('eligibilityWhy').innerHTML='<strong>Έλεγχος ένταξης</strong>'+e.why;
   let p=[]; if($('pedagogical').checked) p.push('Πρόταξη λόγω Παιδαγωγικής & Διδακτικής Επάρκειας'); p=p.concat(EaeSensoryProof.priorityLabels());
   $('priorities').innerHTML=p.map(x=>'<div class="result-message edu-message result-message--success edu-message--success">✓ '+x+'</div>').join('');
   return {a,b,c,t,e,p};
 }
 function summary(v){return ['Υπολογισμός μορίων 3ΕΑ/2025',`Πίνακας: ${v.e.label}`,v.e.why,`Ακαδημαϊκά: ${fmt(v.a)} / 120`,`Προϋπηρεσία: ${fmt(v.b)} / 120`,`Κοινωνικά: ${fmt(v.c)}`,`ΣΥΝΟΛΟ: ${fmt(v.t)}`,AsepDigitalTutoring.summary('digitalTutoring',fmt),v.p.length?'Προτάξεις/προτεραιότητες: '+v.p.join(' · '):'',AsepPedagogicalProof.summary('pedagogical'),EaeSensoryProof.summary(),TrainingProof.summary('trainingProof')].filter(Boolean).join('\n');}
 document.addEventListener('input',render);
 document.addEventListener('change',render);
 $('copyBtn').addEventListener('click',async()=>{const txt=summary(render());try{await navigator.clipboard.writeText(txt);$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent='Αντιγραφή',1200)}catch(e){alert(txt)}});
 document.addEventListener('asep-digital-tutoring-change',render);
 $('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type=number]').forEach(x=>x.value=0);$('degreeGrade').value='';document.querySelectorAll('input[type=text]').forEach(x=>x.value='');document.querySelectorAll('input[type=checkbox]').forEach(x=>x.checked=false);document.querySelectorAll('input[name="trainingProofDates"]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);AsepPeAcademic.reset('asepPeAcademic',{silent:true});AsepServiceController.reset('asepService',{silent:true});AsepPedagogicalProof.reset('pedagogical');EaeSensoryProof.reset();render();});
 render();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-computer-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
