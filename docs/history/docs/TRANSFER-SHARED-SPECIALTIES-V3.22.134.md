# v3.22.134 — Κοινό μητρώο ειδικοτήτων στις μεταθέσεις Διαπολιτισμικών Δ.Ε.

## Τι αλλάζει

- Το `includes/transfer-intercultural-vacancies.js` κρατά πλέον **μόνο** κωδικό κλάδου, σχολείο, ΔΔΕ και πλήθος οργανικών κενών. Δεν συντηρεί ξεχωριστές ονομασίες κλάδων από το ετήσιο Excel.
- Χρησιμοποιεί τη μοναδική browser-side κανονικοποίηση `EducationSpecialtyCodes.normalize()` του `includes/specialty-code-normalization.js`, που φορτώνεται ήδη από το κοινό `includes/header.php`.
- Το `ypologismos-morion-metathesis.php` φορτώνει `includes/teacher-specialties.php` και εκθέτει μόνο στον browser μη προσωπικό λεξικό `EducationTransferTeacherSpecialtyLabels` από το κοινό registry. Το λεξικό παράγεται στην PHP με canonical κωδικούς και JSON escaping.
- Οι επιλογές του dropdown δημιουργούνται από τους κωδικούς που **έχουν οργανικό κενό Δ.Ε. 2026–2027**· η ετικέτα επιλύεται από το κοινό registry. Αν λείπει ετικέτα, εμφανίζεται μόνο ο κωδικός (όχι δεύτερη ανεξάρτητη περιγραφή).
- Οι έλεγχοι διαθεσιμότητας δέχονται και ισοδύναμες κανονικοποιήσιμες γραφές (`PE3` → `ΠΕ03`, `ΠΕ04-02` → `ΠΕ04.02`), χωρίς να ενεργοποιούνται μη διαθέσιμοι κλάδοι.
- Η βάση παραμένει ο συνημμένος πίνακας `ORGANIKA KENA DIAPOLITISMIKA DE 2026_2027 (1).xlsx`: 22 εγγραφές, 23 κενά, 13 κλάδοι Δ.Ε. Δεν χρησιμοποιούνται οι πίνακες Πρωτοβάθμιας.

## Έλεγχοι

- `tests/transfer-v322133-intercultural-vacancies.js`
- `tests/transfer-v322134-shared-specialties.js`
- `tests/specialty-code-normalization-parity-contract.py`
- PHP syntax / rendered template and Node JS syntax.
- 35 προϋπάρχουσες δοκιμές μεταθέσεων.
- Responsive Chromium 320/390/768/1280 px, με επιλογή ΠΕ03 και σχολείου με 2 κενά.

Δεν επηρεάζονται οι άλλες επτά επιλογές αίτησης, οι όροι θεμελίωσης ή η μοριοδότηση. Δεν πρόκειται για ζωντανή ενημέρωση κενών.
