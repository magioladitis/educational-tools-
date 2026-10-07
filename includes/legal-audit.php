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
                'review_after' => '2027-05-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.83',
            ),
            'orologio-programma-mathimaton.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Νομικές πηγές ωρολογίων 2026–2027 και cross-audit με τις ενεργές αναθέσεις· ενημερώθηκε και η αναφορά των Καλλιτεχνικών στο ΦΕΚ Β΄ 5940/2026.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέο ωρολόγιο πρόγραμμα ή αλλαγή αναθέσεων που επηρεάζει το cross-audit.',
                'review_after' => '2027-05-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.83',
            ),
            'ypologismos-didaktikon-anagkon.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος των κανονιστικών εξαρτήσεων του simulator: canonical ωρολόγια και αναθέσεις 2026–2027, λειτουργία Γυμνασίων/ΓΕΛ, κανόνες σχηματισμού τμημάτων και ειδικός κανόνας Ηθικής, με cross-audit των αντίστοιχων datasets.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Αλλαγή σε ωρολόγιο, αναθέσεις, υποχρεωτικό ωράριο, οδηγίες λειτουργίας σχολείων ή κανόνες σχηματισμού τμημάτων.',
                'review_after' => '2027-05-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.89',
            ),
            'adeies-monimon.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος του structured οδηγού έναντι του συγκεντρωτικού πίνακα ΥΠΑΙΘΑ και των μεταγενέστερων τροποποιήσεων/επίσημων οδηγιών έως 07/10/2026, συμπεριλαμβανομένων Μίτος, ΥΠΕΣ και Ν. 5270/2026.',
                'review_trigger' => 'Αλλαγή Υπαλληλικού Κώδικα, Ν. 1566/1985 ή νέα επίσημη εγκύκλιος/διαδικασία για άδειες δημοσίων υπαλλήλων/εκπαιδευτικών.',
                'version' => '3.22.89',
            ),
            'adeies-anapliroton.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος του structured οδηγού ΙΔΟΧ/αναπληρωτών έναντι του πίνακα ΥΠΑΙΘΑ, Ν. 4808/2021, Π.Δ. 62/2025, εγκυκλίων ΥΠΕΣ/Υπουργείου Εργασίας και τρεχουσών επίσημων οδηγιών έως 07/10/2026.',
                'review_trigger' => 'Νέα ρύθμιση Εργατικού Κώδικα/ΙΔΟΧ, ειδική ρύθμιση αναπληρωτών ή νέα επίσημη εγκύκλιος αδειών.',
                'version' => '3.22.89',
            ),
            'adeies-sygkrisi.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης cross-audit της σύγκρισης πάνω στα δύο επαληθευμένα datasets μονίμων και αναπληρωτών, με έλεγχο διάρκειας, αποδοχών, προϋποθέσεων και πραγματικών διαφορών πεδίου δικαιώματος.',
                'review_trigger' => 'Αλλαγή σε οποιοδήποτε από τα δύο καθεστώτα αδειών ή στη comparison schema των structured datasets.',
                'version' => '3.22.89',
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
                'review_after' => '2026-10-20',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.57',
            ),
            'ypologismos-didaktikou-orariou.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Κλίμακες υποχρεωτικού διδακτικού/υποστηρικτικού ωραρίου Π.Ε./Δ.Ε., θέσεις ευθύνης, ΕΕΠ/ΕΒΠ και ειδικές ρυθμίσεις εργαστηρίων/σχολικών βιβλιοθηκών.',
                'review_trigger' => 'Νέα νομοθετική ρύθμιση ή εγκύκλιος για το υποχρεωτικό ωράριο ή τις ειδικές μειώσεις.',
                'version' => '3.22.87',
            ),
            'ypologismos-morion-metathesis.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Κριτήρια και μοριοδότηση μεταθέσεων Δ.Ε. βάσει της εγκυκλίου 129787/Ε2/15-10-2025, όπως εφαρμόστηκε στον κύκλο μεταθέσεων 2025–2026.',
                'school_year' => '2025-2026',
                'review_trigger' => 'Έκδοση νέας ετήσιας εγκυκλίου μεταθέσεων 2026–2027 ή τροποποίηση του π.δ. 50/1996/συναφών κανόνων.',
                'review_after' => '2026-10-20',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.87',
            ),
            'ypologismos-morion-apospasis.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Κριτήρια, μόρια, κατά προτεραιότητα κατηγορίες και βασικά κωλύματα αποσπάσεων ΠΥΣΠΕ/ΠΥΣΔΕ 2026–2027 βάσει 41297/Ε2/02-04-2026.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα ετήσια εγκύκλιος αποσπάσεων ή τροποποίηση των κριτηρίων/κατά προτεραιότητα κατηγοριών.',
                'review_after' => '2027-03-20',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.87',
            ),
            'ypologismos-morion-topothetisis-neodioriston.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Προσωρινή τοποθέτηση νεοδιόριστων Π.Ε./Δ.Ε.: βασικό άθροισμα οικογενειακών λόγων, συνυπηρέτησης, εντοπιότητας, ισοβαθμίες και υποχρέωση παραμονής.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Ενημέρωση ΜΙΤΟΣ ή αλλαγή στα π.δ. 154/1996, 144/1997, 50/1996 ή στη διετή υποχρέωση παραμονής.',
                'review_after' => '2027-07-15',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.87',
            ),
            'ypologismos-morion.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Κριτήρια κατάταξης 1ΓΕ/2026 και 2ΓΕ/2026: ακαδημαϊκά, προϋπηρεσία, κοινωνικά κριτήρια, Π.Δ.Ε. και ειδικοί περιορισμοί. Στις ιστορικές τρίμηνες συμβάσεις αποτυπώνονται και τα μέγιστα πραγματικά δυνατής υπηρεσίας 8/7 πλήρων μηνών για 2020–2021/2021–2022.',
                'school_year' => '1ΓΕ/2026 · 2ΓΕ/2026',
                'review_trigger' => 'Τροποποίηση/διόρθωση των προκηρύξεων ή νέα προκήρυξη Γενικής Εκπαίδευσης.',
                'version' => '3.22.88',
            ),
            'dikaioma-symmetoxis.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Παράρτημα Α΄ 1ΓΕ/2026 και 2ΓΕ/2026: γενικά προσόντα συμμετοχής/διορισμού, ηλικία, κωλύματα και περιπτώσεις δικαιώματος αίτησης με κώλυμα ανάληψης.',
                'school_year' => '1ΓΕ/2026 · 2ΓΕ/2026',
                'review_trigger' => 'Διόρθωση/τροποποίηση των προκηρύξεων ή αλλαγή γενικών προσόντων/κωλυμάτων διορισμού.',
                'version' => '3.22.87',
            ),
            'ypologismos-misthologikou-klimakiou.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος Μ.Κ., προώθησης τίτλων, βασικών μισθών από 01/04/2026, οικογενειακής παροχής/θέσης, φορολογίας 2026 και τυπικών ασφαλιστικών βάσεων/κρατήσεων που υπολογίζει το εργαλείο· ελέγχθηκαν και οι νεότερες μισθολογικές εγκύκλιοι Ν. 5313/2026 ως προς τη συνάφεια με το γενικό προφίλ εκπαιδευτικού.',
                'review_trigger' => 'Νέα αναπροσαρμογή βασικών μισθών, μισθολογική εγκύκλιος γενικής εφαρμογής, φορολογική αλλαγή ή μεταβολή ασφαλιστικών εισφορών/παροχών.',
                'version' => '3.22.89',
            ),
            'ypologismos-morion-1ea-2025.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος 1ΕΑ/2025 (ΕΒΠ ΔΕ01): τυπικά προσόντα, ακαδημαϊκή μοριοδότηση 64/96, προϋπηρεσία, κοινωνικά κριτήρια, ειδική προτεραιότητα ΕΝΓ και ιστορικά τρίμηνων συμβάσεων. Επιβεβαιώθηκε και η έκδοση τελικών πινάκων 29/04/2026.',
                'school_year' => '1ΕΑ/2025',
                'review_trigger' => 'Διόρθωση/τροποποίηση 1ΕΑ/2025, νέα προκήρυξη ΕΒΠ ή αλλαγή των κριτηρίων του άρθρου 60 ν. 4589/2019.',
                'version' => '3.22.90',
            ),
            'ypologismos-morion-2ea-2025.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος 2ΕΑ/2025 (ΕΕΠ): τυπικά/επαγγελματικά προσόντα, πρόταξη ΠΔΕ και ΠΕ23, ακαδημαϊκά έως 120, προϋπηρεσία και κοινωνικά κριτήρια. Ελέγχθηκαν το αρχικό ΦΕΚ 21/2025, το συμπληρωματικό ΦΕΚ 24/2025 και οι τελικοί πίνακες 02/06/2026.',
                'school_year' => '2ΕΑ/2025',
                'review_trigger' => 'Διόρθωση/τροποποίηση 2ΕΑ/2025, νέα προκήρυξη ΕΕΠ ή μεταβολή επαγγελματικών/τυπικών προσόντων κλάδων ΠΕ21–ΠΕ31.',
                'version' => '3.22.90',
            ),
            'ypologismos-morion-3ea-2025.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος 3ΕΑ/2025 (εκπαιδευτικοί ΕΑΕ ΠΕ): κύριος/επικουρικός πίνακας, ειδικά κριτήρια ΕΑΕ, ακαδημαϊκά, προϋπηρεσία, κοινωνικά, ΠΔΕ, ΕΝΓ/Braille και ειδικές πρόταξεις. Ελέγχθηκαν ΦΕΚ 22/2025 και 25/2025 και οι τελικοί πίνακες 30/06/2026.',
                'school_year' => '3ΕΑ/2025',
                'review_trigger' => 'Διόρθωση/τροποποίηση 3ΕΑ/2025, νέα προκήρυξη ΕΑΕ ΠΕ ή μεταβολή κριτηρίων ένταξης/μοριοδότησης ΕΑΕ.',
                'version' => '3.22.90',
            ),
            'ypologismos-morion-4ea-2025.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος 4ΕΑ/2025 (ΕΑΕ ΤΕ01/ΤΕ02/ΤΕ16): κύριος/επικουρικός πίνακας, ακαδημαϊκά έως 120, προϋπηρεσία, κοινωνικά, ΠΔΕ και ειδικές πρόταξεις. Επιβεβαιώθηκε και η έκδοση τελικών πινάκων 29/04/2026.',
                'school_year' => '4ΕΑ/2025',
                'review_trigger' => 'Διόρθωση/τροποποίηση 4ΕΑ/2025, νέα προκήρυξη ΕΑΕ ΤΕ ή μεταβολή κριτηρίων/ειδικοτήτων ΤΕ.',
                'version' => '3.22.90',
            ),
            'ypologismos-morion-5ea-2022.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Ιστορικός πλήρης έλεγχος 5ΕΑ/2022 (ΕΑΕ ΔΕ): 7 ειδικότητες, τριετής επαγγελματική πείρα ως τυπικό προσόν, κύριος/επικουρικός πίνακας, ακαδημαϊκά/προϋπηρεσία/κοινωνικά και τελικοί πίνακες 22/06/2023. Έως 07/10/2026 δεν εντοπίστηκε αντίστοιχη 5ΕΑ/2025 στην επίσημη σειρά ΑΣΕΠ.',
                'school_year' => '5ΕΑ/2022 (ιστορικό)',
                'review_trigger' => 'Νέα προκήρυξη ΕΑΕ κατηγορίας ΔΕ ή επίσημη τροποποίηση/αναμόρφωση των ιστορικών πινάκων 5ΕΑ/2022.',
                'version' => '3.22.90',
            ),
            'ypologismos-morion-1gt-2024.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος 1ΓΤ/2024 (Γενική ΤΕ01/ΤΕ02/ΤΕ16): ακαδημαϊκά έως 120, προϋπηρεσία, κοινωνικά, ΠΔΕ και ισοβαθμίες. Ελέγχθηκαν ΦΕΚ 25/2024 και διορθωτικό/συμπληρωματικό ΦΕΚ 28/2024, καθώς και οι τελικοί πίνακες 18/06/2025.',
                'school_year' => '1ΓΤ/2024',
                'review_trigger' => 'Νέα προκήρυξη Γενικής Εκπαίδευσης κατηγορίας ΤΕ ή επίσημη διόρθωση/τροποποίηση 1ΓΤ/2024.',
                'version' => '3.22.90',
            ),
            'ypologismos-morion-apospasis-dimos.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος πρόσκλησης 28/ΔΕΔΗΜΩΣ/26-06-2026 για αποσπάσεις στα ΔΗΜ.Ω.Σ. 2026–2027, κριτηρίων 53 μονάδων και ισχυουσών παραπομπών 71071/Δ6/2025, 81473/Δ6/2025 και 9789/Δ6/2026.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα πρόσκληση αποσπάσεων ΔΗΜ.Ω.Σ. ή τροποποίηση των υπουργικών αποφάσεων λειτουργίας/επιλογής διδακτικού προσωπικού.',
                'review_after' => '2027-06-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.91',
            ),
            'ypologismos-morion-apospasis-psifiako-frontistirio.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος της πρόσκλησης 86300/Δ7/29-06-2026 για αποσπάσεις μονίμων στο Ψηφιακό Φροντιστήριο: προϋποθέσεις, Β+Γ πριν από συνέντευξη, βιντεοσκοπημένο μάθημα, βάση 20/35 και τελικό σύνολο 100.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα ετήσια πρόσκληση Ψηφιακού Φροντιστηρίου ή αλλαγή άρθρου 26Α ν. 4368/2016/κριτηρίων επιλογής.',
                'review_after' => '2027-06-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.91',
            ),
            'ypologismos-morion-anapliroti-psifiako-frontistirio.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος της πρόσκλησης 126274/Δ7/24-09-2026 για προσωρινούς αναπληρωτές στο Ψηφιακό Φροντιστήριο, συμπεριλαμβανομένων θέσεων, κριτηρίων επιλογής και ξεχωριστής μοριοδότησης πραγματικής υπηρεσίας στο Ψηφιακό Φροντιστήριο.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα/διορθωτική πρόσκληση αναπληρωτών Ψηφιακού Φροντιστηρίου ή μεταβολή των κριτηρίων επιλογής/μοριοδότησης υπηρεσίας.',
                'review_after' => '2027-09-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.91',
            ),
            'ypologismos-morion-apospasis-sde.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος αποσπάσεων ΣΔΕ 2026–2027 έναντι Υ.Α. 88422/Κ1 (Β΄ 4088/2026), Διόρθωσης Σφάλματος Β΄ 4199/2026 και πρόσκλησης 94386/Κ1/13-07-2026.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα απόφαση μοριοδότησης/λειτουργίας ΣΔΕ, διόρθωση ΦΕΚ ή νέα ετήσια πρόσκληση αποσπάσεων.',
                'review_after' => '2027-06-15',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.91',
            ),
            'ypologismos-morion-mitroo-sde.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος Μητρώου ωρομίσθιου προσωπικού ΣΔΕ βάσει Υ.Α. 75975/Κ1 (Β΄ 3224/2025) και της ενεργής πρόσκλησης Μητρώου 2025–2026, μαζί με τις ειδικές εξαιρέσεις ΠΕ06/ΠΕ86.',
                'school_year' => '2025-2026',
                'review_trigger' => 'Νέα υπουργική απόφαση διαχείρισης Μητρώου ή νέα πρόσκληση ένταξης/επικαιροποίησης που αλλάζει κριτήρια.',
                'version' => '3.22.91',
            ),
            'ypologismos-morion-diefthynton-ypodiefthynton-sde.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος κριτηρίων Διευθυντών/Υποδιευθυντών ΣΔΕ βάσει Υ.Α. 70621/Κ1/13-06-2025 (Β΄ 3037) και των σχετικών προσκλήσεων 2025 που εξακολουθούν να αποτελούν το ισχύον πλαίσιο επιλογής.',
                'review_trigger' => 'Νέα Υ.Α. κριτηρίων ή νέα πρόσκληση στελεχών ΣΔΕ με διαφορετική μοριοδότηση/προϋποθέσεις.',
                'version' => '3.22.91',
            ),
            'ypologismos-morion-sivitanidios-saek.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος μοριοδότησης εκπαιδευτών ΣΑΕΚ Σιβιτανιδείου έναντι της επίσημης πρόσκλησης 7903/21-08-2026 (ΑΔΑ ΨΞ0Ο469ΒΨ1-3ΥΚ) για 2026Β και 2027Α.',
                'school_year' => '2026Β · 2027Α',
                'review_trigger' => 'Νέα πρόσκληση Σιβιτανιδείου ή αλλαγή του πίνακα μοριοδότησης εκπαιδευτών ΣΑΕΚ.',
                'review_after' => '2027-08-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.91',
            ),
            'dikaioma-ypodiefthynti-saek.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος προϋποθέσεων της πρόσκλησης Κ5/113585/01-09-2026 για κενές θέσεις Υποδιευθυντών δημόσιων ΣΑΕΚ και περιορισμού υποβολής στη ΣΑΕΚ υπηρεσίας.',
                'school_year' => '2026',
                'review_trigger' => 'Νέα πρόσκληση Υποδιευθυντών ΣΑΕΚ ή αλλαγή του θεσμικού πλαισίου επιλογής στελεχών ΣΑΕΚ.',
                'review_after' => '2027-08-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.91',
            ),
            'ypologismos-morion-scholeio-evropaikis-paideias-irakleiou.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος πρόσκλησης 69163/Η2/28-05-2026 για το Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου: προϋποθέσεις, κατάρτιση έως 45, εμπειρία έως 25, συνέντευξη έως 30 και σύνολο 100.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα ετήσια πρόσκληση Σ.Ε.Π. Ηρακλείου ή μεταβολή των κριτηρίων/προϋποθέσεων επιλογής.',
                'review_after' => '2027-05-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.91',
            ),
            'ypologismos-morion-apospasis-evropaika-scholeia.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος πρόσκλησης 33598/Η2/18-03-2026 και Υ.Α. 26754/Η2/10-03-2022 (Β΄ 1165, διόρθωση Β΄ 1300): Α+Β έως 50, συνέντευξη έως 40, γλωσσική βάση 5/10 και τελικό έως 90.',
                'school_year' => '2026',
                'review_trigger' => 'Νέα πρόσκληση Ευρωπαϊκών Σχολείων ή τροποποίηση της Υ.Α. 26754/Η2/2022.',
                'review_after' => '2027-03-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.91',
            ),
            'posa-paravola.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος 1ΓΕ/2026–2ΓΕ/2026 ως προς αντιστοίχιση κλάδων στις δύο προκηρύξεις, αυτοτελή ηλεκτρονική αίτηση ανά προκήρυξη και e-Παράβολο 15 € με 20ψήφιο κωδικό σε κατάσταση ΠΛΗΡΩΜΕΝΟ πριν από την οριστικοποίηση.',
                'school_year' => '1ΓΕ/2026 · 2ΓΕ/2026',
                'review_trigger' => 'Νέα/διορθωτική προκήρυξη Γενικής Εκπαίδευσης ή αλλαγή ποσού/διαδικασίας e-Παραβόλου ΑΣΕΠ.',
                'version' => '3.22.92',
            ),
            'dikaiologitika-titlon-spoudon.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης cross-audit με τα Κεφάλαια/Παραρτήματα δικαιολογητικών 1ΓΕ/2026–2ΓΕ/2026: ημεδαποί τίτλοι, εκκρεμής ορκωμοσία, integrated master, κοινά Π.Μ.Σ., τίτλοι αλλοδαπής και αποδεκτές πράξεις ακαδημαϊκής/επαγγελματικής αναγνώρισης.',
                'school_year' => '1ΓΕ/2026 · 2ΓΕ/2026',
                'review_trigger' => 'Νέα προκήρυξη ΑΣΕΠ εκπαιδευτικών, αλλαγή πλαισίου ΔΟΑΤΑΠ/επαγγελματικής αναγνώρισης ή νέος κανόνας αποδεικτικών τίτλων.',
                'version' => '3.22.92',
            ),
            'ypologismos-morion-onaseia.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος μοριοδότησης αναπληρωτών ΔΗΜ.Ω.Σ. 2026–2027: ακαδημαϊκά μόρια των ενεργών πινάκων ΑΣΕΠ και προϋπηρεσία σε Πρότυπα/Πειραματικά με 1,5 μόριο ανά μήνα, έως 15 ανά σχολικό έτος, χωρίς Ιούλιο–Αύγουστο· ελέγχθηκαν και οι λειτουργικές ανάγκες 55/56 ΔΕΔΗΜΩΣ.',
                'school_year' => '2026-2027',
                'review_trigger' => 'Νέα πρόσκληση αναπληρωτών ΔΗΜ.Ω.Σ., νέοι πίνακες ΑΣΕΠ ή νέα απόφαση λειτουργικών αναγκών/μοριοδότησης.',
                'review_after' => '2027-08-01',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.92',
            ),
            'odigos-enstasis.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος της διαδικασίας ενστάσεων 1ΓΕ/2026–2ΓΕ/2026: 12/08/2026 08:00 έως 21/08/2026 14:00, ηλεκτρονική υποβολή, e-Παράβολο 50 €, βασική διαχείριση επανυποβολής και επίσημα εγχειρίδια ΑΣΕΠ.',
                'school_year' => '1ΓΕ/2026 · 2ΓΕ/2026',
                'review_trigger' => 'Νέα ανακοίνωση/παράταση ενστάσεων, αλλαγή ποσού e-Παραβόλου ή νέο εγχειρίδιο ηλεκτρονικής ένστασης ΑΣΕΠ.',
                'version' => '3.22.92',
            ),
            'dikaiologitika-tekna-anapiria.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης cross-audit των κοινωνικών κριτηρίων 1ΓΕ/2026–2ΓΕ/2026 και των αποδεικτικών τους: επιλέξιμα τέκνα, ειδικές οικογενειακές περιπτώσεις, αναπηρία υποψηφίου/συζύγου/τέκνου, όριο 50% και αποδεκτές πιστοποιήσεις.',
                'school_year' => '1ΓΕ/2026 · 2ΓΕ/2026',
                'review_trigger' => 'Νέα προκήρυξη ΑΣΕΠ εκπαιδευτικών ή αλλαγή κοινωνικών κριτηρίων/πιστοποίησης αναπηρίας.',
                'version' => '3.22.92',
            ),
            'ypologismos-morion-apospasis-exoteriko.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος αποσπάσεων σε ελληνόγλωσσες μονάδες εξωτερικού 2026–2027/Νότιο Ημισφαίριο 2027 έναντι 11771/Η2/30-01-2026 και Υ.Α. 83046/Η2/2020: προϋποθέσεις, μοριοδότηση, γλώσσες, πίνακες προτιμήσεων και ενσωματωμένα Παραρτήματα ΙΙΙ/V.',
                'school_year' => '2026-2027 · Ν. Ημισφαίριο 2027',
                'review_trigger' => 'Νέα πρόσκληση αποσπάσεων εξωτερικού, τροποποίηση Υ.Α. 83046/Η2/2020 ή νέοι πίνακες χωρών/επιμισθίων.',
                'review_after' => '2027-01-15',
                'review_policy' => 'Εσωτερικό όριο προληπτικού επανελέγχου για ετήσιο/σχολικό κύκλο· δεν αποτελεί επίσημη προθεσμία έκδοσης νέας πράξης.',
                'version' => '3.22.92',
            ),
            'metatropi-klimakas.php' => array(
                'status' => 'verified',
                'last_verified' => '2026-10-07',
                'scope' => 'Πλήρης έλεγχος μετατροπής βαθμού για 1ΓΕ/2026 και 1ΓΤ/2024: 10βάθμια/20βάθμια αναγωγή, λεκτικοί βαθμοί Καλώς=5,00 · Λίαν Καλώς=6,50 · Άριστα=8,50 και αντίστοιχη μοριοδότηση βαθμού βασικού τίτλου.',
                'school_year' => '1ΓΕ/2026 · 1ΓΤ/2024',
                'review_trigger' => 'Νέα προκήρυξη που αλλάζει κλίμακα καταχώρισης, λεκτική αντιστοίχιση ή συντελεστή μοριοδότησης βαθμού.',
                'version' => '3.22.92',
            ),
            'kena-sxoleion-login.php' => array(
                'status' => 'not_applicable',
                'last_verified' => '2026-10-07',
                'scope' => 'Τεχνική πύλη σύνδεσης της ξεχωριστής υπηρεσίας «Κενά σχολείων». Δεν περιέχει αυτοτελείς νομικούς/μοριοδοτικούς κανόνες προς freshness audit.',
                'review_trigger' => 'Αν προστεθεί στη σελίδα αυτοτελής νομικός κανόνας, προθεσμία ή κανονιστική οδηγία.',
                'version' => '3.22.92',
            ),
            'adeies-ekpaideutikon.php' => array(
                'status' => 'not_applicable',
                'last_verified' => '2026-10-07',
                'scope' => 'Σελίδα-κόμβος χωρίς ανεξάρτητους κανόνες. Η νομική επικαιρότητα καλύπτεται από τα τρία verified υποεργαλεία: άδειες μονίμων, άδειες αναπληρωτών/ΙΔΟΧ και σύγκριση αδειών.',
                'review_trigger' => 'Αν ο κόμβος αποκτήσει αυτοτελές νομικό περιεχόμενο ή πάψει να αντλεί αποκλειστικά από τα verified datasets αδειών.',
                'version' => '3.22.92',
            ),
        );

        return $registry;
    }
}


if (!function_exists('legalAuditToday')) {
    function legalAuditToday()
    {
        if (defined('EDU_LEGAL_AUDIT_TODAY')) {
            $forced = trim((string) EDU_LEGAL_AUDIT_TODAY);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $forced)) return $forced;
        }
        return date('Y-m-d');
    }
}

if (!function_exists('legalAuditApplyAutomaticReview')) {
    /**
     * Turn an otherwise verified/partial entry into review_due once an internal
     * maintenance threshold is reached. `review_after` is an editorial safety
     * threshold, never an assertion that a new legal act must exist by that day.
     */
    function legalAuditApplyAutomaticReview($entry)
    {
        if (!is_array($entry)) return $entry;
        $status = isset($entry['status']) ? (string) $entry['status'] : 'pending';
        $reviewAfter = isset($entry['review_after']) ? trim((string) $entry['review_after']) : '';
        if (!in_array($status, array('verified', 'partial'), true)) return $entry;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $reviewAfter)) return $entry;

        $today = legalAuditToday();
        if ($today >= $reviewAfter) {
            $entry['declared_status'] = $status;
            $entry['status'] = 'review_due';
            $entry['auto_review_due'] = true;
            if (empty($entry['review_due_reason'])) {
                $entry['review_due_reason'] = 'Έφτασε το εσωτερικό όριο προληπτικού επανελέγχου (' . legalAuditFormatDate($reviewAfter) . '). Ελέγξτε αν έχει εκδοθεί νέα πρόσκληση, απόφαση ή εγκύκλιος.';
            }
        }
        return $entry;
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
            'not_applicable' => 'Δεν απαιτεί αυτοτελή νομικό έλεγχο',
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
            return legalAuditApplyAutomaticReview($entry);
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
        $counts = array('verified' => 0, 'partial' => 0, 'review_due' => 0, 'pending' => 0, 'not_applicable' => 0, 'total' => 0);
        foreach (legalAuditCatalogueRows() as $row) {
            $status = isset($row['audit']['status']) ? $row['audit']['status'] : 'pending';
            if (!isset($counts[$status])) $status = 'pending';
            $counts[$status]++;
            $counts['total']++;
        }
        return $counts;
    }
}
