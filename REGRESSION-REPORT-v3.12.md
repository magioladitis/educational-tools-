# Regression & visual parity report — ypologismos-morion.php v3.12

Η v3.12 είναι δεύτερο, ελεγχόμενο UI pass του βασικού calculator 1ΓΕ/2026 & 2ΓΕ/2026, με master visual template το `ypologismos-morion-1gt-2024.php`.

## Structural safety

- 25/25 form controls της v3.11 διατηρήθηκαν.
- Κανένα υφιστάμενο HTML ID δεν αφαιρέθηκε: 34/34 retained.
- Προστέθηκαν μόνο 3 display IDs: `academicSubtotal`, `serviceSubtotal`, `socialSubtotal`.
- Τα `value` των επιλογών των select controls παραμένουν ίδια.
- Τα `min`, `max`, `step`, `oninput` και τα IDs των input controls παραμένουν ίδια.
- Rollback: `backups/ypologismos-morion-v3.11.php.bak`.

## Calculation safety

- Η `calculatePoints()` και όλος ο κώδικας από την έναρξή της έως το τέλος του αρχείου είναι byte-for-byte ίδιος με τη v3.11.
- Οι μόνες νέες γραμμές JavaScript ενημερώνουν τα 3 subtotal pills από τα ήδη υπολογισμένα totals.
- Δεν άλλαξαν τα calculation modules:
  - `includes/academic-calculations.js`
  - `includes/service-calculations.js`
  - `includes/social-calculations.js`

## Golden regression scenarios

Όλα πέρασαν:

1. Βασικός τίτλος 7,50 → 18,75 ακαδημαϊκά μόρια.
2. Rich academic profile → 115 μόρια.
3. Maximum academic profile → 120 μόρια.
4. ΠΕ06: η Αγγλική ως γλώσσα διορισμού εξαιρείται, η Γαλλική C1 μετρά → 5 μόρια.
5. ΠΕ86: γνώση Η/Υ → 0 μόρια.
6. Προϋπηρεσία raw 149 → cap 120.
7. Τρίμηνες συμβάσεις: σωστά ετήσια πλαφόν 10 / 20 μορίων.
8. Κοινωνικά: 2 τέκνα + υψηλότερη επιλέξιμη αναπηρία 67% → 32,8 μόρια.

## Browser/UI smoke test

Με Chromium headless και πραγματικό DOM:

- specialty ΠΕ02 + βαθμός 7,50 → sidebar total 18,75.
- `academicSubtotal` → 18,75 / 120.
- `serviceSubtotal` → 0,00 / 120.
- `socialSubtotal` → 0,00.
- Το αναλυτικό αποτέλεσμα εμφανίζεται κανονικά.
- Η κάρτα πηγών βρίσκεται μέσα στο `.app`.
- Δεν καταγράφηκαν JavaScript console/page errors.

## Visual parity check με 1ΓΤ/2024

Οι computed styles ταυτίζονται για τα βασικά visual components:

- `.hero` — MATCH
- `.card` — MATCH
- `.results` — MATCH
- `.source` — MATCH
- `.field-grid` — MATCH
- `.note` — MATCH
- `.actions` — MATCH

Η διαφορά που παραμένει είναι λειτουργική/περιεχομένου: ο 1ΓΕ/2ΓΕ έχει κουμπί «Υπολογισμός μορίων» και αναλυτικό breakdown, επειδή αυτά αποτελούν ήδη λειτουργίες του συγκεκριμένου calculator.
