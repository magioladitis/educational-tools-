# Validation μεταθέσεων — 22/10/2026

Βάση: 3.22.198-FULL.

- Διορθώθηκε το παρωχημένο `tests/transfer-calculator-contract.py`, ώστε να διαβάζει τα αναμενόμενα IDs από το κοινό `includes/components/transfer-score-card.php` και να επιβεβαιώνει ότι η σελίδα καλεί `transferScoreCard('secondary')`.
- Γενικό calculator contract: PASS 37.
- Όλα τα tests με `transfer` στο όνομα (`tests/*transfer*.py`, `tests/*transfer*.js`): **83/83 PASS**, με timeout 8 δευτερολέπτων ανά script και παράλληλη εκτέλεση.
- Καμία αλλαγή στον υπολογιστικό πυρήνα/μοριοδότηση και στο runtime PHP/JS του εργαλείου κατά τη συγκεκριμένη φάση.

## Όρια
- Οι 83 επιτυχίες αφορούν αρχεία δοκιμών και όχι ανεξάρτητα assertions.
- Δεν πραγματοποιήθηκε διαδραστικό browser E2E ούτε PHP 7.4 production compatibility test.
- Το πακέτο διατηρεί έκδοση εφαρμογής 3.22.198 (validation revision, όχι νέο release).
