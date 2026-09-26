<?php
/**
 * Contract: καταργημένοι/παλαιοί κλάδοι δεν εμφανίζονται ως ενεργές
 * ειδικότητες στις αναθέσεις. Επιτρέπονται μόνο μέσα σε επεξηγηματικές
 * σημειώσεις προτεραιότητας του ισχύοντος κλάδου.
 */
$root = dirname(__DIR__);
require_once $root . '/includes/teaching-assignments-data.php';

$failures = array();
$rows = teachingAssignmentsData();
$legacyCodes = array('ΠΕ15');
$assignmentFields = array('A', 'B', 'C', 'special_codes');

foreach ($rows as $idx => $row) {
    if (!is_array($row)) continue;
    foreach ($assignmentFields as $field) {
        if (empty($row[$field]) || !is_array($row[$field])) continue;
        foreach ($row[$field] as $code) {
            if (in_array($code, $legacyCodes, true)) {
                $school = isset($row['school']) ? $row['school'] : '?';
                $subject = isset($row['subject']) ? $row['subject'] : '?';
                $failures[] = "row #$idx [$school / $subject] περιέχει τον παλαιό κλάδο '$code' στο πεδίο '$field'";
            }
        }
    }
}

$known = teachingAssignmentKnownSpecialties();
foreach ($legacyCodes as $legacyCode) {
    if (in_array($legacyCode, $known, true)) {
        $failures[] = "Ο παλαιός κλάδος '$legacyCode' εμφανίζεται στο teachingAssignmentKnownSpecialties()";
    }
}

// Regression για το συγκεκριμένο εύρημα ΕΝ.Ε.Ε.ΓΥ.-Λ.:
// ο ενεργός κλάδος είναι ΠΕ80 και η αναφορά στον πρώην ΠΕ15 είναι μόνο σημείωση.
$targetFound = false;
foreach ($rows as $row) {
    if (!is_array($row)) continue;
    if (($row['school'] ?? '') !== 'eneegyl_gymnasio') continue;
    if (($row['subject'] ?? '') !== 'Κοινωνική και Πολιτική Αγωγή') continue;
    $targetFound = true;
    $b = isset($row['B']) && is_array($row['B']) ? $row['B'] : array();
    $notes = isset($row['B_notes']) && is_array($row['B_notes']) ? $row['B_notes'] : array();
    if (!in_array('ΠΕ80', $b, true)) {
        $failures[] = "ΕΝ.Ε.Ε.ΓΥ.-Λ. / Κοινωνική και Πολιτική Αγωγή: λείπει ο ΠΕ80 από τη Β΄ ανάθεση";
    }
    if (in_array('ΠΕ15', $b, true)) {
        $failures[] = "ΕΝ.Ε.Ε.ΓΥ.-Λ. / Κοινωνική και Πολιτική Αγωγή: ο ΠΕ15 δεν πρέπει να είναι ενεργός κλάδος";
    }
    $note = isset($notes['ΠΕ80']) ? (string) $notes['ΠΕ80'] : '';
    if (strpos($note, 'Προτεραιότητα:') !== 0 || strpos($note, 'ΠΕ15') === false) {
        $failures[] = "ΕΝ.Ε.Ε.ΓΥ.-Λ. / Κοινωνική και Πολιτική Αγωγή: η προτεραιότητα πρώην ΠΕ15 πρέπει να βρίσκεται στη σημείωση του ΠΕ80";
    }
}
if (!$targetFound) {
    $failures[] = "Δεν βρέθηκε η αναμενόμενη εγγραφή ΕΝ.Ε.Ε.ΓΥ.-Λ. / Κοινωνική και Πολιτική Αγωγή";
}

if ($failures) {
    echo "Teaching assignments legacy-specialty contract: FAIL\n";
    foreach ($failures as $failure) echo "  - $failure\n";
    exit(1);
}

echo "Teaching assignments legacy-specialty contract: PASS — ΠΕ15 μόνο ως ιστορική σημείωση, όχι ως ενεργός κλάδος\n";
exit(0);
