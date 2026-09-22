<?php
/**
 * Canonical legal/regulatory source registry.
 *
 * DATA ONLY: this file deliberately contains no HTML and no dependency on the
 * web source-card component. Web pages, CLI tools and a future API/mobile app
 * can all consume the same normalized metadata.
 *
 * Migration is intentionally incremental. Only sources already migrated from
 * page-level hardcoding are registered here.
 */

if (!function_exists('legalSourcesRegistry')) {
    function legalSourcesRegistry()
    {
        static $registry = null;
        if ($registry !== null) {
            return $registry;
        }

        $registry = array(
            'sde_leadership_selection_2025' => array(
                'title' => 'Κριτήρια και διαδικασία επιλογής Διευθυντών και Υποδιευθυντών Σ.Δ.Ε.',
                'citation_title' => 'Διευθυντές και Υποδιευθυντές Σ.Δ.Ε.',
                'decision' => 'Υ.Α. 70621/Κ1/13-06-2025',
                'fek' => 'ΦΕΚ Β΄ 3037/19-06-2025',
                'date' => '2025-06-13',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2025/20250203037.pdf',
                'valid_from' => '2025-2026',
                'school_types' => array('sde'),
                'topics' => array('leadership_selection', 'adult_education', 'school_directors', 'school_deputy_directors'),
                'link_label' => 'Υ.Α. 70621/Κ1/13-06-2025 — ΦΕΚ Β΄ 3037/19-06-2025 · Διευθυντές και Υποδιευθυντές Σ.Δ.Ε. ↗',
            ),
            'gymnasio_timetable_2026' => array(
                'title' => 'Ωρολόγιο Πρόγραμμα Ημερήσιου Γυμνασίου',
                'citation_title' => 'Ημερήσιο Γυμνάσιο',
                'decision' => 'Υ.Α. 44257/Δ2/08-04-2026',
                'fek' => 'ΦΕΚ Β΄ 2132/09-04-2026',
                'date' => '2026-04-08',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202132_09_04_26_OP%20EM%20GYMN.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('gymnasio'),
                'topics' => array('weekly_timetable', 'general_education'),
                'link_label' => 'Υ.Α. 44257/Δ2/08-04-2026 — ΦΕΚ Β΄ 2132/09-04-2026 · Ημερήσιο Γυμνάσιο ↗',
            ),
            'gel_timetable_2026' => array(
                'title' => 'Ωρολόγιο Πρόγραμμα Ημερήσιου ΓΕΛ',
                'citation_title' => 'Ημερήσιο ΓΕΛ',
                'decision' => 'Υ.Α. 43684/Δ2/07-04-2026',
                'fek' => 'ΦΕΚ Β΄ 2106/09-04-2026',
                'date' => '2026-04-07',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202106_09_04_26_OP%20EM%20GEL_ESP%20Gymnasio.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('gel'),
                'topics' => array('weekly_timetable', 'general_education'),
                'link_label' => 'Υ.Α. 43684/Δ2/07-04-2026 — ΦΕΚ Β΄ 2106/09-04-2026 · Ημερήσιο ΓΕΛ ↗',
            ),
            'esperino_gymnasio_timetable_2026' => array(
                'title' => 'Ωρολόγιο Πρόγραμμα Εσπερινού Γυμνασίου',
                'citation_title' => 'Εσπερινό Γυμνάσιο',
                'decision' => 'Υ.Α. 43751/Δ2/07-04-2026',
                'fek' => 'ΦΕΚ Β΄ 2106/09-04-2026',
                'date' => '2026-04-07',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202106_09_04_26_OP%20EM%20GEL_ESP%20Gymnasio.pdf',
                'alternate_urls' => array(
                    'ministry_index' => 'https://www.minedu.gov.gr/protovathmia-defterovathmia/mousika-sxoleia-eisagogi-mathiton-mathitrion/70059-orologio-programma-ton-mathimaton-ton-a-v-kai-g-takseon-tou-esperinoy-gymnasiou-genikoy-lykeiou?filter_tag%5B0%5D=64',
                ),
                'valid_from' => '2026-2027',
                'school_types' => array('esperino_gymnasio'),
                'topics' => array('weekly_timetable', 'general_education'),
                'link_label' => 'Υ.Α. 43751/Δ2/07-04-2026 — ΦΕΚ Β΄ 2106/09-04-2026 · Εσπερινό Γυμνάσιο ↗',
            ),
            'esperino_gel_timetable_2026' => array(
                'title' => 'Ωρολόγιο Πρόγραμμα Εσπερινού ΓΕΛ',
                'citation_title' => 'Εσπερινό ΓΕΛ',
                'decision' => 'Υ.Α. 43706/Δ2/07-04-2026',
                'fek' => 'ΦΕΚ Β΄ 2102/09-04-2026',
                'date' => '2026-04-07',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2026/20260202102.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('esperino_gel'),
                'topics' => array('weekly_timetable', 'general_education'),
                'link_label' => 'Υ.Α. 43706/Δ2/07-04-2026 — ΦΕΚ Β΄ 2102/09-04-2026 · Εσπερινό ΓΕΛ ↗',
            ),

            'gymnasio_technology_informatics_groups_2020' => array(
                'title' => 'Διδασκαλία του διδακτικού/μαθησιακού πεδίου Τεχνολογία και Πληροφορική στο Γυμνάσιο',
                'citation_title' => 'Τεχνολογία / Πληροφορική Γυμνασίου',
                'decision' => 'Υ.Α. 74472/Δ2/16-06-2020',
                'fek' => 'ΦΕΚ Β΄ 2450/19-06-2020',
                'date' => '2020-06-16',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2020/20200202450.pdf',
                'valid_from' => '2020-2021',
                'school_types' => array('gymnasio'),
                'topics' => array('class_formation', 'technology', 'informatics', 'general_education'),
                'link_label' => 'Υ.Α. 74472/Δ2/2020 — ΦΕΚ Β΄ 2450/2020 · Τεχνολογία / Πληροφορική Γυμνασίου ↗',
            ),
            'eneegyl_gymnasio_timetable_2026' => array(
                'title' => 'Ωρολόγιο Πρόγραμμα των μαθημάτων του Γυμνασίου ΕΝ.Ε.Ε.ΓΥ.-Λ.',
                'citation_title' => 'Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ.',
                'decision' => 'Υ.Α. 45396/Δ3/14-04-2026',
                'fek' => 'ΦΕΚ Β΄ 2259/22-04-2026',
                'date' => '2026-04-14',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%202259tB22-04-2026%20me%20thema%20Orologio%20Programma%20ton%20mathematon%20tou%20Gymnasiou%20EN.E.E.GY-L.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('eneegyl_gymnasio'),
                'topics' => array('weekly_timetable', 'special_education', 'vocational_special_education'),
                'link_label' => 'ΦΕΚ Β΄ 2259/2026 — Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ. ↗',
            ),
            'eneegyl_lykeio_timetable_2026' => array(
                'title' => 'Ωρολόγιο πρόγραμμα του Λυκείου των ΕΝ.Ε.Ε.ΓΥ.-Λ.',
                'citation_title' => 'Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ.',
                'decision' => 'Υ.Α. 44451/Δ3/08-04-2026',
                'fek' => 'ΦΕΚ Β΄ 2149/16-04-2026',
                'date' => '2026-04-08',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%202149%20t.B%2016-04-2026%20me%20thema%20Orologio%20programma%20tou%20Lykeiou%20ton%20Eniaion%20Eidikon%20Epangelmatikon%20Gymnasion%20-%20Lykeion%20EN.E.E.GY.-L.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('eneegyl_lykeio'),
                'topics' => array('weekly_timetable', 'special_education', 'vocational_special_education'),
                'link_label' => 'ΦΕΚ Β΄ 2149/2026 — Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ. ↗',
            ),

            'ecclesiastical_timetable_2021' => array(
                'title' => 'Ωρολόγιο Πρόγραμμα Εκκλησιαστικών Γυμνασίων και Γενικών Εκκλησιαστικών Λυκείων',
                'citation_title' => 'Εκκλησιαστικό Γυμνάσιο/Λύκειο',
                'decision' => 'Υ.Α. 118380/Θ2/21-09-2021',
                'fek' => 'ΦΕΚ Β΄ 4438/25-09-2021',
                'date' => '2021-09-21',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2021/20210204438.pdf',
                'valid_from' => '2021-2022',
                'school_types' => array('protypo_ekklisiastiko_gymnasio', 'protypo_ekklisiastiko_lykeio'),
                'topics' => array('weekly_timetable', 'ecclesiastical_education'),
            ),
            'ecclesiastical_lykeio_timetable_2022' => array(
                'title' => 'Τροποποίηση ωρολογίου Γ΄ Γενικού Εκκλησιαστικού Λυκείου',
                'citation_title' => 'Τροποποίηση Εκκλησιαστικού Λυκείου',
                'decision' => 'Υ.Α. 63979/Θ2/30-05-2022',
                'fek' => 'ΦΕΚ Β΄ 2781/03-06-2022',
                'date' => '2022-05-30',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2022/20220202781.pdf',
                'valid_from' => '2021-2022',
                'school_types' => array('protypo_ekklisiastiko_lykeio'),
                'topics' => array('weekly_timetable', 'ecclesiastical_education'),
            ),
            'ecclesiastical_gymnasio_timetable_2025' => array(
                'title' => 'Τροποποίηση ωρολογίου Προτύπου Εκκλησιαστικού Γυμνασίου',
                'citation_title' => 'Πρότυπο Εκκλησιαστικό Γυμνάσιο',
                'decision' => 'Υ.Α. 110640/Θ2/11-09-2025',
                'fek' => 'ΦΕΚ Β΄ 4881/15-09-2025',
                'date' => '2025-09-11',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2025/20250204881.pdf',
                'valid_from' => '2025-2026',
                'school_types' => array('protypo_ekklisiastiko_gymnasio'),
                'topics' => array('weekly_timetable', 'ecclesiastical_education'),
            ),

            'ecclesiastical_iconography_2020' => array(
                'title' => 'Διδασκαλία του μαθήματος «Εικονογραφία» στα Εκκλησιαστικά Σχολεία',
                'citation_title' => 'Εικονογραφία Π.Ε.Σ.',
                'decision' => 'Υ.Α. 71346/Θ2/10-06-2020',
                'fek' => 'ΦΕΚ Β΄ 2466/22-06-2020',
                'date' => '2020-06-10',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2020/20200202466.pdf',
                'valid_from' => '2020-2021',
                'school_types' => array('protypo_ekklisiastiko_gymnasio', 'protypo_ekklisiastiko_lykeio'),
                'topics' => array('teaching_assignments', 'iconography', 'ecclesiastical_education'),
                'amended_by' => array('ecclesiastical_iconography_2023'),
            ),
            'ecclesiastical_iconography_2023' => array(
                'title' => 'Τροποποίηση της απόφασης για τη διδασκαλία της Εικονογραφίας στα Εκκλησιαστικά Σχολεία',
                'citation_title' => 'Τροποποίηση Εικονογραφίας Π.Ε.Σ.',
                'decision' => 'Υ.Α. 4404/Θ2/16-01-2023',
                'fek' => 'ΦΕΚ Β΄ 253/20-01-2023',
                'date' => '2023-01-16',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2023/20230200253.pdf',
                'valid_from' => '2022-2023',
                'school_types' => array('protypo_ekklisiastiko_gymnasio', 'protypo_ekklisiastiko_lykeio'),
                'topics' => array('teaching_assignments', 'iconography', 'ecclesiastical_education'),
                'amends' => array('ecclesiastical_iconography_2020'),
            ),
            'ecclesiastical_byzantine_music_qualification_2010' => array(
                'title' => 'Πρόσθετο προσόν για τη Βυζαντινή Μουσική στη δευτεροβάθμια εκκλησιαστική εκπαίδευση',
                'citation_title' => 'Δίπλωμα Βυζαντινής Μουσικής Π.Ε.Σ.',
                'decision' => 'ν. 3848/2010, άρθρο 39 παρ. 9',
                'fek' => 'ΦΕΚ Α΄ 71/19-05-2010',
                'date' => '2010-05-19',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/01/2010/20100100071.pdf',
                'valid_from' => '2010-2011',
                'school_types' => array('protypo_ekklisiastiko_gymnasio', 'protypo_ekklisiastiko_lykeio'),
                'topics' => array('teacher_qualifications', 'byzantine_music', 'ecclesiastical_education'),
            ),

            'eae_assignments_2026' => array(
                'title' => 'Αναθέσεις μαθημάτων Γυμνασίων / Λυκείων Ε.Α.Ε.',
                'citation_title' => 'Ε.Α.Ε.',
                'decision' => 'Υ.Α. 72559/Δ3/04-06-2026',
                'fek' => 'ΦΕΚ Β΄ 3275/11-06-2026',
                'date' => '2026-06-04',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK_3275_B_2026_ANATHESEIS%20EAE_GYMN_LYK_2026-2027.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('eae_gymnasio', 'eae_lykeio'),
                'topics' => array('teaching_assignments', 'special_education'),
                'link_label' => 'ΦΕΚ Β΄ 3275/2026 — Ε.Α.Ε. ↗',
                'amended_by' => array('eae_assignments_2026_5610'),
            ),
            'eae_assignments_2026_5610' => array(
                'title' => 'Τροποποίηση αναθέσεων μαθημάτων Γυμνασίων / Λυκείων Ε.Α.Ε.',
                'citation_title' => 'Τροποποίηση αναθέσεων Ε.Α.Ε.',
                'decision' => 'Υ.Α. 117015/Δ3/08-09-2026',
                'fek' => 'ΦΕΚ Β΄ 5610/17-09-2026',
                'date' => '2026-09-08',
                'url' => 'https://search.et.gr/el/fek/?fekId=805582',
                'valid_from' => '2026-2027',
                'school_types' => array('eae_gymnasio', 'eae_lykeio'),
                'topics' => array('teaching_assignments', 'special_education'),
                'link_label' => 'ΦΕΚ Β΄ 5610/2026 — Τροποποίηση αναθέσεων Ε.Α.Ε. ↗',
                'amends' => array('eae_assignments_2026'),
            ),
            'eneegyl_assignments_2026' => array(
                'title' => 'Αναθέσεις μαθημάτων ΕΝ.Ε.Ε.ΓΥ.-Λ.',
                'citation_title' => 'ΕΝ.Ε.Ε.ΓΥ.-Λ.',
                'decision' => 'Υ.Α. 69785/Δ3/29-05-2026',
                'fek' => 'ΦΕΚ Β΄ 3216/05-06-2026',
                'date' => '2026-05-29',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK_3216_B_2026_ANATHESEIS%20ENEEGYL_2026-2027.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('eneegyl_gymnasio', 'eneegyl_lykeio'),
                'topics' => array('teaching_assignments', 'special_education', 'vocational_special_education'),
                'link_label' => 'ΦΕΚ Β΄ 3216/2026 — ΕΝ.Ε.Ε.ΓΥ.-Λ. ↗',
            ),


            'kallitexnika_assignments_2018' => array(
                'title' => 'Αναθέσεις μαθημάτων καλλιτεχνικής παιδείας Καλλιτεχνικών Σχολείων',
                'citation_title' => 'Βασική απόφαση αναθέσεων Καλλιτεχνικών Σχολείων',
                'decision' => 'Υ.Α. 148262/Δ2/10-09-2018',
                'fek' => 'ΦΕΚ Β΄ 4077/17-09-2018',
                'date' => '2018-09-10',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2018/20180204077.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('kallitexniko_gymnasio', 'kallitexniko_gel'),
                'topics' => array('teaching_assignments', 'art_schools'),
                'amended_by' => array('kallitexnika_assignments_2024'),
            ),
            'kallitexnika_assignments_2024' => array(
                'title' => 'Τροποποίηση αναθέσεων μαθημάτων καλλιτεχνικής παιδείας Καλλιτεχνικών Σχολείων',
                'citation_title' => 'Αναθέσεις καλλιτεχνικής παιδείας',
                'decision' => 'Υ.Α. 65409/Δ2/12-06-2024',
                'fek' => 'ΦΕΚ Β΄ 3418/13-06-2024',
                'date' => '2024-06-12',
                'ada' => '99ΓΦ46ΝΚΠΔ-9Γ1',
                'url' => 'https://diavgeia.gov.gr/doc/99ΓΦ46ΝΚΠΔ-9Γ1?inline=true',
                'alternate_urls' => array(
                    'encoded_ada' => 'https://diavgeia.gov.gr/doc/99%CE%93%CE%A646%CE%9D%CE%9A%CE%A0%CE%94-9%CE%931?inline=true',
                ),
                'valid_from' => '2024-2025',
                'school_types' => array('kallitexniko_gymnasio', 'kallitexniko_gel'),
                'topics' => array('teaching_assignments', 'art_schools'),
                'amends' => array('kallitexnika_assignments_2018'),
            ),
            'kallitexnika_timetable_2026' => array(
                'title' => 'Ωρολόγιο πρόγραμμα Καλλιτεχνικού Γυμνασίου και Γενικού Καλλιτεχνικού Λυκείου',
                'citation_title' => 'Καλλιτεχνικά Σχολεία',
                'decision' => 'Υ.Α. 43820/Δ2/07-04-2026',
                'fek' => 'ΦΕΚ Β΄ 2104/09-04-2026',
                'date' => '2026-04-07',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202104_09_04_26_OP%20KALL%20GYMN%20GEL%201.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('kallitexniko_gymnasio', 'kallitexniko_gel'),
                'topics' => array('weekly_timetable', 'art_schools'),
            ),
            'mousika_assignments_2018' => array(
                'title' => 'Αναθέσεις μαθημάτων μουσικής παιδείας Μουσικών Σχολείων',
                'citation_title' => 'Αναθέσεις μουσικής παιδείας',
                'decision' => 'Υ.Α. 144236/Δ2/05-09-2018',
                'fek' => 'ΦΕΚ Β΄ 4202/25-09-2018',
                'date' => '2018-09-05',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2018/20180204202.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('mousiko_gymnasio', 'mousiko_gel'),
                'topics' => array('teaching_assignments', 'music_schools'),
            ),
            'mousika_timetable_2026' => array(
                'title' => 'Ωρολόγιο πρόγραμμα Μουσικού Γυμνασίου και Γενικού Μουσικού Λυκείου',
                'citation_title' => 'Μουσικά Σχολεία',
                'decision' => 'Υ.Α. 43787/Δ2/07-04-2026',
                'fek' => 'ΦΕΚ Β΄ 2107/09-04-2026',
                'date' => '2026-04-07',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202107_09_04_26_OP%20MOUSIKOU%20GYMN%20GEL.pdf',
                'alternate_urls' => array(
                    'assignments_card' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/4.%20PHEK%20B%202107_09_04_26_OP%20MOUSIKOU%20GYMN%20GEL.pdf',
                ),
                'valid_from' => '2026-2027',
                'school_types' => array('mousiko_gymnasio', 'mousiko_gel'),
                'topics' => array('weekly_timetable', 'music_schools'),
            ),
            'eeeek_assignments_2018' => array(
                'title' => 'Αναθέσεις μαθημάτων στα Εργαστήρια Ειδικής Επαγγελματικής Εκπαίδευσης (Ε.Ε.Ε.ΕΚ.)',
                'citation_title' => 'Αναθέσεις Ε.Ε.Ε.ΕΚ.',
                'decision' => 'Υ.Α. 71105/Δ3/04-05-2018',
                'fek' => 'ΦΕΚ Β΄ 1761/17-05-2018',
                'date' => '2018-05-04',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2018/20180201761.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('eeeek'),
                'topics' => array('teaching_assignments', 'special_education', 'eeeek'),
            ),
            'eeeek_timetable_2002' => array(
                'title' => 'Καθορισμός εβδομαδιαίου ωρολογίου προγράμματος Ε.Ε.Ε.ΕΚ.',
                'citation_title' => 'Ωρολόγιο Ε.Ε.Ε.ΕΚ.',
                'decision' => 'Υ.Α. 57523/Γ6/04-06-2002',
                'fek' => 'ΦΕΚ Β΄ 765/19-06-2002',
                'date' => '2002-06-04',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2002/20020200765.pdf',
                'valid_from' => '2002-2003',
                'school_types' => array('eeeek'),
                'topics' => array('weekly_timetable', 'special_education', 'eeeek'),
            ),
            'eeeek_six_classes_2016' => array(
                'title' => 'Έξι τάξεις και ειδική λειτουργία της ΣΤ΄ τάξης των Ε.Ε.Ε.ΕΚ.',
                'citation_title' => 'Έξι τάξεις & ΣΤ΄ Ε.Ε.Ε.ΕΚ.',
                'decision' => 'ν. 4415/2016, άρθρο 48 παρ. 4',
                'fek' => 'ΦΕΚ Α΄ 159/06-09-2016',
                'date' => '2016-09-06',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/01/2016/20160100159.pdf',
                'valid_from' => '2016-2017',
                'school_types' => array('eeeek'),
                'topics' => array('school_structure', 'weekly_timetable', 'special_education', 'eeeek'),
            ),

            // -----------------------------------------------------------------
            // Vocational education (EPAL / Model EPAL) — assignments
            // -----------------------------------------------------------------
            'epal_assignments_2018' => array(
                'title' => 'Αναθέσεις μαθημάτων Επαγγελματικού Λυκείου',
                'citation_title' => 'Αναθέσεις ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ22/75401/Δ4/10-05-2018',
                'fek' => 'ΦΕΚ Β΄ 1664/15-05-2018',
                'date' => '2018-05-10',
                'url' => 'https://gsvetlly.minedu.gov.gr/publications/mathiteia/thesmiko/%CE%91%CE%9D%CE%91%CE%98%CE%95%CE%A3%CE%95%CE%99%CE%A3_%CE%9C%CE%91%CE%98%CE%97%CE%9C%CE%91%CE%A4%CE%A9%CE%9D_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_1664-4-15-5-18.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education'),
                'amended_by' => array(
                    'epal_assignments_2018_2637',
                    'epal_assignments_2019_2779',
                    'epal_assignments_2020_453',
                    'epal_assignments_2020_3609',
                    'epal_assignments_2023_418',
                    'epal_assignments_2023_5206',
                    'epal_assignments_2025_1975',
                    'epal_assignments_2026_2625',
                ),
                'supplemented_by' => array('epal_assignments_2018_3520'),
                'link_label' => 'ΦΕΚ Β΄ 1664/2018 — Αναθέσεις ΕΠΑ.Λ. ↗',
            ),
            'epal_assignments_2018_2637' => array(
                'title' => 'Τροποποίηση αναθέσεων ΕΠΑ.Λ. — Α΄ τάξη και ειδικότητες Γ΄ τάξης',
                'citation_title' => 'Τροποποιήσεις ειδικοτήτων Γ΄ ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ22/107970/Δ4/28-06-2018',
                'fek' => 'ΦΕΚ Β΄ 2637/05-07-2018',
                'date' => '2018-06-28',
                'url' => 'https://www.minedu.gov.gr/publications/docs2018/EPAL_FEK_2637%CE%92_05-07-2018.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education'),
                'amends' => array('epal_assignments_2018'),
                'link_label' => 'ΦΕΚ Β΄ 2637/2018 — Τροποποιήσεις Ειδικοτήτων Γ΄ ΕΠΑ.Λ. ↗',
            ),
            'epal_assignments_2018_3520' => array(
                'title' => 'Αναθέσεις Τομέα Ναυτιλιακών Επαγγελμάτων ΕΠΑ.Λ.',
                'citation_title' => 'Τομέας Ναυτιλιακών Επαγγελμάτων',
                'decision' => 'Υ.Α. Φ22/134291/Δ4/08-08-2018',
                'fek' => 'ΦΕΚ Β΄ 3520/21-08-2018',
                'date' => '2018-08-08',
                'url' => 'https://www.minedu.gov.gr/publications/docs2018/4._%CE%A6%CE%95%CE%9A_3520_%CE%92_21.08.2018.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'maritime'),
                'supplements' => array('epal_assignments_2018'),
                'link_label' => 'ΦΕΚ Β΄ 3520/2018 — Τομέας Ναυτιλιακών Επαγγελμάτων ↗',
            ),
            'epal_assignments_2019_2779' => array(
                'title' => 'Τροποποίηση αναθέσεων ΕΠΑ.Λ. — Εφαρμοσμένες Τέχνες και Υγεία-Πρόνοια-Ευεξία',
                'citation_title' => 'Εφαρμοσμένες Τέχνες & Υγεία – Πρόνοια – Ευεξία',
                'decision' => 'Υ.Α. Φ22/105626/Δ4/01-07-2019',
                'fek' => 'ΦΕΚ Β΄ 2779/04-07-2019',
                'date' => '2019-07-01',
                'url' => 'https://www.minedu.gov.gr/publications/docs2019/%CE%A6%CE%95%CE%9A_2779%CE%92_04.07.2019.pdf',
                'valid_from' => '2019-2020',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education'),
                'amends' => array('epal_assignments_2018'),
                'link_label' => 'ΦΕΚ Β΄ 2779/2019 — Εφαρμοσμένες Τέχνες & Υγεία – Πρόνοια – Ευεξία ↗',
            ),
            'epal_assignments_2020_453' => array(
                'title' => 'Τροποποίηση αναθέσεων ΕΠΑ.Λ. — Εφαρμοσμένες Τέχνες Β΄ τάξης',
                'citation_title' => 'Εφαρμοσμένες Τέχνες Β΄ ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ22/18418/Δ4/07-02-2020',
                'fek' => 'ΦΕΚ Β΄ 453/13-02-2020',
                'date' => '2020-02-07',
                'url' => 'https://www.minedu.gov.gr/publications/docs2020/%CE%A6%CE%95%CE%9A_453%CE%92_13.02.2020.pdf',
                'valid_from' => '2019-2020',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education'),
                'amends' => array('epal_assignments_2018'),
                'link_label' => 'ΦΕΚ Β΄ 453/2020 — Εφαρμοσμένες Τέχνες Β΄ ΕΠΑ.Λ. ↗',
            ),
            'epal_assignments_2020_3609' => array(
                'title' => 'Τροποποίηση αναθέσεων ΕΠΑ.Λ. — Τεχνικός Τεχνολογίας Τροφίμων και Ποτών',
                'citation_title' => 'Τεχνικός Τεχνολογίας Τροφίμων και Ποτών',
                'decision' => 'Υ.Α. Φ22/110010/Δ4/25-08-2020',
                'fek' => 'ΦΕΚ Β΄ 3609/29-08-2020',
                'date' => '2020-08-25',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2020_08_25_EXE_110010_trop_YA_Anath_mathem_EPAL_Tomea%20Geop_Troph%20kai%20Peribal_PHEK_3609_29.08.2020.pdf',
                'valid_from' => '2020-2021',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education'),
                'amends' => array('epal_assignments_2018'),
                'link_label' => 'ΦΕΚ Β΄ 3609/2020 — Τεχνικός Τεχνολογίας Τροφίμων και Ποτών Γ΄ ΕΠΑ.Λ. ↗',
            ),
            'epal_assignments_2023_418' => array(
                'title' => 'Τροποποίηση αναθέσεων ΕΠΑ.Λ. — Ερευνητική Εργασία στην Τεχνολογία',
                'citation_title' => 'Ερευνητική Εργασία στην Τεχνολογία',
                'decision' => 'Υ.Α. Φ22/8778/Δ4/25-01-2023',
                'fek' => 'ΦΕΚ Β΄ 418/30-01-2023',
                'date' => '2023-01-25',
                'url' => 'https://www.minedu.gov.gr/site/54459-27-01-23-tropopoiisi-tis-ypo-stoixeia-f22-75401-d4-10-05-2018-v-1664-ypourgikis-apofasis-peri-ton-anatheseon-mathimaton-epaggelmatikoy-lykeiou-os-pros-tis-anatheseis-tou-mathimatos-prosanatolismoy-erevnitiki-ergasia-stin-texnologia-tis-a-taksis-imerisiou-kai-esperinoy-epa-l',
                'valid_from' => '2022-2023',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education'),
                'amends' => array('epal_assignments_2018'),
            ),
            'epal_assignments_2023_5206' => array(
                'title' => 'Τροποποίηση αναθέσεων ΕΠΑ.Λ. — Χημεία Α΄/Β΄/Γ΄ τάξης',
                'citation_title' => 'Χημεία ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ22/92691/Δ4/23-08-2023',
                'fek' => 'ΦΕΚ Β΄ 5206/28-08-2023',
                'date' => '2023-08-23',
                'url' => 'https://www.minedu.gov.gr/publications/docs2020/2023_08_23_%CE%95%CE%9E%CE%95_92691_%CF%84%CF%81%CE%BF%CF%80%CE%BF%CF%80_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8_%CE%BC%CE%B1%CE%B8_%CE%A7%CE%97%CE%9C%CE%95%CE%99%CE%91_%CE%93%CE%B5%CE%BD%CE%A0_%CE%91_%CE%92_%CE%93_%CF%84%CE%AC%CE%BE%CE%B7%CF%82_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_5206%CE%92_29.08.2023.pdf',
                'valid_from' => '2023-2024',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'chemistry'),
                'amends' => array('epal_assignments_2018'),
                'link_label' => 'ΦΕΚ Β΄ 5206/2023 — Χημεία ΕΠΑ.Λ. / Π.ΕΠΑ.Λ. ↗',
            ),
            'epal_assignments_2025_1975' => array(
                'title' => 'Τροποποίηση αναθέσεων ΕΠΑ.Λ. — Ιστορία Α΄ τάξης',
                'citation_title' => 'Ιστορία Α΄ ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ22/40504/Δ4/10-04-2025',
                'fek' => 'ΦΕΚ Β΄ 1975/23-04-2025',
                'date' => '2025-04-10',
                'url' => 'https://www.minedu.gov.gr/publications/docs2023/2025_04_10_%CE%95%CE%9E%CE%95_40504_%CF%84%CF%81%CE%BF%CF%80%CE%BF%CF%80_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8%CE%AD%CF%83%CE%B5%CE%B9%CF%82_%CE%BC%CE%B1%CE%B8%CE%AE%CE%BC_%CE%99%CE%A3%CE%A4%CE%9F%CE%A1%CE%99%CE%91_%CE%91_%CF%84%CE%AC%CE%BE%CE%B7%CF%82_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_1975%CE%92_23.04.2025.pdf',
                'valid_from' => '2025-2026',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'history'),
                'amends' => array('epal_assignments_2018'),
                'link_label' => 'ΦΕΚ Β΄ 1975/2025 — Ιστορία Α΄ ΕΠΑ.Λ. ↗',
            ),
            'epal_assignments_2026_2625' => array(
                'title' => 'Τροποποίηση αναθέσεων ΕΠΑ.Λ. — Ηθική και Γενική Παιδεία Α΄/Β΄ τάξης',
                'citation_title' => 'Ηθική Α΄/Β΄ ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ22/55785/Δ4/07-05-2026',
                'fek' => 'ΦΕΚ Β΄ 2625/11-05-2026',
                'date' => '2026-05-07',
                'url' => 'https://diavgeia.gov.gr/doc/ΨΩ0Α46ΝΚΠΔ-Α4Υ?inline=true',
                'valid_from' => '2026-2027',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'ethics'),
                'amends' => array('epal_assignments_2018'),
                'link_label' => 'Υ.Α. Φ22/55785/Δ4/2026 — ΦΕΚ Β΄ 2625/2026 · Ηθική Α΄/Β΄ ΕΠΑ.Λ. ↗',
            ),

            'pepal_a_assignments_2021' => array(
                'title' => 'Αναθέσεις Α΄ τάξης Πρότυπων Επαγγελματικών Λυκείων',
                'citation_title' => 'Α΄ Π.ΕΠΑ.Λ. / αναθέσεις',
                'decision' => 'Υ.Α. Φ9/116550/Δ4/17-09-2021',
                'fek' => 'ΦΕΚ Β΄ 4367/22-09-2021',
                'date' => '2021-09-17',
                'url' => 'https://www.minedu.gov.gr/publications/docs2025/epal/%CE%99%CE%A3%CE%A7%CE%A5%CE%9F%CE%9D_%CE%98%CE%95%CE%A3%CE%9C%CE%99%CE%9A%CE%9F_%CE%A0%CE%9B%CE%91%CE%99%CE%A3%CE%99%CE%9F_%CE%95%CE%A0%CE%91%CE%9B_12-02-2025.pdf',
                'valid_from' => '2021-2022',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'model_vocational_school'),
                'amended_by' => array('pepal_a_assignments_2023_5188', 'pepal_a_assignments_2023_7403', 'pepal_a_assignments_2025_1832', 'pepal_a_assignments_2026_2687'),
            ),
            'pepal_a_assignments_2023_5188' => array(
                'title' => 'Τροποποίηση αναθέσεων Α΄ Π.ΕΠΑ.Λ. — Χημεία',
                'citation_title' => 'Α΄ Π.ΕΠΑ.Λ. / Χημεία',
                'decision' => 'Υ.Α. Φ9/92688/Δ4/23-08-2023',
                'fek' => 'ΦΕΚ Β΄ 5188/25-08-2023',
                'date' => '2023-08-23',
                'url' => 'https://www.minedu.gov.gr/publications/docs2020/2023_08_23_%CE%95%CE%9E%CE%95_92688_%CF%84%CF%81%CE%BF%CF%80%CE%BF%CF%80_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8%CE%AD%CF%83%CE%B5%CE%B9%CF%82_%CE%BC%CE%B1%CE%B8%CE%B7%CE%BC%CE%AC%CF%84%CF%89%CE%BD_%CE%91_%CF%84%CE%AC%CE%BE%CE%B7%CF%82_%CE%A0_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_5188%CE%92_25.08.2023.pdf',
                'valid_from' => '2023-2024',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'chemistry'),
                'amends' => array('pepal_a_assignments_2021'),
                'link_label' => 'ΦΕΚ Β΄ 5188/2023 — Α΄ Π.ΕΠΑ.Λ. / Χημεία ↗',
            ),
            'pepal_a_assignments_2023_7403' => array(
                'title' => 'Τροποποίηση αναθέσεων Α΄ Π.ΕΠΑ.Λ. — διαθεματικές αναθέσεις και συνδιδασκαλία',
                'citation_title' => 'Α΄ Π.ΕΠΑ.Λ. / διαθεματικές αναθέσεις',
                'decision' => 'Υ.Α. Φ9/143775/Δ4/14-12-2023',
                'fek' => 'ΦΕΚ Β΄ 7403/28-12-2023',
                'date' => '2023-12-14',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2023/20230207403.pdf',
                'valid_from' => '2023-2024',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'cross_curricular_assignment'),
                'amends' => array('pepal_a_assignments_2021'),
                'link_label' => 'ΦΕΚ Β΄ 7403/2023 — Α΄ Π.ΕΠΑ.Λ. / διαθεματικές αναθέσεις ↗',
            ),
            'pepal_a_assignments_2025_1832' => array(
                'title' => 'Τροποποίηση αναθέσεων Α΄ Π.ΕΠΑ.Λ. — Ιστορία',
                'citation_title' => 'Α΄ Π.ΕΠΑ.Λ. / Ιστορία',
                'decision' => 'Υ.Α. Φ9/40530/Δ4/10-04-2025',
                'fek' => 'ΦΕΚ Β΄ 1832/14-04-2025',
                'date' => '2025-04-10',
                'url' => 'https://www.minedu.gov.gr/publications/docs2023/2025_04_10_%CE%95%CE%9E%CE%95_40530_%CF%84%CF%81%CE%BF%CF%80%CE%BF%CF%80_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8%CE%AD%CF%83%CE%B5%CE%B9%CF%82_%CE%BC%CE%B1%CE%B8%CE%AE%CE%BC_%CE%99%CE%A3%CE%A4%CE%9F%CE%A1%CE%99%CE%91_%CE%91_%CF%84%CE%AC%CE%BE%CE%B7%CF%82_%CE%A0_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_1832%CE%92_14.04.2025.pdf',
                'valid_from' => '2025-2026',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'history'),
                'amends' => array('pepal_a_assignments_2021'),
                'link_label' => 'ΦΕΚ Β΄ 1832/2025 — Α΄ Π.ΕΠΑ.Λ. / Ιστορία ↗',
            ),
            'pepal_a_assignments_2026_2687' => array(
                'title' => 'Τροποποίηση αναθέσεων Α΄ Π.ΕΠΑ.Λ. — Ηθική και Γενική Παιδεία',
                'citation_title' => 'Α΄ Π.ΕΠΑ.Λ. / Ηθική και Γενική Παιδεία',
                'decision' => 'Υ.Α. Φ9/55830/Δ4/07-05-2026',
                'fek' => 'ΦΕΚ Β΄ 2687/13-05-2026',
                'date' => '2026-05-07',
                'url' => 'https://diavgeia.gov.gr/doc/%CE%A8%CE%A0%CE%9F%CE%A146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A9%CE%9C%CE%93?inline=true',
                'valid_from' => '2026-2027',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'ethics'),
                'amends' => array('pepal_a_assignments_2021'),
                'link_label' => 'ΦΕΚ Β΄ 2687/2026 — Α΄ Π.ΕΠΑ.Λ. / Ηθική και Γενική Παιδεία ↗',
            ),
            'pepal_b_assignments_2022' => array(
                'title' => 'Αναθέσεις Β΄ τάξης Πρότυπων Επαγγελματικών Λυκείων',
                'citation_title' => 'Β΄ Π.ΕΠΑ.Λ. / αναθέσεις',
                'decision' => 'Υ.Α. Φ9/114791/Δ4/21-09-2022',
                'fek' => 'ΦΕΚ Β΄ 4983/26-09-2022',
                'date' => '2022-09-21',
                'url' => 'https://diavgeia.gov.gr/doc/%CE%A8%CE%A0%CE%A4246%CE%9C%CE%A4%CE%9B%CE%97-%CE%914%CE%97?inline=true',
                'valid_from' => '2022-2023',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'model_vocational_school'),
                'amended_by' => array('pepal_b_assignments_2023_418', 'pepal_b_assignments_2023_5206', 'pepal_b_assignments_2026_2624'),
                'link_label' => 'ΦΕΚ Β΄ 4983/2022 — Β΄ Π.ΕΠΑ.Λ. / Γενική Παιδεία & Τομείς ↗',
            ),
            'pepal_b_assignments_2023_418' => array(
                'title' => 'Τροποποίηση αναθέσεων Β΄ Π.ΕΠΑ.Λ. — Ναυτιλιακά',
                'citation_title' => 'Β΄ Π.ΕΠΑ.Λ. / Ναυτιλιακά',
                'decision' => 'Υ.Α. Φ9/8772/Δ4/25-01-2023',
                'fek' => 'ΦΕΚ Β΄ 418/30-01-2023',
                'date' => '2023-01-25',
                'url' => 'https://www.minedu.gov.gr/publications/docs2020/2023_01_25_%CE%95%CE%9E%CE%95_8772_%CF%84%CF%81%CE%BF%CF%80%CE%BF%CF%80_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8%CE%AD%CF%83%CE%B5%CE%B9%CF%82_%CE%BC%CE%B1%CE%B8%CE%B7%CE%BC%CE%AC%CF%84%CF%89%CE%BD_%CE%92_%CF%84%CE%AC%CE%BE%CE%B7%CF%82_%CE%A0_%CE%95%CE%A0%CE%91%CE%9B.pdf',
                'valid_from' => '2022-2023',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'maritime'),
                'amends' => array('pepal_b_assignments_2022'),
                'link_label' => 'ΦΕΚ Β΄ 418/2023 — Β΄ Π.ΕΠΑ.Λ. / Ναυτιλιακά ↗',
            ),
            'pepal_b_assignments_2023_5206' => array(
                'title' => 'Τροποποίηση αναθέσεων Β΄ Π.ΕΠΑ.Λ. — Χημεία',
                'citation_title' => 'Β΄ Π.ΕΠΑ.Λ. / Χημεία',
                'decision' => 'Υ.Α. Φ9/92706/Δ4/23-08-2023',
                'fek' => 'ΦΕΚ Β΄ 5206/28-08-2023',
                'date' => '2023-08-23',
                'url' => 'https://www.minedu.gov.gr/publications/docs2020/2023_08_23_%CE%95%CE%9E%CE%95_92706_%CF%84%CF%81%CE%BF%CF%80%CE%BF%CF%80_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8%CE%AD%CF%83%CE%B5%CE%B9%CF%82_%CE%BC%CE%B1%CE%B8%CE%B7%CE%BC%CE%AC%CF%84%CF%89%CE%BD_%CE%92_%CF%84%CE%AC%CE%BE%CE%B7%CF%82_%CE%A0_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_5206%CE%92_28.08.2023.pdf',
                'valid_from' => '2023-2024',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'chemistry'),
                'amends' => array('pepal_b_assignments_2022'),
                'link_label' => 'ΦΕΚ Β΄ 5206/2023 — Β΄ Π.ΕΠΑ.Λ. / Χημεία ↗',
            ),
            'pepal_b_assignments_2026_2624' => array(
                'title' => 'Τροποποίηση αναθέσεων Β΄ Π.ΕΠΑ.Λ. — Ηθική και Γενική Παιδεία',
                'citation_title' => 'Β΄ Π.ΕΠΑ.Λ. / Ηθική και Γενική Παιδεία',
                'decision' => 'Υ.Α. Φ9/55875/Δ4/07-05-2026',
                'fek' => 'ΦΕΚ Β΄ 2624/11-05-2026',
                'date' => '2026-05-07',
                'url' => 'https://diavgeia.gov.gr/doc/9%CE%9C%CE%A4%CE%A146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A6%CE%97%CE%95?inline=true',
                'valid_from' => '2026-2027',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'ethics'),
                'amends' => array('pepal_b_assignments_2022'),
                'link_label' => 'ΦΕΚ Β΄ 2624/2026 — Β΄ Π.ΕΠΑ.Λ. / Ηθική και Γενική Παιδεία ↗',
            ),
            'pepal_g_assignments_2023' => array(
                'title' => 'Αναθέσεις Γ΄ τάξης Πρότυπων Επαγγελματικών Λυκείων',
                'citation_title' => 'Γ΄ Π.ΕΠΑ.Λ. / αναθέσεις',
                'decision' => 'Υ.Α. Φ9/101003/Δ4/13-09-2023',
                'fek' => 'ΦΕΚ Β΄ 5510/18-09-2023',
                'date' => '2023-09-13',
                'url' => 'https://www.minedu.gov.gr/publications/docs2023/2023_09_13_%CE%95%CE%9E%CE%95_101003_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8%CE%AD%CF%83%CE%B5%CE%B9%CF%82_%CE%9C%CE%B1%CE%B8%CE%B7%CE%BC%CE%AC%CF%84%CF%89%CE%BD_%CE%93_%CE%A4%CE%AC%CE%BE%CE%B7%CF%82_%CE%A0_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_5510%CE%92_18.09.2023.pdf',
                'valid_from' => '2023-2024',
                'school_types' => array('pepal'),
                'topics' => array('teaching_assignments', 'vocational_education', 'model_vocational_school'),
                'link_label' => 'ΦΕΚ Β΄ 5510/2023 — Γ΄ Π.ΕΠΑ.Λ. / Γενική Παιδεία & Ειδικότητες ↗',
            ),

            // -----------------------------------------------------------------
            // Vocational education (EPAL / Model EPAL) — weekly timetables
            // -----------------------------------------------------------------
            'epal_ab_timetable_2018' => array(
                'title' => 'Ωρολόγιο Α΄/Β΄ τάξης Ημερήσιου ΕΠΑ.Λ.',
                'citation_title' => 'Α΄/Β΄ ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ2/92271/Δ4/05-06-2018',
                'fek' => 'ΦΕΚ Β΄ 2187/12-06-2018',
                'date' => '2018-06-05',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2018.05_YA_OPS_AB_taxes_EPAL_N_4386_2016_NEO_PHEK_2187B_12.06.2018.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('epal'),
                'topics' => array('weekly_timetable', 'vocational_education'),
                'amended_by' => array('epal_day_ab_timetable_2026'),
                'link_label' => 'ΦΕΚ Β΄ 2187/2018 — Α΄/Β΄ ΕΠΑ.Λ. ↗',
            ),
            'epal_day_ab_timetable_2026' => array(
                'title' => 'Τροποποίηση ωρολογίου Γενικής Παιδείας Α΄/Β΄ Ημερήσιου ΕΠΑ.Λ.',
                'citation_title' => 'Α΄/Β΄ Ημερήσιου ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ2/44260/Δ4',
                'fek' => 'ΦΕΚ Β΄ 2151/16-04-2026',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2026_04_08_EXE_44260_tropop_YA_OPS_mathema_ETHIKE_A_B_EPAL_PHEK_2151B_16.04.2026.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('epal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'ethics'),
                'amends' => array('epal_ab_timetable_2018'),
            ),
            'epal_evening_a_timetable_2026' => array(
                'title' => 'Τροποποίηση ωρολογίου Γενικής Παιδείας Α΄ τριετούς Εσπερινού ΕΠΑ.Λ.',
                'citation_title' => 'Α΄ Εσπερινού ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ2/44268/Δ4',
                'fek' => 'ΦΕΚ Β΄ 2151/16-04-2026',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2026_04_08_EXE_44260_tropop_YA_OPS_mathema_ETHIKE_A_B_EPAL_PHEK_2151B_16.04.2026.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('esperino_epal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'ethics'),
                'amends' => array('esperino_epal_timetable_2018'),
            ),
            'epal_g_timetable_2017' => array(
                'title' => 'Ωρολόγιο Γ΄ τάξης Ημερήσιου ΕΠΑ.Λ.',
                'citation_title' => 'Γ΄ Ημερήσιου ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ2/65921/Δ4/21-04-2017',
                'fek' => 'ΦΕΚ Β΄ 1426/26-04-2017',
                'date' => '2017-04-21',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2017.04_YA_OPS_GD_taxes_EPAL_N.4386%20PHEK%201426%20B_26-4-17.pdf',
                'valid_from' => '2017-2018',
                'school_types' => array('epal'),
                'topics' => array('weekly_timetable', 'vocational_education'),
                'amended_by' => array('epal_g_timetable_2017_2072', 'epal_tourism_languages_2018', 'epal_g_art_timetable_2018_day'),
                'supplemented_by' => array('epal_naval_timetable_2018'),
                'link_label' => 'ΦΕΚ Β΄ 1426/2017 — Γ΄ Ημερήσιου ΕΠΑ.Λ. ↗',
            ),
            'epal_g_timetable_2017_2072' => array(
                'title' => 'Τροποποιήσεις ωρολογίου Γ΄/Δ΄ ΕΠΑ.Λ.',
                'citation_title' => 'Τροποποιήσεις Γ΄ ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ2/95229/Δ4/07-06-2017',
                'fek' => 'ΦΕΚ Β΄ 2072/15-06-2017',
                'date' => '2017-06-07',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2017.06_tropop_YA_OPS_G_D_taxes_EPAL_N_4386_2016_PHEK_2072_t.B_15-6-17.pdf',
                'valid_from' => '2017-2018',
                'school_types' => array('epal'),
                'topics' => array('weekly_timetable', 'vocational_education'),
                'amends' => array('epal_g_timetable_2017'),
                'link_label' => 'ΦΕΚ Β΄ 2072/2017 — Τροποποιήσεις Γ΄ ΕΠΑ.Λ. (Γεωπονία κ.ά.) ↗',
            ),
            'esperino_epal_timetable_2018' => array(
                'title' => 'Ωρολόγιο τριετούς Εσπερινού ΕΠΑ.Λ.',
                'citation_title' => 'Τριετές Εσπερινό ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ2/107972/Δ4/28-06-2018',
                'fek' => 'ΦΕΚ Β΄ 2636/05-07-2018',
                'date' => '2018-06-28',
                'url' => 'https://www.minedu.gov.gr/publications/docs2018/FEK_2636B.pdf',
                'valid_from' => '2018-2019 (Α΄/Β΄), 2019-2020 (Γ΄)',
                'school_types' => array('esperino_epal'),
                'topics' => array('weekly_timetable', 'vocational_education'),
                'amended_by' => array('epal_evening_a_timetable_2026', 'epal_g_art_timetable_2018_evening'),
                'supplemented_by' => array('epal_naval_timetable_2018'),
                'link_label' => 'ΦΕΚ Β΄ 2636/2018 — Τριετές Εσπερινό ΕΠΑ.Λ. ↗',
            ),
            'epal_tourism_languages_2018' => array(
                'title' => 'Τροποποίηση ωρολογίου — ξένες γλώσσες ειδικότητας Υπαλλήλου Τουριστικών Επιχειρήσεων',
                'citation_title' => 'Ξένες γλώσσες Υπαλλήλου Τουριστικών Επιχειρήσεων',
                'decision' => 'Υ.Α. Φ2/89289/Δ4/31-05-2018',
                'fek' => 'ΦΕΚ Β΄ 2122/08-06-2018',
                'date' => '2018-05-31',
                'url' => 'https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2018/20180202122.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'foreign_languages', 'tourism'),
                'amends' => array('epal_g_timetable_2017'),
                'link_label' => 'ΦΕΚ Β΄ 2122/2018 — Ξένες γλώσσες ειδικότητας Υπαλλήλου Τουριστικών Επιχειρήσεων ↗',
            ),
            'epal_naval_timetable_2018' => array(
                'title' => 'Ωρολόγιο Τομέα Ναυτιλιακών Επαγγελμάτων ΕΠΑ.Λ.',
                'citation_title' => 'Ναυτιλιακά ΕΠΑ.Λ.',
                'decision' => 'Κ.Υ.Α. Φ2/129460/Δ4',
                'fek' => 'ΦΕΚ Β΄ 3224/07-08-2018',
                'url' => 'https://www.minedu.gov.gr/publications/docs2018/orologio%CE%9D%CE%91%CE%A5%CE%A4.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'maritime'),
                'supplements' => array('epal_g_timetable_2017', 'esperino_epal_timetable_2018'),
                'link_label' => 'ΦΕΚ Β΄ 3224/2018 — Ναυτιλιακά ΕΠΑ.Λ. ↗',
            ),
            'epal_g_art_timetable_2018_day' => array(
                'title' => 'Τροποποίηση ωρολογίου ειδικότητας Σχεδιασμού-Διακόσμησης Εσωτερικών Χώρων Γ΄ Ημερήσιου ΕΠΑ.Λ.',
                'citation_title' => 'Σχεδιασμός–Διακόσμηση Γ΄ Ημερήσιου ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ2/160042/Δ4/26-09-2018',
                'fek' => 'ΦΕΚ Β΄ 4373/01-10-2018',
                'date' => '2018-09-26',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2018_09_26_tropop_YA_PHEK_1426B_2017_%20OPS_Tomea_EPHARM_TECHNON_EMER_EPAL_PHEK_4373B_01.10.2018.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('epal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'applied_arts'),
                'amends' => array('epal_g_timetable_2017'),
                'corrected_by' => array('epal_g_art_timetable_correction_2018'),
            ),
            'epal_g_art_timetable_2018_evening' => array(
                'title' => 'Τροποποίηση ωρολογίου ειδικοτήτων Εφαρμοσμένων Τεχνών Γ΄ Εσπερινού ΕΠΑ.Λ.',
                'citation_title' => 'Γραφικών Τεχνών / Σχεδιασμού–Διακόσμησης Γ΄ Εσπερινού ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ2/160050/Δ4/26-09-2018',
                'fek' => 'ΦΕΚ Β΄ 4373/01-10-2018',
                'date' => '2018-09-26',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2018_09_26_tropop_YA_PHEK_1426B_2017_%20OPS_Tomea_EPHARM_TECHNON_EMER_EPAL_PHEK_4373B_01.10.2018.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('esperino_epal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'applied_arts'),
                'amends' => array('esperino_epal_timetable_2018'),
                'corrected_by' => array('epal_g_art_timetable_correction_2018'),
            ),
            'epal_g_art_timetable_correction_2018' => array(
                'title' => 'Διορθώσεις σφαλμάτων στις τροποποιήσεις ωρολογίων Εφαρμοσμένων Τεχνών Γ΄ ΕΠΑ.Λ.',
                'citation_title' => 'Διορθώσεις σφαλμάτων Γ΄ ΕΠΑ.Λ.',
                'decision' => 'Διόρθωση σφαλμάτων',
                'fek' => 'ΦΕΚ Β΄ 4815/30-10-2018',
                'date' => '2018-10-30',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2018_10_30_tropop_YA_%20OPS_Tomea_EPHARM_TECHNON_PHEK%204373_01.10.2018_DIORTH.SPHAL._PHEK%204815B_30.10.2018.pdf',
                'valid_from' => '2018-2019',
                'school_types' => array('epal', 'esperino_epal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'applied_arts', 'correction'),
                'corrects' => array('epal_g_art_timetable_2018_day', 'epal_g_art_timetable_2018_evening'),
                'link_label' => 'ΦΕΚ Β΄ 4815/2018 — Διορθώσεις σφαλμάτων Γ΄ ΕΠΑ.Λ. ↗',
            ),

            'pepal_a_timetable_2021' => array(
                'title' => 'Ωρολόγιο Α΄ τάξης Πρότυπων Επαγγελματικών Λυκείων',
                'citation_title' => 'Α΄ Π.ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ9/90217/Δ4/23-07-2021',
                'fek' => 'ΦΕΚ Β΄ 3470/29-07-2021',
                'date' => '2021-07-23',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2021_07_23_EXE_90217_YA_OPS_A_TAXES_P_EPAL_PHEK_3470B_29.07.2021.pdf',
                'valid_from' => '2021-2022',
                'school_types' => array('pepal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'model_vocational_school'),
                'amended_by' => array('pepal_a_timetable_2026'),
                'link_label' => 'ΦΕΚ Β΄ 3470/2021 — Α΄ Π.ΕΠΑ.Λ. ↗',
            ),
            'pepal_b_timetable_2022' => array(
                'title' => 'Ωρολόγιο Β΄ τάξης Πρότυπων Επαγγελματικών Λυκείων',
                'citation_title' => 'Β΄ Π.ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ9/103460/Δ4/25-08-2022',
                'fek' => 'ΦΕΚ Β΄ 4578/30-08-2022',
                'date' => '2022-08-25',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2022_08_25_EXE_103460_YA_OPS_B_taxes_P_EPAL_n4763_2020_PHEK_4578B_30.08.2022.pdf',
                'valid_from' => '2022-2023',
                'school_types' => array('pepal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'model_vocational_school'),
                'amended_by' => array('pepal_b_timetable_2026'),
                'corrected_by' => array('pepal_b_timetable_correction_2022'),
                'link_label' => 'ΦΕΚ Β΄ 4578/2022 — Β΄ Π.ΕΠΑ.Λ. ↗',
            ),
            'pepal_b_timetable_correction_2022' => array(
                'title' => 'Τροποποίηση/διόρθωση ωρολογίου Β΄ Π.ΕΠΑ.Λ.',
                'citation_title' => 'Διόρθωση Β΄ Π.ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ9/112468/Δ4/16-09-2022',
                'fek' => 'ΦΕΚ Β΄ 4961/22-09-2022',
                'date' => '2022-09-16',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2022_09_16_EXE_112468_tropop_YA_OPS_B_taxes_P_EPAL_n4763_2020_PHEK_4961B_22.09.2022.pdf',
                'valid_from' => '2022-2023',
                'school_types' => array('pepal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'model_vocational_school', 'correction'),
                'corrects' => array('pepal_b_timetable_2022'),
                'link_label' => 'ΦΕΚ Β΄ 4961/2022 — Διόρθωση Β΄ Π.ΕΠΑ.Λ. ↗',
            ),
            'pepal_a_timetable_2026' => array(
                'title' => 'Τροποποίηση ωρολογίου Γενικής Παιδείας Α΄ Π.ΕΠΑ.Λ.',
                'citation_title' => 'Α΄ Π.ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ9/44281/Δ4',
                'fek' => 'ΦΕΚ Β΄ 2136/09-04-2026',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2026_04_08_EXE_44286_tropop_YA_OPS_mathema_ETHIKE_B_PEPAL_PHEK_2136B_09.04.2026.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('pepal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'model_vocational_school', 'ethics'),
                'amends' => array('pepal_a_timetable_2021'),
            ),
            'pepal_b_timetable_2026' => array(
                'title' => 'Τροποποίηση ωρολογίου Γενικής Παιδείας Β΄ Π.ΕΠΑ.Λ.',
                'citation_title' => 'Β΄ Π.ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ9/44286/Δ4',
                'fek' => 'ΦΕΚ Β΄ 2136/09-04-2026',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2026_04_08_EXE_44286_tropop_YA_OPS_mathema_ETHIKE_B_PEPAL_PHEK_2136B_09.04.2026.pdf',
                'valid_from' => '2026-2027',
                'school_types' => array('pepal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'model_vocational_school', 'ethics'),
                'amends' => array('pepal_b_timetable_2022'),
            ),
            'pepal_g_timetable_2023' => array(
                'title' => 'Ωρολόγιο Γ΄ τάξης Πρότυπων Επαγγελματικών Λυκείων',
                'citation_title' => 'Γ΄ Π.ΕΠΑ.Λ.',
                'decision' => 'Υ.Α. Φ9/93929/Δ4/28-08-2023',
                'fek' => 'ΦΕΚ Β΄ 5251/30-08-2023',
                'date' => '2023-08-28',
                'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/2023_08_28_EXE_93929_YA_OPS_G_taxes_P_EPAL_n4763_2020_PHEK_5251B_30.08.2023.pdf',
                'alternate_urls' => array(
                    'assignments_card' => 'https://www.minedu.gov.gr/2023_08_28_%CE%95%CE%9E%CE%95_93929_%CE%A5%CE%91_%CE%A9%CE%A0%CE%A3_%CE%93_%CF%84%CE%AC%CE%BE%CE%B7%CF%82_%CE%A0_%CE%95%CE%A0%CE%91%CE%9B_%CE%BD4763_2020_%CE%A6%CE%95%CE%9A_5251%CE%92_30.08.2023.pdf',
                ),
                'valid_from' => '2023-2024',
                'school_types' => array('pepal'),
                'topics' => array('weekly_timetable', 'vocational_education', 'model_vocational_school'),
                'link_label' => 'ΦΕΚ Β΄ 5251/2023 — Γ΄ Π.ΕΠΑ.Λ. ↗',
            ),

            'gymnasio_gel_assignments_2026' => array(
                'title' => 'Αναθέσεις μαθημάτων Γυμνασίου / ΓΕΛ',
                'citation_title' => 'Αναθέσεις Γυμνασίου / ΓΕΛ',
                'decision' => 'Υ.Α. 54058/Δ2/05-05-2026',
                'fek' => 'ΦΕΚ Β΄ 2583/07-05-2026',
                'date' => '2026-05-05',
                'url' => 'https://www.minedu.gov.gr/protovathmia-defterovathmia/dioikitika-themata-geniko-lykeio',
                'valid_from' => '2026-2027',
                'school_types' => array('gymnasio', 'esperino_gymnasio', 'gel', 'esperino_gel'),
                'topics' => array('teaching_assignments', 'general_education'),
                'amended_by' => array('gymnasio_gel_assignments_2026_5555'),
                // Optional portable label. Consumers may ignore it and build
                // their own presentation from the structured fields above.
                'link_label' => 'ΥΠΑΙΘΑ — Αναθέσεις Γυμνασίου / ΓΕΛ ↗',
            ),
            'gymnasio_gel_assignments_2026_5555' => array(
                'title' => 'Τροποποίηση αναθέσεων Γυμνασίου / ΓΕΛ',
                'citation_title' => 'Τροποποίηση αναθέσεων Γυμνασίου / ΓΕΛ',
                'decision' => 'Υ.Α. 112867/Δ2/31-08-2026',
                'fek' => 'ΦΕΚ Β΄ 5555/11-09-2026',
                'date' => '2026-08-31',
                'url' => 'https://www.et.gr/api/DownloadFekPdf?fek_pdf=2026/B/5555',
                'valid_from' => '2026-2027',
                'school_types' => array('gymnasio', 'esperino_gymnasio', 'gel', 'esperino_gel'),
                'topics' => array('teaching_assignments', 'general_education'),
                'amends' => array('gymnasio_gel_assignments_2026'),
                'link_label' => 'Υ.Α. 112867/Δ2/31-08-2026 — ΦΕΚ Β΄ 5555/11-09-2026 · Τροποποίηση αναθέσεων Γυμνασίου / ΓΕΛ ↗',
            ),
        );

        return $registry;
    }
}

if (!function_exists('legalSourcesRequiredFields')) {
    function legalSourcesRequiredFields()
    {
        return array('title', 'fek', 'url', 'valid_from');
    }
}

if (!function_exists('legalSourceByKey')) {
    function legalSourceByKey($key)
    {
        $registry = legalSourcesRegistry();
        $key = trim((string) $key);
        return ($key !== '' && isset($registry[$key])) ? $registry[$key] : null;
    }
}

if (!function_exists('legalSourcesForKeys')) {
    /**
     * Return normalized registry entries for the requested source keys.
     * Unknown/empty keys are ignored; first occurrence order is preserved.
     */
    function legalSourcesForKeys($keys)
    {
        if (!is_array($keys)) {
            $keys = array($keys);
        }

        $registry = legalSourcesRegistry();
        $result = array();
        $seen = array();

        foreach ($keys as $key) {
            $key = trim((string) $key);
            if ($key === '' || isset($seen[$key]) || !isset($registry[$key])) {
                continue;
            }
            $seen[$key] = true;
            $result[$key] = $registry[$key];
        }

        return $result;
    }
}

if (!function_exists('legalSourceUrl')) {
    /**
     * Resolve the canonical URL or a named alternate reference for one source.
     * This keeps multiple trustworthy mirrors attached to the same legal act
     * without duplicating the act itself in the registry.
     */
    function legalSourceUrl($sourceOrKey, $variant = null)
    {
        $source = is_array($sourceOrKey) ? $sourceOrKey : legalSourceByKey($sourceOrKey);
        if (!$source) {
            return null;
        }

        $variant = trim((string) $variant);
        if ($variant !== '' && isset($source['alternate_urls']) && is_array($source['alternate_urls'])
            && !empty($source['alternate_urls'][$variant])) {
            return $source['alternate_urls'][$variant];
        }

        return !empty($source['url']) ? $source['url'] : null;
    }
}

if (!function_exists('legalSourceCompactFek')) {
    /**
     * Compact a FEK citation from e.g. "ΦΕΚ Β΄ 2132/09-04-2026" to
     * "ΦΕΚ Β΄ 2132/2026" without changing the stored canonical citation.
     */
    function legalSourceCompactFek($fek)
    {
        $fek = trim((string) $fek);
        if (preg_match('/^(.*\\/)\\d{2}-\\d{2}-(\\d{4})$/u', $fek, $matches)) {
            return $matches[1] . $matches[2];
        }
        return $fek;
    }
}

if (!function_exists('legalSourceCompactDecision')) {
    /**
     * Compact a dated decision citation from e.g.
     * "Υ.Α. 118380/Θ2/21-09-2021" to "Υ.Α. 118380/Θ2/2021".
     * The canonical stored decision remains unchanged.
     */
    function legalSourceCompactDecision($decision)
    {
        $decision = trim((string) $decision);
        if (preg_match('/^(.*\/)\d{2}-\d{2}-(\d{4})$/u', $decision, $matches)) {
            return $matches[1] . $matches[2];
        }
        return $decision;
    }
}

if (!function_exists('legalSourceLinksForKeys')) {
    /**
     * Flatten registered sources/amendments to portable link metadata.
     * No HTML is generated here. This is safe for PHP pages, JSON/API output
     * and future mobile consumers.
     */
    function legalSourceLinksForKeys($keys)
    {
        $links = array();

        foreach (legalSourcesForKeys($keys) as $key => $entry) {
            if (!empty($entry['url'])) {
                $links[] = array(
                    'source_key' => $key,
                    'relation' => 'base',
                    'url' => $entry['url'],
                    'label' => !empty($entry['link_label']) ? $entry['link_label'] : $entry['title'],
                );
            }

            $amendments = isset($entry['amendments']) && is_array($entry['amendments'])
                ? $entry['amendments']
                : array();
            foreach ($amendments as $amendment) {
                if (empty($amendment['url'])) {
                    continue;
                }
                $links[] = array(
                    'source_key' => $key,
                    'relation' => 'amendment',
                    'url' => $amendment['url'],
                    'label' => !empty($amendment['link_label'])
                        ? $amendment['link_label']
                        : (isset($amendment['fek']) ? $amendment['fek'] : $entry['title']),
                );
            }
        }

        return $links;
    }
}
