# Calculator layout — Phase 1 audit

Ημερομηνία: 2026-08-24  
Βάση: `educational-tools-duration-limits.zip`

## Στόχος

Δημιουργία κοινού presentation-only layout layer χωρίς αλλαγή business logic ή rendered DOM.

Νέο shared component:

`includes/components/calculator-layout.php`

Το component γνωρίζει μόνο δομικά primitives:

- `calculatorHero()`
- `calculatorColumnsStart()` / `calculatorColumnsEnd()`
- `calculatorMainStart()` / `calculatorMainEnd()`
- `calculatorCardStart()` / `calculatorCardEnd()`
- `calculatorResultsStart()` / `calculatorResultsEnd()`
- `calculatorActions()`

Δεν περιέχει κανένα scoring rule, specialty rule, cap rule, validation rule ή calculator-specific JavaScript.

## Pilot migration

Μεταφέρθηκαν τέσσερις αντιπροσωπευτικές σελίδες:

1. `ypologismos-morion.php` — Standard ASEP
2. `ypologismos-morion-apospasis-sde.php` — SDE / page-shell
3. `ypologismos-morion-apospasis-evropaika-scholeia.php` — modern non-SDE / page-shell
4. `ypologismos-morion-3ea-2025.php` — special grid / result-card

Στις τέσσερις σελίδες το κοινό component παράγει πλέον:

- hero,
- columns/grid wrapper,
- main-column wrapper,
- όλα τα page-local `<section class="card">` wrappers,
- results `<aside>` wrapper.

Το outer family shell (`div.app`, `main.page-shell`, `div.page`) παραμένει επίτηδες page-specific στο Phase 1.

Τα source cards και τα action button groups δεν μεταφέρθηκαν ακόμη σε κοινό component, παρότι υπάρχει ήδη helper `calculatorActions()` για επόμενη φάση.

## Rendered DOM parity

Για κάθε pilot έγινε PHP render πριν και μετά και canonical DOM comparison με κανονικοποίηση μόνο whitespace.

| Σελίδα | DOM parity | ID parity | `data-*` parity | Script parity | Duplicate IDs |
|---|---:|---:|---:|---:|---:|
| `ypologismos-morion.php` | PASS | PASS (44 IDs) | PASS | PASS | 0 |
| `ypologismos-morion-apospasis-sde.php` | PASS | PASS (34 IDs) | PASS | PASS | 0 |
| `ypologismos-morion-apospasis-evropaika-scholeia.php` | PASS | PASS (61 IDs) | PASS | PASS | 0 |
| `ypologismos-morion-3ea-2025.php` | PASS | PASS (61 IDs) | PASS | PASS | 0 |

Το canonical rendered DOM είναι ισοδύναμο και στις **4/4** σελίδες.

## Runtime parity

Οι rendered before/after σελίδες εκτελέστηκαν σε headless Chromium με τα υπάρχοντα local JS assets inlined, ώστε να ελεγχθεί πραγματική browser execution χωρίς αλλαγή των scripts.

Αποτελέσματα:

- 1ΓΕ/2ΓΕ: ΠΕ03 + βαθμός 8 → `20,00`, ίδιο πριν/μετά.
- Απόσπαση ΣΔΕ: PE03 + 3 έτη → ίδιο runtime output πριν/μετά.
- Ευρωπαϊκά Σχολεία: τιμή 7 σε πεδίο `0–5` → live clamp σε `5`, ίδιο πριν/μετά.
- 3ΕΑ: ΠΕ03 + βαθμός 8 → `20.00`, ίδιο πριν/μετά.
- Browser page errors: **0**.
- Console errors: **0**.

## Business-logic isolation

Όλα τα JavaScript assets είναι byte-identical με το προηγούμενο master. Δεν άλλαξε κανένα scoring engine/controller.

Αλλαγμένα υπάρχοντα production files:

- `ypologismos-morion.php`
- `ypologismos-morion-apospasis-sde.php`
- `ypologismos-morion-apospasis-evropaika-scholeia.php`
- `ypologismos-morion-3ea-2025.php`

Νέο production file:

- `includes/components/calculator-layout.php`

## Repository checks

- PHP lint: **37/37 PASS** (36 υπάρχοντα + νέο layout component)
- JavaScript syntax: **31/31 PASS**
- Local require/script/style dependencies: **223 checks / 0 missing**
- Obsolete `includes/sde-calculations-v3.16.js`: **δεν υπάρχει**

## Τι αποδείχθηκε στο Phase 1

Το ίδιο layout API μπορεί να παράγει χωρίς DOM regression και τις τέσσερις βασικές οικογένειες που επιλέχθηκαν για pilot:

- standard ASEP,
- SDE page-shell,
- modern page-shell,
- special 3EA grid/result-card.

Άρα το component είναι κατάλληλο για Phase 2 migration των υπόλοιπων non-legacy calculators.

## Phase 2 recommendation

Μετά από live-server visual spot check των τεσσάρων pilot pages, να μεταφερθούν οι υπόλοιπες standard/SDE/modern σελίδες. Τα Ωνάσεια και οι γενικές αποσπάσεις (`app-box`) παραμένουν τελευταία, όπως προβλέπει το αρχικό layout audit.
