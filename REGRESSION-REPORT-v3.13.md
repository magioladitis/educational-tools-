# Regression report — v3.13

## 1ΓΕ/2026 & 2ΓΕ/2026
- Τα calculation modules `academic-calculations.js`, `service-calculations.js`, `social-calculations.js` δεν τροποποιήθηκαν.
- Εκτελέστηκε `tests/regression-ypologismos-morion.js`: **ALL REGRESSION TESTS PASSED**.
- Ελέγχθηκαν: βαθμός τίτλου, ακαδημαϊκό πλαφόν, εξαίρεση γλώσσας ΠΕ06, εξαίρεση Η/Υ ΠΕ86, πλαφόν προϋπηρεσίας, τρίμηνες συμβάσεις και κοινωνικά κριτήρια.
- Ο νέος έλεγχος ημερομηνιών σεμιναρίου είναι αποκλειστικά έλεγχος δικαιολογητικού και δεν περνά στα calculation modules.
- Live calculation ενεργοποιείται μόνο όταν υπάρχουν κλάδος και έγκυρος βαθμός τίτλου 5–10, ώστε να αποφεύγονται πρόωρα validation errors.

## Live consistency
- `ypologismos-morion-onaseia.php`: προστέθηκε safe live wrapper· ο υπολογισμός γίνεται μόνο όταν έχουν συμπληρωθεί τα βασικά στοιχεία του ενεργού academic mode.
- `ypologismos-morion-apospasis.php`: προστέθηκε live calculation σε input/change.
- Κατά τα live updates καταργείται το αυτόματο scroll προς το αποτέλεσμα στα δύο παραπάνω εργαλεία, ώστε η πληκτρολόγηση να μην μετακινεί τη σελίδα.
- Τα manual buttons παραμένουν ως fallback.

## Syntax
- PHP syntax check: PASS σε όλα τα PHP του package.
- Inline JS syntax check: PASS στα τρία τροποποιημένα εργαλεία.
