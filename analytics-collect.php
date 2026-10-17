<?php
/** Receives ONLY bounded active-time increments; pageviews are counted on PHP page load. */
require_once __DIR__ . '/includes/analytics-store.php';
header('Cache-Control: no-store, max-age=0');
header('X-Content-Type-Options: nosniff');
header('Content-Type: application/json; charset=utf-8');
if (!empty($_COOKIE['edu_analytics_off']) || (!empty($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] === '1') || (!empty($_SERVER['HTTP_SEC_GPC']) && $_SERVER['HTTP_SEC_GPC'] === '1')) { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); echo '{"ok":false}'; exit; }
if (isset($_SERVER['HTTP_SEC_FETCH_SITE']) && !in_array($_SERVER['HTTP_SEC_FETCH_SITE'], array('same-origin', 'none'), true)) {
    http_response_code(403); echo '{"ok":false}'; exit;
}
if (!empty($_SERVER['HTTP_ORIGIN'])) {
    $originHost = parse_url((string)$_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
    $currentHost = strtolower((string)(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
    $currentHost = strtolower(explode(':', $currentHost)[0]);
    if (!is_string($originHost) || strtolower($originHost) !== $currentHost) {
        http_response_code(403); echo '{"ok":false}'; exit;
    }
}
if (empty($_SERVER['CONTENT_TYPE']) || stripos((string)$_SERVER['CONTENT_TYPE'], 'application/json') !== 0) {
    http_response_code(415); echo '{"ok":false}'; exit;
}
$raw = file_get_contents('php://input', false, null, 0, 300);
$payload = json_decode((string)$raw, true);
if (!is_array($payload) || count($payload) !== 2 || !isset($payload['tool'], $payload['seconds'])) {
    http_response_code(400); echo '{"ok":false}'; exit;
}
$tool = $payload['tool']; $seconds = $payload['seconds'];
if (!is_string($tool) || !is_int($seconds) || !eduAnalyticsRecord($tool, 'engaged', $seconds)) {
    http_response_code(400); echo '{"ok":false}'; exit;
}
echo '{"ok":true}';
