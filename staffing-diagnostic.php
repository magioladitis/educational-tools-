<?php
/* Temporary diagnostic for ypologismos-didaktikon-anagkon.php.
 * Compatible with legacy PHP 5.x style syntax. Delete after diagnosis.
 */
error_reporting(E_ALL);
@ini_set('display_errors', '1');
@ini_set('display_startup_errors', '1');
@ini_set('html_errors', '0');
@ini_set('log_errors', '1');

function staffing_diag_shutdown()
{
    $e = error_get_last();
    if (!$e) return;
    $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR);
    if (!in_array($e['type'], $fatalTypes, true)) return;
    echo "\n\n=== FATAL ERROR ===\n";
    echo 'Type: ' . $e['type'] . "\n";
    echo 'Message: ' . $e['message'] . "\n";
    echo 'File: ' . $e['file'] . "\n";
    echo 'Line: ' . $e['line'] . "\n";
}
register_shutdown_function('staffing_diag_shutdown');

header('Content-Type: text/plain; charset=UTF-8');
echo "Staffing diagnostic\n";
echo "PHP_VERSION=" . PHP_VERSION . "\n";
if (defined('PHP_VERSION_ID')) echo "PHP_VERSION_ID=" . PHP_VERSION_ID . "\n";
echo "memory_limit=" . ini_get('memory_limit') . "\n";
echo "max_execution_time=" . ini_get('max_execution_time') . "\n";
echo "max_input_vars=" . ini_get('max_input_vars') . "\n";
echo "post_max_size=" . ini_get('post_max_size') . "\n";
echo "\n";

$files = array(
    'includes/config.php',
    'includes/school-profile.php',
    'includes/school-profile-general-education.php',
    'includes/school-profile-workload.php',
    'includes/ethics-class-formation.php',
    'includes/personnel-workload.php',
    'includes/teaching-allocation-engine.php',
    'includes/teaching-workload-aggregation.php'
);

foreach ($files as $rel) {
    $path = __DIR__ . '/' . $rel;
    echo '[CHECK] ' . $rel . ' ... ';
    if (!file_exists($path)) {
        echo "MISSING\n";
        continue;
    }
    if (!is_readable($path)) {
        echo "NOT READABLE\n";
        continue;
    }
    echo "exists, including...\n";
    require_once $path;
    echo '[OK] ' . $rel . "\n";
    @ob_flush(); @flush();
}

$functions = array(
    'schoolProfileBuildDayGymnasium2026',
    'schoolProfileBuildEveningGymnasium2026',
    'schoolProfileBuildDayGel2026',
    'schoolProfileBuildEveningGel2026',
    'schoolProfileBuildGymnasiumWithLyceumClasses2026',
    'personnelWorkloadStructureShortLabel',
    'teachingAllocationEngineProposal'
);

echo "\nCritical functions:\n";
foreach ($functions as $fn) {
    echo $fn . '=' . (function_exists($fn) ? 'OK' : 'MISSING') . "\n";
}

$main = __DIR__ . '/ypologismos-didaktikon-anagkon.php';
echo "\n[CHECK] main page GET render...\n";
if (!file_exists($main)) {
    echo "MAIN FILE MISSING\n";
    exit;
}
$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST = array();
ob_start();
include $main;
$html = ob_get_clean();
echo '[OK] main page rendered. bytes=' . strlen($html) . "\n";
echo "\nDIAGNOSTIC COMPLETE\n";
