<?php
/**
 * Builders / validators για Γυμνάσιο / ΓΕΛ γενικής εκπαίδευσης.
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

    $validationIssues = array();
    foreach ($languageGroups as $grade => $groups) {
        foreach ($groups as $language => $count) {
            if ($count > $general[$grade]) {
                $validationIssues[] = 'gymnasio:' . $grade
                    . ':second_foreign_language_groups_exceeds_general_sections:'
                    . $language . ':' . $count . '>' . $general[$grade];
            }
        }
    }

    $choiceOptions = array();
    foreach ($languageGroups as $grade => $groups) {
        $choiceOptions[$grade]['gym.deyteri_xeni'] = $groups;
    }

    // Υ.Α. 74472/Δ2/2020 (Β΄ 2450): όταν ένα τμήμα έχει πάνω από 21
    // μαθητές/ήτριες, δημιουργείται δεύτερη ομάδα στο πεδίο Τεχνολογία /
    // Πληροφορική. Στην Α΄ η ίδια ομαδοποίηση αφορά και την Οικιακή Οικονομία.
    // Αποθηκεύουμε μόνο τις *επιπλέον* ομάδες, μία ανά τμήμα που χωρίζεται.
    $techSplitRequested = schoolProfileNormalizeGradeCounts(
        isset($config['technology_informatics_split_sections'])
            ? $config['technology_informatics_split_sections'] : array(),
        $grades
    );
    $techSplit = $techSplitRequested;
    foreach ($grades as $grade) {
        if ($techSplitRequested[$grade] > $general[$grade]) {
            $validationIssues[] = 'gymnasio:' . $grade . ':technology_informatics_split_sections_exceeds_general_sections:'
                . $techSplitRequested[$grade] . '>' . $general[$grade];
        }
        // Defensive clamp: ακόμη και σε crafted POST ή απευθείας κλήση builder,
        // δεν επιτρέπουμε να δημιουργηθούν περισσότερες split ομάδες από τα
        // πραγματικά δηλωμένα τμήματα της τάξης.
        $techSplit[$grade] = min($techSplitRequested[$grade], $general[$grade]);
    }
    $extraCourseSections = array(
        'Α΄' => array(
            'gym.texnologia' => $techSplit['Α΄'],
            'gym.pliroforiki' => $techSplit['Α΄'],
            'gym.oikiaki_oikonomia' => $techSplit['Α΄'],
        ),
        'Β΄' => array(
            'gym.texnologia' => $techSplit['Β΄'],
            'gym.pliroforiki' => $techSplit['Β΄'],
        ),
        'Γ΄' => array(
            'gym.texnologia' => $techSplit['Γ΄'],
            'gym.pliroforiki' => $techSplit['Γ΄'],
        ),
    );

    return array(
        'profile_id' => isset($config['profile_id']) ? $config['profile_id'] : 'day-gymnasium-2026-2027',
        'school_year' => isset($config['school_year']) ? $config['school_year'] : '2026-2027',
        'school' => isset($config['school']) ? $config['school'] : array('type'=>'Ημερήσιο Γυμνάσιο'),
        'source' => isset($config['source']) ? $config['source'] : array('kind'=>'manual_school_profile'),
        'structures' => array(
            'gymnasio' => array(
                'general_sections' => $general,
                'choice_option_sections' => $choiceOptions,
                'technology_informatics_split_sections' => $techSplit,
                'extra_course_sections' => $extraCourseSections,
                'conditions' => array(),
            ),
        ),
        'validation_issues' => $validationIssues,
        'ethics' => array(
            'formation_policy_scope' => 'in_scope',
            'by_structure_grade' => array(
                'gymnasio' => isset($config['ethics_by_grade']) && is_array($config['ethics_by_grade'])
                    ? $config['ethics_by_grade'] : array(),
            ),
        ),
    );
}


function schoolProfileBuildEveningGymnasium2026($config)
{
    $grades = array('Α΄','Β΄','Γ΄');
    $general = schoolProfileNormalizeGradeCounts(
        isset($config['general_sections']) ? $config['general_sections'] : array(),
        $grades
    );

    return array(
        'profile_id' => isset($config['profile_id']) ? $config['profile_id'] : 'evening-gymnasium-2026-2027',
        'school_year' => isset($config['school_year']) ? $config['school_year'] : '2026-2027',
        'school' => isset($config['school']) ? $config['school'] : array('type'=>'Εσπερινό Γυμνάσιο'),
        'source' => isset($config['source']) ? $config['source'] : array('kind'=>'manual_school_profile'),
        'structures' => array(
            'esperino_gymnasio' => array(
                'general_sections' => $general,
                'choice_option_sections' => array(),
                'extra_course_sections' => array(),
                'conditions' => array(),
            ),
        ),
        'validation_issues' => array(),
        'ethics' => array(
            'formation_policy_scope' => 'in_scope',
            'by_structure_grade' => array(
                'esperino_gymnasio' => isset($config['ethics_by_grade']) && is_array($config['ethics_by_grade'])
                    ? $config['ethics_by_grade'] : array(),
            ),
        ),
    );
}

function schoolProfileBuildEveningGel2026($config)
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
            'egel.c.health.field_choice' => array(
                'egel.c.health.mathimatika' => $fieldGroups['Μαθηματικά'],
                'egel.c.health.viologia' => $fieldGroups['Βιολογία'],
            ),
        ),
    );

    $conditional = array();
    if (!empty($config['grade_c_conditional_groups']) && is_array($config['grade_c_conditional_groups'])) {
        if (array_key_exists('Μαθηματικά', $config['grade_c_conditional_groups'])) {
            $conditional['Γ΄']['egel.general.mathimatika_conditional'] = max(0, (int) $config['grade_c_conditional_groups']['Μαθηματικά']);
        }
        if (array_key_exists('Ιστορία', $config['grade_c_conditional_groups'])) {
            $conditional['Γ΄']['egel.general.istoria'] = max(0, (int) $config['grade_c_conditional_groups']['Ιστορία']);
        }
    }

    // Στη Β΄ Εσπερινού ΓΕΛ Χημεία και Βιολογία εναλλάσσονται 1/2 και 2/1
    // ώρες ανά τετράμηνο. Ο υπολογιστής χρειάζεται ρητό τετράμηνο ώστε οι
    // ώρες ανά κλάδο να παραμένουν ακριβείς χωρίς να μετατρέπουμε το 1/2 σε
    // αυθαίρετο ετήσιο μέσο όρο.
    $period = isset($config['grade_b_period']) ? trim((string) $config['grade_b_period']) : 'Α΄ τετράμηνο';
    $validationIssues = array();
    if (!in_array($period, array('Α΄ τετράμηνο','Β΄ τετράμηνο'), true)) {
        $validationIssues[] = 'esperino_gel:Β΄:period_selection_invalid';
        $period = 'Α΄ τετράμηνο';
    }

    return array(
        'profile_id' => isset($config['profile_id']) ? $config['profile_id'] : 'evening-gel-2026-2027',
        'school_year' => isset($config['school_year']) ? $config['school_year'] : '2026-2027',
        'school' => isset($config['school']) ? $config['school'] : array('type'=>'Εσπερινό Γενικό Λύκειο'),
        'source' => isset($config['source']) ? $config['source'] : array('kind'=>'manual_school_profile'),
        'structures' => array(
            'esperino_gel' => array(
                'general_sections' => $general,
                'track_sections' => $trackSections,
                'choice_option_sections' => array(),
                'choice_sections' => $choiceSections,
                'conditional_sections' => $conditional,
                'period_selection' => array('Β΄' => $period),
                'conditions' => array(),
            ),
        ),
        'validation_issues' => $validationIssues,
        'ethics' => array(
            'formation_policy_scope' => 'in_scope',
            'by_structure_grade' => array(
                'esperino_gel' => isset($config['ethics_by_grade']) && is_array($config['ethics_by_grade'])
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
    $validationIssues = array();
    foreach ($languageGroups as $grade => $groups) {
        foreach ($groups as $language => $count) {
            if ($count > $general[$grade]) {
                $validationIssues[] = 'gel:' . $grade
                    . ':second_foreign_language_groups_exceeds_general_sections:'
                    . $language . ':' . $count . '>' . $general[$grade];
            }
        }
    }
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
        'validation_issues' => $validationIssues,
        'ethics' => array(
            'formation_policy_scope' => 'in_scope',
            'by_structure_grade' => array(
                'gel' => isset($config['ethics_by_grade']) && is_array($config['ethics_by_grade'])
                    ? $config['ethics_by_grade'] : array(),
            ),
        ),
    );
}


/**
 * Composite profile για Ημερήσιο Γυμνάσιο με Λυκειακές Τάξεις.
 *
 * Δεν αντιγράφει κανονιστικά δεδομένα: συνθέτει αυτούσια τα δύο ήδη
 * ελεγμένα profiles Ημερήσιου Γυμνασίου και Ημερήσιου ΓΕΛ σε μία σχολική
 * μονάδα, ώστε προσωπικό, υποχρεωτικό ωράριο και allocation engine να
 * λειτουργούν πάνω σε κοινό pool.
 */
function schoolProfileBuildGymnasiumWithLyceumClasses2026($config)
{
    $commonSchool = isset($config['school']) ? $config['school'] : array('type'=>'Γυμνάσιο με Λυκειακές Τάξεις');
    $commonSource = isset($config['source']) ? $config['source'] : array('kind'=>'manual_school_profile');
    $schoolYear = isset($config['school_year']) ? $config['school_year'] : '2026-2027';

    $gym = schoolProfileBuildDayGymnasium2026(array(
        'profile_id' => 'composite-gymnasium-part',
        'school_year' => $schoolYear,
        'school' => $commonSchool,
        'source' => $commonSource,
        'general_sections' => isset($config['gymnasium_general_sections']) ? $config['gymnasium_general_sections'] : array(),
        'second_foreign_language_groups' => isset($config['gymnasium_second_foreign_language_groups']) ? $config['gymnasium_second_foreign_language_groups'] : array(),
        'technology_informatics_split_sections' => isset($config['gymnasium_technology_informatics_split_sections']) ? $config['gymnasium_technology_informatics_split_sections'] : array(),
        'ethics_by_grade' => isset($config['gymnasium_ethics_by_grade']) ? $config['gymnasium_ethics_by_grade'] : array(),
    ));

    $gel = schoolProfileBuildDayGel2026(array(
        'profile_id' => 'composite-lyceum-part',
        'school_year' => $schoolYear,
        'school' => $commonSchool,
        'source' => $commonSource,
        'general_sections' => isset($config['lyceum_general_sections']) ? $config['lyceum_general_sections'] : array(),
        'second_foreign_language_groups' => isset($config['lyceum_second_foreign_language_groups']) ? $config['lyceum_second_foreign_language_groups'] : array(),
        'orientation_sections' => isset($config['lyceum_orientation_sections']) ? $config['lyceum_orientation_sections'] : array(),
        'grade_c_science_health_field_groups' => isset($config['lyceum_grade_c_science_health_field_groups']) ? $config['lyceum_grade_c_science_health_field_groups'] : array(),
        'grade_c_conditional_groups' => isset($config['lyceum_grade_c_conditional_groups']) ? $config['lyceum_grade_c_conditional_groups'] : array(),
        'ethics_by_grade' => isset($config['lyceum_ethics_by_grade']) ? $config['lyceum_ethics_by_grade'] : array(),
    ));

    return array(
        'profile_id' => isset($config['profile_id']) ? $config['profile_id'] : 'gymnasium-with-lyceum-classes-2026-2027',
        'school_year' => $schoolYear,
        'school' => $commonSchool,
        'source' => $commonSource,
        'structures' => array(
            'gymnasio' => $gym['structures']['gymnasio'],
            'gel' => $gel['structures']['gel'],
        ),
        'validation_issues' => array_values(array_merge(
            isset($gym['validation_issues']) ? $gym['validation_issues'] : array(),
            isset($gel['validation_issues']) ? $gel['validation_issues'] : array()
        )),
        'ethics' => array(
            'formation_policy_scope' => 'in_scope',
            'by_structure_grade' => array(
                'gymnasio' => isset($gym['ethics']['by_structure_grade']['gymnasio']) ? $gym['ethics']['by_structure_grade']['gymnasio'] : array(),
                'gel' => isset($gel['ethics']['by_structure_grade']['gel']) ? $gel['ethics']['by_structure_grade']['gel'] : array(),
            ),
        ),
        'composite' => array(
            'kind' => 'gymnasium_with_lyceum_classes',
            'shared_personnel_pool' => true,
            'structures' => array('gymnasio','gel'),
        ),
    );
}

function schoolProfileGeneralEducationReadiness($profile)
{
    $issues = isset($profile['validation_issues']) && is_array($profile['validation_issues'])
        ? array_values($profile['validation_issues']) : array();
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

    if (isset($structures['esperino_gymnasio'])) {
        $s = $structures['esperino_gymnasio'];
        foreach (array('Α΄','Β΄','Γ΄') as $grade) {
            if (schoolProfileGeneralSectionCount($s, $grade) < 1) {
                $issues[] = 'esperino_gymnasio:' . $grade . ':general_sections_required';
            }
        }
    }

    if (isset($structures['esperino_gel'])) {
        $s = $structures['esperino_gel'];
        foreach (array('Α΄','Β΄','Γ΄') as $grade) {
            if (schoolProfileGeneralSectionCount($s, $grade) < 1) {
                $issues[] = 'esperino_gel:' . $grade . ':general_sections_required';
            }
        }

        $bTrackTotal = 0;
        foreach (array('humanities','science') as $track) {
            if (!isset($s['track_sections']['Β΄'][$track])) {
                $issues[] = 'esperino_gel:Β΄:orientation_sections:' . $track . ':required';
            } else {
                $bTrackTotal += max(0, (int) $s['track_sections']['Β΄'][$track]);
            }
        }
        if (schoolProfileGeneralSectionCount($s, 'Β΄') > 0 && $bTrackTotal < 1) {
            $issues[] = 'esperino_gel:Β΄:at_least_one_orientation_group_required';
        }

        $cTrackTotal = 0;
        foreach (array('humanities','science_health','economics_it') as $track) {
            if (!isset($s['track_sections']['Γ΄'][$track])) {
                $issues[] = 'esperino_gel:Γ΄:orientation_sections:' . $track . ':required';
            } else {
                $cTrackTotal += max(0, (int) $s['track_sections']['Γ΄'][$track]);
            }
        }
        if (schoolProfileGeneralSectionCount($s, 'Γ΄') > 0 && $cTrackTotal < 1) {
            $issues[] = 'esperino_gel:Γ΄:at_least_one_orientation_group_required';
        }

        $mathField = schoolProfileExplicitChoiceSectionCount($s, 'Γ΄', 'egel.c.health.field_choice', 'egel.c.health.mathimatika');
        $bioField = schoolProfileExplicitChoiceSectionCount($s, 'Γ΄', 'egel.c.health.field_choice', 'egel.c.health.viologia');
        if ($mathField === null || $bioField === null) {
            $issues[] = 'esperino_gel:Γ΄:science_health_field_groups_required';
        } elseif (!empty($s['track_sections']['Γ΄']['science_health']) && ($mathField + $bioField) < 1) {
            $issues[] = 'esperino_gel:Γ΄:science_health_field_groups_empty';
        }

        $mathConditional = schoolProfileConditionalSectionCount($s, 'Γ΄', 'egel.general.mathimatika_conditional');
        $historyConditional = schoolProfileConditionalSectionCount($s, 'Γ΄', 'egel.general.istoria');
        if (!empty($s['track_sections']['Γ΄']['humanities']) && ($mathConditional === null || $mathConditional < 1)) {
            $issues[] = 'esperino_gel:Γ΄:conditional_groups:egel.general.mathimatika_conditional:required';
        }
        $nonHumanitiesGroups = max(0, (int) $s['track_sections']['Γ΄']['science_health'])
            + max(0, (int) $s['track_sections']['Γ΄']['economics_it']);
        if ($nonHumanitiesGroups > 0 && ($historyConditional === null || $historyConditional < 1)) {
            $issues[] = 'esperino_gel:Γ΄:conditional_groups:egel.general.istoria:required';
        }

        $period = isset($s['period_selection']['Β΄']) ? $s['period_selection']['Β΄'] : '';
        if (schoolProfileGeneralSectionCount($s, 'Β΄') > 0 && !in_array($period, array('Α΄ τετράμηνο','Β΄ τετράμηνο'), true)) {
            $issues[] = 'esperino_gel:Β΄:period_selection_required';
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
