<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Τι δικαιολογητικά χρειάζομαι για τίτλους σπουδών;</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body class="edu-ui edu-guide-standard">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Τι δικαιολογητικά χρειάζομαι για τίτλους σπουδών;</h1>
<p class="intro">
    Το εργαλείο παρέχει <strong>ενδεικτική</strong> καθοδήγηση για μεταπτυχιακούς τίτλους,
    διδακτορικά διπλώματα, integrated master και τίτλους σπουδών της αλλοδαπής.
  </p>
</section>

  <div class="question">
    <label for="titleType">Τι θέλεις να δηλώσεις;</label>
    <select id="titleType">
      <option value="">-- Επιλογή --</option>
      <option value="none">Δεν δηλώνω μεταπτυχιακό, διδακτορικό ή integrated master</option>
      <option value="msc_gr">Μεταπτυχιακός τίτλος σπουδών ημεδαπής</option>
      <option value="phd_gr">Διδακτορικό δίπλωμα ημεδαπής</option>
      <option value="integrated_master">Ενιαίος και αδιάσπαστος τίτλος σπουδών μεταπτυχιακού επιπέδου / integrated master</option>
	  <option value="joint_msc">Μεταπτυχιακός τίτλος Ειδίκευσης κοινού Π.Μ.Σ. μεταξύ Πανεπιστημίων ημεδαπής και αλλοδαπής</option>
      <option value="msc_foreign">Μεταπτυχιακός τίτλος σπουδών αλλοδαπής</option>
      <option value="phd_foreign">Διδακτορικό δίπλωμα αλλοδαπής</option>
    </select>
  </div>

  <div id="greekTitleQuestions" class="hidden">
    <div class="question">
      <label for="greekTitleStatus">Έχει εκδοθεί ο τίτλος ή εκκρεμεί ορκωμοσία;</label>
      <select id="greekTitleStatus">
        <option value="">-- Επιλογή --</option>
        <option value="issued">Έχει εκδοθεί ο τίτλος</option>
        <option value="pending_oath">Εκκρεμεί η ορκωμοσία / δεν έχει ακόμη απονεμηθεί επίσημα ο τίτλος</option>
      </select>
    </div>
  </div>

<div id="jointMscQuestions" class="hidden">
  <div class="question">
    <label for="jointMscStatus">
      Έχει εκδοθεί ο μεταπτυχιακός τίτλος ή εκκρεμεί η απονομή / ορκωμοσία;
    </label>
    <select id="jointMscStatus">
      <option value="">-- Επιλογή --</option>
      <option value="issued">Έχει εκδοθεί ο τίτλος</option>
      <option value="pending_oath">Εκκρεμεί η απονομή / ορκωμοσία</option>
    </select>
  </div>

  <div class="question">
    <label for="jointMscProof">
      Προκύπτει από τον τίτλο ή από βεβαίωση ότι πρόκειται για κοινό Πρόγραμμα Μεταπτυχιακών Σπουδών μεταξύ Πανεπιστημίου της ημεδαπής και Πανεπιστημίου της αλλοδαπής;
    </label>
    <select id="jointMscProof">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>
</div>

  <div id="integratedMasterQuestions" class="hidden">
    <div class="question">
      <label for="integratedDecision">
        Έχει εκδοθεί και δημοσιευθεί η διαπιστωτική απόφαση υπαγωγής του Τμήματος στις διατάξεις για integrated master μέχρι τη λήξη της προθεσμίας;
      </label>
      <select id="integratedDecision">
        <option value="">-- Επιλογή --</option>
        <option value="yes">Ναι</option>
        <option value="no">Όχι</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>

    <div class="question">
      <label for="departmentNameDifferent">
        Το Τμήμα σου έχει διαφορετική ονομασία από αυτή που αναφέρεται στη σχετική διαπιστωτική απόφαση;
      </label>
      <select id="departmentNameDifferent">
        <option value="">-- Επιλογή --</option>
        <option value="yes">Ναι</option>
        <option value="no">Όχι</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>
  </div>

  <div id="foreignTitleQuestions" class="hidden">
    <div class="question">
      <label for="foreignRecognition">
        Τι ισχύει για την αναγνώριση του τίτλου σπουδών της αλλοδαπής;
      </label>
      <select id="foreignRecognition">
        <option value="">-- Επιλογή --</option>
        <option value="academic_recognition">
          Έχω πράξη αναγνώρισης ακαδημαϊκής ισοδυναμίας / ισοτιμίας / αντιστοιχίας από Δ.Ο.Α.Τ.Α.Π.
        </option>
        <option value="exception">
          Εμπίπτω σε εξαίρεση και διαθέτω άλλη πράξη ή απόφαση επαγγελματικής αναγνώρισης
        </option>
        <option value="pending">
          Έχω υποβάλει αίτηση αναγνώρισης, αλλά εκκρεμεί
        </option>
        <option value="none">
          Δεν έχω αναγνώριση και δεν έχω υποβάλει αίτηση
        </option>
        <option value="unknown">
          Δεν είμαι σίγουρος/η
        </option>
      </select>
    </div>

    <div id="foreignExemptionQuestions" class="hidden">
      <div class="question">
        <label for="foreignExemption">
          Σε ποια περίπτωση εξαίρεσης ανήκεις;
        </label>

        <select id="foreignExemption">
          <option value="">-- Επιλογή --</option>

          <option value="saeitte">
            Έχω πτυχίο ή δίπλωμα ανώτατης εκπαίδευσης από χώρα της Ευρωπαϊκής Ένωσης και πράξη αναγνώρισης επαγγελματικής ισοτιμίας από το Σ.Α.Ε.Ι.Τ.Τ.Ε.
          </option>

          <option value="saep_professional_qualifications">
            Έχω απόφαση αναγνώρισης επαγγελματικών προσόντων από το Σ.Α.Ε.Π.
          </option>

          <option value="saep_professional_equivalence">
            Έχω απόφαση αναγνώρισης επαγγελματικής ισοδυναμίας τίτλου τυπικής ανώτατης εκπαίδευσης από το Σ.Α.Ε.Π.
          </option>

          <option value="saetek">
            Έχω τίτλο μεταδευτεροβάθμιας εκπαίδευσης από χώρα της Ευρωπαϊκής Ένωσης και απόφαση αναγνώρισης επαγγελματικής εκπαίδευσης από το Σ.Α.Ε.Τ.Ε.Κ.
          </option>

          <option value="automatic_recognition">
            Έχω άδεια άσκησης επαγγέλματος βάσει αυτόματης αναγνώρισης διπλωμάτων / πιστοποιητικών / τίτλων από αρμόδια εθνική αρχή
          </option>

          <option value="ateen_professional_qualifications">
            Έχω απόφαση αναγνώρισης επαγγελματικών προσόντων από το Α.Τ.Ε.Ε.Ν.
          </option>

          <option value="ateen_professional_equivalence">
            Έχω απόφαση αναγνώρισης επαγγελματικής ισοδυναμίας τίτλου τυπικής ανώτατης εκπαίδευσης από το Α.Τ.Ε.Ε.Ν.
          </option>

          <option value="unknown">
            Δεν είμαι σίγουρος/η ποια περίπτωση με αφορά
          </option>
        </select>
      </div>
    </div>
  </div>

  <button id="showDocumentsBtn" type="button" class="guide-submit">Εμφάνιση δικαιολογητικών</button>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό και βασίζεται στις οδηγίες για μεταπτυχιακούς,
    διδακτορικούς και integrated master τίτλους. Δεν αντικαθιστά την επίσημη προκήρυξη,
    τις οδηγίες του Α.Σ.Ε.Π., τον έλεγχο του Ο.Π.ΣΥ.Δ. ή τον έλεγχο των αρμόδιων υπηρεσιών.
  </p>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/study-title-documents-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>

<?php sourceCardStart(); ?>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>, ιδίως τα κεφάλαια για τα απαιτούμενα δικαιολογητικά και τις προϋποθέσεις αναγνώρισης τίτλων σπουδών ημεδαπής και αλλοδαπής. Το εργαλείο είναι βοηθητικός οδηγός και δεν υποκαθιστά τον έλεγχο Ο.Π.ΣΥ.Δ./Α.Σ.Ε.Π.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78701', '2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>