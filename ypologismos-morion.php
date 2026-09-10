<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/teacher-specialties.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body class="edu-ui edu-calc-standard edu-calc-asep-main">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pe-academic.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-digital-tutoring-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pedagogical-proof.php'; ?>

<div class="app">
<?php calculatorHero(array(
    'title_html' => 'Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026',
    'intro' => 'Ενδεικτικός υπολογισμός για τους αξιολογικούς πίνακες Γενικής Εκπαίδευσης κατηγορίας Π.Ε.',
    'meta_class' => 'meta',
    'badges' => array('1ΓΕ/2026', '2ΓΕ/2026', 'Ακαδημαϊκά έως 120', 'Προϋπηρεσία έως 120')
)); ?>

<?php
renderDeadlineCard(array(
    'title' => '📅 Δήλωση περιοχών αναπληρωτών 2026–2027',
    'intro' => 'Η φετινή πρόσκληση του ΥΠΑΙΘΑ για πρόσληψη αναπληρωτών/ωρομισθίων είναι σε εξέλιξη.',
    'items' => array(array(
        'title' => '1ΓΕ/2026 & 2ΓΕ/2026',
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
        <h2>Κλάδος / ειδικότητα</h2>
        <p class="cap">Ο κλάδος εφαρμόζει τους ειδικούς κανόνες της προκήρυξης, όπως τις εξαιρέσεις ξένων γλωσσών και τη μη μοριοδότηση Η/Υ στον ΠΕ86.</p>

        <div class="field-grid">
          <div class="field">
            <label for="specialty">Κλάδος / ειδικότητα</label>
            <?php
            // This calculator keeps its own allowlist. The shared registry only
            // resolves the human-readable label for each permitted code.
            $allowedSpecialties = array(
                'ΠΕ01', 'ΠΕ02', 'ΠΕ03', 'ΠΕ04', 'ΠΕ05', 'ΠΕ06', 'ΠΕ07', 'ΠΕ08', 'ΠΕ11',
                'ΠΕ33', 'ΠΕ34', 'ΠΕ40', 'ΠΕ41', 'ΠΕ60', 'ΠΕ70', 'ΠΕ73', 'ΠΕ78',
                'ΠΕ79.01', 'ΠΕ79.02', 'ΠΕ80', 'ΠΕ81', 'ΠΕ82', 'ΠΕ83', 'ΠΕ84', 'ΠΕ85',
                'ΠΕ86', 'ΠΕ87', 'ΠΕ88', 'ΠΕ89', 'ΠΕ90', 'ΠΕ91'
            );
            ?>
            <select id="specialty">
              <option value="">— Επιλογή κλάδου —</option>
              <?php foreach ($allowedSpecialties as $code) { ?>
                <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8'); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>Α. Ακαδημαϊκά προσόντα</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>
<?php
renderAsepPeAcademic(array(
    'id' => 'asepPeAcademic',
    'specialty_id' => 'specialty',
    'field_class' => 'field',
    'degree_input_type' => 'number',
    'show_subtotal' => true,
    'subtotal_id' => 'academicSubtotal'
));
?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('id' => 'asepService', 'attrs' => array('data-component' => 'asep-service-criteria', 'data-subtotal-id' => 'serviceSubtotal', 'data-subtotal-with-cap' => 'true'))); ?>
        <h2>Β. Εκπαιδευτική προϋπηρεσία</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="note">Βάλε κάθε χρονικό διάστημα σε <strong>ένα μόνο</strong> αντίστοιχο πεδίο, ώστε να μη γίνει διπλή μέτρηση.</div>

        <div class="field-grid">
          <div class="field"><label for="regularMonths">Δημόσια εκπαιδευτική προϋπηρεσία<small>1 μόριο/μήνα · έως 120 μήνες</small></label><input type="number" id="regularMonths" data-service-role="regular" min="0" max="120" step="1" value="0" inputmode="numeric"></div>
          <div class="field"><label for="difficultMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–2021<small>2 μόρια/μήνα · έως 60 μήνες</small></label><input type="number" id="difficultMonths" data-service-role="difficult" min="0" max="60" step="1" value="0" inputmode="numeric"></div>
        </div>

<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'threeMonthRegular2020',
    'difficult_2020_id' => 'threeMonthDifficult2020',
    'regular_2021_id' => 'threeMonthRegular2021',
    'difficult_2021_id' => 'threeMonthDifficult2021'
));
?>

        <div class="field-grid">
          <div class="field"><label for="privateMonths">Ιδιωτική εκπαίδευση<small>0,9 μόρια/μήνα</small></label><input type="number" id="privateMonths" data-service-role="private" min="0" max="600" step="1" value="0" inputmode="numeric"></div>
        </div>

<?php renderAsepDigitalTutoringService(array('container_id' => 'digitalTutoring', 'input_class' => 'service-months')); ?>
        <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Προϋπηρεσίας', 'value_id' => 'serviceSubtotal', 'value_html' => '0,00 / 120')); ?>
      <?php calculatorCardEnd(); ?>

<?php
renderAsepSocialCriteria(array(
    'title' => 'Γ. Κοινωνικά κριτήρια',
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
    'child_extra_note' => 'Ανεξαρτήτως ηλικίας.',
    'child_auxiliary_note' => '',
    'warning_id' => '',
    'subtotal_id' => 'socialSubtotal',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>

      <?php calculatorCardStart(); ?>
        <h2>Δ. Πρόταξη — Παιδαγωγική και Διδακτική Επάρκεια</h2>
        <p class="cap">Η Π.Δ.Ε. δεν προσθέτει από μόνη της μόρια, αλλά επηρεάζει τη σειρά κατάταξης.</p>
<?php renderAsepPedagogicalProof(array(
    'context' => 'general-pe-2026',
    'input_id' => 'pedagogical'
)); ?>
      <?php calculatorCardEnd(); ?>

      <p class="small-note">Το αποτέλεσμα είναι ενδεικτικό και δεν αντικαθιστά την επίσημη προκήρυξη, τον έλεγχο του Α.Σ.Ε.Π., τον Ο.Π.ΣΥ.Δ. ή τον επίσημο πίνακα κατάταξης.</p>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'card results', 'aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array('value_id' => 'grandTotal', 'value_html' => '0,00', 'label' => 'συνολικά μόρια')); ?>

      <?php calculatorResultRow(array('label_html' => 'Ακαδημαϊκά', 'value_html' => '0,00 / 120', 'value_id' => 'resAcademic')); ?>
      <?php calculatorResultRow(array('label_html' => 'Προϋπηρεσία', 'value_html' => '0,00 / 120', 'value_id' => 'resService')); ?>
      <?php calculatorResultRow(array('label_html' => 'Κοινωνικά', 'value_html' => '0,00', 'value_id' => 'resSocial')); ?>
      <?php calculatorResultRow(array('label_html' => 'Βαθμός τίτλου', 'value_html' => '0,00', 'value_id' => 'resDegree')); ?>

      <?php calculatorResultMessage(array('id' => 'pedagogicalPriorityBox', 'variant' => 'status', 'text' => 'Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.')); ?>
      <?php calculatorResultMessage(array('id' => 'sidebarStatus', 'variant' => 'status', 'text' => 'Συμπλήρωσε κλάδο και βαθμό τίτλου· στη συνέχεια τα μόρια ενημερώνονται αυτόματα.')); ?>

      <?php calculatorActions(array(array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'copyResultBtn', 'disabled' => true), 'html' => 'Αντιγραφή'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetCalculatorBtn'), 'html' => 'Καθαρισμός'))); ?>

      <?php calculatorResultMessage(array('variant' => 'disclaimer', 'text' => 'Η τελική σειρά κατάταξης εξαρτάται από τους κανόνες της αντίστοιχης προκήρυξης και τον επίσημο έλεγχο των δικαιολογητικών.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <?php sourceCardStart(); ?>
    <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> (ΦΕΚ 21/29.04.2026) και <strong>2ΓΕ/2026</strong> (ΦΕΚ 22/29.04.2026), ιδίως το Κεφάλαιο Γ΄ «Κριτήρια Κατάταξης».</p>
    <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78701', '2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
    <?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι ενημερωτικό. Η τελική μοριοδότηση προκύπτει από τον επίσημο έλεγχο της αίτησης και των δικαιολογητικών.<?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardEnd(); ?>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/academic-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-computer-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/training-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pe-academic.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/service-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-service-controller.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-digital-tutoring.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/social-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-social-criteria.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pedagogical-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-points-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
