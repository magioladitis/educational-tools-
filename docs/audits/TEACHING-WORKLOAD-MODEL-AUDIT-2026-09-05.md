# Teaching Workload Model — Audit 2026-09-05

## Στόχος

Να δημιουργηθεί ένα καθαρά εσωτερικό layer που ενώνει τα δύο ήδη ελεγμένα datasets:

`μάθημα → εβδομαδιαίες ώρες → κανονιστικό context → Α΄/Β΄/Γ΄ ανάθεση → κλάδος εκπαιδευτικού`

χωρίς καμία αλλαγή στο οπτικό αποτέλεσμα των δημόσιων εργαλείων.

## Υλοποίηση

Προστέθηκε το:

- `includes/teaching-workload-model.php`

Το αρχείο δεν γίνεται `require` από:

- `orologio-programma-mathimaton.php`
- `anatheseis-mathimaton.php`

και συνεπώς δεν μεταβάλλει HTML, CSS, JavaScript ή browser payload.

Κάθε εγγραφή του μοντέλου έχει σταθερό `instance_id = course_id@grade` και διατηρεί το σχολείο, την τάξη, την ομάδα, τον τομέα/ειδικότητα όπου υπάρχει, τις ώρες και τον τρόπο επίλυσης της ανάθεσης.

## Κατάσταση των 2.037 περιπτώσεων

| resolution_status | Πλήθος |
|---|---:|
| `direct` | 1.792 |
| `alias` | 99 |
| `components` | 80 |
| `choice_dependent` | 31 |
| `thematic_dependent` | 6 |
| `regulatory_gap` | 29 |
| **Σύνολο** | **2.037** |

Δεν παραμένει καμία περίπτωση `unresolved_assignment` ή `ambiguous_assignment_context`.

## Context-aware resolution

Η αντιστοίχιση δεν βασίζεται αποκλειστικά στον τίτλο του μαθήματος. Ο resolver σταθμίζει, όπου υπάρχουν:

1. ακριβές `assignment_section`,
2. ειδικότητα,
3. ομάδα/κατεύθυνση,
4. τομέα.

Αυτό αποτρέπει λάθος ένωση ομώνυμων μαθημάτων σε διαφορετικές ειδικότητες. Contract example:

- `epal.g.mechanical_installations.05@Γ΄` / «Στοιχεία Ψύξης - Κλιματισμού» → Α΄ `ΠΕ82, ΤΕ01.04`, Β΄ `ΤΕ02.02`.
- `epal.g.cooling.02@Γ΄` / ίδιος τίτλος → Α΄ `ΠΕ82`.

## Θ/Ε components

Οι 80 ωρολογιακές γραμμές που έχουν ενιαίο Θ+Ε αλλά διαφορετικές αναθέσεις ανά μέρος παραμένουν `components`.

- 80 workload instances
- 160 component targets
- 160/160 resolved
- το άθροισμα των component hours ισούται με τις ώρες του αντίστοιχου ωρολογιακού μαθήματος

Δεν επιτρέπεται downstream engine να εφαρμόσει την ανάθεση της θεωρίας στο εργαστήριο ή αντίστροφα.

## Choice-dependent περιπτώσεις

Υπάρχουν 31 grade instances με πραγματική επιλογή. Το workload model παράγει 173 choice branches και 173/173 οδηγούν σε πραγματικό assignment target.

Ιδιαίτερα:

- οι ξένες γλώσσες περιορίζονται στον κλάδο της επιλεγμένης γλώσσας,
- η Β΄ Π.ΕΠΑ.Λ. Υγείας διατηρεί κοινό choice group `pepal.b.health.special_courses`, `required=2`, `distinct=true`,
- στις επιλογές Υγείας με διαφορετική θεωρία/εργαστήριο διατηρούνται χωριστά assignment targets,
- όταν το ωρολόγιο δεν δίνει ασφαλή κατανομή ωρών ανά component, το μοντέλο δηλώνει `component_hours_status=not_fixed_by_timetable_bridge` αντί να επινοήσει ώρες.

Στο ΕΝ.Ε.Ε.ΓΥ.-Λ. οι label-only επιλογές Υγείας συνδέονται μόνο με assignment subjects του ίδιου school/grade/context που έχουν το ίδιο κανονιστικό prefix (π.χ. `Μικροβιολογία Ι — Θεωρία/Εργαστήριο`). Η προέλευση αυτή δηλώνεται ρητά με `targets_derived_from_assignment_prefix=true`.

## Α΄ Π.ΕΠΑ.Λ.

Τα 6 ενιαία blocks παραμένουν `thematic_dependent`.

Το μοντέλο συνδέει κάθε block με τις πραγματικές θεματικές γραμμές αναθέσεων, αλλά δηλώνει:

`component_hours_status=not_fixed_by_regulation`

ώστε να μη δημιουργηθεί τεχνητή κατανομή των 2Ε/3Ε σε υποενότητες.

## Regulatory gaps

Και οι 29 επιβεβαιωμένες περιπτώσεις παραμένουν `regulatory_gap`:

- `assignment = null`
- `confirmed = true`
- source metadata διατηρούνται
- `inference_guard` διατηρείται

Άρα το μελλοντικό placement engine θα μπορεί να αρνείται assignment αντί να «δανείζεται» κλάδο από άλλη τάξη ή άλλο κανονιστικό context.

## Ώρες ανά τετράμηνο

Εντοπίζονται 6 grade instances με `period_hours`.

Για αυτές:

- `hours_mode = periodic`
- διατηρείται ο πίνακας ανά τετράμηνο
- δεν παράγεται `hours_total`

Για όλες τις υπόλοιπες:

- `hours_mode = fixed`
- `hours_total = hours_value`

Αυτό εμποδίζει το επόμενο layer να θεωρήσει π.χ. μια κατανομή `1 / 2` ως σταθερή εβδομαδιαία ώρα για όλο το σχολικό έτος.

## Special all-PE assignments

Το normalized assignment payload διατηρεί επίσης τους ειδικούς κανονιστικούς δείκτες:

- `A_all_pe`
- `A_all_pe_note`
- `special_all_pe`
- `special_note`
- `B_all_others` / `C_all_others`

ώστε μαθήματα όπως ΣΕΠ ή Ζώνη Δημιουργικών Δραστηριοτήτων να μη χάσουν τη θεσμική σημασιολογία τους επειδή δεν έχουν απλό array κωδικών ΠΕ.

## Tests

Νέο contract:

- `tests/teaching-workload-model-contract.py`: **4948 PASS / 0 FAIL**

Στοχευμένα regressions μετά την αλλαγή:

- teaching timetable cross-audit: **1010/1010**
- weekly timetable: **496/496**
- teaching assignments: **51/51**
- regulatory gaps: **37/37**
- ΕΝ.Ε.Ε.ΓΥ.-Λ.: **71/71**
- Μουσικά: **43/43**
- Α΄ ΕΠΑ.Λ.: **52/52**
- Β΄ ΕΠΑ.Λ.: **96/96**
- Γ΄ ΕΠΑ.Λ.: **133/133**
- Α΄ Π.ΕΠΑ.Λ.: **59/59**
- Β΄ Π.ΕΠΑ.Λ.: **204/204**
- Γ΄ Π.ΕΠΑ.Λ.: **32/32**
- general structures / myschool operational cross-check: **70/70**
- PHP lint: **66/66**

Το πλήρες repository-wide suite δεν χρησιμοποιείται ως acceptance criterion αυτού του βήματος, επειδή ξεπερνά το διαθέσιμο single-run execution window. Το νέο αρχείο δεν φορτώνεται από public runtime paths και όλα τα σχετικά regression surfaces πέρασαν.

## Επόμενο ασφαλές βήμα

Το μοντέλο είναι πλέον κατάλληλη βάση για να προστεθεί, επίσης server-side, aggregation ανά κλάδο:

`ΠΕxx → διαθέσιμα workload instances → ώρες Α΄/Β΄/Γ΄ ανάθεσης`

Η aggregation πρέπει να παραμένει choice/variant-aware και να μην προσθέτει ταυτόχρονα αμοιβαία αποκλειόμενα slots.
