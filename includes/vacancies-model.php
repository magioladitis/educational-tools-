<?php
require_once __DIR__ . '/vacancies-db.php';
require_once __DIR__ . '/teacher-specialties.php';

function vacanciesH($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function vacanciesSchools()
{
    return vacanciesQueryAll("SELECT id, ministry_code, name, school_type FROM vacancy_schools WHERE active=1 ORDER BY name");
}

function vacanciesSchool($id)
{
    $id = (int) $id;
    if ($id <= 0) return null;
    return vacanciesQueryOne("SELECT id, ministry_code, name, school_type FROM vacancy_schools WHERE id=" . $id . " AND active=1");
}

function vacanciesSpecialties()
{
    return vacanciesQueryAll("SELECT id, code, label FROM vacancy_specialties WHERE active=1 ORDER BY sort_order, code");
}

function vacanciesRounds()
{
    return vacanciesQueryAll("SELECT id, title, reference_date, school_year, status FROM vacancy_rounds ORDER BY reference_date DESC, id DESC");
}

function vacanciesActiveRound()
{
    return vacanciesQueryOne("SELECT id, title, reference_date, school_year, status FROM vacancy_rounds WHERE status='open' ORDER BY reference_date DESC, id DESC LIMIT 1");
}

function vacanciesRound($id)
{
    $id = (int) $id;
    if ($id <= 0) return null;
    return vacanciesQueryOne("SELECT id, title, reference_date, school_year, status FROM vacancy_rounds WHERE id=" . $id);
}

function vacanciesLatestSubmission($roundId, $schoolId, $includeDraft)
{
    $roundId = (int) $roundId;
    $schoolId = (int) $schoolId;
    if ($roundId <= 0 || $schoolId <= 0) return null;
    $statusSql = $includeDraft ? "" : " AND status='submitted'";
    return vacanciesQueryOne("SELECT * FROM vacancy_submissions WHERE round_id=".$roundId." AND school_id=".$schoolId.$statusSql." ORDER BY revision_no DESC, id DESC LIMIT 1");
}

function vacanciesSubmissionEntries($submissionId)
{
    $submissionId = (int) $submissionId;
    if ($submissionId <= 0) return array();
    $rows = vacanciesQueryAll("SELECT e.*, s.code, s.label FROM vacancy_entries e JOIN vacancy_specialties s ON s.id=e.specialty_id WHERE e.submission_id=".$submissionId." ORDER BY s.sort_order, s.code");
    $byId = array();
    foreach ($rows as $row) $byId[(int) $row['specialty_id']] = $row;
    return $byId;
}

function vacanciesReasonOptions()
{
    return array(
        '' => '— χωρίς ειδική αιτιολογία —',
        'new_section' => 'Δημιουργία επιπλέον τμήματος',
        'timetable_change' => 'Μεταβολή ωρολογίου / διδακτικών αναγκών',
        'leave_absence' => 'Άδεια ή μακροχρόνια απουσία',
        'teacher_move' => 'Μετακίνηση / διάθεση εκπαιδευτικού',
        'correction' => 'Διόρθωση προηγούμενης καταχώρισης',
        'other' => 'Άλλη αιτία',
    );
}

function vacanciesSaveSubmission($roundId, $schoolId, $status, $rows, $schoolNote)
{
    $db = vacanciesDb();
    if (!$db) return array(false, 'Δεν υπάρχει σύνδεση με τη βάση δεδομένων.');
    $roundId = (int) $roundId;
    $schoolId = (int) $schoolId;
    $status = $status === 'submitted' ? 'submitted' : 'draft';
    $round = vacanciesRound($roundId);
    $school = vacanciesSchool($schoolId);
    if (!$round || !$school) return array(false, 'Μη έγκυρος γύρος ή σχολική μονάδα.');
    if ($round['status'] !== 'open') return array(false, 'Ο συγκεκριμένος γύρος δεν είναι ανοικτός για υποβολές.');

    $latest = vacanciesLatestSubmission($roundId, $schoolId, true);
    $revision = $latest ? ((int) $latest['revision_no'] + 1) : 1;
    $schoolNote = trim((string) $schoolNote);

    $db->begin_transaction();
    try {
        $stmt = $db->prepare("INSERT INTO vacancy_submissions (round_id, school_id, revision_no, status, school_note, submitted_at) VALUES (?, ?, ?, ?, ?, IF(?='submitted', NOW(), NULL))");
        if (!$stmt) throw new Exception($db->error);
        $stmt->bind_param('iiisss', $roundId, $schoolId, $revision, $status, $schoolNote, $status);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $submissionId = (int) $stmt->insert_id;
        $stmt->close();

        $entryStmt = $db->prepare("INSERT INTO vacancy_entries (submission_id, specialty_id, balance_type, hours, change_reason, change_note) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$entryStmt) throw new Exception($db->error);
        foreach ($rows as $specialtyId => $row) {
            $specialtyId = (int) $specialtyId;
            $hours = isset($row['hours']) ? (int) $row['hours'] : 0;
            $type = isset($row['type']) ? (string) $row['type'] : 'zero';
            if ($specialtyId <= 0 || $hours <= 0 || !in_array($type, array('vacancy','surplus'), true)) continue;
            if ($hours > 999) throw new Exception('Οι ώρες ανά ειδικότητα δεν μπορούν να υπερβαίνουν τις 999.');
            $reason = isset($row['reason']) ? (string) $row['reason'] : '';
            $note = isset($row['note']) ? trim((string) $row['note']) : '';
            if (!array_key_exists($reason, vacanciesReasonOptions())) $reason = '';
            $entryStmt->bind_param('iisiss', $submissionId, $specialtyId, $type, $hours, $reason, $note);
            if (!$entryStmt->execute()) throw new Exception($entryStmt->error);
        }
        $entryStmt->close();
        $db->commit();
        return array(true, $status === 'submitted' ? 'Η υποβολή καταχωρίστηκε οριστικά.' : 'Το πρόχειρο αποθηκεύτηκε.');
    } catch (Exception $e) {
        $db->rollback();
        return array(false, 'Αποτυχία αποθήκευσης: ' . $e->getMessage());
    }
}

function vacanciesDashboardStats($roundId)
{
    $roundId = (int) $roundId;
    if ($roundId <= 0) return array();
    $base = vacanciesQueryOne("SELECT COUNT(*) AS schools_total FROM vacancy_schools WHERE active=1");
    $submitted = vacanciesQueryOne("SELECT COUNT(DISTINCT school_id) AS schools_submitted FROM vacancy_submissions WHERE round_id=".$roundId." AND status='submitted'");
    $latestSql = "SELECT s.school_id, MAX(s.revision_no) rev FROM vacancy_submissions s WHERE s.round_id=".$roundId." AND s.status='submitted' GROUP BY s.school_id";
    $totals = vacanciesQueryOne("SELECT COALESCE(SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END),0) vacancies, COALESCE(SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END),0) surpluses, COALESCE(SUM(CASE WHEN e.balance_type='vacancy' AND e.change_reason='new_section' THEN e.hours ELSE 0 END),0) new_section_hours FROM vacancy_submissions s JOIN (".$latestSql.") latest ON latest.school_id=s.school_id AND latest.rev=s.revision_no LEFT JOIN vacancy_entries e ON e.submission_id=s.id WHERE s.round_id=".$roundId." AND s.status='submitted'");
    return array(
        'schools_total' => $base ? (int) $base['schools_total'] : 0,
        'schools_submitted' => $submitted ? (int) $submitted['schools_submitted'] : 0,
        'vacancies' => $totals ? (int) $totals['vacancies'] : 0,
        'surpluses' => $totals ? (int) $totals['surpluses'] : 0,
        'new_section_hours' => $totals ? (int) $totals['new_section_hours'] : 0,
    );
}

function vacanciesDashboardSchools($roundId)
{
    $roundId = (int) $roundId;
    $sql = "SELECT sc.id, sc.name, sc.ministry_code, sub.status, sub.submitted_at, sub.revision_no, sub.school_note, COALESCE(SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END),0) vacancies, COALESCE(SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END),0) surpluses FROM vacancy_schools sc LEFT JOIN (SELECT s1.* FROM vacancy_submissions s1 JOIN (SELECT school_id, MAX(revision_no) rev FROM vacancy_submissions WHERE round_id=".$roundId." GROUP BY school_id) x ON x.school_id=s1.school_id AND x.rev=s1.revision_no WHERE s1.round_id=".$roundId.") sub ON sub.school_id=sc.id LEFT JOIN vacancy_entries e ON e.submission_id=sub.id WHERE sc.active=1 GROUP BY sc.id, sc.name, sc.ministry_code, sub.status, sub.submitted_at, sub.revision_no, sub.school_note ORDER BY CASE WHEN sub.status='submitted' THEN 0 WHEN sub.status='draft' THEN 1 ELSE 2 END, sc.name";
    return vacanciesQueryAll($sql);
}

function vacanciesDashboardSpecialties($roundId)
{
    $roundId = (int) $roundId;
    $latestSql = "SELECT s.school_id, MAX(s.revision_no) rev FROM vacancy_submissions s WHERE s.round_id=".$roundId." AND s.status='submitted' GROUP BY s.school_id";
    return vacanciesQueryAll("SELECT sp.code, sp.label, SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END) vacancies, SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END) surpluses FROM vacancy_submissions s JOIN (".$latestSql.") latest ON latest.school_id=s.school_id AND latest.rev=s.revision_no JOIN vacancy_entries e ON e.submission_id=s.id JOIN vacancy_specialties sp ON sp.id=e.specialty_id WHERE s.round_id=".$roundId." AND s.status='submitted' GROUP BY sp.id, sp.code, sp.label HAVING vacancies>0 OR surpluses>0 ORDER BY vacancies DESC, sp.code");
}

function vacanciesDashboardAllocation($roundId)
{
    $roundId = (int) $roundId;
    $result = array('specialties' => array(), 'schools' => array());
    if ($roundId <= 0) return $result;

    $latestSql = "SELECT school_id, MAX(revision_no) rev FROM vacancy_submissions WHERE round_id=".$roundId." AND status='submitted' GROUP BY school_id";
    $rows = vacanciesQueryAll(
        "SELECT sc.id AS school_id, sc.name AS school_name, sc.ministry_code, " .
        "sp.id AS specialty_id, sp.code, sp.label, sp.sort_order, " .
        "e.balance_type, e.hours, e.change_reason, e.change_note " .
        "FROM vacancy_submissions s " .
        "JOIN (".$latestSql.") latest ON latest.school_id=s.school_id AND latest.rev=s.revision_no " .
        "JOIN vacancy_schools sc ON sc.id=s.school_id " .
        "JOIN vacancy_entries e ON e.submission_id=s.id " .
        "JOIN vacancy_specialties sp ON sp.id=e.specialty_id " .
        "WHERE s.round_id=".$roundId." AND s.status='submitted' " .
        "ORDER BY sp.sort_order, sp.code, e.balance_type, e.hours DESC, sc.name"
    );

    foreach ($rows as $row) {
        $specialtyId = (int) $row['specialty_id'];
        $schoolId = (int) $row['school_id'];
        $hours = (int) $row['hours'];
        $type = (string) $row['balance_type'];
        if ($hours <= 0 || !in_array($type, array('vacancy', 'surplus'), true)) continue;

        if (!isset($result['specialties'][$specialtyId])) {
            $result['specialties'][$specialtyId] = array(
                'id' => $specialtyId,
                'code' => $row['code'],
                'label' => $row['label'],
                'sort_order' => (int) $row['sort_order'],
                'vacancies' => 0,
                'surpluses' => 0,
                'vacancy_schools' => array(),
                'surplus_schools' => array(),
            );
        }
        if (!isset($result['schools'][$schoolId])) {
            $result['schools'][$schoolId] = array(
                'id' => $schoolId,
                'name' => $row['school_name'],
                'ministry_code' => $row['ministry_code'],
                'vacancies' => 0,
                'surpluses' => 0,
                'entries' => array(),
            );
        }

        $entry = array(
            'school_id' => $schoolId,
            'school_name' => $row['school_name'],
            'ministry_code' => $row['ministry_code'],
            'specialty_id' => $specialtyId,
            'code' => $row['code'],
            'label' => $row['label'],
            'type' => $type,
            'hours' => $hours,
            'reason' => $row['change_reason'],
            'note' => $row['change_note'],
        );

        if ($type === 'vacancy') {
            $result['specialties'][$specialtyId]['vacancies'] += $hours;
            $result['specialties'][$specialtyId]['vacancy_schools'][] = $entry;
            $result['schools'][$schoolId]['vacancies'] += $hours;
        } else {
            $result['specialties'][$specialtyId]['surpluses'] += $hours;
            $result['specialties'][$specialtyId]['surplus_schools'][] = $entry;
            $result['schools'][$schoolId]['surpluses'] += $hours;
        }
        $result['schools'][$schoolId]['entries'][] = $entry;
    }

    $result['specialties'] = array_values($result['specialties']);
    usort($result['specialties'], function ($a, $b) {
        if ((int) $a['vacancies'] === (int) $b['vacancies']) {
            if ((int) $a['surpluses'] === (int) $b['surpluses']) {
                return strcmp((string) $a['code'], (string) $b['code']);
            }
            return (int) $a['surpluses'] > (int) $b['surpluses'] ? -1 : 1;
        }
        return (int) $a['vacancies'] > (int) $b['vacancies'] ? -1 : 1;
    });

    $result['schools'] = array_values($result['schools']);
    usort($result['schools'], function ($a, $b) {
        if ((int) $a['vacancies'] === (int) $b['vacancies']) return strcmp((string) $a['name'], (string) $b['name']);
        return (int) $a['vacancies'] > (int) $b['vacancies'] ? -1 : 1;
    });

    return $result;
}

function vacanciesCreateRound($title, $referenceDate, $schoolYear)
{
    $db = vacanciesDb();
    if (!$db) return array(false, 'Δεν υπάρχει σύνδεση με τη βάση.');
    $title = trim((string) $title);
    $referenceDate = trim((string) $referenceDate);
    $schoolYear = trim((string) $schoolYear);
    if ($title === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $referenceDate) || $schoolYear === '') {
        return array(false, 'Συμπλήρωσε τίτλο, ημερομηνία και σχολικό έτος.');
    }
    $db->begin_transaction();
    try {
        if (!$db->query("UPDATE vacancy_rounds SET status='closed' WHERE status='open'")) throw new Exception($db->error);
        $stmt = $db->prepare("INSERT INTO vacancy_rounds (title, reference_date, school_year, status) VALUES (?, ?, ?, 'open')");
        if (!$stmt) throw new Exception($db->error);
        $stmt->bind_param('sss', $title, $referenceDate, $schoolYear);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $id = (int) $stmt->insert_id;
        $stmt->close();
        $db->commit();
        return array(true, 'Δημιουργήθηκε νέος ανοικτός γύρος καταγραφής.', $id);
    } catch (Exception $e) {
        $db->rollback();
        return array(false, 'Δεν δημιουργήθηκε ο γύρος: ' . $e->getMessage());
    }
}

function vacanciesSetRoundStatus($roundId, $status)
{
    $db = vacanciesDb();
    if (!$db) return array(false, 'Δεν υπάρχει σύνδεση με τη βάση.');
    $roundId = (int) $roundId;
    if (!in_array($status, array('draft','open','closed'), true) || $roundId <= 0) return array(false, 'Μη έγκυρη αλλαγή κατάστασης.');
    $db->begin_transaction();
    try {
        if ($status === 'open') {
            if (!$db->query("UPDATE vacancy_rounds SET status='closed' WHERE status='open' AND id<>".$roundId)) throw new Exception($db->error);
        }
        $stmt = $db->prepare("UPDATE vacancy_rounds SET status=? WHERE id=?");
        if (!$stmt) throw new Exception($db->error);
        $stmt->bind_param('si', $status, $roundId);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $db->commit();
        return array(true, 'Η κατάσταση του γύρου ενημερώθηκε.');
    } catch (Exception $e) {
        $db->rollback();
        return array(false, 'Αποτυχία ενημέρωσης: ' . $e->getMessage());
    }
}

function vacanciesPreviousRound($round)
{
    if (!$round || !isset($round['reference_date'])) return null;
    $date = vacanciesDbEscape($round['reference_date']);
    $id = isset($round['id']) ? (int) $round['id'] : 0;
    return vacanciesQueryOne("SELECT id, title, reference_date, school_year, status FROM vacancy_rounds WHERE reference_date<'".$date."' AND id<>".$id." ORDER BY reference_date DESC, id DESC LIMIT 1");
}

function vacanciesLatestSubmittedTotalsBySchool($roundId)
{
    $roundId = (int) $roundId;
    if ($roundId <= 0) return array();
    $latestSql = "SELECT school_id, MAX(revision_no) rev FROM vacancy_submissions WHERE round_id=".$roundId." AND status='submitted' GROUP BY school_id";
    $rows = vacanciesQueryAll("SELECT sc.id school_id, sc.name, COALESCE(SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END),0) vacancies, COALESCE(SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END),0) surpluses, COALESCE(SUM(CASE WHEN e.balance_type='vacancy' AND e.change_reason='new_section' THEN e.hours ELSE 0 END),0) new_section_hours FROM vacancy_submissions s JOIN (".$latestSql.") x ON x.school_id=s.school_id AND x.rev=s.revision_no JOIN vacancy_schools sc ON sc.id=s.school_id LEFT JOIN vacancy_entries e ON e.submission_id=s.id WHERE s.round_id=".$roundId." AND s.status='submitted' GROUP BY sc.id, sc.name ORDER BY sc.name");
    $out = array();
    foreach ($rows as $row) $out[(int) $row['school_id']] = $row;
    return $out;
}

function vacanciesCompareRounds($currentRoundId, $previousRoundId)
{
    $current = vacanciesLatestSubmittedTotalsBySchool($currentRoundId);
    $previous = vacanciesLatestSubmittedTotalsBySchool($previousRoundId);
    $result = array(
        'schools' => 0,
        'previous_vacancies' => 0,
        'current_vacancies' => 0,
        'new_section_hours' => 0,
        'changes' => array(),
    );
    foreach ($current as $schoolId => $now) {
        if (!isset($previous[$schoolId])) continue;
        $before = $previous[$schoolId];
        $prevVac = (int) $before['vacancies'];
        $curVac = (int) $now['vacancies'];
        $newSection = (int) $now['new_section_hours'];
        $result['schools']++;
        $result['previous_vacancies'] += $prevVac;
        $result['current_vacancies'] += $curVac;
        $result['new_section_hours'] += $newSection;
        $result['changes'][] = array(
            'school_id' => $schoolId,
            'name' => $now['name'],
            'previous' => $prevVac,
            'current' => $curVac,
            'delta' => $curVac - $prevVac,
            'new_section_hours' => $newSection,
        );
    }
    usort($result['changes'], function ($a, $b) {
        if ($a['delta'] === $b['delta']) return strcmp($a['name'], $b['name']);
        return $a['delta'] > $b['delta'] ? -1 : 1;
    });
    return $result;
}

