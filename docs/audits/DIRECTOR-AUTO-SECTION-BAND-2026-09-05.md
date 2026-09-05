# Αυτόματη κλίμακα τμημάτων Διευθυντή — 2026-09-05

## Στόχος

Στο tab «Εκπαιδευτικοί» του `ypologismos-didaktikon-anagkon.php` η κλίμακα τμημάτων του/της Διευθυντή/ντριας δεν εισάγεται πλέον χειροκίνητα. Προκύπτει από τα ήδη δηλωμένα **Κανονικά τμήματα ανά τάξη** του school profile.

## Κανόνας

Το σύστημα αθροίζει αποκλειστικά τα `general_sections` της σχολικής μονάδας. Δεν προσμετρώνται ομάδες ξένης γλώσσας, Ομάδες Προσανατολισμού, ομάδες 2ου/3ου πεδίου, τμήματα Ηθικής ή split ομάδες Τεχνολογίας/Πληροφορικής.

Η αντιστοίχιση είναι:

- 3–5 κανονικά τμήματα → `3-5`
- 6–9 → `6-9`
- 10–12 → `10-12`
- 13+ → `13+`

Αν δεν υπάρχει επαρκής δήλωση κανονικών τμημάτων, ο υπολογισμός του/της Διευθυντή/ντριας παραμένει unresolved αντί να χρησιμοποιήσει χειροκίνητη ή παλιά τιμή.

## UI / CSV

- Αφαιρέθηκε το editable dropdown «Τμήματα σχολικής μονάδας» από τον Διευθυντή.
- Εμφανίζεται πληροφοριακά ο πραγματικός αριθμός κανονικών τμημάτων και η αυτόματη κλίμακα.
- Η πληροφορία ενημερώνεται live όταν αλλάζουν τα κανονικά τμήματα στο school profile.
- Το νέο πρότυπο CSV δεν έχει στήλη κλίμακας τμημάτων Διευθυντή.
- Παλιότερα CSV με τέτοια στήλη εξακολουθούν να διαβάζονται, αλλά η τιμή δεν χρησιμοποιείται από το frontend/personnel calculation.

## Αρχιτεκτονική

Προστέθηκαν:

- `schoolProfileTotalGeneralSections()` στο `includes/school-profile.php`
- `personnelWorkloadDirectorSectionsBandFromCount()` στο `includes/personnel-workload.php`

Το `personnelWorkloadSecondaryObligation()` προτιμά το `school_general_section_count` όταν υπάρχει, κρατώντας το παλιό `director_sections_band` μόνο ως backward-compatible API fallback για παλαιότερες εσωτερικές κλήσεις.

## Έλεγχοι

- Personnel tab frontend: 23/23 PASS
- Personnel CSV frontend: 15/15 PASS
- Personnel workload: 50/50 PASS
- General Gymnasium/GEL profiles: 45/45 PASS
- School profile workload matrix: 49/49 PASS
- General simulator frontend: 45/45 PASS
- Real Gymnasium Corfu myschool cross-audit: 109/109 PASS
- Real GEL Corfu myschool cross-audit: 182/182 PASS
- Workload aggregation: 364/364 PASS
- Overall timetable/assignments cross-audit: 2191/2191 PASS
- Weekly timetable: 497/497 PASS
- Assignments: 52/52 PASS
- PHP lint: 78/78 PASS
