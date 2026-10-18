<?php
/**
 * Shared explanatory disclosures for the transfer service/MSD input modes.
 * These are guidance panels only: no changes to calculation or legal routing.
 * Keep the existing Secondary IDs for backwards compatibility.
 */
require_once __DIR__ . '/calculator-layout.php';

if (!function_exists('transferServiceHelp')) {
    function transferServiceHelp($level, $kind) {
        $primary = ($level === 'primary');
        if ($kind === 'service') {
            calculatorDisclosure(array(
                'id' => $primary ? 'primaryServiceRegistryHelp' : 'serviceRegistryHelp',
                'summary' => 'Οδηγίες',
                'html' => '<p>Η προϋπηρεσία και οι Μ.Σ.Δ. καταχωρίζονται ξεχωριστά, όπως και στο επίσημο σύστημα. Επιλέγεται <strong>ένας</strong> τρόπος καταχώρισης για την προϋπηρεσία — οι τιμές δεν προστίθενται μεταξύ τους.</p>'
                    . '<p' . ($primary ? '' : ' id="serviceDirectHelp"') . '>Για απευθείας καταχώριση μορίων, δώστε τον ακριβή αριθμό με έως δύο δεκαδικά (κόμμα ή τελεία), π.χ. <strong>21,04</strong>.</p>'
                    . '<p>Μπορείτε να δείτε τα καταχωρισμένα <strong>μόρια συνολικής υπηρεσίας</strong> και τις υπηρετήσεις σας στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">teachers.minedu.gov.gr ↗</a> (σύνδεση με λογαριασμό ΠΣΔ).</p>'
            ));
        } elseif ($kind === 'msd') {
            calculatorDisclosure(array(
                'id' => $primary ? 'primaryMsdRegistryHelp' : 'msdRegistryHelp',
                'summary' => 'Πού βλέπω τις υπηρετήσεις μου;',
                'html' => 'Μπορείτε να δείτε τις υπηρετήσεις και τα ήδη καταχωρισμένα μόρια μετάθεσης στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">teachers.minedu.gov.gr ↗</a> (σύνδεση με λογαριασμό ΠΣΔ).'
            ));
        }
    }
}
