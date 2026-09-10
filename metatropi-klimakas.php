<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Μετατροπή βαθμού πτυχίου από 10βάθμια σε 20βάθμια κλίμακα και από δεκαδική ή λεκτική μορφή σε ακέραιο μέρος, αριθμητή και παρονομαστή για 1ΓΕ/2026 και 1ΓΤ/2024.">
<title>Μετατροπή κλίμακας βαθμού πτυχίου</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-page-grade-converter">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box edu-modernized">
  <section class="hero edu-legacy-hero">
<h1>Μετατροπή κλίμακας βαθμού πτυχίου</h1>
<p class="intro">Μετατροπή από 10βάθμια σε 20βάθμια κλίμακα και από δεκαδική ή λεκτική μορφή στα πεδία <strong>Ακέραιο μέρος – Αριθμητής – Παρονομαστής</strong>.</p>
</section>

  <div class="notice">
    <strong>⚠️ Προσοχή στη διαφορετική κλίμακα:</strong><br>
    Στην <strong>1ΓΕ/2026</strong> ο βαθμός πτυχίου δηλώνεται σε <strong>10βάθμια κλίμακα</strong>, ενώ στην <strong>1ΓΤ/2024</strong> σε <strong>20βάθμια κλίμακα</strong>. Για την 1ΓΤ/2024 γίνεται πρώτα η αναγωγή στην 20βάθμια και μετά η μετατροπή της δεκαδικής μορφής σε κλασματική.
  </div>

  <div class="mode-tabs" role="group" aria-label="Τρόπος καταχώρισης βαθμού">
    <button type="button" id="decimalTab" class="active" aria-pressed="true">🔢 Δεκαδικός βαθμός</button>
    <button type="button" id="lexicalTab" aria-pressed="false">🔤 Λεκτική κλίμακα</button>
  </div>

  <div class="panel">
    <h2>1. Καταχώρισε τον βαθμό</h2>

    <div id="decimalPanel">
      <label for="decimalGrade">Βαθμός σε 10βάθμια κλίμακα</label>
      <input id="decimalGrade" type="text" inputmode="decimal" autocomplete="off" placeholder="π.χ. 7,34" aria-describedby="decimalHint decimalError">
      <p id="decimalHint" class="hint">Δέχεται κόμμα ή τελεία. Επιτρεπτές τιμές: 1 έως 10.</p>
      <div id="decimalError" class="error" role="alert"></div>
    </div>

    <div id="lexicalPanel" class="hidden">
      <label for="lexicalGrade">Λεκτικός βαθμός</label>
      <select id="lexicalGrade">
        <option value="">-- Επιλογή --</option>
        <option value="5">ΚΑΛΩΣ (5)</option>
        <option value="6.5">ΛΙΑΝ ΚΑΛΩΣ (6,5)</option>
        <option value="8.5">ΑΡΙΣΤΑ (8,5)</option>
      </select>
      <div class="lexical-map">
        <strong>Αντιστοίχιση λεκτικής κλίμακας:</strong>
        ΚΑΛΩΣ → 5 &nbsp;•&nbsp; ΛΙΑΝ ΚΑΛΩΣ → 6,5 &nbsp;•&nbsp; ΑΡΙΣΤΑ → 8,5
      </div>
    </div>
  </div>

  <div id="result" class="result hidden" aria-live="polite">
    <h2 class="result-title">Αποτέλεσμα μετατροπής</h2>
    <div id="sourceSummary" class="source-summary"></div>

    <div class="result-grid">
      <section class="result-card">
        <h3>1ΓΕ/2026</h3>
        <div class="subtitle">Δήλωση σε 10βάθμια κλίμακα</div>
        <div id="grade10" class="big-grade">—</div>
        <div class="fraction-title">Σε κλασματική μορφή</div>
        <div class="fraction-grid">
          <div class="field-box"><span>Ακέραιο μέρος</span><strong id="int10">—</strong></div>
          <div class="field-box"><span>Αριθμητής</span><strong id="num10">—</strong></div>
          <div class="field-box"><span>Παρονομαστής</span><strong id="den10">100</strong></div>
        </div>
        <div id="points10" class="points">Μόρια: —</div>
      </section>

      <section class="result-card recommended">
        <h3>1ΓΤ/2024</h3>
        <div class="subtitle">Αναγωγή σε 20βάθμια κλίμακα</div>
        <div id="grade20" class="big-grade">—</div>
        <div class="fraction-title">Πεδία αίτησης</div>
        <div class="fraction-grid">
          <div class="field-box"><span>Ακέραιο μέρος</span><strong id="int20">—</strong></div>
          <div class="field-box"><span>Αριθμητής</span><strong id="num20">—</strong></div>
          <div class="field-box"><span>Παρονομαστής</span><strong id="den20">100</strong></div>
        </div>
        <div id="points20" class="points">Μόρια: —</div>
        <div class="action-row">
          <button type="button" id="copy20" class="copy-btn">📋 Αντιγραφή πεδίων 1ΓΤ/2024</button>
          <span id="copyStatus" class="copy-status" aria-live="polite"></span>
        </div>
      </section>
    </div>

    <div class="action-row">
      <button type="button" id="resetBtn" class="reset-btn">↺ Καθαρισμός</button>
      <button type="button" id="printBtn" class="reset-btn">🖨 Εκτύπωση</button>
    </div>
  </div>

  <p class="small-note">Το εργαλείο κάνει αριθμητική μετατροπή με βάση τους κανόνες που έχουν ενσωματωθεί στη σελίδα. Πριν από οριστική καταχώριση, έλεγξε πάντα την αντίστοιχη προκήρυξη και τα πεδία της ηλεκτρονικής αίτησης.</p>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/grade-scale-converter-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>

<?php sourceCardStart(); ?>
  <p>Για την <strong>1ΓΕ/2026</strong> χρησιμοποιείται η 10βάθμια κλίμακα του βαθμού πτυχίου και η αντίστοιχη μοριοδότηση του Κεφαλαίου Γ΄. Για την <strong>1ΓΤ/2024</strong> εφαρμόζεται η αναγωγή στην 20βάθμια κλίμακα και η καταχώριση του βαθμού σύμφωνα με την οικεία προκήρυξη. Η μετατροπή σε ακέραιο μέρος/αριθμητή/παρονομαστή είναι αριθμητική απεικόνιση για τα πεδία της αίτησης.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/73068', '1ΓΤ/2024 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
