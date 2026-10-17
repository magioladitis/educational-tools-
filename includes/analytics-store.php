<?php
/**
 * First-party aggregate analytics: no visitor IDs, cookies, IP/User-Agent storage,
 * request paths, query strings or form contents. PHP 7.4 compatible.
 * Counters are stored as PHP-protected daily aggregates in monthly files.
 */
require_once __DIR__ . '/config.php';

function eduAnalyticsCatalog()
{
    static $valid = null;
    if ($valid !== null) return $valid;
    $catalog = require __DIR__ . '/tools-catalog.php';
    $valid = array();
    foreach ($catalog['tools'] as $item) {
        if (empty($item['href'])) continue;
        $path = basename((string) parse_url($item['href'], PHP_URL_PATH));
        if (!preg_match('/^[a-z0-9-]+\.php$/', $path)) continue;
        $valid[$path] = array('title' => (string)$item['title'], 'group' => (string)$item['group']);
    }
    return $valid;
}

function eduAnalyticsDataDir()
{
    $configured = getenv('EDU_ANALYTICS_DATA_DIR');
    // To keep data outside the web root, define an absolute directory here on the server.
    if (is_string($configured) && $configured !== '' && $configured[0] === '/') return rtrim($configured, '/');
    return dirname(__DIR__) . '/data/_private_analytics';
}

function eduAnalyticsEnsureDir()
{
    $dir = eduAnalyticsDataDir();
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) return false;
    if (!is_writable($dir)) return false;
    // A fallback inside the document root is protected on Apache and on PHP-capable servers.
    if (!is_file($dir . '/.htaccess')) @file_put_contents($dir . '/.htaccess', "Require all denied\nDeny from all\n", LOCK_EX);
    if (!is_file($dir . '/index.php')) @file_put_contents($dir . '/index.php', "<?php http_response_code(404); exit; ?>\n", LOCK_EX);
    return true;
}

function eduAnalyticsPrefix()
{
    return "<?php http_response_code(404); exit; ?>\n";
}

function eduAnalyticsRecord($tool, $event, $seconds)
{
    $catalog = eduAnalyticsCatalog();
    if (!isset($catalog[$tool]) || !in_array($event, array('view', 'engaged'), true)) return false;
    if ($event === 'engaged' && (!is_int($seconds) || $seconds < 1 || $seconds > 30)) return false;
    if (!eduAnalyticsEnsureDir()) return false;

    $today = date('Y-m-d');
    $file = eduAnalyticsDataDir() . '/' . substr($today, 0, 7) . '.php';
    $stream = @fopen($file, 'c+');
    if (!$stream) return false;
    if (!flock($stream, LOCK_EX)) { fclose($stream); return false; }
    rewind($stream);
    $raw = stream_get_contents($stream);
    $prefix = eduAnalyticsPrefix();
    $rows = array();
    if (is_string($raw) && strpos($raw, $prefix) === 0) {
        $decoded = json_decode(substr($raw, strlen($prefix)), true);
        if (is_array($decoded)) $rows = $decoded;
    } elseif ($raw !== '') { flock($stream, LOCK_UN); fclose($stream); return false; }
    if (!isset($rows[$today]) || !is_array($rows[$today])) $rows[$today] = array();
    if (!isset($rows[$today][$tool]) || !is_array($rows[$today][$tool])) {
        $rows[$today][$tool] = array('views' => 0, 'engagement_seconds' => 0);
    }
    if ($event === 'view') $rows[$today][$tool]['views'] = min(1000000000, (int)$rows[$today][$tool]['views'] + 1);
    else $rows[$today][$tool]['engagement_seconds'] = min(1000000000, (int)$rows[$today][$tool]['engagement_seconds'] + $seconds);
    $json = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $ok = is_string($json);
    if ($ok) {
        rewind($stream);
        $ok = ftruncate($stream, 0) && fwrite($stream, $prefix . $json) === strlen($prefix . $json);
        fflush($stream);
    }
    flock($stream, LOCK_UN);
    fclose($stream);
    return $ok;
}

function eduAnalyticsReadMonth($month)
{
    if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) return array();
    $path = eduAnalyticsDataDir() . '/' . $month . '.php';
    if (!is_file($path)) return array();
    $stream = @fopen($path, 'rb');
    if (!$stream) return array();
    flock($stream, LOCK_SH);
    $raw = stream_get_contents($stream);
    flock($stream, LOCK_UN);
    fclose($stream);
    $prefix = eduAnalyticsPrefix();
    if (!is_string($raw) || strpos($raw, $prefix) !== 0) return array();
    $rows = json_decode(substr($raw, strlen($prefix)), true);
    return is_array($rows) ? $rows : array();
}

function eduAnalyticsSummary($days)
{
    $days = max(1, min(90, (int)$days));
    $catalog = eduAnalyticsCatalog();
    $totals = array();
    foreach ($catalog as $id => $meta) {
        $totals[$id] = array('title' => $meta['title'], 'group' => $meta['group'], 'views' => 0, 'engagement_seconds' => 0);
    }
    $daily = array();
    $cache = array();
    for ($i = $days - 1; $i >= 0; --$i) {
        $date = date('Y-m-d', strtotime('-' . $i . ' days'));
        $month = substr($date, 0, 7);
        if (!isset($cache[$month])) $cache[$month] = eduAnalyticsReadMonth($month);
        $viewSum = 0; $secondSum = 0;
        $tools = isset($cache[$month][$date]) && is_array($cache[$month][$date]) ? $cache[$month][$date] : array();
        foreach ($tools as $tool => $value) {
            if (!isset($totals[$tool]) || !is_array($value)) continue;
            $views = max(0, (int)(isset($value['views']) ? $value['views'] : 0));
            $seconds = max(0, (int)(isset($value['engagement_seconds']) ? $value['engagement_seconds'] : 0));
            $totals[$tool]['views'] += $views;
            $totals[$tool]['engagement_seconds'] += $seconds;
            $viewSum += $views; $secondSum += $seconds;
        }
        $daily[] = array('date' => $date, 'views' => $viewSum, 'seconds' => $secondSum);
    }
    uasort($totals, function ($a, $b) {
        if ($a['views'] === $b['views']) return strcmp($a['title'], $b['title']);
        return $a['views'] < $b['views'] ? 1 : -1;
    });
    return array('tools' => $totals, 'daily' => $daily, 'days' => $days);
}
