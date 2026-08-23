# ASEP TE Academic unification audit

Date: 2026-08-23

## Scope

Unified the repeated TE academic UI/controller code used by:

- `ypologismos-morion-1gt-2024.php`
- `ypologismos-morion-4ea-2025.php`

The scoring engine `includes/te-academic-calculations.js` remains the single numerical source of truth.

## New shared files

- `includes/components/asep-te-academic.php`
- `includes/asep-te-academic.js`

The PHP component deliberately supports separate render parts:

1. `grade-scale`
2. `degree-details`
3. `qualifications`

This preserves the 4EA layout where the EAE Main/Auxiliary eligibility section stays between the degree block and the rest of the academic qualifications.

## Rules preserved

- Title grade is normalized to the 20-point scale.
- Degree score: normalized grade × 3, maximum 60 points.
- Second TE title: 10 points.
- Foreign language: one language only, Excellent 20 / Very Good 15 / Good 10.
- Computer knowledge: 20 points.
- Training ≥300 hours and ≥7 months: 10 points.
- Academic category cap: 120 points.

### TE16 behavior preserved

- TE16 is recognized in both Latin-value (`te16`) and Greek-value (`ΤΕ16`) branch selectors.
- Default numeric scale switches to 5–10 for TE16.
- Descriptive TE16 grade becomes available only for TE16.
- `ΚΑΛΩΣ`, `ΛΙΑΝ ΚΑΛΩΣ`, `ΑΡΙΣΤΑ` retain their existing normalized values.
- The second-title description changes to the music-specialization wording for TE16.

### 4EA-specific hook preserved

`auxSeminar400` is passed as an additional training source. Therefore:

- 400-hour EAE seminar alone satisfies the 10-point training criterion.
- General training + 400-hour EAE seminar still scores 10 points once, never 20.
- The training-proof widget is triggered by either `training` or `auxSeminar400`.
- The EAE Main/Auxiliary eligibility logic itself remains page-specific and unchanged.

## Code removed from pages

The two pages no longer contain local copies of:

- `updateBranchUI()`
- `updateGradeUI()`
- `trainingActive()` (4EA)
- direct `TEAcademic.calculate(...)`
- direct `AsepLanguageSelector.calculate(...)`
- direct training-proof summary orchestration
- local degree validation / normalized-grade presentation logic

Page-size reduction:

- 1GT: 327 → 223 lines (−104)
- 4EA: 548 → 411 lines (−137)
- Total page reduction: 241 lines and approximately 14.2 KB of repeated/page-local code.

## Files changed

Compared with the previous Service/Social-unified master, exactly four production files differ:

- CHANGED `ypologismos-morion-1gt-2024.php`
- CHANGED `ypologismos-morion-4ea-2025.php`
- ADDED `includes/asep-te-academic.js`
- ADDED `includes/components/asep-te-academic.php`

No CSS file changed.

## Verification

New TE suite:

- TE Academic numerical regression: 19 PASS / 0 FAIL
- TE Academic architecture/render: 42 PASS / 0 FAIL

Cross-regression after the refactor:

- PE Academic regression: 20 PASS / 0 FAIL
- PE Academic architecture: 24 PASS / 0 FAIL
- Service/Social regression: 14 PASS / 0 FAIL
- Service/Social architecture: 67 PASS / 0 FAIL

Combined regression/architecture checks: **186 PASS / 0 FAIL**.

Repository syntax/dependency checks:

- PHP syntax: 36 files checked / 0 failures
- JavaScript syntax: 26 files checked / 0 failures
- Local PHP/script/style dependency references: 247 checked / 0 missing
- Rendered 1GT/4EA: 0 duplicate HTML IDs

## Deployment

Only four production files need upload. See `FILES-TO-UPLOAD-TE-ACADEMIC.txt`.
