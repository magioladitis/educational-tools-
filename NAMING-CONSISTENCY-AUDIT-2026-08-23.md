# Naming consistency & final unification audit — 23/08/2026

## Scope

Audit performed on the latest master after:
- unified language engine,
- PE Academic profiles (`general`, `eep`, `eae`),
- TE Academic,
- Service/Social controllers,
- EAE eligibility,
- SDE secondment language integration and runtime hotfix.

The goal of this pass was specifically to find:
1. the same concept under different names,
2. the same name used for materially different concepts,
3. legacy identifiers that survived after logic was centralized,
4. API naming drift between shared calculation modules.

## Current architecture health

- 24 canonical PHP pages/hubs.
- 9 shared PHP components.
- 24 shared JS modules under `includes/`.
- PHP syntax: **36/36 PASS**.
- JS syntax: **25/25 PASS** (24 includes modules + `assets/common.js`).
- Local dependency audit: **211 references / 0 missing**.
- Local `<style>` blocks in canonical PHP: **0**.
- Inline `style=` attributes in canonical PHP: **0**.
- Existing regression suites: **53/53 numeric PASS**.
- Existing architecture suites: **133/133 PASS**.
- New naming contract: **66/66 PASS**.
- Chromium runtime smoke tests for the six ASEP calculators touched by the rename: **27/27 PASS**, **0 runtime JS exceptions**.

## Canonical identifiers adopted in this pass

| Concept | Canonical name |
|---|---|
| Degree/title grade input | `degreeGrade` |
| Number of scored MSc/integrated-master titles | `mscCount` |
| Pedagogical/didactic competence checkbox | `pedagogical` |
| Greek Sign Language priority | `signLanguage` |
| 400h / 7-month EAE seminar | `seminar400` |
| Regular/public educational service months | `regularMonths` |
| Difficult-area / detention-facility months | `difficultMonths` |
| 2020–21 regular three-month contracts | `threeMonthRegular2020` |
| 2020–21 difficult three-month contracts | `threeMonthDifficult2020` |
| 2021–22 regular three-month contracts | `threeMonthRegular2021` |
| 2021–22 difficult three-month contracts | `threeMonthDifficult2021` |
| Four-plus years of marriage | `marriageYears4Plus` |
| Candidate mental-condition exclusion | `candidateMentalCondition` |

### Aliases removed from the six main ASEP calculators

- `degree` → `degreeGrade` (3EA)
- `masters` → `mscCount` (2EA, 3EA)
- `pde` → `pedagogical` (3EA)
- `sign` → `signLanguage` (3EA)
- `auxSeminar400` → `seminar400` (4EA)
- `normalMonths`, `publicMonths` → `regularMonths`
- `hardMonths` → `difficultMonths`
- four separate historical naming families (`covid2020`, `covid20Regular`, `threeMonthMonths2020`, etc.) → one canonical four-field family
- `marriage4` → `marriageYears4Plus` (1EA, 2EA)
- `mental` → `candidateMentalCondition` (1EA, 2EA)

The naming contract test explicitly verifies that these obsolete aliases are absent from the six ASEP calculators.

## Important distinction: same concept vs. genuinely different concept

Not every similar name should be forcibly made identical.

### `branch` vs `specialty` — keep for now

`specialty` is the canonical property passed to common calculation engines. Some TE/EEP pages still use a DOM field named `branch`, because their page-specific UI and legal terminology are organized around a branch/κλάδο selector. The shared controllers already isolate this through `data-specialty-id`.

**Recommendation:** do not rename this merely for cosmetic consistency. If normalized later, normalize through a shared specialty-code layer rather than mass DOM replacement.

### `secondTitle` vs `secondDegree` — intentionally different

- `secondDegree` means a second university degree in PE profiles.
- `secondTitle` in EBP/TE tools can legally be a different type of second qualifying title.

These should remain separate.

### `master` vs `mscCount` — intentionally different outside PE academic

`mscCount` now means **a count** of scored postgraduate/integrated-master titles in the PE academic engine.

Several non-ASEP/SDE tools use `master` to mean a single qualification, relevance category, or yes/no state. Those are not the same data shape and should not be renamed to `mscCount`.

### `degreeGrade` and scale

`degreeGrade` now consistently means **the raw entered grade of the basic qualifying title**, not necessarily a 10-point value:
- PE academic: 5–10,
- 1EA EBP: 10–20,
- TE academic: scale is provided separately by `gradeScale`.

This is acceptable, but future shared components should always carry the scale explicitly (`gradeScale` / metadata) and never infer it from the variable name.

## Remaining naming/API inconsistencies worth fixing

### 1. Shared result contract: `raw` / `rawPoints` / `total` / `points` — HIGH VALUE

Current shared modules are not yet fully uniform:

- `EducationAcademic` → `rawPoints`, `points`
- `EducationService` → both `raw` and `rawPoints`, plus `points`
- `TEAcademic` → `raw`, `points`
- `EducationLanguages` → `raw`, `points`
- `EducationSocial` → `total` (no `points` alias)

This is the most important remaining naming inconsistency because it affects **module APIs**, not just DOM ids.

**Safe migration plan:**
1. Add non-breaking aliases (`rawPoints`, `points`) to every scoring result.
2. Migrate pages/controllers to the canonical fields.
3. Keep old aliases for one release.
4. Remove old aliases only after regression/runtime parity.

Recommended canonical scoring result shape:

```js
{
  rawPoints,
  points,
  details,
  warnings
}
```

with domain-specific fields added as needed (`months`, `degreePoints`, `childrenPoints`, etc.).

### 2. Specialty codes use mixed Greek and Latin prefixes — HIGH VALUE

The repository contains both:
- `ΠΕ06`, `ΤΕ16`
- `PE06`, `TE16`

This already caused a real runtime/business-rule risk during the SDE language migration. The language engine currently handles both spellings explicitly, but other modules may not.

**Recommendation:** create one shared `normalizeSpecialtyCode()` and make all shared engines consume the normalized canonical code. This is more valuable than renaming every DOM field.

### 3. `sanitizeServiceMonthInput()` remains page-local — MEDIUM VALUE

It is still repeated in 1EA, 2EA and 3EA, while 1GT/4EA also maintain local lists of service ids for normalization/reset behavior.

Now that all six ASEP pages use identical `data-service-role` fields and canonical ids, this should move into `AsepServiceController`.

Benefit: page code no longer needs to know any service input ids at all.

### 4. Sensory priority state/summary remains page-local — MEDIUM VALUE

`eae-sensory-priority.php` and `eae-sensory-proof.js` own the proof UI, but 1EA/2EA/3EA/4EA still construct their own priority/summary text.

Now the identifiers are normalized (`signLanguage`, `braille`), the controller can safely gain:

```js
EaeSensoryProof.getState(...)
EaeSensoryProof.summary(...)
EaeSensoryProof.reset(...)
```

This would remove the last direct page-level knowledge of ENG/Braille ids.

### 5. Pedagogical competence presentation/controller — MEDIUM, legal-profile dependent

3EA now uses the same `pedagogical` identifier as 1GT/2EA/4EA. The naming problem is solved, but the legal priority logic is not necessarily identical across calls.

If unified, it should be a profile-based controller, not one universal boolean rule.

### 6. Remaining SDE language engines — RESEARCH BEFORE REFACTOR

`SDE secondment` is now on `EducationLanguages`, but:
- `sde-leadership-calculations.js`
- `sde-registry-calculations.js`

still contain their own `calculateLanguages()` scoring logic.

These are plausible future profiles for the central language engine, but the exact scoring/exclusion rules must be verified against their own legal bases first.

## Deployment / repository cleanup still pending

### Asset cache-version drift

The same physical assets are currently referenced with multiple query versions:

- `common.css`: `3.20.13`, `3.20.15-rc1`, `3.20.17`
- `language-calculations.js`: mostly `3.20.24`, SDE `3.20.25`
- `training-proof.js`: `3.20.18` and `3.20.25`
- `asep-computer-proof.js`: `3.20.15-rc2`, `3.20.25`, `3.20.27`

`common.js` is consistently `3.20.13`.

**Recommendation:** define a central deploy/cache version in `includes/config.php` and render asset URLs through one helper. Do not keep page-specific cache versions for a shared physical file.

### Obsolete versioned SDE module

`includes/sde-calculations-v3.16.js` is not referenced by any production PHP page. It is also now stale relative to the canonical `sde-calculations.js`, which has been migrated to the central language engine.

**Recommendation:** delete it from the production/master tree after one final reference check.

### Generated/debug artifacts

`__sde_rendered.html` and audit/test reports belong in a tests/docs/release area rather than the production root. They are harmless but make the master package look less canonical.

## Recommended next sequence

1. **Standardize shared result APIs** (`rawPoints` / `points`) with backwards-compatible aliases.
2. **Create shared specialty-code normalization** (`ΠΕ`/`PE`, `ΤΕ`/`TE`).
3. Move **service input sanitization** into `AsepServiceController`.
4. Finish **ENG/Braille controller state + summary**.
5. Centralize **asset cache versioning**.
6. Remove obsolete/unreferenced versioned files and move debug reports out of production root.
7. Research whether **SDE Leadership / SDE Registry languages** can become additional `EducationLanguages` profiles.

At that point, further cleanup is mostly presentation-level rather than business-logic unification.
