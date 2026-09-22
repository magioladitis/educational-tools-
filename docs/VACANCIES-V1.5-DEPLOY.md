# Vacancies v1.5 — αναβάθμιση από v1.4

1. Πάρε backup της `mmagiolad_vacancies`.
2. Στο phpMyAdmin κάνε import το `sql/vacancies-v1.5-accounts-school-profile.sql`.
3. Ανέβασε τα αρχεία της v1.5.
4. Μπες με το υπάρχον pilot admin και άνοιξε `Λογαριασμοί`.
5. Δημιούργησε έναν πραγματικό admin. Αντέγραψε τον προσωρινό κωδικό εκείνη τη στιγμή.
6. Κάνε logout, μπες με τον νέο admin και άλλαξε υποχρεωτικά τον προσωρινό κωδικό.
7. Δημιούργησε λογαριασμό για κάθε σχολείο που θα συμμετέχει. Το username του σχολείου είναι ο κωδικός Υπουργείου.
8. Όταν ολοκληρωθούν οι δοκιμές, βάλε `dev_mode => false` στο `includes/vacancies-config.php`.

## Αρχεία που αλλάζουν στη v1.5
- `kena-sxoleion-login.php`
- `kena-sxoleion.php`
- `kena-sxoleion-admin.php`
- `kena-sxoleion-users.php` (νέο)
- `kena-sxoleion-password.php` (νέο)
- `includes/vacancies-auth.php`
- `includes/vacancies-model.php`
- `assets/vacancies.css`
- `sql/vacancies-v1.5-accounts-school-profile.sql` (migration)

## Σημαντικό
Μην αποστείλεις πραγματικά usernames/passwords σε σχολεία όσο ο browser εμφανίζει προειδοποίηση για το HTTPS πιστοποιητικό του hostname.
