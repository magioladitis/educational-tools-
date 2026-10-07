<?php
require_once dirname(__DIR__) . '/legal-audit.php';
/**
 * Shared presentation helpers for “Πηγές / Νομική βάση”.
 * Presentation only: page-specific legal/source content stays in each tool.
 * Conservative PHP syntax is intentional for compatibility with older runtimes.
 */

if (!function_exists('sourceCardEscape')) {
    function sourceCardEscape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sourceCardStart')) {
    /**
     * Default source card entry point.
     *
     * All legacy callers now inherit the responsive disclosure pattern:
     * rendered closed by default on every screen, so legal/source material stays
     * available without dominating the tool. Print still expands disclosures.
     * Page code does not need to opt in individually.
     */
    function sourceCardStart($config = array())
    {
        $config = is_array($config) ? $config : array();
        if (!isset($config['mobile_collapsed'])) $config['mobile_collapsed'] = true;
        if (!isset($config['desktop_expanded'])) $config['desktop_expanded'] = false;
        /* Render closed by default. Explicit callers may opt into desktop expansion. */
        if (!isset($config['open'])) $config['open'] = false;
        sourceCardDisclosureStart($config);
    }
}


if (!function_exists('sourceCardDisclosureStart')) {
    function sourceCardDisclosureStart($config = array())
    {
        $config = is_array($config) ? $config : array();
        $id = isset($config['title_id']) && $config['title_id'] !== '' ? (string) $config['title_id'] : 'sourcesTitle';
        $title = isset($config['title']) && $config['title'] !== '' ? (string) $config['title'] : 'Πηγές / Νομική βάση';
        $mobileCollapsed = !isset($config['mobile_collapsed']) || (bool) $config['mobile_collapsed'];
        $desktopExpanded = isset($config['desktop_expanded']) && (bool) $config['desktop_expanded'];
        $open = isset($config['open']) && (bool) $config['open'];

        echo '<section class="edu-source-card edu-source-card--responsive" aria-labelledby="' . sourceCardEscape($id) . '"';
        if ($mobileCollapsed) echo ' data-mobile-collapsed="true"';
        if ($desktopExpanded) echo ' data-desktop-expanded="true"';
        echo '>';
        echo '<details class="edu-disclosure edu-source-card__details"';
        if ($open) echo ' open';
        echo '>';
        echo '<summary class="edu-disclosure__summary">';
        echo '<span id="' . sourceCardEscape($id) . '">' . sourceCardEscape($title) . '</span>';
        echo '<span class="edu-disclosure__chevron" aria-hidden="true">›</span>';
        echo '</summary>';
        echo '<div class="edu-disclosure__body edu-source-card__body">';

        $audit = legalAuditForPage(legalAuditCurrentPage());
        if (legalAuditIsPubliclyVisible($audit)) {
            $status = isset($audit['status']) ? (string) $audit['status'] : 'pending';
            $date = !empty($audit['last_verified']) ? legalAuditFormatDate($audit['last_verified']) : '';
            $label = $status === 'verified' ? 'Έλεγχος πηγών' : ($status === 'review_due' ? 'Επανέλεγχος πηγών' : 'Στοχευμένος έλεγχος');
            echo '<div class="edu-source-audit edu-source-audit--' . sourceCardEscape($status) . '">';
            echo '<span class="edu-source-audit__label">' . sourceCardEscape($label) . '</span>';
            if ($date !== '') echo '<strong class="edu-source-audit__date">' . sourceCardEscape($date) . '</strong>';
            if (!empty($audit['school_year'])) echo '<span class="edu-source-audit__scope">σχ. έτος ' . sourceCardEscape($audit['school_year']) . '</span>';
            if (!empty($audit['auto_review_due'])) echo '<span class="edu-source-audit__scope">αυτόματη υπενθύμιση</span>';
            echo '<a class="edu-source-audit__details" href="nomiki-epikairotita.php#' . sourceCardEscape(str_replace('.', '-', $audit['page'])) . '">λεπτομέρειες</a>';
            echo '</div>';
        }
    }
}

if (!function_exists('sourceCardDisclosureEnd')) {
    function sourceCardDisclosureEnd()
    {
        echo '</div></details></section>';
    }
}

if (!function_exists('sourceCardLinksStart')) {
    function sourceCardLinksStart()
    {
        echo '<div class="source-links">';
    }
}

if (!function_exists('sourceCardLink')) {
    function sourceCardLink($href, $label, $config = array())
    {
        $config = is_array($config) ? $config : array();
        $target = isset($config['target']) ? (string) $config['target'] : '_blank';
        $rel = isset($config['rel']) ? (string) $config['rel'] : 'noopener noreferrer';
        echo '<a href="' . sourceCardEscape($href) . '"';
        if ($target !== '') echo ' target="' . sourceCardEscape($target) . '"';
        if ($rel !== '') echo ' rel="' . sourceCardEscape($rel) . '"';
        echo '>' . sourceCardEscape($label) . '</a>';
    }
}

if (!function_exists('sourceCardLinksEnd')) {
    function sourceCardLinksEnd()
    {
        echo '</div>';
    }
}

if (!function_exists('sourceCardDisclaimerStart')) {
    function sourceCardDisclaimerStart()
    {
        echo '<p class="source-disclaimer">';
    }
}

if (!function_exists('sourceCardDisclaimerEnd')) {
    function sourceCardDisclaimerEnd()
    {
        echo '</p>';
    }
}

if (!function_exists('sourceCardEnd')) {
    /** Close the default responsive source card opened by sourceCardStart(). */
    function sourceCardEnd()
    {
        sourceCardDisclosureEnd();
    }
}
