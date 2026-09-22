<?php
require_once __DIR__ . '/vacancies-db.php';
require_once __DIR__ . '/teacher-specialties.php';

function vacanciesH($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function vacanciesSchools()
{
    return vacanciesQueryAll("SELECT id, ministry_code, name, school_type, address, email, phone FROM vacancy_schools WHERE active=1 ORDER BY name");
}

function vacanciesSchool($id)
{
    $id = (int) $id;
    if ($id <= 0) return null;
    return vacanciesQueryOne("SELECT id, ministry_code, name, school_type, address, email, phone FROM vacancy_schools WHERE id=" . $id . " AND active=1");
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

function vacanciesLatestSubmission($roundId, $schoolId, $includeDraft, $scope = 'general')
{
    $roundId = (int) $roundId;
    $schoolId = (int) $schoolId;
    $scope = $scope === 'special' ? 'special' : 'general';
    if ($roundId <= 0 || $schoolId <= 0) return null;
    $statusSql = $includeDraft ? "" : " AND status='submitted'";
    return vacanciesQueryOne("SELECT * FROM vacancy_submissions WHERE round_id=".$roundId." AND school_id=".$schoolId." AND education_scope='".$scope."'".$statusSql." ORDER BY revision_no DESC, id DESC LIMIT 1");
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

function vacanciesSubmissionHistory($schoolId, $limit = 50)
{
    $schoolId = (int) $schoolId;
    $limit = max(1, min(200, (int) $limit));
    if ($schoolId <= 0) return array();

    // Keep every final submission visible. A draft is visible only while it is
    // the newest revision for that round/scope. Older draft autosaves remain
    // safely in the database but do not clutter the director's history.
    return vacanciesQueryAll(
        "SELECT s.id,s.round_id,s.education_scope,s.revision_no,s.status,s.school_note,s.created_at,s.submitted_at," .
        " r.title,r.reference_date,r.school_year,r.status round_status," .
        " COALESCE(SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END),0) vacancies," .
        " COALESCE(SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END),0) surpluses" .
        " FROM vacancy_submissions s JOIN vacancy_rounds r ON r.id=s.round_id" .
        " LEFT JOIN vacancy_entries e ON e.submission_id=s.id" .
        " WHERE s.school_id=".$schoolId.
        " AND (s.status='submitted' OR (s.status='draft' AND NOT EXISTS (" .
        " SELECT 1 FROM vacancy_submissions newer" .
        " WHERE newer.school_id=s.school_id AND newer.round_id=s.round_id AND newer.education_scope=s.education_scope" .
        " AND (newer.revision_no>s.revision_no OR (newer.revision_no=s.revision_no AND newer.id>s.id))" .
        ")))" .
        " GROUP BY s.id,s.round_id,s.education_scope,s.revision_no,s.status,s.school_note,s.created_at,s.submitted_at,r.title,r.reference_date,r.school_year,r.status" .
        " ORDER BY r.reference_date DESC,s.revision_no DESC,s.id DESC LIMIT ".$limit
    );
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

function vacanciesSaveSubmission($roundId, $schoolId, $status, $rows, $schoolNote, $scope = 'general')
{
    $db = vacanciesDb();
    if (!$db) return array(false, 'Δεν υπάρχει σύνδεση με τη βάση δεδομένων.');
    $roundId = (int) $roundId;
    $schoolId = (int) $schoolId;
    $status = $status === 'submitted' ? 'submitted' : 'draft';
    $scope = $scope === 'special' ? 'special' : 'general';
    $round = vacanciesRound($roundId);
    $school = vacanciesSchool($schoolId);
    if (!$round || !$school) return array(false, 'Μη έγκυρος γύρος ή σχολική μονάδα.');
    if ($round['status'] !== 'open') return array(false, 'Ο συγκεκριμένος γύρος δεν είναι ανοικτός για υποβολές.');

    $latest = vacanciesLatestSubmission($roundId, $schoolId, true, $scope);
    $reuseDraft = $latest && $latest['status'] === 'draft';
    $revision = $reuseDraft ? (int) $latest['revision_no'] : ($latest ? ((int) $latest['revision_no'] + 1) : 1);
    $schoolNote = trim((string) $schoolNote);

    $db->begin_transaction();
    try {
        if ($reuseDraft) {
            // A draft is a working copy, not a new historical version on every save.
            // Final submission promotes that same draft revision to submitted.
            $submissionId = (int) $latest['id'];
            $stmt = $db->prepare("UPDATE vacancy_submissions SET status=?, school_note=?, submitted_at=IF(?='submitted', NOW(), NULL), created_at=IF(?='draft', NOW(), created_at) WHERE id=? AND round_id=? AND school_id=? AND education_scope=?");
            if (!$stmt) throw new Exception($db->error);
            $stmt->bind_param('ssssiiis', $status, $schoolNote, $status, $status, $submissionId, $roundId, $schoolId, $scope);
            if (!$stmt->execute()) throw new Exception($stmt->error);
            $stmt->close();

            $deleteStmt = $db->prepare("DELETE FROM vacancy_entries WHERE submission_id=?");
            if (!$deleteStmt) throw new Exception($db->error);
            $deleteStmt->bind_param('i', $submissionId);
            if (!$deleteStmt->execute()) throw new Exception($deleteStmt->error);
            $deleteStmt->close();
        } else {
            $stmt = $db->prepare("INSERT INTO vacancy_submissions (round_id, school_id, education_scope, revision_no, status, school_note, submitted_at) VALUES (?, ?, ?, ?, ?, ?, IF(?='submitted', NOW(), NULL))");
            if (!$stmt) throw new Exception($db->error);
            $stmt->bind_param('iisisss', $roundId, $schoolId, $scope, $revision, $status, $schoolNote, $status);
            if (!$stmt->execute()) throw new Exception($stmt->error);
            $submissionId = (int) $stmt->insert_id;
            $stmt->close();
        }

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
    $submitted = vacanciesQueryOne("SELECT COUNT(DISTINCT school_id) AS schools_submitted FROM vacancy_submissions WHERE round_id=".$roundId." AND education_scope='general' AND status='submitted'");
    $latestSql = "SELECT s.school_id, MAX(s.revision_no) rev FROM vacancy_submissions s WHERE s.round_id=".$roundId." AND s.education_scope='general' AND s.status='submitted' GROUP BY s.school_id";
    $totals = vacanciesQueryOne("SELECT COALESCE(SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END),0) vacancies, COALESCE(SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END),0) surpluses, COALESCE(SUM(CASE WHEN e.balance_type='vacancy' AND e.change_reason='new_section' THEN e.hours ELSE 0 END),0) new_section_hours FROM vacancy_submissions s JOIN (".$latestSql.") latest ON latest.school_id=s.school_id AND latest.rev=s.revision_no LEFT JOIN vacancy_entries e ON e.submission_id=s.id WHERE s.round_id=".$roundId." AND s.education_scope='general' AND s.status='submitted'");
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
    $sql = "SELECT sc.id, sc.name, sc.ministry_code, sub.status, sub.submitted_at, sub.revision_no, sub.school_note, COALESCE(SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END),0) vacancies, COALESCE(SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END),0) surpluses FROM vacancy_schools sc LEFT JOIN (SELECT s1.* FROM vacancy_submissions s1 JOIN (SELECT school_id, MAX(revision_no) rev FROM vacancy_submissions WHERE round_id=".$roundId." AND education_scope='general' GROUP BY school_id) x ON x.school_id=s1.school_id AND x.rev=s1.revision_no WHERE s1.round_id=".$roundId." AND s1.education_scope='general') sub ON sub.school_id=sc.id LEFT JOIN vacancy_entries e ON e.submission_id=sub.id WHERE sc.active=1 GROUP BY sc.id, sc.name, sc.ministry_code, sub.status, sub.submitted_at, sub.revision_no, sub.school_note ORDER BY CASE WHEN sub.status='submitted' THEN 0 WHEN sub.status='draft' THEN 1 ELSE 2 END, sc.name";
    return vacanciesQueryAll($sql);
}

function vacanciesDashboardSpecialties($roundId)
{
    $roundId = (int) $roundId;
    $latestSql = "SELECT s.school_id, MAX(s.revision_no) rev FROM vacancy_submissions s WHERE s.round_id=".$roundId." AND s.education_scope='general' AND s.status='submitted' GROUP BY s.school_id";
    return vacanciesQueryAll("SELECT sp.code, sp.label, SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END) vacancies, SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END) surpluses FROM vacancy_submissions s JOIN (".$latestSql.") latest ON latest.school_id=s.school_id AND latest.rev=s.revision_no JOIN vacancy_entries e ON e.submission_id=s.id JOIN vacancy_specialties sp ON sp.id=e.specialty_id WHERE s.round_id=".$roundId." AND s.education_scope='general' AND s.status='submitted' GROUP BY sp.id, sp.code, sp.label HAVING vacancies>0 OR surpluses>0 ORDER BY vacancies DESC, sp.code");
}

function vacanciesDashboardAllocation($roundId)
{
    $roundId = (int) $roundId;
    $result = array('specialties' => array(), 'schools' => array());
    if ($roundId <= 0) return $result;

    $latestSql = "SELECT school_id, MAX(revision_no) rev FROM vacancy_submissions WHERE round_id=".$roundId." AND education_scope='general' AND status='submitted' GROUP BY school_id";
    $rows = vacanciesQueryAll(
        "SELECT sc.id AS school_id, sc.name AS school_name, sc.ministry_code, " .
        "sp.id AS specialty_id, sp.code, sp.label, sp.sort_order, " .
        "e.balance_type, e.hours, e.change_reason, e.change_note " .
        "FROM vacancy_submissions s " .
        "JOIN (".$latestSql.") latest ON latest.school_id=s.school_id AND latest.rev=s.revision_no " .
        "JOIN vacancy_schools sc ON sc.id=s.school_id " .
        "JOIN vacancy_entries e ON e.submission_id=s.id " .
        "JOIN vacancy_specialties sp ON sp.id=e.specialty_id " .
        "WHERE s.round_id=".$roundId." AND s.education_scope='general' AND s.status='submitted' " .
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
    $latestSql = "SELECT school_id, MAX(revision_no) rev FROM vacancy_submissions WHERE round_id=".$roundId." AND education_scope='general' AND status='submitted' GROUP BY school_id";
    $rows = vacanciesQueryAll("SELECT sc.id school_id, sc.name, COALESCE(SUM(CASE WHEN e.balance_type='vacancy' THEN e.hours ELSE 0 END),0) vacancies, COALESCE(SUM(CASE WHEN e.balance_type='surplus' THEN e.hours ELSE 0 END),0) surpluses, COALESCE(SUM(CASE WHEN e.balance_type='vacancy' AND e.change_reason='new_section' THEN e.hours ELSE 0 END),0) new_section_hours FROM vacancy_submissions s JOIN (".$latestSql.") x ON x.school_id=s.school_id AND x.rev=s.revision_no JOIN vacancy_schools sc ON sc.id=s.school_id LEFT JOIN vacancy_entries e ON e.submission_id=s.id WHERE s.round_id=".$roundId." AND s.education_scope='general' AND s.status='submitted' GROUP BY sc.id, sc.name ORDER BY sc.name");
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


/**
 * Legacy-compatible export order and labels for the Corfu summary matrix.
 * Unknown/new schools are appended after the known list and keep their official name.
 */
function vacanciesExportSchoolDefinitions()
{
    return array(
        '2401010' => '1ο Γυμνάσιο',
        '2401020' => '2ο Γυμνάσιο',
        '2401030' => '3ο Γυμνάσιο',
        '2401040' => '4ο Γυμνάσιο',
        '2401050' => '5ο Γυμνάσιο',
        '2401070' => '6ο Γυμνάσιο',
        '2401055' => '7ο Γυμνάσιο',
        '2402090' => 'Γ/σιο Αγ. Ιωάννη',
        '2404010' => 'Γ/σιο Αγρού',
        '2406020' => 'Γ/σιο Αμφιπαγιτών',
        '2410010' => 'Γ/σιο Θιναλίου',
        '2406010' => 'Γ/σιο Καρουσάδων',
        '2407010' => 'Γ/σιο Καστελλάνων',
        '2402010' => 'Γ/σιο Λευκίμμης',
        '2408050' => 'Γ/σιο Λιαπάδων',
        '2402050' => 'Γ/σιο Φαιάκων',
        '2401060' => 'Γ/σιο Εσπερινό',
        '2405010' => 'Γ/σιο ΛΤ Αργυράδων',
        '2409010' => 'Γ/σιο ΛΤ Κασσιόπης',
        '2408010' => 'Γ/σιο ΛΤ Σκριπερού',
        '2401065' => 'Μουσικό',
        '2403010' => 'Γ/σιο ΛΤ Παξών',
        '2451010' => '1ο Λύκειο',
        '2451020' => '2ο Λύκειο',
        '2451030' => '3ο Λύκειο',
        '2451040' => '4ο Λύκειο',
        '2490030' => '5ο Λύκειο',
        '2454010' => 'Λύκειο Αγρού',
        '2457010' => 'Λύκειο Καστελλάνων',
        '2452010' => 'Λύκειο Λευκίμμης',
        '2451060' => 'Λύκειο Εσπερινό',
        '2440030' => '1ο ΕΠΑΛ',
        '2448000' => 'ΠΕΠΑΛ',
        '2440045' => 'ΕΠΑΛ Εσπερινό',
        '2440050' => 'ΕΠΑΛ Κορακιάνας',
        '2411001' => 'ΕΝΕΕΓΥΛ',
        '2441001' => 'ΕΕΕΕΚ',
        'SEK087' => '1ο Ε.Κ.',
    );
}

function vacanciesExportLegacySpecialtyCodes()
{
    return array(
        'ΠΕ01','ΠΕ02','ΠΕ03','ΠΕ04.01','ΠΕ04.02','ΠΕ04.04','ΠΕ04.05',
        'ΠΕ05','ΠΕ06','ΠΕ07','ΠΕ08','ΠΕ11','ΠΕ33','ΠΕ78','ΠΕ79.01','ΠΕ80',
        'ΠΕ81','ΠΕ82','ΠΕ83','ΠΕ84','ΠΕ85','ΠΕ86','SKILLS_GYM','TECH_GYM',
        'ΠΕ87.01','ΠΕ87.02','ΠΕ87.09','ΠΕ88.01','ΠΕ88.02','ΠΕ88.03','ΠΕ88.04',
        'ΠΕ88.05','ΠΕ89.01','ΠΕ90'
    );
}

function vacanciesExportMatrixData($roundId, $scope)
{
    $roundId = (int) $roundId;
    $scope = $scope === 'special' ? 'special' : 'general';
    $round = vacanciesRound($roundId);
    if (!$round) return null;

    $schools = vacanciesQueryAll("SELECT id,ministry_code,name,school_type FROM vacancy_schools WHERE active=1 ORDER BY id");
    $schoolLabels = vacanciesExportSchoolDefinitions();
    $schoolOrder = array_keys($schoolLabels);
    $schoolOrderMap = array();
    foreach ($schoolOrder as $idx => $code) $schoolOrderMap[$code] = $idx;
    usort($schools, function ($a, $b) use ($schoolOrderMap) {
        $ac = (string) $a['ministry_code'];
        $bc = (string) $b['ministry_code'];
        $ai = isset($schoolOrderMap[$ac]) ? $schoolOrderMap[$ac] : 10000 + (int) $a['id'];
        $bi = isset($schoolOrderMap[$bc]) ? $schoolOrderMap[$bc] : 10000 + (int) $b['id'];
        if ($ai === $bi) return 0;
        return $ai < $bi ? -1 : 1;
    });
    foreach ($schools as &$school) {
        $code = (string) $school['ministry_code'];
        $school['export_label'] = isset($schoolLabels[$code]) ? $schoolLabels[$code] : (string) $school['name'];
    }
    unset($school);

    $scopeSql = vacanciesDbEscape($scope);
    $latestSql = "SELECT school_id,MAX(revision_no) rev FROM vacancy_submissions WHERE round_id=".$roundId." AND education_scope='".$scopeSql."' AND status='submitted' GROUP BY school_id";
    $submissions = vacanciesQueryAll(
        "SELECT s.id,s.school_id,s.school_note,s.submitted_at,s.revision_no " .
        "FROM vacancy_submissions s JOIN (".$latestSql.") x ON x.school_id=s.school_id AND x.rev=s.revision_no " .
        "WHERE s.round_id=".$roundId." AND s.education_scope='".$scopeSql."' AND s.status='submitted'"
    );
    $submitted = array();
    $notes = array();
    foreach ($submissions as $sub) {
        $schoolId = (int) $sub['school_id'];
        $submitted[$schoolId] = (int) $sub['id'];
        $notes[$schoolId] = array(
            'school_note' => isset($sub['school_note']) ? (string) $sub['school_note'] : '',
            'submitted_at' => isset($sub['submitted_at']) ? (string) $sub['submitted_at'] : '',
            'revision_no' => (int) $sub['revision_no'],
        );
    }

    $entries = vacanciesQueryAll(
        "SELECT s.school_id,e.specialty_id,e.balance_type,e.hours,sp.code,sp.label,sp.sort_order " .
        "FROM vacancy_submissions s JOIN (".$latestSql.") x ON x.school_id=s.school_id AND x.rev=s.revision_no " .
        "JOIN vacancy_entries e ON e.submission_id=s.id JOIN vacancy_specialties sp ON sp.id=e.specialty_id " .
        "WHERE s.round_id=".$roundId." AND s.education_scope='".$scopeSql."' AND s.status='submitted' " .
        "ORDER BY sp.sort_order,sp.code,s.school_id"
    );

    $values = array();
    $usedCodes = array();
    foreach ($entries as $entry) {
        $schoolId = (int) $entry['school_id'];
        $specialtyId = (int) $entry['specialty_id'];
        $hours = (int) $entry['hours'];
        if ($hours <= 0) continue;
        $signed = $entry['balance_type'] === 'vacancy' ? -$hours : $hours;
        if (!isset($values[$specialtyId])) $values[$specialtyId] = array();
        $values[$specialtyId][$schoolId] = $signed;
        $usedCodes[(string) $entry['code']] = true;
    }

    $allSpecialties = vacanciesQueryAll("SELECT id,code,label,sort_order FROM vacancy_specialties WHERE active=1 ORDER BY sort_order,code");
    $specialtyByCode = array();
    foreach ($allSpecialties as $sp) $specialtyByCode[(string) $sp['code']] = $sp;

    $specialties = array();
    $included = array();
    foreach (vacanciesExportLegacySpecialtyCodes() as $code) {
        if (!isset($specialtyByCode[$code])) continue;
        $sp = $specialtyByCode[$code];
        $sp['export_label'] = $code === 'SKILLS_GYM' ? 'ΔΕΞΙΟΤΗΤΕΣ ΓΥΜΝΑΣΙΟΥ' : ($code === 'TECH_GYM' ? 'ΤΕΧΝΟΛΟΓΙΑ ΓΥΜΝΑΣΙΟΥ' : $code);
        $specialties[] = $sp;
        $included[$code] = true;
    }
    foreach ($allSpecialties as $sp) {
        $code = (string) $sp['code'];
        if (isset($included[$code]) || empty($usedCodes[$code])) continue;
        $sp['export_label'] = $code;
        $specialties[] = $sp;
        $included[$code] = true;
    }

    return array(
        'round' => $round,
        'scope' => $scope,
        'schools' => $schools,
        'specialties' => $specialties,
        'submitted' => $submitted,
        'notes' => $notes,
        'values' => $values,
    );
}
