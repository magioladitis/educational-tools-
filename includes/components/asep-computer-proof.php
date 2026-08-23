<?php
/**
 * Shared ASEP presentation-only component for knowledge of Informatics / computer use.
 *
 * Scope: ASEP calculators only.
 * This component does NOT calculate points and does NOT implement specialty rules.
 * In particular, PE86 exclusions remain in each calculator/calculation module.
 * The quick proof check only helps the user identify an accepted proof route.
 *
 * Conservative syntax for compatibility with older PHP runtimes.
 */
if (!function_exists('renderAsepComputerProof')) {
    function renderAsepComputerProof($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }

        $inputId = isset($config['input_id']) ? (string) $config['input_id'] : 'computer';
        $controlType = isset($config['control_type']) ? (string) $config['control_type'] : 'checkbox';
        $pointsText = isset($config['points_text']) ? trim((string) $config['points_text']) : '';
        $restrictionNote = isset($config['restriction_note']) ? trim((string) $config['restriction_note']) : '';
        $proofId = isset($config['proof_id']) ? (string) $config['proof_id'] : 'computerProof';
        $methodId = isset($config['method_id']) ? (string) $config['method_id'] : 'computerProofMethod';
        $statusId = isset($config['status_id']) ? (string) $config['status_id'] : 'computerProofStatus';

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $h = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };

        $small = 'Στα τρία αντικείμενα: επεξεργασία κειμένου, υπολογιστικά φύλλα και υπηρεσίες διαδικτύου.';
        if ($pointsText !== '') {
            $small .= ' ' . $pointsText . '.';
        }
        if ($restrictionNote !== '') {
            $small .= ' ' . $restrictionNote;
        }
        ?>
<div class="asep-computer-proof" data-component="asep-computer-proof" data-input-id="<?php echo $h($inputId); ?>">
<?php if ($controlType === 'select'): ?>
  <div class="field asep-computer-control">
    <label for="<?php echo $h($inputId); ?>">Γνώση πληροφορικής ή χειρισμού Η/Υ
      <small><?php echo $h($small); ?></small>
    </label>
    <select id="<?php echo $h($inputId); ?>">
      <option value="no">Όχι</option>
      <option value="yes">Ναι</option>
    </select>
  </div>
<?php else: ?>
  <div class="checkrow check asep-computer-control">
    <input type="checkbox" id="<?php echo $h($inputId); ?>">
    <label for="<?php echo $h($inputId); ?>">Γνώση πληροφορικής ή χειρισμού Η/Υ
      <small><?php echo $h($small); ?></small>
    </label>
  </div>
<?php endif; ?>

  <div class="asep-computer-proof-panel hidden" id="<?php echo $h($proofId); ?>" data-computer-proof-panel aria-hidden="true">
    <div class="asep-computer-proof-title">Γνώση πληροφορικής ή χειρισμού Η/Υ — τρόπος απόδειξης</div>
    <div class="asep-computer-proof-question">Με ποιον από τους παρακάτω τρόπους αποδεικνύεις τη γνώση πληροφορικής ή χειρισμού Η/Υ;</div>
    <label class="asep-computer-proof-method-label" for="<?php echo $h($methodId); ?>">Τρόπος απόδειξης</label>
    <select id="<?php echo $h($methodId); ?>" data-computer-proof-method>
      <option value="">— Επίλεξε τον τρόπο που διαθέτεις —</option>
      <option value="certificate">1. Πιστοποιητικό γνώσης πληροφορικής ή χειρισμού Η/Υ</option>
      <option value="informatics-title">2. Τίτλος σπουδών ειδικότητας Πληροφορικής / γνώσης χειρισμού Η/Υ</option>
      <option value="four-courses">3. Τίτλος σπουδών με τουλάχιστον 4 σχετικά μαθήματα</option>
      <option value="state-certificate">4. Κρατικό Πιστοποιητικό Πληροφορικής</option>
      <option value="esdda">5. Πιστοποιητικό αποφοίτησης από την Ε.Σ.Δ.Δ.Α.</option>
      <option value="tpe-a">6. Πιστοποίηση εκπαιδευτικών Τ.Π.Ε. Α΄ επιπέδου</option>
    </select>
    <div class="asep-computer-proof-status neutral" id="<?php echo $h($statusId); ?>" data-computer-proof-status>Επίλεξε τον τρόπο απόδειξης που διαθέτεις.</div>

    <details class="asep-computer-proof-details">
      <summary>Δες αναλυτικά τους 6 αποδεκτούς τρόπους απόδειξης</summary>
      <ol>
        <li>Με πιστοποιητικά γνώσης πληροφορικής ή χειρισμού Η/Υ.</li>
        <li>Με τίτλους σπουδών τριτοβάθμιας, μεταδευτεροβάθμιας ή δευτεροβάθμιας εκπαίδευσης, ειδικότητας Πληροφορικής ή γνώσης χειρισμού Η/Υ, όπως αυτοί προσδιορίζονται για τους κλάδους ΠΕ Πληροφορικής, ΤΕ Πληροφορικής και ΔΕ Πληροφορικής στους Πίνακες 1, 2 και 3 αντίστοιχα του Παραρτήματος Α΄ του Π.Δ. 85/2022 (Α΄ 232).</li>
        <li>Με τίτλους σπουδών, προπτυχιακούς ή/και μεταπτυχιακούς ή/και διδακτορικούς, Πανεπιστημιακής ή/και Τεχνολογικής Εκπαίδευσης, από την αναλυτική βαθμολογία των οποίων προκύπτει ότι ο/η υποψήφιος/α έχει παρακολουθήσει τουλάχιστον τέσσερα μαθήματα, υποχρεωτικά ή κατ’ επιλογή, Πληροφορικής ή γνώσης χειρισμού Η/Υ.</li>
        <li>Με Κρατικό Πιστοποιητικό Πληροφορικής (άρθρο 28 ν. 4653/2020).</li>
        <li>Με πιστοποιητικό αποφοίτησης από την Εθνική Σχολή Δημόσιας Διοίκησης και Αυτοδιοίκησης (Ε.Σ.Δ.Δ.Α.).</li>
        <li>Με βεβαίωση πιστοποίησης δεξιοτήτων και γνώσεων στις Τεχνολογίες Πληροφορίας και Επικοινωνιών (Τ.Π.Ε.) Α΄ επιπέδου του Υπουργείου Παιδείας, Θρησκευμάτων και Αθλητισμού.</li>
      </ol>
    </details>
    <div class="asep-computer-proof-note">Ο γρήγορος έλεγχος αφορά μόνο τον <strong>τρόπο απόδειξης</strong>. Η μοριοδότηση και τυχόν ειδικοί περιορισμοί της προκήρυξης εφαρμόζονται χωριστά — για παράδειγμα, όπου προβλέπεται, η γνώση Η/Υ δεν μοριοδοτείται στον ΠΕ86.</div>
  </div>
</div>
        <?php
    }
}
