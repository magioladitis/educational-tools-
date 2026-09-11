<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Μάθε σε ποια προκήρυξη μπορείς να συμμετέχεις και πόσα παράβολα χρειάζεσαι</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body class="edu-ui edu-guide-standard edu-guide-paravolo">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

  <div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Μάθε σε ποια προκήρυξη μπορείς να συμμετέχεις και πόσα παράβολα χρειάζεσαι</h1>
<p>Επίλεξε την 1η ειδικότητα και, προαιρετικά, τη 2η ειδικότητα. Το αποτέλεσμα ενημερώνεται αυτόματα.</p>
</section>

    <section class="card edu-legacy-main-card">
    <div class="paravolo-rule">
      ℹ️ Το παράβολο υπολογίζεται <strong>ανά προκήρυξη και όχι ανά ειδικότητα</strong>.
      Αν δύο ειδικότητες ανήκουν στην ίδια προκήρυξη, χρειάζεται ένα μόνο παράβολο.
    </div>

    <label for="specialty1">1η ειδικότητα</label>
    <select id="specialty1">
      <option value="">-- Επιλογή ειδικότητας --</option>
    </select>

    <label for="specialty2">2η ειδικότητα <span class="optional">(προαιρετική)</span></label>
	<select id="specialty2">
	  <option value="">-- Δεν έχω 2η ειδικότητα --</option>
	</select>

    <div id="duplicateWarning" class="duplicate-warning" role="alert"></div>
    <div id="result" class="result" role="status" aria-live="polite"></div>
<?php calculatorDisclosureStart(array(
  'summary' => 'Οδηγίες για την έκδοση και πληρωμή παραβόλου',
  'open' => true,
  'attrs' => array('data-mobile-collapsed' => 'true')
)); ?>
<div class="instructions-content">
    <p>
      Ο/Η υποψήφιος/α πρέπει να εκδώσει ηλεκτρονικό παράβολο αξίας
      <strong>δεκαπέντε (15) ευρώ</strong> για κάθε απαιτούμενο παράβολο.
    </p>

    <p>
      Η έκδοση γίνεται μέσω της εφαρμογής
      <strong><a href="https://www1.gsis.gr/sgsisapps/eparavolo/public/welcome.htm" target="_blank" rel="noopener">e-Παράβολο</a></strong>
      ή, αν υπάρχουν κωδικοί TAXISnet, μέσω της υπηρεσίας
      <strong><a href="https://www.aade.gr/e-parabolo-me-kodikoys-taxisnet" target="_blank" rel="noopener">e-Παράβολο με κωδικούς TAXISnet</a></strong>.
    </p>

    <p>
      Στην εφαρμογή επιλέγεται:
    </p>

    <p class="path">
      Φορέας Δημοσίου → Ανώτατο Συμβούλιο Επιλογής Προσωπικού (Α.Σ.Ε.Π.)
    </p>

    <p>
      Ο/Η υποψήφιος/α πρέπει να αναγράψει τον <strong>20ψήφιο κωδικό παραβόλου</strong> στο κατάλληλο πεδίο της ηλεκτρονικής αίτησης.
    </p>

    <p>
      Η ηλεκτρονική υποβολή της αίτησης στο Α.Σ.Ε.Π. ολοκληρώνεται με την επιλογή
      <strong>«Οριστικοποίηση»</strong>, μόνο εφόσον ο κωδικός του παραβόλου
      βρίσκεται σε κατάσταση <strong>«ΠΛΗΡΩΜΕΝΟ»</strong>, ώστε να δεσμευτεί.
    </p>

    <p class="warning">
      Προσοχή: Πλήρωσε τον ίδιο ακριβώς κωδικό παραβόλου που έχεις αναγράψει
      στην ηλεκτρονική αίτηση και φρόντισε να έχει πληρωθεί πριν την υποβολή της.
    </p>

    <p>
      Περισσότερες πληροφορίες για το e-Παράβολο:
      <br>
      <a href="https://www.gsis.gr/polites-epiheiriseis/pliromes-kai-eispraxeis/e-paravolo" target="_blank" rel="noopener">
        https://www.gsis.gr/polites-epiheiriseis/pliromes-kai-eispraxeis/e-paravolo
      </a>
    </p>
  </div>
<?php calculatorDisclosureEnd(); ?>
    </section>
  </div>

  <script src="<?php echo htmlspecialchars(edu_asset_url('includes/paravola-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>


<?php sourceCardStart(); ?>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>. Η υποχρέωση έκδοσης e-Παραβόλου και το ποσό των <strong>15 € ανά προκήρυξη</strong> προκύπτουν από τους όρους υποβολής της αντίστοιχης αίτησης.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78737', 'Έκδοση ΦΕΚ 1ΓΕ/2026 & 2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78799', 'Υποβολή αιτήσεων — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>