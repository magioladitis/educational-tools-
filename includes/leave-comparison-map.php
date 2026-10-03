<?php
/**
 * Explicit mapping of leave rights that are meaningfully comparable between
 * permanent staff and substitutes / fixed-term (ΙΔΟΧ) staff.
 *
 * We intentionally do not auto-match every similarly named card: some rights
 * have a different legal nature and forcing a comparison would be misleading.
 */
return array(
    array('id' => 'kanoniki', 'title' => 'Κανονική άδεια', 'permanent' => 'kanoniki', 'substitute' => 'kanoniki'),
    array('id' => 'anarrotiki', 'title' => 'Αναρρωτική άδεια', 'permanent' => 'anarrotiki', 'substitute' => 'anarrotiki'),
    array('id' => 'gamos', 'title' => 'Άδεια γάμου / συμφώνου συμβίωσης', 'permanent' => 'gamos', 'substitute' => 'gamos'),
    array('id' => 'thanatos', 'title' => 'Άδεια λόγω θανάτου συγγενούς', 'permanent' => 'thanatos', 'substitute' => 'thanatos'),
    array('id' => 'aimodosia', 'title' => 'Άδεια αιμοδοσίας / λήψης αιμοπεταλίων', 'permanent' => 'aimodosia', 'substitute' => 'aimodosia'),
    array('id' => 'exetaseon', 'title' => 'Άδεια εξετάσεων', 'permanent' => 'exetaseon', 'substitute' => 'exetaseon'),
    array('id' => 'epistimoniki', 'title' => 'Άδεια για επιστημονικούς / επιμορφωτικούς λόγους', 'permanent' => 'epistimoniki', 'substitute' => 'epistimoniki'),
    array('id' => 'nosimatos', 'title' => 'Ειδική άδεια νοσήματος', 'permanent' => 'nosimatos', 'substitute' => 'nosimatos'),
    array('id' => 'anapirias', 'title' => 'Ειδική άδεια αναπηρίας', 'permanent' => 'anapirias', 'substitute' => 'anapirias'),
    array('id' => 'eklogiko', 'title' => 'Άδεια για άσκηση εκλογικού δικαιώματος', 'permanent' => 'eklogiko', 'substitute' => 'eklogiko'),
    array('id' => 'ivf', 'title' => 'Άδεια ιατρικώς υποβοηθούμενης αναπαραγωγής', 'permanent' => 'ivf', 'substitute' => 'ivf'),
    array('id' => 'prenatal', 'title' => 'Προγεννητικές εξετάσεις', 'permanent' => 'progenitikos-elegxos', 'substitute' => 'prenatal'),
    array('id' => 'mitrotitas', 'title' => 'Άδεια μητρότητας', 'permanent' => 'mitrotitas', 'substitute' => 'mitrotitas'),
    array('id' => 'patrotitas', 'title' => 'Άδεια πατρότητας', 'permanent' => 'patrotitas', 'substitute' => 'patrotitas'),
    array('id' => 'anatrofis', 'title' => 'Άδεια ανατροφής τέκνου', 'permanent' => 'enneamini-anatrofis', 'substitute' => 'anatrofis'),
    array('id' => 'meiwmeno-anatrofis', 'title' => 'Μειωμένο διδακτικό ωράριο για ανατροφή τέκνου', 'permanent' => 'meiwmeno-anatrofis', 'substitute' => 'meiwmeno-anatrofis'),
    array('id' => 'monogoneas', 'title' => 'Άδεια μονογονέα', 'permanent' => 'monogoneas-2026', 'substitute' => 'monogoneiki'),
    array('id' => 'astheneia-teknou', 'title' => 'Άδεια ασθένειας τέκνου', 'permanent' => 'astheneia-teknou', 'substitute' => 'astheneia-teknou'),
    array('id' => 'sxoliki-epidosi', 'title' => 'Άδεια παρακολούθησης σχολικής επίδοσης τέκνων', 'permanent' => 'sxoliki-epidosi', 'substitute' => 'sxoliki-epidosi'),
);
