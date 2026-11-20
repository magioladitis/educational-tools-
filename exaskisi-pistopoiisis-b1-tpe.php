<?php
require_once __DIR__ . '/includes/config.php';
$quizAsset = static function ($path) {
    return htmlspecialchars(edu_asset_url($path), ENT_QUOTES, 'UTF-8');
};
?>
<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/includes/head-pwa.php'; ?>
<meta name="description" content="Προετοιμασία για την πιστοποίηση Β1 ΤΠΕ Μαθηματικών με τυχαίες ερωτήσεις και επανάληψη λαθών.">
<title>Εξάσκηση Β1 ΤΠΕ – Μαθηματικά | Εργαλειοθήκη Εκπαιδευτικού</title>
<link rel="stylesheet" href="<?= $quizAsset('assets/common.css') ?>">
<link rel="stylesheet" href="<?= $quizAsset('assets/b1-tpe-quiz.css') ?>">
<script src="<?= $quizAsset('includes/b1-tpe-question-bank.js') ?>" defer></script>
<script src="<?= $quizAsset('includes/b1-tpe-quiz.js') ?>" defer></script>
</head>
<body class="b1-tpe-quiz" data-edu-draft-persist="off">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<header class="mast">
  <div class="frame mast-inner">
    <div class="brand"><span class="brand-mark" aria-hidden="true">∑</span><span>Εξάσκηση Β1 ΤΠΕ<small>Β1.3 · Μαθηματικά</small></span></div>
    <button type="button" class="ghost" id="home" hidden>Ρυθμίσεις κουίζ</button>
  </div>
</header>
<main id="main-content"><div id="app" tabindex="-1"><noscript><p class="frame">Ενεργοποίησε τη JavaScript στον φυλλομετρητή σου για να ξεκινήσεις το κουίζ.</p></noscript></div></main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<template id="setup">
  <section class="hero">
    <div class="frame hero-inner">
      <div><p class="eyebrow">ΠΙΣΤΟΠΟΙΗΣΗ Β1 ΤΠΕ</p><h1>Εξάσκηση στα<br>Μαθηματικά</h1><p class="intro">Τυχαίες ερωτήσεις για τη διδακτική των Μαθηματικών, τα ψηφιακά εργαλεία και την αξιοποίηση των ΤΠΕ στην τάξη.</p></div>
      <div class="hero-graphic" aria-hidden="true"><span class="symbol sigma">∑</span><span class="symbol fx">ƒ(x)</span><span class="symbol angle">∠</span></div>
    </div>
  </section>
  <div class="frame dashboard">
    <form class="card settings" id="settings">
      <h2>Νέα προσπάθεια</h2>
      <fieldset><legend>Τρόπος εξάσκησης</legend>
        <div class="modes">
          <label class="mode"><input name="mode" type="radio" value="practice" checked><span><strong>Εξάσκηση</strong><small>Απάντηση και εξήγηση μετά από κάθε ερώτηση.</small></span></label>
          <label class="mode"><input name="mode" type="radio" value="exam"><span><strong>Δοκιμαστικό τεστ</strong><small>Βαθμολογία και εξηγήσεις στο τέλος.</small></span></label>
        </div>
      </fieldset>
      <div class="filters">
        <label>Θεματική<select id="topic" name="topic"><option value="all">Όλες οι θεματικές</option></select></label>
        <label>Ερωτήσεις<select id="count" name="count"></select></label>
      </div>
      <div class="launch"><button class="primary" type="submit">Έναρξη κουίζ</button><span id="available" class="muted" aria-live="polite"></span></div>
    </form>
    <aside class="card progress-card">
      <h2>Η πρόοδός σου</h2>
      <div class="stats">
        <div><strong id="runs">0</strong><span>Προσπάθειες</span></div>
        <div><strong id="best">–</strong><span>Καλύτερο σκορ</span></div>
      </div>
      <p class="muted last-duration" id="last-duration" hidden></p>
      <div class="retry"><h3>Επανάληψη λαθών</h3><p class="muted" id="retry-info"></p><button type="button" class="secondary" id="retry">Εξάσκηση στα λάθη</button></div>
    </aside>
    <section class="card bank-card"><h2>Τράπεζα ερωτήσεων <span class="pill" id="bank-count"></span></h2><div id="topics" class="topics"></div><p class="muted sources">Ερωτήσεις από τα δείγματα του ΙΤΥΕ «Διόφαντος» (Ιούλιος 2026) και 10 ερωτήσεις από την «Επιμορφωτική Συνάντηση Μαθηματικών για την Προετοιμασία στις Εξετάσεις Πιστοποίησης Β1 ΤΠΕ – 20/11/2026», μαζί με πρόσθετες ασκήσεις. Επιπλέον ερωτήσεις βασίζονται στο Υλικό Αναφοράς Β1.3 (Μάρτιος 2024), στους επίσημους οδηγούς της η-Τάξης και της e-me και στην πολιτική χρήσης της Ιφιγένειας.</p></section>
  </div>
</template>
<script src="<?= $quizAsset('assets/common.js') ?>"></script>
</body>
</html>
