<?php
/**
 * Εβδομαδιαίο ωρολόγιο πρόγραμμα μαθημάτων — Δευτεροβάθμια Εκπαίδευση.
 *
 * Πηγές / ισχύς 2026-2027:
 * - ΦΕΚ Β΄ 2132/09-04-2026, Υ.Α. 44257/Δ2: Ημερήσιο Γυμνάσιο.
 * - ΦΕΚ Β΄ 2106/09-04-2026, Υ.Α. 43684/Δ2: Ημερήσιο ΓΕΛ.
 * - ΦΕΚ Β΄ 2106/09-04-2026, Υ.Α. 43751/Δ2: Εσπερινό Γυμνάσιο.
 * - ΦΕΚ Β΄ 2102/09-04-2026, Υ.Α. 43706/Δ2: Εσπερινό ΓΕΛ.
 * - ΦΕΚ Β΄ 2104/09-04-2026, Υ.Α. 43820/Δ2: Καλλιτεχνικό Γυμνάσιο και Γενικό Καλλιτεχνικό Λύκειο.
 * - ΦΕΚ Β΄ 2107/09-04-2026, Υ.Α. 43787/Δ2: Μουσικό Γυμνάσιο και Γενικό Μουσικό Λύκειο.
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
        'kallitexniko_gymnasio' => array(
            'label' => 'Καλλιτεχνικό Γυμνάσιο',
            'grades' => array('Α΄', 'Β΄', 'Γ΄'),
            'tracks' => array(
                'visual' => 'Εικαστικών Τεχνών',
                'theatre' => 'Θεάτρου - Κινηματογράφου',
                'dance' => 'Χορού',
            ),
            'source' => 'ΦΕΚ Β΄ 2104/2026',
            'program' => array(
                'Α΄' => array('total' => 40, 'parts' => array('Γενική Παιδεία' => 26, 'Καλλιτεχνική Παιδεία' => 14)),
                'Β΄' => array('total' => 40, 'parts' => array('Γενική Παιδεία' => 26, 'Καλλιτεχνική Παιδεία' => 14)),
                'Γ΄' => array('total' => 40, 'parts' => array('Γενική Παιδεία' => 27, 'Καλλιτεχνική Παιδεία' => 13)),
            ),
        ),
        'kallitexniko_gel' => array(
            'label' => 'Γενικό Καλλιτεχνικό Λύκειο',
            'grades' => array('Α΄', 'Β΄', 'Γ΄'),
            'tracks' => array(
                'visual' => 'Εικαστικών Τεχνών',
                'theatre' => 'Θεάτρου - Κινηματογράφου',
                'dance' => 'Χορού',
            ),
            'source' => 'ΦΕΚ Β΄ 2104/2026',
            'program' => array(
                'Α΄' => array('total' => 40, 'parts' => array('Γενική Παιδεία' => 30, 'Καλλιτεχνική Παιδεία' => 10)),
                'Β΄' => array('total' => 40, 'parts' => array('Γενική Παιδεία' => 26, 'Ομάδα Προσανατολισμού' => 5, 'Καλλιτεχνική Παιδεία' => 9)),
                'Γ΄' => array('total' => 40, 'parts' => array('Γενική Παιδεία' => 11, 'Ομάδα Προσανατολισμού' => 18, 'Καλλιτεχνική Παιδεία' => 11)),
            ),
        ),
        'mousiko_gymnasio' => array(
            'label' => 'Μουσικό Γυμνάσιο',
            'grades' => array('Α΄', 'Β΄', 'Γ΄'),
            'source' => 'ΦΕΚ Β΄ 2107/2026',
            'program' => array(
                'Α΄' => array('total' => 42, 'parts' => array('Γενική Παιδεία' => 29, 'Μουσική Παιδεία' => 13)),
                'Β΄' => array('total' => 42, 'parts' => array('Γενική Παιδεία' => 29, 'Μουσική Παιδεία' => 13)),
                'Γ΄' => array('total' => 42, 'parts' => array('Γενική Παιδεία' => 28, 'Μουσική Παιδεία' => 14)),
            ),
        ),
        'mousiko_gel' => array(
            'label' => 'Γενικό Μουσικό Λύκειο',
            'grades' => array('Α΄', 'Β΄', 'Γ΄'),
            'source' => 'ΦΕΚ Β΄ 2107/2026',
            'program' => array(
                'Α΄' => array('total' => 42, 'parts' => array('Γενική Παιδεία' => 29, 'Μουσική Παιδεία' => 13)),
                'Β΄' => array('total' => 42, 'parts' => array('Γενική Παιδεία' => 26, 'Ομάδα Προσανατολισμού' => 5, 'Μουσική Παιδεία' => 11)),
                'Γ΄' => array('total' => 42, 'parts' => array('Γενική Παιδεία' => 12, 'Ομάδα Προσανατολισμού' => 18, 'Μουσική Παιδεία' => 12)),
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

        /* ---------------- ΚΑΛΛΙΤΕΧΝΙΚΑ & ΜΟΥΣΙΚΑ ΣΧΟΛΕΙΑ ---------------- */
        array('course_id'=>'kgym.glosiki','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Νεοελληνική Γλώσσα και Γραμματεία','subject'=>'Γλωσσική Διδασκαλία','hours'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.logotexnia','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Νεοελληνική Γλώσσα και Γραμματεία','subject'=>'Νεοελληνική Λογοτεχνία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.archaia_glossa','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','subject'=>'Αρχαία Ελληνική Γλώσσα','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.archaia_metafrasi','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','subject'=>'Αρχαία Ελληνικά Κείμενα από Μετάφραση','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.mathimatika','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μαθηματικά','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>4)),
        array('course_id'=>'kgym.fysiki','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική','hours'=>array('Α΄'=>1,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.ximeia','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Χημεία','hours'=>array('Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.viologia','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Βιολογία','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.geologia_geografia','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Γεωλογία - Γεωγραφία','hours'=>array('Α΄'=>1,'Β΄'=>1)),
        array('course_id'=>'kgym.istoria','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.kpa','slot_id'=>'kgym.c.semester_social','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Κοινωνική και Πολιτική Αγωγή','hours'=>array('Γ΄'=>1),'hours_display'=>array('Γ΄'=>'1 / –'),'period_hours'=>array('Γ΄'=>array('Α΄ τετράμηνο'=>1,'Β΄ τετράμηνο'=>0)),'note'=>'Διδάσκεται στο Α΄ τετράμηνο.'),
        array('course_id'=>'kgym.oikonomika','slot_id'=>'kgym.c.semester_social','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Οικονομικά','hours'=>array('Γ΄'=>1),'hours_display'=>array('Γ΄'=>'– / 1'),'period_hours'=>array('Γ΄'=>array('Α΄ τετράμηνο'=>0,'Β΄ τετράμηνο'=>1)),'note'=>'Διδάσκεται στο Β΄ τετράμηνο.'),
        array('course_id'=>'kgym.thriskeftika','slot_id'=>'kgym.religion_ethics','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative'),
        array('course_id'=>'kgym.ithiki','slot_id'=>'kgym.religion_ethics','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ηθική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative','condition'=>'Για μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.'),
        array('course_id'=>'kgym.agglika','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αγγλικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.deyteri_xeni','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Γαλλικά / Γερμανικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.pliroforiki','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Πληροφορική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.fysiki_agogi','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>2,'Β΄'=>1,'Γ΄'=>2)),
        array('course_id'=>'kgym.visual.eikastiko_ergastiri','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Εικαστικό εργαστήρι','hours'=>array('Α΄'=>8,'Β΄'=>9,'Γ΄'=>8)),
        array('course_id'=>'kgym.visual.istoria_texnis','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Ιστορία Τέχνης','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.visual.xoros','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Χορός','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.visual.theatro','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Θέατρο','hours'=>array('Α΄'=>2,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.visual.mousiki','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Μουσική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.theatre.ypokritiki_aftosxediasmos','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Υποκριτική - Αυτοσχεδιασμός','hours'=>array('Α΄'=>4,'Γ΄'=>3)),
        array('course_id'=>'kgym.theatre.ypokritiki','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Υποκριτική','hours'=>array('Β΄'=>3)),
        array('course_id'=>'kgym.theatre.agogi_proforikou','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Αγωγή Προφορικού Λόγου','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.theatre.theatrologia','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Θεατρολογία','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.theatre.theatriki_kinisi','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Θεατρική Κίνηση - Χορός','hours'=>array('Α΄'=>1,'Β΄'=>2,'Γ΄'=>1)),
        array('course_id'=>'kgym.theatre.kinimatografos','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Κινηματογράφος','hours'=>array('Α΄'=>3,'Β΄'=>3,'Γ΄'=>3)),
        array('course_id'=>'kgym.theatre.eikastika','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Εικαστικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.theatre.mousiki','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Μουσική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.theatre.dimiourgiki_grafi','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Εργαστήρι Δημιουργικής Γραφής','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.dance.klasikos','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Κλασικός Χορός','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>3)),
        array('course_id'=>'kgym.dance.sygxronos','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Σύγχρονος Χορός','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>4)),
        array('course_id'=>'kgym.dance.paradosiakos','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Παραδοσιακός Χορός','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.dance.mousiki','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Μουσική','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgym.dance.theatro','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Θέατρο','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgym.dance.eikastika','school'=>'kallitexniko_gymnasio','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Εικαστικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgel.general.archaia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Α΄'=>5,'Β΄'=>2)),
        array('course_id'=>'kgel.general.neoelliniki','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Νεοελληνική Γλώσσα και Λογοτεχνία','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>6),'note_by_grade'=>array('Γ΄'=>'5 ώρες για το κυρίως μάθημα και 1 ώρα για επίλυση αποριών, ανακεφαλαίωση κ.λπ.')),
        array('course_id'=>'kgel.general.algebra','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Μαθηματικά','subject'=>'Άλγεβρα','hours'=>array('Α΄'=>3,'Β΄'=>3)),
        array('course_id'=>'kgel.general.geometria','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Μαθηματικά','subject'=>'Γεωμετρία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'kgel.general.fysiki','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Φυσική','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'kgel.general.ximeia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Χημεία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'kgel.general.viologia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Βιολογία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'kgel.general.thriskeftika','slot_id'=>'kgel.general.religion_ethics','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative'),
        array('course_id'=>'kgel.general.ithiki','slot_id'=>'kgel.general.religion_ethics','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ηθική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative','condition'=>'Για μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.'),
        array('course_id'=>'kgel.general.istoria','slot_id'=>'kgel.c.general.orientation_choice','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2),'condition_by_grade'=>array('Γ΄'=>'Μόνο για μαθητές/ήτριες των Ομάδων Θετικών Σπουδών & Σπουδών Υγείας ή Σπουδών Οικονομίας & Πληροφορικής.')),
        array('course_id'=>'kgel.general.agglika','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αγγλικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1)),
        array('course_id'=>'kgel.general.fysiki_agogi','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgel.general.politiki_paideia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Πολιτική Παιδεία','hours'=>array('Α΄'=>2),'note'=>'Οικονομία, Πολιτικοί Θεσμοί και Αρχές Δικαίου και Κοινωνιολογία.'),
        array('course_id'=>'kgel.general.efarmoges_pliroforikis','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εφαρμογές Πληροφορικής','hours'=>array('Α΄'=>2)),
        array('course_id'=>'kgel.general.eisagogi_hy','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εισαγωγή στις Αρχές της Επιστήμης των Η/Υ','hours'=>array('Β΄'=>2)),
        array('course_id'=>'kgel.general.filosofia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φιλοσοφία','hours'=>array('Β΄'=>1)),
        array('course_id'=>'kgel.general.mathimatika_conditional','slot_id'=>'kgel.c.general.orientation_choice','school'=>'kallitexniko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>2),'condition'=>'Μόνο για μαθητές/ήτριες που επιλέγουν την Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών.'),
        array('course_id'=>'kgel.b.humanities.archaia','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Β΄'=>3)),
        array('course_id'=>'kgel.b.humanities.latinika','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Β΄'=>2)),
        array('course_id'=>'kgel.b.science.fysiki','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Φυσική','hours'=>array('Β΄'=>2)),
        array('course_id'=>'kgel.b.science.mathimatika','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Μαθηματικά','hours'=>array('Β΄'=>3)),
        array('course_id'=>'kgel.c.humanities.archaia','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'kgel.c.humanities.istoria','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Ιστορία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'kgel.c.humanities.latinika','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'kgel.c.health.mathimatika','slot_id'=>'kgel.c.health.field_choice','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6),'mode'=>'choice','condition'=>'Για μαθητές/ήτριες που επιλέγουν το 2ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'kgel.c.health.viologia','slot_id'=>'kgel.c.health.field_choice','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Βιολογία','hours'=>array('Γ΄'=>6),'mode'=>'choice','condition'=>'Για μαθητές/ήτριες που επιλέγουν το 3ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'kgel.c.health.fysiki','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Φυσική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'kgel.c.health.ximeia','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Χημεία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'kgel.c.econ.mathimatika','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'kgel.c.econ.pliroforiki','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Πληροφορική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'kgel.c.econ.oikonomia','school'=>'kallitexniko_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Οικονομία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'kgel.visual.zografiki','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Ζωγραφική','hours'=>array('Α΄'=>3,'Β΄'=>4,'Γ΄'=>3)),
        array('course_id'=>'kgel.visual.glyptiki','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Γλυπτική','hours'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgel.visual.xaraktiki','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Χαρακτική','hours'=>array('Α΄'=>2,'Β΄'=>1,'Γ΄'=>2)),
        array('course_id'=>'kgel.visual.fotografia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Φωτογραφία','hours'=>array('Α΄'=>1)),
        array('course_id'=>'kgel.visual.polymesa','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Πολυμέσα','hours'=>array('Β΄'=>1)),
        array('course_id'=>'kgel.visual.viomixaniko_sxedio','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Βιομηχανικό Σχέδιο','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'kgel.visual.ergastiria_polymesa','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Εργαστήρια Πολυμέσα','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'kgel.visual.istoria_texnis','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'visual','subject'=>'Ιστορία Τέχνης','hours'=>array('Α΄'=>1,'Β΄'=>1)),
        array('course_id'=>'kgel.theatre.kinisiologia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Κινησιολογία','hours'=>array('Α΄'=>1)),
        array('course_id'=>'kgel.theatre.kinisiologia_somatiki','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Κινησιολογία - Σωματική Έκφραση','hours'=>array('Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgel.theatre.ypokritiki','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Υποκριτική','hours'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgel.theatre.fonitiki_orthofonia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Φωνητική - Ορθοφωνία','hours'=>array('Α΄'=>1)),
        array('course_id'=>'kgel.theatre.dramatopoiisi','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Δραματοποίηση κειμένου','hours'=>array('Α΄'=>2)),
        array('course_id'=>'kgel.theatre.istoria_kinimatografou','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Ιστορία Κινηματογράφου','hours'=>array('Α΄'=>1)),
        array('course_id'=>'kgel.theatre.skinothesia_theatrou','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Στοιχεία Σκηνοθεσίας Θεάτρου','hours'=>array('Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgel.theatre.fonitiki_tragoudi','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Φωνητική - Τραγούδι','hours'=>array('Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'kgel.theatre.istoria_theatrou_kinimatografou','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Ιστορία Θεάτρου - Ιστορία Κινηματογράφου','hours'=>array('Β΄'=>1)),
        array('course_id'=>'kgel.theatre.istoria_theatrou','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Ιστορία Θεάτρου','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'kgel.theatre.aisthitiki_skinothesia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'theatre','subject'=>'Αισθητική Κινηματογράφου - Βασικές Αρχές Σκηνοθεσίας Κινηματογράφου','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'kgel.dance.klasikos','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Κλασικός Χορός','hours'=>array('Α΄'=>4,'Β΄'=>3,'Γ΄'=>3)),
        array('course_id'=>'kgel.dance.sygxronos','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Τεχνικές Σύγχρονου Χορού','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>3)),
        array('course_id'=>'kgel.dance.rythmos','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Ρυθμός - Μετρική - Κίνηση','hours'=>array('Α΄'=>2)),
        array('course_id'=>'kgel.dance.mousiki_metriki','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Μουσική - Μετρική - Αυτοσχεδιασμός','hours'=>array('Β΄'=>1)),
        array('course_id'=>'kgel.dance.istoria_texnis','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Ιστορία Τέχνης','hours'=>array('Β΄'=>1)),
        array('course_id'=>'kgel.dance.kinisiologia','school'=>'kallitexniko_gel','group'=>'Μαθήματα Καλλιτεχνικής Παιδείας','track'=>'dance','subject'=>'Κινησιολογία','hours'=>array('Γ΄'=>3)),
        array('course_id'=>'kgel.c.choice.eleythero_sxedio','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Ελεύθερο Σχέδιο','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.grammiko_sxedio','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Γραμμικό Σχέδιο','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.mme','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Μέσα Μαζικής Επικοινωνίας (ΜΜΕ)','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.video_dance','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Χορός για την Κάμερα (Video Dance)','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.keramiki','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Κεραμική','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.maska','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Εργαστήρι Μάσκας και Θεατρικών εξαρτημάτων','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.skinografia','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Σκηνογραφία - Ενδυματολογία','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.ntokimanter','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Ντοκιμαντέρ','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.theoria_texnis','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Θεωρία Τέχνης - Στοιχεία Αισθητικής','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.fotismos','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Φωτισμός θεάτρου','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.dimiourgiki_grafi','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Δημιουργική γραφή','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'kgel.c.choice.kinimatografiki_texnologia','slot_id'=>'kgel.c.art_choice','school'=>'kallitexniko_gel','group'=>'Μάθημα Επιλογής Καλλιτεχνικής Παιδείας (1 από 12)','subject'=>'Κινηματογραφική Τεχνολογία - Φωτογραφία Κινηματογράφου','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgym.glosiki','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Νεοελληνική Γλώσσα και Γραμματεία','subject'=>'Γλωσσική Διδασκαλία','hours'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.logotexnia','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Νεοελληνική Γλώσσα και Γραμματεία','subject'=>'Νεοελληνική Λογοτεχνία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.archaia_glossa','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','subject'=>'Αρχαία Ελληνική Γλώσσα','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.archaia_metafrasi','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','subject'=>'Αρχαία Ελληνικά Κείμενα από Μετάφραση','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.mathimatika','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μαθηματικά','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>4)),
        array('course_id'=>'mgym.fysiki','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική','hours'=>array('Α΄'=>1,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.ximeia','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Χημεία','hours'=>array('Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgym.viologia','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Βιολογία','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgym.geologia_geografia','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Γεωλογία - Γεωγραφία','hours'=>array('Α΄'=>1,'Β΄'=>1)),
        array('course_id'=>'mgym.istoria','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.kpa','slot_id'=>'mgym.c.semester_social','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Κοινωνική και Πολιτική Αγωγή','hours'=>array('Γ΄'=>1),'hours_display'=>array('Γ΄'=>'1 / –'),'period_hours'=>array('Γ΄'=>array('Α΄ τετράμηνο'=>1,'Β΄ τετράμηνο'=>0)),'note'=>'Διδάσκεται στο Α΄ τετράμηνο.'),
        array('course_id'=>'mgym.oikonomika','slot_id'=>'mgym.c.semester_social','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Οικονομικά','hours'=>array('Γ΄'=>1),'hours_display'=>array('Γ΄'=>'– / 1'),'period_hours'=>array('Γ΄'=>array('Α΄ τετράμηνο'=>0,'Β΄ τετράμηνο'=>1)),'note'=>'Διδάσκεται στο Β΄ τετράμηνο.'),
        array('course_id'=>'mgym.thriskeftika','slot_id'=>'mgym.religion_ethics','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative'),
        array('course_id'=>'mgym.ithiki','slot_id'=>'mgym.religion_ethics','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ηθική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative','condition'=>'Για μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.'),
        array('course_id'=>'mgym.agglika','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αγγλικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.deyteri_xeni','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Γαλλικά / Γερμανικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.pliroforiki','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Πληροφορική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgym.fysiki_agogi','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1),'note_by_grade'=>array('Α΄'=>'Η 2η ώρα είναι Ελληνικοί Χοροί.','Β΄'=>'Η 2η ώρα είναι Ελληνικοί Χοροί.')),
        array('course_id'=>'mgym.theatro','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Αισθητική Αγωγή','subject'=>'Θέατρο','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgym.kallitexnika','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Αισθητική Αγωγή','subject'=>'Καλλιτεχνικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgym.istoria_texnis','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Αισθητική Αγωγή','subject'=>'Ιστορία Τέχνης','hours'=>array('Α΄'=>1)),
        array('course_id'=>'mgym.music.evropaiki','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ευρωπαϊκή Μουσική - Θεωρία και Πράξη','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'mgym.music.evropaiki_armonia','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ευρωπαϊκή Μουσική - Θεωρία και Πράξη και Εισαγωγή στην Αρμονία','hours'=>array('Γ΄'=>3)),
        array('course_id'=>'mgym.music.elliniki_paradosiaki','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ελληνική Παραδοσιακή Μουσική - Θεωρία και Πράξη','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.music.istoria_mousikis','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ιστορία Μουσικής','hours'=>array('Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgym.music.kritiki_akroasi','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Κριτική Μουσική Ακρόαση','hours'=>array('Α΄'=>1)),
        array('course_id'=>'mgym.music.xorodia','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Χορωδία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.music.mousiko_synolo','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Μουσικό Σύνολο (Οργανοχρησίας ή άλλου είδους)','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgym.music.piano','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Πιάνο','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgym.music.tambouras','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ταμπουράς ή άλλο τοπικό παραδοσιακό όργανο αναφοράς','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgym.music.atomiko_epilogis','school'=>'mousiko_gymnasio','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ατομικό Όργανο Επιλογής','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgel.general.archaia','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Α΄'=>5,'Β΄'=>2)),
        array('course_id'=>'mgel.general.neoelliniki','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Νεοελληνική Γλώσσα και Λογοτεχνία','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>6),'note_by_grade'=>array('Γ΄'=>'5 ώρες για το κυρίως μάθημα και 1 ώρα για επίλυση αποριών, ανακεφαλαίωση κ.λπ.')),
        array('course_id'=>'mgel.general.algebra','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Μαθηματικά','subject'=>'Άλγεβρα','hours'=>array('Α΄'=>3,'Β΄'=>3)),
        array('course_id'=>'mgel.general.geometria','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Μαθηματικά','subject'=>'Γεωμετρία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'mgel.general.fysiki','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Φυσική','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'mgel.general.ximeia','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Χημεία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'mgel.general.viologia','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','section'=>'Φυσικές Επιστήμες','subject'=>'Βιολογία','hours'=>array('Α΄'=>2,'Β΄'=>2)),
        array('course_id'=>'mgel.general.thriskeftika','slot_id'=>'mgel.general.religion_ethics','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative'),
        array('course_id'=>'mgel.general.ithiki','slot_id'=>'mgel.general.religion_ethics','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ηθική','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),'mode'=>'alternative','condition'=>'Για μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.'),
        array('course_id'=>'mgel.general.istoria','slot_id'=>'mgel.c.general.orientation_choice','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2),'condition_by_grade'=>array('Γ΄'=>'Μόνο για μαθητές/ήτριες των Ομάδων Θετικών Σπουδών & Σπουδών Υγείας ή Σπουδών Οικονομίας & Πληροφορικής.')),
        array('course_id'=>'mgel.general.agglika','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αγγλικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1)),
        array('course_id'=>'mgel.general.fysiki_agogi','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>2)),
        array('course_id'=>'mgel.general.politiki_paideia','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Πολιτική Παιδεία','hours'=>array('Α΄'=>2),'note'=>'Οικονομία, Πολιτικοί Θεσμοί και Αρχές Δικαίου και Κοινωνιολογία.'),
        array('course_id'=>'mgel.general.efarmoges_pliroforikis','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εφαρμογές Πληροφορικής','hours'=>array('Α΄'=>1)),
        array('course_id'=>'mgel.general.eisagogi_hy','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εισαγωγή στις Αρχές της Επιστήμης των Η/Υ','hours'=>array('Β΄'=>2)),
        array('course_id'=>'mgel.general.filosofia','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φιλοσοφία','hours'=>array('Β΄'=>1)),
        array('course_id'=>'mgel.general.mathimatika_conditional','slot_id'=>'mgel.c.general.orientation_choice','school'=>'mousiko_gel','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>2),'condition'=>'Μόνο για μαθητές/ήτριες που επιλέγουν την Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών.'),
        array('course_id'=>'mgel.b.humanities.archaia','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Β΄'=>3)),
        array('course_id'=>'mgel.b.humanities.latinika','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Β΄'=>2)),
        array('course_id'=>'mgel.b.science.fysiki','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Φυσική','hours'=>array('Β΄'=>2)),
        array('course_id'=>'mgel.b.science.mathimatika','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Μαθηματικά','hours'=>array('Β΄'=>3)),
        array('course_id'=>'mgel.c.humanities.archaia','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'mgel.c.humanities.istoria','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Ιστορία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'mgel.c.humanities.latinika','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'mgel.c.health.mathimatika','slot_id'=>'mgel.c.health.field_choice','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6),'mode'=>'choice','condition'=>'Για μαθητές/ήτριες που επιλέγουν το 2ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'mgel.c.health.viologia','slot_id'=>'mgel.c.health.field_choice','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Βιολογία','hours'=>array('Γ΄'=>6),'mode'=>'choice','condition'=>'Για μαθητές/ήτριες που επιλέγουν το 3ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'mgel.c.health.fysiki','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Φυσική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'mgel.c.health.ximeia','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Χημεία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'mgel.c.econ.mathimatika','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'mgel.c.econ.pliroforiki','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Πληροφορική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'mgel.c.econ.oikonomia','school'=>'mousiko_gel','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Οικονομία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'mgel.music.armonia','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Αρμονία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgel.music.akoustikes','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ανάπτυξη Ακουστικών Ικανοτήτων','hours'=>array('Α΄'=>1,'Β΄'=>1),'note'=>'Περιλαμβάνει Γραφή καθ’ υπαγόρευση - Μουσική Ανάγνωση.'),
        array('course_id'=>'mgel.music.akoustikes_c','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ανάπτυξη Μουσικών Ακουστικών Ικανοτήτων','hours'=>array('Γ΄'=>1),'note'=>'Περιλαμβάνει Γραφή καθ’ υπαγόρευση - Μουσική Ανάγνωση και Αρμονία στο πιάνο.'),
        array('course_id'=>'mgel.music.elliniki_paradosiaki','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ελληνική Παραδοσιακή Μουσική','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgel.music.istoria','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ιστορία της Μουσικής','hours'=>array('Α΄'=>1)),
        array('course_id'=>'mgel.music.morfologia','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Μορφολογία','hours'=>array('Β΄'=>1)),
        array('course_id'=>'mgel.music.piano','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Υποχρεωτικό Ατομικό Μουσικό Όργανο: Πιάνο','hours'=>array('Α΄'=>1)),
        array('course_id'=>'mgel.music.atomiko','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Ατομικό Όργανο Επιλογής','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'mgel.music.synolo_organa','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Μουσικό Σύνολο (Οργανοχρησίας ή άλλου είδους)','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgel.music.synolo_ekfrasi','school'=>'mousiko_gel','group'=>'Μαθήματα Μουσικής Παιδείας','subject'=>'Μουσικό Σύνολο (Μουσικής Έκφρασης και Δημιουργίας)','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'mgel.a.choice.org_paradosiaka','slot_id'=>'mgel.a.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 6)','subject'=>'Οργανολογία Ελληνικών Παραδοσιακών Οργάνων','hours'=>array('Α΄'=>1),'mode'=>'choice'),
        array('course_id'=>'mgel.a.choice.org_symfonika','slot_id'=>'mgel.a.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 6)','subject'=>'Οργανολογία Μουσικών Οργάνων Συμφωνικής Ορχήστρας','hours'=>array('Α΄'=>1),'mode'=>'choice'),
        array('course_id'=>'mgel.a.choice.antistixi','slot_id'=>'mgel.a.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 6)','subject'=>'Στοιχεία Αντίστιξης','hours'=>array('Α΄'=>1),'mode'=>'choice'),
        array('course_id'=>'mgel.a.choice.pliroforiki_mousiki','slot_id'=>'mgel.a.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 6)','subject'=>'Εφαρμογές Πληροφορικής στη Μουσική','hours'=>array('Α΄'=>1),'mode'=>'choice'),
        array('course_id'=>'mgel.a.choice.mousiko_keimeno','slot_id'=>'mgel.a.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 6)','subject'=>'Επεξεργασία Μουσικού Κειμένου με Η/Υ','hours'=>array('Α΄'=>1),'mode'=>'choice'),
        array('course_id'=>'mgel.a.choice.ixolipsia1','slot_id'=>'mgel.a.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 6)','subject'=>'Στοιχειώδεις αρχές ηχοληψίας Ι','hours'=>array('Α΄'=>1),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.armonia_eidiko','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Αρμονία (Ειδικό)','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.akoustikes_elegxos','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Ανάπτυξη (Έλεγχος) Μουσικών Ακουστικών Ικανοτήτων','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.elliniki_paradosiaki','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Ελληνική Παραδοσιακή Μουσική','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.org_symfonika','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Οργανολογία Μουσικών Οργάνων Συμφωνικής Ορχήστρας','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.org_paradosiaka','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Οργανολογία Ελληνικών Παραδοσιακών Οργάνων','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.mousiko_keimeno','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Επεξεργασία Μουσικού Κειμένου με Η/Υ','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.atomiko_anforas','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Ατομικό Όργανο Επιλογής ή και Αναφοράς (Πιάνο - Ταμπουράς ή άλλο)','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.analysi_partitouras','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Ανάλυση Παρτιτούρας Ορχήστρας','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.choral','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Επεξεργασία Χορικού (Choral)','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
        array('course_id'=>'mgel.c.choice.ixolipsia2','slot_id'=>'mgel.c.music_choice','school'=>'mousiko_gel','group'=>'Μάθημα Επιλογής Μουσικής Παιδείας (1 από 10)','subject'=>'Στοιχειώδεις αρχές ηχοληψίας ΙΙ','hours'=>array('Γ΄'=>2),'mode'=>'choice'),
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
