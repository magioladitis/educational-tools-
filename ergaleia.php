<?php
require_once __DIR__ . '/includes/config.php';
$catalog = require __DIR__ . '/includes/tools-catalog.php';
require_once __DIR__ . '/includes/components/tool-card.php';

$groups = isset($catalog['groups']) && is_array($catalog['groups']) ? $catalog['groups'] : array();
$tools = isset($catalog['tools']) && is_array($catalog['tools']) ? $catalog['tools'] : array();
$toolsByGroup = array();
$groupCounts = array();

foreach ($groups as $groupSlug => $groupConfig) {
    $toolsByGroup[$groupSlug] = array();
    $groupCounts[$groupSlug] = 0;
}

foreach ($tools as $tool) {
    $groupSlug = isset($tool['group']) ? (string) $tool['group'] : '';
    if ($groupSlug !== '' && isset($toolsByGroup[$groupSlug])) {
        $toolsByGroup[$groupSlug][] = $tool;
        $groupCounts[$groupSlug]++;
    }
}

$categoryToneClasses = array(
    'green' => 'category-card--green',
    'orange' => 'category-card--orange',
    'purple' => 'category-card--purple',
    'teal' => 'category-card--teal'
);

$initialGroup = 'all';
if (isset($_GET['group'])) {
    $requestedGroup = (string) $_GET['group'];
    if (isset($groups[$requestedGroup])) {
        $initialGroup = $requestedGroup;
    }
}

$flags = ENT_QUOTES;
if (defined('ENT_SUBSTITUTE')) {
    $flags = $flags | ENT_SUBSTITUTE;
}
$h = function ($value) use ($flags) {
    return htmlspecialchars((string) $value, $flags, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="Εργαλειοθήκη Εκπαιδευτικού: δωρεάν εργαλεία για ΑΣΕΠ, αναπληρωτές, ειδική αγωγή, αποσπάσεις, μεταθέσεις, σχολική μονάδα, ΣΔΕ και ΣΑΕΚ." name="description">
  <title>Εργαλειοθήκη Εκπαιδευτικού</title>
  <link href="<?php echo $h(edu_asset_url('assets/common.css')); ?>" rel="stylesheet">
</head>
<body class="edu-ui edu-tools-directory">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="page-shell">
  <section class="hero">
    <span class="hero-kicker">ΔΩΡΕΑΝ ΕΡΓΑΛΕΙΑ ΓΙΑ ΕΚΠΑΙΔΕΥΤΙΚΟΥΣ</span>
    <h1>Εργαλειοθήκη Εκπαιδευτικού</h1>
    <p>Υπολογιστές, έλεγχοι και οδηγοί οργανωμένοι πλέον σε θεματικές ενότητες, ώστε να βρίσκεις γρηγορότερα το εργαλείο που χρειάζεσαι.</p>
    <div class="hero-meta" aria-label="Σύνοψη Εργαλειοθήκης">
      <span><?php echo count($tools); ?> διαθέσιμα εργαλεία</span>
      <span><?php echo count($groups); ?> βασικές κατηγορίες</span>
      <span>Ξεχωριστή σελίδα προθεσμιών</span>
    </div>
    <div class="hero-actions">
      <a class="hero-action hero-action--primary" href="#tool-categories">Δες κατηγορίες</a>
      <a class="hero-action" href="#tools-directory">Όλα τα εργαλεία</a>
      <a class="hero-action" href="prothesmies.php">Προθεσμίες →</a>
    </div>
  </section>

  <section class="directory-section" id="tool-categories" aria-labelledby="toolCategoriesTitle">
    <div class="directory-section__heading">
      <div>
        <span class="section-kicker">ΞΕΚΙΝΑ ΑΠΟ ΕΔΩ</span>
        <h2 id="toolCategoriesTitle">Κατηγορίες εργαλείων</h2>
      </div>
      <p>Διάλεξε οικογένεια εργαλείων ή χρησιμοποίησε την αναζήτηση πιο κάτω.</p>
    </div>

    <div class="category-grid">
      <?php foreach ($groups as $groupSlug => $groupConfig) {
          $title = isset($groupConfig['title']) ? $groupConfig['title'] : $groupSlug;
          $description = isset($groupConfig['description']) ? $groupConfig['description'] : '';
          $short = isset($groupConfig['short']) ? $groupConfig['short'] : '';
          $tone = isset($groupConfig['tone']) ? $groupConfig['tone'] : 'blue';
          $count = isset($groupCounts[$groupSlug]) ? (int) $groupCounts[$groupSlug] : 0;
          ?>
      <a
        class="category-card<?php echo isset($categoryToneClasses[$tone]) ? ' ' . $h($categoryToneClasses[$tone]) : ''; ?>"
        data-directory-filter="<?php echo $h($groupSlug); ?>"
        href="ergaleia.php?group=<?php echo rawurlencode($groupSlug); ?>#tools-directory"
      >
        <span class="category-card__icon" aria-hidden="true"><?php echo $h($short); ?></span>
        <span class="category-card__body">
          <strong><?php echo $h($title); ?></strong>
          <span><?php echo $h($description); ?></span>
        </span>
        <span class="category-card__count"><?php echo $count; ?> εργαλεία</span>
      </a>
      <?php } ?>
    </div>
  </section>

  <section aria-label="Αναζήτηση και φίλτρα εργαλείων" class="toolbar" id="tools-directory" data-initial-filter="<?php echo $h($initialGroup); ?>">
    <div class="toolbar-heading">
      <div>
        <span class="section-kicker">ΚΑΤΑΛΟΓΟΣ</span>
        <h2>Βρες το εργαλείο που χρειάζεσαι</h2>
      </div>
      <a class="deadline-shortcut" href="prothesmies.php">📅 Προθεσμίες</a>
    </div>
    <div class="search-wrap">
      <input aria-label="Αναζήτηση εργαλείου" autocomplete="off" id="toolSearch" placeholder="Αναζήτηση π.χ. μόρια, παράβολο, ωράριο, απόσπαση..." type="search">
    </div>
    <div aria-label="Βασικές κατηγορίες εργαλείων" class="filters" role="group">
      <button aria-pressed="<?php echo $initialGroup === 'all' ? 'true' : 'false'; ?>" class="filter-btn<?php echo $initialGroup === 'all' ? ' active' : ''; ?>" data-filter="all" type="button">Όλα</button>
      <?php foreach ($groups as $groupSlug => $groupConfig) { ?>
        <button aria-pressed="<?php echo $initialGroup === $groupSlug ? 'true' : 'false'; ?>" class="filter-btn<?php echo $initialGroup === $groupSlug ? ' active' : ''; ?>" data-filter="<?php echo $h($groupSlug); ?>" type="button"><?php echo $h(isset($groupConfig['title']) ? $groupConfig['title'] : $groupSlug); ?></button>
      <?php } ?>
    </div>
    <div aria-live="polite" class="results-line" id="resultsLine" role="status"></div>
  </section>

  <div class="tool-groups" id="toolGroups">
    <?php foreach ($groups as $groupSlug => $groupConfig) {
        $title = isset($groupConfig['title']) ? $groupConfig['title'] : $groupSlug;
        $description = isset($groupConfig['description']) ? $groupConfig['description'] : '';
        $groupTools = isset($toolsByGroup[$groupSlug]) ? $toolsByGroup[$groupSlug] : array();
        ?>
    <section class="tool-group" data-tool-group="<?php echo $h($groupSlug); ?>" id="group-<?php echo $h($groupSlug); ?>" aria-labelledby="groupTitle-<?php echo $h($groupSlug); ?>">
      <div class="tool-group__heading">
        <div>
          <h2 id="groupTitle-<?php echo $h($groupSlug); ?>"><?php echo $h($title); ?></h2>
          <p><?php echo $h($description); ?></p>
        </div>
        <span class="tool-group__count"><?php echo count($groupTools); ?></span>
      </div>
      <div class="tools-grid">
        <?php foreach ($groupTools as $tool) {
            renderDirectoryToolCard($tool);
        } ?>
      </div>
    </section>
    <?php } ?>
  </div>

  <div aria-hidden="true" class="no-results" id="noResults">
    Δεν βρέθηκε εργαλείο που να ταιριάζει στην αναζήτησή σου.
  </div>

  <section class="info-grid">
    <div class="notice">
      <strong>Σημαντική σημείωση:</strong>
      Τα εργαλεία παρέχουν ενδεικτική πληροφόρηση και δεν αντικαθιστούν τις επίσημες προκηρύξεις, εγκυκλίους και οδηγίες των αρμόδιων φορέων. Πριν από την οριστική υποβολή αίτησης, ελέγχετε πάντοτε τα επίσημα έγγραφα και τα στοιχεία που εμφανίζονται στο ΑΣΕΠ ή/και στο ΟΠΣΥΔ.
    </div>
    <div class="side-box">
      <strong>Προθεσμίες σε ξεχωριστή σελίδα</strong>
      Οι ενεργές και προσεχείς ημερομηνίες δεν εμφανίζονται πλέον ανάμεσα στα εργαλεία.
      <a href="prothesmies.php">Άνοιγμα προθεσμιών →</a>
    </div>
  </section>
</main>

<script src="<?php echo $h(edu_asset_url('assets/tools-directory.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo $h(edu_asset_url('assets/common.js')); ?>"></script>
</body>
</html>
