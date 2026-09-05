<?php
/**
 * ΕΣΩΤΕΡΙΚΟ school-profile workload matrix.
 *
 * Μετατρέπει τα fixed, επιλυμένα curriculum slots ενός συγκεκριμένου
 * school profile σε πίνακα επιλεξιμότητας ανά πραγματικό κλάδο εκπαιδευτικού.
 *
 * ΚΡΙΣΙΜΗ ΣΗΜΑΣΙΟΛΟΓΙΑ:
 * - Τα top-priority eligible hours ΔΕΝ είναι τελική ανάθεση σε εκπαιδευτικό.
 * - Οι shared top-priority ώρες μπορούν να εμφανίζονται σε περισσότερους
 *   κλάδους, επειδή το ΦΕΚ δίνει ισότιμη ανάθεση.
 * - Μόνο οι exclusive top-priority ώρες είναι ώρες όπου ένας κλάδος είναι
 *   ο μοναδικός staffing-leaf κλάδος στην υψηλότερη διαθέσιμη προτεραιότητα.
 * - Regulatory gaps / unresolved dependencies δεν εισάγονται στη μήτρα.
 *
 * Δεν φορτώνεται από δημόσια σελίδα.
 */

require_once __DIR__ . '/school-profile.php';
require_once __DIR__ . '/teacher-specialties.php';

function schoolProfileWorkloadStaffingCodes()
{
    $registry = teacherSpecialties();
    $codes = array_keys($registry);
    $result = array();
    foreach ($codes as $code) {
        $isParent = false;
        foreach ($codes as $other) {
            if ($other !== $code && strpos($other, $code . '.') === 0) {
                $isParent = true;
                break;
            }
        }
        // Parent/family codes (π.χ. ΠΕ87) χρησιμοποιούνται για regulatory
        // matching, όχι ως ξεχωριστό staffing leaf όταν υπάρχουν υποκλάδοι.
        if (!$isParent) {
            $result[] = $code;
        }
    }
    usort($result, 'strnatcmp');
    return $result;
}

function schoolProfileWorkloadAssignmentUnits($profile, $model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $realized = schoolProfileRealize($profile, $model);
    $fixedSlots = array();
    foreach ($realized['slots'] as $slot) {
        if ($slot['staffing_status'] === 'fixed') {
            $fixedSlots[$slot['instance_id']] = $slot;
        }
    }

    $units = array();
    foreach ($model as $instance) {
        if (!isset($fixedSlots[$instance['instance_id']])) {
            continue;
        }
        $slot = $fixedSlots[$instance['instance_id']];
        $sections = (int) $slot['section_count'];
        if ($sections < 1) {
            continue;
        }

        $base = array(
            'instance_id' => $instance['instance_id'],
            'course_id' => $instance['course_id'],
            'school' => $instance['school'],
            'grade' => $instance['grade'],
            'group' => isset($instance['group']) ? $instance['group'] : '',
            'subject' => $instance['subject'],
            'section_count' => $sections,
        );
        foreach (array('track','specialty','slot_id','choice_set_id','variant') as $key) {
            if (isset($instance[$key])) {
                $base[$key] = $instance[$key];
            }
        }

        if ($instance['resolution_status'] === 'direct' || $instance['resolution_status'] === 'alias') {
            $assignment = isset($instance['assignment_resolution']['assignment'])
                ? $instance['assignment_resolution']['assignment'] : null;
            if (!$assignment || !isset($instance['hours_total'])) {
                continue;
            }
            $unit = $base;
            $unit['unit_id'] = $instance['instance_id'] . '|whole';
            $unit['assignment_subject'] = isset($instance['assignment_subject'])
                ? $instance['assignment_subject'] : $instance['subject'];
            $unit['hours_per_section'] = (int) $instance['hours_total'];
            $unit['school_hours'] = (int) $instance['hours_total'] * $sections;
            $unit['assignment'] = $assignment;
            $units[] = $unit;
            continue;
        }

        if ($instance['resolution_status'] === 'components') {
            $idx = 0;
            foreach ($instance['components'] as $component) {
                if (empty($component['assignment']) || !isset($component['hours'])) {
                    continue;
                }
                $idx++;
                $unit = $base;
                $kind = isset($component['kind']) ? $component['kind'] : ('component' . $idx);
                $unit['unit_id'] = $instance['instance_id'] . '|' . $kind . '|' . $idx;
                $unit['component_kind'] = $kind;
                $unit['assignment_subject'] = isset($component['subject']) ? $component['subject'] : $instance['subject'];
                $unit['hours_per_section'] = (int) $component['hours'];
                $unit['school_hours'] = (int) $component['hours'] * $sections;
                $unit['assignment'] = $component['assignment'];
                $units[] = $unit;
            }
        }
    }
    return $units;
}

function schoolProfileWorkloadUnitEligibility($assignment, $staffingCodes = null)
{
    if ($staffingCodes === null) {
        $staffingCodes = schoolProfileWorkloadStaffingCodes();
    }
    $byPriority = array('A'=>array(),'B'=>array(),'C'=>array(),'SPECIAL'=>array());
    $details = array();
    foreach ($staffingCodes as $code) {
        $match = teachingWorkloadAggregationPriorityForCode($assignment, $code);
        if ($match === null) {
            continue;
        }
        $priority = $match['priority'];
        if (!isset($byPriority[$priority])) {
            $byPriority[$priority] = array();
        }
        $byPriority[$priority][] = $code;
        $details[$code] = $match;
    }
    foreach ($byPriority as $priority => $codes) {
        usort($codes, 'strnatcmp');
        $byPriority[$priority] = array_values(array_unique($codes));
    }

    $topPriority = null;
    foreach (array('A','B','C','SPECIAL') as $priority) {
        if (!empty($byPriority[$priority])) {
            $topPriority = $priority;
            break;
        }
    }
    $topCodes = $topPriority === null ? array() : $byPriority[$topPriority];
    return array(
        'by_priority' => $byPriority,
        'details' => $details,
        'top_priority' => $topPriority,
        'top_codes' => $topCodes,
        'top_code_count' => count($topCodes),
    );
}

function schoolProfileWorkloadMatrix($profile, $model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $realized = schoolProfileRealize($profile, $model);
    $codes = schoolProfileWorkloadStaffingCodes();
    $units = schoolProfileWorkloadAssignmentUnits($profile, $model);

    $rows = array();
    foreach ($codes as $code) {
        $rows[$code] = array(
            'code' => $code,
            'label' => teacherSpecialtyLabel($code),
            'top_priority_hours' => 0,
            'exclusive_top_priority_hours' => 0,
            'shared_top_priority_hours' => 0,
            'ordered_top_priority_hours' => 0,
            'ordered_exclusive_top_priority_hours' => 0,
            'ordered_shared_top_priority_hours' => 0,
            'fallback_hours' => 0,
            'special_top_priority_hours' => 0,
            'eligible_hours_by_priority' => array('A'=>0,'B'=>0,'C'=>0,'SPECIAL'=>0),
            'top_unit_count' => 0,
            'fallback_unit_count' => 0,
            'claims' => array(),
        );
    }

    $coveredHours = 0;
    $uncoveredHours = 0;
    $exclusiveUnitHours = 0;
    $sharedUnitHours = 0;
    $specialTopUnitHours = 0;
    $orderedTopUnitHours = 0;
    $orderedExclusiveUnitHours = 0;
    $orderedSharedUnitHours = 0;
    $unitRows = array();

    foreach ($units as $unit) {
        $eligibility = schoolProfileWorkloadUnitEligibility($unit['assignment'], $codes);
        $hours = (int) $unit['school_hours'];
        $topPriority = $eligibility['top_priority'];
        $topCodes = $eligibility['top_codes'];
        $topCount = count($topCodes);

        if ($topPriority === null || $topCount < 1) {
            $uncoveredHours += $hours;
        } else {
            $coveredHours += $hours;
            if ($topCount === 1) {
                $exclusiveUnitHours += $hours;
            } else {
                $sharedUnitHours += $hours;
            }
            if ($topPriority === 'SPECIAL') {
                $specialTopUnitHours += $hours;
            } else {
                $orderedTopUnitHours += $hours;
                if ($topCount === 1) {
                    $orderedExclusiveUnitHours += $hours;
                } else {
                    $orderedSharedUnitHours += $hours;
                }
            }
        }

        $unitRow = $unit;
        unset($unitRow['assignment']);
        $unitRow['eligible_by_priority'] = $eligibility['by_priority'];
        $unitRow['top_priority'] = $topPriority;
        $unitRow['top_codes'] = $topCodes;
        $unitRow['top_code_count'] = $topCount;
        $unitRows[] = $unitRow;

        foreach ($eligibility['details'] as $code => $match) {
            if (!isset($rows[$code])) {
                continue;
            }
            $priority = $match['priority'];
            $rows[$code]['eligible_hours_by_priority'][$priority] += $hours;
            $isTop = $priority === $topPriority;
            $claim = array(
                'unit_id' => $unit['unit_id'],
                'instance_id' => $unit['instance_id'],
                'grade' => $unit['grade'],
                'subject' => $unit['subject'],
                'assignment_subject' => $unit['assignment_subject'],
                'school_hours' => $hours,
                'priority' => $priority,
                'top_priority' => $topPriority,
                'is_top_priority' => $isTop,
                'top_code_count' => $topCount,
                'match_mode' => isset($match['match_mode']) ? $match['match_mode'] : '',
                'source_code' => isset($match['source_code']) ? $match['source_code'] : '',
            );
            if (!empty($match['note'])) {
                $claim['assignment_note'] = $match['note'];
            }
            foreach (array('track','specialty','component_kind') as $key) {
                if (isset($unit[$key])) {
                    $claim[$key] = $unit[$key];
                }
            }
            $rows[$code]['claims'][] = $claim;

            if ($isTop) {
                $rows[$code]['top_priority_hours'] += $hours;
                $rows[$code]['top_unit_count']++;
                if ($topCount === 1) {
                    $rows[$code]['exclusive_top_priority_hours'] += $hours;
                } else {
                    $rows[$code]['shared_top_priority_hours'] += $hours;
                }
                if ($topPriority === 'SPECIAL') {
                    $rows[$code]['special_top_priority_hours'] += $hours;
                } else {
                    $rows[$code]['ordered_top_priority_hours'] += $hours;
                    if ($topCount === 1) {
                        $rows[$code]['ordered_exclusive_top_priority_hours'] += $hours;
                    } else {
                        $rows[$code]['ordered_shared_top_priority_hours'] += $hours;
                    }
                }
            } else {
                $rows[$code]['fallback_hours'] += $hours;
                $rows[$code]['fallback_unit_count']++;
            }
        }
    }

    // Keep only codes with at least one claim in this school profile.
    foreach (array_keys($rows) as $code) {
        if (empty($rows[$code]['claims'])) {
            unset($rows[$code]);
        }
    }
    uasort($rows, function ($a, $b) {
        if ($a['exclusive_top_priority_hours'] !== $b['exclusive_top_priority_hours']) {
            return $b['exclusive_top_priority_hours'] - $a['exclusive_top_priority_hours'];
        }
        if ($a['top_priority_hours'] !== $b['top_priority_hours']) {
            return $b['top_priority_hours'] - $a['top_priority_hours'];
        }
        return strnatcmp($a['code'], $b['code']);
    });

    $readiness = 'ready_for_eligibility_matrix';
    $readinessReasons = array();
    if (empty($units)) {
        $readiness = 'structure_only';
        $readinessReasons[] = 'No fixed realized curriculum units. Section counts and/or profile decisions are still required.';
    }
    if (!empty($realized['summary']['dependency_instances'])) {
        $readinessReasons[] = $realized['summary']['dependency_instances'] . ' active curriculum instances still depend on unresolved inputs.';
    }
    if (!empty($realized['summary']['regulatory_gap_instances'])) {
        $readinessReasons[] = $realized['summary']['regulatory_gap_instances'] . ' active curriculum instances are confirmed regulatory gaps.';
    }

    return array(
        'profile_id' => $realized['profile_id'],
        'school_year' => $realized['school_year'],
        'school' => $realized['school'],
        'readiness' => $readiness,
        'readiness_reasons' => $readinessReasons,
        'summary' => array(
            'realized_fixed_curriculum_hours' => (int) $realized['summary']['fixed_curriculum_hours'],
            'assignment_unit_count' => count($units),
            'assignment_unit_hours' => array_sum(array_map(function ($u) { return (int) $u['school_hours']; }, $units)),
            'covered_unit_hours' => $coveredHours,
            'uncovered_unit_hours' => $uncoveredHours,
            'exclusive_top_unit_hours' => $exclusiveUnitHours,
            'shared_top_unit_hours' => $sharedUnitHours,
            'special_top_unit_hours' => $specialTopUnitHours,
            'ordered_top_unit_hours' => $orderedTopUnitHours,
            'ordered_exclusive_top_unit_hours' => $orderedExclusiveUnitHours,
            'ordered_shared_top_unit_hours' => $orderedSharedUnitHours,
            'active_dependency_instances' => (int) $realized['summary']['dependency_instances'],
            'active_regulatory_gap_instances' => (int) $realized['summary']['regulatory_gap_instances'],
            'active_regulatory_gap_curriculum_hours' => (int) $realized['summary']['regulatory_gap_curriculum_hours'],
            'staffing_leaf_codes_with_claims' => count($rows),
        ),
        'codes' => $rows,
        'units' => $unitRows,
        'semantics' => array(
            'top_priority_hours' => 'Ώρες curriculum όπου ο κλάδος ανήκει στην υψηλότερη διαθέσιμη ανάθεση. Περιλαμβάνει και ειδικές μη Α΄/Β΄/Γ΄ αναθέσεις και μπορεί να επικαλύπτεται μεταξύ κλάδων.',
            'ordered_top_priority_hours' => 'Μόνο ώρες όπου η υψηλότερη διαθέσιμη ανάθεση είναι Α΄, Β΄ ή Γ΄. Οι ειδικές αναθέσεις εμφανίζονται χωριστά.',
            'exclusive_top_priority_hours' => 'Ώρες όπου ο κλάδος είναι ο μόνος staffing-leaf κλάδος στην υψηλότερη διαθέσιμη ανάθεση.',
            'shared_top_priority_hours' => 'Ώρες όπου ο κλάδος μοιράζεται την υψηλότερη διαθέσιμη ανάθεση με άλλους κλάδους.',
            'fallback_hours' => 'Ώρες όπου ο κλάδος έχει χαμηλότερη ανάθεση από άλλον διαθέσιμο κλάδο.',
            'final_teacher_allocation' => false,
            'vacancy_calculation' => false,
            'unresolved_dependencies_excluded' => true,
            'regulatory_gaps_excluded' => true,
        ),
    );
}
