<?php
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
    function sourceCardStart($config = array())
    {
        $config = is_array($config) ? $config : array();
        $id = isset($config['title_id']) && $config['title_id'] !== '' ? (string) $config['title_id'] : 'sourcesTitle';
        $title = isset($config['title']) && $config['title'] !== '' ? (string) $config['title'] : 'Πηγές / Νομική βάση';
        echo '<section class="edu-source-card" aria-labelledby="' . sourceCardEscape($id) . '">';
        echo '<h2 id="' . sourceCardEscape($id) . '">' . sourceCardEscape($title) . '</h2>';
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
    function sourceCardEnd()
    {
        echo '</section>';
    }
}
