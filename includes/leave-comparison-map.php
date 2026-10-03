<?php
/**
 * Explicit mapping of leave rights that are meaningfully comparable between
 * permanent staff and substitutes / fixed-term (ΙΔΟΧ) staff.
 *
 * We intentionally do not auto-match every similarly named card: some rights
 * have a different legal nature and forcing a comparison would be misleading.
 */
return array(
    array('id' => 'kanoniki', 'title' => 'Κανονική άδεια', 'permanent' => 'kanoniki', 'substitute' => 'kanoniki', 'same_base_duration' => false),
    array('id' => 'anarrotiki', 'title' => 'Αναρρωτική άδεια', 'permanent' => 'anarrotiki', 'substitute' => 'anarrotiki', 'same_base_duration' => false),
    array('id' => 'gamos', 'title' => 'Άδεια γάμου / συμφώνου συμβίωσης', 'permanent' => 'gamos', 'substitute' => 'gamos', 'same_base_duration' => true),
    array('id' => 'thanatos', 'title' => 'Άδεια λόγω θανάτου συγγενούς', 'permanent' => 'thanatos', 'substitute' => 'thanatos', 'same_base_duration' => false),
    array('id' => 'aimodosia', 'title' => 'Άδεια αιμοδοσίας / λήψης αιμοπεταλίων', 'permanent' => 'aimodosia', 'substitute' => 'aimodosia', 'same_base_duration' => true),
    array('id' => 'exetaseon', 'title' => 'Άδεια εξετάσεων', 'permanent' => 'exetaseon', 'substitute' => 'exetaseon', 'same_base_duration' => true),
    array('id' => 'epistimoniki', 'title' => 'Άδεια για επιστημονικούς / επιμορφωτικούς λόγους', 'permanent' => 'epistimoniki', 'substitute' => 'epistimoniki', 'same_base_duration' => true),
    array('id' => 'nosimatos', 'title' => 'Ειδική άδεια νοσήματος', 'permanent' => 'nosimatos', 'substitute' => 'nosimatos', 'same_base_duration' => true),
    array('id' => 'anapirias', 'title' => 'Ειδική άδεια αναπηρίας', 'permanent' => 'anapirias', 'substitute' => 'anapirias', 'same_base_duration' => true),
    array('id' => 'eklogiko', 'title' => 'Άδεια για άσκηση εκλογικού δικαιώματος', 'permanent' => 'eklogiko', 'substitute' => 'eklogiko', 'same_base_duration' => true),
    array('id' => 'ivf', 'title' => 'Άδεια ιατρικώς υποβοηθούμενης αναπαραγωγής', 'permanent' => 'ivf', 'substitute' => 'ivf', 'same_base_duration' => true),
    array('id' => 'prenatal', 'title' => 'Προγεννητικές εξετάσεις', 'permanent' => 'progenitikos-elegxos', 'substitute' => 'prenatal', 'same_base_duration' => false),
    array('id' => 'mitrotitas', 'title' => 'Άδεια μητρότητας', 'permanent' => 'mitrotitas', 'substitute' => 'mitrotitas', 'same_base_duration' => false),
    array('id' => 'patrotitas', 'title' => 'Άδεια πατρότητας', 'permanent' => 'patrotitas', 'substitute' => 'patrotitas', 'same_base_duration' => true),
    array('id' => 'anatrofis', 'title' => 'Άδεια ανατροφής τέκνου', 'permanent' => 'enneamini-anatrofis', 'substitute' => 'anatrofis', 'same_base_duration' => false),
    array('id' => 'meiwmeno-anatrofis', 'title' => 'Μειωμένο διδακτικό ωράριο για ανατροφή τέκνου', 'permanent' => 'meiwmeno-anatrofis', 'substitute' => 'meiwmeno-anatrofis', 'same_base_duration' => true),
    array('id' => 'monogoneas', 'title' => 'Άδεια μονογονέα', 'permanent' => 'monogoneas-2026', 'substitute' => 'monogoneiki', 'same_base_duration' => true),
    array('id' => 'astheneia-teknou', 'title' => 'Άδεια ασθένειας τέκνου', 'permanent' => 'astheneia-teknou', 'substitute' => 'astheneia-teknou', 'same_base_duration' => true),
    array('id' => 'sxoliki-epidosi', 'title' => 'Άδεια παρακολούθησης σχολικής επίδοσης τέκνων', 'permanent' => 'sxoliki-epidosi', 'substitute' => 'sxoliki-epidosi', 'same_base_duration' => true),
);
