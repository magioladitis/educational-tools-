# Audit ενοποίησης Ψηφιακού Φροντιστηρίου

## Scope

Η ενοποίηση αφορά:

- `ypologismos-morion.php` — 1ΓΕ/2026 & 2ΓΕ/2026
- `ypologismos-morion-3ea-2025.php` — 3ΕΑ/2025

Δεν αφορά τον ξεχωριστό υπολογιστή **απόσπασης** στο Ψηφιακό Φροντιστήριο, ο οποίος έχει διαφορετικό σκοπό και διαφορετικά κριτήρια.

## Αρχιτεκτονική

### Presentation

`includes/components/asep-digital-tutoring-service.php`

- κοινή επικεφαλίδα και επεξηγήσεις,
- κοινό container δυναμικών γραμμών,
- κοινό κουμπί προσθήκης σχολικού έτους,
- κανένας αριθμητικός κανόνας PHP.

### UI controller

`includes/asep-digital-tutoring.js`

- δημιουργία/αφαίρεση σχολικών ετών,
- αποτροπή διπλής επιλογής ίδιου έτους,
- συγχρονισμός `max` των inputs με τους κοινά ορισμένους κανόνες,
- ανάγνωση των entries,
- custom event για live recalculation.

### Business logic

`includes/service-calculations.js`

Η νέα `EducationService.digitalTutoring(entries)` είναι η μοναδική αριθμητική υλοποίηση.

Κλειδωμένοι κανόνες:

- 2024–2025: έως **9 μήνες και 16 ημέρες**,
- 2025–2026: έως **8 μήνες και 2 ημέρες**,
- **1,5 μόριο/μήνα**,
- έως **15 μόρια ανά σχολικό έτος**,
- κοινή δεξαμενή υπολοίπων ημερών,
- `30 ημέρες = 1 επιπλέον μήνας`.

Η legacy `digitalPerSchoolYear()` διατηρείται μόνο για backwards compatibility και δεν πρέπει να χρησιμοποιείται από τα νέα multi-year πεδία 1ΓΕ/2ΓΕ ή 3ΕΑ.

## Naming cleanup

Καταργείται το όνομα:

`asep-digital-tutoring-php56.php`

Νέο canonical όνομα:

`asep-digital-tutoring-service.php`

Η συμβατότητα με παλιότερο PHP runtime παραμένει στον τρόπο γραφής του κώδικα, όχι στο δημόσιο όνομα του component.

## Regression cases

Τα tests καλύπτουν:

1. 2024–2025 max 9m16d.
2. 2025–2026 max 8m2d.
3. 9m16d → 9 μετρήσιμοι μήνες + 16 ημέρες υπόλοιπο → 13,5 μόρια.
4. 8m2d → 8 μετρήσιμοι μήνες + 2 ημέρες υπόλοιπο → 12 μόρια.
5. Υπόλοιπα 16 + 14 ημερών → +1 μήνας.
6. Πραγματικά μέγιστα και των δύο ετών → 17 μήνες + 18 ημέρες → 25,5 μόρια.
7. Clamp υπερβολικών μηνών/ημερών ανά έτος.
8. Duplicate school year → υπολογίζεται μία φορά.
9. Legacy `digitalPerSchoolYear()` παραμένει συμβατή.

## Acceptance criterion

Η ενοποίηση θεωρείται ολοκληρωμένη όταν:

- και οι δύο σελίδες καλούν το ίδιο PHP component,
- και οι δύο χρησιμοποιούν `AsepDigitalTutoring.calculate(...)`,
- κανένα από τα δύο PHP αρχεία δεν έχει δικούς του κανόνες `9/16`, `8/2` ή day-carry arithmetic,
- όλα τα regression tests περνούν.
