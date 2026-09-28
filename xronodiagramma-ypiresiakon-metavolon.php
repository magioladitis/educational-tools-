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
  <section class="timeline-hero">
    <span class="timeline-kicker">ΥΠΗΡΕΣΙΑΚΕΣ ΜΕΤΑΒΟΛΕΣ</span>
    <h1>Χρονοδιάγραμμα Εκπαιδευτικών</h1>
    <p>Ιστορική εικόνα βασικών διαδικασιών — μεταθέσεις, αποσπάσεις, μετατάξεις και νεοδιόριστοι — με σαφή διάκριση ανάμεσα σε ελεγμένες πηγές και στοιχεία που ακόμη τεκμηριώνονται.</p>
    <div class="timeline-hero-meta" aria-label="Σύνοψη χρονοδιαγράμματος">
      <span>Ιστορικό 2019–2026</span>
      <span><?php echo (int) $verifiedCount; ?> γεγονότα με ελεγμένη τελευταία ημερομηνία</span>
      <span>Ενημέρωση: <?php echo $h($data['updated_at']); ?></span>
    </div>
  </section>

  <section class="timeline-now edu-card" aria-labelledby="timelineNowTitle">
    <div>
      <span class="timeline-eyebrow">ΤΡΕΧΩΝ ΚΥΚΛΟΣ <?php echo $h($data['current_cycle']); ?></span>
      <h2 id="timelineNowTitle">Τι γνωρίζουμε τώρα</h2>
      <p>Στην έκδοση δεδομένων της <?php echo $h($data['updated_at']); ?> δεν έχει ακόμη καταχωριστεί επίσημη ημερομηνία για την εγκύκλιο μεταθέσεων του κύκλου 2026–2027. Το εργαλείο δεν μετατρέπει την ιστορική επανάληψη σε «πρόβλεψη»· νέα ημερομηνία εμφανίζεται ως επίσημη μόνο όταν συνδεθεί με πηγή.</p>
    </div>
    <a class="timeline-deadlines-link" href="prothesmies.php">Δες ενεργές προθεσμίες →</a>
  </section>

  <section class="timeline-controls edu-card" aria-label="Φίλτρα χρονοδιαγράμματος">
    <div class="timeline-filter-group" role="group" aria-label="Κατηγορία διαδικασίας">
      <button type="button" class="timeline-chip is-active" data-timeline-filter="all">Όλα</button>
      <?php foreach ($groups as $key => $label) { ?>
        <button type="button" class="timeline-chip" data-timeline-filter="<?php echo $h($key); ?>"><?php echo $h($label); ?></button>
      <?php } ?>
    </div>
    <label class="timeline-verified-toggle">
      <input id="timelineVerifiedOnly" type="checkbox" checked>
      <span>Μόνο με ελεγμένη τελευταία ημερομηνία</span>
    </label>
  </section>

  <div class="timeline-status-line" id="timelineStatusLine" role="status" aria-live="polite"></div>

  <section class="timeline-list" id="timelineList" aria-label="Γεγονότα χρονοδιαγράμματος">
    <?php foreach ($events as $event) {
      $verified = !empty($event['latest_verified']);
      $history = isset($event['history']) && is_array($event['history']) ? $event['history'] : array();
      $sources = isset($event['sources']) && is_array($event['sources']) ? $event['sources'] : array();
      $verificationLabel = isset($event['verification_label']) ? $event['verification_label'] : '';
      ?>
      <article class="timeline-event<?php echo $verified ? ' is-verified' : ' is-research'; ?>" data-timeline-event data-group="<?php echo $h($event['group']); ?>" data-verified="<?php echo $verified ? '1' : '0'; ?>">
        <div class="timeline-marker" aria-hidden="true"><span></span></div>
        <div class="timeline-event-card">
          <div class="timeline-event-head">
            <div>
              <span class="timeline-category"><?php echo $h(isset($groups[$event['group']]) ? $groups[$event['group']] : $event['group']); ?></span>
              <h2><?php echo $h($event['title']); ?></h2>
            </div>
            <div class="timeline-event-date"><?php echo $h($event['latest']); ?></div>
          </div>

          <div class="timeline-source-state <?php echo $verified ? 'timeline-source-state--verified' : 'timeline-source-state--research'; ?>">
            <?php if ($verified) { ?>
              <?php echo $h($verificationLabel !== '' ? $verificationLabel : '✓ Τελευταία ημερομηνία ελεγμένη σε επίσημη πηγή'); ?>
            <?php } else { ?>
              ◌ Ιστορικό στοιχείο — τεκμηρίωση πηγής σε εξέλιξη
            <?php } ?>
          </div>

          <?php if (!empty($event['note'])) { ?><p class="timeline-note"><?php echo $h($event['note']); ?></p><?php } ?>

          <details class="timeline-history">
            <summary>Ιστορικό 2019–2026</summary>
            <div class="timeline-history-grid">
              <?php foreach ($years as $index => $year) {
                $value = isset($history[$index]) && $history[$index] !== null && $history[$index] !== '' ? $history[$index] : '—';
                ?>
                <div class="timeline-history-item">
                  <span><?php echo $h($year); ?></span>
                  <strong><?php echo $h($value); ?></strong>
                </div>
              <?php } ?>
            </div>
            <p class="timeline-history-disclaimer">Οι παλαιότερες ημερομηνίες προέρχονται από το ιστορικό αρχείο εργασίας και τεκμηριώνονται σταδιακά ανά έτος. Η πράσινη ένδειξη αφορά την τελευταία τιμή του κύκλου 2025–2026.</p>
          </details>

          <?php if (!empty($sources)) { ?>
            <div class="timeline-event-sources" aria-label="Πηγές και τεκμηρίωση">
              <?php foreach ($sources as $source) { ?>
                <a href="<?php echo $h($source['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo $h($source['label']); ?> ↗</a>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      </article>
    <?php } ?>
  </section>

  <div class="timeline-empty" id="timelineEmpty" hidden>Δεν υπάρχουν γεγονότα που να ταιριάζουν στα επιλεγμένα φίλτρα.</div>

  <?php sourceCardStart(); ?>
    <p><strong>Μεθοδολογία:</strong> το ιστορικό ξεκίνησε από την καρτέλα «ΧΡΟΝΟΔΙΑΓΡΑΜΜΑ» του αρχείου εργασίας. Για δημόσια χρήση, οι τελευταίες ημερομηνίες διασταυρώνονται μία-μία με επίσημες ανακοινώσεις ή έγγραφα του ΥΠΑΙΘΑ και των αρμόδιων εκπαιδευτικών αρχών. Όταν το πρωτογενές URL δεν είναι διαθέσιμο αλλά έχει εντοπιστεί ψηφιακό αντίγραφο του ίδιου επίσημου εγγράφου, αυτό επισημαίνεται ρητά στην κάρτα. Όπου η τεκμηρίωση δεν είναι ακόμη πλήρης, το γεγονός παραμένει ρητά σε κατάσταση έρευνας. Δεν συμπληρώνουμε κενά με εκτιμήσεις.</p>
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
