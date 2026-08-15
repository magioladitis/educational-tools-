<?php
/**
 * Common header / navigation for Educational Tools.
 * Edit this file once to update the return link across all tools.
 */
?>
<style>
  .edu-tools-global-header {
    width: 100%;
    margin: 0 0 18px;
    padding: 0;
  }
  .edu-tools-global-header__inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 4px;
  }
  .edu-tools-global-header__back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #1f6feb;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.4;
    text-decoration: none;
  }
  .edu-tools-global-header__back:hover,
  .edu-tools-global-header__back:focus-visible {
    text-decoration: underline;
  }
</style>
<header class="edu-tools-global-header" aria-label="Πλοήγηση Εργαλειοθήκης Εκπαιδευτικού">
  <div class="edu-tools-global-header__inner">
    <a class="edu-tools-global-header__back" href="ergaleia.php">← Επιστροφή στην Εργαλειοθήκη Εκπαιδευτικού</a>
  </div>
</header>
