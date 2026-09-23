<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/vacancies-model.php';

vacanciesSessionStart();
$actor = vacanciesActor();
if (!$actor || empty($actor['user_id']) || !vacanciesDbReady()) {
    header('Location: kena-sxoleion-login.php');
    exit;
}
$message = '';
$messageType = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!vacanciesCsrfValid(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $message = 'Η συνεδρία έληξε. Ανανέωσε τη σελίδα και προσπάθησε ξανά.';
        $messageType = 'danger';
    } else {
        $newPassword = isset($_POST['new_password']) ? (string) $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? (string) $_POST['confirm_password'] : '';
        if ($newPassword !== $confirmPassword) {
            $message = 'Οι δύο κωδικοί δεν συμφωνούν.';
            $messageType = 'danger';
        } else {
            list($ok, $msg) = vacanciesChangeOwnPassword($newPassword);
            if ($ok) {
                header('Location: ' . vacanciesRedirectForActor(vacanciesActor()));
                exit;
            }
            $message = $msg;
            $messageType = 'danger';
        }
    }
}
$forced = vacanciesActorNeedsPasswordChange();
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="manifest" href="manifest.webmanifest">
  <title>Αλλαγή κωδικού — Καταγραφή Κενών</title>
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/vacancies.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-vacancies">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell vacancies-shell vacancies-narrow-shell">
  <section class="hero vacancies-hero">
    <span class="hero-kicker">ΑΣΦΑΛΕΙΑ ΛΟΓΑΡΙΑΣΜΟΥ</span>
    <h1>Αλλαγή κωδικού πρόσβασης</h1>
    <p><?php echo $forced ? 'Για λόγους ασφαλείας πρέπει να αλλάξεις τον προσωρινό κωδικό πριν συνεχίσεις.' : 'Δημιούργησε νέο κωδικό πρόσβασης για τον λογαριασμό σου.'; ?></p>
  </section>

  <div class="vacancies-top-actions">
    <a class="vacancy-top-action" href="<?php echo vacanciesH(vacanciesRedirectForActor($actor)); ?>" aria-label="Dashboard" title="Dashboard"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h8v8H3V3Zm10 0h8v5h-8V3Zm0 7h8v11h-8V10ZM3 13h8v8H3v-8Z"/></svg><span class="vacancy-top-action__label">Dashboard</span></a>
    <a class="vacancy-top-action vacancy-top-action--current" href="kena-sxoleion-password.php" aria-label="Αλλαγή κωδικού" title="Αλλαγή κωδικού" aria-current="page"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 14a5 5 0 1 1 4.58-3H24v4h-2v2h-3v2h-4v-5h-3.42A5 5 0 0 1 7 14Zm0-3a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg><span class="vacancy-top-action__label">Αλλαγή κωδικού</span></a>
    <a class="vacancy-top-action vacancy-top-action--logout" href="kena-sxoleion-login.php?logout=1" aria-label="Αποσύνδεση" title="Αποσύνδεση"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 2h2v10h-2V2Zm1 20a9 9 0 0 1-6.36-15.36l1.42 1.42A7 7 0 1 0 16.94 8.06l1.42-1.42A9 9 0 0 1 12 22Z"/></svg><span class="vacancy-top-action__label">Αποσύνδεση</span></a>
  </div>

  <?php if ($message !== '') { ?><div class="vacancy-alert vacancy-alert--<?php echo vacanciesH($messageType); ?>"><?php echo vacanciesH($message); ?></div><?php } ?>
  <section class="card vacancies-login-card">
    <div class="vacancy-account-chip"><strong><?php echo vacanciesH(vacanciesActorDisplayName()); ?></strong><?php if (!empty($actor['username'])) { ?><span><?php echo vacanciesH($actor['username']); ?></span><?php } ?></div>
    <form method="post" action="kena-sxoleion-password.php">
      <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
      <div class="field"><label for="new_password">Νέος κωδικός</label><input id="new_password" name="new_password" type="password" minlength="12" autocomplete="new-password" required><small>Τουλάχιστον 12 χαρακτήρες.</small></div>
      <div class="field"><label for="confirm_password">Επιβεβαίωση νέου κωδικού</label><input id="confirm_password" name="confirm_password" type="password" minlength="12" autocomplete="new-password" required></div>
      <div class="button-row"><button class="primary" type="submit">Αποθήκευση νέου κωδικού</button><?php if (!$forced) { ?><a class="secondary button-like" href="<?php echo vacanciesH(vacanciesRedirectForActor($actor)); ?>">Ακύρωση</a><?php } ?></div>
    </form>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
