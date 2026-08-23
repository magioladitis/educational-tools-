# EAE Main / Auxiliary Table Unification Audit

Date: 2026-08-23
Scope: 3ΕΑ/2025 (ΠΕ) + 4ΕΑ/2025 (ΤΕ)

## Result

The eligibility logic for the Αξιολογικός Πίνακας Β΄ (Κύριος) and Επικουρικός Πίνακας is now centralized in two shared files:

- `includes/eae-table-eligibility.js` — pure calculation/rules engine
- `includes/asep-eae-eligibility.js` — DOM/controller layer with `pe` and `te` profiles

The two calculators keep their different form layouts; only the business logic/controller is unified.

## Official rules verified

### Common Main Table criteria — 3ΕΑ/2025 and 4ΕΑ/2025

The common core is:
1. PhD in EAE / School Psychology
2. MSc in EAE / School Psychology
3. Two-year EAE retraining degree (Διδασκαλείο)
4. AEI degree + at least five years documented EAE service

### Common Auxiliary Table criteria — 3ΕΑ/2025 and 4ΕΑ/2025

Any one of:
1. EAE specialization seminar at least 400 hours and at least 7 months
2. At least one teaching year / 10 months EAE service
3. Teacher parent of a child with disability >=67%

### PE-only additions retained in 3ΕΑ/2025

- ΠΕ61 and ΠΕ71 are EAE branches and are treated as Main Table automatically.
- ΠΕ11 retains its additional qualifying EAE / adapted physical education specialty criterion.

Official sources:
- 3ΕΑ/2025, ΦΕΚ 22/23.05.2025: https://info.asep.gr/sites/default/files/2025-05/22%2023-05-2025.pdf
- 4ΕΑ/2025, ΦΕΚ 42/18.08.2025: https://info.asep.gr/sites/default/files/2025-08/%CE%A6%CE%95%CE%9A%2042%2018-08-2025.pdf

## Shared rules locked in `EaeTableEligibility.RULES`

- Auxiliary seminar: 400 hours
- Auxiliary seminar duration: 7 months
- Auxiliary EAE service: 10 months
- Child disability threshold: 67%
- Main five-year EAE criterion: 5 years (semantic rule; form remains explicit checkbox)
- PE automatic Main specialties: ΠΕ61, ΠΕ71
- PE11 special branch: ΠΕ11

## Profiles

### `pe`
Used by `ypologismos-morion-3ea-2025.php`.

Reads common main criteria plus:
- automatic Main for ΠΕ61/ΠΕ71
- special PE11 criterion

### `te`
Used by `ypologismos-morion-4ea-2025.php`.

Reads the four common Main criteria from the existing single select and the three common Auxiliary criteria.

## Important precedence rule

If both a Main and an Auxiliary criterion are present, Main Table wins. This matches the previous page logic and is locked by parity tests.

## What was deliberately NOT merged

- The actual HTML layout: 3ΕΑ uses multiple PE-specific checkboxes while 4ΕΑ uses a single Main-criterion selector.
- 3ΕΑ academic coupling: `phdEae`, `masterEae`, `seminar400`, and PE11 scoring remain in the 3ΕΑ academic calculation.
- 4ΕΑ academic coupling: `auxSeminar400` continues to count as the TE training criterion through `AsepTeAcademic`, never double-counted with generic training.
- Braille / ΕΝΓ priority remains in the existing `eae-sensory-priority` component; it is priority, not Main/Aux eligibility.
- Pedagogical competence remains a ranking priority, not an eligibility criterion.

## Regression results

Pure-engine parity against the previous inline algorithms:

- PE / 3ΕΑ combinations: 2,560
- TE / 4ΕΑ combinations: 180
- Rule-lock checks: 7
- Total: 2,747 PASS / 0 FAIL

The test matrix includes:
- missing branch
- ΠΕ02 generic branch
- ΠΕ11 special criterion
- ΠΕ61 / ΠΕ71 automatic Main
- all combinations of PhD / MSc / retraining / five-year / PE11 criterion
- seminar 400h
- 0 / 9 / 10 / 60 EAE months
- child disability >=67%
- simultaneous Main + Auxiliary criteria

## Structural verification

- 3ΕΑ no longer contains local `function eligibility(...)`.
- 4ΕΑ no longer contains local `mainEligible`, `auxEligible` or `auxReasons` logic.
- Both pages load the same engine/controller at `v=3.20.28`.
- Both rendered pages contain exactly one `#eaeEligibility` root.
- Rendered duplicate HTML ids: 0.
- PHP syntax: 36/36 PASS.
- JavaScript syntax: 28/28 PASS.
- Local dependency references: 250 checked / 0 missing.
- Hash audit: only the two expected existing production pages changed; the two shared JS files are new.

## Code reduction in page-local logic

- 3ΕΑ: 273 -> 259 lines (-14)
- 4ΕΑ: 411 -> 394 lines (-17)
- Total page-local reduction: 31 lines

The main benefit is not the line count but that the legal thresholds and table-selection precedence now have one shared source of truth.
