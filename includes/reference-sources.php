<?php
/**
 * Canonical registry for official reference/index resources.
 *
 * DATA ONLY: these are useful official pages, framework indexes, consolidated
 * documents and registries that support the tools but are not themselves a
 * single legal/regulatory act. Keep them separate from legal-sources.php so a
 * future API/mobile client can distinguish legal_sources from
 * reference_sources without parsing presentation HTML.
 */

if (!function_exists('referenceSourcesRegistry')) {
    function referenceSourcesRegistry()
    {
        static $registry = null;
        if ($registry !== null) {
            return $registry;
        }

        $registry = array(
            'pes_staff_official_page' => array(
                'title' => 'Θέματα εκπαιδευτικού προσωπικού Προτύπων Εκκλησιαστικών Σχολείων',
                'publisher' => 'ΥΠΑΙΘΑ',
                'kind' => 'official_topic_page',
                'url' => 'https://religiousaffairs.minedu.gov.gr/el/allcategories-el-gr/epiloges-kategorias-el-gr/organotiki-domi-diefthynseis/dieythynsi-thriskeftikis-ekpaidefsis-diathriskeftikon-sxeseon/tmima-ekklisiastikis-ekpaidefsis-thriskeftikis-agogis/protypa-ekklisiastika-sxoleia/themata-ekpaideftikoy-prosopikoy-protypa-ekklisiastika-sxoleia',
                'link_label' => 'ΥΠΑΙΘΑ — Θέματα εκπαιδευτικού προσωπικού Π.Ε.Σ. ↗',
                'topics' => array('teaching_assignments', 'staffing', 'ecclesiastical_education'),
                'school_types' => array('protypo_ekklisiastiko_gymnasio', 'protypo_ekklisiastiko_lykeio'),
            ),
            'eae_assignments_official_index' => array(
                'title' => 'Αναθέσεις μαθημάτων Ειδικής και Ενταξιακής Εκπαίδευσης',
                'publisher' => 'ΥΠΑΙΘΑ',
                'kind' => 'official_index_page',
                'url' => 'https://www.minedu.gov.gr/protovathmia-defterovathmia/anatheseis-mathimaton---eidiki-kai-entaksiaki-ekpaidefsi',
                'link_label' => 'ΥΠΑΙΘΑ — Αναθέσεις Ειδικής & Ενταξιακής Εκπαίδευσης ↗',
                'topics' => array('teaching_assignments', 'special_education'),
            ),
            'epal_assignments_official_framework' => array(
                'title' => 'Θεσμικό πλαίσιο λειτουργίας ΕΠΑ.Λ. — Αναθέσεις μαθημάτων',
                'publisher' => 'ΥΠΑΙΘΑ',
                'kind' => 'official_framework_page',
                'url' => 'https://www.minedu.gov.gr/panelladikes-eksetaseis-pistopoiitika/gel-mixanografiko?catid=1524&id=35699%3Athesmiko-plaisio-leitourgias-epal-sp-299&view=article',
                'link_label' => 'ΥΠΑΙΘΑ — Θεσμικό πλαίσιο ΕΠΑ.Λ. / Αναθέσεις ↗',
                'topics' => array('teaching_assignments', 'vocational_education'),
                'school_types' => array('epal', 'esperino_epal'),
            ),
            'pepal_assignments_official_framework_2025' => array(
                'title' => 'Ισχύον θεσμικό πλαίσιο Π.ΕΠΑ.Λ.',
                'publisher' => 'ΥΠΑΙΘΑ',
                'kind' => 'official_framework_document',
                'url' => 'https://www.minedu.gov.gr/publications/docs2025/epal/%CE%99%CE%A3%CE%A7%CE%A5%CE%9F%CE%9D_%CE%98%CE%95%CE%A3%CE%9C%CE%99%CE%9A%CE%9F_%CE%A0%CE%9B%CE%91%CE%99%CE%A3%CE%99%CE%9F_%CE%95%CE%A0%CE%91%CE%9B_12-02-2025.pdf',
                'link_label' => 'ΥΠΑΙΘΑ — Θεσμικό πλαίσιο Π.ΕΠΑ.Λ. / ΦΕΚ 4367, 5188, 7403 ↗',
                'topics' => array('teaching_assignments', 'vocational_education', 'model_vocational_school'),
                'school_types' => array('pepal'),
            ),
            'art_schools_official_page' => array(
                'title' => 'Καλλιτεχνικά Σχολεία',
                'publisher' => 'ΥΠΑΙΘΑ',
                'kind' => 'official_topic_page',
                'url' => 'https://www.minedu.gov.gr/a-v-vathmia-ekpaidefsi-mob/defterovathmia-2/kallitexnika',
                'link_label' => 'ΥΠΑΙΘΑ — Καλλιτεχνικά Σχολεία ↗',
                'topics' => array('teaching_assignments', 'timetable', 'art_schools'),
                'school_types' => array('kallitexniko_gymnasio', 'kallitexniko_gel'),
            ),
            'pes_regulatory_framework_official_page' => array(
                'title' => 'Νομοθετικό πλαίσιο Προτύπων Εκκλησιαστικών Σχολείων',
                'publisher' => 'ΥΠΑΙΘΑ',
                'kind' => 'official_framework_page',
                'url' => 'https://religiousaffairs.minedu.gov.gr/en/directorates/directorate-for-religious-education-and-interfaith-relations/department-for-ecclesiastical-and-religious-education/model-ecclesiastical-schools/regulatory-framework',
                'link_label' => 'ΥΠΑΙΘΑ — Νομοθετικό πλαίσιο Προτύπων Εκκλησιαστικών Σχολείων ↗',
                'topics' => array('timetable', 'ecclesiastical_education'),
                'school_types' => array('protypo_ekklisiastiko_gymnasio', 'protypo_ekklisiastiko_lykeio'),
            ),
            'vocational_grade_c_timetables_official_index' => array(
                'title' => 'Ισχύοντα ωρολόγια Γ΄ τάξης Επαγγελματικής Εκπαίδευσης',
                'publisher' => 'ΥΠΑΙΘΑ',
                'kind' => 'official_index_page',
                'url' => 'https://www.minedu.gov.gr/protovathmia-defterovathmia/orologio-programma-g-taksi---epaggelmatiki-ekpaidefsi',
                'link_label' => 'ΥΠΑΙΘΑ — Ισχύοντα ωρολόγια Γ΄ τάξης Επαγγελματικής Εκπαίδευσης ↗',
                'topics' => array('timetable', 'vocational_education'),
                'school_types' => array('epal', 'esperino_epal', 'pepal'),
            ),
            'school_units_registry_psd' => array(
                'title' => 'Μητρώο σχολικών μονάδων / κωδικοί Υπουργείου',
                'publisher' => 'Πανελλήνιο Σχολικό Δίκτυο',
                'kind' => 'official_registry',
                'url' => 'https://www.sch.gr/sites/sch-units/',
                'link_label' => 'Πανελλήνιο Σχολικό Δίκτυο — μητρώο σχολικών μονάδων / κωδικοί Υπουργείου ↗',
                'topics' => array('school_registry', 'staffing'),
            ),
        );

        return $registry;
    }
}

if (!function_exists('referenceSourcesRequiredFields')) {
    function referenceSourcesRequiredFields()
    {
        return array('title', 'publisher', 'kind', 'url', 'link_label');
    }
}

if (!function_exists('referenceSourceKinds')) {
    function referenceSourceKinds()
    {
        return array(
            'official_topic_page',
            'official_index_page',
            'official_framework_page',
            'official_framework_document',
            'official_registry',
        );
    }
}

if (!function_exists('referenceSourceByKey')) {
    function referenceSourceByKey($key)
    {
        $registry = referenceSourcesRegistry();
        $key = trim((string) $key);
        return ($key !== '' && isset($registry[$key])) ? $registry[$key] : null;
    }
}

if (!function_exists('referenceSourcesForKeys')) {
    function referenceSourcesForKeys($keys)
    {
        if (!is_array($keys)) {
            $keys = array($keys);
        }

        $registry = referenceSourcesRegistry();
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

if (!function_exists('referenceSourceLinkByKey')) {
    /** Return portable link metadata only; no HTML is generated here. */
    function referenceSourceLinkByKey($key)
    {
        $source = referenceSourceByKey($key);
        if (!$source || empty($source['url']) || empty($source['link_label'])) {
            return null;
        }

        return array(
            'source_key' => trim((string) $key),
            'source_type' => 'reference',
            'url' => $source['url'],
            'label' => $source['link_label'],
        );
    }
}
