<?php
require_once __DIR__ . '/includes/config.php';
$permanentGuide = require __DIR__ . '/includes/permanent-leaves-data.php';
$substituteGuide = require __DIR__ . '/includes/substitute-leaves-data.php';
$comparisonMap = require __DIR__ . '/includes/leave-comparison-map.php';
$h = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };

function leaveComparisonIndex($items) {
    $index = array();
    foreach ($items as $item) {
        if (isset($item['id'])) $index[$item['id']] = $item;
    }
    return $index;
}
function leaveComparisonClientMeta($leave) {
    return array(
        'duration_text' => isset($leave['duration']) ? (string) $leave['duration'] : '',
        'pay' => isset($leave['pay']) ? (string) $leave['pay'] : 'mixed',
        'service' => isset($leave['service']) ? (string) $leave['service'] : 'mixed',
        'proportional' => !empty($leave['proportional']),
        'comparison' => isset($leave['comparison']) && is_array($leave['comparison']) ? $leave['comparison'] : array(),
    );
}

function leaveComparisonPayLabel($value) {
    if ($value === 'yes') return 'Με αποδοχές';
    if ($value === 'no') return 'Χωρίς αποδοχές';
    return 'Μικτό καθεστώς αποδοχών';
}
function leaveComparisonServiceLabel($value) {
    if ($value === 'yes') return 'Πραγματική υπηρεσία';
    if ($value === 'no') return 'Δεν προσμετράται';
    return 'Περιορισμένη προσμέτρηση';
}
function leaveComparisonSourceLinks($leave, $sources, $h) {
    if (empty($leave['sources']) || !is_array($leave['sources'])) return;
    echo '<div class="leave-compare__sources"><strong>Επίσημες πηγές</strong><div class="leave-source-links__items">';
    foreach ($leave['sources'] as $key) {
        if (!isset($sources[$key])) continue;
        $source = $sources[$key];
        echo '<a href="' . $h($source['url']) . '" target="_blank" rel="noopener noreferrer">' . $h($source['label']) . ' ↗</a>';
    }
    echo '</div></div>';
}
function leaveComparisonSide($leave, $sources, $audience, $h) {
    $conditions = isset($leave['conditions']) && is_array($leave['conditions']) ? $leave['conditions'] : array();
    $docs = isset($leave['docs']) && is_array($leave['docs']) ? $leave['docs'] : array();
    $pay = isset($leave['pay']) ? $leave['pay'] : 'mixed';
    $service = isset($leave['service']) ? $leave['service'] : 'mixed';
    $proportional = !empty($leave['proportional']);
    ?>
    <article class="leave-compare__side leave-compare__side--<?php echo $h($audience); ?>">
      <div class="leave-compare__side-head">
        <span class="leave-compare__eyebrow"><?php echo $audience === 'permanent' ? 'ΜΟΝΙΜΟΣ / ΜΟΝΙΜΗ' : 'ΑΝΑΠΛΗΡΩΤΗΣ / ΙΔΟΧ'; ?></span>
        <h3><?php echo $h($leave['title']); ?></h3>
        <p class="leave-compare__duration"><?php echo $h($leave['duration']); ?></p>
        <div class="leave-card__badges leave-compare__badges">
          <span class="leave-badge <?php echo $pay === 'yes' ? 'is-positive' : ($pay === 'no' ? 'is-negative' : 'is-neutral'); ?>"><?php echo $h(leaveComparisonPayLabel($pay)); ?></span>
          <span class="leave-badge <?php echo $service === 'yes' ? 'is-positive' : ($service === 'no' ? 'is-negative' : 'is-neutral'); ?>"><?php echo $h(leaveComparisonServiceLabel($service)); ?></span>
          <?php if ($proportional): ?><span class="leave-badge is-neutral">Αναλογικά με τη σύμβαση</span><?php endif; ?>
        </div>
        <div class="leave-compare__semantic-note" data-leave-compare-scope="<?php echo $h($audience); ?>" hidden></div>
      </div>

      <section class="leave-compare__block">
        <h4>Προϋποθέσεις / βασικές πληροφορίες</h4>
        <ul><?php foreach ($conditions as $item): ?><li><?php echo $h($item); ?></li><?php endforeach; ?></ul>
      </section>

      <section class="leave-compare__block">
        <h4>Δικαιολογητικά / ενέργειες</h4>
        <ul><?php foreach ($docs as $item): ?><li><?php echo $h($item); ?></li><?php endforeach; ?></ul>
      </section>

      <div class="leave-legal"><strong>Νομική παραπομπή:</strong> <?php echo $h($leave['legal']); ?></div>
      <?php leaveComparisonSourceLinks($leave, $sources, $h); ?>
    </article>
    <?php
}

$permanentIndex = leaveComparisonIndex($permanentGuide['leaves'] ?? array());
$substituteIndex = leaveComparisonIndex($substituteGuide['leaves'] ?? array());
$validComparisons = array();
foreach ($comparisonMap as $entry) {
    if (isset($permanentIndex[$entry['permanent']], $substituteIndex[$entry['substitute']])) {
        $validComparisons[] = $entry;
    }
}
$requested = isset($_GET['leave']) ? (string) $_GET['leave'] : '';
$initialId = !empty($validComparisons) ? $validComparisons[0]['id'] : '';
foreach ($validComparisons as $entry) {
    if ($entry['id'] === $requested) { $initialId = $requested; break; }
}
$comparisonPayload = array();
foreach ($validComparisons as $entry) {
    $permanent = $permanentIndex[$entry['permanent']];
    $substitute = $substituteIndex[$entry['substitute']];
    $comparisonPayload[$entry['id']] = array(
        'id' => $entry['id'],
        'title' => $entry['title'],
        'permanent' => leaveComparisonClientMeta($permanent),
        'substitute' => leaveComparisonClientMeta($substitute),
    );
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php require __DIR__ . '/includes/head-pwa.php'; ?>
<meta name="description" content="Σύγκρινε την ίδια άδεια για μόνιμους εκπαιδευτικούς και αναπληρωτές / ΙΔΟΧ, με διάρκεια, προϋποθέσεις, αποδοχές και επίσημες πηγές.">
<title>Σύγκριση Αδειών — Μόνιμοι και Αναπληρωτές</title>
<link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/common.css')); ?>">
<link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/leave-guide.css')); ?>">
</head>
<body class="edu-ui edu-guide-standard edu-page-leave-guide edu-page-leave-comparison">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
  <h1>Σύγκριση Αδειών</h1>
  <p class="intro">Επίλεξε μία άδεια και δες <strong>δίπλα-δίπλα</strong> τι ισχύει για μόνιμους εκπαιδευτικούς και για αναπληρωτές / ΙΔΟΧ. Η σύγκριση χρησιμοποιεί τα ίδια επικαιροποιημένα δεδομένα και τις ίδιες επίσημες πηγές με τους δύο οδηγούς.</p>
</section>

<nav class="leave-audience-switcher leave-audience-switcher--three" aria-label="Επιλογή οδηγού αδειών">
  <a href="adeies-monimon.php" class="leave-audience-switcher__item">Μόνιμοι</a>
  <a href="adeies-anapliroton.php" class="leave-audience-switcher__item">Αναπληρωτές / ΙΔΟΧ</a>
  <a href="adeies-sygkrisi.php" class="leave-audience-switcher__item is-active" aria-current="page">Σύγκριση</a>
</nav>

<section class="leave-compare-picker" aria-labelledby="leaveCompareTitle">
  <span class="section-kicker">ΣΥΓΚΡΙΣΗ ΙΔΙΟΥ ΔΙΚΑΙΩΜΑΤΟΣ</span>
  <h2 id="leaveCompareTitle">Ποια άδεια θέλεις να συγκρίνεις;</h2>
  <div class="leave-compare-picker__grid">
    <div class="question leave-compare-picker__field">
      <label for="leaveCompareSelect">Επιλογή άδειας</label>
      <select id="leaveCompareSelect">
        <?php foreach ($validComparisons as $entry): ?>
          <option value="<?php echo $h($entry['id']); ?>"<?php echo $entry['id'] === $initialId ? ' selected' : ''; ?>><?php echo $h($entry['title']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="question leave-compare-picker__field">
      <label for="leaveCompareView">Προβολή</label>
      <select id="leaveCompareView">
        <option value="all">Όλες οι συγκρίσεις</option>
        <option value="differences">Μόνο με ουσιαστικές διαφορές</option>
        <option value="same-base">Ίδια βασική διάρκεια</option>
      </select>
    </div>
  </div>
  <p class="leave-compare-picker__note">Συγκρίνουμε μόνο δικαιώματα που είναι ουσιαστικά αντίστοιχα. Όπου το νομικό καθεστώς είναι διαφορετικής φύσης, δεν δημιουργείται τεχνητή αντιστοίχιση.</p>
</section>

<section class="leave-compare-list" id="leaveCompareList" aria-live="polite">
<?php foreach ($validComparisons as $entry):
    $permanent = $permanentIndex[$entry['permanent']];
    $substitute = $substituteIndex[$entry['substitute']];
?>
  <article class="leave-compare-panel" data-leave-comparison="<?php echo $h($entry['id']); ?>"<?php echo $entry['id'] !== $initialId ? ' hidden' : ''; ?>>
    <header class="leave-compare-panel__header">
      <div>
        <span class="section-kicker">ΜΟΝΙΜΟΣ ↔ ΑΝΑΠΛΗΡΩΤΗΣ / ΙΔΟΧ</span>
        <h2><?php echo $h($entry['title']); ?></h2>
        <p class="leave-compare-overall" data-leave-compare-overall></p>
      </div>
      <div class="leave-compare-summary" data-leave-compare-summary aria-label="Γρήγορη σύνοψη σύγκρισης"></div>
    </header>
    <div class="leave-compare-grid">
      <?php leaveComparisonSide($permanent, $permanentGuide['sources'], 'permanent', $h); ?>
      <?php leaveComparisonSide($substitute, $substituteGuide['sources'], 'substitute', $h); ?>
    </div>
  </article>
<?php endforeach; ?>
</section>

<details class="leave-update-note">
  <summary>Πώς διαβάζεται η σύγκριση</summary>
  <div class="leave-update-note__body">
    <p>Η ομοιότητα στον τίτλο μιας άδειας δεν σημαίνει απαραίτητα ίδιο δικαίωμα. Η γρήγορη σύνοψη ξεχωρίζει τη βασική διάρκεια από τις ειδικές περιπτώσεις, τη συχνότητα, το πεδίο δικαιώματος και την αναλογικότητα εφαρμογής.</p>
    <p>Οι πληροφορίες προέρχονται απευθείας από τους δύο οδηγούς της Εργαλειοθήκης, ώστε μια μελλοντική ενημέρωση στα δεδομένα να περνά αυτόματα και στη σύγκριση.</p>
  </div>
</details>

<p class="small-note">Το εργαλείο είναι πληροφοριακό και δεν υποκαθιστά την κρίση ή απόφαση της αρμόδιας υπηρεσίας σε ειδικές περιπτώσεις.</p>
</div>
<script type="application/json" id="leaveComparisonData"><?php echo json_encode($comparisonPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<script src="<?php echo $h(edu_asset_url('includes/leave-comparison-ui.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo $h(edu_asset_url('assets/common.js')); ?>"></script>
</body>
</html>
