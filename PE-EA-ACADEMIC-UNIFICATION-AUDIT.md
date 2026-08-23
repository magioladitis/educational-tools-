# PE Academic unification — 2ΕΑ/2025 + 3ΕΑ/2025

Date: 2026-08-23
Base: `educational-tools-sde-language-unified-fixed.zip`

## Goal
Remove the last large page-local PE academic scoring duplication from `ypologismos-morion-2ea-2025.php` and `ypologismos-morion-3ea-2025.php`, while preserving every proclamation-specific rule.

## Official-rule verification
Checked against the official ASEP proclamations:
- 2ΕΑ/2025 — ΦΕΚ ΑΣΕΠ 21/23.05.2025: https://info.asep.gr/sites/default/files/2025-05/21%2023-05-2025.pdf
- 3ΕΑ/2025 — ΦΕΚ ΑΣΕΠ 22/23.05.2025: https://info.asep.gr/sites/default/files/2025-05/22%2023-05-2025.pdf

Common PE academic core:
- degree grade × 2.5
- second degree: 7
- PhD: 40
- first MSc / integrated master: 20
- second MSc: +8 (28 total)
- languages: 7 / 5 / 3, through the central language engine
- computer knowledge: 4
- training >=300h and >=7 months: 2
- academic cap: 120

3ΕΑ-specific academic extensions preserved centrally:
- PE61 / PE71: base EAE degree contributes 20 in the postgraduate/specialization category; with at least one additional MSc the category becomes 28.
- PE11 qualifying EAE / Special Physical Education specialization: +8.
- `phdEae` counts as PhD for scoring.
- `masterEae` guarantees at least one MSc for scoring.
- `seminar400` also satisfies the general 300h/7m training criterion (+2 once, not twice).
- PE86 computer knowledge remains non-scoring.

2ΕΑ-specific non-scoring / eligibility logic remains outside the academic engine:
- professional licenses / registrations
- PE23 School Psychology priority
- pedagogical-competence priority
- ENG / Braille priority
- warnings where a master's degree is a formal appointment qualification and must not be counted again

## Architecture after refactor

`includes/academic-calculations.js`
- `general` profile: existing 1ΓΕ/2ΓΕ + Onaseia consumers
- `eep` profile: 2ΕΑ/2025
- `eae` profile: 3ΕΑ/2025

`includes/asep-pe-academic.js`
- reads all shared academic fields
- owns PE86 computer lock
- reads central language state
- supports EAE overlay ids
- owns PE11 / PE61-PE71 academic UI synchronization
- delegates scoring only to `EducationAcademic.calculate()`

The 2ΕΑ and 3ΕΑ pages retain their specialized HTML/presentation, but no longer own the academic scoring formula.

## Regression results

- 2ΕΑ/3ΕΑ old-algorithm vs new-profile parity: **117,316 PASS / 0 FAIL**
- Existing `general` profile old-vs-new parity: **2,880 PASS / 0 FAIL**
- Browser runtime smoke tests: **16/16 PASS**
- Architecture checks: **17/17 PASS**
- PHP syntax: **36/36 PASS**
- JavaScript syntax: **28/28 PASS**
- Local dependency audit: **250 references / 0 missing**
- PHP render: 2ΕΑ + 3ΕΑ PASS
- Duplicate actual HTML ids in rendered 2ΕΑ/3ΕΑ: **0**

## Runtime cases explicitly tested

2ΕΑ:
- degree 8.00 + second degree + PhD + 2 MSc + C2 language + computer + training = 108 academic points
- invalid degree grade contributes 0 while other criteria remain live and warning appears

3ΕΑ:
- PE61 base EAE degree = 20
- PE61 + MSc = 28
- PE61 + `masterEae` overlay = 28
- PE11 special criterion = +8
- `phdEae` = +40
- `seminar400` = general training +2 exactly once
- PE86 computer field disabled and forcibly injected state is cleared
- no JavaScript runtime exceptions

## Production files changed

1. `ypologismos-morion-2ea-2025.php`
2. `ypologismos-morion-3ea-2025.php`
3. `includes/academic-calculations.js`
4. `includes/asep-pe-academic.js`

No CSS changes are required.
