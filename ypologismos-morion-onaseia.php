<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/teacher-specialties.php';
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός μορίων αναπληρωτών στα Δημόσια Ωνάσεια Σχολεία 2026-2027</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-page-onaseia">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pe-academic.php'; ?>

<?php calculatorContainerStart(array('class' => 'app-box edu-modernized')); ?>
  <?php calculatorHero(array(
  'class' => 'hero edu-legacy-hero',
  'title' => 'Υπολογισμός μορίων αναπληρωτών στα Δημόσια Ωνάσεια Σχολεία',
  'intro_html' => 'Σχολικό έτος <strong>2026-2027</strong>. Ο υπολογισμός βασίζεται στα ακαδημαϊκά προσόντα του πίνακα Α.Σ.Ε.Π. και στην αναγνωρισμένη προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία.',
  'intro_attrs' => array('class' => 'intro')
)); ?>

  <?php
renderDeadlineCard(array(
    'title' => '📅 Προθεσμίες αιτήσεων ΔΗΜ.Ω.Σ. 2026–2027',
    'intro' => 'Οι δύο φετινές προσκλήσεις έχουν διαφορετική καταληκτική ημερομηνία. Η αντίστροφη μέτρηση γίνεται σε ώρα Ελλάδας.',
    'items' => array(
        array(
            'title' => 'Γενική πρόσκληση εκπαιδευτικών',
            'meta_html' => '1ΓΕ/2026, 2ΓΕ/2026 και 1ΓΤ/2024<br>Αιτήσεις έως <strong>Δευτέρα 24 Αυγούστου 2026, 15:00</strong>.',
            'end' => '2026-08-24T15:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/%CE%957%CE%98%CE%9146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A1%CE%9C%CE%98?inline=true',
            'source_label' => 'Πρόσκληση — ΑΔΑ Ε7ΘΑ46ΝΚΠΔ-ΡΜΘ ↗'
        ),
        array(
            'title' => 'Ειδική πρόσκληση ΕΑΕ — Τμήματα Ένταξης',
            'meta_html' => '3ΕΑ/2025 — ΠΕ02, ΠΕ03 και ΠΕ04 με εξειδίκευση στην ΕΑΕ<br>Αιτήσεις έως <strong>Δευτέρα 31 Αυγούστου 2026, 15:00</strong>.<br><strong>Για την ΕΑΕ:</strong> χρησιμοποίησε τη χειροκίνητη καταχώριση των ακαδημαϊκών μορίων όπως εμφανίζονται στον πίνακα 3ΕΑ/2025.',
            'end' => '2026-08-31T15:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/%CE%A1%CE%A4%CE%91%CE%A746%CE%9D%CE%9A%CE%A0%CE%94-%CE%932%CE%97?inline=true',
            'source_label' => 'Πρόσκληση ΕΑΕ — ΑΔΑ ΡΤΑΧ46ΝΚΠΔ-Γ2Η ↗'
        )
    ),
    'note_html' => 'Το countdown είναι ενημερωτικό. Για την ακριβή ισχύ της προθεσμίας υπερισχύει πάντοτε το κείμενο της αντίστοιχης επίσημης πρόσκλησης.'
));
?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <div class="important">
        <strong>Τύπος υπολογισμού:</strong><br>
        Μόρια ΔΗΜ.Ω.Σ. = Ακαδημαϊκά Προσόντα Α.Σ.Ε.Π. + μόρια προϋπηρεσίας σε Πρότυπα/Πειραματικά.<br>
        Η ειδική προϋπηρεσία μοριοδοτείται με <strong>1,5 μόριο ανά αναγνωρισμένο μήνα</strong>,
        με ανώτατο όριο <strong>15 μόρια ανά σχολικό έτος</strong>.
      </div>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>1. Κλάδος / ειδικότητα</h2>

    <div class="question">
      <label for="specialty">Επίλεξε κλάδο</label>
      <select id="specialty">
        <option value="">-- Επιλογή --</option>
        <?php
        // ΔΗΜ.Ω.Σ.: κρατάμε το ειδικό allowlist του εργαλείου.
        // Το κοινό registry παρέχει μόνο τα λεκτικά.
        $onaseiaSpecialties = array(
            'ΠΕ01', 'ΠΕ02', 'ΠΕ03', 'ΠΕ04', 'ΠΕ05', 'ΠΕ06', 'ΠΕ07', 'ΠΕ08', 'ΠΕ11',
            'ΠΕ78', 'ΠΕ79.01', 'ΠΕ80', 'ΠΕ81', 'ΠΕ82', 'ΠΕ83', 'ΠΕ84', 'ΠΕ85', 'ΠΕ86',
            'ΠΕ88', 'ΤΕ16'
        );
        foreach ($onaseiaSpecialties as $code) {
            echo '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8')
                . '</option>';
        }
        ?>
      </select>
    </div>

    <div class="question hidden" id="pe04VacancySpecialtyWrap">
      <label for="pe04VacancySpecialty">Ειδικότητα ΠΕ04 για προβολή φετινών κενών</label>
      <select id="pe04VacancySpecialty">
        <option value="">-- Επιλογή ειδικότητας ΠΕ04 --</option>
        <?php
        $onaseiaPe04VacancySpecialties = array('ΠΕ04.01', 'ΠΕ04.02', 'ΠΕ04.04', 'ΠΕ04.05');
        foreach ($onaseiaPe04VacancySpecialties as $code) {
            echo '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8')
                . '</option>';
        }
        ?>
      </select>
      <p class="note">Η υποεπιλογή χρησιμοποιείται μόνο για την προβολή των λειτουργικών αναγκών και δεν αλλάζει τον υπολογισμό των ακαδημαϊκών μορίων ΠΕ04.</p>
    </div>

    <div id="vacancyPanel" class="important hidden" role="status" aria-live="polite"></div>

    <p class="note">
      Για τους κλάδους ΠΕ γίνεται αναλυτικός υπολογισμός των ακαδημαϊκών προσόντων των 1ΓΕ/2026 και 2ΓΕ/2026.
      Για τον ΤΕ16 χρησιμοποιείται η καταχώριση των ακαδημαϊκών μορίων όπως εμφανίζονται στον πίνακα της 1ΓΤ/2024.
    </p>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section', 'id' => 'academicSection')); ?>
    <h2>2. Ακαδημαϊκά Προσόντα Α.Σ.Ε.Π.</h2>

    <div class="mode-row" id="modeRow">
      <label>
        <input type="radio" name="academicMode" value="detailed" checked>
        Αναλυτικός υπολογισμός
      </label>
      <label>
        <input type="radio" name="academicMode" value="manual">
        Γνωρίζω ήδη τα ακαδημαϊκά μόρια από τον πίνακα Α.Σ.Ε.Π.
      </label>
    </div>

    <div id="manualAcademic" class="hidden">
      <div class="question">
        <label for="manualAcademicPoints">Ακαδημαϊκά μόρια Α.Σ.Ε.Π. (<span id="manualAcademicRange">12,50–120</span>)</label>
        <input type="text" inputmode="decimal" id="manualAcademicPoints" placeholder="π.χ. 82,50" aria-describedby="manualAcademicHelp">
      </div>
      <p class="note" id="manualAcademicHelp">
        Συμπλήρωσε μόνο τη μοριοδότηση της κατηγορίας «Ακαδημαϊκά Προσόντα» και όχι το συνολικό σκορ κατάταξης.
        Για τους κλάδους ΠΕ το ελάχιστο είναι <strong>12,50 μόρια</strong> (βαθμός βασικού τίτλου 5,00 × 2,5).
        Για τον ΤΕ16, που χρησιμοποιεί τα μόρια της 1ΓΤ/2024, το ελάχιστο είναι <strong>30 μόρια</strong>.
      </p>
    </div>

    <div id="detailedAcademic">
<?php
renderAsepPeAcademic(array(
    'id' => 'asepPeAcademic',
    'specialty_id' => 'specialty',
    'field_class' => 'question',
    'degree_input_type' => 'text',
    'show_subtotal' => false
));
?>
    </div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>3. Προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία</h2>

    <?php calculatorDisclosureStart(array(
      'summary' => 'Πώς προσμετράται η ειδική προϋπηρεσία;',
      'open' => true,
      'attrs' => array('data-mobile-collapsed' => 'true')
    )); ?>
      <p>
        Καταχώρισε τους <strong>αναγνωρισμένους μήνες ανά σχολικό έτος από το 2020-2021 και μετά</strong>.
        Κάθε μήνας δίνει 1,5 μόριο, με μέγιστο 15 μόρια ανά σχολικό έτος.
        Οι μήνες Ιουλίου και Αυγούστου δεν προσμετρώνται.
        <strong>Σημείωση:</strong> η πρόσκληση δεν αναγράφει ρητά το 2020-2021 ως αφετηρία· το εργαλείο ακολουθεί την εφαρμογή που αποτυπώνεται στους δημοσιευμένους πίνακες κατάταξης και την αναφορά της πρόσκλησης στα Πρότυπα/Πειραματικά «όπως ορίζονται στον ν. 4692/2020».
      </p>
    <?php calculatorDisclosureEnd(); ?>

    <div id="serviceRows"></div>

    <button type="button" class="add-row" id="addServiceRowBtn">+ Προσθήκη σχολικού έτους</button>
  <?php calculatorCardEnd(); ?>

  <?php calculatorActions(array(
    array('label' => 'Έλεγχος & υπολογισμός', 'attrs' => array('type' => 'button', 'id' => 'calculateBtn')),
    array('label' => 'Καθαρισμός', 'class' => 'reset-btn', 'attrs' => array('type' => 'button', 'id' => 'resetBtn'))
  )); ?>

      <?php calculatorInlineResult(array('id' => 'result', 'class' => 'result', 'attrs' => array('role' => 'status', 'aria-live' => 'polite'))); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'card results', 'aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value_id' => 'grandTotal',
        'value_html' => '0,00',
        'label' => 'συνολικά μόρια ΔΗΜ.Ω.Σ.'
      )); ?>
      <?php calculatorResultRow(array('label_html' => 'Ακαδημαϊκά Α.Σ.Ε.Π.', 'value_html' => '0,00', 'value_id' => 'resAcademic')); ?>
      <?php calculatorResultRow(array('label_html' => 'Πρότυπα / Πειραματικά', 'value_html' => '0,00', 'value_id' => 'resService')); ?>
      <?php calculatorResultMessage(array(
        'id' => 'sidebarStatus',
        'variant' => 'status',
        'text' => 'Επίλεξε κλάδο και συμπλήρωσε τα απαιτούμενα στοιχεία.'
      )); ?>
      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'text' => 'Σε περίπτωση ισοβαθμίας εφαρμόζονται οι κανόνες της επίσημης πρόσκλησης. Ο υπολογισμός είναι ενημερωτικός.'
      )); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/academic-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-computer-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/training-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pe-academic.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/onaseia-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/onaseia-vacancies-2026.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/onaseia-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>


<?php sourceCardStart(); ?>
  <p>Η κατάταξη στα ΔΗΜ.Ω.Σ. συνδυάζει τη μοριοδότηση των ακαδημαϊκών προσόντων όπως έχει διαμορφωθεί στον αντίστοιχο πίνακα Α.Σ.Ε.Π. με την αναγνωρισμένη προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία.</p>
  <p><strong>Χρονική αφετηρία ειδικής προϋπηρεσίας:</strong> στο εργαλείο καταχωρίζεται προϋπηρεσία από το σχολικό έτος <strong>2020-2021</strong> και μετά. Η πρόσκληση δεν αναγράφει ρητά αυτό το έτος ως όριο· η επιλογή ακολουθεί την εφαρμογή που αποτυπώνεται στους δημοσιευμένους πίνακες κατάταξης και την παραπομπή της πρόσκλησης στα Πρότυπα/Πειραματικά του ν. 4692/2020.</p>
  <p><strong>Φετινές προσκλήσεις πρόσληψης 2026–2027:</strong></p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://diavgeia.gov.gr/doc/%CE%957%CE%98%CE%9146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A1%CE%9C%CE%98?inline=true', '14/08/2026 — Γενική πρόσκληση ΔΗΜ.Ω.Σ. για αναπληρωτές — ΑΔΑ Ε7ΘΑ46ΝΚΠΔ-ΡΜΘ ↗'); ?><?php sourceCardLink('https://diavgeia.gov.gr/doc/%CE%A1%CE%A4%CE%91%CE%A746%CE%9D%CE%9A%CE%A0%CE%94-%CE%932%CE%97?inline=true', '20/08/2026 — Ειδική πρόσκληση ΕΑΕ για Τμήματα Ένταξης ΔΗΜ.Ω.Σ. — ΑΔΑ ΡΤΑΧ46ΝΚΠΔ-Γ2Η ↗'); ?><?php sourceCardLinksEnd(); ?>
  <p><strong>Λειτουργικές ανάγκες πλήρους ωραρίου 2026–2027 (26/08/2026):</strong></p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://diavgeia.gov.gr/doc/%CE%A1%CE%9D7%CE%9546%CE%9D%CE%9A%CE%A0%CE%94-%CE%98%CE%A3%CE%94?inline=true', '55/ΔΕΔΗΜΩΣ — Γενική Εκπαίδευση — ΑΔΑ ΡΝ7Ε46ΝΚΠΔ-ΘΣΔ ↗'); ?><?php sourceCardLink('https://diavgeia.gov.gr/doc/9%CE%98%CE%93246%CE%9D%CE%9A%CE%A0%CE%94-%CE%A5%CE%990?inline=true', '56/ΔΕΔΗΜΩΣ — ΕΑΕ — ΑΔΑ 9ΘΓ246ΝΚΠΔ-ΥΙ0 ↗'); ?><?php sourceCardLinksEnd(); ?>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://www.minedu.gov.gr/news?catid=1183&id=63940%3A30-01-26-prokiryksi-diadikasias-katataksis-ekpaideftikon-vvathmias-ekpaidefsis-me-seira-proteraiotitas-kata-klado-kai-eidikotita-ypopsifion-gia-tin-plirosi-kenon-theseon-thiteias-sta-dimosia-onaseia-sxoleia&view=article', 'Προκήρυξη διαδικασίας κατάταξης ΔΗΜ.Ω.Σ. — ΥΠΑΙΘΑ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78737', '1ΓΕ/2026 & 2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://dedimos.minedu.gov.gr/nea/anakoinoseis/anartisi-pinakon-katataxis-tis-prosklisis-tis-d-e-dim-o-s-gia-anaplirotes-plirous-i-meiomenou-orariou-genikis-ekpaidefsis/', '26/08/2026 — Δημοσιευμένοι πίνακες κατάταξης αναπληρωτών ΔΗΜ.Ω.Σ. ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
