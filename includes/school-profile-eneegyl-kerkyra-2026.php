<?php
/**
 * School profile — ΕΝ.Ε.Ε.ΓΥ.-Λ. Κέρκυρας, σχολικό έτος 2026-2027.
 *
 * Πηγή δομικής αποτύπωσης: snapshot «Κενά Μαθημάτων» myschool που
 * παρασχέθηκε από τον χρήστη (05-09-2026 00:59).
 * Οι τιμές «Εκτίμηση myschool» χρησιμοποιούνται μόνο ως τεκμήριο για την
 * ενεργή δομή/πλήθος curriculum groups. ΔΕΝ χαρακτηρίζονται ως πραγματικές
 * ώρες στελέχωσης ή τελικά λειτουργικά κενά.
 */

function schoolProfileEneegylKerkyra2026()
{
    return array(
        'profile_id' => 'eneegyl-kerkyra-2411001-2026-2027',
        'school_year' => '2026-2027',
        'school' => array(
            'name' => 'ΕΝΙΑΙΟ ΕΙΔΙΚΟ ΕΠΑΓΓΕΛΜΑΤΙΚΟ ΓΥΜΝΑΣΙΟ-ΛΥΚΕΙΟ ΚΕΡΚΥΡΑΣ',
            'unit_code' => '2411001',
            'status' => 'active',
            'type' => 'Ενιαίο Ειδικό Επαγγελματικό Γυμνάσιο - Λύκειο',
        ),
        'source' => array(
            'kind' => 'myschool_vacancy_estimate_snapshot',
            'captured_at' => '2026-09-05T00:59:00+03:00',
            'provided_by_user' => true,
            'values_are_actual_staffing_hours' => false,
            'values_used_for_structural_inference' => true,
        ),
        'structures' => array(
            'eneegyl_gymnasio' => array(
                // Ενδεικτικά: Α΄ Θρησκευτικά 4 = 2 τμήματα × 2 ώρες,
                // Γ΄ Μαθηματικά 6 = 2 τμήματα × 3 ώρες, Δ΄ Χημεία 1 = 1 τμήμα.
                'general_sections' => array('Α΄' => 2, 'Β΄' => 2, 'Γ΄' => 2, 'Δ΄' => 1),
            ),
            'eneegyl_lykeio' => array(
                // Το κοινό πρόγραμμα Β΄/Γ΄/Δ΄ εμφανίζεται ως ένα curriculum
                // group, παρότι λειτουργούν παράλληλα διαφορετικοί τομείς/
                // ειδικότητες στα επαγγελματικά μαθήματα.
                'general_sections' => array('Α΄' => 2, 'Β΄' => 1, 'Γ΄' => 1, 'Δ΄' => 1),
                'track_sections' => array(
                    'Β΄' => array('agriculture' => 1, 'admin' => 1),
                    'Γ΄' => array('agriculture' => 1, 'admin' => 1),
                ),
                'specialty_sections' => array(
                    'Δ΄' => array(
                        'agriculture' => array('plant' => 1),
                        'admin' => array('tourism' => 1),
                    ),
                ),
                'choice_sections' => array(
                    'Α΄' => array(
                        'eneegyl.lykeio.a.choices' => array(
                            'eneegyl.lykeio.a.choice.oikonomia' => 2,
                            'eneegyl.lykeio.a.choice.synthesi' => 2,
                            'eneegyl.lykeio.a.choice.geoponia' => 2,
                        ),
                    ),
                ),
                'variants' => array(),
                'conditions' => array(),
            ),
        ),
        'ethics' => array(
            'formation_policy_scope' => 'scope_not_confirmed',
            'exempt_students_by_structure_grade' => array(),
            'note' => 'Δεν εφαρμόζεται αυτομάτως στο ΕΝ.Ε.Ε.ΓΥ.-Λ. το όριο των 10 της Υ.Α. 108070/Δ2/2026, επειδή η απόφαση αφορά ρητά Γυμνάσιο και Γενικό Λύκειο.',
        ),
    );
}
