<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/components/source-card.php';
/** Common header / navigation for Educational Tools — shared UI. */
?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/education-core.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/education-print.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<header class="edu-tools-global-header" aria-label="Πλοήγηση Εργαλειοθήκης Εκπαιδευτικού">
  <div class="edu-tools-global-header__inner">
    <a class="edu-tools-global-header__back" href="ergaleia.php" aria-label="Αρχική Εργαλειοθήκης" title="Αρχική Εργαλειοθήκης">
      <span class="edu-tools-global-header__brand-icon" aria-hidden="true">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
          <path d="M3 11.5 12 4l9 7.5"></path>
          <path d="M5.5 10.5V20h13v-9.5"></path>
          <path d="M9.5 20v-6h5v6"></path>
        </svg>
      </span>
      <span class="edu-tools-global-header__back-label">Εργαλειοθήκη Εκπαιδευτικού</span>
    </a>

    <nav class="edu-tools-global-nav" aria-label="Γρήγορη πλοήγηση">
      <a class="edu-tools-global-nav__link" href="ergaleia.php#tools-directory" aria-label="Όλα τα εργαλεία" title="Όλα τα εργαλεία">
        <span class="edu-tools-global-nav__icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
            <rect x="4" y="4" width="6" height="6" rx="1"></rect>
            <rect x="14" y="4" width="6" height="6" rx="1"></rect>
            <rect x="4" y="14" width="6" height="6" rx="1"></rect>
            <rect x="14" y="14" width="6" height="6" rx="1"></rect>
          </svg>
        </span>
        <span class="edu-tools-global-nav__label">Όλα τα εργαλεία</span>
      </a>
      <a class="edu-tools-global-nav__link" href="prothesmies.php" aria-label="Προθεσμίες" title="Προθεσμίες">
        <span class="edu-tools-global-nav__icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
            <path d="M16 3v4M8 3v4M3 10h18"></path>
            <path d="M8 14h3M13 14h3M8 17h3"></path>
          </svg>
        </span>
        <span class="edu-tools-global-nav__label">Προθεσμίες</span>
      </a>
      <details class="edu-tools-global-menu">
        <summary aria-label="Κατηγορίες" title="Κατηγορίες">
          <span class="edu-tools-global-nav__icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
              <path d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </span>
          <span class="edu-tools-global-nav__label">Κατηγορίες</span>
        </summary>
        <div class="edu-tools-global-menu__panel">
          <a href="ergaleia.php?group=asep-anaplirotes#tools-directory">ΑΣΕΠ &amp; Αναπληρωτές</a>
          <a href="ergaleia.php?group=eidiki-agogi#tools-directory">Ειδική Αγωγή</a>
          <a href="ergaleia.php?group=metakiniseis#tools-directory">Αποσπάσεις, Μεταθέσεις &amp; Τοποθετήσεις</a>
          <a href="ergaleia.php?group=ypiresiaka#tools-directory">Σχολική Μονάδα &amp; Υπηρεσιακά</a>
          <a href="ergaleia.php?group=eidikes-domes#tools-directory">Ειδικές Δομές &amp; Διαδικασίες</a>
        </div>
      </details>
    </nav>
  </div>
</header>
