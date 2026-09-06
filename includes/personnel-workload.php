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
    $secondarySpecialty = isset($person['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($person['secondary_specialty_code']) : '';
    $obligation = personnelWorkloadSecondaryObligation($person);
    $external = isset($person['assigned_external_hours']) ? personnelWorkloadNonNegativeInt($person['assigned_external_hours']) : 0;
    $result = array(
        'status'=>$obligation['status'],
        'person_id'=>$id,
        'display_name'=>isset($person['display_name']) ? trim((string) $person['display_name']) : '',
        'specialty_code'=>$specialty,
        'specialty_label'=>teacherSpecialtyLabel($specialty),
        'secondary_specialty_code'=>$secondarySpecialty,
        'secondary_specialty_label'=>$secondarySpecialty !== '' ? teacherSpecialtyLabel($secondarySpecialty) : '',
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
        'assigned_hours_by_specialty_source'=>array('primary'=>0,'secondary'=>0),
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
        $primaryCode = isset($normalized['specialty_code']) ? teacherSpecialtyCanonicalCode($normalized['specialty_code']) : '';
        $secondaryCode = isset($normalized['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($normalized['secondary_specialty_code']) : '';
        $usedCode = isset($allocation['used_specialty_code']) && trim((string) $allocation['used_specialty_code']) !== ''
            ? teacherSpecialtyCanonicalCode($allocation['used_specialty_code'])
            : $primaryCode;
        if ($usedCode === '' || ($usedCode !== $primaryCode && $usedCode !== $secondaryCode)) {
            $result['valid'] = false;
            $result['allocation_errors'][] = 'allocation_' . $i . '_used_specialty_not_owned';
            continue;
        }
        $claimIndex = personnelWorkloadClaimIndexForCode($matrix, $usedCode);
        if (!isset($claimIndex[$unitId])) {
            $result['valid'] = false;
            $result['allocation_errors'][] = 'allocation_' . $i . '_specialty_not_eligible';
            continue;
        }
        $claim = $claimIndex[$unitId];
        $specialtySource = $usedCode === $secondaryCode && $secondaryCode !== '' && $secondaryCode !== $primaryCode ? 'secondary' : 'primary';
        $row = array(
            'unit_id'=>$unitId,
            'hours'=>$hours,
            'grade'=>$unit['grade'],
            'subject'=>$unit['subject'],
            'assignment_subject'=>$unit['assignment_subject'],
            'priority'=>$claim['priority'],
            'used_specialty_code'=>$usedCode,
            'specialty_source'=>$specialtySource,
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
        if (isset($result['assigned_hours_by_specialty_source'][$specialtySource])) {
            $result['assigned_hours_by_specialty_source'][$specialtySource] += $hours;
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
    $result['b_assignment_hours'] = isset($result['assigned_hours_by_priority']['B']) ? (int) $result['assigned_hours_by_priority']['B'] : 0;
    $result['b_assignment_limit_hours'] = 10;
    $result['b_assignment_limit_exceeded'] = $result['b_assignment_hours'] > $result['b_assignment_limit_hours'];
    if ($result['b_assignment_limit_exceeded']) {
        // Προειδοποίηση μόνο: υπέρβαση μπορεί να επιτραπεί κατ' εξαίρεση με απόφαση ΠΥΣΔΕ.
        $result['allocation_warnings'][] = 'b_assignment_hours_exceed_10_limit';
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
            'used_specialty_code'=>isset($allocation['used_specialty_code']) ? $allocation['used_specialty_code'] : '',
            'specialty_source'=>isset($allocation['specialty_source']) ? $allocation['specialty_source'] : '',
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
        // Μόνο έγκυρη eligibility μετρά για κάλυψη unit. Στο slot layer
        // μπορεί να έχει επιλεγεί νόμιμα η 2η ειδικότητα του ίδιου προσώπου.
        $primaryCode = isset($peopleIndex[$personId]['specialty_code']) ? teacherSpecialtyCanonicalCode($peopleIndex[$personId]['specialty_code']) : '';
        $secondaryCode = isset($peopleIndex[$personId]['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($peopleIndex[$personId]['secondary_specialty_code']) : '';
        $usedCode = isset($allocation['used_specialty_code']) ? teacherSpecialtyCanonicalCode($allocation['used_specialty_code']) : $primaryCode;
        if ($usedCode === '' || ($usedCode !== $primaryCode && $usedCode !== $secondaryCode)) continue;
        $claims = personnelWorkloadClaimIndexForCode($matrix, $usedCode);
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

function personnelWorkloadPriorityRank($priority)
{
    $order = array('A'=>1, 'B'=>2, 'C'=>3, 'SPECIAL'=>4);
    return isset($order[$priority]) ? $order[$priority] : 99;
}

/**
 * Επιλέγει την καλύτερη πραγματική διαδρομή ανάθεσης για ένα slot.
 * Ελέγχει κύρια και 2η ειδικότητα χωρίς να αλλάζει τους πίνακες αναθέσεων:
 * Α΄ προηγείται Β΄, Β΄ προηγείται Γ΄ και σε ισοβαθμία προηγείται η κύρια.
 */
function personnelWorkloadBestAssignmentForSlot($slot, $person)
{
    $primary = isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '';
    $secondary = isset($person['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($person['secondary_specialty_code']) : '';
    $candidates = array();
    if ($primary !== '') {
        $priority = personnelWorkloadPriorityForSlotCode($slot, $primary);
        if ($priority !== null) $candidates[] = array(
            'priority'=>$priority,
            'used_specialty_code'=>$primary,
            'specialty_source'=>'primary',
        );
    }
    if ($secondary !== '' && $secondary !== $primary) {
        $priority = personnelWorkloadPriorityForSlotCode($slot, $secondary);
        if ($priority !== null) $candidates[] = array(
            'priority'=>$priority,
            'used_specialty_code'=>$secondary,
            'specialty_source'=>'secondary',
        );
    }
    if (empty($candidates)) return null;
    usort($candidates, function ($a, $b) {
        $rank = personnelWorkloadPriorityRank($a['priority']) - personnelWorkloadPriorityRank($b['priority']);
        if ($rank !== 0) return $rank;
        if ($a['specialty_source'] === $b['specialty_source']) return 0;
        return $a['specialty_source'] === 'primary' ? -1 : 1;
    });
    return $candidates[0];
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

    // 1ο πέρασμα: βασική εγκυρότητα γραμμής και ακατέργαστη (attempted)
    // κάλυψη ανά slot. Το attempted σύνολο χρησιμοποιείται μόνο για να
    // εντοπιστεί υπέρβαση της χωρητικότητας του ίδιου τμήματος / ομάδας.
    $rowResults = array();
    $slotAttempted = array();
    foreach ($slots as $slotId=>$slot) $slotAttempted[$slotId] = 0;

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
            'used_specialty_code'=>'',
            'specialty_source'=>'',
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
                $assignment = personnelWorkloadBestAssignmentForSlot($slot, $peopleIndex[$personId]);
                if ($assignment === null) {
                    $row['valid'] = false; $row['errors'][] = 'specialty_not_eligible';
                } else {
                    $row['priority'] = $assignment['priority'];
                    $row['used_specialty_code'] = $assignment['used_specialty_code'];
                    $row['specialty_source'] = $assignment['specialty_source'];
                    if (isset($slot['top_priority']) && $slot['top_priority'] !== null && $row['priority'] !== $slot['top_priority']) {
                        $row['warnings'][] = 'uses_lower_priority_assignment';
                    }
                }
            }
        }
        if ($row['valid']) $slotAttempted[$slotId] += $hours;
        $rowResults[] = $row;
    }

    // Αν το ίδιο slot έχει δηλωθεί πάνω από τη χωρητικότητά του, όλες οι
    // γραμμές που συμμετέχουν σε αυτή την υπέρβαση είναι άκυρες. Κρίσιμο:
    // οι άκυρες γραμμές ΔΕΝ πρέπει να μετρούν ούτε σε ώρες εκπαιδευτικού,
    // ούτε σε έγκυρη κάλυψη slot, ούτε στα κενά.
    $overallocatedSlots = array();
    $rawOver = 0;
    foreach ($slots as $slotId=>$slot) {
        $capacity = (int) $slot['capacity_hours'];
        $attempted = isset($slotAttempted[$slotId]) ? (int) $slotAttempted[$slotId] : 0;
        $overage = max(0, $attempted - $capacity);
        if ($overage > 0) {
            $overallocatedSlots[$slotId] = $overage;
            $rawOver += $overage;
        }
    }
    if (!empty($overallocatedSlots)) {
        foreach ($rowResults as &$row) {
            if ($row['slot_id'] !== '' && isset($overallocatedSlots[$row['slot_id']])) {
                $row['valid'] = false;
                if (!in_array('slot_overallocated_across_roster', $row['errors'], true)) $row['errors'][] = 'slot_overallocated_across_roster';
            }
        }
        unset($row);
    }

    // 2ο πέρασμα: χτίζουμε το πραγματικό aggregate και την έγκυρη κάλυψη
    // ΜΟΝΟ από γραμμές που παρέμειναν έγκυρες μετά τον cross-row έλεγχο.
    $aggregate = array();
    $slotAssigned = array();
    foreach ($slots as $slotId=>$slot) $slotAssigned[$slotId] = 0;
    foreach ($rowResults as $row) {
        if (!$row['valid']) continue;
        $personId = $row['person_id'];
        $slotId = $row['slot_id'];
        $hours = (int) $row['hours'];
        $uid = $slots[$slotId]['unit_id'];
        $key = $personId . "\n" . $uid . "\n" . $row['used_specialty_code'];
        if (!isset($aggregate[$key])) $aggregate[$key] = array(
            'person_id'=>$personId,
            'unit_id'=>$uid,
            'hours'=>0,
            'used_specialty_code'=>$row['used_specialty_code'],
            'specialty_source'=>$row['specialty_source'],
        );
        $aggregate[$key]['hours'] += $hours;
        $slotAssigned[$slotId] += $hours;
    }

    $basePlan = personnelWorkloadRosterPlan($profile, $people, array_values($aggregate), $model);
    $slotStates = array();
    $covered = 0; $unassigned = 0; $assignedSlotTotal = 0;
    foreach ($slots as $slotId=>$slot) {
        $capacity = (int) $slot['capacity_hours'];
        $attempted = isset($slotAttempted[$slotId]) ? (int) $slotAttempted[$slotId] : 0;
        $assigned = isset($slotAssigned[$slotId]) ? (int) $slotAssigned[$slotId] : 0;
        $assignedSlotTotal += $assigned;
        $remaining = max(0, $capacity - $assigned);
        $overage = isset($overallocatedSlots[$slotId]) ? (int) $overallocatedSlots[$slotId] : 0;
        if ($remaining === 0 && $overage === 0) $covered += $capacity;
        $unassigned += $remaining;
        $slotStates[$slotId] = array(
            'slot_id'=>$slotId,
            'unit_id'=>$slot['unit_id'],
            'slot_label'=>$slot['slot_label'],
            'grade'=>$slot['grade'],
            'subject'=>$slot['subject'],
            'capacity_hours'=>$capacity,
            'attempted_assigned_hours'=>$attempted,
            'assigned_hours'=>$assigned,
            'remaining_hours'=>$remaining,
            'overallocated_hours'=>$overage,
            'status'=>$overage > 0 ? 'overallocated' : ($remaining > 0 ? 'partially_or_unassigned' : 'fully_assigned'),
        );
    }

    // Το όριο των 10 ωρών Β΄ ανάθεσης ελέγχεται συνολικά ανά εκπαιδευτικό
    // και για τις δύο ειδικότητες. Είναι ισχυρή προειδοποίηση, όχι hard block.
    $bHoursByPerson = array();
    foreach ($peopleIndex as $personId=>$person) $bHoursByPerson[$personId] = 0;
    foreach ($rowResults as $row) {
        if ($row['valid'] && $row['priority'] === 'B' && isset($bHoursByPerson[$row['person_id']])) {
            $bHoursByPerson[$row['person_id']] += (int) $row['hours'];
        }
    }
    $peopleOverBLimit = 0;
    $bHoursOverLimitTotal = 0;
    foreach ($bHoursByPerson as $personId=>$bHours) {
        if ($bHours <= 10) continue;
        $peopleOverBLimit++;
        $bHoursOverLimitTotal += $bHours - 10;
        if (isset($basePlan['people'][$personId])) {
            $basePlan['people'][$personId]['b_assignment_hours'] = $bHours;
            $basePlan['people'][$personId]['b_assignment_limit_hours'] = 10;
            $basePlan['people'][$personId]['b_assignment_limit_exceeded'] = true;
            if (!isset($basePlan['people'][$personId]['allocation_warnings'])) $basePlan['people'][$personId]['allocation_warnings'] = array();
            $basePlan['people'][$personId]['allocation_warnings'][] = 'b_assignment_hours_exceed_10_limit';
            $basePlan['people'][$personId]['allocation_warnings'] = array_values(array_unique($basePlan['people'][$personId]['allocation_warnings']));
        }
        foreach ($rowResults as &$row) {
            if ($row['valid'] && $row['person_id'] === $personId && $row['priority'] === 'B') {
                $row['warnings'][] = 'b_assignment_hours_exceed_10_limit';
                $row['warnings'] = array_values(array_unique($row['warnings']));
            }
        }
        unset($row);
    }
    foreach ($peopleIndex as $personId=>$person) {
        if (!isset($basePlan['people'][$personId])) continue;
        if (!isset($basePlan['people'][$personId]['b_assignment_hours'])) $basePlan['people'][$personId]['b_assignment_hours'] = isset($bHoursByPerson[$personId]) ? (int) $bHoursByPerson[$personId] : 0;
        if (!isset($basePlan['people'][$personId]['b_assignment_limit_hours'])) $basePlan['people'][$personId]['b_assignment_limit_hours'] = 10;
        if (!isset($basePlan['people'][$personId]['b_assignment_limit_exceeded'])) $basePlan['people'][$personId]['b_assignment_limit_exceeded'] = $basePlan['people'][$personId]['b_assignment_hours'] > 10;
    }

    $invalidRows = 0;
    foreach ($rowResults as $row) if (!$row['valid']) $invalidRows++;
    $basePlan['valid'] = $basePlan['valid'] && $rawOver === 0 && $invalidRows === 0;
    $basePlan['allocation_rows'] = $rowResults;
    $basePlan['slots'] = $slotStates;
    $basePlan['summary']['assignment_slot_count'] = count($slots);
    $basePlan['summary']['assigned_slot_hours_total'] = $assignedSlotTotal;
    $basePlan['summary']['fully_covered_slot_hours'] = $covered;
    $basePlan['summary']['unassigned_slot_hours'] = $unassigned;
    $basePlan['summary']['overallocated_slot_hours'] = $rawOver;
    $basePlan['summary']['invalid_allocation_row_count'] = $invalidRows;
    $basePlan['summary']['people_over_b_assignment_limit_count'] = $peopleOverBLimit;
    $basePlan['summary']['b_assignment_hours_over_limit_total'] = $bHoursOverLimitTotal;
    $basePlan['semantics']['b_assignment_10_hour_limit_is_warning_only'] = true;
    $basePlan['semantics']['secondary_specialty_participates_in_slot_eligibility'] = true;
    $basePlan['semantics']['slot_capacity_checked_per_section_or_group'] = true;
    $basePlan['semantics']['invalid_slot_allocations_do_not_count_as_coverage'] = true;
    $basePlan['semantics']['manual_allocation_only'] = true;
    return $basePlan;
}

/**
 * Ειδικές γραμμές αναφοράς της ΔΔΕ που δεν πρέπει να μετατραπούν τεχνητά
 * σε έναν κλάδο. Το πραγματικό υπόδειγμα της ΔΔΕ Κέρκυρας καταγράφει
 * χωριστά τα Εργαστήρια Δεξιοτήτων και την Τεχνολογία Γυμνασίου.
 */
function personnelWorkloadReportingBucketForSlot($profile, $slot)
{
    $schoolType = isset($profile['school']['type']) ? (string) $profile['school']['type'] : '';
    $subject = isset($slot['subject']) ? trim((string) $slot['subject']) : '';
    $isGymnasium = strpos($schoolType, 'Γυμνάσιο') !== false;
    if (!$isGymnasium) return null;
    if ($subject === 'Εργαστήρια Δεξιοτήτων') {
        return array('key'=>'GYM_SKILLS','label'=>'ΔΕΞΙΟΤΗΤΕΣ ΓΥΜΝΑΣΙΟΥ');
    }
    if ($subject === 'Τεχνολογία') {
        return array('key'=>'GYM_TECHNOLOGY','label'=>'ΤΕΧΝΟΛΟΓΙΑ ΓΥΜΝΑΣΙΟΥ');
    }
    return null;
}

/**
 * Κωδικοί της καλύτερης διαθέσιμης ανάθεσης ενός slot. Δεν κατεβαίνουμε
 * σε Β΄/Γ΄ αν υπάρχει Α΄: η «έξυπνη» επιλογή λειτουργεί μόνο ανάμεσα σε
 * ισότιμες καλύτερες αναθέσεις και δεν αλλάζει την κανονιστική ιεραρχία.
 */
function personnelWorkloadTopCandidateCodesForSlot($slot)
{
    $by = isset($slot['eligible_by_priority']) && is_array($slot['eligible_by_priority'])
        ? $slot['eligible_by_priority'] : array();
    $priority = isset($slot['top_priority']) ? $slot['top_priority'] : null;
    if ($priority !== null && !empty($by[$priority])) {
        return array('priority'=>$priority, 'codes'=>array_values(array_unique($by[$priority])));
    }
    foreach (array('A','B','C','SPECIAL') as $p) {
        if (!empty($by[$p])) return array('priority'=>$p, 'codes'=>array_values(array_unique($by[$p])));
    }
    return array('priority'=>null, 'codes'=>array());
}

/**
 * Προτεινόμενη εσωτερική εξισορρόπηση πριν από τη δήλωση κενών/πλεονασμάτων.
 * Δεν αλλάζει τις χειροκίνητες κατανομές της Καρτέλας 4. Ξεκινά από αυτές
 * και προσπαθεί να καλύψει επιπλέον ώρες με το υπάρχον προσωπικό.
 *
 * Heuristic: πρώτα τα πιο «στενά» slots (λιγότεροι διαθέσιμοι εκπαιδευτικοί),
 * και μέσα σε κάθε slot Α΄ πριν Β΄ πριν Γ΄, κύρια πριν 2η ειδικότητα. Το
 * κανονικό όριο Β΄ ανάθεσης 10 ωρών τηρείται ως όριο της αυτόματης πρότασης.
 */
function personnelWorkloadAutomaticBalanceProposal($profile, $people, $slotAllocations = array(), $model = null, $basePlan = null)
{
    if ($model === null) $model = teachingWorkloadModel();
    $matrix = schoolProfileWorkloadMatrix($profile, $model);
    $slots = personnelWorkloadAllocationSlots($profile, $matrix);
    if ($basePlan === null) $basePlan = personnelWorkloadRosterSlotPlan($profile, $people, $slotAllocations, $model);

    $peopleIndex = array();
    foreach ($people as $person) {
        $id = isset($person['person_id']) ? trim((string) $person['person_id']) : '';
        if ($id === '') continue;
        $normalized = personnelWorkloadNormalizePerson($person);
        if ($normalized['status'] !== 'resolved') continue;
        $peopleIndex[$id] = $person;
    }

    $personState = array();
    foreach ($peopleIndex as $personId=>$person) {
        $evaluation = isset($basePlan['people'][$personId]) ? $basePlan['people'][$personId] : null;
        $remaining = $evaluation && isset($evaluation['remaining_hours']) ? max(0, (int) $evaluation['remaining_hours']) : 0;
        $bHours = $evaluation && isset($evaluation['b_assignment_hours']) ? max(0, (int) $evaluation['b_assignment_hours']) : 0;
        $personState[$personId] = array(
            'remaining_hours'=>$remaining,
            'b_assignment_hours'=>$bHours,
            'b_remaining_hours'=>max(0, 10 - $bHours),
            'primary_code'=>isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '',
            'secondary_code'=>isset($person['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($person['secondary_specialty_code']) : '',
        );
    }

    $slotState = array();
    foreach ($slots as $slotId=>$slot) {
        $remaining = isset($basePlan['slots'][$slotId]['remaining_hours'])
            ? max(0, (int) $basePlan['slots'][$slotId]['remaining_hours'])
            : (int) $slot['capacity_hours'];
        $slotState[$slotId] = array('remaining_hours'=>$remaining);
    }

    // Precompute legal person-slot routes and a simple flexibility count.
    $routesBySlot = array();
    $flexibility = array();
    foreach ($peopleIndex as $personId=>$person) $flexibility[$personId] = 0;
    foreach ($slots as $slotId=>$slot) {
        if ($slotState[$slotId]['remaining_hours'] < 1) continue;
        $routesBySlot[$slotId] = array();
        foreach ($peopleIndex as $personId=>$person) {
            if ($personState[$personId]['remaining_hours'] < 1) continue;
            $match = personnelWorkloadBestAssignmentForSlot($slot, $person);
            if ($match === null) continue;
            if ($match['priority'] === 'B' && $personState[$personId]['b_remaining_hours'] < 1) continue;
            $routesBySlot[$slotId][$personId] = $match;
            $flexibility[$personId]++;
        }
    }

    $slotOrder = array_keys($routesBySlot);
    usort($slotOrder, function ($a, $b) use ($routesBySlot, $slots) {
        $ca = count($routesBySlot[$a]); $cb = count($routesBySlot[$b]);
        if ($ca !== $cb) return $ca - $cb;
        $ga = isset($slots[$a]['grade']) ? $slots[$a]['grade'] : '';
        $gb = isset($slots[$b]['grade']) ? $slots[$b]['grade'] : '';
        $g = strnatcmp($ga, $gb); if ($g !== 0) return $g;
        $sa = isset($slots[$a]['subject']) ? $slots[$a]['subject'] : '';
        $sb = isset($slots[$b]['subject']) ? $slots[$b]['subject'] : '';
        $s = strnatcmp($sa, $sb); if ($s !== 0) return $s;
        return strnatcmp($a, $b);
    });

    $proposal = array();
    $covered = 0;
    foreach ($slotOrder as $slotId) {
        $slotRemaining = isset($slotState[$slotId]['remaining_hours']) ? (int) $slotState[$slotId]['remaining_hours'] : 0;
        if ($slotRemaining < 1) continue;
        $candidateIds = array_keys($routesBySlot[$slotId]);
        usort($candidateIds, function ($a, $b) use ($routesBySlot, $slotId, $flexibility) {
            $ma = $routesBySlot[$slotId][$a]; $mb = $routesBySlot[$slotId][$b];
            $rank = personnelWorkloadPriorityRank($ma['priority']) - personnelWorkloadPriorityRank($mb['priority']);
            if ($rank !== 0) return $rank;
            $fa = isset($flexibility[$a]) ? (int) $flexibility[$a] : 999999;
            $fb = isset($flexibility[$b]) ? (int) $flexibility[$b] : 999999;
            if ($fa !== $fb) return $fa - $fb; // πιο «στενός» εκπαιδευτικός πρώτα
            if ($ma['specialty_source'] !== $mb['specialty_source']) return $ma['specialty_source'] === 'primary' ? -1 : 1;
            return strnatcmp($a, $b);
        });
        foreach ($candidateIds as $personId) {
            if ($slotRemaining < 1) break;
            if (!isset($personState[$personId]) || $personState[$personId]['remaining_hours'] < 1) continue;
            $match = $routesBySlot[$slotId][$personId];
            $available = (int) $personState[$personId]['remaining_hours'];
            if ($match['priority'] === 'B') $available = min($available, (int) $personState[$personId]['b_remaining_hours']);
            if ($available < 1) continue;
            $hours = min($slotRemaining, $available);
            if ($hours < 1) continue;
            $proposal[] = array(
                'person_id'=>$personId,
                'slot_id'=>$slotId,
                'slot_label'=>isset($slots[$slotId]['slot_label']) ? $slots[$slotId]['slot_label'] : '',
                'subject'=>isset($slots[$slotId]['subject']) ? $slots[$slotId]['subject'] : '',
                'hours'=>$hours,
                'priority'=>$match['priority'],
                'used_specialty_code'=>$match['used_specialty_code'],
                'specialty_source'=>$match['specialty_source'],
            );
            $personState[$personId]['remaining_hours'] -= $hours;
            if ($match['priority'] === 'B') {
                $personState[$personId]['b_assignment_hours'] += $hours;
                $personState[$personId]['b_remaining_hours'] = max(0, 10 - $personState[$personId]['b_assignment_hours']);
            }
            $slotRemaining -= $hours;
            $covered += $hours;
        }
        $slotState[$slotId]['remaining_hours'] = max(0, $slotRemaining);
    }

    $remainingPersonnel = 0;
    foreach ($personState as $state) $remainingPersonnel += (int) $state['remaining_hours'];
    $remainingSlots = 0;
    foreach ($slotState as $state) $remainingSlots += (int) $state['remaining_hours'];

    return array(
        'allocations'=>$proposal,
        'people'=>$personState,
        'slots'=>$slotState,
        'summary'=>array(
            'auto_covered_hours'=>$covered,
            'remaining_personnel_hours'=>$remainingPersonnel,
            'remaining_slot_hours'=>$remainingSlots,
        ),
        'semantics'=>array(
            'proposal_only'=>true,
            'does_not_modify_manual_allocations'=>true,
            'constrained_slots_first_heuristic'=>true,
            'b_assignment_limit_10_respected_without_exception'=>true,
            'primary_and_secondary_specialty_used'=>true,
        ),
    );
}

/**
 * Τελική προτεινόμενη εικόνα κενών / πλεονασμάτων ανά κλάδο για δήλωση ΔΔΕ.
 * 1) κρατά τις χειροκίνητες κατανομές,
 * 2) κάνει προτεινόμενη εσωτερική εξισορρόπηση του υπάρχοντος προσωπικού,
 * 3) για τα υπόλοιπα κενά επιλέγει μόνο ανάμεσα στις καλύτερες ισότιμες
 *    αναθέσεις και προτιμά τον κλάδο που μπορεί να καλύψει τις περισσότερες
 *    από τις συνολικά ακάλυπτες ώρες.
 */
function personnelWorkloadSpecialtyBalanceReport($profile, $people, $slotAllocations = array(), $model = null)
{
    if ($model === null) $model = teachingWorkloadModel();
    $matrix = schoolProfileWorkloadMatrix($profile, $model);
    $slots = personnelWorkloadAllocationSlots($profile, $matrix);
    $basePlan = personnelWorkloadRosterSlotPlan($profile, $people, $slotAllocations, $model);
    $auto = personnelWorkloadAutomaticBalanceProposal($profile, $people, $slotAllocations, $model, $basePlan);

    $open = array();
    $coverageByCode = array();
    $exclusiveByCode = array();
    $specialBuckets = array();
    foreach ($slots as $slotId=>$slot) {
        $hours = isset($auto['slots'][$slotId]['remaining_hours']) ? max(0, (int) $auto['slots'][$slotId]['remaining_hours']) : 0;
        if ($hours < 1) continue;
        $bucket = personnelWorkloadReportingBucketForSlot($profile, $slot);
        if ($bucket !== null) {
            if (!isset($specialBuckets[$bucket['key']])) $specialBuckets[$bucket['key']] = array('key'=>$bucket['key'],'label'=>$bucket['label'],'gap_hours'=>0,'slots'=>array());
            $specialBuckets[$bucket['key']]['gap_hours'] += $hours;
            $specialBuckets[$bucket['key']]['slots'][] = array('slot_id'=>$slotId,'slot_label'=>$slot['slot_label'],'subject'=>$slot['subject'],'hours'=>$hours);
            continue;
        }
        $top = personnelWorkloadTopCandidateCodesForSlot($slot);
        $codes = $top['codes'];
        usort($codes, 'strnatcmp');
        $row = array(
            'slot_id'=>$slotId,
            'slot_label'=>isset($slot['slot_label']) ? $slot['slot_label'] : '',
            'subject'=>isset($slot['subject']) ? $slot['subject'] : '',
            'hours'=>$hours,
            'priority'=>$top['priority'],
            'candidate_codes'=>$codes,
        );
        $open[$slotId] = $row;
        foreach ($codes as $code) {
            if (!isset($coverageByCode[$code])) $coverageByCode[$code] = 0;
            $coverageByCode[$code] += $hours;
            if (count($codes) === 1) {
                if (!isset($exclusiveByCode[$code])) $exclusiveByCode[$code] = 0;
                $exclusiveByCode[$code] += $hours;
            }
        }
    }

    $recommendations = array();
    $gapByCode = array();
    // Μοναδικές καλύτερες αναθέσεις πρώτα.
    $openRows = array_values($open);
    usort($openRows, function ($a, $b) {
        $ca = count($a['candidate_codes']); $cb = count($b['candidate_codes']);
        if ($ca !== $cb) return $ca - $cb;
        if ($a['hours'] !== $b['hours']) return $b['hours'] - $a['hours'];
        $s = strnatcmp($a['subject'], $b['subject']); if ($s !== 0) return $s;
        return strnatcmp($a['slot_id'], $b['slot_id']);
    });
    foreach ($openRows as $row) {
        $codes = $row['candidate_codes'];
        $selected = '';
        if (!empty($codes)) {
            usort($codes, function ($a, $b) use ($coverageByCode, $exclusiveByCode, $gapByCode) {
                $ca = isset($coverageByCode[$a]) ? (int) $coverageByCode[$a] : 0;
                $cb = isset($coverageByCode[$b]) ? (int) $coverageByCode[$b] : 0;
                if ($ca !== $cb) return $cb - $ca;
                $ga = isset($gapByCode[$a]) ? (int) $gapByCode[$a] : 0;
                $gb = isset($gapByCode[$b]) ? (int) $gapByCode[$b] : 0;
                if ($ga !== $gb) return $gb - $ga; // συγκέντρωση σε ήδη αναγκαίο κλάδο
                $ea = isset($exclusiveByCode[$a]) ? (int) $exclusiveByCode[$a] : 0;
                $eb = isset($exclusiveByCode[$b]) ? (int) $exclusiveByCode[$b] : 0;
                if ($ea !== $eb) return $eb - $ea;
                return strnatcmp($a, $b);
            });
            $selected = $codes[0];
            if (!isset($gapByCode[$selected])) $gapByCode[$selected] = 0;
            $gapByCode[$selected] += (int) $row['hours'];
        }
        $recommendations[] = array(
            'slot_id'=>$row['slot_id'],
            'slot_label'=>$row['slot_label'],
            'subject'=>$row['subject'],
            'hours'=>(int) $row['hours'],
            'priority'=>$row['priority'],
            'selected_code'=>$selected,
            'candidate_codes'=>$row['candidate_codes'],
            'selected_code_total_reachable_hours'=>$selected !== '' && isset($coverageByCode[$selected]) ? (int) $coverageByCode[$selected] : 0,
            'selection_kind'=>count($row['candidate_codes']) <= 1 ? 'unique_top_assignment' : 'smart_shared_top_assignment',
        );
    }

    // Πλεόνασμα: το υπόλοιπο ωραρίου κάθε προσώπου ανήκει στον βασικό κλάδο του.
    $surplusByCode = array();
    $surplusPeopleByCode = array();
    foreach ($auto['people'] as $personId=>$state) {
        $hours = isset($state['remaining_hours']) ? max(0, (int) $state['remaining_hours']) : 0;
        $code = isset($state['primary_code']) ? teacherSpecialtyCanonicalCode($state['primary_code']) : '';
        if ($hours < 1 || $code === '') continue;
        if (!isset($surplusByCode[$code])) $surplusByCode[$code] = 0;
        if (!isset($surplusPeopleByCode[$code])) $surplusPeopleByCode[$code] = 0;
        $surplusByCode[$code] += $hours;
        $surplusPeopleByCode[$code]++;
    }

    $codes = array_values(array_unique(array_merge(array_keys($gapByCode), array_keys($surplusByCode))));
    usort($codes, 'strnatcmp');
    $bySpecialty = array();
    $gapTotal = 0; $surplusTotal = 0;
    foreach ($codes as $code) {
        $gap = isset($gapByCode[$code]) ? (int) $gapByCode[$code] : 0;
        $surplus = isset($surplusByCode[$code]) ? (int) $surplusByCode[$code] : 0;
        $gapTotal += $gap; $surplusTotal += $surplus;
        $bySpecialty[$code] = array(
            'code'=>$code,
            'label'=>teacherSpecialtyLabel($code),
            'gap_hours'=>$gap,
            'surplus_hours'=>$surplus,
            'signed_balance_hours'=>$surplus - $gap,
            'has_both_gap_and_surplus'=>$gap > 0 && $surplus > 0,
            'surplus_people_count'=>isset($surplusPeopleByCode[$code]) ? (int) $surplusPeopleByCode[$code] : 0,
        );
    }

    $bucketGapTotal = 0;
    foreach ($specialBuckets as $bucket) $bucketGapTotal += (int) $bucket['gap_hours'];

    return array(
        'valid'=>$basePlan['valid'],
        'base_plan'=>$basePlan,
        'automatic_balance'=>$auto,
        'vacancy_recommendations'=>$recommendations,
        'by_specialty'=>$bySpecialty,
        'special_reporting_buckets'=>$specialBuckets,
        'summary'=>array(
            'manual_unassigned_hours'=>isset($basePlan['summary']['unassigned_slot_hours']) ? (int) $basePlan['summary']['unassigned_slot_hours'] : 0,
            'auto_internal_covered_hours'=>(int) $auto['summary']['auto_covered_hours'],
            'final_uncovered_hours'=>(int) $auto['summary']['remaining_slot_hours'],
            'specialty_gap_hours_total'=>$gapTotal,
            'special_reporting_bucket_gap_hours_total'=>$bucketGapTotal,
            'surplus_hours_total'=>$surplusTotal,
            'specialty_gap_count'=>count(array_filter($gapByCode, function ($x) { return $x > 0; })),
            'specialty_surplus_count'=>count(array_filter($surplusByCode, function ($x) { return $x > 0; })),
        ),
        'semantics'=>array(
            'official_vacancy_calculation'=>false,
            'smart_choice_only_among_equal_best_assignment_codes'=>true,
            'smart_choice_prefers_code_with_widest_top_assignment_gap_coverage'=>true,
            'existing_staff_auto_balance_is_proposal_only'=>true,
            'second_specialty_used_for_internal_balance'=>true,
            'surplus_reported_under_primary_specialty'=>true,
            'gymnasium_skills_and_technology_reported_separately'=>true,
            'b_assignment_exception_not_assumed_by_automatic_balance'=>true,
        ),
    );
}
