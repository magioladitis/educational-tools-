<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/school-profile-general-education.php';
require_once __DIR__ . '/includes/school-profile-workload.php';
require_once __DIR__ . '/includes/ethics-class-formation.php';

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
    $map = array(
        'general_sections_required' => 'Χρειάζεται τουλάχιστον ένα τμήμα στην τάξη.',
        'second_foreign_language_groups_required' => 'Χρειάζονται οι πραγματικές ομάδες 2ης ξένης γλώσσας.',
        'at_least_one_orientation_group_required' => 'Χρειάζεται τουλάχιστον μία πραγματική Ομάδα Προσανατολισμού.',
        'science_health_field_groups_required' => 'Χρειάζεται κατανομή ομάδων Μαθηματικών / Βιολογίας στη Γ΄ Θετικών–Υγείας.',
        'science_health_field_groups_empty' => 'Υπάρχει Θετικών–Υγείας αλλά δεν δηλώθηκε ομάδα Μαθηματικών ή Βιολογίας.',
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

$submitted = isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
$schoolType = staffingUiPost('school_type', 'gymnasio');
if ($schoolType !== 'gel') $schoolType = 'gymnasio';
$profile = null;
$readiness = null;
$matrix = null;

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
    $matrix = schoolProfileWorkloadMatrix($profile);
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
    .edu-page-staffing-simulator .mode-tabs{display:flex;gap:8px;flex-wrap:wrap;margin:0 0 14px}
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
    @media(max-width:760px){.edu-page-staffing-simulator .mini-grid,.edu-page-staffing-simulator .mini-grid.two,.edu-page-staffing-simulator .staffing-summary-grid{grid-template-columns:1fr}.edu-page-staffing-simulator .staffing-table th:first-child,.edu-page-staffing-simulator .staffing-table td:first-child{position:static}}
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

  <div class="mode-tabs" aria-label="Στάδια εργαλείου">
    <span class="mode-tab is-active">1. Σχολική μονάδα</span>
    <span class="mode-tab<?php echo $submitted ? ' is-active' : ''; ?>">2. Αποτελέσματα ανά κλάδο</span>
    <span class="mode-tab">3. Εκπαιδευτικοί — επόμενο στάδιο</span>
  </div>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(); ?>
        <h2>1. Στοιχεία σχολικής μονάδας</h2>
        <p class="cap">Η πρώτη έκδοση υποστηρίζει τυπικό Ημερήσιο Γυμνάσιο και Ημερήσιο ΓΕΛ. Οι αριθμοί αφορούν πραγματικά τμήματα / ομάδες διδασκαλίας και όχι οργανικές θέσεις.</p>

        <form method="post" id="staffingProfileForm">
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
              <p class="help">Δήλωσε τις πραγματικές ομάδες γλώσσας, όχι τον αριθμό μαθητών.</p>
              <?php foreach (array('a'=>'Α΄','b'=>'Β΄','c'=>'Γ΄') as $s=>$grade): ?>
                <div class="grade-box">
                  <h4><?php echo $grade; ?> τάξη</h4>
                  <div class="mini-grid">
                    <div class="field"><label>Γαλλικά</label><input min="0" step="1" type="number" name="gym_lang_<?php echo $s; ?>_fr" value="<?php echo staffingUiH(staffingUiPost('gym_lang_'.$s.'_fr', '0')); ?>"></div>
                    <div class="field"><label>Γερμανικά</label><input min="0" step="1" type="number" name="gym_lang_<?php echo $s; ?>_de" value="<?php echo staffingUiH(staffingUiPost('gym_lang_'.$s.'_de', '0')); ?>"></div>
                    <div class="field"><label>Ιταλικά</label><input min="0" step="1" type="number" name="gym_lang_<?php echo $s; ?>_it" value="<?php echo staffingUiH(staffingUiPost('gym_lang_'.$s.'_it', '0')); ?>"></div>
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
                    <div class="field"><label for="gym_tech_split_<?php echo $s; ?>"><?php echo $grade; ?> τάξη <small>τμήματα με &gt;21 μαθητές</small></label><input min="0" step="1" type="number" id="gym_tech_split_<?php echo $s; ?>" name="gym_tech_split_<?php echo $s; ?>" data-max-source="gym_general_<?php echo $s; ?>" value="<?php echo staffingUiH(staffingUiPost('gym_tech_split_'.$s, '0')); ?>"></div>
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
              <div class="mini-grid two">
                <?php foreach (array('a'=>'Α΄','b'=>'Β΄') as $s=>$grade): ?>
                  <div class="grade-box">
                    <h4><?php echo $grade; ?> τάξη</h4>
                    <div class="mini-grid two">
                      <div class="field"><label>Γαλλικά</label><input min="0" step="1" type="number" name="gel_lang_<?php echo $s; ?>_fr" value="<?php echo staffingUiH(staffingUiPost('gel_lang_'.$s.'_fr', '0')); ?>"></div>
                      <div class="field"><label>Γερμανικά</label><input min="0" step="1" type="number" name="gel_lang_<?php echo $s; ?>_de" value="<?php echo staffingUiH(staffingUiPost('gel_lang_'.$s.'_de', '0')); ?>"></div>
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
            <button class="edu-btn-primary" type="submit">Υπολόγισε διδακτικές ανάγκες</button>
            <button class="edu-btn-secondary" type="reset" id="staffingReset">Καθαρισμός</button>
          </div>
        </form>
      <?php calculatorCardEnd(); ?>

      <?php if ($submitted && $matrix): ?>
        <?php calculatorCardStart(array('class'=>'card staffing-results-card')); ?>
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
            <div class="summary-chip"><strong><?php echo (int)$matrix['summary']['ordered_exclusive_top_unit_hours']; ?></strong><span>ώρες αποκλειστικής κορυφαίας Α΄/Β΄/Γ΄</span></div>
            <div class="summary-chip"><strong><?php echo (int)$matrix['summary']['ordered_shared_top_unit_hours']; ?></strong><span>ώρες κοινής κορυφαίας Α΄/Β΄/Γ΄</span></div>
            <div class="summary-chip"><strong><?php echo (int)$matrix['summary']['special_top_unit_hours']; ?></strong><span>ώρες ειδικής κορυφαίας ανάθεσης</span></div>
            <div class="summary-chip"><strong><?php echo (int)$matrix['summary']['active_dependency_instances']; ?></strong><span>ενεργές εκκρεμείς εξαρτήσεις</span></div>
            <div class="summary-chip"><strong><?php echo (int)$matrix['summary']['active_regulatory_gap_instances']; ?></strong><span>επιβεβαιωμένα κανονιστικά κενά</span></div>
          </div>

          <div class="field result-filter">
            <label for="staffingResultFilter">Φίλτρο κλάδου / μαθήματος</label>
            <input id="staffingResultFilter" type="search" placeholder="π.χ. ΠΕ03 ή Μαθηματικά">
          </div>

          <div class="matrix-wrap">
            <table class="staffing-table" id="staffingMatrixTable">
              <thead><tr><th>Κλάδος</th><th>Α΄ επιλεξιμότητα</th><th>Β΄</th><th>Γ΄</th><th>Αποκλειστική κορυφαία</th><th>Κοινή κορυφαία</th><th>Ειδική</th><th>Χαμηλότερη ανάθεση</th></tr></thead>
              <tbody>
              <?php foreach ($matrix['codes'] as $code=>$row): ?>
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
          <p class="help"><strong>Προσοχή:</strong> οι στήλες επιλεξιμότητας Α΄/Β΄/Γ΄ μπορούν να επικαλύπτονται μεταξύ κλάδων. Οι «Αποκλειστικές κορυφαίες» είναι το πιο αυστηρό κομμάτι της στελέχωσης· οι «Κοινές κορυφαίες» απαιτούν πραγματική κατανομή μεταξύ ισότιμων κλάδων.</p>
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
      <div class="result-row"><span>Πραγματικό προσωπικό</span><strong>Επόμενο</strong></div>
      <div class="result-row"><span>Αυτόματες τοποθετήσεις</span><strong>Όχι ακόμη</strong></div>
      <div class="info-note">Το εργαλείο δεν χαρακτηρίζει τις ώρες ως επίσημα «κενά». Για αυτό χρειάζεται το επόμενο επίπεδο με πραγματική κατάσταση εκπαιδευτικών, ώρες υπηρεσίας και ήδη ανατεθειμένο έργο.</div>
      <?php if ($submitted && $matrix): ?>
        <h3>Τρέχων υπολογισμός</h3>
        <div class="result-row"><span>Δομή</span><strong><?php echo $schoolType === 'gel' ? 'Ημερήσιο ΓΕΛ' : 'Ημερήσιο Γυμνάσιο'; ?></strong></div>
        <div class="result-row"><span>Μονάδες αντιστοιχισμένης ανάθεσης</span><strong><?php echo (int)$matrix['summary']['assignment_unit_count']; ?></strong></div>
        <div class="result-row"><span>Κλάδοι με επιλεξιμότητα</span><strong><?php echo (int)$matrix['summary']['staffing_leaf_codes_with_claims']; ?></strong></div>
        <div class="result-row"><span>Κατάσταση</span><strong><?php echo staffingUiH(staffingUiReadinessLabel($matrix['readiness'])); ?></strong></div>
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

<script>
(function(){
  const type=document.getElementById('school_type');
  const gym=document.getElementById('gymProfileFields');
  const gel=document.getElementById('gelProfileFields');
  const reset=document.getElementById('staffingReset');
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
      if((parseInt(input.value||'0',10)||0)>max) input.value=String(max);
    });
  }
  document.querySelectorAll('[id^="gym_general_"]').forEach(function(input){ input.addEventListener('input',syncSplitMaximums); });
  syncSplitMaximums();
  if(reset){ reset.addEventListener('click',function(){ setTimeout(function(){ type.value='gymnasio'; sync(); syncSplitMaximums(); },0); }); }
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
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
