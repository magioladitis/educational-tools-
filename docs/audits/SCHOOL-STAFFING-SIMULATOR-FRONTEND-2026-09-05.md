# School Staffing Simulator frontend — 2026-09-05

## Scope

Πρώτη δημόσια frontend έκδοση πάνω στο ήδη ελεγμένο pipeline:

`weekly timetable → teaching workload model → school profile → workload matrix`

Δεν χρησιμοποιεί ακόμη το personnel layer για τελική κατανομή και δεν εκτελεί αυτόματες τοποθετήσεις.

## Public page

`ypologismos-didaktikon-anagkon.php`

Υποστηρίζει στην πρώτη έκδοση:

- Ημερήσιο Γυμνάσιο
- Ημερήσιο Γενικό Λύκειο

### Inputs Γυμνασίου

- πραγματικά τμήματα Α΄/Β΄/Γ΄,
- ομάδες 2ης ξένης γλώσσας ανά τάξη (Γαλλικά / Γερμανικά / Ιταλικά),
- δεδομένα Ηθικής ανά τάξη (πλήθος απαλλασσομένων, εμπρόθεσμη συμπλήρωση ορίου, πραγματική απόφαση ισοδύναμων τμημάτων).

### Inputs ΓΕΛ

- πραγματικά τμήματα Α΄/Β΄/Γ΄,
- ομάδες 2ης ξένης γλώσσας Α΄/Β΄,
- Ομάδες Προσανατολισμού Β΄ και Γ΄,
- πραγματικές ομάδες Μαθηματικών 2ου πεδίου / Βιολογίας 3ου πεδίου στη Γ΄ Θετικών–Υγείας,
- πραγματικές conditional groups Μαθηματικών / Ιστορίας Γενικής Παιδείας στη Γ΄,
- δεδομένα Ηθικής ανά τάξη.

## Output semantics

Ο πίνακας ανά κλάδο εμφανίζει:

- Α΄ / Β΄ / Γ΄ eligibility hours,
- `ordered_exclusive_top_priority_hours`,
- `ordered_shared_top_priority_hours`,
- `special_top_priority_hours`,
- `fallback_hours`,
- drill-down ανά πραγματικό assignment unit / μάθημα / τάξη.

Τα eligibility totals μπορεί να επικαλύπτονται μεταξύ κλάδων. Το UI δηλώνει ρητά ότι:

- δεν πρόκειται ακόμη για επίσημα λειτουργικά κενά,
- δεν πρόκειται για τελική κατανομή εκπαιδευτικών,
- δεν εκτελούνται αυτόματες τοποθετήσεις.

## Incomplete profiles

Αν λείπουν απαιτούμενα δομικά inputs, το αποτέλεσμα χαρακτηρίζεται `Μερικός υπολογισμός` και εμφανίζεται λίστα ελλείψεων. Τα ελλιπή inputs Ηθικής δεν ακυρώνουν τη δομική ετοιμότητα· τα αντίστοιχα slots παραμένουν unresolved dependencies.

## Synthetic regression totals

- Ημερήσιο Γυμνάσιο contract profile: 171 assignment-unit hours.
- Ημερήσιο ΓΕΛ contract profile: 293 assignment-unit hours.

Τα totals είναι ακριβώς ίδια με το internal `general-gymnasium-gel-school-profile-contract.py`.

## Toolbox integration

Προστέθηκε κάρτα #32 στην `ergaleia.php`:

`Υπολογισμός διδακτικών αναγκών σχολικής μονάδας`

## Tests

- `school-staffing-simulator-frontend-contract.py`: 25/25 PASS
- `general-gymnasium-gel-school-profile-contract.py`: 35/35 PASS
- `school-profile-workload-matrix-contract.py`: 49/49 PASS
- `personnel-workload-contract.py`: 42/42 PASS
- `teaching-workload-model-contract.py`: 7383/7383 PASS
- `teaching-workload-aggregation-contract.py`: 364/364 PASS
- `teaching-timetable-cross-audit-contract.py`: 2191/2191 PASS
- `weekly-timetable-2026-contract.py`: 496/496 PASS
- `teaching-assignments-2026-contract.py`: 51/51 PASS
- `ethics-class-formation-2026-contract.py`: 24/24 PASS
- PHP lint: 78/78 files PASS

## Next step

Το τρίτο στάδιο του ίδιου frontend μπορεί πλέον να συνδεθεί με `includes/personnel-workload.php` για πραγματικό roster εκπαιδευτικών, χωρίς να αλλάξει το υπάρχον school-profile UI.
