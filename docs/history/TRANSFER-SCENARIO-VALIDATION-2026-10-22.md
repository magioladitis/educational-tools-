# Έλεγχος σεναρίων μεταθέσεων — 22/10/2026

Βάση: 3.22.198-CROSS-LEVEL-VALIDATED-FULL. Δεν τροποποιήθηκαν κανόνες μοριοδότησης, eligibility ή παραγωγής δικαιολογητικών.

## Νέο executable regression test
`tests/transfer-v322198-scenario-mode-switch-regression.js`

- 21 μεταβάσεις (7 modes επί 3 επαναλήψεις): region, smeae, eae_sensory, kedasy, music, art, intercultural.
- Για κάθε mode, η παραγωγή checklist είναι σταθερή και δεν υπάρχουν διπλά IDs δικαιολογητικών.
- Η επιστροφή στη γενική μετάθεση αναπαράγει την αρχική λίστα.
- Δεν μεταφέρεται το γενικό `record` checklist στα διαπολιτισμικά.
- Κάθε παραγόμενο δικαιολογητικό έχει ορισμένο submission channel.
- Αποτέλεσμα: PASS, 45 assertions.

## Επιπλέον επιτυχημένοι έλεγχοι
- `transfer-v322175-shared-children-documents.js`
- `transfer-v322185-coservice-parity.js` (12 καθεστώτα, δύο βαθμίδες)
- `transfer-v322156-primary-official-codes-regression.js`
- `transfer-v322170-primary-periods-regression.js`
- `transfer-v322150-checklist-actionable-regression.js` (42 checks)
- `transfer-both-levels-rendered-contract.py` (150 μοναδικά IDs Π.Ε., 149 Δ.Ε.)

## Όρια
Δεν εκτελέστηκαν πραγματικά browser click-through / DOM interactions, επειδή loopback HTTP στον Chromium μπλοκάρεται. Το test στοχεύει JS business behavior με mock DOM. Δεν επαληθεύτηκαν τα οπτικά αποτελέσματα, ούτε η πλήρης διαδραστική συμπεριφορά της Πρωτοβάθμιας, ούτε η PHP 7.4 παραγωγής. Δεν έγινε νομικό audit των εγκυκλίων. Προτείνεται πραγματικό browser staging test πριν deployment.
