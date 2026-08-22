<?php
/** Shared navigation header. */
require_once __DIR__ . '/config.php';
?>
<header class="edu-tools-global-header" aria-label="Πλοήγηση Εργαλειοθήκης Εκπαιδευτικού">
  <div class="edu-tools-global-header__inner">
    <a class="edu-tools-global-header__brand" href="<?= htmlspecialchars(EDU_TOOLS_HOME, ENT_QUOTES, 'UTF-8') ?>">
      <span class="edu-tools-global-header__brand-icon" aria-hidden="true">🧰</span>
      <span class="edu-tools-global-header__brand-text"><?= htmlspecialchars(EDU_TOOLS_NAME, ENT_QUOTES, 'UTF-8') ?></span>
    </a>
    <a class="edu-tools-global-header__back" href="<?= htmlspecialchars(EDU_TOOLS_HOME, ENT_QUOTES, 'UTF-8') ?>">
      <span aria-hidden="true">←</span>
      <span class="edu-tools-global-header__back-label">Όλα τα εργαλεία</span>
    </a>
  </div>
</header>
