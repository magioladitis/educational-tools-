<?php
/**
 * Κανόνες συγκρότησης/διδασκαλίας τμήματος Ηθικής από το 2026-2027.
 *
 * Πηγή: Υ.Α. 108070/Δ2/13-08-2026, ΦΕΚ Β΄ 5231/18-08-2026.
 * Η απόφαση έχει ρητό πεδίο εφαρμογής «Γυμνάσιο και Γενικό Λύκειο».
 * Στο εσωτερικό taxonomy περιλαμβάνονται και τα Μουσικά/Καλλιτεχνικά, που
 * είναι αντίστοιχα Γυμνάσια και Γενικά Λύκεια. Για επαγγελματικές/ειδικές
 * δομές (ΕΠΑ.Λ., Π.ΕΠΑ.Λ., ΕΝ.Ε.Ε.ΓΥ.-Λ.) δεν επεκτείνουμε αυτομάτως το όριο των 10.
 */

function ethicsClassFormationPolicy()
{
    return array(
        'decision' => '108070/Δ2/13-08-2026',
        'fek' => 'Β΄ 5231/18-08-2026',
        'effective_from_school_year' => '2026-2027',
        'minimum_exempt_students_per_grade' => 10,
        'deadline_day_after_classes_start' => 5,
        'scope_school_codes' => array(
            'gymnasio', 'esperino_gymnasio', 'gel', 'esperino_gel',
            'kallitexniko_gymnasio', 'kallitexniko_gel',
            'mousiko_gymnasio', 'mousiko_gel',
        ),
        'source_url' => 'https://www.minedu.gov.gr/publications/docs2026/108070_%CE%942_13_8_26_%CE%A5%CE%91_%CE%94%CE%99%CE%94%CE%91%CE%A3%CE%9A%CE%91%CE%9B%CE%99%CE%91_%CE%97%CE%98%CE%99%CE%9A%CE%97%CE%A3.pdf',
        'fallback_rule' => 'περ. 3 άρθρου 22 Κ.Υ.Α. 102791/ΓΔ4/10-09-2024 (Β΄ 5130)',
    );
}

function ethicsClassFormationPublicPolicy()
{
    $policy = ethicsClassFormationPolicy();
    return array(
        'decision' => $policy['decision'],
        'fek' => $policy['fek'],
        'effective_from_school_year' => $policy['effective_from_school_year'],
        'minimum_exempt_students_per_grade' => $policy['minimum_exempt_students_per_grade'],
        'deadline_day_after_classes_start' => $policy['deadline_day_after_classes_start'],
        'scope_school_codes' => $policy['scope_school_codes'],
        'source_url' => $policy['source_url'],
        'fallback_rule' => $policy['fallback_rule'],
    );
}

function ethicsClassFormationScopeStatus($schoolCode)
{
    $policy = ethicsClassFormationPolicy();
    return in_array((string) $schoolCode, $policy['scope_school_codes'], true)
        ? 'in_scope'
        : 'scope_not_confirmed';
}

/**
 * Αξιολογεί μόνο όσα ορίζει ρητά η Υ.Α. — δεν υπολογίζει μόνο του αν ένα
 * τμήμα είναι «ισοδύναμο» με τα υπόλοιπα, επειδή η απόφαση δεν δίνει
 * αριθμητικό αλγόριθμο ισοδυναμίας.
 *
 * $equivalentEthicsSections:
 *   null => δεν έχει κριθεί ακόμη αν σχηματίζεται ισοδύναμο τμήμα,
 *   0    => έχει κριθεί ότι δεν σχηματίζεται,
 *   >0   => πλήθος ξεχωριστών ισοδύναμων τμημάτων Ηθικής.
 */
function ethicsClassFormationEvaluate($schoolCode, $sectionCount, $exemptCount, $withinFifthDay, $equivalentEthicsSections = null)
{
    $policy = ethicsClassFormationPolicy();
    $scope = ethicsClassFormationScopeStatus($schoolCode);
    $result = array(
        'scope_status' => $scope,
        'eligible' => null,
        'status' => '',
        'ethics_groups' => null,
        'total_grade_sections_change' => 0,
        'parallel_same_hour' => null,
        'requires_separate_rooms' => null,
        'source' => $policy['decision'],
    );

    if ($scope !== 'in_scope') {
        $result['status'] = 'scope_not_confirmed';
        $result['note'] = 'Η Υ.Α. 108070/Δ2/2026 αφορά ρητά Γυμνάσιο και Γενικό Λύκειο· δεν γίνεται αυτόματη επέκταση του κανόνα σε αυτόν τον τύπο σχολείου.';
        return $result;
    }

    $sectionCount = (int) $sectionCount;
    if ($sectionCount < 1) {
        $result['status'] = 'invalid_section_count';
        return $result;
    }
    if ($exemptCount === null) {
        $result['status'] = 'needs_exemption_count';
        return $result;
    }
    $exemptCount = (int) $exemptCount;
    if ($withinFifthDay === null) {
        $result['status'] = 'needs_timeliness_data';
        return $result;
    }

    if ($exemptCount < $policy['minimum_exempt_students_per_grade'] || !$withinFifthDay) {
        $result['eligible'] = false;
        $result['status'] = 'fallback_article_22_3';
        $result['ethics_groups'] = 0;
        $result['parallel_same_hour'] = false;
        $result['requires_separate_rooms'] = false;
        $result['note'] = 'Δεν πληρούται εμπρόθεσμα το όριο των 10 απαλλασσομένων ανά τάξη· εφαρμόζεται αναλογικά η περ. 3 του άρθρου 22 της Κ.Υ.Α. 102791/ΓΔ4/2024.';
        return $result;
    }

    $result['eligible'] = true;
    if ($sectionCount === 1) {
        $result['status'] = 'parallel_single_section';
        $result['ethics_groups'] = 1;
        $result['parallel_same_hour'] = true;
        $result['requires_separate_rooms'] = true;
        $result['note'] = 'Θρησκευτικά και Ηθική διδάσκονται την ίδια διδακτική ώρα σε διακριτές αίθουσες.';
        return $result;
    }

    if ($equivalentEthicsSections === null) {
        $result['status'] = 'needs_equivalence_decision';
        $result['note'] = 'Υπάρχουν περισσότερα τμήματα στην τάξη· απαιτείται πραγματική κρίση αν ο αριθμός των απαλλασσομένων επιτρέπει συγκρότηση ισοδύναμου/ων τμήματος/των Ηθικής.';
        return $result;
    }

    $equivalentEthicsSections = (int) $equivalentEthicsSections;
    if ($equivalentEthicsSections > 0) {
        $result['status'] = 'dedicated_equivalent_sections';
        $result['ethics_groups'] = $equivalentEthicsSections;
        $result['parallel_same_hour'] = false;
        $result['requires_separate_rooms'] = false;
        $result['note'] = 'Συγκροτείται/ονται ξεχωριστό/ά ισοδύναμο/α τμήμα/τα Ηθικής και οι υπόλοιποι μαθητές ανακατανέμονται αλφαβητικά χωρίς αύξηση του συνολικού αριθμού τμημάτων.';
        return $result;
    }

    $result['status'] = 'consolidated_parallel';
    $result['ethics_groups'] = 1;
    $result['parallel_same_hour'] = true;
    $result['requires_separate_rooms'] = true;
    $result['note'] = 'Οι απαλλασσόμενοι εντάσσονται στο τμήμα με τους περισσότερους απαλλασσομένους και Θρησκευτικά/Ηθική διδάσκονται την ίδια ώρα σε διακριτές αίθουσες· ακολουθεί αλφαβητική ανακατανομή των υπολοίπων.';
    return $result;
}
