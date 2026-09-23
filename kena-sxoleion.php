<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/vacancies-model.php';

vacanciesSessionStart();
if (vacanciesActorNeedsPasswordChange()) {
    header('Location: kena-sxoleion-password.php');
    exit;
}
$schoolId = vacanciesActorSchoolId();
if ($schoolId <= 0 || !vacanciesDbReady()) {
    header('Location: kena-sxoleion-login.php');
    exit;
}
$school = vacanciesSchool($schoolId);
if (!$school) {
    vacanciesLogout();
    header('Location: kena-sxoleion-login.php');
    exit;
}
$tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'profile';
if (!in_array($tab, array('profile','vacancies','history'), true)) $tab = 'profile';
$scope = 'general';
$round = vacanciesActiveRound();
$message = '';
$messageType = 'success';
$savedAt = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $round && $tab === 'vacancies') {
    if (!vacanciesCsrfValid(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $message = 'Η συνεδρία έληξε. Ανανέωσε τη σελίδα και προσπάθησε ξανά.';
        $messageType = 'danger';
    } else {
        $action = isset($_POST['submit_action']) ? (string) $_POST['submit_action'] : 'draft';
        $status = $action === 'submit' ? 'submitted' : 'draft';
        $rows = array();
        if (isset($_POST['entries']) && is_array($_POST['entries'])) {
            foreach ($_POST['entries'] as $entry) {
                if (!is_array($entry)) continue;
                $sid = isset($entry['specialty_id']) ? (int) $entry['specialty_id'] : 0;
                if ($sid <= 0) continue;
                $rows[$sid] = array(
                    'type' => isset($entry['type']) ? (string) $entry['type'] : 'zero',
                    'hours' => isset($entry['hours']) ? (int) $entry['hours'] : 0,
                    'reason' => isset($entry['reason']) ? (string) $entry['reason'] : '',
                    'note' => isset($entry['note']) ? (string) $entry['note'] : '',
                );
            }
        }
        // school_id comes exclusively from the authenticated session, never from POST/URL.
        list($ok, $saveMessage) = vacanciesSaveSubmission((int) $round['id'], $schoolId, $status, $rows, isset($_POST['school_note']) ? $_POST['school_note'] : '', $scope);
        $message = $saveMessage;
        $messageType = $ok ? 'success' : 'danger';
        if ($ok) $savedAt = date('H:i');
    }
}
$latest = $round ? vacanciesLatestSubmission((int) $round['id'], $schoolId, true, $scope) : null;
$existing = $latest ? vacanciesSubmissionEntries((int) $latest['id']) : array();
$specialties = vacanciesSpecialties();
$reasons = vacanciesReasonOptions();
$history = $tab === 'history' ? vacanciesSubmissionHistory($schoolId, 100) : array();
$actor = vacanciesActor();
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="manifest" href="manifest.webmanifest">
  <title><?php echo vacanciesH($school['name']); ?> — Καταγραφή κενών</title>
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/vacancies.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-vacancies">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell vacancies-shell">
  <section class="hero vacancies-hero">
    <span class="hero-kicker">ΔΙΕΥΘΥΝΤΗΣ ΣΧΟΛΕΙΟΥ</span>
    <h1><?php echo vacanciesH($school['name']); ?></h1>
    <div class="hero-meta">
      <span>Κωδικός: <?php echo vacanciesH($school['ministry_code']); ?></span>
      <span><?php echo vacanciesH($school['school_type']); ?></span>
      <?php if ($round) { ?><span><?php echo vacanciesH($round['title']); ?></span><?php } ?>
    </div>
  </section>

  <div class="vacancies-top-actions">
    <span class="vacancy-signed-in-as"><?php echo vacanciesH(vacanciesActorDisplayName()); ?></span>
    <?php if (!empty($actor['user_id'])) { ?>
      <a class="vacancy-top-action" href="kena-sxoleion-password.php" aria-label="Αλλαγή κωδικού" title="Αλλαγή κωδικού"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 14a5 5 0 1 1 4.58-3H24v4h-2v2h-3v2h-4v-5h-3.42A5 5 0 0 1 7 14Zm0-3a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg><span class="vacancy-top-action__label">Αλλαγή κωδικού</span></a>
    <?php } ?>
    <a class="vacancy-top-action vacancy-top-action--logout" href="kena-sxoleion-login.php?logout=1" aria-label="Αποσύνδεση" title="Αποσύνδεση"><svg class="vacancy-top-action__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 2h2v10h-2V2Zm1 20a9 9 0 0 1-6.36-15.36l1.42 1.42A7 7 0 1 0 16.94 8.06l1.42-1.42A9 9 0 0 1 12 22Z"/></svg><span class="vacancy-top-action__label">Αποσύνδεση</span></a>
  </div>

  <nav class="vacancy-tabs" aria-label="Ενότητες σχολικής μονάδας">
    <a href="kena-sxoleion.php?tab=profile" class="<?php echo $tab==='profile'?'is-active':''; ?>">Στοιχεία σχολείου</a>
    <a href="kena-sxoleion.php?tab=vacancies" class="<?php echo $tab==='vacancies'?'is-active':''; ?>">Καταχώριση κενών</a>
    <a href="kena-sxoleion.php?tab=history" class="<?php echo $tab==='history'?'is-active':''; ?>">Ιστορικό υποβολών</a>
  </nav>

  <?php if ($message !== '') { ?><div class="vacancy-alert vacancy-alert--<?php echo vacanciesH($messageType); ?>"><?php echo vacanciesH($message); ?></div><?php } ?>

  <?php if ($tab === 'profile') { ?>
    <section class="card vacancy-school-profile">
      <div class="section-head"><div><h2>Στοιχεία σχολείου</h2><p>Η ταυτότητα της σχολικής μονάδας είναι συνδεδεμένη με τον λογαριασμό σου και δεν αλλάζει από αυτή την εφαρμογή.</p></div></div>
      <dl class="vacancy-profile-grid">
        <div><dt>Επίσημη ονομασία</dt><dd><?php echo vacanciesH($school['name']); ?></dd></div>
        <div><dt>Κωδικός Υπουργείου</dt><dd><code><?php echo vacanciesH($school['ministry_code']); ?></code></dd></div>
        <div><dt>Τύπος σχολείου</dt><dd><?php echo vacanciesH($school['school_type'] ?: '—'); ?></dd></div>
        <div><dt>Διεύθυνση</dt><dd><?php echo vacanciesH($school['address'] ?: '—'); ?></dd></div>
        <div><dt>Υπηρεσιακό email</dt><dd><?php if (!empty($school['email'])) { ?><a href="mailto:<?php echo vacanciesH($school['email']); ?>"><?php echo vacanciesH($school['email']); ?></a><?php } else { ?>—<?php } ?></dd></div>
        <div><dt>Τηλέφωνο</dt><dd><?php if (!empty($school['phone'])) { ?><a href="tel:<?php echo vacanciesH(preg_replace('/[^0-9+]/', '', $school['phone'])); ?>"><?php echo vacanciesH($school['phone']); ?></a><?php } else { ?>—<?php } ?></dd></div>
      </dl>
    </section>

    <section class="vacancy-kpis vacancy-school-home-kpis">
      <article><span>Τρέχων γύρος</span><strong class="vacancy-kpi-text"><?php echo $round ? vacanciesH(date('d/m/Y', strtotime($round['reference_date']))) : '—'; ?></strong><small><?php echo $round ? 'Ανοικτός για υποβολή' : 'Δεν υπάρχει ανοικτός γύρος'; ?></small></article>
      <article><span>Τρέχουσα κατάσταση</span><strong class="vacancy-kpi-text"><?php echo !$round ? '—' : (!$latest ? 'Δεν υπέβαλε' : ($latest['status']==='submitted'?'Οριστική':'Πρόχειρη')); ?></strong><small><?php echo $latest ? 'Αναθεώρηση #'.(int)$latest['revision_no'] : 'Γενική'; ?></small></article>
    </section>

  <?php } elseif ($tab === 'vacancies') { ?>
    <?php if (!$round) { ?>
      <section class="card vacancy-status vacancy-status--info"><h2>Δεν υπάρχει ανοικτός γύρος</h2><p>Η Διεύθυνση δεν έχει ανοίξει αυτή τη στιγμή περίοδο καταγραφής.</p></section>
    <?php } else { ?>
      <form method="post" action="kena-sxoleion.php?tab=vacancies" data-vacancies-form>
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <section class="card">
          <div class="section-head"><div><h2>Κενά και πλεονάσματα — Γενική</h2><p>Δεν γράφεις πρόσημο. Επιλέγεις «Κενό» ή «Πλεόνασμα» και καταχωρίζεις μόνο θετικό αριθμό ωρών.</p></div><?php if ($latest) { ?><div class="vacancy-current-revision"><span><?php echo $latest['status']==='submitted'?'Οριστική':'Πρόχειρη'; ?></span><strong>#<?php echo (int)$latest['revision_no']; ?></strong></div><?php } ?></div>
          <div class="vacancy-zero-rule"><strong>Σημαντικό:</strong> Αν κάνεις οριστική υποβολή, κάθε ειδικότητα που δεν έχει προστεθεί θεωρείται <strong>0 ώρες</strong>. Χωρίς οριστική υποβολή το σχολείο εμφανίζεται ως <strong>«δεν έχει υποβάλει»</strong>, όχι ως μηδενικό.</div>

          <div class="vacancy-add-row">
            <label for="specialtyPicker">Πρόσθεσε ειδικότητα</label>
            <div class="vacancy-add-controls">
              <select id="specialtyPicker"><option value="">— Επίλεξε ειδικότητα —</option><?php foreach ($specialties as $sp) { ?><option value="<?php echo (int) $sp['id']; ?>" data-code="<?php echo vacanciesH($sp['code']); ?>" data-label="<?php echo vacanciesH($sp['label']); ?>"><?php echo vacanciesH($sp['code'] . ' — ' . $sp['label']); ?></option><?php } ?></select>
              <button type="button" class="secondary" data-add-specialty>Προσθήκη</button>
            </div>
          </div>

          <div id="vacancyEntries" class="vacancy-entry-list" data-next-index="<?php echo count($existing); ?>">
            <?php $i=0; foreach ($existing as $entry) { ?>
              <article class="vacancy-entry" data-specialty-id="<?php echo (int) $entry['specialty_id']; ?>">
                <div class="vacancy-entry__head"><strong><?php echo vacanciesH($entry['code'] . ' — ' . $entry['label']); ?></strong><button type="button" class="vacancy-remove" data-remove-entry aria-label="Αφαίρεση">🗑</button></div>
                <input type="hidden" name="entries[<?php echo $i; ?>][specialty_id]" value="<?php echo (int) $entry['specialty_id']; ?>">
                <div class="vacancy-entry__grid">
                  <div class="field"><label>Κατάσταση</label><select name="entries[<?php echo $i; ?>][type]"><option value="vacancy"<?php echo $entry['balance_type']==='vacancy'?' selected':''; ?>>Κενό</option><option value="surplus"<?php echo $entry['balance_type']==='surplus'?' selected':''; ?>>Πλεόνασμα</option></select></div>
                  <div class="field"><label>Ώρες</label><input type="number" min="1" max="999" step="1" inputmode="numeric" name="entries[<?php echo $i; ?>][hours]" value="<?php echo (int) $entry['hours']; ?>" required></div>
                  <div class="field"><label>Αιτία μεταβολής</label><select name="entries[<?php echo $i; ?>][reason]"><?php foreach ($reasons as $value=>$label) { ?><option value="<?php echo vacanciesH($value); ?>"<?php echo $entry['change_reason']===$value?' selected':''; ?>><?php echo vacanciesH($label); ?></option><?php } ?></select></div>
                  <div class="field"><label>Σημείωση</label><input type="text" maxlength="500" name="entries[<?php echo $i; ?>][note]" value="<?php echo vacanciesH($entry['change_note']); ?>" placeholder="π.χ. νέο τμήμα Α΄"></div>
                </div>
              </article>
            <?php $i++; } ?>
          </div>
          <div id="vacancyEmptyState" class="vacancy-empty"<?php echo $existing ? ' hidden' : ''; ?>>Δεν έχει προστεθεί ακόμη κενό ή πλεόνασμα. Αν το σχολείο δεν έχει καμία ανάγκη, μπορείς να κάνεις οριστική υποβολή με κενή λίστα.</div>
        </section>

        <section class="card"><h2>Παρατηρήσεις σχολείου</h2><div class="field"><label for="school_note">Προαιρετική γενική παρατήρηση</label><textarea id="school_note" name="school_note" rows="4" maxlength="4000" placeholder="Π.χ. δημιουργήθηκε νέο τμήμα μετά την προηγούμενη καταγραφή."><?php echo vacanciesH($latest ? $latest['school_note'] : ''); ?></textarea></div></section>

        <div class="vacancies-submit-bar">
          <button class="secondary" type="submit" name="submit_action" value="draft">Αποθήκευση πρόχειρου</button>
          <button class="primary" type="submit" name="submit_action" value="submit" data-final-submit>Οριστική υποβολή</button>
          <?php if ($savedAt !== '') { ?><span class="vacancy-save-status" role="status" aria-live="polite">✓ Αποθηκεύτηκε στις <?php echo vacanciesH($savedAt); ?></span><?php } ?>
        </div>
      </form>

      <template id="vacancyEntryTemplate">
        <article class="vacancy-entry" data-specialty-id="__SID__"><div class="vacancy-entry__head"><strong>__DISPLAY__</strong><button type="button" class="vacancy-remove" data-remove-entry aria-label="Αφαίρεση">🗑</button></div><input type="hidden" name="entries[__IDX__][specialty_id]" value="__SID__"><div class="vacancy-entry__grid"><div class="field"><label>Κατάσταση</label><select name="entries[__IDX__][type]"><option value="vacancy">Κενό</option><option value="surplus">Πλεόνασμα</option></select></div><div class="field"><label>Ώρες</label><input type="number" min="1" max="999" step="1" inputmode="numeric" name="entries[__IDX__][hours]" value="1" required></div><div class="field"><label>Αιτία μεταβολής</label><select name="entries[__IDX__][reason]"><?php foreach ($reasons as $value=>$label) { ?><option value="<?php echo vacanciesH($value); ?>"><?php echo vacanciesH($label); ?></option><?php } ?></select></div><div class="field"><label>Σημείωση</label><input type="text" maxlength="500" name="entries[__IDX__][note]" placeholder="π.χ. νέο τμήμα Α΄"></div></div></article>
      </template>
    <?php } ?>

  <?php } else { ?>
    <section class="card">
      <div class="section-head"><div><h2>Ιστορικό υποβολών</h2><p>Εμφανίζονται οι οριστικές υποβολές και, εφόσον υπάρχει, το πιο πρόσφατο πρόχειρο.</p></div></div>
      <?php if (!$history) { ?><div class="vacancy-empty">Δεν υπάρχει ακόμη ιστορικό υποβολών.</div><?php } else { ?>
      <div class="table-wrap"><table class="vacancy-table"><thead><tr><th>Ημερομηνία αναφοράς</th><th>Πεδίο</th><th>Κατάσταση</th><th>Αναθ.</th><th>Κενά</th><th>Πλεονάσματα</th><th>Υποβολή</th><th>Παρατηρήσεις</th></tr></thead><tbody>
      <?php foreach ($history as $item) { ?>
        <tr><td><strong><?php echo vacanciesH(date('d/m/Y', strtotime($item['reference_date']))); ?></strong><small><?php echo vacanciesH($item['title']); ?></small></td><td><?php echo $item['education_scope']==='special'?'Ειδική':'Γενική'; ?></td><td><span class="vacancy-badge vacancy-badge--<?php echo $item['status']==='submitted'?'ok':'draft'; ?>"><?php echo $item['status']==='submitted'?'Οριστική':'Πρόχειρη'; ?></span></td><td>#<?php echo (int)$item['revision_no']; ?></td><td><?php echo (int)$item['vacancies']; ?></td><td><?php echo (int)$item['surpluses']; ?></td><td><?php echo !empty($item['submitted_at']) ? vacanciesH($item['submitted_at']) : vacanciesH($item['created_at']); ?></td><td><?php echo trim((string)$item['school_note'])!=='' ? nl2br(vacanciesH($item['school_note'])) : '—'; ?></td></tr>
      <?php } ?>
      </tbody></table></div>
      <?php } ?>
    </section>
  <?php } ?>
</main>
<script src="<?php echo vacanciesH(edu_asset_url('assets/vacancies.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
