<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Οδηγός ένστασης 1ΓΕ/2026 &amp; 2ΓΕ/2026</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-guide-standard edu-guide-objection">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box edu-modernized">
  <section class="hero edu-legacy-hero">
<h1>Οδηγός ένστασης 1ΓΕ/2026 &amp; 2ΓΕ/2026</h1>
<p class="intro">Γρήγορος, διαδραστικός οδηγός για την υποβολή ένστασης κατά των προσωρινών πινάκων εκπαιδευτικών.</p>
</section>

  <div class="deadline-card">
    <strong>📅 Προθεσμία ενστάσεων</strong>
    <div class="deadline-line">Από <b>Τετάρτη 12 Αυγούστου 2026, ώρα 08:00</b> έως και <b>Παρασκευή 21 Αυγούστου 2026, ώρα 14:00</b> (ώρα Ελλάδας).</div>
    <div id="deadlineStatus" class="status" role="status" aria-live="polite"></div>
  </div>

  <div class="quick-card">
    <strong>📘 Επίσημες οδηγίες ΑΣΕΠ</strong>
    Για λεπτομέρειες μπορείς να ανοίξεις απευθείας το επίσημο εγχειρίδιο ηλεκτρονικής ένστασης εκπαιδευτικών.
    <br><a class="inline-link" href="https://info.asep.gr/sites/default/files/2023-10/manual_enstasi_ekpaideutikon.pdf" target="_blank" rel="noopener">Άνοιγμα επίσημου εγχειριδίου ΑΣΕΠ</a>
  </div>

  <div class="question">
    <label for="objectionReason"><span class="question-number">1</span>Ποιος είναι ο βασικός λόγος της ένστασής σου;</label>
    <select id="objectionReason">
      <option value="">-- Επιλογή --</option>
      <option value="points">Λανθασμένος υπολογισμός μορίων</option>
      <option value="rejection">Απόρριψη / μη ένταξη στον πίνακα</option>
      <option value="missing">Δεν λήφθηκε υπόψη προσόν ή δικαιολογητικό</option>
      <option value="foreign">Αναγνώριση τίτλου αλλοδαπής</option>
      <option value="personal">Λάθος προσωπικά ή υπηρεσιακά στοιχεία</option>
      <option value="other">Άλλος λόγος</option>
    </select>
  </div>

  <div id="pointsTools" class="quick-card hidden">
    <strong>🧮 Έλεγξε ξανά τα μόριά σου</strong>
    Αν η ένσταση αφορά λανθασμένο υπολογισμό μορίων, μπορείς πρώτα να κάνεις ανεξάρτητο επανυπολογισμό με το εργαλείο 1ΓΕ/2026 &amp; 2ΓΕ/2026.
    <br><a class="inline-link" href="ypologismos-morion.php" target="_blank" rel="noopener">Άνοιγμα υπολογιστή μορίων</a>
  </div>

  <div id="foreignQuestions" class="hidden">
    <div class="question">
      <label for="recognitionStatus">Έχει εκδοθεί πιστοποιητικό / πράξη αναγνώρισης του τίτλου αλλοδαπής;</label>
      <select id="recognitionStatus">
        <option value="">-- Επιλογή --</option>
        <option value="issued">Ναι, έχει εκδοθεί</option>
        <option value="pending">Όχι, η αναγνώριση εκκρεμεί</option>
        <option value="none">Δεν έχει γίνει αίτηση αναγνώρισης</option>
      </select>
    </div>

    <div id="recognitionDateQuestion" class="question hidden">
      <label for="recognitionDate">Η πράξη αναγνώρισης έχει εκδοθεί έως τη λήξη της προθεσμίας ενστάσεων;</label>
      <select id="recognitionDate">
        <option value="">-- Επιλογή --</option>
        <option value="yes">Ναι</option>
        <option value="no">Όχι</option>
      </select>
    </div>
  </div>

  <div class="question">
    <label for="submissionMode"><span class="question-number">2</span>Ποια είναι η κατάσταση της ένστασής σου;</label>
    <select id="submissionMode">
      <option value="">-- Επιλογή --</option>
      <option value="first">Δεν έχω υποβάλει ακόμη ένσταση</option>
      <option value="resubmit">Έχω ήδη υποβάλει και θέλω να τη διορθώσω / επανυποβάλω</option>
      <option value="recalled">Έχω ήδη κάνει Ανάκληση και θα την υποβάλω ξανά</option>
    </select>
  </div>

  <div id="resubmissionInfo" class="quick-card hidden">
    <strong>🔁 Επανυποβολή / Ανάκληση ένστασης</strong>
    <span id="resubmissionText"></span>
    <br><a class="inline-link" href="https://info.asep.gr/sites/default/files/2023-10/e-paravolo_enstasi.pdf" target="_blank" rel="noopener">Επίσημες οδηγίες e-Παραβόλου ΑΣΕΠ</a>
  </div>

  <div class="question">
    <label for="paravoloStatus"><span class="question-number">3</span>Έχεις εκδώσει και πληρώσει το παράβολο ένστασης των 50€;</label>
    <select id="paravoloStatus">
      <option value="">-- Επιλογή --</option>
      <option value="paid">Ναι, είναι πληρωμένο</option>
      <option value="issued_not_paid">Έχει εκδοθεί, αλλά δεν έχει πληρωθεί</option>
      <option value="no">Όχι, δεν το έχω εκδώσει</option>
    </select>
  </div>

  <div id="paravoloValidator" class="question hidden">
    <label for="paravoloCode"><span class="question-number">4</span>Έλεγχος μορφής κωδικού e-Παραβόλου</label>
    <input id="paravoloCode" type="text" inputmode="numeric" autocomplete="off" maxlength="32" placeholder="Π.χ. 12345678901234567890">
    <div class="field-hint">Επικόλλησε τον κωδικό που σκοπεύεις να καταχωρίσεις στην ένσταση. Το εργαλείο ελέγχει μόνο τη <strong>μορφή</strong> του κωδικού — όχι αν είναι έγκυρος ή πληρωμένος στο σύστημα του ΑΣΕΠ.</div>
    <div id="paravoloValidation" class="validation-message neutral" role="status" aria-live="polite">Ο σωστός κωδικός e-Παραβόλου έχει ακριβώς 20 ψηφία, χωρίς κενά.</div>
  </div>

  <div class="paravolo-summary">
    <strong>Στοιχεία e-Παραβόλου ένστασης</strong>
    <ul>
      <li><strong>Φορέας Δημοσίου:</strong> Ανώτατο Συμβούλιο Επιλογής Προσωπικού (ΑΣΕΠ)</li>
      <li><strong>Κατηγορία Παραβόλου:</strong> Υποβολή ένστασης</li>
      <li><strong>Τύπος Παραβόλου:</strong> <b>[1236]</b> Υποβολή ένστασης για όλες τις διαδικασίες πλήρωσης θέσεων των φορέων της παρ. 1</li>
      <li><strong>Ποσό:</strong> 50,00 €</li>
      <li><strong>Κωδικός e-Παραβόλου:</strong> <b>20 ψηφία, χωρίς κενά</b> — αυτός καταχωρίζεται στην ηλεκτρονική ένσταση του ΑΣΕΠ.</li>
      <li><strong>Προσοχή:</strong> μην χρησιμοποιήσεις τον κωδικό πληρωμής <b>RF</b> στη θέση του 20ψήφιου κωδικού e-Παραβόλου.</li>
    </ul>
  </div>

  <button class="guide-submit edu-mt-4" type="button" id="guidanceBtn">Εμφάνιση οδηγιών</button>
  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">Το εργαλείο παρέχει ενδεικτική καθοδήγηση για τις ενστάσεις των προσωρινών πινάκων 1ΓΕ/2026 και 2ΓΕ/2026. Για την υποβολή ισχύουν η επίσημη ανακοίνωση, οι προκηρύξεις και οι οδηγίες της ηλεκτρονικής πλατφόρμας του ΑΣΕΠ.</p>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/objection-guide-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>

<?php sourceCardStart(); ?>
  <p>Προκηρύξεις Α.Σ.Ε.Π. 1ΓΕ/2026 και 2ΓΕ/2026, η επίσημη ανακοίνωση για τις ενστάσεις των προσωρινών πινάκων, καθώς και τα επίσημα εγχειρίδια Α.Σ.Ε.Π. για ηλεκτρονική ένσταση και e-Παράβολο.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/79576', 'Ανακοίνωση ενστάσεων — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/sites/default/files/2023-10/manual_enstasi_ekpaideutikon.pdf', 'Εγχειρίδιο ένστασης ↗'); ?><?php sourceCardLink('https://info.asep.gr/sites/default/files/2023-10/e-paravolo_enstasi.pdf', 'Οδηγίες e-Παραβόλου ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
