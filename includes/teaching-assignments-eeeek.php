<?php
/**
 * Αναθέσεις μαθημάτων Ε.Ε.Ε.ΕΚ.
 * Υ.Α. 71105/Δ3/04-05-2018 — ΦΕΚ Β΄ 1761/17-05-2018.
 */
require_once __DIR__ . '/eeeek-regulatory-data.php';

$rows = array(
    array('school'=>'eeeek','grade'=>'','section'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Γλώσσα','A'=>array('ΠΕ02','ΠΕ70','ΠΕ71')),
    array('school'=>'eeeek','grade'=>'','section'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μαθηματικά','A'=>array('ΠΕ03','ΠΕ70','ΠΕ71')),
    array('school'=>'eeeek','grade'=>'','section'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Μουσική','A'=>array('ΠΕ79.01'),'B'=>array('ΤΕ16')),
    array('school'=>'eeeek','grade'=>'','section'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Φυσική Αγωγή','A'=>array('ΠΕ11')),
    array('school'=>'eeeek','grade'=>'','section'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Πληροφορική','A'=>array('ΠΕ86')),
    array('school'=>'eeeek','grade'=>'','section'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Κοινωνική Επαγγελματική Αγωγή','A'=>array('ΠΕ02','ΠΕ70','ΠΕ71','ΠΕ78','ΠΕ87.06')),
    array('school'=>'eeeek','grade'=>'','section'=>'Μαθήματα Γενικής Παιδείας','subject'=>'Αισθητική Αγωγή','A'=>array('ΠΕ08'),'B'=>array('ΠΕ89.01'),'B_notes'=>array('ΠΕ89.01'=>'κατά προτεραιότητα σε εκπαιδευτικούς με πτυχία που αντιστοιχούν στον πρώην κλάδο ΠΕ18.26')),
);

foreach (eeeekWorkshopDefinitions() as $definition) {
    $row = array(
        'school' => 'eeeek',
        'grade' => '',
        'section' => 'Εργαστήρια',
        'subject' => $definition['subject'],
    );
    foreach (array('A','B','A_notes','B_notes') as $key) {
        if (!empty($definition[$key])) $row[$key] = $definition[$key];
    }
    $rows[] = $row;
}

return $rows;
