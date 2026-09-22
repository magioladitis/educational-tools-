<?php
require_once __DIR__ . '/includes/vacancies-auth.php';
require_once __DIR__ . '/includes/vacancies-model.php';
require_once __DIR__ . '/includes/vacancies-xlsx.php';

vacanciesSessionStart();
if (!vacanciesIsAdmin() || !vacanciesDbReady()) {
    header('Location: kena-sxoleion-login.php');
    exit;
}
if (vacanciesActorNeedsPasswordChange()) {
    header('Location: kena-sxoleion-password.php');
    exit;
}

$roundId = isset($_GET['round']) ? (int) $_GET['round'] : 0;
$scope = isset($_GET['scope']) && $_GET['scope'] === 'special' ? 'special' : 'general';
$data = vacanciesExportMatrixData($roundId, $scope);
if (!$data) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Ο γύρος καταγραφής δεν βρέθηκε.';
    exit;
}

list($ok, $payload) = vacanciesXlsxBuild($data);
if (!$ok) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $payload;
    exit;
}

$date = preg_replace('/[^0-9-]/', '', (string) $data['round']['reference_date']);
$scopePart = $scope === 'special' ? '-eidiki' : '';
$filename = 'kena-'.$date.$scopePart.'.xlsx';
while (ob_get_level() > 0) @ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Length: '.strlen($payload));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
echo $payload;
exit;
