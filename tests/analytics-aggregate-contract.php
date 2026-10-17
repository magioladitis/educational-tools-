<?php
/** Run with: php tests/analytics-aggregate-contract.php */
$tmp = sys_get_temp_dir() . '/edu-analytics-test-' . bin2hex(random_bytes(5));
putenv('EDU_ANALYTICS_DATA_DIR=' . $tmp);
require dirname(__DIR__) . '/includes/analytics-store.php';
function checkAnalytics($pass, $message) { if (!$pass) { fwrite(STDERR, 'FAIL ' . $message . "\n"); exit(1); } }
$id = 'ypologismos-morion-metathesis.php';
checkAnalytics(eduAnalyticsRecord($id, 'view', 0), 'valid view');
checkAnalytics(eduAnalyticsRecord($id, 'view', 0), 'second view');
checkAnalytics(eduAnalyticsRecord($id, 'engaged', 25), 'valid time');
checkAnalytics(!eduAnalyticsRecord($id, 'engaged', 55), 'oversized time blocked');
checkAnalytics(!eduAnalyticsRecord($id, 'engaged', -1), 'negative time blocked');
checkAnalytics(!eduAnalyticsRecord('unknown.php', 'view', 0), 'unknown pages blocked');
$stats = eduAnalyticsSummary(7);
checkAnalytics($stats['tools'][$id]['views'] === 2, 'views aggregation');
checkAnalytics($stats['tools'][$id]['engagement_seconds'] === 25, 'time aggregation');
$raw = file_get_contents($tmp . '/' . date('Y-m') . '.php');
checkAnalytics(strpos($raw, '<?php http_response_code(404); exit; ?>') === 0, 'protected file prefix');
checkAnalytics(strpos($raw, 'IP') === false && strpos($raw, 'User-Agent') === false, 'no request metadata recorded');
checkAnalytics(is_file($tmp . '/.htaccess'), 'web server deny file');
foreach (glob($tmp . '/*') as $f) unlink($f);
@unlink($tmp . '/.htaccess');
@rmdir($tmp);
echo "OK: aggregates, bounds, whitelist and storage guard\n";
