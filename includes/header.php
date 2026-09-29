<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/components/source-card.php';
require_once __DIR__ . '/components/page-hero.php';
/** Common header / navigation for Educational Tools — shared UI. */
?>
<?php
$eduCurrentPage = basename(isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '');
$eduHomeCurrent = $eduCurrentPage === 'ergaleia.php' ? ' aria-current="page"' : '';
$eduDeadlinesCurrent = $eduCurrentPage === 'prothesmies.php' ? ' aria-current="page"' : '';
$eduCurrentTool = null;
$eduToolsCatalog = require __DIR__ . '/tools-catalog.php';
if (isset($eduToolsCatalog['tools']) && is_array($eduToolsCatalog['tools'])) {
    foreach ($eduToolsCatalog['tools'] as $eduToolCandidate) {
        if (!is_array($eduToolCandidate) || empty($eduToolCandidate['href'])) continue;
        $eduToolPath = parse_url((string) $eduToolCandidate['href'], PHP_URL_PATH);
        if (basename((string) $eduToolPath) === $eduCurrentPage) {
            $eduCurrentTool = $eduToolCandidate;
            break;
        }
    }
}
$eduCurrentToolHref = $eduCurrentTool && isset($eduCurrentTool['href']) ? (string) $eduCurrentTool['href'] : '';
$eduCurrentToolTitle = $eduCurrentTool && isset($eduCurrentTool['title']) ? (string) $eduCurrentTool['title'] : '';
$eduHeaderFlags = ENT_QUOTES;
if (defined('ENT_SUBSTITUTE')) $eduHeaderFlags = $eduHeaderFlags | ENT_SUBSTITUTE;
$eduHeaderEscape = function ($value) use ($eduHeaderFlags) {
    return htmlspecialchars((string) $value, $eduHeaderFlags, 'UTF-8');
};
?>
<a class="edu-skip-link" href="#main-content">Μετάβαση στο κύριο περιεχόμενο</a>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/specialty-code-normalization.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/education-core.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/education-print.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script defer src="<?php echo htmlspecialchars(edu_asset_url('assets/app-experience.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<header class="edu-tools-global-header" aria-label="Πλοήγηση Εργαλειοθήκης Εκπαιδευτικού"<?php if ($eduCurrentToolHref !== '') { ?> data-edu-current-tool-href="<?php echo $eduHeaderEscape($eduCurrentToolHref); ?>" data-edu-current-tool-title="<?php echo $eduHeaderEscape($eduCurrentToolTitle); ?>"<?php } ?>>
  <div class="edu-tools-global-header__inner">
    <a class="edu-tools-global-header__back" href="ergaleia.php"<?php echo $eduHomeCurrent; ?> aria-label="Αρχική Εργαλειοθήκης" title="Αρχική Εργαλειοθήκης">
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
      <a class="edu-tools-global-nav__link" href="prothesmies.php"<?php echo $eduDeadlinesCurrent; ?> aria-label="Προθεσμίες" title="Προθεσμίες">
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
        <summary aria-label="Μενού" title="Μενού">
          <span class="edu-tools-global-nav__icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
              <path d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </span>
          <span class="edu-tools-global-nav__label">Μενού</span>
        </summary>
        <div class="edu-tools-global-menu__panel">
          <div class="edu-tools-global-menu__section" aria-label="Ενέργειες">
            <span class="edu-tools-global-menu__heading">Ενέργειες</span>
            <?php if ($eduCurrentToolHref !== '') { ?>
            <button type="button" class="edu-tools-menu-action" data-edu-favorite-toggle aria-pressed="false">
              <span class="edu-tools-menu-action__icon" data-edu-favorite-icon aria-hidden="true">☆</span>
              <span data-edu-favorite-label>Προσθήκη στα αγαπημένα</span>
            </button>
            <?php } ?>
            <?php if ($eduCurrentToolHref !== '' || $eduCurrentPage === 'ergaleia.php' || $eduCurrentPage === 'prothesmies.php') { ?>
            <button type="button" class="edu-tools-menu-action" data-edu-share>
              <span class="edu-tools-menu-action__icon" aria-hidden="true">↗</span>
              <span>Κοινοποίηση</span>
            </button>
            <?php } ?>
            <button type="button" class="edu-tools-menu-action" data-edu-install>
              <span class="edu-tools-menu-action__icon" aria-hidden="true">＋</span>
              <span>Εγκατάσταση στο κινητό</span>
            </button>
          </div>
          <div class="edu-tools-global-menu__section edu-tools-global-menu__section--categories" aria-label="Κατηγορίες">
            <span class="edu-tools-global-menu__heading">Κατηγορίες</span>
            <a href="ergaleia.php?group=asep-anaplirotes#tools-directory">ΑΣΕΠ &amp; Αναπληρωτές</a>
            <a href="ergaleia.php?group=eidiki-agogi#tools-directory">Ειδική Αγωγή</a>
            <a href="ergaleia.php?group=metakiniseis#tools-directory">Αποσπάσεις, Μεταθέσεις &amp; Τοποθετήσεις</a>
            <a href="ergaleia.php?group=ypiresiaka#tools-directory">Σχολική Μονάδα &amp; Υπηρεσιακά</a>
            <a href="ergaleia.php?group=eidikes-domes#tools-directory">Ειδικές Δομές &amp; Διαδικασίες</a>
          </div>
        </div>
      </details>
    </nav>
  </div>
</header>
