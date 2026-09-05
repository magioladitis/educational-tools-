<?php
/**
 * ΕΣΩΤΕΡΙΚΟ school-profile layer.
 *
 * Μετατρέπει το κανονιστικό catalog (ωρολόγιο + αναθέσεις) σε πραγματική
 * αποτύπωση συγκεκριμένης σχολικής μονάδας, όταν είναι γνωστά τα τμήματα,
 * οι τομείς/ειδικότητες και οι πραγματικές επιλογές μαθημάτων.
 *
 * Δεν φορτώνεται από δημόσια σελίδα.
 */

require_once __DIR__ . '/teaching-workload-aggregation.php';
require_once __DIR__ . '/ethics-class-formation.php';

function schoolProfileStructure($profile, $schoolCode)
{
    return isset($profile['structures'][$schoolCode]) && is_array($profile['structures'][$schoolCode])
        ? $profile['structures'][$schoolCode]
        : null;
}

function schoolProfileGeneralSectionCount($structure, $grade)
{
    return isset($structure['general_sections'][$grade]) ? max(0, (int) $structure['general_sections'][$grade]) : 0;
}


function schoolProfileChoiceOptionSections($structure, $grade, $courseId)
{
    if (!isset($structure['choice_option_sections'][$grade][$courseId])
        || !is_array($structure['choice_option_sections'][$grade][$courseId])) {
        return null;
    }
    $result = array();
    foreach ($structure['choice_option_sections'][$grade][$courseId] as $label => $count) {
        $result[(string) $label] = max(0, (int) $count);
    }
    return $result;
}

function schoolProfileExplicitChoiceSectionCount($structure, $grade, $setId, $courseId)
{
    if (!isset($structure['choice_sections'][$grade][$setId])
        || !array_key_exists($courseId, $structure['choice_sections'][$grade][$setId])) {
        return null;
    }
    return max(0, (int) $structure['choice_sections'][$grade][$setId][$courseId]);
}

function schoolProfileConditionalSectionCount($structure, $grade, $courseId)
{
    if (!isset($structure['conditional_sections'][$grade])
        || !array_key_exists($courseId, $structure['conditional_sections'][$grade])) {
        return null;
    }
    return max(0, (int) $structure['conditional_sections'][$grade][$courseId]);
}

function schoolProfileEthicsInputsForGrade($profile, $schoolCode, $grade)
{
    if (!isset($profile['ethics']['by_structure_grade'][$schoolCode][$grade])
        || !is_array($profile['ethics']['by_structure_grade'][$schoolCode][$grade])) {
        return null;
    }
    return $profile['ethics']['by_structure_grade'][$schoolCode][$grade];
}

function schoolProfileEthicsEvaluationForGrade($profile, $schoolCode, $grade)
{
    $structure = schoolProfileStructure($profile, $schoolCode);
    if ($structure === null) {
        return null;
    }
    $inputs = schoolProfileEthicsInputsForGrade($profile, $schoolCode, $grade);
    if ($inputs === null) {
        return null;
    }
    $sectionCount = schoolProfileGeneralSectionCount($structure, $grade);
    $exempt = array_key_exists('exempt_students', $inputs) ? $inputs['exempt_students'] : null;
    $within = array_key_exists('within_fifth_day', $inputs) ? $inputs['within_fifth_day'] : null;
    $equivalent = array_key_exists('equivalent_ethics_sections', $inputs)
        ? $inputs['equivalent_ethics_sections'] : null;
    return ethicsClassFormationEvaluate($schoolCode, $sectionCount, $exempt, $within, $equivalent);
}

function schoolProfileEthicsSectionCounts($profile, $schoolCode, $grade)
{
    $structure = schoolProfileStructure($profile, $schoolCode);
    if ($structure === null) {
        return null;
    }
    $general = schoolProfileGeneralSectionCount($structure, $grade);
    $evaluation = schoolProfileEthicsEvaluationForGrade($profile, $schoolCode, $grade);
    if ($evaluation === null) {
        return null;
    }

    $resolvedStatuses = array(
        'fallback_article_22_3',
        'parallel_single_section',
        'dedicated_equivalent_sections',
        'consolidated_parallel',
    );
    if (!in_array(isset($evaluation['status']) ? $evaluation['status'] : '', $resolvedStatuses, true)) {
        return null;
    }

    $ethicsGroups = isset($evaluation['ethics_groups']) ? max(0, (int) $evaluation['ethics_groups']) : 0;
    $religionGroups = $general;
    if ($evaluation['status'] === 'dedicated_equivalent_sections') {
        $religionGroups = max(0, $general - $ethicsGroups);
    }

    return array(
        'religion_groups' => $religionGroups,
        'ethics_groups' => $ethicsGroups,
        'evaluation' => $evaluation,
    );
}

function schoolProfileSectionCountForInstance($profile, $instance)
{
    $structure = schoolProfileStructure($profile, $instance['school']);
    if ($structure === null) {
        return 0;
    }
    $grade = $instance['grade'];

    if (!empty($instance['slot_id']) && preg_match('/religion_ethics$/', $instance['slot_id'])) {
        $counts = schoolProfileEthicsSectionCounts($profile, $instance['school'], $grade);
        if ($counts !== null) {
            if ($instance['subject'] === 'Ηθική') {
                return $counts['ethics_groups'];
            }
            if ($instance['subject'] === 'Θρησκευτικά') {
                return $counts['religion_groups'];
            }
        }
        // Μέχρι να λυθούν τα inputs κρατάμε το slot ορατό ως dependency.
        return schoolProfileGeneralSectionCount($structure, $grade);
    }

    if (!empty($instance['choice_set_id'])) {
        $setId = $instance['choice_set_id'];
        if (isset($structure['choice_sections'][$grade][$setId])
            && is_array($structure['choice_sections'][$grade][$setId])) {
            $explicit = schoolProfileExplicitChoiceSectionCount(
                $structure,
                $grade,
                $setId,
                $instance['course_id']
            );
            // Διατηρούμε την παλιά σημασιολογία των choice sets: όταν το
            // set έχει δηλωθεί στο profile αλλά ένα course_id απουσιάζει,
            // το συγκεκριμένο μάθημα δεν λειτουργεί (0), δεν πέφτει στα
            // general_sections.
            return $explicit === null ? 0 : $explicit;
        }
    }

    if ($instance['resolution_status'] === 'choice_dependent') {
        $optionSections = schoolProfileChoiceOptionSections($structure, $grade, $instance['course_id']);
        if ($optionSections !== null) {
            return array_sum($optionSections);
        }
    }

    if (!empty($instance['condition'])) {
        $conditional = schoolProfileConditionalSectionCount($structure, $grade, $instance['course_id']);
        if ($conditional !== null) {
            return $conditional;
        }
    }

    if (!empty($instance['specialty'])) {
        $track = isset($instance['track']) ? $instance['track'] : '';
        return isset($structure['specialty_sections'][$grade][$track][$instance['specialty']])
            ? max(0, (int) $structure['specialty_sections'][$grade][$track][$instance['specialty']])
            : 0;
    }

    if (!empty($instance['track'])) {
        return isset($structure['track_sections'][$grade][$instance['track']])
            ? max(0, (int) $structure['track_sections'][$grade][$instance['track']])
            : 0;
    }

    if (!empty($instance['profile_track'])) {
        return isset($structure['track_sections'][$grade][$instance['profile_track']])
            ? max(0, (int) $structure['track_sections'][$grade][$instance['profile_track']])
            : 0;
    }

    return schoolProfileGeneralSectionCount($structure, $grade);
}

function schoolProfileDependencyState($profile, $instance)
{
    $structure = schoolProfileStructure($profile, $instance['school']);
    if ($structure === null) {
        return array('status' => 'structure_not_active', 'resolved' => false, 'include' => false);
    }

    if (!empty($instance['slot_id']) && preg_match('/religion_ethics$/', $instance['slot_id'])) {
        $scope = ethicsClassFormationScopeStatus($instance['school']);
        if ($scope !== 'in_scope') {
            return array(
                'status' => 'ethics_policy_scope_not_confirmed',
                'resolved' => false,
                'include' => true,
                'scope_status' => $scope,
            );
        }
        $counts = schoolProfileEthicsSectionCounts($profile, $instance['school'], $instance['grade']);
        if ($counts === null) {
            $evaluation = schoolProfileEthicsEvaluationForGrade($profile, $instance['school'], $instance['grade']);
            return array(
                'status' => $evaluation === null ? 'ethics_inputs_required' : $evaluation['status'],
                'resolved' => false,
                'include' => true,
                'scope_status' => $scope,
            );
        }
        $count = $instance['subject'] === 'Ηθική' ? $counts['ethics_groups'] : $counts['religion_groups'];
        return array(
            'status' => 'ethics_' . $counts['evaluation']['status'],
            'resolved' => true,
            'include' => $count > 0,
            'scope_status' => $scope,
            'ethics_evaluation' => $counts['evaluation'],
        );
    }

    if ($instance['resolution_status'] === 'choice_dependent') {
        $optionSections = schoolProfileChoiceOptionSections($structure, $instance['grade'], $instance['course_id']);
        if ($optionSections === null) {
            return array('status' => 'choice_option_sections_required', 'resolved' => false, 'include' => true);
        }
        return array(
            'status' => array_sum($optionSections) > 0 ? 'choice_options_selected' : 'choice_options_not_active',
            'resolved' => true,
            'include' => array_sum($optionSections) > 0,
            'choice_option_sections' => $optionSections,
        );
    }

    if (!empty($instance['variant'])) {
        $grade = $instance['grade'];
        $track = isset($instance['track']) ? $instance['track'] : '';
        if (!isset($structure['variants'][$grade][$track])) {
            return array('status' => 'variant_selection_required', 'resolved' => false, 'include' => true);
        }
        $selected = $structure['variants'][$grade][$track];
        return array(
            'status' => $selected === $instance['variant'] ? 'variant_selected' : 'variant_not_selected',
            'resolved' => true,
            'include' => $selected === $instance['variant'],
        );
    }

    if (!empty($instance['condition'])) {
        if (!empty($instance['choice_set_id'])) {
            $setId = $instance['choice_set_id'];
            if (isset($structure['choice_sections'][$instance['grade']][$setId])
                && is_array($structure['choice_sections'][$instance['grade']][$setId])) {
                $explicit = schoolProfileExplicitChoiceSectionCount(
                    $structure,
                    $instance['grade'],
                    $setId,
                    $instance['course_id']
                );
                $explicit = $explicit === null ? 0 : $explicit;
                return array(
                    'status' => $explicit > 0 ? 'choice_section_selected' : 'choice_section_not_selected',
                    'resolved' => true,
                    'include' => $explicit > 0,
                );
            }
        }
        $conditional = schoolProfileConditionalSectionCount($structure, $instance['grade'], $instance['course_id']);
        if ($conditional !== null) {
            return array(
                'status' => $conditional > 0 ? 'conditional_sections_selected' : 'conditional_sections_not_active',
                'resolved' => true,
                'include' => $conditional > 0,
            );
        }
        $key = $instance['instance_id'];
        if (!array_key_exists($key, isset($structure['conditions']) ? $structure['conditions'] : array())) {
            return array('status' => 'condition_input_required', 'resolved' => false, 'include' => true);
        }
        $active = !empty($structure['conditions'][$key]);
        return array('status' => $active ? 'condition_met' : 'condition_not_met', 'resolved' => true, 'include' => $active);
    }

    if ($instance['resolution_status'] === 'thematic_dependent') {
        return array('status' => 'thematic_hours_not_fixed', 'resolved' => false, 'include' => true);
    }

    return array('status' => 'resolved', 'resolved' => true, 'include' => true);
}

function schoolProfileRealize($profile, $model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $slots = array();
    $summary = array(
        'catalog_instances_considered' => 0,
        'active_instances' => 0,
        'fixed_staffing_instances' => 0,
        'dependency_instances' => 0,
        'regulatory_gap_instances' => 0,
        'fixed_curriculum_hours' => 0,
        'regulatory_gap_curriculum_hours' => 0,
    );

    foreach ($model as $instance) {
        if (schoolProfileStructure($profile, $instance['school']) === null) {
            continue;
        }
        $summary['catalog_instances_considered']++;
        $sectionCount = schoolProfileSectionCountForInstance($profile, $instance);
        if ($sectionCount < 1) {
            continue;
        }

        $dependency = schoolProfileDependencyState($profile, $instance);
        if (empty($dependency['include'])) {
            continue;
        }

        $slot = array(
            'instance_id' => $instance['instance_id'],
            'course_id' => $instance['course_id'],
            'school' => $instance['school'],
            'grade' => $instance['grade'],
            'group' => $instance['group'],
            'subject' => $instance['subject'],
            'section_count' => $sectionCount,
            'hours_mode' => $instance['hours_mode'],
            'hours_per_section' => isset($instance['hours_total']) ? $instance['hours_total'] : null,
            'resolution_status' => $instance['resolution_status'],
            'dependency' => $dependency,
        );
        foreach (array('track','profile_track','profile_choice_id','specialty','slot_id','choice_set_id','variant') as $key) {
            if (isset($instance[$key])) {
                $slot[$key] = $instance[$key];
            }
        }
        if (isset($instance['period_hours'])) {
            $slot['period_hours'] = $instance['period_hours'];
        }

        $isRegulatoryGap = $instance['resolution_status'] === 'regulatory_gap';
        $isFixedHours = $instance['hours_mode'] === 'fixed' && isset($instance['hours_total']);
        $dependencyResolved = !empty($dependency['resolved']);

        if ($isFixedHours) {
            $slot['curriculum_hours'] = (int) $instance['hours_total'] * $sectionCount;
        }

        if ($isRegulatoryGap) {
            $slot['staffing_status'] = 'regulatory_gap';
            if (isset($slot['curriculum_hours'])) {
                $summary['regulatory_gap_curriculum_hours'] += $slot['curriculum_hours'];
            }
            $summary['regulatory_gap_instances']++;
        } elseif (!$isFixedHours) {
            $slot['staffing_status'] = 'periodic_hours';
            $summary['dependency_instances']++;
        } elseif (!$dependencyResolved) {
            $slot['staffing_status'] = 'dependency_unresolved';
            $summary['dependency_instances']++;
        } elseif ($instance['resolution_status'] === 'thematic_dependent') {
            $slot['staffing_status'] = 'thematic_hours_not_fixed';
            $summary['dependency_instances']++;
        } else {
            $slot['staffing_status'] = 'fixed';
            $summary['fixed_staffing_instances']++;
            $summary['fixed_curriculum_hours'] += $slot['curriculum_hours'];
        }

        $slots[] = $slot;
        $summary['active_instances']++;
    }

    return array(
        'profile_id' => isset($profile['profile_id']) ? $profile['profile_id'] : '',
        'school_year' => isset($profile['school_year']) ? $profile['school_year'] : '',
        'school' => isset($profile['school']) ? $profile['school'] : array(),
        'source' => isset($profile['source']) ? $profile['source'] : array(),
        'summary' => $summary,
        'slots' => $slots,
    );
}

function schoolProfileAggregateByCode($profile, $code, $model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $realized = schoolProfileRealize($profile, $model);
    $slotMap = array();
    foreach ($realized['slots'] as $slot) {
        if ($slot['staffing_status'] === 'fixed') {
            $slotMap[$slot['instance_id']] = $slot;
        }
    }

    $catalog = teachingWorkloadAggregateByCode($code, $model);
    $claims = array();
    $totals = array('A' => 0, 'B' => 0, 'C' => 0, 'SPECIAL' => 0);
    foreach ($catalog['claims'] as $claim) {
        if (!isset($slotMap[$claim['instance_id']])) {
            continue;
        }
        $slot = $slotMap[$claim['instance_id']];
        if (!isset($claim['hours'])) {
            continue;
        }
        $hours = (int) $claim['hours'] * (int) $slot['section_count'];
        $priority = isset($claim['priority']) ? $claim['priority'] : '';
        if (isset($totals[$priority])) {
            $totals[$priority] += $hours;
        }
        $row = $claim;
        $row['school_section_count'] = $slot['section_count'];
        $row['school_hours'] = $hours;
        $claims[] = $row;
    }

    return array(
        'profile_id' => $realized['profile_id'],
        'code' => $code,
        'fixed_hours_by_priority' => $totals,
        'fixed_hours_total' => array_sum($totals),
        'claims' => $claims,
        'semantics' => array(
            'school_specific' => true,
            'based_on_profile' => true,
            'actual_teacher_assignment' => false,
            'unresolved_dependencies_excluded' => true,
        ),
    );
}
