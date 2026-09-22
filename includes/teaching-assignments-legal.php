<?php
/**
 * Legal metadata mapping for the canonical teaching-assignment dataset.
 *
 * Kept in a companion file instead of teaching-assignments-data.php so legal
 * metadata changes do not invalidate the staffing simulator's mtime-based
 * scoped snapshot freshness check.
 *
 * This file contains no HTML and does not load the assignment rows.
 */

require_once __DIR__ . '/legal-sources.php';

if (!function_exists('teachingAssignmentsLegalSourceKeysForSchools')) {
    function teachingAssignmentsLegalSourceKeysForSchools($schools)
    {
        if (!is_array($schools)) {
            $schools = array($schools);
        }

        $generalSchools = array(
            'gymnasio' => true,
            'esperino_gymnasio' => true,
            'gel' => true,
            'esperino_gel' => true,
        );
        $ecclesiasticalSchools = array(
            'protypo_ekklisiastiko_gymnasio' => true,
            'protypo_ekklisiastiko_lykeio' => true,
            // UI/profile aggregate type; canonical assignment rows remain split
            // into the two school scopes above.
            'protypo_ekklisiastiko_gymnasio_lykeio' => true,
        );
        $eaeSchools = array(
            'eae_gymnasio' => true,
            'eae_lykeio' => true,
            'eae' => true,
        );
        $eneegylSchools = array(
            'eneegyl_gymnasio' => true,
            'eneegyl_lykeio' => true,
            'eneegyl' => true,
        );
        $artSchools = array(
            'kallitexniko_gymnasio' => true,
            'kallitexniko_gel' => true,
        );
        $musicSchools = array(
            'mousiko_gymnasio' => true,
            'mousiko_gel' => true,
        );
        $eeeekSchools = array(
            'eeeek' => true,
        );
        $epalSchools = array(
            'epal' => true,
            'esperino_epal' => true,
        );
        $pepalSchools = array(
            'pepal' => true,
        );

        $usesGeneral = false;
        $usesEcclesiastical = false;
        $usesEae = false;
        $usesEneegyl = false;
        $usesArt = false;
        $usesMusic = false;
        $usesEeeek = false;
        $usesEpal = false;
        $usesPepal = false;
        foreach ($schools as $school) {
            $school = trim((string) $school);
            if ($school === '') {
                continue;
            }
            if (isset($generalSchools[$school])) {
                $usesGeneral = true;
            }
            if (isset($ecclesiasticalSchools[$school])) {
                $usesEcclesiastical = true;
            }
            if (isset($eaeSchools[$school])) {
                $usesEae = true;
            }
            if (isset($eneegylSchools[$school])) {
                $usesEneegyl = true;
            }
            if (isset($artSchools[$school])) {
                $usesArt = true;
            }
            if (isset($musicSchools[$school])) {
                $usesMusic = true;
            }
            if (isset($eeeekSchools[$school])) {
                $usesEeeek = true;
            }
            if (isset($epalSchools[$school])) {
                $usesEpal = true;
            }
            if (isset($pepalSchools[$school])) {
                $usesPepal = true;
            }
        }

        $keys = array();
        if ($usesGeneral || $usesEcclesiastical || $usesArt || $usesMusic) {
            // The common curriculum of P.E.S. reuses the current Gymnasio/GEL
            // assignment table, so the same legal act applies there too.
            $keys[] = 'gymnasio_gel_assignments_2026';
            $keys[] = 'gymnasio_gel_assignments_2026_5555';
        }
        if ($usesEcclesiastical) {
            // P.E.S. assignments are also constrained by their special
            // timetable and by subject-specific qualification provisions.
            $keys[] = 'ecclesiastical_timetable_2021';
            $keys[] = 'ecclesiastical_iconography_2020';
            $keys[] = 'ecclesiastical_iconography_2023';
            $keys[] = 'ecclesiastical_byzantine_music_qualification_2010';
        }
        if ($usesEae) {
            $keys[] = 'eae_assignments_2026';
            $keys[] = 'eae_assignments_2026_5610';
        }
        if ($usesEneegyl) {
            $keys[] = 'eneegyl_assignments_2026';
        }
        if ($usesArt) {
            $keys[] = 'kallitexnika_assignments_2018';
            $keys[] = 'kallitexnika_assignments_2024';
            $keys[] = 'kallitexnika_timetable_2026';
        }
        if ($usesMusic) {
            $keys[] = 'mousika_assignments_2018';
            $keys[] = 'mousika_timetable_2026';
        }
        if ($usesEeeek) {
            $keys[] = 'eeeek_assignments_2018';
        }
        if ($usesEpal) {
            $keys = array_merge($keys, array(
                'epal_assignments_2018',
                'epal_assignments_2018_2637',
                'epal_assignments_2018_3520',
                'epal_assignments_2019_2779',
                'epal_assignments_2020_453',
                'epal_assignments_2020_3609',
                'epal_assignments_2023_418',
                'epal_assignments_2023_5206',
                'epal_assignments_2025_1975',
                'epal_assignments_2026_2625',
            ));
        }
        if ($usesPepal) {
            $keys = array_merge($keys, array(
                'pepal_a_assignments_2021',
                'pepal_a_assignments_2023_5188',
                'pepal_a_assignments_2023_7403',
                'pepal_a_assignments_2025_1832',
                'pepal_a_assignments_2026_2687',
                'pepal_b_assignments_2022',
                'pepal_b_assignments_2023_418',
                'pepal_b_assignments_2023_5206',
                'pepal_b_assignments_2026_2624',
                'pepal_g_assignments_2023',
            ));
        }

        return array_values(array_unique($keys));
    }
}

if (!function_exists('teachingAssignmentsSpecialSchoolOverviewLinks')) {
    /**
     * Link metadata for the legacy source-card sequence of Art/Music/EEE.EK.
     * Legal facts come only from legal-sources.php. Context wording and URL
     * variants live here so the current web UI can remain exactly unchanged.
     */
    function teachingAssignmentsSpecialSchoolOverviewLinks()
    {
        $links = array();

        $artCurrent = legalSourceByKey('kallitexnika_assignments_2024');
        if ($artCurrent) {
            $decision = legalSourceCompactDecision($artCurrent['decision']);
            $fek = legalSourceCompactFek($artCurrent['fek']);
            $ada = !empty($artCurrent['ada']) ? $artCurrent['ada'] : '';
            $url = legalSourceUrl($artCurrent);
            if ($url) {
                $label = $decision . ' — ' . $fek;
                if ($ada !== '') {
                    $label .= ' (ΑΔΑ ' . $ada . ')';
                }
                $links[] = array('source_key'=>'kallitexnika_assignments_2024','url'=>$url,'label'=>$label . ' ↗');
            }
        }

        $artBase = legalSourceByKey('kallitexnika_assignments_2018');
        if ($artBase && ($url = legalSourceUrl($artBase))) {
            $links[] = array(
                'source_key'=>'kallitexnika_assignments_2018',
                'url'=>$url,
                'label'=>legalSourceCompactDecision($artBase['decision']) . ' — ' . legalSourceCompactFek($artBase['fek']) . ' (βασική απόφαση) ↗',
            );
        }

        $artTimetable = legalSourceByKey('kallitexnika_timetable_2026');
        if ($artTimetable && ($url = legalSourceUrl($artTimetable, 'assignments_card'))) {
            $links[] = array(
                'source_key'=>'kallitexnika_timetable_2026',
                'url'=>$url,
                'label'=>legalSourceCompactFek($artTimetable['fek']) . ' — Ωρολόγιο Καλλιτεχνικών Σχολείων ↗',
            );
        }

        if ($artCurrent && ($url = legalSourceUrl($artCurrent, 'encoded_ada'))) {
            $ada = !empty($artCurrent['ada']) ? $artCurrent['ada'] : '';
            $label = legalSourceCompactDecision($artCurrent['decision']);
            if ($ada !== '') {
                $label .= ' — ΑΔΑ ' . $ada;
            }
            $links[] = array('source_key'=>'kallitexnika_assignments_2024','url'=>$url,'label'=>$label . ' ↗');
        }

        $musicAssignments = legalSourceByKey('mousika_assignments_2018');
        if ($musicAssignments && ($url = legalSourceUrl($musicAssignments))) {
            $links[] = array(
                'source_key'=>'mousika_assignments_2018',
                'url'=>$url,
                'label'=>legalSourceCompactFek($musicAssignments['fek']) . ' — Αναθέσεις μουσικής παιδείας Μουσικών Σχολείων ↗',
            );
        }

        $musicTimetable = legalSourceByKey('mousika_timetable_2026');
        if ($musicTimetable && ($url = legalSourceUrl($musicTimetable, 'assignments_card'))) {
            $links[] = array(
                'source_key'=>'mousika_timetable_2026',
                'url'=>$url,
                'label'=>legalSourceCompactFek($musicTimetable['fek']) . ' — Ωρολόγιο Μουσικού Γυμνασίου / Γενικού Μουσικού Λυκείου ↗',
            );
        }

        $eeeekAssignments = legalSourceByKey('eeeek_assignments_2018');
        if ($eeeekAssignments && ($url = legalSourceUrl($eeeekAssignments))) {
            $links[] = array(
                'source_key'=>'eeeek_assignments_2018',
                'url'=>$url,
                'label'=>legalSourceCompactFek($eeeekAssignments['fek']) . ' — Αναθέσεις μαθημάτων Ε.Ε.Ε.ΕΚ. ↗',
            );
        }

        return $links;
    }
}

if (!function_exists('teachingAssignmentsVocationalOverviewLinks')) {
    /**
     * Preserve the current EPAL/P.EPAL source-card order and wording while
     * sourcing every legal fact from the central registry. Official framework
     * index pages remain outside this function because they are collections,
     * not individual legal acts.
     */
    function teachingAssignmentsVocationalOverviewLinks()
    {
        $keys = array(
            'pepal_a_assignments_2023_5188',
            'pepal_a_assignments_2023_7403',
            'pepal_a_assignments_2025_1832',
            'pepal_a_assignments_2026_2687',
            'pepal_b_assignments_2022',
            'pepal_b_assignments_2023_418',
            'pepal_b_assignments_2023_5206',
            'pepal_b_assignments_2026_2624',
            'pepal_g_assignments_2023',
        );
        $links = legalSourceLinksForKeys($keys);

        $pepalGTimetable = legalSourceByKey('pepal_g_timetable_2023');
        if ($pepalGTimetable && ($url = legalSourceUrl($pepalGTimetable, 'assignments_card'))) {
            $links[] = array(
                'source_key' => 'pepal_g_timetable_2023',
                'relation' => 'crosscheck',
                'url' => $url,
                'label' => 'ΦΕΚ Β΄ 5251/2023 — Γ΄ Π.ΕΠΑ.Λ. / Ωρολόγιο (διασταύρωση τίτλων) ↗',
            );
        }

        $epalKeys = array(
            'epal_assignments_2018',
            'epal_tourism_languages_2018',
            'epal_assignments_2018_2637',
            'epal_assignments_2018_3520',
            'epal_assignments_2019_2779',
            'epal_assignments_2020_3609',
            'epal_assignments_2020_453',
            'epal_assignments_2023_5206',
            'epal_assignments_2025_1975',
            'epal_assignments_2026_2625',
        );
        foreach (legalSourceLinksForKeys($epalKeys) as $link) {
            $links[] = $link;
        }

        $epalTimetable2026 = legalSourceByKey('epal_day_ab_timetable_2026');
        if ($epalTimetable2026 && ($url = legalSourceUrl($epalTimetable2026))) {
            $links[] = array(
                'source_keys' => array('epal_day_ab_timetable_2026', 'epal_evening_a_timetable_2026'),
                'relation' => 'crosscheck-shared-publication',
                'url' => $url,
                'label' => 'ΦΕΚ Β΄ 2151/2026 — Ωρολόγια Ημερήσιου / Εσπερινού ΕΠΑ.Λ. ↗',
            );
        }

        return $links;
    }
}

if (!function_exists('teachingAssignmentsGeneralTimetableCrosscheckLinks')) {
    /**
     * Legacy source-card cross-checks for evening-school timetables.
     *
     * Legal facts and alternate URLs live in legal-sources.php; this helper
     * preserves only the current presentation/order and emits no HTML.
     */
    function teachingAssignmentsGeneralTimetableCrosscheckLinks()
    {
        $links = array();

        $eveningGym = legalSourceByKey('esperino_gymnasio_timetable_2026');
        if ($eveningGym && ($url = legalSourceUrl($eveningGym, 'ministry_index'))) {
            $links[] = array(
                'source_key' => 'esperino_gymnasio_timetable_2026',
                'relation' => 'timetable-crosscheck',
                'url' => $url,
                'label' => legalSourceCompactFek($eveningGym['fek']) . ' — Ωρολόγιο Εσπερινού Γυμνασίου ↗',
            );
        }

        $eveningGel = legalSourceByKey('esperino_gel_timetable_2026');
        if ($eveningGel && ($url = legalSourceUrl($eveningGel, 'legal_text'))) {
            $links[] = array(
                'source_key' => 'esperino_gel_timetable_2026',
                'relation' => 'timetable-crosscheck',
                'url' => $url,
                'label' => legalSourceCompactFek($eveningGel['fek']) . ' — Ωρολόγιο Εσπερινού ΓΕΛ ↗',
            );
        }

        return $links;
    }
}
