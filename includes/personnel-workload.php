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

    $service = isset($person['service']) && is_array($person['service']) ? $person['service'] : array();
    $serviceDays = personnelWorkloadServiceDays(
        isset($service['years']) ? $service['years'] : 0,
        isset($service['months']) ? $service['months'] : 0,
        isset($service['days']) ? $service['days'] : 0
    );
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
    $role = isset($person['role']) ? (string) $person['role'] : 'teacher';
    $allowedRoles = array('teacher','director','lab_director','vice_or_sector','lab_responsible','epal_ek_lab_sector');
    if (!in_array($role, $allowedRoles, true)) {
        return array('status'=>'invalid','valid'=>false,'reason'=>'unknown_secondary_role');
    }

    $twentyYears = $serviceDays >= 20 * 360;
    $baseBand = personnelWorkloadSecondaryTeacherBaseHours($branch, $serviceDays);
    if ($baseBand === null) {
        return array('status'=>'invalid','valid'=>false,'reason'=>'unsupported_hours_branch');
    }

    $hours = null;
    $rule = '';
    $extra = array();
    if ($role === 'director') {
        $sections = isset($person['director_sections_band']) ? (string) $person['director_sections_band'] : '';
        $bases = array('3-5'=>10,'6-9'=>9,'10-12'=>7,'13+'=>5);
        if (!isset($bases[$sections])) {
            return array('status'=>'needs_input','valid'=>false,'reason'=>'director_sections_band_required');
        }
        $hours = $bases[$sections] - ($twentyYears ? 2 : 0);
        $rule = 'Διευθυντής/ντρια Γυμνασίου/Λυκείου — κλίμακα τμημάτων ' . $sections . ($twentyYears ? ', με συμπληρωμένα 20 έτη.' : '.');
        $extra['director_sections_band'] = $sections;
    } elseif ($role === 'lab_director') {
        $hours = $twentyYears ? 8 : 10;
        $rule = 'Διευθυντής/ντρια Εργαστηριακού Κέντρου' . ($twentyYears ? ' με συμπληρωμένα 20 έτη.' : '.');
    } elseif ($role === 'vice_or_sector') {
        $hours = $twentyYears ? 14 : 16;
        $rule = 'Υποδιευθυντής/ντρια ή Υπεύθυνος/η Τομέα' . ($twentyYears ? ' με συμπληρωμένα 20 έτη.' : '.');
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
    foreach ($extra as $key=>$value) {
        $result[$key] = $value;
    }
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
