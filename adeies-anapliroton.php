<?php
require_once __DIR__ . '/includes/config.php';
$data = require __DIR__ . '/includes/substitute-leaves-data.php';
require_once __DIR__ . '/includes/components/source-card.php';

$leaves = isset($data['leaves']) && is_array($data['leaves']) ? $data['leaves'] : array();
$sources = isset($data['sources']) && is_array($data['sources']) ? $data['sources'] : array();
$categories = array(
    'general' => 'Γενικές',
    'health' => 'Υγεία',
    'family' => 'Οικογένεια / τέκνα',
    'parenthood' => 'Μητρότητα / πατρότητα / ανατροφή',
    'studies' => 'Σπουδές / επιμόρφωση',
    'civic' => 'Ειδικές / πολιτειακές'
);
$h = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
function substituteLeaveBadge($value, $kind) {
    if ($kind === 'pay') {
        if ($value === 'yes') return array('Με αποδοχές', 'is-positive');
        if ($value === 'no') return array('Χωρίς αποδοχές', 'is-negative');
        return array('Μικτό καθεστώς αποδοχών', 'is-neutral');
    }
    if ($value === 'yes') return array('Πραγματική υπηρεσία', 'is-positive');
    if ($value === 'no') return array('Δεν προσμετράται', 'is-negative');
    return array('Περιορισμένη προσμέτρηση', 'is-neutral');
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php require __DIR__ . '/includes/head-pwa.php'; ?>
<meta name="description" content="Διαδραστικός οδηγός αδειών αναπληρωτών εκπαιδευτικών, ΕΕΠ και ΕΒΠ με διάρκεια, προϋποθέσεις, αποδοχές, πραγματική υπηρεσία και επίσημες πηγές.">
<title>Άδειες Αναπληρωτών Εκπαιδευτικών / ΕΕΠ–ΕΒΠ</title>
<link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/common.css')); ?>">
<link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/substitute-leaves.css')); ?>">
</head>
<body class="edu-ui edu-guide-standard edu-page-substitute-leaves">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
  <h1>Άδειες Αναπληρωτών Εκπαιδευτικών / ΕΕΠ–ΕΒΠ</h1>
  <p class="intro">Βρες γρήγορα τη διάρκεια, τις βασικές προϋποθέσεις, τις αποδοχές και αν ο χρόνος προσμετράται ως πραγματική υπηρεσία. Κάθε άδεια έχει <strong>πραγματικά links στις επίσημες πηγές</strong>.</p>
</section>

<div class="notice leave-source-note">
  <strong>Νομικό audit έως 2026:</strong> το εργαλείο ξεκινά από τον επίσημο συγκεντρωτικό πίνακα του ΥΠΑΙΘΑ (16/12/2021) και έχει συμπληρωθεί με νεότερες επίσημες διευκρινίσεις, ιδίως την εγκύκλιο ΥΠΕΣ ΔΙΔΑΔ/Φ.69/229/οικ.8177/08-05-2023 για προσωπικό ΙΔΟΧ και τις τρέχουσες οδηγίες της ΔΥΠΑ για τη γονική άδεια. Τα links οδηγούν σε πρωτογενείς επίσημες πηγές.
</div>

<section class="leave-finder" aria-labelledby="leaveFinderTitle">
  <div class="leave-finder__heading">
    <div>
      <span class="section-kicker">ΓΡΗΓΟΡΗ ΑΝΑΖΗΤΗΣΗ</span>
      <h2 id="leaveFinderTitle">Βρες την άδεια που σε αφορά</h2>
    </div>
    <span class="leave-count" id="leaveCount"><?php echo count($leaves); ?> άδειες</span>
  </div>
  <div class="leave-finder__grid">
    <div class="question">
      <label for="leaveJump">Πήγαινε απευθείας σε άδεια</label>
      <select id="leaveJump">
        <option value="">-- Επιλογή άδειας --</option>
        <?php foreach ($leaves as $leave): ?>
          <option value="<?php echo $h($leave['id']); ?>"><?php echo $h($leave['title']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="question">
      <label for="leaveSearch">Αναζήτηση</label>
      <input type="search" id="leaveSearch" autocomplete="off" placeholder="π.χ. παιδί, εξετάσεις, αιμοδοσία, αναπηρία…">
    </div>
  </div>
  <div class="leave-filter-row" role="group" aria-label="Φίλτρο κατηγορίας">
    <button type="button" class="leave-filter is-active" data-leave-filter="all">Όλες</button>
    <?php foreach ($categories as $slug => $label): ?>
      <button type="button" class="leave-filter" data-leave-filter="<?php echo $h($slug); ?>"><?php echo $h($label); ?></button>
    <?php endforeach; ?>
  </div>
</section>

<section class="leave-list" id="leaveList" aria-label="Κατάλογος αδειών">
<?php foreach ($leaves as $leave):
    $payBadge = substituteLeaveBadge($leave['pay'], 'pay');
    $serviceBadge = substituteLeaveBadge($leave['service'], 'service');
    $searchText = $leave['title'] . ' ' . $leave['duration'] . ' ' . $leave['legal'] . ' ' . implode(' ', $leave['conditions']);
?>
  <details class="leave-card" id="leave-<?php echo $h($leave['id']); ?>" data-leave-id="<?php echo $h($leave['id']); ?>" data-leave-category="<?php echo $h($leave['category']); ?>" data-leave-search="<?php echo $h($searchText); ?>">
    <summary>
      <span class="leave-card__main">
        <span class="leave-card__title"><?php echo $h($leave['title']); ?></span>
        <span class="leave-card__duration"><?php echo $h($leave['duration']); ?></span>
      </span>
      <span class="leave-card__badges">
        <span class="leave-badge <?php echo $h($payBadge[1]); ?>"><?php echo $h($payBadge[0]); ?></span>
        <span class="leave-badge <?php echo $h($serviceBadge[1]); ?>"><?php echo $h($serviceBadge[0]); ?></span>
        <?php if (!empty($leave['proportional'])): ?><span class="leave-badge is-neutral">Αναλογικά με τη σύμβαση</span><?php endif; ?>
      </span>
      <span class="leave-card__chevron" aria-hidden="true">›</span>
    </summary>
    <div class="leave-card__body">
      <div class="leave-info-grid">
        <section>
          <h3>Προϋποθέσεις / βασικές πληροφορίες</h3>
          <ul>
          <?php foreach ($leave['conditions'] as $item): ?><li><?php echo $h($item); ?></li><?php endforeach; ?>
          </ul>
        </section>
        <section>
          <h3>Δικαιολογητικά / ενέργειες</h3>
          <ul>
          <?php foreach ($leave['docs'] as $item): ?><li><?php echo $h($item); ?></li><?php endforeach; ?>
          </ul>
        </section>
      </div>
      <div class="leave-legal">
        <strong>Νομική παραπομπή:</strong> <?php echo $h($leave['legal']); ?>
      </div>
      <div class="leave-source-links" aria-label="Επίσημες πηγές για <?php echo $h($leave['title']); ?>">
        <strong>Επίσημες πηγές</strong>
        <div class="leave-source-links__items">
        <?php foreach ($leave['sources'] as $sourceKey): if (isset($sources[$sourceKey])): $source = $sources[$sourceKey]; ?>
          <a href="<?php echo $h($source['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo $h($source['label']); ?> ↗</a>
        <?php endif; endforeach; ?>
        </div>
      </div>
    </div>
  </details>
<?php endforeach; ?>
</section>

<div class="no-results leave-no-results" id="leaveNoResults" hidden>Δεν βρέθηκε άδεια που να ταιριάζει στα φίλτρα ή στην αναζήτησή σου.</div>

<?php sourceCardStart(array('title' => 'Πηγές / Νομική βάση')); ?>
<p>Ο κατάλογος οργανώνει σε φιλικότερη μορφή τον επίσημο συγκεντρωτικό πίνακα αδειών αναπληρωτών εκπαιδευτικών, ΕΕΠ και ΕΒΠ του ΥΠΑΙΘΑ και τον συμπληρώνει με νεότερες επίσημες διευκρινίσεις. Οι σύνδεσμοι μέσα σε κάθε άδεια οδηγούν σε ΦΕΚ, εγκυκλίους ΥΠΕΣ/ΥΠΑΙΘΑ ή τρέχουσες οδηγίες ΔΥΠΑ.</p>
<?php sourceCardLinksStart(); ?>
<?php if (isset($sources['minedu_guide_page'])) sourceCardLink($sources['minedu_guide_page']['url'], $sources['minedu_guide_page']['label'] . ' ↗'); ?>
<?php if (isset($sources['minedu_guide_pdf'])) sourceCardLink($sources['minedu_guide_pdf']['url'], $sources['minedu_guide_pdf']['label'] . ' ↗'); ?>
<?php if (isset($sources['ypes_2023_leaves'])) sourceCardLink($sources['ypes_2023_leaves']['url'], $sources['ypes_2023_leaves']['label'] . ' ↗'); ?>
<?php if (isset($sources['dypa_parental'])) sourceCardLink($sources['dypa_parental']['url'], $sources['dypa_parental']['label'] . ' ↗'); ?>
<?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<p class="small-note">Το εργαλείο είναι πληροφοριακό και δεν υποκαθιστά την απόφαση της αρμόδιας Διεύθυνσης Εκπαίδευσης, του ασφαλιστικού φορέα ή άλλη ειδική υπηρεσιακή κρίση.</p>
</div>

<script src="<?php echo $h(edu_asset_url('includes/substitute-leaves-ui.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo $h(edu_asset_url('assets/common.js')); ?>"></script>
</body>
</html>
