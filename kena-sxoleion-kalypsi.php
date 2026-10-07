<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/vacancies-model.php';
require_once __DIR__ . '/includes/vacancies-coverage-engine.php';
require_once __DIR__ . '/includes/vacancies-xlsx.php';

vacanciesSessionStart();
if (!vacanciesIsAdmin() || !vacanciesDbReady()) {
    header('Location: kena-sxoleion-login.php');
    exit;
}
if (vacanciesActorNeedsPasswordChange()) {
    header('Location: kena-sxoleion-password.php');
    exit;
}

$rounds = vacanciesRounds();
$roundId = isset($_REQUEST['round']) ? (int) $_REQUEST['round'] : 0;
if ($roundId <= 0 && isset($rounds[0])) $roundId = (int) $rounds[0]['id'];
$round = vacanciesRound($roundId);
$allocation = $round ? vacanciesDashboardAllocation($roundId) : array('specialties'=>array(),'schools'=>array());
$vacancyPreview = vacanciesCoverageBuildVacancies($allocation);

$message = '';
$messageType = 'success';
$teachersData = null;
$stat51 = null;
$match = null;
$coverageAction = isset($_POST['coverage_action']) ? (string) $_POST['coverage_action'] : '';
$vacancySource = isset($_POST['vacancy_source']) && $_POST['vacancy_source'] === 'myschool' ? 'myschool' : 'dde';

// Keep only normalized preview data in the authenticated PHP session between
// "load" and "match". Raw uploaded CSV/ZIP bytes are never persisted.
if (isset($_SESSION['vacancies_coverage_preview']) && is_array($_SESSION['vacancies_coverage_preview'])) {
    $cached = $_SESSION['vacancies_coverage_preview'];
    if (isset($cached['round_id']) && (int)$cached['round_id'] === $roundId) {
        $teachersData = isset($cached['teachers']) && is_array($cached['teachers']) ? $cached['teachers'] : null;
        $stat51 = isset($cached['stat51']) && is_array($cached['stat51']) ? $cached['stat51'] : null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!vacanciesCsrfValid(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $message = 'Η συνεδρία έληξε. Ανανέωσε τη σελίδα και προσπάθησε ξανά.';
        $messageType = 'danger';
    } elseif (!$round) {
        $message = 'Δεν βρέθηκε έγκυρος γύρος κενών.';
        $messageType = 'danger';
    } elseif ($coverageAction === 'clear') {
        unset($_SESSION['vacancies_coverage_preview']);
        $teachersData = null;
        $stat51 = null;
        $match = null;
        $message = 'Τα προσωρινά δεδομένα 4.8 / 5.1 καθαρίστηκαν από τη συνεδρία.';
    } elseif ($coverageAction === 'export_preview') {
        if (!$teachersData || !$stat51) {
            $message = 'Πρώτα φόρτωσε και έλεγξε τα στατιστικά 4.8 και 5.1.';
            $messageType = 'warning';
        } else {
            list($exportOk, $exportPayload) = vacanciesXlsxBuildCoveragePreview($round, $teachersData, $vacancyPreview, $stat51);
            if (!$exportOk) {
                $message = (string)$exportPayload;
                $messageType = 'danger';
            } else {
                $datePart = !empty($round['reference_date']) ? preg_replace('/[^0-9-]/', '', (string)$round['reference_date']) : date('Y-m-d');
                $filename = 'proepiskopisi-kalypsis-dd' . ($datePart !== '' ? '-' . $datePart : '') . '.xlsx';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Length: ' . strlen($exportPayload));
                header('Cache-Control: private, no-store, max-age=0');
                echo $exportPayload;
                exit;
            }
        }
    } elseif ($coverageAction === 'match') {
        if (!$teachersData || !$stat51) {
            $message = 'Πρώτα φόρτωσε και έλεγξε τα στατιστικά 4.8 και 5.1.';
            $messageType = 'warning';
        } else {
            try {
                $match = vacanciesCoverageMatch($teachersData, $stat51, $allocation, $vacancySource);
                $message = 'Ο υπολογισμός προτάσεων ολοκληρώθηκε. Οι προτάσεις είναι υποστηρικτικές και δεν καταχωρίζουν μετακίνηση ή διάθεση.';
            } catch (Throwable $e) {
                error_log('Vacancy coverage matcher failed: ' . $e->getMessage());
                $message = 'Τα δεδομένα φόρτωσαν κανονικά, αλλά ο matcher δεν ολοκληρώθηκε. Οι δύο λίστες παραμένουν διαθέσιμες για έλεγχο.';
                $messageType = 'danger';
            }
        }
    } else {
        list($ok48, $err48, $text48) = vacanciesCoverageReadUpload(isset($_FILES['stat48']) ? $_FILES['stat48'] : array());
        list($ok51, $err51, $text51) = vacanciesCoverageReadUpload(isset($_FILES['stat51']) ? $_FILES['stat51'] : array());
        if (!$ok48 || !$ok51) {
            $message = trim(($ok48 ? '' : '4.8: ' . $err48 . ' ') . ($ok51 ? '' : '5.1: ' . $err51));
            $messageType = 'danger';
        } else {
            list($parsed48, $parseErr48, $teachersData) = vacanciesCoverageParseStat48Text($text48);
            list($parsed51, $parseErr51, $stat51) = vacanciesCoverageParseStat51Text($text51);
            if (!$parsed48 || !$parsed51) {
                $message = trim(($parsed48 ? '' : '4.8: ' . $parseErr48 . ' ') . ($parsed51 ? '' : '5.1: ' . $parseErr51));
                $messageType = 'danger';
                $teachersData = $parsed48 ? $teachersData : null;
                $stat51 = $parsed51 ? $stat51 : null;
            } else {
                $_SESSION['vacancies_coverage_preview'] = array(
                    'round_id'=>$roundId,
                    'loaded_at'=>date('Y-m-d H:i:s'),
                    'teachers'=>$teachersData,
                    'stat51'=>$stat51,
                );
                $message = 'Τα δεδομένα φόρτωσαν. Έλεγξε πρώτα τις λίστες εκπαιδευτικών και κενών και μετά εκτέλεσε τον matcher.';
            }
        }
    }
}

function coverageFmt($value)
{
    $value = (float)$value;
    return abs($value - round($value)) < 0.001 ? (string)(int)round($value) : number_format($value, 1, ',', '.');
}

function coverageAssignmentLabel($kind)
{
    if ($kind === 'A') return 'Α΄ ανάθεση';
    if ($kind === 'B') return 'Β΄ ανάθεση';
    if ($kind === 'same_specialty') return 'Ίδια ειδικότητα';
    return '—';
}

function coverageMovementLabel($row)
{
    if (!empty($row['same_school'])) return 'Ήδη υπηρετεί στο σχολείο';
    if (!empty($row['continued_destination'])) return 'Συνέχιση στην ίδια νέα μονάδα';
    if (!empty($row['new_destination'])) return 'Νέα σχολική μονάδα';
    return 'Μετακίνηση';
}
?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Προτάσεις κάλυψης κενών ΔΔΕ</title>
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo vacanciesH(edu_asset_url('assets/vacancies.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-vacancies edu-vacancy-coverage">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell vacancies-shell">
  <section class="hero vacancies-hero">
    <span class="hero-kicker">ΔΙΕΥΘΥΝΣΗ — DECISION SUPPORT</span>
    <h1>Προτάσεις κάλυψης κενών</h1>
    <p>Συνδυάζει υπόλοιπο υποχρεωτικού ωραρίου από το myschool 4.8, ελλείμματα/αναθέσεις μαθημάτων από το 5.1 και τα κενά του επιλεγμένου γύρου της ΔΔΕ.</p>
  </section>

  <div class="vacancies-top-actions">
    <a class="vacancy-top-action" href="kena-sxoleion-admin.php<?php echo $roundId ? '?round='.(int)$roundId : ''; ?>">← Dashboard κενών</a>
    <a class="vacancy-top-action vacancy-top-action--logout" href="kena-sxoleion-login.php?logout=1">Αποσύνδεση</a>
  </div>

  <?php if ($message !== '') { ?><div class="vacancy-alert vacancy-alert--<?php echo vacanciesH($messageType); ?>"><?php echo vacanciesH($message); ?></div><?php } ?>

  <section class="card vacancy-coverage-import">
    <h2>1. Δεδομένα ανάλυσης</h2>
    <form method="post" enctype="multipart/form-data" action="kena-sxoleion-kalypsi.php">
      <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
      <div class="field">
        <label for="round">Γύρος κενών ΔΔΕ</label>
        <select id="round" name="round">
          <?php foreach ($rounds as $r) { ?>
          <option value="<?php echo (int)$r['id']; ?>"<?php echo (int)$r['id']===$roundId?' selected':''; ?>><?php echo vacanciesH($r['title'].' · '.$r['reference_date'].' · '.$r['status']); ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="vacancy-coverage-upload-grid">
        <div class="field">
          <label for="stat48">Στατιστικό 4.8</label>
          <input id="stat48" name="stat48" type="file" accept=".csv,.zip" required>
          <p class="help">Εκπαιδευτικοί, ειδικότητες, τοποθετήσεις και Υπόλοιπο Υποχρεωτικού Διδακτικού Ωραρίου.</p>
        </div>
        <div class="field">
          <label for="stat51">Στατιστικό 5.1</label>
          <input id="stat51" name="stat51" type="file" accept=".csv,.zip" required>
          <p class="help">Μαθήματα με έλλειμμα και κλάδοι Α΄/Β΄ ανάθεσης.</p>
        </div>
      </div>
      <input type="hidden" name="coverage_action" value="load">
      <div class="vacancy-coverage-submit"><button type="submit" class="btn primary">Φόρτωση και έλεγχος δεδομένων</button></div>
    </form>
    <div class="note vacancy-coverage-privacy"><strong>Ιδιωτικότητα:</strong> τα αρχεία αναλύονται μόνο για το τρέχον αίτημα και δεν αποθηκεύονται από αυτό το εργαλείο στη βάση.</div>
  </section>

  <?php if ($teachersData && $stat51) { ?>
  <?php
    $vacancyPreviewHours = 0.0;
    foreach ($vacancyPreview as $previewVacancy) $vacancyPreviewHours += (float)$previewVacancy['hours'];
  ?>
  <section class="card vacancy-coverage-preview">
    <h2>2. Έλεγχος φόρτωσης</h2>
    <p class="cap">Πριν γίνει οποιοσδήποτε υπολογισμός, έλεγξε ότι φορτώθηκαν οι σωστοί εκπαιδευτικοί και τα σωστά κενά του επιλεγμένου γύρου.</p>

    <div class="vacancy-coverage-load-status">
      <div class="is-ok"><strong>4.8 ✓</strong><span><?php echo (int)$teachersData['teacher_count']; ?> εκπαιδευτικοί · <?php echo vacanciesH(coverageFmt($teachersData['remaining_hours'])); ?> διαθέσιμες ώρες</span></div>
      <div class="is-ok"><strong>5.1 ✓</strong><span>myschool: <?php echo (int)$stat51['myschool_deficit_rows']; ?> γραμμές · <?php echo vacanciesH(coverageFmt($stat51['myschool_deficit_hours'])); ?> ώρες<br><small>από μονάδα: <?php echo (int)$stat51['unit_deficit_rows']; ?> γραμμές · <?php echo vacanciesH(coverageFmt($stat51['unit_deficit_hours'])); ?> ώρες</small></span></div>
      <div class="<?php echo $vacancyPreview ? 'is-ok' : 'is-warning'; ?>"><strong>Κενά ΔΔΕ <?php echo $vacancyPreview ? '✓' : '!' ?></strong><span><?php echo count($vacancyPreview); ?> εγγραφές · <?php echo vacanciesH(coverageFmt($vacancyPreviewHours)); ?> ώρες</span></div>
    </div>

    <div class="vacancy-coverage-reconciliation">
      <h3>Συμφωνία Κενών ΔΔΕ ↔ 5.1</h3>
      <?php $agreementDiff = $vacancyPreviewHours - (float)$stat51['myschool_deficit_hours']; ?>
      <div class="vacancy-coverage-load-status">
        <div><strong>Κενά γύρου ΔΔΕ</strong><span><?php echo vacanciesH(coverageFmt($vacancyPreviewHours)); ?> ώρες</span></div>
        <div><strong>5.1 — Εκτίμηση Κενών από myschool</strong><span><?php echo vacanciesH(coverageFmt($stat51['myschool_deficit_hours'])); ?> ώρες</span></div>
        <div class="<?php echo abs($agreementDiff) < 0.001 ? 'is-ok' : 'is-warning'; ?>"><strong>Διαφορά</strong><span><?php echo vacanciesH(coverageFmt(abs($agreementDiff))); ?> ώρες<?php echo $agreementDiff < 0 ? ' περισσότερες στο myschool' : ($agreementDiff > 0 ? ' περισσότερες στον γύρο ΔΔΕ' : ''); ?></span></div>
      </div>
      <?php if (empty($stat51['school_scoped'])) { ?>
        <p class="help"><strong>Σημαντικό:</strong> το συγκεκριμένο 5.1 δεν περιέχει κωδικό σχολικής μονάδας. Η σύγκριση με τα κενά ΔΔΕ είναι συνολική και όχι ανά σχολείο. Αν μελλοντικό 5.1 περιέχει σχολικό κωδικό, το εργαλείο θα μπορεί να κάνει school-level σύγκριση.</p>
      <?php } ?>
    </div>

    <div class="vacancy-coverage-preview-grid">
      <section class="vacancy-coverage-preview-pane">
        <header><h3>Εκπαιδευτικοί με υπόλοιπο (4.8)</h3><span><?php echo (int)$teachersData['teacher_count']; ?> άτομα</span></header>
        <div class="vacancy-coverage-scroll">
          <table class="vacancy-table vacancy-coverage-table vacancy-coverage-table--preview">
            <thead><tr><th>Εκπαιδευτικός</th><th>Ειδικότητα</th><th>Υπόλοιπο</th><th>Υπηρετεί</th></tr></thead>
            <tbody>
            <?php foreach ($teachersData['teachers'] as $teacher) {
              $placementNames = array();
              foreach ((array)$teacher['placements'] as $placement) if (!empty($placement['school_name'])) $placementNames[] = $placement['school_name'];
            ?>
              <tr>
                <td><strong><?php echo vacanciesH(trim($teacher['last_name'].' '.$teacher['first_name'])); ?></strong><small>Α.Μ. <?php echo vacanciesH($teacher['am']); ?></small></td>
                <td><?php echo vacanciesH($teacher['primary_code']); ?><?php echo $teacher['secondary_code'] !== '' ? '<small>2η: '.vacanciesH($teacher['secondary_code']).'</small>' : ''; ?></td>
                <td><strong><?php echo vacanciesH(coverageFmt($teacher['remaining_hours'])); ?></strong></td>
                <td><?php echo vacanciesH(implode(' · ', $placementNames)); ?></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="vacancy-coverage-preview-pane">
        <header><h3>Κενά επιλεγμένου γύρου ΔΔΕ</h3><span><?php echo count($vacancyPreview); ?> εγγραφές</span></header>
        <div class="vacancy-coverage-scroll">
          <table class="vacancy-table vacancy-coverage-table vacancy-coverage-table--preview">
            <thead><tr><th>Σχολείο</th><th>Ειδικότητα</th><th>Ώρες</th></tr></thead>
            <tbody>
            <?php if (!$vacancyPreview) { ?>
              <tr><td colspan="3">Δεν βρέθηκαν υποβλημένα κενά στον επιλεγμένο γύρο.</td></tr>
            <?php } else { foreach ($vacancyPreview as $vacancy) { ?>
              <tr>
                <td><strong><?php echo vacanciesH($vacancy['school_name']); ?></strong><small><?php echo vacanciesH($vacancy['ministry_code']); ?></small></td>
                <td><strong><?php echo vacanciesH($vacancy['code']); ?></strong><small><?php echo vacanciesH($vacancy['label']); ?></small></td>
                <td><strong><?php echo vacanciesH(coverageFmt($vacancy['hours'])); ?></strong></td>
              </tr>
            <?php }} ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <div class="vacancy-coverage-source-picker">
      <strong>Πηγή κενών για τον matcher</strong>
      <p class="help">Επίλεξε αν οι διαθέσιμοι εκπαιδευτικοί του 4.8 θα ελεγχθούν απέναντι στα δηλωμένα κενά του γύρου ΔΔΕ ή στην «Εκτίμηση Κενών από myschool» του 5.1.</p>
    </div>
    <div class="vacancy-coverage-preview-actions">
      <form method="post" action="kena-sxoleion-kalypsi.php" class="vacancy-coverage-match-form">
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <input type="hidden" name="round" value="<?php echo (int)$roundId; ?>">
        <input type="hidden" name="coverage_action" value="match">
        <div class="vacancy-coverage-source-options">
          <label><input type="radio" name="vacancy_source" value="dde" checked> Κενά γύρου ΔΔΕ</label>
          <label><input type="radio" name="vacancy_source" value="myschool"> Εκτίμηση Κενών από myschool (5.1)</label>
        </div>
        <button type="submit" class="btn primary">Υπολογισμός προτάσεων κάλυψης</button>
      </form>
      <form method="post" action="kena-sxoleion-kalypsi.php">
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <input type="hidden" name="round" value="<?php echo (int)$roundId; ?>">
        <input type="hidden" name="coverage_action" value="export_preview">
        <button type="submit" class="btn">Εξαγωγή Excel (2 φύλλα)</button>
      </form>
      <form method="post" action="kena-sxoleion-kalypsi.php">
        <input type="hidden" name="csrf" value="<?php echo vacanciesH(vacanciesCsrfToken()); ?>">
        <input type="hidden" name="round" value="<?php echo (int)$roundId; ?>">
        <input type="hidden" name="coverage_action" value="clear">
        <button type="submit" class="btn">Καθαρισμός</button>
      </form>
    </div>
    <p class="help">Σε αυτό το στάδιο δεν έχει εκτελεστεί ο matcher. Τα κανονικοποιημένα δεδομένα κρατούνται μόνο προσωρινά στην authenticated συνεδρία ώστε να μπορείς να τα ελέγξεις πρώτα.</p>
  </section>
  <?php } ?>

  <?php if ($teachersData && $stat51 && $match) { ?>
  <section class="card">
    <h2>3. Σύνοψη προτάσεων</h2>
    <div class="vacancy-coverage-metrics">
      <div><strong><?php echo (int)$teachersData['teacher_count']; ?></strong><span>εκπαιδευτικοί με θετικό υπόλοιπο</span></div>
      <div><strong><?php echo vacanciesH(coverageFmt($teachersData['remaining_hours'])); ?></strong><span>διαθέσιμες ώρες 4.8</span></div>
      <div><strong><?php echo vacanciesH(coverageFmt($match['vacancy_hours'])); ?></strong><span><?php echo $match['vacancy_source'] === 'myschool' ? 'ώρες ελλείμματος myschool 5.1' : 'ώρες κενών επιλεγμένου γύρου'; ?></span></div>
      <div><strong><?php echo vacanciesH(coverageFmt($match['suggested_hours'])); ?></strong><span>ώρες που προτείνονται</span></div>
    </div>
    <?php if (empty($stat51['school_scoped']) && $match['vacancy_source'] === 'myschool') { ?>
      <div class="vacancy-alert vacancy-alert--warning vacancy-coverage-scope-warning">
        <strong>Περιορισμός του συγκεκριμένου 5.1:</strong> δεν περιέχει κωδικό/ονομασία σχολικής μονάδας. Στη λειτουργία myschool ο matcher αντιστοιχίζει εκπαιδευτικούς με μαθήματα και ώρες ελλείμματος, αλλά δεν μπορεί να προτείνει συγκεκριμένο σχολείο προορισμού.
      </div>
    <?php } elseif (empty($stat51['school_scoped'])) { ?>
      <div class="vacancy-alert vacancy-alert--warning vacancy-coverage-scope-warning">
        <strong>Περιορισμός του συγκεκριμένου 5.1:</strong> δεν περιέχει κωδικό/ονομασία σχολικής μονάδας. Στη λειτουργία «Κενά γύρου ΔΔΕ» το σχολείο προορισμού προκύπτει από τη βάση της ΔΔΕ και το 5.1 χρησιμοποιείται μόνο για έλεγχο μαθήματος και Α΄/Β΄ ανάθεσης.
      </div>
    <?php } else { ?>
      <div class="vacancy-alert vacancy-alert--success">Το 5.1 περιέχει σχολική μονάδα· οι αντιστοιχίσεις μαθημάτων μπορούν να ελεγχθούν και ανά σχολείο.</div>
    <?php } ?>
    <p class="help">5.1: <strong>myschool</strong> <?php echo (int)$stat51['myschool_deficit_rows']; ?> γραμμές · <?php echo vacanciesH(coverageFmt($stat51['myschool_deficit_hours'])); ?> ώρες · <strong>από μονάδα</strong> <?php echo (int)$stat51['unit_deficit_rows']; ?> γραμμές · <?php echo vacanciesH(coverageFmt($stat51['unit_deficit_hours'])); ?> ώρες.</p>
  </section>

  <?php
    $byTeacher = vacanciesCoverageRecommendationsByTeacher($match, $teachersData);
    $bySchool = vacanciesCoverageRecommendationsBySchool($match);
    $byVacancy = vacanciesCoverageRecommendationsByVacancy($match);
  ?>
  <section class="card vacancy-coverage-results">
    <h2>4. Προτάσεις κάλυψης</h2>
    <?php if (empty($match['recommendations'])) { ?>
      <div class="vacancy-alert vacancy-alert--warning">Δεν προέκυψε ασφαλής αντιστοίχιση με τα διαθέσιμα δεδομένα.</div>
    <?php } else { ?>
      <div class="vacancy-coverage-tabs" role="tablist" aria-label="Προβολή προτάσεων κάλυψης">
        <button type="button" class="vacancy-coverage-tab is-active" data-coverage-tab="teacher" role="tab" aria-selected="true">Ανά εκπαιδευτικό</button>
        <button type="button" class="vacancy-coverage-tab" data-coverage-tab="school" role="tab" aria-selected="false">Ανά σχολείο</button>
        <button type="button" class="vacancy-coverage-tab" data-coverage-tab="vacancy" role="tab" aria-selected="false">Ανά ακάλυπτο κενό</button>
        <button type="button" class="vacancy-coverage-tab" data-coverage-tab="sequence" role="tab" aria-selected="false">Σειρά προτάσεων</button>
      </div>

      <div class="vacancy-coverage-panel" data-coverage-panel="teacher" role="tabpanel">
        <?php foreach ($byTeacher as $group) { ?>
          <article class="vacancy-coverage-group">
            <header class="vacancy-coverage-group__header">
              <div><strong><?php echo vacanciesH($group['teacher_name']); ?></strong><small>Α.Μ. <?php echo vacanciesH($group['am']); ?> · <?php echo vacanciesH($group['primary_code']); ?><?php echo $group['secondary_code']!==''?' / 2η '.$group['secondary_code']:''; ?></small></div>
              <div class="vacancy-coverage-group__numbers"><strong><?php echo vacanciesH(coverageFmt($group['suggested_hours'])); ?> ώρες</strong><small>από <?php echo vacanciesH(coverageFmt($group['initial_hours'])); ?> διαθέσιμες · υπόλοιπο <?php echo vacanciesH(coverageFmt($group['remaining_hours'])); ?></small></div>
            </header>
            <?php if (!empty($group['new_destinations'])) { ?><p class="help">Νέες μονάδες στην πρόταση: <?php echo vacanciesH((string)count($group['new_destinations'])); ?> · <?php echo vacanciesH(implode(' · ', array_values($group['new_destinations']))); ?></p><?php } ?>
            <div class="vacancy-table-wrap"><table class="vacancy-table vacancy-coverage-table vacancy-coverage-table--compact"><thead><tr><th>Σχολείο</th><th>Κενό</th><th>Ώρες</th><th>Ανάθεση</th><th>Μετακίνηση</th></tr></thead><tbody>
            <?php foreach ($group['rows'] as $r) { ?>
              <tr><td><strong><?php echo vacanciesH($r['school_name']); ?></strong><small><?php echo vacanciesH($r['ministry_code']); ?></small></td><td><?php echo vacanciesH($r['vacancy_code']); ?> · <?php echo vacanciesH($r['vacancy_label']); ?></td><td><strong><?php echo vacanciesH(coverageFmt($r['hours'])); ?></strong></td><td><?php echo vacanciesH(coverageAssignmentLabel($r['assignment_kind'])); ?></td><td><?php echo vacanciesH(coverageMovementLabel($r)); ?></td></tr>
            <?php } ?>
            </tbody></table></div>
          </article>
        <?php } ?>
      </div>

      <div class="vacancy-coverage-panel hidden" data-coverage-panel="school" role="tabpanel">
        <?php foreach ($bySchool as $group) { $remaining=max(0,$group['vacancy_hours']-$group['suggested_hours']); ?>
          <article class="vacancy-coverage-group">
            <header class="vacancy-coverage-group__header"><div><strong><?php echo vacanciesH($group['school_name']); ?></strong><small><?php echo vacanciesH($group['ministry_code']); ?><?php echo !empty($group['school_address'])?' · '.vacanciesH($group['school_address']):''; ?></small></div><div class="vacancy-coverage-group__numbers"><strong><?php echo vacanciesH(coverageFmt($group['suggested_hours'])); ?>/<?php echo vacanciesH(coverageFmt($group['vacancy_hours'])); ?> ώρες</strong><small>προτεινόμενη κάλυψη · απομένουν <?php echo vacanciesH(coverageFmt($remaining)); ?></small></div></header>
            <?php if (!$group['rows']) { ?><p class="cap">Δεν προέκυψε πρόταση κάλυψης για τη μονάδα.</p><?php } else { ?>
            <div class="vacancy-table-wrap"><table class="vacancy-table vacancy-coverage-table vacancy-coverage-table--compact"><thead><tr><th>Εκπαιδευτικός</th><th>Ειδικότητα</th><th>Κενό</th><th>Ώρες</th><th>Ανάθεση</th></tr></thead><tbody>
            <?php foreach ($group['rows'] as $r) { ?><tr><td><strong><?php echo vacanciesH($r['teacher_name']); ?></strong><small>Α.Μ. <?php echo vacanciesH($r['am']); ?></small></td><td><?php echo vacanciesH($r['primary_code']); ?><?php echo $r['secondary_code']!==''?' / '.$r['secondary_code']:''; ?></td><td><?php echo vacanciesH($r['vacancy_code']); ?></td><td><strong><?php echo vacanciesH(coverageFmt($r['hours'])); ?></strong></td><td><?php echo vacanciesH(coverageAssignmentLabel($r['assignment_kind'])); ?></td></tr><?php } ?>
            </tbody></table></div><?php } ?>
          </article>
        <?php } ?>
      </div>

      <div class="vacancy-coverage-panel hidden" data-coverage-panel="vacancy" role="tabpanel">
        <?php foreach ($byVacancy as $group) { ?>
          <article class="vacancy-coverage-group<?php echo $group['remaining_hours']>0.001?' vacancy-coverage-group--open':''; ?>">
            <header class="vacancy-coverage-group__header"><div><strong><?php echo vacanciesH($group['school_name']); ?></strong><small><?php echo vacanciesH($group['code'].' · '.$group['label']); ?></small></div><div class="vacancy-coverage-group__numbers"><strong><?php echo vacanciesH(coverageFmt($group['remaining_hours'])); ?> ώρες</strong><small>ακάλυπτες από <?php echo vacanciesH(coverageFmt($group['initial_hours'])); ?> · πρόταση <?php echo vacanciesH(coverageFmt($group['suggested_hours'])); ?></small></div></header>
            <?php if (!$group['rows']) { ?><p class="cap">Δεν βρέθηκε διαθέσιμος εκπαιδευτικός με ασφαλή αντιστοίχιση.</p><?php } else { ?>
              <div class="vacancy-coverage-chip-list"><?php foreach ($group['rows'] as $r) { ?><span class="vacancy-coverage-chip"><strong><?php echo vacanciesH($r['teacher_name']); ?></strong> · <?php echo vacanciesH(coverageFmt($r['hours'])); ?> ώρες · <?php echo vacanciesH(coverageAssignmentLabel($r['assignment_kind'])); ?></span><?php } ?></div>
            <?php } ?>
          </article>
        <?php } ?>
      </div>

      <div class="vacancy-coverage-panel hidden" data-coverage-panel="sequence" role="tabpanel">
        <div class="vacancy-table-wrap">
          <table class="vacancy-table vacancy-coverage-table">
            <thead><tr><th>Εκπαιδευτικός</th><th>Υπόλοιπο</th><th>Σχολείο προορισμού</th><th>Κενό</th><th>Πρόταση</th><th>Τεκμηρίωση 5.1</th></tr></thead>
            <tbody>
            <?php foreach ($match['recommendations'] as $r) { $ev=$r['evidence']; ?>
              <tr>
                <td><strong><?php echo vacanciesH($r['teacher_name']); ?></strong><small>Α.Μ. <?php echo vacanciesH($r['am']); ?> · <?php echo vacanciesH($r['primary_code']); ?><?php echo $r['secondary_code']!==''?' / 2η '.$r['secondary_code']:''; ?></small></td>
                <td><?php echo vacanciesH(coverageFmt($r['teacher_initial_hours'])); ?> ώρες</td>
                <td><strong><?php echo vacanciesH($r['school_name']); ?></strong><small><?php echo vacanciesH($r['ministry_code']); ?> · <?php echo vacanciesH(coverageMovementLabel($r)); ?></small></td>
                <td><?php echo vacanciesH($r['vacancy_code']); ?> · <?php echo vacanciesH(coverageFmt($r['vacancy_initial_hours'])); ?> ώρες</td>
                <td><strong><?php echo vacanciesH(coverageFmt($r['hours'])); ?> ώρες</strong><small><?php echo vacanciesH(coverageAssignmentLabel($r['assignment_kind'])); ?></small></td>
                <td><?php if ($ev) { ?><strong><?php echo vacanciesH($ev['subject']); ?></strong><small><?php echo vacanciesH(trim($ev['grade'].' '.$ev['sector'])); ?> · <?php echo vacanciesH($ev['level'].'΄ ανάθεση με '.$ev['teacher_code']); ?></small><?php } else { ?><span class="muted">Ίδια ειδικότητα — χωρίς ανάγκη γέφυρας Α΄/Β΄</span><?php } ?></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } ?>
  </section>

  <section class="card">
    <h2>4. Υπόλοιπα μετά την πρόταση</h2>
    <?php $leftTeachers=array_filter($match['teacher_remaining'],function($h){return $h>0.001;}); ?>
    <?php if (!$leftTeachers) { ?><p class="cap">Με βάση την προτεινόμενη κατανομή δεν απομένει διαθέσιμο υπόλοιπο εκπαιδευτικών.</p><?php } else { ?>
      <h3>Εκπαιδευτικοί με διαθέσιμες ώρες</h3>
      <div class="vacancy-coverage-chip-list">
      <?php foreach ($leftTeachers as $am=>$hours) { $t=$teachersData['teachers'][$am]; ?>
        <span class="vacancy-coverage-chip"><strong><?php echo vacanciesH(trim($t['last_name'].' '.$t['first_name'])); ?></strong> · <?php echo vacanciesH($t['primary_code']); ?> · <?php echo vacanciesH(coverageFmt($hours)); ?> ώρες</span>
      <?php } ?>
      </div>
    <?php } ?>
    <?php $leftVacancies=array_filter($match['vacancy_remaining'],function($h){return $h>0.001;}); ?>
    <h3>Κενά που παραμένουν ακάλυπτα</h3>
    <?php if (!$leftVacancies) { ?><p class="cap">Η προτεινόμενη κατανομή καλύπτει όλες τις ώρες του επιλεγμένου γύρου που μπόρεσαν να αντιστοιχιστούν.</p><?php } else { ?>
      <div class="vacancy-coverage-chip-list"><?php foreach ($byVacancy as $group) { if ($group['remaining_hours']<=0.001) continue; ?><span class="vacancy-coverage-chip"><strong><?php echo vacanciesH($group['school_name']); ?></strong> · <?php echo vacanciesH($group['code']); ?> · <?php echo vacanciesH(coverageFmt($group['remaining_hours'])); ?> ώρες</span><?php } ?></div>
    <?php } ?>
  </section>
  <?php } ?>

  <section class="card">
    <h2>Κανόνες της πρώτης έκδοσης</h2>
    <p class="cap">Η πρώτη προτεραιότητα είναι να συμπληρώνεται το υποχρεωτικό ωράριο σε σχολική μονάδα όπου ο εκπαιδευτικός ήδη υπηρετεί, εφόσον υπάρχει επιλέξιμο κενό. Μέσα στην ίδια κατηγορία προτιμώνται η ίδια ειδικότητα / Α΄ ανάθεση και έπειτα η Β΄ ανάθεση. Μόνο όταν δεν υπάρχει κατάλληλη κάλυψη στις ήδη υπάρχουσες μονάδες εξετάζεται νέα σχολική μονάδα· τότε η μηχανή προτιμά να συνεχίζει στην ίδια νέα μονάδα αντί να διασπείρει τον εκπαιδευτικό σε περισσότερα σχολεία. Δεν χρησιμοποιείται ακόμη πραγματική γεωγραφική απόσταση. Οι διευθύνσεις των σχολείων μεταφέρονται ήδη στο αποτέλεσμα ώστε το επόμενο βήμα να είναι επαληθευμένο μητρώο συντεταγμένων/αποστάσεων και όχι εκτίμηση από ονόματα ή ΤΚ.</p>
    <div class="note"><strong>Δεν είναι αυτόματη τοποθέτηση.</strong> Το αποτέλεσμα είναι υποστηρικτικό για τη ΔΔΕ. Δεν γράφει στη βάση, δεν μειώνει τα δηλωμένα κενά και δεν δημιουργεί υπηρεσιακή μεταβολή.</div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script>
(function(){
  var tabs=document.querySelectorAll('[data-coverage-tab]');
  var panels=document.querySelectorAll('[data-coverage-panel]');
  if(!tabs.length) return;
  tabs.forEach(function(tab){ tab.addEventListener('click',function(){
    var key=tab.getAttribute('data-coverage-tab');
    tabs.forEach(function(t){ var active=t===tab; t.classList.toggle('is-active',active); t.setAttribute('aria-selected',active?'true':'false'); });
    panels.forEach(function(p){ p.classList.toggle('hidden',p.getAttribute('data-coverage-panel')!==key); });
  }); });
})();
</script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
