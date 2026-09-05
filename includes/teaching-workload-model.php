<?php
/**
 * ΕΣΩΤΕΡΙΚΟ μοντέλο διδακτικού φόρτου.
 *
 * Ενώνει, χωρίς καμία αλλαγή στο public UI:
 *   ωρολόγιο πρόγραμμα -> πραγματική εβδομαδιαία ώρα -> αναθέσεις Α΄/Β΄/Γ΄
 *
 * Το μοντέλο είναι συντηρητικό:
 * - δεν μαντεύει ανάθεση όταν υπάρχει regulatory gap,
 * - δεν συμπτύσσει Θ/Ε όταν έχουν διαφορετικές αναθέσεις,
 * - κρατά choice/thematic περιπτώσεις ως εξαρτώμενες από πραγματική επιλογή,
 * - δεν μοιράζει ώρες σε components όταν το κανονιστικό dataset δεν δίνει
 *   ασφαλή κατανομή για το συγκεκριμένο ωρολογιακό slot.
 *
 * Δεν γίνεται require από τις δημόσιες σελίδες. Προορίζεται για audits και
 * για το μελλοντικό layer τοποθετήσεων/κατανομής ωρών εκπαιδευτικών.
 */

require_once __DIR__ . '/weekly-timetable-data.php';
require_once __DIR__ . '/teaching-assignments-data.php';

function teachingWorkloadUtf8Lower($text)
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($text, 'UTF-8');
    }

    // Το production περιβάλλον πρέπει να λειτουργεί και χωρίς mbstring.
    // Το strtolower() καλύπτει ASCII/λατινικά· για τους ελληνικούς τίτλους
    // χρησιμοποιούμε ρητό UTF-8 map ώστε η αντιστοίχιση να μην εξαρτάται
    // από τυπογραφικές διαφορές κεφαλαίων/πεζών ανάμεσα στα ΦΕΚ.
    $text = strtolower($text);
    return strtr($text, array(
        'Α'=>'α', 'Ά'=>'ά', 'Β'=>'β', 'Γ'=>'γ', 'Δ'=>'δ', 'Ε'=>'ε', 'Έ'=>'έ',
        'Ζ'=>'ζ', 'Η'=>'η', 'Ή'=>'ή', 'Θ'=>'θ', 'Ι'=>'ι', 'Ί'=>'ί', 'Ϊ'=>'ϊ',
        'Κ'=>'κ', 'Λ'=>'λ', 'Μ'=>'μ', 'Ν'=>'ν', 'Ξ'=>'ξ', 'Ο'=>'ο', 'Ό'=>'ό',
        'Π'=>'π', 'Ρ'=>'ρ', 'Σ'=>'σ', 'Τ'=>'τ', 'Υ'=>'υ', 'Ύ'=>'ύ', 'Ϋ'=>'ϋ',
        'Φ'=>'φ', 'Χ'=>'χ', 'Ψ'=>'ψ', 'Ω'=>'ω', 'Ώ'=>'ώ',
    ));
}

function teachingWorkloadNormalizeText($text)
{
    $text = trim((string) $text);
    $text = str_replace(array('–', '—', '−', '&'), array('-', '-', '-', ' και '), $text);
    $text = preg_replace('/\s*-\s*/u', '-', $text);
    $text = preg_replace('/\s+/u', ' ', $text);
    return teachingWorkloadUtf8Lower($text);
}

function teachingWorkloadFlattenSpecialties($tree)
{
    $result = array();
    foreach ($tree as $items) {
        foreach ($items as $code => $label) {
            $result[$code] = $label;
        }
    }
    return $result;
}

function teachingWorkloadSpecialtyLabelForRow($row)
{
    if (empty($row['specialty'])) {
        return '';
    }
    $map = isset($row['school']) && $row['school'] === 'eneegyl_lykeio'
        ? teachingWorkloadFlattenSpecialties(weeklyTimetableEneegylSpecialties())
        : teachingWorkloadFlattenSpecialties(weeklyTimetableVocationalSpecialties());
    return isset($map[$row['specialty']]) ? $map[$row['specialty']] : '';
}

function teachingWorkloadTrackLabelForRow($row)
{
    if (empty($row['track'])) {
        return '';
    }
    $track = $row['track'];
    if (isset($row['school']) && $row['school'] === 'eneegyl_lykeio') {
        $labels = weeklyTimetableEneegylTrackLabels();
        return isset($labels[$track]) ? $labels[$track] : '';
    }
    $labels = weeklyTimetableVocationalTrackLabels();
    if (isset($labels[$track])) {
        return $labels[$track];
    }
    $special = array(
        'visual' => 'Εικαστικών Τεχνών',
        'theatre' => 'Θεάτρου',
        'dance' => 'Χορού',
    );
    return isset($special[$track]) ? $special[$track] : '';
}

function teachingWorkloadAssignmentPayload($assignment)
{
    $payload = array(
        'section' => isset($assignment['section']) ? $assignment['section'] : '',
        'subject' => isset($assignment['subject']) ? $assignment['subject'] : '',
    );
    foreach (array('A', 'B', 'C', 'special_codes') as $level) {
        if (!empty($assignment[$level]) && is_array($assignment[$level])) {
            $payload[$level] = array_values($assignment[$level]);
        }
        $notesKey = $level . '_notes';
        if (!empty($assignment[$notesKey]) && is_array($assignment[$notesKey])) {
            $payload[$notesKey] = $assignment[$notesKey];
        }
    }
    foreach (array('B_all_others', 'C_all_others', 'A_all_pe', 'special_all_pe') as $flag) {
        if (!empty($assignment[$flag])) {
            $payload[$flag] = true;
        }
    }
    foreach (array('A_all_pe_note', 'special_note') as $noteKey) {
        if (!empty($assignment[$noteKey])) {
            $payload[$noteKey] = $assignment[$noteKey];
        }
    }
    // Τα special_codes χρησιμοποιούν το machine-readable special_notes (όχι
    // special_codes_notes). Το κρατάμε ρητά ώστε το workload/aggregation layer
    // να μη χάνει τις ειδικές παρατηρήσεις συνδιδασκαλίας της Α΄ Π.ΕΠΑ.Λ.
    if (!empty($assignment['special_notes']) && is_array($assignment['special_notes'])) {
        $payload['special_notes'] = $assignment['special_notes'];
    }
    if (!empty($assignment['note'])) {
        $payload['note'] = $assignment['note'];
    }
    return $payload;
}

function teachingWorkloadAssignmentSignature($assignment)
{
    $payload = teachingWorkloadAssignmentPayload($assignment);
    unset($payload['section']);
    return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function teachingWorkloadGradeMatches($assignment, $grade)
{
    $assignmentGrade = isset($assignment['grade']) ? $assignment['grade'] : '';
    if ($assignmentGrade === '' || $assignmentGrade === $grade) {
        return true;
    }
    return !empty($assignment['grades'])
        && is_array($assignment['grades'])
        && in_array($grade, $assignment['grades'], true);
}

function teachingWorkloadAssignmentContextScore($row, $assignment)
{
    $section = teachingWorkloadNormalizeText(isset($assignment['section']) ? $assignment['section'] : '');
    $score = 0;

    if (!empty($row['assignment_section'])) {
        $hint = teachingWorkloadNormalizeText($row['assignment_section']);
        if ($section === $hint) {
            $score = max($score, 400);
        } elseif ($hint !== '' && (strpos($section, $hint) !== false || strpos($hint, $section) !== false)) {
            $score = max($score, 360);
        }
    }

    $specialty = teachingWorkloadNormalizeText(teachingWorkloadSpecialtyLabelForRow($row));
    if ($specialty !== '' && strpos($section, $specialty) !== false) {
        // Η ειδικότητα είναι ισχυρότερη από τον κοινό τομέα.
        $score = max($score, 320);
    }

    if (!empty($row['group'])) {
        $group = teachingWorkloadNormalizeText($row['group']);
        if ($section === $group) {
            $score = max($score, 280);
        } elseif ($group !== '' && (strpos($section, $group) !== false || strpos($group, $section) !== false)) {
            $score = max($score, 250);
        }
    }

    $track = teachingWorkloadNormalizeText(teachingWorkloadTrackLabelForRow($row));
    if ($track !== '' && strpos($section, $track) !== false) {
        $score = max($score, 180);
    }

    return $score;
}

function teachingWorkloadFindAssignmentMatches($assignments, $row, $grade, $subject)
{
    $target = teachingWorkloadNormalizeText($subject);
    $candidates = array();
    foreach ($assignments as $assignment) {
        if (!isset($assignment['school']) || $assignment['school'] !== $row['school']) {
            continue;
        }
        if (!teachingWorkloadGradeMatches($assignment, $grade)) {
            continue;
        }
        if (teachingWorkloadNormalizeText(isset($assignment['subject']) ? $assignment['subject'] : '') !== $target) {
            continue;
        }
        $candidates[] = array(
            'score' => teachingWorkloadAssignmentContextScore($row, $assignment),
            'row' => $assignment,
        );
    }

    if (!$candidates) {
        return array();
    }

    $best = null;
    foreach ($candidates as $candidate) {
        if ($best === null || $candidate['score'] > $best) {
            $best = $candidate['score'];
        }
    }

    $result = array();
    foreach ($candidates as $candidate) {
        if ($candidate['score'] === $best) {
            $result[] = $candidate['row'];
        }
    }
    return $result;
}

function teachingWorkloadResolveAssignmentTarget($assignments, $row, $grade, $subject)
{
    $matches = teachingWorkloadFindAssignmentMatches($assignments, $row, $grade, $subject);
    if (!$matches) {
        return array(
            'status' => 'unresolved',
            'subject' => $subject,
            'match_count' => 0,
        );
    }

    $signatures = array();
    foreach ($matches as $match) {
        $signatures[teachingWorkloadAssignmentSignature($match)] = true;
    }
    if (count($signatures) > 1) {
        $payloads = array();
        foreach ($matches as $match) {
            $payloads[] = teachingWorkloadAssignmentPayload($match);
        }
        return array(
            'status' => 'ambiguous_context',
            'subject' => $subject,
            'match_count' => count($matches),
            'candidates' => $payloads,
        );
    }

    return array(
        'status' => 'resolved',
        'subject' => $subject,
        'match_count' => count($matches),
        'assignment' => teachingWorkloadAssignmentPayload($matches[0]),
    );
}

function teachingWorkloadRestrictResolvedTargetToCodes($resolved, $codes)
{
    if (empty($codes) || !is_array($codes) || !isset($resolved['assignment'])) {
        return $resolved;
    }
    $allowed = array_flip($codes);
    foreach (array('A', 'B', 'C', 'special_codes') as $level) {
        if (empty($resolved['assignment'][$level])) {
            continue;
        }
        $filtered = array();
        foreach ($resolved['assignment'][$level] as $code) {
            if (isset($allowed[$code])) {
                $filtered[] = $code;
            }
        }
        if ($filtered) {
            $resolved['assignment'][$level] = $filtered;
        } else {
            unset($resolved['assignment'][$level]);
        }
        $notesKey = $level . '_notes';
        if (!empty($resolved['assignment'][$notesKey])) {
            $filteredNotes = array();
            foreach ($resolved['assignment'][$notesKey] as $code => $note) {
                if (isset($allowed[$code])) {
                    $filteredNotes[$code] = $note;
                }
            }
            if ($filteredNotes) {
                $resolved['assignment'][$notesKey] = $filteredNotes;
            } else {
                unset($resolved['assignment'][$notesKey]);
            }
        }
    }
    $resolved['codes_restricted'] = array_values($codes);
    return $resolved;
}

function teachingWorkloadChoiceTargetsFromLabel($assignments, $row, $grade, $label)
{
    $prefix = teachingWorkloadNormalizeText($label);
    $targets = array();
    foreach ($assignments as $assignment) {
        if (!isset($assignment['school']) || $assignment['school'] !== $row['school']) {
            continue;
        }
        if (!teachingWorkloadGradeMatches($assignment, $grade)) {
            continue;
        }
        $subject = isset($assignment['subject']) ? $assignment['subject'] : '';
        $normalized = teachingWorkloadNormalizeText($subject);
        if ($normalized === $prefix || strpos($normalized, $prefix . '-') === 0) {
            // Κρατάμε μόνο το σωστό track/context όταν υπάρχουν ομώνυμα μαθήματα.
            $score = teachingWorkloadAssignmentContextScore($row, $assignment);
            if ($score > 0) {
                $targets[$subject] = true;
            }
        }
    }
    return array_keys($targets);
}

function teachingWorkloadResolveChoiceOption($assignments, $row, $grade, $option)
{
    $result = array(
        'label' => isset($option['label']) ? $option['label'] : '',
        'hours_total' => (int) $row['hours'][$grade],
    );
    if (!empty($option['condition'])) {
        $result['condition'] = $option['condition'];
    }

    $targets = array();
    if (!empty($option['subject'])) {
        $targets[] = $option['subject'];
    }
    if (!empty($option['components']) && is_array($option['components'])) {
        $targets = array_merge($targets, $option['components']);
    }
    if (!$targets && !empty($option['label'])) {
        $targets = teachingWorkloadChoiceTargetsFromLabel($assignments, $row, $grade, $option['label']);
        if ($targets) {
            $result['targets_derived_from_assignment_prefix'] = true;
        }
    }
    $targets = array_values(array_unique($targets));

    $resolvedTargets = array();
    foreach ($targets as $target) {
        $resolved = teachingWorkloadResolveAssignmentTarget($assignments, $row, $grade, $target);
        if (!empty($option['codes'])) {
            $resolved = teachingWorkloadRestrictResolvedTargetToCodes($resolved, $option['codes']);
        }
        $resolvedTargets[] = $resolved;
    }
    $result['targets'] = $resolvedTargets;

    $allResolved = $resolvedTargets ? true : false;
    foreach ($resolvedTargets as $resolved) {
        if ($resolved['status'] !== 'resolved') {
            $allResolved = false;
            break;
        }
    }
    $result['status'] = $allResolved ? 'resolved' : 'unresolved';

    if (count($resolvedTargets) > 1) {
        // Το slot έχει γνωστές συνολικές ώρες, όχι ασφαλή κατανομή ανά Θ/Ε target.
        $result['component_hours_status'] = 'not_fixed_by_timetable_bridge';
    }
    return $result;
}

function teachingWorkloadResolveThematicAssignments($assignments, $row, $grade)
{
    $section = isset($row['assignment_section']) ? $row['assignment_section'] : '';
    $result = array();
    foreach ($assignments as $assignment) {
        if (!isset($assignment['school']) || $assignment['school'] !== $row['school']) {
            continue;
        }
        if (!teachingWorkloadGradeMatches($assignment, $grade)) {
            continue;
        }
        if (!isset($assignment['section']) || $assignment['section'] !== $section) {
            continue;
        }
        $result[] = teachingWorkloadAssignmentPayload($assignment);
    }
    return $result;
}

function teachingWorkloadBuildInstance($row, $grade, $assignments)
{
    $instance = array(
        'instance_id' => $row['course_id'] . '@' . $grade,
        'course_id' => $row['course_id'],
        'school' => $row['school'],
        'grade' => $grade,
        'group' => isset($row['group']) ? $row['group'] : '',
        'subject' => $row['subject'],
        'hours_value' => (int) $row['hours'][$grade],
        'hours_display' => isset($row['hours_display'][$grade]) ? $row['hours_display'][$grade] : (string) $row['hours'][$grade],
    );
    if (!empty($row['period_hours'][$grade]) && is_array($row['period_hours'][$grade])) {
        $instance['hours_mode'] = 'periodic';
        $instance['period_hours'] = $row['period_hours'][$grade];
    } elseif (isset($row['hours_mode']) && $row['hours_mode'] === 'dynamic') {
        // No numeric weekly total is fixed by regulation (e.g. EEEEΚ ST class).
        $instance['hours_mode'] = 'dynamic';
    } else {
        $instance['hours_mode'] = 'fixed';
        $instance['hours_total'] = (int) $row['hours'][$grade];
    }
    foreach (array('track', 'specialty', 'slot_id', 'choice_set_id', 'choice_count', 'mode', 'variant', 'variant_group') as $key) {
        if (isset($row[$key])) {
            $instance[$key] = $row[$key];
        }
    }
    if (isset($row['condition_by_grade'][$grade])) {
        $instance['condition'] = $row['condition_by_grade'][$grade];
    } elseif (!empty($row['condition'])) {
        $instance['condition'] = $row['condition'];
    }
    if (!empty($row['note_by_grade'][$grade])) {
        $instance['note'] = $row['note_by_grade'][$grade];
    } elseif (!empty($row['note'])) {
        $instance['note'] = $row['note'];
    }
    if (!empty($row['group_note'])) {
        $instance['group_note'] = $row['group_note'];
    }

    $linkStatus = isset($row['assignment_link_status']) ? $row['assignment_link_status'] : '';

    if ($linkStatus === 'regulatory_gap') {
        $instance['resolution_status'] = 'regulatory_gap';
        $instance['assignment'] = null;
        foreach (array(
            'assignment_gap_confirmed' => 'confirmed',
            'assignment_gap_kind' => 'kind',
            'assignment_gap_timetable_source' => 'timetable_source',
            'assignment_gap_assignment_source' => 'assignment_source',
            'assignment_gap_inference_guard' => 'inference_guard',
            'assignment_gap_related_scope' => 'related_scope',
        ) as $source => $target) {
            if (isset($row[$source])) {
                $instance['regulatory_gap'][$target] = $row[$source];
            }
        }
        return $instance;
    }

    if ($linkStatus === 'thematic_dependent') {
        $instance['resolution_status'] = 'thematic_dependent';
        $instance['thematic_assignments'] = teachingWorkloadResolveThematicAssignments($assignments, $row, $grade);
        $instance['component_hours_status'] = 'not_fixed_by_regulation';
        return $instance;
    }

    if ($linkStatus === 'choice_dependent') {
        $instance['resolution_status'] = 'choice_dependent';
        $instance['choice_options'] = array();
        foreach ($row['assignment_choice_options'] as $option) {
            $instance['choice_options'][] = teachingWorkloadResolveChoiceOption($assignments, $row, $grade, $option);
        }
        if (!empty($row['assignment_choice_group_id'])) {
            $instance['choice_group'] = array(
                'id' => $row['assignment_choice_group_id'],
                'required' => isset($row['assignment_choice_group_required']) ? $row['assignment_choice_group_required'] : 1,
                'distinct' => !empty($row['assignment_choice_group_distinct']),
            );
        }
        return $instance;
    }

    if (!empty($row['assignment_components_by_grade'][$grade])) {
        $instance['resolution_status'] = 'components';
        $instance['components'] = array();
        foreach ($row['assignment_components_by_grade'][$grade] as $component) {
            $resolved = teachingWorkloadResolveAssignmentTarget($assignments, $row, $grade, $component['subject']);
            $resolved['kind'] = $component['kind'];
            $resolved['hours'] = (int) $component['hours'];
            $instance['components'][] = $resolved;
        }
        return $instance;
    }

    $subject = !empty($row['assignment_subject_alias']) ? $row['assignment_subject_alias'] : $row['subject'];
    $resolved = teachingWorkloadResolveAssignmentTarget($assignments, $row, $grade, $subject);
    $instance['resolution_status'] = !empty($row['assignment_subject_alias']) ? 'alias' : 'direct';
    $instance['assignment_subject'] = $subject;
    $instance['assignment_resolution'] = $resolved;
    if ($resolved['status'] !== 'resolved') {
        $instance['resolution_status'] = $resolved['status'] === 'ambiguous_context'
            ? 'ambiguous_assignment_context'
            : 'unresolved_assignment';
    }
    return $instance;
}

function teachingWorkloadModel()
{
    $assignments = teachingAssignmentsData();
    $result = array();
    foreach (weeklyTimetableRows() as $row) {
        foreach ($row['hours'] as $grade => $hours) {
            $result[] = teachingWorkloadBuildInstance($row, $grade, $assignments);
        }
    }
    return $result;
}

function teachingWorkloadModelSummary($model = null)
{
    if ($model === null) {
        $model = teachingWorkloadModel();
    }
    $statuses = array();
    $choiceOptions = 0;
    $choiceOptionsResolved = 0;
    $componentTargets = 0;
    $componentTargetsResolved = 0;
    foreach ($model as $instance) {
        $status = $instance['resolution_status'];
        if (!isset($statuses[$status])) {
            $statuses[$status] = 0;
        }
        $statuses[$status]++;

        if (!empty($instance['choice_options'])) {
            foreach ($instance['choice_options'] as $option) {
                $choiceOptions++;
                if ($option['status'] === 'resolved') {
                    $choiceOptionsResolved++;
                }
            }
        }
        if (!empty($instance['components'])) {
            foreach ($instance['components'] as $component) {
                $componentTargets++;
                if ($component['status'] === 'resolved') {
                    $componentTargetsResolved++;
                }
            }
        }
    }
    ksort($statuses);
    return array(
        'instances' => count($model),
        'statuses' => $statuses,
        'choice_options' => $choiceOptions,
        'choice_options_resolved' => $choiceOptionsResolved,
        'component_targets' => $componentTargets,
        'component_targets_resolved' => $componentTargetsResolved,
    );
}
