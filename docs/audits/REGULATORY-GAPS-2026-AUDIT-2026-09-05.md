# Regulatory gaps 2026 — Ωρολόγιο ↔ Αναθέσεις

**Ημερομηνία audit:** 05/09/2026

## Συμπέρασμα

Ο συνολικός cross-audit δεν έχει 29 «τεχνικά unresolved» που μπορούν να συμπληρωθούν με αντιστοίχιση τίτλου. Έχει **29 επιβεβαιωμένες κανονιστικές περιπτώσεις** (25 γραμμές δεδομένων), στις οποίες το ισχύον ωρολόγιο προβλέπει μάθημα/τάξη χωρίς ρητή αντίστοιχη ανάθεση στο ισχύον κανονιστικό context.

Για τον λόγο αυτό το status παραμένει `regulatory_gap`. Κάθε τέτοια γραμμή φέρει πλέον εσωτερικά τα `assignment_gap_confirmed`, `assignment_gap_kind`, `assignment_gap_timetable_source`, `assignment_gap_assignment_source` και `assignment_gap_inference_guard`. Τα metadata αφαιρούνται από το browser payload και προορίζονται για audit και για τη μελλοντική μηχανή κατανομής ωρών.

## Κανόνας ασφαλείας

- Δεν δανειζόμαστε ανάθεση από άλλη τάξη, ειδικότητα ή context επειδή ο τίτλος είναι ίδιος/παρόμοιος.
- Δεν επαναφέρουμε καταργημένο πίνακα αναθέσεων.
- Νέα έκδοση μαθήματος (π.χ. «ΙΙ») δεν εξομοιώνεται αυτόματα με προγενέστερο μάθημα.

## Κανονιστικές πηγές που διασταυρώθηκαν

- ΕΝ.Ε.Ε.ΓΥ.-Λ. ωρολόγιο: Υ.Α. 44451/Δ3/08-04-2026, ΦΕΚ Β΄ 2149/2026.
- ΕΝ.Ε.Ε.ΓΥ.-Λ. αναθέσεις: Υ.Α. 69785/Δ3/29-05-2026, ΦΕΚ Β΄ 3216/2026.
- Μουσικά Σχολεία ωρολόγιο: ΦΕΚ Β΄ 2107/09-04-2026.
- Μουσική Παιδεία — αναθέσεις: Υ.Α. 144236/Δ2/2018, ΦΕΚ Β΄ 4202/25-09-2018.
- Γενική Παιδεία Γυμνασίου/ΓΕΛ — αναθέσεις: Υ.Α. 54058/Δ2/05-05-2026, ΦΕΚ Β΄ 2583/07-05-2026.
- Η Υ.Α. 94541/Δ2/2015 (ΦΕΚ Β΄ 1356/2015) έχει καταργηθεί από την Υ.Α. 144236/Δ2/2018 και δεν χρησιμοποιείται για να συμπληρωθούν τα νέα μαθήματα του Μουσικού Γυμνασίου.

## ΕΝ.Ε.Ε.ΓΥ.-Λ. — Γ΄ Λυκείου

**17 γραμμές / 17 grade-instances**

| course_id | Μάθημα | Τάξη/ώρες | gap kind | guard |
|---|---|---|---|---|
| eneegyl.lykeio.c.agriculture.5 | Σύγχρονες Γεωργικές Επιχειρήσεις | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.agriculture.6 | Αρχές Βιολογικής Γεωργίας | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.admin.5 | Αρχές Οικονομικής Θεωρίας | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.admin.6 | Αρχές Οργάνωσης και Διοίκησης | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.building.5 | Αρχιτεκτονικό Σχέδιο | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.building.6 | Οικοδομική | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.arts.4 | Ιστορία Σύγχρονης Τέχνης | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.arts.5 | Τεχνολογία Υλικών | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.electrical.4 | Ψηφιακά Συστήματα | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.electrical.5 | Δίκτυα Υπολογιστών | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.mechanical.5 | Στοιχεία Μηχανών | Γ΄: 3 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.it.4 | Προγραμματισμός Υπολογιστών | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.it.5 | Δίκτυα Υπολογιστών | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.health.6 | Ανατομία - Φυσιολογία ΙΙ | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.health.7 | Υγιεινή | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.health.fallback.5 | Ανατομία - Φυσιολογία ΙΙ | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |
| eneegyl.lykeio.c.health.fallback.6 | Υγιεινή | Γ΄: 2 | same_grade_assignment_missing | no_cross_grade_or_specialty_borrow |

## Μουσικό Γυμνάσιο

**2 γραμμές / 4 grade-instances**

| course_id | Μάθημα | Τάξη/ώρες | gap kind | guard |
|---|---|---|---|---|
| mgym.theatro | Θέατρο | Α΄: 1, Β΄: 1, Γ΄: 1 | current_timetable_subject_missing_from_current_assignment_tables | do_not_revive_repealed_2015_assignment |
| mgym.istoria_texnis | Ιστορία Τέχνης | Α΄: 1 | current_timetable_subject_missing_from_current_assignment_tables | do_not_revive_repealed_2015_assignment |

## Γενικό Μουσικό Λύκειο

**6 γραμμές / 8 grade-instances**

| course_id | Μάθημα | Τάξη/ώρες | gap kind | guard |
|---|---|---|---|---|
| mgel.music.elliniki_paradosiaki | Ελληνική Παραδοσιακή Μουσική | Α΄: 2, Β΄: 2, Γ΄: 2 | current_timetable_subject_missing_from_music_assignment_table | no_title_similarity_inference |
| mgel.c.choice.elliniki_paradosiaki | Ελληνική Παραδοσιακή Μουσική | Γ΄: 2 | current_timetable_subject_missing_from_music_assignment_table | no_title_similarity_inference |
| mgel.c.choice.mousiko_keimeno | Επεξεργασία Μουσικού Κειμένου με Η/Υ | Γ΄: 2 | assignment_exists_only_other_grade | no_cross_grade_borrow |
| mgel.c.choice.analysi_partitouras | Ανάλυση Παρτιτούρας Ορχήστρας | Γ΄: 2 | current_timetable_subject_missing_from_music_assignment_table | no_title_similarity_inference |
| mgel.c.choice.choral | Επεξεργασία Χορικού (Choral) | Γ΄: 2 | current_timetable_subject_missing_from_music_assignment_table | no_title_similarity_inference |
| mgel.c.choice.ixolipsia2 | Στοιχειώδεις αρχές ηχοληψίας ΙΙ | Γ΄: 2 | new_course_version_without_explicit_assignment | do_not_map_version_ii_to_first_course |

## Ειδικό εύρημα ΕΝ.Ε.Ε.ΓΥ.-Λ.

Το ΦΕΚ Β΄ 3216/2026 περιλαμβάνει ρητό τμήμα «Αναθέσεις μαθημάτων τομέων των Β΄ και Γ΄ τάξεων». Για τα 17 παραπάνω μαθήματα της νέας Γ΄ δεν υπάρχει αντίστοιχη γραμμή σε αυτό το τμήμα. Αρκετοί ίδιοι ή συναφείς τίτλοι εμφανίζονται αργότερα στους πίνακες της Δ΄ τάξης/ειδικοτήτων. Αυτό καταγράφεται ως χρήσιμη ένδειξη για μελλοντική θεσμική διόρθωση, όχι ως νόμιμη βάση για αυτόματη μεταφορά ανάθεσης.

## Ειδικό εύρημα Μουσικών Σχολείων

Το ΦΕΚ Β΄ 2107/2026 περιλαμβάνει νέα/αναδιαρθρωμένα μαθήματα και επιλογές. Το ΦΕΚ Β΄ 4202/2018 δεν δίνει ρητή ίδιας τάξης ανάθεση για τις περιπτώσεις του πίνακα. Ιδίως η «Επεξεργασία Μουσικού Κειμένου με Η/Υ» έχει ρητή ανάθεση ως επιλογή άλλης τάξης, ενώ η «Στοιχειώδεις αρχές ηχοληψίας ΙΙ» δεν εξομοιώνεται με την παλαιότερη «Στοιχειώδεις Αρχές Ηχοληψίας».

## Regression contract

Το `tests/regulatory-gaps-2026-contract.py` κλειδώνει τα ακριβή 25 course IDs / 29 grade-instances, τις πηγές και τα inference guards. Οποιαδήποτε μελλοντική μείωση του αριθμού των gaps πρέπει να συνοδεύεται από νέα ρητή κανονιστική πηγή και όχι από fuzzy title matching.
