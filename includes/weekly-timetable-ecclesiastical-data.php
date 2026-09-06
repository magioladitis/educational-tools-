<?php
/**
 * Πρότυπα Εκκλησιαστικά Σχολεία — εβδομαδιαίο ωρολόγιο πρόγραμμα.
 *
 * Νομοθετική βάση:
 * - Υ.Α. 118380/Θ2/21-09-2021, ΦΕΚ Β΄ 4438/25-09-2021.
 * - Διορθώσεις σφαλμάτων ΦΕΚ Β΄ 4569/02-10-2021 και Β΄ 4728/12-10-2021.
 * - Υ.Α. 63979/Θ2/30-05-2022, ΦΕΚ Β΄ 2781/03-06-2022.
 * - Υ.Α. 110640/Θ2/11-09-2025, ΦΕΚ Β΄ 4881/15-09-2025
 *   (ισχύς από το σχολικό έτος 2025-2026 για το Πρότυπο Εκκλησιαστικό Γυμνάσιο).
 *
 * Η διασύνδεση με το dataset αναθέσεων είναι ενεργή. Οι ειδικές περιπτώσεις
 * Βυζαντινής Μουσικής και Εικονογραφίας διατηρούν qualification metadata στο
 * dataset αναθέσεων, ενώ η επιλογή Ρωσικών/Αραβικών/Τουρκικών στο Γυμνάσιο
 * παραμένει ρητό regulatory gap ως προς τον κλάδο ανάθεσης.
 */

function weeklyTimetableEcclesiasticalRows()
{
    $rows = array(
        // -----------------------------------------------------------------
        // Πρότυπο Εκκλησιαστικό Γυμνάσιο — κοινό πρόγραμμα (30 ώρες)
        // -----------------------------------------------------------------
        array('course_id'=>'pes.gym.glossiki','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Γλωσσική Διδασκαλία','hours'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.logotexnia','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Νεοελληνική Λογοτεχνία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.archaia_glossa','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Αρχαία Ελληνική Γλώσσα','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.archaia_metafrasi','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Αρχαία Ελληνικά Κείμενα από μετάφραση','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.mathimatika','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Μαθηματικά','hours'=>array('Α΄'=>4,'Β΄'=>4,'Γ΄'=>4)),
        array('course_id'=>'pes.gym.fysiki','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Φυσική','hours'=>array('Α΄'=>1,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.chimeia','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Χημεία','hours'=>array('Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'pes.gym.biologia','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Βιολογία','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'pes.gym.geologia_geografia','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Γεωλογία - Γεωγραφία','hours'=>array('Α΄'=>1,'Β΄'=>2)),
        array('course_id'=>'pes.gym.oikiaki_oikonomia','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Οικιακή Οικονομία','hours'=>array('Α΄'=>1)),
        array('course_id'=>'pes.gym.istoria','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Ιστορία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.koinoniki_politiki','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Κοινωνική και Πολιτική Αγωγή','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'pes.gym.oikonomika','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Οικονομικά','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'pes.gym.thriskeftika','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.agglika','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Αγγλικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.deuteri_xeni','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Γαλλικά / Γερμανικά / Ρωσικά / Αραβικά / Τουρκικά','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2),'mode'=>'choice','condition'=>'Μία από τις προβλεπόμενες ξένες γλώσσες.','assignment_link_status'=>'regulatory_gap','assignment_link_note'=>'Οι Γαλλικές/Γερμανικές αντιστοιχούν σε ΠΕ05/ΠΕ07. Για Ρωσικά, Αραβικά και Τουρκικά δεν αποδίδεται κλάδος χωρίς ρητή ισχύουσα διάταξη ανάθεσης.'),
        array('course_id'=>'pes.gym.fysiki_agogi','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.pliroforiki','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Πληροφορική','hours'=>array('Α΄'=>2,'Β΄'=>1,'Γ΄'=>1)),
        array('course_id'=>'pes.gym.ergastiria_deksiotiton','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Κοινό πρόγραμμα','subject'=>'Εργαστήρια Δεξιοτήτων','hours'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1)),

        // Πρότυπο Εκκλησιαστικό Γυμνάσιο — Θρησκευτική Εξειδίκευση (6 ώρες)
        array('course_id'=>'pes.gym.agia_grafi','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Θέματα από την Αγία Γραφή','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.gym.leitourgiki_zoi','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Λειτουργική Ζωή της Εκκλησίας','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.gym.ekklisiastiki_istoria_pateres','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Εκκλησιαστική Ιστορία και Πατέρες και Θεολόγοι της Εκκλησίας','hours'=>array('Γ΄'=>2)),
        array('course_id'=>'pes.gym.vyzantini_mousiki','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Βυζαντινή Μουσική','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),
        array('course_id'=>'pes.gym.eikonografia','school'=>'protypo_ekklisiastiko_gymnasio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Εικονογραφία','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2)),

        // -----------------------------------------------------------------
        // Πρότυπο Εκκλησιαστικό Λύκειο — Α΄ τάξη (29 + 6 = 35)
        // -----------------------------------------------------------------
        array('course_id'=>'pes.lykeio.a.archaia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Α΄'=>4)),
        array('course_id'=>'pes.lykeio.a.neoelliniki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Νέα Ελληνική Γλώσσα και Λογοτεχνία','hours'=>array('Α΄'=>4)),
        array('course_id'=>'pes.lykeio.a.thriskeftika','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.istoria','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.algebra','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Άλγεβρα','hours'=>array('Α΄'=>3)),
        array('course_id'=>'pes.lykeio.a.geometria','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Γεωμετρία','hours'=>array('Α΄'=>1)),
        array('course_id'=>'pes.lykeio.a.xeni_glossa','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','hours'=>array('Α΄'=>2),'mode'=>'choice','assignment_link_status'=>'choice_dependent','assignment_choice_options'=>array(array('label'=>'Αγγλικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ06')),array('label'=>'Γαλλικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ05')),array('label'=>'Γερμανικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ07')))),
        array('course_id'=>'pes.lykeio.a.fysiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.chimeia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Χημεία','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.biologia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Βιολογία','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.fysiki_agogi','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.politiki_paideia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Πολιτική Παιδεία (Οικονομία, Πολιτικοί Θεσμοί και Αρχές Δικαίου και Κοινωνιολογία)','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.efarmoges_pliroforikis','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εφαρμογές Πληροφορικής','hours'=>array('Α΄'=>1)),
        array('course_id'=>'pes.lykeio.a.leitourgiki_teletourgiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Στοιχεία Λειτουργικής και Τελετουργικής','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.vyzantini_mousiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Βυζαντινή Μουσική','hours'=>array('Α΄'=>2)),
        array('course_id'=>'pes.lykeio.a.eikonografia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Εικονογραφία','hours'=>array('Α΄'=>2)),

        // -----------------------------------------------------------------
        // Πρότυπο Εκκλησιαστικό Λύκειο — Β΄ τάξη (24 + 6 + 5 = 35)
        // -----------------------------------------------------------------
        array('course_id'=>'pes.lykeio.b.archaia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.neoelliniki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Νέα Ελληνική Γλώσσα και Λογοτεχνία','hours'=>array('Β΄'=>4)),
        array('course_id'=>'pes.lykeio.b.algebra','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Άλγεβρα','hours'=>array('Β΄'=>3)),
        array('course_id'=>'pes.lykeio.b.geometria','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Γεωμετρία','hours'=>array('Β΄'=>1)),
        array('course_id'=>'pes.lykeio.b.fysiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.chimeia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Χημεία','hours'=>array('Β΄'=>1)),
        array('course_id'=>'pes.lykeio.b.biologia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Βιολογία','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.arxes_ypologiston','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Εισαγωγή στις Αρχές της Επιστήμης των Η/Υ','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.istoria','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.filosofia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φιλοσοφία','hours'=>array('Β΄'=>1)),
        array('course_id'=>'pes.lykeio.b.thriskeftika','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.xeni_glossa','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','hours'=>array('Β΄'=>1),'mode'=>'choice','assignment_link_status'=>'choice_dependent','assignment_choice_options'=>array(array('label'=>'Αγγλικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ06')),array('label'=>'Γαλλικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ05')),array('label'=>'Γερμανικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ07')))),
        array('course_id'=>'pes.lykeio.b.fysiki_agogi','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Β΄'=>1)),
        array('course_id'=>'pes.lykeio.b.dogmatiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Θέματα Δογματικής Θεολογίας','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.vyzantini_mousiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Βυζαντινή Μουσική','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.eikonografia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Εικονογραφία','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.hum.archaia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνική Γλώσσα και Γραμματεία','hours'=>array('Β΄'=>3)),
        array('course_id'=>'pes.lykeio.b.hum.latinika','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Β΄'=>2)),
        array('course_id'=>'pes.lykeio.b.sci.fysiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Φυσική','hours'=>array('Β΄'=>3)),
        array('course_id'=>'pes.lykeio.b.sci.mathimatika','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών','subject'=>'Μαθηματικά','hours'=>array('Β΄'=>2)),

        // -----------------------------------------------------------------
        // Πρότυπο Εκκλησιαστικό Λύκειο — Γ΄ τάξη (11 + 6 + 18 = 35)
        // -----------------------------------------------------------------
        array('course_id'=>'pes.lykeio.g.thriskeftika','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Θρησκευτικά','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'pes.lykeio.g.neoelliniki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Νεοελληνική Γλώσσα και Λογοτεχνία','hours'=>array('Γ΄'=>6),'note'=>'5 ώρες διδασκαλία + 1 ώρα για ανακεφαλαίωση/ερωτήσεις, σύμφωνα με το ωρολόγιο.'),
        array('course_id'=>'pes.lykeio.g.mathimatika_gen','slot_id'=>'pes.lykeio.g.general.choice','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>2),'mode'=>'alternative','condition'=>'Για τους/τις μαθητές/ήτριες της Ομάδας Προσανατολισμού Ανθρωπιστικών Σπουδών.'),
        array('course_id'=>'pes.lykeio.g.istoria_gen','slot_id'=>'pes.lykeio.g.general.choice','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ιστορία','hours'=>array('Γ΄'=>2),'mode'=>'alternative','condition'=>'Για τους/τις μαθητές/ήτριες των Ομάδων Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας ή Σπουδών Οικονομίας και Πληροφορικής.'),
        array('course_id'=>'pes.lykeio.g.xeni_glossa','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','hours'=>array('Γ΄'=>1),'mode'=>'choice','assignment_link_status'=>'choice_dependent','assignment_choice_options'=>array(array('label'=>'Αγγλικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ06')),array('label'=>'Γαλλικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ05')),array('label'=>'Γερμανικά','subject'=>'Ξένη Γλώσσα (Αγγλικά ή Γαλλικά ή Γερμανικά)','codes'=>array('ΠΕ07')))),
        array('course_id'=>'pes.lykeio.g.fysiki_agogi','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','hours'=>array('Γ΄'=>1)),
        array('course_id'=>'pes.lykeio.g.ithiki_poimantiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Θέματα Χριστιανικής Ηθικής και Ποιμαντικής Θεολογίας','hours'=>array('Γ΄'=>2)),
        array('course_id'=>'pes.lykeio.g.vyzantini_mousiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Βυζαντινή Μουσική','hours'=>array('Γ΄'=>2)),
        array('course_id'=>'pes.lykeio.g.eikonografia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Μαθήματα Θρησκευτικής Εξειδίκευσης','subject'=>'Εικονογραφία','hours'=>array('Γ΄'=>2)),
        array('course_id'=>'pes.lykeio.g.hum.archaia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Αρχαία Ελληνικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'pes.lykeio.g.hum.istoria','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Ιστορία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'pes.lykeio.g.hum.latinika','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Ανθρωπιστικών Σπουδών','subject'=>'Λατινικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'pes.lykeio.g.sci.mathimatika','slot_id'=>'pes.lykeio.g.science_field_choice','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6),'mode'=>'alternative','condition'=>'Για το 2ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'pes.lykeio.g.sci.biologia','slot_id'=>'pes.lykeio.g.science_field_choice','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Βιολογία','hours'=>array('Γ΄'=>6),'mode'=>'alternative','condition'=>'Για το 3ο Επιστημονικό Πεδίο.'),
        array('course_id'=>'pes.lykeio.g.sci.fysiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Φυσική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'pes.lykeio.g.sci.chimeia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Θετικών Σπουδών και Σπουδών Υγείας','subject'=>'Χημεία','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'pes.lykeio.g.econ.mathimatika','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Μαθηματικά','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'pes.lykeio.g.econ.pliroforiki','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Πληροφορική','hours'=>array('Γ΄'=>6)),
        array('course_id'=>'pes.lykeio.g.econ.oikonomia','school'=>'protypo_ekklisiastiko_lykeio','group'=>'Ομάδα Προσανατολισμού Σπουδών Οικονομίας και Πληροφορικής','subject'=>'Οικονομία','hours'=>array('Γ΄'=>6)),
    );

    return $rows;
}
