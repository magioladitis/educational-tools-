# Εργαλειοθήκη Εκπαιδευτικού

Συλλογή δωρεάν εργαλείων για εκπαιδευτικούς, υποψηφίους ΑΣΕΠ, αναπληρωτές και μόνιμους εκπαιδευτικούς.

**Σχεδιασμός & υλοποίηση:** Μάριος Μαγιολαδίτης (ΠΕ03, ΠΕ86)

Κεντρική σελίδα: `ergaleia.php`

## Εργαλεία

1. `dikaioma-symmetoxis.php` — Έχω δικαίωμα συμμετοχής;
2. `posa-paravola.php` — Πόσα παράβολα χρειάζομαι;
3. `dikaiologitika-titlon-spoudon.php` — Δικαιολογητικά τίτλων σπουδών
4. `ypologismos-morion.php` — Υπολογισμός μορίων 1ΓΕ/2026 & 2ΓΕ/2026
5. `ypologismos-morion-1gt-2024.php` — Υπολογισμός μορίων 1ΓΤ/2024
6. `paidagogiki-eparkeia.php` — Έλεγχος Παιδαγωγικής και Διδακτικής Επάρκειας
7. `ypologismos-morion-onaseia.php` — Μόρια αναπληρωτή στα Δημόσια Ωνάσεια Σχολεία
8. `ypologismos-morion-apospasis-dimos.php` — Μόρια απόσπασης στα ΔΗΜ.Ω.Σ.
9. `ypologismos-morion-apospasis.php` — Υπολογισμός μορίων απόσπασης
10. `ypologismos-morion-1ea-2025.php` — Υπολογισμός μορίων 1ΕΑ/2025 (ΔΕ01-ΕΒΠ)
11. `ypologismos-morion-2ea-2025.php` — Υπολογισμός μορίων 2ΕΑ/2025 (ΕΕΠ) και ενδείξεις πρόταξης
12. `ypologismos-morion-3ea-2025.php` — Υπολογισμός μορίων 3ΕΑ/2025
13. `ypologismos-morion-4ea-2025.php` — Υπολογισμός μορίων 4ΕΑ/2025 (ΤΕ Ειδικής Αγωγής) και έλεγχος Κύριου/Επικουρικού Πίνακα
14. `odigos-enstasis.php` — Οδηγός ένστασης 1ΓΕ/2026 & 2ΓΕ/2026
15. `dikaiologitika-tekna-anapiria.php` — Δικαιολογητικά τέκνων & αναπηρίας
16. `ypologismos-morion-apospasis-psifiako-frontistirio.php` — Μόρια απόσπασης στο Ψηφιακό Φροντιστήριο
17. `ypologismos-morion-apospasis-sde.php` — Μόρια απόσπασης στα Σχολεία Δεύτερης Ευκαιρίας (ΣΔΕ)
18. `ypologismos-morion-apospasis-exoteriko.php` — Μόρια απόσπασης στο εξωτερικό
19. `ypologismos-morion-apospasis-evropaika-scholeia.php` — Μόρια απόσπασης σε Ευρωπαϊκά Σχολεία, έλεγχος γλωσσών, αναλυτικό breakdown και δυναμικό checklist δικαιολογητικών
20. `metatropi-klimakas.php` — Μετατροπή κλίμακας βαθμού
21. `ypologismos-morion-diefthynton-ypodiefthynton-sde.php` — Μόρια επιλογής Διευθυντών & Υποδιευθυντών ΣΔΕ

Η σελίδα `asep-tools.php` λειτουργεί ως επιπλέον θεματικός κόμβος για εργαλεία ΑΣΕΠ.

## Δομή κοινών αρχείων

```text
assets/
└── common.css                  # κοινή εμφάνιση header/footer

includes/
├── config.php                  # κοινές ρυθμίσεις project
├── header.php                  # κοινή επιστροφή στην Εργαλειοθήκη
├── footer.php                  # κοινό footer/δημιουργός
├── academic-calculations.js    # κοινά ακαδημαϊκά κριτήρια
├── service-calculations.js     # κοινός υπολογισμός προϋπηρεσίας
├── sde-calculations.js         # μοριοδότηση και αντιστοίχιση ειδικοτήτων ΣΔΕ
└── sde-leadership-calculations.js # μοριοδότηση Διευθυντών / Υποδιευθυντών ΣΔΕ
```

Τα επιμέρους εργαλεία διατηρούν το δικό τους CSS/JavaScript μόνο όταν η λειτουργία ή η εμφάνιση είναι ειδική για το συγκεκριμένο εργαλείο.

## Βασική αρχή ανάπτυξης

Τα filenames που χρησιμοποιούνται στον server και στο Git παραμένουν **σταθερά**. Προσωρινά αρχεία τύπου `-v2`, `-v3` κ.λπ. χρησιμοποιούνται μόνο για δοκιμές cache και δεν αποθηκεύονται στο repository.

Πριν από κάθε commit συνιστάται:

```bash
git status
git diff
php -l <αρχείο.php>
```

## Αποποίηση ευθύνης

Τα εργαλεία παρέχουν ενδεικτική πληροφόρηση και δεν αντικαθιστούν τις επίσημες προκηρύξεις, εγκυκλίους, αποφάσεις ή οδηγίες των αρμόδιων φορέων. Πριν από οριστική αίτηση ή ενέργεια πρέπει να ελέγχονται τα επίσημα έγγραφα.

### Κοινή λογική ΤΕ
- `includes/te-academic-calculations.js` — κοινή ακαδημαϊκή μοριοδότηση για 1ΓΤ/2024 και 4ΕΑ/2025.

- `includes/social-calculations.js` — κοινή μοριοδότηση κοινωνικών κριτηρίων για 1ΓΕ/2ΓΕ, 1ΓΤ και 1ΕΑ–4ΕΑ.
- `includes/abroad-calculations.js` — μοριοδότηση και βασικός έλεγχος απόσπασης στο εξωτερικό.

- `includes/european-schools-calculations.js` — υπολογισμός μορίων και βασικών προϋποθέσεων Ευρωπαϊκών Σχολείων.

- `metatropi-klimakas.php`: μετατροπή βαθμού 10βάθμιας/20βάθμιας κλίμακας, δεκαδικής ή λεκτικής μορφής, με έξοδο ακέραιο μέρος/αριθμητής/παρονομαστής.

## Κοινό UI
Από την έκδοση Common UI v2, όλα τα εργαλεία μοιράζονται το ίδιο design system μέσω των `assets/common.css` και `assets/common.js`. Οι υπάρχουσες κλάσεις παραμένουν συμβατές, ενώ για νέα εργαλεία προτείνονται οι canonical `edu-*` κλάσεις που περιγράφονται στο `UI-GUIDE.md`.


### Common UI v3
The common UI now standardises the **structure** of older tools (hero, cards, fields, results and actions) while allowing each tool to retain its own colour identity. Ten legacy `app-box` pages were upgraded through the `edu-modernized` compatibility layer.

### Απόσπαση στο εξωτερικό — Παράρτημα V
Το `ypologismos-morion-apospasis-exoteriko.php` περιλαμβάνει πλέον τα μηνιαία επιμίσθια της πρόσκλησης 11771/Η2/30-01-2026. Οι έως τρεις προτιμήσεις συγκρίνονται αυτόματα ως προς το ονομαστικό μηνιαίο ποσό και εμφανίζεται ενδεικτικό 12μηνο (μηνιαίο × 12).
