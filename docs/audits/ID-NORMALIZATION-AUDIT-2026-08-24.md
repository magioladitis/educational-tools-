# Audit κανονικοποίησης IDs πεδίων — 24/08/2026

Βάση: master 3.20.48 μετά το tri-state proof γλωσσομάθειας της Σιβιτανιδείου.
Νέα έκδοση μετά το cleanup: **3.20.49**.

## Στόχος

Να ισχύει ο κανόνας:

- ίδιο business concept → ίδιο canonical field ID,
- διαφορετικό business concept / διαφορετική μονάδα → δεν εξαναγκάζεται σε κοινό ID,
- form-field IDs σε camelCase,
- shared components να μη διατηρούν legacy DOM-ID aliases,
- δυναμικά IDs να ακολουθούν την ίδια σύμβαση.

## Αλλαγές που έγιναν

### 1. Κλάδος / ειδικότητα → `specialty`

Τα DOM fields `id="branch"` καταργήθηκαν από:

- `ypologismos-morion-1gt-2024.php`
- `ypologismos-morion-2ea-2025.php`
- `ypologismos-morion-4ea-2025.php`

και έγιναν `id="specialty"`.

Το shared TE component/controller ευθυγραμμίστηκε επίσης:

- `branch_id` → `specialty_id`
- `data-branch-id` → `data-specialty-id`
- `branchId()` → `specialtyId()`
- state key `branch` → `specialty`
- exported `branchFamily` → `specialtyFamily`

Η κανονικοποίηση του ίδιου του κωδικού ειδικότητας εξακολουθεί να γίνεται από `EducationCore.normalizeSpecialtyCode()`.

### 2. Training proof IDs

Η δεύτερη οικογένεια IDs καταργήθηκε:

- `trainingDatesYes` → `trainingProofDatesYes`
- `trainingDatesNo` → `trainingProofDatesNo`
- `trainingDatesStatus` → `trainingProofDatesStatus`
- radio name `trainingDates` → `trainingProofDates`

Το ίδιο canonical naming χρησιμοποιείται πλέον σε όλα τα ASEP calculators που έχουν TrainingProof.

### 3. ΔΗΜ.Ω.Σ. γλώσσες και δυναμικά IDs

- `languageName1` → `language1`
- `languageName2` → `language2`
- παλιά point fields `language1/language2` → `languageLevel1/languageLevel2`
- `${id}_solo` → `${id}Solo`
- `${id}_group` → `${id}Group`

Άρα η γλωσσική οικογένεια είναι πλέον συνεπής με τα ΣΔΕ εργαλεία:
`language1`, `languageLevel1`, `language2`, `languageLevel2`.

### 4. Ωνάσεια — δυναμικές γραμμές προϋπηρεσίας

- `serviceYear_1` → `serviceYear1`
- `serviceMonths_1` → `serviceMonths1`

και αντίστοιχα για όλες τις δυναμικές γραμμές.

### 5. 2ΕΑ — δυναμικά IDs επαγγελματικών δικαιολογητικών

- `licenseReq_0` → `licenseReq0`

και αντίστοιχα για όλες τις δυναμικές εγγραφές.

### 6. Σιβιτανίδειος — κοινωνικά κριτήρια

- `threeChildrenParent` → `threeChildren`
- `manyChildrenMember` → `manyChildren`
- `singleParentMember` → `singleParent`

Το calculation engine ενημερώθηκε στα ίδια canonical keys.

### 7. Debug/render artifacts

Αφαιρέθηκαν από το production root τα παλιά:

- `__phase1_ypologismos-morion-3ea-2025.php.html`
- `__phase1_ypologismos-morion-apospasis-evropaika-scholeia.php.html`
- `__phase1_ypologismos-morion-apospasis-sde.php.html`
- `__phase1_ypologismos-morion.php.html`
- `__sde_rendered.html`

Τα ενεργά test snapshots ανανεώθηκαν κάτω από `tests/rendered/`.

## Canonical families που επιβεβαιώθηκαν

- `specialty`
- `degreeGrade`
- `mscCount`
- `pedagogical`
- `signLanguage`
- `braille`
- `seminar400`
- `regularMonths`
- `difficultMonths`
- `threeMonthRegular2020`
- `threeMonthDifficult2020`
- `threeMonthRegular2021`
- `threeMonthDifficult2021`
- `marriageYears4Plus`
- `candidateMentalCondition`
- `trainingProofDatesYes/No/Status`

## Σκόπιμες διαφορές που ΔΕΝ πρέπει να κανονικοποιηθούν βίαια

- `secondTitle` και `secondDegree`: δεν είναι πάντα το ίδιο νομικό προσόν.
- `master` και `mscCount`: το πρώτο σε μη-ASEP εργαλεία μπορεί να είναι boolean/κατηγορία, το δεύτερο είναι count στον PE academic engine.
- `workMonths` Σιβιτανιδείου και `regularMonths` ΑΣΕΠ: είναι διαφορετική μορφή εμπειρίας και διαφορετικός κανόνας.
- page-specific selectors όπως `selectedBranch` στα Ευρωπαϊκά Σχολεία περιγράφουν ειδική επιλογή θέσης και δεν είναι ο κοινός ASEP specialty selector.

## Έλεγχοι

- 26 root PHP pages rendered.
- 505 form-field occurrences.
- 289 unique form-field IDs.
- **0 duplicate IDs** ανά rendered page.
- **0 broken `label for` references**.
- **0 form-field IDs με underscore ή hyphen separators**.
- **239/239 ID-normalization contract PASS**.
- **43/43 PHP lint PASS**.
- **38/38 JavaScript syntax PASS**.
- **17/17 regression/architecture test files PASS**.
- TE academic regression: **19/19 PASS**.
- TE academic architecture: **42/42 PASS**.
- Service/Social architecture: **67/67 PASS**.

## Συμπέρασμα

Η κανονικοποίηση των IDs των πεδίων θεωρείται πλέον **κλεισμένη** για το τρέχον production tree. Νέα πεδία πρέπει να ακολουθούν camelCase και να επαναχρησιμοποιούν canonical IDs μόνο όταν το business concept είναι πραγματικά το ίδιο.
