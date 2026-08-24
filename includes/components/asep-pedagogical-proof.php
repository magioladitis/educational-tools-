<?php
/**
 * Shared ASEP presentation/proof component for Pedagogical & Teaching Competence (PDE).
 * No points are calculated here. The main checkbox keeps the page-local precedence logic intact.
 * Compatible with older PHP runtimes.
 */
if (!function_exists('renderAsepPedagogicalProof')) {
    function renderAsepPedagogicalProof($config = array())
    {
        if (!is_array($config)) $config = array();

        $inputId = isset($config['input_id']) ? (string) $config['input_id'] : 'pedagogical';
        $context = isset($config['context']) ? (string) $config['context'] : 'general-pe-2026';
        $panelId = isset($config['panel_id']) ? (string) $config['panel_id'] : $inputId . 'Proof';
        $methodId = isset($config['method_id']) ? (string) $config['method_id'] : $inputId . 'ProofMethod';
        $statusId = isset($config['status_id']) ? (string) $config['status_id'] : $inputId . 'ProofStatus';
        $link = !isset($config['show_guide_link']) || (bool) $config['show_guide_link'];

        $profiles = array(
            'general-pe-2026' => array(
                'label' => '1ΓΕ/2026 & 2ΓΕ/2026',
                'aei_cutoff' => '2026–2027',
                'allow_three_month' => false,
                'legal' => 'Κεφάλαιο Δ΄ και Παράρτημα Ε΄ των 1ΓΕ/2026 και 2ΓΕ/2026.'
            ),
            '2ea-2025' => array(
                'label' => '2ΕΑ/2025',
                'aei_cutoff' => '2023–2024',
                'allow_three_month' => false,
                'legal' => 'Κεφάλαιο Δ΄ / Παράρτημα Ε΄ της 2ΕΑ/2025, όπως διορθώθηκε με ΦΕΚ 24/02.06.2025.'
            ),
            '3ea-2025' => array(
                'label' => '3ΕΑ/2025',
                'aei_cutoff' => '2023–2024',
                'allow_three_month' => false,
                'legal' => 'Κεφάλαιο Δ΄ / Παράρτημα Ε΄ της 3ΕΑ/2025, όπως διορθώθηκε με ΦΕΚ 25/02.06.2025.'
            ),
            '4ea-2025' => array(
                'label' => '4ΕΑ/2025',
                'aei_cutoff' => '2023–2024',
                'allow_three_month' => true,
                'legal' => 'Κεφάλαιο Δ΄ και Παράρτημα Ε΄ της 4ΕΑ/2025. Για κλάδους ΤΕ–ΔΕ προβλέπεται και η ειδική βεβαίωση τρίμηνης παιδαγωγικής επιμόρφωσης ΑΣΠΑΙΤΕ/πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ.'
            ),
            '1gt-2024' => array(
                'label' => '1ΓΤ/2024',
                'aei_cutoff' => '2022–2023',
                'allow_three_month' => true,
                'legal' => 'Κεφάλαιο Δ΄ και Παράρτημα Ε΄ της 1ΓΤ/2024. Για κλάδους ΤΕ–ΔΕ προβλέπεται και βεβαίωση τρίμηνης παιδαγωγικής επιμόρφωσης ΑΣΠΑΙΤΕ/πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ.'
            )
        );
        if (!isset($profiles[$context])) $context = 'general-pe-2026';
        $profile = $profiles[$context];

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) $flags = $flags | ENT_SUBSTITUTE;
        $h = function ($value) use ($flags) { return htmlspecialchars((string) $value, $flags, 'UTF-8'); };
        ?>
<div class="asep-pedagogical-proof" data-component="asep-pedagogical-proof" data-input-id="<?php echo $h($inputId); ?>" data-context="<?php echo $h($context); ?>" data-aei-cutoff="<?php echo $h($profile['aei_cutoff']); ?>">
  <div class="checkrow check asep-pedagogical-control">
    <input type="checkbox" id="<?php echo $h($inputId); ?>">
    <label for="<?php echo $h($inputId); ?>">Πιστοποιημένη Παιδαγωγική και Διδακτική Επάρκεια
      <small>Δεν προσθέτει από μόνη της μόρια· δίνει πρόταξη έναντι υποψηφίων που δεν τη διαθέτουν. Αν ο τίτλος που την αποδεικνύει είναι ταυτόχρονα μοριοδοτούμενος, μπορεί να μοριοδοτείται χωριστά σύμφωνα με την προκήρυξη.</small>
    </label>
  </div>

  <div class="asep-pedagogical-proof-panel hidden" id="<?php echo $h($panelId); ?>" data-pedagogical-proof-panel aria-hidden="true">
    <div class="asep-pedagogical-proof-title">Παιδαγωγική &amp; Διδακτική Επάρκεια — αποδεικτικό</div>
    <div class="asep-pedagogical-proof-question">Με ποιο αποδεικτικό θεμελιώνεις την Π.Δ.Ε. για <?php echo $h($profile['label']); ?>;</div>
    <label class="asep-pedagogical-proof-method-label" for="<?php echo $h($methodId); ?>">Κατηγορία αποδεικτικού</label>
    <select id="<?php echo $h($methodId); ?>" data-pedagogical-proof-method>
      <option value="">— Επίλεξε το αποδεικτικό που διαθέτεις —</option>
      <option value="aei-certificate">Βεβαίωση Α.Ε.Ι. μετά από ομάδα μαθημάτων / ειδικό πρόγραμμα σπουδών</option>
      <option value="education-degree">Μεταπτυχιακός ή διδακτορικός τίτλος στις επιστήμες της αγωγής</option>
      <option value="old-certificate">Πιστοποιητικό παιδαγωγικής επάρκειας ν. 3027/2002</option>
      <option value="pedagogical-department">Πτυχίο Παιδαγωγικού Τμήματος / άλλου ρητά προβλεπόμενου Παιδαγωγικού Τμήματος</option>
      <option value="aspaite-degree">Πτυχίο Α.Σ.ΠΑΙ.Τ.Ε.</option>
      <option value="aspaite-eppaik">Πιστοποιητικό ΕΠΠΑΙΚ Α.Σ.ΠΑΙ.Τ.Ε. / πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ</option>
<?php if ($profile['allow_three_month']): ?>
      <option value="aspaite-three-month">Βεβαίωση τρίμηνης Παιδαγωγικής Επιμόρφωσης Α.Σ.ΠΑΙ.Τ.Ε. / πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ</option>
<?php endif; ?>
      <option value="article99">Πιστοποιητικό Π.Δ.Ε. άρθρου 99 ν. 4957/2022</option>
      <option value="epath">Πτυχίο Ε.Π.Α.Θ.</option>
      <option value="professor-school">Πτυχίο πρώην καθηγητικής σχολής</option>
      <option value="other">Άλλο / δεν είμαι βέβαιος για την κατηγορία</option>
    </select>
    <div class="asep-pedagogical-proof-status neutral" id="<?php echo $h($statusId); ?>" data-pedagogical-proof-status>Επίλεξε την κατηγορία αποδεικτικού.</div>

    <details class="asep-pedagogical-proof-details">
      <summary>Χρονικές προϋποθέσεις και σημεία προσοχής</summary>
      <ul>
        <li><strong>Βεβαίωση Α.Ε.Ι.:</strong> για το συγκεκριμένο πλαίσιο, η μεταβατική δυνατότητα αφορά αποφοίτους που είχαν εισαχθεί έως και το ακαδημαϊκό έτος <strong><?php echo $h($profile['aei_cutoff']); ?></strong> σε Τμήμα/Σχολή που χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής.</li>
        <li><strong>Ε.Π.Α.Θ.:</strong> η σχετική περίπτωση απαιτεί ημερομηνία κτήσης πριν από <strong>12/06/2018</strong>.</li>
        <li><strong>Πρώην καθηγητική σχολή:</strong> η a priori Π.Δ.Ε. συνδέεται με εισαγωγή έως <strong>2014–2015</strong> ή κτήση πτυχίου έως <strong>2017–2018</strong>. Για μεταγενέστερες περιπτώσεις χρειάζεται άλλο αποδεικτικό.</li>
<?php if ($profile['allow_three_month']): ?>
        <li><strong>Τρίμηνη ΑΣΠΑΙΤΕ:</strong> η ειδική βεβαίωση εμφανίζεται εδώ επειδή προβλέπεται για κλάδους δευτεροβάθμιας εκπαίδευσης ΤΕ–ΔΕ στο συγκεκριμένο πλαίσιο.</li>
<?php endif; ?>
        <li>Η αναγνώριση επαγγελματικής ισοδυναμίας τίτλου από μόνη της δεν πιστοποιεί Π.Δ.Ε.</li>
      </ul>
    </details>

    <div class="asep-pedagogical-proof-legal"><strong><?php echo $h($profile['legal']); ?></strong> Ο έλεγχος είναι ενημερωτικός· η τελική αποδοχή του δικαιολογητικού γίνεται από ΑΣΕΠ/ΟΠΣΥΔ και τα αρμόδια όργανα.</div>
<?php if ($link): ?>
    <div class="asep-pedagogical-proof-guide"><a href="paidagogiki-eparkeia.php">Αναλυτικός έλεγχος Παιδαγωγικής &amp; Διδακτικής Επάρκειας →</a></div>
<?php endif; ?>
  </div>
</div>
        <?php
    }
}
