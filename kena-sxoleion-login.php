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
    header('Location: ' . vacanciesRedirectForActor($actor));
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!vacanciesCsrfValid(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $error = 'Η συνεδρία έληξε. Ανανέωσε τη σελίδα και προσπάθησε ξανά.';
    } else {
        $loginMode = isset($_POST['login_mode']) ? (string) $_POST['login_mode'] : 'account';
        if ($loginMode === 'pilot') {
            $role = isset($_POST['role']) ? (string) $_POST['role'] : 'school';
            $key = isset($_POST['access_key']) ? (string) $_POST['access_key'] : '';
            $schoolId = isset($_POST['school_id']) ? (int) $_POST['school_id'] : 0;
            if (vacanciesDevLogin($role, $key, $schoolId)) {
                header('Location: ' . vacanciesRedirectForActor(vacanciesActor()));
                exit;
            }
            $error = 'Δεν ήταν δυνατή η πιλοτική σύνδεση.';
        } else {
            $username = isset($_POST['username']) ? (string) $_POST['username'] : '';
            $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
            if (vacanciesAccountLogin($username, $password)) {
                header('Location: ' . vacanciesRedirectForActor(vacanciesActor()));
                exit;
            }
            // Deliberately generic so the page does not reveal whether a username exists.
            usleep(250000);
            $error = 'Λανθασμένο όνομα χρήστη ή κωδικός πρόσβασης.';
        }
    }
}
$schools = vacanciesDbReady() ? vacanciesSchools() : array();
$accountsReady = vacanciesAccountsReady();
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Καταγραφή Κενών Σχολικών Μονάδων</title>
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/vacancies.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-vacancies">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell vacancies-shell">
  <section class="hero vacancies-hero">
    <span class="hero-kicker">ΚΑΤΑΓΡΑΦΗ ΚΕΝΩΝ</span>
    <h1>Καταγραφή Κενών Σχολικών Μονάδων</h1>
    <p>Κάθε λογαριασμός διευθυντή είναι συνδεδεμένος αποκλειστικά με τη σχολική μονάδα του. Δεν απαιτείται — και δεν επιτρέπεται — επιλογή άλλου σχολείου.</p>
  </section>

  <?php if (!is_file(vacanciesConfigPath())) { ?>
    <section class="card vacancy-status vacancy-status--warning"><h2>Χρειάζεται αρχική ρύθμιση</h2><p>Δεν υπάρχει ακόμη το ιδιωτικό <code>includes/vacancies-config.php</code>.</p></section>
  <?php } elseif (!vacanciesDbReady()) { ?>
    <section class="card vacancy-status vacancy-status--danger"><h2>Δεν υπάρχει σύνδεση με τη βάση</h2><p>Έλεγξε τα στοιχεία της MariaDB στο ιδιωτικό configuration.</p></section>
  <?php } else { ?>
    <?php if ($error !== '') { ?><div class="vacancy-alert vacancy-alert--danger"><?php echo vacanciesH($error); ?></div><?php } ?>

    <?php if ($accountsReady) { ?>
    <section class="card vacancies-login-card">
      <div class="section-head"><div><h2>Είσοδος</h2><p>Χρησιμοποίησε τον προσωπικό λογαριασμό που έχει αποδοθεί στη σχολική μονάδα ή στη Διεύθυνση.</p></div></div>
      <form method="post" action="kena-sxoleion-login.php" autocomplete="on">
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <input type="hidden" name="login_mode" value="account">
        <div class="field-grid">
          <div class="field"><label for="username">Όνομα χρήστη</label><input id="username" name="username" autocomplete="username" maxlength="80" required></div>
          <div class="field"><label for="password">Κωδικός πρόσβασης</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
        </div>
        <div class="button-row"><button class="primary" type="submit">Είσοδος</button></div>
      </form>
    </section>
    <?php } else { ?>
      <section class="card vacancy-status vacancy-status--warning"><h2>Δεν έχει εγκατασταθεί ακόμη η διαχείριση λογαριασμών</h2><p>Εκτέλεσε το migration <code>sql/vacancies-v1.5-accounts-school-profile.sql</code>. Μέχρι τότε μπορεί να χρησιμοποιηθεί μόνο η κλειστή πιλοτική είσοδος, εφόσον είναι ενεργή.</p></section>
    <?php } ?>

    <?php if (vacanciesDevLoginEnabled()) { ?>
    <details class="card vacancy-dev-login">
      <summary>Πιλοτική είσοδος ανάπτυξης</summary>
      <p class="vacancy-muted">Μόνο για δοκιμές. Απενεργοποίησέ την (<code>dev_mode=false</code>) πριν δοθούν πραγματικοί λογαριασμοί στα σχολεία.</p>
      <form method="post" action="kena-sxoleion-login.php" data-vacancies-login>
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <input type="hidden" name="login_mode" value="pilot">
        <div class="field-grid">
          <div class="field"><label for="role">Ρόλος δοκιμής</label><select id="role" name="role"><option value="school">Διευθυντής Σχολείου</option><option value="admin">Διεύθυνση / διαχειριστής</option></select></div>
          <div class="field" data-school-picker><label for="school_id">Σχολική μονάδα</label><select id="school_id" name="school_id"><option value="">— Επίλεξε σχολείο —</option><?php foreach ($schools as $school) { ?><option value="<?php echo (int) $school['id']; ?>"><?php echo vacanciesH($school['name']); ?></option><?php } ?></select></div>
          <div class="field full"><label for="access_key">Κλειδί pilot</label><input id="access_key" name="access_key" type="password" autocomplete="off" required></div>
        </div>
        <div class="button-row"><button class="secondary" type="submit">Πιλοτική είσοδος</button></div>
      </form>
    </details>
    <?php } ?>
  <?php } ?>
</main>
<script src="<?php echo vacanciesH(edu_asset_url('assets/vacancies.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
