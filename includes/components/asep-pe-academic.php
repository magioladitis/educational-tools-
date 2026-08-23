<?php
/**
 * Shared ASEP PE academic-criteria presentation block.
 *
 * Scope: PE academic criteria used by 1GE/2026-2GE/2026 and consumers that
 * explicitly reuse those criteria (currently DIM.O.S./Onaseia detailed mode).
 *
 * Scoring remains exclusively in includes/academic-calculations.js.
 * Foreign-language rules remain exclusively in includes/language-calculations.js.
 */
require_once __DIR__ . '/asep-language-selector.php';
require_once __DIR__ . '/asep-computer-proof.php';
require_once __DIR__ . '/training-proof.php';

if (!function_exists('renderAsepPeAcademic')) {
    function renderAsepPeAcademic($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }

        $defaults = array(
            'id' => 'asepPeAcademic',
            'specialty_id' => 'specialty',
            'field_class' => 'field',
            'grid_class' => 'field-grid',
            'degree_id' => 'degreeGrade',
            'degree_input_type' => 'number',
            'second_degree_id' => 'secondDegree',
            'phd_id' => 'phd',
            'msc_id' => 'mscCount',
            'language_id' => 'asepLanguages',
            'computer_id' => 'computer',
            'training_id' => 'training',
            'training_proof_id' => 'trainingProof',
            'show_subtotal' => false,
            'subtotal_id' => 'academicSubtotal',
            'subtotal_label' => 'Σύνολο Ακαδημαϊκών',
        );
        $c = array_merge($defaults, $config);

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $h = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };

        $degreeType = $c['degree_input_type'] === 'text' ? 'text' : 'number';
        $degreeExtra = $degreeType === 'text'
            ? ' inputmode="decimal"'
            : ' min="5" max="10" step="0.01"';
        ?>
<div
  id="<?php echo $h($c['id']); ?>"
  class="asep-pe-academic"
  data-component="asep-pe-academic"
  data-specialty-id="<?php echo $h($c['specialty_id']); ?>"
  data-degree-id="<?php echo $h($c['degree_id']); ?>"
  data-second-degree-id="<?php echo $h($c['second_degree_id']); ?>"
  data-phd-id="<?php echo $h($c['phd_id']); ?>"
  data-msc-id="<?php echo $h($c['msc_id']); ?>"
  data-language-id="<?php echo $h($c['language_id']); ?>"
  data-computer-id="<?php echo $h($c['computer_id']); ?>"
  data-training-id="<?php echo $h($c['training_id']); ?>"
  data-training-proof-id="<?php echo $h($c['training_proof_id']); ?>"
>
  <h3>Τίτλοι σπουδών</h3>
  <div class="<?php echo $h($c['grid_class']); ?>">
    <div class="<?php echo $h($c['field_class']); ?>">
      <label for="<?php echo $h($c['degree_id']); ?>">Βαθμός βασικού τίτλου σπουδών
        <small>Βαθμός 5,00–10,00 × 2,5.</small>
      </label>
      <input type="<?php echo $h($degreeType); ?>"<?php echo $degreeExtra; ?> id="<?php echo $h($c['degree_id']); ?>" placeholder="π.χ. 7,50">
    </div>

    <div class="<?php echo $h($c['field_class']); ?>">
      <label for="<?php echo $h($c['second_degree_id']); ?>">Δεύτερο πτυχίο Α.Ε.Ι.<small>7 μόρια</small></label>
      <select id="<?php echo $h($c['second_degree_id']); ?>"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
    </div>

    <div class="<?php echo $h($c['field_class']); ?>">
      <label for="<?php echo $h($c['phd_id']); ?>">Διδακτορικό δίπλωμα<small>40 μόρια</small></label>
      <select id="<?php echo $h($c['phd_id']); ?>"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
    </div>

    <div class="<?php echo $h($c['field_class']); ?>">
      <label for="<?php echo $h($c['msc_id']); ?>">Μεταπτυχιακός τίτλος / integrated master<small>1ος τίτλος: 20 · 2ος τίτλος: 8 μόρια</small></label>
      <select id="<?php echo $h($c['msc_id']); ?>"><option value="0">Κανένας</option><option value="1">Ένας τίτλος</option><option value="2">Δύο τίτλοι</option></select>
    </div>
  </div>

  <div class="note">Βασικός τίτλος: βαθμός × 2,5 · Δεύτερο πτυχίο: 7 · Διδακτορικό: 40 · 1ος μεταπτυχιακός / integrated master: 20 · 2ος: 8 μόρια.</div>

<?php
renderAsepLanguageSelector(array(
    'id' => $c['language_id'],
    'profile' => 'pe',
    'specialty_id' => $c['specialty_id'],
    'field_class' => $c['field_class']
));
?>

  <h3>Λοιπά ακαδημαϊκά προσόντα</h3>
  <div class="<?php echo $h($c['grid_class']); ?>">
<?php
renderAsepComputerProof(array(
    'input_id' => $c['computer_id'],
    'control_type' => 'select',
    'points_text' => '4 μόρια',
    'restriction_note' => 'Δεν μοριοδοτείται στον ΠΕ86.',
    'field_class' => $c['field_class']
));
?>
    <div class="<?php echo $h($c['field_class']); ?>">
      <label for="<?php echo $h($c['training_id']); ?>">Επιμόρφωση ≥300 ωρών και διάρκειας ≥7 μηνών<small>2 μόρια · μοριοδοτείται μία επιμόρφωση</small></label>
      <select id="<?php echo $h($c['training_id']); ?>"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
    </div>

<?php
renderTrainingProof(array(
    'id' => $c['training_proof_id'],
    'input_id' => $c['training_id'],
    'radio_name' => $c['training_proof_id'] . 'Dates',
    'yes_id' => $c['training_proof_id'] . 'DatesYes',
    'no_id' => $c['training_proof_id'] . 'DatesNo',
    'status_id' => $c['training_proof_id'] . 'DatesStatus',
    'context' => 'asep-pe-300h-7m',
    'legal_html' => 'Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα.'
));
?>
  </div>

  <div class="note">Γνώση Η/Υ: 4 μόρια (δεν μοριοδοτείται στον ΠΕ86) · Επιμόρφωση: 2 μόρια. Το σύνολο των Ακαδημαϊκών Προσόντων δεν μπορεί να υπερβεί τις 120 μονάδες.</div>

<?php if (!empty($c['show_subtotal'])): ?>
  <div class="subtot"><span><?php echo $h($c['subtotal_label']); ?></span><span class="pill" id="<?php echo $h($c['subtotal_id']); ?>">0,00 / 120</span></div>
<?php endif; ?>
</div>
        <?php
    }
}
