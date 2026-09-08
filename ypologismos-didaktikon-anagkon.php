<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/school-profile-general-education.php';
require_once __DIR__ . '/includes/school-profile-workload.php';
require_once __DIR__ . '/includes/ethics-class-formation.php';
require_once __DIR__ . '/includes/personnel-workload.php';
require_once __DIR__ . '/includes/teaching-allocation-engine.php';
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
if (!defined('STAFFING_UI_MAX_BASIC_SECTIONS')) define('STAFFING_UI_MAX_BASIC_SECTIONS', 120);
function staffingUiBasicSectionPostCounts($schoolType) {
    if ($schoolType === 'gymnasio_lt') {
        return array(
            'gym_a'=>staffingUiInt('gym_general_a'), 'gym_b'=>staffingUiInt('gym_general_b'), 'gym_c'=>staffingUiInt('gym_general_c'),
            'lt_a'=>staffingUiInt('gel_general_a'), 'lt_b'=>staffingUiInt('gel_general_b'), 'lt_c'=>staffingUiInt('gel_general_c'),
        );
    }
    $prefix = in_array($schoolType, array('gel','esperino_gel','gymnasio_lt'), true) ? 'gel_general_' : 'gym_general_';
    return array(
        'a' => staffingUiInt($prefix . 'a'),
        'b' => staffingUiInt($prefix . 'b'),
        'c' => staffingUiInt($prefix . 'c'),
    );
}
function staffingUiBasicSectionPostTotal($schoolType) {
    return array_sum(staffingUiBasicSectionPostCounts($schoolType));
}
function staffingUiRenderBasicSectionFields($prefix) {
    $prefix = $prefix === 'gel' ? 'gel' : 'gym';
    echo '<div class="mini-grid">';
    foreach (array('a'=>'Α΄','b'=>'Β΄','c'=>'Γ΄') as $suffix=>$grade) {
        $name = $prefix . '_general_' . $suffix;
        echo '<div class="field"><label for="' . staffingUiH($name) . '">' . staffingUiH($grade) . ' τάξη</label>';
        echo '<input min="0" max="' . (int) STAFFING_UI_MAX_BASIC_SECTIONS . '" inputmode="numeric" step="1" type="number" data-basic-section="' . staffingUiH($prefix) . '" id="' . staffingUiH($name) . '" name="' . staffingUiH($name) . '" value="' . staffingUiH(staffingUiPost($name, '0')) . '"></div>';
    }
    echo '</div>';
}
function staffingUiSchoolTypeLabel($schoolType, $long = false) {
    $labels = array(
        'gymnasio' => array('short'=>'Ημερήσιο Γυμνάσιο','long'=>'Ημερήσιο Γυμνάσιο'),
        'gel' => array('short'=>'Ημερήσιο ΓΕΛ','long'=>'Ημερήσιο Γενικό Λύκειο'),
        'esperino_gymnasio' => array('short'=>'Εσπερινό Γυμνάσιο','long'=>'Εσπερινό Γυμνάσιο'),
        'esperino_gel' => array('short'=>'Εσπερινό ΓΕΛ','long'=>'Εσπερινό Γενικό Λύκειο'),
        'gymnasio_lt' => array('short'=>'Γυμνάσιο με Λ.Τ.','long'=>'Γυμνάσιο με Λ.Τ.'),
    );
    if (!isset($labels[$schoolType])) $schoolType = 'gymnasio';
    return $labels[$schoolType][$long ? 'long' : 'short'];
}
function staffingUiNullableInt($key) {
    if (!isset($_POST[$key]) || $_POST[$key] === '') return null;
    return max(0, (int) $_POST[$key]);
}
function staffingUiNullableBool($key) {
    if (!isset($_POST[$key]) || $_POST[$key] === '') return null;
    return $_POST[$key] === '1';
}
function staffingUiEthicsGradeWithPrefix($prefix, $suffix) {
    $exempt = staffingUiNullableInt($prefix . $suffix . '_exempt');
    $timely = staffingUiNullableBool($prefix . $suffix . '_timely');
    $equivalent = staffingUiNullableInt($prefix . $suffix . '_equivalent');
    if ($exempt === null && $timely === null && $equivalent === null) return array();
    return array(
        'exempt_students' => $exempt,
        'within_fifth_day' => $timely,
        'equivalent_ethics_sections' => $equivalent,
    );
}
function staffingUiEthicsGrade($suffix) {
    return staffingUiEthicsGradeWithPrefix('ethics_', $suffix);
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
function staffingUiEthicsPanelHasInput($prefix) {
    foreach (array('a','b','c') as $suffix) {
        if (staffingUiPost($prefix.$suffix.'_exempt') !== ''
            || staffingUiPost($prefix.$suffix.'_timely') !== ''
            || staffingUiPost($prefix.$suffix.'_equivalent') !== '') return true;
    }
    return false;
}
function staffingUiRenderEthicsPanel($prefix, $title, $panelId) {
    $hasInput = staffingUiEthicsPanelHasInput($prefix);
    echo '<details class="option-panel" id="' . staffingUiH($panelId) . '"' . ($hasInput ? ' open' : '') . '>';
    echo '<summary>' . staffingUiH($title) . ' <small style="font-weight:400;color:var(--edu-muted)">άνοιξέ το μόνο αν θέλεις να υπολογιστεί</small></summary>';
    echo '<div class="option-panel-body">';
    echo '<p class="help">Αν δεν έχεις ακόμη τα στοιχεία απαλλαγών, άφησε την ενότητα κλειστή. Οι αντίστοιχες ώρες θα παραμείνουν σε εκκρεμότητα και δεν θα προστεθούν τεχνητά στα αποτελέσματα.</p>';
    foreach (array('a'=>'Α΄','b'=>'Β΄','c'=>'Γ΄') as $s=>$grade) {
        echo '<div class="grade-box"><h4>' . staffingUiH($grade) . ' τάξη</h4><div class="mini-grid">';
        echo '<div class="field"><label>Απαλλασσόμενοι/ες</label><input min="0" step="1" type="number" name="' . staffingUiH($prefix.$s.'_exempt') . '" value="' . staffingUiH(staffingUiPost($prefix.$s.'_exempt')) . '" placeholder="άγνωστο"></div>';
        echo '<div class="field"><label>Συμπληρώθηκαν έως 5η ημέρα;</label><select name="' . staffingUiH($prefix.$s.'_timely') . '"><option value=""' . (staffingUiPost($prefix.$s.'_timely')===''?' selected':'') . '>— άγνωστο —</option><option value="1"' . (staffingUiPost($prefix.$s.'_timely')==='1'?' selected':'') . '>Ναι</option><option value="0"' . (staffingUiPost($prefix.$s.'_timely')==='0'?' selected':'') . '>Όχι</option></select></div>';
        echo '<div class="field"><label>Ισοδύναμα τμήματα Ηθικής <small>0 = κρίθηκε ότι δεν σχηματίζεται</small></label><input min="0" step="1" type="number" name="' . staffingUiH($prefix.$s.'_equivalent') . '" value="' . staffingUiH(staffingUiPost($prefix.$s.'_equivalent')) . '" placeholder="άγνωστο"></div>';
        echo '</div></div>';
    }
    echo '</div></details>';
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

function staffingUiMatrixNotices($profile, $model) {
    $result = array('dependencies'=>array(), 'regulatory'=>array());
    if (!$profile || !$model) return $result;

    $realized = schoolProfileRealize($profile, $model);
    $isComposite = isset($profile['structures']) && is_array($profile['structures']) && count($profile['structures']) > 1;
    $ethicsGrades = array();
    $dependencySeen = array();
    $regulatorySeen = array();

    foreach ($realized['slots'] as $slot) {
        $staffingStatus = isset($slot['staffing_status']) ? (string)$slot['staffing_status'] : '';
        $dependency = isset($slot['dependency']) && is_array($slot['dependency']) ? $slot['dependency'] : array();
        $dependencyResolved = !empty($dependency['resolved']);
        $isDependency = in_array($staffingStatus, array('dependency_unresolved','periodic_hours','thematic_hours_not_fixed'), true)
            || (!$dependencyResolved && $staffingStatus !== 'regulatory_gap');
        $structure = isset($slot['school']) ? (string)$slot['school'] : '';
        $structureLabel = $isComposite ? personnelWorkloadStructureShortLabel($structure) . ' ' : '';

        if ($isDependency) {
            $slotId = isset($slot['slot_id']) ? (string)$slot['slot_id'] : '';
            if ($slotId !== '' && preg_match('/religion_ethics$/', $slotId)) {
                $ethicsGrades[$structureLabel . (string)$slot['grade']] = true;
            } else {
                $label = trim($structureLabel . (string)$slot['grade'] . ' · ' . (string)$slot['subject']);
                $dependencySeen[$label] = true;
            }
        }

        if ($staffingStatus === 'regulatory_gap') {
            $label = trim($structureLabel . (string)$slot['grade'] . ' · ' . (string)$slot['subject']);
            $regulatorySeen[$label] = true;
        }
    }

    if (!empty($ethicsGrades)) {
        $grades = array_keys($ethicsGrades);
        sort($grades, SORT_NATURAL);
        $result['dependencies'][] = 'Εκκρεμούν στοιχεία για Ηθική στις τάξεις ' . implode(', ', $grades) . '. Τα Θρησκευτικά έχουν υπολογιστεί προσωρινά στα κανονικά τμήματα· συμπλήρωσε τα στοιχεία Ηθικής για ακριβή ανακατανομή όπου απαιτείται.';
    }
    foreach (array_keys($dependencySeen) as $label) {
        $result['dependencies'][] = 'Εκκρεμούν πρόσθετα στοιχεία για ' . $label . '. Οι αντίστοιχες ώρες δεν περιλαμβάνονται ακόμη στα σύνολα.';
    }
    foreach (array_keys($regulatorySeen) as $label) {
        $result['regulatory'][] = 'Για ' . $label . ' δεν υπάρχει ρητή αντιστοίχιση ανάθεσης στην ισχύουσα κανονιστική βάση. Οι αντίστοιχες ώρες εξαιρούνται από τον υπολογισμό.';
    }
    return $result;
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
function staffingUiPayloadArrays($name) {
    if (!isset($_POST[$name]) || !is_string($_POST[$name]) || trim($_POST[$name]) === '') return array();
    $decoded = json_decode($_POST[$name], true);
    return is_array($decoded) ? $decoded : array();
}
function staffingUiPersonnelRowsFromPost() {
    $keys = array('person_id','display_name','specialty_code','secondary_specialty_code','required_teaching_hours','service_years','service_months','service_days','role','assigned_external_hours','director_sections_band','hours_branch','obligation_source','source_base_required_hours','source_reduction_hours','source_hours_at_unit');
    $payload = staffingUiPayloadArrays('personnel_payload_json');
    $arrays = array();
    $count = 0;
    foreach ($keys as $key) {
        $name = 'personnel_' . $key;
        if (isset($payload[$name]) && is_array($payload[$name])) $arrays[$key] = $payload[$name];
        else $arrays[$key] = isset($_POST[$name]) && is_array($_POST[$name]) ? $_POST[$name] : array();
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
        $obligationSource = isset($arrays['obligation_source'][$i]) ? trim((string)$arrays['obligation_source'][$i]) : '';
        $sourceBaseRequiredHours = isset($arrays['source_base_required_hours'][$i]) ? trim((string)$arrays['source_base_required_hours'][$i]) : '';
        $sourceReductionHours = isset($arrays['source_reduction_hours'][$i]) ? trim((string)$arrays['source_reduction_hours'][$i]) : '';
        $sourceHoursAtUnit = isset($arrays['source_hours_at_unit'][$i]) ? trim((string)$arrays['source_hours_at_unit'][$i]) : '';
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
            'obligation_source'=>$obligationSource,
            'source_base_required_hours'=>$sourceBaseRequiredHours,
            'source_reduction_hours'=>$sourceReductionHours,
            'source_hours_at_unit'=>$sourceHoursAtUnit,
        );
    }
    return $rows;
}
function staffingUiAllocationRowsFromPost() {
    $payload = staffingUiPayloadArrays('allocation_payload_json');
    $personIds = isset($payload['allocation_person_id']) && is_array($payload['allocation_person_id']) ? $payload['allocation_person_id'] : (isset($_POST['allocation_person_id']) && is_array($_POST['allocation_person_id']) ? $_POST['allocation_person_id'] : array());
    $slotIds = isset($payload['allocation_slot_id']) && is_array($payload['allocation_slot_id']) ? $payload['allocation_slot_id'] : (isset($_POST['allocation_slot_id']) && is_array($_POST['allocation_slot_id']) ? $_POST['allocation_slot_id'] : array());
    $hours = isset($payload['allocation_hours']) && is_array($payload['allocation_hours']) ? $payload['allocation_hours'] : (isset($_POST['allocation_hours']) && is_array($_POST['allocation_hours']) ? $_POST['allocation_hours'] : array());
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
    $payload = array(
        'personnel_person_id'=>array(),
        'personnel_display_name'=>array(),
        'personnel_specialty_code'=>array(),
        'personnel_secondary_specialty_code'=>array(),
        'personnel_required_teaching_hours'=>array(),
        'personnel_service_years'=>array(),
        'personnel_service_months'=>array(),
        'personnel_service_days'=>array(),
        'personnel_role'=>array(),
        'personnel_assigned_external_hours'=>array(),
        'personnel_director_sections_band'=>array(),
        'personnel_hours_branch'=>array(),
        'personnel_obligation_source'=>array(),
        'personnel_source_base_required_hours'=>array(),
        'personnel_source_reduction_hours'=>array(),
        'personnel_source_hours_at_unit'=>array(),
    );
    foreach ($rows as $person) {
        $payload['personnel_person_id'][] = isset($person['person_id']) ? $person['person_id'] : '';
        $payload['personnel_display_name'][] = isset($person['display_name']) ? $person['display_name'] : '';
        $payload['personnel_specialty_code'][] = isset($person['specialty_code']) ? $person['specialty_code'] : '';
        $payload['personnel_secondary_specialty_code'][] = isset($person['secondary_specialty_code']) ? $person['secondary_specialty_code'] : '';
        $payload['personnel_required_teaching_hours'][] = isset($person['required_teaching_hours']) ? $person['required_teaching_hours'] : '';
        $payload['personnel_service_years'][] = isset($person['service']['years']) ? $person['service']['years'] : 0;
        $payload['personnel_service_months'][] = isset($person['service']['months']) ? $person['service']['months'] : 0;
        $payload['personnel_service_days'][] = isset($person['service']['days']) ? $person['service']['days'] : 0;
        $payload['personnel_role'][] = isset($person['role']) ? $person['role'] : 'teacher';
        $payload['personnel_assigned_external_hours'][] = isset($person['assigned_external_hours']) ? $person['assigned_external_hours'] : 0;
        $payload['personnel_director_sections_band'][] = isset($person['director_sections_band']) ? $person['director_sections_band'] : '';
        $payload['personnel_hours_branch'][] = isset($person['hours_branch']) ? $person['hours_branch'] : '';
        $payload['personnel_obligation_source'][] = isset($person['obligation_source']) ? $person['obligation_source'] : '';
        $payload['personnel_source_base_required_hours'][] = isset($person['source_base_required_hours']) ? $person['source_base_required_hours'] : '';
        $payload['personnel_source_reduction_hours'][] = isset($person['source_reduction_hours']) ? $person['source_reduction_hours'] : '';
        $payload['personnel_source_hours_at_unit'][] = isset($person['source_hours_at_unit']) ? $person['source_hours_at_unit'] : '';
    }
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json)) $json = '{}';
    echo '<input type="hidden" name="personnel_payload_json" value="' . staffingUiH($json) . '">';
}
function staffingUiRenderAllocationStateHiddenInputs($rows) {
    $payload = array(
        'allocation_person_id'=>array(),
        'allocation_slot_id'=>array(),
        'allocation_hours'=>array(),
    );
    foreach ($rows as $row) {
        $payload['allocation_person_id'][] = isset($row['person_id']) ? $row['person_id'] : '';
        $payload['allocation_slot_id'][] = isset($row['slot_id']) ? $row['slot_id'] : '';
        $payload['allocation_hours'][] = isset($row['hours']) ? $row['hours'] : 0;
    }
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json)) $json = '{}';
    echo '<input type="hidden" name="allocation_payload_json" value="' . staffingUiH($json) . '">';
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
        $structureOrder = array('gymnasio'=>1,'esperino_gymnasio'=>1,'gel'=>2,'esperino_gel'=>2);
        $schoolA = isset($a['school']) ? $a['school'] : '';
        $sa = isset($structureOrder[$schoolA]) ? $structureOrder[$schoolA] : 99;
        $schoolB = isset($b['school']) ? $b['school'] : '';
        $sb = isset($structureOrder[$schoolB]) ? $structureOrder[$schoolB] : 99;
        if ($sa !== $sb) return $sa - $sb;
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
function staffingUiRenderAllocationSlotOptions($slots, $selected, $allSlots = null) {
    echo '<option value="">— επιλογή τμήματος / ομάδας και μαθήματος —</option>';
    if ($selected !== '' && !isset($slots[$selected]) && is_array($allSlots) && isset($allSlots[$selected])) {
        echo '<option value="' . staffingUiH($selected) . '" selected disabled>' . staffingUiH(staffingUiAllocationSlotOptionLabel($allSlots[$selected]) . ' · χωρίς επιλέξιμο εκπαιδευτικό') . '</option>';
    }
    $lastGroup = null;
    $openGroup = false;
    foreach ($slots as $slotId=>$slot) {
        $grade = isset($slot['grade']) ? $slot['grade'] : 'Άλλο';
        $structureLabel = isset($slot['structure_label']) ? trim((string)$slot['structure_label']) : '';
        $groupLabel = ($structureLabel !== '' ? $structureLabel . ' · ' : '') . $grade . ' τάξη';
        if ($groupLabel !== $lastGroup) {
            if ($openGroup) echo '</optgroup>';
            echo '<optgroup label="' . staffingUiH($groupLabel) . '">';
            $openGroup = true;
            $lastGroup = $groupLabel;
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
        'atomic_slot_requires_full_hours'=>'Το μάθημα στο συγκεκριμένο τμήμα / ομάδα πρέπει να ανατεθεί ολόκληρο σε έναν εκπαιδευτικό· δεν επιτρέπεται διάσπαση των ωρών.',
        'specialty_not_eligible'=>'Οι δηλωμένες ειδικότητες του εκπαιδευτικού δεν έχουν ανάθεση στο συγκεκριμένο μάθημα.',
        'slot_overallocated_across_roster'=>'Το ίδιο τμήμα / ομάδα έχει κατανεμηθεί πάνω από τις διαθέσιμες ώρες του.',
    );
    return isset($map[$error]) ? $map[$error] : $error;
}
function staffingUiBAssignmentLimitWarning() {
    return 'Οι ώρες μαθημάτων Β΄ ανάθεσης, από τη βασική και τη δεύτερη ειδικότητα συνολικά, υπερβαίνουν το όριο των 10 διδακτικών ωρών. Υπέρβαση επιτρέπεται μόνο κατ’ εξαίρεση, ύστερα από απόφαση ΠΥΣΔΕ και υπό τις προβλεπόμενες προϋποθέσεις.';
}
function staffingUiCompactSpecialtyCodes($codes, $limit = 8) {
    $codes = is_array($codes) ? array_values(array_unique(array_filter($codes))) : array();
    if (count($codes) <= $limit) return implode(', ', $codes);
    $visible = array_slice($codes, 0, $limit);
    return implode(', ', $visible) . ' +' . (count($codes) - $limit);
}
function staffingUiAllocationAssignmentLabel($rowResult) {
    if (!$rowResult || empty($rowResult['priority'])) return '';
    $label = staffingUiPriorityLabel($rowResult['priority']) . ' ανάθεση';
    $usedCode = isset($rowResult['used_specialty_code']) ? teacherSpecialtyCanonicalCode($rowResult['used_specialty_code']) : '';
    $source = isset($rowResult['specialty_source']) ? $rowResult['specialty_source'] : '';
    if ($source === 'secondary' && $usedCode !== '') {
        $label .= ' · μέσω 2ης ειδικότητας ' . $usedCode;
    }
    return $label;
}
function staffingUiAllocationWarningLabels($rowResult) {
    $labels = array();
    if (!$rowResult || empty($rowResult['warnings'])) return $labels;
    foreach ($rowResult['warnings'] as $warning) {
        if ($warning === 'uses_lower_priority_assignment') continue;
        elseif ($warning === 'b_assignment_hours_exceed_10_limit') $labels[] = staffingUiBAssignmentLimitWarning();
        else $labels[] = $warning;
    }
    return array_values(array_unique($labels));
}
function staffingUiSchoolStateKeys() {
    return array(
        // School identity must travel with every POST made from the later tabs.
        // Without these fields the personnel/allocation forms would keep the
        // structural profile but lose the real myschool identity on reload.
        'school_type','school_registry_id','school_code','school_name',
        'gym_general_a','gym_general_b','gym_general_c',
        'gym_lang_a_fr','gym_lang_a_de','gym_lang_a_it','gym_lang_b_fr','gym_lang_b_de','gym_lang_b_it','gym_lang_c_fr','gym_lang_c_de','gym_lang_c_it',
        'gym_tech_split_a','gym_tech_split_b','gym_tech_split_c',
        'gel_general_a','gel_general_b','gel_general_c',
        'gel_lang_a_fr','gel_lang_a_de','gel_lang_b_fr','gel_lang_b_de',
        'gel_b_hum','gel_b_sci','gel_c_hum','gel_c_scihealth','gel_c_econit',
        'gel_c_field_math','gel_c_field_bio','gel_c_cond_math','gel_c_cond_history','egel_b_period',
        'ethics_a_exempt','ethics_a_timely','ethics_a_equivalent','ethics_b_exempt','ethics_b_timely','ethics_b_equivalent','ethics_c_exempt','ethics_c_timely','ethics_c_equivalent',
        'lt_ethics_a_exempt','lt_ethics_a_timely','lt_ethics_a_equivalent','lt_ethics_b_exempt','lt_ethics_b_timely','lt_ethics_b_equivalent','lt_ethics_c_exempt','lt_ethics_c_timely','lt_ethics_c_equivalent'
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
$staffingAction = '';
if ($requestMethod === 'POST') {
    $staffingAction = staffingUiPost('staffing_action', '');
    if ($staffingAction === '') $staffingAction = staffingUiPost('staffing_action_fallback', '');
}
$submitted = $requestMethod === 'POST'
    && in_array($staffingAction, array('profile','personnel','allocation','allocation_auto'), true);
$schoolType = staffingUiPost('school_type', 'gymnasio');
if (!in_array($schoolType, array('gymnasio','gel','esperino_gymnasio','esperino_gel','gymnasio_lt'), true)) $schoolType = 'gymnasio';
// Keep identity available independently of which tab submitted the page.
$schoolName = trim((string) staffingUiPost('school_name', ''));
$schoolRegistryId = trim((string) staffingUiPost('school_registry_id', ''));
$schoolCode = trim((string) staffingUiPost('school_code', ''));
$profile = null;
$readiness = null;
$matrix = null;
$teachingModel = null;
$displayMatrix = null;
$collapsedSkills = array('active'=>false,'hours'=>0,'unit_count'=>0,'units'=>array(),'label'=>'Οποιαδήποτε ειδικότητα','subject'=>'Εργαστήρια Δεξιοτήτων');
$staffingNotices = array('dependencies'=>array(),'regulatory'=>array());
$schoolProfileInputErrors = array();
$postedBasicSectionTotal = $submitted ? staffingUiBasicSectionPostTotal($schoolType) : 0;
if ($submitted && $postedBasicSectionTotal > STAFFING_UI_MAX_BASIC_SECTIONS) {
    $schoolProfileInputErrors[] = 'Το σύνολο των βασικών τμημάτων της σχολικής μονάδας είναι ' . $postedBasicSectionTotal
        . ' και υπερβαίνει το τεχνικό όριο ασφαλείας των ' . STAFFING_UI_MAX_BASIC_SECTIONS . ' τμημάτων.';
}

if ($submitted && empty($schoolProfileInputErrors)) {
    if ($schoolType === 'gymnasio_lt') {
        try {
            if (!function_exists('schoolProfileBuildGymnasiumWithLyceumClasses2026')
                || !function_exists('personnelWorkloadStructureShortLabel')) {
                throw new RuntimeException('missing_composite_profile_runtime');
            }
            $profile = schoolProfileBuildGymnasiumWithLyceumClasses2026(array(
            'profile_id' => 'ui-gymnasio-lt-' . date('YmdHis'),
            'school' => array(
                'type' => 'Γυμνάσιο με Λυκειακές Τάξεις',
                'registry_id' => $schoolRegistryId,
                'ministry_code' => $schoolCode,
                'name' => $schoolName !== '' ? $schoolName : 'Προσωρινό προφίλ Γυμνασίου με Λυκειακές Τάξεις',
            ),
            'source' => array('kind' => $schoolRegistryId !== '' ? 'school_registry_v1' : 'manual_frontend_test'),
            'gymnasium_general_sections' => array(
                'Α΄' => staffingUiInt('gym_general_a'),
                'Β΄' => staffingUiInt('gym_general_b'),
                'Γ΄' => staffingUiInt('gym_general_c'),
            ),
            'gymnasium_second_foreign_language_groups' => array(
                'Α΄' => array('Γαλλικά'=>staffingUiInt('gym_lang_a_fr'),'Γερμανικά'=>staffingUiInt('gym_lang_a_de'),'Ιταλικά'=>staffingUiInt('gym_lang_a_it')),
                'Β΄' => array('Γαλλικά'=>staffingUiInt('gym_lang_b_fr'),'Γερμανικά'=>staffingUiInt('gym_lang_b_de'),'Ιταλικά'=>staffingUiInt('gym_lang_b_it')),
                'Γ΄' => array('Γαλλικά'=>staffingUiInt('gym_lang_c_fr'),'Γερμανικά'=>staffingUiInt('gym_lang_c_de'),'Ιταλικά'=>staffingUiInt('gym_lang_c_it')),
            ),
            'gymnasium_technology_informatics_split_sections' => array(
                'Α΄' => staffingUiInt('gym_tech_split_a'),
                'Β΄' => staffingUiInt('gym_tech_split_b'),
                'Γ΄' => staffingUiInt('gym_tech_split_c'),
            ),
            'gymnasium_ethics_by_grade' => array(
                'Α΄' => staffingUiEthicsGrade('a'),
                'Β΄' => staffingUiEthicsGrade('b'),
                'Γ΄' => staffingUiEthicsGrade('c'),
            ),
            'lyceum_general_sections' => array(
                'Α΄' => staffingUiInt('gel_general_a'),
                'Β΄' => staffingUiInt('gel_general_b'),
                'Γ΄' => staffingUiInt('gel_general_c'),
            ),
            'lyceum_second_foreign_language_groups' => array(
                'Α΄' => array('Γαλλικά'=>staffingUiInt('gel_lang_a_fr'),'Γερμανικά'=>staffingUiInt('gel_lang_a_de')),
                'Β΄' => array('Γαλλικά'=>staffingUiInt('gel_lang_b_fr'),'Γερμανικά'=>staffingUiInt('gel_lang_b_de')),
            ),
            'lyceum_orientation_sections' => array(
                'Β΄' => array('humanities'=>staffingUiInt('gel_b_hum'),'science'=>staffingUiInt('gel_b_sci')),
                'Γ΄' => array('humanities'=>staffingUiInt('gel_c_hum'),'science_health'=>staffingUiInt('gel_c_scihealth'),'economics_it'=>staffingUiInt('gel_c_econit')),
            ),
            'lyceum_grade_c_science_health_field_groups' => array(
                'Μαθηματικά' => staffingUiInt('gel_c_field_math'),
                'Βιολογία' => staffingUiInt('gel_c_field_bio'),
            ),
            'lyceum_grade_c_conditional_groups' => array(
                'Μαθηματικά' => staffingUiInt('gel_c_cond_math'),
                'Ιστορία' => staffingUiInt('gel_c_cond_history'),
            ),
            'lyceum_ethics_by_grade' => array(
                'Α΄' => staffingUiEthicsGradeWithPrefix('lt_ethics_', 'a'),
                'Β΄' => staffingUiEthicsGradeWithPrefix('lt_ethics_', 'b'),
                'Γ΄' => staffingUiEthicsGradeWithPrefix('lt_ethics_', 'c'),
            ),
        ));
        } catch (Exception $e) {
            error_log('Staffing composite profile runtime error: ' . $e->getMessage());
            $profile = null;
            $schoolProfileInputErrors[] = 'Δεν ήταν δυνατό να φορτωθεί το προφίλ «Γυμνάσιο με Λυκειακές Τάξεις». Βεβαιώσου ότι έχουν ενημερωθεί μαζί το κύριο αρχείο και τα includes της ίδιας έκδοσης.';
        }
    } elseif ($schoolType === 'esperino_gymnasio') {
        $profile = schoolProfileBuildEveningGymnasium2026(array(
            'profile_id' => 'ui-esperino-gymnasio-' . date('YmdHis'),
            'school' => array(
                'type' => 'Εσπερινό Γυμνάσιο',
                'registry_id' => $schoolRegistryId,
                'ministry_code' => $schoolCode,
                'name' => $schoolName !== '' ? $schoolName : 'Προσωρινό προφίλ Εσπερινού Γυμνασίου',
            ),
            'source' => array('kind' => $schoolRegistryId !== '' ? 'school_registry_v1' : 'manual_frontend_test'),
            'general_sections' => array(
                'Α΄' => staffingUiInt('gym_general_a'),
                'Β΄' => staffingUiInt('gym_general_b'),
                'Γ΄' => staffingUiInt('gym_general_c'),
            ),
            'ethics_by_grade' => array(
                'Α΄' => staffingUiEthicsGrade('a'),
                'Β΄' => staffingUiEthicsGrade('b'),
                'Γ΄' => staffingUiEthicsGrade('c'),
            ),
        ));
    } elseif ($schoolType === 'gymnasio') {
        $profile = schoolProfileBuildDayGymnasium2026(array(
            'profile_id' => 'ui-gymnasio-' . date('YmdHis'),
            'school' => array(
                'type' => 'Ημερήσιο Γυμνάσιο',
                'registry_id' => $schoolRegistryId,
                'ministry_code' => $schoolCode,
                'name' => $schoolName !== '' ? $schoolName : 'Προσωρινό προφίλ Γυμνασίου',
            ),
            'source' => array('kind' => $schoolRegistryId !== '' ? 'school_registry_v1' : 'manual_frontend_test'),
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
    } elseif ($schoolType === 'esperino_gel') {
        $profile = schoolProfileBuildEveningGel2026(array(
            'profile_id' => 'ui-esperino-gel-' . date('YmdHis'),
            'school' => array(
                'type' => 'Εσπερινό Γενικό Λύκειο',
                'registry_id' => $schoolRegistryId,
                'ministry_code' => $schoolCode,
                'name' => $schoolName !== '' ? $schoolName : 'Προσωρινό προφίλ Εσπερινού ΓΕΛ',
            ),
            'source' => array('kind' => $schoolRegistryId !== '' ? 'school_registry_v1' : 'manual_frontend_test'),
            'general_sections' => array(
                'Α΄' => staffingUiInt('gel_general_a'),
                'Β΄' => staffingUiInt('gel_general_b'),
                'Γ΄' => staffingUiInt('gel_general_c'),
            ),
            'orientation_sections' => array(
                'Β΄' => array('humanities'=>staffingUiInt('gel_b_hum'),'science'=>staffingUiInt('gel_b_sci')),
                'Γ΄' => array('humanities'=>staffingUiInt('gel_c_hum'),'science_health'=>staffingUiInt('gel_c_scihealth'),'economics_it'=>staffingUiInt('gel_c_econit')),
            ),
            'grade_c_science_health_field_groups' => array(
                'Μαθηματικά' => staffingUiInt('gel_c_field_math'),
                'Βιολογία' => staffingUiInt('gel_c_field_bio'),
            ),
            'grade_c_conditional_groups' => array(
                'Μαθηματικά' => staffingUiInt('gel_c_cond_math'),
                'Ιστορία' => staffingUiInt('gel_c_cond_history'),
            ),
            'grade_b_period' => staffingUiPost('egel_b_period', 'Α΄ τετράμηνο'),
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
                'registry_id' => $schoolRegistryId,
                'ministry_code' => $schoolCode,
                'name' => $schoolName !== '' ? $schoolName : 'Προσωρινό προφίλ ΓΕΛ',
            ),
            'source' => array('kind' => $schoolRegistryId !== '' ? 'school_registry_v1' : 'manual_frontend_test'),
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
    $staffingNotices = staffingUiMatrixNotices($profile, $teachingModel);
}
$generalSectionTotal = $profile ? schoolProfileTotalGeneralSections($profile) : 0;
$directorSectionsBandAuto = personnelWorkloadDirectorSectionsBandFromCount($generalSectionTotal);

$calculationAvailable = $submitted && $profile && $matrix && $displayMatrix;
$activePanel = staffingUiPost('active_panel', $calculationAvailable ? 'results' : 'school');
if (!in_array($activePanel, array('school','results','personnel','allocation','vacancies','specialties'), true)) $activePanel = $calculationAvailable ? 'results' : 'school';
if (!$calculationAvailable) $activePanel = 'school';
if ($calculationAvailable && $staffingAction === 'personnel') $activePanel = 'personnel';
if ($calculationAvailable && in_array($staffingAction, array('allocation','allocation_auto'), true)) $activePanel = 'allocation';

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
$allocationAutoProposal = null;
if ($allocationEnabled && $staffingAction === 'allocation_auto') {
    $allocationAutoProposal = teachingAllocationEngineProposal($profile, $allocationPeople, $allocationRows, $teachingModel);
    if (isset($allocationAutoProposal['status']) && $allocationAutoProposal['status'] === 'ok') {
        $allocationRows = isset($allocationAutoProposal['combined_allocations']) ? $allocationAutoProposal['combined_allocations'] : $allocationRows;
        $allocationPlan = isset($allocationAutoProposal['combined_plan']) ? $allocationAutoProposal['combined_plan'] : $allocationPlan;
        $allocationRowResults = $allocationPlan && isset($allocationPlan['allocation_rows']) ? $allocationPlan['allocation_rows'] : array();
    }
}
$vacanciesEnabled = $submitted && $profile && $matrix && $matrix['readiness'] !== 'structure_only';
$specialtyBalanceEnabled = $vacanciesEnabled;
$allocationPeopleClient = array();
foreach ($allocationPeople as $person) {
    $normalized = personnelWorkloadNormalizePerson($person);
    if ($normalized['status'] !== 'resolved') continue;
    $allocationPeopleClient[$person['person_id']] = array(
        'person_id'=>$person['person_id'],
        'display_name'=>$person['display_name'],
        'label'=>staffingUiAllocationPersonLabel($person),
        'specialty_code'=>$person['specialty_code'],
        'secondary_specialty_code'=>isset($person['secondary_specialty_code']) ? $person['secondary_specialty_code'] : '',
        'required_hours'=>(int)$normalized['required_teaching_hours'],
        'external_hours'=>(int)$normalized['assigned_external_hours'],
        'available_here_hours'=>(int)$normalized['remaining_before_profile_hours'],
    );
}
$allocationSelectableSlots = array();
$allocationNoEligibleSlotCount = 0;
$allocationNoEligibleHours = 0;
foreach ($allocationSlots as $slotId=>$slot) {
    $hasEligiblePerson = false;
    foreach ($allocationPeopleClient as $personData) {
        if (personnelWorkloadBestAssignmentForSlot($slot, $personData) !== null) {
            $hasEligiblePerson = true;
            break;
        }
    }
    if ($hasEligiblePerson) {
        $allocationSelectableSlots[$slotId] = $slot;
    } else {
        $allocationNoEligibleSlotCount++;
        $allocationNoEligibleHours += isset($slot['capacity_hours']) ? (int)$slot['capacity_hours'] : 0;
    }
}
$allocationSlotsClient = array();
foreach ($allocationSlots as $slotId=>$slot) {
    $reportingBucket = $profile ? personnelWorkloadReportingBucketForSlot($profile, $slot) : null;
    $allocationSlotsClient[$slotId] = array(
        'slot_id'=>$slotId,
        'label'=>staffingUiAllocationSlotOptionLabel($slot),
        'slot_label'=>$slot['slot_label'],
        'school'=>isset($slot['school']) ? $slot['school'] : '',
        'structure_label'=>isset($slot['structure_label']) ? $slot['structure_label'] : '',
        'grade'=>$slot['grade'],
        'subject'=>$slot['subject'],
        'capacity_hours'=>(int)$slot['capacity_hours'],
        'eligible_by_priority'=>$slot['eligible_by_priority'],
        'top_priority'=>isset($slot['top_priority']) ? $slot['top_priority'] : null,
        'has_eligible_person'=>isset($allocationSelectableSlots[$slotId]),
        'reporting_bucket'=>$reportingBucket,
        'choice_option'=>isset($slot['choice_option']) ? $slot['choice_option'] : '',
        'track'=>isset($slot['track']) ? $slot['track'] : (isset($slot['profile_track']) ? $slot['profile_track'] : ''),
    );
}
$vacancySlotState = array();
foreach ($allocationSlots as $slotId=>$slot) {
    $capacity = isset($slot['capacity_hours']) ? (int)$slot['capacity_hours'] : 0;
    $remaining = $capacity;
    if ($allocationPlan && isset($allocationPlan['slots'][$slotId])) {
        $remaining = isset($allocationPlan['slots'][$slotId]['remaining_hours']) ? (int)$allocationPlan['slots'][$slotId]['remaining_hours'] : $capacity;
    }
    $vacancySlotState[$slotId] = array(
        'remaining_hours'=>max(0, $remaining),
        'has_eligible_person'=>isset($allocationSelectableSlots[$slotId]),
    );
}

$specialtyBalanceReport = null;
if ($specialtyBalanceEnabled) {
    $specialtyBalanceReport = personnelWorkloadSpecialtyBalanceReport($profile, $allocationPeople, $allocationRows, $teachingModel);
}
$specialtyLabelsClient = array();
foreach ($allocationSlots as $slot) {
    if (empty($slot['eligible_by_priority'])) continue;
    foreach ($slot['eligible_by_priority'] as $codes) {
        if (!is_array($codes)) continue;
        foreach ($codes as $code) {
            $canonical = teacherSpecialtyCanonicalCode($code);
            if ($canonical !== '' && !isset($specialtyLabelsClient[$canonical])) $specialtyLabelsClient[$canonical] = teacherSpecialtyLabel($canonical);
        }
    }
}
foreach ($allocationPeopleClient as $personData) {
    foreach (array('specialty_code','secondary_specialty_code') as $key) {
        $canonical = isset($personData[$key]) ? teacherSpecialtyCanonicalCode($personData[$key]) : '';
        if ($canonical !== '' && !isset($specialtyLabelsClient[$canonical])) $specialtyLabelsClient[$canonical] = teacherSpecialtyLabel($canonical);
    }
}
uksort($specialtyLabelsClient, 'strnatcmp');
?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Υπολογισμός διδακτικών αναγκών σχολικής μονάδας</title>
  <link rel="stylesheet" href="<?php echo staffingUiH(edu_asset_url('assets/common.css')); ?>">
  <link rel="stylesheet" href="<?php echo staffingUiH(edu_asset_url('assets/staffing-simulator.css')); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-staffing-simulator">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<main id="schoolStaffingSimulator" class="app">
  <?php calculatorHero(array(
    'title_html' => 'Υπολογισμός διδακτικών αναγκών σχολικής μονάδας',
    'intro' => 'Καταχώρισε τα πραγματικά στοιχεία της σχολικής μονάδας και δες πώς μετατρέπεται το ωρολόγιο πρόγραμμα σε ώρες ανά κλάδο, διαθέσιμο προσωπικό και προτεινόμενη κατανομή.',
    'meta_class' => 'meta',
    'badges' => array('2026–2027','Στοιχεία σχολικής μονάδας','Ωρολόγιο + Αναθέσεις','Α΄ · Β΄ · Γ΄','Ηθική','Αυτόματη πρόταση + χειροκίνητες αλλαγές')
  )); ?>

  <div class="staffing-stage-toolbar">
    <div class="mode-tabs" aria-label="Στάδια εργαλείου" role="tablist">
      <button type="button" id="staffingTabSchool" class="mode-tab<?php echo $activePanel === 'school' ? ' is-active' : ''; ?>" data-staffing-tab="school" role="tab" aria-controls="staffingPanelSchool" aria-selected="<?php echo $activePanel === 'school' ? 'true' : 'false'; ?>" tabindex="<?php echo $activePanel === 'school' ? '0' : '-1'; ?>">1. Σχολική μονάδα</button>
      <button type="button" id="staffingTabResults" class="mode-tab<?php echo $activePanel === 'results' ? ' is-active' : ''; ?>" data-staffing-tab="results" role="tab" aria-controls="staffingPanelResults" aria-selected="<?php echo $activePanel === 'results' ? 'true' : 'false'; ?>" tabindex="<?php echo $activePanel === 'results' ? '0' : '-1'; ?>"<?php echo !$calculationAvailable ? ' disabled' : ''; ?>>2. Αποτελέσματα ανά κλάδο</button>
      <button type="button" id="staffingTabPersonnel" class="mode-tab<?php echo $activePanel === 'personnel' ? ' is-active' : ''; ?>" data-staffing-tab="personnel" role="tab" aria-controls="staffingPanelPersonnel" aria-selected="<?php echo $activePanel === 'personnel' ? 'true' : 'false'; ?>" tabindex="<?php echo $activePanel === 'personnel' ? '0' : '-1'; ?>"<?php echo !$calculationAvailable ? ' disabled' : ''; ?>>3. Εκπαιδευτικοί</button>
      <button type="button" id="staffingTabAllocation" class="mode-tab<?php echo $activePanel === 'allocation' ? ' is-active' : ''; ?>" data-staffing-tab="allocation" role="tab" aria-controls="staffingPanelAllocation" aria-selected="<?php echo $activePanel === 'allocation' ? 'true' : 'false'; ?>" tabindex="<?php echo $activePanel === 'allocation' ? '0' : '-1'; ?>"<?php echo !$allocationEnabled ? ' disabled' : ''; ?>>4. Κατανομή μαθημάτων</button>
      <button type="button" id="staffingTabVacancies" class="mode-tab<?php echo $activePanel === 'vacancies' ? ' is-active' : ''; ?>" data-staffing-tab="vacancies" role="tab" aria-controls="staffingPanelVacancies" aria-selected="<?php echo $activePanel === 'vacancies' ? 'true' : 'false'; ?>" tabindex="<?php echo $activePanel === 'vacancies' ? '0' : '-1'; ?>"<?php echo !$vacanciesEnabled ? ' disabled' : ''; ?>>5. Κενά μαθημάτων</button>
      <button type="button" id="staffingTabSpecialties" class="mode-tab<?php echo $activePanel === 'specialties' ? ' is-active' : ''; ?>" data-staffing-tab="specialties" role="tab" aria-controls="staffingPanelSpecialties" aria-selected="<?php echo $activePanel === 'specialties' ? 'true' : 'false'; ?>" tabindex="<?php echo $activePanel === 'specialties' ? '0' : '-1'; ?>"<?php echo !$specialtyBalanceEnabled ? ' disabled' : ''; ?>>6. Κενά / πλεονάσματα ειδικοτήτων</button>
    </div>
    <div class="staffing-stage-actions">
      <details class="staffing-help-popover">
        <summary>ⓘ Βοήθεια</summary>
        <div class="staffing-help-panel">
          <h3>Πώς χρησιμοποιείται το εργαλείο</h3>
          <ol>
            <li><strong>Σχολική μονάδα:</strong> καταχώρισε τα πραγματικά τμήματα και τις ομάδες διδασκαλίας.</li>
            <li><strong>Αποτελέσματα ανά κλάδο:</strong> έλεγξε τις ώρες που προκύπτουν από ωρολόγιο και αναθέσεις.</li>
            <li><strong>Εκπαιδευτικοί:</strong> πρόσθεσε το πραγματικό προσωπικό και το διαθέσιμο ωράριό του.</li>
            <li><strong>Κατανομή μαθημάτων:</strong> χρησιμοποίησε την αυτόματη πρόταση ή κάνε χειροκίνητες αλλαγές.</li>
            <li><strong>Κενά μαθημάτων:</strong> δες ποιες διδακτικές ώρες παραμένουν ακάλυπτες.</li>
            <li><strong>Κενά / πλεονάσματα ειδικοτήτων:</strong> δες την προτεινόμενη τελική εικόνα ανά κλάδο.</li>
          </ol>
          <p><strong>Σημαντικό:</strong> Η αυτόματη λειτουργία δημιουργεί <strong>πρόταση</strong>, όχι διοικητική πράξη τοποθέτησης. Η εικόνα κενών είναι εργαλείο ελέγχου και όχι από μόνη της επίσημος προσδιορισμός λειτουργικών κενών.</p>
        </div>
      </details>
      <?php if ($submitted && $matrix && $displayMatrix): ?>
        <button type="button" class="edu-btn-secondary staffing-print-btn" id="staffingPrintButton">Εκτύπωση</button>
      <?php endif; ?>
    </div>
  </div>

  <?php
    $staffingContextSchool = $schoolName !== '' ? $schoolName : ($submitted ? staffingUiSchoolTypeLabel($schoolType, true) : 'Δεν έχει φορτωθεί σχολική μονάδα');
    $staffingContextState = ($submitted && $matrix) ? staffingUiReadinessLabel($matrix['readiness']) : 'Αναμονή υπολογισμού';
    $staffingContextAssignmentUnits = ($submitted && $matrix) ? (int)$matrix['summary']['assignment_unit_count'] : 0;
    $staffingContextEligibleBranches = ($submitted && $displayMatrix) ? (int)(isset($displayMatrix['summary']['presentation_staffing_leaf_codes_with_claims']) ? $displayMatrix['summary']['presentation_staffing_leaf_codes_with_claims'] : 0) : 0;
  ?>
  <div class="staffing-context-bar" id="staffingContextBar" aria-label="Τρέχουσα σχολική μονάδα και κατάσταση υπολογισμού">
    <span class="staffing-context-label">Τρέχον σχολείο</span>
    <strong class="staffing-context-school" id="staffingContextSchool"><?php echo staffingUiH($staffingContextSchool); ?></strong>
    <span class="staffing-context-chip" id="staffingContextType"><?php echo staffingUiH(staffingUiSchoolTypeLabel($schoolType)); ?></span>
    <span class="staffing-context-chip" id="staffingContextCode"<?php echo empty($schoolCode) ? ' hidden' : ''; ?>>κωδ. <strong><?php echo staffingUiH($schoolCode); ?></strong></span>
    <span class="staffing-context-chip" id="staffingContextSections"<?php echo $generalSectionTotal > 0 ? '' : ' hidden'; ?>><strong><?php echo (int)$generalSectionTotal; ?></strong> τμήματα</span>
    <span class="staffing-context-chip staffing-context-state" id="staffingContextState" role="status" aria-live="polite" aria-atomic="true"><?php echo staffingUiH($staffingContextState); ?></span>
    <?php if ($submitted && $matrix): ?>
      <span class="staffing-context-chip staffing-context-detail"><strong><?php echo $staffingContextAssignmentUnits; ?></strong> αντιστοιχίσεις</span>
      <span class="staffing-context-chip staffing-context-detail"><strong><?php echo $staffingContextEligibleBranches; ?></strong> κλάδοι</span>
    <?php endif; ?>
    <span class="staffing-context-chip staffing-context-detail" id="staffingContextAssigned"<?php echo $allocationPlan ? '' : ' hidden'; ?>><strong><?php echo $allocationPlan ? (int)$allocationPlan['summary']['assigned_slot_hours_total'] : 0; ?></strong> ώρες κατανεμημένες</span>
    <span class="staffing-context-chip staffing-context-detail<?php echo $allocationPlan && (int)$allocationPlan['summary']['unassigned_slot_hours'] > 0 ? ' has-warning' : ''; ?>" id="staffingContextUnassigned"<?php echo $allocationPlan ? '' : ' hidden'; ?>><strong><?php echo $allocationPlan ? (int)$allocationPlan['summary']['unassigned_slot_hours'] : 0; ?></strong> ακάλυπτες</span>
  </div>

  <?php calculatorColumnsStart(array('class'=>'layout staffing-single-column')); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('class'=>'card staffing-panel','attrs'=>array('id'=>'staffingPanelSchool','data-staffing-panel'=>'school','role'=>'tabpanel','aria-labelledby'=>'staffingTabSchool','tabindex'=>'0') + ($activePanel !== 'school' ? array('hidden'=>true) : array()))); ?>
        <h2>1. Στοιχεία σχολικής μονάδας</h2>
        <p class="cap">Η τρέχουσα έκδοση υποστηρίζει Ημερήσιο Γυμνάσιο, Εσπερινό Γυμνάσιο, Ημερήσιο ΓΕΛ, Εσπερινό ΓΕΛ και Γυμνάσιο με Λ.Τ.. Οι αριθμοί αφορούν πραγματικά τμήματα / ομάδες διδασκαλίας και όχι οργανικές θέσεις.</p>
        <div class="status-warn" id="schoolProfileStaleNotice" role="status" aria-live="polite" hidden><strong>Τα στοιχεία της σχολικής μονάδας άλλαξαν.</strong> Τα προηγούμενα αποτελέσματα, το προσωπικό, η κατανομή και τα κενά έχουν κλειδωθεί μέχρι να πατήσεις ξανά «Υπολόγισε διδακτικές ανάγκες».</div>
        <?php if (!empty($schoolProfileInputErrors)): ?>
          <div class="status-warn" role="alert"><strong>Ο υπολογισμός δεν εκτελέστηκε.</strong><ul><?php foreach ($schoolProfileInputErrors as $inputError): ?><li><?php echo staffingUiH($inputError); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="post" id="staffingProfileForm">
          <input type="hidden" name="staffing_action" value="">
          <input type="hidden" name="school_registry_id" id="school_registry_id" value="<?php echo staffingUiH(staffingUiPost('school_registry_id')); ?>">
          <div class="field-grid">
            <div class="field">
              <label for="school_type">Τύπος σχολείου</label>
              <select id="school_type" name="school_type">
                <option value="gymnasio"<?php echo $schoolType === 'gymnasio' ? ' selected' : ''; ?>>Ημερήσιο Γυμνάσιο</option>
                <option value="esperino_gymnasio"<?php echo $schoolType === 'esperino_gymnasio' ? ' selected' : ''; ?>>Εσπερινό Γυμνάσιο</option>
                <option value="gel"<?php echo $schoolType === 'gel' ? ' selected' : ''; ?>>Ημερήσιο Γενικό Λύκειο</option>
                <option value="esperino_gel"<?php echo $schoolType === 'esperino_gel' ? ' selected' : ''; ?>>Εσπερινό ΓΕΛ</option>
                <option value="gymnasio_lt"<?php echo $schoolType === 'gymnasio_lt' ? ' selected' : ''; ?>>Γυμνάσιο με Λ.Τ.</option>
                <optgroup label="Προσεχώς — προσωρινά ανενεργά">
                  <option value="epal" disabled>ΕΠΑΛ</option>
                  <option value="esperino_epal" disabled>Εσπερινό ΕΠΑΛ</option>
                  <option value="pepal" disabled>Πρότυπο ΕΠΑΛ</option>
                  <option value="eneegyl" disabled>ΕΝ.Ε.Ε.ΓΥ.-Λ.</option>
                  <option value="eeeek" disabled>Ε.Ε.Ε.ΕΚ.</option>
                  <option value="mousiko" disabled>Μουσικό Σχολείο</option>
                  <option value="kallitexniko" disabled>Καλλιτεχνικό Σχολείο</option>
                  <option value="protypo_ekklisiastiko_gymnasio" disabled>Πρότυπο Εκκλησιαστικό Γυμνάσιο</option>
                  <option value="protypo_ekklisiastiko_lykeio" disabled>Πρότυπο Εκκλησιαστικό Λύκειο</option>
                  <option value="sek" disabled>Εργαστηριακό Κέντρο</option>
                </optgroup>
              </select>
            </div>
            <div class="field">
              <label for="school_name">Ονομασία σχολείου <small>προαιρετικό</small></label>
              <input id="school_name" name="school_name" type="text" value="<?php echo staffingUiH(staffingUiPost('school_name')); ?>" placeholder="π.χ. 1ο Γυμνάσιο Κέρκυρας">
            </div>
            <div class="field">
              <label for="school_code">Κωδικός Υπουργείου / myschool <small>προαιρετικό</small></label>
              <input id="school_code" name="school_code" type="text" value="<?php echo staffingUiH(staffingUiPost('school_code')); ?>" placeholder="π.χ. 2401020" autocomplete="off">
              <small class="help">Σταθερός κωδικός σχολικής μονάδας. Όταν υπάρχει, είναι προτιμότερος από την ονομασία για αντιστοίχιση μεταξύ CSV / myschool / μελλοντικού ελέγχου ΔΔΕ.</small>
            </div>
          </div>

          <div class="school-registry-toolbar">
            <button class="edu-btn-secondary" type="button" id="openSchoolCsv">Εισαγωγή / μητρώο σχολείων CSV</button>
            <button class="edu-btn-secondary" type="button" id="downloadSchoolCsvTemplate">Λήψη προτύπου CSV</button>
            <button class="edu-btn-secondary" type="button" id="loadCorfuSchoolDirectory">Κατάλογος ΔΔΕ Κέρκυρας 2026-27</button>
            <input type="file" id="schoolCsvFile" accept=".csv,text/csv,text/plain" hidden>
          </div>
          <div class="info-note school-registry-note"><strong>Πολλαπλές σχολικές μονάδες στο ίδιο CSV.</strong> Το portable schema <code>school_registry_v1</code> κρατά μία γραμμή ανά σχολείο. Στην τρέχουσα έκδοση μπορείς να φορτώνεις μία μονάδα κάθε φορά στην Καρτέλα 1· το μητρώο του CSV παραμένει διαθέσιμο στον browser ώστε να αλλάζεις σχολείο χωρίς νέο αρχείο. Οι τύποι που εμφανίζονται ως «προσεχώς» αναγνωρίζονται από το μητρώο αλλά δεν φορτώνονται ακόμη στον υπολογισμό. Ο ενσωματωμένος κατάλογος ΔΔΕ Κέρκυρας 2026-2027 περιέχει πραγματικούς κωδικούς και ονομασίες, βασικά τμήματα, χωρισμούς Πληροφορικής–Τεχνολογίας, ομάδες προσανατολισμού και τις πραγματικές ομάδες 2ης ξένης γλώσσας από το myschool stat3_10 της 06-09-2026 για τις υποστηριζόμενες μονάδες. Όσα ειδικότερα πεδία δεν έχουν ακόμη τεκμηριωθεί παραμένουν κενά για συμπλήρωση.</div>
          <div class="personnel-csv-panel school-csv-panel" id="schoolCsvPanel" hidden>
            <div class="personnel-csv-head">
              <div>
                <strong>Μητρώο σχολικών μονάδων από CSV</strong>
                <div class="personnel-csv-meta" id="schoolCsvMeta">Επίλεξε αρχείο CSV. Η ανάγνωση γίνεται μόνο στον browser σου.</div>
              </div>
              <div class="school-csv-head-actions">
                <button type="button" class="edu-btn-secondary" id="chooseSchoolCsvFile">Επιλογή CSV</button>
                <button type="button" class="edu-btn-secondary" id="downloadCorfuSchoolDirectory">Λήψη πλήρους καταλόγου Κέρκυρας CSV</button>
                <button type="button" class="edu-btn-secondary" id="clearSchoolCsvRegistry">Καθαρισμός μητρώου</button>
                <button type="button" class="edu-btn-secondary" id="closeSchoolCsv">Κλείσιμο</button>
              </div>
            </div>
            <div class="info-note"><strong>Δεν γίνεται μεταφόρτωση στον διακομιστή.</strong> Υποστηρίζονται semicolon (;), κόμμα ή tab. Ελάχιστες στήλες: «Ονομασία σχολείου» και «Είδος σχολείου». Προαιρετικά μπορούν να υπάρχουν «Κωδικός Υπουργείου» και «Διεύθυνση σχολείου». Τα «Α τμήματα / Β τμήματα / Γ τμήματα» και τα ειδικότερα πεδία μπορούν να συμπληρώνονται στην ίδια γραμμή. Το άθροισμα των βασικών τμημάτων ανά σχολείο δεν μπορεί να υπερβαίνει τα 120.</div>
            <div class="field school-registry-search"><label for="schoolRegistrySearch">Αναζήτηση στο μητρώο</label><input type="search" id="schoolRegistrySearch" placeholder="π.χ. 2401020, 2ο Γυμνάσιο, Λευκίμμη"></div>
            <div class="personnel-csv-preview" id="schoolCsvPreview"><div class="empty-personnel">Δεν έχει επιλεγεί ακόμη CSV.</div></div>
            <div class="personnel-csv-status" id="schoolCsvStatus" role="status" aria-live="polite" aria-atomic="true"></div>
          </div>
          <div class="info-note school-csv-active" id="schoolCsvActive" role="status" aria-live="polite" aria-atomic="true" hidden></div>

          <div id="gymProfileFields"<?php echo in_array($schoolType, array('gymnasio','esperino_gymnasio','gymnasio_lt'), true) ? '' : ' hidden'; ?>>
            <section class="staffing-section">
              <h3><?php echo $schoolType === 'gymnasio_lt' ? 'Γυμνάσιο — κανονικά τμήματα ανά τάξη' : 'Κανονικά τμήματα ανά τάξη'; ?></h3>
              <p class="help">Τεχνικό όριο ασφαλείας: έως <strong>120 βασικά τμήματα συνολικά</strong><?php echo $schoolType === 'gymnasio_lt' ? ' στο άθροισμα Γυμνασίου + Λυκειακών Τάξεων' : ' (Α΄ + Β΄ + Γ΄)'; ?>, μέγεθος που αντιστοιχεί περίπου σε 3.500 μαθητές και είναι πολύ πάνω από μια πραγματική σχολική μονάδα.</p>
              <?php staffingUiRenderBasicSectionFields('gym'); ?>
              <small class="profile-validation-error" id="gymBasicSectionsError" data-basic-sections-error="gym" hidden></small>
            </section>
            <section class="staffing-section" id="gymLanguageGroupsSection" data-day-gym-only<?php echo $schoolType === 'esperino_gymnasio' ? ' hidden' : ''; ?>>
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

            <details class="option-panel" id="technologyInformaticsPanel"<?php echo in_array($schoolType, array('gymnasio','gymnasio_lt'), true) && (staffingUiInt('gym_tech_split_a') + staffingUiInt('gym_tech_split_b') + staffingUiInt('gym_tech_split_c')) > 0 ? ' open' : ''; ?> data-day-gym-only<?php echo $schoolType === 'esperino_gymnasio' ? ' hidden' : ''; ?>>
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

          <div id="gelProfileFields"<?php echo in_array($schoolType, array('gel','esperino_gel','gymnasio_lt'), true) ? '' : ' hidden'; ?>>
            <section class="staffing-section">
              <h3><?php echo $schoolType === 'gymnasio_lt' ? 'Λυκειακές Τάξεις — κανονικά τμήματα ανά τάξη' : 'Κανονικά τμήματα ανά τάξη'; ?></h3>
              <p class="help">Τεχνικό όριο ασφαλείας: έως <strong>120 βασικά τμήματα συνολικά</strong><?php echo $schoolType === 'gymnasio_lt' ? ' στο άθροισμα Γυμνασίου + Λυκειακών Τάξεων' : ' (Α΄ + Β΄ + Γ΄)'; ?>, μέγεθος που αντιστοιχεί περίπου σε 3.500 μαθητές και είναι πολύ πάνω από μια πραγματική σχολική μονάδα.</p>
              <?php staffingUiRenderBasicSectionFields('gel'); ?>
              <small class="profile-validation-error" id="gelBasicSectionsError" data-basic-sections-error="gel" hidden></small>
            </section>
            <section class="staffing-section" id="gelLanguageGroupsSection" data-day-gel-only<?php echo $schoolType === 'esperino_gel' ? ' hidden' : ''; ?>>
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
            <section class="staffing-section" id="eveningGelPeriodSection" data-evening-gel-only<?php echo $schoolType === 'esperino_gel' ? '' : ' hidden'; ?>>
              <h3>Β΄ Εσπερινού ΓΕΛ — τετράμηνο υπολογισμού</h3>
              <p class="help">Στη Β΄ τάξη η Χημεία διδάσκεται <strong>1 / 2</strong> ώρες και η Βιολογία <strong>2 / 1</strong> ώρες στα Α΄ / Β΄ τετράμηνα αντίστοιχα. Επίλεξε το τετράμηνο για το οποίο θέλεις να αποτυπωθούν τα κενά / πλεονάσματα και η αυτόματη κατανομή.</p>
              <div class="field">
                <label for="egel_b_period">Τετράμηνο</label>
                <?php $egelPeriod = staffingUiPost('egel_b_period', 'Α΄ τετράμηνο'); ?>
                <select id="egel_b_period" name="egel_b_period">
                  <option value="Α΄ τετράμηνο"<?php echo $egelPeriod === 'Α΄ τετράμηνο' ? ' selected' : ''; ?>>Α΄ τετράμηνο</option>
                  <option value="Β΄ τετράμηνο"<?php echo $egelPeriod === 'Β΄ τετράμηνο' ? ' selected' : ''; ?>>Β΄ τετράμηνο</option>
                </select>
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
            <?php if ($schoolType === 'gymnasio_lt'): ?>
              <?php staffingUiRenderEthicsPanel('ethics_', 'Ηθική / Θρησκευτικά — Γυμνάσιο', 'ethicsPanelGym'); ?>
              <?php staffingUiRenderEthicsPanel('lt_ethics_', 'Ηθική / Θρησκευτικά — Λυκειακές Τάξεις', 'ethicsPanelLt'); ?>
            <?php else: ?>
              <?php staffingUiRenderEthicsPanel('ethics_', 'Ηθική / Θρησκευτικά', 'ethicsPanel'); ?>
            <?php endif; ?>
          </section>

          <div class="actions">
            <button class="edu-btn-primary" type="submit" name="staffing_action_fallback" value="profile" data-staffing-request-action="profile">Υπολόγισε διδακτικές ανάγκες</button><input type="hidden" name="active_panel" value="results">
            <button class="edu-btn-secondary" type="reset" id="staffingReset">Καθαρισμός</button>
          </div>
        </form>
      <?php calculatorCardEnd(); ?>

      <?php if ($submitted && $matrix && $displayMatrix): ?>
        <?php calculatorCardStart(array('class'=>'card staffing-results-card staffing-panel','attrs'=>array('id'=>'staffingPanelResults','data-staffing-panel'=>'results','role'=>'tabpanel','aria-labelledby'=>'staffingTabResults','tabindex'=>'0') + ($activePanel !== 'results' ? array('hidden'=>true) : array()))); ?>
          <h2>2. Αποτελέσματα ανά κλάδο</h2>
          <p class="cap">Τα αθροίσματα είναι ώρες του ωρολογίου προγράμματος για τις οποίες ο κάθε κλάδος είναι επιλέξιμος στη συγκεκριμένη σχολική μονάδα. Δεν αποτελούν ακόμη επίσημα λειτουργικά κενά ούτε τελική κατανομή σε εκπαιδευτικούς.</p>

          <?php if ($readiness && $readiness['ready']): ?>
            <div class="status-good"><strong>Τα στοιχεία της σχολικής μονάδας είναι δομικά πλήρη.</strong> Τυχόν ειδικές εκκρεμότητες εμφανίζονται μόνο όταν υπάρχουν.</div>
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
          </div>

          <?php if (!empty($staffingNotices['dependencies'])): ?>
            <div class="status-warn">
              <strong>Χρειάζονται επιπλέον στοιχεία πριν οριστικοποιηθούν όλες οι ώρες.</strong>
              <ul>
                <?php foreach ($staffingNotices['dependencies'] as $notice): ?><li><?php echo staffingUiH($notice); ?></li><?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (!empty($staffingNotices['regulatory'])): ?>
            <div class="status-warn">
              <strong>Υπάρχει κανονιστική εκκρεμότητα που επηρεάζει τον υπολογισμό.</strong>
              <ul>
                <?php foreach ($staffingNotices['regulatory'] as $notice): ?><li><?php echo staffingUiH($notice); ?></li><?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <div class="field result-filter">
            <label for="staffingResultFilter">Φίλτρο κλάδου / μαθήματος</label>
            <input id="staffingResultFilter" type="search" placeholder="π.χ. ΠΕ03 ή Μαθηματικά">
          </div>

          <div class="matrix-wrap">
            <table class="staffing-table" id="staffingMatrixTable">
              <caption class="edu-tools-sr-only">Διδακτικές ώρες ανά κλάδο και προτεραιότητα ανάθεσης</caption>
              <thead><tr><th scope="col">Κλάδος</th><th scope="col">Α΄</th><th scope="col">Β΄</th><th scope="col">Γ΄</th></tr></thead>
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
                  <td colspan="3"><strong><?php echo (int)$collapsedSkills['hours']; ?> ώρες συνολικά</strong></td>
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
                              <?php echo staffingUiH(staffingUiPriorityLabel($claim['priority'])); ?> ανάθεση
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
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <p class="help"><strong>Προσοχή:</strong> οι ώρες Α΄/Β΄/Γ΄ δείχνουν επιλεξιμότητα βάσει της αντίστοιχης ανάθεσης και μπορούν να επικαλύπτονται μεταξύ κλάδων. Η πραγματική κατανομή γίνεται στην επόμενη καρτέλα.<?php if (!empty($collapsedSkills['active'])): ?> Τα <strong>Εργαστήρια Δεξιοτήτων</strong> εξαιρούνται από τα επιμέρους αθροίσματα κλάδων της οθόνης και εμφανίζονται μία φορά ως «Οποιαδήποτε ειδικότητα», ώστε να αποφεύγεται η τεχνητή επανάληψη δεκάδων αναθέσεων.<?php endif; ?></p>
          <?php if (in_array($schoolType, array('protypo_ekklisiastiko_gymnasio','protypo_ekklisiastiko_lykeio'), true)): ?>
            <div class="info-note"><strong>Πρότυπα Εκκλησιαστικά Σχολεία:</strong> η <strong>Εικονογραφία</strong> ακολουθεί ειδική πρόβλεψη ανάθεσης και απαιτεί έλεγχο των προβλεπόμενων πρόσθετων προϋποθέσεων. Για τον λόγο αυτό δεν αποτυπώνεται ως συνηθισμένη Α΄/Β΄/Γ΄ ανάθεση στον παραπάνω πίνακα.</div>
          <?php endif; ?>
        <?php calculatorCardEnd(); ?>
      <?php endif; ?>

      <?php if ($submitted && $matrix): ?>
        <?php calculatorCardStart(array('class'=>'card staffing-panel personnel-card','attrs'=>array('id'=>'staffingPanelPersonnel','data-staffing-panel'=>'personnel','role'=>'tabpanel','aria-labelledby'=>'staffingTabPersonnel','tabindex'=>'0') + ($activePanel !== 'personnel' ? array('hidden'=>true) : array()))); ?>
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
                </div>
              <?php endforeach; ?>
            </div>
            <p class="help">Η σύνοψη δείχνει το διαθέσιμο διδακτικό ωράριο του προσωπικού ανά κλάδο. Η αντιστοίχιση με συγκεκριμένα μαθήματα γίνεται στην επόμενη καρτέλα.</p>
          <?php endif; ?>

          <form method="post" id="staffingPersonnelForm">
            <?php staffingUiRenderSchoolStateHiddenInputs(); ?>
            <?php staffingUiRenderAllocationStateHiddenInputs($allocationRows); ?>
            <input type="hidden" name="personnel_payload_json" value="">
            <input type="hidden" name="staffing_action" value="">
            <input type="hidden" name="active_panel" value="personnel">

            <div class="personnel-toolbar">
              <div class="field">
                <label for="personnelFilter">Φίλτρο προσωπικού</label>
                <input id="personnelFilter" type="search" placeholder="π.χ. ΠΕ03 ή επώνυμο">
              </div>
              <div class="personnel-toolbar-actions">
                <button class="edu-btn-secondary" type="button" id="addPersonnelRow">+ Προσθήκη εκπαιδευτικού</button>
                <button class="edu-btn-secondary" type="button" id="openMySchoolStaff">myschool stat4_8</button>
                <button class="edu-btn-secondary" type="button" id="openPersonnelCsv">Εισαγωγή CSV</button>
                <button class="edu-btn-secondary" type="button" id="exportPersonnelRegistryCsv">Εξαγωγή μητρώου CSV</button>
                <button class="edu-btn-secondary" type="button" id="downloadPersonnelCsvTemplate">Λήψη προτύπου CSV</button>
              </div>
            </div>

            <input id="mySchoolStaffFile" type="file" accept=".zip,.csv,application/zip,text/csv,text/plain" hidden>
            <div class="personnel-csv-panel" id="mySchoolStaffPanel" hidden>
              <div class="personnel-csv-head">
                <div>
                  <strong>Μητρώο προσωπικού myschool · stat4_8</strong>
                  <div class="personnel-csv-meta" id="mySchoolStaffMeta">Φόρτωσε το αυθεντικό ZIP ή CSV του stat4_8. Το αρχείο επεξεργάζεται μόνο τοπικά στον browser.</div>
                </div>
                <button class="personnel-remove" type="button" id="closeMySchoolStaff">Κλείσιμο</button>
              </div>
              <div class="info-note"><strong>Δεν χρειάζεται προσαρμογή του export.</strong> Ο importer κρατά μόνο κωδικό σχολείου, ονοματεπώνυμο, κύρια/2η ειδικότητα, ρόλο και τα απολύτως απαραίτητα στοιχεία ωραρίου. Α.Μ., Α.Φ.Μ., τηλέφωνα, email, διευθύνσεις και πράξεις τοποθέτησης δεν αποθηκεύονται στο μητρώο της εφαρμογής. Το καθαρισμένο μητρώο παραμένει μόνο στο <code>sessionStorage</code>.</div>
              <div class="personnel-csv-actions">
                <button class="edu-btn-secondary" type="button" id="pickMySchoolStaffFile">Επιλογή stat4_8 ZIP / CSV</button>
                <button class="edu-btn-primary" type="button" id="loadMySchoolStaffForSchool" disabled>Φόρτωση προσωπικού τρέχοντος σχολείου</button>
                <button class="edu-btn-secondary" type="button" id="downloadCleanMySchoolStaff" disabled>Λήψη καθαρισμένου CSV</button>
                <button class="edu-btn-secondary" type="button" id="clearMySchoolStaff" disabled>Καθαρισμός μητρώου</button>
              </div>
              <div class="personnel-csv-status" id="mySchoolStaffStatus" role="status" aria-live="polite" aria-atomic="true"></div>
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
              <div class="personnel-csv-status" id="personnelCsvStatus" role="status" aria-live="polite" aria-atomic="true"></div>
            </div>

            <div class="info-note"><strong>Υ.Ω. = Υποχρεωτικό ωράριο.</strong> Το Υ.Ω. του απλού εκπαιδευτικού καταχωρίζεται απευθείας. Αν δεν το γνωρίζεις, χρησιμοποίησε τον <a href="ypologismos-didaktikou-orariou.php">Υπολογισμό υποχρεωτικού διδακτικού ωραρίου</a> και επέστρεψε εδώ με το αποτέλεσμα. Για χειροκίνητη καταχώριση Διευθυντή/Υποδιευθυντή το ωράριο υπολογίζεται από τον ρόλο και την υπηρεσία. Σε εισαγωγή myschool stat4_8 χρησιμοποιείται το πραγματικό ωράριο της πηγής μετά τη δηλωμένη μείωση και σημειώνεται ρητά η προέλευσή του.</div>

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
                  $obligationSource = isset($person['obligation_source']) ? trim((string)$person['obligation_source']) : '';
                  $isMySchoolSource = $obligationSource === 'myschool_stat4_8';
                  $requiredInputHours = ($person['role'] === 'teacher' || $isMySchoolSource) ? $manualRequiredHours : ($requiredHours === null ? '' : (string)$requiredHours);
                  $manualRequiredHoursMax = (($person['role'] === 'teacher' || $isMySchoolSource) && strpos(teacherSpecialtyCanonicalCode($person['specialty_code']), 'ΠΕ') === 0) ? 23 : 35;
                  $availableHours = $resolved ? (int)$eval['remaining_before_profile_hours'] : null;
                  $externalHours = isset($person['assigned_external_hours']) ? (int)$person['assigned_external_hours'] : 0;
                  $selectedCode = isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '';
                  $selectedSecondaryCode = isset($person['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($person['secondary_specialty_code']) : '';
                ?>
                <div class="personnel-row" data-personnel-row data-search="<?php echo staffingUiH(trim($selectedCode . ($selectedSecondaryCode !== '' ? ' ' . $selectedSecondaryCode : '') . ' ' . $person['display_name'])); ?>">
                  <input type="hidden" name="personnel_person_id[]" value="<?php echo staffingUiH($person['person_id']); ?>">
                  <input type="hidden" name="personnel_obligation_source[]" class="personnel-obligation-source" value="<?php echo staffingUiH($obligationSource); ?>">
                  <input type="hidden" name="personnel_source_base_required_hours[]" class="personnel-source-base-required" value="<?php echo staffingUiH(isset($person['source_base_required_hours']) ? $person['source_base_required_hours'] : ''); ?>">
                  <input type="hidden" name="personnel_source_reduction_hours[]" class="personnel-source-reduction" value="<?php echo staffingUiH(isset($person['source_reduction_hours']) ? $person['source_reduction_hours'] : ''); ?>">
                  <input type="hidden" name="personnel_source_hours_at_unit[]" class="personnel-source-at-unit" value="<?php echo staffingUiH(isset($person['source_hours_at_unit']) ? $person['source_hours_at_unit'] : ''); ?>">
                  <div class="personnel-row-main">
                    <div class="field">
                      <label>Κλάδος</label>
                      <select name="personnel_specialty_code[]" class="personnel-specialty" aria-label="Κλάδος εκπαιδευτικού"><?php staffingUiRenderPersonnelSpecialtyOptions($personnelSpecialtyOptions, $selectedCode); ?></select>
                    </div>
                    <div class="field">
                      <label>Ονοματεπώνυμο</label>
                      <input type="text" name="personnel_display_name[]" class="personnel-name" aria-label="Ονοματεπώνυμο εκπαιδευτικού" value="<?php echo staffingUiH($person['display_name']); ?>" placeholder="π.χ. Μαρία Παπαδοπούλου">
                    </div>
                    <div class="field">
                      <label title="Υποχρεωτικό ωράριο">Υ.Ω.</label>
                      <input type="number" min="1" max="<?php echo (int)$manualRequiredHoursMax; ?>" step="1" name="personnel_required_teaching_hours[]" class="personnel-required" aria-label="Υποχρεωτικό ωράριο εκπαιδευτικού" data-required-hours data-manual-value="<?php echo staffingUiH($manualRequiredHours); ?>" value="<?php echo staffingUiH($requiredInputHours); ?>"<?php echo $isMySchoolSource || $person['role'] !== 'teacher' ? ' readonly' : ' required'; ?>>
                    </div>
                    <div class="field">
                      <label>Ώρες αλλού</label>
                      <input type="number" min="0" max="35" step="1" name="personnel_assigned_external_hours[]" class="personnel-external" aria-label="Ώρες εκπαιδευτικού σε άλλη μονάδα" value="<?php echo $externalHours; ?>"<?php echo $isMySchoolSource ? ' readonly' : ''; ?>>
                    </div>
                    <div class="metric"><strong data-available-hours><?php echo $availableHours === null ? '—' : $availableHours; ?></strong><span>διαθέσιμο εδώ</span></div>
                    <button type="button" class="personnel-remove personnel-remove-compact" title="Αφαίρεση εκπαιδευτικού" aria-label="Αφαίρεση εκπαιδευτικού"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false" style="display:block" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg></button>
                  </div>
                  <details<?php echo !$isMySchoolSource && ($person['role'] !== 'teacher' || !$resolved || $selectedSecondaryCode !== '') ? ' open' : ''; ?>>
                    <summary>2η ειδικότητα, ρόλος και στοιχεία διοίκησης<?php if ($selectedSecondaryCode !== ''): ?> · 2η <?php echo staffingUiH($selectedSecondaryCode); ?><?php endif; ?><?php if ($isMySchoolSource): ?> · myschool<?php elseif ($person['role'] !== 'teacher' && $resolved && !empty($eval['obligation']['service_label'])): ?> · <?php echo staffingUiH($eval['obligation']['service_label']); ?><?php endif; ?></summary>
                    <div class="personnel-row-details">
                      <div class="mini-grid two personnel-secondary-role-grid">
                        <div class="field">
                          <label>2η ειδικότητα <small>προαιρετική</small></label>
                          <select name="personnel_secondary_specialty_code[]" class="personnel-secondary-specialty" aria-label="Δεύτερη ειδικότητα εκπαιδευτικού"><?php staffingUiRenderPersonnelSpecialtyOptions($personnelSpecialtyOptions, $selectedSecondaryCode); ?></select>
                        </div>
                        <div class="field">
                          <label>Ρόλος</label>
                          <select name="personnel_role[]" class="personnel-role" aria-label="Ρόλος εκπαιδευτικού">
                            <?php foreach (array('teacher','director','vice_or_sector') as $role): ?><option value="<?php echo $role; ?>"<?php echo $person['role'] === $role ? ' selected' : ''; ?>><?php echo staffingUiH(staffingUiPersonnelRoleLabel($role)); ?></option><?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="mini-grid personnel-service-fields"<?php echo $person['role'] === 'teacher' || $isMySchoolSource ? ' hidden' : ''; ?>>
                        <div class="field"><label>Έτη υπηρεσίας</label><input type="number" min="0" max="50" step="1" name="personnel_service_years[]" class="personnel-years" aria-label="Έτη υπηρεσίας εκπαιδευτικού" value="<?php echo staffingUiH($person['service']['years']); ?>"></div>
                        <div class="field"><label>Μήνες</label><input type="number" min="0" max="11" step="1" name="personnel_service_months[]" class="personnel-months" aria-label="Μήνες υπηρεσίας εκπαιδευτικού" value="<?php echo staffingUiH($person['service']['months']); ?>"></div>
                        <div class="field"><label>Ημέρες</label><input type="number" min="0" max="29" step="1" name="personnel_service_days[]" class="personnel-days" aria-label="Ημέρες υπηρεσίας εκπαιδευτικού" value="<?php echo staffingUiH($person['service']['days']); ?>"></div>
                      </div>
                      <div class="mini-grid personnel-director-row">
                        <div class="field personnel-director-band"<?php echo $person['role'] === 'director' && !$isMySchoolSource ? '' : ' hidden'; ?>>
                          <label>Τμήματα σχολικής μονάδας <small>αυτόματα</small></label>
                          <div class="summary-chip personnel-director-section-info"><strong data-director-section-count><?php echo (int)$generalSectionTotal; ?></strong><span data-director-section-band><?php echo $directorSectionsBandAuto ? 'κλίμακα ' . staffingUiH($directorSectionsBandAuto) : 'χρειάζονται τα κανονικά τμήματα'; ?></span></div>
                        </div>
                      </div>
                      <?php if ($resolved && !empty($eval['obligation']['rule'])): ?><p class="help" data-personnel-rule><?php echo staffingUiH($eval['obligation']['rule']); ?></p><?php else: ?><p class="help" data-personnel-rule></p><?php endif; ?>
                    </div>
                  </details>
                  <?php if (!$resolved): ?><div class="personnel-status-error" data-personnel-error role="status" aria-live="polite"><?php echo staffingUiH(staffingUiPersonnelReasonLabel(isset($eval['reason']) ? $eval['reason'] : 'Χρειάζεται συμπλήρωση στοιχείων.')); ?></div><?php elseif (!empty($eval['external_overage_hours'])): ?><div class="personnel-status-error" data-personnel-error role="status" aria-live="polite">Οι ώρες σε άλλη μονάδα υπερβαίνουν το υποχρεωτικό ωράριο κατά <?php echo (int)$eval['external_overage_hours']; ?> ώρες.</div><?php else: ?><div class="personnel-status-error" data-personnel-error role="status" aria-live="polite" hidden></div><?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="actions">
              <button class="edu-btn-primary" type="submit" name="staffing_action_fallback" value="personnel" data-staffing-request-action="personnel">Έλεγχος ωραρίων προσωπικού</button>
            </div>
          </form>

          <template id="personnelRowTemplate">
            <div class="personnel-row" data-personnel-row data-search="">
              <input type="hidden" name="personnel_person_id[]" value="">
              <input type="hidden" name="personnel_obligation_source[]" class="personnel-obligation-source" value="">
              <input type="hidden" name="personnel_source_base_required_hours[]" class="personnel-source-base-required" value="">
              <input type="hidden" name="personnel_source_reduction_hours[]" class="personnel-source-reduction" value="">
              <input type="hidden" name="personnel_source_hours_at_unit[]" class="personnel-source-at-unit" value="">
              <div class="personnel-row-main">
                <div class="field"><label>Κλάδος</label><select name="personnel_specialty_code[]" class="personnel-specialty" aria-label="Κλάδος εκπαιδευτικού"><?php staffingUiRenderPersonnelSpecialtyOptions($personnelSpecialtyOptions, ''); ?></select></div>
                <div class="field"><label>Ονοματεπώνυμο</label><input type="text" name="personnel_display_name[]" class="personnel-name" aria-label="Ονοματεπώνυμο εκπαιδευτικού" placeholder="π.χ. Μαρία Παπαδοπούλου"></div>
                <div class="field"><label title="Υποχρεωτικό ωράριο">Υ.Ω.</label><input type="number" min="1" max="35" step="1" name="personnel_required_teaching_hours[]" class="personnel-required" aria-label="Υποχρεωτικό ωράριο εκπαιδευτικού" data-required-hours data-manual-value="" value="" required></div>
                <div class="field"><label>Ώρες αλλού</label><input type="number" min="0" max="35" step="1" name="personnel_assigned_external_hours[]" class="personnel-external" aria-label="Ώρες εκπαιδευτικού σε άλλη μονάδα" value="0"></div>
                <div class="metric"><strong data-available-hours>—</strong><span>διαθέσιμο εδώ</span></div>
                <button type="button" class="personnel-remove personnel-remove-compact" title="Αφαίρεση εκπαιδευτικού" aria-label="Αφαίρεση εκπαιδευτικού"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false" style="display:block" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg></button>
              </div>
              <details>
                <summary>2η ειδικότητα, ρόλος και στοιχεία διοίκησης</summary>
                <div class="personnel-row-details">
                  <div class="mini-grid two personnel-secondary-role-grid">
                    <div class="field"><label>2η ειδικότητα <small>προαιρετική</small></label><select name="personnel_secondary_specialty_code[]" class="personnel-secondary-specialty" aria-label="Δεύτερη ειδικότητα εκπαιδευτικού"><?php staffingUiRenderPersonnelSpecialtyOptions($personnelSpecialtyOptions, ''); ?></select></div>
                    <div class="field"><label>Ρόλος</label><select name="personnel_role[]" class="personnel-role" aria-label="Ρόλος εκπαιδευτικού"><option value="teacher">Εκπαιδευτικός</option><option value="director">Διευθυντής/ντρια</option><option value="vice_or_sector">Υποδιευθυντής/ντρια</option></select></div>
                  </div>
                  <div class="mini-grid personnel-service-fields" hidden>
                    <div class="field"><label>Έτη υπηρεσίας</label><input type="number" min="0" max="50" step="1" name="personnel_service_years[]" class="personnel-years" aria-label="Έτη υπηρεσίας εκπαιδευτικού" value="0"></div>
                    <div class="field"><label>Μήνες</label><input type="number" min="0" max="11" step="1" name="personnel_service_months[]" class="personnel-months" aria-label="Μήνες υπηρεσίας εκπαιδευτικού" value="0"></div>
                    <div class="field"><label>Ημέρες</label><input type="number" min="0" max="29" step="1" name="personnel_service_days[]" class="personnel-days" aria-label="Ημέρες υπηρεσίας εκπαιδευτικού" value="0"></div>
                  </div>
                  <div class="mini-grid personnel-director-row">
                    <div class="field personnel-director-band" hidden><label>Τμήματα σχολικής μονάδας <small>αυτόματα</small></label><div class="summary-chip personnel-director-section-info"><strong data-director-section-count>—</strong><span data-director-section-band>από τα κανονικά τμήματα</span></div></div>
                  </div>
                  <p class="help" data-personnel-rule></p>
                </div>
              </details>
              <div class="personnel-status-error" data-personnel-error role="status" aria-live="polite" hidden></div>
            </div>
          </template>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(array('class'=>'card staffing-panel allocation-card','attrs'=>array('id'=>'staffingPanelAllocation','data-staffing-panel'=>'allocation','role'=>'tabpanel','aria-labelledby'=>'staffingTabAllocation','tabindex'=>'0') + ($activePanel !== 'allocation' ? array('hidden'=>true) : array()))); ?>
          <h2>4. Κατανομή μαθημάτων</h2>
          <p class="cap">Δημιούργησε αυτόματη πρόταση ή άλλαξε χειροκίνητα την κατανομή των πραγματικών μαθημάτων / ομάδων στους διαθέσιμους εκπαιδευτικούς. Το εργαλείο ελέγχει την ισχύουσα ανάθεση, το ατομικό υπόλοιπο ωραρίου και τη χωρητικότητα κάθε συγκεκριμένου τμήματος / ομάδας.</p>
          <div class="info-note"><strong>Πρόταση κάλυψης — πάντα επεξεργάσιμη.</strong> Ο αυτόματος μηχανισμός επιδιώκει πρώτα τη μέγιστη δυνατή κάλυψη των ωρών σε ολόκληρη τη σχολική μονάδα και έπειτα προτιμά Α΄/ειδική ανάθεση πριν από Β΄ και Β΄ πριν από Γ΄. Οι γραμμές που έχει ήδη ορίσει ο χρήστης διατηρούνται και η πρόταση συμπληρώνει μόνο το υπόλοιπο. Ο Διευθυντής μπορεί στη συνέχεια να αλλάξει οποιοδήποτε όνομα ή μάθημα και να ξαναελέγξει αμέσως κενά / πλεονάσματα.</div>

          <?php if (!$allocationEnabled): ?>
            <div class="allocation-empty">Για να ενεργοποιηθεί η κατανομή χρειάζεται τουλάχιστον ένας εκπαιδευτικός με πλήρως υπολογισμένο ωράριο στο tab «Εκπαιδευτικοί».</div>
          <?php else: ?>
            <?php
              $allocationTotalHours = (int)$matrix['summary']['assignment_unit_hours'];
              $allocationAssignedHours = $allocationPlan ? (int)$allocationPlan['summary']['assigned_slot_hours_total'] : 0;
              $allocationUnassignedHours = $allocationPlan ? (int)$allocationPlan['summary']['unassigned_slot_hours'] : $allocationTotalHours;
              $allocationOverHours = $allocationPlan ? (int)$allocationPlan['summary']['overallocated_slot_hours'] : 0;
              $allocationInvalidRows = $allocationPlan ? (int)$allocationPlan['summary']['invalid_allocation_row_count'] : 0;
              $allocationCoverage = $allocationTotalHours > 0 ? (100 * $allocationAssignedHours / $allocationTotalHours) : 0;
            ?>
            <?php if ($allocationAutoProposal): ?>
              <?php if (isset($allocationAutoProposal['status']) && $allocationAutoProposal['status'] === 'ok'): ?>
                <div class="info-note allocation-auto-result is-success"><strong>Η αυτόματη πρόταση δημιουργήθηκε.</strong> Προστέθηκαν <?php echo (int)$allocationAutoProposal['summary']['auto_covered_hours']; ?> ώρες πάνω στις ήδη ορισμένες <?php echo (int)$allocationAutoProposal['summary']['locked_hours']; ?> ώρες. Απομένουν <?php echo (int)$allocationAutoProposal['summary']['final_uncovered_hours']; ?> ακάλυπτες ώρες.</div>
                <?php if (empty($allocationAutoProposal['optimizer_state']['summary']['maximum_coverage_certified'])): ?><div class="info-note allocation-auto-result is-warning"><strong>Χρειάζεται τελικός έλεγχος.</strong> Το component ήταν πολύ σύνθετο για πλήρη πιστοποίηση της μέγιστης κάλυψης εντός του ορίου ασφαλείας· εμφανίζεται η καλύτερη έγκυρη atomic πρόταση που βρέθηκε.</div><?php endif; ?>
              <?php else: ?>
                <div class="info-note allocation-auto-result is-warning"><strong>Δεν δημιουργήθηκε αυτόματη πρόταση.</strong> <?php echo staffingUiH(isset($allocationAutoProposal['message']) ? $allocationAutoProposal['message'] : 'Χρειάζεται πρώτα διόρθωση της τρέχουσας κατανομής.'); ?></div>
              <?php endif; ?>
            <?php endif; ?>
            <div class="staffing-summary-grid" id="allocationGlobalSummary">
              <div class="summary-chip"><strong data-allocation-total><?php echo $allocationTotalHours; ?></strong><span>ώρες μαθημάτων προς κατανομή</span></div>
              <div class="summary-chip"><strong data-allocation-assigned><?php echo $allocationAssignedHours; ?></strong><span>ώρες που έχουν κατανεμηθεί έγκυρα</span></div>
              <div class="summary-chip"><strong data-allocation-coverage><?php echo number_format($allocationCoverage, 1, ',', ''); ?>%</strong><span>κάλυψη διδακτικών ωρών</span></div>
              <div class="summary-chip"><strong data-allocation-unassigned><?php echo $allocationUnassignedHours; ?></strong><span>ώρες που απομένουν χωρίς κατανομή</span></div>
              <div class="summary-chip"><strong data-allocation-over><?php echo $allocationOverHours; ?></strong><span>ώρες υπέρβασης τμήματος / ομάδας</span></div>
              <div class="summary-chip"><strong data-allocation-errors><?php echo $allocationInvalidRows; ?></strong><span>γραμμές που χρειάζονται διόρθωση</span></div>
            </div>
            <div class="edu-tools-sr-only" id="allocationLiveStatus" role="status" aria-live="polite" aria-atomic="true"></div>

            <div class="allocation-subtabs" role="tablist" aria-label="Προβολή κατανομής μαθημάτων">
              <button type="button" id="allocationViewTabSlots" class="allocation-subtab is-active" data-allocation-view="slots" role="tab" aria-controls="allocationViewPanelSlots" aria-selected="true" tabindex="0">Ανά μάθημα / τμήμα</button>
              <button type="button" id="allocationViewTabPeople" class="allocation-subtab" data-allocation-view="people" role="tab" aria-controls="allocationViewPanelPeople" aria-selected="false" tabindex="-1">Ανά εκπαιδευτικό</button>
            </div>

            <div id="allocationViewPanelPeople" data-allocation-view-panel="people" role="tabpanel" aria-labelledby="allocationViewTabPeople" tabindex="0" hidden>
              <h3>Κατάσταση και αναθέσεις ανά εκπαιδευτικό</h3>
              <div class="allocation-person-summary" id="allocationPersonSummary">
                <?php foreach ($allocationPeopleClient as $personId=>$personData): ?>
                  <?php
                    $pResult = $allocationPlan && isset($allocationPlan['people'][$personId]) ? $allocationPlan['people'][$personId] : null;
                    $assignedHere = $pResult ? (int)$pResult['assigned_profile_hours'] : 0;
                    $remainingHere = $pResult ? (int)$pResult['remaining_hours'] : (int)$personData['available_here_hours'];
                    $aHours = $pResult && isset($pResult['assigned_hours_by_priority']['A']) ? (int)$pResult['assigned_hours_by_priority']['A'] : 0;
                    $bHours = $pResult && isset($pResult['assigned_hours_by_priority']['B']) ? (int)$pResult['assigned_hours_by_priority']['B'] : 0;
                    $primarySourceHours = $pResult && isset($pResult['assigned_hours_by_specialty_source']['primary']) ? (int)$pResult['assigned_hours_by_specialty_source']['primary'] : 0;
                    $secondarySourceHours = $pResult && isset($pResult['assigned_hours_by_specialty_source']['secondary']) ? (int)$pResult['assigned_hours_by_specialty_source']['secondary'] : 0;
                    $bLimitExceeded = $bHours > 10;
                    $personAllocationDetails = array();
                    foreach ($allocationRowResults as $detailIndex=>$detailResult) {
                        if (!$detailResult || !$detailResult['valid'] || $detailResult['person_id'] !== $personId || (int)$detailResult['hours'] < 1) continue;
                        $detailSlot = isset($allocationSlots[$detailResult['slot_id']]) ? $allocationSlots[$detailResult['slot_id']] : null;
                        $detailLabel = $detailSlot ? $detailSlot['slot_label'] . ' · ' . $detailSlot['subject'] : $detailResult['slot_id'];
                        $personAllocationDetails[] = $detailLabel . ' · ' . (int)$detailResult['hours'] . ' ώρ. — ' . staffingUiAllocationAssignmentLabel($detailResult);
                    }
                  ?>
                  <div class="allocation-person-summary-row" data-allocation-person-summary="<?php echo staffingUiH($personId); ?>">
                    <div>
                      <strong><?php echo staffingUiH($personData['specialty_code'] . ' · ' . ($personData['display_name'] !== '' ? $personData['display_name'] : 'Χωρίς ονοματεπώνυμο')); ?></strong>
                      <?php if ($personData['secondary_specialty_code'] !== ''): ?><small>2η ειδικότητα <?php echo staffingUiH($personData['secondary_specialty_code']); ?></small><?php endif; ?>
                    </div>
                    <div><strong data-person-required><?php echo (int)$personData['required_hours']; ?></strong><small>υποχρεωτικό ωράριο</small></div>
                    <div><strong data-person-assigned><?php echo $assignedHere; ?></strong><small>ανατεθειμένες ώρες</small></div>
                    <div><strong data-person-remaining><?php echo $remainingHere; ?></strong><small>υπόλοιπο</small></div>
                    <div><strong data-person-a><?php echo $aHours; ?></strong><small>Α΄ ανάθεση</small></div>
                    <div><strong data-person-b class="<?php echo $bLimitExceeded ? 'b-limit-over' : ''; ?>"><?php echo $bHours; ?>/10</strong><small>Β΄ ανάθεση</small></div>
                    <div class="allocation-person-meta" data-person-source-summary>
                      <?php if ($personData['secondary_specialty_code'] !== ''): ?>Μέσω κύριας <?php echo staffingUiH($personData['specialty_code']); ?>: <?php echo $primarySourceHours; ?> ώρ. · μέσω 2ης <?php echo staffingUiH($personData['secondary_specialty_code']); ?>: <?php echo $secondarySourceHours; ?> ώρ.<?php else: ?>Μέσω κύριας <?php echo staffingUiH($personData['specialty_code']); ?>: <?php echo $primarySourceHours; ?> ώρ.<?php endif; ?><?php if ((int)$personData['external_hours'] > 0): ?> · <?php echo (int)$personData['external_hours']; ?> ώρ. σε άλλη μονάδα<?php endif; ?>
                    </div>
                    <div class="b-limit-warning" data-person-b-warning<?php echo $bLimitExceeded ? '' : ' hidden'; ?>><?php echo staffingUiH(staffingUiBAssignmentLimitWarning()); ?></div>
                    <div class="allocation-person-assignments" data-person-assignments>
                      <?php if (empty($personAllocationDetails)): ?>
                        <div class="allocation-person-assignment-item" data-empty-assignment>Δεν έχουν κατανεμηθεί μαθήματα.</div>
                      <?php else: ?>
                        <?php foreach ($personAllocationDetails as $detailText): ?><div class="allocation-person-assignment-item"><?php echo staffingUiH($detailText); ?></div><?php endforeach; ?>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div id="allocationViewPanelSlots" data-allocation-view-panel="slots" role="tabpanel" aria-labelledby="allocationViewTabSlots" tabindex="0">
              <form method="post" id="staffingAllocationForm">
                <?php staffingUiRenderSchoolStateHiddenInputs(); ?>
                <?php staffingUiRenderPersonnelStateHiddenInputs($personnelRows); ?>
                <input type="hidden" name="allocation_payload_json" value="">
                <input type="hidden" name="staffing_action" value="">
                <input type="hidden" name="active_panel" value="allocation">

                <div class="allocation-toolbar">
                  <div>
                    <strong>Κατανομή ανά μάθημα / τμήμα</strong>
                    <div class="help">Επίλεξε πρώτα συγκεκριμένο τμήμα / ομάδα + μάθημα και μετά έναν από τους επιλέξιμους εκπαιδευτικούς. Η επιλεξιμότητα ελέγχει μαζί κύρια και 2η ειδικότητα και επιλέγει την καλύτερη ανάθεση.</div>
                    <?php if ($allocationNoEligibleSlotCount > 0): ?><div class="help"><strong>Δεν εμφανίζονται <?php echo (int)$allocationNoEligibleSlotCount; ?> μαθήματα / τμήματα χωρίς επιλέξιμο εκπαιδευτικό</strong> (<?php echo (int)$allocationNoEligibleHours; ?> ώρες). Οι ώρες τους εξακολουθούν να υπολογίζονται στις ακάλυπτες ώρες.</div><?php endif; ?>
                    <div class="help">Όταν καλυφθούν πλήρως οι ώρες ενός μαθήματος / τμήματος, η επιλογή του γίνεται αυτόματα ανενεργή στις υπόλοιπες γραμμές κατανομής.</div>
                  </div>
                  <div class="allocation-toolbar-actions">
                    <button class="edu-btn-primary" type="submit" name="staffing_action_fallback" value="allocation_auto" data-staffing-request-action="allocation_auto" id="autoAllocateRemaining">Αυτόματη πρόταση κάλυψης</button>
                    <button class="edu-btn-secondary" type="button" id="addAllocationRow">+ Προσθήκη μαθήματος</button>
                    <button class="edu-btn-secondary" type="button" id="clearAllocationRows">Καθαρισμός κατανομής</button>
                  </div>
                </div>

                <div class="allocation-list" id="allocationList">
                  <?php if (empty($allocationRows)): ?>
                    <div class="allocation-empty" id="emptyAllocationState">Δεν έχει γίνει ακόμη κατανομή. Πάτησε «+ Προσθήκη μαθήματος» για να ξεκινήσεις.</div>
                  <?php endif; ?>
                  <?php foreach ($allocationRows as $allocationIndex=>$allocation): ?>
                    <?php
                      $rowResult = isset($allocationRowResults[$allocationIndex]) ? $allocationRowResults[$allocationIndex] : null;
                      $rowStatusClass = '';
                      $rowStatusText = 'Συμπλήρωσε μάθημα και εκπαιδευτικό.';
                      if ($rowResult) {
                          if (!$rowResult['valid']) {
                              $labels = array(); foreach ($rowResult['errors'] as $error) $labels[] = staffingUiAllocationErrorLabel($error);
                              $rowStatusClass = ' is-error'; $rowStatusText = implode(' ', $labels);
                          } else {
                              $rowStatusText = staffingUiAllocationAssignmentLabel($rowResult);
                              $warningLabels = staffingUiAllocationWarningLabels($rowResult);
                              if (!empty($warningLabels)) {
                                  $rowStatusClass = ' is-warning'; $rowStatusText .= ' · ' . implode(' ', $warningLabels);
                              } else {
                                  $rowStatusClass = ' is-ok'; $rowStatusText .= ' ✓';
                              }
                          }
                      }
                    ?>
                    <div class="allocation-row" data-allocation-row>
                      <div class="allocation-row-main">
                        <div class="field"><label>Τμήμα / ομάδα · μάθημα</label><select name="allocation_slot_id[]" class="allocation-slot" aria-label="Μάθημα και τμήμα προς κατανομή"><?php staffingUiRenderAllocationSlotOptions($allocationSelectableSlots, isset($allocation['slot_id']) ? $allocation['slot_id'] : '', $allocationSlots); ?></select></div>
                        <div class="field"><label>Εκπαιδευτικός</label><select name="allocation_person_id[]" class="allocation-person" aria-label="Εκπαιδευτικός για την κατανομή"><?php staffingUiRenderAllocationPersonOptions($allocationPeople, isset($allocation['person_id']) ? $allocation['person_id'] : ''); ?></select></div>
                        <div class="field"><label>Ώρες <small>αυτόματα</small></label><input type="number" min="1" max="35" step="1" name="allocation_hours[]" class="allocation-hours" aria-label="Ώρες κατανομής" value="<?php echo staffingUiH(isset($allocation['hours']) ? $allocation['hours'] : 0); ?>" readonly></div>
                        <div class="allocation-status<?php echo $rowStatusClass; ?>" data-allocation-status><?php echo staffingUiH($rowStatusText); ?></div>
                        <button type="button" class="personnel-remove personnel-remove-compact allocation-remove" title="Αφαίρεση κατανομής" aria-label="Αφαίρεση κατανομής"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false" style="display:block" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg></button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="actions">
                  <button class="edu-btn-primary" type="submit" name="staffing_action_fallback" value="allocation" data-staffing-request-action="allocation">Έλεγχος κατανομής</button>
                </div>
              </form>

              <template id="allocationRowTemplate">
                <div class="allocation-row" data-allocation-row>
                  <div class="allocation-row-main">
                    <div class="field"><label>Τμήμα / ομάδα · μάθημα</label><select name="allocation_slot_id[]" class="allocation-slot" aria-label="Μάθημα και τμήμα προς κατανομή"><?php staffingUiRenderAllocationSlotOptions($allocationSelectableSlots, '', $allocationSlots); ?></select></div>
                    <div class="field"><label>Εκπαιδευτικός</label><select name="allocation_person_id[]" class="allocation-person" aria-label="Εκπαιδευτικός για την κατανομή"><?php staffingUiRenderAllocationPersonOptions($allocationPeople, ''); ?></select></div>
                    <div class="field"><label>Ώρες <small>αυτόματα</small></label><input type="number" min="1" max="35" step="1" name="allocation_hours[]" class="allocation-hours" aria-label="Ώρες κατανομής" value="0" readonly></div>
                    <div class="allocation-status" data-allocation-status>Συμπλήρωσε μάθημα και εκπαιδευτικό.</div>
                    <button type="button" class="personnel-remove personnel-remove-compact allocation-remove" title="Αφαίρεση κατανομής" aria-label="Αφαίρεση κατανομής"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false" style="display:block" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg></button>
                  </div>
                </div>
              </template>
            </div>
          <?php endif; ?>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(array('class'=>'card staffing-panel vacancies-card','attrs'=>array('id'=>'staffingPanelVacancies','data-staffing-panel'=>'vacancies','role'=>'tabpanel','aria-labelledby'=>'staffingTabVacancies','tabindex'=>'0') + ($activePanel !== 'vacancies' ? array('hidden'=>true) : array()))); ?>
          <h2>5. Κενά μαθημάτων</h2>
          <p class="cap">Συγκεντρώνει τις ώρες μαθημάτων που απομένουν ακάλυπτες μετά την τρέχουσα κατανομή. Είναι η πρακτική λίστα που μπορεί να χρησιμοποιηθεί για αναζήτηση εκπαιδευτικών, χωρίς να χαρακτηρίζει από μόνη της τις ώρες ως επίσημα λειτουργικά κενά.</p>
          <div class="info-note"><strong>Ζωντανή εικόνα της Καρτέλας 4.</strong> Η λίστα ενημερώνεται αμέσως όταν αλλάζει η κατανομή. Αν υπάρχει ήδη επιλέξιμος εκπαιδευτικός με υπόλοιπο ωραρίου, το εργαλείο το επισημαίνει ώστε να ελεγχθεί πρώτα η εσωτερική κατανομή.</div>

          <div class="staffing-summary-grid" id="vacancySummary">
            <div class="summary-chip"><strong data-vacancy-total>0</strong><span>ακάλυπτες ώρες</span></div>
            <div class="summary-chip"><strong data-vacancy-slots>0</strong><span>μαθήματα / τμήματα με υπόλοιπο</span></div>
            <div class="summary-chip"><strong data-vacancy-no-staff>0</strong><span>μαθήματα / τμήματα χωρίς διαθέσιμο επιλέξιμο προσωπικό</span></div>
            <div class="summary-chip"><strong data-vacancy-has-staff>0</strong><span>μαθήματα / τμήματα με επιλέξιμο προσωπικό και υπόλοιπο</span></div>
          </div>

          <div class="vacancy-toolbar">
            <div class="field vacancy-filter">
              <label for="vacancyFilter">Φίλτρο τάξης / μαθήματος / κλάδου</label>
              <input id="vacancyFilter" type="search" placeholder="π.χ. Β΄, Μαθηματικά ή ΠΕ03">
            </div>
          </div>

          <details class="stat51-compare-panel" id="stat51ComparePanel">
            <summary><strong>Προαιρετικός έλεγχος με stat5_1 myschool</strong> <span>σύγκριση των ακάλυπτων ωρών</span></summary>
            <div class="stat51-compare-body">
              <p class="help">Φόρτωσε το αυθεντικό <code>stat5_1</code> σε ZIP ή CSV. Η ανάγνωση γίνεται μόνο στον browser σου και το αρχείο <strong>δεν αλλάζει</strong> την κατανομή ή τα κενά του εργαλείου. Η αντιστοίχιση του σχολείου γίνεται από τον κωδικό myschool της Καρτέλας 1.</p>
              <div class="stat51-actions">
                <button type="button" class="edu-btn-secondary" id="pickStat51File">Επιλογή stat5_1 ZIP / CSV</button>
                <button type="button" class="edu-btn-secondary" id="clearStat51File" hidden>Καθαρισμός σύγκρισης</button>
                <input id="stat51FileInput" type="file" accept=".zip,.csv,text/csv,application/zip" hidden>
              </div>
              <div class="personnel-csv-status stat51-status" id="stat51Status" role="status" aria-live="polite">Δεν έχει φορτωθεί stat5_1.</div>
              <div class="staffing-summary-grid stat51-summary" id="stat51Summary" hidden>
                <div class="summary-chip"><strong data-stat51-ours>0</strong><span>δικές μας ακάλυπτες ώρες</span></div>
                <div class="summary-chip"><strong data-stat51-myschool>0</strong><span>ώρες κενού stat5_1</span></div>
                <div class="summary-chip"><strong data-stat51-difference>0</strong><span>διαφορά (δικό μας − stat5_1)</span></div>
                <div class="summary-chip"><strong data-stat51-agreements>0</strong><span>γραμμές με ακριβή συμφωνία</span></div>
              </div>
              <div class="matrix-wrap stat51-table-wrap" id="stat51TableWrap" hidden>
                <table class="staffing-table stat51-table" id="stat51ComparisonTable">
                  <caption class="edu-tools-sr-only">Σύγκριση ακάλυπτων ωρών του εργαλείου με το myschool stat5_1</caption>
                  <thead><tr><th scope="col">Τάξη</th><th scope="col">Μάθημα</th><th scope="col">Δικό μας</th><th scope="col">stat5_1</th><th scope="col">Διαφορά</th><th scope="col">Έλεγχος</th></tr></thead>
                  <tbody id="stat51ComparisonBody"></tbody>
                </table>
              </div>
              <div class="vacancy-empty stat51-empty" id="stat51Empty" hidden>Δεν υπάρχουν γραμμές για σύγκριση στο τρέχον σχολείο.</div>
              <p class="help" id="stat51Footnote" hidden><strong>Πώς διαβάζεται:</strong> το stat5_1 είναι στιγμιότυπο του myschool τη στιγμή της εξαγωγής. Διαφορά δεν σημαίνει αυτομάτως λάθος· μπορεί να οφείλεται σε μεταγενέστερη κατανομή, διαφορετικά καταχωρισμένα τμήματα/ομάδες ή διαφορετική ονομασία μαθήματος.</p>
            </div>
          </details>

          <div class="matrix-wrap" id="vacancyTableWrap">
            <table class="staffing-table vacancy-table" id="vacancyTable">
              <caption class="edu-tools-sr-only">Ακάλυπτες ώρες ανά τάξη, μάθημα και διαθέσιμο κλάδο</caption>
              <thead><tr><th scope="col">Τάξη</th><th scope="col">Μάθημα</th><th scope="col">Ακάλυπτες ώρες</th><th scope="col">Κλάδοι ανάθεσης</th><th scope="col">Τρέχον προσωπικό</th></tr></thead>
              <tbody>
              <?php foreach ($allocationSlots as $vacancySlotId=>$vacancySlot): ?>
                <?php
                  $vacancyState = isset($vacancySlotState[$vacancySlotId]) ? $vacancySlotState[$vacancySlotId] : array('remaining_hours'=>(int)$vacancySlot['capacity_hours'],'has_eligible_person'=>false);
                  $vacancyRemaining = isset($vacancyState['remaining_hours']) ? (int)$vacancyState['remaining_hours'] : 0;
                  $vacancyEligibility = isset($vacancySlot['eligible_by_priority']) ? $vacancySlot['eligible_by_priority'] : array();
                  $vacancySearchParts = array(isset($vacancySlot['slot_label'])?$vacancySlot['slot_label']:'', isset($vacancySlot['subject'])?$vacancySlot['subject']:'');
                  foreach (array('A','B','C','SPECIAL') as $vp) if (!empty($vacancyEligibility[$vp])) $vacancySearchParts = array_merge($vacancySearchParts, $vacancyEligibility[$vp]);
                ?>
                <tr data-vacancy-row="<?php echo staffingUiH($vacancySlotId); ?>" data-search="<?php echo staffingUiH(implode(' ', $vacancySearchParts)); ?>"<?php echo $vacancyRemaining < 1 ? ' hidden' : ''; ?>>
                  <td><strong title="<?php echo staffingUiH(isset($vacancySlot['slot_label']) ? $vacancySlot['slot_label'] : ''); ?>"><?php echo staffingUiH(isset($vacancySlot['grade']) && $vacancySlot['grade'] !== '' ? $vacancySlot['grade'] . ' τάξη' : '—'); ?></strong></td>
                  <td><?php echo staffingUiH(isset($vacancySlot['subject']) ? $vacancySlot['subject'] : ''); ?></td>
                  <td><span class="vacancy-hours" data-vacancy-hours><?php echo $vacancyRemaining; ?></span></td>
                  <td><div class="vacancy-assignments">
                    <?php foreach (array('A'=>'Α΄','B'=>'Β΄','C'=>'Γ΄','SPECIAL'=>'Ειδική') as $vp=>$vpLabel): ?>
                      <?php if (!empty($vacancyEligibility[$vp])): ?><span><strong><?php echo $vpLabel; ?>:</strong> <?php echo staffingUiH(staffingUiCompactSpecialtyCodes($vacancyEligibility[$vp])); ?></span><?php endif; ?>
                    <?php endforeach; ?>
                  </div></td>
                  <td><span class="vacancy-status" data-vacancy-status>—</span></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="vacancy-empty" id="vacancyEmpty" hidden>Δεν υπάρχουν ακάλυπτες ώρες μαθημάτων στην τρέχουσα κατανομή.</div>
          <p class="help"><strong>Σημείωση:</strong> οι κλάδοι ανάθεσης προέρχονται από την ίδια κανονιστική λογική της Καρτέλας 2. Η ένδειξη «τρέχον προσωπικό» εξετάζει κύρια και 2η ειδικότητα και το διαθέσιμο υπόλοιπο ωραρίου των εκπαιδευτικών της μονάδας.</p>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(array('class'=>'card staffing-panel specialty-balance-card','attrs'=>array('id'=>'staffingPanelSpecialties','data-staffing-panel'=>'specialties','role'=>'tabpanel','aria-labelledby'=>'staffingTabSpecialties','tabindex'=>'0') + ($activePanel !== 'specialties' ? array('hidden'=>true) : array()))); ?>
          <h2>6. Κενά / πλεονάσματα ειδικοτήτων</h2>
          <p class="cap">Μετατρέπει την εικόνα μαθημάτων και προσωπικού σε προτεινόμενη δήλωση ανά κλάδο. Πριν δημιουργήσει έλλειμμα, το εργαλείο ελέγχει αν οι ακάλυπτες ώρες μπορούν να απορροφηθούν από το υπάρχον προσωπικό μέσω Α΄/Β΄/Γ΄ ανάθεσης ή 2ης ειδικότητας.</p>
          <div class="info-note"><strong>«Έξυπνη» επιλογή κλάδου:</strong> όταν ένα ακάλυπτο μάθημα έχει περισσότερους από έναν ισότιμους κλάδους στην καλύτερη ανάθεση, προτείνεται ο κλάδος που μπορεί να καλύψει τις περισσότερες από τις συνολικά ακάλυπτες ώρες. Δεν επιλέγεται χαμηλότερη ανάθεση μόνο και μόνο για να βελτιωθεί η συγκέντρωση των κενών.</div>
          <?php if ($schoolType === 'gymnasio'): ?><div class="info-note"><strong>Υπόδειγμα ΔΔΕ Κέρκυρας:</strong> τα Εργαστήρια Δεξιοτήτων και η Τεχνολογία Γυμνασίου διατηρούνται ως ξεχωριστές γραμμές και δεν αποδίδονται τεχνητά σε έναν κλάδο.</div><?php endif; ?>
          <?php if ($specialtyBalanceReport && empty($specialtyBalanceReport['summary']['maximum_coverage_certified'])): ?><div class="info-note is-warning"><strong>Χρειάζεται τελικός έλεγχος.</strong> Η εσωτερική εξισορρόπηση είναι έγκυρη και atomic, αλλά η μέγιστη κάλυψη δεν πιστοποιήθηκε πλήρως εντός του ορίου ασφαλείας του optimizer.</div><?php endif; ?>

          <div class="staffing-summary-grid" id="specialtyBalanceSummary">
            <div class="summary-chip"><strong data-specialty-manual-uncovered><?php echo $specialtyBalanceReport ? (int)$specialtyBalanceReport['summary']['manual_unassigned_hours'] : 0; ?></strong><span>ώρες χωρίς χειροκίνητη κατανομή</span></div>
            <div class="summary-chip"><strong data-specialty-auto-covered><?php echo $specialtyBalanceReport ? (int)$specialtyBalanceReport['summary']['auto_internal_covered_hours'] : 0; ?></strong><span>ώρες που μπορεί να καλύψει εσωτερικά το υπάρχον προσωπικό</span></div>
            <div class="summary-chip"><strong data-specialty-final-uncovered><?php echo $specialtyBalanceReport ? (int)$specialtyBalanceReport['summary']['final_uncovered_hours'] : 0; ?></strong><span>τελικές ακάλυπτες ώρες προς δήλωση</span></div>
            <div class="summary-chip"><strong data-specialty-surplus-total><?php echo $specialtyBalanceReport ? (int)$specialtyBalanceReport['summary']['surplus_hours_total'] : 0; ?></strong><span>ώρες πλεονάσματος προσωπικού</span></div>
          </div>

          <div class="specialty-balance-toolbar">
            <p class="help" style="margin:0"><strong>Σύμβαση δήλωσης:</strong> έλλειμμα με πρόσημο −, πλεόνασμα χωρίς πρόσημο.</p>
            <button type="button" class="edu-btn-secondary" id="specialtyBalanceCsv">Λήψη CSV για ΔΔΕ</button>
          </div>

          <div class="matrix-wrap" id="specialtyBalanceTableWrap">
            <table class="staffing-table specialty-balance-table" id="specialtyBalanceTable">
              <caption class="edu-tools-sr-only">Προτεινόμενα κενά και πλεονάσματα ανά κλάδο</caption>
              <thead><tr><th scope="col">Κλάδος / γραμμή δήλωσης</th><th scope="col">Περιγραφή</th><th scope="col">Έλλειμμα</th><th scope="col">Πλεόνασμα</th><th scope="col">Δήλωση</th><th scope="col">Παρατήρηση</th></tr></thead>
              <tbody id="specialtyBalanceBody">
                <?php if ($specialtyBalanceReport): ?>
                  <?php foreach ($specialtyBalanceReport['by_specialty'] as $balanceCode=>$balanceRow): ?>
                    <?php if ((int)$balanceRow['gap_hours'] < 1 && (int)$balanceRow['surplus_hours'] < 1) continue; $signed=(int)$balanceRow['signed_balance_hours']; ?>
                    <tr data-specialty-balance-row="<?php echo staffingUiH($balanceCode); ?>">
                      <td><strong><?php echo staffingUiH($balanceCode); ?></strong></td>
                      <td><?php echo staffingUiH($balanceRow['label']); ?></td>
                      <td class="specialty-balance-value specialty-balance-deficit"><?php echo (int)$balanceRow['gap_hours']; ?></td>
                      <td class="specialty-balance-value specialty-balance-surplus"><?php echo (int)$balanceRow['surplus_hours']; ?></td>
                      <td class="specialty-balance-value"><?php echo (string)$signed; ?></td>
                      <td class="specialty-balance-note"><?php echo !empty($balanceRow['has_both_gap_and_surplus']) ? 'Ταυτόχρονο έλλειμμα και πλεόνασμα στον ίδιο κλάδο — χρειάζεται έλεγχος πριν από οριστική δήλωση.' : ''; ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php foreach ($specialtyBalanceReport['special_reporting_buckets'] as $bucketKey=>$bucketRow): ?>
                    <?php if ((int)$bucketRow['gap_hours'] < 1) continue; ?>
                    <tr data-specialty-balance-row="<?php echo staffingUiH($bucketKey); ?>" class="specialty-balance-special">
                      <td><strong><?php echo staffingUiH($bucketRow['label']); ?></strong></td>
                      <td>Δεν αποδίδεται σε συγκεκριμένη ειδικότητα</td>
                      <td class="specialty-balance-value specialty-balance-deficit"><?php echo (int)$bucketRow['gap_hours']; ?></td>
                      <td class="specialty-balance-value specialty-balance-surplus">0</td>
                      <td class="specialty-balance-value">-<?php echo (int)$bucketRow['gap_hours']; ?></td>
                      <td class="specialty-balance-note">Ξεχωριστή γραμμή του υποδείγματος.</td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="vacancy-empty" id="specialtyBalanceEmpty"<?php echo $specialtyBalanceReport && (!empty($specialtyBalanceReport['by_specialty']) || !empty($specialtyBalanceReport['special_reporting_buckets'])) ? ' hidden' : ''; ?>>Δεν προκύπτει έλλειμμα ή πλεόνασμα προς δήλωση.</div>

          <details class="specialty-balance-details" id="specialtySmartDetails">
            <summary>Πώς έγινε η «έξυπνη» επιλογή στα κοινά κενά</summary>
            <div class="matrix-wrap">
              <table class="staffing-table specialty-smart-table"><caption class="edu-tools-sr-only">Αιτιολόγηση επιλογής κλάδου για κοινά κενά</caption><thead><tr><th scope="col">Τμήμα / ομάδα</th><th scope="col">Μάθημα</th><th scope="col">Ώρες</th><th scope="col">Προτεινόμενος κλάδος</th><th scope="col">Ισότιμες εναλλακτικές</th></tr></thead><tbody id="specialtySmartBody">
              <?php if ($specialtyBalanceReport): foreach ($specialtyBalanceReport['vacancy_recommendations'] as $smartRow): if (count($smartRow['candidate_codes']) < 2) continue; ?>
                <tr><td><?php echo staffingUiH($smartRow['slot_label']); ?></td><td><?php echo staffingUiH($smartRow['subject']); ?></td><td><?php echo (int)$smartRow['hours']; ?></td><td><strong><?php echo staffingUiH($smartRow['selected_code']); ?></strong></td><td><?php echo staffingUiH(implode(', ', $smartRow['candidate_codes'])); ?></td></tr>
              <?php endforeach; endif; ?>
              </tbody></table>
            </div>
          </details>

          <details class="specialty-balance-details" id="specialtyAutoDetails">
            <summary>Προτεινόμενη εσωτερική κάλυψη πριν δηλωθούν κενά</summary>
            <p class="help">Η πρόταση δεν αλλάζει την Καρτέλα 4. Χρησιμοποιείται μόνο για να μην δηλωθεί ως κενό κάτι που μπορεί κανονικά να καλυφθεί από το υπάρχον προσωπικό. Η αυτόματη πρόταση δεν υπερβαίνει τις 10 ώρες Β΄ ανάθεσης.</p>
            <div class="matrix-wrap">
              <table class="staffing-table specialty-auto-table"><caption class="edu-tools-sr-only">Προτεινόμενη εσωτερική κάλυψη από υπάρχον προσωπικό</caption><thead><tr><th scope="col">Εκπαιδευτικός</th><th scope="col">Τμήμα / ομάδα</th><th scope="col">Μάθημα</th><th scope="col">Ώρες</th><th scope="col">Ανάθεση</th></tr></thead><tbody id="specialtyAutoBody">
              <?php if ($specialtyBalanceReport): foreach ($specialtyBalanceReport['automatic_balance']['allocations'] as $autoRow): $autoPerson=isset($allocationPeopleClient[$autoRow['person_id']])?$allocationPeopleClient[$autoRow['person_id']]:null; ?>
                <tr><td><?php echo staffingUiH($autoPerson ? $autoPerson['label'] : $autoRow['person_id']); ?></td><td><?php echo staffingUiH($autoRow['slot_label']); ?></td><td><?php echo staffingUiH($autoRow['subject']); ?></td><td><?php echo (int)$autoRow['hours']; ?></td><td><?php echo staffingUiH(($autoRow['priority']==='A'?'Α΄':($autoRow['priority']==='B'?'Β΄':($autoRow['priority']==='C'?'Γ΄':'Ειδική'))) . ' ανάθεση' . ($autoRow['specialty_source']==='secondary'?' · μέσω 2ης ειδικότητας '.$autoRow['used_specialty_code']:'')); ?></td></tr>
              <?php endforeach; endif; ?>
              </tbody></table>
            </div>
          </details>

          <p class="help"><strong>Σημαντικό:</strong> η Καρτέλα 6 είναι προτεινόμενη υπηρεσιακή εικόνα για έλεγχο και αποστολή προς ΔΔΕ, όχι αυτόματη επίσημη πράξη. Το CSV χρησιμοποιεί portable schema <code>staffing_balance_v1</code> ώστε αργότερα να μπορούν να συγκεντρώνονται πολλαπλά σχολεία σε επίπεδο Διεύθυνσης Εκπαίδευσης.</p>
        <?php calculatorCardEnd(); ?>
      <?php endif; ?>
    <?php calculatorMainEnd(); ?>


  <?php calculatorColumnsEnd(); ?>
</main>

<?php sourceCardStart(); ?>
  <?php sourceCardDisclaimerStart(); ?>
    Ο υπολογισμός συνδυάζει τα ωρολόγια προγράμματα και τις ισχύουσες αναθέσεις που χρησιμοποιούνται ήδη στα δύο αντίστοιχα εργαλεία της Εργαλειοθήκης. Τα αποτελέσματα είναι εργαλείο ελέγχου / προσομοίωσης και δεν αποτελούν από μόνα τους επίσημη πράξη προσδιορισμού λειτουργικών κενών ή τοποθέτησης εκπαιδευτικών.
  <?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202132_09_04_26_OP%20EM%20GYMN.pdf', 'Υ.Α. 44257/Δ2/08-04-2026 — ΦΕΚ Β΄ 2132/09-04-2026 · Ημερήσιο Γυμνάσιο ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-ekpaideuse/deuterobathmia-ekpaideuse/upourgike-apophase-74472-d2-2020.html', 'Υ.Α. 74472/Δ2/2020 — ΦΕΚ Β΄ 2450/2020 · Τεχνολογία / Πληροφορική Γυμνασίου ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202106_09_04_26_OP%20EM%20GEL_ESP%20Gymnasio.pdf', 'Υ.Α. 43684/Δ2/07-04-2026 — ΦΕΚ Β΄ 2106/09-04-2026 · Ημερήσιο ΓΕΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202106_09_04_26_OP%20EM%20GEL_ESP%20Gymnasio.pdf', 'Υ.Α. 43751/Δ2/07-04-2026 — ΦΕΚ Β΄ 2106/09-04-2026 · Εσπερινό Γυμνάσιο ↗'); ?>
    <?php sourceCardLink('https://dide.ira.sch.gr/wp-content/uploads/2026/04/%CE%A6%CE%95%CE%9A-%CE%92-2102_09_04_26_%CE%A9%CE%A0-%CE%95%CE%A3%CE%A0-%CE%93%CE%95%CE%9B.pdf', 'Υ.Α. 43706/Δ2/07-04-2026 — ΦΕΚ Β΄ 2102/09-04-2026 · Εσπερινό ΓΕΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/dioikitika-themata-geniko-lykeio', 'Υ.Α. 54058/Δ2/05-05-2026 — ΦΕΚ Β΄ 2583/07-05-2026 · Αναθέσεις Γυμνασίου / ΓΕΛ ↗'); ?>
    <?php sourceCardLink(ethicsClassFormationPolicy()['source_url'], 'Υ.Α. 108070/Δ2/2026 — ΦΕΚ Β΄ 5231/2026 · Ηθική ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php if ($submitted && $matrix && $displayMatrix): ?>
<section class="staffing-print-report" id="staffingPrintReport" aria-label="Εκτυπώσιμο αποτέλεσμα διδακτικών αναγκών">
  <div class="print-header">
    <div>
      <h1>Υπολογισμός διδακτικών αναγκών σχολικής μονάδας</h1>
      <p class="print-subtitle"><strong><?php echo staffingUiH($schoolName !== '' ? $schoolName : staffingUiSchoolTypeLabel($schoolType, true)); ?></strong> · <?php echo staffingUiH(staffingUiSchoolTypeLabel($schoolType)); ?><?php if (!empty($schoolCode)): ?> · κωδ. <?php echo staffingUiH($schoolCode); ?><?php endif; ?> · σχολικό έτος 2026–2027</p>
    </div>
    <div class="print-meta">Εργαλειοθήκη Εκπαιδευτικού<br><span data-print-generated-at>—</span></div>
  </div>

  <h2>Συνοπτική εικόνα</h2>
  <div class="print-summary">
    <div class="print-summary-item"><strong><?php echo (int)$generalSectionTotal; ?></strong><span>κανονικά τμήματα σχολικής μονάδας</span></div>
    <div class="print-summary-item"><strong><?php echo (int)$matrix['summary']['assignment_unit_hours']; ?></strong><span>ώρες με αντιστοιχισμένη ανάθεση</span></div>
    <div class="print-summary-item"><strong><?php echo (int)(isset($displayMatrix['summary']['presentation_staffing_leaf_codes_with_claims']) ? $displayMatrix['summary']['presentation_staffing_leaf_codes_with_claims'] : 0); ?></strong><span>κλάδοι με επιλεξιμότητα</span></div>
    <div class="print-summary-item"><strong><?php echo (int)$personnelSummary['available_here_hours']; ?></strong><span>ώρες προσωπικού διαθέσιμες εδώ</span></div>
    <div class="print-summary-item"><strong><?php echo staffingUiH(staffingUiReadinessLabel($matrix['readiness'])); ?></strong><span>κατάσταση δομικών στοιχείων</span></div>
  </div>

  <h2>Διδακτικές ανάγκες ανά κλάδο</h2>
  <table class="print-matrix">
    <thead><tr><th>Κλάδος</th><th class="num">Α΄</th><th class="num">Β΄</th><th class="num">Γ΄</th></tr></thead>
    <tbody>
      <?php if (!empty($collapsedSkills['active'])): ?>
        <tr><td><span class="print-code">Οποιαδήποτε ειδικότητα</span> · Εργαστήρια Δεξιοτήτων</td><td class="num" colspan="3"><?php echo (int)$collapsedSkills['hours']; ?> ώρες συνολικά</td></tr>
      <?php endif; ?>
      <?php foreach ($displayMatrix['codes'] as $code=>$row): ?>
        <tr>
          <td><span class="print-code"><?php echo staffingUiH($code); ?></span><?php if (!empty($row['label'])): ?> · <?php echo staffingUiH($row['label']); ?><?php endif; ?></td>
          <td class="num"><?php echo (int)$row['eligible_hours_by_priority']['A']; ?></td>
          <td class="num"><?php echo (int)$row['eligible_hours_by_priority']['B']; ?></td>
          <td class="num"><?php echo (int)$row['eligible_hours_by_priority']['C']; ?></td>
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
                ? $allocationPerson['label']
                : 'Μη έγκυρος εκπαιδευτικός';
            $allocationSlot = isset($allocationSlots[$allocation['slot_id']]) ? $allocationSlots[$allocation['slot_id']] : null;
            $allocationSlotText = $allocationSlot ? $allocationSlot['slot_label'] . ' · ' . $allocationSlot['subject'] : 'Μη έγκυρο μάθημα / ομάδα';
            if ($allocationResult && !$allocationResult['valid']) {
                $labels = array(); foreach ($allocationResult['errors'] as $error) $labels[] = staffingUiAllocationErrorLabel($error);
                $allocationStatus = implode(' ', $labels); $allocationStatusClass = 'print-status-error';
            } elseif ($allocationResult && $allocationResult['valid']) {
                $allocationStatus = staffingUiAllocationAssignmentLabel($allocationResult);
                $allocationWarningLabels = staffingUiAllocationWarningLabels($allocationResult);
                if (!empty($allocationWarningLabels)) {
                    $allocationStatus .= ' · ' . implode(' ', $allocationWarningLabels); $allocationStatusClass = 'print-status-warn';
                } else {
                    $allocationStatus .= ' ✓'; $allocationStatusClass = 'print-status-ok';
                }
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
    <?php if ($allocationPlan): ?>
      <h2>Σύνοψη κατανομής ανά εκπαιδευτικό</h2>
      <table>
        <thead><tr><th>Εκπαιδευτικός</th><th class="num">Υποχρ.</th><th class="num">Ανατεθ.</th><th class="num">Υπόλοιπο</th><th class="num">Α΄</th><th class="num">Β΄ / 10</th><th>Ειδικότητα που χρησιμοποιήθηκε</th></tr></thead>
        <tbody>
          <?php foreach ($allocationPeopleClient as $personId=>$personData): ?>
            <?php
              $printPersonResult = isset($allocationPlan['people'][$personId]) ? $allocationPlan['people'][$personId] : null;
              $printAssigned = $printPersonResult ? (int)$printPersonResult['assigned_profile_hours'] : 0;
              $printRemaining = $printPersonResult ? (int)$printPersonResult['remaining_hours'] : (int)$personData['available_here_hours'];
              $printA = $printPersonResult && isset($printPersonResult['assigned_hours_by_priority']['A']) ? (int)$printPersonResult['assigned_hours_by_priority']['A'] : 0;
              $printB = $printPersonResult && isset($printPersonResult['assigned_hours_by_priority']['B']) ? (int)$printPersonResult['assigned_hours_by_priority']['B'] : 0;
              $printPrimarySource = $printPersonResult && isset($printPersonResult['assigned_hours_by_specialty_source']['primary']) ? (int)$printPersonResult['assigned_hours_by_specialty_source']['primary'] : 0;
              $printSecondarySource = $printPersonResult && isset($printPersonResult['assigned_hours_by_specialty_source']['secondary']) ? (int)$printPersonResult['assigned_hours_by_specialty_source']['secondary'] : 0;
              $printSourceText = 'κύρια ' . $personData['specialty_code'] . ': ' . $printPrimarySource . ' ώρ.';
              if ($personData['secondary_specialty_code'] !== '') $printSourceText .= ' · 2η ' . $personData['secondary_specialty_code'] . ': ' . $printSecondarySource . ' ώρ.';
            ?>
            <tr>
              <td><?php echo staffingUiH($personData['label']); ?></td>
              <td class="num"><?php echo (int)$personData['required_hours']; ?></td>
              <td class="num"><?php echo $printAssigned; ?></td>
              <td class="num"><?php echo $printRemaining; ?></td>
              <td class="num"><?php echo $printA; ?></td>
              <td class="num <?php echo $printB > 10 ? 'print-status-warn' : ''; ?>"><?php echo $printB; ?>/10</td>
              <td><?php echo staffingUiH($printSourceText); ?><?php if ($printB > 10): ?><br><strong><?php echo staffingUiH(staffingUiBAssignmentLimitWarning()); ?></strong><?php endif; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  <?php endif; ?>

  <h2>Κενά μαθημάτων</h2>
  <p class="print-note">Ακάλυπτες ώρες μετά την τρέχουσα κατανομή. Η εικόνα αυτή δεν αποτελεί από μόνη της επίσημη πράξη προσδιορισμού λειτουργικών κενών.</p>
  <table class="print-vacancies">
    <thead><tr><th>Τμήμα / ομάδα</th><th>Μάθημα</th><th class="num">Ακάλυπτες ώρες</th><th>Κλάδοι ανάθεσης</th><th>Κατάσταση διαθέσιμου προσωπικού</th></tr></thead>
    <tbody>
      <?php foreach ($allocationSlots as $vacancySlotId=>$vacancySlot): ?>
        <?php
          $printVacancyState = isset($vacancySlotState[$vacancySlotId]) ? $vacancySlotState[$vacancySlotId] : array('remaining_hours'=>(int)$vacancySlot['capacity_hours']);
          $printVacancyRemaining = isset($printVacancyState['remaining_hours']) ? (int)$printVacancyState['remaining_hours'] : 0;
          $printVacancyEligibility = isset($vacancySlot['eligible_by_priority']) ? $vacancySlot['eligible_by_priority'] : array();
          $printAssignmentParts = array();
          foreach (array('A'=>'Α΄','B'=>'Β΄','C'=>'Γ΄','SPECIAL'=>'Ειδική') as $printVp=>$printVpLabel) {
              if (!empty($printVacancyEligibility[$printVp])) $printAssignmentParts[] = $printVpLabel . ': ' . staffingUiCompactSpecialtyCodes($printVacancyEligibility[$printVp]);
          }
        ?>
        <tr data-print-vacancy-row="<?php echo staffingUiH($vacancySlotId); ?>"<?php echo $printVacancyRemaining < 1 ? ' hidden' : ''; ?>>
          <td><?php echo staffingUiH(isset($vacancySlot['slot_label']) ? $vacancySlot['slot_label'] : ''); ?></td>
          <td><?php echo staffingUiH(isset($vacancySlot['subject']) ? $vacancySlot['subject'] : ''); ?></td>
          <td class="num" data-print-vacancy-hours><?php echo $printVacancyRemaining; ?></td>
          <td><?php echo staffingUiH(implode(' · ', $printAssignmentParts)); ?></td>
          <td data-print-vacancy-status>—</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p class="print-note" id="printVacancyEmpty" hidden>Δεν υπάρχουν ακάλυπτες ώρες μαθημάτων στην τρέχουσα κατανομή.</p>

  <h2>Κενά / πλεονάσματα ειδικοτήτων</h2>
  <p class="print-note">Προτεινόμενη δήλωση μετά από εσωτερική εξισορρόπηση του υπάρχοντος προσωπικού. Έλλειμμα με πρόσημο −, πλεόνασμα χωρίς πρόσημο.</p>
  <div class="print-summary">
    <div class="print-summary-item"><strong data-print-specialty-manual-uncovered><?php echo $specialtyBalanceReport ? (int)$specialtyBalanceReport['summary']['manual_unassigned_hours'] : 0; ?></strong><span>ώρες χωρίς χειροκίνητη κατανομή</span></div>
    <div class="print-summary-item"><strong data-print-specialty-auto-covered><?php echo $specialtyBalanceReport ? (int)$specialtyBalanceReport['summary']['auto_internal_covered_hours'] : 0; ?></strong><span>ώρες εσωτερικής κάλυψης</span></div>
    <div class="print-summary-item"><strong data-print-specialty-final-uncovered><?php echo $specialtyBalanceReport ? (int)$specialtyBalanceReport['summary']['final_uncovered_hours'] : 0; ?></strong><span>τελικές ακάλυπτες ώρες</span></div>
    <div class="print-summary-item"><strong data-print-specialty-surplus-total><?php echo $specialtyBalanceReport ? (int)$specialtyBalanceReport['summary']['surplus_hours_total'] : 0; ?></strong><span>ώρες πλεονάσματος</span></div>
  </div>
  <table class="print-specialty-balance">
    <thead><tr><th>Κλάδος / γραμμή</th><th>Περιγραφή</th><th class="num">Έλλειμμα</th><th class="num">Πλεόνασμα</th><th class="num">Δήλωση</th><th>Παρατήρηση</th></tr></thead>
    <tbody id="printSpecialtyBalanceBody">
      <?php if ($specialtyBalanceReport): ?>
        <?php foreach ($specialtyBalanceReport['by_specialty'] as $balanceCode=>$balanceRow): ?>
          <?php if ((int)$balanceRow['gap_hours'] < 1 && (int)$balanceRow['surplus_hours'] < 1) continue; $signed=(int)$balanceRow['signed_balance_hours']; ?>
          <tr><td><?php echo staffingUiH($balanceCode); ?></td><td><?php echo staffingUiH($balanceRow['label']); ?></td><td class="num"><?php echo (int)$balanceRow['gap_hours']; ?></td><td class="num"><?php echo (int)$balanceRow['surplus_hours']; ?></td><td class="num"><?php echo (string)$signed; ?></td><td><?php echo !empty($balanceRow['has_both_gap_and_surplus']) ? 'Ταυτόχρονο έλλειμμα και πλεόνασμα — απαιτεί έλεγχο.' : ''; ?></td></tr>
        <?php endforeach; ?>
        <?php foreach ($specialtyBalanceReport['special_reporting_buckets'] as $bucketKey=>$bucketRow): ?>
          <?php if ((int)$bucketRow['gap_hours'] < 1) continue; ?>
          <tr><td><strong><?php echo staffingUiH($bucketRow['label']); ?></strong></td><td>Δεν αποδίδεται σε συγκεκριμένη ειδικότητα</td><td class="num"><?php echo (int)$bucketRow['gap_hours']; ?></td><td class="num">0</td><td class="num">-<?php echo (int)$bucketRow['gap_hours']; ?></td><td>Ξεχωριστή γραμμή υποδείγματος.</td></tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <p class="print-note" id="printSpecialtyBalanceEmpty" hidden>Δεν προκύπτει έλλειμμα ή πλεόνασμα προς δήλωση.</p>

  <div class="print-footer">Το παρόν αποτελεί αποτέλεσμα εργαλείου προσομοίωσης/ελέγχου. Δεν συνιστά από μόνο του επίσημη πράξη προσδιορισμού λειτουργικών κενών ή τοποθέτησης εκπαιδευτικών. Οι αναθέσεις και το ωρολόγιο πρόγραμμα ακολουθούν τις κανονιστικές πηγές που χρησιμοποιεί η Εργαλειοθήκη Εκπαιδευτικού.</div>
</section>
<?php endif; ?>

<script src="<?php echo staffingUiH(edu_asset_url('includes/teaching-hours-calculations.js')); ?>"></script>
<script src="<?php echo staffingUiH(edu_asset_url('includes/school-profile-csv-import.js')); ?>"></script>
<script src="<?php echo staffingUiH(edu_asset_url('includes/personnel-csv-import.js')); ?>"></script>
<script src="<?php echo staffingUiH(edu_asset_url('includes/myschool-staff-import.js')); ?>"></script>
<script src="<?php echo staffingUiH(edu_asset_url('includes/myschool-stat51-import.js')); ?>"></script>
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
  function compactRepeatedFormState(form,prefix,payloadName){
    if(!form) return;
    const payloadInput=form.querySelector('input[name="'+payloadName+'"]');
    if(!payloadInput) return;
    const payload={};
    const fields=Array.from(form.querySelectorAll('[name^="'+prefix+'"]')).filter(function(el){
      return el.name!==payloadName && /\[\]$/.test(el.name) && !el.disabled;
    });
    fields.forEach(function(el){
      const key=el.name.replace(/\[\]$/,'');
      if(!payload[key]) payload[key]=[];
      payload[key].push(el.value);
    });
    payloadInput.value=JSON.stringify(payload);
    fields.forEach(function(el){el.disabled=true;});
  }
  function installExplicitRequestGate(form){
    if(!form || form.dataset.requestGateBound==='1') return;
    form.dataset.requestGateBound='1';
    const actionInput=form.querySelector('input[name="staffing_action"]');
    const requestButtons=Array.from(form.querySelectorAll('[data-staffing-request-action]'));
    requestButtons.forEach(function(button){
      button.addEventListener('click',function(event){
        if(form.dataset.requestInFlight==='1'){
          event.preventDefault();
          return;
        }
        const action=button.getAttribute('data-staffing-request-action')||'';
        if(action===''){
          event.preventDefault();
          return;
        }
        if(typeof form.reportValidity==='function' && !form.reportValidity()){
          event.preventDefault();
          return;
        }
        if(form.id==='staffingPersonnelForm') compactRepeatedFormState(form,'personnel_','personnel_payload_json');
        if(form.id==='staffingAllocationForm') compactRepeatedFormState(form,'allocation_','allocation_payload_json');
        if(actionInput) actionInput.value=action;
        form.dataset.explicitRequest='1';
      });
    });
    form.addEventListener('submit',function(event){
      var submitter=event.submitter || null;
      var submitterAction=submitter && submitter.getAttribute ? (submitter.getAttribute('data-staffing-request-action')||'') : '';
      if(actionInput && actionInput.value==='' && submitterAction!=='') actionInput.value=submitterAction;
      const armed=(actionInput && actionInput.value!=='') || submitterAction!=='' || form.dataset.explicitRequest==='1';
      if(!armed || form.dataset.requestInFlight==='1'){
        event.preventDefault();
        return;
      }
      form.dataset.requestInFlight='1';
      requestButtons.forEach(function(button){button.disabled=true;});
    });
  }
  ['staffingProfileForm','staffingPersonnelForm','staffingAllocationForm'].forEach(function(id){ installExplicitRequestGate(document.getElementById(id)); });

  const tabs=Array.from(document.querySelectorAll('[data-staffing-tab]'));
  const panels=Array.from(document.querySelectorAll('[data-staffing-panel]'));
  function activatePanel(name){
    tabs.forEach(function(tab){
      const active=tab.getAttribute('data-staffing-tab')===name;
      tab.classList.toggle('is-active',active);
      tab.setAttribute('aria-selected',active?'true':'false');
      tab.tabIndex=active?0:-1;
    });
    panels.forEach(function(panel){ panel.hidden=panel.getAttribute('data-staffing-panel')!==name; });
    if(name==='specialties' && typeof allocationCollectState==='function' && typeof renderSpecialtyBalance==='function') renderSpecialtyBalance(allocationCollectState());
  }
  function moveTabFocus(current,key){
    const enabled=tabs.filter(function(tab){return !tab.disabled;});
    const index=enabled.indexOf(current);
    if(index<0||enabled.length<1) return;
    let next=index;
    if(key==='ArrowRight'||key==='ArrowDown') next=(index+1)%enabled.length;
    else if(key==='ArrowLeft'||key==='ArrowUp') next=(index-1+enabled.length)%enabled.length;
    else if(key==='Home') next=0;
    else if(key==='End') next=enabled.length-1;
    else return;
    const target=enabled[next];
    activatePanel(target.getAttribute('data-staffing-tab'));
    target.focus();
  }
  tabs.forEach(function(tab){
    tab.addEventListener('click',function(){ if(!tab.disabled) activatePanel(tab.getAttribute('data-staffing-tab')); });
    tab.addEventListener('keydown',function(event){
      if(['ArrowRight','ArrowDown','ArrowLeft','ArrowUp','Home','End'].indexOf(event.key)<0) return;
      event.preventDefault();
      moveTabFocus(tab,event.key);
    });
  });
  const maxBasicSections=<?php echo (int) STAFFING_UI_MAX_BASIC_SECTIONS; ?>;
  function sync(){
    const isGel=type.value==='gel';
    const isEveningGel=type.value==='esperino_gel';
    const isEveningGym=type.value==='esperino_gymnasio';
    const isComposite=type.value==='gymnasio_lt';
    const showGym=type.value==='gymnasio'||isEveningGym||isComposite;
    const showGel=isGel||isEveningGel||isComposite;
    gym.hidden=!showGym;
    gel.hidden=!showGel;
    gym.querySelectorAll('input,select').forEach(el=>{ el.disabled=!showGym; });
    gel.querySelectorAll('input,select').forEach(el=>{ el.disabled=!showGel; });
    gym.querySelectorAll('[data-day-gym-only]').forEach(function(panel){
      const visible=showGym&&!isEveningGym;
      panel.hidden=!visible;
      panel.querySelectorAll('input,select').forEach(function(el){el.disabled=!visible;});
      if(!visible && panel.tagName==='DETAILS') panel.open=false;
    });
    gel.querySelectorAll('[data-day-gel-only]').forEach(function(panel){
      const visible=showGel&&!isEveningGel;
      panel.hidden=!visible;
      panel.querySelectorAll('input,select').forEach(function(el){el.disabled=!visible;});
    });
    gel.querySelectorAll('[data-evening-gel-only]').forEach(function(panel){
      panel.hidden=!isEveningGel;
      panel.querySelectorAll('input,select').forEach(function(el){el.disabled=!isEveningGel;});
    });
    syncBasicSectionLimit();
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

  function basicSectionSafeInteger(input){
    const raw=String(input.value==null?'':input.value).trim();
    if(raw==='') return 0;
    if(!/^\d+$/.test(raw)){
      input.value=input.dataset.lastBasicSectionValue||'0';
      return parseInt(input.value||'0',10)||0;
    }
    const digits=raw.replace(/^0+(?=\d)/,'');
    const maxDigits=String(maxBasicSections);
    let value=0;
    if(digits.length>maxDigits.length || (digits.length===maxDigits.length && digits>maxDigits)){
      value=maxBasicSections;
    }else{
      value=parseInt(digits||'0',10)||0;
    }
    input.value=String(value);
    input.dataset.lastBasicSectionValue=String(value);
    return value;
  }
  function syncBasicSectionLimit(changedInput){
    const isComposite=type && type.value==='gymnasio_lt';
    const groups=isComposite ? [['composite',Array.from(document.querySelectorAll('[data-basic-section]'))]] : ['gym','gel'].map(function(kind){return [kind,Array.from(document.querySelectorAll('[data-basic-section="'+kind+'"]'))];});
    groups.forEach(function(entry){
      const kind=entry[0], inputs=entry[1];
      if(!inputs.length) return;
      if(changedInput && inputs.indexOf(changedInput)<0) return;
      let clamped=false;
      if(changedInput){
        const value=basicSectionSafeInteger(changedInput);
        const otherTotal=inputs.reduce(function(sum,input){
          if(input===changedInput) return sum;
          return sum+basicSectionSafeInteger(input);
        },0);
        const allowed=Math.max(0,maxBasicSections-otherTotal);
        if(value>allowed){
          changedInput.value=String(allowed);
          changedInput.dataset.lastBasicSectionValue=String(allowed);
          clamped=true;
        }
      }else{
        let remaining=maxBasicSections;
        inputs.forEach(function(input){
          const value=basicSectionSafeInteger(input);
          const allowed=Math.min(value,remaining);
          if(value!==allowed){
            input.value=String(allowed);
            input.dataset.lastBasicSectionValue=String(allowed);
            clamped=true;
          }
          remaining-=allowed;
        });
      }
      const total=inputs.reduce(function(sum,input){return sum+(parseInt(input.value||'0',10)||0);},0);
      inputs.forEach(function(input){
        const current=parseInt(input.value||'0',10)||0;
        const others=total-current;
        input.max=String(Math.max(0,maxBasicSections-others));
        input.setCustomValidity('');
        input.removeAttribute('aria-invalid');
      });
      const errorTargets=isComposite ? Array.from(document.querySelectorAll('[data-basic-sections-error]')) : Array.from(document.querySelectorAll('[data-basic-sections-error="'+kind+'"]'));
      errorTargets.forEach(function(error){
        error.hidden=!clamped;
        error.textContent=clamped?'Η τιμή περιορίστηκε αυτόματα ώστε το σύνολο των βασικών τμημάτων της σχολικής μονάδας να μην υπερβαίνει τα '+maxBasicSections+'.':'';
      });
    });
    syncSplitMaximums();
    syncLanguageGroupMaximums();
  }
  document.querySelectorAll('[data-basic-section]').forEach(function(input){
    input.addEventListener('keydown',function(event){
      if(['e','E','+','-','.'].indexOf(event.key)>=0) event.preventDefault();
    });
    input.addEventListener('input',function(){syncBasicSectionLimit(input);},true);
  });
  syncBasicSectionLimit();

  const schoolProfileForm=document.getElementById('staffingProfileForm');
  const staffingContextSchool=document.getElementById('staffingContextSchool');
  const staffingContextType=document.getElementById('staffingContextType');
  const staffingContextCode=document.getElementById('staffingContextCode');
  const staffingContextSections=document.getElementById('staffingContextSections');
  const staffingContextState=document.getElementById('staffingContextState');
  const staffingContextAssigned=document.getElementById('staffingContextAssigned');
  const staffingContextUnassigned=document.getElementById('staffingContextUnassigned');
  const staffingContextInitialAllocation=<?php echo $allocationPlan ? 'true' : 'false'; ?>;
  function staffingContextTypeLabel(value){
    const labels={
      gymnasio:'Ημερήσιο Γυμνάσιο',
      esperino_gymnasio:'Εσπερινό Γυμνάσιο',
      gel:'Ημερήσιο ΓΕΛ',
      esperino_gel:'Εσπερινό ΓΕΛ',
      gymnasio_lt:'Γυμνάσιο με Λ.Τ.'
    };
    return labels[value]||value||'—';
  }
  function refreshStaffingContextFromForm(stateText){
    if(!schoolProfileForm) return;
    const nameField=schoolProfileForm.elements.namedItem('school_name');
    const codeField=schoolProfileForm.elements.namedItem('school_code');
    const typeField=schoolProfileForm.elements.namedItem('school_type');
    const name=String(nameField&&nameField.value||'').trim();
    const code=String(codeField&&codeField.value||'').trim();
    const typeValue=String(typeField&&typeField.value||'').trim();
    const sectionTotal=Array.from(schoolProfileForm.querySelectorAll('[data-basic-section]')).reduce(function(sum,input){
      if(input.disabled) return sum;
      return sum+(parseInt(input.value||'0',10)||0);
    },0);
    if(staffingContextSchool) staffingContextSchool.textContent=name||staffingContextTypeLabel(typeValue)||'Δεν έχει φορτωθεί σχολική μονάδα';
    if(staffingContextType) staffingContextType.textContent=staffingContextTypeLabel(typeValue);
    if(staffingContextCode){
      staffingContextCode.hidden=code==='';
      staffingContextCode.innerHTML=code===''?'':'κωδ. <strong>'+escapeHtml(code)+'</strong>';
    }
    if(staffingContextSections){
      staffingContextSections.hidden=sectionTotal<=0;
      staffingContextSections.innerHTML=sectionTotal>0?'<strong>'+sectionTotal+'</strong> τμήματα':'';
    }
    if(staffingContextState && stateText) staffingContextState.textContent=stateText;
  }
  const openSchoolCsv=document.getElementById('openSchoolCsv');
  const schoolCsvFile=document.getElementById('schoolCsvFile');
  const chooseSchoolCsvFile=document.getElementById('chooseSchoolCsvFile');
  const clearSchoolCsvRegistry=document.getElementById('clearSchoolCsvRegistry');
  const schoolCsvPanel=document.getElementById('schoolCsvPanel');
  const closeSchoolCsv=document.getElementById('closeSchoolCsv');
  const schoolCsvMeta=document.getElementById('schoolCsvMeta');
  const schoolCsvPreview=document.getElementById('schoolCsvPreview');
  const schoolCsvStatus=document.getElementById('schoolCsvStatus');
  const schoolCsvActive=document.getElementById('schoolCsvActive');
  const downloadSchoolCsvTemplate=document.getElementById('downloadSchoolCsvTemplate');
  const loadCorfuSchoolDirectory=document.getElementById('loadCorfuSchoolDirectory');
  const downloadCorfuSchoolDirectory=document.getElementById('downloadCorfuSchoolDirectory');
  const schoolRegistrySearch=document.getElementById('schoolRegistrySearch');
  let schoolCsvRegistry=[];
  const schoolCsvStorageKey='education_school_registry_v1';

  function schoolCsvSetStatus(message,kind){
    if(!schoolCsvStatus) return;
    schoolCsvStatus.className='personnel-csv-status'+(kind?' is-'+kind:'');
    schoolCsvStatus.textContent=message||'';
  }
  function persistSchoolCsvRegistry(){
    try{
      if(schoolCsvRegistry.length) sessionStorage.setItem(schoolCsvStorageKey,JSON.stringify(schoolCsvRegistry));
      else sessionStorage.removeItem(schoolCsvStorageKey);
    }catch(e){}
  }
  function restoreSchoolCsvRegistry(){
    try{
      const raw=sessionStorage.getItem(schoolCsvStorageKey);
      if(!raw) return false;
      const parsed=JSON.parse(raw);
      if(!Array.isArray(parsed)) return false;
      const problems=schoolRegistryValidationProblems(parsed);
      if(schoolRegistryHasProblems(problems)){
        sessionStorage.removeItem(schoolCsvStorageKey);
        schoolCsvRegistry=[];
        return false;
      }
      schoolCsvRegistry=parsed;
      return schoolCsvRegistry.length>0;
    }catch(e){return false;}
  }
  function schoolCsvKnownPlaceholder(typeValue){
    return !!(window.EducationSchoolCsv && Array.isArray(window.EducationSchoolCsv.placeholderTypes) && window.EducationSchoolCsv.placeholderTypes.indexOf(typeValue)>=0);
  }
  function schoolCsvTotalSections(record){
    let total=['general_a','general_b','general_c'].reduce(function(sum,key){return sum+(parseInt(record[key]||0,10)||0);},0);
    if(record && record.school_type==='gymnasio_lt') total+=['lt_general_a','lt_general_b','lt_general_c'].reduce(function(sum,key){return sum+(parseInt(record[key]||0,10)||0);},0);
    return total;
  }
  function schoolRegistryValidationProblems(records){
    const seenIds=new Map(), seenCodes=new Map(), duplicateIds=new Set(), duplicateCodes=new Set(), oversized=[];
    (records||[]).forEach(function(record,index){
      const id=String(record&&record.school_id||'').trim();
      const code=String(record&&record.school_code||'').trim();
      if(id){ if(seenIds.has(id)) duplicateIds.add(id); else seenIds.set(id,index); }
      if(code){ if(seenCodes.has(code)) duplicateCodes.add(code); else seenCodes.set(code,index); }
      const total=schoolCsvTotalSections(record||{});
      if(total>maxBasicSections) oversized.push({index:index,name:String(record&&record.school_name||id||code||('Σχολείο '+(index+1))),total:total});
    });
    return {duplicateIds:Array.from(duplicateIds),duplicateCodes:Array.from(duplicateCodes),oversized:oversized};
  }
  function schoolRegistryProblemsMessage(problems){
    const messages=[];
    if(problems.duplicateIds.length) messages.push('διπλό αναγνωριστικό σχολείου: '+problems.duplicateIds.join(', '));
    if(problems.duplicateCodes.length) messages.push('διπλό κωδικό Υπουργείου / myschool: '+problems.duplicateCodes.join(', '));
    if(problems.oversized.length) messages.push('υπέρβαση του ορίου των '+maxBasicSections+' βασικών τμημάτων: '+problems.oversized.map(function(item){return item.name+' ('+item.total+')';}).join(', '));
    return messages.join(' · ');
  }
  function schoolRegistryHasProblems(problems){
    return !!(problems.duplicateIds.length || problems.duplicateCodes.length || problems.oversized.length);
  }
  function normalizeSchoolRegistrySearch(value){
    return String(value==null?'':value).toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  }
  function renderSchoolCsvRegistry(){
    if(!schoolCsvPreview) return;
    schoolCsvPreview.innerHTML='';
    if(!schoolCsvRegistry.length){
      const empty=document.createElement('div');
      empty.className='empty-personnel';
      empty.textContent='Το μητρώο δεν περιέχει σχολικές μονάδες.';
      schoolCsvPreview.appendChild(empty);
      return;
    }
    const query=normalizeSchoolRegistrySearch(schoolRegistrySearch?schoolRegistrySearch.value:'').trim();
    const table=document.createElement('table');
    table.className='school-registry-table';
    const thead=document.createElement('thead');
    thead.innerHTML='<tr><th>Σχολική μονάδα</th><th>Κωδικός</th><th>Είδος</th><th>Α΄</th><th>Β΄</th><th>Γ΄</th><th title="Σύνολο τμημάτων" aria-label="Σύνολο τμημάτων">Σ</th><th>Κατάσταση</th><th></th></tr>';
    table.appendChild(thead);
    const tbody=document.createElement('tbody');
    let visibleCount=0;
    schoolCsvRegistry.forEach(function(record,index){
      const hay=normalizeSchoolRegistrySearch([record.school_name,record.school_code,record.school_type_label,record.school_type,record.school_address].join(' '));
      if(query!=='' && !hay.includes(query)) return;
      visibleCount++;
      const tr=document.createElement('tr');
      const displaySchoolType=(window.EducationSchoolCsv&&typeof window.EducationSchoolCsv.typeLabel==='function')?window.EducationSchoolCsv.typeLabel(record.school_type):(record.school_type_label||record.school_type);
      const knownSoon=schoolCsvKnownPlaceholder(record.school_type);
      let status='';
      if(record.supported){
        if(record.directory_only) status='Ταυτότητα μόνο · συμπλήρωσε τμήματα';
        else if(record.pending_fields) status='Δομικά στοιχεία 2026-27 · θέλει συμπλήρωση';
        else status='Έτοιμο για φόρτωση';
      }else status=knownSoon?'Προσεχώς':'Μη αναγνωρισμένο είδος';
      const statusClass=record.supported?'':(knownSoon?'school-type-soon':'school-type-unknown');
      const schoolTd=document.createElement('td');
      schoolTd.textContent=String(record.school_name||record.school_id||('Σχολείο '+(index+1)));
      if(record.school_address){
        const address=document.createElement('small');
        address.className='school-address';
        address.textContent=record.school_address;
        schoolTd.appendChild(address);
      }
      tr.appendChild(schoolTd);
      [record.school_code||'—',displaySchoolType,record.general_a,record.general_b,record.general_c,schoolCsvTotalSections(record)].forEach(function(value){const td=document.createElement('td');td.textContent=String(value==null?'':value);tr.appendChild(td);});
      const statusTd=document.createElement('td');
      statusTd.className=statusClass;
      statusTd.textContent=status;
      if(record.completeness_status || record.pending_fields){
        statusTd.title=[record.completeness_status,record.pending_fields?('Εκκρεμούν: '+record.pending_fields):''].filter(Boolean).join(' · ');
      }
      tr.appendChild(statusTd);
      const actionTd=document.createElement('td');
      const button=document.createElement('button');
      button.type='button';
      button.className='edu-btn-secondary school-load-btn';
      button.textContent=record.supported?'Φόρτωση':'Ανενεργό';
      button.disabled=!record.supported;
      button.dataset.schoolRegistryIndex=String(index);
      actionTd.appendChild(button);
      tr.appendChild(actionTd);
      tbody.appendChild(tr);
    });
    if(visibleCount===0){
      const tr=document.createElement('tr');
      const td=document.createElement('td');
      td.colSpan=9;
      td.className='empty-personnel';
      td.textContent='Δεν βρέθηκε σχολική μονάδα με αυτό το κριτήριο.';
      tr.appendChild(td);
      tbody.appendChild(tr);
    }
    table.appendChild(tbody);
    schoolCsvPreview.appendChild(table);
  }
  function loadSchoolRegistryRecord(record){
    if(!record || !record.supported || !schoolProfileForm || !window.EducationSchoolCsv) return;
    if(schoolCsvTotalSections(record)>maxBasicSections){
      schoolCsvSetStatus('Δεν φορτώθηκε το «'+(record.school_name||record.school_id)+'»: το σύνολο των βασικών τμημάτων της σχολικής μονάδας υπερβαίνει τα '+maxBasicSections+' βασικά τμήματα.','error');
      return;
    }
    const values=window.EducationSchoolCsv.schoolToFormValues(record);
    Object.keys(values).forEach(function(name){
      const field=schoolProfileForm.elements.namedItem(name);
      if(field) field.value=values[name];
    });
    sync();
    syncSplitMaximums();
    syncLanguageGroupMaximums();
    syncBasicSectionLimit();
    const techPanel=document.getElementById('technologyInformaticsPanel');
    if(techPanel){
      techPanel.open=(record.school_type==='gymnasio' || record.school_type==='gymnasio_lt') && ['tech_split_a','tech_split_b','tech_split_c'].some(function(key){return (parseInt(record[key]||0,10)||0)>0;});
    }
    const ethicsPanel=document.getElementById('ethicsPanel')||document.getElementById('ethicsPanelGym');
    if(ethicsPanel){
      ethicsPanel.open=['a','b','c'].some(function(g){return record['ethics_'+g+'_exempt']!=='' || record['ethics_'+g+'_timely']!=='' || record['ethics_'+g+'_equivalent']!=='';});
    }
    const ethicsPanelLt=document.getElementById('ethicsPanelLt');
    if(ethicsPanelLt){
      ethicsPanelLt.open=['a','b','c'].some(function(g){return record['lt_ethics_'+g+'_exempt']!=='' || record['lt_ethics_'+g+'_timely']!=='' || record['lt_ethics_'+g+'_equivalent']!=='';});
    }
    if(schoolCsvActive){
      schoolCsvActive.hidden=false;
      const displaySchoolType=(window.EducationSchoolCsv&&typeof window.EducationSchoolCsv.typeLabel==='function')?window.EducationSchoolCsv.typeLabel(record.school_type):(record.school_type_label||record.school_type);
      const profileYear=record.school_year?' · '+escapeHtml(record.school_year):'';
      const pending=record.pending_fields?' <br><strong>Προς συμπλήρωση:</strong> '+escapeHtml(record.pending_fields)+'.':'';
      schoolCsvActive.innerHTML='<strong>Τρέχουσα εγγραφή μητρώου:</strong> '+escapeHtml(record.school_name||record.school_id)+(record.school_code?' · κωδ. '+escapeHtml(record.school_code):'')+' · '+escapeHtml(displaySchoolType)+profileYear+(record.school_address?' · '+escapeHtml(record.school_address):'')+'. Τα διαθέσιμα στοιχεία τμημάτων/ομάδων φορτώθηκαν στη φόρμα χωρίς server request.'+pending+' Πάτησε «Υπολόγισε διδακτικές ανάγκες» όταν θέλεις νέο υπολογισμό.';
    }
    schoolCsvSetStatus('Φορτώθηκε το «'+(record.school_name||record.school_id)+'» με τα διαθέσιμα δομικά στοιχεία του 2026-2027.'+(record.pending_fields?' Συμπλήρωσε τα πεδία που παραμένουν εκκρεμή.':'') ,'success');
    refreshStaffingContextFromForm('Χρειάζεται υπολογισμός');
    markSchoolProfileDirty();
  }
  function escapeHtml(value){
    return String(value==null?'':value).replace(/[&<>"']/g,function(ch){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch];});
  }
  function schoolCsvImporterSupportsRegistry(){
    return !!(window.EducationSchoolCsv && window.EducationSchoolCsv.supportsMultipleSchools===true && window.EducationSchoolCsv.registrySchemaVersion==='school_registry_v1');
  }
  function decodeSchoolCsvBuffer(buffer){
    let text='';
    try{text=new TextDecoder('utf-8',{fatal:false}).decode(buffer);}catch(e){text='';}
    if(text.indexOf('\uFFFD')>=0){
      try{const alt=new TextDecoder('windows-1253').decode(buffer); if(alt && alt.indexOf('\uFFFD')<0) text=alt;}catch(e){}
    }
    return text.replace(/^\uFEFF/,'');
  }
  function parseSchoolCsvFile(file){
    if(!file || !schoolCsvImporterSupportsRegistry()){
      schoolCsvSetStatus('Δεν φορτώθηκε η έκδοση του CSV importer που υποστηρίζει το school_registry_v1. Κάνε ανανέωση της σελίδας και δοκίμασε ξανά.','error');
      return;
    }
    const reader=new FileReader();
    reader.onload=function(){
      try{
        const parsed=window.EducationSchoolCsv.parse(decodeSchoolCsvBuffer(reader.result));
        const mapping=window.EducationSchoolCsv.autoMap(parsed.headers);
        if(!mapping.school_name || !mapping.school_type){
          schoolCsvRegistry=[];
          renderSchoolCsvRegistry();
          schoolCsvSetStatus('Δεν βρέθηκαν οι απαιτούμενες στήλες «Ονομασία σχολείου» και «Είδος σχολείου». Χρησιμοποίησε το πρότυπο school_registry_v1 ή αντίστοιχες ονομασίες στηλών.','error');
          return;
        }
        const identityOnly=!mapping.general_a && !mapping.general_b && !mapping.general_c;
        schoolCsvRegistry=parsed.rows.map(function(row,index){const record=window.EducationSchoolCsv.rowToSchool(row,mapping,index);record.directory_only=identityOnly;return record;});
        const registryProblems=schoolRegistryValidationProblems(schoolCsvRegistry);
        if(schoolRegistryHasProblems(registryProblems)){
          schoolCsvRegistry=[];
          renderSchoolCsvRegistry();
          schoolCsvSetStatus('Το CSV απορρίφθηκε: '+schoolRegistryProblemsMessage(registryProblems)+'. Κάθε σχολική μονάδα πρέπει να έχει μοναδικό αναγνωριστικό και μοναδικό πραγματικό κωδικό, ενώ το σύνολο Α΄ + Β΄ + Γ΄ δεν μπορεί να υπερβαίνει τα '+maxBasicSections+' βασικά τμήματα.','error');
          return;
        }
        persistSchoolCsvRegistry();
        renderSchoolCsvRegistry();
        const supported=schoolCsvRegistry.filter(function(r){return r.supported;}).length;
        const soon=schoolCsvRegistry.filter(function(r){return !r.supported && schoolCsvKnownPlaceholder(r.school_type);}).length;
        if(schoolCsvMeta) schoolCsvMeta.textContent=file.name+' · '+schoolCsvRegistry.length+' σχολικές μονάδες · delimiter '+(parsed.delimiter==='\t'?'tab':parsed.delimiter);
        schoolCsvSetStatus('Διαβάστηκαν '+schoolCsvRegistry.length+' σχολικές μονάδες: '+supported+' μπορούν να φορτωθούν τώρα'+(soon?' και '+soon+' ανήκουν σε προσωρινά ανενεργούς τύπους.':'.'),'success');
      }catch(error){
        schoolCsvRegistry=[];
        renderSchoolCsvRegistry();
        schoolCsvSetStatus('Αποτυχία ανάγνωσης CSV: '+(error&&error.message?error.message:'άγνωστο σφάλμα')+'.','error');
      }
    };
    reader.onerror=function(){schoolCsvSetStatus('Δεν ήταν δυνατή η ανάγνωση του αρχείου CSV.','error');};
    reader.readAsArrayBuffer(file);
  }
  function schoolRegistryIdentity(record){
    return String((record&&record.school_code)||(record&&record.school_id)||'').trim();
  }
  function loadBuiltinSchoolDirectory(directoryId){
    if(!schoolCsvImporterSupportsRegistry() || typeof window.EducationSchoolCsv.getBuiltinDirectory!=='function'){
      schoolCsvSetStatus('Δεν είναι διαθέσιμος ο ενσωματωμένος κατάλογος σχολικών μονάδων. Κάνε ανανέωση της σελίδας και δοκίμασε ξανά.','error');
      return;
    }
    const directory=window.EducationSchoolCsv.getBuiltinDirectory(directoryId);
    if(!directory || !Array.isArray(directory.schools)){
      schoolCsvSetStatus('Δεν βρέθηκε ο ζητούμενος ενσωματωμένος κατάλογος.','error');
      return;
    }
    const existingByIdentity=new Map();
    schoolCsvRegistry.forEach(function(record){
      const id=schoolRegistryIdentity(record);
      if(id) existingByIdentity.set(id,record);
    });
    const used=new Set();
    const merged=directory.schools.map(function(record){
      const id=schoolRegistryIdentity(record);
      const existing=id?existingByIdentity.get(id):null;
      if(!existing) return record;
      used.add(id);
      /* A previous built-in/identity-only registry must refresh to the richer 2026-2027 dataset.
         A genuinely imported populated CSV keeps the user's values and only inherits missing directory metadata. */
      if(existing.directory_id===directoryId || existing.directory_only===true || schoolCsvTotalSections(existing)===0){
        const refreshed=Object.assign({},existing,record);
        refreshed.school_id=existing.school_id||record.school_id;
        refreshed.directory_id=directoryId;
        return refreshed;
      }
      const combined=Object.assign({},record,existing);
      combined.school_id=existing.school_id||record.school_id;
      combined.school_code=existing.school_code||record.school_code;
      combined.school_name=existing.school_name||record.school_name;
      combined.school_address=existing.school_address||record.school_address;
      combined.school_type=record.school_type;
      combined.school_type_label=record.school_type_label;
      combined.supported=record.supported;
      combined.directory_only=false;
      combined.directory_id=directoryId;
      combined.registry_dataset_version=directory.dataset_version||record.registry_dataset_version||'';
      return combined;
    });
    schoolCsvRegistry.forEach(function(record){
      const id=schoolRegistryIdentity(record);
      if(!id || !used.has(id) && !directory.schools.some(function(item){return schoolRegistryIdentity(item)===id;})) merged.push(record);
    });
    schoolCsvRegistry=merged;
    persistSchoolCsvRegistry();
    if(schoolRegistrySearch) schoolRegistrySearch.value='';
    renderSchoolCsvRegistry();
    if(schoolCsvPanel) schoolCsvPanel.hidden=false;
    const supported=schoolCsvRegistry.filter(function(r){return r.supported;}).length;
    const withSections=directory.schools.filter(function(r){return schoolCsvTotalSections(r)>0;}).length;
    if(schoolCsvMeta) schoolCsvMeta.textContent=directory.label+' · '+directory.schools.length+' σχολικές μονάδες · πλήρες μητρώο 2026-2027 · πραγματικοί κωδικοί · ανάκτηση '+directory.retrieved_on;
    schoolCsvSetStatus('Φορτώθηκε ο εμπλουτισμένος κατάλογος '+directory.label+' με '+directory.schools.length+' σχολικές μονάδες. Σε '+withSections+' μονάδες υπάρχουν ήδη διαθέσιμα στοιχεία βασικών τμημάτων και, όπου προβλέπονται, χωρισμών, ομάδων προσανατολισμού και πραγματικών ομάδων 2ης ξένης γλώσσας από myschool stat3_10. '+supported+' εγγραφές είναι τύπων που υποστηρίζονται σήμερα· τα υπόλοιπα μη διαθέσιμα πεδία παραμένουν κενά για συμπλήρωση και οι υπόλοιποι τύποι παραμένουν προσωρινά ανενεργοί.','success');
  }
  function openSchoolCsvPicker(){
    if(!schoolCsvFile) return;
    schoolCsvFile.value='';
    schoolCsvFile.click();
  }
  if(openSchoolCsv && schoolCsvPanel){
    openSchoolCsv.addEventListener('click',function(){schoolCsvPanel.hidden=false;if(!schoolCsvRegistry.length) openSchoolCsvPicker();});
  }
  if(chooseSchoolCsvFile) chooseSchoolCsvFile.addEventListener('click',openSchoolCsvPicker);
  if(loadCorfuSchoolDirectory) loadCorfuSchoolDirectory.addEventListener('click',function(){loadBuiltinSchoolDirectory('dde_corfu_2026');});
  if(schoolRegistrySearch) schoolRegistrySearch.addEventListener('input',renderSchoolCsvRegistry);
  if(schoolCsvFile){
    schoolCsvFile.addEventListener('change',function(){if(schoolCsvFile.files && schoolCsvFile.files[0]) parseSchoolCsvFile(schoolCsvFile.files[0]);});
  }
  if(clearSchoolCsvRegistry){
    clearSchoolCsvRegistry.addEventListener('click',function(){
      schoolCsvRegistry=[];
      persistSchoolCsvRegistry();
      renderSchoolCsvRegistry();
      if(schoolCsvMeta) schoolCsvMeta.textContent='Δεν υπάρχει ενεργό μητρώο σχολικών μονάδων.';
      schoolCsvSetStatus('Το προσωρινό μητρώο σχολικών μονάδων καθαρίστηκε.','');
      if(schoolCsvActive) schoolCsvActive.hidden=true;
    });
  }
  if(closeSchoolCsv && schoolCsvPanel){closeSchoolCsv.addEventListener('click',function(){schoolCsvPanel.hidden=true;});}
  if(schoolCsvPreview){
    schoolCsvPreview.addEventListener('click',function(event){
      const button=event.target.closest('[data-school-registry-index]');
      if(!button) return;
      const index=parseInt(button.dataset.schoolRegistryIndex||'-1',10);
      if(index>=0 && schoolCsvRegistry[index]) loadSchoolRegistryRecord(schoolCsvRegistry[index]);
    });
  }
  if(restoreSchoolCsvRegistry()){
    renderSchoolCsvRegistry();
    if(schoolCsvMeta) schoolCsvMeta.textContent='Προσωρινό μητρώο browser · '+schoolCsvRegistry.length+' σχολικές μονάδες · school_registry_v1';
    schoolCsvSetStatus('Το μητρώο αποκαταστάθηκε από την τρέχουσα καρτέλα του browser. Μπορείς να φορτώσεις άλλο σχολείο χωρίς να επιλέξεις ξανά το CSV.','success');
  }

  function csvSpreadsheetSafeText(value){
    const text=String(value==null?'':value);
    if(typeof value==='number' || typeof value==='bigint') return text;
    const probe=text.replace(/^[\u0000-\u0020]+/,'');
    if(/^[=+\-@]/.test(probe) && !/^[+\-]?\d+(?:[.,]\d+)?$/.test(probe)) return "'"+text;
    return text;
  }
  function schoolCsvEscape(value){
    const text=csvSpreadsheetSafeText(value);
    return /[;"\r\n]/.test(text)?'"'+text.replace(/"/g,'""')+'"':text;
  }
  function downloadSchoolRegistryTemplate(){
    const headers=['Έκδοση μητρώου','Αναγνωριστικό σχολείου','Κωδικός Υπουργείου','Ονομασία σχολείου','Είδος σχολείου','Διεύθυνση σχολείου','Α τμήματα','Β τμήματα','Γ τμήματα','Α Γαλλικά ομάδες','Α Γερμανικά ομάδες','Α Ιταλικά ομάδες','Β Γαλλικά ομάδες','Β Γερμανικά ομάδες','Β Ιταλικά ομάδες','Γ Γαλλικά ομάδες','Γ Γερμανικά ομάδες','Γ Ιταλικά ομάδες','Α τμήματα άνω 21','Β τμήματα άνω 21','Γ τμήματα άνω 21','Β ομάδες Ανθρωπιστικών','Β ομάδες Θετικών','Γ ομάδες Ανθρωπιστικών','Γ ομάδες Θετικών Υγείας','Γ ομάδες Οικονομίας Πληροφορικής','Γ Μαθηματικά 2ου πεδίου','Γ Βιολογία 3ου πεδίου','Γ Μαθηματικά Γενικής Παιδείας','Γ Ιστορία Γενικής Παιδείας','Α απαλλασσόμενοι','Α Ηθική εντός 5ης','Α τμήματα Ηθικής','Β απαλλασσόμενοι','Β Ηθική εντός 5ης','Β τμήματα Ηθικής','Γ απαλλασσόμενοι','Γ Ηθική εντός 5ης','Γ τμήματα Ηθικής','ΛΤ Α Γενικής','ΛΤ Β Γενικής','ΛΤ Γ Γενικής','ΛΤ Α Γαλλικά ομάδες','ΛΤ Α Γερμανικά ομάδες','ΛΤ Β Γαλλικά ομάδες','ΛΤ Β Γερμανικά ομάδες','ΛΤ Β Ανθρωπιστικών','ΛΤ Β Θετικών','ΛΤ Γ Ανθρωπιστικών','ΛΤ Γ Θετικών Υγείας','ΛΤ Γ Οικονομίας Πληροφορικής','ΛΤ Γ Μαθηματικά 2ου πεδίου','ΛΤ Γ Βιολογία 3ου πεδίου','ΛΤ Γ Μαθηματικά Γενικής Παιδείας','ΛΤ Γ Ιστορία Γενικής Παιδείας','ΛΤ Α απαλλασσόμενοι','ΛΤ Α Ηθική εντός 5ης','ΛΤ Α τμήματα Ηθικής','ΛΤ Β απαλλασσόμενοι','ΛΤ Β Ηθική εντός 5ης','ΛΤ Β τμήματα Ηθικής','ΛΤ Γ απαλλασσόμενοι','ΛΤ Γ Ηθική εντός 5ης','ΛΤ Γ τμήματα Ηθικής'];
    const blank=new Array(headers.length).fill('');
    function exampleRow(values){ const row=blank.slice(); Object.keys(values).forEach(function(key){ const i=headers.indexOf(key); if(i>=0) row[i]=values[key]; }); return row; }
    const gym=exampleRow({'Έκδοση μητρώου':'school_registry_v1','Αναγνωριστικό σχολείου':'school-001','Κωδικός Υπουργείου':'','Ονομασία σχολείου':'Παράδειγμα Γυμνασίου','Είδος σχολείου':'Ημερήσιο Γυμνάσιο','Α τμήματα':'2','Β τμήματα':'2','Γ τμήματα':'2','Α Γαλλικά ομάδες':'1','Α Γερμανικά ομάδες':'1','Β Γαλλικά ομάδες':'1','Β Γερμανικά ομάδες':'1','Γ Γαλλικά ομάδες':'1','Γ Γερμανικά ομάδες':'1'});
    const gelRow=exampleRow({'Έκδοση μητρώου':'school_registry_v1','Αναγνωριστικό σχολείου':'school-002','Κωδικός Υπουργείου':'','Ονομασία σχολείου':'Παράδειγμα ΓΕΛ','Είδος σχολείου':'Ημερήσιο Γενικό Λύκειο','Α τμήματα':'3','Β τμήματα':'2','Γ τμήματα':'3','Α Γαλλικά ομάδες':'1','Α Γερμανικά ομάδες':'1','Β Γαλλικά ομάδες':'1','Β Γερμανικά ομάδες':'1','Β ομάδες Ανθρωπιστικών':'1','Β ομάδες Θετικών':'1','Γ ομάδες Ανθρωπιστικών':'1','Γ ομάδες Θετικών Υγείας':'2','Γ ομάδες Οικονομίας Πληροφορικής':'1','Γ Μαθηματικά 2ου πεδίου':'1','Γ Βιολογία 3ου πεδίου':'1'});
    const composite=exampleRow({'Έκδοση μητρώου':'school_registry_v1','Αναγνωριστικό σχολείου':'school-003','Ονομασία σχολείου':'Παράδειγμα Γυμνασίου με Λ.Τ.','Είδος σχολείου':'Γυμνάσιο με Λ.Τ.','Α τμήματα':'2','Β τμήματα':'2','Γ τμήματα':'1','Α Γαλλικά ομάδες':'1','Α Γερμανικά ομάδες':'1','Β Γαλλικά ομάδες':'1','Β Γερμανικά ομάδες':'1','Γ Γαλλικά ομάδες':'1','ΛΤ Α Γενικής':'1','ΛΤ Β Γενικής':'1','ΛΤ Γ Γενικής':'1','ΛΤ Α Γαλλικά ομάδες':'1','ΛΤ Β Γερμανικά ομάδες':'1','ΛΤ Β Ανθρωπιστικών':'1','ΛΤ Β Θετικών':'1','ΛΤ Γ Ανθρωπιστικών':'1','ΛΤ Γ Θετικών Υγείας':'1','ΛΤ Γ Μαθηματικά 2ου πεδίου':'1','ΛΤ Γ Μαθηματικά Γενικής Παιδείας':'1','ΛΤ Γ Ιστορία Γενικής Παιδείας':'1'});
    const csv='\uFEFF'+[headers,gym,gelRow,composite].map(function(row){return row.map(schoolCsvEscape).join(';');}).join('\r\n');
    const blob=new Blob([csv],{type:'text/csv;charset=utf-8'});
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');
    a.href=url; a.download='school_registry_v1-template.csv';
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(function(){URL.revokeObjectURL(url);},0);
  }
  function downloadBuiltinSchoolDirectory(directoryId){
    if(!window.EducationSchoolCsv || typeof window.EducationSchoolCsv.getBuiltinDirectory!=='function') return;
    const directory=window.EducationSchoolCsv.getBuiltinDirectory(directoryId);
    if(!directory) return;
    let csv='';
    if(directory.raw_csv){
      csv='\uFEFF'+String(directory.raw_csv).replace(/^\uFEFF/,'');
    }else{
      const headers=['Έκδοση μητρώου','Αναγνωριστικό σχολείου','Κωδικός Υπουργείου','Ονομασία σχολείου','Είδος σχολείου','Διεύθυνση σχολείου'];
      const rows=directory.schools.map(function(record){return ['school_registry_v1',record.school_id||record.school_code,record.school_code,record.school_name,record.school_type_label||record.school_type,record.school_address||''];});
      csv='\uFEFF'+[headers].concat(rows).map(function(row){return row.map(schoolCsvEscape).join(';');}).join('\r\n');
    }
    const blob=new Blob([csv],{type:'text/csv;charset=utf-8'});
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');
    a.href=url; a.download=directory.filename||'school_registry_v1-dde-kerkyras.csv';
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(function(){URL.revokeObjectURL(url);},0);
  }
  if(downloadCorfuSchoolDirectory){downloadCorfuSchoolDirectory.addEventListener('click',function(){downloadBuiltinSchoolDirectory('dde_corfu_2026');});}
  if(downloadSchoolCsvTemplate){downloadSchoolCsvTemplate.addEventListener('click',downloadSchoolRegistryTemplate);}

  if(reset){ reset.addEventListener('click',function(){ setTimeout(function(){ type.value='gymnasio'; sync(); syncSplitMaximums(); syncLanguageGroupMaximums(); syncBasicSectionLimit(); if(schoolCsvActive) schoolCsvActive.hidden=true; },0); }); }
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

  const schoolProfileHasCalculatedResults=<?php echo $calculationAvailable ? 'true' : 'false'; ?>;
  const schoolProfileStaleNotice=document.getElementById('schoolProfileStaleNotice');
  function markSchoolProfileDirty(){
    refreshStaffingContextFromForm(schoolProfileHasCalculatedResults?'Αλλαγμένα στοιχεία · υπολόγισε ξανά':'Χρειάζεται υπολογισμός');
    if(!schoolProfileHasCalculatedResults) return;
    ['results','personnel','allocation','vacancies','specialties'].forEach(function(name){
      const tab=document.querySelector('[data-staffing-tab="'+name+'"]');
      if(!tab) return;
      tab.disabled=true;
      tab.title='Τα στοιχεία της σχολικής μονάδας άλλαξαν. Υπολόγισε ξανά τις διδακτικές ανάγκες.';
    });
    if(schoolProfileStaleNotice) schoolProfileStaleNotice.hidden=false;
    activatePanel('school');
  }
  if(schoolProfileForm){
    schoolProfileForm.addEventListener('input',function(event){
      if(event.target && event.target.matches('input[name]:not([type="hidden"]), select[name]')) markSchoolProfileDirty();
    });
    schoolProfileForm.addEventListener('change',function(event){
      if(event.target && event.target.matches('input[name]:not([type="hidden"]), select[name]')) markSchoolProfileDirty();
    });
    schoolProfileForm.addEventListener('reset',function(){ setTimeout(markSchoolProfileDirty,0); });
  }

  const personnelList=document.getElementById('personnelList');
  const allocationTab=document.querySelector('[data-staffing-tab="allocation"]');
  const vacanciesTab=document.querySelector('[data-staffing-tab="vacancies"]');
  const specialtiesTab=document.querySelector('[data-staffing-tab="specialties"]');
  function markPersonnelDirty(){
    if(allocationTab){ allocationTab.disabled=true; allocationTab.title='Υπολόγισε ξανά τα ωράρια προσωπικού πριν από νέα κατανομή.'; }
    if(vacanciesTab){ vacanciesTab.disabled=true; vacanciesTab.title='Υπολόγισε ξανά τα ωράρια προσωπικού πριν από τον έλεγχο κενών.'; }
    if(specialtiesTab){ specialtiesTab.disabled=true; specialtiesTab.title='Υπολόγισε ξανά τα ωράρια προσωπικού πριν από τη δήλωση κενών / πλεονασμάτων ειδικοτήτων.'; }
  }
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
  const openMySchoolStaff=document.getElementById('openMySchoolStaff');
  const mySchoolStaffFile=document.getElementById('mySchoolStaffFile');
  const mySchoolStaffPanel=document.getElementById('mySchoolStaffPanel');
  const mySchoolStaffMeta=document.getElementById('mySchoolStaffMeta');
  const mySchoolStaffStatus=document.getElementById('mySchoolStaffStatus');
  const closeMySchoolStaff=document.getElementById('closeMySchoolStaff');
  const pickMySchoolStaffFile=document.getElementById('pickMySchoolStaffFile');
  const loadMySchoolStaffForSchool=document.getElementById('loadMySchoolStaffForSchool');
  const downloadCleanMySchoolStaff=document.getElementById('downloadCleanMySchoolStaff');
  const clearMySchoolStaff=document.getElementById('clearMySchoolStaff');
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
    {key:'assigned_external_hours',label:'Ώρες σε άλλη μονάδα'},
    {key:'obligation_source',label:'Πηγή ωραρίου'},
    {key:'source_base_required_hours',label:'Υ.Ω. πηγής'},
    {key:'source_reduction_hours',label:'Μείωση πηγής'},
    {key:'source_hours_at_unit',label:'Ώρες Υ.Ω. στον φορέα'}
  ];

  function schoolGeneralSectionCount(){
    const typeEl=document.getElementById('school_type');
    const schoolType=typeEl ? typeEl.value : 'gymnasio';
    let prefixes=['gym_general_'];
    if(schoolType==='gel' || schoolType==='esperino_gel') prefixes=['gel_general_'];
    else if(schoolType==='gymnasio_lt') prefixes=['gym_general_','gel_general_'];
    return prefixes.reduce(function(total,prefix){
      return total+['a','b','c'].reduce(function(subtotal,suffix){
        const input=document.querySelector('[name="'+prefix+suffix+'"]');
        return subtotal+Math.max(0,parseInt(input&&input.value?input.value:'0',10)||0);
      },0);
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
    const obligationSourceEl=row.querySelector('.personnel-obligation-source');
    const obligationSource=obligationSourceEl?String(obligationSourceEl.value||'').trim():'';
    const mySchoolSource=obligationSource==='myschool_stat4_8';
    if(serviceWrap) serviceWrap.hidden=!managementRole||mySchoolSource;
    if(directorBandWrap) directorBandWrap.hidden=roleValue!=='director'||mySchoolSource;
    if(requiredInput){
      if(mySchoolSource){
        requiredInput.readOnly=true;
        requiredInput.required=false;
        requiredInput.setCustomValidity('');
      }else if(managementRole){
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
    if(mySchoolSource){
      const raw=requiredInput?String(requiredInput.value||'').trim():'';
      const parsedRaw=/^\d+$/.test(raw)?parseInt(raw,10):NaN;
      if(raw===''||!Number.isFinite(parsedRaw)||parsedRaw<1||parsedRaw>35||(code.indexOf('ΠΕ')===0&&parsedRaw>23)){
        if(availableEl) availableEl.textContent='—';
        if(rule) rule.textContent='';
        if(error){error.hidden=false;error.textContent='Το ωράριο του myschool δεν είναι έγκυρο για τον συγκεκριμένο κλάδο.';}
        return;
      }
      required=parsedRaw;
      const baseEl=row.querySelector('.personnel-source-base-required');
      const reductionEl=row.querySelector('.personnel-source-reduction');
      const atUnitEl=row.querySelector('.personnel-source-at-unit');
      const base=Math.max(0,parseInt(baseEl&&baseEl.value?baseEl.value:'0',10)||0);
      const reduction=Math.max(0,parseInt(reductionEl&&reductionEl.value?reductionEl.value:'0',10)||0);
      const atUnit=Math.max(0,parseInt(atUnitEl&&atUnitEl.value?atUnitEl.value:'0',10)||0);
      if(rule) rule.textContent='myschool stat4_8 · Υ.Ω. '+base+' − μείωση '+reduction+' = '+required+' ώρες · ώρες Υ.Ω. στον φορέα '+atUnit+'.';
    }else if(managementRole){
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

    if(external) external.readOnly=mySchoolSource;
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
      const idInput=row.querySelector('input[name="personnel_person_id[]"]');
      const personId=idInput?idInput.value:'';
      const nameInput=row.querySelector('.personnel-name');
      const specialtyInput=row.querySelector('.personnel-specialty');
      const personLabel=((specialtyInput&&specialtyInput.value?specialtyInput.value+' · ':'')+(nameInput&&nameInput.value?nameInput.value:'τον/την εκπαιδευτικό')).trim();
      let linkedAllocationRows=[];
      if(personId){
        linkedAllocationRows=Array.from(document.querySelectorAll('[data-allocation-row]')).filter(function(allocationRow){
          const personSelect=allocationRow.querySelector('.allocation-person');
          return personSelect&&personSelect.value===personId;
        });
      }
      let message='Να αφαιρεθεί '+personLabel+' από το προσωπικό της σχολικής μονάδας;';
      if(linkedAllocationRows.length){
        message+='\n\nΥπάρχουν '+linkedAllocationRows.length+' γραμμές κατανομής που έχουν ανατεθεί σε αυτόν/ήν. Θα αφαιρεθούν και αυτές.';
      }
      if(!window.confirm(message)) return;
      linkedAllocationRows.forEach(function(allocationRow){allocationRow.remove();});
      row.remove();
      refreshDirectorRoleConstraints(); markPersonnelDirty(); ensurePersonnelEmptyState();
      if(typeof updateAllocationSummary==='function') updateAllocationSummary();
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
    const sourceEl=row.querySelector('.personnel-obligation-source'); if(sourceEl) sourceEl.value=person.obligation_source||person.source_kind||'';
    const sourceBaseEl=row.querySelector('.personnel-source-base-required'); if(sourceBaseEl) sourceBaseEl.value=person.source_base_required_hours==null?'':String(person.source_base_required_hours);
    const sourceReductionEl=row.querySelector('.personnel-source-reduction'); if(sourceReductionEl) sourceReductionEl.value=person.source_reduction_hours==null?'':String(person.source_reduction_hours);
    const sourceAtUnitEl=row.querySelector('.personnel-source-at-unit'); if(sourceAtUnitEl) sourceAtUnitEl.value=person.source_hours_at_unit==null?'':String(person.source_hours_at_unit);
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
  let mySchoolStaffRegistry=(window.EducationMySchoolStaff&&window.EducationMySchoolStaff.loadSession)?window.EducationMySchoolStaff.loadSession():null;
  function currentSchoolCodeForStaff(){
    const el=document.getElementById('school_code');
    return window.EducationMySchoolStaff?window.EducationMySchoolStaff.normalizeSchoolCode(el?el.value:''):String(el&&el.value||'').trim();
  }
  function mySchoolStaffSetStatus(message,type){
    if(!mySchoolStaffStatus) return;
    mySchoolStaffStatus.textContent=message||'';
    mySchoolStaffStatus.className='personnel-csv-status'+(type?' is-'+type:'');
  }
  function refreshMySchoolStaffPanel(){
    const code=currentSchoolCodeForStaff();
    const rows=(mySchoolStaffRegistry&&window.EducationMySchoolStaff)?window.EducationMySchoolStaff.forSchool(mySchoolStaffRegistry,code):[];
    if(mySchoolStaffMeta){
      if(mySchoolStaffRegistry){
        const unique=mySchoolStaffRegistry.unique_people_count?(' · '+mySchoolStaffRegistry.unique_people_count+' μοναδικοί εκπαιδευτικοί'):'';
        mySchoolStaffMeta.textContent=(mySchoolStaffRegistry.source_file||'stat4_8')+' · '+mySchoolStaffRegistry.placement_count+' τοποθετήσεις'+unique+' · '+mySchoolStaffRegistry.school_count+' μονάδες'+(code?' · '+rows.length+' εγγραφές στο '+code:' · επίλεξε σχολείο με πραγματικό κωδικό');
      }else{
        mySchoolStaffMeta.textContent='Φόρτωσε το αυθεντικό ZIP ή CSV του stat4_8. Το αρχείο επεξεργάζεται μόνο τοπικά στον browser.';
      }
    }
    if(loadMySchoolStaffForSchool) loadMySchoolStaffForSchool.disabled=!mySchoolStaffRegistry||!code||!rows.length;
    if(downloadCleanMySchoolStaff) downloadCleanMySchoolStaff.disabled=!mySchoolStaffRegistry;
    if(clearMySchoolStaff) clearMySchoolStaff.disabled=!mySchoolStaffRegistry;
    if(mySchoolStaffRegistry&&code&&!rows.length) mySchoolStaffSetStatus('Δεν βρέθηκε προσωπικό στο μητρώο stat4_8 για τον κωδικό '+code+'.','error');
    else if(mySchoolStaffRegistry&&code&&rows.length) mySchoolStaffSetStatus('Βρέθηκαν '+rows.length+' εγγραφές προσωπικού για το τρέχον σχολείο. Μπορείς να τις φορτώσεις με ένα κλικ.','success');
    else if(!mySchoolStaffRegistry) mySchoolStaffSetStatus('', '');
  }
  function openMySchoolStaffPanel(){ if(mySchoolStaffPanel) mySchoolStaffPanel.hidden=false; refreshMySchoolStaffPanel(); }
  if(openMySchoolStaff) openMySchoolStaff.addEventListener('click',openMySchoolStaffPanel);
  if(closeMySchoolStaff) closeMySchoolStaff.addEventListener('click',function(){if(mySchoolStaffPanel) mySchoolStaffPanel.hidden=true;});
  if(pickMySchoolStaffFile) pickMySchoolStaffFile.addEventListener('click',function(){if(mySchoolStaffFile){mySchoolStaffFile.value='';mySchoolStaffFile.click();}});
  if(mySchoolStaffFile){
    mySchoolStaffFile.addEventListener('change',function(){
      const file=mySchoolStaffFile.files&&mySchoolStaffFile.files[0]; if(!file) return;
      if(!window.EducationMySchoolStaff){mySchoolStaffSetStatus('Δεν φορτώθηκε ο importer myschool stat4_8.','error');return;}
      const reader=new FileReader();
      reader.onload=async function(){
        try{
          mySchoolStaffSetStatus('Ανάγνωση και καθαρισμός του stat4_8…','');
          const registry=await window.EducationMySchoolStaff.parseArrayBuffer(reader.result,file.name);
          window.EducationMySchoolStaff.saveSession(registry);
          mySchoolStaffRegistry=registry;
          refreshMySchoolStaffPanel();
          const code=currentSchoolCodeForStaff();
          const count=code?window.EducationMySchoolStaff.forSchool(registry,code).length:0;
          mySchoolStaffSetStatus('Το μητρώο καθαρίστηκε και αποθηκεύτηκε μόνο για τη συνεδρία: '+registry.placement_count+' τοποθετήσεις σε '+registry.school_count+' μονάδες.'+(code?' Για το τρέχον σχολείο βρέθηκαν '+count+'.':''),'success');
        }catch(err){
          mySchoolStaffSetStatus(err&&err.message?err.message:'Δεν ήταν δυνατή η ανάγνωση του stat4_8.','error');
        }
      };
      reader.onerror=function(){mySchoolStaffSetStatus('Δεν ήταν δυνατή η ανάγνωση του αρχείου.','error');};
      reader.readAsArrayBuffer(file);
    });
  }
  if(loadMySchoolStaffForSchool){
    loadMySchoolStaffForSchool.addEventListener('click',function(){
      if(!mySchoolStaffRegistry||!window.EducationMySchoolStaff) return;
      const code=currentSchoolCodeForStaff();
      const people=window.EducationMySchoolStaff.forSchool(mySchoolStaffRegistry,code);
      if(!people.length){refreshMySchoolStaffPanel();return;}
      const existing=personnelList?personnelList.querySelectorAll('[data-personnel-row]').length:0;
      if(existing&&!window.confirm('Θα αντικατασταθούν οι '+existing+' υπάρχουσες εγγραφές προσωπικού με τις '+people.length+' εγγραφές του myschool για το σχολείο '+code+'. Συνέχεια;')) return;
      clearPersonnelRows();
      let imported=0,unknown=0;
      people.forEach(function(person){
        const result=addPersonnelFromData(person);
        if(result.ok) imported++;
        if(result.unknownCode) unknown++;
      });
      ensurePersonnelEmptyState(); refreshDirectorRoleConstraints(); markPersonnelDirty();
      mySchoolStaffSetStatus('Φορτώθηκαν '+imported+' εκπαιδευτικοί από το myschool για το '+code+'.'+(unknown?' '+unknown+' κλάδοι χρειάζονται χειροκίνητο έλεγχο.':'')+' Πάτησε «Έλεγχος ωραρίων προσωπικού» για να ενημερωθούν οι επόμενες καρτέλες.',unknown?'error':'success');
    });
  }
  if(downloadCleanMySchoolStaff){
    downloadCleanMySchoolStaff.addEventListener('click',function(){
      if(!mySchoolStaffRegistry||!window.EducationMySchoolStaff) return;
      const csv=window.EducationMySchoolStaff.cleanedCsv(mySchoolStaffRegistry);
      const blob=new Blob([csv],{type:'text/csv;charset=utf-8'}),url=URL.createObjectURL(blob),a=document.createElement('a');
      a.href=url;a.download='dde-staff-registry-v1-myschool-clean.csv';document.body.appendChild(a);a.click();a.remove();setTimeout(function(){URL.revokeObjectURL(url);},500);
    });
  }
  if(clearMySchoolStaff){
    clearMySchoolStaff.addEventListener('click',function(){
      if(window.EducationMySchoolStaff) window.EducationMySchoolStaff.clearSession();
      mySchoolStaffRegistry=null;refreshMySchoolStaffPanel();mySchoolStaffSetStatus('Το προσωρινό μητρώο myschool καθαρίστηκε από τη συνεδρία.','success');
    });
  }
  refreshMySchoolStaffPanel();

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
    const text=csvSpreadsheetSafeText(value);
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
      const source=value('.personnel-obligation-source');
      const sourceRequired=source==='myschool_stat4_8';
      return [
        'staff_registry_v1',
        value('input[name="personnel_person_id[]"]'),
        value('.personnel-specialty'),
        value('.personnel-secondary-specialty'),
        value('.personnel-name'),
        (role==='teacher'||sourceRequired)?value('.personnel-required'):'',
        personnelRegistryRoleLabel(role),
        (role==='teacher'||sourceRequired)?'':value('.personnel-years'),
        (role==='teacher'||sourceRequired)?'':value('.personnel-months'),
        (role==='teacher'||sourceRequired)?'':value('.personnel-days'),
        value('.personnel-external'),
        source,
        value('.personnel-source-base-required'),
        value('.personnel-source-reduction'),
        value('.personnel-source-at-unit')
      ];
    });
  }
  function downloadPersonnelRegistryCsv(rows,filename){
    const headers=['Έκδοση μητρώου','Αναγνωριστικό','Κλάδος','2η ειδικότητα','Ονοματεπώνυμο','Υποχρεωτικό ωράριο','Ρόλος','Έτη υπηρεσίας','Μήνες','Ημέρες','Ώρες αλλού','Πηγή ωραρίου','Υ.Ω. πηγής','Μείωση πηγής','Ώρες Υ.Ω. στον φορέα'];
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
        ['staff_registry_v1','', 'ΠΕ03','ΠΕ86','Μαρία Παπαδοπούλου','20','Εκπαιδευτικός','','','','0','','','',''],
        ['staff_registry_v1','', 'ΠΕ02','','Γιώργος Διευθυντής','','Διευθυντής','20','0','0','0','','','','']
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
  const specialtyLabelsData=<?php echo json_encode($specialtyLabelsClient, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  const specialtyReportSchemaVersion='staffing_balance_v1';
  const allocationList=document.getElementById('allocationList');
  const allocationTemplate=document.getElementById('allocationRowTemplate');
  const addAllocation=document.getElementById('addAllocationRow');
  const clearAllocation=document.getElementById('clearAllocationRows');
  const allocationBAssignmentWarning='Οι ώρες μαθημάτων Β΄ ανάθεσης, από τη βασική και τη δεύτερη ειδικότητα συνολικά, υπερβαίνουν το όριο των 10 διδακτικών ωρών. Υπέρβαση επιτρέπεται μόνο κατ’ εξαίρεση, ύστερα από απόφαση ΠΥΣΔΕ και υπό τις προβλεπόμενες προϋποθέσεις.';
  function allocationPriority(code,slot){
    if(!code||!slot||!slot.eligible_by_priority) return '';
    const order=['A','B','C','SPECIAL'];
    for(let i=0;i<order.length;i++){
      const p=order[i], arr=slot.eligible_by_priority[p]||[];
      if(arr.indexOf(code)>=0) return p;
    }
    return '';
  }
  function allocationPriorityRank(priority){
    const rank={A:1,SPECIAL:1,B:2,C:3};
    return rank[priority]||99;
  }
  function allocationPriorityLabel(priority){
    if(priority==='A') return 'Α΄';
    if(priority==='B') return 'Β΄';
    if(priority==='C') return 'Γ΄';
    if(priority==='SPECIAL') return 'Ειδική';
    return priority||'';
  }
  function allocationBestAssignment(person,slot){
    if(!person||!slot) return null;
    const candidates=[];
    const primary=person.specialty_code||'';
    const secondary=person.secondary_specialty_code||'';
    const p1=allocationPriority(primary,slot);
    if(p1) candidates.push({priority:p1,used_specialty_code:primary,specialty_source:'primary'});
    if(secondary&&secondary!==primary){
      const p2=allocationPriority(secondary,slot);
      if(p2) candidates.push({priority:p2,used_specialty_code:secondary,specialty_source:'secondary'});
    }
    if(!candidates.length) return null;
    candidates.sort(function(a,b){
      const d=allocationPriorityRank(a.priority)-allocationPriorityRank(b.priority);
      if(d) return d;
      if(a.specialty_source===b.specialty_source) return 0;
      return a.specialty_source==='primary'?-1:1;
    });
    return candidates[0];
  }
  function allocationAssignmentLabel(match){
    if(!match) return '';
    let text=allocationPriorityLabel(match.priority)+' ανάθεση';
    if(match.specialty_source==='secondary') text+=' · μέσω 2ης ειδικότητας '+match.used_specialty_code;
    return text;
  }
  function allocationPopulatePeopleForSlot(row,preserveSelected){
    const personEl=row.querySelector('.allocation-person'), slotEl=row.querySelector('.allocation-slot');
    if(!personEl) return;
    const oldSelected=personEl.value||'';
    const slot=slotEl?allocationSlotsData[slotEl.value]||null:null;
    personEl.innerHTML='';
    const placeholder=document.createElement('option'); placeholder.value=''; placeholder.textContent=slot?'— επιλογή επιλέξιμου εκπαιδευτικού —':'— επίλεξε πρώτα μάθημα / τμήμα —'; personEl.appendChild(placeholder);
    if(!slot){ personEl.disabled=true; return; }
    personEl.disabled=false;
    Object.keys(allocationPeopleData||{}).forEach(function(pid){
      const person=allocationPeopleData[pid], match=allocationBestAssignment(person,slot);
      if(!match) return;
      const option=document.createElement('option'); option.value=pid; option.textContent=(person.label||pid)+' · '+allocationAssignmentLabel(match); if(oldSelected===pid) option.selected=true; personEl.appendChild(option);
    });
    if(preserveSelected&&oldSelected&&allocationPeopleData[oldSelected]&&!allocationBestAssignment(allocationPeopleData[oldSelected],slot)){
      const invalid=document.createElement('option'); invalid.value=oldSelected; invalid.textContent=(allocationPeopleData[oldSelected].label||oldSelected)+' · ΜΗ ΕΠΙΤΡΕΠΤΟ'; invalid.selected=true; personEl.insertBefore(invalid,personEl.children[1]||null);
    }
  }
  // Διατηρείται ως μικρό compatibility helper, αλλά η κύρια προβολή είναι slot-first.
  function allocationPopulateSlotsForPerson(row,preserveSelected){
    const personEl=row.querySelector('.allocation-person'), slotEl=row.querySelector('.allocation-slot');
    if(!slotEl) return;
    const oldSelected=slotEl.value||'';
    const person=personEl?allocationPeopleData[personEl.value]||null:null;
    slotEl.innerHTML='';
    const placeholder=document.createElement('option'); placeholder.value=''; placeholder.textContent=person?'— επιλογή επιλέξιμου μαθήματος —':'— επιλογή τμήματος / ομάδας και μαθήματος —'; slotEl.appendChild(placeholder);
    let currentGrade=null, group=null;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid];
      if(!slot.has_eligible_person) return;
      if(person&&!allocationBestAssignment(person,slot)) return;
      if(slot.grade!==currentGrade){ group=document.createElement('optgroup'); group.label=(slot.grade||'Άλλο')+' τάξη'; slotEl.appendChild(group); currentGrade=slot.grade; }
      const option=document.createElement('option'); option.value=slot.slot_id; option.textContent=slot.label; option.setAttribute('data-capacity',String(slot.capacity_hours)); if(oldSelected===slot.slot_id) option.selected=true; group.appendChild(option);
    });
    if(preserveSelected&&oldSelected&&allocationSlotsData[oldSelected]&&person&&!allocationBestAssignment(person,allocationSlotsData[oldSelected])){
      const invalid=document.createElement('option'); invalid.value=oldSelected; invalid.textContent=allocationSlotsData[oldSelected].label+' · ΜΗ ΕΠΙΤΡΕΠΤΟ'; invalid.selected=true; slotEl.insertBefore(invalid,slotEl.children[1]||null);
    }
  }
  function allocationSetStatus(row,text,kind){
    const el=row.querySelector('[data-allocation-status]'); if(!el) return;
    el.textContent=text; el.classList.remove('is-ok','is-warning','is-error');
    if(kind) el.classList.add('is-'+kind);
  }
  function allocationRows(){ return allocationList ? Array.from(allocationList.querySelectorAll('[data-allocation-row]')) : []; }
  const vacancyRows=Array.from(document.querySelectorAll('[data-vacancy-row]'));
  const printVacancyRowsById={};
  Array.from(document.querySelectorAll('[data-print-vacancy-row]')).forEach(function(row){ printVacancyRowsById[row.getAttribute('data-print-vacancy-row')||'']=row; });
  const vacancyFilter=document.getElementById('vacancyFilter');
  const stat51Panel=document.getElementById('stat51ComparePanel');
  const stat51FileInput=document.getElementById('stat51FileInput');
  const pickStat51File=document.getElementById('pickStat51File');
  const clearStat51File=document.getElementById('clearStat51File');
  const stat51Status=document.getElementById('stat51Status');
  const stat51Summary=document.getElementById('stat51Summary');
  const stat51TableWrap=document.getElementById('stat51TableWrap');
  const stat51ComparisonBody=document.getElementById('stat51ComparisonBody');
  const stat51Empty=document.getElementById('stat51Empty');
  const stat51Footnote=document.getElementById('stat51Footnote');
  let stat51Registry=(window.EducationMySchoolStat51&&window.EducationMySchoolStat51.loadSession)?window.EducationMySchoolStat51.loadSession():null;
  function stat51CurrentSchoolCode(){
    const field=document.querySelector('[name="school_code"]'), raw=field?field.value:'';
    return window.EducationMySchoolStat51?window.EducationMySchoolStat51.normalizeSchoolCode(raw):String(raw||'').trim();
  }
  function stat51SetStatus(text,kind){
    if(!stat51Status) return;
    stat51Status.textContent=text;
    stat51Status.classList.remove('is-error','is-success','is-warning');
    if(kind) stat51Status.classList.add('is-'+kind);
  }
  function stat51DisplayGrade(value){
    const g=String(value||'').replace(/[΄’']/g,'').trim().toUpperCase();
    return ['Α','Β','Γ','Δ'].indexOf(g)>=0?g+'΄':(value||'—');
  }
  function stat51LocalGroups(slotAssigned){
    const api=window.EducationMySchoolStat51, groups={};
    if(!api) return groups;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid]||{}, remaining=Math.max(0,(slot.capacity_hours||0)-(slotAssigned&&slotAssigned[sid]||0));
      if(remaining<1) return;
      const subject=String(slot.choice_option||slot.subject||'').trim();
      if(!subject) return;
      const structure=api.localStructure(slot.school||''), strict=api.strictKey(structure,slot.grade||'',subject), loose=api.looseKey(slot.grade||'',subject);
      if(!groups[strict]) groups[strict]={key:strict,loose_key:loose,structure:structure,structure_label:slot.structure_label||'',grade:slot.grade||'',subject:subject,ours_hours:0,slot_count:0};
      groups[strict].ours_hours+=remaining; groups[strict].slot_count++;
    });
    return groups;
  }
  function stat51PairGroups(localGroups,statGroups){
    const pairs=[], usedLocal={}, usedStat={};
    Object.keys(localGroups).forEach(function(key){
      if(statGroups[key]){pairs.push({local:localGroups[key],stat:statGroups[key],match_kind:'strict'});usedLocal[key]=true;usedStat[key]=true;}
    });
    const localLoose={}, statLoose={};
    Object.keys(localGroups).forEach(function(key){if(!usedLocal[key]){const lk=localGroups[key].loose_key;(localLoose[lk]||(localLoose[lk]=[])).push(key);}});
    Object.keys(statGroups).forEach(function(key){if(!usedStat[key]){const lk=statGroups[key].loose_key;(statLoose[lk]||(statLoose[lk]=[])).push(key);}});
    Object.keys(localLoose).forEach(function(lk){
      if(localLoose[lk].length===1&&statLoose[lk]&&statLoose[lk].length===1){
        const lkey=localLoose[lk][0],skey=statLoose[lk][0];pairs.push({local:localGroups[lkey],stat:statGroups[skey],match_kind:'loose'});usedLocal[lkey]=true;usedStat[skey]=true;
      }
    });
    Object.keys(localGroups).forEach(function(key){if(!usedLocal[key])pairs.push({local:localGroups[key],stat:null,match_kind:'local_only'});});
    Object.keys(statGroups).forEach(function(key){if(!usedStat[key])pairs.push({local:null,stat:statGroups[key],match_kind:'stat_only'});});
    return pairs;
  }
  function stat51AppendCell(row,text,className){
    const td=document.createElement('td');td.textContent=String(text==null?'':text);if(className)td.className=className;row.appendChild(td);return td;
  }
  function stat51RenderComparison(slotAssigned){
    if(!stat51ComparisonBody||!window.EducationMySchoolStat51) return;
    const api=window.EducationMySchoolStat51, code=stat51CurrentSchoolCode();
    stat51ComparisonBody.innerHTML='';
    if(!stat51Registry){
      if(stat51Summary) stat51Summary.hidden=true;if(stat51TableWrap) stat51TableWrap.hidden=true;if(stat51Empty) stat51Empty.hidden=true;if(stat51Footnote)stat51Footnote.hidden=true;
      if(clearStat51File) clearStat51File.hidden=true;
      stat51SetStatus('Δεν έχει φορτωθεί stat5_1.','');
      return;
    }
    if(clearStat51File) clearStat51File.hidden=false;
    if(!code){
      if(stat51Summary) stat51Summary.hidden=true;if(stat51TableWrap) stat51TableWrap.hidden=true;if(stat51Empty) stat51Empty.hidden=true;if(stat51Footnote)stat51Footnote.hidden=true;
      stat51SetStatus('Το stat5_1 έχει φορτωθεί, αλλά χρειάζεται κωδικός myschool στην Καρτέλα 1 για να επιλεγεί η σωστή σχολική μονάδα.','warning');
      return;
    }
    const schoolRows=api.forSchool(stat51Registry,code);
    if(!schoolRows.length){
      if(stat51Summary) stat51Summary.hidden=true;if(stat51TableWrap) stat51TableWrap.hidden=true;if(stat51Footnote)stat51Footnote.hidden=false;
      if(stat51Empty){stat51Empty.hidden=false;stat51Empty.textContent='Δεν βρέθηκε σχολική μονάδα με κωδικό '+code+' στο φορτωμένο stat5_1.';}
      stat51SetStatus('Δεν υπάρχει εγγραφή stat5_1 για τον κωδικό '+code+'.','warning');
      return;
    }
    const localGroups=stat51LocalGroups(slotAssigned||{}), statGroups=api.aggregateRows(schoolRows), pairs=stat51PairGroups(localGroups,statGroups);
    const looseCount={};pairs.forEach(function(pair){const item=pair.local||pair.stat;if(item){const k=item.loose_key||'';looseCount[k]=(looseCount[k]||0)+1;}});
    pairs.sort(function(a,b){
      const ag=api.normalizeGrade((a.local||a.stat).grade),bg=api.normalizeGrade((b.local||b.stat).grade),order={Α:1,Β:2,Γ:3,Δ:4};
      if((order[ag]||9)!==(order[bg]||9))return (order[ag]||9)-(order[bg]||9);
      return String((a.local||a.stat).subject||'').localeCompare(String((b.local||b.stat).subject||''),'el',{numeric:true});
    });
    let oursTotal=0,statTotal=0,agreements=0,differences=0;
    pairs.forEach(function(pair){
      const local=pair.local,stat=pair.stat,ours=local?local.ours_hours:0,mys=stat?stat.myschool_gap_hours:0,diff=ours-mys;
      oursTotal+=ours;statTotal+=mys;
      let label='',cls='';
      if(local&&stat&&Math.abs(diff)<0.001){label='Συμφωνία';cls='is-agreement';agreements++;}
      else if(local&&stat){label='Διαφορά';cls='is-difference';differences++;}
      else if(local){label='Μόνο στο εργαλείο';cls='is-only';differences++;}
      else {label='Μόνο στο stat5_1';cls='is-only';differences++;}
      const item=local||stat,tr=document.createElement('tr');tr.className=cls;
      stat51AppendCell(tr,stat51DisplayGrade(item.grade));
      let subject=String(item.subject||'');
      if(looseCount[item.loose_key||'']>1){const structureLabel=local&&local.structure_label?local.structure_label:(stat&&stat.structure?stat.structure:'');if(structureLabel)subject+=' · '+structureLabel;}
      stat51AppendCell(tr,subject);
      stat51AppendCell(tr,ours,'num');stat51AppendCell(tr,mys,'num');stat51AppendCell(tr,(diff>0?'+':'')+String(diff),'num stat51-diff');
      const statusCell=stat51AppendCell(tr,label,'stat51-check');
      if(stat){
        const details=[];if(stat.assignment_a&&stat.assignment_a.length)details.push('Α΄: '+stat.assignment_a.join(' | '));if(stat.assignment_b&&stat.assignment_b.length)details.push('Β΄: '+stat.assignment_b.join(' | '));
        if(details.length)statusCell.title=details.join(' · ');
      }
      stat51ComparisonBody.appendChild(tr);
    });
    const oursEl=document.querySelector('[data-stat51-ours]'),mysEl=document.querySelector('[data-stat51-myschool]'),diffEl=document.querySelector('[data-stat51-difference]'),agreeEl=document.querySelector('[data-stat51-agreements]');
    if(oursEl)oursEl.textContent=String(oursTotal);if(mysEl)mysEl.textContent=String(statTotal);if(diffEl)diffEl.textContent=(oursTotal-statTotal>0?'+':'')+String(oursTotal-statTotal);if(agreeEl)agreeEl.textContent=String(agreements);
    if(stat51Summary)stat51Summary.hidden=false;if(stat51TableWrap)stat51TableWrap.hidden=pairs.length===0;if(stat51Empty)stat51Empty.hidden=pairs.length!==0;if(stat51Footnote)stat51Footnote.hidden=false;
    const schoolName=schoolRows[0].school_name||code, source=stat51Registry.source_inner_file||stat51Registry.source_file||'stat5_1';
    const mismatch=stat51Registry.formula_mismatch_count||0;
    stat51SetStatus(source+' · '+schoolName+' ('+code+') · '+schoolRows.length+' γραμμές κενών · '+statTotal+' ώρες στο stat5_1 · '+differences+' αποκλίσεις.'+(mismatch?' Προσοχή: '+mismatch+' γραμμές του αρχείου δεν συμφωνούν με τον τύπο συνολικές ώρες − κάλυψη.':''),differences?'warning':'success');
  }
  function stat51RefreshFromCurrentAllocation(){const state=allocationCollectState();stat51RenderComparison(state.slotAssigned);}
  if(pickStat51File&&stat51FileInput)pickStat51File.addEventListener('click',function(){stat51FileInput.click();});
  if(stat51FileInput)stat51FileInput.addEventListener('change',function(){
    const file=stat51FileInput.files&&stat51FileInput.files[0];if(!file)return;
    if(!window.EducationMySchoolStat51){stat51SetStatus('Δεν φορτώθηκε ο importer myschool stat5_1.','error');return;}
    stat51SetStatus('Ανάγνωση '+file.name+'…','');
    const reader=new FileReader();reader.onload=async function(){
      try{
        const registry=await window.EducationMySchoolStat51.parseArrayBuffer(reader.result,file.name);stat51Registry=registry;
        const saved=window.EducationMySchoolStat51.saveSession(registry);
        if(stat51Panel)stat51Panel.open=true;
        stat51RefreshFromCurrentAllocation();
        if(!saved)stat51SetStatus(stat51Status.textContent+' Η σύγκριση λειτουργεί, αλλά το αρχείο είναι πολύ μεγάλο για προσωρινή αποθήκευση στη συνεδρία του browser.','warning');
      }catch(error){stat51SetStatus('Αποτυχία ανάγνωσης stat5_1: '+(error&&error.message?error.message:'άγνωστο σφάλμα')+'.','error');}
      stat51FileInput.value='';
    };reader.onerror=function(){stat51SetStatus('Δεν ήταν δυνατή η ανάγνωση του αρχείου stat5_1.','error');};reader.readAsArrayBuffer(file);
  });
  if(clearStat51File)clearStat51File.addEventListener('click',function(){
    stat51Registry=null;if(window.EducationMySchoolStat51)window.EducationMySchoolStat51.clearSession();stat51RenderComparison({});
  });
  const stat51SchoolCodeField=document.querySelector('[name="school_code"]');if(stat51SchoolCodeField)stat51SchoolCodeField.addEventListener('input',function(){stat51RefreshFromCurrentAllocation();});
  function vacancyEligiblePeopleAvailability(slot,personAssigned,personPriority){
    let normal=0, exceptionB=0;
    Object.keys(allocationPeopleData||{}).forEach(function(pid){
      const person=allocationPeopleData[pid], match=allocationBestAssignment(person,slot);
      if(!match) return;
      const remaining=Math.max(0,(person.available_here_hours||0)-(personAssigned[pid]||0));
      if(remaining<1) return;
      const bHours=personPriority&&personPriority[pid] ? (personPriority[pid].B||0) : 0;
      if(match.priority==='B' && bHours>=10) exceptionB++; else normal++;
    });
    return {normal:normal,exceptionB:exceptionB,total:normal+exceptionB};
  }
  function allocationCollectState(){
    const personAssigned={}, personPriority={}, personSource={}, slotAssigned={}, slotAttempted={}, rowState=[];
    Object.keys(allocationPeopleData||{}).forEach(function(id){
      personAssigned[id]=0;
      personPriority[id]={A:0,B:0,C:0,SPECIAL:0};
      personSource[id]={primary:0,secondary:0};
    });
    Object.keys(allocationSlotsData||{}).forEach(function(id){ slotAssigned[id]=0; slotAttempted[id]=0; });

    allocationRows().forEach(function(row){
      const personEl=row.querySelector('.allocation-person'), slotEl=row.querySelector('.allocation-slot'), hoursEl=row.querySelector('.allocation-hours');
      const pid=personEl?personEl.value:'', sid=slotEl?slotEl.value:'', hours=Math.max(0,parseInt(hoursEl&&hoursEl.value?hoursEl.value:'0',10)||0);
      const person=allocationPeopleData[pid]||null, slot=allocationSlotsData[sid]||null, match=person&&slot?allocationBestAssignment(person,slot):null;
      let error='', warnings=[];
      if((pid||sid||hours)&&!slot) error='Δεν έχει επιλεγεί έγκυρο τμήμα / ομάδα και μάθημα.';
      else if((pid||sid||hours)&&!person) error='Δεν έχει επιλεγεί έγκυρος εκπαιδευτικός.';
      else if((pid||sid)&&hours<1) error='Οι ώρες πρέπει να είναι θετικές.';
      else if(person&&slot&&hours>slot.capacity_hours) error='Οι ώρες υπερβαίνουν τις '+slot.capacity_hours+' ώρες του συγκεκριμένου τμήματος / ομάδας.';
      else if(person&&slot&&hours>0&&!match) error='Οι ειδικότητες '+person.specialty_code+(person.secondary_specialty_code?' / '+person.secondary_specialty_code:'')+' δεν έχουν ανάθεση στο συγκεκριμένο μάθημα.';
      if(!error&&person&&slot&&hours>0&&match){
        slotAttempted[sid]=(slotAttempted[sid]||0)+hours;
              }
      rowState.push({row:row,pid:pid,sid:sid,hours:hours,person:person,slot:slot,match:match,error:error,warnings:warnings,finalError:''});
    });

    const overallocatedSlots={};
    let overSlots=0;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const cap=allocationSlotsData[sid].capacity_hours||0, attempted=slotAttempted[sid]||0, over=Math.max(0,attempted-cap);
      if(over>0){ overallocatedSlots[sid]=over; overSlots+=over; }
    });

    let basicAssigned=0;
    rowState.forEach(function(st){
      st.finalError=st.error;
      if(!st.finalError&&st.sid&&overallocatedSlots[st.sid]){
        st.finalError='Το ίδιο τμήμα / ομάδα έχει συνολικά '+(slotAttempted[st.sid]||0)+' ώρες, ενώ διαθέτει '+st.slot.capacity_hours+'.';
      }
      if(st.finalError||!st.person||!st.slot||st.hours<1||!st.match) return;
      personAssigned[st.pid]=(personAssigned[st.pid]||0)+st.hours;
      personPriority[st.pid][st.match.priority]=(personPriority[st.pid][st.match.priority]||0)+st.hours;
      personSource[st.pid][st.match.specialty_source]=(personSource[st.pid][st.match.specialty_source]||0)+st.hours;
      slotAssigned[st.sid]=(slotAssigned[st.sid]||0)+st.hours;
      basicAssigned+=st.hours;
    });

    let unassigned=0;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const cap=allocationSlotsData[sid].capacity_hours||0, assigned=slotAssigned[sid]||0;
      unassigned+=Math.max(0,cap-assigned);
    });
    return {
      rowState:rowState,
      personAssigned:personAssigned,
      personPriority:personPriority,
      personSource:personSource,
      slotAssigned:slotAssigned,
      slotAttempted:slotAttempted,
      overallocatedSlots:overallocatedSlots,
      overSlots:overSlots,
      unassigned:unassigned,
      basicAssigned:basicAssigned
    };
  }
  function currentAllocationTotals(){
    const state=allocationCollectState();
    return {personAssigned:state.personAssigned,slotAssigned:state.slotAssigned};
  }
  function specialtyTopCandidates(slot){
    if(!slot||!slot.eligible_by_priority) return {priority:'',codes:[]};
    const top=slot.top_priority||'';
    if(top&&Array.isArray(slot.eligible_by_priority[top])&&slot.eligible_by_priority[top].length){
      return {priority:top,codes:Array.from(new Set(slot.eligible_by_priority[top])).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});})};
    }
    const order=['A','B','C','SPECIAL'];
    for(let i=0;i<order.length;i++){
      const p=order[i], codes=slot.eligible_by_priority[p]||[];
      if(codes.length) return {priority:p,codes:Array.from(new Set(codes)).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});})};
    }
    return {priority:'',codes:[]};
  }
  function allocationObjectiveCompare(a,b){
    for(const key of ['covered','top','b','primary']){
      const av=a[key]||0,bv=b[key]||0;
      if(av!==bv) return av>bv?1:-1;
    }
    return 0;
  }
  function allocationObjectiveForRows(rows){
    const o={covered:0,top:0,b:0,primary:0};
    (rows||[]).forEach(function(row){
      const h=Math.max(0,parseInt(row.hours||0,10)||0), p=row.priority||'';
      o.covered+=h;
      if(p==='A'||p==='SPECIAL') o.top+=h; else if(p==='B') o.b+=h;
      if(row.specialty_source==='primary') o.primary+=h;
    });
    return o;
  }
  function allocationOptimizeRemaining(personStateInput,slotStateInput){
    const originalPeople=JSON.parse(JSON.stringify(personStateInput||{}));
    const originalSlots=JSON.parse(JSON.stringify(slotStateInput||{}));
    const peopleIds=Object.keys(originalPeople).filter(function(pid){return (originalPeople[pid].remaining_hours||0)>0;}).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});});
    const routesBySlot={};
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const state=originalSlots[sid]||{}, need=Math.max(0,state.remaining_hours||0);
      if(need<1||state.atomic_blocked) return;
      const routes={};
      peopleIds.forEach(function(pid){
        const match=allocationBestAssignment(allocationPeopleData[pid],allocationSlotsData[sid]);
        if(!match) return;
        const ps=originalPeople[pid];
        if((ps.remaining_hours||0)<need) return;
        if(match.priority==='B'&&(ps.b_remaining_hours||0)<need) return;
        routes[pid]=match;
      });
      if(Object.keys(routes).length) routesBySlot[sid]=routes;
    });

    // Fast atomic seed. It is only the initial lower bound; the exact search
    // below is what fixes combinations such as 6 versus 3+2+2.
    const seedPeople=JSON.parse(JSON.stringify(originalPeople)), seedSlots=JSON.parse(JSON.stringify(originalSlots)), seed=[];
    Object.keys(routesBySlot).sort(function(a,b){
      const ca=Object.keys(routesBySlot[a]).length,cb=Object.keys(routesBySlot[b]).length;if(ca!==cb)return ca-cb;
      const ha=seedSlots[a].remaining_hours||0,hb=seedSlots[b].remaining_hours||0;if(ha!==hb)return hb-ha;
      return String(a).localeCompare(String(b),'el',{numeric:true});
    }).forEach(function(sid){
      const need=seedSlots[sid].remaining_hours||0;
      const candidates=Object.keys(routesBySlot[sid]).filter(function(pid){
        const m=routesBySlot[sid][pid],ps=seedPeople[pid];
        return ps&&(ps.remaining_hours||0)>=need&&(m.priority!=='B'||(ps.b_remaining_hours||0)>=need);
      }).sort(function(a,b){
        const ma=routesBySlot[sid][a],mb=routesBySlot[sid][b];
        const r=allocationPriorityRank(ma.priority)-allocationPriorityRank(mb.priority);if(r)return r;
        if(ma.specialty_source!==mb.specialty_source)return ma.specialty_source==='primary'?-1:1;
        const la=(seedPeople[a].remaining_hours||0)-need,lb=(seedPeople[b].remaining_hours||0)-need;if(la!==lb)return la-lb;
        return String(a).localeCompare(String(b),'el',{numeric:true});
      });
      if(!candidates.length)return;
      const pid=candidates[0],m=routesBySlot[sid][pid],slot=allocationSlotsData[sid];
      seed.push({person_id:pid,slot_id:sid,slot_label:slot.slot_label||slot.label||sid,subject:slot.subject||'',hours:need,priority:m.priority,used_specialty_code:m.used_specialty_code,specialty_source:m.specialty_source,source:'automatic_live_seed'});
      seedPeople[pid].remaining_hours-=need;
      if(m.priority==='B'){seedPeople[pid].b_assignment_hours=(seedPeople[pid].b_assignment_hours||0)+need;seedPeople[pid].b_remaining_hours=Math.max(0,10-seedPeople[pid].b_assignment_hours);}
      seedSlots[sid].remaining_hours=0;
    });

    const groupMap=new Map();
    Object.keys(routesBySlot).forEach(function(sid){
      const need=originalSlots[sid].remaining_hours||0,routes=routesBySlot[sid];
      const sig=Object.keys(routes).sort().map(function(pid){const m=routes[pid];return pid+'='+m.priority+'/'+m.specialty_source+'/'+m.used_specialty_code;}).join(';');
      const key=need+'|'+sig;
      if(!groupMap.has(key))groupMap.set(key,{need:need,routes:routes,slot_ids:[]});
      groupMap.get(key).slot_ids.push(sid);
    });
    const groups=Array.from(groupMap.values()).sort(function(a,b){const ca=Object.keys(a.routes).length,cb=Object.keys(b.routes).length;if(ca!==cb)return ca-cb;if(a.need!==b.need)return b.need-a.need;return String(a.slot_ids[0]).localeCompare(String(b.slot_ids[0]),'el',{numeric:true});});
    const personToGroups={};
    groups.forEach(function(g,gi){Object.keys(g.routes).forEach(function(pid){if(!personToGroups[pid])personToGroups[pid]=[];personToGroups[pid].push(gi);});});
    const visited=new Set(),components=[];
    groups.forEach(function(g,start){
      if(visited.has(start))return;
      const queue=[start],gis=[],pids=new Set();visited.add(start);
      while(queue.length){const gi=queue.shift();gis.push(gi);Object.keys(groups[gi].routes).forEach(function(pid){pids.add(pid);(personToGroups[pid]||[]).forEach(function(ngi){if(!visited.has(ngi)){visited.add(ngi);queue.push(ngi);}});});}
      components.push({group_indexes:gis,person_ids:Array.from(pids)});
    });
    const seedBySlot={};seed.forEach(function(row){seedBySlot[row.slot_id]=row;});
    let finalRows=[],allCertified=true,totalNodes=0;

    components.forEach(function(component){
      const cg=component.group_indexes.map(function(i){return groups[i];}), cpids=component.person_ids.slice().sort();
      if(cpids.length===1){
        const pid=cpids[0],cap=originalPeople[pid].remaining_hours||0,bcap=originalPeople[pid].b_remaining_hours||0,items=[];
        cg.forEach(function(g){const m=g.routes[pid];if(!m)return;g.slot_ids.forEach(function(sid){items.push({sid:sid,need:g.need,m:m});});});
        let dp=new Map();dp.set('0:0',{objective:{covered:0,top:0,b:0,primary:0},rows:[],used:0,bused:0});
        items.forEach(function(item){const next=new Map(dp);dp.forEach(function(st){totalNodes++;const nu=st.used+item.need,nb=st.bused+(item.m.priority==='B'?item.need:0);if(nu>cap||nb>bcap)return;const o={...st.objective};o.covered+=item.need;if(item.m.priority==='A'||item.m.priority==='SPECIAL')o.top+=item.need;else if(item.m.priority==='B')o.b+=item.need;if(item.m.specialty_source==='primary')o.primary+=item.need;const slot=allocationSlotsData[item.sid],rows=st.rows.concat([{person_id:pid,slot_id:item.sid,slot_label:slot.slot_label||slot.label||item.sid,subject:slot.subject||'',hours:item.need,priority:item.m.priority,used_specialty_code:item.m.used_specialty_code,specialty_source:item.m.specialty_source,source:'automatic_live_optimizer_dp'}]);const key=nu+':'+nb,prev=next.get(key);if(!prev||allocationObjectiveCompare(o,prev.objective)>0)next.set(key,{objective:o,rows:rows,used:nu,bused:nb});});dp=next;});
        let best={objective:{covered:0,top:0,b:0,primary:0},rows:[]};dp.forEach(function(st){if(allocationObjectiveCompare(st.objective,best.objective)>0)best=st;});finalRows=finalRows.concat(best.rows);return;
      }
      const counts=cg.map(function(g){return g.slot_ids.length;}),originalCounts=counts.slice();
      const rem={},brem={};cpids.forEach(function(pid){rem[pid]=originalPeople[pid].remaining_hours||0;brem[pid]=originalPeople[pid].b_remaining_hours||0;});
      const equiv={};cpids.forEach(function(pid){equiv[pid]=cg.map(function(g){const m=g.routes[pid];return m?(m.priority+'/'+m.specialty_source+'/'+m.used_specialty_code):'-';}).join(';');});
      let bestRows=[];cg.forEach(function(g){g.slot_ids.forEach(function(sid){if(seedBySlot[sid])bestRows.push(seedBySlot[sid]);});});
      let bestObj=allocationObjectiveForRows(bestRows),currentRows=[],cur={covered:0,top:0,b:0,primary:0},nodes=0,aborted=false;
      const memo=new Map(),nodeLimit=30000;
      function search(){
        if(aborted)return;if(++nodes>nodeLimit){aborted=true;return;}
        let remainingHours=0,done=true;cg.forEach(function(g,gi){if(counts[gi]>0){done=false;remainingHours+=counts[gi]*g.need;}});
        if(done){if(allocationObjectiveCompare(cur,bestObj)>0){bestObj={...cur};bestRows=currentRows.map(function(r){return {...r};});}return;}
        const personHours=cpids.reduce(function(t,pid){return t+(rem[pid]||0);},0),upper=cur.covered+Math.min(remainingHours,personHours);
        if(upper<bestObj.covered)return;if(upper===bestObj.covered&&cur.top+Math.min(remainingHours,personHours)<bestObj.top)return;
        const equivStates={};cpids.forEach(function(pid){const sig=equiv[pid]||pid;if(!equivStates[sig])equivStates[sig]=[];equivStates[sig].push(rem[pid]+':'+brem[pid]);});
        const key=counts.join(',')+'|'+Object.keys(equivStates).sort().map(function(sig){return sig+'='+equivStates[sig].sort().join(',');}).join('|');
        const seen=memo.get(key);if(seen&&(seen.top>cur.top||(seen.top===cur.top&&seen.primary>=cur.primary)))return;memo.set(key,{top:cur.top,primary:cur.primary});
        let chosen=-1,cands=[],few=1e9;
        cg.forEach(function(g,gi){if(counts[gi]<1)return;const local=Object.keys(g.routes).filter(function(pid){const m=g.routes[pid];return rem[pid]>=g.need&&(m.priority!=='B'||brem[pid]>=g.need);}).map(function(pid){return {pid:pid,m:g.routes[pid],left:rem[pid]-g.need};});if(chosen<0||local.length<few||(local.length===few&&g.need>cg[chosen].need)){chosen=gi;cands=local;few=local.length;}});
        if(chosen<0)return;
        if(!cands.length){const old=counts[chosen];counts[chosen]=0;search();counts[chosen]=old;return;}
        cands.sort(function(a,b){const r=allocationPriorityRank(a.m.priority)-allocationPriorityRank(b.m.priority);if(r)return r;if(a.m.specialty_source!==b.m.specialty_source)return a.m.specialty_source==='primary'?-1:1;if(a.left!==b.left)return a.left-b.left;return String(a.pid).localeCompare(String(b.pid),'el',{numeric:true});});
        const g=cg[chosen],idx=originalCounts[chosen]-counts[chosen],sid=g.slot_ids[idx],slot=allocationSlotsData[sid];counts[chosen]--;
        const sym=new Set();
        cands.forEach(function(c){const pid=c.pid,m=c.m,sk=equiv[pid]+'|'+rem[pid]+'|'+brem[pid];if(sym.has(sk))return;sym.add(sk);rem[pid]-=g.need;if(m.priority==='B')brem[pid]-=g.need;currentRows.push({person_id:pid,slot_id:sid,slot_label:slot.slot_label||slot.label||sid,subject:slot.subject||'',hours:g.need,priority:m.priority,used_specialty_code:m.used_specialty_code,specialty_source:m.specialty_source,source:'automatic_live_optimizer'});cur.covered+=g.need;if(m.priority==='A'||m.priority==='SPECIAL')cur.top+=g.need;else if(m.priority==='B')cur.b+=g.need;if(m.specialty_source==='primary')cur.primary+=g.need;search();if(m.specialty_source==='primary')cur.primary-=g.need;if(m.priority==='A'||m.priority==='SPECIAL')cur.top-=g.need;else if(m.priority==='B')cur.b-=g.need;cur.covered-=g.need;currentRows.pop();if(m.priority==='B')brem[pid]+=g.need;rem[pid]+=g.need;});
        search();counts[chosen]++;
      }
      search();totalNodes+=nodes;if(aborted)allCertified=false;finalRows=finalRows.concat(bestRows);
    });

    const finalPeople=JSON.parse(JSON.stringify(originalPeople)),finalSlots=JSON.parse(JSON.stringify(originalSlots));
    finalRows.forEach(function(row){const ps=finalPeople[row.person_id],ss=finalSlots[row.slot_id],h=row.hours||0;if(!ps||!ss)return;ps.remaining_hours=Math.max(0,(ps.remaining_hours||0)-h);if(row.priority==='B'){ps.b_assignment_hours=(ps.b_assignment_hours||0)+h;ps.b_remaining_hours=Math.max(0,10-ps.b_assignment_hours);}ss.remaining_hours=0;});
    return {allocations:finalRows,people:finalPeople,slots:finalSlots,summary:{auto_covered_hours:finalRows.reduce(function(t,r){return t+(r.hours||0);},0),maximum_coverage_certified:allCertified,optimizer_search_nodes:totalNodes}};
  }
  function specialtyBuildReport(state){
    const personState={}, slotState={};
    Object.keys(allocationPeopleData||{}).forEach(function(pid){
      const person=allocationPeopleData[pid];
      const assigned=state.personAssigned[pid]||0, bHours=(state.personPriority[pid]&&state.personPriority[pid].B)||0;
      personState[pid]={
        remaining_hours:Math.max(0,(person.available_here_hours||0)-assigned),
        b_assignment_hours:bHours,
        b_remaining_hours:Math.max(0,10-bHours),
        primary_code:person.specialty_code||'',
        secondary_code:person.secondary_specialty_code||''
      };
    });
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid],assigned=state.slotAssigned[sid]||0,remaining=Math.max(0,(slot.capacity_hours||0)-assigned);
      slotState[sid]={remaining_hours:remaining,atomic_blocked:assigned>0&&remaining>0};
    });
    const optimized=allocationOptimizeRemaining(personState,slotState);
    const autoAllocations=optimized.allocations;
    const autoCovered=optimized.summary.auto_covered_hours||0;
    Object.keys(personState).forEach(function(pid){personState[pid]=optimized.people[pid]||personState[pid];});
    Object.keys(slotState).forEach(function(sid){slotState[sid]=optimized.slots[sid]||slotState[sid];});

    const openRows=[], specialBuckets={}, coverageByCode={}, exclusiveByCode={};
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid], hours=slotState[sid]?slotState[sid].remaining_hours:0;
      if(hours<1) return;
      const bucket=slot.reporting_bucket||null;
      if(bucket&&bucket.key){
        if(!specialBuckets[bucket.key]) specialBuckets[bucket.key]={key:bucket.key,label:bucket.label||bucket.key,gap_hours:0,slots:[]};
        specialBuckets[bucket.key].gap_hours+=hours;
        specialBuckets[bucket.key].slots.push({slot_id:sid,slot_label:slot.slot_label||'',subject:slot.subject||'',hours:hours});
        return;
      }
      const top=specialtyTopCandidates(slot), codes=top.codes.slice();
      openRows.push({slot_id:sid,slot_label:slot.slot_label||slot.label||sid,subject:slot.subject||'',hours:hours,priority:top.priority,candidate_codes:codes});
      codes.forEach(function(code){
        coverageByCode[code]=(coverageByCode[code]||0)+hours;
        if(codes.length===1) exclusiveByCode[code]=(exclusiveByCode[code]||0)+hours;
      });
    });
    openRows.sort(function(a,b){
      if(a.candidate_codes.length!==b.candidate_codes.length) return a.candidate_codes.length-b.candidate_codes.length;
      if(a.hours!==b.hours) return b.hours-a.hours;
      const s=String(a.subject).localeCompare(String(b.subject),'el',{numeric:true}); if(s) return s;
      return String(a.slot_id).localeCompare(String(b.slot_id),'el',{numeric:true});
    });
    const gapByCode={}, recommendations=[];
    openRows.forEach(function(row){
      const codes=row.candidate_codes.slice().sort(function(a,b){
        const ca=coverageByCode[a]||0, cb=coverageByCode[b]||0; if(ca!==cb) return cb-ca;
        const ga=gapByCode[a]||0, gb=gapByCode[b]||0; if(ga!==gb) return gb-ga;
        const ea=exclusiveByCode[a]||0, eb=exclusiveByCode[b]||0; if(ea!==eb) return eb-ea;
        return String(a).localeCompare(String(b),'el',{numeric:true});
      });
      const selected=codes.length?codes[0]:'';
      if(selected) gapByCode[selected]=(gapByCode[selected]||0)+row.hours;
      recommendations.push({slot_id:row.slot_id,slot_label:row.slot_label,subject:row.subject,hours:row.hours,priority:row.priority,selected_code:selected,candidate_codes:row.candidate_codes.slice(),selected_code_total_reachable_hours:selected?(coverageByCode[selected]||0):0,selection_kind:row.candidate_codes.length<=1?'unique_top_assignment':'smart_shared_top_assignment'});
    });
    const surplusByCode={}, surplusPeopleByCode={};
    Object.keys(personState).forEach(function(pid){
      const ps=personState[pid], hours=Math.max(0,ps.remaining_hours||0), code=ps.primary_code||'';
      if(hours<1||!code) return;
      surplusByCode[code]=(surplusByCode[code]||0)+hours;
      surplusPeopleByCode[code]=(surplusPeopleByCode[code]||0)+1;
    });
    const bySpecialty={};
    Array.from(new Set(Object.keys(gapByCode).concat(Object.keys(surplusByCode)))).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});}).forEach(function(code){
      const gap=gapByCode[code]||0, surplus=surplusByCode[code]||0;
      bySpecialty[code]={code:code,label:specialtyLabelsData[code]||'',gap_hours:gap,surplus_hours:surplus,signed_balance_hours:surplus-gap,has_both_gap_and_surplus:gap>0&&surplus>0,surplus_people_count:surplusPeopleByCode[code]||0};
    });
    let finalUncovered=0, bucketGap=0, surplusTotal=0;
    Object.keys(slotState).forEach(function(sid){finalUncovered+=slotState[sid].remaining_hours||0;});
    Object.keys(specialBuckets).forEach(function(k){bucketGap+=specialBuckets[k].gap_hours||0;});
    Object.keys(surplusByCode).forEach(function(k){surplusTotal+=surplusByCode[k]||0;});
    return {
      automatic_balance:{allocations:autoAllocations,people:personState,slots:slotState,summary:{auto_covered_hours:autoCovered,remaining_slot_hours:finalUncovered,maximum_coverage_certified:optimized.summary.maximum_coverage_certified===true,optimizer_search_nodes:optimized.summary.optimizer_search_nodes||0}},
      vacancy_recommendations:recommendations,
      by_specialty:bySpecialty,
      special_reporting_buckets:specialBuckets,
      summary:{manual_unassigned_hours:state.unassigned||0,auto_internal_covered_hours:autoCovered,final_uncovered_hours:finalUncovered,specialty_gap_hours_total:Object.keys(gapByCode).reduce(function(t,k){return t+(gapByCode[k]||0);},0),special_reporting_bucket_gap_hours_total:bucketGap,surplus_hours_total:surplusTotal}
    };
  }
  let latestSpecialtyBalance=null;
  function specialtySignedText(value){ return String(value); }
  function specialtyAppendCell(row,text,className){ const td=document.createElement('td'); td.textContent=text; if(className) td.className=className; row.appendChild(td); return td; }
  function renderSpecialtyBalance(state){
    const body=document.getElementById('specialtyBalanceBody');
    if(!body) return;
    const specialtyPanel=document.querySelector('[data-staffing-panel="specialties"]');
    if(specialtyPanel&&specialtyPanel.hidden) return;
    const report=specialtyBuildReport(state); latestSpecialtyBalance=report;
    const manual=document.querySelector('[data-specialty-manual-uncovered]'), auto=document.querySelector('[data-specialty-auto-covered]'), final=document.querySelector('[data-specialty-final-uncovered]'), surplus=document.querySelector('[data-specialty-surplus-total]');
    if(manual) manual.textContent=String(report.summary.manual_unassigned_hours||0);
    if(auto) auto.textContent=String(report.summary.auto_internal_covered_hours||0);
    if(final) final.textContent=String(report.summary.final_uncovered_hours||0);
    if(surplus) surplus.textContent=String(report.summary.surplus_hours_total||0);
    body.innerHTML='';
    let rows=0;
    Object.keys(report.by_specialty||{}).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});}).forEach(function(code){
      const item=report.by_specialty[code]; if((item.gap_hours||0)<1&&(item.surplus_hours||0)<1) return;
      const tr=document.createElement('tr'); tr.setAttribute('data-specialty-balance-row',code);
      const c1=specialtyAppendCell(tr,code); const st=document.createElement('strong'); st.textContent=code; c1.textContent=''; c1.appendChild(st);
      specialtyAppendCell(tr,item.label||'');
      specialtyAppendCell(tr,String(item.gap_hours||0),'specialty-balance-value specialty-balance-deficit');
      specialtyAppendCell(tr,String(item.surplus_hours||0),'specialty-balance-value specialty-balance-surplus');
      specialtyAppendCell(tr,specialtySignedText(item.signed_balance_hours||0),'specialty-balance-value');
      specialtyAppendCell(tr,item.has_both_gap_and_surplus?'Ταυτόχρονο έλλειμμα και πλεόνασμα στον ίδιο κλάδο — χρειάζεται έλεγχος πριν από οριστική δήλωση.':'','specialty-balance-note');
      body.appendChild(tr); rows++;
    });
    Object.keys(report.special_reporting_buckets||{}).sort().forEach(function(key){
      const item=report.special_reporting_buckets[key]; if((item.gap_hours||0)<1) return;
      const tr=document.createElement('tr'); tr.className='specialty-balance-special'; tr.setAttribute('data-specialty-balance-row',key);
      const c1=specialtyAppendCell(tr,''); const st=document.createElement('strong'); st.textContent=item.label||key; c1.appendChild(st);
      specialtyAppendCell(tr,'Δεν αποδίδεται σε συγκεκριμένη ειδικότητα');
      specialtyAppendCell(tr,String(item.gap_hours||0),'specialty-balance-value specialty-balance-deficit');
      specialtyAppendCell(tr,'0','specialty-balance-value specialty-balance-surplus');
      specialtyAppendCell(tr,'-'+String(item.gap_hours||0),'specialty-balance-value');
      specialtyAppendCell(tr,'Ξεχωριστή γραμμή του υποδείγματος.','specialty-balance-note');
      body.appendChild(tr); rows++;
    });
    const empty=document.getElementById('specialtyBalanceEmpty'), wrap=document.getElementById('specialtyBalanceTableWrap');
    if(empty) empty.hidden=rows!==0; if(wrap) wrap.hidden=rows===0;

    const smartBody=document.getElementById('specialtySmartBody');
    if(smartBody){
      smartBody.innerHTML=''; let smartCount=0;
      (report.vacancy_recommendations||[]).forEach(function(item){
        if(!item.candidate_codes||item.candidate_codes.length<2) return;
        const tr=document.createElement('tr'); specialtyAppendCell(tr,item.slot_label||''); specialtyAppendCell(tr,item.subject||''); specialtyAppendCell(tr,String(item.hours||0));
        const td=specialtyAppendCell(tr,''); const strong=document.createElement('strong'); strong.textContent=item.selected_code||'—'; td.appendChild(strong);
        specialtyAppendCell(tr,item.candidate_codes.join(', ')); smartBody.appendChild(tr); smartCount++;
      });
      if(!smartCount){ const tr=document.createElement('tr'); const td=document.createElement('td'); td.colSpan=5; td.textContent='Δεν υπάρχουν κοινά κενά με περισσότερους από έναν ισότιμους κλάδους στην καλύτερη ανάθεση.'; tr.appendChild(td); smartBody.appendChild(tr); }
    }
    const autoBody=document.getElementById('specialtyAutoBody');
    if(autoBody){
      autoBody.innerHTML=''; let autoCount=0;
      (report.automatic_balance.allocations||[]).forEach(function(item){
        const tr=document.createElement('tr'), person=allocationPeopleData[item.person_id]||null;
        specialtyAppendCell(tr,person?(person.label||item.person_id):item.person_id); specialtyAppendCell(tr,item.slot_label||''); specialtyAppendCell(tr,item.subject||''); specialtyAppendCell(tr,String(item.hours||0));
        specialtyAppendCell(tr,allocationPriorityLabel(item.priority)+' ανάθεση'+(item.specialty_source==='secondary'?' · μέσω 2ης ειδικότητας '+item.used_specialty_code:''));
        autoBody.appendChild(tr); autoCount++;
      });
      if(!autoCount){ const tr=document.createElement('tr'); const td=document.createElement('td'); td.colSpan=5; td.textContent='Δεν εντοπίστηκαν πρόσθετες ώρες που να μπορούν να καλυφθούν αυτόματα από υπάρχον προσωπικό πέρα από την τρέχουσα κατανομή.'; tr.appendChild(td); autoBody.appendChild(tr); }
    }
    const printBody=document.getElementById('printSpecialtyBalanceBody');
    if(printBody){
      printBody.innerHTML='';
      Array.from(body.querySelectorAll('tr')).forEach(function(src){
        const cells=Array.from(src.children).map(function(td){return td.textContent||'';});
        const tr=document.createElement('tr'); cells.forEach(function(text,i){specialtyAppendCell(tr,text,(i>=2&&i<=4)?'num':'');}); printBody.appendChild(tr);
      });
    }
    const pm=document.querySelector('[data-print-specialty-manual-uncovered]'), pa=document.querySelector('[data-print-specialty-auto-covered]'), pf=document.querySelector('[data-print-specialty-final-uncovered]'), ps=document.querySelector('[data-print-specialty-surplus-total]');
    if(pm) pm.textContent=String(report.summary.manual_unassigned_hours||0); if(pa) pa.textContent=String(report.summary.auto_internal_covered_hours||0); if(pf) pf.textContent=String(report.summary.final_uncovered_hours||0); if(ps) ps.textContent=String(report.summary.surplus_hours_total||0);
    const pe=document.getElementById('printSpecialtyBalanceEmpty'); if(pe) pe.hidden=rows!==0;
  }
  function specialtyCsvCell(value){ return '"'+csvSpreadsheetSafeText(value).replace(/"/g,'""')+'"'; }
  function downloadSpecialtyBalanceCsv(){
    const state=allocationCollectState(), report=specialtyBuildReport(state); latestSpecialtyBalance=report;
    const registryEl=document.querySelector('[name="school_registry_id"]'), nameEl=document.querySelector('[name="school_name"]'), codeEl=document.querySelector('[name="school_code"]');
    const schoolRegistryId=registryEl?(registryEl.value||'').trim():'', schoolName=nameEl?(nameEl.value||'').trim():'', schoolCode=codeEl?(codeEl.value||'').trim():'';
    const rows=[['schema_version','school_registry_id','school_code','school_name','report_key','label','kind','deficit_hours','surplus_hours','balance_hours','note']];
    Object.keys(report.by_specialty||{}).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});}).forEach(function(code){
      const item=report.by_specialty[code]; if((item.gap_hours||0)<1&&(item.surplus_hours||0)<1) return;
      rows.push([specialtyReportSchemaVersion,schoolRegistryId,schoolCode,schoolName,code,item.label||'','specialty',item.gap_hours||0,item.surplus_hours||0,item.signed_balance_hours||0,item.has_both_gap_and_surplus?'Ταυτόχρονο έλλειμμα και πλεόνασμα — απαιτεί έλεγχο.':'']);
    });
    Object.keys(report.special_reporting_buckets||{}).sort().forEach(function(key){
      const item=report.special_reporting_buckets[key]; if((item.gap_hours||0)<1) return;
      rows.push([specialtyReportSchemaVersion,schoolRegistryId,schoolCode,schoolName,key,item.label||key,'subject_bucket',item.gap_hours||0,0,-(item.gap_hours||0),'Ξεχωριστή γραμμή υποδείγματος· δεν αποδίδεται αυτόματα σε ειδικότητα.']);
    });
    const text='\ufeff'+rows.map(function(row){return row.map(specialtyCsvCell).join(';');}).join('\r\n');
    const blob=new Blob([text],{type:'text/csv;charset=utf-8'}), url=URL.createObjectURL(blob), a=document.createElement('a');
    const stem=(schoolCode||schoolName||'school').replace(/[^0-9A-Za-zΑ-Ωα-ω._-]+/g,'-').replace(/^-+|-+$/g,'')||'school';
    a.href=url; a.download='staffing_balance_v1-'+stem+'.csv'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function(){URL.revokeObjectURL(url);},0);
  }
  const specialtyBalanceCsv=document.getElementById('specialtyBalanceCsv'); if(specialtyBalanceCsv) specialtyBalanceCsv.addEventListener('click',downloadSpecialtyBalanceCsv);
  function updateAllocationSlotOptionAvailability(slotAssigned){
    allocationRows().forEach(function(row){
      const select=row.querySelector('.allocation-slot');
      if(!select) return;
      const current=select.value||'';
      Array.from(select.querySelectorAll('option[value]')).forEach(function(option){
        const sid=option.value||'';
        if(sid==='') return;
        if(option.dataset.baseAllocationLabel===undefined) option.dataset.baseAllocationLabel=option.textContent||'';
        if(option.dataset.staticDisabled===undefined) option.dataset.staticDisabled=option.disabled?'1':'0';
        const slot=allocationSlotsData[sid]||null;
        const full=!!(slot&&(slotAssigned[sid]||0)>=slot.capacity_hours);
        const dynamicallyDisabled=full&&current!==sid;
        option.disabled=option.dataset.staticDisabled==='1'||dynamicallyDisabled;
        option.textContent=option.dataset.baseAllocationLabel+(dynamicallyDisabled?' · καλύφθηκε':'');
      });
    });
  }
  function updateVacancyView(slotAssigned,personAssigned,personPriority){
    if(!vacancyRows.length){ stat51RenderComparison(slotAssigned||{}); return; }
    let total=0, slots=0, noStaff=0, hasStaff=0;
    const q=vacancyFilter?(vacancyFilter.value||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,''):'';
    vacancyRows.forEach(function(row){
      const sid=row.getAttribute('data-vacancy-row')||'', slot=allocationSlotsData[sid]||null;
      if(!slot){ row.hidden=true; return; }
      const remaining=Math.max(0,(slot.capacity_hours||0)-(slotAssigned[sid]||0));
      const availability=remaining>0?vacancyEligiblePeopleAvailability(slot,personAssigned,personPriority):{normal:0,exceptionB:0,total:0};
      const availableCount=availability.total;
      const hoursEl=row.querySelector('[data-vacancy-hours]'); if(hoursEl) hoursEl.textContent=String(remaining);
      const status=row.querySelector('[data-vacancy-status]');
      if(status){
        status.classList.remove('has-staff','no-staff');
        if(remaining<1) status.textContent='—';
        else if(availability.normal>0){ status.textContent='Υπάρχει επιλέξιμο προσωπικό με υπόλοιπο ('+availability.normal+(availability.exceptionB?' + '+availability.exceptionB+' μόνο κατ’ εξαίρεση Β΄':'')+')'; status.classList.add('has-staff'); }
        else if(availability.exceptionB>0){ status.textContent='Διαθέσιμο μόνο με κατ’ εξαίρεση υπέρβαση του ορίου Β΄ ανάθεσης ('+availability.exceptionB+')'; status.classList.add('has-staff'); }
        else { status.textContent='Δεν υπάρχει επιλέξιμο προσωπικό με διαθέσιμο υπόλοιπο'; status.classList.add('no-staff'); }
      }
      const printRow=printVacancyRowsById[sid]||null;
      if(printRow){
        const printHours=printRow.querySelector('[data-print-vacancy-hours]'); if(printHours) printHours.textContent=String(remaining);
        const printStatus=printRow.querySelector('[data-print-vacancy-status]'); if(printStatus && status) printStatus.textContent=status.textContent;
        printRow.hidden=remaining<1;
      }
      const hay=(row.getAttribute('data-search')||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
      row.hidden=remaining<1||(q!==''&&!hay.includes(q));
      if(remaining>0){ total+=remaining; slots++; if(availableCount>0) hasStaff++; else noStaff++; }
    });
    const totalEl=document.querySelector('[data-vacancy-total]'), slotsEl=document.querySelector('[data-vacancy-slots]'), noStaffEl=document.querySelector('[data-vacancy-no-staff]'), hasStaffEl=document.querySelector('[data-vacancy-has-staff]');
    if(totalEl) totalEl.textContent=String(total);
    if(slotsEl) slotsEl.textContent=String(slots);
    if(noStaffEl) noStaffEl.textContent=String(noStaff);
    if(hasStaffEl) hasStaffEl.textContent=String(hasStaff);
    const tableWrap=document.getElementById('vacancyTableWrap'), empty=document.getElementById('vacancyEmpty');
    if(tableWrap) tableWrap.hidden=slots===0;
    if(empty) empty.hidden=slots!==0;
    const printEmpty=document.getElementById('printVacancyEmpty'); if(printEmpty) printEmpty.hidden=slots!==0;
    stat51RenderComparison(slotAssigned||{});
  }
  if(vacancyFilter) vacancyFilter.addEventListener('input',function(){ const state=allocationCollectState(); updateVacancyView(state.slotAssigned,state.personAssigned,state.personPriority); });
  function updateAllocationSummary(){
    const state=allocationCollectState();
    if(!allocationList){
      updateVacancyView(state.slotAssigned,state.personAssigned,state.personPriority);
      renderSpecialtyBalance(state);
      return;
    }
    const rowState=state.rowState, personAssigned=state.personAssigned, personPriority=state.personPriority, personSource=state.personSource, slotAssigned=state.slotAssigned;
    let errorRows=0;
    rowState.forEach(function(st){
      let error=st.finalError, warnings=st.warnings.slice();
      if(!error&&st.pid&&st.person&&(personAssigned[st.pid]||0)>st.person.available_here_hours){
        warnings.push('Υπέρβαση ατομικού διαθέσιμου ωραρίου κατά '+((personAssigned[st.pid]||0)-st.person.available_here_hours)+' ώρες.');
      }
      if(!error&&st.pid&&st.match&&st.match.priority==='B'&&(personPriority[st.pid].B||0)>10){
        warnings.push(allocationBAssignmentWarning);
      }
      if(error){ allocationSetStatus(st.row,error,'error'); errorRows++; }
      else if(st.person&&st.slot&&st.hours>0&&st.match){
        const base=allocationAssignmentLabel(st.match);
        if(warnings.length) allocationSetStatus(st.row,base+' · '+warnings.join(' '),'warning');
        else allocationSetStatus(st.row,base+' ✓','ok');
      } else allocationSetStatus(st.row,'Συμπλήρωσε μάθημα και εκπαιδευτικό.','');
    });
    const assignedEl=document.querySelector('[data-allocation-assigned]'), coverageEl=document.querySelector('[data-allocation-coverage]'), unassignedEl=document.querySelector('[data-allocation-unassigned]'), overEl=document.querySelector('[data-allocation-over]'), errorsEl=document.querySelector('[data-allocation-errors]');
    if(assignedEl) assignedEl.textContent=String(state.basicAssigned);
    if(coverageEl){
      let totalHours=0; Object.keys(allocationSlotsData||{}).forEach(function(sid){ totalHours+=Math.max(0,allocationSlotsData[sid].capacity_hours||0); });
      const pct=totalHours>0?(100*state.basicAssigned/totalHours):0;
      coverageEl.textContent=pct.toLocaleString('el-GR',{minimumFractionDigits:1,maximumFractionDigits:1})+'%';
    }
    if(unassignedEl) unassignedEl.textContent=String(state.unassigned);
    if(overEl) overEl.textContent=String(state.overSlots);
    if(errorsEl) errorsEl.textContent=String(errorRows);
    const allocationLiveStatus=document.getElementById('allocationLiveStatus');
    if(allocationLiveStatus) allocationLiveStatus.textContent='Κατανομή: '+state.basicAssigned+' ώρες κατανεμημένες, '+state.unassigned+' ακάλυπτες, '+errorRows+' γραμμές με σφάλμα.';
    const hasAllocationSlots=Object.keys(allocationSlotsData||{}).some(function(sid){return Math.max(0,(allocationSlotsData[sid]&&allocationSlotsData[sid].capacity_hours)||0)>0;});
    const hasAllocationContext=hasAllocationSlots && (staffingContextInitialAllocation || allocationRows().length>0);
    if(staffingContextAssigned){
      staffingContextAssigned.hidden=!hasAllocationContext;
      staffingContextAssigned.innerHTML='<strong>'+state.basicAssigned+'</strong> ώρες κατανεμημένες';
    }
    if(staffingContextUnassigned){
      staffingContextUnassigned.hidden=!hasAllocationContext;
      staffingContextUnassigned.innerHTML='<strong>'+state.unassigned+'</strong> ακάλυπτες';
      staffingContextUnassigned.classList.toggle('has-warning',state.unassigned>0);
    }
    updateAllocationSlotOptionAvailability(slotAssigned);
    updateVacancyView(slotAssigned,personAssigned,personPriority);
    renderSpecialtyBalance(state);
    Object.keys(allocationPeopleData||{}).forEach(function(pid){
      const summary=document.querySelector('[data-allocation-person-summary="'+CSS.escape(pid)+'"]'); if(!summary) return;
      const p=allocationPeopleData[pid], assigned=personAssigned[pid]||0, remain=Math.max(0,p.available_here_hours-assigned), aHours=personPriority[pid].A||0, bHours=personPriority[pid].B||0;
      const a=summary.querySelector('[data-person-assigned]'), r=summary.querySelector('[data-person-remaining]'), av=summary.querySelector('[data-person-a]'), bv=summary.querySelector('[data-person-b]');
      if(a) a.textContent=String(assigned); if(r) r.textContent=String(remain); if(av) av.textContent=String(aHours); if(bv){ bv.textContent=String(bHours)+'/10'; bv.classList.toggle('b-limit-over',bHours>10); }
      const source=summary.querySelector('[data-person-source-summary]');
      if(source){
        let text='Μέσω κύριας '+p.specialty_code+': '+(personSource[pid].primary||0)+' ώρ.';
        if(p.secondary_specialty_code) text+=' · μέσω 2ης '+p.secondary_specialty_code+': '+(personSource[pid].secondary||0)+' ώρ.';
        if((p.external_hours||0)>0) text+=' · '+p.external_hours+' ώρ. σε άλλη μονάδα';
        source.textContent=text;
      }
      const limitWarning=summary.querySelector('[data-person-b-warning]');
      if(limitWarning){ limitWarning.hidden=bHours<=10; limitWarning.textContent=allocationBAssignmentWarning; }
      const assignments=summary.querySelector('[data-person-assignments]');
      if(assignments){
        assignments.innerHTML='';
        const personRows=rowState.filter(function(st){return st.pid===pid&&!st.finalError&&st.match&&st.slot&&st.hours>0;});
        if(!personRows.length){ const empty=document.createElement('div'); empty.className='allocation-person-assignment-item'; empty.setAttribute('data-empty-assignment',''); empty.textContent='Δεν έχουν κατανεμηθεί μαθήματα.'; assignments.appendChild(empty); }
        else personRows.forEach(function(st){ const item=document.createElement('div'); item.className='allocation-person-assignment-item'; item.textContent=st.slot.slot_label+' · '+st.slot.subject+' · '+st.hours+' ώρ. — '+allocationAssignmentLabel(st.match); assignments.appendChild(item); });
      }
    });
  }
  function bindAllocationRow(row){
    if(!row||row.dataset.initialized==='1') return; row.dataset.initialized='1';
    const slot=row.querySelector('.allocation-slot'), hours=row.querySelector('.allocation-hours');
    allocationPopulatePeopleForSlot(row,true);
    if(slot){ const initial=allocationSlotsData[slot.value]||null; if(initial&&hours) hours.max=String(initial.capacity_hours); }
  }
  if(allocationList&&allocationList.dataset.eventsBound!=='1'){
    allocationList.dataset.eventsBound='1';
    allocationList.addEventListener('change',function(event){
      const row=event.target.closest('[data-allocation-row]'); if(!row) return;
      if(event.target.matches('.allocation-slot')){
        const slot=event.target, hours=row.querySelector('.allocation-hours'), data=allocationSlotsData[slot.value]||null;
        allocationPopulatePeopleForSlot(row,false);
        if(data&&hours){ hours.max=String(data.capacity_hours); hours.value=String(data.capacity_hours); }
        else if(hours) hours.value='0';
        updateAllocationSummary(); return;
      }
      if(event.target.matches('.allocation-person')){ updateAllocationSummary(); return; }
    });
    allocationList.addEventListener('input',function(event){ if(event.target.matches('.allocation-hours')) updateAllocationSummary(); });
    allocationList.addEventListener('click',function(event){
      const remove=event.target.closest('.allocation-remove'); if(!remove||!allocationList.contains(remove)) return;
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
      const fragment=allocationTemplate.content.cloneNode(true), row=fragment.querySelector('[data-allocation-row]'); allocationList.appendChild(fragment); bindAllocationRow(row); const first=row.querySelector('.allocation-slot'); if(first) first.focus(); updateAllocationSummary();
    });
  }
  if(clearAllocation&&allocationList){
    clearAllocation.addEventListener('click',function(){
      const rows=allocationRows();
      if(rows.length&&!window.confirm('Να καθαριστεί όλη η τρέχουσα κατανομή μαθημάτων;')) return;
      rows.forEach(function(row){row.remove();});
      let empty=document.getElementById('emptyAllocationState');
      if(!empty){ empty=document.createElement('div'); empty.id='emptyAllocationState'; empty.className='allocation-empty'; empty.textContent='Δεν έχει γίνει ακόμη κατανομή. Πάτησε «Αυτόματη πρόταση κάλυψης» ή «+ Προσθήκη μαθήματος» για να ξεκινήσεις.'; allocationList.appendChild(empty); }
      updateAllocationSummary();
    });
  }
  const allocationViewButtons=Array.from(document.querySelectorAll('[data-allocation-view]'));
  const allocationViewPanels=Array.from(document.querySelectorAll('[data-allocation-view-panel]'));
  function activateAllocationView(button){
    const view=button.getAttribute('data-allocation-view');
    allocationViewButtons.forEach(function(b){
      const active=b===button;
      b.classList.toggle('is-active',active);
      b.setAttribute('aria-selected',active?'true':'false');
      b.tabIndex=active?0:-1;
    });
    allocationViewPanels.forEach(function(panel){ panel.hidden=panel.getAttribute('data-allocation-view-panel')!==view; });
  }
  allocationViewButtons.forEach(function(button){
    button.addEventListener('click',function(){ activateAllocationView(button); });
    button.addEventListener('keydown',function(event){
      if(['ArrowRight','ArrowDown','ArrowLeft','ArrowUp','Home','End'].indexOf(event.key)<0) return;
      event.preventDefault();
      const index=allocationViewButtons.indexOf(button);
      let next=index;
      if(event.key==='ArrowRight'||event.key==='ArrowDown') next=(index+1)%allocationViewButtons.length;
      else if(event.key==='ArrowLeft'||event.key==='ArrowUp') next=(index-1+allocationViewButtons.length)%allocationViewButtons.length;
      else if(event.key==='Home') next=0;
      else if(event.key==='End') next=allocationViewButtons.length-1;
      const target=allocationViewButtons[next];
      activateAllocationView(target);
      target.focus();
    });
  });
  updateAllocationSummary();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
