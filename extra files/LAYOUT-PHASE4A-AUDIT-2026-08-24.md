# Layout Phase 4A Audit — card internals

Ημερομηνία: 24/08/2026

## Στόχος

Το Phase 4A ενοποιεί μόνο τρία πολύ ομοιόμορφα presentation patterns μέσα στα calculator cards, χωρίς αλλαγή business logic, JavaScript calculations ή CSS:

1. `section-head`
2. `actions`
3. απλά `result-row`

Η βάση είναι το Phase 3, όπου και τα 15 calculators είχαν ήδη μεταφερθεί στο κοινό `includes/components/calculator-layout.php`.

## Αλλαγές στο κοινό layout component

Το `calculatorCardStart()` επεκτάθηκε ώστε τα headings να μπορούν να διατηρούν attributes/IDs:

- `title_attrs`
- `subtitle_attrs`
- `cap_attrs`

Αυτό είναι απαραίτητο για runtime hooks όπως:

- `experienceSubtitle`
- `teachingMax`
- `adminMax`

Προστέθηκε επίσης ο presentation-only helper:

```php
calculatorResultRow(array(
    'label_html' => 'Προϋπηρεσία',
    'value_html' => '0,00',
    'value_id' => 'servicePoints'
));
```

Ο helper υποστηρίζει attributes/IDs τόσο στο row όσο και στα `span` / `strong`, ώστε να διατηρούνται πλήρως τα υφιστάμενα JS hooks και states (`hidden`, `emphasis`, κ.λπ.).

## 1. `section-head`

Μεταφέρθηκαν **15/15** SDE section headers στο `calculatorCardStart(..., 'header_variant' => 'section-head')`.

Μετά τη μεταφορά:

- literal `<div class="section-head">` στα calculator PHP: **0**
- shared section-head configs: **15**

Διατηρήθηκαν τα δυναμικά IDs `experienceSubtitle`, `teachingMax`, `adminMax`.

## 2. `actions`

Μεταφέρθηκαν όλα τα **14 page-level literal action blocks** στο ήδη υπάρχον `calculatorActions()`.

Μαζί με τα δύο legacy εργαλεία που χρησιμοποιούσαν ήδη τον helper, υπάρχουν πλέον **16 page-level `calculatorActions()` calls**.

Μετά τη μεταφορά:

- literal `<div class="actions">` στα calculator PHP: **0**

Τα δύο action blocks του shared Digital Tutoring component παραμένουν component-owned και δεν μετακινήθηκαν.

## 3. `result-row`

Το αρχικό audit είχε υποεκτιμήσει τα page-source literals λόγω του τρόπου εξαγωγής. Η τελική source-level καταμέτρηση βρήκε **62 static result rows** και μεταφέρθηκαν **όλα τα 62** στον νέο `calculatorResultRow()` helper.

Μετά τη μεταφορά:

- static literal result rows στα calculator PHP: **0**
- `calculatorResultRow()` calls: **62**
- literal `result-row` που παραμένει: **1**, σκόπιμα, μέσα σε JavaScript runtime generation του breakdown στο `ypologismos-morion-apospasis-sde.php`

Το runtime-generated row δεν αποτελεί PHP presentation duplication και δεν πρέπει να μεταφερθεί στον PHP helper.

## DOM parity

Έγινε PHP render πριν και μετά για **όλους τους 15 calculators** και δομική σύγκριση DOM με κανονικοποίηση μόνο whitespace.

Αποτέλεσμα: **15/15 PASS**.

Διατηρήθηκαν:

- tags / nesting
- attributes
- IDs
- `data-*`
- visible text
- scripts
- hidden/emphasis states
- dynamic hooks όπως `primaryResultLabel`, `criteriaRow`, `interviewRow`, `teachingMax`, `adminMax`, `experienceSubtitle`

Duplicate IDs μετά το refactor: **0 σε 15/15 σελίδες**.

## Regression / architecture tests

Υπάρχοντα maintained tests: **538/538 PASS**.

Νέο `tests/layout-phase4a-contract.py`: **47/47 PASS**.

Συνολικά: **585/585 PASS**.

Επιπλέον:

- PHP syntax: **37/37 PASS**
- production JavaScript syntax: **25/25 PASS**
- JavaScript μαζί με tests: **31/31 PASS**
- local dependency references: **238 / 0 missing**
- production JavaScript files changed: **0**
- CSS files changed: **0**

## Production files που άλλαξαν

1. `includes/components/calculator-layout.php`
2. `ypologismos-morion.php`
3. `ypologismos-morion-1ea-2025.php`
4. `ypologismos-morion-1gt-2024.php`
5. `ypologismos-morion-2ea-2025.php`
6. `ypologismos-morion-3ea-2025.php`
7. `ypologismos-morion-4ea-2025.php`
8. `ypologismos-morion-apospasis-dimos.php`
9. `ypologismos-morion-apospasis-evropaika-scholeia.php`
10. `ypologismos-morion-apospasis-exoteriko.php`
11. `ypologismos-morion-apospasis-psifiako-frontistirio.php`
12. `ypologismos-morion-apospasis-sde.php`
13. `ypologismos-morion-diefthynton-ypodiefthynton-sde.php`
14. `ypologismos-morion-mitroo-sde.php`

Τα δύο legacy calculators `ypologismos-morion-onaseia.php` και `ypologismos-morion-apospasis.php` δεν χρειάστηκαν αλλαγή στο Phase 4A, επειδή τα action rows τους ήταν ήδη shared και δεν χρησιμοποιούν το ίδιο result-row model.

## Συμπέρασμα

Το Phase 4A αφαιρεί την επανάληψη από τα τρία πιο σταθερά internal presentation patterns χωρίς να δημιουργεί generic field framework. Τα `field`, `field-grid`, notices, questions και tool-specific score/total families παραμένουν σκόπιμα page/domain-specific.
