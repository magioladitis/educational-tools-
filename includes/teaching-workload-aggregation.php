<?php
/**
 * ΕΣΩΤΕΡΙΚΟ aggregation διδακτικού φόρτου ανά κλάδο/ειδικότητα.
 *
 * Αθροίζονται αριθμητικά μόνο ώρες που είναι ασφαλώς αποδοτέες σε
 * συγκεκριμένο curriculum slot. Επιλογές, variants, προϋποθέσεις,
 * περιοδικές ώρες και θεματικά blocks διατηρούνται ως ξεχωριστές
 * opportunities και δεν διογκώνουν τα fixed totals.
 *
 * Δεν φορτώνεται από δημόσια σελίδα.
 */

require_once __DIR__ . '/teaching-workload-model.php';

function teachingWorkloadAggregationIsPeCode($code)
{
    return strpos((string) $code, 'ΠΕ') === 0;
}

function teachingWorkloadAggregationCodeMatches($queryCode, $assignedCode)
{
    $queryCode = trim((string) $queryCode);
    $assignedCode = trim((string) $assignedCode);
    if ($queryCode === '' || $assignedCode === '') {
        return false;
    }
    if ($queryCode === $assignedCode) {
        return true;
    }
    // Π.χ. κανονιστική αναφορά ΠΕ87 καλύπτει συγκεκριμένο ΠΕ87.01.
    return strpos($queryCode, $assignedCode . '.') === 0;
}

function teachingWorkloadAggregationMatchingCode($queryCode, $codes)
{
    if (empty($codes) || !is_array($codes)) {
        return null;
    }
    foreach ($codes as $assignedCode) {
        if ((string) $assignedCode === (string) $queryCode) {
            return $assignedCode;
        }
    }
    foreach ($codes as $assignedCode) {
        if (teachingWorkloadAggregationCodeMatches($queryCode, $assignedCode)) {
            return $assignedCode;
        }
    }
    return null;
}

function teachingWorkloadAggregationPriorityForCode($assignment, $code)
{
    if (empty($assignment) || !is_array($assignment)) {
        return null;
    }

    foreach (array('A', 'B', 'C') as $priority) {
        $matched = teachingWorkloadAggregationMatchingCode($code, isset($assignment[$priority]) ? $assignment[$priority] : array());
        if ($matched !== null) {
            $notesKey = $priority . '_notes';
            return array(
                'priority' => $priority,
                'source_code' => $matched,
                'match_mode' => $matched === $code ? 'exact' : 'family',
                'note' => isset($assignment[$notesKey][$matched]) ? $assignment[$notesKey][$matched] : '',
            );
        }
        if ($priority === 'A' && !empty($assignment['A_all_pe']) && teachingWorkloadAggregationIsPeCode($code)) {
            return array(
                'priority' => 'A',
                'source_code' => 'ΠΕ*',
                'match_mode' => 'all_pe',
                'note' => isset($assignment['A_all_pe_note']) ? $assignment['A_all_pe_note'] : '',
            );
        }
        if ($priority === 'B' && !empty($assignment['B_all_others'])) {
            return array(
                'priority' => 'B',
                'source_code' => '*',
                'match_mode' => 'all_others',
                'note' => isset($assignment['note']) ? $assignment['note'] : '',
            );
        }
        if ($priority === 'C' && !empty($assignment['C_all_others'])) {
            return array(
                'priority' => 'C',
                'source_code' => '*',
                'match_mode' => 'all_others',
                'note' => isset($assignment['note']) ? $assignment['note'] : '',
            );
        }
    }

    $matched = teachingWorkloadAggregationMatchingCode($code, isset($assignment['special_codes']) ? $assignment['special_codes'] : array());
    if ($matched !== null) {
        return array(
            'priority' => 'SPECIAL',
            'source_code' => $matched,
            'match_mode' => $matched === $code ? 'exact' : 'family',
            'note' => isset($assignment['special_notes'][$matched])
                ? $assignment['special_notes'][$matched]
                : (isset($assignment['special_note']) ? $assignment['special_note'] : ''),
        );
    }
    if (!empty($assignment['special_all_pe']) && teachingWorkloadAggregationIsPeCode($code)) {
        return array(
            'priority' => 'SPECIAL',
            'source_code' => 'ΠΕ*',
            'match_mode' => 'all_pe',
            'note' => isset($assignment['special_note']) ? $assignment['special_note'] : '',
        );
    }
    return null;
}

function teachingWorkloadAggregationVariantScopeKey($instance)
{
    return implode('|', array(
        isset($instance['school']) ? $instance['school'] : '',
        isset($instance['grade']) ? $instance['grade'] : '',
        isset($instance['group']) ? $instance['group'] : '',
        isset($instance['track']) ? $instance['track'] : '',
        isset($instance['specialty']) ? $instance['specialty'] : '',
    ));
}

function teachingWorkloadAggregationBaseClaim($instance)
{
    $claim = array(
        'instance_id' => $instance['instance_id'],
        'course_id' => $instance['course_id'],
        'school' => $instance['school'],
        'grade' => $instance['grade'],
        'group' => isset($instance['group']) ? $instance['group'] : '',
        'subject' => $instance['subject'],
        'hours_mode' => $instance['hours_mode'],
        'hours_display' => $instance['hours_display'],
    );
    foreach (array('track', 'specialty', 'condition', 'variant', 'note') as $key) {
        if (isset($instance[$key])) {
            $claim[$key] = $instance[$key];
        }
    }
    if (isset($instance['variant'])) {
        $claim['variant_scope_key'] = teachingWorkloadAggregationVariantScopeKey($instance);
    }
    return $claim;
}

function teachingWorkloadAggregationClassifyClaim($instance)
{
    if (isset($instance['variant'])) {
        return 'variant';
    }
    if (!empty($instance['condition'])) {
        return 'condition';
    }
    if (isset($instance['hours_mode']) && $instance['hours_mode'] === 'periodic') {
        return 'periodic';
    }
    return 'fixed';
}

function teachingWorkloadAggregationAttachHours($claim, $instance, $fixedHours = null)
{
    if ($fixedHours !== null) {
        $claim['hours'] = (int) $fixedHours;
        return $claim;
    }
    if (isset($instance['hours_mode']) && $instance['hours_mode'] === 'periodic') {
        $claim['period_hours'] = $instance['period_hours'];
    } else {
        $claim['hours'] = isset($instance['hours_total']) ? (int) $instance['hours_total'] : (int) $instance['hours_value'];
    }
    return $claim;
}

function teachingWorkloadAggregationResolvedClaim($instance, $assignment, $code, $hours = null, $extra = array())
{
    $eligibility = teachingWorkloadAggregationPriorityForCode($assignment, $code);
    if ($eligibility === null) {
        return null;
    }
    $claim = teachingWorkloadAggregationBaseClaim($instance);
    $claim['category'] = teachingWorkloadAggregationClassifyClaim($instance);
    $claim['priority'] = $eligibility['priority'];
    $claim['assignment_source_code'] = $eligibility['source_code'];
    $claim['code_match_mode'] = $eligibility['match_mode'];
    if ($eligibility['note'] !== '') {
        $claim['assignment_note'] = $eligibility['note'];
    }
    $claim = teachingWorkloadAggregationAttachHours($claim, $instance, $hours);
    foreach ($extra as $key => $value) {
        $claim[$key] = $value;
    }
    return $claim;
}

function teachingWorkloadAggregationChoiceClaim($instance, $option, $code)
{
    $matches = array();
    $targets = isset($option['targets']) ? $option['targets'] : array();
    $targetCount = count($targets);
    foreach ($targets as $target) {
        if (!isset($target['assignment'])) {
            continue;
        }
        $eligibility = teachingWorkloadAggregationPriorityForCode($target['assignment'], $code);
        if ($eligibility !== null) {
            $matches[] = array(
                'subject' => isset($target['subject']) ? $target['subject'] : '',
                'priority' => $eligibility['priority'],
                'source_code' => $eligibility['source_code'],
                'match_mode' => $eligibility['match_mode'],
                'note' => $eligibility['note'],
            );
        }
    }
    if (!$matches) {
        return null;
    }

    $claim = teachingWorkloadAggregationBaseClaim($instance);
    $claim['category'] = 'choice';
    $claim['choice_label'] = isset($option['label']) ? $option['label'] : '';
    $claim['slot_hours'] = isset($option['hours_total']) ? (int) $option['hours_total'] : (int) $instance['hours_value'];
    $claim['eligible_targets'] = $matches;
    $claim['target_count'] = $targetCount;
    $claim['eligible_target_count'] = count($matches);
    $priorities = array_values(array_unique(array_map(function ($x) { return $x['priority']; }, $matches)));
    $claim['priorities'] = $priorities;
    if (!empty($instance['choice_group'])) {
        $claim['choice_group'] = $instance['choice_group'];
    }
    if (isset($instance['variant'])) {
        $claim['variant_scope_key'] = teachingWorkloadAggregationVariantScopeKey($instance);
    }

    if ($targetCount === 1 || (count($matches) === $targetCount && count($priorities) === 1)) {
        $claim['hours_attribution'] = 'full_slot_eligible';
        $claim['attributable_hours'] = $claim['slot_hours'];
        $claim['priority'] = $priorities[0];
    } else {
        $claim['hours_attribution'] = 'component_split_unknown';
    }
    return $claim;
}

function teachingWorkloadAggregationThematicClaim($instance, $code)
{
    $matches = array();
    foreach (isset($instance['thematic_assignments']) ? $instance['thematic_assignments'] : array() as $assignment) {
        $eligibility = teachingWorkloadAggregationPriorityForCode($assignment, $code);
        if ($eligibility !== null) {
            $matches[] = array(
                'subject' => isset($assignment['subject']) ? $assignment['subject'] : '',
                'priority' => $eligibility['priority'],
                'source_code' => $eligibility['source_code'],
                'match_mode' => $eligibility['match_mode'],
                'note' => $eligibility['note'],
            );
        }
    }
    if (!$matches) {
        return null;
    }
    $claim = teachingWorkloadAggregationBaseClaim($instance);
    $claim['category'] = 'thematic';
    $claim['slot_hours'] = isset($instance['hours_total']) ? (int) $instance['hours_total'] : (int) $instance['hours_value'];
    $claim['hours_attribution'] = 'not_fixed_by_regulation';
    $claim['eligible_thematic_rows'] = $matches;
    $claim['priorities'] = array_values(array_unique(array_map(function ($x) { return $x['priority']; }, $matches)));
    return $claim;
}

function teachingWorkloadClaimsForCode($code, $model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $claims = array();
    foreach ($model as $instance) {
        $status = $instance['resolution_status'];
        if ($status === 'direct' || $status === 'alias') {
            $assignment = isset($instance['assignment_resolution']['assignment']) ? $instance['assignment_resolution']['assignment'] : null;
            $claim = teachingWorkloadAggregationResolvedClaim($instance, $assignment, $code);
            if ($claim !== null) {
                $claims[] = $claim;
            }
            continue;
        }
        if ($status === 'components') {
            foreach ($instance['components'] as $component) {
                if (empty($component['assignment'])) {
                    continue;
                }
                $claim = teachingWorkloadAggregationResolvedClaim(
                    $instance,
                    $component['assignment'],
                    $code,
                    isset($component['hours']) ? (int) $component['hours'] : null,
                    array(
                        'component_kind' => isset($component['kind']) ? $component['kind'] : '',
                        'assignment_subject' => isset($component['subject']) ? $component['subject'] : '',
                    )
                );
                if ($claim !== null) {
                    $claims[] = $claim;
                }
            }
            continue;
        }
        if ($status === 'choice_dependent') {
            foreach ($instance['choice_options'] as $option) {
                $claim = teachingWorkloadAggregationChoiceClaim($instance, $option, $code);
                if ($claim !== null) {
                    $claims[] = $claim;
                }
            }
            continue;
        }
        if ($status === 'thematic_dependent') {
            $claim = teachingWorkloadAggregationThematicClaim($instance, $code);
            if ($claim !== null) {
                $claims[] = $claim;
            }
        }
        // regulatory_gap: εσκεμμένα δεν δημιουργείται claim.
    }
    return $claims;
}

function teachingWorkloadAggregateByCode($code, $model = null)
{
    $claims = teachingWorkloadClaimsForCode($code, $model);
    $fixedByPriority = array('A' => 0, 'B' => 0, 'C' => 0, 'SPECIAL' => 0);
    $fixedBySchool = array();
    $categoryCounts = array();
    $fixedClaimCount = 0;
    foreach ($claims as $claim) {
        $category = $claim['category'];
        if (!isset($categoryCounts[$category])) {
            $categoryCounts[$category] = 0;
        }
        $categoryCounts[$category]++;
        if ($category !== 'fixed' || !isset($claim['hours']) || !isset($claim['priority'])) {
            continue;
        }
        $priority = $claim['priority'];
        if (!isset($fixedByPriority[$priority])) {
            $fixedByPriority[$priority] = 0;
        }
        $fixedByPriority[$priority] += (int) $claim['hours'];
        if (!isset($fixedBySchool[$claim['school']])) {
            $fixedBySchool[$claim['school']] = array('A' => 0, 'B' => 0, 'C' => 0, 'SPECIAL' => 0, 'total' => 0);
        }
        $fixedBySchool[$claim['school']][$priority] += (int) $claim['hours'];
        $fixedBySchool[$claim['school']]['total'] += (int) $claim['hours'];
        $fixedClaimCount++;
    }
    ksort($categoryCounts);
    ksort($fixedBySchool);
    return array(
        'code' => $code,
        'fixed_hours_by_priority' => $fixedByPriority,
        'fixed_hours_total' => array_sum($fixedByPriority),
        'fixed_hours_by_school' => $fixedBySchool,
        'claim_count' => count($claims),
        'fixed_claim_count' => $fixedClaimCount,
        'category_counts' => $categoryCounts,
        'claims' => $claims,
        'semantics' => array(
            'fixed_hours_total' => 'Άθροισμα μόνο μη εναλλακτικών, μη υπό όρο, μη περιοδικών curriculum slots όπου ο κλάδος έχει ρητή ανάθεση.',
            'conditional_not_summed' => true,
            'actual_staffing_hours' => false,
        ),
    );
}

function teachingWorkloadKnownAssignmentCodes($model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $codes = array();
    $collect = function ($assignment) use (&$codes) {
        if (empty($assignment) || !is_array($assignment)) {
            return;
        }
        foreach (array('A', 'B', 'C', 'special_codes') as $key) {
            if (empty($assignment[$key]) || !is_array($assignment[$key])) {
                continue;
            }
            foreach ($assignment[$key] as $code) {
                $codes[$code] = true;
            }
        }
    };
    foreach ($model as $instance) {
        if (!empty($instance['assignment_resolution']['assignment'])) {
            $collect($instance['assignment_resolution']['assignment']);
        }
        foreach (isset($instance['components']) ? $instance['components'] : array() as $component) {
            if (!empty($component['assignment'])) {
                $collect($component['assignment']);
            }
        }
        foreach (isset($instance['choice_options']) ? $instance['choice_options'] : array() as $option) {
            foreach (isset($option['targets']) ? $option['targets'] : array() as $target) {
                if (!empty($target['assignment'])) {
                    $collect($target['assignment']);
                }
            }
        }
        foreach (isset($instance['thematic_assignments']) ? $instance['thematic_assignments'] : array() as $assignment) {
            $collect($assignment);
        }
    }
    $result = array_keys($codes);
    usort($result, 'strnatcmp');
    return $result;
}

function teachingWorkloadAggregationIndex($model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $result = array();
    foreach (teachingWorkloadKnownAssignmentCodes($model) as $code) {
        $result[$code] = teachingWorkloadAggregateByCode($code, $model);
    }
    return $result;
}

function teachingWorkloadAggregationSummary($model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $codes = teachingWorkloadKnownAssignmentCodes($model);
    $categoryCounts = array();
    $claimCount = 0;
    foreach ($codes as $code) {
        $agg = teachingWorkloadAggregateByCode($code, $model);
        $claimCount += $agg['claim_count'];
        foreach ($agg['category_counts'] as $category => $count) {
            if (!isset($categoryCounts[$category])) {
                $categoryCounts[$category] = 0;
            }
            $categoryCounts[$category] += $count;
        }
    }
    ksort($categoryCounts);
    return array(
        'known_codes' => count($codes),
        'claims_across_known_codes' => $claimCount,
        'claim_categories' => $categoryCounts,
        'regulatory_gap_instances_excluded' => 29,
    );
}
