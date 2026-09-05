<?php
/**
 * Ωρολόγιο πρόγραμμα Ε.Ε.Ε.ΕΚ.
 *
 * Α΄–Ε΄: Υ.Α. 57523/Γ6/04-06-2002, ΦΕΚ Β΄ 765/19-06-2002.
 * ΣΤ΄: άρθρο 48 παρ. 4 ν. 4415/2016 — πρακτική άσκηση με απόφαση Συλλόγου
 * Διδασκόντων και συμπλήρωση από μαθήματα προηγούμενων τάξεων βάσει ΕΠΕ.
 */
require_once __DIR__ . '/eeeek-regulatory-data.php';

function weeklyTimetableEeeekWorkshopChoiceOptions()
{
    $options = array();
    foreach (eeeekWorkshopSubjects() as $subject) {
        $options[] = array('label' => $subject, 'subject' => $subject, 'section' => 'Εργαστήρια');
    }
    return $options;
}

function weeklyTimetableEeeekRows()
{
    $workshopOptions = weeklyTimetableEeeekWorkshopChoiceOptions();
    $workshopNote = 'Η πραγματική εξειδίκευση εργαστηρίου καθορίζεται από τη σχολική μονάδα. Η λίστα αναθέσεων περιλαμβάνει τις 42 ονομασίες εργαστηρίων του ΦΕΚ Β΄ 1761/2018.';
    return array(
        array('course_id'=>'eeeek.lab.main','school'=>'eeeek','group'=>'Εξειδικεύσεις Εργαστηρίων','subject'=>'Κύρια εξειδίκευση εργαστηρίου','hours'=>array('Α΄'=>10,'Β΄'=>10,'Γ΄'=>11,'Δ΄'=>12,'Ε΄'=>15),'note'=>$workshopNote,'assignment_link_status'=>'choice_dependent','assignment_choice_options'=>$workshopOptions),
        array('course_id'=>'eeeek.lab.second','school'=>'eeeek','group'=>'Εξειδικεύσεις Εργαστηρίων','subject'=>'Β΄ εξειδίκευση εργαστηρίου','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2,'Δ΄'=>2,'Ε΄'=>2),'note'=>$workshopNote,'assignment_link_status'=>'choice_dependent','assignment_choice_options'=>$workshopOptions),
        array('course_id'=>'eeeek.lab.third','school'=>'eeeek','group'=>'Εξειδικεύσεις Εργαστηρίων','subject'=>'Γ΄ εξειδίκευση εργαστηρίου','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2,'Δ΄'=>2),'note'=>'Όπου δεν υπάρχει Γ΄ εξειδίκευση, οι ώρες κατανέμονται στην κύρια ή στη Β΄ εξειδίκευση.','assignment_link_status'=>'choice_dependent','assignment_choice_options'=>$workshopOptions),
        array('course_id'=>'eeeek.general.language','school'=>'eeeek','group'=>'Μαθήματα','subject'=>'Γλώσσα','hours'=>array('Α΄'=>3,'Β΄'=>3,'Γ΄'=>3,'Δ΄'=>2,'Ε΄'=>2)),
        array('course_id'=>'eeeek.general.math','school'=>'eeeek','group'=>'Μαθήματα','subject'=>'Μαθηματικά','hours'=>array('Α΄'=>3,'Β΄'=>3,'Γ΄'=>3,'Δ΄'=>3,'Ε΄'=>2)),
        array('course_id'=>'eeeek.general.music','school'=>'eeeek','group'=>'Μαθήματα','subject'=>'Μουσική','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>1,'Δ΄'=>1,'Ε΄'=>1)),
        array('course_id'=>'eeeek.general.pe','school'=>'eeeek','group'=>'Μαθήματα','subject'=>'Φυσική Αγωγή','hours'=>array('Α΄'=>3,'Β΄'=>3,'Γ΄'=>3,'Δ΄'=>2,'Ε΄'=>2)),
        array('course_id'=>'eeeek.general.informatics','school'=>'eeeek','group'=>'Μαθήματα','subject'=>'Πληροφορική','hours'=>array('Δ΄'=>2,'Ε΄'=>2)),
        array('course_id'=>'eeeek.general.social','school'=>'eeeek','group'=>'Μαθήματα','subject'=>'Κοινωνική Επαγγελματική Αγωγή','hours'=>array('Α΄'=>3,'Β΄'=>3,'Γ΄'=>3,'Δ΄'=>3,'Ε΄'=>3)),
        array('course_id'=>'eeeek.general.aesthetic','school'=>'eeeek','group'=>'Μαθήματα','subject'=>'Αισθητική Αγωγή','hours'=>array('Α΄'=>2,'Β΄'=>2,'Γ΄'=>2,'Δ΄'=>1,'Ε΄'=>1)),
        array(
            'course_id'=>'eeeek.st.dynamic_program',
            'school'=>'eeeek',
            'group'=>'ΣΤ΄ τάξη · Επαγγελματική εξειδίκευση',
            'subject'=>'Πρακτική άσκηση και εξατομικευμένη συμπλήρωση προγράμματος',
            'hours'=>array('ΣΤ΄'=>0),
            'hours_display'=>array('ΣΤ΄'=>'Μεταβλητό'),
            'note'=>'Η πρακτική άσκηση προσδιορίζεται με απόφαση του Συλλόγου Διδασκόντων στο πλαίσιο των εργαστηριακών μαθημάτων. Το πρόγραμμα συμπληρώνεται από μαθήματα προηγούμενων τάξεων, ανάλογα με τις ειδικές εκπαιδευτικές ανάγκες που προκύπτουν από την εκπαιδευτική αξιολόγηση/ΕΠΕ.',
            'assignment_link_status'=>'thematic_dependent',
            'assignment_section'=>'Εργαστήρια',
            'hours_mode'=>'dynamic',
        ),
    );
}
