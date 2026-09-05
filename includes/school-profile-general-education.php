<?php
/**
 * Builders / validators για τυπικό Ημερήσιο Γυμνάσιο και Ημερήσιο ΓΕΛ.
 *
 * Σκοπός: να τροφοδοτούν το κοινό school-profile -> workload -> personnel
 * pipeline χωρίς να συγχέουν τα κανονικά τμήματα τάξης με ομάδες ξένης
 * γλώσσας, ομάδες προσανατολισμού ή τμήματα Ηθικής.
 *
 * Δεν φορτώνεται από δημόσια σελίδα.
 */

require_once __DIR__ . '/school-profile.php';

function schoolProfileNormalizeGradeCounts($counts, $grades)
{
    $result = array();
    foreach ($grades as $grade) {
        $result[$grade] = isset($counts[$grade]) ? max(0, (int) $counts[$grade]) : 0;
    }
    return $result;
}

function schoolProfileNormalizeOptionGroups($groupsByGrade, $grades, $allowedLabels)
{
    $result = array();
    foreach ($grades as $grade) {
        if (!isset($groupsByGrade[$grade]) || !is_array($groupsByGrade[$grade])) {
            continue;
        }
        foreach ($allowedLabels as $label) {
            if (array_key_exists($label, $groupsByGrade[$grade])) {
                $result[$grade][$label] = max(0, (int) $groupsByGrade[$grade][$label]);
            }
        }
    }
    return $result;
}

function schoolProfileBuildDayGymnasium2026($config)
{
    $grades = array('Α΄','Β΄','Γ΄');
    $general = schoolProfileNormalizeGradeCounts(
        isset($config['general_sections']) ? $config['general_sections'] : array(),
        $grades
    );
    $languageGroups = schoolProfileNormalizeOptionGroups(
        isset($config['second_foreign_language_groups']) ? $config['second_foreign_language_groups'] : array(),
        $grades,
        array('Γαλλικά','Γερμανικά','Ιταλικά')
    );

    $choiceOptions = array();
    foreach ($languageGroups as $grade => $groups) {
        $choiceOptions[$grade]['gym.deyteri_xeni'] = $groups;
    }

    return array(
        'profile_id' => isset($config['profile_id']) ? $config['profile_id'] : 'day-gymnasium-2026-2027',
        'school_year' => isset($config['school_year']) ? $config['school_year'] : '2026-2027',
        'school' => isset($config['school']) ? $config['school'] : array('type'=>'Ημερήσιο Γυμνάσιο'),
        'source' => isset($config['source']) ? $config['source'] : array('kind'=>'manual_school_profile'),
        'structures' => array(
            'gymnasio' => array(
                'general_sections' => $general,
                'choice_option_sections' => $choiceOptions,
                'conditions' => array(),
            ),
        ),
        'ethics' => array(
            'formation_policy_scope' => 'in_scope',
            'by_structure_grade' => array(
                'gymnasio' => isset($config['ethics_by_grade']) && is_array($config['ethics_by_grade'])
                    ? $config['ethics_by_grade'] : array(),
            ),
        ),
    );
}

function schoolProfileBuildDayGel2026($config)
{
    $grades = array('Α΄','Β΄','Γ΄');
    $general = schoolProfileNormalizeGradeCounts(
        isset($config['general_sections']) ? $config['general_sections'] : array(),
        $grades
    );

    $trackSections = array(
        'Β΄' => array(
            'humanities' => 0,
            'science' => 0,
        ),
        'Γ΄' => array(
            'humanities' => 0,
            'science_health' => 0,
            'economics_it' => 0,
        ),
    );
    if (!empty($config['orientation_sections']) && is_array($config['orientation_sections'])) {
        foreach ($trackSections as $grade => $tracks) {
            foreach ($tracks as $track => $zero) {
                if (isset($config['orientation_sections'][$grade][$track])) {
                    $trackSections[$grade][$track] = max(0, (int) $config['orientation_sections'][$grade][$track]);
                }
            }
        }
    }

    $languageGroups = schoolProfileNormalizeOptionGroups(
        isset($config['second_foreign_language_groups']) ? $config['second_foreign_language_groups'] : array(),
        array('Α΄','Β΄'),
        array('Γαλλικά','Γερμανικά')
    );
    $choiceOptionSections = array();
    foreach ($languageGroups as $grade => $groups) {
        $choiceOptionSections[$grade]['gel.general.deyteri_xeni'] = $groups;
    }

    $fieldGroups = array('Μαθηματικά'=>0, 'Βιολογία'=>0);
    if (!empty($config['grade_c_science_health_field_groups']) && is_array($config['grade_c_science_health_field_groups'])) {
        foreach ($fieldGroups as $label => $zero) {
            if (isset($config['grade_c_science_health_field_groups'][$label])) {
                $fieldGroups[$label] = max(0, (int) $config['grade_c_science_health_field_groups'][$label]);
            }
        }
    }
    $choiceSections = array(
        'Γ΄' => array(
            'gel.c.health.field_choice' => array(
                'gel.c.health.mathimatika' => $fieldGroups['Μαθηματικά'],
                'gel.c.health.viologia' => $fieldGroups['Βιολογία'],
            ),
        ),
    );

    // Δεν συναγάγουμε αυτομάτως πόσα teaching groups σχηματίζονται για τα
    // δύο conditional μαθήματα Γενικής Παιδείας της Γ΄. Το profile τα δέχεται
    // ως πραγματικό input, επειδή η ομαδοποίηση μπορεί να μη συμπίπτει με τα
    // κανονικά τμήματα ούτε με το άθροισμα των ομάδων προσανατολισμού.
    $conditional = array();
    if (!empty($config['grade_c_conditional_groups']) && is_array($config['grade_c_conditional_groups'])) {
        if (array_key_exists('Μαθηματικά', $config['grade_c_conditional_groups'])) {
            $conditional['Γ΄']['gel.general.mathimatika_conditional'] = max(0, (int) $config['grade_c_conditional_groups']['Μαθηματικά']);
        }
        if (array_key_exists('Ιστορία', $config['grade_c_conditional_groups'])) {
            $conditional['Γ΄']['gel.general.istoria'] = max(0, (int) $config['grade_c_conditional_groups']['Ιστορία']);
        }
    }

    return array(
        'profile_id' => isset($config['profile_id']) ? $config['profile_id'] : 'day-gel-2026-2027',
        'school_year' => isset($config['school_year']) ? $config['school_year'] : '2026-2027',
        'school' => isset($config['school']) ? $config['school'] : array('type'=>'Ημερήσιο Γενικό Λύκειο'),
        'source' => isset($config['source']) ? $config['source'] : array('kind'=>'manual_school_profile'),
        'structures' => array(
            'gel' => array(
                'general_sections' => $general,
                'track_sections' => $trackSections,
                'choice_option_sections' => $choiceOptionSections,
                'choice_sections' => $choiceSections,
                'conditional_sections' => $conditional,
                'conditions' => array(),
            ),
        ),
        'ethics' => array(
            'formation_policy_scope' => 'in_scope',
            'by_structure_grade' => array(
                'gel' => isset($config['ethics_by_grade']) && is_array($config['ethics_by_grade'])
                    ? $config['ethics_by_grade'] : array(),
            ),
        ),
    );
}

function schoolProfileGeneralEducationReadiness($profile)
{
    $issues = array();
    $structures = isset($profile['structures']) && is_array($profile['structures']) ? $profile['structures'] : array();

    if (isset($structures['gymnasio'])) {
        $s = $structures['gymnasio'];
        foreach (array('Α΄','Β΄','Γ΄') as $grade) {
            if (schoolProfileGeneralSectionCount($s, $grade) < 1) {
                $issues[] = 'gymnasio:' . $grade . ':general_sections_required';
            }
            $language = schoolProfileChoiceOptionSections($s, $grade, 'gym.deyteri_xeni');
            if ($language === null || array_sum($language) < 1) {
                $issues[] = 'gymnasio:' . $grade . ':second_foreign_language_groups_required';
            }
        }
    }

    if (isset($structures['gel'])) {
        $s = $structures['gel'];
        foreach (array('Α΄','Β΄','Γ΄') as $grade) {
            if (schoolProfileGeneralSectionCount($s, $grade) < 1) {
                $issues[] = 'gel:' . $grade . ':general_sections_required';
            }
        }
        $bTrackTotal = 0;
        foreach (array('humanities','science') as $track) {
            if (!isset($s['track_sections']['Β΄'][$track])) {
                $issues[] = 'gel:Β΄:orientation_sections:' . $track . ':required';
            } else {
                $bTrackTotal += max(0, (int) $s['track_sections']['Β΄'][$track]);
            }
        }
        if (schoolProfileGeneralSectionCount($s, 'Β΄') > 0 && $bTrackTotal < 1) {
            $issues[] = 'gel:Β΄:at_least_one_orientation_group_required';
        }

        $cTrackTotal = 0;
        foreach (array('humanities','science_health','economics_it') as $track) {
            if (!isset($s['track_sections']['Γ΄'][$track])) {
                $issues[] = 'gel:Γ΄:orientation_sections:' . $track . ':required';
            } else {
                $cTrackTotal += max(0, (int) $s['track_sections']['Γ΄'][$track]);
            }
        }
        if (schoolProfileGeneralSectionCount($s, 'Γ΄') > 0 && $cTrackTotal < 1) {
            $issues[] = 'gel:Γ΄:at_least_one_orientation_group_required';
        }

        foreach (array('Α΄','Β΄') as $grade) {
            $language = schoolProfileChoiceOptionSections($s, $grade, 'gel.general.deyteri_xeni');
            if ($language === null || array_sum($language) < 1) {
                $issues[] = 'gel:' . $grade . ':second_foreign_language_groups_required';
            }
        }

        $mathField = schoolProfileExplicitChoiceSectionCount($s, 'Γ΄', 'gel.c.health.field_choice', 'gel.c.health.mathimatika');
        $bioField = schoolProfileExplicitChoiceSectionCount($s, 'Γ΄', 'gel.c.health.field_choice', 'gel.c.health.viologia');
        if ($mathField === null || $bioField === null) {
            $issues[] = 'gel:Γ΄:science_health_field_groups_required';
        } elseif (!empty($s['track_sections']['Γ΄']['science_health']) && ($mathField + $bioField) < 1) {
            $issues[] = 'gel:Γ΄:science_health_field_groups_empty';
        }

        $mathConditional = schoolProfileConditionalSectionCount($s, 'Γ΄', 'gel.general.mathimatika_conditional');
        $historyConditional = schoolProfileConditionalSectionCount($s, 'Γ΄', 'gel.general.istoria');
        if (!empty($s['track_sections']['Γ΄']['humanities']) && ($mathConditional === null || $mathConditional < 1)) {
            $issues[] = 'gel:Γ΄:conditional_groups:gel.general.mathimatika_conditional:required';
        }
        $nonHumanitiesGroups = max(0, (int) $s['track_sections']['Γ΄']['science_health'])
            + max(0, (int) $s['track_sections']['Γ΄']['economics_it']);
        if ($nonHumanitiesGroups > 0 && ($historyConditional === null || $historyConditional < 1)) {
            $issues[] = 'gel:Γ΄:conditional_groups:gel.general.istoria:required';
        }
    }

    return array(
        'ready' => count($issues) === 0,
        'issues' => $issues,
        'note' => 'Η απουσία inputs Ηθικής δεν ακυρώνει τη δομική ετοιμότητα· αφήνει μόνο τα αντίστοιχα religion/ethics slots ως unresolved dependency.',
    );
}
