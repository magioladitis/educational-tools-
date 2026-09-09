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
    define('EDU_TOOLS_VERSION', '3.20.72');
}
if (!function_exists('edu_asset_url')) {
    function edu_asset_url($path)
    {
        $path = (string) $path;
        $version = EDU_TOOLS_VERSION;

        // Keep the release version, but also include the actual local asset
        // modification time so changed JS/CSS can never reuse a stale URL.
        $urlPath = parse_url($path, PHP_URL_PATH);
        if (is_string($urlPath) && $urlPath !== '' && !preg_match('~^(?:https?:)?//~i', $urlPath)) {
            $relativePath = ltrim(str_replace('\\', '/', $urlPath), '/');
            if ($relativePath !== '' && strpos($relativePath, '../') === false) {
                $localPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
                if (is_file($localPath)) {
                    $mtime = @filemtime($localPath);
                    if ($mtime !== false) $version .= '-' . (string) $mtime;
                }
            }
        }

        $sep = (strpos($path, '?') === false) ? '?' : '&';
        return $path . $sep . 'v=' . rawurlencode($version);
    }
}
