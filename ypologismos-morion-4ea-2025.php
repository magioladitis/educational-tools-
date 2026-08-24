<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 4ΕΑ/2025 για εκπαιδευτικούς Ειδικής Αγωγής κατηγορίας ΤΕ (ΤΕ01, ΤΕ02, ΤΕ16).">
  <title>Υπολογισμός μορίων 4ΕΑ/2025</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-ea4">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-te-academic.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/eae-sensory-priority.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pedagogical-proof.php'; ?>
<div class="app">
<?php calculatorHero(array(
    'title' => 'Υπολογισμός μορίων 4ΕΑ/2025',
    'intro' => 'Ενδεικτικός υπολογισμός μορίων και ελέγχου ένταξης στους πίνακες Ειδικής Αγωγής και Εκπαίδευσης κατηγορίας Τ.Ε.',
    'badges' => array('4ΕΑ/2025', 'ΤΕ01', 'ΤΕ02', 'ΤΕ16', 'Κύριος / Επικουρικός Πίνακας', 'Ακαδημαϊκά έως 120', 'Προϋπηρεσία έως 120')
  )); ?>

  <?php
renderDeadlineCard(array(
    'title' => '📅 Δήλωση περιοχών αναπληρωτών 2026–2027',
    'intro' => 'Η φετινή πρόσκληση του ΥΠΑΙΘΑ για πρόσληψη αναπληρωτών/ωρομισθίων είναι σε εξέλιξη.',
    'items' => array(array(
        'title' => '4ΕΑ/2025 — ΤΕ01, ΤΕ02, ΤΕ16 Ειδικής Αγωγής',
        'meta_html' => 'Δήλωση περιοχών στο <strong>ΟΠΣΥΔ</strong> από <strong>Παρασκευή 14</strong> έως και <strong>Δευτέρα 24 Αυγούστου 2026</strong>.',
        'start' => '2026-08-14T00:00:00+03:00',
        'end_exclusive' => '2026-08-25T00:00:00+03:00',
        'source_url' => 'https://diavgeia.gov.gr/doc/9%CE%96%CE%A5%CE%A146%CE%9D%CE%9A%CE%A0%CE%94-%CE%93%CE%A8%CE%A9?inline=true',
        'source_label' => 'Επίσημη πρόσκληση — ΑΔΑ 9ΖΥΡ46ΝΚΠΔ-ΓΨΩ ↗'
    )),
    'note_html' => '<strong>Σημείωση ώρας:</strong> η επίσημη πρόσκληση αναφέρει την καταληκτική ημερομηνία 24/08/2026 χωρίς συγκεκριμένη ώρα. Το countdown χρησιμοποιεί τεχνικά το τέλος της ημέρας σε ώρα Ελλάδας· υπερισχύει πάντοτε η επίσημη πρόσκληση και το ΟΠΣΥΔ.'
));
?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(); ?>
        <h2>Κλάδος και βασικός τίτλος</h2>
        <p class="cap">Η 4ΕΑ/2025 αφορά τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16 με εξειδίκευση στην Ειδική Αγωγή και Εκπαίδευση.</p>

        <div class="field-grid">
          <div class="field">
            <label for="specialty">Κλάδος</label>
            <select id="specialty">
              <option value="">— Επιλογή κλάδου / ειδικότητας —</option>
              <optgroup label="ΤΕ01">
                <option value="ΤΕ01.04">ΤΕ01.04 — Ψυκτικοί</option>
                <option value="ΤΕ01.06">ΤΕ01.06 — Ηλεκτρολόγοι</option>
                <option value="ΤΕ01.07">ΤΕ01.07 — Ηλεκτρονικοί</option>
                <option value="ΤΕ01.13">ΤΕ01.13 — Προγραμματιστές Η/Υ</option>
                <option value="ΤΕ01.19">ΤΕ01.19 — Κομμωτικής</option>
                <option value="ΤΕ01.20">ΤΕ01.20 — Αισθητικής</option>
                <option value="ΤΕ01.25">ΤΕ01.25 — Αργυροχρυσοχοΐας</option>
                <option value="ΤΕ01.26">ΤΕ01.26 — Οδοντοτεχνικής</option>
                <option value="ΤΕ01.29">ΤΕ01.29 — Βοηθών Ιατρικών &amp; Βιολογικών Εργαστηρίων</option>
                <option value="ΤΕ01.30">ΤΕ01.30 — Βοηθοί Βρεφοκόμων – Παιδοκόμων</option>
                <option value="ΤΕ01.31">ΤΕ01.31 — Χειριστές Ιατρικών Συσκευών (Βοηθοί Ακτινολόγοι)</option>
              </optgroup>
              <optgroup label="ΤΕ02">
                <option value="ΤΕ02.01">ΤΕ02.01 — Σχεδιαστές – Δομικοί</option>
                <option value="ΤΕ02.02">ΤΕ02.02 — Μηχανολόγοι</option>
                <option value="ΤΕ02.03">ΤΕ02.03 — Χημικοί Εργαστηρίων</option>
                <option value="ΤΕ02.04">ΤΕ02.04 — Οικονομίας – Διοίκησης</option>
                <option value="ΤΕ02.05">ΤΕ02.05 — Εφαρμοσμένων Τεχνών</option>
                <option value="ΤΕ02.06">ΤΕ02.06 — Σχεδιασμού και Παραγωγής Προϊόντων</option>
                <option value="ΤΕ02.07">ΤΕ02.07 — Γεωπονίας</option>
              </optgroup>
              <optgroup label="ΤΕ16">
                <option value="ΤΕ16">ΤΕ16 — Μουσικής μη Ανώτατων Ιδρυμάτων</option>
              </optgroup>
            </select>
          </div>
          <?php renderAsepTeAcademic(array('part' => 'grade-scale')); ?>
        </div>

        <?php renderAsepTeAcademic(array(
            'part' => 'degree-details',
            'id' => 'asepTeAcademic',
            'specialty_id' => 'specialty',
            'extra_training_ids' => array('seminar400'),
            'degree_placeholder_20' => 'π.χ. 15,40'
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>Ένταξη σε πίνακα Ε.Α.Ε.</h2>
        <p class="cap">Ενδεικτικός έλεγχος των ειδικών κριτηρίων ένταξης της 4ΕΑ/2025.</p>

        <div id="eaeEligibility" data-eae-profile="te" data-specialty-id="specialty" data-social-id="socialCriteria">
        <div class="field">
          <label for="mainCriterion">Κριτήριο ένταξης στον Αξιολογικό Πίνακα Β΄ (Κύριος)</label>
          <select id="mainCriterion" data-eae-main-select>
            <option value="none">Δεν διαθέτω κάποιο από τα παρακάτω</option>
            <option value="phd">Διδακτορικό στην Ε.Α.Ε. ή Σχολική Ψυχολογία, με βασικές σπουδές σε Α.Ε.Ι.</option>
            <option value="msc">Μεταπτυχιακό στην Ε.Α.Ε. ή Σχολική Ψυχολογία, με βασικές σπουδές σε Α.Ε.Ι.</option>
            <option value="retraining">Πτυχίο διετούς μετεκπαίδευσης στην Ε.Α.Ε. Διδασκαλείου, με βασικές σπουδές σε Α.Ε.Ι.</option>
            <option value="aei5years">Πτυχίο Α.Ε.Ι. και τουλάχιστον 5 έτη αποδεδειγμένης προϋπηρεσίας στην Ε.Α.Ε.</option>
          </select>
          <div class="help">Αρκεί ένα από τα παραπάνω κριτήρια για ένταξη στον Πίνακα Β΄.</div>
        </div>

        <h3>Κριτήρια Επικουρικού Πίνακα</h3>
        <div class="note">Για τον Επικουρικό Πίνακα αρκεί <strong>ένα από τα τρία</strong> παρακάτω κριτήρια.</div>

        <div class="checkrow">
          <input type="checkbox" id="seminar400" data-eae-aux="seminar400">
          <label for="seminar400">Σεμινάριο εξειδίκευσης στην Ε.Α.Ε. ≥400 ωρών και διάρκειας ≥7 μηνών
            <small>Α.Ε.Ι. ή εποπτευόμενος φορέας του δημόσιου τομέα.</small>
          </label>
        </div>

        <div class="field">
          <label for="eaeMonths">Μήνες προϋπηρεσίας στην Ε.Α.Ε.
            <small>Για το κριτήριο του Επικουρικού απαιτούνται τουλάχιστον 10 μήνες.</small>
          </label>
          <input type="number" id="eaeMonths" data-eae-aux="months" min="0" max="480" step="1" value="0">
        </div>

        <div class="info-note">
          Το κριτήριο <strong>εκπαιδευτικού γονέα παιδιού με αναπηρία 67% και άνω</strong> ελέγχεται αυτόματα από το ποσοστό αναπηρίας τέκνου που δηλώνεται στα Κοινωνικά Κριτήρια παρακάτω.
        </div>
        </div>

        <?php
        renderEaeSensoryPriority(array(
    'context' => '4ea-2025',
            'eng_enabled' => true,
            'braille_enabled' => true
        ));
        ?>

        <div class="priority" id="tableStatus">Δεν έχει δηλωθεί ακόμη κριτήριο ένταξης.</div>
        <div class="info-note" id="eligibilityWhy">Συμπλήρωσε κλάδο και προσόντα για αναλυτικό έλεγχο ένταξης.</div>

        <div class="note">
          Η προϋπηρεσία Ε.Α.Ε. που χρησιμοποιείται για την ένταξη στον Επικουρικό Πίνακα δεν προστίθεται αυτόματα στα μόρια.
          Καταχώρισέ την και στο κατάλληλο πεδίο προϋπηρεσίας παρακάτω, χωρίς διπλή μέτρηση.
        </div>
      <?php calculatorCardEnd(); ?>

      <?php
renderAsepTeAcademic(array(
    'part' => 'qualifications',
    'id' => 'asepTeAcademic',
    'training_context' => '4ea-2025-general-300h-or-eae-400h-7m',
    'extra_training_ids' => array('seminar400'),
    'training_help_suffix' => 'Το σεμινάριο Ε.Α.Ε. ≥400 ωρών του Επικουρικού καλύπτει και αυτό το κριτήριο.'
));
?>

      <section id="asepService" class="card" data-component="asep-service-criteria" data-subtotal-id="serviceSubtotal" data-subtotal-with-cap="true">
        <h2>Β. Εκπαιδευτική προϋπηρεσία</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="note">
          Βάλε τους μήνες σε <strong>ένα μόνο</strong> από τα αντίστοιχα πεδία. Μήνας που δηλώνεται ως δυσπρόσιτος ή ως τρίμηνη σύμβαση δεν πρέπει να ξαναμπεί στους απλούς μήνες, ώστε να μη γίνει διπλή μέτρηση.
        </div>

        <div class="note">
          <strong>Σημείωση 4ΕΑ/2025:</strong> Λαμβάνεται υπόψη η εκπαιδευτική προϋπηρεσία σε μήνες χωρίς να υπολογίζονται τα υπόλοιπα ημερών. Για τον λόγο αυτό, όλα τα πεδία προϋπηρεσίας δέχονται μόνο ακέραιους μήνες.
        </div>

        <div class="field">
          <label for="regularMonths">Μήνες δημόσιας εκπαιδευτικής προϋπηρεσίας
            <small>1 μόριο ανά μήνα πραγματικής εκπαιδευτικής προϋπηρεσίας · έως 120 μήνες.</small>
          </label>
          <input type="number" id="regularMonths" data-service-role="regular" min="0" max="120" step="1" value="0">
        </div>

        <div class="field">
          <label for="difficultMonths">Δυσπρόσιτα / σχολικές μονάδες σε καταστήματα κράτησης
            <small>Από το σχολικό έτος 2020–2021 και μετά · 2 μόρια ανά μήνα · έως 60 μήνες.</small>
          </label>
          <input type="number" id="difficultMonths" data-service-role="difficult" min="0" max="60" step="1" value="0">
        </div>

<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'threeMonthRegular2020',
    'difficult_2020_id' => 'threeMonthDifficult2020',
    'regular_2021_id' => 'threeMonthRegular2021',
    'difficult_2021_id' => 'threeMonthDifficult2021'
));
?>

        <div class="field">
          <label for="privateMonths">Μήνες προϋπηρεσίας στην ιδιωτική εκπαίδευση
            <small>0,9 μόρια ανά μήνα, εφόσον πληρούνται οι ειδικές προϋποθέσεις της προκήρυξης.</small>
          </label>
          <input type="number" id="privateMonths" data-service-role="private" min="0" max="480" step="1" value="0">
        </div>

        <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Προϋπηρεσίας', 'value_id' => 'serviceSubtotal', 'value_html' => '0,00 / 120')); ?>
      </section>

<?php
renderAsepSocialCriteria(array(
    'title' => 'Γ. Κοινωνικά κριτήρια',
    'children_id' => 'children',
    'candidate_id' => 'candidateDisability',
    'spouse_id' => 'spouseDisability',
    'child_id' => 'childDisability',
    'marriage_id' => 'marriageYears4Plus',
    'mental_id' => 'candidateMentalCondition',
    'input_step' => '0.01',
    'child_points' => 3,
    'min_disability_percent' => 50,
    'disability_rate' => '0,4',
    'spouse_min_marriage_years' => 4,
    'child_extra_note' => '',
    'child_auxiliary_note' => 'Από 67% και άνω θεμελιώνει και κριτήριο ένταξης στον Επικουρικό Πίνακα.',
    'warning_id' => 'socialWarning',
    'subtotal_id' => 'socialSubtotal',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>

      <?php calculatorCardStart(); ?>
        <h2>Παιδαγωγική και Διδακτική Επάρκεια</h2>
<?php renderAsepPedagogicalProof(array(
    'context' => '4ea-2025',
    'input_id' => 'pedagogical'
)); ?>
      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(); ?>
      <?php calculatorTotalBlock(array('value_id' => 'grandTotal', 'value_html' => '0,00', 'label' => 'συνολικά μόρια')); ?>

      <?php calculatorResultRow(array('label_html' => 'Ακαδημαϊκά', 'value_html' => '0,00 / 120', 'value_id' => 'resAcademic')); ?>
      <?php calculatorResultRow(array('label_html' => 'Προϋπηρεσία', 'value_html' => '0,00 / 120', 'value_id' => 'resService')); ?>
      <?php calculatorResultRow(array('label_html' => 'Κοινωνικά', 'value_html' => '0,00', 'value_id' => 'resSocial')); ?>
      <?php calculatorResultRow(array('label_html' => 'Βαθμός τίτλου', 'value_html' => '0,00', 'value_id' => 'resDegree')); ?>
      <?php calculatorResultRow(array('label_html' => 'Ξένη γλώσσα', 'value_html' => '0,00', 'value_id' => 'resLanguage')); ?>
      <?php calculatorResultRow(array('label_html' => 'Τέκνα', 'value_html' => '0,00', 'value_id' => 'resChildren')); ?>
      <?php calculatorResultRow(array('label_html' => 'Αναπηρία', 'value_html' => '0,00', 'value_id' => 'resDisability')); ?>

      <?php calculatorResultRow(array('label_html' => 'Πίνακας Ε.Α.Ε.', 'value_html' => '—', 'value_id' => 'resTable')); ?>

      <div class="priority" id="priorityBox">Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.</div>

      <?php calculatorActions(array(array('attrs' => array('type' => 'button', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή αποτελέσματος'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Μηδενισμός'))); ?>

      <div class="info-note edu-mt-14">
        Σε ισοβαθμία προηγούνται κατά σειρά: περισσότερα κοινωνικά μόρια (και ειδικότερα αναπηρία), έπειτα περισσότερα ακαδημαϊκά / μεγαλύτερος βαθμός πτυχίου και τέλος περισσότερη προϋπηρεσία.
      </div>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>

  <?php sourceCardStart(); ?>
    <p>Προκήρυξη ΑΣΕΠ <strong>4ΕΑ/2025</strong>, <strong>ΦΕΚ Α.Σ.Ε.Π. 42/18.08.2025</strong>, ιδίως Κεφάλαια Β΄, Γ΄ και Δ΄.</p>
    <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/77020', 'Επίσημη σελίδα 4ΕΑ/2025 στο ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
    <?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι ενημερωτικό. Η τελική ένταξη σε πίνακα και η μοριοδότηση προκύπτουν από τον έλεγχο της αίτησης, του ΟΠΣΥΔ και των δικαιολογητικών από τα αρμόδια όργανα.<?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardEnd(); ?>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/service-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-service-controller.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/social-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-social-criteria.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/eae-table-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-eae-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/te-academic-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/training-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-computer-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-te-academic.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pedagogical-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/eae-sensory-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function(){
  "use strict";
  const $ = id => document.getElementById(id);
  const fmt = v => (Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});

  function branchFamily(){
    const value = $('specialty').value;
    if(value === 'ΤΕ16') return 'ΤΕ16';
    if(value.startsWith('ΤΕ01')) return 'ΤΕ01';
    if(value.startsWith('ΤΕ02')) return 'ΤΕ02';
    return '';
  }

  function selectedBranchLabel(){
    const option = $('specialty').selectedOptions[0];
    return option && option.value ? option.textContent.trim() : 'κλάδος/ειδικότητα μη επιλεγμένος/η';
  }


  function socialResult(){return AsepSocialCriteria.getState('socialCriteria',fmt);}

  function serviceResult(){return AsepServiceController.getState('asepService',fmt);}

  function calc(){
    const academic=AsepTeAcademic.getState('asepTeAcademic',fmt);
    const academicResult=academic.result, languages=academic.languages;
    const service=serviceResult();
    const social=socialResult();

    const eligibility=AsepEaeEligibility.getState('eaeEligibility',{socialResult:social});
    const tableCode=eligibility.code, tableLabel=eligibility.label, why=eligibility.why;

    const total = academicResult.points + service.points + social.points;

    $('grandTotal').textContent=fmt(total);
    $('resAcademic').textContent=`${fmt(academicResult.points)} / 120`;
    $('resService').textContent=`${fmt(service.points)} / 120`;
    $('resSocial').textContent=fmt(social.points);
    $('resDegree').textContent=fmt(academicResult.degreePoints);
    $('resLanguage').textContent=fmt(languages.points);
    $('resChildren').textContent=fmt(social.childrenPoints);
    $('resDisability').textContent=fmt(social.disabilityPoints);
    $('resTable').textContent=tableLabel;


    $('tableStatus').classList.toggle('yes',tableCode==='main'||tableCode==='aux');
    $('tableStatus').textContent=tableLabel;
    $('eligibilityWhy').textContent=why;

    const ped=$('pedagogical').checked;
    const priorities=[];
    if(ped) priorities.push('ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής και Διδακτικής Επάρκειας');
    priorities.push.apply(priorities,EaeSensoryProof.priorityLabels());
    $('priorityBox').classList.toggle('yes',priorities.length>0);
    $('priorityBox').textContent=priorities.length?priorities.join(' · '):'Χωρίς δηλωμένη ειδική πρόταξη / προτεραιότητα';

    return {academicResult,languages,service,social,total,ped,tableCode,tableLabel,why,priorities};
  }

  function summary(v){
    return [
      `Υπολογισμός μορίων 4ΕΑ/2025 — ${selectedBranchLabel()}`,
      `Σύνολο: ${fmt(v.total)}`,
      `Ακαδημαϊκά: ${fmt(v.academicResult.points)} / 120`,
      `Ξένη γλώσσα: ${fmt(v.languages.points)}`,
      `Προϋπηρεσία: ${fmt(v.service.points)} / 120`,
      `Κοινωνικά: ${fmt(v.social.points)}`,
      `Πίνακας Ε.Α.Ε.: ${v.tableLabel}`,
      v.why,
      AsepPedagogicalProof.summary('pedagogical'),
      v.priorities.length?'Προτεραιότητες: '+v.priorities.join(' · '):'',
      EaeSensoryProof.summary(),
      AsepTeAcademic.trainingSummary('asepTeAcademic')
    ].filter(Boolean).join('\n');
  }

  function sanitizeIntegerInput(el){
    if(!el) return;
    const ids=['eaeMonths','children'];
    if(!ids.includes(el.id) || el.value==='') return;
    let value=Math.max(0,Math.floor(Number(el.value)||0));
    const max=el.getAttribute('max');
    if(max!==null && max!=='') value=Math.min(value,Number(max));
    el.value=String(value);
  }

  document.addEventListener('input',e=>{sanitizeIntegerInput(e.target);calc();});
  document.addEventListener('change',e=>{
    sanitizeIntegerInput(e.target);
    calc();
  });


  $('copyBtn').addEventListener('click',async()=>{
    const text=summary(calc());
    try{
      await navigator.clipboard.writeText(text);
      const old=$('copyBtn').textContent;
      $('copyBtn').textContent='Αντιγράφηκε';
      setTimeout(()=>$('copyBtn').textContent=old,1400);
    }catch(e){alert(text);}
  });

  $('resetBtn').addEventListener('click',()=>{
    document.querySelectorAll('input[type="number"]').forEach(el=>el.value='0');
    $('degreeGrade').value='';
    document.querySelectorAll('input[type="text"]').forEach(el=>el.value='');
    document.querySelectorAll('input[type="checkbox"]').forEach(el=>el.checked=false);
    document.querySelectorAll('input[name="trainingProofDates"]').forEach(el=>el.checked=false);
    $('specialty').value='';
    $('mainCriterion').value='none';
    AsepServiceController.reset('asepService',{silent:true});
    AsepTeAcademic.reset('asepTeAcademic',{silent:true});
    AsepPedagogicalProof.reset('pedagogical');
    EaeSensoryProof.reset();
    calc();
  });

  AsepTeAcademic.sync('asepTeAcademic');
  calc();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
