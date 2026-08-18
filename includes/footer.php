<?php
/** Shared project footer. */
require_once __DIR__ . '/config.php';
?>
<footer class="edu-tools-global-footer">
  <div class="edu-tools-global-footer__inner">
    <div class="edu-tools-global-footer__meta">
      <span><strong><?= htmlspecialchars(EDU_TOOLS_NAME, ENT_QUOTES, 'UTF-8') ?></strong></span>
      <span>Σχεδιασμός &amp; υλοποίηση: <?= htmlspecialchars(EDU_TOOLS_AUTHOR, ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars(EDU_TOOLS_AUTHOR_ROLES, ENT_QUOTES, 'UTF-8') ?>), <?= htmlspecialchars(EDU_TOOLS_YEAR, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <span class="edu-tools-global-footer__disclaimer">Τα εργαλεία παρέχουν βοηθητικούς υπολογισμούς και οδηγίες· για την επίσημη διαδικασία ισχύουν οι αντίστοιχες προκηρύξεις και ανακοινώσεις.</span>
  </div>
</footer>
