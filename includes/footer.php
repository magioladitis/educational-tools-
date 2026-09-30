<?php
/** Shared project footer. */
require_once __DIR__ . '/config.php';
?>
<footer class="edu-tools-global-footer" data-version="<?= htmlspecialchars(EDU_TOOLS_VERSION, ENT_QUOTES, 'UTF-8') ?>">
  <div class="edu-tools-global-footer__inner">
    <span>Σχεδιασμός &amp; υλοποίηση: <?= htmlspecialchars(EDU_TOOLS_AUTHOR, ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars(EDU_TOOLS_AUTHOR_ROLES, ENT_QUOTES, 'UTF-8') ?>), <?= htmlspecialchars(EDU_TOOLS_YEAR, ENT_QUOTES, 'UTF-8') ?></span>
    <span class="edu-tools-global-footer__version" title="Τρέχουσα έκδοση της Εργαλειοθήκης">Έκδοση <?= htmlspecialchars(EDU_TOOLS_VERSION, ENT_QUOTES, 'UTF-8') ?></span>
  </div>
</footer>
