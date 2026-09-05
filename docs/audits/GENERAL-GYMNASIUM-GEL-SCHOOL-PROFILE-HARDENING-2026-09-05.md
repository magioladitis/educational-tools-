# General Gymnasium / GEL school-profile hardening — 2026-09-05

## Στόχος

Να μπορεί το κοινό server-side pipeline

`school profile → school workload → personnel workload`

να χρησιμοποιηθεί με ασφάλεια και σε τυπικό Ημερήσιο Γυμνάσιο / Ημερήσιο ΓΕΛ, χωρίς να συγχέονται τα κανονικά τμήματα τάξης με ομάδες ξένης γλώσσας, ομάδες προσανατολισμού, επιλογές επιστημονικού πεδίου ή τμήματα Ηθικής.

Η αλλαγή είναι backend-only. Δεν προστέθηκε νέο UI και τα νέα `profile_*` metadata αφαιρούνται από το public timetable payload.

## Νέο internal layer

Προστέθηκε το `includes/school-profile-general-education.php` με:

- `schoolProfileBuildDayGymnasium2026()`
- `schoolProfileBuildDayGel2026()`
- `schoolProfileGeneralEducationReadiness()`

Οι builders δέχονται πραγματικά στοιχεία σχολικής μονάδας και παράγουν profile συμβατό με το ήδη υπάρχον `school-profile.php`.

## Διακριτές έννοιες τμημάτων / ομάδων

Το profile πλέον ξεχωρίζει ρητά:

- `general_sections`: κανονικά τμήματα τάξης.
- `track_sections`: πραγματικές ομάδες προσανατολισμού.
- `choice_option_sections`: πραγματικές ομάδες μιας επιλογής, π.χ. Γαλλικά / Γερμανικά / Ιταλικά.
- `choice_sections`: πραγματικές ομάδες συγκεκριμένης επιλογής μέσα σε κοινό choice set, π.χ. Μαθηματικά ή Βιολογία στη Γ΄ ΓΕΛ Θετικών / Υγείας.
- `conditional_sections`: πραγματικές ομάδες μαθημάτων που εμφανίζονται μόνο υπό προϋπόθεση.
- `ethics`: πραγματικά δεδομένα απαλλαγών / εμπρόθεσμης δήλωσης / απόφασης συγκρότησης Ηθικής.

Κρίσιμη εγγύηση: ο αριθμός `general_sections` δεν χρησιμοποιείται ως υποκατάστατο του αριθμού ομάδων προσανατολισμού.

## 2η ξένη γλώσσα

Στο timetable dataset προστέθηκε internal `profile_choice_id = second_foreign_language` στις σχετικές γραμμές Γυμνασίου και ΓΕΛ.

Το school profile μπορεί πλέον να δηλώσει πραγματικές ομάδες ανά τάξη και γλώσσα. Το workload layer δημιουργεί ξεχωριστό staffing unit για κάθε ενεργή επιλογή και χρησιμοποιεί το πραγματικό resolved assignment target της επιλογής:

- Γαλλικά → ΠΕ05
- Γερμανικά → ΠΕ07
- Ιταλικά → ΠΕ34 (Γυμνάσιο)

Δεν αθροίζονται ανύπαρκτες επιλογές και δεν εφαρμόζεται μία γενική ανάθεση σε όλες τις γλώσσες.

## Ομάδες Προσανατολισμού ΓΕΛ

Προστέθηκε internal `profile_track` στα μαθήματα:

- Β΄: Ανθρωπιστικών / Θετικών
- Γ΄: Ανθρωπιστικών / Θετικών και Υγείας / Οικονομίας και Πληροφορικής

Οι ώρες πραγματοποιούνται πλέον με βάση `orientation_sections` του πραγματικού σχολείου και όχι με βάση τα κανονικά τμήματα της τάξης.

## Γ΄ ΓΕΛ — 2ο / 3ο επιστημονικό πεδίο

Οι γραμμές:

- `gel.c.health.mathimatika`
- `gel.c.health.viologia`

ανήκουν στο `choice_set_id = gel.c.health.field_choice`.

Το profile απαιτεί πραγματικές ομάδες `Μαθηματικά` / `Βιολογία` και δεν συναγάγει τον αριθμό τους από τα γενικά τμήματα ή από το συνολικό πλήθος ομάδων Θετικών / Υγείας.

## Conditional μαθήματα Γ΄ ΓΕΛ

Για τα conditional μαθήματα Γενικής Παιδείας της Γ΄ χρησιμοποιείται `conditional_sections` με ρητό πραγματικό αριθμό teaching groups. Δεν γίνεται αυτόματη συναγωγή από `general_sections` ή `track_sections`, επειδή η πραγματική ομαδοποίηση μπορεί να διαφέρει.

## Ηθική μέσα στο school profile

Το `school-profile.php` συνδέθηκε με τον υπάρχοντα `ethics-class-formation.php`.

Το αποτέλεσμα της αξιολόγησης της Ηθικής μετατρέπεται πλέον σε πραγματικό αριθμό teaching groups:

- κάτω από το απαιτούμενο όριο → 0 groups Ηθικής,
- μία κανονική τάξη / παράλληλη διδασκαλία → διατηρείται το group Θρησκευτικών και προστίθεται 1 παράλληλο group Ηθικής,
- πολλαπλά τμήματα με ισοδύναμο τμήμα Ηθικής → το αντίστοιχο πλήθος αφαιρείται από τα groups Θρησκευτικών, χωρίς αύξηση του συνολικού πλήθους τμημάτων,
- πολλαπλά τμήματα χωρίς ισοδύναμο τμήμα → groups Θρησκευτικών αμετάβλητα + 1 παράλληλο group Ηθικής.

Η δομική readiness του profile δεν αποτυγχάνει αν δεν έχουν ακόμη δοθεί στοιχεία Ηθικής: τότε η Ηθική παραμένει dependency και δεν κατασκευάζεται τεχνητή ώρα.

## Backward compatibility

Η προσθήκη των νέων semantics στο κοινό `school-profile.php` διατηρεί την προηγούμενη συμπεριφορά των ήδη υπαρχόντων profiles.

Ειδικά, για choice sets ισχύει ο παλιός ασφαλής κανόνας: όταν ένα `choice_set_id` υπάρχει στο profile αλλά συγκεκριμένο course δεν έχει επιλεγεί, ο αριθμός sections είναι 0 και δεν γίνεται fallback στα `general_sections`.

Έτσι το ΕΝ.Ε.Ε.ΓΥ.-Λ. Κέρκυρας παραμένει ακριβώς στα 396 fixed curriculum hours.

## Synthetic contract

Νέο test: `tests/general-gymnasium-gel-school-profile-contract.py`.

### Synthetic Ημερήσιο Γυμνάσιο

- fixed teaching-group hours: 171
- assignment-unit hours: 171
- assignment units: 61
- unresolved dependencies: 0
- contract: 35/35 PASS (μαζί με GEL assertions)

Ελέγχονται ξεχωριστά Γαλλικά / Γερμανικά / Ιταλικά και τρία διαφορετικά σενάρια Ηθικής.

### Synthetic Ημερήσιο ΓΕΛ

- fixed teaching-group hours: 293
- assignment-unit hours: 293
- assignment units: 52
- unresolved dependencies: 0

Ελέγχονται:

- Β΄ Ανθρωπιστικών και Θετικών με πραγματικό πλήθος orientation groups,
- Γ΄ Ανθρωπιστικών / Θετικών-Υγείας / Οικονομίας-Πληροφορικής,
- Μαθηματικά 2ου πεδίου και Βιολογία 3ου πεδίου ως ξεχωριστές ομάδες,
- conditional Μαθηματικά / Ιστορία με ρητό group count,
- dedicated-equivalent, consolidated-parallel και below-threshold σενάρια Ηθικής.

## Regression results

- General Gymnasium / GEL profile contract: **35/35 PASS**
- Personnel workload: **42/42 PASS**
- School profile workload matrix: **49/49 PASS**
- ENEEGYL school profile: **30/30 PASS**
- EEEEK school profile: **47/47 PASS**
- EEEEK cross-audit: **50/50 PASS**
- ENEEGYL crosswalk: **71/71 PASS**
- Workload aggregation: **364/364 PASS**
- Workload model: **7383/7383 PASS**
- Teaching timetable cross-audit: **2191/2191 PASS**
- Weekly timetable: **496/496 PASS**
- Teaching assignments: **51/51 PASS**
- Ethics formation: **24/24 PASS**
- Teaching-hours JS regression: **70 PASS**
- PHP lint: **77/77 files clean**

## Επόμενη χρήση

Για πραγματικό Ημερήσιο Γυμνάσιο απαιτούνται:

1. κανονικά τμήματα ανά τάξη,
2. πραγματικές ομάδες 2ης ξένης γλώσσας,
3. στοιχεία Ηθικής ανά τάξη.

Για πραγματικό Ημερήσιο ΓΕΛ απαιτούνται επιπλέον:

1. ομάδες προσανατολισμού Β΄ και Γ΄,
2. ομάδες Μαθηματικών / Βιολογίας στη Γ΄ Θετικών-Υγείας,
3. πραγματικές ομάδες των conditional μαθημάτων της Γ΄.

Με αυτά τα inputs το ίδιο personnel layer μπορεί να ελέγξει σχέδιο αναθέσεων χωρίς ειδικό κώδικα για συγκεκριμένο σχολείο.
