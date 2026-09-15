<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/vacancies-model.php';

vacanciesSessionStart();
$schoolId = vacanciesActorSchoolId();
if ($schoolId <= 0 || !vacanciesDbReady()) {
    header('Location: kena-sxoleion-login.php');
    exit;
}
$school = vacanciesSchool($schoolId);
$round = vacanciesActiveRound();
if (!$school || !$round) {
    $round = null;
}
$message = '';
$messageType = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $round) {
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
        list($ok, $saveMessage) = vacanciesSaveSubmission((int) $round['id'], $schoolId, $status, $rows, isset($_POST['school_note']) ? $_POST['school_note'] : '');
        $message = $saveMessage;
        $messageType = $ok ? 'success' : 'danger';
    }
}
$latest = $round ? vacanciesLatestSubmission((int) $round['id'], $schoolId, true) : null;
$existing = $latest ? vacanciesSubmissionEntries((int) $latest['id']) : array();
$specialties = vacanciesSpecialties();
$reasons = vacanciesReasonOptions();
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υποβολή κενών — <?php echo vacanciesH($school ? $school['name'] : 'Σχολείο'); ?></title>
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/vacancies.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-vacancies">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell vacancies-shell">
  <section class="hero vacancies-hero">
    <span class="hero-kicker">ΚΑΤΑΓΡΑΦΗ ΚΕΝΩΝ</span>
    <h1><?php echo vacanciesH($school ? $school['name'] : 'Σχολική μονάδα'); ?></h1>
    <div class="hero-meta">
      <span>Κωδικός: <?php echo vacanciesH($school ? $school['ministry_code'] : '—'); ?></span>
      <?php if ($round) { ?><span><?php echo vacanciesH($round['title']); ?></span><?php } ?>
      <?php if ($latest) { ?><span>Τελευταία αναθεώρηση: #<?php echo (int) $latest['revision_no']; ?> · <?php echo $latest['status'] === 'submitted' ? 'Οριστική' : 'Πρόχειρη'; ?></span><?php } ?>
    </div>
  </section>

  <div class="vacancies-top-actions"><a href="kena-sxoleion-login.php?logout=1">Αποσύνδεση</a></div>

  <?php if ($message !== '') { ?><div class="vacancy-alert vacancy-alert--<?php echo vacanciesH($messageType); ?>"><?php echo vacanciesH($message); ?></div><?php } ?>

  <?php if (!$round) { ?>
    <section class="card vacancy-status vacancy-status--info"><h2>Δεν υπάρχει ανοικτός γύρος</h2><p>Η Διεύθυνση δεν έχει ανοίξει αυτή τη στιγμή περίοδο καταγραφής.</p></section>
  <?php } else { ?>
    <form method="post" action="kena-sxoleion.php" data-vacancies-form>
      <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
      <section class="card">
        <div class="section-head"><div><h2>Κενά και πλεονάσματα</h2><p>Δεν γράφεις πρόσημο. Επιλέγεις «Κενό» ή «Πλεόνασμα» και καταχωρίζεις μόνο θετικό αριθμό ωρών.</p></div></div>
        <div class="vacancy-zero-rule"><strong>Σημαντικό:</strong> Αν κάνεις οριστική υποβολή, κάθε ειδικότητα που δεν έχει προστεθεί θεωρείται <strong>0 ώρες</strong>. Χωρίς οριστική υποβολή το σχολείο εμφανίζεται ως <strong>«δεν έχει υποβάλει»</strong>, όχι ως μηδενικό.</div>

        <div class="vacancy-add-row">
          <label for="specialtyPicker">Πρόσθεσε ειδικότητα</label>
          <div class="vacancy-add-controls">
            <select id="specialtyPicker">
              <option value="">— Επίλεξε ειδικότητα —</option>
              <?php foreach ($specialties as $sp) { ?>
                <option value="<?php echo (int) $sp['id']; ?>" data-code="<?php echo vacanciesH($sp['code']); ?>" data-label="<?php echo vacanciesH($sp['label']); ?>"><?php echo vacanciesH($sp['code'] . ' — ' . $sp['label']); ?></option>
              <?php } ?>
            </select>
            <button type="button" class="secondary" data-add-specialty>Προσθήκη</button>
          </div>
        </div>

        <div id="vacancyEntries" class="vacancy-entry-list" data-next-index="<?php echo count($existing); ?>">
          <?php $i=0; foreach ($existing as $entry) { ?>
            <article class="vacancy-entry" data-specialty-id="<?php echo (int) $entry['specialty_id']; ?>">
              <div class="vacancy-entry__head">
                <strong><?php echo vacanciesH($entry['code'] . ' — ' . $entry['label']); ?></strong>
                <button type="button" class="vacancy-remove" data-remove-entry aria-label="Αφαίρεση">🗑</button>
              </div>
              <input type="hidden" name="entries[<?php echo $i; ?>][specialty_id]" value="<?php echo (int) $entry['specialty_id']; ?>">
              <div class="vacancy-entry__grid">
                <div class="field"><label>Κατάσταση</label><select name="entries[<?php echo $i; ?>][type]"><option value="vacancy"<?php echo $entry['balance_type']==='vacancy'?' selected':''; ?>>Κενό</option><option value="surplus"<?php echo $entry['balance_type']==='surplus'?' selected':''; ?>>Πλεόνασμα</option></select></div>
                <div class="field"><label>Ώρες</label><input type="number" min="1" max="999" step="1" inputmode="numeric" name="entries[<?php echo $i; ?>][hours]" value="<?php echo (int) $entry['hours']; ?>" required></div>
                <div class="field"><label>Αιτία μεταβολής</label><select name="entries[<?php echo $i; ?>][reason]">
                  <?php foreach ($reasons as $value=>$label) { ?><option value="<?php echo vacanciesH($value); ?>"<?php echo $entry['change_reason']===$value?' selected':''; ?>><?php echo vacanciesH($label); ?></option><?php } ?>
                </select></div>
                <div class="field"><label>Σημείωση</label><input type="text" maxlength="500" name="entries[<?php echo $i; ?>][note]" value="<?php echo vacanciesH($entry['change_note']); ?>" placeholder="π.χ. νέο τμήμα Α΄"></div>
              </div>
            </article>
          <?php $i++; } ?>
        </div>
        <div id="vacancyEmptyState" class="vacancy-empty"<?php echo $existing ? ' hidden' : ''; ?>>Δεν έχει προστεθεί ακόμη κενό ή πλεόνασμα. Αν το σχολείο δεν έχει καμία ανάγκη, μπορείς να κάνεις οριστική υποβολή με κενή λίστα.</div>
      </section>

      <section class="card">
        <h2>Σημείωση σχολείου</h2>
        <div class="field"><label for="school_note">Προαιρετική γενική παρατήρηση</label><textarea id="school_note" name="school_note" rows="4" maxlength="4000" placeholder="Π.χ. δημιουργήθηκε νέο τμήμα μετά την προηγούμενη καταγραφή."><?php echo vacanciesH($latest ? $latest['school_note'] : ''); ?></textarea></div>
      </section>

      <div class="vacancies-submit-bar">
        <button class="secondary" type="submit" name="submit_action" value="draft">Αποθήκευση πρόχειρου</button>
        <button class="primary" type="submit" name="submit_action" value="submit" data-final-submit>Οριστική υποβολή</button>
      </div>
    </form>

    <template id="vacancyEntryTemplate">
      <article class="vacancy-entry" data-specialty-id="__SID__">
        <div class="vacancy-entry__head"><strong>__DISPLAY__</strong><button type="button" class="vacancy-remove" data-remove-entry aria-label="Αφαίρεση">🗑</button></div>
        <input type="hidden" name="entries[__IDX__][specialty_id]" value="__SID__">
        <div class="vacancy-entry__grid">
          <div class="field"><label>Κατάσταση</label><select name="entries[__IDX__][type]"><option value="vacancy">Κενό</option><option value="surplus">Πλεόνασμα</option></select></div>
          <div class="field"><label>Ώρες</label><input type="number" min="1" max="999" step="1" inputmode="numeric" name="entries[__IDX__][hours]" value="1" required></div>
          <div class="field"><label>Αιτία μεταβολής</label><select name="entries[__IDX__][reason]">
            <?php foreach ($reasons as $value=>$label) { ?><option value="<?php echo vacanciesH($value); ?>"><?php echo vacanciesH($label); ?></option><?php } ?>
          </select></div>
          <div class="field"><label>Σημείωση</label><input type="text" maxlength="500" name="entries[__IDX__][note]" placeholder="π.χ. νέο τμήμα Α΄"></div>
        </div>
      </article>
    </template>
  <?php } ?>
</main>
<script src="<?php echo vacanciesH(edu_asset_url('assets/vacancies.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
