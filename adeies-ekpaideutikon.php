<?php
require_once __DIR__ . '/includes/config.php';
$h = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$permanentGuide = require __DIR__ . '/includes/permanent-leaves-data.php';
$substituteGuide = require __DIR__ . '/includes/substitute-leaves-data.php';
$permanentLeaveCount = count($permanentGuide['leaves'] ?? array());
$substituteLeaveCount = count($substituteGuide['leaves'] ?? array());
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php require __DIR__ . '/includes/head-pwa.php'; ?>
<meta name="description" content="Άδειες εκπαιδευτικών: επίλεξε μόνιμο ή αναπληρωτή / ΙΔΟΧ και βρες διάρκεια, προϋποθέσεις, αποδοχές, υπηρεσία και επίσημες πηγές.">
<title>Άδειες Εκπαιδευτικών</title>
<link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/common.css')); ?>">
<link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/leave-guide.css')); ?>">
</head>
<body class="edu-ui edu-guide-standard edu-page-leave-guide edu-page-leave-hub">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
  <h1>Άδειες Εκπαιδευτικών</h1>
  <p class="intro">Επίλεξε την κατηγορία προσωπικού για να δεις τις άδειες που σε αφορούν ή άνοιξε τη <strong>σύγκριση Μόνιμου ↔ Αναπληρωτή / ΙΔΟΧ</strong>, με διάρκεια, προϋποθέσεις, αποδοχές, πραγματική υπηρεσία και επίσημες πηγές.</p>
</section>

<section class="leave-hub" aria-labelledby="leaveHubTitle">
  <span class="section-kicker">ΕΠΙΛΟΓΗ ΚΑΤΗΓΟΡΙΑΣ</span>
  <h2 id="leaveHubTitle">Ποιο καθεστώς σε αφορά;</h2>
  <div class="leave-hub__grid">
    <a class="leave-hub__card" href="adeies-monimon.php">
      <span class="leave-hub__eyebrow">ΜΟΝΙΜΟ ΠΡΟΣΩΠΙΚΟ</span>
      <strong>Μόνιμοι Εκπαιδευτικοί / ΕΕΠ–ΕΒΠ</strong>
      <span><?php echo $h($permanentLeaveCount); ?> οργανωμένες άδειες και διευκολύνσεις, με βάση τον Υπαλληλικό Κώδικα, το ειδικό εκπαιδευτικό πλαίσιο και νεότερες επίσημες ρυθμίσεις.</span>
      <span class="leave-hub__action">Άνοιγμα οδηγού →</span>
    </a>
    <a class="leave-hub__card" href="adeies-anapliroton.php">
      <span class="leave-hub__eyebrow">ΙΔΟΧ / ΑΝΑΠΛΗΡΩΤΕΣ</span>
      <strong>Αναπληρωτές Εκπαιδευτικοί / ΕΕΠ–ΕΒΠ</strong>
      <span><?php echo $h($substituteLeaveCount); ?> άδειες και διευκολύνσεις με ειδική επισήμανση για αναλογικότητα σύμβασης, αποδοχές και εφαρμογή σε ΙΔΟΧ.</span>
      <span class="leave-hub__action">Άνοιγμα οδηγού →</span>
    </a>
    <a class="leave-hub__card leave-hub__card--compare" href="adeies-sygkrisi.php">
      <span class="leave-hub__eyebrow">ΜΟΝΙΜΟΣ ↔ ΑΝΑΠΛΗΡΩΤΗΣ / ΙΔΟΧ</span>
      <strong>Σύγκριση της ίδιας άδειας</strong>
      <span>Επίλεξε δικαίωμα και δες δίπλα-δίπλα διάρκεια, αποδοχές, πραγματική υπηρεσία, προϋποθέσεις και νομική βάση.</span>
      <span class="leave-hub__action">Άνοιγμα σύγκρισης →</span>
    </a>
  </div>
</section>

<details class="leave-update-note">
  <summary>Πώς είναι οργανωμένη η ενότητα</summary>
  <div class="leave-update-note__body">
    <p>Οι δύο οδηγοί και η σύγκριση χρησιμοποιούν κοινό μηχανισμό εμφάνισης και επίσημων πηγών. Τα δεδομένα παραμένουν χωριστά, ώστε οι διαφορετικές προϋποθέσεις μονίμων και αναπληρωτών να μην συγχέονται.</p>
    <p>Αυτό επιτρέπει στο εξής να ενημερώνεται μία φορά το περιβάλλον χρήσης και να προστίθενται ξεχωριστά οι αλλαγές της νομοθεσίας για κάθε κατηγορία προσωπικού.</p>
  </div>
</details>

<p class="small-note">Για ειδικές ή σύνθετες περιπτώσεις, έλεγξε πάντοτε και την αντίστοιχη νομική βάση μέσα στην κάρτα της άδειας.</p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo $h(edu_asset_url('assets/common.js')); ?>"></script>
</body>
</html>
