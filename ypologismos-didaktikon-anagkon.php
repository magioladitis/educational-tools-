<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/school-profile-general-education.php';
require_once __DIR__ . '/includes/school-profile-workload.php';
require_once __DIR__ . '/includes/ethics-class-formation.php';
require_once __DIR__ . '/includes/personnel-workload.php';
require_once __DIR__ . '/includes/teaching-workload-aggregation.php';

function staffingUiH($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
function staffingUiPost($key, $default = '') {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}
function staffingUiInt($key, $default = 0) {
    $value = staffingUiPost($key, $default);
    if ($value === '' || $value === null) return (int) $default;
    return max(0, (int) $value);
}
function staffingUiNullableInt($key) {
    if (!isset($_POST[$key]) || $_POST[$key] === '') return null;
    return max(0, (int) $_POST[$key]);
}
function staffingUiNullableBool($key) {
    if (!isset($_POST[$key]) || $_POST[$key] === '') return null;
    return $_POST[$key] === '1';
}
function staffingUiEthicsGrade($suffix) {
    $exempt = staffingUiNullableInt('ethics_' . $suffix . '_exempt');
    $timely = staffingUiNullableBool('ethics_' . $suffix . '_timely');
    $equivalent = staffingUiNullableInt('ethics_' . $suffix . '_equivalent');
    if ($exempt === null && $timely === null && $equivalent === null) return array();
    return array(
        'exempt_students' => $exempt,
        'within_fifth_day' => $timely,
        'equivalent_ethics_sections' => $equivalent,
    );
}
function staffingUiIssueLabel($issue) {
    if (preg_match('/^(gymnasio|gel):([^:]+):second_foreign_language_groups_exceeds_general_sections:([^:]+):(\d+)>(\d+)$/u', $issue, $m)) {
        return 'Οι ομάδες «' . $m[3] . '» της ' . $m[2] . ' τάξης (' . $m[4]
            . ') δεν μπορούν να ξεπερνούν τα ' . $m[5]
            . ' κανονικά τμήματα της ίδιας τάξης. (' . $issue . ')';
    }
    $map = array(
        'general_sections_required' => 'Χρειάζεται τουλάχιστον ένα τμήμα στην τάξη.',
        'second_foreign_language_groups_required' => 'Χρειάζονται οι πραγματικές ομάδες 2ης ξένης γλώσσας.',
        'at_least_one_orientation_group_required' => 'Χρειάζεται τουλάχιστον μία πραγματική Ομάδα Προσανατολισμού.',
        'science_health_field_groups_required' => 'Χρειάζεται κατανομή ομάδων Μαθηματικών / Βιολογίας στη Γ΄ Θετικών–Υγείας.',
        'science_health_field_groups_empty' => 'Υπάρχει Θετικών–Υγείας αλλά δεν δηλώθηκε ομάδα Μαθηματικών ή Βιολογίας.',
        'technology_informatics_split_sections_exceeds_general_sections' => 'Τα τμήματα με πάνω από 21 μαθητές δεν μπορούν να είναι περισσότερα από τα δηλωμένα τμήματα της ίδιας τάξης.',
    );
    foreach ($map as $needle => $label) {
        if (strpos($issue, $needle) !== false) return $label . ' (' . $issue . ')';
    }
    if (strpos($issue, 'conditional_groups') !== false) {
        return 'Χρειάζεται ο πραγματικός αριθμός ομάδων για το μάθημα υπό προϋπόθεση της Γ΄ ΓΕΛ. (' . $issue . ')';
    }
    if (strpos($issue, 'orientation_sections') !== false) {
        return 'Χρειάζεται ο πραγματικός αριθμός ομάδων προσανατολισμού. (' . $issue . ')';
    }
    return $issue;
}
function staffingUiPriorityLabel($priority) {
    if ($priority === 'A') return 'Α΄';
    if ($priority === 'B') return 'Β΄';
    if ($priority === 'C') return 'Γ΄';
    if ($priority === 'SPECIAL') return 'Ειδική';
    return (string) $priority;
}
function staffingUiReadinessLabel($readiness) {
    $map = array(
        'ready_for_eligibility_matrix' => 'Έτοιμο για πίνακα επιλεξιμότητας',
        'structure_only' => 'Μόνο δομή — απαιτούνται επιπλέον στοιχεία',
    );
    return isset($map[$readiness]) ? $map[$readiness] : 'Απαιτείται έλεγχος';
}
function staffingUiTrackLabel($track) {
    $map = array(
        'humanities' => 'Ανθρωπιστικών',
        'science' => 'Θετικών',
        'science_health' => 'Θετικών / Υγείας',
        'economics_it' => 'Οικονομίας / Πληροφορικής',
    );
    return isset($map[$track]) ? $map[$track] : $track;
}

function staffingUiPersonnelRoleLabel($role) {
    $map = array(
        'teacher' => 'Εκπαιδευτικός',
        'director' => 'Διευθυντής/ντρια',
        'vice_or_sector' => 'Υποδιευθυντής/ντρια',
    );
    return isset($map[$role]) ? $map[$role] : $role;
}
function staffingUiPersonnelReasonLabel($reason) {
    $map = array(
        'unknown_specialty_code' => 'Δεν έχει επιλεγεί έγκυρος κλάδος.',
        'director_sections_band_required' => 'Για Διευθυντή/ντρια χρειάζονται τα δηλωμένα κανονικά τμήματα της σχολικής μονάδας.',
        'de_hours_scale_requires_explicit_architect_or_technician' => 'Για κλάδο ΔΕ χρειάζεται ρητή επιλογή κλίμακας ωραρίου Αρχιτεχνίτη ή Τεχνίτη.',
        'unknown_hours_branch' => 'Η επιλεγμένη κλίμακα ωραρίου δεν είναι έγκυρη.',
        'unsupported_specialty_for_secondary_hours' => 'Ο κλάδος δεν υποστηρίζεται από τον υπολογισμό ωραρίου Δευτεροβάθμιας.',
        'required_teaching_hours_required' => 'Συμπλήρωσε το υποχρεωτικό διδακτικό ωράριο του εκπαιδευτικού.',
        'required_teaching_hours_invalid' => 'Το υποχρεωτικό διδακτικό ωράριο πρέπει να είναι ακέραιος αριθμός από 1 έως 35 ώρες.',
        'required_teaching_hours_exceeds_pe_max' => 'Για κλάδο ΠΕ το υποχρεωτικό διδακτικό ωράριο δεν μπορεί να ξεπερνά τις 23 ώρες.',
        'multiple_directors_not_allowed' => 'Μπορεί να δηλωθεί μόνο ένας/μία Διευθυντής/ντρια στη σχολική μονάδα.',
    );
    return isset($map[$reason]) ? $map[$reason] : $reason;
}
function staffingUiPersonnelRowsFromPost() {
    $keys = array('person_id','display_name','specialty_code','secondary_specialty_code','required_teaching_hours','service_years','service_months','service_days','role','assigned_external_hours','director_sections_band','hours_branch');
    $arrays = array();
    $count = 0;
    foreach ($keys as $key) {
        $name = 'personnel_' . $key;
        $arrays[$key] = isset($_POST[$name]) && is_array($_POST[$name]) ? $_POST[$name] : array();
        $count = max($count, count($arrays[$key]));
    }
    $rows = array();
    for ($i=0; $i<$count; $i++) {
        $name = isset($arrays['display_name'][$i]) ? trim((string)$arrays['display_name'][$i]) : '';
        $specialty = isset($arrays['specialty_code'][$i]) ? teacherSpecialtyCanonicalCode($arrays['specialty_code'][$i]) : '';
        $secondarySpecialty = isset($arrays['secondary_specialty_code'][$i]) ? teacherSpecialtyCanonicalCode($arrays['secondary_specialty_code'][$i]) : '';
        $requiredHours = isset($arrays['required_teaching_hours'][$i]) ? trim((string)$arrays['required_teaching_hours'][$i]) : '';
        $years = isset($arrays['service_years'][$i]) ? (string)$arrays['service_years'][$i] : '';
        $months = isset($arrays['service_months'][$i]) ? (string)$arrays['service_months'][$i] : '';
        $days = isset($arrays['service_days'][$i]) ? (string)$arrays['service_days'][$i] : '';
        $role = isset($arrays['role'][$i]) ? (string)$arrays['role'][$i] : 'teacher';
        $external = isset($arrays['assigned_external_hours'][$i]) ? (string)$arrays['assigned_external_hours'][$i] : '0';
        $directorBand = isset($arrays['director_sections_band'][$i]) ? (string)$arrays['director_sections_band'][$i] : '';
        $hoursBranch = isset($arrays['hours_branch'][$i]) ? (string)$arrays['hours_branch'][$i] : '';
        // A newly-added but untouched blank row must not become an unresolved person.
        if ($name === '' && $specialty === '') continue;
        $id = isset($arrays['person_id'][$i]) ? trim((string)$arrays['person_id'][$i]) : '';
        if ($id === '') $id = 'person-' . ($i + 1);
        $rows[] = array(
            'person_id'=>$id,
            'display_name'=>$name,
            'specialty_code'=>$specialty,
            'secondary_specialty_code'=>$secondarySpecialty,
            'required_teaching_hours'=>$requiredHours,
            'service'=>array('years'=>$years === '' ? 0 : $years,'months'=>$months === '' ? 0 : $months,'days'=>$days === '' ? 0 : $days),
            'role'=>$role,
            'assigned_external_hours'=>$external === '' ? 0 : $external,
            'director_sections_band'=>$directorBand,
            'hours_branch'=>$hoursBranch,
        );
    }
    return $rows;
}
function staffingUiAllocationRowsFromPost() {
    $personIds = isset($_POST['allocation_person_id']) && is_array($_POST['allocation_person_id']) ? $_POST['allocation_person_id'] : array();
    $slotIds = isset($_POST['allocation_slot_id']) && is_array($_POST['allocation_slot_id']) ? $_POST['allocation_slot_id'] : array();
    $hours = isset($_POST['allocation_hours']) && is_array($_POST['allocation_hours']) ? $_POST['allocation_hours'] : array();
    $count = max(count($personIds), count($slotIds), count($hours));
    $rows = array();
    for ($i=0; $i<$count; $i++) {
        $personId = isset($personIds[$i]) ? trim((string)$personIds[$i]) : '';
        $slotId = isset($slotIds[$i]) ? trim((string)$slotIds[$i]) : '';
        $hourValue = isset($hours[$i]) ? trim((string)$hours[$i]) : '';
        if ($personId === '' && $slotId === '' && ($hourValue === '' || $hourValue === '0')) continue;
        $rows[] = array(
            'person_id'=>$personId,
            'slot_id'=>$slotId,
            'hours'=>$hourValue === '' ? 0 : max(0, (int)$hourValue),
        );
    }
    return $rows;
}
function staffingUiRenderPersonnelStateHiddenInputs($rows) {
    foreach ($rows as $person) {
        echo '<input type="hidden" name="personnel_person_id[]" value="' . staffingUiH($person['person_id']) . '">';
        echo '<input type="hidden" name="personnel_display_name[]" value="' . staffingUiH($person['display_name']) . '">';
        echo '<input type="hidden" name="personnel_specialty_code[]" value="' . staffingUiH($person['specialty_code']) . '">';
        echo '<input type="hidden" name="personnel_secondary_specialty_code[]" value="' . staffingUiH(isset($person['secondary_specialty_code']) ? $person['secondary_specialty_code'] : '') . '">';
        echo '<input type="hidden" name="personnel_required_teaching_hours[]" value="' . staffingUiH(isset($person['required_teaching_hours']) ? $person['required_teaching_hours'] : '') . '">';
        echo '<input type="hidden" name="personnel_service_years[]" value="' . staffingUiH(isset($person['service']['years']) ? $person['service']['years'] : 0) . '">';
        echo '<input type="hidden" name="personnel_service_months[]" value="' . staffingUiH(isset($person['service']['months']) ? $person['service']['months'] : 0) . '">';
        echo '<input type="hidden" name="personnel_service_days[]" value="' . staffingUiH(isset($person['service']['days']) ? $person['service']['days'] : 0) . '">';
        echo '<input type="hidden" name="personnel_role[]" value="' . staffingUiH(isset($person['role']) ? $person['role'] : 'teacher') . '">';
        echo '<input type="hidden" name="personnel_assigned_external_hours[]" value="' . staffingUiH(isset($person['assigned_external_hours']) ? $person['assigned_external_hours'] : 0) . '">';
        echo '<input type="hidden" name="personnel_hours_branch[]" value="' . staffingUiH(isset($person['hours_branch']) ? $person['hours_branch'] : '') . '">';
    }
}
function staffingUiRenderAllocationStateHiddenInputs($rows) {
    foreach ($rows as $row) {
        echo '<input type="hidden" name="allocation_person_id[]" value="' . staffingUiH(isset($row['person_id']) ? $row['person_id'] : '') . '">';
        echo '<input type="hidden" name="allocation_slot_id[]" value="' . staffingUiH(isset($row['slot_id']) ? $row['slot_id'] : '') . '">';
        echo '<input type="hidden" name="allocation_hours[]" value="' . staffingUiH(isset($row['hours']) ? $row['hours'] : 0) . '">';
    }
}
function staffingUiAllocationPersonLabel($person) {
    $code = isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '';
    $secondary = isset($person['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($person['secondary_specialty_code']) : '';
    $name = isset($person['display_name']) ? trim((string)$person['display_name']) : '';
    if ($name === '') $name = 'Χωρίς ονοματεπώνυμο';
    $codes = $code;
    if ($secondary !== '') $codes .= ' / 2η ' . $secondary;
    return trim($codes . ' · ' . $name, ' ·');
}
function staffingUiRenderAllocationPersonOptions($people, $selected) {
    echo '<option value="">— επιλογή εκπαιδευτικού —</option>';
    foreach ($people as $person) {
        $id = isset($person['person_id']) ? (string)$person['person_id'] : '';
        if ($id === '') continue;
        echo '<option value="' . staffingUiH($id) . '"' . ($selected === $id ? ' selected' : '') . '>' . staffingUiH(staffingUiAllocationPersonLabel($person)) . '</option>';
    }
}
function staffingUiSortAllocationSlots($slots) {
    uasort($slots, function ($a, $b) {
        $order = array('Α΄'=>1,'Β΄'=>2,'Γ΄'=>3,'Δ΄'=>4,'Ε΄'=>5,'ΣΤ΄'=>6);
        $ga = isset($order[$a['grade']]) ? $order[$a['grade']] : 99;
        $gb = isset($order[$b['grade']]) ? $order[$b['grade']] : 99;
        if ($ga !== $gb) return $ga - $gb;
        $la = isset($a['slot_label']) ? $a['slot_label'] : '';
        $lb = isset($b['slot_label']) ? $b['slot_label'] : '';
        $cmp = strnatcmp($la, $lb);
        if ($cmp !== 0) return $cmp;
        return strnatcmp(isset($a['subject']) ? $a['subject'] : '', isset($b['subject']) ? $b['subject'] : '');
    });
    return $slots;
}
function staffingUiAllocationSlotOptionLabel($slot) {
    $label = isset($slot['slot_label']) ? $slot['slot_label'] : '';
    $subject = isset($slot['subject']) ? $slot['subject'] : '';
    $hours = isset($slot['capacity_hours']) ? (int)$slot['capacity_hours'] : 0;
    return $label . ' · ' . $subject . ' · ' . $hours . ' ώρ.';
}
function staffingUiRenderAllocationSlotOptions($slots, $selected) {
    echo '<option value="">— επιλογή τμήματος / ομάδας και μαθήματος —</option>';
    $lastGrade = null;
    $openGroup = false;
    foreach ($slots as $slotId=>$slot) {
        $grade = isset($slot['grade']) ? $slot['grade'] : 'Άλλο';
        if ($grade !== $lastGrade) {
            if ($openGroup) echo '</optgroup>';
            echo '<optgroup label="' . staffingUiH($grade . ' τάξη') . '">';
            $openGroup = true;
            $lastGrade = $grade;
        }
        echo '<option value="' . staffingUiH($slotId) . '" data-capacity="' . (int)$slot['capacity_hours'] . '"' . ($selected === $slotId ? ' selected' : '') . '>' . staffingUiH(staffingUiAllocationSlotOptionLabel($slot)) . '</option>';
    }
    if ($openGroup) echo '</optgroup>';
}
function staffingUiAllocationErrorLabel($error) {
    $map = array(
        'unknown_person'=>'Δεν έχει επιλεγεί έγκυρος εκπαιδευτικός.',
        'unknown_slot'=>'Δεν έχει επιλεγεί έγκυρο τμήμα / ομάδα και μάθημα.',
        'positive_hours_required'=>'Οι ώρες πρέπει να είναι θετικές.',
        'hours_exceed_slot_capacity'=>'Οι ώρες υπερβαίνουν τις ώρες του συγκεκριμένου τμήματος / ομάδας.',
        'specialty_not_eligible'=>'Ο κλάδος του εκπαιδευτικού δεν έχει ανάθεση στο συγκεκριμένο μάθημα.',
        'slot_overallocated_across_roster'=>'Το ίδιο τμήμα / ομάδα έχει κατανεμηθεί πάνω από τις διαθέσιμες ώρες του.',
    );
    return isset($map[$error]) ? $map[$error] : $error;
}
function staffingUiSchoolStateKeys() {
    return array(
        'school_type','school_name',
        'gym_general_a','gym_general_b','gym_general_c',
        'gym_lang_a_fr','gym_lang_a_de','gym_lang_a_it','gym_lang_b_fr','gym_lang_b_de','gym_lang_b_it','gym_lang_c_fr','gym_lang_c_de','gym_lang_c_it',
        'gym_tech_split_a','gym_tech_split_b','gym_tech_split_c',
        'gel_general_a','gel_general_b','gel_general_c',
        'gel_lang_a_fr','gel_lang_a_de','gel_lang_b_fr','gel_lang_b_de',
        'gel_b_hum','gel_b_sci','gel_c_hum','gel_c_scihealth','gel_c_econit',
        'gel_c_field_math','gel_c_field_bio','gel_c_cond_math','gel_c_cond_history',
        'ethics_a_exempt','ethics_a_timely','ethics_a_equivalent','ethics_b_exempt','ethics_b_timely','ethics_b_equivalent','ethics_c_exempt','ethics_c_timely','ethics_c_equivalent'
    );
}
function staffingUiRenderSchoolStateHiddenInputs() {
    foreach (staffingUiSchoolStateKeys() as $key) {
        $value = staffingUiPost($key, '');
        echo '<input type="hidden" name="' . staffingUiH($key) . '" value="' . staffingUiH($value) . '">';
    }
}
function staffingUiPersonnelSpecialtyOptions($matrix, $model = null) {
    $leafCodes = schoolProfileWorkloadStaffingCodes();
    $knownAssignmentCodes = teachingWorkloadKnownAssignmentCodes($model);
    $teachingLeafCodes = array();
    foreach ($leafCodes as $leaf) {
        foreach ($knownAssignmentCodes as $assigned) {
            if (teachingWorkloadAggregationCodeMatches($leaf, $assigned)) {
                $teachingLeafCodes[] = $leaf;
                break;
            }
        }
    }
    $relevant = $matrix && !empty($matrix['codes']) ? array_keys($matrix['codes']) : array();
    $relevantMap = array();
    foreach ($relevant as $code) $relevantMap[$code] = true;
    $other = array();
    foreach ($teachingLeafCodes as $code) if (!isset($relevantMap[$code])) $other[] = $code;
    usort($relevant, 'strnatcmp');
    usort($other, 'strnatcmp');
    return array('relevant'=>$relevant,'other'=>$other);
}
function staffingUiRenderPersonnelSpecialtyOptions($options, $selected) {
    echo '<option value="">— επιλογή κλάδου —</option>';
    if (!empty($options['relevant'])) {
        echo '<optgroup label="Κλάδοι με επιλεξιμότητα στη μονάδα">';
        foreach ($options['relevant'] as $code) {
            echo '<option value="' . staffingUiH($code) . '"' . ($selected === $code ? ' selected' : '') . '>' . staffingUiH(teacherSpecialtyDisplay($code)) . '</option>';
        }
        echo '</optgroup>';
    }
    if (!empty($options['other'])) {
        echo '<optgroup label="Λοιποί αναγνωρισμένοι κλάδοι">';
        foreach ($options['other'] as $code) {
            echo '<option value="' . staffingUiH($code) . '"' . ($selected === $code ? ' selected' : '') . '>' . staffingUiH(teacherSpecialtyDisplay($code)) . '</option>';
        }
        echo '</optgroup>';
    }
}

/**
 * Ταξινόμηση μόνο για την παρουσίαση του πίνακα ανά κλάδο.
 *
 * Το workload matrix διατηρεί τη δική του εσωτερική σειρά αξιολόγησης.
 * Στο UI όμως οι κλάδοι εμφανίζονται σε φυσική σειρά κωδικού
 * (ΠΕ01, ΠΕ02, ΠΕ03, ΠΕ04.01, ΠΕ04.02, ...), ώστε ο πίνακας να
 * λειτουργεί ως ευανάγνωστος κατάλογος ειδικοτήτων.
 */
function staffingUiSortCodesNatural($matrix) {
    if (!$matrix || empty($matrix['codes']) || !is_array($matrix['codes'])) return $matrix;
    uksort($matrix['codes'], 'strnatcmp');
    return $matrix;
}

/**
 * Front-end only collapse for Εργαστήρια Δεξιοτήτων.
 *
 * The regulatory A/B assignment remains intact in the backend workload matrix.
 * For presentation, the same broad slot is removed from every individual branch
 * and surfaced once as «Οποιαδήποτε ειδικότητα» so it does not create dozens of
 * repetitive eligibility rows.
 */
function staffingUiCollapseSkillsWorkshops($matrix) {
    $result = array(
        'matrix' => $matrix,
        'collapsed' => array(
            'active' => false,
            'subject' => 'Εργαστήρια Δεξιοτήτων',
            'label' => 'Οποιαδήποτε ειδικότητα',
            'hours' => 0,
            'unit_count' => 0,
            'units' => array(),
        ),
    );
    if (!$matrix || empty($matrix['units'])) return $result;

    $unitIds = array();
    foreach ($matrix['units'] as $unit) {
        if (!isset($unit['subject']) || $unit['subject'] !== 'Εργαστήρια Δεξιοτήτων') continue;
        $id = isset($unit['unit_id']) ? (string)$unit['unit_id'] : '';
        if ($id === '' || isset($unitIds[$id])) continue;
        $unitIds[$id] = true;
        $result['collapsed']['active'] = true;
        $result['collapsed']['unit_count']++;
        $result['collapsed']['hours'] += isset($unit['school_hours']) ? (int)$unit['school_hours'] : 0;
        $result['collapsed']['units'][] = array(
            'unit_id' => $id,
            'grade' => isset($unit['grade']) ? $unit['grade'] : '',
            'hours' => isset($unit['school_hours']) ? (int)$unit['school_hours'] : 0,
        );
    }
    if (!$result['collapsed']['active']) return $result;

    $display = $matrix;
    foreach ($display['codes'] as $code => &$row) {
        $claims = array();
        foreach ($row['claims'] as $claim) {
            $id = isset($claim['unit_id']) ? (string)$claim['unit_id'] : '';
            if ($id !== '' && isset($unitIds[$id])) continue;
            $claims[] = $claim;
        }
        $row['claims'] = $claims;
        $row['top_priority_hours'] = 0;
        $row['exclusive_top_priority_hours'] = 0;
        $row['shared_top_priority_hours'] = 0;
        $row['ordered_top_priority_hours'] = 0;
        $row['ordered_exclusive_top_priority_hours'] = 0;
        $row['ordered_shared_top_priority_hours'] = 0;
        $row['fallback_hours'] = 0;
        $row['special_top_priority_hours'] = 0;
        $row['eligible_hours_by_priority'] = array('A'=>0,'B'=>0,'C'=>0,'SPECIAL'=>0);
        $row['top_unit_count'] = 0;
        $row['fallback_unit_count'] = 0;
        foreach ($claims as $claim) {
            $hours = isset($claim['school_hours']) ? (int)$claim['school_hours'] : 0;
            $priority = isset($claim['priority']) ? $claim['priority'] : '';
            if (isset($row['eligible_hours_by_priority'][$priority])) {
                $row['eligible_hours_by_priority'][$priority] += $hours;
            }
            $isTop = !empty($claim['is_top_priority']);
            if (!$isTop) {
                $row['fallback_hours'] += $hours;
                $row['fallback_unit_count']++;
                continue;
            }
            $row['top_priority_hours'] += $hours;
            $row['top_unit_count']++;
            $topCount = isset($claim['top_code_count']) ? (int)$claim['top_code_count'] : 0;
            if ($topCount === 1) $row['exclusive_top_priority_hours'] += $hours;
            else $row['shared_top_priority_hours'] += $hours;
            $topPriority = isset($claim['top_priority']) ? $claim['top_priority'] : '';
            if ($topPriority === 'SPECIAL') {
                $row['special_top_priority_hours'] += $hours;
            } else {
                $row['ordered_top_priority_hours'] += $hours;
                if ($topCount === 1) $row['ordered_exclusive_top_priority_hours'] += $hours;
                else $row['ordered_shared_top_priority_hours'] += $hours;
            }
        }
    }
    unset($row);
    foreach (array_keys($display['codes']) as $code) {
        if (empty($display['codes'][$code]['claims'])) unset($display['codes'][$code]);
    }
    uasort($display['codes'], function ($a, $b) {
        if ($a['ordered_exclusive_top_priority_hours'] !== $b['ordered_exclusive_top_priority_hours']) {
            return $b['ordered_exclusive_top_priority_hours'] - $a['ordered_exclusive_top_priority_hours'];
        }
        if ($a['top_priority_hours'] !== $b['top_priority_hours']) {
            return $b['top_priority_hours'] - $a['top_priority_hours'];
        }
        return strnatcmp($a['code'], $b['code']);
    });

    $skillHours = (int)$result['collapsed']['hours'];
    $skillUnits = (int)$result['collapsed']['unit_count'];
    // The overall curriculum/assignment total remains untouched. Only branch-level
    // presentation metrics exclude the collapsed workshop slots.
    $display['summary']['presentation_collapsed_skills_hours'] = $skillHours;
    $display['summary']['presentation_collapsed_skills_units'] = $skillUnits;
    $display['summary']['presentation_staffing_leaf_codes_with_claims'] = count($display['codes']);
    $display['summary']['ordered_top_unit_hours'] = max(0, (int)$display['summary']['ordered_top_unit_hours'] - $skillHours);
    $display['summary']['shared_top_unit_hours'] = max(0, (int)$display['summary']['shared_top_unit_hours'] - $skillHours);
    $display['summary']['ordered_shared_top_unit_hours'] = max(0, (int)$display['summary']['ordered_shared_top_unit_hours'] - $skillHours);

    $result['matrix'] = $display;
    return $result;
}

$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : 'GET';
$staffingAction = $requestMethod === 'POST' ? staffingUiPost('staffing_action', '') : '';
$submitted = $requestMethod === 'POST'
    && in_array($staffingAction, array('profile','personnel','allocation'), true);
$schoolType = staffingUiPost('school_type', 'gymnasio');
if ($schoolType !== 'gel') $schoolType = 'gymnasio';
$profile = null;
$readiness = null;
$matrix = null;
$teachingModel = null;
$displayMatrix = null;
$collapsedSkills = array('active'=>false,'hours'=>0,'unit_count'=>0,'units'=>array(),'label'=>'Οποιαδήποτε ειδικότητα','subject'=>'Εργαστήρια Δεξιοτήτων');

if ($submitted) {
    $schoolName = trim((string) staffingUiPost('school_name', ''));
    if ($schoolType === 'gymnasio') {
        $profile = schoolProfileBuildDayGymnasium2026(array(
            'profile_id' => 'ui-gymnasio-' . date('YmdHis'),
            'school' => array(
                'type' => 'Ημερήσιο Γυμνάσιο',
                'name' => $schoolName !== '' ? $schoolName : 'Προσωρινό προφίλ Γυμνασίου',
            ),
            'source' => array('kind' => 'manual_frontend_test'),
            'general_sections' => array(
                'Α΄' => staffingUiInt('gym_general_a'),
                'Β΄' => staffingUiInt('gym_general_b'),
                'Γ΄' => staffingUiInt('gym_general_c'),
            ),
            'second_foreign_language_groups' => array(
                'Α΄' => array('Γαλλικά'=>staffingUiInt('gym_lang_a_fr'),'Γερμανικά'=>staffingUiInt('gym_lang_a_de'),'Ιταλικά'=>staffingUiInt('gym_lang_a_it')),
                'Β΄' => array('Γαλλικά'=>staffingUiInt('gym_lang_b_fr'),'Γερμανικά'=>staffingUiInt('gym_lang_b_de'),'Ιταλικά'=>staffingUiInt('gym_lang_b_it')),
                'Γ΄' => array('Γαλλικά'=>staffingUiInt('gym_lang_c_fr'),'Γερμανικά'=>staffingUiInt('gym_lang_c_de'),'Ιταλικά'=>staffingUiInt('gym_lang_c_it')),
            ),
            'technology_informatics_split_sections' => array(
                'Α΄' => staffingUiInt('gym_tech_split_a'),
                'Β΄' => staffingUiInt('gym_tech_split_b'),
                'Γ΄' => staffingUiInt('gym_tech_split_c'),
            ),
            'ethics_by_grade' => array(
                'Α΄' => staffingUiEthicsGrade('a'),
                'Β΄' => staffingUiEthicsGrade('b'),
                'Γ΄' => staffingUiEthicsGrade('c'),
            ),
        ));
    } else {
        $profile = schoolProfileBuildDayGel2026(array(
            'profile_id' => 'ui-gel-' . date('YmdHis'),
            'school' => array(
                'type' => 'Ημερήσιο Γενικό Λύκειο',
                'name' => $schoolName !== '' ? $schoolName : 'Προσωρινό προφίλ ΓΕΛ',
            ),
            'source' => array('kind' => 'manual_frontend_test'),
            'general_sections' => array(
                'Α΄' => staffingUiInt('gel_general_a'),
                'Β΄' => staffingUiInt('gel_general_b'),
                'Γ΄' => staffingUiInt('gel_general_c'),
            ),
            'orientation_sections' => array(
                'Β΄' => array('humanities'=>staffingUiInt('gel_b_hum'),'science'=>staffingUiInt('gel_b_sci')),
                'Γ΄' => array('humanities'=>staffingUiInt('gel_c_hum'),'science_health'=>staffingUiInt('gel_c_scihealth'),'economics_it'=>staffingUiInt('gel_c_econit')),
            ),
            'second_foreign_language_groups' => array(
                'Α΄' => array('Γαλλικά'=>staffingUiInt('gel_lang_a_fr'),'Γερμανικά'=>staffingUiInt('gel_lang_a_de')),
                'Β΄' => array('Γαλλικά'=>staffingUiInt('gel_lang_b_fr'),'Γερμανικά'=>staffingUiInt('gel_lang_b_de')),
            ),
            'grade_c_science_health_field_groups' => array(
                'Μαθηματικά' => staffingUiInt('gel_c_field_math'),
                'Βιολογία' => staffingUiInt('gel_c_field_bio'),
            ),
            'grade_c_conditional_groups' => array(
                'Μαθηματικά' => staffingUiInt('gel_c_cond_math'),
                'Ιστορία' => staffingUiInt('gel_c_cond_history'),
            ),
            'ethics_by_grade' => array(
                'Α΄' => staffingUiEthicsGrade('a'),
                'Β΄' => staffingUiEthicsGrade('b'),
                'Γ΄' => staffingUiEthicsGrade('c'),
            ),
        ));
    }
    $readiness = schoolProfileGeneralEducationReadiness($profile);
    // Build the deterministic workload model once per explicit calculation and
    // reuse it for both the matrix and the personnel-specialty catalogue.
    $teachingModel = teachingWorkloadModel();
    $matrix = schoolProfileWorkloadMatrix($profile, $teachingModel);
    $presentation = staffingUiCollapseSkillsWorkshops($matrix);
    $displayMatrix = staffingUiSortCodesNatural($presentation['matrix']);
    $collapsedSkills = $presentation['collapsed'];
}
$generalSectionTotal = $profile ? schoolProfileTotalGeneralSections($profile) : 0;
$directorSectionsBandAuto = personnelWorkloadDirectorSectionsBandFromCount($generalSectionTotal);

$activePanel = staffingUiPost('active_panel', $submitted ? 'results' : 'school');
if (!in_array($activePanel, array('school','results','personnel','allocation'), true)) $activePanel = $submitted ? 'results' : 'school';
if ($staffingAction === 'personnel') $activePanel = 'personnel';
if ($staffingAction === 'allocation') $activePanel = 'allocation';

$personnelRows = staffingUiPersonnelRowsFromPost();
$duplicateDirectorIndexes = array();
$seenDirector = false;
foreach ($personnelRows as $personnelIndex=>$person) {
    if (isset($person['role']) && $person['role'] === 'director') {
        if ($seenDirector) {
            $duplicateDirectorIndexes[$personnelIndex] = true;
        } else {
            $seenDirector = true;
        }
    }
}
$personnelEvaluations = array();
$personnelSummary = array(
    'people_count'=>0,
    'resolved_count'=>0,
    'unresolved_count'=>0,
    'required_hours'=>0,
    'external_hours'=>0,
    'available_here_hours'=>0,
    'by_code'=>array(),
);
if (!empty($personnelRows)) {
    foreach ($personnelRows as $personnelIndex=>$person) {
        $personForEvaluation = $person;
        if (isset($personForEvaluation['role']) && $personForEvaluation['role'] === 'director') {
            $personForEvaluation['school_general_section_count'] = $generalSectionTotal;
        }
        if (isset($duplicateDirectorIndexes[$personnelIndex])) {
            $normalized = array(
                'status'=>'invalid',
                'valid'=>false,
                'reason'=>'multiple_directors_not_allowed',
                'specialty_code'=>isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '',
            );
        } else {
            $normalized = personnelWorkloadNormalizePerson($personForEvaluation);
        }
        $personnelEvaluations[$person['person_id']] = $normalized;
        $personnelSummary['people_count']++;
        $code = isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '';
        if ($code !== '' && !isset($personnelSummary['by_code'][$code])) {
            $personnelSummary['by_code'][$code] = array(
                'code'=>$code,
                'label'=>teacherSpecialtyLabel($code),
                'people_count'=>0,
                'resolved_count'=>0,
                'required_hours'=>0,
                'external_hours'=>0,
                'available_here_hours'=>0,
            );
        }
        if ($code !== '') $personnelSummary['by_code'][$code]['people_count']++;
        if ($normalized['status'] !== 'resolved') {
            $personnelSummary['unresolved_count']++;
            continue;
        }
        $personnelSummary['resolved_count']++;
        $required = (int)$normalized['required_teaching_hours'];
        $external = (int)$normalized['assigned_external_hours'];
        $available = (int)$normalized['remaining_before_profile_hours'];
        $personnelSummary['required_hours'] += $required;
        $personnelSummary['external_hours'] += $external;
        $personnelSummary['available_here_hours'] += $available;
        if ($code !== '') {
            $personnelSummary['by_code'][$code]['resolved_count']++;
            $personnelSummary['by_code'][$code]['required_hours'] += $required;
            $personnelSummary['by_code'][$code]['external_hours'] += $external;
            $personnelSummary['by_code'][$code]['available_here_hours'] += $available;
        }
    }
}
uksort($personnelSummary['by_code'], 'strnatcmp');
// Do not build the full teaching-workload model on the initial GET merely to
// populate a template inside a disabled tab. The options become necessary only
// after an explicit staffing calculation has unlocked the personnel stage.
$personnelSpecialtyOptions = $submitted
    ? staffingUiPersonnelSpecialtyOptions($displayMatrix, $teachingModel)
    : array('relevant'=>array(), 'other'=>array());

$allocationRows = staffingUiAllocationRowsFromPost();
$allocationSlots = ($profile && $matrix) ? staffingUiSortAllocationSlots(personnelWorkloadAllocationSlots($profile, $matrix)) : array();
$allocationPeople = array();
foreach ($personnelRows as $personnelIndex=>$person) {
    if (isset($duplicateDirectorIndexes[$personnelIndex])) continue;
    $p = $person;
    if (isset($p['role']) && $p['role'] === 'director') $p['school_general_section_count'] = $generalSectionTotal;
    $allocationPeople[] = $p;
}
$allocationPlan = null;
$allocationRowResults = array();
if ($profile && $matrix && !empty($allocationPeople) && !empty($allocationRows)) {
    $allocationPlan = personnelWorkloadRosterSlotPlan($profile, $allocationPeople, $allocationRows);
    if (isset($allocationPlan['allocation_rows'])) $allocationRowResults = $allocationPlan['allocation_rows'];
}
$allocationEnabled = $submitted && $profile && $matrix && $matrix['readiness'] !== 'structure_only' && $personnelSummary['resolved_count'] > 0 && empty($duplicateDirectorIndexes);
$allocationPeopleClient = array();
foreach ($allocationPeople as $person) {
    $normalized = personnelWorkloadNormalizePerson($person);
    if ($normalized['status'] !== 'resolved') continue;
    $allocationPeopleClient[$person['person_id']] = array(
        'person_id'=>$person['person_id'],
        'display_name'=>$person['display_name'],
        'specialty_code'=>$person['specialty_code'],
        'secondary_specialty_code'=>isset($person['secondary_specialty_code']) ? $person['secondary_specialty_code'] : '',
        'required_hours'=>(int)$normalized['required_teaching_hours'],
        'external_hours'=>(int)$normalized['assigned_external_hours'],
        'available_here_hours'=>(int)$normalized['remaining_before_profile_hours'],
    );
}
$allocationSlotsClient = array();
foreach ($allocationSlots as $slotId=>$slot) {
    $allocationSlotsClient[$slotId] = array(
        'slot_id'=>$slotId,
        'label'=>staffingUiAllocationSlotOptionLabel($slot),
        'slot_label'=>$slot['slot_label'],
        'grade'=>$slot['grade'],
        'subject'=>$slot['subject'],
        'capacity_hours'=>(int)$slot['capacity_hours'],
        'eligible_by_priority'=>$slot['eligible_by_priority'],
        'top_priority'=>isset($slot['top_priority']) ? $slot['top_priority'] : null,
    );
}
?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Υπολογισμός διδακτικών αναγκών σχολικής μονάδας</title>
  <link rel="stylesheet" href="<?php echo staffingUiH(edu_asset_url('assets/common.css')); ?>">
  <style>
    .edu-page-staffing-simulator .staffing-section{margin-top:18px;padding-top:4px;border-top:1px solid var(--edu-result-row-separator)}
    .edu-page-staffing-simulator .staffing-section:first-of-type{border-top:0;margin-top:8px}
    .edu-page-staffing-simulator .staffing-section h3{margin:12px 0 10px}
    .edu-page-staffing-simulator .mini-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
    .edu-page-staffing-simulator .mini-grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}
    .edu-page-staffing-simulator .grade-box{padding:13px;border:1px solid var(--edu-border);border-radius:12px;background:var(--edu-surface-soft);margin:10px 0}
    .edu-page-staffing-simulator .grade-box h4{margin:0 0 10px;color:var(--edu-primary-dark)}
    .edu-page-staffing-simulator .staffing-summary-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:12px 0}
    .edu-page-staffing-simulator .summary-chip{padding:12px;border:1px solid var(--edu-border);border-radius:12px;background:var(--edu-surface-soft)}
    .edu-page-staffing-simulator .summary-chip strong{display:block;font-size:1.35rem;color:var(--edu-primary-dark);font-variant-numeric:tabular-nums}
    .edu-page-staffing-simulator .summary-chip span{display:block;margin-top:3px;color:var(--edu-muted);font-size:12.5px;line-height:1.3}
    .edu-page-staffing-simulator .matrix-wrap{overflow-x:auto;margin-top:14px}
    .edu-page-staffing-simulator .staffing-table{width:100%;border-collapse:collapse;min-width:850px}
    .edu-page-staffing-simulator .staffing-table th,.edu-page-staffing-simulator .staffing-table td{padding:9px 8px;border-bottom:1px solid var(--edu-result-row-separator);text-align:right;vertical-align:top;font-size:13.5px}
    .edu-page-staffing-simulator .staffing-table th:first-child,.edu-page-staffing-simulator .staffing-table td:first-child{text-align:left;position:sticky;left:0;background:var(--edu-surface);z-index:1}
    .edu-page-staffing-simulator .staffing-table th{color:var(--edu-muted);font-size:12px;white-space:nowrap}
    .edu-page-staffing-simulator .staffing-table .code{font-weight:800;color:var(--edu-primary-dark);white-space:nowrap}
    .edu-page-staffing-simulator details.staffing-details{margin:0}
    .edu-page-staffing-simulator details.staffing-details summary{cursor:pointer;font-weight:800;color:var(--edu-primary-dark);list-style:none}
    .edu-page-staffing-simulator details.staffing-details summary::-webkit-details-marker{display:none}
    .edu-page-staffing-simulator .claim-list{margin:8px 0 0;padding:0;list-style:none;min-width:360px}
    .edu-page-staffing-simulator .claim-list li{padding:7px 0;border-top:1px solid var(--edu-result-row-separator);line-height:1.35}
    .edu-page-staffing-simulator .claim-meta{display:block;color:var(--edu-muted);font-size:12px;margin-top:2px}
    .edu-page-staffing-simulator .staffing-stage-toolbar{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin:0 0 14px}
    .edu-page-staffing-simulator .mode-tabs{display:flex;gap:8px;flex-wrap:wrap;margin:0}
    .edu-page-staffing-simulator .staffing-print-btn{white-space:nowrap}
    .edu-page-staffing-simulator .mode-tab{padding:9px 12px;border:1px solid var(--edu-border);border-radius:999px;background:var(--edu-surface-soft);font-weight:800;color:var(--edu-muted)}
    .edu-page-staffing-simulator .mode-tab.is-active{background:var(--edu-primary-soft);border-color:var(--edu-primary);color:var(--edu-primary-dark)}
    .edu-page-staffing-simulator .result-filter{margin:12px 0 4px}
    .edu-page-staffing-simulator .status-good{background:var(--edu-success-soft);border:1px solid var(--edu-success-border);color:var(--edu-success);padding:11px 12px;border-radius:10px;margin:12px 0}
    .edu-page-staffing-simulator .status-warn{background:var(--edu-warning-soft);border:1px solid var(--edu-warning-border);color:#7b4900;padding:11px 12px;border-radius:10px;margin:12px 0}
    .edu-page-staffing-simulator details.option-panel{margin:14px 0;border:1px solid var(--edu-border);border-radius:12px;background:var(--edu-surface-soft);overflow:hidden}
    .edu-page-staffing-simulator details.option-panel>summary{cursor:pointer;padding:13px 15px;font-weight:800;color:var(--edu-primary-dark);list-style:none;display:flex;align-items:center;justify-content:space-between;gap:12px}
    .edu-page-staffing-simulator details.option-panel>summary::-webkit-details-marker{display:none}
    .edu-page-staffing-simulator details.option-panel>summary::after{content:'＋';font-size:1.1rem;color:var(--edu-muted)}
    .edu-page-staffing-simulator details.option-panel[open]>summary::after{content:'−'}
    .edu-page-staffing-simulator .option-panel-body{padding:0 15px 14px;border-top:1px solid var(--edu-result-row-separator)}
    .edu-page-staffing-simulator .staffing-panel[hidden]{display:none!important}
    .edu-page-staffing-simulator button.mode-tab{font:inherit;cursor:pointer}
    .edu-page-staffing-simulator .personnel-toolbar{display:flex;gap:10px;align-items:end;justify-content:space-between;flex-wrap:wrap;margin:12px 0}
    .edu-page-staffing-simulator .personnel-toolbar .field{min-width:240px;flex:1}
    .edu-page-staffing-simulator .personnel-list{display:grid;gap:10px;margin-top:12px}
    .edu-page-staffing-simulator .personnel-row{border:1px solid var(--edu-border);border-radius:12px;background:var(--edu-surface);overflow:hidden}
    .edu-page-staffing-simulator .personnel-row-main{display:grid;grid-template-columns:minmax(150px,.8fr) minmax(200px,1.4fr) repeat(3,minmax(92px,.55fr)) auto;gap:10px;align-items:end;padding:12px}
    .edu-page-staffing-simulator .personnel-row-main .metric{padding:8px 10px;border-radius:9px;background:var(--edu-surface-soft);min-height:42px}
    .edu-page-staffing-simulator .personnel-row-main .metric strong{display:block;font-size:1.05rem;color:var(--edu-primary-dark);font-variant-numeric:tabular-nums}
    .edu-page-staffing-simulator .personnel-row-main .metric span{font-size:11.5px;color:var(--edu-muted)}
    .edu-page-staffing-simulator .personnel-required[readonly]{background:var(--edu-surface-soft);color:var(--edu-primary-dark);font-weight:800}
    .edu-page-staffing-simulator .personnel-service-fields[hidden]{display:none!important}
    .edu-page-staffing-simulator .personnel-row details{border-top:1px solid var(--edu-result-row-separator)}
    .edu-page-staffing-simulator .personnel-row details>summary{cursor:pointer;padding:9px 12px;font-weight:700;color:var(--edu-muted);list-style:none}
    .edu-page-staffing-simulator .personnel-row details>summary::-webkit-details-marker{display:none}
    .edu-page-staffing-simulator .personnel-row-details{padding:0 12px 12px}
    .edu-page-staffing-simulator .personnel-remove{border:1px solid var(--edu-border);background:var(--edu-surface-soft);border-radius:9px;padding:9px 10px;cursor:pointer;color:var(--edu-muted)}
    .edu-page-staffing-simulator .personnel-status-error{color:#9c2f2f;font-size:12px;margin:7px 12px 10px}
    .edu-page-staffing-simulator .personnel-toolbar-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
    .edu-page-staffing-simulator .personnel-csv-panel{margin:12px 0;padding:14px;border:1px solid var(--edu-border);border-radius:12px;background:var(--edu-surface-soft)}
    .edu-page-staffing-simulator .personnel-csv-panel[hidden]{display:none!important}
    .edu-page-staffing-simulator .personnel-csv-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap}
    .edu-page-staffing-simulator .personnel-csv-meta{color:var(--edu-muted);font-size:12.5px;margin-top:3px}
    .edu-page-staffing-simulator .personnel-csv-mappings{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px;margin-top:10px}
    .edu-page-staffing-simulator .personnel-csv-mappings .field{min-width:0}
    .edu-page-staffing-simulator .personnel-csv-preview{overflow-x:auto;margin-top:12px;max-height:270px;overflow-y:auto;border:1px solid var(--edu-border);border-radius:10px;background:var(--edu-surface)}
    .edu-page-staffing-simulator .personnel-csv-preview table{width:100%;border-collapse:collapse;min-width:650px}
    .edu-page-staffing-simulator .personnel-csv-preview th,.edu-page-staffing-simulator .personnel-csv-preview td{padding:7px 8px;border-bottom:1px solid var(--edu-result-row-separator);text-align:left;font-size:12px;white-space:nowrap}
    .edu-page-staffing-simulator .personnel-csv-preview th{position:sticky;top:0;background:var(--edu-surface-soft);z-index:1;color:var(--edu-muted)}
    .edu-page-staffing-simulator .personnel-csv-actions{display:flex;gap:8px;align-items:end;justify-content:space-between;flex-wrap:wrap;margin-top:12px}
    .edu-page-staffing-simulator .personnel-csv-actions .field{min-width:220px}
    .edu-page-staffing-simulator .personnel-csv-status{font-size:12.5px;margin-top:10px;color:var(--edu-muted)}
    .edu-page-staffing-simulator .personnel-csv-status.is-error{color:#9c2f2f}
    .edu-page-staffing-simulator .personnel-csv-status.is-success{color:var(--edu-success)}
    .edu-page-staffing-simulator .branch-summary{display:grid;gap:8px;margin:12px 0}
    .edu-page-staffing-simulator .branch-summary-row{display:grid;grid-template-columns:minmax(140px,.8fr) repeat(4,minmax(110px,.65fr));gap:8px;align-items:center;padding:10px 12px;border:1px solid var(--edu-border);border-radius:10px;background:var(--edu-surface-soft)}
    .edu-page-staffing-simulator .branch-summary-row .branch-code{font-weight:800;color:var(--edu-primary-dark)}
    .edu-page-staffing-simulator .branch-summary-row small{display:block;color:var(--edu-muted)}
    .edu-page-staffing-simulator .empty-personnel{padding:18px;border:1px dashed var(--edu-border);border-radius:12px;text-align:center;color:var(--edu-muted);background:var(--edu-surface-soft)}
    .edu-page-staffing-simulator .allocation-toolbar{display:flex;justify-content:space-between;gap:10px;align-items:end;flex-wrap:wrap;margin:12px 0}
    .edu-page-staffing-simulator .allocation-list{display:grid;gap:10px;margin:12px 0}
    .edu-page-staffing-simulator .allocation-row{border:1px solid var(--edu-border);border-radius:12px;background:var(--edu-surface);padding:12px}
    .edu-page-staffing-simulator .allocation-row-main{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.65fr) minmax(86px,.42fr) auto;gap:10px;align-items:end}
    .edu-page-staffing-simulator .allocation-row-main>*{min-width:0}
    .edu-page-staffing-simulator .allocation-row-main .field select,.edu-page-staffing-simulator .allocation-row-main .field input{min-width:0;width:100%}
    .edu-page-staffing-simulator .allocation-status{grid-column:1/-1;grid-row:2;min-width:0;min-height:38px;display:flex;align-items:center;padding:8px 10px;border-radius:9px;background:var(--edu-surface-soft);border:1px solid var(--edu-border);font-size:12.5px;font-weight:700;color:var(--edu-muted);overflow-wrap:anywhere}
    .edu-page-staffing-simulator .allocation-row-main .allocation-remove{grid-column:4;grid-row:1}
    .edu-page-staffing-simulator .allocation-status.is-ok{color:var(--edu-success);border-color:rgba(39,128,85,.35)}
    .edu-page-staffing-simulator .allocation-status.is-warning{color:#8a6400;border-color:rgba(138,100,0,.3)}
    .edu-page-staffing-simulator .allocation-status.is-error{color:#9c2f2f;border-color:rgba(156,47,47,.3)}
    .edu-page-staffing-simulator .profile-validation-error{display:block;margin-top:6px;color:#9c2f2f;font-size:.86rem;line-height:1.35}
    .edu-page-staffing-simulator .allocation-person-summary{display:grid;gap:8px;margin:12px 0}
    .edu-page-staffing-simulator .allocation-person-summary-row{display:grid;grid-template-columns:minmax(180px,1fr) repeat(4,minmax(100px,.55fr));gap:8px;align-items:center;padding:9px 11px;border:1px solid var(--edu-border);border-radius:10px;background:var(--edu-surface-soft)}
    .edu-page-staffing-simulator .allocation-person-summary-row small{display:block;color:var(--edu-muted)}
    .edu-page-staffing-simulator .allocation-empty{padding:18px;border:1px dashed var(--edu-border);border-radius:12px;text-align:center;color:var(--edu-muted);background:var(--edu-surface-soft)}
    .staffing-print-report{display:none}
    @media print{
      @page{size:A4 landscape;margin:10mm}
      body.edu-page-staffing-simulator{background:#fff!important;color:#111!important;font-family:Arial,Helvetica,sans-serif!important}
      body.edu-page-staffing-simulator> *:not(.staffing-print-report):not(script){display:none!important}
      body.edu-page-staffing-simulator .staffing-print-report{display:block!important;width:100%;max-width:none;margin:0;padding:0;color:#111;background:#fff}
      .staffing-print-report *{box-sizing:border-box}
      .staffing-print-report .print-header{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;border-bottom:2px solid #222;padding:0 0 8px;margin:0 0 10px}
      .staffing-print-report h1{font-size:18pt;line-height:1.15;margin:0 0 4px;color:#111}
      .staffing-print-report .print-subtitle{font-size:10pt;margin:0;color:#444}
      .staffing-print-report .print-meta{text-align:right;font-size:8.5pt;line-height:1.35;color:#555;white-space:nowrap}
      .staffing-print-report h2{font-size:12pt;margin:14px 0 6px;padding-bottom:3px;border-bottom:1px solid #777;color:#111;break-after:avoid}
      .staffing-print-report .print-summary{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:6px;margin:7px 0 10px}
      .staffing-print-report .print-summary-item{border:1px solid #bbb;border-radius:5px;padding:6px 7px;min-height:43px}
      .staffing-print-report .print-summary-item strong{display:block;font-size:12pt;line-height:1.1}
      .staffing-print-report .print-summary-item span{display:block;font-size:7.5pt;line-height:1.25;color:#555;margin-top:2px}
      .staffing-print-report table{width:100%;border-collapse:collapse;table-layout:fixed;margin:4px 0 10px;font-size:7.8pt}
      .staffing-print-report th,.staffing-print-report td{border:1px solid #b9b9b9;padding:4px 5px;vertical-align:top;overflow-wrap:anywhere}
      .staffing-print-report th{background:#efefef;font-weight:700;text-align:left}
      .staffing-print-report td.num,.staffing-print-report th.num{text-align:center;font-variant-numeric:tabular-nums}
      .staffing-print-report tr{break-inside:avoid}
      .staffing-print-report .print-code{font-weight:700;white-space:nowrap}
      .staffing-print-report .print-note{font-size:7.7pt;line-height:1.35;color:#444;margin:5px 0 9px}
      .staffing-print-report .print-status-ok{font-weight:700}
      .staffing-print-report .print-status-warn{font-weight:700}
      .staffing-print-report .print-status-error{font-weight:700}
      .staffing-print-report .print-footer{margin-top:12px;padding-top:6px;border-top:1px solid #999;font-size:7.2pt;line-height:1.35;color:#555}
      .staffing-print-report .print-personnel th:nth-child(1){width:11%}
      .staffing-print-report .print-personnel th:nth-child(2){width:28%}
      .staffing-print-report .print-personnel th:nth-child(3){width:17%}
      .staffing-print-report .print-allocation th:nth-child(1){width:25%}
      .staffing-print-report .print-allocation th:nth-child(2){width:39%}
      .staffing-print-report .print-allocation th:nth-child(3){width:7%}
      .staffing-print-report .print-allocation th:nth-child(4){width:29%}
    }
    @media(max-width:960px){.edu-page-staffing-simulator .personnel-row-main{grid-template-columns:1fr 1fr}.edu-page-staffing-simulator .branch-summary-row{grid-template-columns:1fr 1fr}.edu-page-staffing-simulator .allocation-row-main{grid-template-columns:1fr 1fr}.edu-page-staffing-simulator .allocation-row-main .allocation-status{grid-column:1/-1;grid-row:auto}.edu-page-staffing-simulator .allocation-row-main .allocation-remove{grid-column:auto;grid-row:auto}.edu-page-staffing-simulator .allocation-person-summary-row{grid-template-columns:1fr 1fr}}
    @media(max-width:760px){.edu-page-staffing-simulator .mini-grid,.edu-page-staffing-simulator .mini-grid.two,.edu-page-staffing-simulator .staffing-summary-grid,.edu-page-staffing-simulator .personnel-row-main,.edu-page-staffing-simulator .branch-summary-row,.edu-page-staffing-simulator .personnel-csv-mappings{grid-template-columns:1fr}.edu-page-staffing-simulator .allocation-row-main,.edu-page-staffing-simulator .allocation-person-summary-row{grid-template-columns:1fr}.edu-page-staffing-simulator .staffing-table th:first-child,.edu-page-staffing-simulator .staffing-table td:first-child{position:static}}
  </style>
</head>
<body class="edu-ui edu-calc-standard edu-page-staffing-simulator">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<main id="schoolStaffingSimulator" class="app">
  <?php calculatorHero(array(
    'title_html' => 'Υπολογισμός διδακτικών αναγκών σχολικής μονάδας',
    'intro' => 'Καταχώρισε τα πραγματικά στοιχεία ενός Ημερήσιου Γυμνασίου ή ΓΕΛ και δες πώς μετατρέπεται το ωρολόγιο πρόγραμμα σε ώρες ανά κλάδο και προτεραιότητα ανάθεσης.',
    'meta_class' => 'meta',
    'badges' => array('2026–2027','Στοιχεία σχολικής μονάδας','Ωρολόγιο + Αναθέσεις','Α΄ · Β΄ · Γ΄','Ηθική','Χωρίς αυτόματες τοποθετήσεις')
  )); ?>

  <div class="staffing-stage-toolbar">
    <div class="mode-tabs" aria-label="Στάδια εργαλείου" role="tablist">
      <button type="button" class="mode-tab<?php echo $activePanel === 'school' ? ' is-active' : ''; ?>" data-staffing-tab="school" role="tab" aria-selected="<?php echo $activePanel === 'school' ? 'true' : 'false'; ?>">1. Σχολική μονάδα</button>
      <button type="button" class="mode-tab<?php echo $activePanel === 'results' ? ' is-active' : ''; ?>" data-staffing-tab="results" role="tab" aria-selected="<?php echo $activePanel === 'results' ? 'true' : 'false'; ?>"<?php echo !$submitted ? ' disabled' : ''; ?>>2. Αποτελέσματα ανά κλάδο</button>
      <button type="button" class="mode-tab<?php echo $activePanel === 'personnel' ? ' is-active' : ''; ?>" data-staffing-tab="personnel" role="tab" aria-selected="<?php echo $activePanel === 'personnel' ? 'true' : 'false'; ?>"<?php echo !$submitted ? ' disabled' : ''; ?>>3. Εκπαιδευτικοί</button>
      <button type="button" class="mode-tab<?php echo $activePanel === 'allocation' ? ' is-active' : ''; ?>" data-staffing-tab="allocation" role="tab" aria-selected="<?php echo $activePanel === 'allocation' ? 'true' : 'false'; ?>"<?php echo !$allocationEnabled ? ' disabled' : ''; ?>>4. Κατανομή μαθημάτων</button>
    </div>
    <?php if ($submitted && $matrix && $displayMatrix): ?>
      <button type="button" class="edu-btn-secondary staffing-print-btn" id="staffingPrintButton">Εκτύπωση</button>
    <?php endif; ?>
  </div>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('class'=>'card staffing-panel','attrs'=>array('data-staffing-panel'=>'school') + ($activePanel !== 'school' ? array('hidden'=>true) : array()))); ?>
        <h2>1. Στοιχεία σχολικής μονάδας</h2>
        <p class="cap">Η πρώτη έκδοση υποστηρίζει τυπικό Ημερήσιο Γυμνάσιο και Ημερήσιο ΓΕΛ. Οι αριθμοί αφορούν πραγματικά τμήματα / ομάδες διδασκαλίας και όχι οργανικές θέσεις.</p>

        <form method="post" id="staffingProfileForm">
          <input type="hidden" name="staffing_action" value="">
          <div class="field-grid">
            <div class="field">
              <label for="school_type">Τύπος σχολείου</label>
              <select id="school_type" name="school_type">
                <option value="gymnasio"<?php echo $schoolType === 'gymnasio' ? ' selected' : ''; ?>>Ημερήσιο Γυμνάσιο</option>
                <option value="gel"<?php echo $schoolType === 'gel' ? ' selected' : ''; ?>>Ημερήσιο Γενικό Λύκειο (ΓΕΛ)</option>
              </select>
            </div>
            <div class="field">
              <label for="school_name">Ονομασία σχολείου <small>προαιρετικό</small></label>
              <input id="school_name" name="school_name" type="text" value="<?php echo staffingUiH(staffingUiPost('school_name')); ?>" placeholder="π.χ. 1ο Γυμνάσιο Κέρκυρας">
            </div>
          </div>

          <div id="gymProfileFields"<?php echo $schoolType === 'gymnasio' ? '' : ' hidden'; ?>>
            <section class="staffing-section">
              <h3>Κανονικά τμήματα ανά τάξη</h3>
              <div class="mini-grid">
                <?php foreach (array('a'=>'Α΄','b'=>'Β΄','c'=>'Γ΄') as $s=>$grade): ?>
                  <div class="field"><label for="gym_general_<?php echo $s; ?>"><?php echo $grade; ?> τάξη</label><input min="0" step="1" type="number" id="gym_general_<?php echo $s; ?>" name="gym_general_<?php echo $s; ?>" value="<?php echo staffingUiH(staffingUiPost('gym_general_'.$s, '0')); ?>"></div>
                <?php endforeach; ?>
              </div>
            </section>
            <section class="staffing-section">
              <h3>Ομάδες 2ης ξένης γλώσσας</h3>
              <p class="help">Δήλωσε τις πραγματικές ομάδες γλώσσας, όχι τον αριθμό μαθητών. <strong>Κάθε γλώσσα ελέγχεται χωριστά</strong> και δεν μπορεί να έχει περισσότερες ομάδες από τα κανονικά τμήματα της ίδιας τάξης· το άθροισμα Γαλλικών + Γερμανικών + Ιταλικών δεν περιορίζεται σε αυτόν τον αριθμό.</p>
              <?php foreach (array('a'=>'Α΄','b'=>'Β΄','c'=>'Γ΄') as $s=>$grade): ?>
                <div class="grade-box">
                  <h4><?php echo $grade; ?> τάξη</h4>
                  <div class="mini-grid">
                    <div class="field"><label for="gym_lang_<?php echo $s; ?>_fr">Γαλλικά</label><input min="0" max="<?php echo (int) staffingUiInt('gym_general_'.$s); ?>" step="1" type="number" id="gym_lang_<?php echo $s; ?>_fr" name="gym_lang_<?php echo $s; ?>_fr" data-language-max-source="gym_general_<?php echo $s; ?>" data-language-grade="<?php echo staffingUiH($grade); ?>" data-language-name="Γαλλικά" aria-describedby="gym_lang_<?php echo $s; ?>_fr_error" value="<?php echo staffingUiH(staffingUiPost('gym_lang_'.$s.'_fr', '0')); ?>"><small class="profile-validation-error" id="gym_lang_<?php echo $s; ?>_fr_error" data-language-error-for="gym_lang_<?php echo $s; ?>_fr" hidden></small></div>
                    <div class="field"><label for="gym_lang_<?php echo $s; ?>_de">Γερμανικά</label><input min="0" max="<?php echo (int) staffingUiInt('gym_general_'.$s); ?>" step="1" type="number" id="gym_lang_<?php echo $s; ?>_de" name="gym_lang_<?php echo $s; ?>_de" data-language-max-source="gym_general_<?php echo $s; ?>" data-language-grade="<?php echo staffingUiH($grade); ?>" data-language-name="Γερμανικά" aria-describedby="gym_lang_<?php echo $s; ?>_de_error" value="<?php echo staffingUiH(staffingUiPost('gym_lang_'.$s.'_de', '0')); ?>"><small class="profile-validation-error" id="gym_lang_<?php echo $s; ?>_de_error" data-language-error-for="gym_lang_<?php echo $s; ?>_de" hidden></small></div>
                    <div class="field"><label for="gym_lang_<?php echo $s; ?>_it">Ιταλικά</label><input min="0" max="<?php echo (int) staffingUiInt('gym_general_'.$s); ?>" step="1" type="number" id="gym_lang_<?php echo $s; ?>_it" name="gym_lang_<?php echo $s; ?>_it" data-language-max-source="gym_general_<?php echo $s; ?>" data-language-grade="<?php echo staffingUiH($grade); ?>" data-language-name="Ιταλικά" aria-describedby="gym_lang_<?php echo $s; ?>_it_error" value="<?php echo staffingUiH(staffingUiPost('gym_lang_'.$s.'_it', '0')); ?>"><small class="profile-validation-error" id="gym_lang_<?php echo $s; ?>_it_error" data-language-error-for="gym_lang_<?php echo $s; ?>_it" hidden></small></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </section>

            <details class="option-panel" id="technologyInformaticsPanel"<?php echo (staffingUiInt('gym_tech_split_a') + staffingUiInt('gym_tech_split_b') + staffingUiInt('gym_tech_split_c')) > 0 ? ' open' : ''; ?>>
              <summary>Χωρισμός Τεχνολογίας / Πληροφορικής <small style="font-weight:400;color:var(--edu-muted)">μόνο για τμήματα άνω των 21 μαθητών</small></summary>
              <div class="option-panel-body">
                <p class="help">Όταν ένα τμήμα έχει πάνω από 21 μαθητές/ήτριες, χωρίζεται σε δύο ομάδες. Δήλωσε πόσα από τα κανονικά τμήματα κάθε τάξης ξεπερνούν το όριο. Αν κανένα δεν ξεπερνά τους 21, άφησε 0.</p>
                <div class="mini-grid">
                  <?php foreach (array('a'=>'Α΄','b'=>'Β΄','c'=>'Γ΄') as $s=>$grade): ?>
                    <div class="field"><label for="gym_tech_split_<?php echo $s; ?>"><?php echo $grade; ?> τάξη <small>τμήματα με &gt;21 μαθητές · μέγιστο <span data-max-label="gym_general_<?php echo $s; ?>"><?php echo (int) staffingUiInt('gym_general_'.$s); ?></span></small></label><input min="0" max="<?php echo (int) staffingUiInt('gym_general_'.$s); ?>" step="1" type="number" id="gym_tech_split_<?php echo $s; ?>" name="gym_tech_split_<?php echo $s; ?>" data-max-source="gym_general_<?php echo $s; ?>" value="<?php echo staffingUiH(staffingUiPost('gym_tech_split_'.$s, '0')); ?>"></div>
                  <?php endforeach; ?>
                </div>
                <p class="help"><strong>Α΄ τάξη:</strong> ο χωρισμός αυξάνει τις ομάδες Πληροφορικής, Τεχνολογίας και Οικιακής Οικονομίας. <strong>Β΄–Γ΄:</strong> αυξάνει τις ομάδες Πληροφορικής και Τεχνολογίας.</p>
              </div>
            </details>
          </div>

          <div id="gelProfileFields"<?php echo $schoolType === 'gel' ? '' : ' hidden'; ?>>
            <section class="staffing-section">
              <h3>Κανονικά τμήματα ανά τάξη</h3>
              <div class="mini-grid">
                <?php foreach (array('a'=>'Α΄','b'=>'Β΄','c'=>'Γ΄') as $s=>$grade): ?>
                  <div class="field"><label for="gel_general_<?php echo $s; ?>"><?php echo $grade; ?> τάξη</label><input min="0" step="1" type="number" id="gel_general_<?php echo $s; ?>" name="gel_general_<?php echo $s; ?>" value="<?php echo staffingUiH(staffingUiPost('gel_general_'.$s, '0')); ?>"></div>
                <?php endforeach; ?>
              </div>
            </section>
            <section class="staffing-section">
              <h3>Ομάδες 2ης ξένης γλώσσας</h3>
              <p class="help"><strong>Κάθε γλώσσα ελέγχεται χωριστά</strong> και δεν μπορεί να έχει περισσότερες ομάδες από τα κανονικά τμήματα της ίδιας τάξης. Δεν περιορίζεται το άθροισμα Γαλλικών + Γερμανικών.</p>
              <div class="mini-grid two">
                <?php foreach (array('a'=>'Α΄','b'=>'Β΄') as $s=>$grade): ?>
                  <div class="grade-box">
                    <h4><?php echo $grade; ?> τάξη</h4>
                    <div class="mini-grid two">
                      <div class="field"><label for="gel_lang_<?php echo $s; ?>_fr">Γαλλικά</label><input min="0" max="<?php echo (int) staffingUiInt('gel_general_'.$s); ?>" step="1" type="number" id="gel_lang_<?php echo $s; ?>_fr" name="gel_lang_<?php echo $s; ?>_fr" data-language-max-source="gel_general_<?php echo $s; ?>" data-language-grade="<?php echo staffingUiH($grade); ?>" data-language-name="Γαλλικά" aria-describedby="gel_lang_<?php echo $s; ?>_fr_error" value="<?php echo staffingUiH(staffingUiPost('gel_lang_'.$s.'_fr', '0')); ?>"><small class="profile-validation-error" id="gel_lang_<?php echo $s; ?>_fr_error" data-language-error-for="gel_lang_<?php echo $s; ?>_fr" hidden></small></div>
                      <div class="field"><label for="gel_lang_<?php echo $s; ?>_de">Γερμανικά</label><input min="0" max="<?php echo (int) staffingUiInt('gel_general_'.$s); ?>" step="1" type="number" id="gel_lang_<?php echo $s; ?>_de" name="gel_lang_<?php echo $s; ?>_de" data-language-max-source="gel_general_<?php echo $s; ?>" data-language-grade="<?php echo staffingUiH($grade); ?>" data-language-name="Γερμανικά" aria-describedby="gel_lang_<?php echo $s; ?>_de_error" value="<?php echo staffingUiH(staffingUiPost('gel_lang_'.$s.'_de', '0')); ?>"><small class="profile-validation-error" id="gel_lang_<?php echo $s; ?>_de_error" data-language-error-for="gel_lang_<?php echo $s; ?>_de" hidden></small></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </section>
            <section class="staffing-section">
              <h3>Ομάδες Προσανατολισμού</h3>
              <div class="grade-box">
                <h4>Β΄ ΓΕΛ</h4>
                <div class="mini-grid two">
                  <div class="field"><label>Ανθρωπιστικών</label><input min="0" step="1" type="number" name="gel_b_hum" value="<?php echo staffingUiH(staffingUiPost('gel_b_hum', '0')); ?>"></div>
                  <div class="field"><label>Θετικών</label><input min="0" step="1" type="number" name="gel_b_sci" value="<?php echo staffingUiH(staffingUiPost('gel_b_sci', '0')); ?>"></div>
                </div>
              </div>
              <div class="grade-box">
                <h4>Γ΄ ΓΕΛ</h4>
                <div class="mini-grid">
                  <div class="field"><label>Ανθρωπιστικών</label><input min="0" step="1" type="number" name="gel_c_hum" value="<?php echo staffingUiH(staffingUiPost('gel_c_hum', '0')); ?>"></div>
                  <div class="field"><label>Θετικών / Υγείας</label><input min="0" step="1" type="number" name="gel_c_scihealth" value="<?php echo staffingUiH(staffingUiPost('gel_c_scihealth', '0')); ?>"></div>
                  <div class="field"><label>Οικονομίας / Πληροφορικής</label><input min="0" step="1" type="number" name="gel_c_econit" value="<?php echo staffingUiH(staffingUiPost('gel_c_econit', '0')); ?>"></div>
                </div>
              </div>
            </section>
            <section class="staffing-section">
              <h3>Γ΄ ΓΕΛ — ειδικές ομάδες</h3>
              <div class="grade-box">
                <h4>Θετικών / Υγείας: 2ο και 3ο πεδίο</h4>
                <div class="mini-grid two">
                  <div class="field"><label>Μαθηματικά <small>ομάδες 2ου πεδίου</small></label><input min="0" step="1" type="number" name="gel_c_field_math" value="<?php echo staffingUiH(staffingUiPost('gel_c_field_math', '0')); ?>"></div>
                  <div class="field"><label>Βιολογία <small>ομάδες 3ου πεδίου</small></label><input min="0" step="1" type="number" name="gel_c_field_bio" value="<?php echo staffingUiH(staffingUiPost('gel_c_field_bio', '0')); ?>"></div>
                </div>
              </div>
              <div class="grade-box">
                <h4>Μαθήματα Γενικής Παιδείας υπό προϋπόθεση</h4>
                <p class="help">Δήλωσε τις πραγματικές ομάδες που διδάσκονται τα αντίστοιχα μαθήματα· δεν τις εξάγουμε αυτόματα από τα γενικά τμήματα.</p>
                <div class="mini-grid two">
                  <div class="field"><label>Μαθηματικά Γενικής Παιδείας</label><input min="0" step="1" type="number" name="gel_c_cond_math" value="<?php echo staffingUiH(staffingUiPost('gel_c_cond_math', '0')); ?>"></div>
                  <div class="field"><label>Ιστορία Γενικής Παιδείας</label><input min="0" step="1" type="number" name="gel_c_cond_history" value="<?php echo staffingUiH(staffingUiPost('gel_c_cond_history', '0')); ?>"></div>
                </div>
              </div>
            </section>
          </div>

          <section class="staffing-section">
            <?php $ethicsHasInput = false; foreach (array('a','b','c') as $ethicsSuffix) { if (staffingUiPost('ethics_'.$ethicsSuffix.'_exempt') !== '' || staffingUiPost('ethics_'.$ethicsSuffix.'_timely') !== '' || staffingUiPost('ethics_'.$ethicsSuffix.'_equivalent') !== '') { $ethicsHasInput = true; break; } } ?>
            <details class="option-panel" id="ethicsPanel"<?php echo $ethicsHasInput ? ' open' : ''; ?>>
              <summary>Ηθική / Θρησκευτικά <small style="font-weight:400;color:var(--edu-muted)">άνοιξέ το μόνο αν θέλεις να υπολογιστεί</small></summary>
              <div class="option-panel-body">
                <p class="help">Αν δεν έχεις ακόμη τα στοιχεία απαλλαγών, άφησε την ενότητα κλειστή. Οι αντίστοιχες ώρες θα παραμείνουν σε εκκρεμότητα και δεν θα προστεθούν τεχνητά στα αποτελέσματα.</p>
                <?php foreach (array('a'=>'Α΄','b'=>'Β΄','c'=>'Γ΄') as $s=>$grade): ?>
                  <div class="grade-box">
                    <h4><?php echo $grade; ?> τάξη</h4>
                    <div class="mini-grid">
                      <div class="field"><label>Απαλλασσόμενοι/ες</label><input min="0" step="1" type="number" name="ethics_<?php echo $s; ?>_exempt" value="<?php echo staffingUiH(staffingUiPost('ethics_'.$s.'_exempt')); ?>" placeholder="άγνωστο"></div>
                      <div class="field"><label>Συμπληρώθηκαν έως 5η ημέρα;</label><select name="ethics_<?php echo $s; ?>_timely"><option value=""<?php echo staffingUiPost('ethics_'.$s.'_timely')===''?' selected':''; ?>>— άγνωστο —</option><option value="1"<?php echo staffingUiPost('ethics_'.$s.'_timely')==='1'?' selected':''; ?>>Ναι</option><option value="0"<?php echo staffingUiPost('ethics_'.$s.'_timely')==='0'?' selected':''; ?>>Όχι</option></select></div>
                      <div class="field"><label>Ισοδύναμα τμήματα Ηθικής <small>0 = κρίθηκε ότι δεν σχηματίζεται</small></label><input min="0" step="1" type="number" name="ethics_<?php echo $s; ?>_equivalent" value="<?php echo staffingUiH(staffingUiPost('ethics_'.$s.'_equivalent')); ?>" placeholder="άγνωστο"></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </details>
          </section>

          <div class="actions">
            <button class="edu-btn-primary" type="button" data-staffing-request-action="profile">Υπολόγισε διδακτικές ανάγκες</button><input type="hidden" name="active_panel" value="results">
            <button class="edu-btn-secondary" type="reset" id="staffingReset">Καθαρισμός</button>
          </div>
        </form>
      <?php calculatorCardEnd(); ?>

      <?php if ($submitted && $matrix && $displayMatrix): ?>
        <?php calculatorCardStart(array('class'=>'card staffing-results-card staffing-panel','attrs'=>array('data-staffing-panel'=>'results') + ($activePanel !== 'results' ? array('hidden'=>true) : array()))); ?>
          <h2>2. Αποτελέσματα ανά κλάδο</h2>
          <p class="cap">Τα αθροίσματα είναι ώρες του ωρολογίου προγράμματος για τις οποίες ο κάθε κλάδος είναι επιλέξιμος στη συγκεκριμένη σχολική μονάδα. Δεν αποτελούν ακόμη επίσημα λειτουργικά κενά ούτε τελική κατανομή σε εκπαιδευτικούς.</p>

          <?php if ($readiness && $readiness['ready']): ?>
            <div class="status-good"><strong>Τα στοιχεία της σχολικής μονάδας είναι δομικά πλήρη.</strong> Οι τυχόν εκκρεμότητες της Ηθικής ή τα κανονιστικά κενά εμφανίζονται χωριστά.</div>
          <?php else: ?>
            <div class="status-warn"><strong>Μερικός υπολογισμός.</strong> Λείπουν στοιχεία της σχολικής μονάδας, επομένως τα παρακάτω σύνολα δεν είναι πλήρη.
              <?php if ($readiness && !empty($readiness['issues'])): ?>
                <ul>
                  <?php foreach ($readiness['issues'] as $issue): ?><li><?php echo staffingUiH(staffingUiIssueLabel($issue)); ?></li><?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="staffing-summary-grid">
            <div class="summary-chip"><strong><?php echo (int)$matrix['summary']['assignment_unit_hours']; ?></strong><span>ώρες με αντιστοιχισμένη ανάθεση</span></div>
            <?php if (!empty($collapsedSkills['active'])): ?>
              <div class="summary-chip"><strong><?php echo (int)$collapsedSkills['hours']; ?></strong><span>ώρες Εργαστηρίων Δεξιοτήτων · συγκεντρωτικά</span></div>
            <?php endif; ?>
            <div class="summary-chip"><strong><?php echo (int)$displayMatrix['summary']['ordered_exclusive_top_unit_hours']; ?></strong><span>ώρες αποκλειστικής κορυφαίας Α΄/Β΄/Γ΄</span></div>
            <div class="summary-chip"><strong><?php echo (int)$displayMatrix['summary']['ordered_shared_top_unit_hours']; ?></strong><span>ώρες κοινής κορυφαίας Α΄/Β΄/Γ΄</span></div>
            <div class="summary-chip"><strong><?php echo (int)$displayMatrix['summary']['special_top_unit_hours']; ?></strong><span>ώρες ειδικής κορυφαίας ανάθεσης</span></div>
            <div class="summary-chip"><strong><?php echo (int)$matrix['summary']['active_dependency_instances']; ?></strong><span>ενεργές εκκρεμείς εξαρτήσεις</span></div>
            <div class="summary-chip"><strong><?php echo (int)$matrix['summary']['active_regulatory_gap_instances']; ?></strong><span>επιβεβαιωμένα κανονιστικά κενά</span></div>
          </div>

          <div class="field result-filter">
            <label for="staffingResultFilter">Φίλτρο κλάδου / μαθήματος</label>
            <input id="staffingResultFilter" type="search" placeholder="π.χ. ΠΕ03 ή Μαθηματικά">
          </div>

          <div class="matrix-wrap">
            <table class="staffing-table" id="staffingMatrixTable">
              <thead><tr><th>Κλάδος</th><th>Α΄</th><th>Β΄</th><th>Γ΄</th><th>Αποκλειστική κορυφαία</th><th>Κοινή κορυφαία</th><th>Ειδική</th><th>Χαμηλότερη ανάθεση</th></tr></thead>
              <tbody>
              <?php if (!empty($collapsedSkills['active'])): ?>
                <tr class="staffing-code-row staffing-collapsed-row" data-search="Οποιαδήποτε ειδικότητα Εργαστήρια Δεξιοτήτων">
                  <td>
                    <details class="staffing-details">
                      <summary><span class="code">Οποιαδήποτε ειδικότητα</span> · Εργαστήρια Δεξιοτήτων</summary>
                      <ul class="claim-list">
                        <?php foreach ($collapsedSkills['units'] as $skillUnit): ?>
                          <li><strong><?php echo staffingUiH($skillUnit['grade'] . ' · Εργαστήρια Δεξιοτήτων'); ?></strong> — <?php echo (int)$skillUnit['hours']; ?> ώρες</li>
                        <?php endforeach; ?>
                      </ul>
                      <p class="claim-meta">Συγκεντρωτική εμφάνιση μόνο για το εργαλείο. Η πλήρης Α΄/Β΄ ανάθεση όλων των κλάδων διατηρείται στο εσωτερικό μοντέλο.</p>
                    </details>
                  </td>
                  <td colspan="7"><strong><?php echo (int)$collapsedSkills['hours']; ?> ώρες συνολικά</strong></td>
                </tr>
              <?php endif; ?>
              <?php foreach ($displayMatrix['codes'] as $code=>$row): ?>
                <tr class="staffing-code-row" data-search="<?php echo staffingUiH($code . ' ' . $row['label'] . ' ' . implode(' ', array_map(function($c){return isset($c['subject'])?$c['subject']:'';}, $row['claims']))); ?>">
                  <td>
                    <details class="staffing-details">
                      <summary><span class="code"><?php echo staffingUiH($code); ?></span><?php if (!empty($row['label'])): ?> · <?php echo staffingUiH($row['label']); ?><?php endif; ?></summary>
                      <ul class="claim-list">
                        <?php foreach ($row['claims'] as $claim): ?>
                          <li>
                            <strong><?php echo staffingUiH($claim['grade'] . ' · ' . $claim['subject']); ?></strong> — <?php echo (int)$claim['school_hours']; ?> ώρες
                            <span class="claim-meta">
                              <?php echo staffingUiH(staffingUiPriorityLabel($claim['priority'])); ?> ανάθεση<?php echo !empty($claim['is_top_priority']) ? ' · κορυφαία' : ' · χαμηλότερη ανάθεση'; ?><?php echo !empty($claim['top_code_count']) && $claim['top_code_count'] > 1 ? ' · κοινή με ' . ((int)$claim['top_code_count'] - 1) . ' ακόμη κλάδο/υς' : ''; ?>
                              <?php if (!empty($claim['track'])): ?> · <?php echo staffingUiH(staffingUiTrackLabel($claim['track'])); ?><?php endif; ?>
                              <?php if (!empty($claim['choice_option'])): ?> · <?php echo staffingUiH($claim['choice_option']); ?><?php endif; ?>
                            </span>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </details>
                  </td>
                  <td><?php echo (int)$row['eligible_hours_by_priority']['A']; ?></td>
                  <td><?php echo (int)$row['eligible_hours_by_priority']['B']; ?></td>
                  <td><?php echo (int)$row['eligible_hours_by_priority']['C']; ?></td>
                  <td><?php echo (int)$row['ordered_exclusive_top_priority_hours']; ?></td>
                  <td><?php echo (int)$row['ordered_shared_top_priority_hours']; ?></td>
                  <td><?php echo (int)$row['special_top_priority_hours']; ?></td>
                  <td><?php echo (int)$row['fallback_hours']; ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <p class="help"><strong>Προσοχή:</strong> οι στήλες επιλεξιμότητας Α΄/Β΄/Γ΄ μπορούν να επικαλύπτονται μεταξύ κλάδων. Οι «Αποκλειστικές κορυφαίες» είναι το πιο αυστηρό κομμάτι της στελέχωσης· οι «Κοινές κορυφαίες» απαιτούν πραγματική κατανομή μεταξύ ισότιμων κλάδων.<?php if (!empty($collapsedSkills['active'])): ?> Τα <strong>Εργαστήρια Δεξιοτήτων</strong> εξαιρούνται από τα επιμέρους αθροίσματα κλάδων της οθόνης και εμφανίζονται μία φορά ως «Οποιαδήποτε ειδικότητα», ώστε να αποφεύγεται η τεχνητή επανάληψη δεκάδων αναθέσεων.<?php endif; ?></p>
        <?php calculatorCardEnd(); ?>
      <?php endif; ?>

      <?php if ($submitted && $matrix): ?>
        <?php calculatorCardStart(array('class'=>'card staffing-panel personnel-card','attrs'=>array('data-staffing-panel'=>'personnel') + ($activePanel !== 'personnel' ? array('hidden'=>true) : array()))); ?>
          <h2>3. Εκπαιδευτικοί</h2>
          <p class="cap">Καταχώρισε το πραγματικό προσωπικό της σχολικής μονάδας. Για τον απλό εκπαιδευτικό δήλωσε απευθείας το υποχρεωτικό διδακτικό ωράριο· για Διευθυντή/ντρια ή Υποδιευθυντή/ντρια εφαρμόζεται η ειδική αυτόματη λογική της θέσης. Οι ώρες «σε άλλη μονάδα» αφαιρούνται από το διαθέσιμο ωράριο εδώ.</p>
          <div class="info-note"><strong>Η κατανομή μαθημάτων γίνεται στο επόμενο tab.</strong> Το «διαθέσιμο εδώ» είναι το υπόλοιπο του ατομικού υποχρεωτικού ωραρίου πριν από τις αναθέσεις μαθημάτων της συγκεκριμένης μονάδας.</div>

          <?php if (!empty($personnelRows)): ?>
            <div class="staffing-summary-grid">
              <div class="summary-chip"><strong><?php echo (int)$personnelSummary['people_count']; ?></strong><span>εκπαιδευτικοί στο προσωρινό προσωπικό</span></div>
              <div class="summary-chip"><strong><?php echo (int)$personnelSummary['required_hours']; ?></strong><span>συνολικό υποχρεωτικό διδακτικό ωράριο</span></div>
              <div class="summary-chip"><strong><?php echo (int)$personnelSummary['external_hours']; ?></strong><span>ώρες ήδη δεσμευμένες σε άλλη μονάδα</span></div>
              <div class="summary-chip"><strong><?php echo (int)$personnelSummary['available_here_hours']; ?></strong><span>ώρες διαθέσιμες για τη συγκεκριμένη μονάδα</span></div>
              <div class="summary-chip"><strong><?php echo (int)$personnelSummary['unresolved_count']; ?></strong><span>εγγραφές που χρειάζονται συμπλήρωση</span></div>
            </div>

            <h3>Σύνοψη ανά κλάδο</h3>
            <div class="branch-summary" id="personnelBranchSummary">
              <?php foreach ($personnelSummary['by_code'] as $code=>$summary): ?>
                <?php $needRow = $displayMatrix && isset($displayMatrix['codes'][$code]) ? $displayMatrix['codes'][$code] : null; ?>
                <div class="branch-summary-row" data-personnel-branch="<?php echo staffingUiH($code); ?>">
                  <div><span class="branch-code"><?php echo staffingUiH($code); ?></span><small><?php echo staffingUiH($summary['label']); ?></small></div>
                  <div><strong><?php echo (int)$summary['people_count']; ?></strong><small>εκπαιδευτικοί</small></div>
                  <div><strong><?php echo (int)$summary['available_here_hours']; ?></strong><small>ώρες διαθέσιμες εδώ</small></div>
                  <div><strong><?php echo $needRow ? (int)$needRow['ordered_exclusive_top_priority_hours'] : 0; ?></strong><small>αποκλειστικές κορυφαίες ώρες</small></div>
                  <div><strong><?php echo $needRow ? (int)$needRow['ordered_shared_top_priority_hours'] : 0; ?></strong><small>κοινές κορυφαίες ώρες</small></div>
                </div>
              <?php endforeach; ?>
            </div>
            <p class="help">Η σύγκριση ανά κλάδο είναι ενδεικτική για έλεγχο. Δεν χαρακτηρίζει τη διαφορά ως «κενό» ή «πλεόνασμα», επειδή δεν έχουν ακόμη κατανεμηθεί τα πραγματικά μαθήματα και οι κοινές αναθέσεις.</p>
          <?php endif; ?>

          <form method="post" id="staffingPersonnelForm">
            <?php staffingUiRenderSchoolStateHiddenInputs(); ?>
            <?php staffingUiRenderAllocationStateHiddenInputs($allocationRows); ?>
            <input type="hidden" name="staffing_action" value="">
            <input type="hidden" name="active_panel" value="personnel">

            <div class="personnel-toolbar">
              <div class="field">
                <label for="personnelFilter">Φίλτρο προσωπικού</label>
                <input id="personnelFilter" type="search" placeholder="π.χ. ΠΕ03 ή επώνυμο">
              </div>
              <div class="personnel-toolbar-actions">
                <button class="edu-btn-secondary" type="button" id="addPersonnelRow">+ Προσθήκη εκπαιδευτικού</button>
                <button class="edu-btn-secondary" type="button" id="openPersonnelCsv">Εισαγωγή CSV</button>
                <button class="edu-btn-secondary" type="button" id="exportPersonnelRegistryCsv">Εξαγωγή μητρώου CSV</button>
                <button class="edu-btn-secondary" type="button" id="downloadPersonnelCsvTemplate">Λήψη προτύπου CSV</button>
              </div>
            </div>

            <input id="personnelCsvFile" type="file" accept=".csv,text/csv,text/plain" hidden>
            <div class="personnel-csv-panel" id="personnelCsvPanel" hidden>
              <div class="personnel-csv-head">
                <div>
                  <strong>Εισαγωγή προσωπικού από CSV</strong>
                  <div class="personnel-csv-meta" id="personnelCsvMeta">Επίλεξε αρχείο CSV για προεπισκόπηση και αντιστοίχιση στηλών.</div>
                </div>
                <button class="personnel-remove" type="button" id="closePersonnelCsv">Κλείσιμο</button>
              </div>
              <div class="info-note"><strong>Το αρχείο διαβάζεται μόνο στον browser σου.</strong> Δεν μεταφορτώνεται στον διακομιστή. Υποστηρίζονται CSV με ελληνικό ερωτηματικό/semicolon (;), κόμμα ή tab και γίνεται αυτόματη προσπάθεια αναγνώρισης των στηλών.</div>
              <details class="option-panel" open>
                <summary>Αντιστοίχιση στηλών CSV</summary>
                <div class="option-panel-body">
                  <div class="personnel-csv-mappings" id="personnelCsvMappings"></div>
                  <p class="help">Απαραίτητος είναι ο <strong>Κλάδος / ειδικότητα</strong>. Η <strong>2η ειδικότητα</strong> είναι προαιρετική και ανήκει στον ίδιο εκπαιδευτικό. Για απλό εκπαιδευτικό δήλωσε απευθείας το <strong>υποχρεωτικό διδακτικό ωράριο</strong>. Τα έτη/μήνες/ημέρες υπηρεσίας χρησιμοποιούνται μόνο όταν ο ρόλος είναι Διευθυντής/ντρια ή Υποδιευθυντής/ντρια. Το ονοματεπώνυμο μπορεί να προέρχεται από μία στήλη ή από χωριστές στήλες Επώνυμο + Όνομα.</p>
                </div>
              </details>
              <div class="personnel-csv-preview" id="personnelCsvPreview" hidden></div>
              <div class="personnel-csv-actions">
                <div class="field">
                  <label for="personnelCsvMode">Τρόπος εισαγωγής</label>
                  <select id="personnelCsvMode"><option value="append">Προσθήκη στις υπάρχουσες εγγραφές</option><option value="replace">Αντικατάσταση του προσωρινού προσωπικού</option></select>
                </div>
                <button class="edu-btn-primary" type="button" id="importPersonnelCsv" disabled>Εισαγωγή στο προσωπικό</button>
              </div>
              <div class="personnel-csv-status" id="personnelCsvStatus"></div>
            </div>

            <div class="info-note"><strong>Το υποχρεωτικό ωράριο του απλού εκπαιδευτικού καταχωρίζεται απευθείας.</strong> Αν δεν το γνωρίζεις, χρησιμοποίησε τον <a href="ypologismos-didaktikou-orariou.php">Υπολογισμό υποχρεωτικού διδακτικού ωραρίου</a> και επέστρεψε εδώ με το αποτέλεσμα. Για Διευθυντή/Υποδιευθυντή το ωράριο υπολογίζεται από τον ρόλο και την υπηρεσία.</div>

            <div class="personnel-list" id="personnelList">
              <?php if (empty($personnelRows)): ?>
                <div class="empty-personnel" id="emptyPersonnelState">Δεν έχει προστεθεί ακόμη εκπαιδευτικός. Πάτησε «+ Προσθήκη εκπαιδευτικού» ή «Εισαγωγή CSV» για να ξεκινήσεις.</div>
              <?php endif; ?>
              <?php foreach ($personnelRows as $person): ?>
                <?php
                  $eval = isset($personnelEvaluations[$person['person_id']]) ? $personnelEvaluations[$person['person_id']] : array('status'=>'invalid','reason'=>'unknown');
                  $resolved = isset($eval['status']) && $eval['status'] === 'resolved';
                  $requiredHours = $resolved ? (int)$eval['required_teaching_hours'] : null;
                  $manualRequiredHours = isset($person['required_teaching_hours']) ? trim((string)$person['required_teaching_hours']) : '';
                  $requiredInputHours = $person['role'] === 'teacher' ? $manualRequiredHours : ($requiredHours === null ? '' : (string)$requiredHours);
                  $manualRequiredHoursMax = ($person['role'] === 'teacher' && strpos(teacherSpecialtyCanonicalCode($person['specialty_code']), 'ΠΕ') === 0) ? 23 : 35;
                  $availableHours = $resolved ? (int)$eval['remaining_before_profile_hours'] : null;
                  $externalHours = isset($person['assigned_external_hours']) ? (int)$person['assigned_external_hours'] : 0;
                  $selectedCode = isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '';
                  $selectedSecondaryCode = isset($person['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($person['secondary_specialty_code']) : '';
                ?>
                <div class="personnel-row" data-personnel-row data-search="<?php echo staffingUiH(trim($selectedCode . ($selectedSecondaryCode !== '' ? ' ' . $selectedSecondaryCode : '') . ' ' . $person['display_name'])); ?>">
                  <input type="hidden" name="personnel_person_id[]" value="<?php echo staffingUiH($person['person_id']); ?>">
                  <div class="personnel-row-main">
                    <div class="field">
                      <label>Κλάδος</label>
                      <select name="personnel_specialty_code[]" class="personnel-specialty"><?php staffingUiRenderPersonnelSpecialtyOptions($personnelSpecialtyOptions, $selectedCode); ?></select>
                    </div>
                    <div class="field">
                      <label>Ονοματεπώνυμο</label>
                      <input type="text" name="personnel_display_name[]" class="personnel-name" value="<?php echo staffingUiH($person['display_name']); ?>" placeholder="π.χ. Μαρία Παπαδοπούλου">
                    </div>
                    <div class="field">
                      <label>Υποχρεωτικό ωράριο</label>
                      <input type="number" min="1" max="<?php echo (int)$manualRequiredHoursMax; ?>" step="1" name="personnel_required_teaching_hours[]" class="personnel-required" data-required-hours data-manual-value="<?php echo staffingUiH($manualRequiredHours); ?>" value="<?php echo staffingUiH($requiredInputHours); ?>"<?php echo $person['role'] === 'teacher' ? ' required' : ' readonly'; ?>>
                    </div>
                    <div class="field">
                      <label>Ώρες αλλού</label>
                      <input type="number" min="0" max="35" step="1" name="personnel_assigned_external_hours[]" class="personnel-external" value="<?php echo $externalHours; ?>">
                    </div>
                    <div class="metric"><strong data-available-hours><?php echo $availableHours === null ? '—' : $availableHours; ?></strong><span>διαθέσιμο εδώ</span></div>
                    <button type="button" class="personnel-remove" title="Αφαίρεση εκπαιδευτικού">Αφαίρεση</button>
                  </div>
                  <details<?php echo $person['role'] !== 'teacher' || !$resolved || $selectedSecondaryCode !== '' ? ' open' : ''; ?>>
                    <summary>2η ειδικότητα, ρόλος και στοιχεία διοίκησης<?php if ($selectedSecondaryCode !== ''): ?> · 2η <?php echo staffingUiH($selectedSecondaryCode); ?><?php endif; ?><?php if ($person['role'] !== 'teacher' && $resolved && !empty($eval['obligation']['service_label'])): ?> · <?php echo staffingUiH($eval['obligation']['service_label']); ?><?php endif; ?></summary>
                    <div class="personnel-row-details">
                      <div class="mini-grid">
                        <div class="field">
                          <label>2η ειδικότητα <small>προαιρετική</small></label>
                          <select name="personnel_secondary_specialty_code[]" class="personnel-secondary-specialty"><?php staffingUiRenderPersonnelSpecialtyOptions($personnelSpecialtyOptions, $selectedSecondaryCode); ?></select>
                        </div>
                      </div>
                      <div class="mini-grid personnel-service-fields"<?php echo $person['role'] === 'teacher' ? ' hidden' : ''; ?>>
                        <div class="field"><label>Έτη υπηρεσίας</label><input type="number" min="0" max="50" step="1" name="personnel_service_years[]" class="personnel-years" value="<?php echo staffingUiH($person['service']['years']); ?>"></div>
                        <div class="field"><label>Μήνες</label><input type="number" min="0" max="11" step="1" name="personnel_service_months[]" class="personnel-months" value="<?php echo staffingUiH($person['service']['months']); ?>"></div>
                        <div class="field"><label>Ημέρες</label><input type="number" min="0" max="29" step="1" name="personnel_service_days[]" class="personnel-days" value="<?php echo staffingUiH($person['service']['days']); ?>"></div>
                      </div>
                      <div class="mini-grid">
                        <div class="field">
                          <label>Ρόλος</label>
                          <select name="personnel_role[]" class="personnel-role">
                            <?php foreach (array('teacher','director','vice_or_sector') as $role): ?><option value="<?php echo $role; ?>"<?php echo $person['role'] === $role ? ' selected' : ''; ?>><?php echo staffingUiH(staffingUiPersonnelRoleLabel($role)); ?></option><?php endforeach; ?>
                          </select>
                        </div>
                        <div class="field personnel-director-band"<?php echo $person['role'] === 'director' ? '' : ' hidden'; ?>>
                          <label>Τμήματα σχολικής μονάδας <small>αυτόματα</small></label>
                          <div class="summary-chip personnel-director-section-info"><strong data-director-section-count><?php echo (int)$generalSectionTotal; ?></strong><span data-director-section-band><?php echo $directorSectionsBandAuto ? 'κλίμακα ' . staffingUiH($directorSectionsBandAuto) : 'χρειάζονται τα κανονικά τμήματα'; ?></span></div>
                        </div>
                      </div>
                      <?php if ($resolved && !empty($eval['obligation']['rule'])): ?><p class="help" data-personnel-rule><?php echo staffingUiH($eval['obligation']['rule']); ?></p><?php else: ?><p class="help" data-personnel-rule></p><?php endif; ?>
                    </div>
                  </details>
                  <?php if (!$resolved): ?><div class="personnel-status-error" data-personnel-error><?php echo staffingUiH(staffingUiPersonnelReasonLabel(isset($eval['reason']) ? $eval['reason'] : 'Χρειάζεται συμπλήρωση στοιχείων.')); ?></div><?php elseif (!empty($eval['external_overage_hours'])): ?><div class="personnel-status-error" data-personnel-error>Οι ώρες σε άλλη μονάδα υπερβαίνουν το υποχρεωτικό ωράριο κατά <?php echo (int)$eval['external_overage_hours']; ?> ώρες.</div><?php else: ?><div class="personnel-status-error" data-personnel-error hidden></div><?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="actions">
              <button class="edu-btn-primary" type="button" data-staffing-request-action="personnel">Έλεγχος ωραρίων προσωπικού</button>
            </div>
          </form>

          <template id="personnelRowTemplate">
            <div class="personnel-row" data-personnel-row data-search="">
              <input type="hidden" name="personnel_person_id[]" value="">
              <div class="personnel-row-main">
                <div class="field"><label>Κλάδος</label><select name="personnel_specialty_code[]" class="personnel-specialty"><?php staffingUiRenderPersonnelSpecialtyOptions($personnelSpecialtyOptions, ''); ?></select></div>
                <div class="field"><label>Ονοματεπώνυμο</label><input type="text" name="personnel_display_name[]" class="personnel-name" placeholder="π.χ. Μαρία Παπαδοπούλου"></div>
                <div class="field"><label>Υποχρεωτικό ωράριο</label><input type="number" min="1" max="35" step="1" name="personnel_required_teaching_hours[]" class="personnel-required" data-required-hours data-manual-value="" value="" required></div>
                <div class="field"><label>Ώρες αλλού</label><input type="number" min="0" max="35" step="1" name="personnel_assigned_external_hours[]" class="personnel-external" value="0"></div>
                <div class="metric"><strong data-available-hours>—</strong><span>διαθέσιμο εδώ</span></div>
                <button type="button" class="personnel-remove" title="Αφαίρεση εκπαιδευτικού">Αφαίρεση</button>
              </div>
              <details>
                <summary>2η ειδικότητα, ρόλος και στοιχεία διοίκησης</summary>
                <div class="personnel-row-details">
                  <div class="mini-grid">
                    <div class="field"><label>2η ειδικότητα <small>προαιρετική</small></label><select name="personnel_secondary_specialty_code[]" class="personnel-secondary-specialty"><?php staffingUiRenderPersonnelSpecialtyOptions($personnelSpecialtyOptions, ''); ?></select></div>
                  </div>
                  <div class="mini-grid personnel-service-fields" hidden>
                    <div class="field"><label>Έτη υπηρεσίας</label><input type="number" min="0" max="50" step="1" name="personnel_service_years[]" class="personnel-years" value="0"></div>
                    <div class="field"><label>Μήνες</label><input type="number" min="0" max="11" step="1" name="personnel_service_months[]" class="personnel-months" value="0"></div>
                    <div class="field"><label>Ημέρες</label><input type="number" min="0" max="29" step="1" name="personnel_service_days[]" class="personnel-days" value="0"></div>
                  </div>
                  <div class="mini-grid">
                    <div class="field"><label>Ρόλος</label><select name="personnel_role[]" class="personnel-role"><option value="teacher">Εκπαιδευτικός</option><option value="director">Διευθυντής/ντρια</option><option value="vice_or_sector">Υποδιευθυντής/ντρια</option></select></div>
                    <div class="field personnel-director-band" hidden><label>Τμήματα σχολικής μονάδας <small>αυτόματα</small></label><div class="summary-chip personnel-director-section-info"><strong data-director-section-count>—</strong><span data-director-section-band>από τα κανονικά τμήματα</span></div></div>
                  </div>
                  <p class="help" data-personnel-rule></p>
                </div>
              </details>
              <div class="personnel-status-error" data-personnel-error hidden></div>
            </div>
          </template>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(array('class'=>'card staffing-panel allocation-card','attrs'=>array('data-staffing-panel'=>'allocation') + ($activePanel !== 'allocation' ? array('hidden'=>true) : array()))); ?>
          <h2>4. Κατανομή μαθημάτων</h2>
          <p class="cap">Κατάνειμε χειροκίνητα τα πραγματικά μαθήματα και τις ομάδες στους εκπαιδευτικούς. Το εργαλείο ελέγχει την ισχύουσα ανάθεση, το ατομικό υπόλοιπο ωραρίου και τη χωρητικότητα κάθε συγκεκριμένου τμήματος / ομάδας.</p>
          <div class="info-note"><strong>Χειροκίνητη κατανομή με αυτόματο έλεγχο — όχι αυτόματη τοποθέτηση.</strong> Τα κανονικά τμήματα εμφανίζονται ως Α1, Α2, Β1 κ.λπ. Οι ομάδες ξένων γλωσσών, προσανατολισμού και οι πρόσθετες ομάδες χωρισμού εμφανίζονται ως «Ομάδα 1», «Ομάδα 2» ώστε να μη δημιουργείται ψεύτικη ταυτότητα τμήματος.</div>

          <?php if (!$allocationEnabled): ?>
            <div class="allocation-empty">Για να ενεργοποιηθεί η κατανομή χρειάζεται τουλάχιστον ένας εκπαιδευτικός με πλήρως υπολογισμένο ωράριο στο tab «Εκπαιδευτικοί».</div>
          <?php else: ?>
            <?php
              $allocationTotalHours = (int)$matrix['summary']['assignment_unit_hours'];
              $allocationAssignedHours = $allocationPlan ? (int)$allocationPlan['summary']['assigned_slot_hours_total'] : 0;
              $allocationUnassignedHours = $allocationPlan ? (int)$allocationPlan['summary']['unassigned_slot_hours'] : $allocationTotalHours;
              $allocationOverHours = $allocationPlan ? (int)$allocationPlan['summary']['overallocated_slot_hours'] : 0;
              $allocationInvalidRows = $allocationPlan ? (int)$allocationPlan['summary']['invalid_allocation_row_count'] : 0;
            ?>
            <div class="staffing-summary-grid" id="allocationGlobalSummary">
              <div class="summary-chip"><strong data-allocation-total><?php echo $allocationTotalHours; ?></strong><span>ώρες μαθημάτων προς κατανομή</span></div>
              <div class="summary-chip"><strong data-allocation-assigned><?php echo $allocationAssignedHours; ?></strong><span>ώρες που έχουν κατανεμηθεί έγκυρα</span></div>
              <div class="summary-chip"><strong data-allocation-unassigned><?php echo $allocationUnassignedHours; ?></strong><span>ώρες που απομένουν χωρίς κατανομή</span></div>
              <div class="summary-chip"><strong data-allocation-over><?php echo $allocationOverHours; ?></strong><span>ώρες υπέρβασης τμήματος / ομάδας</span></div>
              <div class="summary-chip"><strong data-allocation-errors><?php echo $allocationInvalidRows; ?></strong><span>γραμμές που χρειάζονται διόρθωση</span></div>
            </div>

            <h3>Κατάσταση εκπαιδευτικών</h3>
            <div class="allocation-person-summary" id="allocationPersonSummary">
              <?php foreach ($allocationPeopleClient as $personId=>$personData): ?>
                <?php
                  $pResult = $allocationPlan && isset($allocationPlan['people'][$personId]) ? $allocationPlan['people'][$personId] : null;
                  $assignedHere = $pResult ? (int)$pResult['assigned_profile_hours'] : 0;
                  $remainingHere = $pResult ? (int)$pResult['remaining_hours'] : (int)$personData['available_here_hours'];
                  $overageHere = $pResult ? (int)$pResult['overage_hours'] : 0;
                ?>
                <div class="allocation-person-summary-row" data-allocation-person-summary="<?php echo staffingUiH($personId); ?>">
                  <div><strong><?php echo staffingUiH($personData['specialty_code'] . ' · ' . ($personData['display_name'] !== '' ? $personData['display_name'] : 'Χωρίς ονοματεπώνυμο')); ?></strong></div>
                  <div><strong><?php echo (int)$personData['available_here_hours']; ?></strong><small>διαθέσιμο εδώ</small></div>
                  <div><strong data-person-assigned><?php echo $assignedHere; ?></strong><small>κατανεμημένες ώρες</small></div>
                  <div><strong data-person-remaining><?php echo $remainingHere; ?></strong><small>υπόλοιπο</small></div>
                  <div><strong data-person-over><?php echo $overageHere; ?></strong><small>υπέρβαση</small></div>
                </div>
              <?php endforeach; ?>
            </div>

            <form method="post" id="staffingAllocationForm">
              <?php staffingUiRenderSchoolStateHiddenInputs(); ?>
              <?php staffingUiRenderPersonnelStateHiddenInputs($personnelRows); ?>
              <input type="hidden" name="staffing_action" value="">
              <input type="hidden" name="active_panel" value="allocation">

              <div class="allocation-toolbar">
                <div>
                  <strong>Γραμμές κατανομής</strong>
                  <div class="help">Επίλεξε εκπαιδευτικό και μετά συγκεκριμένο τμήμα / ομάδα + μάθημα. Οι ώρες συμπληρώνονται αρχικά με το πλήρες ωράριο του slot και μπορούν να μειωθούν.</div>
                </div>
                <button class="edu-btn-secondary" type="button" id="addAllocationRow">+ Προσθήκη μαθήματος</button>
              </div>

              <div class="allocation-list" id="allocationList">
                <?php if (empty($allocationRows)): ?>
                  <div class="allocation-empty" id="emptyAllocationState">Δεν έχει γίνει ακόμη κατανομή. Πάτησε «+ Προσθήκη μαθήματος» για να ξεκινήσεις.</div>
                <?php endif; ?>
                <?php foreach ($allocationRows as $allocationIndex=>$allocation): ?>
                  <?php
                    $rowResult = isset($allocationRowResults[$allocationIndex]) ? $allocationRowResults[$allocationIndex] : null;
                    $rowStatusClass = '';
                    $rowStatusText = 'Συμπλήρωσε εκπαιδευτικό και μάθημα.';
                    if ($rowResult) {
                        if (!$rowResult['valid']) {
                            $labels = array(); foreach ($rowResult['errors'] as $error) $labels[] = staffingUiAllocationErrorLabel($error);
                            $rowStatusClass = ' is-error'; $rowStatusText = implode(' ', $labels);
                        } elseif (!empty($rowResult['warnings'])) {
                            $rowStatusClass = ' is-warning';
                            $rowStatusText = staffingUiPriorityLabel($rowResult['priority']) . ' ανάθεση · χαμηλότερη προτεραιότητα';
                        } else {
                            $rowStatusClass = ' is-ok';
                            $rowStatusText = staffingUiPriorityLabel($rowResult['priority']) . ' ανάθεση ✓';
                        }
                    }
                  ?>
                  <div class="allocation-row" data-allocation-row>
                    <div class="allocation-row-main">
                      <div class="field"><label>Εκπαιδευτικός</label><select name="allocation_person_id[]" class="allocation-person"><?php staffingUiRenderAllocationPersonOptions($allocationPeople, isset($allocation['person_id']) ? $allocation['person_id'] : ''); ?></select></div>
                      <div class="field"><label>Τμήμα / ομάδα · μάθημα</label><select name="allocation_slot_id[]" class="allocation-slot"><?php staffingUiRenderAllocationSlotOptions($allocationSlots, isset($allocation['slot_id']) ? $allocation['slot_id'] : ''); ?></select></div>
                      <div class="field"><label>Ώρες</label><input type="number" min="1" max="35" step="1" name="allocation_hours[]" class="allocation-hours" value="<?php echo staffingUiH(isset($allocation['hours']) ? $allocation['hours'] : 0); ?>"></div>
                      <div class="allocation-status<?php echo $rowStatusClass; ?>" data-allocation-status><?php echo staffingUiH($rowStatusText); ?></div>
                      <button type="button" class="personnel-remove allocation-remove">Αφαίρεση</button>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="actions">
                <button class="edu-btn-primary" type="button" data-staffing-request-action="allocation">Έλεγχος κατανομής</button>
              </div>
            </form>

            <template id="allocationRowTemplate">
              <div class="allocation-row" data-allocation-row>
                <div class="allocation-row-main">
                  <div class="field"><label>Εκπαιδευτικός</label><select name="allocation_person_id[]" class="allocation-person"><?php staffingUiRenderAllocationPersonOptions($allocationPeople, ''); ?></select></div>
                  <div class="field"><label>Τμήμα / ομάδα · μάθημα</label><select name="allocation_slot_id[]" class="allocation-slot"><?php staffingUiRenderAllocationSlotOptions($allocationSlots, ''); ?></select></div>
                  <div class="field"><label>Ώρες</label><input type="number" min="1" max="35" step="1" name="allocation_hours[]" class="allocation-hours" value="0"></div>
                  <div class="allocation-status" data-allocation-status>Συμπλήρωσε εκπαιδευτικό και μάθημα.</div>
                  <button type="button" class="personnel-remove allocation-remove">Αφαίρεση</button>
                </div>
              </div>
            </template>
          <?php endif; ?>
        <?php calculatorCardEnd(); ?>
      <?php endif; ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class'=>'card results')); ?>
      <h2>Τι κάνει αυτή η έκδοση</h2>
      <p class="cap">Είναι εργαλείο προσομοίωσης και ελέγχου της εσωτερικής λογικής που έχουμε ήδη χτίσει.</p>
      <div class="result-row"><span>Ωρολόγιο πρόγραμμα</span><strong>✓</strong></div>
      <div class="result-row"><span>Α΄/Β΄/Γ΄ αναθέσεις</span><strong>✓</strong></div>
      <div class="result-row"><span>2η ξένη γλώσσα</span><strong>✓</strong></div>
      <div class="result-row"><span>Ομάδες Προσανατολισμού ΓΕΛ</span><strong>✓</strong></div>
      <div class="result-row"><span>Ηθική</span><strong>✓</strong></div>
      <div class="result-row"><span>Πραγματικό προσωπικό</span><strong>✓</strong></div>
      <div class="result-row"><span>Χειροκίνητη κατανομή μαθημάτων</span><strong>✓</strong></div>
      <div class="result-row"><span>Αυτόματες τοποθετήσεις</span><strong>Όχι ακόμη</strong></div>
      <div class="info-note">Το εργαλείο δεν χαρακτηρίζει τις ώρες ως επίσημα «κενά». Η κατανομή μαθημάτων είναι χειροκίνητη και ελέγχεται απέναντι στις αναθέσεις, στο ατομικό ωράριο και στη χωρητικότητα κάθε τμήματος / ομάδας.</div>
      <?php if ($submitted && $matrix): ?>
        <h3>Τρέχων υπολογισμός</h3>
        <div class="result-row"><span>Δομή</span><strong><?php echo $schoolType === 'gel' ? 'Ημερήσιο ΓΕΛ' : 'Ημερήσιο Γυμνάσιο'; ?></strong></div>
        <div class="result-row"><span>Μονάδες αντιστοιχισμένης ανάθεσης</span><strong><?php echo (int)$matrix['summary']['assignment_unit_count']; ?></strong></div>
        <div class="result-row"><span>Κλάδοι με επιλεξιμότητα</span><strong><?php echo (int)$displayMatrix['summary']['presentation_staffing_leaf_codes_with_claims']; ?></strong></div>
        <div class="result-row"><span>Κατάσταση</span><strong><?php echo staffingUiH(staffingUiReadinessLabel($matrix['readiness'])); ?></strong></div>
        <?php if ($allocationPlan): ?>
          <div class="result-row"><span>Κατανεμημένες ώρες</span><strong><?php echo (int)$allocationPlan['summary']['assigned_slot_hours_total']; ?></strong></div>
          <div class="result-row"><span>Ώρες χωρίς κατανομή</span><strong><?php echo (int)$allocationPlan['summary']['unassigned_slot_hours']; ?></strong></div>
        <?php endif; ?>
      <?php endif; ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
</main>

<?php sourceCardStart(); ?>
  <?php sourceCardDisclaimerStart(); ?>
    Ο υπολογισμός συνδυάζει τα ωρολόγια προγράμματα και τις ισχύουσες αναθέσεις που χρησιμοποιούνται ήδη στα δύο αντίστοιχα εργαλεία της Εργαλειοθήκης. Τα αποτελέσματα είναι εργαλείο ελέγχου / προσομοίωσης και δεν αποτελούν από μόνα τους επίσημη πράξη προσδιορισμού λειτουργικών κενών ή τοποθέτησης εκπαιδευτικών.
  <?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202132_09_04_26_OP%20EM%20GYMN.pdf', 'ΦΕΚ Β΄ 2132/2026 — Ημερήσιο Γυμνάσιο ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-ekpaideuse/deuterobathmia-ekpaideuse/upourgike-apophase-74472-d2-2020.html', 'Υ.Α. 74472/Δ2/2020 — ΦΕΚ Β΄ 2450/2020 · Τεχνολογία / Πληροφορική Γυμνασίου ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202106_09_04_26_OP%20EM%20GEL_ESP%20Gymnasio.pdf', 'ΦΕΚ Β΄ 2106/2026 — Ημερήσιο ΓΕΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/dioikitika-themata-geniko-lykeio', 'ΥΠΑΙΘΑ — Αναθέσεις Γυμνασίου / ΓΕΛ ↗'); ?>
    <?php sourceCardLink(ethicsClassFormationPolicy()['source_url'], 'Υ.Α. 108070/Δ2/2026 — ΦΕΚ Β΄ 5231/2026 · Ηθική ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php if ($submitted && $matrix && $displayMatrix): ?>
<section class="staffing-print-report" id="staffingPrintReport" aria-label="Εκτυπώσιμο αποτέλεσμα διδακτικών αναγκών">
  <div class="print-header">
    <div>
      <h1>Υπολογισμός διδακτικών αναγκών σχολικής μονάδας</h1>
      <p class="print-subtitle"><strong><?php echo staffingUiH($schoolName !== '' ? $schoolName : ($schoolType === 'gel' ? 'Ημερήσιο Γενικό Λύκειο' : 'Ημερήσιο Γυμνάσιο')); ?></strong> · <?php echo $schoolType === 'gel' ? 'Ημερήσιο ΓΕΛ' : 'Ημερήσιο Γυμνάσιο'; ?> · σχολικό έτος 2026–2027</p>
    </div>
    <div class="print-meta">Εργαλειοθήκη Εκπαιδευτικού<br><span data-print-generated-at>—</span></div>
  </div>

  <h2>Συνοπτική εικόνα</h2>
  <div class="print-summary">
    <div class="print-summary-item"><strong><?php echo (int)$generalSectionTotal; ?></strong><span>κανονικά τμήματα σχολικής μονάδας</span></div>
    <div class="print-summary-item"><strong><?php echo (int)$matrix['summary']['assignment_unit_hours']; ?></strong><span>ώρες με αντιστοιχισμένη ανάθεση</span></div>
    <div class="print-summary-item"><strong><?php echo (int)$displayMatrix['summary']['presentation_staffing_leaf_codes_with_claims']; ?></strong><span>κλάδοι με επιλεξιμότητα</span></div>
    <div class="print-summary-item"><strong><?php echo (int)$personnelSummary['available_here_hours']; ?></strong><span>ώρες προσωπικού διαθέσιμες εδώ</span></div>
    <div class="print-summary-item"><strong><?php echo staffingUiH(staffingUiReadinessLabel($matrix['readiness'])); ?></strong><span>κατάσταση δομικών στοιχείων</span></div>
  </div>

  <h2>Διδακτικές ανάγκες ανά κλάδο</h2>
  <table class="print-matrix">
    <thead><tr><th>Κλάδος</th><th class="num">Α΄</th><th class="num">Β΄</th><th class="num">Γ΄</th><th class="num">Αποκλ. κορυφαία</th><th class="num">Κοινή κορυφαία</th><th class="num">Ειδική</th><th class="num">Χαμηλότερη</th></tr></thead>
    <tbody>
      <?php if (!empty($collapsedSkills['active'])): ?>
        <tr><td><span class="print-code">Οποιαδήποτε ειδικότητα</span> · Εργαστήρια Δεξιοτήτων</td><td class="num" colspan="7"><?php echo (int)$collapsedSkills['hours']; ?> ώρες συνολικά</td></tr>
      <?php endif; ?>
      <?php foreach ($displayMatrix['codes'] as $code=>$row): ?>
        <tr>
          <td><span class="print-code"><?php echo staffingUiH($code); ?></span><?php if (!empty($row['label'])): ?> · <?php echo staffingUiH($row['label']); ?><?php endif; ?></td>
          <td class="num"><?php echo (int)$row['eligible_hours_by_priority']['A']; ?></td>
          <td class="num"><?php echo (int)$row['eligible_hours_by_priority']['B']; ?></td>
          <td class="num"><?php echo (int)$row['eligible_hours_by_priority']['C']; ?></td>
          <td class="num"><?php echo (int)$row['ordered_exclusive_top_priority_hours']; ?></td>
          <td class="num"><?php echo (int)$row['ordered_shared_top_priority_hours']; ?></td>
          <td class="num"><?php echo (int)$row['special_top_priority_hours']; ?></td>
          <td class="num"><?php echo (int)$row['fallback_hours']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p class="print-note">Οι ώρες επιλεξιμότητας μπορούν να επικαλύπτονται μεταξύ κλάδων. Ο πίνακας δεν αποτελεί από μόνος του επίσημο προσδιορισμό λειτουργικών κενών.</p>

  <?php if (!empty($personnelRows)): ?>
    <h2>Εκπαιδευτικοί και διαθέσιμο ωράριο</h2>
    <table class="print-personnel">
      <thead><tr><th>Κλάδος</th><th>Ονοματεπώνυμο</th><th>Ρόλος</th><th class="num">Υποχρ.</th><th class="num">Ώρες αλλού</th><th class="num">Διαθέσιμο εδώ</th><th>Κατάσταση</th></tr></thead>
      <tbody>
        <?php foreach ($personnelRows as $person): ?>
          <?php
            $personEval = isset($personnelEvaluations[$person['person_id']]) ? $personnelEvaluations[$person['person_id']] : null;
            $personResolved = $personEval && $personEval['status'] === 'resolved';
            $personStatus = $personResolved ? 'OK' : staffingUiPersonnelReasonLabel($personEval && isset($personEval['reason']) ? $personEval['reason'] : 'Χρειάζεται συμπλήρωση στοιχείων.');
          ?>
          <tr>
            <td class="print-code"><?php echo staffingUiH(isset($person['specialty_code']) ? $person['specialty_code'] : ''); ?><?php if (!empty($person['secondary_specialty_code'])): ?><br><small>2η <?php echo staffingUiH($person['secondary_specialty_code']); ?></small><?php endif; ?></td>
            <td><?php echo staffingUiH($person['display_name'] !== '' ? $person['display_name'] : 'Χωρίς ονοματεπώνυμο'); ?></td>
            <td><?php echo staffingUiH(staffingUiPersonnelRoleLabel(isset($person['role']) ? $person['role'] : 'teacher')); ?></td>
            <td class="num"><?php echo $personResolved ? (int)$personEval['required_teaching_hours'] : '—'; ?></td>
            <td class="num"><?php echo $personResolved ? (int)$personEval['assigned_external_hours'] : (int)$person['assigned_external_hours']; ?></td>
            <td class="num"><?php echo $personResolved ? (int)$personEval['remaining_before_profile_hours'] : '—'; ?></td>
            <td class="<?php echo $personResolved ? 'print-status-ok' : 'print-status-error'; ?>"><?php echo staffingUiH($personStatus); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <?php if (!empty($allocationRows)): ?>
    <h2>Κατανομή μαθημάτων</h2>
    <?php if ($allocationPlan): ?>
      <div class="print-summary">
        <div class="print-summary-item"><strong><?php echo (int)$matrix['summary']['assignment_unit_hours']; ?></strong><span>ώρες μαθημάτων προς κατανομή</span></div>
        <div class="print-summary-item"><strong><?php echo (int)$allocationPlan['summary']['assigned_slot_hours_total']; ?></strong><span>κατανεμημένες ώρες</span></div>
        <div class="print-summary-item"><strong><?php echo (int)$allocationPlan['summary']['unassigned_slot_hours']; ?></strong><span>ώρες χωρίς κατανομή</span></div>
        <div class="print-summary-item"><strong><?php echo (int)$allocationPlan['summary']['overallocated_slot_hours']; ?></strong><span>ώρες υπέρβασης slot</span></div>
        <div class="print-summary-item"><strong><?php echo (int)$allocationPlan['summary']['invalid_allocation_row_count']; ?></strong><span>γραμμές για διόρθωση</span></div>
      </div>
    <?php endif; ?>
    <table class="print-allocation">
      <thead><tr><th>Εκπαιδευτικός</th><th>Τμήμα / ομάδα · μάθημα</th><th class="num">Ώρες</th><th>Έλεγχος</th></tr></thead>
      <tbody>
        <?php foreach ($allocationRows as $allocationIndex=>$allocation): ?>
          <?php
            $allocationResult = isset($allocationRowResults[$allocationIndex]) ? $allocationRowResults[$allocationIndex] : null;
            $allocationPerson = isset($allocationPeopleClient[$allocation['person_id']]) ? $allocationPeopleClient[$allocation['person_id']] : null;
            $allocationPersonText = $allocationPerson
                ? $allocationPerson['specialty_code'] . ' · ' . ($allocationPerson['display_name'] !== '' ? $allocationPerson['display_name'] : 'Χωρίς ονοματεπώνυμο')
                : 'Μη έγκυρος εκπαιδευτικός';
            $allocationSlot = isset($allocationSlots[$allocation['slot_id']]) ? $allocationSlots[$allocation['slot_id']] : null;
            $allocationSlotText = $allocationSlot ? $allocationSlot['slot_label'] . ' · ' . $allocationSlot['subject'] : 'Μη έγκυρο μάθημα / ομάδα';
            if ($allocationResult && !$allocationResult['valid']) {
                $labels = array(); foreach ($allocationResult['errors'] as $error) $labels[] = staffingUiAllocationErrorLabel($error);
                $allocationStatus = implode(' ', $labels); $allocationStatusClass = 'print-status-error';
            } elseif ($allocationResult && !empty($allocationResult['warnings'])) {
                $allocationStatus = staffingUiPriorityLabel($allocationResult['priority']) . ' ανάθεση · χαμηλότερη προτεραιότητα'; $allocationStatusClass = 'print-status-warn';
            } elseif ($allocationResult && $allocationResult['valid']) {
                $allocationStatus = staffingUiPriorityLabel($allocationResult['priority']) . ' ανάθεση ✓'; $allocationStatusClass = 'print-status-ok';
            } else {
                $allocationStatus = 'Δεν έχει ελεγχθεί'; $allocationStatusClass = 'print-status-warn';
            }
          ?>
          <tr>
            <td><?php echo staffingUiH($allocationPersonText); ?></td>
            <td><?php echo staffingUiH($allocationSlotText); ?></td>
            <td class="num"><?php echo (int)$allocation['hours']; ?></td>
            <td class="<?php echo $allocationStatusClass; ?>"><?php echo staffingUiH($allocationStatus); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <div class="print-footer">Το παρόν αποτελεί αποτέλεσμα εργαλείου προσομοίωσης/ελέγχου. Δεν συνιστά από μόνο του επίσημη πράξη προσδιορισμού λειτουργικών κενών ή τοποθέτησης εκπαιδευτικών. Οι αναθέσεις και το ωρολόγιο πρόγραμμα ακολουθούν τις κανονιστικές πηγές που χρησιμοποιεί η Εργαλειοθήκη Εκπαιδευτικού.</div>
</section>
<?php endif; ?>

<script src="<?php echo staffingUiH(edu_asset_url('includes/teaching-hours-calculations.js')); ?>"></script>
<script src="<?php echo staffingUiH(edu_asset_url('includes/personnel-csv-import.js')); ?>"></script>
<script>
(function(){
  const type=document.getElementById('school_type');
  const gym=document.getElementById('gymProfileFields');
  const gel=document.getElementById('gelProfileFields');
  const reset=document.getElementById('staffingReset');
  const staffingPrintButton=document.getElementById('staffingPrintButton');
  if(staffingPrintButton){
    staffingPrintButton.addEventListener('click',function(){
      const stamp=document.querySelector('[data-print-generated-at]');
      if(stamp){
        const now=new Date();
        stamp.textContent='Εκτύπωση: '+now.toLocaleDateString('el-GR')+' '+now.toLocaleTimeString('el-GR',{hour:'2-digit',minute:'2-digit'});
      }
      window.print();
    });
  }
  function installExplicitRequestGate(form){
    if(!form || form.dataset.requestGateBound==='1') return;
    form.dataset.requestGateBound='1';
    const actionInput=form.querySelector('input[name="staffing_action"]');
    const requestButtons=Array.from(form.querySelectorAll('[data-staffing-request-action]'));
    form.addEventListener('submit',function(event){
      const armed=form.dataset.explicitRequest==='1' && actionInput && actionInput.value!=='';
      if(!armed || form.dataset.requestInFlight==='1'){
        event.preventDefault();
        return;
      }
      form.dataset.requestInFlight='1';
      requestButtons.forEach(function(button){button.disabled=true;});
    });
    requestButtons.forEach(function(button){
      button.addEventListener('click',function(){
        if(form.dataset.requestInFlight==='1' || !actionInput) return;
        const action=button.getAttribute('data-staffing-request-action')||'';
        if(action==='') return;
        if(!form.reportValidity()) return;
        actionInput.value=action;
        form.dataset.explicitRequest='1';
        form.requestSubmit();
      });
    });
  }
  ['staffingProfileForm','staffingPersonnelForm','staffingAllocationForm'].forEach(function(id){ installExplicitRequestGate(document.getElementById(id)); });
  function sync(){
    const isGel=type.value==='gel';
    gym.hidden=isGel;
    gel.hidden=!isGel;
    gym.querySelectorAll('input,select').forEach(el=>{ el.disabled=isGel; });
    gel.querySelectorAll('input,select').forEach(el=>{ el.disabled=!isGel; });
  }
  type.addEventListener('change',sync); sync();
  function syncSplitMaximums(){
    document.querySelectorAll('[data-max-source]').forEach(function(input){
      const source=document.getElementById(input.getAttribute('data-max-source'));
      const max=source ? Math.max(0,parseInt(source.value||'0',10)||0) : 0;
      input.max=String(max);
      document.querySelectorAll('[data-max-label="'+input.getAttribute('data-max-source')+'"]').forEach(function(label){ label.textContent=String(max); });
      if((parseInt(input.value||'0',10)||0)>max) input.value=String(max);
    });
  }
  document.querySelectorAll('[id^="gym_general_"]').forEach(function(input){ input.addEventListener('input',syncSplitMaximums); });
  syncSplitMaximums();
  function syncLanguageGroupMaximums(){
    document.querySelectorAll('[data-language-max-source]').forEach(function(input){
      const source=document.getElementById(input.getAttribute('data-language-max-source'));
      const max=source ? Math.max(0,parseInt(source.value||'0',10)||0) : 0;
      const value=input.value==='' ? 0 : Math.max(0,parseInt(input.value||'0',10)||0);
      const grade=input.getAttribute('data-language-grade')||'';
      const language=input.getAttribute('data-language-name')||'Η γλώσσα';
      const error=document.querySelector('[data-language-error-for="'+input.id+'"]');
      input.max=String(max);
      if(value>max){
        const message='Οι ομάδες «'+language+'» της '+grade+' τάξης ('+value+') δεν μπορούν να ξεπερνούν τα '+max+' κανονικά τμήματα της ίδιας τάξης.';
        input.setCustomValidity(message);
        input.setAttribute('aria-invalid','true');
        if(error){error.hidden=false;error.textContent=message;}
      }else{
        input.setCustomValidity('');
        input.removeAttribute('aria-invalid');
        if(error){error.hidden=true;error.textContent='';}
      }
    });
  }
  document.querySelectorAll('[data-language-max-source]').forEach(function(input){ input.addEventListener('input',syncLanguageGroupMaximums); });
  document.querySelectorAll('[id^="gym_general_"],[id^="gel_general_"]').forEach(function(input){ input.addEventListener('input',syncLanguageGroupMaximums); });
  syncLanguageGroupMaximums();
  if(reset){ reset.addEventListener('click',function(){ setTimeout(function(){ type.value='gymnasio'; sync(); syncSplitMaximums(); syncLanguageGroupMaximums(); },0); }); }
  const filter=document.getElementById('staffingResultFilter');
  if(filter){
    const rows=Array.from(document.querySelectorAll('.staffing-code-row'));
    filter.addEventListener('input',function(){
      const q=(filter.value||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
      rows.forEach(function(row){
        const hay=(row.getAttribute('data-search')||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
        row.hidden=q!=='' && !hay.includes(q);
      });
    });
  }

  const tabs=Array.from(document.querySelectorAll('[data-staffing-tab]'));
  const panels=Array.from(document.querySelectorAll('[data-staffing-panel]'));
  function activatePanel(name){
    tabs.forEach(function(tab){
      const active=tab.getAttribute('data-staffing-tab')===name;
      tab.classList.toggle('is-active',active);
      tab.setAttribute('aria-selected',active?'true':'false');
    });
    panels.forEach(function(panel){ panel.hidden=panel.getAttribute('data-staffing-panel')!==name; });
  }
  tabs.forEach(function(tab){
    tab.addEventListener('click',function(){ if(!tab.disabled) activatePanel(tab.getAttribute('data-staffing-tab')); });
  });

  const personnelList=document.getElementById('personnelList');
  const allocationTab=document.querySelector('[data-staffing-tab="allocation"]');
  function markPersonnelDirty(){ if(allocationTab){ allocationTab.disabled=true; allocationTab.title='Υπολόγισε ξανά τα ωράρια προσωπικού πριν από νέα κατανομή.'; } }
  const personnelTemplate=document.getElementById('personnelRowTemplate');
  const addPersonnel=document.getElementById('addPersonnelRow');
  const personnelFilter=document.getElementById('personnelFilter');
  const openPersonnelCsv=document.getElementById('openPersonnelCsv');
  const personnelCsvFile=document.getElementById('personnelCsvFile');
  const personnelCsvPanel=document.getElementById('personnelCsvPanel');
  const closePersonnelCsv=document.getElementById('closePersonnelCsv');
  const personnelCsvMeta=document.getElementById('personnelCsvMeta');
  const personnelCsvMappings=document.getElementById('personnelCsvMappings');
  const personnelCsvPreview=document.getElementById('personnelCsvPreview');
  const personnelCsvStatus=document.getElementById('personnelCsvStatus');
  const personnelCsvMode=document.getElementById('personnelCsvMode');
  const importPersonnelCsv=document.getElementById('importPersonnelCsv');
  const exportPersonnelRegistryCsv=document.getElementById('exportPersonnelRegistryCsv');
  const downloadPersonnelCsvTemplate=document.getElementById('downloadPersonnelCsvTemplate');
  let personnelCounter=Date.now();
  let personnelCsvData=null;
  let personnelCsvMapping={};

  const personnelCsvFields=[
    {key:'person_id',label:'Αναγνωριστικό εκπαιδευτικού'},
    {key:'specialty_code',label:'Κλάδος / ειδικότητα',required:true},
    {key:'secondary_specialty_code',label:'2η ειδικότητα'},
    {key:'display_name',label:'Ονοματεπώνυμο'},
    {key:'surname',label:'Επώνυμο'},
    {key:'given_name',label:'Όνομα'},
    {key:'required_teaching_hours',label:'Υποχρεωτικό διδακτικό ωράριο'},
    {key:'role',label:'Ρόλος'},
    {key:'service_years',label:'Έτη υπηρεσίας (μόνο διοίκηση)'},
    {key:'service_months',label:'Μήνες υπηρεσίας (μόνο διοίκηση)'},
    {key:'service_days',label:'Ημέρες υπηρεσίας (μόνο διοίκηση)'},
    {key:'service_combined',label:'Προϋπηρεσία ενιαία (μόνο διοίκηση)'},
    {key:'assigned_external_hours',label:'Ώρες σε άλλη μονάδα'}
  ];

  function schoolGeneralSectionCount(){
    const typeEl=document.getElementById('school_type');
    const prefix=typeEl && typeEl.value==='gel' ? 'gel_general_' : 'gym_general_';
    return ['a','b','c'].reduce(function(total,suffix){
      const input=document.querySelector('[name="'+prefix+suffix+'"]');
      return total+Math.max(0,parseInt(input&&input.value?input.value:'0',10)||0);
    },0);
  }
  function directorSectionsBandFromCount(count){
    count=Math.max(0,parseInt(count,10)||0);
    if(count<3) return '';
    if(count<=5) return '3-5';
    if(count<=9) return '6-9';
    if(count<=12) return '10-12';
    return '13+';
  }
  function updateDirectorSectionInfo(row){
    const count=schoolGeneralSectionCount();
    const band=directorSectionsBandFromCount(count);
    const countEl=row.querySelector('[data-director-section-count]');
    const bandEl=row.querySelector('[data-director-section-band]');
    if(countEl) countEl.textContent=String(count);
    if(bandEl) bandEl.textContent=band ? 'κλίμακα '+band : 'χρειάζονται τα κανονικά τμήματα';
    return {count:count,band:band};
  }

  const singleDirectorMessage='Μπορεί να δηλωθεί μόνο ένας/μία Διευθυντής/ντρια στη σχολική μονάδα.';
  function refreshDirectorRoleConstraints(){
    if(!personnelList) return;
    const rows=Array.from(personnelList.querySelectorAll('[data-personnel-row]'));
    const directorRows=rows.filter(function(row){
      const role=row.querySelector('.personnel-role');
      return role && role.value==='director';
    });
    const hasDirector=directorRows.length>0;
    rows.forEach(function(row){
      const role=row.querySelector('.personnel-role');
      if(!role) return;
      const directorOption=role.querySelector('option[value="director"]');
      const isDirector=role.value==='director';
      const duplicateDirector=isDirector && directorRows.indexOf(row)>0;
      if(directorOption) directorOption.disabled=hasDirector && !isDirector;
      role.setCustomValidity(duplicateDirector?singleDirectorMessage:'');
      if(duplicateDirector) role.setAttribute('aria-invalid','true');
      else role.removeAttribute('aria-invalid');
      const error=row.querySelector('[data-personnel-error]');
      if(duplicateDirector && error){
        error.hidden=false;
        error.textContent=singleDirectorMessage;
      }else if(error && error.textContent===singleDirectorMessage){
        error.hidden=true;
        error.textContent='';
      }
    });
  }

  function updatePersonnelRow(row){
    if(!row) return;
    const specialty=row.querySelector('.personnel-specialty');
    const secondarySpecialty=row.querySelector('.personnel-secondary-specialty');
    const years=row.querySelector('.personnel-years');
    const months=row.querySelector('.personnel-months');
    const days=row.querySelector('.personnel-days');
    const role=row.querySelector('.personnel-role');
    const requiredInput=row.querySelector('.personnel-required');
    const external=row.querySelector('.personnel-external');
    const serviceWrap=row.querySelector('.personnel-service-fields');
    const directorBandWrap=row.querySelector('.personnel-director-band');
    const rule=row.querySelector('[data-personnel-rule]');
    const error=row.querySelector('[data-personnel-error]');
    const availableEl=row.querySelector('[data-available-hours]');
    const roleValue=role?role.value:'teacher';
    const managementRole=roleValue==='director'||roleValue==='vice_or_sector';
    if(serviceWrap) serviceWrap.hidden=!managementRole;
    if(directorBandWrap) directorBandWrap.hidden=roleValue!=='director';
    if(requiredInput){
      if(managementRole){
        if(!requiredInput.readOnly) requiredInput.dataset.manualValue=requiredInput.value||'';
        requiredInput.readOnly=true;
        requiredInput.required=false;
        requiredInput.setCustomValidity('');
      }else{
        if(requiredInput.readOnly){
          requiredInput.readOnly=false;
          requiredInput.value=requiredInput.dataset.manualValue||'';
        }
        requiredInput.required=true;
      }
    }
    const code=specialty?specialty.value:'';
    const secondaryCode=secondarySpecialty?secondarySpecialty.value:'';
    const manualHoursMax=code.indexOf('ΠΕ')===0 ? 23 : 35;
    if(requiredInput && !managementRole) requiredInput.max=String(manualHoursMax);
    const name=(row.querySelector('.personnel-name')||{}).value||'';
    row.setAttribute('data-search',code+' '+secondaryCode+' '+name);
    if(!specialty || !specialty.value){
      if(managementRole && requiredInput) requiredInput.value='';
      if(availableEl) availableEl.textContent='—';
      if(rule) rule.textContent='';
      if(error){error.hidden=false;error.textContent='Επίλεξε κλάδο / ειδικότητα.';}
      return;
    }

    let required=0;
    if(managementRole){
      if(!window.EducationTeachingHours){
        if(requiredInput) requiredInput.value='';
        if(availableEl) availableEl.textContent='—';
        if(error){error.hidden=false;error.textContent='Δεν φορτώθηκε ο υπολογισμός ωραρίου διοικητικών ρόλων.';}
        return;
      }
      const directorSectionInfo=updateDirectorSectionInfo(row);
      if(roleValue==='director' && !directorSectionInfo.band){
        if(requiredInput) requiredInput.value='';
        if(availableEl) availableEl.textContent='—';
        if(rule) rule.textContent='';
        if(error){error.hidden=false;error.textContent='Για Διευθυντή/ντρια χρειάζονται τα δηλωμένα κανονικά τμήματα της σχολικής μονάδας.';}
        return;
      }
      const result=window.EducationTeachingHours.secondary({
        branch:'PE',
        role:roleValue,
        years:years?years.value:0,
        months:months?months.value:0,
        days:days?days.value:0,
        sections:directorSectionInfo.band
      });
      if(!result || !result.valid){
        if(requiredInput) requiredInput.value='';
        if(availableEl) availableEl.textContent='—';
        if(rule) rule.textContent='';
        if(error){error.hidden=false;error.textContent=(result&&result.error)?result.error:'Δεν μπορεί να υπολογιστεί το ωράριο της διοικητικής θέσης.';}
        return;
      }
      required=Math.max(0,parseInt(result.hours,10)||0);
      if(requiredInput) requiredInput.value=String(required);
      if(rule) rule.textContent=result.rule||'';
    }else{
      const raw=requiredInput?String(requiredInput.value||'').trim():'';
      const parsedRaw=/^\d+$/.test(raw) ? parseInt(raw,10) : NaN;
      const invalidMessage=code.indexOf('ΠΕ')===0 && Number.isFinite(parsedRaw) && parsedRaw>23
        ? 'Για κλάδο ΠΕ το υποχρεωτικό διδακτικό ωράριο δεν μπορεί να ξεπερνά τις 23 ώρες.'
        : 'Το υποχρεωτικό ωράριο πρέπει να είναι ακέραιος αριθμός από 1 έως '+manualHoursMax+' ώρες.';
      if(raw==='' || !Number.isFinite(parsedRaw) || parsedRaw<1 || parsedRaw>manualHoursMax){
        if(requiredInput) requiredInput.setCustomValidity(raw===''?'Συμπλήρωσε το υποχρεωτικό διδακτικό ωράριο.':invalidMessage);
        if(availableEl) availableEl.textContent='—';
        if(rule) rule.textContent='';
        if(error){error.hidden=false;error.textContent=raw===''?'Συμπλήρωσε το υποχρεωτικό διδακτικό ωράριο.':invalidMessage;}
        return;
      }
      required=parseInt(raw,10);
      requiredInput.setCustomValidity('');
      requiredInput.dataset.manualValue=String(required);
      if(rule) rule.textContent='Το υποχρεωτικό διδακτικό ωράριο δηλώνεται απευθείας από τον χρήστη.';
    }

    const ext=Math.max(0,parseInt(external&&external.value?external.value:'0',10)||0);
    if(availableEl) availableEl.textContent=String(Math.max(0,required-ext));
    if(error){
      if(ext>required){error.hidden=false;error.textContent='Οι ώρες σε άλλη μονάδα υπερβαίνουν το υποχρεωτικό ωράριο κατά '+(ext-required)+' ώρες.';}
      else{error.hidden=true;error.textContent='';}
    }
  }

  function bindPersonnelRow(row){
    if(!row || row.dataset.initialized==='1') return;
    row.dataset.initialized='1';
    const hiddenId=row.querySelector('input[name="personnel_person_id[]"]');
    if(hiddenId && !hiddenId.value){ personnelCounter+=1; hiddenId.value='person-'+personnelCounter; }
    updatePersonnelRow(row);
    refreshDirectorRoleConstraints();
  }
  if(personnelList && personnelList.dataset.eventsBound!=='1'){
    personnelList.dataset.eventsBound='1';
    personnelList.addEventListener('input',function(event){
      if(!event.target.matches('input:not([type="hidden"])')) return;
      const row=event.target.closest('[data-personnel-row]');
      if(!row) return;
      updatePersonnelRow(row); refreshDirectorRoleConstraints(); markPersonnelDirty();
    });
    personnelList.addEventListener('change',function(event){
      if(!event.target.matches('select')) return;
      const row=event.target.closest('[data-personnel-row]');
      if(!row) return;
      updatePersonnelRow(row); refreshDirectorRoleConstraints(); markPersonnelDirty();
    });
    personnelList.addEventListener('click',function(event){
      const remove=event.target.closest('.personnel-remove');
      if(!remove || !personnelList.contains(remove)) return;
      const row=remove.closest('[data-personnel-row]');
      if(!row) return;
      row.remove(); refreshDirectorRoleConstraints(); markPersonnelDirty(); ensurePersonnelEmptyState();
    });
  }
  function clearPersonnelRows(){
    if(!personnelList) return;
    personnelList.querySelectorAll('[data-personnel-row]').forEach(function(row){row.remove();});
    const empty=document.getElementById('emptyPersonnelState'); if(empty) empty.remove();
    refreshDirectorRoleConstraints();
  }
  function ensurePersonnelEmptyState(){
    if(!personnelList || personnelList.querySelector('[data-personnel-row]')) return;
    const empty=document.createElement('div'); empty.id='emptyPersonnelState'; empty.className='empty-personnel'; empty.textContent='Δεν έχει προστεθεί ακόμη εκπαιδευτικός. Πάτησε «+ Προσθήκη εκπαιδευτικού» ή «Εισαγωγή CSV» για να ξεκινήσεις.'; personnelList.appendChild(empty);
  }
  function addPersonnelFromData(person){
    if(!personnelTemplate || !personnelList) return {ok:false,unknownCode:false};
    const empty=document.getElementById('emptyPersonnelState'); if(empty) empty.remove();
    const fragment=personnelTemplate.content.cloneNode(true);
    const row=fragment.querySelector('[data-personnel-row]');
    const hiddenId=row.querySelector('input[name="personnel_person_id[]"]');
    const requestedId=String(person.person_id||'').trim();
    if(hiddenId && requestedId){
      const duplicateId=Array.from(personnelList.querySelectorAll('input[name="personnel_person_id[]"]')).some(function(input){return input.value===requestedId;});
      if(!duplicateId) hiddenId.value=requestedId;
    }
    const specialty=row.querySelector('.personnel-specialty');
    const code=person.specialty_code||'';
    let matched=false;
    if(specialty && code){
      Array.from(specialty.options).forEach(function(opt){ if(opt.value===code){ specialty.value=code; matched=true; } });
    }
    const secondarySpecialty=row.querySelector('.personnel-secondary-specialty');
    const secondaryCode=person.secondary_specialty_code||'';
    let secondaryMatched=false;
    if(secondarySpecialty && secondaryCode){
      Array.from(secondarySpecialty.options).forEach(function(opt){ if(opt.value===secondaryCode){ secondarySpecialty.value=secondaryCode; secondaryMatched=true; } });
    }
    const name=row.querySelector('.personnel-name'); if(name) name.value=person.display_name||'';
    const years=row.querySelector('.personnel-years'); if(years) years.value=String(person.service_years||0);
    const months=row.querySelector('.personnel-months'); if(months) months.value=String(person.service_months||0);
    const days=row.querySelector('.personnel-days'); if(days) days.value=String(person.service_days||0);
    const required=row.querySelector('.personnel-required'); if(required){ required.value=person.required_teaching_hours==null?'':String(person.required_teaching_hours); required.dataset.manualValue=required.value; }
    const role=row.querySelector('.personnel-role'); if(role) role.value=person.role||'teacher';
    const external=row.querySelector('.personnel-external'); if(external) external.value=String(person.assigned_external_hours||0);
    personnelList.appendChild(fragment);
    bindPersonnelRow(row);
    if(code && !matched){
      const error=row.querySelector('[data-personnel-error]');
      if(error){ error.hidden=false; error.textContent='Ο κλάδος «'+code+'» του CSV δεν αναγνωρίζεται από τις διαθέσιμες αναθέσεις. Επίλεξε κλάδο χειροκίνητα.'; }
    }
    if(secondaryCode && !secondaryMatched){
      const error=row.querySelector('[data-personnel-error]');
      if(error){ error.hidden=false; error.textContent='Η 2η ειδικότητα «'+secondaryCode+'» του CSV δεν αναγνωρίζεται. Επίλεξέ την χειροκίνητα.'; }
    }
    return {ok:true,unknownCode:!!((code&&!matched)||(secondaryCode&&!secondaryMatched))};
  }
  function personnelCsvSetStatus(message,type){
    if(!personnelCsvStatus) return;
    personnelCsvStatus.textContent=message||'';
    personnelCsvStatus.classList.toggle('is-error',type==='error');
    personnelCsvStatus.classList.toggle('is-success',type==='success');
  }
  function personnelCsvSelectOptions(select,headers,selected){
    select.innerHTML='';
    const empty=document.createElement('option'); empty.value=''; empty.textContent='— δεν χρησιμοποιείται —'; select.appendChild(empty);
    headers.forEach(function(header){ const opt=document.createElement('option'); opt.value=header; opt.textContent=header; if(header===selected) opt.selected=true; select.appendChild(opt); });
  }
  function personnelCsvCurrentMapping(){
    const map={};
    if(personnelCsvMappings) personnelCsvMappings.querySelectorAll('select[data-csv-map]').forEach(function(select){ if(select.value) map[select.getAttribute('data-csv-map')]=select.value; });
    return map;
  }
  function renderPersonnelCsvMappings(){
    if(!personnelCsvData || !personnelCsvMappings || !window.EducationPersonnelCsv) return;
    const auto=window.EducationPersonnelCsv.autoMap(personnelCsvData.headers);
    personnelCsvMappings.innerHTML='';
    personnelCsvFields.forEach(function(field){
      const wrap=document.createElement('div'); wrap.className='field';
      const label=document.createElement('label'); label.textContent=field.label+(field.required?' *':'');
      const select=document.createElement('select'); select.setAttribute('data-csv-map',field.key);
      personnelCsvSelectOptions(select,personnelCsvData.headers,auto[field.key]||'');
      wrap.appendChild(label); wrap.appendChild(select); personnelCsvMappings.appendChild(wrap);
    });
    personnelCsvMapping=personnelCsvCurrentMapping();
  }
  function renderPersonnelCsvPreview(){
    if(!personnelCsvPreview || !personnelCsvData) return;
    const headers=personnelCsvData.headers;
    const rows=personnelCsvData.rows.slice(0,5);
    if(!rows.length){ personnelCsvPreview.hidden=true; personnelCsvPreview.innerHTML=''; return; }
    const table=document.createElement('table');
    const thead=document.createElement('thead'); const hr=document.createElement('tr');
    headers.forEach(function(h){const th=document.createElement('th');th.textContent=h;hr.appendChild(th);}); thead.appendChild(hr); table.appendChild(thead);
    const tbody=document.createElement('tbody');
    rows.forEach(function(r){const tr=document.createElement('tr');headers.forEach(function(h){const td=document.createElement('td');td.textContent=r[h]||'';tr.appendChild(td);});tbody.appendChild(tr);});
    table.appendChild(tbody); personnelCsvPreview.innerHTML=''; personnelCsvPreview.appendChild(table); personnelCsvPreview.hidden=false;
  }
  function personnelCsvImporterSupportsRegistry(){
    return !!(window.EducationPersonnelCsv
      && window.EducationPersonnelCsv.supportsManualRequiredTeachingHours===true
      && window.EducationPersonnelCsv.supportsSecondarySpecialty===true);
  }
  function validatePersonnelCsvImport(){
    if(!personnelCsvImporterSupportsRegistry()){
      if(importPersonnelCsv) importPersonnelCsv.disabled=true;
      personnelCsvSetStatus('Έχει φορτωθεί παλαιότερη cached έκδοση του CSV importer. Κάνε ανανέωση της σελίδας για να φορτωθεί η έκδοση που υποστηρίζει το portable μητρώο και τη 2η ειδικότητα.','error');
      return;
    }
    const ready=!!(personnelCsvData && personnelCsvData.rows.length && personnelCsvMapping.specialty_code);
    if(importPersonnelCsv) importPersonnelCsv.disabled=!ready;
    if(personnelCsvData && !personnelCsvMapping.specialty_code) personnelCsvSetStatus('Χρειάζεται αντιστοίχιση της στήλης «Κλάδος / ειδικότητα».','error');
    else if(personnelCsvData) personnelCsvSetStatus('Έτοιμο για εισαγωγή. Θα εισαχθούν έως '+personnelCsvData.rows.length+' εγγραφές.','');
  }
  if(personnelCsvMappings && personnelCsvMappings.dataset.eventsBound!=='1'){
    personnelCsvMappings.dataset.eventsBound='1';
    personnelCsvMappings.addEventListener('change',function(event){
      if(!event.target.matches('select[data-csv-map]')) return;
      personnelCsvMapping=personnelCsvCurrentMapping(); renderPersonnelCsvPreview(); validatePersonnelCsvImport();
    });
  }
  function decodePersonnelCsvBuffer(buffer){
    let text='';
    try{text=new TextDecoder('utf-8',{fatal:false}).decode(buffer);}catch(e){text='';}
    if(text.indexOf('\uFFFD')>=0){
      try{const alt=new TextDecoder('windows-1253').decode(buffer); if(alt && alt.indexOf('\uFFFD')<0) text=alt;}catch(e){}
    }
    return text.replace(/^\uFEFF/,'');
  }
  function openCsvPicker(){ if(personnelCsvFile){ personnelCsvFile.value=''; personnelCsvFile.click(); } }
  if(openPersonnelCsv) openPersonnelCsv.addEventListener('click',openCsvPicker);
  if(closePersonnelCsv) closePersonnelCsv.addEventListener('click',function(){ if(personnelCsvPanel) personnelCsvPanel.hidden=true; });
  if(personnelCsvFile){
    personnelCsvFile.addEventListener('change',function(){
      const file=personnelCsvFile.files && personnelCsvFile.files[0]; if(!file) return;
      const reader=new FileReader();
      reader.onload=function(){
        if(!window.EducationPersonnelCsv){ personnelCsvSetStatus('Δεν φορτώθηκε ο μηχανισμός ανάγνωσης CSV.','error'); return; }
        if(!personnelCsvImporterSupportsRegistry()){
          personnelCsvSetStatus('Έχει φορτωθεί παλαιότερη cached έκδοση του CSV importer. Κάνε ανανέωση της σελίδας και επίλεξε ξανά το αρχείο.','error');
          if(importPersonnelCsv) importPersonnelCsv.disabled=true;
          return;
        }
        const text=decodePersonnelCsvBuffer(reader.result);
        personnelCsvData=window.EducationPersonnelCsv.parse(text);
        if(personnelCsvPanel) personnelCsvPanel.hidden=false;
        if(personnelCsvMeta){
          const delim=personnelCsvData.delimiter==='\t'?'tab':personnelCsvData.delimiter;
          personnelCsvMeta.textContent=file.name+' · '+personnelCsvData.rows.length+' εγγραφές · διαχωριστικό «'+delim+'»';
        }
        renderPersonnelCsvMappings(); renderPersonnelCsvPreview(); validatePersonnelCsvImport();
      };
      reader.onerror=function(){ personnelCsvSetStatus('Δεν ήταν δυνατή η ανάγνωση του αρχείου.','error'); };
      reader.readAsArrayBuffer(file);
    });
  }
  if(importPersonnelCsv){
    importPersonnelCsv.addEventListener('click',function(){
      if(!personnelCsvData || !window.EducationPersonnelCsv) return;
      if(!personnelCsvImporterSupportsRegistry()){
        personnelCsvSetStatus('Η εισαγωγή σταμάτησε επειδή ο browser έχει παλιότερη cached έκδοση του CSV importer. Ανανέωσε τη σελίδα και δοκίμασε ξανά.','error');
        return;
      }
      personnelCsvMapping=personnelCsvCurrentMapping();
      if(!personnelCsvMapping.specialty_code){ validatePersonnelCsvImport(); return; }
      if(personnelCsvMode && personnelCsvMode.value==='replace') clearPersonnelRows();
      let imported=0,skipped=0,unknown=0;
      personnelCsvData.rows.forEach(function(raw){
        const person=window.EducationPersonnelCsv.rowToPersonnel(raw,personnelCsvMapping);
        if(!person.specialty_code && !person.display_name){ skipped++; return; }
        const result=addPersonnelFromData(person); if(result.ok) imported++; if(result.unknownCode) unknown++;
      });
      ensurePersonnelEmptyState();
      markPersonnelDirty();
      let msg='Εισήχθησαν '+imported+' εκπαιδευτικοί.';
      if(skipped) msg+=' Παραλείφθηκαν '+skipped+' κενές εγγραφές.';
      if(unknown) msg+=' '+unknown+' εγγραφές έχουν μη αναγνωρισμένο κλάδο και χρειάζονται χειροκίνητο έλεγχο.';
      refreshDirectorRoleConstraints();
      const importedDirectorCount=personnelList ? Array.from(personnelList.querySelectorAll('.personnel-role')).filter(function(role){return role.value==='director';}).length : 0;
      if(importedDirectorCount>1) msg+=' Έχουν δηλωθεί '+importedDirectorCount+' Διευθυντές/ντριες· επιτρέπεται μόνο ένας/μία.';
      msg+=' Πάτησε «Έλεγχος ωραρίων προσωπικού» για να ενημερωθεί και η σύνοψη ανά κλάδο.';
      personnelCsvSetStatus(msg,(unknown||importedDirectorCount>1)?'error':'success');
    });
  }
  function personnelRegistryCsvEscape(value){
    const text=String(value==null?'':value);
    return /[;"\r\n]/.test(text)?'"'+text.replace(/"/g,'""')+'"':text;
  }
  function personnelRegistryRoleLabel(value){
    if(value==='director') return 'Διευθυντής';
    if(value==='vice_or_sector') return 'Υποδιευθυντής';
    return 'Εκπαιδευτικός';
  }
  function personnelRegistryRows(){
    if(!personnelList) return [];
    return Array.from(personnelList.querySelectorAll('[data-personnel-row]')).map(function(row){
      const value=function(selector){ const el=row.querySelector(selector); return el?String(el.value||'').trim():''; };
      const role=value('.personnel-role')||'teacher';
      return [
        'staff_registry_v1',
        value('input[name="personnel_person_id[]"]'),
        value('.personnel-specialty'),
        value('.personnel-secondary-specialty'),
        value('.personnel-name'),
        role==='teacher'?value('.personnel-required'):'',
        personnelRegistryRoleLabel(role),
        role==='teacher'?'':value('.personnel-years'),
        role==='teacher'?'':value('.personnel-months'),
        role==='teacher'?'':value('.personnel-days'),
        value('.personnel-external')
      ];
    });
  }
  function downloadPersonnelRegistryCsv(rows,filename){
    const headers=['Έκδοση μητρώου','Αναγνωριστικό','Κλάδος','2η ειδικότητα','Ονοματεπώνυμο','Υποχρεωτικό ωράριο','Ρόλος','Έτη υπηρεσίας','Μήνες','Ημέρες','Ώρες αλλού'];
    const lines=[headers].concat(rows).map(function(cols){return cols.map(personnelRegistryCsvEscape).join(';');});
    const csv='\uFEFF'+lines.join('\r\n')+'\r\n';
    const blob=new Blob([csv],{type:'text/csv;charset=utf-8'}); const url=URL.createObjectURL(blob); const a=document.createElement('a');
    a.href=url; a.download=filename; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function(){URL.revokeObjectURL(url);},500);
  }
  if(exportPersonnelRegistryCsv){
    exportPersonnelRegistryCsv.addEventListener('click',function(){
      const rows=personnelRegistryRows();
      if(!rows.length){ window.alert('Δεν υπάρχει προσωπικό για εξαγωγή.'); return; }
      downloadPersonnelRegistryCsv(rows,'mitroo-ekpaideftikon.csv');
    });
  }
  if(downloadPersonnelCsvTemplate){
    downloadPersonnelCsvTemplate.addEventListener('click',function(){
      downloadPersonnelRegistryCsv([
        ['staff_registry_v1','', 'ΠΕ03','ΠΕ86','Μαρία Παπαδοπούλου','20','Εκπαιδευτικός','','','','0'],
        ['staff_registry_v1','', 'ΠΕ02','','Γιώργος Διευθυντής','','Διευθυντής','20','0','0','0']
      ],'protypo-mitroou-ekpaideftikon.csv');
    });
  }

  if(personnelList) personnelList.querySelectorAll('[data-personnel-row]').forEach(bindPersonnelRow);
  function refreshPersonnelRows(){ if(personnelList) personnelList.querySelectorAll('[data-personnel-row]').forEach(updatePersonnelRow); }
  document.querySelectorAll('[name="gym_general_a"],[name="gym_general_b"],[name="gym_general_c"],[name="gel_general_a"],[name="gel_general_b"],[name="gel_general_c"]').forEach(function(el){ el.addEventListener('input',refreshPersonnelRows); });
  if(type) type.addEventListener('change',refreshPersonnelRows);
  if(addPersonnel && personnelTemplate && personnelList){
    addPersonnel.addEventListener('click',function(){
      const empty=document.getElementById('emptyPersonnelState'); if(empty) empty.remove();
      const fragment=personnelTemplate.content.cloneNode(true);
      const row=fragment.querySelector('[data-personnel-row]');
      personnelList.appendChild(fragment);
      bindPersonnelRow(row);
      markPersonnelDirty();
      const first=row.querySelector('.personnel-specialty'); if(first) first.focus();
    });
  }
  if(personnelFilter && personnelList){
    personnelFilter.addEventListener('input',function(){
      const q=(personnelFilter.value||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
      personnelList.querySelectorAll('[data-personnel-row]').forEach(function(row){
        const specialty=row.querySelector('.personnel-specialty');
        const secondary=row.querySelector('.personnel-secondary-specialty');
        const name=row.querySelector('.personnel-name');
        const hay=((specialty?specialty.value:'')+' '+(secondary?secondary.value:'')+' '+(name?name.value:'')).toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
        row.hidden=q!==''&&!hay.includes(q);
      });
    });
  }

  const allocationPeopleData=<?php echo json_encode($allocationPeopleClient, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  const allocationSlotsData=<?php echo json_encode($allocationSlotsClient, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  const allocationList=document.getElementById('allocationList');
  const allocationTemplate=document.getElementById('allocationRowTemplate');
  const addAllocation=document.getElementById('addAllocationRow');
  function allocationPriority(code,slot){
    if(!code||!slot||!slot.eligible_by_priority) return '';
    const order=['A','B','C','SPECIAL'];
    for(let i=0;i<order.length;i++){
      const p=order[i], arr=slot.eligible_by_priority[p]||[];
      if(arr.indexOf(code)>=0) return p;
    }
    return '';
  }
  function allocationPriorityLabel(priority){
    if(priority==='A') return 'Α΄';
    if(priority==='B') return 'Β΄';
    if(priority==='C') return 'Γ΄';
    if(priority==='SPECIAL') return 'Ειδική';
    return priority||'';
  }
  function allocationPopulateSlotsForPerson(row,preserveSelected){
    const personEl=row.querySelector('.allocation-person'), slotEl=row.querySelector('.allocation-slot');
    if(!slotEl) return;
    const oldSelected=slotEl.value||'';
    const person=personEl?allocationPeopleData[personEl.value]||null:null;
    slotEl.innerHTML='';
    const placeholder=document.createElement('option'); placeholder.value=''; placeholder.textContent=person?'— επιλογή επιλέξιμου μαθήματος —':'— επίλεξε πρώτα εκπαιδευτικό —'; slotEl.appendChild(placeholder);
    if(!person){ slotEl.disabled=true; return; }
    slotEl.disabled=false;
    const eligible=[];
    Object.keys(allocationSlotsData||{}).forEach(function(sid){ const slot=allocationSlotsData[sid]; if(allocationPriority(person.specialty_code,slot)) eligible.push(slot); });
    let currentGrade=null, group=null;
    eligible.forEach(function(slot){
      if(slot.grade!==currentGrade){ group=document.createElement('optgroup'); group.label=(slot.grade||'Άλλο')+' τάξη'; slotEl.appendChild(group); currentGrade=slot.grade; }
      const option=document.createElement('option'); option.value=slot.slot_id; option.textContent=slot.label; option.setAttribute('data-capacity',String(slot.capacity_hours)); if(oldSelected===slot.slot_id) option.selected=true; group.appendChild(option);
    });
    if(preserveSelected && oldSelected && !allocationPriority(person.specialty_code,allocationSlotsData[oldSelected]||null) && allocationSlotsData[oldSelected]){
      const invalid=document.createElement('option'); invalid.value=oldSelected; invalid.textContent=allocationSlotsData[oldSelected].label+' · ΜΗ ΕΠΙΤΡΕΠΤΟ'; invalid.selected=true; slotEl.insertBefore(invalid,slotEl.children[1]||null);
    }
  }
  function allocationSetStatus(row,text,kind){
    const el=row.querySelector('[data-allocation-status]'); if(!el) return;
    el.textContent=text; el.classList.remove('is-ok','is-warning','is-error');
    if(kind) el.classList.add('is-'+kind);
  }
  function allocationRows(){ return allocationList ? Array.from(allocationList.querySelectorAll('[data-allocation-row]')) : []; }
  function updateAllocationSummary(){
    if(!allocationList) return;
    const rows=allocationRows();
    const personAssigned={}, slotAssigned={}, rowState=[];
    Object.keys(allocationPeopleData||{}).forEach(id=>personAssigned[id]=0);
    Object.keys(allocationSlotsData||{}).forEach(id=>slotAssigned[id]=0);
    let basicAssigned=0;
    rows.forEach(function(row){
      const personEl=row.querySelector('.allocation-person'), slotEl=row.querySelector('.allocation-slot'), hoursEl=row.querySelector('.allocation-hours');
      const pid=personEl?personEl.value:'', sid=slotEl?slotEl.value:'', hours=Math.max(0,parseInt(hoursEl&&hoursEl.value?hoursEl.value:'0',10)||0);
      const person=allocationPeopleData[pid]||null, slot=allocationSlotsData[sid]||null;
      const priority=person&&slot?allocationPriority(person.specialty_code,slot):'';
      let error='', warning='';
      if((pid||sid||hours) && !person) error='Δεν έχει επιλεγεί έγκυρος εκπαιδευτικός.';
      else if((pid||sid||hours) && !slot) error='Δεν έχει επιλεγεί έγκυρο τμήμα / ομάδα και μάθημα.';
      else if((pid||sid) && hours<1) error='Οι ώρες πρέπει να είναι θετικές.';
      else if(person&&slot&&hours>slot.capacity_hours) error='Οι ώρες υπερβαίνουν τις '+slot.capacity_hours+' ώρες του συγκεκριμένου τμήματος / ομάδας.';
      else if(person&&slot&&hours>0&&!priority) error='Ο κλάδος '+person.specialty_code+' δεν έχει ανάθεση στο συγκεκριμένο μάθημα.';
      if(!error && person&&slot&&hours>0){
        personAssigned[pid]=(personAssigned[pid]||0)+hours;
        slotAssigned[sid]=(slotAssigned[sid]||0)+hours;
        basicAssigned+=hours;
        if(slot.top_priority && priority!==slot.top_priority) warning=allocationPriorityLabel(priority)+' ανάθεση · χαμηλότερη προτεραιότητα';
      }
      rowState.push({row:row,pid:pid,sid:sid,hours:hours,person:person,slot:slot,priority:priority,error:error,warning:warning});
    });
    let overSlots=0, unassigned=0;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const cap=allocationSlotsData[sid].capacity_hours||0, assigned=slotAssigned[sid]||0;
      unassigned+=Math.max(0,cap-assigned); overSlots+=Math.max(0,assigned-cap);
    });
    let errorRows=0;
    rowState.forEach(function(st){
      let error=st.error, warning=st.warning;
      if(!error && st.sid && st.slot && (slotAssigned[st.sid]||0)>st.slot.capacity_hours){
        error='Το ίδιο τμήμα / ομάδα έχει συνολικά '+slotAssigned[st.sid]+' ώρες, ενώ διαθέτει '+st.slot.capacity_hours+'.';
      }
      if(!error && st.pid && st.person && (personAssigned[st.pid]||0)>st.person.available_here_hours){
        warning='Υπέρβαση ατομικού διαθέσιμου ωραρίου κατά '+((personAssigned[st.pid]||0)-st.person.available_here_hours)+' ώρες.';
      }
      if(error){ allocationSetStatus(st.row,error,'error'); errorRows++; }
      else if(warning){ allocationSetStatus(st.row,warning,'warning'); }
      else if(st.person&&st.slot&&st.hours>0){ allocationSetStatus(st.row,allocationPriorityLabel(st.priority)+' ανάθεση ✓','ok'); }
      else allocationSetStatus(st.row,'Συμπλήρωσε εκπαιδευτικό και μάθημα.','');
    });
    const assignedEl=document.querySelector('[data-allocation-assigned]'), unassignedEl=document.querySelector('[data-allocation-unassigned]'), overEl=document.querySelector('[data-allocation-over]'), errorsEl=document.querySelector('[data-allocation-errors]');
    if(assignedEl) assignedEl.textContent=String(basicAssigned);
    if(unassignedEl) unassignedEl.textContent=String(unassigned);
    if(overEl) overEl.textContent=String(overSlots);
    if(errorsEl) errorsEl.textContent=String(errorRows);
    Object.keys(allocationPeopleData||{}).forEach(function(pid){
      const summary=document.querySelector('[data-allocation-person-summary="'+CSS.escape(pid)+'"]'); if(!summary) return;
      const p=allocationPeopleData[pid], assigned=personAssigned[pid]||0, remain=Math.max(0,p.available_here_hours-assigned), over=Math.max(0,assigned-p.available_here_hours);
      const a=summary.querySelector('[data-person-assigned]'), r=summary.querySelector('[data-person-remaining]'), o=summary.querySelector('[data-person-over]');
      if(a) a.textContent=String(assigned); if(r) r.textContent=String(remain); if(o) o.textContent=String(over);
    });
  }
  function bindAllocationRow(row){
    if(!row||row.dataset.initialized==='1') return; row.dataset.initialized='1';
    const person=row.querySelector('.allocation-person'), slot=row.querySelector('.allocation-slot'), hours=row.querySelector('.allocation-hours');
    allocationPopulateSlotsForPerson(row,true);
    if(slot){
      const initial=allocationSlotsData[slot.value]||null; if(initial&&hours) hours.max=String(initial.capacity_hours);
    }
  }
  if(allocationList && allocationList.dataset.eventsBound!=='1'){
    allocationList.dataset.eventsBound='1';
    allocationList.addEventListener('change',function(event){
      const row=event.target.closest('[data-allocation-row]'); if(!row) return;
      if(event.target.matches('.allocation-person')){
        const hours=row.querySelector('.allocation-hours'); allocationPopulateSlotsForPerson(row,false); if(hours) hours.value='0'; updateAllocationSummary(); return;
      }
      if(event.target.matches('.allocation-slot')){
        const slot=event.target, hours=row.querySelector('.allocation-hours'), data=allocationSlotsData[slot.value]||null;
        if(data&&hours){ hours.max=String(data.capacity_hours); if((parseInt(hours.value||'0',10)||0)<1) hours.value=String(data.capacity_hours); }
        updateAllocationSummary();
      }
    });
    allocationList.addEventListener('input',function(event){ if(event.target.matches('.allocation-hours')) updateAllocationSummary(); });
    allocationList.addEventListener('click',function(event){
      const remove=event.target.closest('.allocation-remove'); if(!remove || !allocationList.contains(remove)) return;
      const row=remove.closest('[data-allocation-row]'); if(!row) return;
      row.remove();
      if(!allocationList.querySelector('[data-allocation-row]')){const empty=document.createElement('div');empty.id='emptyAllocationState';empty.className='allocation-empty';empty.textContent='Δεν έχει γίνει ακόμη κατανομή. Πάτησε «+ Προσθήκη μαθήματος» για να ξεκινήσεις.';allocationList.appendChild(empty);}
      updateAllocationSummary();
    });
  }
  if(allocationList) allocationRows().forEach(bindAllocationRow);
  if(addAllocation&&allocationTemplate&&allocationList){
    addAllocation.addEventListener('click',function(){
      const empty=document.getElementById('emptyAllocationState'); if(empty) empty.remove();
      const fragment=allocationTemplate.content.cloneNode(true), row=fragment.querySelector('[data-allocation-row]'); allocationList.appendChild(fragment); bindAllocationRow(row); const first=row.querySelector('.allocation-person'); if(first) first.focus(); updateAllocationSummary();
    });
  }
  updateAllocationSummary();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
