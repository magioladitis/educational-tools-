<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/service-timeline-xlsx.php';
$data = require __DIR__ . '/includes/service-timeline-data.php';

list($ok, $payload) = serviceTimelineXlsxBuild($data);
if (!$ok) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $payload;
    exit;
}

$date = isset($data['updated_at']) ? (string)$data['updated_at'] : date('d/m/Y');
$parts = explode('/', $date);
$datePart = count($parts) === 3 ? ($parts[2].'-'.$parts[1].'-'.$parts[0]) : date('Y-m-d');
$filename = 'xronodiagramma-ypiresiakon-metavolon-'.$datePart.'.xlsx';
while (ob_get_level() > 0) @ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Length: '.strlen($payload));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
echo $payload;
exit;
