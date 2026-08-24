<?php
/**
 * Shared presentation-only renderer for the seminar certificate proof UI.
 *
 * Compatibility target: conservative PHP syntax (PHP 5.4+).
 * No arrow functions, scalar/return type declarations or other PHP 7.4+ syntax.
 *
 * IMPORTANT: This component contains no eligibility, scoring, hours, duration,
 * specialty or programme rules. Every page keeps those rules in its own markup
 * and JavaScript and passes only page-specific explanatory copy here.
 */
if (!function_exists('renderTrainingProof')) {
    function renderTrainingProof($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }

        $defaults = array(
            'id' => 'trainingProof',
            'input_ids' => array('training'),
            'context' => '',
            'hidden' => true,
            'title' => 'Έλεγχος πιστοποιητικού σεμιναρίου',
            'question' => 'Στο πιστοποιητικό αναγράφονται η ημερομηνία έναρξης και η ημερομηνία λήξης του σεμιναρίου;',
            'radio_name' => 'trainingProofDates',
            'yes_id' => 'trainingProofDatesYes',
            'no_id' => 'trainingProofDatesNo',
            'aria_label' => 'Ημερομηνίες έναρξης και λήξης στο πιστοποιητικό',
            'yes_label' => '✓ Ναι',
            'no_label' => 'Όχι',
            'status_id' => 'trainingProofDatesStatus',
            'neutral_status' => 'Έλεγξε το πιστοποιητικό πριν την υποβολή των δικαιολογητικών.',
            'legal_html' => '',
        );

        $c = array_merge($defaults, $config);
        if (isset($c['input_id']) && $c['input_id'] !== '') {
            $c['input_ids'] = array($c['input_id']);
        }
        if (!is_array($c['input_ids']) || count($c['input_ids']) === 0) {
            $c['input_ids'] = array('training');
        }
        $inputIds = implode(' ', array_map('strval', $c['input_ids']));
        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $e = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };
        $classes = 'training-proof' . ($c['hidden'] ? ' hidden' : '');
        ?>
<div class="<?= $e($classes) ?>" id="<?= $e($c['id']) ?>" data-component="training-proof" data-input-ids="<?= $e($inputIds) ?>" aria-hidden="<?= $c['hidden'] ? 'true' : 'false' ?>"<?= $c['context'] !== '' ? ' data-training-context="' . $e($c['context']) . '"' : '' ?>>
  <div class="training-proof-title"><?= $e($c['title']) ?></div>
  <div class="training-proof-question"><?= $e($c['question']) ?></div>
  <div class="segmented-choice" role="radiogroup" aria-label="<?= $e($c['aria_label']) ?>">
    <label><input type="radio" name="<?= $e($c['radio_name']) ?>" id="<?= $e($c['yes_id']) ?>" value="yes"><span><?= $e($c['yes_label']) ?></span></label>
    <label><input type="radio" name="<?= $e($c['radio_name']) ?>" id="<?= $e($c['no_id']) ?>" value="no"><span><?= $e($c['no_label']) ?></span></label>
  </div>
  <div class="training-proof-status neutral" id="<?= $e($c['status_id']) ?>"><?= $e($c['neutral_status']) ?></div>
<?php if ($c['legal_html'] !== ''): ?>
  <small class="training-proof-legal"><?= $c['legal_html'] ?></small>
<?php endif; ?>
</div>
        <?php
    }
}
