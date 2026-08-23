# ASEP Social Criteria UI refactor — v3.20.13-rc1

Candidate refactor για κοινή εμφάνιση και κοινές περιγραφές της καρτέλας «Κοινωνικά κριτήρια» στους υπολογιστές προκηρύξεων ΑΣΕΠ.

## Περιλαμβάνει

- Νέο `includes/components/asep-social-criteria.php`.
- Εφαρμογή μόνο σε 6 calculators: 1ΓΕ/2ΓΕ, 1ΓΤ/2024, 1ΕΑ/2025, 2ΕΑ/2025, 3ΕΑ/2025, 4ΕΑ/2025.
- Κοινή ορολογία για τέκνα και αναπηρία.
- Κοινό responsive layout.
- Ρητή παραμετροποίηση των κανόνων από κάθε σελίδα.
- Ειδική αναφορά 67% μόνο στις 3ΕΑ/4ΕΑ.

## Εκτός scope

ΣΔΕ, Ωνάσεια, αποσπάσεις, Ψηφιακό Φροντιστήριο, Ευρωπαϊκά Σχολεία, απόσπαση εξωτερικού κ.λπ. δεν χρησιμοποιούν το component.

## Safety

Το component δεν εκτελεί καμία μοριοδότηση. Τα calculation modules και τα inline JavaScript των 6 calculators παραμένουν αμετάβλητα.

Η σύνταξη του partial είναι συντηρητική: χωρίς PHP arrow functions, null-coalescing operator ή return/scalar type declarations.
