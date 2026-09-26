<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/vacancies-model.php';

vacanciesSessionStart();
if (!vacanciesIsAdmin() || !vacanciesDbReady()) {
    header('Location: kena-sxoleion-login.php');
    exit;
}
if (vacanciesActorNeedsPasswordChange()) {
    header('Location: kena-sxoleion-password.php');
    exit;
}

$message = '';
$messageType = 'success';
$credential = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!vacanciesCsrfValid(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $message = 'Η συνεδρία έληξε. Ανανέωσε τη σελίδα και προσπάθησε ξανά.';
        $messageType = 'danger';
    } else {
        $action = isset($_POST['user_action']) ? (string) $_POST['user_action'] : '';
        $result = array(false, 'Μη έγκυρη ενέργεια.');

        if ($action === 'create_director') {
            $result = vacanciesCreateDirectorAccount(isset($_POST['school_id']) ? (int) $_POST['school_id'] : 0);
            if (!empty($result[0])) $credential = array('username'=>$result[2], 'password'=>$result[3]);
        } elseif ($action === 'create_admin') {
            $result = vacanciesCreateAdminAccount(
                isset($_POST['username']) ? $_POST['username'] : '',
                isset($_POST['display_name']) ? $_POST['display_name'] : ''
            );
            if (!empty($result[0])) $credential = array('username'=>$result[2], 'password'=>$result[3]);
        } elseif ($action === 'reset_password') {
            $userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
            $result = vacanciesResetAccountPassword($userId);
            if (!empty($result[0])) {
                $userRow = vacanciesGetUser($userId);
                $credential = array('username'=>$userRow ? $userRow['username'] : '', 'password'=>$result[2]);
            }
        } elseif ($action === 'set_active') {
            $result = vacanciesSetAccountActive(
                isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0,
                isset($_POST['active']) ? (int) $_POST['active'] : 0
            );
        } elseif ($action === 'update_account') {
            $result = vacanciesUpdateAccount(
                isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0,
                isset($_POST['username']) ? $_POST['username'] : '',
                isset($_POST['display_name']) ? $_POST['display_name'] : '',
                isset($_POST['role']) ? $_POST['role'] : '',
                isset($_POST['school_id']) ? (int) $_POST['school_id'] : 0
            );
        } elseif ($action === 'delete_account') {
            $result = vacanciesDeleteAccount(isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0);
        }

        $ok = !empty($result[0]);
        $message = isset($result[1]) ? $result[1] : '';
        $messageType = $ok ? 'success' : 'danger';
    }
}

$users = vacanciesListUsers();
$schools = vacanciesSchools();
$actor = vacanciesActor();
$actorUserId = !empty($actor['user_id']) ? (int) $actor['user_id'] : 0;
$activeAdminCount = vacanciesActiveAdminCount();
$directorBySchool = array();
foreach ($users as $user) {
    if ($user['role'] === 'school_director' && !empty($user['school_id'])) {
        $directorBySchool[(int) $user['school_id']] = $user;
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Λογαριασμοί — Καταγραφή Κενών</title>
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/vacancies.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-vacancies">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell vacancies-shell">
  <section class="hero vacancies-hero">
    <span class="hero-kicker">ΔΙΕΥΘΥΝΣΗ — ΠΡΟΣΒΑΣΗ</span>
    <h1>Λογαριασμοί χρηστών</h1>
    <p>Κάθε Διευθυντής Σχολείου συνδέεται με μία μόνο σχολική μονάδα. Οι αλλαγές ρόλου και σχολείου γίνονται μόνο από εδώ και ελέγχονται ξανά στον server.</p>
  </section>

  <div class="vacancies-top-actions">
    <a class="vacancy-top-action" href="kena-sxoleion-admin.php" aria-label="Dashboard" title="Dashboard"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h8v8H3V3Zm10 0h8v5h-8V3Zm0 7h8v11h-8V10ZM3 13h8v8H3v-8Z"/></svg><span class="vacancy-top-action__label">Dashboard</span></a>
    <a class="vacancy-top-action" href="kena-sxoleion-password.php" aria-label="Αλλαγή κωδικού" title="Αλλαγή κωδικού"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 14a5 5 0 1 1 4.58-3H24v4h-2v2h-3v2h-4v-5h-3.42A5 5 0 0 1 7 14Zm0-3a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg><span class="vacancy-top-action__label">Αλλαγή κωδικού</span></a>
    <a class="vacancy-top-action vacancy-top-action--logout" href="kena-sxoleion-login.php?logout=1" aria-label="Αποσύνδεση" title="Αποσύνδεση"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 2h2v10h-2V2Zm1 20a9 9 0 0 1-6.36-15.36l1.42 1.42A7 7 0 1 0 16.94 8.06l1.42-1.42A9 9 0 0 1 12 22Z"/></svg><span class="vacancy-top-action__label">Αποσύνδεση</span></a>
  </div>

  <?php if ($message !== '') { ?>
    <div class="vacancy-alert vacancy-alert--<?php echo vacanciesH($messageType); ?>"><?php echo vacanciesH($message); ?></div>
  <?php } ?>

  <?php if ($credential) { ?>
    <section class="card vacancy-credential-card">
      <h2>Προσωρινά στοιχεία πρόσβασης</h2>
      <p><strong>Αντέγραψέ τα τώρα.</strong> Ο κωδικός δεν αποθηκεύεται σε αναγνώσιμη μορφή και δεν μπορεί να εμφανιστεί ξανά.</p>
      <div class="vacancy-credential-grid">
        <div><span>Όνομα χρήστη</span><code><?php echo vacanciesH($credential['username']); ?></code></div>
        <div><span>Προσωρινός κωδικός</span><code><?php echo vacanciesH($credential['password']); ?></code></div>
      </div>
    </section>
  <?php } ?>

  <section class="card">
    <div class="section-head">
      <div>
        <h2>Διαχειριστές Διεύθυνσης</h2>
        <p>Ενεργοί admin: <strong><?php echo (int) $activeAdminCount; ?></strong>. Η εφαρμογή δεν επιτρέπει απενεργοποίηση, υποβιβασμό ή διαγραφή του τελευταίου ενεργού admin.</p>
      </div>
    </div>
    <form method="post" action="kena-sxoleion-users.php" class="vacancy-inline-account-form">
      <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
      <input type="hidden" name="user_action" value="create_admin">
      <div class="field">
        <label for="admin_username">Username</label>
        <input id="admin_username" name="username" placeholder="π.χ. dde_admin" required pattern="[A-Za-z0-9._-]{3,80}">
      </div>
      <div class="field">
        <label for="admin_display">Εμφανιζόμενο όνομα</label>
        <input id="admin_display" name="display_name" placeholder="π.χ. Διαχειριστής ΔΔΕ Κέρκυρας">
      </div>
      <button class="primary" type="submit">Δημιουργία admin</button>
    </form>
  </section>

  <section class="card">
    <div class="section-head">
      <div>
        <h2>Λογαριασμοί σχολικών μονάδων</h2>
        <p>Το username δημιουργείται από τον κωδικό Υπουργείου. Αν γίνει λάθος σε ρόλο ή σχολείο, διορθώνεται από την ενότητα «Όλοι οι λογαριασμοί» πιο κάτω.</p>
      </div>
    </div>
    <div class="table-wrap">
      <table class="vacancy-table vacancy-users-table">
        <thead><tr><th>Σχολείο</th><th>Username</th><th>Κατάσταση</th><th>Τελευταία είσοδος</th><th>Ενέργειες</th></tr></thead>
        <tbody>
        <?php foreach ($schools as $school) {
          $sid = (int) $school['id'];
          $u = isset($directorBySchool[$sid]) ? $directorBySchool[$sid] : null;
        ?>
          <tr>
            <td><strong><?php echo vacanciesH($school['name']); ?></strong><small><?php echo vacanciesH($school['ministry_code']); ?></small></td>
            <td><?php echo $u ? '<code>'.vacanciesH($u['username']).'</code>' : '—'; ?></td>
            <td>
              <?php if (!$u) { ?>
                <span class="vacancy-badge vacancy-badge--missing">Χωρίς λογαριασμό</span>
              <?php } elseif (!empty($u['active'])) { ?>
                <span class="vacancy-badge vacancy-badge--ok">Ενεργός</span>
                <?php if (!empty($u['must_change_password'])) { ?> <small>αναμένει αλλαγή κωδικού</small><?php } ?>
              <?php } else { ?>
                <span class="vacancy-badge vacancy-badge--draft">Ανενεργός</span>
              <?php } ?>
            </td>
            <td><?php echo $u && !empty($u['last_login_at']) ? vacanciesH($u['last_login_at']) : '—'; ?></td>
            <td class="vacancy-user-actions">
              <?php if (!$u) { ?>
                <form method="post" action="kena-sxoleion-users.php">
                  <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
                  <input type="hidden" name="user_action" value="create_director">
                  <input type="hidden" name="school_id" value="<?php echo $sid; ?>">
                  <button class="secondary" type="submit">Δημιουργία</button>
                </form>
              <?php } else { ?>
                <a class="secondary vacancy-button-link" href="#user-<?php echo (int) $u['id']; ?>">Διαχείριση</a>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="card">
    <div class="section-head">
      <div>
        <h2>Όλοι οι λογαριασμοί</h2>
        <p>Εδώ μπορείς να διορθώσεις username, ρόλο και σχολική μονάδα, να επαναφέρεις κωδικό, να απενεργοποιήσεις ή να διαγράψεις λογαριασμό.</p>
      </div>
    </div>

    <div class="vacancy-account-list">
      <?php foreach ($users as $u) {
        $uid = (int) $u['id'];
        $isSelf = $actorUserId > 0 && $actorUserId === $uid;
      ?>
        <article class="vacancy-account-card" id="user-<?php echo $uid; ?>">
          <div class="vacancy-account-summary">
            <div>
              <strong><code><?php echo vacanciesH($u['username']); ?></code></strong>
              <span><?php echo $u['role'] === 'admin' ? 'Διαχειριστής Διεύθυνσης' : 'Διευθυντής Σχολείου'; ?></span>
              <?php if (!empty($u['school_name'])) { ?><small><?php echo vacanciesH($u['school_name']); ?></small><?php } ?>
            </div>
            <div class="vacancy-account-state">
              <?php if (!empty($u['active'])) { ?><span class="vacancy-badge vacancy-badge--ok">Ενεργός</span><?php } else { ?><span class="vacancy-badge vacancy-badge--draft">Ανενεργός</span><?php } ?>
              <?php if ($isSelf) { ?><span class="vacancy-badge vacancy-badge--neutral">Τρέχων λογαριασμός</span><?php } ?>
            </div>
          </div>

          <div class="vacancy-account-meta">
            <span><strong>Όνομα:</strong> <?php echo vacanciesH($u['display_name']); ?></span>
            <span><strong>Τελευταία είσοδος:</strong> <?php echo !empty($u['last_login_at']) ? vacanciesH($u['last_login_at']) : '—'; ?></span>
          </div>

          <?php if ($isSelf) { ?>
            <div class="vacancy-account-self-note">Ο τρέχων λογαριασμός προστατεύεται από αλλαγή ρόλου, απενεργοποίηση, reset και διαγραφή. Για τον κωδικό χρησιμοποίησε την «Αλλαγή κωδικού».</div>
          <?php } else { ?>
            <details class="vacancy-account-editor">
              <summary>Επεξεργασία ρόλου / σχολείου</summary>
              <form method="post" action="kena-sxoleion-users.php" class="vacancy-account-edit-form">
                <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
                <input type="hidden" name="user_action" value="update_account">
                <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                <div class="field">
                  <label>Username</label>
                  <input name="username" value="<?php echo vacanciesH($u['username']); ?>" required pattern="[A-Za-z0-9._-]{3,80}">
                </div>
                <div class="field">
                  <label>Εμφανιζόμενο όνομα</label>
                  <input name="display_name" value="<?php echo vacanciesH($u['display_name']); ?>">
                </div>
                <div class="field">
                  <label>Ρόλος</label>
                  <select name="role" class="vacancy-role-select" data-school-target="school-select-<?php echo $uid; ?>">
                    <option value="admin"<?php echo $u['role'] === 'admin' ? ' selected' : ''; ?>>Διαχειριστής Διεύθυνσης</option>
                    <option value="school_director"<?php echo $u['role'] === 'school_director' ? ' selected' : ''; ?>>Διευθυντής Σχολείου</option>
                  </select>
                </div>
                <div class="field">
                  <label>Σχολική μονάδα</label>
                  <select name="school_id" id="school-select-<?php echo $uid; ?>"<?php echo $u['role'] === 'admin' ? ' disabled' : ''; ?>>
                    <option value="0">— Επιλογή σχολείου —</option>
                    <?php foreach ($schools as $school) { ?>
                      <option value="<?php echo (int) $school['id']; ?>"<?php echo (int) $u['school_id'] === (int) $school['id'] ? ' selected' : ''; ?>><?php echo vacanciesH($school['name']); ?> · <?php echo vacanciesH($school['ministry_code']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <button class="primary" type="submit">Αποθήκευση αλλαγών</button>
              </form>
            </details>

            <div class="vacancy-user-actions vacancy-account-actions">
              <form method="post" action="kena-sxoleion-users.php" data-confirm="Να δημιουργηθεί νέος προσωρινός κωδικός; Ο προηγούμενος θα πάψει να ισχύει.">
                <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
                <input type="hidden" name="user_action" value="reset_password">
                <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                <button class="secondary" type="submit">Νέος κωδικός</button>
              </form>
              <form method="post" action="kena-sxoleion-users.php">
                <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
                <input type="hidden" name="user_action" value="set_active">
                <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                <input type="hidden" name="active" value="<?php echo !empty($u['active']) ? 0 : 1; ?>">
                <button class="secondary" type="submit"><?php echo !empty($u['active']) ? 'Απενεργοποίηση' : 'Ενεργοποίηση'; ?></button>
              </form>
              <form method="post" action="kena-sxoleion-users.php" data-confirm="<?php echo vacanciesH('Ο λογαριασμός ' . $u['username'] . ' θα διαγραφεί οριστικά. Συνέχεια;'); ?>">
                <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
                <input type="hidden" name="user_action" value="delete_account">
                <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                <button class="vacancy-danger-button" type="submit">Διαγραφή</button>
              </form>
            </div>
          <?php } ?>
        </article>
      <?php } ?>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo vacanciesH(edu_asset_url('assets/vacancies.js')); ?>"></script>
</body>
</html>
