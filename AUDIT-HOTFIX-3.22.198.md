# Διορθωτική έκδοση 3.22.198 — 22/10/2026

Βάση: 3.22.197-FULL.

- Μεταφέρθηκε το page-local CSS block της `ypologismos-morion-metathesis.php` στο `assets/transfer-secondary.css`.
- Μεταφέρθηκαν 19 στατικές δηλώσεις `style` σε 11 CSS classes. Διατηρήθηκαν 19 δηλώσεις inline `display` που ελέγχονται από τη JavaScript, για αποφυγή λειτουργικής παλινδρόμησης.
- Συγχρονίστηκε η έκδοση στο `includes/config.php` και στον `service-worker.js`.
- Επικαιροποιήθηκαν συμβόλαια test που περιείχαν παρωχημένη έκδοση 3.22.149 ή αυστηρές συγκρίσεις κειμένου.
- Στοχευμένες δοκιμές: δύο service timeline tests PASS, MSD disclosure PASS. Το γενικό transfer-calculator contract παραμένει FAIL σε παλαιότερο έλεγχο IDs αποτελεσμάτων και χρειάζεται ξεχωριστή αναθεώρηση συμβολαίου. Δεν έχει ολοκληρωθεί πλήρης regression suite ούτε δοκιμή PHP 7.4/browser.

Δεν τροποποιήθηκε σκοπίμως η αριθμητική ή οι κανόνες επιλεξιμότητας.
