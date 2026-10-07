<?php
/**
 * Admin-only decision-support engine for cross-school vacancy coverage.
 *
 * Sources:
 * - myschool statistic 4.8: teacher placements / remaining compulsory hours.
 * - myschool statistic 5.1: subject deficits / A-B teaching assignments.
 * - vacancy_schools + latest submitted vacancy round: destination schools and
 *   specialty-level vacancy hours.
 *
 * The engine only proposes coverage. It never persists placements or changes
 * vacancy submissions.
 */
require_once __DIR__ . '/teacher-specialties.php';

function vacanciesCoverageCleanExcelValue($value)
{
    $value = trim((string) $value);
    if (preg_match('/^="(.*)"$/u', $value, $m)) return trim($m[1]);
    return $value;
}

function vacanciesCoverageNumber($value)
{
    $value = vacanciesCoverageCleanExcelValue($value);
    $value = str_replace(array("\xc2\xa0", ' '), '', $value);
    $value = str_replace(',', '.', $value);
    if ($value === '' || !is_numeric($value)) return 0.0;
    return (float) $value;
}

function vacanciesCoverageNormalizeText($value)
{
    $value = trim((string) $value);
    $value = preg_replace('/\s+/u', ' ', $value);
    return $value;
}

function vacanciesCoverageSchoolCode($value)
{
    $value = vacanciesCoverageCleanExcelValue($value);
    return preg_replace('/[^0-9A-Za-z_-]/u', '', $value);
}

function vacanciesCoverageCanonicalSpecialty($code)
{
    $code = strtoupper(trim((string) $code));
    $code = preg_replace('/\s+/u', '', $code);
    if ($code === '') return '';

    // myschool EAE suffixes are not a different base specialty for assignment
    // matching. ΠΕ79.01.50 -> ΠΕ79.01, ΤΕ16.00.50 -> ΤΕ16.
    $code = preg_replace('/\.50$/u', '', $code);
    $code = preg_replace('/\.00$/u', '', $code);
    $canonical = teacherSpecialtyCanonicalCode($code);
    return $canonical !== '' ? $canonical : $code;
}

function vacanciesCoverageCodeMatches($entry, $code)
{
    $entry = vacanciesCoverageCanonicalSpecialty($entry);
    $code = vacanciesCoverageCanonicalSpecialty($code);
    if ($entry === '' || $code === '') return false;
    if ($entry === $code) return true;
    if (preg_match('/^ΠΕ\d{2}$/u', $entry) && strpos($code, $entry . '.') === 0) return true;
    if (preg_match('/^ΤΕ\d{2}$/u', $entry) && strpos($code, $entry . '.') === 0) return true;
    if ($entry === 'ΤΕ' && strpos($code, 'ΤΕ') === 0) return true;
    return false;
}

function vacanciesCoverageSpecialtyList($value)
{
    $parts = preg_split('/[,;]+/u', (string) $value);
    $out = array();
    foreach ($parts as $part) {
        $code = vacanciesCoverageCanonicalSpecialty($part);
        if ($code !== '') $out[$code] = true;
    }
    return array_keys($out);
}

function vacanciesCoverageToUtf8($bytes)
{
    if ($bytes === '') return '';
    if (function_exists('mb_check_encoding') && mb_check_encoding($bytes, 'UTF-8')) {
        return preg_replace('/^\xEF\xBB\xBF/', '', $bytes);
    }
    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($bytes, 'UTF-8', 'Windows-1253,ISO-8859-7,UTF-8');
    }
    if (function_exists('iconv')) {
        $converted = @iconv('Windows-1253', 'UTF-8//IGNORE', $bytes);
        if ($converted !== false) return $converted;
    }
    return $bytes;
}

function vacanciesCoverageReadUpload($file)
{
    if (!is_array($file) || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return array(false, 'Δεν βρέθηκε έγκυρο αρχείο μεταφόρτωσης.', '');
    }
    if (!empty($file['error'])) return array(false, 'Η μεταφόρτωση απέτυχε (κωδικός ' . (int) $file['error'] . ').', '');
    if (!empty($file['size']) && (int) $file['size'] > 12 * 1024 * 1024) {
        return array(false, 'Το αρχείο είναι μεγαλύτερο από το επιτρεπτό όριο των 12 MB.', '');
    }

    $name = isset($file['name']) ? (string) $file['name'] : '';
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if ($ext === 'csv') {
        $bytes = @file_get_contents($file['tmp_name']);
        return $bytes === false ? array(false, 'Δεν ήταν δυνατή η ανάγνωση του CSV.', '') : array(true, '', vacanciesCoverageToUtf8($bytes));
    }
    if ($ext === 'zip') {
        if (!class_exists('ZipArchive')) return array(false, 'Ο server δεν διαθέτει ZipArchive. Ανέβασε το CSV χωρίς συμπίεση.', '');
        $zip = new ZipArchive();
        if ($zip->open($file['tmp_name']) !== true) return array(false, 'Το ZIP δεν άνοιξε.', '');
        $csvIndex = -1;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            if (strtolower(pathinfo($entryName, PATHINFO_EXTENSION)) === 'csv') { $csvIndex = $i; break; }
        }
        if ($csvIndex < 0) { $zip->close(); return array(false, 'Το ZIP δεν περιέχει CSV.', ''); }
        $bytes = $zip->getFromIndex($csvIndex);
        $zip->close();
        if ($bytes === false) return array(false, 'Δεν ήταν δυνατή η ανάγνωση του CSV μέσα στο ZIP.', '');
        return array(true, '', vacanciesCoverageToUtf8($bytes));
    }
    return array(false, 'Υποστηρίζονται μόνο αρχεία .csv ή .zip από το myschool.', '');
}

function vacanciesCoverageParseCsvText($text)
{
    $handle = fopen('php://temp', 'r+');
    fwrite($handle, (string) $text);
    rewind($handle);
    $header = fgetcsv($handle, 0, ';');
    if (!$header || !is_array($header)) { fclose($handle); return array(array(), array()); }
    $header = array_map('vacanciesCoverageNormalizeText', $header);
    $rows = array();
    while (($values = fgetcsv($handle, 0, ';')) !== false) {
        if (!$values || count(array_filter($values, function($v){ return trim((string)$v) !== ''; })) === 0) continue;
        $row = array();
        foreach ($header as $i => $key) $row[$key] = isset($values[$i]) ? $values[$i] : '';
        $rows[] = $row;
    }
    fclose($handle);
    return array($header, $rows);
}

function vacanciesCoverageHeaderHas($header, $name)
{
    return in_array($name, $header, true);
}

function vacanciesCoverageParseStat48Text($text)
{
    list($header, $rows) = vacanciesCoverageParseCsvText($text);
    $required = array('Α.Μ.', 'Επώνυμο', 'Όνομα', 'Κωδικός Κύριας Ειδικότητας', 'Κωδικός Σχολείου', 'Ονομασία Σχολείου', 'Υπόλοιπο Υποχρεωτικού Διδακτικού Ωραρίου');
    foreach ($required as $column) {
        if (!vacanciesCoverageHeaderHas($header, $column)) return array(false, 'Το 4.8 δεν περιέχει τη στήλη «' . $column . '».', array());
    }

    $teachers = array();
    $sourceRows = 0;
    foreach ($rows as $row) {
        $am = vacanciesCoverageCleanExcelValue(isset($row['Α.Μ.']) ? $row['Α.Μ.'] : '');
        if ($am === '') continue;
        $sourceRows++;
        if (!isset($teachers[$am])) {
            $teachers[$am] = array(
                'am'=>$am,
                'last_name'=>vacanciesCoverageNormalizeText(isset($row['Επώνυμο']) ? $row['Επώνυμο'] : ''),
                'first_name'=>vacanciesCoverageNormalizeText(isset($row['Όνομα']) ? $row['Όνομα'] : ''),
                'primary_code'=>vacanciesCoverageCanonicalSpecialty(isset($row['Κωδικός Κύριας Ειδικότητας']) ? $row['Κωδικός Κύριας Ειδικότητας'] : ''),
                'secondary_code'=>vacanciesCoverageCanonicalSpecialty(isset($row['Κωδικός 2ης Ειδικότητας']) ? $row['Κωδικός 2ης Ειδικότητας'] : ''),
                'remaining_hours'=>0.0,
                'placements'=>array(),
            );
        }
        $remaining = vacanciesCoverageNumber(isset($row['Υπόλοιπο Υποχρεωτικού Διδακτικού Ωραρίου']) ? $row['Υπόλοιπο Υποχρεωτικού Διδακτικού Ωραρίου'] : 0);
        $teachers[$am]['remaining_hours'] += $remaining;
        $schoolCode = vacanciesCoverageSchoolCode(isset($row['Κωδικός Σχολείου']) ? $row['Κωδικός Σχολείου'] : '');
        $schoolName = vacanciesCoverageNormalizeText(isset($row['Ονομασία Σχολείου']) ? $row['Ονομασία Σχολείου'] : '');
        $placementKey = $schoolCode !== '' ? $schoolCode : $schoolName;
        if ($placementKey !== '') {
            if (!isset($teachers[$am]['placements'][$placementKey])) {
                $teachers[$am]['placements'][$placementKey] = array('school_code'=>$schoolCode,'school_name'=>$schoolName,'remaining_component'=>0.0);
            }
            $teachers[$am]['placements'][$placementKey]['remaining_component'] += $remaining;
        }
        if ($teachers[$am]['secondary_code'] === '' && isset($row['Κωδικός 2ης Ειδικότητας'])) {
            $teachers[$am]['secondary_code'] = vacanciesCoverageCanonicalSpecialty($row['Κωδικός 2ης Ειδικότητας']);
        }
    }

    $available = array();
    $hours = 0.0;
    foreach ($teachers as $am => $teacher) {
        $teacher['remaining_hours'] = round($teacher['remaining_hours'], 2);
        $teacher['placements'] = array_values($teacher['placements']);
        if ($teacher['remaining_hours'] > 0) {
            $available[$am] = $teacher;
            $hours += $teacher['remaining_hours'];
        }
    }
    uasort($available, function($a,$b){
        if ($a['remaining_hours'] == $b['remaining_hours']) return strcmp($a['last_name'].$a['first_name'], $b['last_name'].$b['first_name']);
        return $a['remaining_hours'] < $b['remaining_hours'] ? 1 : -1;
    });
    return array(true, '', array(
        'teachers'=>$available,
        'teacher_count'=>count($available),
        'remaining_hours'=>round($hours,2),
        'source_rows'=>$sourceRows,
        'all_teacher_count'=>count($teachers),
    ));
}

function vacanciesCoverageFindHeader($header, $candidates)
{
    foreach ($candidates as $candidate) if (in_array($candidate, $header, true)) return $candidate;
    return '';
}

function vacanciesCoverageParseStat51Text($text)
{
    list($header, $rows) = vacanciesCoverageParseCsvText($text);
    $required = array('Τάξη','Μάθημα','Ειδικότητες που έχουν το μάθημα ως Α ανάθεση','Ειδικότητες που έχουν το μάθημα ως Β ανάθεση');
    foreach ($required as $column) {
        if (!vacanciesCoverageHeaderHas($header, $column)) return array(false, 'Το 5.1 δεν περιέχει τη στήλη «' . $column . '».', array());
    }
    $unitDeficitColumn = vacanciesCoverageFindHeader($header, array('Εκτίμηση Κενών από μονάδα','Εκτίμηση Κενών από Μονάδα'));
    $myschoolDeficitColumn = vacanciesCoverageFindHeader($header, array('Εκτίμηση Κενών από myschool','Εκτίμηση Κενών από Myschool'));
    if ($unitDeficitColumn === '' && $myschoolDeficitColumn === '') return array(false, 'Το 5.1 δεν περιέχει στήλη εκτίμησης κενών.', array());

    $schoolCodeColumn = vacanciesCoverageFindHeader($header, array('Κωδικός Σχολείου','Κωδικός Σχολικής Μονάδας','Κωδικός Μονάδας'));
    $schoolNameColumn = vacanciesCoverageFindHeader($header, array('Ονομασία Σχολείου','Ονομασία Σχολικής Μονάδας','Σχολική Μονάδα'));
    $schoolScoped = ($schoolCodeColumn !== '' || $schoolNameColumn !== '');

    $allRows = array();
    $unitDeficits = array();
    $myschoolDeficits = array();
    $unitTotal = 0.0;
    $myschoolTotal = 0.0;
    foreach ($rows as $i => $row) {
        $unitValue = $unitDeficitColumn !== '' ? vacanciesCoverageNumber(isset($row[$unitDeficitColumn]) ? $row[$unitDeficitColumn] : 0) : 0;
        $myschoolValue = $myschoolDeficitColumn !== '' ? vacanciesCoverageNumber(isset($row[$myschoolDeficitColumn]) ? $row[$myschoolDeficitColumn] : 0) : 0;
        $entry = array(
            'row_no'=>$i + 2,
            'grade'=>vacanciesCoverageNormalizeText(isset($row['Τάξη']) ? $row['Τάξη'] : ''),
            'sector'=>vacanciesCoverageNormalizeText(isset($row['Τομέας Σπουδών']) ? $row['Τομέας Σπουδών'] : ''),
            'subject'=>vacanciesCoverageNormalizeText(isset($row['Μάθημα']) ? $row['Μάθημα'] : ''),
            'unit_hours'=>round($unitValue,2),
            'myschool_hours'=>round($myschoolValue,2),
            'A'=>vacanciesCoverageSpecialtyList(isset($row['Ειδικότητες που έχουν το μάθημα ως Α ανάθεση']) ? $row['Ειδικότητες που έχουν το μάθημα ως Α ανάθεση'] : ''),
            'B'=>vacanciesCoverageSpecialtyList(isset($row['Ειδικότητες που έχουν το μάθημα ως Β ανάθεση']) ? $row['Ειδικότητες που έχουν το μάθημα ως Β ανάθεση'] : ''),
            'school_code'=>$schoolCodeColumn !== '' ? vacanciesCoverageSchoolCode(isset($row[$schoolCodeColumn]) ? $row[$schoolCodeColumn] : '') : '',
            'school_name'=>$schoolNameColumn !== '' ? vacanciesCoverageNormalizeText(isset($row[$schoolNameColumn]) ? $row[$schoolNameColumn] : '') : '',
        );
        $allRows[] = $entry;
        if ($unitValue > 0) {
            $e = $entry; $e['deficit_hours'] = round($unitValue,2); $e['deficit_source'] = 'unit';
            $unitDeficits[] = $e; $unitTotal += $unitValue;
        }
        if ($myschoolValue > 0) {
            $e = $entry; $e['deficit_hours'] = round($myschoolValue,2); $e['deficit_source'] = 'myschool';
            $myschoolDeficits[] = $e; $myschoolTotal += $myschoolValue;
        }
    }
    // Backward-compatible aliases keep the school-entered estimate as the
    // default 5.1 dataset; the matcher can explicitly select myschool instead.
    return array(true, '', array(
        'rows'=>$allRows,
        'deficits'=>$unitDeficits,
        'deficit_rows'=>count($unitDeficits),
        'deficit_hours'=>round($unitTotal,2),
        'unit_deficits'=>$unitDeficits,
        'unit_deficit_rows'=>count($unitDeficits),
        'unit_deficit_hours'=>round($unitTotal,2),
        'myschool_deficits'=>$myschoolDeficits,
        'myschool_deficit_rows'=>count($myschoolDeficits),
        'myschool_deficit_hours'=>round($myschoolTotal,2),
        'school_scoped'=>$schoolScoped,
        'school_code_column'=>$schoolCodeColumn,
        'school_name_column'=>$schoolNameColumn,
    ));
}

function vacanciesCoverageTeacherCodes($teacher)
{
    $out = array();
    foreach (array('primary_code','secondary_code') as $key) {
        if (!empty($teacher[$key])) $out[$teacher[$key]] = true;
    }
    return array_keys($out);
}

function vacanciesCoverageListMatchesCode($list, $code)
{
    foreach ((array) $list as $entry) if (vacanciesCoverageCodeMatches($entry, $code)) return true;
    return false;
}

function vacanciesCoverageEvidenceFor($teacher, $vacancy, $stat51)
{
    $teacherCodes = vacanciesCoverageTeacherCodes($teacher);
    $vacancyCode = vacanciesCoverageCanonicalSpecialty(isset($vacancy['code']) ? $vacancy['code'] : '');
    $schoolCode = vacanciesCoverageSchoolCode(isset($vacancy['ministry_code']) ? $vacancy['ministry_code'] : '');
    $schoolScoped = !empty($stat51['school_scoped']);
    $best = null;

    foreach ((array) $stat51['deficits'] as $row) {
        if ($schoolScoped && $row['school_code'] !== '' && $schoolCode !== '' && $row['school_code'] !== $schoolCode) continue;
        // Treat the vacancy specialty as the intended/base assignment for this
        // course. This prevents unrelated global 5.1 rows from creating a bridge.
        if (!vacanciesCoverageListMatchesCode($row['A'], $vacancyCode) && !vacanciesCoverageListMatchesCode($row['B'], $vacancyCode)) continue;
        foreach ($teacherCodes as $teacherCode) {
            $level = '';
            if (vacanciesCoverageListMatchesCode($row['A'], $teacherCode)) $level = 'A';
            elseif (vacanciesCoverageListMatchesCode($row['B'], $teacherCode)) $level = 'B';
            if ($level === '') continue;
            $candidate = array(
                'level'=>$level,
                'teacher_code'=>$teacherCode,
                'subject'=>$row['subject'],
                'grade'=>$row['grade'],
                'sector'=>$row['sector'],
                'deficit_hours'=>$row['deficit_hours'],
                'school_scoped'=>$schoolScoped,
            );
            if ($best === null || ($best['level'] === 'B' && $level === 'A') || ($best['level'] === $level && $row['deficit_hours'] > $best['deficit_hours'])) {
                $best = $candidate;
            }
        }
    }
    return $best;
}

function vacanciesCoverageTeacherAtSchool($teacher, $schoolCode)
{
    $schoolCode = vacanciesCoverageSchoolCode($schoolCode);
    if ($schoolCode === '') return false;
    foreach ((array) $teacher['placements'] as $placement) {
        if (vacanciesCoverageSchoolCode(isset($placement['school_code']) ? $placement['school_code'] : '') === $schoolCode) return true;
    }
    return false;
}

function vacanciesCoverageBuildVacancies($allocation)
{
    $vacancies = array();
    if (empty($allocation['schools']) || !is_array($allocation['schools'])) return $vacancies;
    foreach ($allocation['schools'] as $school) {
        foreach ((array) $school['entries'] as $entry) {
            if (!isset($entry['type']) || $entry['type'] !== 'vacancy' || (float) $entry['hours'] <= 0) continue;
            $vacancies[] = array(
                'key'=>(int)$school['id'] . ':' . vacanciesCoverageCanonicalSpecialty($entry['code']),
                'school_id'=>(int)$school['id'],
                'school_name'=>$school['name'],
                'ministry_code'=>$school['ministry_code'],
                'school_address'=>isset($school['address']) ? $school['address'] : '',
                'code'=>vacanciesCoverageCanonicalSpecialty($entry['code']),
                'label'=>$entry['label'],
                'hours'=>(float)$entry['hours'],
            );
        }
    }
    return $vacancies;
}

function vacanciesCoverageBuildMyschoolVacancies($stat51)
{
    $vacancies = array();
    foreach ((array)(isset($stat51['myschool_deficits']) ? $stat51['myschool_deficits'] : array()) as $row) {
        if ((float)$row['myschool_hours'] <= 0) continue;
        $schoolCode = isset($row['school_code']) ? vacanciesCoverageSchoolCode($row['school_code']) : '';
        $schoolName = isset($row['school_name']) ? (string)$row['school_name'] : '';
        if ($schoolName === '') $schoolName = $schoolCode !== '' ? $schoolCode : '5.1 — χωρίς σχολική μονάδα';
        $vacancies[] = array(
            'key'=>'myschool:' . (int)$row['row_no'],
            'school_id'=>0,
            'school_name'=>$schoolName,
            'ministry_code'=>$schoolCode,
            'school_address'=>'',
            'code'=>'',
            'label'=>trim(($row['grade'] !== '' ? $row['grade'].' · ' : '') . $row['subject']),
            'hours'=>(float)$row['myschool_hours'],
            'A'=>(array)$row['A'],
            'B'=>(array)$row['B'],
            'subject'=>$row['subject'],
            'grade'=>$row['grade'],
            'sector'=>$row['sector'],
            'source'=>'myschool',
        );
    }
    return $vacancies;
}

function vacanciesCoverageMyschoolCandidateEvidence($teacher, $vacancy)
{
    foreach (vacanciesCoverageTeacherCodes($teacher) as $teacherCode) {
        if (vacanciesCoverageListMatchesCode(isset($vacancy['A']) ? $vacancy['A'] : array(), $teacherCode)) {
            return array('level'=>'A','teacher_code'=>$teacherCode,'subject'=>$vacancy['subject'],'grade'=>$vacancy['grade'],'sector'=>$vacancy['sector'],'deficit_hours'=>$vacancy['hours'],'school_scoped'=>$vacancy['ministry_code'] !== '');
        }
    }
    foreach (vacanciesCoverageTeacherCodes($teacher) as $teacherCode) {
        if (vacanciesCoverageListMatchesCode(isset($vacancy['B']) ? $vacancy['B'] : array(), $teacherCode)) {
            return array('level'=>'B','teacher_code'=>$teacherCode,'subject'=>$vacancy['subject'],'grade'=>$vacancy['grade'],'sector'=>$vacancy['sector'],'deficit_hours'=>$vacancy['hours'],'school_scoped'=>$vacancy['ministry_code'] !== '');
        }
    }
    return null;
}

function vacanciesCoverageCandidateScore($teacher, $vacancy, $evidence)
{
    $codes = vacanciesCoverageTeacherCodes($teacher);
    $sameSpecialty = false;
    foreach ($codes as $code) if (vacanciesCoverageCodeMatches($vacancy['code'], $code)) { $sameSpecialty = true; break; }
    $sameSchool = vacanciesCoverageTeacherAtSchool($teacher, $vacancy['ministry_code']);

    // Placement policy: whenever an eligible vacancy exists in a school where
    // the teacher already serves, that school is considered before any new
    // destination. Inside the same location tier we still prefer the teacher's
    // own specialty / A assignment, then B assignment.
    //
    // The +1000 location tier intentionally dominates the assignment weights
    // below. This makes e.g. same-school B rank above external-school A, because
    // completing compulsory hours without an additional movement is the first
    // operational objective of this decision-support tool.
    $score = $sameSchool ? 1000 : 0;
    $kind = 'unsupported';
    if ($sameSpecialty) { $score += 300; $kind = 'same_specialty'; }
    if ($evidence) {
        if ($evidence['level'] === 'A') {
            if (!$sameSpecialty) $score += 260;
            $kind = $sameSpecialty ? 'same_specialty' : 'A';
        } elseif ($evidence['level'] === 'B') {
            if (!$sameSpecialty) $score += 220;
            if (!$sameSpecialty) $kind = 'B';
        }
    }
    if (!empty($teacher['secondary_code']) && $evidence && $evidence['teacher_code'] === $teacher['secondary_code']) $score -= 2;
    $score += min(20, (int) round($teacher['remaining_hours']));
    $score += min(10, (int) round($vacancy['hours']));
    return array($score, $kind, $sameSchool, $sameSpecialty);
}

function vacanciesCoverageDynamicCandidateScore($candidate, $teacherRemaining, $vacancyRemaining, $assignedDestinations)
{
    $am = $candidate['am'];
    $vk = $candidate['vacancy_key'];
    $teacherHours = isset($teacherRemaining[$am]) ? (float)$teacherRemaining[$am] : 0.0;
    $vacancyHours = isset($vacancyRemaining[$vk]) ? (float)$vacancyRemaining[$vk] : 0.0;
    if ($teacherHours <= 0 || $vacancyHours <= 0) return null;

    $destinationCode = vacanciesCoverageSchoolCode($candidate['vacancy']['ministry_code']);
    $used = isset($assignedDestinations[$am]) ? $assignedDestinations[$am] : array();
    $alreadyAssignedDestination = $destinationCode !== '' && isset($used[$destinationCode]);
    $destinationCount = count($used);

    // Keep the legal/assignment ranking dominant. Then prefer staying at an
    // existing placement or continuing at a destination already selected by
    // this proposal, and discourage scattering a teacher across many new schools.
    $dynamic = (int)$candidate['score'];
    if ($alreadyAssignedDestination) $dynamic += 28;
    elseif (!$candidate['same_school'] && $destinationCount > 0) $dynamic -= min(36, 12 * $destinationCount);

    $allocatable = min($teacherHours, $vacancyHours);
    $dynamic += min(12, (int)round($allocatable));
    if (abs($allocatable - $teacherHours) < 0.001 || abs($allocatable - $vacancyHours) < 0.001) $dynamic += 5;

    return array(
        'score'=>$dynamic,
        'allocatable'=>$allocatable,
        'already_assigned_destination'=>$alreadyAssignedDestination,
        'destination_count_before'=>$destinationCount,
    );
}

function vacanciesCoverageMatch($teachersData, $stat51, $allocation, $vacancySource = 'dde')
{
    $teachers = isset($teachersData['teachers']) ? $teachersData['teachers'] : array();
    $vacancySource = $vacancySource === 'myschool' ? 'myschool' : 'dde';
    $vacancies = $vacancySource === 'myschool' ? vacanciesCoverageBuildMyschoolVacancies($stat51) : vacanciesCoverageBuildVacancies($allocation);
    $teacherRemaining = array();
    foreach ($teachers as $am => $teacher) $teacherRemaining[$am] = (float) $teacher['remaining_hours'];
    $vacancyRemaining = array();
    foreach ($vacancies as $vacancy) $vacancyRemaining[$vacancy['key']] = (float) $vacancy['hours'];

    $candidates = array();
    foreach ($teachers as $am => $teacher) {
        foreach ($vacancies as $vacancy) {
            $evidence = $vacancySource === 'myschool' ? vacanciesCoverageMyschoolCandidateEvidence($teacher, $vacancy) : vacanciesCoverageEvidenceFor($teacher, $vacancy, $stat51);
            list($score, $kind, $sameSchool, $sameSpecialty) = vacanciesCoverageCandidateScore($teacher, $vacancy, $evidence);
            if ($kind === 'unsupported') continue;
            if (!$sameSpecialty && !$evidence) continue;
            $candidates[] = array(
                'am'=>$am,
                'vacancy_key'=>$vacancy['key'],
                'score'=>$score,
                'kind'=>$kind,
                'same_school'=>$sameSchool,
                'same_specialty'=>$sameSpecialty,
                'teacher'=>$teacher,
                'vacancy'=>$vacancy,
                'evidence'=>$evidence,
            );
        }
    }

    $recommendations = array();
    $assignedDestinations = array();
    $usedCandidate = array();
    $guard = 0;
    while ($guard++ < 5000) {
        $bestIndex = null;
        $bestMeta = null;
        foreach ($candidates as $index => $candidate) {
            if (isset($usedCandidate[$index])) continue;
            $meta = vacanciesCoverageDynamicCandidateScore($candidate, $teacherRemaining, $vacancyRemaining, $assignedDestinations);
            if ($meta === null) continue;
            if ($bestMeta === null || $meta['score'] > $bestMeta['score'] ||
                ($meta['score'] === $bestMeta['score'] && $meta['allocatable'] > $bestMeta['allocatable'])) {
                $bestIndex = $index;
                $bestMeta = $meta;
            }
        }
        if ($bestIndex === null) break;
        $candidate = $candidates[$bestIndex];
        $usedCandidate[$bestIndex] = true;
        $am = $candidate['am'];
        $vk = $candidate['vacancy_key'];
        $hours = $bestMeta['allocatable'];
        if ($hours <= 0) continue;

        $destinationCode = vacanciesCoverageSchoolCode($candidate['vacancy']['ministry_code']);
        if (!isset($assignedDestinations[$am])) $assignedDestinations[$am] = array();
        $isNewDestination = !$candidate['same_school'] && $destinationCode !== '' && !isset($assignedDestinations[$am][$destinationCode]);
        if ($destinationCode !== '') $assignedDestinations[$am][$destinationCode] = true;

        $recommendations[] = array(
            'am'=>$am,
            'teacher_name'=>trim($candidate['teacher']['last_name'].' '.$candidate['teacher']['first_name']),
            'primary_code'=>$candidate['teacher']['primary_code'],
            'secondary_code'=>$candidate['teacher']['secondary_code'],
            'teacher_initial_hours'=>$candidate['teacher']['remaining_hours'],
            'school_id'=>$candidate['vacancy']['school_id'],
            'school_name'=>$candidate['vacancy']['school_name'],
            'ministry_code'=>$candidate['vacancy']['ministry_code'],
            'school_address'=>isset($candidate['vacancy']['school_address']) ? $candidate['vacancy']['school_address'] : '',
            'vacancy_key'=>$vk,
            'vacancy_code'=>$candidate['vacancy']['code'],
            'vacancy_label'=>$candidate['vacancy']['label'],
            'vacancy_initial_hours'=>$candidate['vacancy']['hours'],
            'hours'=>round($hours,2),
            'assignment_kind'=>$candidate['kind'],
            'same_school'=>$candidate['same_school'],
            'new_destination'=>$isNewDestination,
            'continued_destination'=>!$candidate['same_school'] && !empty($bestMeta['already_assigned_destination']),
            'destination_count_after'=>count($assignedDestinations[$am]),
            'evidence'=>$candidate['evidence'],
            'score'=>$bestMeta['score'],
        );
        $teacherRemaining[$am] -= $hours;
        $vacancyRemaining[$vk] -= $hours;
    }

    $suggestedHours = 0.0;
    foreach ($recommendations as $r) $suggestedHours += $r['hours'];
    $vacancyHours = 0.0;
    foreach ($vacancies as $v) $vacancyHours += $v['hours'];
    $teacherHours = isset($teachersData['remaining_hours']) ? (float)$teachersData['remaining_hours'] : 0;
    return array(
        'recommendations'=>$recommendations,
        'candidate_count'=>count($candidates),
        'suggested_hours'=>round($suggestedHours,2),
        'vacancy_hours'=>round($vacancyHours,2),
        'available_hours'=>round($teacherHours,2),
        'remaining_teacher_hours'=>round(array_sum($teacherRemaining),2),
        'remaining_vacancy_hours'=>round(array_sum($vacancyRemaining),2),
        'teacher_remaining'=>$teacherRemaining,
        'vacancy_remaining'=>$vacancyRemaining,
        'assigned_destinations'=>$assignedDestinations,
        'vacancies'=>$vacancies,
        'vacancy_source'=>$vacancySource,
    );
}

function vacanciesCoverageRecommendationsByTeacher($match, $teachersData)
{
    $out = array();
    foreach ((array)$match['recommendations'] as $r) {
        $am = $r['am'];
        if (!isset($out[$am])) {
            $teacher = isset($teachersData['teachers'][$am]) ? $teachersData['teachers'][$am] : array();
            $out[$am] = array(
                'am'=>$am,
                'teacher_name'=>$r['teacher_name'],
                'primary_code'=>$r['primary_code'],
                'secondary_code'=>$r['secondary_code'],
                'initial_hours'=>$r['teacher_initial_hours'],
                'remaining_hours'=>isset($match['teacher_remaining'][$am]) ? $match['teacher_remaining'][$am] : 0,
                'placements'=>isset($teacher['placements']) ? $teacher['placements'] : array(),
                'rows'=>array(),
                'suggested_hours'=>0.0,
                'new_destinations'=>array(),
            );
        }
        $out[$am]['rows'][] = $r;
        $out[$am]['suggested_hours'] += $r['hours'];
        if (!empty($r['new_destination'])) $out[$am]['new_destinations'][$r['ministry_code']] = $r['school_name'];
    }
    uasort($out, function($a,$b){
        if ($a['suggested_hours'] == $b['suggested_hours']) return strcmp($a['teacher_name'],$b['teacher_name']);
        return $a['suggested_hours'] < $b['suggested_hours'] ? 1 : -1;
    });
    return $out;
}

function vacanciesCoverageRecommendationsBySchool($match)
{
    $out = array();
    foreach ((array)$match['vacancies'] as $v) {
        $key = (int)$v['school_id'];
        if (!isset($out[$key])) $out[$key] = array('school_id'=>$key,'school_name'=>$v['school_name'],'ministry_code'=>$v['ministry_code'],'school_address'=>isset($v['school_address'])?$v['school_address']:'','vacancy_hours'=>0.0,'suggested_hours'=>0.0,'rows'=>array());
        $out[$key]['vacancy_hours'] += $v['hours'];
    }
    foreach ((array)$match['recommendations'] as $r) {
        $key = (int)$r['school_id'];
        if (!isset($out[$key])) $out[$key] = array('school_id'=>$key,'school_name'=>$r['school_name'],'ministry_code'=>$r['ministry_code'],'school_address'=>isset($r['school_address'])?$r['school_address']:'','vacancy_hours'=>0.0,'suggested_hours'=>0.0,'rows'=>array());
        $out[$key]['suggested_hours'] += $r['hours'];
        $out[$key]['rows'][] = $r;
    }
    uasort($out, function($a,$b){
        $ar = $a['vacancy_hours'] - $a['suggested_hours'];
        $br = $b['vacancy_hours'] - $b['suggested_hours'];
        if ($ar == $br) return strcmp($a['school_name'],$b['school_name']);
        return $ar < $br ? 1 : -1;
    });
    return $out;
}

function vacanciesCoverageRecommendationsByVacancy($match)
{
    $out = array();
    foreach ((array)$match['vacancies'] as $v) {
        $out[$v['key']] = array(
            'key'=>$v['key'],
            'school_id'=>$v['school_id'],
            'school_name'=>$v['school_name'],
            'ministry_code'=>$v['ministry_code'],
            'school_address'=>isset($v['school_address']) ? $v['school_address'] : '',
            'code'=>$v['code'],
            'label'=>$v['label'],
            'initial_hours'=>$v['hours'],
            'suggested_hours'=>0.0,
            'remaining_hours'=>isset($match['vacancy_remaining'][$v['key']]) ? $match['vacancy_remaining'][$v['key']] : $v['hours'],
            'rows'=>array(),
        );
    }
    foreach ((array)$match['recommendations'] as $r) {
        $key = $r['vacancy_key'];
        if (!isset($out[$key])) continue;
        $out[$key]['suggested_hours'] += $r['hours'];
        $out[$key]['rows'][] = $r;
    }
    uasort($out, function($a,$b){
        if ($a['remaining_hours'] == $b['remaining_hours']) return strcmp($a['school_name'].$a['code'],$b['school_name'].$b['code']);
        return $a['remaining_hours'] < $b['remaining_hours'] ? 1 : -1;
    });
    return $out;
}
