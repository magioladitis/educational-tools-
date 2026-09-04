<?php
/**
 * Εβδομαδιαίο ωρολόγιο πρόγραμμα μαθημάτων — Γενική Δευτεροβάθμια Εκπαίδευση.
 *
 * Πηγές / ισχύς 2026-2027:
 * - ΦΕΚ Β΄ 2132/09-04-2026, Υ.Α. 44257/Δ2: Ημερήσιο Γυμνάσιο.
 * - ΦΕΚ Β΄ 2106/09-04-2026, Υ.Α. 43684/Δ2: Ημερήσιο ΓΕΛ.
 * - ΦΕΚ Β΄ 2106/09-04-2026, Υ.Α. 43751/Δ2: Εσπερινό Γυμνάσιο.
 * - ΦΕΚ Β΄ 2102/09-04-2026, Υ.Α. 43706/Δ2: Εσπερινό ΓΕΛ.
 *
 * Σχεδιασμός δεδομένων:
 * - Κάθε γραμμή έχει σταθερό `course_id` ώστε αργότερα να συνδεθεί με το
 *   dataset των αναθέσεων χωρίς αντιστοίχιση πάνω στον εμφανιζόμενο τίτλο.
 * - Τα `hours` είναι map ανά τάξη.
 * - Το `hours_display` υπερισχύει όταν το ΦΕΚ δεν δίνει έναν μοναδικό αριθμό
 *   (π.χ. 1/2 και 2/1 στο Β΄ Εσπερινού ΓΕΛ).
 * - Το `condition` διατηρεί τους όρους επιλογής/προσανατολισμού, ώστε να μη
 *   θεωρηθούν αργότερα όλες οι ώρες ταυτόχρονα διαθέσιμες στην ίδια τάξη.
 * - Το `slot_id` συνδέει αμοιβαία αποκλειόμενα μαθήματα που καταλαμβάνουν την
 *   ίδια ωριαία ζώνη (π.χ. Θρησκευτικά/Ηθική ή Μαθηματικά/Βιολογία).
 * - Το `period_hours` κρατά μεταβολές ανά τετράμηνο όταν το ΦΕΚ χρησιμοποιεί
 *   σημειογραφία όπως 1/2 ή 2/1.
 */

function weeklyTimetableSchoolTypes()
{
    return array(
        'gymnasio' => array(
            'label' => 'Ημερήσιο Γυμνάσιο',
            'grades' => array('Α΄', 'Β΄', 'Γ΄'),
            'source' => 'ΦΕΚ Β΄ 2132/2026',
            'program' => array(
                'Α΄' => array('total' => 33),
                'Β΄' => array('total' => 33),
                'Γ΄' => array('total' => 35),
            ),
        ),
        'esperino_gymnasio' => array(
            'label' => 'Εσπερινό Γυμνάσιο',
            'grades' => array('Α΄', 'Β΄', 'Γ΄'),
            'source' => 'ΦΕΚ Β΄ 2106/2026',
            'program' => array(
                'Α΄' => array('total' => 24),
                'Β΄' => array('total' => 25),
                'Γ΄' => array('total' => 25),
            ),
        ),
        'gel' => array(
            'label' => 'Ημερήσιο Γενικό Λύκειο (ΓΕΛ)',
            'grades' => array('Α΄', 'Β΄', 'Γ΄'),
            'source' => 'ΦΕΚ Β΄ 2106/2026',
            'program' => array(
                'Α΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 35)),
                'Β΄' => array('total' => 35, 'parts' => array('Γενική Παιδεία' => 30, 'Ομάδα Προσανατολισμού' => 5)),
                'Γ΄' => array('total' => 32, 'parts' => array('Γενική Παιδεία' => 14, 'Ομάδα Προσανατολισμού' => 18)),
            ),
        ),
        'esperino_gel' => array(
            'label' => 'Εσπερινό Γενικό Λύκειο (ΓΕΛ)',
            'grades' => array('Α΄', 'Β΄', 'Γ΄'),
            'source' => 'ΦΕΚ Β΄ 2102/2026',
            'program' => array(
                'Α΄' => array('total' => 25, 'parts' => array('Γενική Παιδεία' => 25)),
                'Β΄' => array('total' => 25, 'parts' => array('Γενική Παιδεία' => 20, 'Ομάδα Προσανατολισμού' => 5)),
                'Γ΄' => array('total' => 25, 'parts' => array('Γενική Παιδεία' => 7, 'Ομάδα Προσανατολισμού' => 18)),
            ),
        ),
    );
}

function weeklyTimetableRows()
{
    return array(
        /* ---------------- ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ---------------- */
        array('course_id'=>'gym.glosiki','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Νεοελληνική Γλώσσα και Γραμματεία','subject'=>'Γλωσσική Διδασκαλία','hours'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gym.logotexnia','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Νεοελληνική Γλώσσα και Γραμματεία','subject'=>'Νεοελληνική Λογοτεχνία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gym.archaia_glossa','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','subject'=>'Αρχαία Ελληνική Γλώσσα','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gym.archaia_metafrasi','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','subject'=>'Αρχαία Ελληνικά Κείμενα από Μετάφραση','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gym.mathimatika','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Μαθηματικά','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>4)),
        array('course_id'=>'gym.fysiki','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Φυσική','hours'=>array('Α΄'=>1,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gym.ximeia','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Χημεία','hours'=>array('Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'gym.viologia','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Βιολογία','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'gym.geologia_geografia','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Γεωλογία - Γεωγραφία','hours'=>array('Α΄'=>1,'Β΄'=>2)),
        array('course_id'=>'gym.istoria','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gym.thriskeftika','slot_id'=>'gym.religion_ethics','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2),'mode'=>'alternative'),
        array('course_id'=>'gym.ithiki','slot_id'=>'gym.religion_ethics','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Ηθική','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2),'mode'=>'alternative','condition'=>'Για μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.'),
        array('course_id'=>'gym.agglika','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Αγγλικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gym.deyteri_xeni','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'2η Ξένη Γλώσσα (Γαλλικά / Γερμανικά / Ιταλικά)','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2),'note'=>'Η Ιταλική διδάσκεται στα Γυμνάσια όπου εκπαιδευτικοί ΠΕ34 είναι τοποθετημένοι οριστικά ή προσωρινά από το σχολικό έτος 2016-2017.'),
        array('course_id'=>'gym.kpa','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Κοινωνική και Πολιτική Αγωγή','hours'=>array('Γ΄'=>3)),
        array('course_id'=>'gym.oikonomika','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Οικονομικά','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'gym.oikiaki_oikonomia','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Οικιακή Οικονομία','hours'=>array('Α΄'=>1)),
        array('course_id'=>'gym.fysiki_agogi','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gym.texnologia','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Τεχνολογία και Πληροφορική','subject'=>'Τεχνολογία','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'gym.pliroforiki','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Τεχνολογία και Πληροφορική','subject'=>'Πληροφορική','hours'=>array('Α΄'=>2,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'gym.mousiki','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Πολιτισμός και Δραστηριότητες','subject'=>'Μουσική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'gym.kallitexnika','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Πολιτισμός και Δραστηριότητες','subject'=>'Καλλιτεχνικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'gym.ergastiria_dexiotiton','school'=>'gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Εργαστήρια Δεξιοτήτων','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),

        /* ---------------- ΕΣΠΕΡΙΝΟ ΓΥΜΝΑΣΙΟ ---------------- */
        array('course_id'=>'egym.glosiki','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Νεοελληνική Γλώσσα και Γραμματεία','subject'=>'Γλωσσική Διδασκαλία','hours'=>array('Α΄'=>3,'Β΄'=>3,'Γ΄'=>2)),
        array('course_id'=>'egym.logotexnia','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Νεοελληνική Γλώσσα και Γραμματεία','subject'=>'Νεοελληνική Λογοτεχνία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'egym.archaia_glossa','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','subject'=>'Αρχαία Ελληνική Γλώσσα','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1)),
        array('course_id'=>'egym.archaia_metafrasi','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','section'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','subject'=>'Αρχαία Ελληνικά Κείμενα από Μετάφραση','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'egym.mathimatika','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Μαθηματικά','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>4)),
        array('course_id'=>'egym.fysiki','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Φυσική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>2)),
        array('course_id'=>'egym.ximeia','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Χημεία','hours'=>array('Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'egym.viologia','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Βιολογία','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'egym.geologia_geografia','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Γεωλογία - Γεωγραφία','hours'=>array('Α΄'=>2,'Β΄'=>1)),
        array('course_id'=>'egym.istoria','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'egym.kpa','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Κοινωνική και Πολιτική Αγωγή','hours'=>array('Β΄'=>1,'Γ΄'=>2)),
        array('course_id'=>'egym.oikonomika','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Οικονομικά','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'egym.thriskeftika','slot_id'=>'egym.religion_ethics','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative'),
        array('course_id'=>'egym.ithiki','slot_id'=>'egym.religion_ethics','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Ηθική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative','condition'=>'Για μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.'),
        array('course_id'=>'egym.agglika','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Αγγλικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'egym.pliroforiki','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Πληροφορική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'egym.ergastiria_dexiotiton','school'=>'esperino_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Εργαστήρια Δεξιοτήτων','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),

        /* ---------------- ΗΜΕΡΗΣΙΟ ΓΕΛ ---------------- */
        array('course_id'=>'gel.general.archaia','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Ελληνική Γλώσσα','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Α΄'=>5,'Β΄'=>2)),
        array('course_id'=>'gel.general.neoelliniki','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Ελληνική Γλώσσα','subject'=>'Νεοελληνική Γλώσσα και Λογοτεχνία','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>6),'note_by_grade'=>array('Γ΄'=>'5 ώρες για το κυρίως μάθημα και 1 ώρα για επίλυση αποριών, ανακεφαλαίωση κ.λπ.')),
        array('course_id'=>'gel.general.algebra','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Μαθηματικά','subject'=>'Άλγεβρα','hours'=>array('Α΄'=>3,'Β΄'=>3)),
        array('course_id'=>'gel.general.geometry','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Μαθηματικά','subject'=>'Γεωμετρία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'gel.general.fysiki','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Φυσική','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'gel.general.ximeia','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Χημεία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'gel.general.viologia','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Βιολογία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'gel.general.thriskeftika','slot_id'=>'gel.general.religion_ethics','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1),'mode'=>'alternative'),
        array('course_id'=>'gel.general.ithiki','slot_id'=>'gel.general.religion_ethics','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ηθική','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1),'mode'=>'alternative','condition'=>'Για μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.'),
        array('course_id'=>'gel.general.istoria','slot_id'=>'gel.c.general.orientation_choice','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2),'condition_by_grade'=>array('Γ΄'=>'Μόνο για μαθητές/ήτριες των Ομάδων Θετικών Σπουδών & Σπουδών Υγείας ή Σπουδών Οικονομίας & Πληροφορικής.')),
        array('course_id'=>'gel.general.agglika','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αγγλικά','hours'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'gel.general.deyteri_xeni','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'2η Ξένη Γλώσσα (Γαλλικά ή Γερμανικά)','hours'=>array('Α΄'=>2,'Β΄'=>1)),
        array('course_id'=>'gel.general.fysiki_agogi','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>3)),
        array('course_id'=>'gel.general.politiki_paideia','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Πολιτική Παιδεία (Οικονομία, Πολιτικοί Θεσμοί και Αρχές Δικαίου και Κοινωνιολογία)','hours'=>array('Α΄'=>2)),
        array('course_id'=>'gel.general.efarmoges_pliroforikis','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εφαρμογές Πληροφορικής','hours'=>array('Α΄'=>2)),
        array('course_id'=>'gel.general.eisagogi_aei_y','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εισαγωγή στις Αρχές της Επιστήμης των Η/Υ','hours'=>array('Β΄'=>2)),
        array('course_id'=>'gel.general.filosofia','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φιλοσοφία','hours'=>array('Β΄'=>2)),
        array('course_id'=>'gel.general.mathimatika_conditional','slot_id'=>'gel.c.general.orientation_choice','school'=>'gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>2),'condition'=>'Μόνο για μαθητές/ήτριες που επιλέγουν την Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών.'),

        array('course_id'=>'gel.b.humanities.archaia','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Β΄'=>3)),
        array('course_id'=>'gel.b.humanities.latinika','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Β΄'=>2)),
        array('course_id'=>'gel.b.science.fysiki','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Φυσική','hours'=>array('Β΄'=>2)),
        array('course_id'=>'gel.b.science.mathimatika','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Μαθηματικά','hours'=>array('Β΄'=>3)),

        array('course_id'=>'gel.c.humanities.archaia','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'gel.c.humanities.istoria','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Ιστορία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'gel.c.humanities.latinika','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'gel.c.health.mathimatika','slot_id'=>'gel.c.health.field_choice','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6),'mode'=>'choice','condition'=>'Για μαθητές/ήτριες που επιλέγουν το 2ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'gel.c.health.viologia','slot_id'=>'gel.c.health.field_choice','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Βιολογία','hours'=>array('Γ΄'=>6),'mode'=>'choice','condition'=>'Για μαθητές/ήτριες που επιλέγουν το 3ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'gel.c.health.fysiki','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Φυσική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'gel.c.health.ximeia','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Χημεία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'gel.c.econ.mathimatika','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'gel.c.econ.pliroforiki','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Πληροφορική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'gel.c.econ.oikonomia','school'=>'gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Οικονομία','hours'=>array('Γ΄'=>6)),

        /* ---------------- ΕΣΠΕΡΙΝΟ ΓΕΛ ---------------- */
        array('course_id'=>'egel.general.archaia','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Ελληνική Γλώσσα','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Α΄'=>2,'Β΄'=>1)),
        array('course_id'=>'egel.general.neoelliniki','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Ελληνική Γλώσσα','subject'=>'Νεοελληνική Γλώσσα και Λογοτεχνία','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>5)),
        array('course_id'=>'egel.general.algebra','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Μαθηματικά','subject'=>'Άλγεβρα','hours'=>array('Α΄'=>2,'Β΄'=>3)),
        array('course_id'=>'egel.general.geometry','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Μαθηματικά','subject'=>'Γεωμετρία','hours'=>array('Α΄'=>2,'Β΄'=>1)),
        array('course_id'=>'egel.general.fysiki','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Φυσική','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'egel.general.ximeia','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Χημεία','hours'=>array('Α΄'=>2,'Β΄'=>1),'hours_display'=>array('Β΄'=>'1 / 2'),'period_hours'=>array('Β΄'=>array('Α΄ τετράμηνο'=>1,'Β΄ τετράμηνο'=>2)),'note_by_grade'=>array('Β΄'=>'1 ώρα στο Α΄ τετράμηνο και 2 ώρες στο Β΄ τετράμηνο.')),
        array('course_id'=>'egel.general.viologia','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Βιολογία','hours'=>array('Α΄'=>2,'Β΄'=>2),'hours_display'=>array('Β΄'=>'2 / 1'),'period_hours'=>array('Β΄'=>array('Α΄ τετράμηνο'=>2,'Β΄ τετράμηνο'=>1)),'note_by_grade'=>array('Β΄'=>'2 ώρες στο Α΄ τετράμηνο και 1 ώρα στο Β΄ τετράμηνο.')),
        array('course_id'=>'egel.general.thriskeftika','slot_id'=>'egel.general.religion_ethics','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative'),
        array('course_id'=>'egel.general.ithiki','slot_id'=>'egel.general.religion_ethics','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ηθική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative','condition'=>'Για μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.'),
        array('course_id'=>'egel.general.istoria','slot_id'=>'egel.c.general.orientation_choice','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1),'condition_by_grade'=>array('Γ΄'=>'Μόνο για μαθητές/ήτριες των Ομάδων Θετικών Σπουδών & Σπουδών Υγείας ή Σπουδών Οικονομίας & Πληροφορικής.')),
        array('course_id'=>'egel.general.agglika','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αγγλικά','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'egel.general.fysiki_agogi','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>1,'Β΄'=>1)),
        array('course_id'=>'egel.general.politiki_paideia','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Πολιτική Παιδεία (Οικονομία, Πολιτικοί Θεσμοί και Αρχές Δικαίου και Κοινωνιολογία)','hours'=>array('Α΄'=>1)),
        array('course_id'=>'egel.general.efarmoges_pliroforikis','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εφαρμογές Πληροφορικής','hours'=>array('Α΄'=>2)),
        array('course_id'=>'egel.general.mathimatika_conditional','slot_id'=>'egel.c.general.orientation_choice','school'=>'esperino_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>1),'condition'=>'Μόνο για μαθητές/ήτριες που επιλέγουν την Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών.'),

        array('course_id'=>'egel.b.humanities.archaia','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Β΄'=>3)),
        array('course_id'=>'egel.b.humanities.latinika','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Β΄'=>2)),
        array('course_id'=>'egel.b.science.fysiki','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Φυσική','hours'=>array('Β΄'=>2)),
        array('course_id'=>'egel.b.science.mathimatika','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Μαθηματικά','hours'=>array('Β΄'=>3)),

        array('course_id'=>'egel.c.humanities.archaia','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'egel.c.humanities.istoria','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Ιστορία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'egel.c.humanities.latinika','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'egel.c.health.mathimatika','slot_id'=>'egel.c.health.field_choice','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6),'mode'=>'choice','condition'=>'Για μαθητές/ήτριες που επιλέγουν το 2ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'egel.c.health.viologia','slot_id'=>'egel.c.health.field_choice','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Βιολογία','hours'=>array('Γ΄'=>6),'mode'=>'choice','condition'=>'Για μαθητές/ήτριες που επιλέγουν το 3ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'egel.c.health.fysiki','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Φυσική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'egel.c.health.ximeia','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Χημεία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'egel.c.econ.mathimatika','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'egel.c.econ.pliroforiki','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Πληροφορική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'egel.c.econ.oikonomia','school'=>'esperino_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Οικονομία','hours'=>array('Γ΄'=>6)),
    );
}

function weeklyTimetableRowsFor($school, $grade)
{
    $result = array();
    foreach (weeklyTimetableRows() as $row) {
        if ($row['school'] !== $school) continue;
        if (!isset($row['hours'][$grade])) continue;
        $copy = $row;
        $copy['grade'] = $grade;
        $copy['hours_value'] = $row['hours'][$grade];
        $copy['hours_text'] = isset($row['hours_display'][$grade]) ? $row['hours_display'][$grade] : (string) $row['hours'][$grade];
        if (isset($row['condition_by_grade'][$grade])) $copy['condition_text'] = $row['condition_by_grade'][$grade];
        elseif (isset($row['condition'])) $copy['condition_text'] = $row['condition'];
        else $copy['condition_text'] = '';
        if (isset($row['note_by_grade'][$grade])) $copy['note_text'] = $row['note_by_grade'][$grade];
        elseif (isset($row['note'])) $copy['note_text'] = $row['note'];
        else $copy['note_text'] = '';
        $result[] = $copy;
    }
    return $result;
}

function weeklyTimetableProgramInfo($school, $grade)
{
    $schools = weeklyTimetableSchoolTypes();
    if (!isset($schools[$school]['program'][$grade])) return null;
    return $schools[$school]['program'][$grade];
}
