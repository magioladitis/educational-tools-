# Υπολογισμός διδακτικών αναγκών — καθαρισμός ελληνικής ορολογίας

Ημερομηνία: 2026-09-05

Η δημόσια σελίδα `ypologismos-didaktikon-anagkon.php` χρησιμοποιεί πλέον αποκλειστικά ελληνική ορολογία για τις έννοιες του νέου school staffing framework. Τα εσωτερικά machine-readable keys δεν άλλαξαν.

## Κύριες αντιστοιχίσεις UI

- assignment units → ώρες / μονάδες με αντιστοιχισμένη ανάθεση
- eligibility → επιλεξιμότητα
- fallback → χαμηλότερη ανάθεση
- unresolved dependencies → εκκρεμείς εξαρτήσεις
- regulatory gaps → κανονιστικά κενά
- school profile → στοιχεία σχολικής μονάδας
- conditional → υπό προϋπόθεση
- simulator / test harness / backend → εργαλείο προσομοίωσης και ελέγχου της εσωτερικής λογικής
- roster → κατάσταση εκπαιδευτικών
- ready_for_eligibility_matrix → Έτοιμο για πίνακα επιλεξιμότητας
- structure_only → Μόνο δομή — απαιτούνται επιπλέον στοιχεία

## Έλεγχοι

- School staffing simulator frontend: 29/29 PASS
- General Gymnasium/GEL profile: 35/35 PASS
- School profile workload matrix: 49/49 PASS
- Personnel workload: 42/42 PASS
- Teaching workload model: 7383/7383 PASS
- Teaching workload aggregation: 364/364 PASS
- Teaching timetable cross-audit: 2191/2191 PASS
- Weekly timetable: 496/496 PASS
- Teaching assignments: 51/51 PASS
- Ethics class formation: 24/24 PASS
- PHP lint: 78/78 PASS

Dedicated rendered-page check confirms that the removed English UI terms are no longer present.
