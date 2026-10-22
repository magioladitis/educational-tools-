# Έλεγχος καναλιών υποβολής / συνθέτη — 22/10/2026

Βάση: 3.22.198 SCENARIOS VALIDATED FULL.

## Νέος έλεγχος
`tests/transfer-v322198-submission-matrix-regression.js`: **PASS — 59 assertions, 12 συνδυασμοί εγγράφων**.

Σενάρια: κενή λίστα, αυτεπάγγελτα, μόνο email, αποκλεισμένη συνυπηρέτηση, μόνο mydocs, μεικτή υποβολή, κλειστός φυσικός φάκελος, ΚΕΠΑ, φυσικός φάκελος + email, ειδικό mydocs + φάκελος, mydocs + ανενεργή συνυπηρέτηση, μεικτό σύνολο τριών καναλιών.

Διαδρομές με διαθέσιμα πρότυπα ελέγχθηκαν για τους οκτώ τύπους Δευτεροβάθμιας: `region`, `local`, `smeae`, `eae_sensory`, `kedasy`, `music`, `art`, `intercultural`. Ελέγχθηκαν παραγωγή θέματος/σώματος και mailto χωρίς προσυμπληρωμένο παραλήπτη.

## Υφιστάμενοι έλεγχοι που επανεκτελέστηκαν
- `transfer-v322151-email-feature-regression.js`: PASS 41
- `transfer-v322146-submission-channels-regression.js`: PASS 58
- `transfer-v322157-primary-email-format-regression.js`: PASS

## Όρια
Αυτοί είναι unit/contract tests με Node.js και όχι πραγματικές διαδραστικές ενέργειες browser. Δεν επαληθεύουν τη νομική εγκυρότητα κάθε οδηγίας ούτε πραγματική αποστολή email/mydocs. Δεν έγινε αλλαγή στους υπολογισμούς, στους κανόνες υποβολής ή στα επίσημα templates. Συνεχίζουν να εκκρεμούν browser test, PHP 7.4 test και επιβεβαίωση πηγών ανά ειδική διαδικασία.
