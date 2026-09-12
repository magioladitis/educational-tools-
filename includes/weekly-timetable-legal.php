<?php
/**
 * Legal metadata mapping for the canonical weekly-timetable dataset.
 *
 * Kept outside weekly-timetable-data.php so legal metadata changes do not
 * invalidate the staffing simulator's mtime-based scoped snapshot freshness
 * check. This file contains no HTML and does not load timetable rows.
 */

require_once __DIR__ . '/legal-sources.php';

if (!function_exists('weeklyTimetableLegalSourceKeysForSchools')) {
    function weeklyTimetableLegalSourceKeysForSchools($schools)
    {
        if (!is_array($schools)) {
            $schools = array($schools);
        }

        $sourceKeysBySchool = array(
            'gymnasio' => array('gymnasio_timetable_2026'),
            'gel' => array('gel_timetable_2026'),
            'esperino_gymnasio' => array('esperino_gymnasio_timetable_2026'),
            'esperino_gel' => array('esperino_gel_timetable_2026'),
            'protypo_ekklisiastiko_gymnasio' => array(
                'ecclesiastical_timetable_2021',
                'ecclesiastical_gymnasio_timetable_2025',
            ),
            'protypo_ekklisiastiko_lykeio' => array(
                'ecclesiastical_timetable_2021',
                'ecclesiastical_lykeio_timetable_2022',
            ),
            'kallitexniko_gymnasio' => array(
                'kallitexnika_timetable_2026',
                'gymnasio_gel_assignments_2026',
                'kallitexnika_assignments_2018',
                'kallitexnika_assignments_2024',
            ),
            'kallitexniko_gel' => array(
                'kallitexnika_timetable_2026',
                'gymnasio_gel_assignments_2026',
                'kallitexnika_assignments_2018',
                'kallitexnika_assignments_2024',
            ),
            'mousiko_gymnasio' => array(
                'mousika_timetable_2026',
                'gymnasio_gel_assignments_2026',
                'mousika_assignments_2018',
            ),
            'mousiko_gel' => array(
                'mousika_timetable_2026',
                'gymnasio_gel_assignments_2026',
                'mousika_assignments_2018',
            ),
            'eneegyl_gymnasio' => array(
                'eneegyl_gymnasio_timetable_2026',
            ),
            'eneegyl_lykeio' => array(
                'eneegyl_lykeio_timetable_2026',
            ),
            'eeeek' => array(
                'eeeek_timetable_2002',
                'eeeek_six_classes_2016',
            ),
            'epal' => array(
                'epal_day_ab_timetable_2026',
                'epal_ab_timetable_2018',
                'epal_g_timetable_2017',
                'epal_g_timetable_2017_2072',
                'epal_tourism_languages_2018',
                'epal_naval_timetable_2018',
                'epal_g_art_timetable_2018_day',
                'epal_g_art_timetable_correction_2018',
            ),
            'esperino_epal' => array(
                'epal_evening_a_timetable_2026',
                'esperino_epal_timetable_2018',
                'epal_tourism_languages_2018',
                'epal_naval_timetable_2018',
                'epal_g_art_timetable_2018_evening',
                'epal_g_art_timetable_correction_2018',
            ),
            'pepal' => array(
                'pepal_a_timetable_2026',
                'pepal_b_timetable_2026',
                'pepal_a_timetable_2021',
                'pepal_a_assignments_2021',
                'pepal_a_assignments_2023_7403',
                'pepal_b_timetable_2022',
                'pepal_b_timetable_correction_2022',
                'pepal_g_timetable_2023',
                'pepal_g_assignments_2023',
            ),
        );

        $result = array();
        $seen = array();
        foreach ($schools as $school) {
            $school = trim((string) $school);
            if ($school === '' || !isset($sourceKeysBySchool[$school])) {
                continue;
            }
            foreach ($sourceKeysBySchool[$school] as $key) {
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $result[] = $key;
            }
        }

        return $result;
    }
}

if (!function_exists('weeklyTimetableLegalOverviewGroups')) {
    /**
     * Source-key groups used by the weekly-timetable overview source card.
     *
     * This is intentionally metadata-only: no URLs, FEK numbers or HTML are
     * duplicated here. A group may contain more than one source key when the
     * underlying legal acts share the same published FEK/document and the
     * overview should keep one compact link, as in the legacy UI.
     */
    function weeklyTimetableLegalOverviewGroups()
    {
        return array(
            array('gymnasio_timetable_2026'),
            array('gel_timetable_2026', 'esperino_gymnasio_timetable_2026'),
            array('esperino_gel_timetable_2026'),
        );
    }
}

if (!function_exists('weeklyTimetableLegalOverviewLinks')) {
    /**
     * Build portable link metadata for the weekly-timetable overview.
     * Facts (URL/FEK/title) come only from the central legal-source registry.
     * No HTML is emitted here, so the same data remains usable by an API/app.
     */
    function weeklyTimetableLegalOverviewLinks()
    {
        $links = array();

        foreach (weeklyTimetableLegalOverviewGroups() as $groupKeys) {
            $sources = legalSourcesForKeys($groupKeys);
            if (empty($sources)) {
                continue;
            }

            $url = null;
            $compactFek = null;
            $titles = array();
            $resolvedKeys = array();
            $validGroup = true;

            foreach ($sources as $key => $source) {
                if (empty($source['url']) || empty($source['fek'])) {
                    $validGroup = false;
                    break;
                }

                $sourceUrl = (string) $source['url'];
                $sourceCompactFek = legalSourceCompactFek($source['fek']);
                if ($url === null) {
                    $url = $sourceUrl;
                    $compactFek = $sourceCompactFek;
                } elseif ($url !== $sourceUrl || $compactFek !== $sourceCompactFek) {
                    // Defensive fallback: never merge unrelated legal sources.
                    $validGroup = false;
                    break;
                }

                $titles[] = !empty($source['citation_title'])
                    ? (string) $source['citation_title']
                    : (string) $source['title'];
                $resolvedKeys[] = $key;
            }

            if (!$validGroup || $url === null || $compactFek === null || empty($titles)) {
                // If a future edit makes a configured group heterogeneous,
                // keep every source visible instead of silently losing one.
                foreach (legalSourceLinksForKeys($groupKeys) as $fallbackLink) {
                    $links[] = $fallbackLink;
                }
                continue;
            }

            $links[] = array(
                'source_keys' => $resolvedKeys,
                'relation' => count($resolvedKeys) > 1 ? 'shared-publication' : 'base',
                'url' => $url,
                'label' => $compactFek . ' — ' . implode(' & ', $titles) . ' ↗',
            );
        }

        return $links;
    }
}

if (!function_exists('weeklyTimetableEcclesiasticalLegalLink')) {
    /**
     * Portable citation metadata for the Ecclesiastical timetable sources.
     * Legal facts come from legal-sources.php; only context-specific wording
     * lives here so the existing web UI can remain byte-for-byte equivalent
     * at link level while API/mobile consumers can use the structured facts.
     */
    function weeklyTimetableEcclesiasticalLegalLink($sourceKey, $context)
    {
        $source = legalSourceByKey($sourceKey);
        if (!$source || empty($source['url']) || empty($source['fek'])) {
            return null;
        }

        $decision = !empty($source['decision']) ? legalSourceCompactDecision($source['decision']) : '';
        $fek = legalSourceCompactFek($source['fek']);
        $label = '';

        if ($context === 'overview') {
            if ($sourceKey === 'ecclesiastical_gymnasio_timetable_2025') {
                $label = $fek . ' — Πρότυπο Εκκλησιαστικό Γυμνάσιο ↗';
            } elseif ($sourceKey === 'ecclesiastical_timetable_2021') {
                $label = $decision . ' — ' . $fek . ' · Εκκλησιαστικό Γυμνάσιο/Λύκειο ↗';
            } elseif ($sourceKey === 'ecclesiastical_lykeio_timetable_2022') {
                $label = $fek . ' — τροποποίηση Γ΄ Εκκλησιαστικού Λυκείου ↗';
            }
        } elseif ($context === 'staffing') {
            if ($sourceKey === 'ecclesiastical_timetable_2021') {
                $label = $decision . ' — ' . $fek . ' · Πρότυπα Εκκλησιαστικά Σχολεία ↗';
            } elseif ($sourceKey === 'ecclesiastical_gymnasio_timetable_2025') {
                $label = $decision . ' — ' . $fek . ' · Πρότυπο Εκκλησιαστικό Γυμνάσιο ↗';
            } elseif ($sourceKey === 'ecclesiastical_lykeio_timetable_2022') {
                $label = $decision . ' — ' . $fek . ' · Τροποποίηση Εκκλησιαστικού Λυκείου ↗';
            }
        }

        if ($label === '') {
            return null;
        }

        $urlVariant = ($context === 'staffing' && $sourceKey === 'ecclesiastical_timetable_2021')
            ? 'legal_text'
            : null;
        $url = legalSourceUrl($source, $urlVariant);
        if (!$url) {
            return null;
        }

        return array(
            'source_key' => $sourceKey,
            'url' => $url,
            'label' => $label,
        );
    }
}

if (!function_exists('weeklyTimetableSpecialSchoolOverviewLinks')) {
    /**
     * Context-specific source-card links for Art/Music schools and EEE.EK.
     * The registry owns all legal facts; this helper only preserves the
     * wording/order of the existing web card while staying HTML-free.
     */
    function weeklyTimetableSpecialSchoolOverviewLinks($group)
    {
        $links = array();
        $group = trim((string) $group);

        if ($group === 'arts_music') {
            $artTimetable = legalSourceByKey('kallitexnika_timetable_2026');
            if ($artTimetable && ($url = legalSourceUrl($artTimetable))) {
                $links[] = array(
                    'source_key'=>'kallitexnika_timetable_2026',
                    'url'=>$url,
                    'label'=>legalSourceCompactFek($artTimetable['fek']) . ' — Καλλιτεχνικά Σχολεία ↗',
                );
            }

            $generalAssignments = legalSourceByKey('gymnasio_gel_assignments_2026');
            if ($generalAssignments && ($url = legalSourceUrl($generalAssignments))) {
                $baseFek = legalSourceCompactFek($generalAssignments['fek']);
                $amendment = legalSourceByKey('gymnasio_gel_assignments_2026_5555');
                $amendFek = '';
                if ($amendment && !empty($amendment['fek'])) {
                    $amendFek = legalSourceCompactFek($amendment['fek']);
                    $amendFek = preg_replace('/^ΦΕΚ\s+/u', '', $amendFek);
                }
                $label = $baseFek;
                if ($amendFek !== '') {
                    $label .= ' + ' . $amendFek;
                }
                $links[] = array(
                    'source_keys'=>array('gymnasio_gel_assignments_2026', 'gymnasio_gel_assignments_2026_5555'),
                    'url'=>$url,
                    'label'=>$label . ' — Αναθέσεις γενικής παιδείας Καλλιτεχνικών Σχολείων / διασταύρωση ↗',
                );
            }

            $artAssignments = legalSourceByKey('kallitexnika_assignments_2024');
            if ($artAssignments && ($url = legalSourceUrl($artAssignments))) {
                $links[] = array(
                    'source_key'=>'kallitexnika_assignments_2024',
                    'url'=>$url,
                    'label'=>legalSourceCompactFek($artAssignments['fek']) . ' — Αναθέσεις καλλιτεχνικής παιδείας / διασταύρωση ↗',
                );
            }

            $musicTimetable = legalSourceByKey('mousika_timetable_2026');
            if ($musicTimetable && ($url = legalSourceUrl($musicTimetable))) {
                $links[] = array(
                    'source_key'=>'mousika_timetable_2026',
                    'url'=>$url,
                    'label'=>legalSourceCompactFek($musicTimetable['fek']) . ' — Μουσικά Σχολεία ↗',
                );
            }

            $musicAssignments = legalSourceByKey('mousika_assignments_2018');
            if ($musicAssignments && ($url = legalSourceUrl($musicAssignments))) {
                $links[] = array(
                    'source_key'=>'mousika_assignments_2018',
                    'url'=>$url,
                    'label'=>legalSourceCompactFek($musicAssignments['fek']) . ' — Αναθέσεις μουσικής παιδείας / διασταύρωση τίτλων ↗',
                );
            }
        } elseif ($group === 'eeeek') {
            $eeeekTimetable = legalSourceByKey('eeeek_timetable_2002');
            if ($eeeekTimetable && ($url = legalSourceUrl($eeeekTimetable))) {
                $links[] = array(
                    'source_key'=>'eeeek_timetable_2002',
                    'url'=>$url,
                    'label'=>legalSourceCompactDecision($eeeekTimetable['decision']) . ' — ' . legalSourceCompactFek($eeeekTimetable['fek']) . ' · Ωρολόγιο Ε.Ε.Ε.ΕΚ. ↗',
                );
            }

            $eeeekLaw = legalSourceByKey('eeeek_six_classes_2016');
            if ($eeeekLaw && ($url = legalSourceUrl($eeeekLaw))) {
                $links[] = array(
                    'source_key'=>'eeeek_six_classes_2016',
                    'url'=>$url,
                    'label'=>'ν. 4415/2016 — έξι τάξεις & ΣΤ΄ Ε.Ε.Ε.ΕΚ. ↗',
                );
            }
        }

        return $links;
    }
}

if (!function_exists('weeklyTimetableVocationalOverviewLinks')) {
    /**
     * Source-card links for EPAL / P.EPAL. Legal facts live exclusively in
     * legal-sources.php; this helper only preserves legacy ordering/wording.
     */
    function weeklyTimetableVocationalOverviewLinks()
    {
        $links = array();

        $sharedEpal2026 = legalSourceByKey('epal_day_ab_timetable_2026');
        if ($sharedEpal2026 && ($url = legalSourceUrl($sharedEpal2026))) {
            $links[] = array(
                'source_keys' => array('epal_day_ab_timetable_2026', 'epal_evening_a_timetable_2026'),
                'relation' => 'shared-publication',
                'url' => $url,
                'label' => 'ΦΕΚ Β΄ 2151/2026 — Α΄/Β΄ Ημερήσιου ΕΠΑ.Λ. & Α΄ Εσπερινού ΕΠΑ.Λ. ↗',
            );
        }

        foreach (legalSourceLinksForKeys(array(
            'epal_ab_timetable_2018',
        )) as $link) $links[] = $link;

        // Keep the official Ministry index link itself in the page: it is a
        // navigational collection, not an individual legal act.

        foreach (legalSourceLinksForKeys(array(
            'epal_g_timetable_2017',
            'epal_g_timetable_2017_2072',
            'esperino_epal_timetable_2018',
            'epal_tourism_languages_2018',
            'epal_naval_timetable_2018',
        )) as $link) $links[] = $link;

        $artDay = legalSourceByKey('epal_g_art_timetable_2018_day');
        if ($artDay && ($url = legalSourceUrl($artDay))) {
            $links[] = array(
                'source_keys' => array('epal_g_art_timetable_2018_day', 'epal_g_art_timetable_2018_evening'),
                'relation' => 'shared-publication',
                'url' => $url,
                'label' => 'ΦΕΚ Β΄ 4373/2018 — Γραφικών Τεχνών / Σχεδιασμού–Διακόσμησης Γ΄ ΕΠΑ.Λ. ↗',
            );
        }
        foreach (legalSourceLinksForKeys(array('epal_g_art_timetable_correction_2018')) as $link) $links[] = $link;

        $sharedPepal2026 = legalSourceByKey('pepal_b_timetable_2026');
        if ($sharedPepal2026 && ($url = legalSourceUrl($sharedPepal2026))) {
            $links[] = array(
                'source_keys' => array('pepal_a_timetable_2026', 'pepal_b_timetable_2026'),
                'relation' => 'shared-publication',
                'url' => $url,
                'label' => 'ΦΕΚ Β΄ 2136/2026 — Α΄/Β΄ Π.ΕΠΑ.Λ. ↗',
            );
        }

        foreach (legalSourceLinksForKeys(array(
            'pepal_a_timetable_2021',
        )) as $link) $links[] = $link;

        $aAssignments = legalSourceByKey('pepal_a_assignments_2021');
        if ($aAssignments && ($url = legalSourceUrl($aAssignments))) {
            $links[] = array(
                'source_key' => 'pepal_a_assignments_2021',
                'relation' => 'crosscheck',
                'url' => $url,
                'label' => 'ΦΕΚ Β΄ 4367/2021 — Α΄ Π.ΕΠΑ.Λ. / Αναθέσεις ανά θεματική ενότητα ↗',
            );
        }
        $aCross = legalSourceByKey('pepal_a_assignments_2023_7403');
        if ($aCross && ($url = legalSourceUrl($aCross))) {
            $links[] = array(
                'source_key' => 'pepal_a_assignments_2023_7403',
                'relation' => 'crosscheck',
                'url' => $url,
                'label' => 'ΦΕΚ Β΄ 7403/2023 — Α΄ Π.ΕΠΑ.Λ. / κανόνας ανάθεσης & συνδιδασκαλία ↗',
            );
        }

        foreach (legalSourceLinksForKeys(array(
            'pepal_b_timetable_2022',
            'pepal_b_timetable_correction_2022',
            'pepal_g_timetable_2023',
        )) as $link) $links[] = $link;

        $gAssignments = legalSourceByKey('pepal_g_assignments_2023');
        if ($gAssignments && ($url = legalSourceUrl($gAssignments))) {
            $links[] = array(
                'source_key' => 'pepal_g_assignments_2023',
                'relation' => 'crosscheck',
                'url' => $url,
                'label' => 'ΦΕΚ Β΄ 5510/2023 — Γ΄ Π.ΕΠΑ.Λ. / Αναθέσεις (διασταύρωση τίτλων) ↗',
            );
        }

        return $links;
    }
}

