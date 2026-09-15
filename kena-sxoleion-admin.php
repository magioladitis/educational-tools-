<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/vacancies-model.php';

vacanciesSessionStart();
if (!vacanciesIsAdmin() || !vacanciesDbReady()) {
    header('Location: kena-sxoleion-login.php');
    exit;
}
$adminMessage = '';
$adminMessageType = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!vacanciesCsrfValid(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $adminMessage = 'Η συνεδρία έληξε. Ανανέωσε τη σελίδα και προσπάθησε ξανά.';
        $adminMessageType = 'danger';
    } else {
        $adminAction = isset($_POST['admin_action']) ? (string) $_POST['admin_action'] : '';
        if ($adminAction === 'create_round') {
            list($ok, $msg, $newRoundId) = vacanciesCreateRound(
                isset($_POST['round_title']) ? $_POST['round_title'] : '',
                isset($_POST['reference_date']) ? $_POST['reference_date'] : '',
                isset($_POST['school_year']) ? $_POST['school_year'] : ''
            ) + array(null, null, 0);
            $adminMessage = $msg;
            $adminMessageType = $ok ? 'success' : 'danger';
            if ($ok && $newRoundId) $_GET['round'] = (int) $newRoundId;
        } elseif ($adminAction === 'round_status') {
            list($ok, $msg) = vacanciesSetRoundStatus(
                isset($_POST['round_id']) ? (int) $_POST['round_id'] : 0,
                isset($_POST['round_status']) ? $_POST['round_status'] : ''
            );
            $adminMessage = $msg;
            $adminMessageType = $ok ? 'success' : 'danger';
        }
    }
}
$rounds = vacanciesRounds();
$roundId = isset($_GET['round']) ? (int) $_GET['round'] : 0;
if ($roundId <= 0 && isset($rounds[0])) $roundId = (int) $rounds[0]['id'];
$round = vacanciesRound($roundId);
$stats = $round ? vacanciesDashboardStats($roundId) : array();
$schools = $round ? vacanciesDashboardSchools($roundId) : array();
$specialties = $round ? vacanciesDashboardSpecialties($roundId) : array();
$previousRound = $round ? vacanciesPreviousRound($round) : null;
$comparison = ($round && $previousRound) ? vacanciesCompareRounds($roundId, (int) $previousRound['id']) : null;
$config = vacanciesConfig();
$defaultSchoolYear = ($config && isset($config['default_school_year'])) ? (string) $config['default_school_year'] : '2026-2027';
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard κενών σχολικών μονάδων</title>
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/vacancies.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-vacancies">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell vacancies-shell">
  <section class="hero vacancies-hero">
    <span class="hero-kicker">ΔΙΕΥΘΥΝΣΗ — DASHBOARD</span>
    <h1>Καταγραφή κενών σχολικών μονάδων</h1>
    <p>Ζωντανή εικόνα υποβολών, κενών και πλεονασμάτων. Η μη υποβολή παραμένει ξεχωριστή κατάσταση και δεν μετατρέπεται σε μηδενικό.</p>
  </section>
  <div class="vacancies-top-actions"><a href="kena-sxoleion-login.php?logout=1">Αποσύνδεση</a></div>

  <?php if ($adminMessage !== '') { ?><div class="vacancy-alert vacancy-alert--<?php echo vacanciesH($adminMessageType); ?>"><?php echo vacanciesH($adminMessage); ?></div><?php } ?>

  <section class="card vacancy-round-picker">
    <form method="get" action="kena-sxoleion-admin.php">
      <div class="field"><label for="round">Γύρος καταγραφής</label><select id="round" name="round" onchange="this.form.submit()">
      <?php foreach ($rounds as $r) { ?><option value="<?php echo (int) $r['id']; ?>"<?php echo (int)$r['id']===$roundId?' selected':''; ?>><?php echo vacanciesH($r['title'] . ' · ' . $r['status']); ?></option><?php } ?>
      </select></div>
    </form>
  </section>

  <section class="card vacancy-round-admin">
    <div class="section-head"><div><h2>Διαχείριση γύρων</h2><p>Κάθε νέα ημερομηνία γίνεται ξεχωριστός γύρος, ώστε οι συγκρίσεις να είναι αυτόματες και να μην χάνεται το ιστορικό.</p></div></div>
    <div class="vacancy-admin-grid">
      <form method="post" action="kena-sxoleion-admin.php">
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <input type="hidden" name="admin_action" value="create_round">
        <h3>Νέος γύρος</h3>
        <div class="field"><label for="round_title">Τίτλος</label><input id="round_title" name="round_title" required placeholder="π.χ. Καταγραφή 22/09/2026"></div>
        <div class="field-grid"><div class="field"><label for="reference_date">Ημερομηνία αναφοράς</label><input id="reference_date" name="reference_date" type="date" required></div><div class="field"><label for="school_year">Σχολικό έτος</label><input id="school_year" name="school_year" value="<?php echo vacanciesH($defaultSchoolYear); ?>" required></div></div>
        <button class="primary" type="submit">Δημιουργία και άνοιγμα</button>
      </form>
      <?php if ($round) { ?>
      <form method="post" action="kena-sxoleion-admin.php?round=<?php echo (int)$round['id']; ?>">
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <input type="hidden" name="admin_action" value="round_status">
        <input type="hidden" name="round_id" value="<?php echo (int)$round['id']; ?>">
        <h3>Κατάσταση επιλεγμένου γύρου</h3>
        <div class="field"><label for="round_status">Κατάσταση</label><select id="round_status" name="round_status"><option value="open"<?php echo $round['status']==='open'?' selected':''; ?>>Ανοικτός</option><option value="closed"<?php echo $round['status']==='closed'?' selected':''; ?>>Κλειστός</option><option value="draft"<?php echo $round['status']==='draft'?' selected':''; ?>>Πρόχειρος</option></select></div>
        <button class="secondary" type="submit">Ενημέρωση κατάστασης</button>
      </form>
      <?php } ?>
    </div>
  </section>

  <?php if (!$round) { ?>
    <section class="card vacancy-status vacancy-status--info"><h2>Δεν υπάρχουν γύροι καταγραφής</h2></section>
  <?php } else { ?>
    <section class="vacancy-kpis">
      <article><span>Υποβολές</span><strong><?php echo (int)$stats['schools_submitted']; ?>/<?php echo (int)$stats['schools_total']; ?></strong><small><?php echo (int)$stats['schools_total']-(int)$stats['schools_submitted']; ?> εκκρεμούν</small></article>
      <article><span>Κενά</span><strong><?php echo (int)$stats['vacancies']; ?></strong><small>ώρες</small></article>
      <article><span>Πλεονάσματα</span><strong><?php echo (int)$stats['surpluses']; ?></strong><small>ώρες</small></article>
      <article><span>Νέα τμήματα</span><strong><?php echo (int)$stats['new_section_hours']; ?></strong><small>ώρες νέων αναγκών</small></article>
    </section>

    <?php if ($previousRound && $comparison && $comparison['schools'] > 0) {
      $delta = (int)$comparison['current_vacancies'] - (int)$comparison['previous_vacancies'];
      $pct = (int)$comparison['previous_vacancies'] > 0 ? (100.0 * $delta / (int)$comparison['previous_vacancies']) : 0;
      $adjustedCurrent = max(0, (int)$comparison['current_vacancies'] - (int)$comparison['new_section_hours']);
    ?>
    <section class="card vacancy-comparison">
      <div class="section-head"><div><h2>Σύγκριση με προηγούμενο γύρο</h2><p><?php echo vacanciesH($previousRound['title']); ?> → <?php echo vacanciesH($round['title']); ?> · μόνο σχολεία με οριστική υποβολή και στους δύο γύρους.</p></div></div>
      <div class="vacancy-comparison-grid">
        <div><span>Συγκρίσιμα σχολεία</span><strong><?php echo (int)$comparison['schools']; ?></strong></div>
        <div><span>Προηγούμενα κενά</span><strong><?php echo (int)$comparison['previous_vacancies']; ?></strong></div>
        <div><span>Τρέχοντα κενά</span><strong><?php echo (int)$comparison['current_vacancies']; ?></strong></div>
        <div><span>Μεταβολή</span><strong><?php echo ($delta>0?'+':'').$delta; ?></strong><small><?php echo number_format($pct,1,',','.'); ?>%</small></div>
        <div><span>Νέες ανάγκες από τμήματα</span><strong><?php echo (int)$comparison['new_section_hours']; ?></strong></div>
        <div><span>Τρέχον χωρίς νέες ανάγκες</span><strong><?php echo $adjustedCurrent; ?></strong></div>
      </div>
      <details class="vacancy-details"><summary>Μεταβολές ανά σχολείο</summary><div class="table-wrap"><table class="vacancy-table"><thead><tr><th>Σχολείο</th><th>Πριν</th><th>Τώρα</th><th>Δ</th><th>Νέα τμήματα</th></tr></thead><tbody>
      <?php foreach ($comparison['changes'] as $change) { ?><tr><td><?php echo vacanciesH($change['name']); ?></td><td><?php echo (int)$change['previous']; ?></td><td><?php echo (int)$change['current']; ?></td><td><?php echo ((int)$change['delta']>0?'+':'').(int)$change['delta']; ?></td><td><?php echo (int)$change['new_section_hours']; ?></td></tr><?php } ?>
      </tbody></table></div></details>
    </section>
    <?php } ?>

    <section class="card">
      <div class="section-head"><div><h2>Κατάσταση σχολικών μονάδων</h2><p>«Δεν υπέβαλε» και «0 κενά» εμφανίζονται διαφορετικά.</p></div></div>
      <div class="table-wrap"><table class="vacancy-table">
        <thead><tr><th>Σχολείο</th><th>Κατάσταση</th><th>Κενά</th><th>Πλεονάσματα</th><th>Αναθ.</th><th>Υποβολή</th></tr></thead>
        <tbody>
        <?php foreach ($schools as $row) {
          $status = isset($row['status']) ? $row['status'] : '';
          $statusLabel = $status === 'submitted' ? 'Οριστική' : ($status === 'draft' ? 'Πρόχειρη' : 'Δεν υπέβαλε');
          $statusClass = $status === 'submitted' ? 'ok' : ($status === 'draft' ? 'draft' : 'missing');
        ?>
          <tr><td><strong><?php echo vacanciesH($row['name']); ?></strong><small><?php echo vacanciesH($row['ministry_code']); ?></small></td><td><span class="vacancy-badge vacancy-badge--<?php echo $statusClass; ?>"><?php echo vacanciesH($statusLabel); ?></span></td><td><?php echo $status ? (int)$row['vacancies'] : '—'; ?></td><td><?php echo $status ? (int)$row['surpluses'] : '—'; ?></td><td><?php echo $status ? '#'.(int)$row['revision_no'] : '—'; ?></td><td><?php echo !empty($row['submitted_at']) ? vacanciesH($row['submitted_at']) : '—'; ?></td></tr>
        <?php } ?>
        </tbody>
      </table></div>
    </section>

    <section class="card">
      <div class="section-head"><div><h2>Σύνολα ανά ειδικότητα</h2><p>Μόνο από τις τελευταίες οριστικές υποβολές κάθε σχολείου.</p></div></div>
      <div class="table-wrap"><table class="vacancy-table"><thead><tr><th>Ειδικότητα</th><th>Κενά</th><th>Πλεονάσματα</th></tr></thead><tbody>
      <?php foreach ($specialties as $row) { ?><tr><td><strong><?php echo vacanciesH($row['code']); ?></strong> — <?php echo vacanciesH($row['label']); ?></td><td><?php echo (int)$row['vacancies']; ?></td><td><?php echo (int)$row['surpluses']; ?></td></tr><?php } ?>
      </tbody></table></div>
    </section>
  <?php } ?>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
