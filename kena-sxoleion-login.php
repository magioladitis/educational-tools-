<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/vacancies-model.php';

vacanciesSessionStart();
if (isset($_GET['logout'])) {
    vacanciesLogout();
    header('Location: kena-sxoleion-login.php');
    exit;
}
$actor = vacanciesActor();
if ($actor) {
    header('Location: ' . ($actor['role'] === 'admin' ? 'kena-sxoleion-admin.php' : 'kena-sxoleion.php'));
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = isset($_POST['role']) ? (string) $_POST['role'] : 'school';
    $key = isset($_POST['access_key']) ? (string) $_POST['access_key'] : '';
    $schoolId = isset($_POST['school_id']) ? (int) $_POST['school_id'] : 0;
    if (!vacanciesCsrfValid(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $error = 'Η συνεδρία έληξε. Ανανέωσε τη σελίδα και προσπάθησε ξανά.';
    } elseif (vacanciesDevLogin($role, $key, $schoolId)) {
        header('Location: ' . ($role === 'admin' ? 'kena-sxoleion-admin.php' : 'kena-sxoleion.php'));
        exit;
    } else {
        $error = 'Δεν ήταν δυνατή η δοκιμαστική σύνδεση. Έλεγξε τον ρόλο, το σχολείο και το κλειδί πρόσβασης.';
    }
}
$schools = vacanciesDbReady() ? vacanciesSchools() : array();
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Καταγραφή Κενών Σχολικών Μονάδων</title>
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/vacancies.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-vacancies">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell vacancies-shell">
  <section class="hero vacancies-hero">
    <span class="hero-kicker">ΠΙΛΟΤΙΚΗ ΕΦΑΡΜΟΓΗ</span>
    <h1>Καταγραφή Κενών Σχολικών Μονάδων</h1>
    <p>Άμεση υποβολή κενών και πλεονασμάτων ανά σχολείο, με ιστορικό αναθεωρήσεων και σαφή διάκριση ανάμεσα στο «0» και στο «δεν έχει υποβληθεί».</p>
  </section>

  <?php if (!is_file(vacanciesConfigPath())) { ?>
    <section class="card vacancy-status vacancy-status--warning">
      <h2>Χρειάζεται αρχική ρύθμιση</h2>
      <p>Δεν υπάρχει ακόμη το ιδιωτικό αρχείο <code>includes/vacancies-config.php</code>. Αντέγραψε το <code>includes/vacancies-config.example.php</code>, συμπλήρωσε τη MySQL βάση και κάνε import τα δύο SQL αρχεία του φακέλου <code>sql/</code>.</p>
    </section>
  <?php } elseif (!vacanciesDbReady()) { ?>
    <section class="card vacancy-status vacancy-status--danger">
      <h2>Δεν υπάρχει σύνδεση με τη βάση</h2>
      <p>Η εφαρμογή βρήκε configuration αλλά δεν μπόρεσε να συνδεθεί στη MySQL. Έλεγξε host, database, user και password.</p>
    </section>
  <?php } elseif (!vacanciesDevLoginEnabled()) { ?>
    <section class="card vacancy-status vacancy-status--info">
      <h2>Η δοκιμαστική είσοδος είναι κλειστή</h2>
      <p>Αυτό είναι το ασφαλές default. Το επόμενο production βήμα είναι σύνδεση με SSO ΠΣΔ. Για κλειστή τοπική/πιλοτική δοκιμή μπορεί προσωρινά να ενεργοποιηθεί το <code>dev_mode</code> στο ιδιωτικό configuration.</p>
    </section>
  <?php } else { ?>
    <section class="card vacancies-login-card">
      <div class="section-head">
        <div><h2>Δοκιμαστική είσοδος</h2><p>Μόνο για κλειστό pilot πριν από το SSO ΠΣΔ.</p></div>
      </div>
      <?php if ($error !== '') { ?><div class="vacancy-alert vacancy-alert--danger"><?php echo vacanciesH($error); ?></div><?php } ?>
      <form method="post" action="kena-sxoleion-login.php" data-vacancies-login>
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <div class="field-grid">
          <div class="field">
            <label for="role">Ρόλος δοκιμής</label>
            <select id="role" name="role">
              <option value="school">Σχολική μονάδα</option>
              <option value="admin">Διεύθυνση / διαχειριστής</option>
            </select>
          </div>
          <div class="field" data-school-picker>
            <label for="school_id">Σχολική μονάδα</label>
            <select id="school_id" name="school_id">
              <option value="">— Επίλεξε σχολείο —</option>
              <?php foreach ($schools as $school) { ?>
                <option value="<?php echo (int) $school['id']; ?>"><?php echo vacanciesH($school['name']); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="field full">
            <label for="access_key">Κλειδί pilot</label>
            <input id="access_key" name="access_key" type="password" autocomplete="current-password" required>
          </div>
        </div>
        <div class="button-row"><button class="primary" type="submit">Είσοδος</button></div>
      </form>
    </section>
  <?php } ?>
</main>
<script src="<?php echo vacanciesH(edu_asset_url('assets/vacancies.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
