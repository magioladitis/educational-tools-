<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/components/source-card.php';
/** Common header / navigation for Educational Tools — UI v3.20.1. */
?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/education-core.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<header class="edu-tools-global-header" aria-label="Πλοήγηση Εργαλειοθήκης Εκπαιδευτικού">
  <div class="edu-tools-global-header__inner">
    <a class="edu-tools-global-header__back" href="ergaleia.php">
      <span class="edu-tools-global-header__brand-icon" aria-hidden="true">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
          <path d="M3 11.5 12 4l9 7.5"></path>
          <path d="M5.5 10.5V20h13v-9.5"></path>
          <path d="M9.5 20v-6h5v6"></path>
        </svg>
      </span>
      <span class="edu-tools-global-header__back-label">Επιστροφή στην Εργαλειοθήκη Εκπαιδευτικού</span>
    </a>
  </div>
</header>
