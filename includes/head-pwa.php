<?php
/**
 * Shared browser/PWA metadata for all public PHP pages.
 * Keep manifest discovery, favicons and service-worker registration here so
 * individual tools do not duplicate or drift from the global PWA setup.
 */
require_once __DIR__ . '/config.php';

$eduHeadPwaFlags = ENT_QUOTES;
if (defined('ENT_SUBSTITUTE')) {
    $eduHeadPwaFlags |= ENT_SUBSTITUTE;
}
$eduHeadPwaH = static function ($value) use ($eduHeadPwaFlags) {
    return htmlspecialchars((string) $value, $eduHeadPwaFlags, 'UTF-8');
};
?>
<link rel="manifest" href="manifest.webmanifest">
<meta name="theme-color" content="#1f6feb">
<link rel="icon" href="<?php echo $eduHeadPwaH(edu_asset_url('favicon.ico')); ?>" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $eduHeadPwaH(edu_asset_url('assets/icons/favicon-32.png')); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $eduHeadPwaH(edu_asset_url('assets/icons/favicon-16.png')); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $eduHeadPwaH(edu_asset_url('assets/icons/apple-touch-icon.png')); ?>">
<script defer src="<?php echo $eduHeadPwaH(edu_asset_url('assets/pwa.js')); ?>"></script>
