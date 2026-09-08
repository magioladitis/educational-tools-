<?php
require_once __DIR__ . '/../includes/teaching-workload-model.php';

function scopedFail($message) { fwrite(STDERR, "FAIL: " . $message . "\n"); exit(1); }
function scopedHash($value) { return hash('sha256', serialize($value)); }

$schools = array('gymnasio', 'gel', 'esperino_gymnasio', 'esperino_gel');
$fullWeekly = weeklyTimetableRows();
$fullAssignments = teachingAssignmentsData();
$fullModel = teachingWorkloadModel();
$checks = 0;

foreach ($schools as $school) {
    $expectedWeekly = array_values(array_filter($fullWeekly, function ($row) use ($school) { return $row['school'] === $school; }));
    $expectedAssignments = array_values(array_filter($fullAssignments, function ($row) use ($school) { return $row['school'] === $school; }));
    $expectedModel = array_values(array_filter($fullModel, function ($row) use ($school) { return $row['school'] === $school; }));

    $actualWeekly = weeklyTimetableRowsForSchools(array($school));
    $actualAssignments = teachingAssignmentsDataForSchools(array($school));
    $actualModel = teachingWorkloadModelForSchools(array($school));

    if (scopedHash($expectedWeekly) !== scopedHash($actualWeekly)) scopedFail($school . ' weekly snapshot differs from canonical data');
    $checks++;
    if (scopedHash($expectedAssignments) !== scopedHash($actualAssignments)) scopedFail($school . ' assignment snapshot differs from canonical data');
    $checks++;
    if (scopedHash($expectedModel) !== scopedHash($actualModel)) scopedFail($school . ' workload model differs from canonical model');
    $checks++;
}

$comboExpected = array_values(array_filter($fullModel, function ($row) { return $row['school'] === 'gymnasio' || $row['school'] === 'gel'; }));
$comboActual = teachingWorkloadModelForSchools(array('gymnasio', 'gel'));
if (scopedHash($comboExpected) !== scopedHash($comboActual)) scopedFail('gymnasio+gel composite differs from canonical model');
$checks++;

$source = file_get_contents(__DIR__ . '/../ypologismos-didaktikon-anagkon.php');
if (strpos($source, 'teachingWorkloadModelForProfile($profile)') === false) scopedFail('staffing page does not use scoped model');
$checks++;

if ($checks !== 14) scopedFail('unexpected check count ' . $checks);
echo "PASS 14/14 scoped workload checks\n";
