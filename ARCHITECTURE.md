# Αρχιτεκτονική — Εργαλειοθήκη Εκπαιδευτικού

**Reference baseline:** v3.22.13 · 2026-09-26

Το αρχείο αυτό είναι ο σύντομος οδηγός συντήρησης της Εργαλειοθήκης. Πριν από κάθε αλλαγή πρέπει να είναι σαφές:

1. ποια είναι η **canonical πηγή αλήθειας**,
2. ποιο layer εφαρμόζει τον κανόνα,
3. ποιο contract αποδεικνύει ότι δεν δημιουργήθηκε drift.

> Κεντρική αρχή: **data → policy → pure calculations → UI**.  
> Δεν αντιγράφουμε κανονιστικά δεδομένα, eligibility tables, normalization regex ή optimizer rules μέσα σε UI handlers.

---

## 1. Η βασική ροή

```text
Νομικές / κανονιστικές πηγές
        ↓
Canonical PHP datasets
        ↓
School profile / workload model
        ↓
Server-built runtime contract
        ↓
Pure calculations (PHP reference ↔ JS parity)
        ↓
Browser UI
```

Για τη στελέχωση:

```text
weekly-timetable-data.php + teaching-assignments-data.php
        ↓
teaching-workload-model / crosswalk / aggregation
        ↓
school-profile.php
        ↓
school-profile-workload.php
        ↓
personnelWorkloadAllocationSlots()
        ↓
allocationSlots[].eligible_by_priority
        + staffing_optimizer_policy_v1
        ↓
personnel-workload-calculations.js
        ↓
staffing-simulator-ui.js
```

Το UI καταναλώνει αυτό το contract. Δεν ξανακατασκευάζει τους κανόνες μόνο του.

---

## 2. Canonical πηγές αλήθειας

| Θέμα | Canonical source | Κανόνας |
|---|---|---|
| ΦΕΚ / αποφάσεις / URLs | `includes/legal-sources.php` | Ένα registry. Όχι δεύτερα canonical links σε UI/datasets. |
| Κλάδοι και labels | `includes/teacher-specialties.php` | Τα datasets κρατούν codes, όχι δικά τους labels. |
| Specialty normalization | PHP `teacherSpecialtyCanonicalCode()` + JS `includes/specialty-code-normalization.js` | Schema: `teacher_specialty_code_normalization_v1`. |
| Αναθέσεις | `includes/teaching-assignments-data.php` + επιμέρους datasets | Μετά από αλλαγή regenerate scoped snapshots. |
| Ωρολόγια | `includes/weekly-timetable-data.php` + επιμέρους datasets | Ίδιος κανόνας: canonical first, snapshots derived. |
| Crosswalk | `includes/teaching-timetable-crosswalk.php` | Context-scoped aliases· δεν αλλοιώνουμε τίτλο ΦΕΚ για matching. |
| Πραγματική δομή σχολείου | `includes/school-profile.php` | Τμήματα/επιλογές/groups ανήκουν στο profile layer. |
| Workload matrix | `includes/school-profile-workload.php` | Regulatory gaps δεν γίνονται αυθαίρετα ώρες. |
| PHP workload reference | `includes/personnel-workload.php` | Reference/fallback + parity source. |
| Browser workload/optimizer | `includes/personnel-workload-calculations.js` | Pure shared module· όχι optimizer μέσα στο UI. |
| Optimizer policy | `personnelWorkloadOptimizerPolicy()` | Objective, ranks, B΄ limit, budgets, schemas. |
| PWA/browser head | `includes/head-pwa.php` | Manifest/favicon/SW bootstrap μία φορά. |
| Release version | `includes/config.php` + `service-worker.js` | Coordinated version/cache bump όταν αλλάζουν runtime assets. |

---

## 3. Νομικές πηγές

Το `includes/legal-sources.php` είναι το canonical registry. Για `search.et.gr/el/fek/` χρησιμοποιούμε συγκεκριμένο deep link/`fekId` όταν είναι διαθέσιμο, όχι τη γενική αναζήτηση.

Οι σχέσεις `amends/amended_by`, `corrects/corrected_by`, `supplements/supplemented_by` πρέπει να είναι αμφίδρομα συνεπείς.

```bash
php tests/legal-sources-contract-test.php
php tools/source-registry-audit.php
```

Αν ένα URL είναι λάθος στο UI, διορθώνεται στο registry — όχι μόνο εκεί όπου εμφανίζεται.

---

## 4. Ειδικότητες και normalization

Το `includes/teacher-specialties.php` είναι το registry κωδικών/labels. Ιστορικός κλάδος μπορεί να υπάρχει ως σημείωση, όχι να ξαναγίνει ενεργή ανάθεση. Παράδειγμα: πρώην `ΠΕ15` ως σημείωση προτεραιότητας του `ΠΕ80`.

Canonical schema:

```text
teacher_specialty_code_normalization_v1
```

Παραδείγματα:

```text
PE3    → ΠΕ03
ΠΕ4/1  → ΠΕ04.01
TE1-4  → ΤΕ01.04
DE1_5  → ΔΕ01.05
```

Το CSV import μπορεί να **εξάγει** code από ελεύθερο κείμενο, αλλά η τελική canonicalization περνά από το ίδιο shared normalizer. Δεν προσθέτουμε νέα regex normalization σε consumer modules.

```bash
python3 tests/specialty-code-normalization-parity-contract.py
python3 tests/specialty-code-canonicalization-contract.py
node tests/education-core-contract.js
node tests/personnel-csv-import-regression.js
```

---

## 5. Αναθέσεις, ωρολόγια και generated snapshots

Canonical datasets:

```text
includes/teaching-assignments-data.php
includes/weekly-timetable-data.php
```

Τα school-scoped αρχεία είναι **generated performance snapshots**, όχι source of truth. Active/prepared τύποι σχολείων ορίζονται στο `includes/scoped-workload-config.php`.

Μετά από αλλαγή canonical assignments/timetable:

```bash
php tools/scoped-workload-sync.php --write
php tools/scoped-workload-sync.php --check
```

Τα auto-generated αρχεία στο `includes/scoped-workload/` δεν επεξεργάζονται χειροκίνητα. Αν υπάρχει mismatch, διορθώνουμε source ή generator.

---

## 6. School profile → workload

Το canonical curriculum δεν είναι πραγματικό σχολείο. Το `includes/school-profile.php` εφαρμόζει αριθμό τμημάτων, επιλογές, κατευθύνσεις, groups και conditional sections.

Το `includes/school-profile-workload.php` το μετατρέπει σε staffing/workload units.

Κρίσιμα invariants:

- eligible hours ≠ τελική ανάθεση σε εκπαιδευτικό,
- shared top-priority ώρες μπορεί να είναι διαθέσιμες σε περισσότερους κλάδους,
- unresolved regulatory gaps δεν μετατρέπονται σε fixed workload.

Αν ένα κενό/πλεόνασμα φαίνεται λάθος, ελέγχουμε τη διαδρομή:

```text
timetable → crosswalk → assignment resolution → school profile → workload unit → allocation slot
```

---

## 7. Personnel workload / optimizer

### Server reference

`includes/personnel-workload.php` κρατά person normalization, allocation slots, validation, optimizer policy και fallback/reporting semantics. Ο PHP optimizer βρίσκεται στο `includes/teaching-allocation-engine.php`.

### Browser implementation

`includes/personnel-workload-calculations.js` είναι το shared pure module. Το `includes/staffing-simulator-ui.js` είναι controller/view layer και δεν πρέπει να αποκτήσει δικό του optimizer, eligibility table ή specialty normalizer.

### Eligibility

Ο browser παίρνει το canonical:

```text
allocationSlots[].eligible_by_priority
```

που έχει ήδη παραχθεί από το server workload matrix. Άρα αλλαγή tier, π.χ. `ΤΕ16`, γίνεται στα canonical data — όχι σε JavaScript mapping.

### Shared policy

Schema:

```text
staffing_optimizer_policy_v1
```

Περιλαμβάνει priority ranks, objective order, B΄ limit, safety budgets και `specialty_code_normalization_schema`. Αν browser/server normalization schemas διαφέρουν, ο client optimizer απενεργοποιείται και χρησιμοποιείται PHP fallback.

### Exact vs equivalent optimum

Διαφορετικά assignment rows δεν είναι απαραίτητα bug. Επιτρέπονται όταν client/server έχουν:

- ίδιο λεξικογραφικό objective,
- ίδια invariants,
- `certified_optimum` και οι δύο.

Τα tests διακρίνουν:

```text
exact
objective-equivalent certified optimum
objective divergence / invalid result  ← FAIL
```

Δεν προσθέτουμε τεχνητό tie-break μόνο για να γίνουν ίδια τα row IDs, εκτός αν γίνει ρητό product requirement.

### Client-first, όχι client-only

Μετά τον πρώτο server-side υπολογισμό της σχολικής μονάδας, το browser έχει ήδη το canonical workload matrix contract και τα allocation slots. Ο έλεγχος της Καρτέλας 3 (`normalizeRoster()`), το unlock της Καρτέλας 4, το allocation validation και το automatic proposal γίνονται client-side όταν workload module + specialty-normalization schema είναι συμβατά.

Η τρέχουσα ροή είναι:

```text
Καρτέλα 1 → explicit server POST → profile / matrix / allocation slots
Καρτέλα 3 → client normalizeRoster() → Καρτέλα 4 unlock (χωρίς POST)
Καρτέλα 4/5/6 → client validation / optimizer / derived views
```

Το πρώτο profile/matrix build παραμένει server-side canonical boundary. Δεν αντιγράφουμε ωρολόγια/αναθέσεις/profile builders σε JavaScript.

Ο PHP personnel POST και ο PHP engine παραμένουν reference, no-JS fallback και safety fallback σε module exception/schema mismatch. Πριν από πιθανό allocation fallback, ο browser συγχρονίζει το live personnel payload στο allocation form ώστε ο server να λάβει ακριβώς το ίδιο roster.

---

## 8. Performance

Το UX κέρδος του client path δεν είναι ότι η JS είναι αναγκαστικά ταχύτερη από την PHP στο pure compute. Το κέρδος είναι ότι αποφεύγεται:

```text
HTTP → PHP bootstrap → recomputation → full HTML render → response → repaint
```

Performance guard:

```bash
python3 tests/personnel-stage-client-transition-contract.py
python3 tests/personnel-workload-performance-benchmark.py --contract
```

Πλήρες report:

```bash
python3 tests/personnel-workload-performance-benchmark.py
```

Δεν αυξάνουμε performance budget απλώς για να περάσει regression· πρώτα διορθώνουμε την αιτία.

---

## 9. PWA / browser shell

Όλα τα public PHP pages χρησιμοποιούν `includes/head-pwa.php`. Εκεί ανήκουν manifest, favicon, Apple touch icon, theme color και `assets/pwa.js`. Το `index.html` είναι η στατική εξαίρεση.

Το `service-worker.js` είναι σκόπιμα συντηρητικό:

- same-origin static assets μόνο,
- network-first,
- όχι PHP/HTML/navigation/dynamic responses.

Για runtime release με αλλαγμένα assets:

1. bump `EDU_TOOLS_VERSION` στο `includes/config.php`,
2. bump service-worker cache version,
3. τρέξε το PWA contract.

Το `edu_asset_url()` προσθέτει release version + local mtime για cache busting.

---

## 10. Tests και release gate

Τέσσερις βασικές κατηγορίες:

- **contract tests:** schema / architecture / canonical-source rules,
- **parity/differential:** PHP ↔ JS στα ίδια inputs,
- **mutation/oracle:** αποδεικνύουν ότι το suite πιάνει πραγματικό σφάλμα,
- **performance budgets:** προστατεύουν complexity και DOM hot paths.

Κεντρικό gate:

```bash
bash tests/pre-pwa-regression.sh
```

Timeout του εξωτερικού runner δεν θεωρείται PASS. Αν χρειάζεται, τα blocks τρέχουν χωριστά· δεν χαλαρώνουμε correctness/performance budgets μόνο για να χωρέσει όλο το suite σε μικρότερο wall-clock.

---

## 11. Πού αλλάζω τι;

| Αλλαγή | Σωστό σημείο | Όχι εδώ |
|---|---|---|
| ΦΕΚ / URL | `includes/legal-sources.php` + canonical data | UI hard-code |
| Ανάθεση κλάδου | canonical assignment dataset | optimizer JS mapping |
| Ώρες μαθήματος | canonical timetable dataset | workload result patch |
| Κωδικός ειδικότητας | registry + normalization contract | CSV/UI regex |
| Optimizer rule | server policy/reference + shared JS module + tests | `includes/staffing-simulator-ui.js` |
| Favicon/manifest | `includes/head-pwa.php` / manifest / PWA files | κάθε PHP page |
| Κοινό layout | shared CSS/component | page-specific workaround χωρίς λόγο |

Για αλλαγή assignments/timetable, θυμήσου και το scoped sync.

---

## 12. Release checklist

```bash
# Αν άλλαξαν assignments/timetables
php tools/scoped-workload-sync.php --write
php tools/scoped-workload-sync.php --check

# Main correctness/architecture gate
bash tests/pre-pwa-regression.sh

# Προαιρετικό αναλυτικό performance report
python3 tests/personnel-workload-performance-benchmark.py
```

Σε runtime asset release γίνεται και coordinated version/cache bump.

Για αλλαγές υψηλού ρίσκου (optimizer, normalization, legal/regulatory data), προσθέτουμε fixture/contract που **θα αποτύγχανε πριν από τη διόρθωση**.

---

## 13. Κανόνας για μελλοντικά refactors

Πριν δημιουργηθεί νέο helper, mapping ή dataset, ρωτάμε:

> **Υπάρχει ήδη canonical representation αυτού του πράγματος;**

Αν ναι, ο νέος κώδικας το **καταναλώνει** — δεν το ξαναορίζει.

Η ωριμότητα της Εργαλειοθήκης βασίζεται πλέον κυρίως στη συνέπεια μεταξύ layers. Μια αλλαγή σε data ή policy πρέπει να διαδίδεται μέσω της αρχιτεκτονικής και να αποδεικνύεται από contracts, όχι να απαιτεί patches σε πολλαπλά σημεία.
