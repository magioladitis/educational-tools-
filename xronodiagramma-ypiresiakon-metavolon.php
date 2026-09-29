<?php
require_once __DIR__ . '/includes/config.php';
$data = require __DIR__ . '/includes/service-timeline-data.php';
$flags = ENT_QUOTES;
if (defined('ENT_SUBSTITUTE')) $flags |= ENT_SUBSTITUTE;
$h = function ($value) use ($flags) { return htmlspecialchars((string) $value, $flags, 'UTF-8'); };
$years = isset($data['history_years']) ? $data['history_years'] : array();
$groups = isset($data['groups']) ? $data['groups'] : array();
$events = isset($data['events']) ? $data['events'] : array();
$verifiedCount = 0;
foreach ($events as $event) if (!empty($event['latest_verified'])) $verifiedCount++;
$hasUnverified = $verifiedCount < count($events);
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <meta name="description" content="Χρονοδιάγραμμα υπηρεσιακών μεταβολών εκπαιδευτικών με ιστορικές ημερομηνίες, φίλτρα και επίσημες πηγές για μεταθέσεις, αποσπάσεις, μετατάξεις και νεοδιόριστους.">
  <title>Χρονοδιάγραμμα Υπηρεσιακών Μεταβολών | Εργαλειοθήκη Εκπαιδευτικού</title>
  <link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/common.css')); ?>">
</head>
<body class="edu-ui edu-page-service-timeline">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="timeline-shell">
  <?php eduPageHero(array(
    'class' => 'timeline-hero',
    'kicker' => 'ΥΠΗΡΕΣΙΑΚΕΣ ΜΕΤΑΒΟΛΕΣ',
    'kicker_class' => 'timeline-kicker',
    'title' => 'Χρονοδιάγραμμα Εκπαιδευτικών',
    'intro' => 'Δείτε πότε πραγματοποιήθηκαν οι βασικές υπηρεσιακές διαδικασίες τα προηγούμενα έτη και ποιες ημερομηνίες έχουν ήδη ανακοινωθεί για τον τρέχοντα κύκλο.',
    'meta' => array(
      'Ιστορικό 2019–2026',
      'Ενημέρωση: ' . $data['updated_at']
    ),
    'meta_class' => 'timeline-hero-meta',
    'meta_attrs' => array('aria-label' => 'Σύνοψη χρονοδιαγράμματος')
  )); ?>

  <section class="timeline-now edu-card" aria-labelledby="timelineNowTitle">
    <div>
      <span class="timeline-eyebrow">ΤΡΕΧΩΝ ΚΥΚΛΟΣ <?php echo $h($data['current_cycle']); ?></span>
      <h2 id="timelineNowTitle">Τι γνωρίζουμε τώρα</h2>
      <p>Η εγκύκλιος μεταθέσεων για τον κύκλο <?php echo $h($data['current_cycle']); ?> δεν έχει ακόμη ανακοινωθεί. Όταν δημοσιευτεί, η ημερομηνία θα προστεθεί εδώ.</p>
    </div>
    <a class="timeline-deadlines-link" href="prothesmies.php">Δες ενεργές προθεσμίες →</a>
  </section>

  <section class="timeline-controls edu-card" aria-label="Φίλτρα χρονοδιαγράμματος">
    <div class="timeline-filter-stack">
      <div class="timeline-filter-row">
        <span class="timeline-filter-label">Κατηγορία</span>
        <div class="timeline-filter-group" role="group" aria-label="Κατηγορία διαδικασίας">
          <button type="button" class="timeline-chip is-active" data-timeline-filter="all">Όλα</button>
          <?php foreach ($groups as $key => $label) { ?>
            <button type="button" class="timeline-chip" data-timeline-filter="<?php echo $h($key); ?>"><?php echo $h($label); ?></button>
          <?php } ?>
        </div>
      </div>
      <div class="timeline-filter-row">
        <span class="timeline-filter-label">Βαθμίδα / προσωπικό</span>
        <div class="timeline-filter-group" role="group" aria-label="Βαθμίδα ή κατηγορία προσωπικού">
          <button type="button" class="timeline-chip is-active" data-timeline-level="all">Όλα</button>
          <button type="button" class="timeline-chip" data-timeline-level="pe">Π.Ε.</button>
          <button type="button" class="timeline-chip" data-timeline-level="de">Δ.Ε.</button>
          <button type="button" class="timeline-chip" data-timeline-level="eep-evp">ΕΕΠ-ΕΒΠ</button>
        </div>
      </div>
    </div>
    <div class="timeline-control-actions">
      <?php if ($hasUnverified) { ?>
        <label class="timeline-verified-toggle">
          <input id="timelineVerifiedOnly" type="checkbox" checked>
          <span>Μόνο επιβεβαιωμένες ημερομηνίες</span>
        </label>
      <?php } ?>
      <a class="timeline-export-link" href="xronodiagramma-ypiresiakon-metavolon-export.php" aria-label="Εξαγωγή χρονοδιαγράμματος σε Excel">Εξαγωγή σε XLS</a>
    </div>
  </section>

  <div class="timeline-status-line" id="timelineStatusLine" role="status" aria-live="polite"></div>

  <section class="timeline-list" id="timelineList" aria-label="Γεγονότα χρονοδιαγράμματος">
    <?php foreach ($events as $event) {
      $verified = !empty($event['latest_verified']);
      $history = isset($event['history']) && is_array($event['history']) ? $event['history'] : array();
      $sources = isset($event['sources']) && is_array($event['sources']) ? $event['sources'] : array();
      $historicalSources = isset($event['historical_sources']) && is_array($event['historical_sources']) ? $event['historical_sources'] : array();
      $verifiedHistoryIndices = isset($event['verified_history_indices']) && is_array($event['verified_history_indices']) ? $event['verified_history_indices'] : array();
      $historyNeedsSources = false;
      foreach ($history as $historyIndex => $historyValue) {
        if ($historyValue !== null && $historyValue !== '' && !in_array($historyIndex, $verifiedHistoryIndices, true)) {
          $historyNeedsSources = true;
          break;
        }
      }
      ?>
      <article class="timeline-event<?php echo $verified ? ' is-verified' : ' is-research'; ?>" data-timeline-event data-group="<?php echo $h($event['group']); ?>" data-levels="<?php echo $h(isset($event['levels']) && is_array($event['levels']) ? implode(',', $event['levels']) : 'all'); ?>" data-verified="<?php echo $verified ? '1' : '0'; ?>">
        <div class="timeline-marker" aria-hidden="true"><span></span></div>
        <div class="timeline-event-card">
          <div class="timeline-event-head">
            <div>
              <span class="timeline-category"><?php echo $h(isset($groups[$event['group']]) ? $groups[$event['group']] : $event['group']); ?></span>
              <h2><?php echo $h($event['title']); ?></h2>
            </div>
            <div class="timeline-event-date"><?php echo $h($event['latest']); ?></div>
          </div>

          <?php if (!$verified) { ?>
            <div class="timeline-source-state timeline-source-state--research">Δεν έχει ανακοινωθεί επίσημη ημερομηνία</div>
          <?php } ?>

          <?php if (!empty($event['note'])) { ?><p class="timeline-note"><?php echo $h($event['note']); ?></p><?php } ?>

          <details class="timeline-history">
            <summary>Ιστορικό 2019–2026<?php if ($historyNeedsSources) { ?><span class="timeline-history-summary-note">Πηγές υπό συμπλήρωση</span><?php } ?></summary>
            <div class="timeline-history-grid">
              <?php foreach ($years as $index => $year) {
                $value = isset($history[$index]) && $history[$index] !== null && $history[$index] !== '' ? $history[$index] : '—';
                ?>
                <div class="timeline-history-item<?php echo $value === '—' ? ' is-empty' : ''; ?>">
                  <span><?php echo $h($year); ?></span>
                  <strong><?php echo $h($value); ?></strong>
                </div>
              <?php } ?>
            </div>
            <?php if (!empty($historicalSources)) { ?>
              <details class="timeline-historical-sources">
                <summary>Πηγές προηγούμενων ετών</summary>
                <div class="timeline-historical-source-links">
                  <?php foreach ($historicalSources as $source) { ?>
                    <a href="<?php echo $h($source['url']); ?>" target="_blank" rel="noopener noreferrer"><span><?php echo $h($source['year']); ?></span> <?php echo $h($source['label']); ?> ↗</a>
                  <?php } ?>
                </div>
              </details>
            <?php } ?>
          </details>

          <?php if (!empty($sources)) { ?>
            <details class="timeline-event-sources">
              <summary>Επίσημες πηγές</summary>
              <div class="timeline-source-links">
                <?php foreach ($sources as $source) { ?>
                  <a href="<?php echo $h($source['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo $h($source['label']); ?> ↗</a>
                <?php } ?>
              </div>
            </details>
          <?php } ?>
        </div>
      </article>
    <?php } ?>
  </section>

  <div class="timeline-empty" id="timelineEmpty" hidden>Δεν υπάρχουν γεγονότα που να ταιριάζουν στα επιλεγμένα φίλτρα.</div>

  <?php sourceCardStart(); ?>
    <p>Οι ημερομηνίες βασίζονται σε επίσημες ανακοινώσεις, εγκυκλίους και αποφάσεις.</p>
    <?php sourceCardLinksStart(); ?>
      <?php sourceCardLink('https://www.minedu.gov.gr/kinitikotita/metatheseis-egkyklioi-proskliseis', 'ΥΠΑΙΘΑ — Μεταθέσεις / Εγκύκλιοι & Προσκλήσεις ↗'); ?>
      <?php sourceCardLink('https://www.minedu.gov.gr/kinitikotita/apospaseis-egkyklioi-proskliseis', 'ΥΠΑΙΘΑ — Αποσπάσεις / Εγκύκλιοι & Προσκλήσεις ↗'); ?>
      <?php sourceCardLink('https://www.minedu.gov.gr/monimoi-metatakseis-metatheseis-apospaseis', 'ΥΠΑΙΘΑ — Μόνιμοι / Κινητικότητα ↗'); ?>
    <?php sourceCardLinksEnd(); ?>
    <?php sourceCardDisclaimerStart(); ?>Το χρονοδιάγραμμα είναι ενημερωτικό. Για αιτήσεις, προθεσμίες και υπηρεσιακές ενέργειες ισχύει πάντοτε το επίσημο έγγραφο της αντίστοιχης διαδικασίας.<?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardEnd(); ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo $h(edu_asset_url('assets/common.js')); ?>"></script>
<script src="<?php echo $h(edu_asset_url('assets/service-timeline.js')); ?>"></script>
</body>
</html>
