# European Schools interview limits audit — 2026-08-23

## Scope
`ypologismos-morion-apospasis-evropaika-scholeia.php`

## Required bounds
- `oralPrerequisiteLanguage`: 0–10
- `oralWorkingLanguage1`: 0–5
- `oralWorkingLanguage2`: 0–5
- `thoughtSpeech`: 0–5
- `interculturalInnovation`: 0–5
- `curriculumKnowledge`: 0–10

## Protection layers
1. HTML `min` / `max` / `step=0.1` attributes.
2. Page runtime `normalize()` clamps the entered value immediately to the permitted range and rounds to one decimal place.
3. `EuropeanSchools.interviewPoints()` independently clamps all six values before scoring.

## Tests
- PHP syntax: PASS
- JavaScript syntax (`european-schools-calculations.js`): PASS
- UI bounds/normalize contract: 7/7 PASS
- Calculation engine bounds: 10/10 PASS
- Maximum interview score remains 40/40.

A Chromium E2E run was attempted, but the environment blocks local/file navigation with `ERR_BLOCKED_BY_ADMINISTRATOR`; it is therefore not claimed as a browser PASS.
