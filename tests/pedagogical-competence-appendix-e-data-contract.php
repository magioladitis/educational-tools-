<?php
$data = require __DIR__ . '/../includes/pedagogical-competence-data.php';
$programs = $data['appendix_named_programs'] ?? array();
$errors = array();

if (count($programs) !== 39) {
    $errors[] = 'Expected 39 named Appendix E programs (rows 14–52), got ' . count($programs);
}
for ($row = 14; $row <= 52; $row++) {
    if (!isset($programs[$row])) {
        $errors[] = 'Missing row ' . $row;
        continue;
    }
    foreach (array('section', 'title', 'provider', 'legal_basis', 'fek') as $field) {
        if (!isset($programs[$row][$field]) || trim((string)$programs[$row][$field]) === '') {
            $errors[] = 'Row ' . $row . ' missing ' . $field;
        }
    }
}
if (($programs[14]['title'] ?? '') !== 'Πρόγραμμα Μεταπτυχιακών Σπουδών «Σπουδές στην Εκπαίδευση»') {
    $errors[] = 'Row 14 title mismatch';
}
if (($programs[39]['provider'] ?? '') !== 'Οικονομικό Πανεπιστήμιο Αθηνών (ΟΠΑ)') {
    $errors[] = 'Row 39 provider mismatch';
}
if (($programs[52]['fek'] ?? '') !== '1173 Β΄/6-3-2022') {
    $errors[] = 'Row 52 FEK mismatch';
}
if (($data['rules']['named_special_program_requires_exact_match'] ?? false) !== true) {
    $errors[] = 'Named special-program exact-match safety rule missing';
}

if ($errors) {
    foreach ($errors as $error) {
        fwrite(STDERR, "FAIL | $error\n");
    }
    exit(1);
}
echo "PASS | Appendix E rows 14–52: 39/39 complete, no gaps, required fields present\n";
