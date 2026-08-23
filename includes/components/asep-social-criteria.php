<?php
/**
 * Shared presentation component for ASEP social criteria.
 *
 * IMPORTANT:
 * - This component does not calculate points.
 * - Every numeric rule is passed explicitly by the calling calculator.
 * - Special EAE/auxiliary-table rules are opt-in via child_auxiliary_note.
 * - Kept intentionally compatible with older PHP runtimes.
 */

if (!function_exists('renderAsepSocialCriteria')) {
    function renderAsepSocialCriteria($config = array())
    {
        $h = function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };

        $title = isset($config['title']) ? $config['title'] : 'Κοινωνικά κριτήρια';
        $containerId = isset($config['container_id']) ? $config['container_id'] : 'socialCriteria';
        $childrenId = isset($config['children_id']) ? $config['children_id'] : 'children';
        $candidateId = isset($config['candidate_id']) ? $config['candidate_id'] : 'candidateDisability';
        $spouseId = isset($config['spouse_id']) ? $config['spouse_id'] : 'spouseDisability';
        $childId = isset($config['child_id']) ? $config['child_id'] : 'childDisability';
        $marriageId = isset($config['marriage_id']) ? $config['marriage_id'] : 'marriageYears4Plus';
        $mentalId = isset($config['mental_id']) ? $config['mental_id'] : 'candidateMentalCondition';
        $inputStep = isset($config['input_step']) ? $config['input_step'] : '0.01';

        $childPoints = isset($config['child_points']) ? $config['child_points'] : '';
        $minDisability = isset($config['min_disability_percent']) ? $config['min_disability_percent'] : '';
        $disabilityRate = isset($config['disability_rate']) ? $config['disability_rate'] : '';
        $spouseYears = isset($config['spouse_min_marriage_years']) ? $config['spouse_min_marriage_years'] : '';

        $childExtraNote = isset($config['child_extra_note']) ? trim((string) $config['child_extra_note']) : '';
        $childAuxiliaryNote = isset($config['child_auxiliary_note']) ? trim((string) $config['child_auxiliary_note']) : '';
        $warningId = isset($config['warning_id']) ? trim((string) $config['warning_id']) : '';
        $subtotalId = isset($config['subtotal_id']) ? trim((string) $config['subtotal_id']) : '';
        $subtotalLabel = isset($config['subtotal_label']) ? $config['subtotal_label'] : 'Σύνολο Κοινωνικών';
        $warningMode = isset($config['warning_mode']) ? trim((string) $config['warning_mode']) : 'text';

        $childNote = 'Από ' . $minDisability . '% και άνω.';
        if ($childExtraNote !== '') {
            $childNote .= ' ' . $childExtraNote;
        }
        if ($childAuxiliaryNote !== '') {
            $childNote .= ' ' . $childAuxiliaryNote;
        }
        ?>
<section
  id="<?php echo $h($containerId); ?>"
  class="card asep-social-criteria"
  data-component="asep-social-criteria"
  data-children-id="<?php echo $h($childrenId); ?>"
  data-candidate-id="<?php echo $h($candidateId); ?>"
  data-spouse-id="<?php echo $h($spouseId); ?>"
  data-child-id="<?php echo $h($childId); ?>"
  data-marriage-id="<?php echo $h($marriageId); ?>"
  data-mental-id="<?php echo $h($mentalId); ?>"
  data-warning-id="<?php echo $h($warningId); ?>"
  data-subtotal-id="<?php echo $h($subtotalId); ?>"
  data-warning-mode="<?php echo $h($warningMode); ?>"
>
  <h2><?php echo $h($title); ?></h2>

  <div class="field">
    <label for="<?php echo $h($childrenId); ?>">Αριθμός επιλέξιμων τέκνων
      <small><?php echo $h($childPoints); ?> μόρια ανά τέκνο.</small>
    </label>
    <input id="<?php echo $h($childrenId); ?>" type="number" min="0" step="1" value="0">
  </div>

  <h3>Αναπηρία — λαμβάνεται μόνο το υψηλότερο επιλέξιμο ποσοστό</h3>
  <div class="field-grid asep-social-disability-grid">
    <div class="field">
      <label for="<?php echo $h($candidateId); ?>">Αναπηρία υποψηφίου/ας (%)
        <small>Από <?php echo $h($minDisability); ?>% και άνω, εφόσον δεν οφείλεται, έστω και κατά ποσοστό, σε ψυχική πάθηση.</small>
      </label>
      <input id="<?php echo $h($candidateId); ?>" type="number" min="0" max="100" step="<?php echo $h($inputStep); ?>" value="0">
    </div>
    <div class="field">
      <label for="<?php echo $h($spouseId); ?>">Αναπηρία συζύγου (%)
        <small>Από <?php echo $h($minDisability); ?>% και άνω, με έγγαμο βίο τουλάχιστον <?php echo $h($spouseYears); ?> ετών.</small>
      </label>
      <input id="<?php echo $h($spouseId); ?>" type="number" min="0" max="100" step="<?php echo $h($inputStep); ?>" value="0">
    </div>
    <div class="field">
      <label for="<?php echo $h($childId); ?>">Υψηλότερο ποσοστό αναπηρίας τέκνου (%)
        <small><?php echo $h($childNote); ?></small>
      </label>
      <input id="<?php echo $h($childId); ?>" type="number" min="0" max="100" step="<?php echo $h($inputStep); ?>" value="0">
    </div>
  </div>

  <div class="info-note asep-social-rule-note">Για τη μοριοδότηση αναπηρίας λαμβάνεται μόνο το υψηλότερο επιλέξιμο ποσοστό και υπολογίζεται ως ποσοστό × <?php echo $h($disabilityRate); ?>.</div>

  <div class="checkrow">
    <input id="<?php echo $h($marriageId); ?>" type="checkbox">
    <label for="<?php echo $h($marriageId); ?>">Ο έγγαμος βίος έχει διαρκέσει τουλάχιστον <?php echo $h($spouseYears); ?> έτη
      <small>Απαιτείται για τη μοριοδότηση αναπηρίας συζύγου.</small>
    </label>
  </div>
  <div class="checkrow">
    <input id="<?php echo $h($mentalId); ?>" type="checkbox">
    <label for="<?php echo $h($mentalId); ?>">Η αναπηρία του/της υποψηφίου οφείλεται, έστω και κατά ποσοστό, σε ψυχική πάθηση
      <small>Αν επιλεγεί, η αναπηρία του/της υποψηφίου δεν μοριοδοτείται.</small>
    </label>
  </div>

  <?php if ($warningId !== ''): ?>
  <div id="<?php echo $h($warningId); ?>" class="note hidden"></div>
  <?php endif; ?>

  <?php if ($subtotalId !== ''): ?>
  <div class="subtot"><span><?php echo $h($subtotalLabel); ?></span><span class="pill" id="<?php echo $h($subtotalId); ?>">0,00</span></div>
  <?php endif; ?>
</section>
        <?php
    }
}
