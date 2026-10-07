<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/legal-audit.php';
$rows = legalAuditCatalogueRows();
$counts = legalAuditSummaryCounts();
$statusOrder = array('review_due' => 0, 'pending' => 1, 'partial' => 2, 'verified' => 3);
usort($rows, function ($a, $b) use ($statusOrder) {
    $sa = isset($a['audit']['status']) ? $a['audit']['status'] : 'pending';
    $sb = isset($b['audit']['status']) ? $b['audit']['status'] : 'pending';
    $oa = isset($statusOrder[$sa]) ? $statusOrder[$sa] : 1;
    $ob = isset($statusOrder[$sb]) ? $statusOrder[$sb] : 1;
    if ($oa !== $ob) return $oa - $ob;
    return strcasecmp($a['title'], $b['title']);
});
$h = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Νομική επικαιρότητα · <?php echo $h(EDU_TOOLS_NAME); ?></title>
  <link rel="stylesheet" href="<?php echo $h(edu_asset_url('assets/common.css')); ?>">
  <style>
    .legal-audit-page{max-width:1060px;margin:auto}
    .legal-audit-hero{background:linear-gradient(135deg,#173b7a,#1f6feb);color:#fff;border-radius:18px;padding:28px;margin-bottom:18px;box-shadow:var(--edu-shadow)}
    .legal-audit-hero h1{margin:0 0 8px;color:#fff;font-size:clamp(25px,4vw,38px)}
    .legal-audit-hero p{margin:5px 0;color:#e9f1ff;line-height:1.55}
    .legal-audit-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:18px}
    .legal-audit-stat{padding:15px;border:1px solid var(--edu-border);border-radius:14px;background:var(--edu-surface);box-shadow:var(--edu-shadow-sm)}
    .legal-audit-stat strong{display:block;font-size:24px;color:var(--edu-text)}
    .legal-audit-stat span{color:var(--edu-muted);font-size:13px}
    .legal-audit-note{margin:0 0 18px;padding:14px 16px;border:1px solid #d5e4ff;border-radius:12px;background:var(--edu-primary-soft);color:var(--edu-primary-dark);line-height:1.55}
    .legal-audit-list{display:grid;gap:10px}
    .legal-audit-row{scroll-margin-top:18px;padding:15px 16px;border:1px solid var(--edu-border);border-radius:14px;background:var(--edu-surface);box-shadow:var(--edu-shadow-sm)}
    .legal-audit-row__top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
    .legal-audit-row h2{margin:0;font-size:16px;line-height:1.35}
    .legal-audit-row h2 a{color:var(--edu-text);text-decoration:none}.legal-audit-row h2 a:hover{text-decoration:underline}
    .legal-audit-status{display:inline-flex;flex:0 0 auto;padding:5px 9px;border-radius:999px;font-size:12px;font-weight:800;background:#eef2f6;color:#475569}
    .legal-audit-status--verified{background:#e8f7ee;color:#166534}.legal-audit-status--partial{background:#fff4df;color:#7b4900}.legal-audit-status--review_due{background:#fff1f2;color:#9f1239}
    .legal-audit-meta{display:flex;flex-wrap:wrap;gap:6px 14px;margin-top:9px;color:var(--edu-muted);font-size:13px}
    .legal-audit-scope,.legal-audit-trigger{margin:8px 0 0;line-height:1.5;font-size:13.5px}.legal-audit-trigger{color:var(--edu-muted)}
    @media(max-width:720px){.legal-audit-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.legal-audit-row__top{display:block}.legal-audit-status{margin-top:8px}}
  </style>
</head>
<body class="edu-ui edu-calc-standard edu-page-legal-audit">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main id="main-content" class="legal-audit-page">
  <section class="legal-audit-hero">
    <h1>Νομική επικαιρότητα εργαλείων</h1>
    <p>Κεντρικό μητρώο του τελευταίου <strong>καταγεγραμμένου</strong> ελέγχου πηγών. Η ημερομηνία δεν προκύπτει αυτόματα από την έκδοση του κώδικα: καταχωρίζεται μόνο όταν έχει γίνει πραγματικός νομικός/πηγικός έλεγχος.</p>
  </section>

  <section class="legal-audit-summary" aria-label="Σύνοψη ελέγχων">
    <div class="legal-audit-stat"><strong><?php echo (int) $counts['verified']; ?></strong><span>πλήρεις έλεγχοι</span></div>
    <div class="legal-audit-stat"><strong><?php echo (int) $counts['partial']; ?></strong><span>στοχευμένοι έλεγχοι</span></div>
    <div class="legal-audit-stat"><strong><?php echo (int) $counts['review_due']; ?></strong><span>χρειάζονται επανέλεγχο</span></div>
    <div class="legal-audit-stat"><strong><?php echo (int) $counts['pending']; ?></strong><span>χωρίς καταγραφή στο νέο μητρώο</span></div>
  </section>

  <p class="legal-audit-note"><strong>Σημαντικό:</strong> «Χωρίς καταγραφή» δεν σημαίνει ότι ένα εργαλείο είναι λανθασμένο ή χωρίς πηγές. Σημαίνει ότι δεν έχει ακόμη μεταφερθεί στο νέο μητρώο freshness. Αντίστοιχα, «στοχευμένος έλεγχος» δηλώνει ακριβώς το πεδίο που επανελέγχθηκε και δεν παρουσιάζεται ως πλήρης επαλήθευση όλου του εργαλείου.</p>

  <section class="legal-audit-list" aria-label="Κατάσταση ανά εργαλείο">
  <?php foreach ($rows as $row): ?>
    <?php
      $a = $row['audit'];
      $status = !empty($a['status']) ? $a['status'] : 'pending';
      $date = !empty($a['last_verified']) ? legalAuditFormatDate($a['last_verified']) : '';
    ?>
    <article class="legal-audit-row" id="<?php echo $h(str_replace('.', '-', $row['page'])); ?>">
      <div class="legal-audit-row__top">
        <h2><a href="<?php echo $h($row['href']); ?>"><?php echo $h($row['title']); ?></a></h2>
        <span class="legal-audit-status legal-audit-status--<?php echo $h($status); ?>"><?php echo $h(legalAuditStatusLabel($status)); ?></span>
      </div>
      <div class="legal-audit-meta">
        <?php if ($date !== ''): ?><span><strong>Τελευταίος έλεγχος:</strong> <?php echo $h($date); ?></span><?php endif; ?>
        <?php if (!empty($a['school_year'])): ?><span><strong>Σχολικό έτος:</strong> <?php echo $h($a['school_year']); ?></span><?php endif; ?>
        <?php if (!empty($a['version'])): ?><span><strong>Έκδοση:</strong> v<?php echo $h($a['version']); ?></span><?php endif; ?>
      </div>
      <?php if (!empty($a['scope'])): ?><p class="legal-audit-scope"><strong>Πεδίο ελέγχου:</strong> <?php echo $h($a['scope']); ?></p><?php endif; ?>
      <?php if (!empty($a['review_trigger'])): ?><p class="legal-audit-trigger"><strong>Νέος έλεγχος όταν:</strong> <?php echo $h($a['review_trigger']); ?></p><?php endif; ?>
    </article>
  <?php endforeach; ?>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
