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
function staffingUiRenderAllocationPersonSelectedOption($people, $selected) {
    echo '<option value="">— επίλεξε πρώτα μάθημα / τμήμα —</option>';
    $selected = (string)$selected;
    if ($selected === '') return;
    foreach ($people as $person) {
        $id = isset($person['person_id']) ? (string)$person['person_id'] : '';
        if ($id !== $selected) continue;
        echo '<option value="' . staffingUiH($id) . '" selected>' . staffingUiH(staffingUiAllocationPersonLabel($person)) . '</option>';
        return;
    }
    echo '<option value="' . staffingUiH($selected) . '" selected>Μη έγκυρος εκπαιδευτικός</option>';
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
function staffingUiRenderAllocationSlotSelectedOption($slots, $selected) {
    echo '<option value="">— επιλογή τμήματος / ομάδας και μαθήματος —</option>';
    $selected = (string)$selected;
    if ($selected === '') return;
    if (isset($slots[$selected])) {
        $slot = $slots[$selected];
        echo '<option value="' . staffingUiH($selected) . '" data-capacity="' . (int)$slot['capacity_hours'] . '" selected>' . staffingUiH(staffingUiAllocationSlotOptionLabel($slot)) . '</option>';
        return;
    }
    echo '<option value="' . staffingUiH($selected) . '" selected disabled>Μη έγκυρο μάθημα / τμήμα</option>';
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
    // Δεν φορτώνουμε όλους τους .50 σε κάθε select: αν υπάρχει ήδη εισαγμένη
    // εγγραφή ΕΑΕ, προσθέτουμε μόνο τον δικό της κωδικό. Έτσι δεν αυξάνεται
    // άσκοπα το HTML/DOM σε μεγάλα μητρώα προσωπικού.
    if ($selected !== '' && teacherSpecialtyIsEaeCode($selected)
        && !in_array($selected, isset($options['relevant']) ? $options['relevant'] : array(), true)
        && !in_array($selected, isset($options['other']) ? $options['other'] : array(), true)) {
        echo '<optgroup label="Ειδική Αγωγή (.50)">';
        echo '<option value="' . staffingUiH($selected) . '" selected>' . staffingUiH(teacherSpecialtyDisplay($selected)) . '</option>';
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
    // Build only the regulatory workload needed by this school profile.
    // A Gymnasium request must not materialise EPAL/PEPAL/ENEEGYL/etc. data.
    // Composite Gymnasium + Lyceum Classes naturally requests both structures.
    $teachingModel = teachingWorkloadModelForProfile($profile);
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
    'general_resolved_count'=>0,
    'general_available_here_hours'=>0,
    'eae_people_count'=>0,
    'eae_available_here_hours'=>0,
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
        $isEaePerson = teacherSpecialtyIsEaeCode($code);
        if ($isEaePerson) $personnelSummary['eae_people_count']++;
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
        if ($isEaePerson) {
            $personnelSummary['eae_available_here_hours'] += $available;
        } else {
            $personnelSummary['general_resolved_count']++;
            $personnelSummary['general_available_here_hours'] += $available;
        }
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
    $personCode = isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '';
    // Οι εκπαιδευτικοί .50 ανήκουν στην ΕΑΕ. Οι ώρες τους αποτυπώνονται
    // χωριστά και δεν τροφοδοτούν την αυτόματη κατανομή μαθημάτων Γενικής.
    if (teacherSpecialtyIsEaeCode($personCode)) continue;
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
$allocationEnabled = $submitted && $profile && $matrix && $matrix['readiness'] !== 'structure_only' && $personnelSummary['general_resolved_count'] > 0 && !empty($allocationPeople) && empty($duplicateDirectorIndexes);
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
// Η Καρτέλα 6 ανανεώνεται πλήρως client-side όταν ανοίγει. Σε shared hosting
// δεν εκτελούμε τον βαρύ optimizer σε κάθε POST της Καρτέλας 3/4, γιατί σε
// μεγάλα σχολεία αυτό διπλασίαζε άσκοπα CPU και μνήμη και μπορούσε να δώσει 500.
$specialtyBalanceServerNeeded = $specialtyBalanceEnabled && $activePanel === 'specialties';
if ($specialtyBalanceServerNeeded) {
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
              <div class="summary-chip"><strong><?php echo (int)$personnelSummary['available_here_hours']; ?></strong><span>ώρες διαθέσιμες συνολικά στη συγκεκριμένη μονάδα</span></div>
              <div class="summary-chip eae-hours-chip"><strong><?php echo (int)$personnelSummary['eae_available_here_hours']; ?></strong><span>Διαθέσιμες ώρες ΕΑΕ <small>(κωδικοί .50)</small></span></div>
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
            <p class="help">Η σύνοψη δείχνει το διαθέσιμο διδακτικό ωράριο του προσωπικού ανά κλάδο. Οι εκπαιδευτικοί με κωδικό που λήγει σε <strong>.50</strong> αναγνωρίζονται ως ΕΑΕ, οι ώρες τους εμφανίζονται χωριστά και δεν συμμετέχουν στην αυτόματη κατανομή μαθημάτων Γενικής Εκπαίδευσης. Η αντιστοίχιση με συγκεκριμένα μαθήματα γίνεται στην επόμενη καρτέλα.</p>
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
                        <div class="field"><label>Τμήμα / ομάδα · μάθημα</label><select name="allocation_slot_id[]" class="allocation-slot" data-lazy-options="slot" aria-label="Μάθημα και τμήμα προς κατανομή"><?php staffingUiRenderAllocationSlotSelectedOption($allocationSlots, isset($allocation['slot_id']) ? $allocation['slot_id'] : ''); ?></select></div>
                        <div class="field"><label>Εκπαιδευτικός</label><select name="allocation_person_id[]" class="allocation-person" data-lazy-options="person" aria-label="Εκπαιδευτικός για την κατανομή"<?php echo empty($allocation['slot_id']) ? ' disabled' : ''; ?>><?php staffingUiRenderAllocationPersonSelectedOption($allocationPeople, isset($allocation['person_id']) ? $allocation['person_id'] : ''); ?></select></div>
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
                    <div class="field"><label>Τμήμα / ομάδα · μάθημα</label><select name="allocation_slot_id[]" class="allocation-slot" data-lazy-options="slot" aria-label="Μάθημα και τμήμα προς κατανομή"><option value="">— επιλογή τμήματος / ομάδας και μαθήματος —</option></select></div>
                    <div class="field"><label>Εκπαιδευτικός</label><select name="allocation_person_id[]" class="allocation-person" data-lazy-options="person" aria-label="Εκπαιδευτικός για την κατανομή" disabled><option value="">— επίλεξε πρώτα μάθημα / τμήμα —</option></select></div>
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
            <p class="help">Οι κανονιστικοί πίνακες αναθέσεων διατηρούνται αυτούσιοι. Για νέα πρόταση κενού ο <strong>ΠΕ04.03</strong> αντιμετωπίζεται ως legacy κλάδος και, όταν υπάρχει σημερινός εναλλακτικός κλάδος στην καλύτερη διαθέσιμη ανάθεση, δεν προτείνεται. Έτσι στη Γεωλογία-Γεωγραφία προηγείται ο <strong>ΠΕ04.05</strong>.</p>
            <div class="matrix-wrap">
              <table class="staffing-table specialty-smart-table"><caption class="edu-tools-sr-only">Αιτιολόγηση επιλογής κλάδου για κοινά κενά</caption><thead><tr><th scope="col">Τμήμα / ομάδα</th><th scope="col">Μάθημα</th><th scope="col">Ώρες</th><th scope="col">Προτεινόμενος κλάδος</th><th scope="col">Νόμιμες ισότιμες εναλλακτικές</th></tr></thead><tbody id="specialtySmartBody">
              <?php if ($specialtyBalanceReport): foreach ($specialtyBalanceReport['vacancy_recommendations'] as $smartRow): $legalSmartCodes=isset($smartRow['legal_candidate_codes'])?$smartRow['legal_candidate_codes']:$smartRow['candidate_codes']; if (count($legalSmartCodes) < 2) continue; ?>
                <tr><td><?php echo staffingUiH($smartRow['slot_label']); ?></td><td><?php echo staffingUiH($smartRow['subject']); ?></td><td><?php echo (int)$smartRow['hours']; ?></td><td><strong><?php echo staffingUiH($smartRow['selected_code']); ?></strong></td><td><?php echo staffingUiH(implode(', ', $legalSmartCodes)); ?></td></tr>
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
    <div class="print-summary-item"><strong><?php echo (int)$personnelSummary['eae_available_here_hours']; ?></strong><span>διαθέσιμες ώρες ΕΑΕ (.50)</span></div>
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

<script type="application/json" id="staffingRuntimeConfig"><?php echo json_encode(array(
  'maxBasicSections' => (int) STAFFING_UI_MAX_BASIC_SECTIONS,
  'initialAllocation' => $allocationPlan ? true : false,
  'hasCalculatedResults' => $calculationAvailable ? true : false,
  'allocationPeople' => $allocationPeopleClient,
  'allocationSlots' => $allocationSlotsClient,
  'specialtyLabels' => $specialtyLabelsClient
), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
<script src="<?php echo staffingUiH(edu_asset_url('includes/staffing-simulator-ui.js')); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
