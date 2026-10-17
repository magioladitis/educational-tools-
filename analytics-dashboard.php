<?php
/** Restricted analytics dashboard: reuse existing authenticated vacancy admin. */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/analytics-store.php';
vacanciesSessionStart();
if (!vacanciesIsAdmin() || vacanciesActorNeedsPasswordChange()) {
    header('Location: kena-sxoleion-login.php'); exit;
}
header('Cache-Control: private, no-store, max-age=0');
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
if (!in_array($days, array(7, 30, 90), true)) $days = 30;
$stats = eduAnalyticsSummary($days);
$tools = $stats['tools'];
$daily = $stats['daily'];
$totals = array('views'=>0,'engagement_seconds'=>0,'active'=>0);
foreach ($tools as $entry) {
    $totals['views'] += $entry['views'];
    $totals['engagement_seconds'] += $entry['engagement_seconds'];
    if ($entry['views'] > 0) ++$totals['active'];
}
function eduAnalyticsH($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function eduAnalyticsSeconds($seconds) {
    if ($seconds <= 0) return '0 δ.';
    if ($seconds < 60) return (int)$seconds . ' δ.';
    return number_format($seconds/60, 1, ',', '.') . ' λεπτά';
}
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="tool-analytics-'.$days.'-days.csv"');
    header('Cache-Control: no-store');
    $stream = fopen('php://output','w');
    fwrite($stream, "\xEF\xBB\xBF");
    fputcsv($stream, array('Εργαλείο','Αρχείο','Κατηγορία','Προβολές','Ενεργός χρόνος (δευτ.)','Μέσος ενεργός χρόνος/προβολή (δευτ.)'));
    foreach ($tools as $id=>$entry) {
        fputcsv($stream,array($entry['title'],$id,$entry['group'],$entry['views'],$entry['engagement_seconds'], $entry['views'] ? round($entry['engagement_seconds']/$entry['views'],1):0));
    }
    fclose($stream); exit;
}
$maxDay = 1;
foreach ($daily as $entry) $maxDay = max($maxDay, $entry['views']);
$maxTool = 1;
foreach ($tools as $entry) $maxTool = max($maxTool, $entry['views']);
$storageIsReady = eduAnalyticsEnsureDir();
?>
<!doctype html><html lang="el"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Στατιστικά εργαλείων — Εργαλειοθήκη</title>
<link rel="stylesheet" href="<?= eduAnalyticsH(edu_asset_url('assets/common.css')) ?>">
<link rel="stylesheet" href="<?= eduAnalyticsH(edu_asset_url('assets/analytics-dashboard.css')) ?>">
<meta name="robots" content="noindex,nofollow"></head>
<body class="edu-ui edu-calc-standard edu-analytics-dashboard">
<?php require __DIR__ . '/includes/header.php'; ?>
<main class="page-shell edu-analytics-shell" id="main-content">
  <header class="edu-analytics-heading"><div><span class="section-kicker">ΠΡΟΣΒΑΣΗ ΔΙΑΧΕΙΡΙΣΤΗ</span><h1>Στατιστικά χρήσης εργαλείων</h1><p>Συγκεντρωτικές προβολές και εκτιμώμενος ενεργός χρόνος — χωρίς αναγνωριστικά επισκεπτών.</p></div><a href="kena-sxoleion-admin.php">← Διαχείριση</a></header>
  <?php if (!$storageIsReady) { ?><p class="edu-analytics-warning" role="alert">Δεν είναι δυνατή η εγγραφή στατιστικών στο hosting. Ελέγξτε δικαιώματα φακέλου και την οδηγία εγκατάστασης.</p><?php } ?>
  <nav class="edu-analytics-filters" aria-label="Διάστημα προβολής">
    <?php foreach (array(7,30,90) as $period) { ?><a href="?days=<?= $period ?>" class="<?= $days===$period?'selected':'' ?>" <?= $days===$period?'aria-current="true"':'' ?>><?= $period ?> ημέρες</a><?php } ?>
    <a class="edu-analytics-export" href="?days=<?= $days ?>&amp;export=csv">Εξαγωγή CSV ↓</a>
  </nav>
  <div class="edu-analytics-metrics">
    <section><span>Προβολές εργαλείων</span><strong><?= number_format($totals['views'],0,',','.') ?></strong></section>
    <section><span>Ενεργός χρόνος</span><strong><?= eduAnalyticsSeconds($totals['engagement_seconds']) ?></strong></section>
    <section><span>Εργαλεία με επισκέψεις</span><strong><?= $totals['active'] ?> / <?= count($tools) ?></strong></section>
    <section><span>Μέσος χρόνος ανά προβολή</span><strong><?= eduAnalyticsSeconds($totals['views'] ? round($totals['engagement_seconds']/$totals['views']):0) ?></strong></section>
  </div>
  <section class="edu-analytics-panel" aria-labelledby="dailyTitle"><h2 id="dailyTitle">Προβολές ανά ημέρα</h2><div class="edu-analytics-chart" role="img" aria-label="Προβολές εργαλείων ανά ημέρα">
  <?php foreach ($daily as $point) { ?><div class="edu-analytics-chart__item" title="<?= eduAnalyticsH($point['date']) ?>: <?= (int)$point['views'] ?> προβολές"><div class="edu-analytics-chart__bar" style="height:<?= $point['views'] ? max(3,round($point['views']*100/$maxDay)):0 ?>%"></div><span><?= eduAnalyticsH(substr($point['date'],8,2)) ?></span></div><?php } ?></div><p class="small-note">Οι μπάρες αφορούν φορτώσεις εργαλείων, όχι μοναδικούς επισκέπτες.</p></section>
  <section class="edu-analytics-panel" aria-labelledby="toolsTitle"><h2 id="toolsTitle">Επίδοση ανά εργαλείο</h2><div class="edu-analytics-table-wrap"><table class="edu-analytics-table"><thead><tr><th scope="col">Εργαλείο</th><th scope="col">Προβολές</th><th scope="col">Ενεργός χρόνος</th><th scope="col">Μ.Ο. ανά προβολή</th></tr></thead><tbody>
  <?php foreach ($tools as $id=>$entry) { ?><tr><td><span class="edu-analytics-tool-title"><?= eduAnalyticsH($entry['title']) ?></span><small><?= eduAnalyticsH($id) ?></small><div class="edu-analytics-bar"><span style="width:<?= round($entry['views']*100/$maxTool,1) ?>%"></span></div></td><td><?= number_format($entry['views'],0,',','.') ?></td><td><?= eduAnalyticsSeconds($entry['engagement_seconds']) ?></td><td><?= eduAnalyticsSeconds($entry['views']?round($entry['engagement_seconds']/$entry['views']):0) ?></td></tr><?php } ?>
  </tbody></table></div></section>
  <p class="edu-analytics-info"><strong>Πώς να διαβάζεις τα στοιχεία:</strong> Τα «views» είναι φορτώσεις σελίδας (όχι μοναδικά άτομα). Ο ενεργός χρόνος μετριέται όταν η καρτέλα είναι ορατή, στο προσκήνιο και ο χρήστης παραμένει ενεργός· δεν ταυτίζεται με τον συνολικό χρόνο ανοικτής καρτέλας. Οι χρήστες που εξαιρούνται ή χρησιμοποιούν προστασίες ιδιωτικότητας δεν μετρώνται. Το μικρό δείγμα και η εποχικότητα μπορούν να επηρεάσουν τη σύγκριση.</p>
</main><?php require __DIR__ . '/includes/footer.php'; ?></body></html>
