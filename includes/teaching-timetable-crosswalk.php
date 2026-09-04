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

    foreach ($rows as $index => $row) {
        $alias = teachingTimetableAssignmentAliasForRow($row);
        if ($alias !== null) {
            $rows[$index]['assignment_subject_alias'] = $alias;
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
