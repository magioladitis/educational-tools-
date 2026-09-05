# Personnel Workload Layer — Audit 2026-09-05

## Στόχος

Προσθήκη εσωτερικού layer που συνδέει πραγματικό εκπαιδευτικό με το ήδη ελεγμένο
`school_profile → workload matrix`, χωρίς καμία αλλαγή στο δημόσιο UI και χωρίς αυτόματη
τοποθέτηση.

Ροή δεδομένων:

`εκπαιδευτικός → κλάδος → υποχρεωτικό διδακτικό ωράριο → ήδη δεσμευμένες ώρες → συγκεκριμένα curriculum units → προτεραιότητα ανάθεσης → υπόλοιπο`

## Νέο αρχείο

- `includes/personnel-workload.php`

Το αρχείο δεν φορτώνεται από `ergaleia.php`, `anatheseis-mathimaton.php` ή
`orologio-programma-mathimaton.php`.

## Υποχρεωτικό ωράριο

Το server-side layer υλοποιεί τη δευτεροβάθμια κλίμακα που χρησιμοποιεί ήδη το
`includes/teaching-hours-calculations.js` και το νέο contract κάνει parity check μεταξύ
PHP και JS σε αντιπροσωπευτικά όρια:

- ΠΕ: 0, 6 έτη, 6 έτη + 1 ημέρα, 12 έτη, 12 έτη + 1 ημέρα, 20 έτη.
- ΤΕ: 7 έτη και 7 έτη + 1 ημέρα.
- Διευθυντής/ντρια.
- Υπεύθυνος/η εργαστηρίου τομέα/ειδικότητας Ε.Κ./ΕΠΑ.Λ.

Οι κλάδοι ΠΕ και ΤΕ μπορούν να αντιστοιχιστούν αυτόματα στην αντίστοιχη κλίμακα.
Για ΔΕ δεν γίνεται αυθαίρετη επιλογή μεταξύ `DE01_ARCH` και `DE01_TECH`: απαιτείται
ρητό `hours_branch`.

## Allocation semantics

Για κάθε allocation απαιτούνται:

- `person_id`
- `unit_id`
- θετικός αριθμός ωρών

Το layer ελέγχει:

1. ότι το unit είναι fixed/assignable στο συγκεκριμένο school profile,
2. ότι ο κλάδος έχει πραγματική ανάθεση στο unit,
3. την προτεραιότητα Α΄/Β΄/Γ΄/SPECIAL,
4. αν η ανάθεση είναι top-priority ή fallback,
5. το ατομικό υποχρεωτικό ωράριο,
6. σε επίπεδο roster, τη συνολική χωρητικότητα κάθε unit.

Fallback ανάθεση είναι επιτρεπτή αλλά παράγει warning. Μη επιλέξιμο μάθημα παράγει
error. Regulatory gaps και unresolved dependencies δεν εμφανίζονται ως assignable units.

## Open eligible units

Μετά από ένα δοσμένο roster plan, κάθε εκπαιδευτικός με υπόλοιπο ωραρίου λαμβάνει λίστα
`open_eligible_units` με τα ακόμη ακάλυπτα units στα οποία έχει ανάθεση. Η λίστα διατηρεί:

- πραγματική προτεραιότητα,
- top/fallback status,
- διαθέσιμες ώρες unit,
- πλήθος ισότιμων top-priority κλάδων.

Οι ευκαιρίες αυτές είναι επικαλυπτόμενες μεταξύ εκπαιδευτικών και **δεν αθροίζονται** ως
ανάγκη στελέχωσης.

## Sanity checks — ΕΝ.Ε.Ε.ΓΥ.-Λ. Κέρκυρας

Συνθετικός ΠΕ03 με 7 έτη υπηρεσίας:

- υποχρεωτικό ωράριο: 21,
- ήδη δεσμευμένες εκτός profile: 3,
- Μαθηματικά Α΄ + Β΄ + Γ΄: 18,
- συνολικά: 21,
- υπόλοιπο: 0,
- όλες οι 18 ώρες είναι exclusive top-priority Α΄ ανάθεση.

Ανάθεση Φυσικής σε ΠΕ03 αναγνωρίζεται ως επιλέξιμη χαμηλότερη ανάθεση και παράγει
warning. Ανάθεση Γλωσσικής Διδασκαλίας σε ΠΕ03 απορρίπτεται.

Roster test: δύο ΠΕ03 μπορούν να μοιραστούν 3+3 ώρες από unit 6 ωρών. Σχέδιο 4+4 στο
ίδιο unit ανιχνεύεται ως over-allocation 2 ωρών.

## Ε.Ε.Ε.ΕΚ. Κέρκυρας

Το profile παραμένει `structure_only`, επειδή οι τιμές «Εκτίμηση myschool» δεν έχουν
μετατραπεί σε αριθμό τμημάτων. Το personnel layer αρνείται να δημιουργήσει τεχνητές
ώρες ή allocations μέχρι να υπάρξουν ασφαλή section counts.

## Tests

- `tests/personnel-workload-contract.py`: 42/42 PASS
- `tests/school-profile-workload-matrix-contract.py`: 49/49 PASS
- `tests/school-profile-eneegyl-kerkyra-2026-contract.py`: 30/30 PASS
- `tests/school-profile-eeeek-kerkyra-2026-contract.py`: 47/47 PASS
- `tests/eeeek-2026-cross-audit-contract.py`: 50/50 PASS
- `tests/eneegyl-2026-crosswalk-contract.py`: 71/71 PASS
- `tests/teaching-workload-aggregation-contract.py`: 364/364 PASS
- `tests/teaching-workload-model-contract.py`: 7383/7383 PASS
- `tests/teaching-timetable-cross-audit-contract.py`: 2191/2191 PASS
- `tests/weekly-timetable-2026-contract.py`: 496/496 PASS
- `tests/teaching-assignments-2026-contract.py`: 51/51 PASS
- `tests/regulatory-gaps-2026-contract.py`: 37/37 PASS
- `tests/teaching-hours-regression.js`: 70 PASS
- PHP lint: 76/76 PASS

## Τι δεν κάνει ακόμη

- Δεν επιλέγει αυτόματα ποιος εκπαιδευτικός θα πάρει ποιο μάθημα.
- Δεν θεωρεί τα `open_eligible_units` επίσημα κενά.
- Δεν αποφασίζει μεταξύ ισότιμων κλάδων στην ίδια ανάθεση.
- Δεν υποκαθιστά αποφάσεις Συλλόγου/Διεύθυνσης ή πραγματικά στοιχεία υπηρεσιακής
  τοποθέτησης.

Αυτό το layer είναι validator / decision-support foundation για το επόμενο βήμα:
πραγματικό roster σχολείου και ελεγχόμενο σχέδιο τοποθέτησης.
