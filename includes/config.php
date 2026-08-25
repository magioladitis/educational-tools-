<?php
/**
 * Shared project configuration.
 * Keep site-wide labels and metadata in one place.
 */
if (!defined('EDU_TOOLS_NAME')) {
    define('EDU_TOOLS_NAME', 'Εργαλειοθήκη Εκπαιδευτικού');
}
if (!defined('EDU_TOOLS_HOME')) {
    define('EDU_TOOLS_HOME', 'ergaleia.php');
}
if (!defined('EDU_TOOLS_AUTHOR')) {
    define('EDU_TOOLS_AUTHOR', 'Μάριος Μαγιολαδίτης');
}
if (!defined('EDU_TOOLS_AUTHOR_ROLES')) {
    define('EDU_TOOLS_AUTHOR_ROLES', 'ΠΕ03, ΠΕ86');
}
if (!defined('EDU_TOOLS_YEAR')) {
    define('EDU_TOOLS_YEAR', '2026');
}

if (!defined('EDU_TOOLS_VERSION')) {
    define('EDU_TOOLS_VERSION', '3.20.55');
}
if (!function_exists('edu_asset_url')) {
    function edu_asset_url($path)
    {
        $path = (string) $path;
        $sep = (strpos($path, '?') === false) ? '?' : '&';
        return $path . $sep . 'v=' . rawurlencode(EDU_TOOLS_VERSION);
    }
}
