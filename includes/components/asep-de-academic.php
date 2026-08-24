<?php
/** Shared academic presentation for ASEP 5EA/2022 (category DE EAE). */
require_once __DIR__ . '/asep-language-selector.php';
require_once __DIR__ . '/asep-computer-proof.php';
require_once __DIR__ . '/training-proof.php';

if (!function_exists('renderAsepDeAcademic')) {
    function renderAsepDeAcademic($config = array())
    {
        if (!is_array($config)) $config = array();
        $id = isset($config['id']) ? (string) $config['id'] : 'asepDeAcademic';
        $degreeId = isset($config['degree_id']) ? (string) $config['degree_id'] : 'degreeGrade';
        $workId = isset($config['work_id']) ? (string) $config['work_id'] : 'workExperienceYears';
        $languageId = isset($config['language_id']) ? (string) $config['language_id'] : 'asepLanguages';
        $computerId = isset($config['computer_id']) ? (string) $config['computer_id'] : 'computer';
        $trainingId = isset($config['training_id']) ? (string) $config['training_id'] : 'training';
        $trainingProofId = isset($config['training_proof_id']) ? (string) $config['training_proof_id'] : 'trainingProof';
        $extraTrainingIds = isset($config['extra_training_ids']) ? $config['extra_training_ids'] : array();
        if (!is_array($extraTrainingIds)) {
            $extraTrainingIds = array_filter(array_map('trim', explode(',', (string) $extraTrainingIds)));
        }
        $extraTrainingIds = array_values(array_filter(array_map('strval', $extraTrainingIds)));
        $subtotalId = isset($config['subtotal_id']) ? (string) $config['subtotal_id'] : 'academicSubtotal';
        $warningId = isset($config['warning_id']) ? (string) $config['warning_id'] : 'academicWarning';
        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) $flags = $flags | ENT_SUBSTITUTE;
        $h = function ($value) use ($flags) { return htmlspecialchars((string) $value, $flags, 'UTF-8'); };
        ?>
<section id="<?php echo $h($id); ?>" class="card asep-de-academic" data-component="asep-de-academic" data-degree-id="<?php echo $h($degreeId); ?>" data-work-id="<?php echo $h($workId); ?>" data-language-id="<?php echo $h($languageId); ?>" data-computer-id="<?php echo $h($computerId); ?>" data-training-id="<?php echo $h($trainingId); ?>" data-training-proof-id="<?php echo $h($trainingProofId); ?>" data-extra-training-ids="<?php echo $h(implode(',', $extraTrainingIds)); ?>" data-subtotal-id="<?php echo $h($subtotalId); ?>" data-warning-id="<?php echo $h($warningId); ?>">
  <h2>Α. Ακαδημαϊκά προσόντα</h2>
  <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

  <div class="field">
    <label for="<?php echo $h($degreeId); ?>">Βαθμός βασικού τίτλου σε 20βάθμια κλίμακα
      <small>Βαθμός ×2,5 · έως 50 μόρια · στρογγυλοποίηση στο δεύτερο δεκαδικό.</small>
    </label>
    <input type="number" id="<?php echo $h($degreeId); ?>" min="10" max="20" step="0.01" inputmode="decimal" placeholder="π.χ. 16,40">
  </div>

  <div class="field">
    <label for="<?php echo $h($workId); ?>">Πρόσθετα πλήρη έτη εργασιακής εμπειρίας στην ειδικότητα
      <small>4 μόρια ανά έτος · έως 5 έτη / 20 μόρια. <strong>Μην περιλάβεις</strong> την υποχρεωτική τριετή επαγγελματική πείρα που αποτελεί τυπικό προσόν διορισμού ούτε εκπαιδευτική προϋπηρεσία.</small>
    </label>
    <input type="number" id="<?php echo $h($workId); ?>" min="0" max="5" step="1" inputmode="numeric" value="0">
  </div>

<?php renderAsepLanguageSelector(array('id' => $languageId, 'profile' => 'de')); ?>

<?php renderAsepComputerProof(array(
    'input_id' => $computerId,
    'control_type' => 'checkbox',
    'points_text' => '20 μόρια',
    'context' => '5ea-2022'
)); ?>

  <div class="checkrow">
    <input type="checkbox" id="<?php echo $h($trainingId); ?>">
    <label for="<?php echo $h($trainingId); ?>">Επιμόρφωση ≥300 ωρών και διάρκειας ≥7 μηνών
      <small>Α.Ε.Ι. ή εποπτευόμενος δημόσιος φορέας · μοριοδοτείται μία επιμόρφωση · 10 μόρια. Το σεμινάριο Ε.Α.Ε. ≥400 ωρών / ≥7 μηνών καλύπτει αυτόματα και αυτό το κριτήριο.</small>
    </label>
  </div>
  <div class="info-note hidden" id="trainingInheritedHelp" data-de-training-inherited-note>Το σεμινάριο Ε.Α.Ε. ≥400 ωρών / ≥7 μηνών καλύπτει αυτόματα και την επιμόρφωση ≥300 ωρών / ≥7 μηνών. Τα 10 μόρια υπολογίζονται μία φορά.</div>

<?php renderTrainingProof(array(
    'id' => $trainingProofId,
    'input_ids' => array_merge(array($trainingId), $extraTrainingIds),
    'context' => '5ea-2022-300h-or-eae-400h-7m',
    'legal_html' => 'Αν στο πιστοποιητικό δεν αναγράφονται ημερομηνία έναρξης και λήξης, απαιτείται σχετική βεβαίωση του φορέα. Πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών.'
)); ?>

  <div id="<?php echo $h($warningId); ?>" class="note hidden"></div>
  <div class="subtot"><span>Σύνολο Ακαδημαϊκών</span><span class="pill" id="<?php echo $h($subtotalId); ?>">0,00 / 120</span></div>
</section>
        <?php
    }
}
