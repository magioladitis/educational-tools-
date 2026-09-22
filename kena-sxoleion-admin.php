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
$allocation = $round ? vacanciesDashboardAllocation($roundId) : array('specialties' => array(), 'schools' => array());
$schoolAllocationById = array();
foreach ($allocation['schools'] as $schoolAllocationRow) {
    $schoolAllocationById[(int) $schoolAllocationRow['id']] = $schoolAllocationRow;
}
$reasonOptions = vacanciesReasonOptions();
$previousRound = $round ? vacanciesPreviousRound($round) : null;
$comparison = ($round && $previousRound) ? vacanciesCompareRounds($roundId, (int) $previousRound['id']) : null;
$config = vacanciesConfig();
$defaultSchoolYear = ($config && isset($config['default_school_year'])) ? (string) $config['default_school_year'] : '2026-2027';
$roundStatusLabels = array(
    'open' => 'Ανοικτός',
    'closed' => 'Κλειστός',
    'draft' => 'Πρόχειρος',
);
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
    <p>Ζωντανή εικόνα υποβολών, κενών και πλεονασμάτων.</p>
  </section>
  <div class="vacancies-top-actions">
    <span class="vacancy-signed-in-as"><?php echo vacanciesH(vacanciesActorDisplayName()); ?></span>
    <?php if (vacanciesAccountsReady()) { ?><a class="vacancy-top-action" href="kena-sxoleion-users.php" aria-label="Λογαριασμοί" title="Λογαριασμοί"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 11a4 4 0 1 0-3.46-6A4 4 0 0 0 16 11ZM8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 1c-1.08 0-2.12.18-3.08.5A7.3 7.3 0 0 1 16 19v1h7v-1c0-3.31-3.13-6-7-6ZM8 14c-4.42 0-8 2.69-8 6v1h16v-1c0-3.31-3.58-6-8-6Z"/></svg><span class="vacancy-top-action__label">Λογαριασμοί</span></a><?php } ?>
    <?php $adminActor = vacanciesActor(); if ($adminActor && !empty($adminActor['user_id'])) { ?><a class="vacancy-top-action" href="kena-sxoleion-password.php" aria-label="Αλλαγή κωδικού" title="Αλλαγή κωδικού"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 14a5 5 0 1 1 4.58-3H24v4h-2v2h-3v2h-4v-5h-3.42A5 5 0 0 1 7 14Zm0-3a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg><span class="vacancy-top-action__label">Αλλαγή κωδικού</span></a><?php } ?>
    <a class="vacancy-top-action vacancy-top-action--logout" href="kena-sxoleion-login.php?logout=1" aria-label="Αποσύνδεση" title="Αποσύνδεση"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 2h2v10h-2V2Zm1 20a9 9 0 0 1-6.36-15.36l1.42 1.42A7 7 0 1 0 16.94 8.06l1.42-1.42A9 9 0 0 1 12 22Z"/></svg><span class="vacancy-top-action__label">Αποσύνδεση</span></a>
  </div>

  <?php if ($adminMessage !== '') { ?><div class="vacancy-alert vacancy-alert--<?php echo vacanciesH($adminMessageType); ?>"><?php echo vacanciesH($adminMessage); ?></div><?php } ?>

  <section class="card vacancy-round-picker">
    <div class="vacancy-round-toolbar">
      <form method="get" action="kena-sxoleion-admin.php">
        <div class="field"><label for="round">Γύρος καταγραφής</label><select id="round" name="round" onchange="this.form.submit()">
        <?php foreach ($rounds as $r) {
          $roundStatusLabel = isset($roundStatusLabels[$r['status']]) ? $roundStatusLabels[$r['status']] : $r['status'];
        ?><option value="<?php echo (int) $r['id']; ?>"<?php echo (int)$r['id']===$roundId?' selected':''; ?>><?php echo vacanciesH($r['title'] . ' · ' . $roundStatusLabel); ?></option><?php } ?>
        </select></div>
      </form>
      <?php if ($round) { ?>
        <a class="vacancy-export-button" href="kena-sxoleion-export.php?round=<?php echo (int)$round['id']; ?>&amp;scope=general">Εξαγωγή Excel</a>
      <?php } ?>
    </div>
    <?php if ($round) { ?><p class="vacancy-export-help">Μορφή συμβατή με το παλιό συγκεντρωτικό: γραμμές ειδικοτήτων, στήλες σχολείων, αρνητικό = κενό, θετικό = πλεόνασμα. Κενό κελί = δεν υπέβαλε.</p><?php } ?>
  </section>

  <section class="card vacancy-round-admin">
    <div class="section-head"><div><h2>Διαχείριση γύρων</h2></div></div>
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

    <section class="card vacancy-allocation">
      <div class="section-head"><div><h2>Σύνολα ανά ειδικότητα</h2><p>Μία ενιαία εικόνα κενών και πλεονασμάτων. Πάτησε στην ειδικότητα για να δεις αμέσως ποια σχολεία χρειάζονται ή διαθέτουν ώρες.</p></div></div>
      <?php if (empty($allocation['specialties'])) { ?>
        <div class="vacancy-empty">Δεν υπάρχουν ακόμη οριστικές υποβολές με κενά ή πλεονάσματα.</div>
      <?php } else { ?>
      <div class="table-wrap"><table class="vacancy-table vacancy-allocation-summary vacancy-drill-table">
        <thead><tr><th>Ειδικότητα</th><th>Κενά</th><th>Σχολεία με κενό</th><th>Πλεονάσματα</th><th>Σχολεία με πλεόνασμα</th><th>Θεωρητικά ακάλυπτο</th></tr></thead>
        <tbody>
        <?php foreach ($allocation['specialties'] as $spec) {
          $uncovered = max(0, (int)$spec['vacancies'] - (int)$spec['surpluses']);
          $specDetailsId = 'spec-details-' . (int) $spec['id'];
        ?>
          <tr class="vacancy-drill-main-row">
            <td>
              <button type="button" class="vacancy-drill-toggle" data-vacancy-drill-toggle="<?php echo vacanciesH($specDetailsId); ?>" aria-expanded="false">
                <span class="vacancy-drill-chevron" aria-hidden="true">›</span>
                <span><strong><?php echo vacanciesH($spec['code']); ?></strong> — <?php echo vacanciesH($spec['label']); ?></span>
              </button>
            </td>
            <td><strong><?php echo (int)$spec['vacancies']; ?></strong></td>
            <td><?php echo count($spec['vacancy_schools']); ?></td>
            <td><strong><?php echo (int)$spec['surpluses']; ?></strong></td>
            <td><?php echo count($spec['surplus_schools']); ?></td>
            <td><strong><?php echo $uncovered; ?></strong></td>
          </tr>
          <tr id="<?php echo vacanciesH($specDetailsId); ?>" class="vacancy-drill-row" hidden>
            <td colspan="6">
              <div class="vacancy-drill-panel vacancy-allocation-columns">
                <div>
                  <h3>Σχολεία με κενό</h3>
                  <?php if (empty($spec['vacancy_schools'])) { ?><p class="vacancy-muted">Δεν υπάρχουν.</p><?php } else { ?>
                  <div class="table-wrap"><table class="vacancy-table vacancy-inner-table"><thead><tr><th>Σχολείο</th><th>Ώρες</th><th>Αιτία</th></tr></thead><tbody>
                  <?php foreach ($spec['vacancy_schools'] as $entry) { ?>
                    <tr><td><strong><?php echo vacanciesH($entry['school_name']); ?></strong><small><?php echo vacanciesH($entry['ministry_code']); ?></small></td><td><strong><?php echo (int)$entry['hours']; ?></strong></td><td><?php echo vacanciesH(isset($reasonOptions[$entry['reason']]) ? $reasonOptions[$entry['reason']] : '—'); ?><?php if (!empty($entry['note'])) { ?><small><?php echo vacanciesH($entry['note']); ?></small><?php } ?></td></tr>
                  <?php } ?>
                  </tbody></table></div>
                  <?php } ?>
                </div>
                <div>
                  <h3>Σχολεία με πλεόνασμα</h3>
                  <?php if (empty($spec['surplus_schools'])) { ?><p class="vacancy-muted">Δεν υπάρχουν.</p><?php } else { ?>
                  <div class="table-wrap"><table class="vacancy-table vacancy-inner-table"><thead><tr><th>Σχολείο</th><th>Ώρες</th></tr></thead><tbody>
                  <?php foreach ($spec['surplus_schools'] as $entry) { ?>
                    <tr><td><strong><?php echo vacanciesH($entry['school_name']); ?></strong><small><?php echo vacanciesH($entry['ministry_code']); ?></small></td><td><strong><?php echo (int)$entry['hours']; ?></strong></td></tr>
                  <?php } ?>
                  </tbody></table></div>
                  <?php } ?>
                </div>
              </div>
            </td>
          </tr>
        <?php } ?>
        </tbody>
      </table></div>
      <p class="vacancy-footnote">Το «θεωρητικά ακάλυπτο» είναι αριθμητική διαφορά κενών–πλεονασμάτων και δεν αποτελεί αυτόματη πρόταση μετακίνησης.</p>
      <?php } ?>
    </section>

    <section class="card vacancy-schools-unified">
      <div class="section-head"><div><h2>Κατάσταση σχολικών μονάδων</h2><p>Η κατάσταση και η ανάλυση ανά ειδικότητα βρίσκονται πλέον μαζί. Πάτησε στο όνομα του σχολείου για λεπτομέρειες. Οι «Παρατηρήσεις» παραμένουν διαθέσιμες ξεχωριστά.</p></div></div>
      <div class="table-wrap"><table class="vacancy-table vacancy-drill-table vacancy-school-status-table">
        <thead><tr><th>Σχολείο</th><th>Κατάσταση</th><th>Κενά</th><th>Πλεονάσματα</th><th>Αναθ.</th><th>Υποβολή</th><th>Παρατηρήσεις</th></tr></thead>
        <tbody>
        <?php foreach ($schools as $row) {
          $schoolId = (int) $row['id'];
          $status = isset($row['status']) ? $row['status'] : '';
          $statusLabel = $status === 'submitted' ? 'Οριστική' : ($status === 'draft' ? 'Πρόχειρη' : 'Δεν υπέβαλε');
          $statusClass = $status === 'submitted' ? 'ok' : ($status === 'draft' ? 'draft' : 'missing');
          $schoolDetailsId = 'school-details-' . $schoolId;
          $schoolAllocation = isset($schoolAllocationById[$schoolId]) ? $schoolAllocationById[$schoolId] : null;
        ?>
          <tr class="vacancy-drill-main-row">
            <td>
              <button type="button" class="vacancy-drill-toggle vacancy-school-toggle" data-vacancy-drill-toggle="<?php echo vacanciesH($schoolDetailsId); ?>" aria-expanded="false">
                <span class="vacancy-drill-chevron" aria-hidden="true">›</span>
                <span><strong><?php echo vacanciesH($row['name']); ?></strong><small><?php echo vacanciesH($row['ministry_code']); ?></small></span>
              </button>
            </td>
            <td><span class="vacancy-badge vacancy-badge--<?php echo $statusClass; ?>"><?php echo vacanciesH($statusLabel); ?></span></td>
            <td><?php echo $status ? (int)$row['vacancies'] : '—'; ?></td>
            <td><?php echo $status ? (int)$row['surpluses'] : '—'; ?></td>
            <td><?php echo $status ? '#'.(int)$row['revision_no'] : '—'; ?></td>
            <td><?php echo !empty($row['submitted_at']) ? vacanciesH($row['submitted_at']) : '—'; ?></td>
            <td>
              <?php if ($status && trim((string)($row['school_note'] ?? '')) !== '') { ?>
                <details class="vacancy-note-details">
                  <summary>Παρατηρήσεις</summary>
                  <div class="vacancy-note-popover"><?php echo nl2br(vacanciesH($row['school_note'])); ?></div>
                </details>
              <?php } else { ?>
                <span class="vacancy-muted">—</span>
              <?php } ?>
            </td>
          </tr>
          <tr id="<?php echo vacanciesH($schoolDetailsId); ?>" class="vacancy-drill-row" hidden>
            <td colspan="7">
              <div class="vacancy-drill-panel vacancy-school-analysis-panel">
                <?php if ($status === 'submitted' && $schoolAllocation && !empty($schoolAllocation['entries'])) { ?>
                  <div class="table-wrap"><table class="vacancy-table vacancy-inner-table"><thead><tr><th>Ειδικότητα</th><th>Κατάσταση</th><th>Ώρες</th><th>Αιτία / σημείωση</th></tr></thead><tbody>
                  <?php foreach ($schoolAllocation['entries'] as $entry) {
                    $entryStatus = $entry['type'] === 'vacancy' ? 'Κενό' : 'Πλεόνασμα';
                  ?>
                    <tr><td><strong><?php echo vacanciesH($entry['code']); ?></strong> — <?php echo vacanciesH($entry['label']); ?></td><td><?php echo vacanciesH($entryStatus); ?></td><td><strong><?php echo (int)$entry['hours']; ?></strong></td><td><?php echo vacanciesH(isset($reasonOptions[$entry['reason']]) ? $reasonOptions[$entry['reason']] : '—'); ?><?php if (!empty($entry['note'])) { ?><small><?php echo vacanciesH($entry['note']); ?></small><?php } ?></td></tr>
                  <?php } ?>
                  </tbody></table></div>
                <?php } elseif ($status === 'submitted') { ?>
                  <div class="vacancy-zero-submission"><strong>Οριστική μηδενική καταχώριση.</strong><span>Το σχολείο υπέβαλε κανονικά και δεν δήλωσε κενά ή πλεονάσματα σε αυτόν τον γύρο.</span></div>
                <?php } elseif ($status === 'draft') { ?>
                  <div class="vacancy-empty">Υπάρχει πρόχειρη υποβολή. Η αναλυτική επιχειρησιακή εικόνα χρησιμοποιεί μόνο οριστικές υποβολές.</div>
                <?php } else { ?>
                  <div class="vacancy-empty">Δεν υπάρχει υποβολή για αυτόν τον γύρο.</div>
                <?php } ?>
              </div>
            </td>
          </tr>
        <?php } ?>
        </tbody>
      </table></div>
    </section>
  <?php } ?>
</main>
<script src="<?php echo vacanciesH(edu_asset_url('assets/vacancies.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
