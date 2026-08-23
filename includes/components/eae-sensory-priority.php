<?php
/**
 * Shared EAE priority and proof component for Greek Sign Language (ENG)
 * and Greek Braille (EGB). Presentation/proof guidance only; no points.
 * Kept compatible with PHP 5.6.
 */
if (!function_exists('renderEaeSensoryPriority')) {
    function renderEaeSensoryPriority($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }

        $engEnabled = !isset($config['eng_enabled']) || (bool) $config['eng_enabled'];
        $brailleEnabled = !isset($config['braille_enabled']) || (bool) $config['braille_enabled'];
        $engId = isset($config['eng_id']) ? (string) $config['eng_id'] : 'signLanguage';
        $brailleId = isset($config['braille_id']) ? (string) $config['braille_id'] : 'braille';
        $engPanelId = isset($config['eng_panel_id']) ? (string) $config['eng_panel_id'] : $engId . 'Proof';
        $braillePanelId = isset($config['braille_panel_id']) ? (string) $config['braille_panel_id'] : $brailleId . 'Proof';
        $context = isset($config['context']) ? (string) $config['context'] : '';

        $priorityLegal = '';
        $proofLegal = 'Αποδεκτοί τρόποι απόδειξης επάρκειας: έλεγξε την αντίστοιχη προκήρυξη Α.Σ.Ε.Π.';
        if ($context === '1ea-2025') {
            $priorityLegal = 'Προτεραιότητα Ε.Β.Π. για την υποστήριξη κωφών και βαρήκοων μαθητών: παρ. 2 άρθρου 60 ν. 4589/2019.';
            $proofLegal = 'Αποδεκτοί τρόποι απόδειξης ΕΝΓ: Κεφάλαιο Δ΄, σημείο 4 της 1ΕΑ/2025.';
        } elseif ($context === '2ea-2025') {
            $priorityLegal = 'Προτεραιότητα μελών Ε.Ε.Π. για μαθητές με προβλήματα όρασης ή κωφούς/βαρήκοους μαθητές: παρ. 1 άρθρου 59 ν. 4589/2019.';
            $proofLegal = 'Αποδεκτοί τρόποι απόδειξης ΕΝΓ/ΕΓΒ: Κεφάλαιο Δ΄, σημείο 6 της 2ΕΑ/2025.';
        } elseif ($context === '3ea-2025') {
            $priorityLegal = 'Προτεραιότητα εκπαιδευτικών Ε.Α.Ε. για μαθητές με προβλήματα όρασης ή κωφούς/βαρήκοους μαθητές: παρ. 3 άρθρου 7 ν. 3699/2008.';
            $proofLegal = 'Αποδεκτοί τρόποι απόδειξης ΕΝΓ/ΕΓΒ: Κεφάλαιο Δ΄, σημείο 7 της 3ΕΑ/2025.';
        } elseif ($context === '4ea-2025') {
            $priorityLegal = 'Προτεραιότητα εκπαιδευτικών Ε.Α.Ε. για μαθητές με προβλήματα όρασης ή κωφούς/βαρήκοους μαθητές: παρ. 3 άρθρου 7 ν. 3699/2008.';
            $proofLegal = 'Αποδεκτοί τρόποι απόδειξης ΕΝΓ/ΕΓΒ: Κεφάλαιο Δ΄, σημείο 7 της 4ΕΑ/2025.';
        }

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $h = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };
        ?>
<div class="eae-sensory-priority" data-component="eae-sensory-priority">
  <div class="field-grid eae-sensory-controls">
<?php if ($brailleEnabled): ?>
    <div class="checkrow eae-sensory-control">
      <input type="checkbox" id="<?php echo $h($brailleId); ?>">
      <label for="<?php echo $h($brailleId); ?>">Πιστοποιημένη επάρκεια Ελληνικής γραφής Braille (ΕΓΒ)
        <small>Ειδική προτεραιότητα για την εκπαίδευση μαθητών με προβλήματα όρασης. Δεν προσθέτει μόρια.</small>
      </label>
    </div>
<?php endif; ?>
<?php if ($engEnabled): ?>
    <div class="checkrow eae-sensory-control">
      <input type="checkbox" id="<?php echo $h($engId); ?>">
      <label for="<?php echo $h($engId); ?>">Πιστοποιημένη επάρκεια Ελληνικής Νοηματικής Γλώσσας (ΕΝΓ)
        <small>Ειδική προτεραιότητα για την εκπαίδευση κωφών και βαρήκοων μαθητών. Δεν προσθέτει μόρια.</small>
      </label>
    </div>
<?php endif; ?>
  </div>

<?php if ($engEnabled): ?>
  <div class="eae-sensory-panel hidden" id="<?php echo $h($engPanelId); ?>" data-eae-sensory-panel data-kind="eng" data-input-id="<?php echo $h($engId); ?>" aria-hidden="true">
    <div class="eae-sensory-title">Έλεγχος πιστοποιητικού/βεβαίωσης ΕΝΓ</div>
    <label class="eae-sensory-method-label" for="<?php echo $h($engPanelId); ?>Method">Φορέας έκδοσης</label>
    <select id="<?php echo $h($engPanelId); ?>Method" data-eae-sensory-method>
      <option value="">— Επίλεξε τον φορέα έκδοσης —</option>
      <option value="omke">Ομοσπονδία Κωφών Ελλάδος (ΟΜΚΕ)</option>
      <option value="patras">Πανεπιστήμιο Πατρών — Μονάδα Αγωγής Κωφών</option>
      <option value="other">Άλλος φορέας / δεν είμαι βέβαιος</option>
    </select>
    <div class="eae-sensory-status neutral" data-eae-sensory-status>Επίλεξε τον φορέα έκδοσης του πιστοποιητικού ή της βεβαίωσης.</div>
    <details class="eae-sensory-details">
      <summary>Αποδεκτοί τρόποι απόδειξης ΕΝΓ</summary>
      <ul>
        <li>Πιστοποιητικό/βεβαίωση επάρκειας από την Ομοσπονδία Κωφών Ελλάδος (ΟΜΚΕ).</li>
        <li>Πιστοποιητικό/βεβαίωση από το Πανεπιστήμιο Πατρών, Τμήμα Επιστημών της Εκπαίδευσης και Κοινωνικής Εργασίας, Μονάδα Αγωγής Κωφών.</li>
      </ul>
    </details>
  </div>
<?php endif; ?>

<?php if ($brailleEnabled): ?>
  <div class="eae-sensory-panel hidden" id="<?php echo $h($braillePanelId); ?>" data-eae-sensory-panel data-kind="braille" data-input-id="<?php echo $h($brailleId); ?>" aria-hidden="true">
    <div class="eae-sensory-title">Έλεγχος πιστοποιητικού/βεβαίωσης Ελληνικής γραφής Braille</div>
    <label class="eae-sensory-method-label" for="<?php echo $h($braillePanelId); ?>Method">Φορέας ή τρόπος απόδειξης</label>
    <select id="<?php echo $h($braillePanelId); ?>Method" data-eae-sensory-method>
      <option value="">— Επίλεξε τον φορέα ή τον τρόπο απόδειξης —</option>
      <option value="keat">Κέντρο Εκπαίδευσης και Αποκατάστασης Τυφλών (ΚΕΑΤ)</option>
      <option value="ilios">Σχολή Τυφλών Βορείου Ελλάδος «Ο Ήλιος» / ΚΕΑΤ Θεσσαλονίκης</option>
      <option value="uth">Πανεπιστήμιο Θεσσαλίας — Εργαστήριο γραφής και ανάγνωσης στον κώδικα Braille</option>
      <option value="uom">Πανεπιστήμιο Μακεδονίας — Εργαστήριο Εκμάθησης της Braille</option>
      <option value="other">Άλλος φορέας / δεν είμαι βέβαιος</option>
    </select>
    <div class="eae-sensory-status neutral" data-eae-sensory-status>Επίλεξε τον φορέα ή τον τρόπο απόδειξης που διαθέτεις.</div>
    <details class="eae-sensory-details">
      <summary>Αποδεκτοί τρόποι απόδειξης Braille</summary>
      <ul>
        <li>Πιστοποιητικό επάρκειας ΕΓΒ από το ΚΕΑΤ.</li>
        <li>Πιστοποιητικό από τη Σχολή Τυφλών Βορείου Ελλάδος «Ο Ήλιος», πλέον παράρτημα του ΚΕΑΤ Θεσσαλονίκης.</li>
        <li>Βεβαίωση του Παιδαγωγικού Τμήματος Ειδικής Αγωγής του Πανεπιστημίου Θεσσαλίας για το «Εργαστήριο γραφής και ανάγνωσης στον κώδικα Braille».</li>
        <li>Βεβαίωση του Τμήματος Εκπαιδευτικής και Κοινωνικής Πολιτικής του Πανεπιστημίου Μακεδονίας για το «Εργαστήριο Εκμάθησης της Braille».</li>
      </ul>
    </details>
  </div>
<?php endif; ?>

  <div class="eae-sensory-legal">
<?php if ($priorityLegal !== ''): ?>
    <strong><?php echo $h($priorityLegal); ?></strong><br>
<?php endif; ?>
    <?php echo $h($proofLegal); ?> Η επάρκεια ΕΝΓ/ΕΓΒ ρυθμίζεται στην παρ. 3 άρθρου 7 ν. 3699/2008, όπως αντικαταστάθηκε με την παρ. 10 άρθρου 28 ν. 4186/2013 και συμπληρώθηκε με την παρ. 2 άρθρου 11 ν. 4452/2017. Ο έλεγχος είναι ενημερωτικός και δεν υποκαθιστά τον επίσημο έλεγχο των δικαιολογητικών.
  </div>
</div>
        <?php
    }
}
