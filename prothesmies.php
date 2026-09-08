<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Ενεργές και προσεχείς προθεσμίες για εκπαιδευτικούς, αιτήσεις, αποσπάσεις, ΣΑΕΚ και άλλες διαδικασίες.">
  <title>Προθεσμίες Εκπαιδευτικών | Εργαλειοθήκη Εκπαιδευτικού</title>
  <link href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>" rel="stylesheet">
</head>
<body class="edu-ui edu-deadlines-page">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>

<main class="edu-deadlines-shell">
  <section class="edu-deadlines-hero">
    <span class="edu-deadlines-kicker">ΞΕΧΩΡΙΣΤΗ ΕΝΟΤΗΤΑ</span>
    <h1>Προθεσμίες Εκπαιδευτικών</h1>
    <p>Οι σημαντικές ενεργές και προσεχείς ημερομηνίες συγκεντρωμένες εδώ, χωρίς να επιβαρύνουν την κεντρική σελίδα των εργαλείων.</p>
    <div class="edu-deadlines-hero__actions">
      <a href="ergaleia.php">← Επιστροφή στα εργαλεία</a>
    </div>
  </section>

  <?php
  $deadlineConfig = require __DIR__ . '/includes/deadlines.php';
  $deadlineConfig['collapsible'] = false;
  renderDeadlineCard($deadlineConfig);
  unset($deadlineConfig);
  ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
