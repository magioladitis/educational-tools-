<?php
/**
 * Shared renderer for leave guides.
 * Expects:
 *   $leaveGuideData   structured array with leaves + sources
 *   $leaveGuideConfig public-facing page labels/settings
 */
require_once __DIR__ . '/components/source-card.php';

$leaves = isset($leaveGuideData['leaves']) && is_array($leaveGuideData['leaves']) ? $leaveGuideData['leaves'] : array();
$sources = isset($leaveGuideData['sources']) && is_array($leaveGuideData['sources']) ? $leaveGuideData['sources'] : array();
$categories = isset($leaveGuideConfig['categories']) && is_array($leaveGuideConfig['categories'])
    ? $leaveGuideConfig['categories']
    : array(
        'general' => 'Γενικές',
        'health' => 'Υγεία',
        'family' => 'Οικογένεια / τέκνα',
        'parenthood' => 'Μητρότητα / πατρότητα / ανατροφή',
        'studies' => 'Σπουδές / επιμόρφωση',
        'civic' => 'Ειδικές / πολιτειακές'
    );
$h = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };

if (!function_exists('leaveGuideBadge')) {
    function leaveGuideBadge($value, $kind) {
        if ($kind === 'pay') {
            if ($value === 'yes') return array('Με αποδοχές', 'is-positive');
            if ($value === 'no') return array('Χωρίς αποδοχές', 'is-negative');
            return array('Μικτό καθεστώς αποδοχών', 'is-neutral');
        }
        if ($value === 'yes') return array('Πραγματική υπηρεσία', 'is-positive');
        if ($value === 'no') return array('Δεν προσμετράται', 'is-negative');
        return array('Περιορισμένη προσμέτρηση', 'is-neutral');
    }
}

$title = isset($leaveGuideConfig['title']) ? $leaveGuideConfig['title'] : 'Άδειες Εκπαιδευτικών';
$metaDescription = isset($leaveGuideConfig['meta_description']) ? $leaveGuideConfig['meta_description'] : $title;
$intro = isset($leaveGuideConfig['intro']) ? $leaveGuideConfig['intro'] : '';
$bodyClass = isset($leaveGuideConfig['body_class']) ? $leaveGuideConfig['body_class'] : '';
$updateTitle = isset($leaveGuideConfig['update_title']) ? $leaveGuideConfig['update_title'] : 'Επικαιροποίηση και επίσημες πηγές';
$updateParagraphs = isset($leaveGuideConfig['update_paragraphs']) && is_array($leaveGuideConfig['update_paragraphs']) ? $leaveGuideConfig['update_paragraphs'] : array();
$switches = isset($leaveGuideConfig['switches']) && is_array($leaveGuideConfig['switches']) ? $leaveGuideConfig['switches'] : array();
$sourceCardText = isset($leaveGuideConfig['source_card_text']) ? $leaveGuideConfig['source_card_text'] : '';
$sourceCardKeys = isset($leaveGuideConfig['source_card_keys']) && is_array($leaveGuideConfig['source_card_keys']) ? $leaveGuideConfig['source_card_keys'] : array();
$disclaimer = isset($leaveGuideConfig['disclaimer']) ? $leaveGuideConfig['disclaimer'] : 'Το εργαλείο είναι πληροφοριακό και δεν υποκαθιστά την απόφαση της αρμόδιας υπηρεσίας ή άλλη ειδική υπηρεσιακή κρίση.';
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php require __DIR__ . '/head-pwa.php'; ?>
<meta name="description" content="<?php echo $h($metaDescription); ?>">
<title><?php echo $h($title); ?></title>
<link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/common.css')); ?>">
<link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/leave-guide.css')); ?>">
</head>
<body class="edu-ui edu-guide-standard edu-page-leave-guide <?php echo $h($bodyClass); ?>">
<?php require_once __DIR__ . '/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
  <h1><?php echo $h($title); ?></h1>
  <?php if ($intro !== ''): ?><p class="intro"><?php echo $intro; ?></p><?php endif; ?>
</section>

<?php if (!empty($switches)): ?>
<nav class="leave-audience-switcher" aria-label="Επιλογή κατηγορίας προσωπικού">
  <?php foreach ($switches as $switch): ?>
    <a href="<?php echo $h($switch['href']); ?>" class="leave-audience-switcher__item<?php echo !empty($switch['active']) ? ' is-active' : ''; ?>"<?php echo !empty($switch['active']) ? ' aria-current="page"' : ''; ?>><?php echo $h($switch['label']); ?></a>
  <?php endforeach; ?>
</nav>
<?php endif; ?>

<?php if (!empty($updateParagraphs)): ?>
<details class="leave-update-note">
  <summary><?php echo $h($updateTitle); ?></summary>
  <div class="leave-update-note__body">
    <?php foreach ($updateParagraphs as $paragraph): ?><p><?php echo $paragraph; ?></p><?php endforeach; ?>
  </div>
</details>
<?php endif; ?>

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
    $payBadge = leaveGuideBadge(isset($leave['pay']) ? $leave['pay'] : 'mixed', 'pay');
    $serviceBadge = leaveGuideBadge(isset($leave['service']) ? $leave['service'] : 'mixed', 'service');
    $conditions = isset($leave['conditions']) && is_array($leave['conditions']) ? $leave['conditions'] : array();
    $docs = isset($leave['docs']) && is_array($leave['docs']) ? $leave['docs'] : array();
    $leaveSources = isset($leave['sources']) && is_array($leave['sources']) ? $leave['sources'] : array();
    $searchText = $leave['title'] . ' ' . $leave['duration'] . ' ' . $leave['legal'] . ' ' . implode(' ', $conditions);
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
          <?php foreach ($conditions as $item): ?><li><?php echo $h($item); ?></li><?php endforeach; ?>
          </ul>
        </section>
        <section>
          <h3>Δικαιολογητικά / ενέργειες</h3>
          <ul>
          <?php foreach ($docs as $item): ?><li><?php echo $h($item); ?></li><?php endforeach; ?>
          </ul>
        </section>
      </div>
      <div class="leave-legal">
        <strong>Νομική παραπομπή:</strong> <?php echo $h($leave['legal']); ?>
      </div>
      <?php if (!empty($leave['related']) && is_array($leave['related'])): ?>
      <div class="leave-related">
        <strong>Σχετική άδεια:</strong>
        <?php foreach ($leave['related'] as $related): ?>
          <a href="#leave-<?php echo $h($related['id']); ?>" data-related-leave="<?php echo $h($related['id']); ?>"><?php echo $h($related['label']); ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="leave-source-links" aria-label="Επίσημες πηγές για <?php echo $h($leave['title']); ?>">
        <strong>Επίσημες πηγές</strong>
        <div class="leave-source-links__items">
        <?php foreach ($leaveSources as $sourceKey): if (isset($sources[$sourceKey])): $source = $sources[$sourceKey]; ?>
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
<?php if ($sourceCardText !== ''): ?><p><?php echo $sourceCardText; ?></p><?php endif; ?>
<?php sourceCardLinksStart(); ?>
<?php foreach ($sourceCardKeys as $sourceKey): if (isset($sources[$sourceKey])) sourceCardLink($sources[$sourceKey]['url'], $sources[$sourceKey]['label'] . ' ↗'); endforeach; ?>
<?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<p class="small-note"><?php echo $h($disclaimer); ?></p>
</div>

<script src="<?php echo $h(edu_asset_url('includes/leave-guide-ui.js')); ?>"></script>
<?php require_once __DIR__ . '/footer.php'; ?>
<script src="<?php echo $h(edu_asset_url('assets/common.js')); ?>"></script>
</body>
</html>
