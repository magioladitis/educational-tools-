<?php
/**
 * Tool-level legal/source freshness registry.
 *
 * This registry records *when and what was actually reviewed*. It deliberately
 * does not infer that a tool is current merely because its code or source URLs
 * changed recently. Tools without an explicit entry remain `pending`.
 *
 * PHP 5.6/7.4-compatible syntax is intentional.
 */

if (!function_exists('legalAuditRegistry')) {
    function legalAuditRegistry()
    {
        static $registry = null;
        if ($registry !== null) return $registry;

        $registry = array(
            'anatheseis-mathimaton.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Ενεργές αναθέσεις 2026–2027 και πρόσφατες τροποποιήσεις, συμπεριλαμβανομένου του ΦΕΚ Β΄ 5940/2026 για τα Καλλιτεχνικά Σχολεία.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα ή τροποποιητική απόφαση αναθέσεων μαθημάτων.',
                'version' => '3.22.83',
            ),
            'orologio-programma-mathimaton.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Νομικές πηγές ωρολογίων 2026–2027 και cross-audit με τις ενεργές αναθέσεις· ενημερώθηκε και η αναφορά των Καλλιτεχνικών στο ΦΕΚ Β΄ 5940/2026.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέο ωρολόγιο πρόγραμμα ή αλλαγή αναθέσεων που επηρεάζει το cross-audit.',
                'version' => '3.22.83',
            ),
            'ypologismos-didaktikon-anagkon.php' => array(
                'status' => 'partial',
                'last_verified' => '2026-10-07',
                'scope' => 'Εξαρτήσεις από τα canonical datasets ωρολογίων/αναθέσεων και τα αντίστοιχα regression/cross-audit contracts.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Αλλαγή σε ωρολόγιο, αναθέσεις, υποχρεωτικό ωράριο ή κανόνες σχηματισμού τμημάτων.',
                'version' => '3.22.83',
            ),
            'adeies-monimon.php' => array(
                'status' => 'partial',
                'last_verified' => '2026-10-07',
                'scope' => 'Στοχευμένος έλεγχος της άδειας συμμετοχής σε δίκη και πρόσφατος έλεγχος της επιστημονικής/επιμορφωτικής άδειας.',
                'review_trigger' => 'Αλλαγή Υπαλληλικού Κώδικα ή νέα επίσημη εγκύκλιος/οδηγός αδειών.',
                'version' => '3.22.85',
            ),
            'adeies-anapliroton.php' => array(
                'status' => 'partial',
                'last_verified' => '2026-10-04',
                'scope' => 'Επιστημονική/επιμορφωτική άδεια και επιλεγμένες διαφορές μόνιμων–αναπληρωτών στο πλαίσιο του audit 2021–2026.',
                'review_trigger' => 'Νέα ρύθμιση ΙΔΟΧ/αναπληρωτών ή νέα επίσημη εγκύκλιος αδειών.',
                'version' => '3.22.82',
            ),
            'adeies-sygkrisi.php' => array(
                'status' => 'partial',
                'last_verified' => '2026-10-07',
                'scope' => 'Σύγκριση ουσιαστικών διαφορών μόνιμων–αναπληρωτών, με πρόσφατες διορθώσεις σε πένθος, αιμοδοσία, επιμορφωτική άδεια και συμμετοχή σε δίκη.',
                'review_trigger' => 'Αλλαγή σε οποιοδήποτε από τα δύο καθεστώτα αδειών.',
                'version' => '3.22.85',
            ),
            'paidagogiki-eparkeia.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-01',
                'scope' => 'Νομική βάση Π.Δ.Ε., χρονικά όρια, guided preconditions και ονομαστικές διαδρομές τεκμηρίωσης που χρησιμοποιεί το εργαλείο.',
                'review_trigger' => 'Νέα προκήρυξη ΑΣΕΠ ή αλλαγή του νομοθετικού πλαισίου Π.Δ.Ε.',
                'version' => '3.22.66',
            ),
            'xronodiagramma-ypiresiakon-metavolon.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-09-30',
                'scope' => 'Χρονοδιάγραμμα 2019–2026, διάκριση Π.Ε./Δ.Ε./ΕΕΠ-ΕΒΠ και ενοποιημένες επίσημες πηγές ανά διαδικασία.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα εγκύκλιος/πρόσκληση ή νέες ημερομηνίες υπηρεσιακής μεταβολής.',
                'version' => '3.22.57',
            ),
            'ypologismos-misthologikou-klimakiou.php' => array(
                'status' => 'partial',
                'last_verified' => '2026-10-04',
                'scope' => 'Μισθολογική κατάταξη/βασικός μισθός και πρόσφατος έλεγχος οικογενειακής παροχής έναντι εξαρτώμενων τέκνων φορολογίας.',
                'review_trigger' => 'Νέα μισθολογική εγκύκλιος, φορολογική αλλαγή ή μεταβολή κρατήσεων/παροχών.',
                'version' => '3.22.77',
            ),
        );

        return $registry;
    }
}

if (!function_exists('legalAuditStatusLabel')) {
    function legalAuditStatusLabel($status)
    {
        $labels = array(
            'verified' => 'Πλήρης καταγεγραμμένος έλεγχος',
            'partial' => 'Στοχευμένος καταγεγραμμένος έλεγχος',
            'review_due' => 'Χρειάζεται επανέλεγχο',
            'pending' => 'Χωρίς καταγεγραμμένο έλεγχο στο νέο σύστημα',
        );
        return isset($labels[$status]) ? $labels[$status] : $labels['pending'];
    }
}

if (!function_exists('legalAuditFormatDate')) {
    function legalAuditFormatDate($date)
    {
        $date = trim((string) $date);
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
            return $m[3] . '/' . $m[2] . '/' . $m[1];
        }
        return $date;
    }
}

if (!function_exists('legalAuditCurrentPage')) {
    function legalAuditCurrentPage()
    {
        $script = isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '';
        if ($script === '' && isset($_SERVER['PHP_SELF'])) $script = (string) $_SERVER['PHP_SELF'];
        return basename($script);
    }
}

if (!function_exists('legalAuditForPage')) {
    function legalAuditForPage($page)
    {
        $page = basename(trim((string) $page));
        $registry = legalAuditRegistry();
        if ($page !== '' && isset($registry[$page])) {
            $entry = $registry[$page];
            $entry['page'] = $page;
            if (empty($entry['status'])) $entry['status'] = 'pending';
            return $entry;
        }
        return array(
            'page' => $page,
            'status' => 'pending',
            'last_verified' => '',
            'scope' => '',
            'review_trigger' => '',
            'version' => '',
        );
    }
}

if (!function_exists('legalAuditIsPubliclyVisible')) {
    function legalAuditIsPubliclyVisible($entry)
    {
        if (!is_array($entry) || empty($entry['status'])) return false;
        return in_array($entry['status'], array('verified', 'partial', 'review_due'), true);
    }
}

if (!function_exists('legalAuditExtraPages')) {
    function legalAuditExtraPages()
    {
        return array(
            'adeies-monimon.php' => 'Άδειες μόνιμων εκπαιδευτικών',
            'adeies-anapliroton.php' => 'Άδειες αναπληρωτών / ΙΔΟΧ',
            'adeies-sygkrisi.php' => 'Σύγκριση αδειών μόνιμων ↔ αναπληρωτών',
        );
    }
}

if (!function_exists('legalAuditCatalogueRows')) {
    /**
     * Merge the public tools catalogue with sub-pages that carry their own
     * legal logic. Missing entries are intentionally represented as pending.
     */
    function legalAuditCatalogueRows()
    {
        $rows = array();
        $seen = array();
        $cataloguePath = __DIR__ . '/tools-catalog.php';
        if (is_file($cataloguePath)) {
            $catalogue = require $cataloguePath;
            if (isset($catalogue['tools']) && is_array($catalogue['tools'])) {
                foreach ($catalogue['tools'] as $tool) {
                    if (!is_array($tool) || empty($tool['href'])) continue;
                    $page = basename((string) parse_url((string) $tool['href'], PHP_URL_PATH));
                    if ($page === '' || isset($seen[$page])) continue;
                    $seen[$page] = true;
                    $audit = legalAuditForPage($page);
                    $rows[] = array(
                        'page' => $page,
                        'title' => !empty($tool['title']) ? (string) $tool['title'] : $page,
                        'href' => (string) $tool['href'],
                        'audit' => $audit,
                    );
                }
            }
        }

        foreach (legalAuditExtraPages() as $page => $title) {
            if (isset($seen[$page])) continue;
            $seen[$page] = true;
            $rows[] = array(
                'page' => $page,
                'title' => $title,
                'href' => $page,
                'audit' => legalAuditForPage($page),
            );
        }

        return $rows;
    }
}

if (!function_exists('legalAuditSummaryCounts')) {
    function legalAuditSummaryCounts()
    {
        $counts = array('verified' => 0, 'partial' => 0, 'review_due' => 0, 'pending' => 0, 'total' => 0);
        foreach (legalAuditCatalogueRows() as $row) {
            $status = isset($row['audit']['status']) ? $row['audit']['status'] : 'pending';
            if (!isset($counts[$status])) $status = 'pending';
            $counts[$status]++;
            $counts['total']++;
        }
        return $counts;
    }
}
