<?php
/**
 * Shared registry for teacher branches / specialties.
 * PHP 5.6 compatible.
 *
 * Keep assignment datasets code-only (e.g. ΠΕ05). Resolve human-readable
 * labels through this registry so labels have one source of truth.
 */

function teacherSpecialties()
{
    return array(
        'ΠΕ01' => array('label' => 'Θεολόγοι'),
        'ΠΕ02' => array('label' => 'Φιλόλογοι'),
        'ΠΕ03' => array('label' => 'Μαθηματικοί'),
        'ΠΕ04.01' => array('label' => 'Φυσικοί'),
        'ΠΕ04.02' => array('label' => 'Χημικοί'),
        'ΠΕ04.03' => array('label' => 'Φυσιογνώστες'),
        'ΠΕ04.04' => array('label' => 'Βιολόγοι'),
        'ΠΕ04.05' => array('label' => 'Γεωλόγοι'),
        'ΠΕ05' => array('label' => 'Γαλλικής'),
        'ΠΕ06' => array('label' => 'Αγγλικής'),
        'ΠΕ07' => array('label' => 'Γερμανικής'),
        'ΠΕ08' => array('label' => 'Καλλιτεχνικών'),
        'ΠΕ11' => array('label' => 'Φυσικής Αγωγής'),
        'ΠΕ15' => array('label' => 'Οικιακής Οικονομίας'),
        'ΠΕ33' => array('label' => 'Μεθοδολογίας, Ιστορίας και Θεωρίας της Επιστήμης (ΜΙΘΕ)'),
        'ΠΕ34' => array('label' => 'Ιταλικής'),
        'ΠΕ40' => array('label' => 'Ισπανικής'),
        'ΠΕ41' => array('label' => 'Θεωρίας και Ιστορίας της Τέχνης'),
        'ΠΕ78' => array('label' => 'Κοινωνικών Επιστημών'),
        'ΠΕ79' => array('label' => 'Μουσικής'),
        'ΠΕ79.01' => array('label' => 'Μουσικής Επιστήμης'),
        'ΠΕ80' => array('label' => 'Οικονομίας'),
        'ΠΕ81' => array('label' => 'Πολιτικών Μηχανικών - Αρχιτεκτόνων'),
        'ΠΕ82' => array('label' => 'Μηχανολόγων'),
        'ΠΕ83' => array('label' => 'Ηλεκτρολόγων'),
        'ΠΕ84' => array('label' => 'Ηλεκτρονικών'),
        'ΠΕ85' => array('label' => 'Χημικών Μηχανικών'),
        'ΠΕ86' => array('label' => 'Πληροφορικής'),
        'ΠΕ87' => array('label' => 'Υγείας - Πρόνοιας - Ευεξίας'),
        'ΠΕ87.01' => array('label' => 'Ιατρικής'),
        'ΠΕ87.02' => array('label' => 'Νοσηλευτικής'),
        'ΠΕ87.03' => array('label' => 'Αισθητικής'),
        'ΠΕ87.04' => array('label' => 'Ιατρικών Εργαστηρίων'),
        'ΠΕ87.05' => array('label' => 'Οδοντοτεχνικής'),
        'ΠΕ87.06' => array('label' => 'Κοινωνικής Εργασίας'),
        'ΠΕ87.07' => array('label' => 'Ραδιολογίας - Ακτινολογίας'),
        'ΠΕ87.08' => array('label' => 'Φυσιοθεραπείας'),
        'ΠΕ87.09' => array('label' => 'Βρεφονηπιοκόμων'),
        'ΠΕ87.10' => array('label' => 'Δημόσιας Υγιεινής'),
        'ΠΕ88' => array('label' => 'Γεωπονίας, Διατροφής και Περιβάλλοντος'),
        'ΠΕ88.01' => array('label' => 'Γεωπόνοι'),
        'ΠΕ88.02' => array('label' => 'Φυτικής Παραγωγής'),
        'ΠΕ88.03' => array('label' => 'Ζωικής Παραγωγής'),
        'ΠΕ88.04' => array('label' => 'Διατροφής'),
        'ΠΕ88.05' => array('label' => 'Φυσικού Περιβάλλοντος'),
        'ΠΕ89' => array('label' => 'Εφαρμοσμένων Τεχνών'),
        'ΠΕ89.01' => array('label' => 'Καλλιτεχνικών Σπουδών'),
        'ΠΕ89.02' => array('label' => 'Σχεδιασμού και Παραγωγής Προϊόντων'),
        'ΠΕ90' => array('label' => 'Ναυτικών Μαθημάτων'),
        'ΠΕ91' => array('label' => 'Θεατρικής Αγωγής'),
        'ΠΕ91.01' => array('label' => 'Θεατρικών Σπουδών'),
        'ΠΕ91.02' => array('label' => 'Δραματικής Τέχνης'),

        'ΤΕ01.04' => array('label' => 'Ψυκτικοί'),
        'ΤΕ01.06' => array('label' => 'Ηλεκτρολόγοι'),
        'ΤΕ01.07' => array('label' => 'Ηλεκτρονικοί'),
        'ΤΕ01.13' => array('label' => 'Προγραμματιστές Η/Υ'),
        'ΤΕ01.19' => array('label' => 'Κομμωτικής'),
        'ΤΕ01.20' => array('label' => 'Αισθητικής'),
        'ΤΕ01.25' => array('label' => 'Αργυροχρυσοχοΐας'),
        'ΤΕ01.26' => array('label' => 'Οδοντοτεχνικής'),
        'ΤΕ01.29' => array('label' => 'Βοηθών Ιατρικών & Βιολογικών Εργαστηρίων'),
        'ΤΕ01.30' => array('label' => 'Βοηθοί Βρεφοκόμων - Παιδοκόμων'),
        'ΤΕ01.31' => array('label' => 'Χειριστές Ιατρικών Συσκευών (Βοηθ. Ακτιν.)'),
        'ΤΕ02.01' => array('label' => 'Σχεδιαστές - Δομικοί'),
        'ΤΕ02.02' => array('label' => 'Μηχανολόγοι'),
        'ΤΕ02.04' => array('label' => 'Οικονομίας - Διοίκησης'),
        'ΤΕ02.05' => array('label' => 'Εφαρμοσμένων Τεχνών'),
        'ΤΕ02.07' => array('label' => 'Γεωπονίας'),
        'ΤΕ16' => array('label' => 'Μουσικής μη Ανώτατων Ιδρυμάτων')
    );
}

function teacherSpecialtyInfo($code)
{
    $registry = teacherSpecialties();
    return isset($registry[$code]) ? $registry[$code] : null;
}

function teacherSpecialtyLabel($code)
{
    $info = teacherSpecialtyInfo($code);
    return ($info && isset($info['label'])) ? $info['label'] : '';
}

function teacherSpecialtyDisplay($code)
{
    $label = teacherSpecialtyLabel($code);
    return $label !== '' ? $code . ' — ' . $label : $code;
}
