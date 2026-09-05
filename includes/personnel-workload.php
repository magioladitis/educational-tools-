<?php
/**
 * ΕΣΩΤΕΡΙΚΟ personnel workload layer.
 *
 * Συνδέει πραγματικό εκπαιδευτικό με school-profile workload matrix:
 *   κλάδος -> υποχρεωτικό ωράριο -> ήδη δεσμευμένες ώρες ->
 *   συγκεκριμένα curriculum units -> Α΄/Β΄/Γ΄/ειδική ανάθεση.
 *
 * ΑΡΧΕΣ ΑΣΦΑΛΕΙΑΣ:
 * - Δεν κάνει αυτόματη τοποθέτηση / optimization.
 * - Δεν μετατρέπει eligibility σε τελική ανάθεση.
 * - Ελέγχει unit capacity σε επίπεδο roster ώστε να μην διπλομετρώνται ώρες.
 * - Regulatory gaps / unresolved school-profile dependencies δεν είναι assignable units.
 * - Οι κλάδοι ΔΕ απαιτούν ρητή κλίμακα ωραρίου όταν αυτή δεν προκύπτει
 *   με ασφάλεια μόνο από τον κωδικό ειδικότητας.
 *
 * PHP 5.6 compatible. Δεν φορτώνεται από δημόσια σελίδα.
 */

require_once __DIR__ . '/school-profile-workload.php';

function personnelWorkloadNonNegativeInt($value, $max = null)
{
    $n = (int) floor((float) $value);
    if ($n < 0) {
        $n = 0;
    }
    if ($max !== null && $n > $max) {
        $n = (int) $max;
    }
    return $n;
}

function personnelWorkloadServiceDays($years, $months, $days)
{
    $y = personnelWorkloadNonNegativeInt($years, 50);
    $m = personnelWorkloadNonNegativeInt($months, 11);
    $d = personnelWorkloadNonNegativeInt($days, 29);
    return $y * 360 + $m * 30 + $d;
}

function personnelWorkloadServiceLabel($serviceDays)
{
    $total = max(0, (int) $serviceDays);
    $years = (int) floor($total / 360);
    $remainder = $total % 360;
    $months = (int) floor($remainder / 30);
    $days = $remainder % 30;
    $label = $years . ' έτη';
    if ($months) {
        $label .= ' και ' . $months . ' μήν.';
    }
    if ($days) {
        $label .= ' και ' . $days . ' ημ.';
    }
    return $label;
}

function personnelWorkloadHoursBranchForSpecialty($specialtyCode, $explicitBranch = null)
{
    $allowed = array('PE', 'TE01', 'DE01_ARCH', 'DE01_TECH');
    if ($explicitBranch !== null && $explicitBranch !== '') {
        $explicitBranch = (string) $explicitBranch;
        return in_array($explicitBranch, $allowed, true)
            ? array('status'=>'resolved', 'branch'=>$explicitBranch, 'mode'=>'explicit')
            : array('status'=>'invalid', 'branch'=>null, 'mode'=>'explicit', 'reason'=>'unknown_hours_branch');
    }

    $code = teacherSpecialtyCanonicalCode($specialtyCode);
    if (strpos($code, 'ΠΕ') === 0) {
        return array('status'=>'resolved', 'branch'=>'PE', 'mode'=>'inferred_from_specialty');
    }
    if (strpos($code, 'ΤΕ') === 0) {
        // Η δημόσια αριθμομηχανή χρησιμοποιεί την ενιαία κλίμακα
        // «ΤΕ εργαστηριακών κλάδων (κλίμακα πρώην ΤΕ01)».
        return array('status'=>'resolved', 'branch'=>'TE01', 'mode'=>'inferred_from_specialty');
    }
    if (strpos($code, 'ΔΕ') === 0) {
        return array(
            'status'=>'needs_input',
            'branch'=>null,
            'mode'=>'not_safely_inferred',
            'reason'=>'de_hours_scale_requires_explicit_architect_or_technician',
        );
    }
    return array('status'=>'invalid', 'branch'=>null, 'mode'=>'not_safely_inferred', 'reason'=>'unsupported_specialty_for_secondary_hours');
}

function personnelWorkloadDirectorSectionsBandFromCount($sectionCount)
{
    $count = max(0, (int) $sectionCount);
    if ($count < 3) return null;
    if ($count <= 5) return '3-5';
    if ($count <= 9) return '6-9';
    if ($count <= 12) return '10-12';
    return '13+';
}

function personnelWorkloadSecondaryTeacherBaseHours($branch, $serviceDays)
{
    $days = max(0, (int) $serviceDays);
    if ($branch === 'PE') {
        if ($days <= 6 * 360) return array('hours'=>23, 'label'=>'έως 6 έτη');
        if ($days <= 12 * 360) return array('hours'=>21, 'label'=>'πάνω από 6 έως 12 έτη');
        if ($days < 20 * 360) return array('hours'=>20, 'label'=>'πάνω από 12 έως κάτω από 20 έτη');
        return array('hours'=>18, 'label'=>'20 έτη και άνω');
    }
    if ($branch === 'TE01') {
        if ($days <= 7 * 360) return array('hours'=>24, 'label'=>'έως 7 έτη');
        if ($days <= 13 * 360) return array('hours'=>21, 'label'=>'πάνω από 7 έως 13 έτη');
        if ($days < 20 * 360) return array('hours'=>20, 'label'=>'πάνω από 13 έως κάτω από 20 έτη');
        return array('hours'=>18, 'label'=>'20 έτη και άνω');
    }
    if ($branch === 'DE01_ARCH') {
        return $days < 20 * 360
            ? array('hours'=>28, 'label'=>'κάτω από 20 έτη')
            : array('hours'=>26, 'label'=>'20 έτη και άνω');
    }
    if ($branch === 'DE01_TECH') {
        return $days < 20 * 360
            ? array('hours'=>30, 'label'=>'κάτω από 20 έτη')
            : array('hours'=>28, 'label'=>'20 έτη και άνω');
    }
    return null;
}

function personnelWorkloadSecondaryObligation($person)
{
    $specialty = isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '';
    if ($specialty === '' || teacherSpecialtyInfo($specialty) === null) {
        return array('status'=>'invalid', 'valid'=>false, 'reason'=>'unknown_specialty_code');
    }

    $role = isset($person['role']) ? (string) $person['role'] : 'teacher';
    $allowedRoles = array('teacher','director','lab_director','vice_or_sector','lab_responsible','epal_ek_lab_sector');
    if (!in_array($role, $allowedRoles, true)) {
        return array('status'=>'invalid','valid'=>false,'reason'=>'unknown_secondary_role');
    }

    $service = isset($person['service']) && is_array($person['service']) ? $person['service'] : array();
    $serviceDays = personnelWorkloadServiceDays(
        isset($service['years']) ? $service['years'] : 0,
        isset($service['months']) ? $service['months'] : 0,
        isset($service['days']) ? $service['days'] : 0
    );
    $twentyYears = $serviceDays >= 20 * 360;

    // In the staffing UI an ordinary teacher supplies the already-known
    // compulsory teaching hours directly.  Presence of the key is deliberate:
    // callers that do not use the staffing UI retain the legacy calculated path.
    if ($role === 'teacher' && array_key_exists('required_teaching_hours', $person)) {
        $rawHours = trim((string) $person['required_teaching_hours']);
        if ($rawHours === '') {
            return array(
                'status'=>'needs_input',
                'valid'=>false,
                'reason'=>'required_teaching_hours_required',
                'specialty_code'=>$specialty,
                'service_days'=>$serviceDays,
                'service_label'=>personnelWorkloadServiceLabel($serviceDays),
            );
        }
        if (!preg_match('/^\d+$/', $rawHours) || (int) $rawHours < 1 || (int) $rawHours > 35) {
            return array(
                'status'=>'invalid',
                'valid'=>false,
                'reason'=>'required_teaching_hours_invalid',
                'specialty_code'=>$specialty,
                'service_days'=>$serviceDays,
                'service_label'=>personnelWorkloadServiceLabel($serviceDays),
            );
        }
        if (strpos($specialty, 'ΠΕ') === 0 && (int) $rawHours > 23) {
            return array(
                'status'=>'invalid',
                'valid'=>false,
                'reason'=>'required_teaching_hours_exceeds_pe_max',
                'specialty_code'=>$specialty,
                'service_days'=>$serviceDays,
                'service_label'=>personnelWorkloadServiceLabel($serviceDays),
            );
        }
        return array(
            'status'=>'resolved',
            'valid'=>true,
            'specialty_code'=>$specialty,
            'specialty_label'=>teacherSpecialtyLabel($specialty),
            'role'=>$role,
            'hours_branch'=>null,
            'hours_branch_mode'=>'manual_required_hours',
            'service_days'=>$serviceDays,
            'service_label'=>personnelWorkloadServiceLabel($serviceDays),
            'required_teaching_hours'=>(int) $rawHours,
            'rule'=>'Το υποχρεωτικό διδακτικό ωράριο δηλώθηκε από τον χρήστη.',
        );
    }

    // Director / vice-director hours depend on the management role and service
    // threshold, not on the PE/TE/DE branch.  Therefore a DE scale is neither
    // requested nor guessed for these roles.
    if ($role === 'director' || $role === 'vice_or_sector') {
        $hours = null;
        $rule = '';
        $extra = array();
        if ($role === 'director') {
            $sectionCount = isset($person['school_general_section_count']) ? max(0, (int) $person['school_general_section_count']) : null;
            $sections = $sectionCount !== null
                ? personnelWorkloadDirectorSectionsBandFromCount($sectionCount)
                : (isset($person['director_sections_band']) ? (string) $person['director_sections_band'] : '');
            $bases = array('3-5'=>10,'6-9'=>9,'10-12'=>7,'13+'=>5);
            if (!isset($bases[$sections])) {
                return array('status'=>'needs_input','valid'=>false,'reason'=>'director_sections_band_required');
            }
            $hours = $bases[$sections] - ($twentyYears ? 2 : 0);
            $rule = 'Διευθυντής/ντρια Γυμνασίου/Λυκείου — ' . ($sectionCount !== null ? $sectionCount . ' κανονικά τμήματα, ' : '') . 'κλίμακα ' . $sections . ($twentyYears ? ', με συμπληρωμένα 20 έτη.' : '.');
            $extra['director_sections_band'] = $sections;
            if ($sectionCount !== null) $extra['school_general_section_count'] = $sectionCount;
        } else {
            $hours = $twentyYears ? 14 : 16;
            $rule = 'Υποδιευθυντής/ντρια ή Υπεύθυνος/η Τομέα' . ($twentyYears ? ' με συμπληρωμένα 20 έτη.' : '.');
        }
        $result = array(
            'status'=>'resolved',
            'valid'=>true,
            'specialty_code'=>$specialty,
            'specialty_label'=>teacherSpecialtyLabel($specialty),
            'role'=>$role,
            'hours_branch'=>null,
            'hours_branch_mode'=>'role_specific',
            'service_days'=>$serviceDays,
            'service_label'=>personnelWorkloadServiceLabel($serviceDays),
            'required_teaching_hours'=>(int) $hours,
            'rule'=>$rule,
        );
        foreach ($extra as $key=>$value) $result[$key] = $value;
        return $result;
    }

    $branchResolution = personnelWorkloadHoursBranchForSpecialty(
        $specialty,
        isset($person['hours_branch']) ? $person['hours_branch'] : null
    );
    if ($branchResolution['status'] !== 'resolved') {
        return array(
            'status'=>$branchResolution['status'],
            'valid'=>false,
            'reason'=>$branchResolution['reason'],
            'specialty_code'=>$specialty,
            'service_days'=>$serviceDays,
            'service_label'=>personnelWorkloadServiceLabel($serviceDays),
            'hours_branch_resolution'=>$branchResolution,
        );
    }

    $branch = $branchResolution['branch'];
    $baseBand = personnelWorkloadSecondaryTeacherBaseHours($branch, $serviceDays);
    if ($baseBand === null) {
        return array('status'=>'invalid','valid'=>false,'reason'=>'unsupported_hours_branch');
    }

    $hours = null;
    $rule = '';
    $extra = array();
    if ($role === 'lab_director') {
        $hours = $twentyYears ? 8 : 10;
        $rule = 'Διευθυντής/ντρια Εργαστηριακού Κέντρου' . ($twentyYears ? ' με συμπληρωμένα 20 έτη.' : '.');
    } elseif ($role === 'lab_responsible') {
        $limit = $twentyYears ? 18 : 20;
        $hours = min($baseBand['hours'], $limit);
        $rule = 'Υπεύθυνος/η Εργαστηρίου: έως ' . $limit . ' ώρες, με εφαρμογή του μικρότερου ατομικού ωραρίου.';
        $extra['base_teacher_hours'] = $baseBand['hours'];
        $extra['role_limit'] = $limit;
    } elseif ($role === 'epal_ek_lab_sector') {
        $hours = max(18, $baseBand['hours'] - 2);
        $rule = 'Υπεύθυνος/η εργαστηρίου τομέα ή ειδικότητας Ε.Κ./ΕΠΑ.Λ.: μείωση 2 ωρών, με κατώτερο όριο 18 ώρες.';
        $extra['base_teacher_hours'] = $baseBand['hours'];
    } else {
        $hours = $baseBand['hours'];
        $rule = 'Εκπαιδευτικός — ' . $baseBand['label'] . '.';
    }

    $result = array(
        'status'=>'resolved',
        'valid'=>true,
        'specialty_code'=>$specialty,
        'specialty_label'=>teacherSpecialtyLabel($specialty),
        'role'=>$role,
        'hours_branch'=>$branch,
        'hours_branch_mode'=>$branchResolution['mode'],
        'service_days'=>$serviceDays,
        'service_label'=>personnelWorkloadServiceLabel($serviceDays),
        'required_teaching_hours'=>(int) $hours,
        'rule'=>$rule,
    );
    foreach ($extra as $key=>$value) $result[$key] = $value;
    return $result;
}

function personnelWorkloadNormalizePerson($person)
{
    $id = isset($person['person_id']) ? trim((string) $person['person_id']) : '';
    if ($id === '') {
        return array('status'=>'invalid','reason'=>'person_id_required');
    }
    $specialty = isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '';
    $obligation = personnelWorkloadSecondaryObligation($person);
    $external = isset($person['assigned_external_hours']) ? personnelWorkloadNonNegativeInt($person['assigned_external_hours']) : 0;
    $result = array(
        'status'=>$obligation['status'],
        'person_id'=>$id,
        'display_name'=>isset($person['display_name']) ? trim((string) $person['display_name']) : '',
        'specialty_code'=>$specialty,
        'specialty_label'=>teacherSpecialtyLabel($specialty),
        'assigned_external_hours'=>$external,
        'obligation'=>$obligation,
    );
    if (!$obligation['valid']) {
        $result['reason'] = isset($obligation['reason']) ? $obligation['reason'] : 'obligation_unresolved';
        return $result;
    }
    $required = (int) $obligation['required_teaching_hours'];
    $result['required_teaching_hours'] = $required;
    $result['remaining_before_profile_hours'] = max(0, $required - $external);
    $result['external_overage_hours'] = max(0, $external - $required);
    return $result;
}

function personnelWorkloadUnitIndex($matrix)
{
    $index = array();
    foreach ($matrix['units'] as $unit) {
        $index[$unit['unit_id']] = $unit;
    }
    return $index;
}

function personnelWorkloadClaimIndexForCode($matrix, $specialtyCode)
{
    $code = teacherSpecialtyCanonicalCode($specialtyCode);
    if (!isset($matrix['codes'][$code])) {
        return array();
    }
    $index = array();
    foreach ($matrix['codes'][$code]['claims'] as $claim) {
        $index[$claim['unit_id']] = $claim;
    }
    return $index;
}

function personnelWorkloadEvaluatePerson($profile, $person, $allocations = array(), $model = null, $matrix = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    if ($matrix === null) {
        $matrix = schoolProfileWorkloadMatrix($profile, $model);
    }
    $normalized = personnelWorkloadNormalizePerson($person);
    $result = array(
        'person'=>$normalized,
        'profile_id'=>$matrix['profile_id'],
        'matrix_readiness'=>$matrix['readiness'],
        'valid'=>true,
        'allocation_errors'=>array(),
        'allocation_warnings'=>array(),
        'allocations'=>array(),
        'assigned_profile_hours'=>0,
        'assigned_hours_by_priority'=>array('A'=>0,'B'=>0,'C'=>0,'SPECIAL'=>0),
        'assigned_top_priority_hours'=>0,
        'assigned_exclusive_top_hours'=>0,
        'assigned_shared_top_hours'=>0,
        'assigned_fallback_hours'=>0,
    );

    if ($normalized['status'] !== 'resolved') {
        $result['valid'] = false;
        $result['allocation_errors'][] = 'person_obligation_unresolved';
        return $result;
    }
    if ($matrix['readiness'] === 'structure_only') {
        $result['valid'] = false;
        $result['allocation_errors'][] = 'school_profile_has_no_assignable_fixed_units';
        return $result;
    }

    $unitIndex = personnelWorkloadUnitIndex($matrix);
    $claimIndex = personnelWorkloadClaimIndexForCode($matrix, $normalized['specialty_code']);
    foreach ($allocations as $i=>$allocation) {
        $unitId = isset($allocation['unit_id']) ? (string) $allocation['unit_id'] : '';
        $hours = isset($allocation['hours']) ? personnelWorkloadNonNegativeInt($allocation['hours']) : 0;
        if ($unitId === '' || $hours < 1) {
            $result['valid'] = false;
            $result['allocation_errors'][] = 'allocation_' . $i . '_requires_unit_id_and_positive_hours';
            continue;
        }
        if (!isset($unitIndex[$unitId])) {
            $result['valid'] = false;
            $result['allocation_errors'][] = 'allocation_' . $i . '_unknown_or_unresolved_unit';
            continue;
        }
        $unit = $unitIndex[$unitId];
        if ($hours > (int) $unit['school_hours']) {
            $result['valid'] = false;
            $result['allocation_errors'][] = 'allocation_' . $i . '_exceeds_unit_capacity';
            continue;
        }
        if (!isset($claimIndex[$unitId])) {
            $result['valid'] = false;
            $result['allocation_errors'][] = 'allocation_' . $i . '_specialty_not_eligible';
            continue;
        }
        $claim = $claimIndex[$unitId];
        $row = array(
            'unit_id'=>$unitId,
            'hours'=>$hours,
            'grade'=>$unit['grade'],
            'subject'=>$unit['subject'],
            'assignment_subject'=>$unit['assignment_subject'],
            'priority'=>$claim['priority'],
            'top_priority'=>$claim['top_priority'],
            'is_top_priority'=>(bool) $claim['is_top_priority'],
            'top_code_count'=>(int) $claim['top_code_count'],
            'unit_capacity_hours'=>(int) $unit['school_hours'],
        );
        if (isset($unit['track'])) $row['track'] = $unit['track'];
        if (isset($unit['specialty'])) $row['specialty'] = $unit['specialty'];
        if (isset($unit['component_kind'])) $row['component_kind'] = $unit['component_kind'];
        $result['allocations'][] = $row;
        $result['assigned_profile_hours'] += $hours;
        if (isset($result['assigned_hours_by_priority'][$claim['priority']])) {
            $result['assigned_hours_by_priority'][$claim['priority']] += $hours;
        }
        if ($claim['is_top_priority']) {
            $result['assigned_top_priority_hours'] += $hours;
            if ((int) $claim['top_code_count'] === 1) {
                $result['assigned_exclusive_top_hours'] += $hours;
            } else {
                $result['assigned_shared_top_hours'] += $hours;
            }
        } else {
            $result['assigned_fallback_hours'] += $hours;
            $result['allocation_warnings'][] = 'allocation_' . $i . '_uses_lower_priority_assignment';
        }
    }

    $required = (int) $normalized['required_teaching_hours'];
    $external = (int) $normalized['assigned_external_hours'];
    $profileHours = (int) $result['assigned_profile_hours'];
    $total = $external + $profileHours;
    $result['required_teaching_hours'] = $required;
    $result['assigned_external_hours'] = $external;
    $result['assigned_total_hours'] = $total;
    $result['remaining_hours'] = max(0, $required - $total);
    $result['overage_hours'] = max(0, $total - $required);
    $result['hours_status'] = $result['overage_hours'] > 0 ? 'over_required' : ($result['remaining_hours'] > 0 ? 'under_required' : 'exact_required');
    if ($result['overage_hours'] > 0) {
        $result['allocation_warnings'][] = 'assigned_hours_exceed_required_teaching_hours';
    }
    $result['allocation_errors'] = array_values(array_unique($result['allocation_errors']));
    $result['allocation_warnings'] = array_values(array_unique($result['allocation_warnings']));
    return $result;
}

function personnelWorkloadOpenOpportunities($matrix, $specialtyCode, $remainingByUnit = array(), $personRemainingHours = null)
{
    $code = teacherSpecialtyCanonicalCode($specialtyCode);
    if (!isset($matrix['codes'][$code])) {
        return array();
    }
    $unitIndex = personnelWorkloadUnitIndex($matrix);
    $rows = array();
    foreach ($matrix['codes'][$code]['claims'] as $claim) {
        $unitId = $claim['unit_id'];
        if (!isset($unitIndex[$unitId])) {
            continue;
        }
        $unit = $unitIndex[$unitId];
        $open = isset($remainingByUnit[$unitId])
            ? max(0, (int) $remainingByUnit[$unitId])
            : (int) $unit['school_hours'];
        if ($open < 1) {
            continue;
        }
        $row = array(
            'unit_id'=>$unitId,
            'grade'=>$unit['grade'],
            'subject'=>$unit['subject'],
            'assignment_subject'=>$unit['assignment_subject'],
            'open_unit_hours'=>$open,
            'priority'=>$claim['priority'],
            'top_priority'=>$claim['top_priority'],
            'is_top_priority'=>(bool) $claim['is_top_priority'],
            'top_code_count'=>(int) $claim['top_code_count'],
        );
        if ($personRemainingHours !== null) {
            $row['hours_within_person_remaining'] = min($open, max(0, (int) $personRemainingHours));
        }
        if (isset($unit['track'])) $row['track'] = $unit['track'];
        if (isset($unit['specialty'])) $row['specialty'] = $unit['specialty'];
        if (isset($unit['component_kind'])) $row['component_kind'] = $unit['component_kind'];
        $rows[] = $row;
    }
    $priorityOrder = array('A'=>1,'B'=>2,'C'=>3,'SPECIAL'=>4);
    usort($rows, function ($a, $b) use ($priorityOrder) {
        if ($a['is_top_priority'] !== $b['is_top_priority']) {
            return $a['is_top_priority'] ? -1 : 1;
        }
        $pa = isset($priorityOrder[$a['priority']]) ? $priorityOrder[$a['priority']] : 99;
        $pb = isset($priorityOrder[$b['priority']]) ? $priorityOrder[$b['priority']] : 99;
        if ($pa !== $pb) return $pa - $pb;
        $gradeCompare = strnatcmp($a['grade'], $b['grade']);
        if ($gradeCompare !== 0) return $gradeCompare;
        return strnatcmp($a['subject'], $b['subject']);
    });
    return $rows;
}

function personnelWorkloadRosterPlan($profile, $people, $allocations, $model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $matrix = schoolProfileWorkloadMatrix($profile, $model);
    $unitIndex = personnelWorkloadUnitIndex($matrix);

    $peopleIndex = array();
    $personErrors = array();
    foreach ($people as $person) {
        $normalized = personnelWorkloadNormalizePerson($person);
        $id = isset($normalized['person_id']) ? $normalized['person_id'] : '';
        if ($id === '') {
            $personErrors[] = 'person_without_id';
            continue;
        }
        if (isset($peopleIndex[$id])) {
            $personErrors[] = 'duplicate_person_id:' . $id;
            continue;
        }
        $peopleIndex[$id] = $person;
    }

    $byPerson = array();
    foreach ($peopleIndex as $id=>$person) {
        $byPerson[$id] = array();
    }
    $unknownPersonAllocations = array();
    foreach ($allocations as $i=>$allocation) {
        $personId = isset($allocation['person_id']) ? (string) $allocation['person_id'] : '';
        if ($personId === '' || !isset($byPerson[$personId])) {
            $unknownPersonAllocations[] = 'allocation_' . $i . '_unknown_person';
            continue;
        }
        $byPerson[$personId][] = array(
            'unit_id'=>isset($allocation['unit_id']) ? $allocation['unit_id'] : '',
            'hours'=>isset($allocation['hours']) ? $allocation['hours'] : 0,
        );
    }

    $personResults = array();
    $allAllocationErrors = $unknownPersonAllocations;
    $allAllocationWarnings = array();
    foreach ($peopleIndex as $id=>$person) {
        $evaluation = personnelWorkloadEvaluatePerson($profile, $person, $byPerson[$id], $model, $matrix);
        $personResults[$id] = $evaluation;
        foreach ($evaluation['allocation_errors'] as $error) {
            $allAllocationErrors[] = $id . ':' . $error;
        }
        foreach ($evaluation['allocation_warnings'] as $warning) {
            $allAllocationWarnings[] = $id . ':' . $warning;
        }
    }

    $unitAssigned = array();
    foreach ($unitIndex as $unitId=>$unit) {
        $unitAssigned[$unitId] = 0;
    }
    foreach ($allocations as $allocation) {
        $personId = isset($allocation['person_id']) ? (string) $allocation['person_id'] : '';
        $unitId = isset($allocation['unit_id']) ? (string) $allocation['unit_id'] : '';
        $hours = isset($allocation['hours']) ? personnelWorkloadNonNegativeInt($allocation['hours']) : 0;
        if (!isset($peopleIndex[$personId]) || !isset($unitIndex[$unitId]) || $hours < 1) {
            continue;
        }
        // Μόνο έγκυρη eligibility μετρά για κάλυψη unit.
        $claims = personnelWorkloadClaimIndexForCode($matrix, isset($peopleIndex[$personId]['specialty_code']) ? $peopleIndex[$personId]['specialty_code'] : '');
        if (!isset($claims[$unitId])) {
            continue;
        }
        $unitAssigned[$unitId] += $hours;
    }

    $units = array();
    $fullyCoveredHours = 0;
    $unassignedHours = 0;
    $overAllocatedHours = 0;
    foreach ($unitIndex as $unitId=>$unit) {
        $capacity = (int) $unit['school_hours'];
        $assigned = isset($unitAssigned[$unitId]) ? (int) $unitAssigned[$unitId] : 0;
        $remaining = max(0, $capacity - $assigned);
        $over = max(0, $assigned - $capacity);
        if ($remaining === 0 && $over === 0) {
            $fullyCoveredHours += $capacity;
        }
        $unassignedHours += $remaining;
        $overAllocatedHours += $over;
        $units[$unitId] = array(
            'unit_id'=>$unitId,
            'grade'=>$unit['grade'],
            'subject'=>$unit['subject'],
            'capacity_hours'=>$capacity,
            'assigned_hours'=>$assigned,
            'remaining_hours'=>$remaining,
            'overallocated_hours'=>$over,
            'status'=>$over > 0 ? 'overallocated' : ($remaining > 0 ? 'partially_or_unassigned' : 'fully_assigned'),
        );
    }

    $remainingByUnit = array();
    foreach ($units as $unitId=>$unitState) {
        $remainingByUnit[$unitId] = (int) $unitState['remaining_hours'];
    }
    foreach ($personResults as $personId=>&$evaluation) {
        if (!isset($evaluation['person']['specialty_code']) || !isset($evaluation['remaining_hours'])) {
            $evaluation['open_eligible_units'] = array();
            continue;
        }
        $evaluation['open_eligible_units'] = personnelWorkloadOpenOpportunities(
            $matrix,
            $evaluation['person']['specialty_code'],
            $remainingByUnit,
            $evaluation['remaining_hours']
        );
        $evaluation['open_eligible_unit_count'] = count($evaluation['open_eligible_units']);
        $topOpen = 0;
        $fallbackOpen = 0;
        foreach ($evaluation['open_eligible_units'] as $opportunity) {
            if ($opportunity['is_top_priority']) {
                $topOpen += (int) $opportunity['open_unit_hours'];
            } else {
                $fallbackOpen += (int) $opportunity['open_unit_hours'];
            }
        }
        $evaluation['open_eligible_top_hours'] = $topOpen;
        $evaluation['open_eligible_fallback_hours'] = $fallbackOpen;
    }
    unset($evaluation);

    $requiredTotal = 0;
    $externalTotal = 0;
    $profileAssignedTotal = 0;
    $remainingPersonnelTotal = 0;
    $overagePersonnelTotal = 0;
    foreach ($personResults as $evaluation) {
        if (!isset($evaluation['required_teaching_hours'])) continue;
        $requiredTotal += (int) $evaluation['required_teaching_hours'];
        $externalTotal += (int) $evaluation['assigned_external_hours'];
        $profileAssignedTotal += (int) $evaluation['assigned_profile_hours'];
        $remainingPersonnelTotal += (int) $evaluation['remaining_hours'];
        $overagePersonnelTotal += (int) $evaluation['overage_hours'];
    }

    return array(
        'profile_id'=>$matrix['profile_id'],
        'school'=>$matrix['school'],
        'matrix_readiness'=>$matrix['readiness'],
        'valid'=>empty($personErrors) && empty($allAllocationErrors) && $overAllocatedHours === 0,
        'person_errors'=>array_values(array_unique($personErrors)),
        'allocation_errors'=>array_values(array_unique($allAllocationErrors)),
        'allocation_warnings'=>array_values(array_unique($allAllocationWarnings)),
        'people'=>$personResults,
        'units'=>$units,
        'summary'=>array(
            'person_count'=>count($personResults),
            'required_teaching_hours_total'=>$requiredTotal,
            'assigned_external_hours_total'=>$externalTotal,
            'assigned_profile_hours_total'=>$profileAssignedTotal,
            'remaining_personnel_hours_total'=>$remainingPersonnelTotal,
            'personnel_overage_hours_total'=>$overagePersonnelTotal,
            'assignable_unit_hours_total'=>(int) $matrix['summary']['assignment_unit_hours'],
            'fully_covered_unit_hours'=>$fullyCoveredHours,
            'unassigned_unit_hours'=>$unassignedHours,
            'overallocated_unit_hours'=>$overAllocatedHours,
            'regulatory_gap_curriculum_hours_excluded'=>(int) $matrix['summary']['active_regulatory_gap_curriculum_hours'],
            'dependency_instances_excluded'=>(int) $matrix['summary']['active_dependency_instances'],
        ),
        'semantics'=>array(
            'automatic_placement'=>false,
            'official_vacancy_calculation'=>false,
            'eligibility_is_not_final_allocation'=>true,
            'lower_priority_assignment_is_warning_not_error'=>true,
            'unit_capacity_checked_across_roster'=>true,
            'open_eligible_units_are_overlapping_opportunities_not_additive_staffing_need'=>true,
            'regulatory_gaps_excluded'=>true,
            'unresolved_dependencies_excluded'=>true,
        ),
    );
}

/**
 * Επιστρέφει τον αριθμό κανονικών τμημάτων μιας τάξης από το school profile.
 * Δεν μετρά ομάδες ξένων γλωσσών, προσανατολισμού, Ηθικής ή πρόσθετες ομάδες split.
 */
function personnelWorkloadGeneralSectionsForGrade($profile, $grade)
{
    if (!isset($profile['structures']) || !is_array($profile['structures'])) return 0;
    foreach ($profile['structures'] as $structure) {
        if (!isset($structure['general_sections']) || !is_array($structure['general_sections'])) continue;
        if (isset($structure['general_sections'][$grade])) return max(0, (int) $structure['general_sections'][$grade]);
    }
    return 0;
}

function personnelWorkloadTrackLabel($track)
{
    $map = array(
        'humanities'=>'Ανθρωπιστικών',
        'science'=>'Θετικών',
        'science_health'=>'Θετικών / Υγείας',
        'economics_it'=>'Οικονομίας / Πληροφορικής',
    );
    return isset($map[$track]) ? $map[$track] : (string) $track;
}

/**
 * Φιλική ετικέτα για ένα πραγματικό διδακτικό slot.
 *
 * Τα κανονικά τμήματα ονομάζονται Α1, Α2, ... επειδή το profile γνωρίζει
 * μόνο το πλήθος τους. Οι επιλογές/προσανατολισμοί εμφανίζονται ως Ομάδα 1,
 * Ομάδα 2 κ.ο.κ. ώστε να μην κατασκευάζεται ψεύτικη ταυτότητα τμήματος.
 * Για πρόσθετες ομάδες split >21 επίσης δεν υποθέτουμε ποιο συγκεκριμένο
 * κανονικό τμήμα χωρίστηκε.
 */
function personnelWorkloadAllocationSlotLabel($profile, $unit, $sectionIndex)
{
    $idx = max(1, (int) $sectionIndex);
    $grade = isset($unit['grade']) ? (string) $unit['grade'] : '';
    $subject = isset($unit['subject']) ? (string) $unit['subject'] : '';

    if (isset($unit['choice_option']) && $unit['choice_option'] !== '') {
        return trim($grade . ' · ' . $unit['choice_option'] . ' · Ομάδα ' . $idx, ' ·');
    }
    $track = isset($unit['profile_track']) ? $unit['profile_track'] : (isset($unit['track']) ? $unit['track'] : '');
    if ($track !== '') {
        return trim($grade . ' · ' . personnelWorkloadTrackLabel($track) . ' · Ομάδα ' . $idx, ' ·');
    }
    if (isset($unit['slot_id']) && $unit['slot_id'] === 'gel.c.general.orientation_choice') {
        return trim($grade . ' · ' . $subject . ' Γ.Π. · Ομάδα ' . $idx, ' ·');
    }

    $general = personnelWorkloadGeneralSectionsForGrade($profile, $grade);
    if ($general > 0 && $idx <= $general) {
        $gradePlain = str_replace(array('΄','’',"'"), '', $grade);
        return trim($gradePlain) . $idx;
    }
    if ($general > 0 && $idx > $general) {
        return trim($grade . ' · πρόσθετη ομάδα χωρισμού ' . ($idx - $general), ' ·');
    }
    return trim($grade . ' · Ομάδα ' . $idx, ' ·');
}

/**
 * Σπάει κάθε aggregate workload unit σε πραγματικά slots τμήματος/ομάδας.
 * Έτσι ο έλεγχος χωρητικότητας γίνεται σε Α1/Α2/Ομάδα 1 και όχι μόνο στο
 * συνολικό άθροισμα της τάξης.
 */
function personnelWorkloadAllocationSlots($profile, $matrix = null)
{
    if ($matrix === null) $matrix = schoolProfileWorkloadMatrix($profile);
    $claimsByUnit = array();
    if (isset($matrix['codes']) && is_array($matrix['codes'])) {
        foreach ($matrix['codes'] as $code=>$codeRow) {
            if (empty($codeRow['claims'])) continue;
            foreach ($codeRow['claims'] as $claim) {
                if (!isset($claim['unit_id']) || !isset($claim['priority'])) continue;
                $uid = (string) $claim['unit_id'];
                $priority = (string) $claim['priority'];
                if (!isset($claimsByUnit[$uid])) $claimsByUnit[$uid] = array('A'=>array(),'B'=>array(),'C'=>array(),'SPECIAL'=>array());
                if (!isset($claimsByUnit[$uid][$priority])) $claimsByUnit[$uid][$priority] = array();
                $claimsByUnit[$uid][$priority][] = $code;
            }
        }
    }
    foreach ($claimsByUnit as $uid=>&$byPriority) {
        foreach ($byPriority as $priority=>&$codes) {
            usort($codes, 'strnatcmp');
            $codes = array_values(array_unique($codes));
        }
        unset($codes);
    }
    unset($byPriority);

    $slots = array();
    foreach ($matrix['units'] as $unit) {
        $count = isset($unit['section_count']) ? max(0, (int) $unit['section_count']) : 0;
        $hours = isset($unit['hours_per_section']) ? max(0, (int) $unit['hours_per_section']) : 0;
        if ($count < 1 || $hours < 1) continue;
        $uid = (string) $unit['unit_id'];
        for ($i=1; $i<=$count; $i++) {
            $slotId = $uid . '|section|' . $i;
            $row = array(
                'slot_id'=>$slotId,
                'unit_id'=>$uid,
                'section_index'=>$i,
                'grade'=>isset($unit['grade']) ? $unit['grade'] : '',
                'group'=>isset($unit['group']) ? $unit['group'] : '',
                'subject'=>isset($unit['subject']) ? $unit['subject'] : '',
                'assignment_subject'=>isset($unit['assignment_subject']) ? $unit['assignment_subject'] : (isset($unit['subject']) ? $unit['subject'] : ''),
                'slot_label'=>personnelWorkloadAllocationSlotLabel($profile, $unit, $i),
                'capacity_hours'=>$hours,
                'eligible_by_priority'=>isset($claimsByUnit[$uid]) ? $claimsByUnit[$uid] : array('A'=>array(),'B'=>array(),'C'=>array(),'SPECIAL'=>array()),
                'top_priority'=>isset($unit['top_priority']) ? $unit['top_priority'] : null,
                'top_codes'=>isset($unit['top_codes']) ? $unit['top_codes'] : array(),
            );
            foreach (array('choice_option','profile_track','track','specialty','component_kind','slot_id','choice_set_id') as $key) {
                if ($key === 'slot_id') continue;
                if (isset($unit[$key])) $row[$key] = $unit[$key];
            }
            $slots[$slotId] = $row;
        }
    }
    return $slots;
}

function personnelWorkloadPriorityForSlotCode($slot, $specialtyCode)
{
    $code = teacherSpecialtyCanonicalCode($specialtyCode);
    if ($code === '' || empty($slot['eligible_by_priority'])) return null;
    foreach (array('A','B','C','SPECIAL') as $priority) {
        if (!empty($slot['eligible_by_priority'][$priority]) && in_array($code, $slot['eligible_by_priority'][$priority], true)) return $priority;
    }
    return null;
}

/**
 * Roster validation σε επίπεδο πραγματικού slot τμήματος/ομάδας.
 * Μεταφράζει τα slot allocations στο υπάρχον aggregate personnel layer,
 * αλλά επιπλέον ελέγχει ξεχωριστά τη χωρητικότητα κάθε Α1/Α2/Ομάδας.
 */
function personnelWorkloadRosterSlotPlan($profile, $people, $slotAllocations, $model = null)
{
    if ($model === null) $model = teachingWorkloadModel();
    $matrix = schoolProfileWorkloadMatrix($profile, $model);
    $slots = personnelWorkloadAllocationSlots($profile, $matrix);

    $peopleIndex = array();
    foreach ($people as $person) {
        $id = isset($person['person_id']) ? trim((string) $person['person_id']) : '';
        if ($id !== '') $peopleIndex[$id] = $person;
    }

    $aggregate = array();
    $rowResults = array();
    $slotAssigned = array();
    foreach ($slots as $slotId=>$slot) $slotAssigned[$slotId] = 0;

    foreach ($slotAllocations as $i=>$allocation) {
        $personId = isset($allocation['person_id']) ? trim((string) $allocation['person_id']) : '';
        $slotId = isset($allocation['slot_id']) ? trim((string) $allocation['slot_id']) : '';
        $hours = isset($allocation['hours']) ? personnelWorkloadNonNegativeInt($allocation['hours']) : 0;
        $row = array(
            'row_index'=>$i,
            'person_id'=>$personId,
            'slot_id'=>$slotId,
            'hours'=>$hours,
            'valid'=>true,
            'errors'=>array(),
            'warnings'=>array(),
            'priority'=>null,
        );
        if ($personId === '' || !isset($peopleIndex[$personId])) {
            $row['valid'] = false; $row['errors'][] = 'unknown_person';
        }
        if ($slotId === '' || !isset($slots[$slotId])) {
            $row['valid'] = false; $row['errors'][] = 'unknown_slot';
        }
        if ($hours < 1) {
            $row['valid'] = false; $row['errors'][] = 'positive_hours_required';
        }
        if (isset($slots[$slotId])) {
            $slot = $slots[$slotId];
            $row['unit_id'] = $slot['unit_id'];
            $row['slot_label'] = $slot['slot_label'];
            $row['subject'] = $slot['subject'];
            $row['capacity_hours'] = (int) $slot['capacity_hours'];
            if ($hours > (int) $slot['capacity_hours']) {
                $row['valid'] = false; $row['errors'][] = 'hours_exceed_slot_capacity';
            }
            if (isset($peopleIndex[$personId])) {
                $priority = personnelWorkloadPriorityForSlotCode($slot, isset($peopleIndex[$personId]['specialty_code']) ? $peopleIndex[$personId]['specialty_code'] : '');
                $row['priority'] = $priority;
                if ($priority === null) {
                    $row['valid'] = false; $row['errors'][] = 'specialty_not_eligible';
                } elseif (isset($slot['top_priority']) && $slot['top_priority'] !== null && $priority !== $slot['top_priority']) {
                    $row['warnings'][] = 'uses_lower_priority_assignment';
                }
            }
        }
        if ($row['valid']) {
            $uid = $slots[$slotId]['unit_id'];
            $key = $personId . "\n" . $uid;
            if (!isset($aggregate[$key])) $aggregate[$key] = array('person_id'=>$personId,'unit_id'=>$uid,'hours'=>0);
            $aggregate[$key]['hours'] += $hours;
            $slotAssigned[$slotId] += $hours;
        }
        $rowResults[] = $row;
    }

    $basePlan = personnelWorkloadRosterPlan($profile, $people, array_values($aggregate), $model);
    $slotStates = array();
    $covered = 0; $unassigned = 0; $over = 0; $assignedSlotTotal = 0;
    foreach ($slots as $slotId=>$slot) {
        $capacity = (int) $slot['capacity_hours'];
        $assigned = isset($slotAssigned[$slotId]) ? (int) $slotAssigned[$slotId] : 0;
        $assignedSlotTotal += $assigned;
        $remaining = max(0, $capacity - $assigned);
        $overage = max(0, $assigned - $capacity);
        if ($remaining === 0 && $overage === 0) $covered += $capacity;
        $unassigned += $remaining;
        $over += $overage;
        $slotStates[$slotId] = array(
            'slot_id'=>$slotId,
            'unit_id'=>$slot['unit_id'],
            'slot_label'=>$slot['slot_label'],
            'grade'=>$slot['grade'],
            'subject'=>$slot['subject'],
            'capacity_hours'=>$capacity,
            'assigned_hours'=>$assigned,
            'remaining_hours'=>$remaining,
            'overallocated_hours'=>$overage,
            'status'=>$overage > 0 ? 'overallocated' : ($remaining > 0 ? 'partially_or_unassigned' : 'fully_assigned'),
        );
    }

    // Mark rows participating in an overallocated slot.
    if ($over > 0) {
        foreach ($rowResults as &$row) {
            if ($row['slot_id'] !== '' && isset($slotStates[$row['slot_id']]) && $slotStates[$row['slot_id']]['overallocated_hours'] > 0) {
                $row['valid'] = false;
                $row['errors'][] = 'slot_overallocated_across_roster';
            }
        }
        unset($row);
    }

    $invalidRows = 0;
    foreach ($rowResults as $row) if (!$row['valid']) $invalidRows++;
    $basePlan['valid'] = $basePlan['valid'] && $over === 0 && $invalidRows === 0;
    $basePlan['allocation_rows'] = $rowResults;
    $basePlan['slots'] = $slotStates;
    $basePlan['summary']['assignment_slot_count'] = count($slots);
    $basePlan['summary']['assigned_slot_hours_total'] = $assignedSlotTotal;
    $basePlan['summary']['fully_covered_slot_hours'] = $covered;
    $basePlan['summary']['unassigned_slot_hours'] = $unassigned;
    $basePlan['summary']['overallocated_slot_hours'] = $over;
    $basePlan['summary']['invalid_allocation_row_count'] = $invalidRows;
    $basePlan['semantics']['slot_capacity_checked_per_section_or_group'] = true;
    $basePlan['semantics']['manual_allocation_only'] = true;
    return $basePlan;
}
