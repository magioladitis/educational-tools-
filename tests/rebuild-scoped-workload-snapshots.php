<?php
/**
 * DEV-ONLY generator for the school-type scoped workload snapshots used by
 * ypologismos-didaktikon-anagkon.php. The canonical sources remain
 * weekly-timetable-data.php and teaching-assignments-data.php.
 */
require_once __DIR__ . '/../includes/weekly-timetable-data.php';
require_once __DIR__ . '/../includes/teaching-assignments-data.php';

$schools = array('gymnasio', 'gel', 'esperino_gymnasio', 'esperino_gel');
$outDir = __DIR__ . '/../includes/scoped-workload';
if (!is_dir($outDir) && !mkdir($outDir, 0775, true)) {
    fwrite(STDERR, "Cannot create scoped workload directory\n");
    exit(1);
}

$weeklyAll = weeklyTimetableRows();
$assignmentsAll = teachingAssignmentsData();

foreach ($schools as $school) {
    $weekly = array_values(array_filter($weeklyAll, function ($row) use ($school) {
        return isset($row['school']) && $row['school'] === $school;
    }));
    $assignments = array_values(array_filter($assignmentsAll, function ($row) use ($school) {
        return isset($row['school']) && $row['school'] === $school;
    }));

    $header = "<?php\n/** AUTO-GENERATED DEV SNAPSHOT. Canonical data lives in the main regulatory datasets. */\nreturn ";
    file_put_contents($outDir . '/weekly-' . $school . '.php', $header . var_export($weekly, true) . ";\n");
    file_put_contents($outDir . '/assignments-' . $school . '.php', $header . var_export($assignments, true) . ";\n");
    echo $school . ': weekly=' . count($weekly) . ', assignments=' . count($assignments) . "\n";
}
