<?php
/**
 * Shared ASEP TE academic presentation for 1GT/2024 and 4EA/2025.
 *
 * The page-specific branch selector remains outside the component.
 * The component is intentionally split into render parts so 4EA can keep
 * its EAE table-eligibility block between degree controls and qualifications.
 *
 * Scoring remains exclusively in includes/te-academic-calculations.js.
 */
require_once __DIR__ . '/asep-language-selector.php';
require_once __DIR__ . '/asep-computer-proof.php';
require_once __DIR__ . '/training-proof.php';

if (!function_exists('renderAsepTeAcademic')) {
    function renderAsepTeAcademic($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }

        $defaults = array(
            'part' => 'qualifications',
            'id' => 'asepTeAcademic',
            'branch_id' => 'branch',
            'grade_scale_id' => 'gradeScale',
            'degree_id' => 'degreeGrade',
            'text_grade_id' => 'te16TextGrade',
            'normalized_info_id' => 'normalizedGradeInfo',
            'grade_warning_id' => 'gradeWarning',
            'second_title_id' => 'secondTitle',
            'second_title_label_id' => 'secondTitleLabel',
            'language_id' => 'asepLanguages',
            'computer_id' => 'computer',
            'training_id' => 'training',
            'training_proof_id' => 'trainingProof',
            'training_radio_name' => 'trainingDates',
            'training_yes_id' => 'trainingDatesYes',
            'training_no_id' => 'trainingDatesNo',
            'training_status_id' => 'trainingDatesStatus',
            'extra_training_ids' => array(),
            'show_subtotal' => true,
            'subtotal_id' => 'academicSubtotal',
            'subtotal_label' => 'Σύνολο Ακαδημαϊκών',
            'training_context' => 'asep-te-300h-7m',
            'training_help_suffix' => '',
            'training_legal_html' => 'Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών· 6 μήνες και 29 ημέρες δεν γίνονται δεκτοί.</strong>',
            'degree_placeholder_20' => 'π.χ. 15,00',
        );
        $c = array_merge($defaults, $config);

        if (!is_array($c['extra_training_ids'])) {
            $c['extra_training_ids'] = array_filter(array_map('trim', explode(',', (string) $c['extra_training_ids'])));
        }

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $h = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };

        $part = (string) $c['part'];

        if ($part === 'grade-scale') {
            ?>
<div class="field">
  <label for="<?php echo $h($c['grade_scale_id']); ?>">Κλίμακα βαθμού τίτλου</label>
  <select id="<?php echo $h($c['grade_scale_id']); ?>" data-auto="on">
    <option value="20">Κλίμακα 10–20</option>
    <option value="10">Κλίμακα 5–10</option>
    <option value="te16text">ΤΕ16 — περιγραφικός βαθμός</option>
  </select>
</div>
            <?php
            return;
        }

        if ($part === 'degree-details') {
            $extraIds = implode(',', array_map('strval', $c['extra_training_ids']));
            ?>
<div
  id="<?php echo $h($c['id']); ?>"
  class="asep-te-academic-degree"
  data-component="asep-te-academic"
  data-branch-id="<?php echo $h($c['branch_id']); ?>"
  data-grade-scale-id="<?php echo $h($c['grade_scale_id']); ?>"
  data-degree-id="<?php echo $h($c['degree_id']); ?>"
  data-text-grade-id="<?php echo $h($c['text_grade_id']); ?>"
  data-normalized-info-id="<?php echo $h($c['normalized_info_id']); ?>"
  data-grade-warning-id="<?php echo $h($c['grade_warning_id']); ?>"
  data-second-title-id="<?php echo $h($c['second_title_id']); ?>"
  data-second-title-label-id="<?php echo $h($c['second_title_label_id']); ?>"
  data-language-id="<?php echo $h($c['language_id']); ?>"
  data-computer-id="<?php echo $h($c['computer_id']); ?>"
  data-training-id="<?php echo $h($c['training_id']); ?>"
  data-training-proof-id="<?php echo $h($c['training_proof_id']); ?>"
  data-extra-training-ids="<?php echo $h($extraIds); ?>"
  data-subtotal-id="<?php echo $h($c['subtotal_id']); ?>"
  data-degree-placeholder-20="<?php echo $h($c['degree_placeholder_20']); ?>"
>
  <div id="<?php echo $h('numericGradeWrap'); ?>" class="field">
    <label for="<?php echo $h($c['degree_id']); ?>">Βαθμός βασικού τίτλου
      <small>Ο βαθμός ανάγεται σε κλίμακα 20 και πολλαπλασιάζεται ×3. Μέγιστο: 60 μόρια.</small>
    </label>
    <input type="number" id="<?php echo $h($c['degree_id']); ?>" min="10" max="20" step="0.01" value="" placeholder="<?php echo $h($c['degree_placeholder_20']); ?>">
  </div>

  <div id="<?php echo $h('te16TextWrap'); ?>" class="field hidden">
    <label for="<?php echo $h($c['text_grade_id']); ?>">Χαρακτηρισμός βαθμού ΤΕ16</label>
    <select id="<?php echo $h($c['text_grade_id']); ?>">
      <option value="0">Δεν αναγράφεται βαθμολογία → 5,00</option>
      <option value="5">ΚΑΛΩΣ → 5,00</option>
      <option value="6.5">ΛΙΑΝ ΚΑΛΩΣ → 6,50</option>
      <option value="8.5">ΑΡΙΣΤΑ → 8,50</option>
    </select>
    <div class="help">Οι τιμές 5,00 / 6,50 / 8,50 αναφέρονται στην κλίμακα 10 και ο υπολογιστής τις ανάγει αυτόματα σε κλίμακα 20.</div>
  </div>

  <div class="info-note" id="<?php echo $h($c['normalized_info_id']); ?>">Αναγμένος βαθμός: 0,00 / 20 · Μόρια βαθμού: 0,00 / 60</div>
  <div id="<?php echo $h($c['grade_warning_id']); ?>" class="warning hidden">Ο βαθμός δεν βρίσκεται στα επιτρεπτά όρια της επιλεγμένης κλίμακας.</div>
</div>
            <?php
            return;
        }

        if ($part === 'qualifications') {
            $proofInputs = array_merge(array($c['training_id']), $c['extra_training_ids']);
            ?>
<section class="card asep-te-academic-qualifications" data-component-part="asep-te-academic-qualifications">
  <h2>Α. Ακαδημαϊκά προσόντα</h2>
  <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

  <div class="checkrow">
    <input type="checkbox" id="<?php echo $h($c['second_title_id']); ?>">
    <label for="<?php echo $h($c['second_title_id']); ?>"><span id="<?php echo $h($c['second_title_label_id']); ?>">Πτυχίο επιπέδου 5 / Ι.Ε.Κ. ίδιας ειδικότητας</span><small>10 μόρια</small></label>
  </div>

<?php
renderAsepLanguageSelector(array(
    'id' => $c['language_id'],
    'profile' => 'te'
));
?>

<?php
renderAsepComputerProof(array(
    'input_id' => $c['computer_id'],
    'control_type' => 'checkbox',
    'points_text' => '20 μόρια'
));
?>

  <div class="checkrow">
    <input type="checkbox" id="<?php echo $h($c['training_id']); ?>">
    <label for="<?php echo $h($c['training_id']); ?>">Επιμόρφωση ≥300 ωρών και διάρκειας ≥7 μηνών<small>Α.Ε.Ι. ή εποπτευόμενος δημόσιος φορέας — μοριοδοτείται μία επιμόρφωση — 10 μόρια<?php echo $c['training_help_suffix'] ? '. ' . $h($c['training_help_suffix']) : ''; ?></small></label>
  </div>

<?php
renderTrainingProof(array(
    'id' => $c['training_proof_id'],
    'radio_name' => $c['training_radio_name'],
    'yes_id' => $c['training_yes_id'],
    'no_id' => $c['training_no_id'],
    'status_id' => $c['training_status_id'],
    'context' => $c['training_context'],
    'input_ids' => $proofInputs,
    'legal_html' => $c['training_legal_html']
));
?>

<?php if (!empty($c['show_subtotal'])): ?>
  <div class="subtot"><span><?php echo $h($c['subtotal_label']); ?></span><span class="pill" id="<?php echo $h($c['subtotal_id']); ?>">0,00 / 120</span></div>
<?php endif; ?>
</section>
            <?php
            return;
        }
    }
}
