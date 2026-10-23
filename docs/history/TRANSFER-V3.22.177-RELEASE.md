# Εργαλειοθήκη Εκπαιδευτικού — 3.22.177

18/10/2026 · Βάση: ακριβής 3.22.176.

## Audit μικρών τεχνικών εκκρεμοτήτων Πρωτοβάθμιας/Δευτεροβάθμιας

- `modeNote`: δεν υπήρχε νεκρό στοιχείο `modeNote` στο `$ids` της 3.22.176. Τα `modeNote` / `primaryModeNote` παράγονται άμεσα από το disclosure.
- `primaryChildrenExtra`: αποτελεί ήδη πρωτοβάθμιο αποκλειστικό markup, δεν προστίθεται με `str_replace`.
- **Κλειστό:** `includes/components/transfer-social-criteria.php` παράγει τα μεταφρασμένα `id`/`for` απευθείας με ασφαλές `htmlspecialchars`. Αφαιρέθηκε ο κύκλος `str_replace` πάνω σε παραγόμενο HTML. Δοκιμές PHP επιβεβαίωσαν byte-for-byte ίδια απόδοση των δύο social panels με την 3.22.176.
- **Μερικώς κλειστό:** νέος κοινός browser helper `includes/transfer-html-escape.js` παρέχει `EducationTransferHtmlEscape.escapeHtml`, που καλείται από `transfer-primary.js`, `transfer-primary-periods.js`, `transfer-ui.js`. Άλλες αυτοτελώς δοκιμαζόμενες μονάδες (`transfer-documents.js`, `transfer-children-documents.js`) διατηρούν προς το παρόν το δικό τους escape για μη διακοπή των isolated Node tests· μελλοντική μετάπτωση απαιτεί ενημέρωση harnesses.
- **Ανοιχτό:** μηχανισμός checklist. Τα δύο grades έχουν διαφορετική δομή στοιχείων και ειδικούς κανόνες για information-only και action entries. Κοινή διαχείριση κατάστασης και μονάδα εκτύπωσης μπορούν να εξαχθούν μετά από parity testing.
- **Ανοιχτό:** οι τρεις λίστες vacancies (`transfer-artistic-vacancies.js`, `transfer-intercultural-vacancies.js`, `transfer-primary-intercultural-vacancies.js`) περιέχουν χωριστές ετήσιες καταγραφές και ιδιαιτερότητα της κοινής κατηγορίας ΠΕ70+ΠΕ71. Δεν συγχωνεύθηκαν δεδομένα ούτε υπολογισμοί θέσεων. Μόνο κοινά utility APIs μπορούν να εξαχθούν μετά από contract tests.

## Περιορισμός αλλαγών

Δεν τροποποιήθηκαν αριθμητικές μονάδες, μόρια, υπηρεσίες, δικαιολογητικά, επιλεξιμότητα, emails / mydocs, στοιχεία οργανικών κενών, scripts δεδομένων ή εμφάνιση της Δευτεροβάθμιας.

## Έλεγχοι

- 62/62 μεταθεσιολογικά Node.js test files επιτυχή (61 προηγούμενα + νέο regression test).
- `php -l` / `node --check` για όλα τα αλλαγμένα αρχεία.
- PHP rendering: social criteria sections byte-for-byte ίδια στην ΠΕ και ΔΕ.
- Chromium: λειτουργία και των δύο εργαλείων σε 1280 και 390 px· καμία JavaScript exception και κανένα οριζόντιο overflow.
- Το PATCH ισχύει μόνο πάνω στην ακριβή 3.22.176, με έλεγχο FULL/PATCH ισοδυναμίας.
- Χωρίς εγκατάσταση ή production smoke test στον PHP 7.4 server.
