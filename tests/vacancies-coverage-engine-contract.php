<?php
require_once __DIR__ . '/../includes/vacancies-coverage-engine.php';

function vcAssert($condition, $message) {
    if (!$condition) { fwrite(STDERR, "FAIL: $message\n"); exit(1); }
    echo "PASS: $message\n";
}

$stat48 = implode("\n", array(
    'Κωδικός Σχολείου;Ονομασία Σχολείου;Α.Μ.;Επώνυμο;Όνομα;Κωδικός Κύριας Ειδικότητας;Κωδικός 2ης Ειδικότητας;Υπόλοιπο Υποχρεωτικού Διδακτικού Ωραρίου',
    '="2401010";1ο ΓΥΜΝΑΣΙΟ;100001;ΔΟΚΙΜΗ;ΑΝΝΑ;ΠΕ03;;3',
    '="2401020";2ο ΓΥΜΝΑΣΙΟ;100001;ΔΟΚΙΜΗ;ΑΝΝΑ;ΠΕ03;;-1',
    '="2401030";3ο ΓΥΜΝΑΣΙΟ;100002;ΠΑΡΑΔΕΙΓΜΑ;ΝΙΚΟΣ;ΠΕ04.01;ΠΕ03;4',
    '="2401040";4ο ΓΥΜΝΑΣΙΟ;100003;ΜΗΔΕΝ;ΜΑΡΙΑ;ΠΕ02;;0'
));
list($ok48,$err48,$d48)=vacanciesCoverageParseStat48Text($stat48);
vcAssert($ok48, '4.8 parses');
vcAssert($d48['teacher_count']===2, 'only positive aggregate remaining teachers are candidates');
vcAssert(abs($d48['teachers']['100001']['remaining_hours']-2)<0.001, 'remaining hours are signed sum across placements');
vcAssert(count($d48['teachers']['100001']['placements'])===2, 'multiple school placements are retained');
vcAssert($d48['teachers']['100002']['secondary_code']==='ΠΕ03', 'secondary specialty is retained');

$stat51 = implode("\n", array(
    'Τάξη;Τομέας Σπουδών;Μάθημα;Εκτίμηση Κενών από myschool;Εκτίμηση Κενών από μονάδα;Ειδικότητες που έχουν το μάθημα ως Α ανάθεση;Ειδικότητες που έχουν το μάθημα ως Β ανάθεση',
    'Α;Γενικής Παιδείας Γυμνασίου;Μαθηματικά;6;6;ΠΕ03;ΠΕ04.01,ΠΕ86',
    'Β;Γενικής Παιδείας Γυμνασίου;Φυσική;2;2;ΠΕ04.01;ΠΕ03',
    'Γ;Γενικής Παιδείας Γυμνασίου;Ιστορία;3;0;ΠΕ02;ΠΕ01'
));
list($ok51,$err51,$d51)=vacanciesCoverageParseStat51Text($stat51);
vcAssert($ok51, '5.1 parses');
vcAssert($d51['unit_deficit_rows']===2 && abs($d51['unit_deficit_hours']-8)<0.001, '5.1 school-entered deficit rows/hours calculated');
vcAssert($d51['myschool_deficit_rows']===3 && abs($d51['myschool_deficit_hours']-11)<0.001, '5.1 myschool deficit rows/hours calculated independently');
vcAssert($d51['school_scoped']===false, '5.1 without school column is marked aggregate');

$allocation=array('schools'=>array(
    array('id'=>1,'name'=>'1ο ΓΥΜΝΑΣΙΟ','ministry_code'=>'2401010','entries'=>array(
        array('type'=>'vacancy','hours'=>3,'code'=>'ΠΕ03','label'=>'ΜΑΘΗΜΑΤΙΚΟΙ')
    )),
    array('id'=>2,'name'=>'2ο ΓΥΜΝΑΣΙΟ','ministry_code'=>'2401020','entries'=>array(
        array('type'=>'vacancy','hours'=>2,'code'=>'ΠΕ03','label'=>'ΜΑΘΗΜΑΤΙΚΟΙ')
    ))
));
$match=vacanciesCoverageMatch($d48,$d51,$allocation,'dde');
vcAssert($match['suggested_hours']>0, 'matcher proposes coverage');
vcAssert($match['recommendations'][0]['same_school']===true, 'same-school candidate is ranked first when otherwise valid');
vcAssert($match['suggested_hours']<=5.0, 'matcher never exceeds vacancy hours');
vcAssert($match['suggested_hours']<=$d48['remaining_hours'], 'matcher never exceeds available teacher hours');

// Location-first regression: an eligible B assignment at an existing school
// must outrank an A assignment that would require a new school movement.
$stat48Location = implode("\n", array(
    'Κωδικός Σχολείου;Ονομασία Σχολείου;Α.Μ.;Επώνυμο;Όνομα;Κωδικός Κύριας Ειδικότητας;Κωδικός 2ης Ειδικότητας;Υπόλοιπο Υποχρεωτικού Διδακτικού Ωραρίου',
    '="2402010";ΥΠΑΡΧΟΝ ΣΧΟΛΕΙΟ;200001;ΤΟΠΙΚΗ;ΕΛΕΝΗ;ΠΕ03;;2'
));
list($ok48Location,$err48Location,$d48Location)=vacanciesCoverageParseStat48Text($stat48Location);
vcAssert($ok48Location, 'location-first 4.8 fixture parses');
$stat51Location = implode("\n", array(
    'Τάξη;Τομέας Σπουδών;Μάθημα;Εκτίμηση Κενών από myschool;Εκτίμηση Κενών από μονάδα;Ειδικότητες που έχουν το μάθημα ως Α ανάθεση;Ειδικότητες που έχουν το μάθημα ως Β ανάθεση',
    'Α;;Μάθημα Β στο υπάρχον;2;2;ΠΕ02;ΠΕ03',
    'Α;;Μάθημα Α σε νέο;2;2;ΠΕ03;ΠΕ04.01'
));
list($ok51Location,$err51Location,$d51Location)=vacanciesCoverageParseStat51Text($stat51Location);
vcAssert($ok51Location, 'location-first 5.1 fixture parses');
$allocationLocation=array('schools'=>array(
    array('id'=>20,'name'=>'ΥΠΑΡΧΟΝ ΣΧΟΛΕΙΟ','ministry_code'=>'2402010','entries'=>array(
        array('type'=>'vacancy','hours'=>2,'code'=>'ΠΕ02','label'=>'ΚΕΝΟ Β')
    )),
    array('id'=>21,'name'=>'ΝΕΟ ΣΧΟΛΕΙΟ','ministry_code'=>'2402020','entries'=>array(
        array('type'=>'vacancy','hours'=>2,'code'=>'ΠΕ03','label'=>'ΚΕΝΟ Α')
    ))
));
$matchLocation=vacanciesCoverageMatch($d48Location,$d51Location,$allocationLocation,'dde');
vcAssert(count($matchLocation['recommendations'])>0, 'location-first matcher returns a recommendation');
vcAssert($matchLocation['recommendations'][0]['same_school']===true, 'existing-school eligible vacancy outranks new-school A assignment');
vcAssert($matchLocation['recommendations'][0]['ministry_code']==='2402010', 'existing school is selected before a new destination');

vcAssert(isset($match['assigned_destinations']), 'matcher tracks proposed destination schools per teacher');
$byTeacher=vacanciesCoverageRecommendationsByTeacher($match,$d48);
$bySchool=vacanciesCoverageRecommendationsBySchool($match);
$byVacancy=vacanciesCoverageRecommendationsByVacancy($match);
vcAssert(count($byTeacher)>0, 'teacher-grouped recommendation view is available');
vcAssert(count($bySchool)===2, 'school-grouped recommendation view includes destination schools');
vcAssert(count($byVacancy)===2, 'vacancy-grouped recommendation view includes individual vacancy rows');
foreach ($match['recommendations'] as $r) vcAssert(isset($r['new_destination']) && isset($r['continued_destination']), 'movement metadata is attached to each recommendation');

$matchMyschool=vacanciesCoverageMatch($d48,$d51,$allocation,'myschool');
vcAssert($matchMyschool['vacancy_source']==='myschool', 'matcher can use myschool 5.1 as vacancy source');
vcAssert(abs($matchMyschool['vacancy_hours']-11)<0.001, 'myschool matcher uses the myschool estimate total');
vcAssert($matchMyschool['suggested_hours']>0, 'myschool matcher proposes course-level coverage');

vcAssert(vacanciesCoverageCanonicalSpecialty('ΠΕ79.01.50')==='ΠΕ79.01', 'EAE .50 suffix normalizes to base specialty');
vcAssert(vacanciesCoverageCanonicalSpecialty('ΤΕ16.00.50')==='ΤΕ16', 'TE .00.50 suffix normalizes to base specialty');

echo "vacancies coverage engine contract: PASS\n";
