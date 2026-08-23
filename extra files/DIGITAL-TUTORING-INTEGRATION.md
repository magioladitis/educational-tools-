# Ψηφιακό Φροντιστήριο — ενοποίηση 1ΓΕ/2ΓΕ και 3ΕΑ

## Στόχος

Η 1ΓΕ/2026–2ΓΕ/2026 και η 3ΕΑ/2025 πρέπει να χρησιμοποιούν **ακριβώς το ίδιο UI και την ίδια αριθμητική λογική** για την προϋπηρεσία στο Ψηφιακό Φροντιστήριο.

Η τελική δομή είναι:

```text
includes/
├── service-calculations.js
├── asep-digital-tutoring.js
└── components/
    └── asep-digital-tutoring-service.php
```

Το παλιό όνομα `asep-digital-tutoring-php56.php` καταργείται. Η συμβατότητα με παλιότερο PHP runtime παραμένει ιδιότητα της υλοποίησης και **δεν εμφανίζεται στο filename**.

## Κανόνες που κλειδώνουν στο κοινό module

| Σχολικό έτος | Μέγιστη πραγματική διάρκεια |
|---|---:|
| 2024–2025 | 9 μήνες και 16 ημέρες |
| 2025–2026 | 8 μήνες και 2 ημέρες |

Κοινή μοριοδότηση:

- 1,5 μόριο ανά πλήρη μήνα.
- Ανώτατο όριο 15 μορίων ανά σχολικό έτος.
- Τα υπόλοιπα ημερών καταχωρίζονται ανά σχολικό έτος.
- Τα υπόλοιπα ημερών **αθροίζονται μεταξύ των σχολικών ετών**.
- Κάθε 30 ημέρες δίνουν 1 επιπλέον μήνα.
- Το τελικό αποτέλεσμα της προϋπηρεσίας εξακολουθεί να περνά από το συνολικό όριο των 120 μορίων.

## 1. Αντικατάσταση component filename

Σβήσε/μην ανεβάσεις πλέον:

```text
includes/components/asep-digital-tutoring-php56.php
```

Χρησιμοποίησε:

```text
includes/components/asep-digital-tutoring-service.php
```

Σε κάθε PHP αρχείο που έχει παλιό require, άλλαξε μόνο το path:

```php
require_once __DIR__ . '/includes/components/asep-digital-tutoring-service.php';
```

## 2. Κοινό presentation block

Στις δύο σελίδες, το block του Ψηφιακού Φροντιστηρίου πρέπει να παράγεται αποκλειστικά με:

```php
<?php
renderAsepDigitalTutoringService(array(
    'container_id' => 'digitalTutoringService',
    'input_class' => 'service-months'
));
?>
```

Αν η 1ΓΕ/2ΓΕ δεν χρειάζεται την κλάση `service-months`, μπορεί να δοθεί κενή τιμή ή να παραλειφθεί η ρύθμιση. Η αριθμητική δεν εξαρτάται από αυτή την κλάση.

**Να αφαιρεθούν** από τις επιμέρους σελίδες:

- hard-coded `<select>` σχολικών ετών,
- hard-coded inputs μηνών/ημερών του Ψηφιακού Φροντιστηρίου,
- τοπικοί πίνακες ορίων `9/16`, `8/2`,
- τοπικές συναρτήσεις δημιουργίας/αφαίρεσης γραμμών,
- τοπικός κώδικας μετατροπής ημερών σε μήνες.

## 3. Scripts

Η σωστή σειρά είναι:

```html
<script src="includes/service-calculations.js"></script>
<script src="includes/asep-digital-tutoring.js"></script>
```

Το `asep-digital-tutoring.js` είναι UI/controller. **Δεν περιέχει business constants**· τα διαβάζει από `EducationService.RULES.digitalSchoolYears`.

## 4. Υπολογισμός στη 1ΓΕ/2ΓΕ

Στον υπολογισμό προϋπηρεσίας:

```js
const digitalResult = AsepDigitalTutoring.calculate('digitalTutoringService');

if (digitalResult.points > 0) {
  serviceParts.push(digitalResult.points);
  serviceDetails.push(
    "Ψηφιακό Φροντιστήριο: " + formatPoints(digitalResult.points) + " μόρια"
  );
}

if (digitalResult.extraMonths > 0) {
  serviceDetails.push(
    "Υπόλοιπα ημερών Ψηφιακού Φροντιστηρίου: " +
    digitalResult.remainderDays + " ημέρες → " +
    digitalResult.extraMonths + " επιπλέον μήνας/μήνες" +
    (digitalResult.remainingDays > 0
      ? " και " + digitalResult.remainingDays + " ημέρες υπόλοιπο"
      : "")
  );
}

warnings.push.apply(warnings, digitalResult.warnings || []);
```

Να αφαιρεθεί οποιοδήποτε παλιό:

```js
EducationService.digitalPerSchoolYear(...)
```

από τη ροή του νέου multi-year UI. Η παλιά συνάρτηση παραμένει μόνο για backwards compatibility άλλου κώδικα.

### Live recalculation

Επειδή οι γραμμές δημιουργούνται δυναμικά:

```js
document.addEventListener('asep-digital-tutoring-change', liveCalculatePoints);
```

### Reset

Μέσα στο υπάρχον `resetCalculator()`:

```js
AsepDigitalTutoring.reset('digitalTutoringService');
```

## 5. Υπολογισμός στη 3ΕΑ/2025

Στην `calcService()`:

```js
const digitalResult = AsepDigitalTutoring.calculate('digitalTutoringService');
raw += digitalResult.points;
```

Αν η σελίδα εμφανίζει warnings/details, πρόσθεσε και:

```js
warnings.push.apply(warnings, digitalResult.warnings || []);
```

ή πέρασέ τα στο υπάρχον warning container της 3ΕΑ.

Να αφαιρεθεί το παλιό:

```js
EducationService.digitalPerSchoolYear(num('digitalMonths')).points
```

### Live recalculation

```js
document.addEventListener('asep-digital-tutoring-change', render);
```

### Reset

Στο υπάρχον reset handler:

```js
AsepDigitalTutoring.reset('digitalTutoringService');
```

## 6. Source of truth

Μετά την αλλαγή:

| Θέμα | Μοναδικό σημείο |
|---|---|
| 2024–2025 = 9 μήνες + 16 ημέρες | `service-calculations.js` |
| 2025–2026 = 8 μήνες + 2 ημέρες | `service-calculations.js` |
| 1,5 μόριο/μήνα | `service-calculations.js` |
| 15 μόρια/σχολικό έτος | `service-calculations.js` |
| άθροιση ημερών / 30 → μήνας | `service-calculations.js` |
| δυναμικές γραμμές & duplicate-year lock | `asep-digital-tutoring.js` |
| κοινό HTML/κείμενα | `asep-digital-tutoring-service.php` |

Οι `ypologismos-morion.php` και `ypologismos-morion-3ea-2025.php` δεν πρέπει πλέον να γνωρίζουν τα αριθμητικά όρια `9/16` ή `8/2`.

## 7. Έλεγχος μετά την ενσωμάτωση

Αναζήτησε στα δύο κύρια PHP:

```text
9 μήνες και 16 ημέρες
8 μήνες και 2 ημέρες
digitalMaxMonths
digitalMaxDays
digitalPerSchoolYear
asep-digital-tutoring-php56
```

Τα business-rule strings/σταθερές δεν πρέπει να υπάρχουν πλέον εκεί. Οι τιμές `9/16` και `8/2` πρέπει να υπάρχουν μόνο στο `includes/service-calculations.js` και στα tests.
