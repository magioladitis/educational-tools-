<?php
/**
 * ΕΣΩΤΕΡΙΚΟ crosswalk: Ωρολόγιο πρόγραμμα ↔ Αναθέσεις μαθημάτων.
 *
 * Δεν χρησιμοποιείται για αλλαγή των δημόσιων τίτλων του ωρολογίου. Κρατά
 * μόνο ελεγμένες αντιστοιχίσεις όπου τα δύο κανονιστικά datasets έχουν
 * διαφορετική ονομασία ή διαφορετικό επίπεδο ανάλυσης.
 *
 * Σημαντικό: σε ορισμένα μαθήματα ειδικότητας το ωρολόγιο δίνει μία ενιαία
 * γραμμή Θ+Ε, ενώ οι αναθέσεις έχουν χωριστές γραμμές Θεωρίας/Εργαστηρίου.
 * Αυτές οι περιπτώσεις αποθηκεύονται ως components, ώστε μελλοντική κατανομή
 * ωρών σε εκπαιδευτικούς να μην εφαρμόσει μία ανάθεση σε ολόκληρο το Θ+Ε.
 */

function teachingTimetableSafeSubjectAliases()
{
    return array(
        // Ίδιο μάθημα, συντομευμένος τίτλος στο ωρολόγιο.
        'Πολιτική Παιδεία' => 'Πολιτική Παιδεία (Οικονομία, Πολιτικοί Θεσμοί και Αρχές Δικαίου και Κοινωνιολογία)',
        // Συντομογραφία του ωρολογίου ΕΝ.Ε.Ε.ΓΥ.-Λ.
        'ΣΕΠ - Ασφάλεια και Υγεία στο χώρο Εργασίας' => 'Σχολικός Επαγγελματικός Προσανατολισμός - Ασφάλεια και Υγεία στο χώρο εργασίας',
        // Μικρές αποκλίσεις τίτλων ανάμεσα στα ΦΕΚ ωρολογίου/αναθέσεων ειδικοτήτων.
        'Τεχνολογία Παραγωγής Ενδυμάτων' => 'Τεχνολογία Παραγωγή Ενδυμάτων',
        'Εργαστήριο Ηλεκτροτεχνίας - Ηλεκτρικών Μηχανών' => 'Εργαστήριο Ηλεκτροτεχνίας και Ηλεκτρικών Μηχανών',
        'Μηχανές Εσωτερικής Καύσης ΙΙ' => 'Μηχανές Εσωτερικής Καύσης II',
        'Μηχανές Εσωτερικής Καύσης ΙΙ (Εργαστήριο)' => 'Μηχανές Εσωτερικής Καύσης II — Εργαστήριο',
        'Νεότερες Απεικονιστικές Μέθοδοι' => 'Νεώτερες Απεικονιστικές Μέθοδοι',
        'Συνταγολογία - Νομοθεσία - Βιβλία Φαρμακείου' => 'Συνταγολόγια - Νομοθεσία - Βιβλία Φαρμακείου',
        'Ναυτικό Δίκαιο - Διεθνείς Κανονισμοί στην Ναυτιλία - Εφαρμογές' => 'Ναυτικό Δίκαιο – Διεθνείς Κανονισμοί στη Ναυτιλία - Εφαρμογές',
        'Ηλεκτρικές Εγκαταστάσεις Πλοίου ΙΙ' => 'Ηλεκτρολογικές Εγκαταστάσεις Πλοίου ΙΙ',
    );
}

function teachingTimetablePepalBSubjectAliases()
{
    /*
     * Ελεγμένες αποκλίσεις τίτλων μεταξύ ωρολογίου και αναθέσεων της
     * Β΄ Π.ΕΠΑ.Λ. Οι αντιστοιχίσεις ισχύουν μόνο στον αντίστοιχο τομέα.
     * Επιβεβαιώθηκαν και επιχειρησιακά με snapshot myschool Π.ΕΠΑ.Λ.
     * Κέρκυρας 2026-09-05, χωρίς το myschool να αποτελεί κανονιστική πηγή.
     */
    return array(
        'electrical' => array(
            'Ηλεκτροτεχνία'
                => 'Ηλεκτροτεχνία (Κυκλώματα Συνεχούς και Εναλλασσόμενου ρεύματος)',
            'Εισαγωγή στα Υπολογιστικά Συστήματα και Δίκτυα'
                => 'Εισαγωγή στα Υπολογιστικά Συστήματα και στα Δίκτυα Επικοινωνιών',
        ),
    );
}

function teachingTimetablePepalGSubjectAliases()
{
    /*
     * Ελεγμένες, context-specific αποκλίσεις τίτλων μεταξύ:
     * - ΦΕΚ Β΄ 5251/2023 (ωρολόγιο Γ΄ Π.ΕΠΑ.Λ.) και
     * - ΦΕΚ Β΄ 5510/2023 (αναθέσεις Γ΄ Π.ΕΠΑ.Λ.).
     *
     * Δεν μπαίνουν στο γενικό alias table, επειδή εδώ η ειδικότητα αποτελεί
     * μέρος της ταυτότητας της αντιστοίχισης. Ιδίως στα Ναυτιλιακά το
     * ωρολόγιο προσθέτει «Πρακτική Άσκηση», ενώ το ΦΕΚ αναθέσεων όχι.
     */
    return array(
        'administration' => array(
            'Εισαγωγή στην Οργανωσιακή Συμπεριφορά και στη Διοίκηση Ανθρώπινων Πόρων'
                => 'Εισαγωγή στην Οργανωσιακή Συμπεριφορά και στην Διοίκηση Ανθρώπινων Πόρων',
        ),
        'tourism' => array(
            'Γαλλικά ή Γερμανικά ή Ιταλικά ή Ισπανικά'
                => 'Γαλλικά ή Γερμανικά ή Ισπανικά ή Ιταλικά',
        ),
        'building' => array(
            'Ηλεκτρονική Σχεδίαση Δομικών Έργων'
                => 'Ηλεκτρονική Σχεδίαση Τεχνικών Έργων',
        ),
        'silver' => array(
            'Σύγχρονο - Εικαστικό Κόσμημα'
                => 'Σύγχρονο Εικαστικό Κόσμημα',
        ),
        'captain' => array(
            'Ν.Η.Ο. - Επικοινωνίες - Πρακτική Άσκηση'
                => 'Ν.Η.Ο. - Επικοινωνίες',
            'Εφαρμογές Δ.Κ.Α.Σ. - ECDIS - ARPA - Πρακτική Άσκηση'
                => 'Εφαρμογές Δ.Κ.Α.Σ. – ECDIS – ARPA',
        ),
        'engineer' => array(
            'Βοηθητικές Εγκαταστάσεις Πλοίου - Πρακτική Άσκηση'
                => 'Βοηθητικές Εγκαταστάσεις Πλοίου',
            'Μηχανολογικές Κατασκευές Πλοίου - Σχέδιο με Η/Υ - Πρακτική Άσκηση'
                => 'Μηχανολογικές Κατασκευές Πλοίου - Σχέδιο με Η/Υ',
        ),
    );
}

function teachingTimetableEneegylSubjectAliases()
{
    /*
     * Ελεγμένες, context-specific αποκλίσεις τίτλων μεταξύ:
     * - ΦΕΚ Β΄ 2149/2026 (ωρολόγιο Λυκείου ΕΝ.Ε.Ε.ΓΥ.-Λ.) και
     * - ΦΕΚ Β΄ 3216/2026 (αναθέσεις ΕΝ.Ε.Ε.ΓΥ.-Λ.).
     *
     * Δεν είναι global aliases. Ισχύουν μόνο στον συγκεκριμένο τομέα,
     * επειδή το ΦΕΚ ωρολογίου χρησιμοποιεί νεότερο/συντομότερο τίτλο.
     */
    return array(
        'admin' => array(
            'Στοιχεία Δικαίου (Αστικό - Εργατικό)'
                => 'Στοιχεία Δικαίου (Αστικό-Εμπορικό – Εργατικό-Τουριστικό)',
        ),
        'building' => array(
            'Σχέδιο Δομικών Έργων με χρήση Η/Υ'
                => 'Σχέδιο Δομικών Έργων με χρήση Η/Υ Ι',
        ),
    );
}

function teachingTimetableVocationalChoiceOptionsForRow($row)
{
    $school = isset($row['school']) ? $row['school'] : '';
    $track = isset($row['track']) ? $row['track'] : '';
    $subject = isset($row['subject']) ? $row['subject'] : '';

    // Β΄ ΕΠΑ.Λ./Εσπερινού ΕΠΑ.Λ. — Εφαρμοσμένες Τέχνες:
    // ένα εργαστηριακό slot που εξειδικεύεται σε ένα από τέσσερα μαθήματα.
    if (in_array($school, array('epal', 'esperino_epal'), true)
        && $track === 'arts' && $subject === 'Ειδικό Εργαστηριακό Μάθημα') {
        return array(
            array('label'=>'Φωτογραφία και Ηλεκτρονική Επεξεργασία Εικόνας', 'subject'=>'Φωτογραφία και Ηλεκτρονική Επεξεργασία Εικόνας (μάθημα επιλογής)'),
            array('label'=>'Τεχνολογία Υφαντικών Υλών', 'subject'=>'Τεχνολογία Υφαντικών Υλών (μάθημα επιλογής)'),
            array('label'=>'Εργαστήριο Χαρακτικής - Πλαστικής', 'subject'=>'Εργαστήριο Χαρακτικής - Πλαστικής (μάθημα επιλογής)'),
            array('label'=>'Εισαγωγή στις Ξύλινες Κατασκευές', 'subject'=>'Εισαγωγή στις Ξύλινες Κατασκευές'),
        );
    }

    // Β΄ ΕΠΑ.Λ./Εσπερινού ΕΠΑ.Λ. — Υγεία: τα «Ειδικό Μάθημα Α/Β»
    // είναι slots επιλογής. Κάθε πραγματικό μάθημα μπορεί να έχει χωριστή
    // Θεωρία/Εργαστήριο και άρα διαφορετική ανάθεση ανά component.
    if (in_array($school, array('epal', 'esperino_epal'), true)
        && $track === 'health' && strpos($subject, 'Ειδικό Μάθημα') === 0) {
        return array(
            array('label'=>'Μικροβιολογία Ι', 'components'=>array('Μικροβιολογία Ι — Θεωρία','Μικροβιολογία Ι — Εργαστήριο')),
            array('label'=>'Νοσηλευτική Ι', 'components'=>array('Νοσηλευτική Ι — Θεωρία','Νοσηλευτική Ι — Εργαστήριο')),
            array('label'=>'Δημιουργική Απασχόληση στην Προσχολική Ηλικία Ι', 'components'=>array('Δημιουργική Απασχόληση στην Προσχολική Ηλικία Ι — Θεωρία','Δημιουργική Απασχόληση στην Προσχολική Ηλικία Ι — Εργαστήριο')),
            array('label'=>'Σύγχρονη Αισθητική Ι', 'components'=>array('Σύγχρονη Αισθητική Ι — Θεωρία','Σύγχρονη Αισθητική Ι — Εργαστήριο')),
            array('label'=>'Εισαγωγή στη Φυσικοθεραπεία Ι', 'components'=>array('Εισαγωγή στη Φυσικοθεραπεία Ι — Θεωρία','Εισαγωγή στη Φυσικοθεραπεία Ι — Εργαστήριο')),
            array('label'=>'Βασικές Εφαρμογές Κομμωτικής Ι', 'components'=>array('Βασικές Εφαρμογές Κομμωτικής Ι — Εργαστήριο')),
            array('label'=>'Οδοντοτεχνία Ι', 'components'=>array('Οδοντοτεχνία Ι — Θεωρία','Οδοντοτεχνία Ι — Εργαστήριο')),
            array('label'=>'Φαρμακευτική Τεχνολογία Ι', 'components'=>array('Φαρμακευτική Τεχνολογία Ι — Θεωρία','Φαρμακευτική Τεχνολογία Ι — Εργαστήριο')),
            array('label'=>'Ακτινοτεχνολογία Ι', 'components'=>array('Ακτινοτεχνολογία Ι — Θεωρία','Ακτινοτεχνολογία Ι — Εργαστήριο')),
        );
    }

    // Β΄ Π.ΕΠΑ.Λ. — Υγεία: αντίστοιχη λογική επιλογής, αλλά με τους
    // ακριβείς τίτλους του αυτοτελούς πίνακα αναθέσεων Π.ΕΠΑ.Λ.
    if ($school === 'pepal' && $track === 'health' && strpos($subject, 'Ειδικό Μάθημα') === 0) {
        return array(
            array('label'=>'Μικροβιολογία I', 'components'=>array('Μικροβιολογία I — Θεωρία','Μικροβιολογία I — Εργαστήριο')),
            array('label'=>'Βασικές Κλινικές Δεξιότητες', 'components'=>array('Βασικές Κλινικές Δεξιότητες — Θεωρία','Βασικές Κλινικές Δεξιότητες — Εργαστήριο')),
            array('label'=>'Δημιουργία και Έκφραση στην Προσχολική Ηλικία Ι', 'components'=>array('Δημιουργία και Έκφραση στην Προσχολική Ηλικία Ι — Εργαστήριο')),
            array('label'=>'Σύγχρονη Αισθητική I', 'components'=>array('Σύγχρονη Αισθητική I — Θεωρία','Σύγχρονη Αισθητική I — Εργαστήριο')),
            array('label'=>'Εισαγωγή στη Φυσικοθεραπεία', 'components'=>array('Εισαγωγή στη Φυσικοθεραπεία — Θεωρία','Εισαγωγή στη Φυσικοθεραπεία — Εργαστήριο')),
            array('label'=>'Βασικές Εφαρμογές Κομμωτικής I', 'components'=>array('Βασικές Εφαρμογές Κομμωτικής I — Εργαστήριο')),
            array('label'=>'Οδοντοτεχνία I', 'components'=>array('Οδοντοτεχνία I — Θεωρία','Οδοντοτεχνία I — Εργαστήριο')),
            array('label'=>'Φαρμακευτική Τεχνολογία Ι', 'components'=>array('Φαρμακευτική Τεχνολογία Ι — Θεωρία','Φαρμακευτική Τεχνολογία Ι — Εργαστήριο')),
            array('label'=>'Ακτινολογία Ι', 'components'=>array('Ακτινολογία Ι — Θεωρία','Ακτινολογία Ι — Εργαστήριο')),
        );
    }

    // Β΄ Π.ΕΠΑ.Λ. — Ναυτιλιακά: ρητή επιλογή ενός από δύο ειδικά μαθήματα.
    if ($school === 'pepal' && $track === 'naval' && $subject === 'Ειδικό Μάθημα (1 από 2)') {
        return array(
            array('label'=>'Ναυτική Τέχνη - Έκτακτες Ανάγκες', 'subject'=>'Ναυτική Τέχνη - Έκτακτες Ανάγκες'),
            array('label'=>'Αντοχή Υλικών - Εφαρμογές', 'subject'=>'Αντοχή Υλικών - Εφαρμογές'),
        );
    }

    return null;
}

function teachingTimetableEneegylChoiceOptionsForRow($row)
{
    if (!isset($row['school']) || $row['school'] !== 'eneegyl_lykeio') {
        return null;
    }

    $track = isset($row['track']) ? $row['track'] : '';
    $subject = isset($row['subject']) ? $row['subject'] : '';

    // Γ΄ Εφαρμοσμένων Τεχνών: το ΦΕΚ ωρολογίου δίνει ένα slot 4Ε και
    // το ΦΕΚ αναθέσεων εξειδικεύει τις τέσσερις επιτρεπτές επιλογές.
    if ($track === 'arts' && $subject === 'Ειδικό εργαστηριακό μάθημα') {
        return array(
            array('label'=>'Φωτογραφία και Ηλεκτρονική Επεξεργασία Εικόνας', 'subject'=>'Φωτογραφία και Ηλεκτρονική Επεξεργασία Εικόνας (μάθημα επιλογής)'),
            array('label'=>'Τεχνολογία Υφαντικών Υλών', 'subject'=>'Τεχνολογία Υφαντικών Υλών (μάθημα επιλογής)'),
            array('label'=>'Εργαστήριο Χαρακτικής - Πλαστικής', 'subject'=>'Εργαστήριο Χαρακτικής - Πλαστικής (μάθημα επιλογής)'),
            array('label'=>'Εισαγωγή στις Ξύλινες Κατασκευές', 'subject'=>'Εισαγωγή στις Ξύλινες Κατασκευές'),
        );
    }

    // Β΄/Γ΄ Υγείας: «Ειδικό Μάθημα Α/Β» δεν είναι τίτλος μαθήματος αλλά
    // θέση επιλογής. Κρατάμε τις πραγματικές επιλογές ώστε μελλοντικά η
    // ανάθεση να εξαρτάται από το μάθημα που λειτουργεί στη σχολική μονάδα.
    if ($track === 'health' && strpos($subject, 'Ειδικό Μάθημα') === 0) {
        $bases = array(
            'Μικροβιολογία Ι',
            'Νοσηλευτική Ι',
            'Δημιουργική Απασχόληση στην Προσχολική Ηλικία Ι',
            'Σύγχρονη Αισθητική Ι',
            'Εισαγωγή στη Φυσικοθεραπεία Ι',
            'Βασικές Εφαρμογές Κομμωτικής Ι',
            'Οδοντοτεχνία Ι',
            'Φαρμακευτική Τεχνολογία Ι',
            'Ακτινοτεχνολογία Ι',
        );
        $options = array();
        foreach ($bases as $base) {
            $options[] = array('label'=>$base);
        }
        return $options;
    }

    return null;
}

function teachingTimetableEneegylRegulatoryGapCourseIds()
{
    /*
     * Μαθήματα που υπάρχουν ρητά στο νέο ωρολόγιο ΦΕΚ Β΄ 2149/2026,
     * αλλά δεν έχουν αντίστοιχη γραμμή στον πίνακα αναθέσεων Β΄/Γ΄ τάξης
     * του ΦΕΚ Β΄ 3216/2026. Δεν δανειζόμαστε ανάθεση από τη Δ΄ τάξη μόνο
     * επειδή ο τίτλος επανεμφανίζεται εκεί.
     */
    return array(
        'eneegyl.lykeio.c.agriculture.5',
        'eneegyl.lykeio.c.agriculture.6',
        'eneegyl.lykeio.c.admin.5',
        'eneegyl.lykeio.c.admin.6',
        'eneegyl.lykeio.c.building.5',
        'eneegyl.lykeio.c.building.6',
        'eneegyl.lykeio.c.arts.4',
        'eneegyl.lykeio.c.arts.5',
        'eneegyl.lykeio.c.electrical.4',
        'eneegyl.lykeio.c.electrical.5',
        'eneegyl.lykeio.c.mechanical.5',
        'eneegyl.lykeio.c.it.4',
        'eneegyl.lykeio.c.it.5',
        'eneegyl.lykeio.c.health.6',
        'eneegyl.lykeio.c.health.7',
        'eneegyl.lykeio.c.health.fallback.5',
        'eneegyl.lykeio.c.health.fallback.6',
    );
}

function teachingTimetableSplitTheoryLabSubjects()
{
    /* Audited set: το ωρολόγιο έχει μία ενιαία γραμμή Θ+Ε, ενώ οι αναθέσεις
     * του ίδιου μαθήματος/ειδικότητας έχουν ξεχωριστή Θεωρία και Εργαστήριο. */
    return array(
        'Νοσηλευτική ΙΙ' => 'Νοσηλευτική ΙΙ',
        'Μικροβιολογία ΙΙ' => 'Μικροβιολογία ΙΙ',
        'Αιματολογία' => 'Αιματολογία',
        'Κλινική Βιοχημεία' => 'Κλινική Βιοχημεία',
        'Ανοσολογία' => 'Ανοσολογία',
        'Αγωγή Βρέφους και Νηπίου' => 'Αγωγή Βρέφους και Νηπίου',
        'Φυσικοθεραπεία' => 'Φυσικοθεραπεία',
        'Πρακτική Φυσικοθεραπεία' => 'Πρακτική Φυσικοθεραπεία',
        'Φυσικά Μέσα και Εφαρμογή τους' => 'Φυσικά Μέσα και Εφαρμογή τους',
        'Οδοντοτεχνία ΙΙ' => 'Οδοντοτεχνία ΙΙ',
        'Ακίνητη Προσθετική' => 'Ακίνητη Προσθετική',
        'Ακίνητη Προσθετική και Πορσελάνη' => 'Ακίνητη Προσθετική και Πορσελάνη',
        'Στοιχεία Ορθοδοντικής' => 'Στοιχεία Ορθοδοντικής',
        'Ακτινοτεχνολογία ΙΙ' => 'Ακτινοτεχνολογία ΙΙ',
        'Ακτινοανατομική' => 'Ακτινοανατομική',
        'Φαρμακευτική Τεχνολογία ΙΙ' => 'Φαρμακευτική Τεχνολογία ΙΙ',
        'Κοσμητολογία' => 'Κοσμητολογία',
        'Στοιχεία Φαρμακογνωσίας' => 'Στοιχεία Φαρμακογνωσίας',
        // Στο ΦΕΚ αναθέσεων ο ίδιος τίτλος γράφεται «Spa».
        'SPA και Λουτροθεραπεία' => 'Spa και Λουτροθεραπεία',
        'Σύγχρονη Αισθητική ΙΙ' => 'Σύγχρονη Αισθητική ΙΙ',
    );
}

function teachingTimetableTheoryLabHours($display)
{
    $display = (string) $display;
    $theory = 0;
    $lab = 0;

    if (preg_match('/(\d+)\s*Θ/u', $display, $match)) {
        $theory = (int) $match[1];
    }
    if (preg_match('/(\d+)\s*Ε/u', $display, $match)) {
        $lab = (int) $match[1];
    }

    if ($theory <= 0 || $lab <= 0) {
        return null;
    }
    return array('theory' => $theory, 'lab' => $lab);
}

function teachingTimetableAssignmentAliasForRow($row)
{
    $aliases = teachingTimetableSafeSubjectAliases();
    $subject = isset($row['subject']) ? $row['subject'] : '';

    // ΕΝ.Ε.Ε.ΓΥ.-Λ.: ασφαλή aliases μόνο μέσα στον αντίστοιχο τομέα
    // (ΦΕΚ Β΄ 2149/2026 ↔ Β΄ 3216/2026).
    if (isset($row['school']) && $row['school'] === 'eneegyl_lykeio' && !empty($row['track'])) {
        $eneegylAliases = teachingTimetableEneegylSubjectAliases();
        $track = $row['track'];
        if (isset($eneegylAliases[$track][$subject])) {
            return $eneegylAliases[$track][$subject];
        }
    }

    // Β΄ Π.ΕΠΑ.Λ.: ασφαλή aliases μόνο μέσα στον αντίστοιχο τομέα.
    if (isset($row['school']) && $row['school'] === 'pepal'
        && isset($row['hours']['Β΄']) && !empty($row['track'])) {
        $pepalBAliases = teachingTimetablePepalBSubjectAliases();
        $track = $row['track'];
        if (isset($pepalBAliases[$track][$subject])) {
            return $pepalBAliases[$track][$subject];
        }
    }

    // Γ΄ Π.ΕΠΑ.Λ.: οι παρακάτω αντιστοιχίσεις είναι ασφαλείς μόνο μέσα
    // στην αντίστοιχη ειδικότητα (ΦΕΚ Β΄ 5251/2023 ↔ Β΄ 5510/2023).
    if (isset($row['school']) && $row['school'] === 'pepal' && !empty($row['specialty'])) {
        $pepalAliases = teachingTimetablePepalGSubjectAliases();
        $specialty = $row['specialty'];
        if (isset($pepalAliases[$specialty][$subject])) {
            return $pepalAliases[$specialty][$subject];
        }
    }

    /* Σε ορισμένες ειδικότητες το ωρολόγιο έχει ήδη χωριστές γραμμές
     * Θεωρίας/Εργαστηρίου, αλλά η διάκριση φαίνεται μόνο από τις ώρες ή από
     * το «(Εργαστήριο)», ενώ οι αναθέσεις χρησιμοποιούν επίθημα «— Θεωρία» /
     * «— Εργαστήριο». Είναι ασφαλές alias μόνο για τα παρακάτω ελεγμένα
     * μαθήματα ειδικότητας. */
    if (!empty($row['specialty']) && !empty($row['hours_display']) && is_array($row['hours_display'])) {
        $displayValues = array_values($row['hours_display']);
        if (count($displayValues) === 1) {
            $display = (string) $displayValues[0];
            $theorySplit = (
                $subject === 'Προγραμματισμός Υπολογιστών'
                || ($subject === 'Δίκτυα Υπολογιστών'
                    && in_array($row['specialty'], array('applications', 'hardware_networks'), true))
            );
            if ($theorySplit && preg_match('/^\s*\d+\s*Θ\s*$/u', $display)) {
                return $subject . ' — Θεωρία';
            }
            $labBases = array(
                'Προγραμματισμός Υπολογιστών',
                'Δίκτυα Υπολογιστών',
                'Στοιχεία Ψύξης - Κλιματισμού',
                'Κινητήρες Αεροσκαφών',
            );
            foreach ($labBases as $base) {
                if ($subject === $base . ' (Εργαστήριο)' && preg_match('/^\s*\d+\s*Ε(?:\s*\([^)]*\))?\s*$/u', $display)) {
                    return $base . ' — Εργαστήριο';
                }
            }
        }
    }

    if (!isset($aliases[$subject])) {
        return null;
    }

    // Το «Πολιτική Παιδεία» ως ασφαλές alias αφορά την Α΄ τάξη Λυκείου/ΕΠΑ.Λ.
    if ($subject === 'Πολιτική Παιδεία') {
        $allowed = array('gel', 'kallitexniko_gel', 'mousiko_gel', 'epal', 'esperino_epal', 'pepal', 'eneegyl_lykeio');
        if (!in_array(isset($row['school']) ? $row['school'] : '', $allowed, true)) {
            return null;
        }
    }

    // Το ΣΕΠ alias είναι ειδικό για το Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ.
    if ($subject === 'ΣΕΠ - Ασφάλεια και Υγεία στο χώρο Εργασίας'
        && (!isset($row['school']) || $row['school'] !== 'eneegyl_lykeio')) {
        return null;
    }

    return $aliases[$subject];
}

function teachingTimetableEnrichRows($rows)
{
    $splitSubjects = teachingTimetableSplitTheoryLabSubjects();
    $componentSchools = array('epal', 'esperino_epal', 'pepal', 'eneegyl_lykeio');

    $eneegylGapIds = array_flip(teachingTimetableEneegylRegulatoryGapCourseIds());

    foreach ($rows as $index => $row) {
        $alias = teachingTimetableAssignmentAliasForRow($row);
        if ($alias !== null) {
            $rows[$index]['assignment_subject_alias'] = $alias;
        }

        $choiceOptions = teachingTimetableVocationalChoiceOptionsForRow($row);
        if ($choiceOptions === null) {
            $choiceOptions = teachingTimetableEneegylChoiceOptionsForRow($row);
        }
        if ($choiceOptions !== null) {
            $rows[$index]['assignment_link_status'] = 'choice_dependent';
            $rows[$index]['assignment_choice_options'] = $choiceOptions;
        }

        $courseId = isset($row['course_id']) ? $row['course_id'] : '';
        if (isset($eneegylGapIds[$courseId])) {
            $rows[$index]['assignment_link_status'] = 'regulatory_gap';
            $rows[$index]['assignment_link_note'] = 'Το μάθημα υπάρχει στο ωρολόγιο ΦΕΚ Β΄ 2149/2026, χωρίς αντίστοιχη γραμμή στον πίνακα αναθέσεων Β΄/Γ΄ του ΦΕΚ Β΄ 3216/2026.';
        }

        $subject = isset($row['subject']) ? $row['subject'] : '';
        if (!isset($splitSubjects[$subject])) {
            continue;
        }
        if (!in_array(isset($row['school']) ? $row['school'] : '', $componentSchools, true)) {
            continue;
        }
        // Οι ελεγμένες split περιπτώσεις είναι μαθήματα ειδικότητας, όχι οι
        // placeholders «Ειδικό Μάθημα» των Β΄/Γ΄ τομέων.
        if (empty($row['specialty']) || empty($row['hours_display']) || !is_array($row['hours_display'])) {
            continue;
        }

        $assignmentBase = $splitSubjects[$subject];
        $byGrade = array();
        foreach ($row['hours_display'] as $grade => $display) {
            $parsed = teachingTimetableTheoryLabHours($display);
            if ($parsed === null) {
                continue;
            }
            $byGrade[$grade] = array(
                array(
                    'kind' => 'theory',
                    'hours' => $parsed['theory'],
                    'subject' => $assignmentBase . ' — Θεωρία',
                ),
                array(
                    'kind' => 'lab',
                    'hours' => $parsed['lab'],
                    'subject' => $assignmentBase . ' — Εργαστήριο',
                ),
            );
        }
        if ($byGrade) {
            $rows[$index]['assignment_components_by_grade'] = $byGrade;
        }
    }

    return $rows;
}
