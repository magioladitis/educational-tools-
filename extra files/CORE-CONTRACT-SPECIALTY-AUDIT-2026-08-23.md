# Core result contract & specialty normalization audit

Date: 2026-08-23

## Scope

This refactor follows the naming-normalized master and introduces two shared foundations:

1. a canonical scoring-result contract (`rawPoints`, `points`, `details`, `warnings`),
2. one specialty-code normalizer for Greek/Latin PE/TE/DE prefixes.

No scoring coefficients, caps or legal/business rules were intentionally changed.

## New shared core

`includes/education-core.js`

Exports `EducationCore` with:

- `normalizeSpecialtyCode(value)`
- `toLatinSpecialtyCode(value)`
- `createScoreResult(rawPoints, points, extra, aliases)`
- `finiteNumber(value)`

The core is loaded once by `includes/header.php` before calculation modules.

### Canonical specialty examples

| Input | Canonical output |
|---|---|
| `PE06` | `ΠΕ06` |
| `ΠΕ06` | `ΠΕ06` |
| `pe06` | `ΠΕ06` |
| mixed-script `PΕ06` / `ΠE06` | `ΠΕ06` |
| `PE 04.01` | `ΠΕ04.01` |
| `TE16` | `ΤΕ16` |
| `ΤΕ16` | `ΤΕ16` |
| `DE01` | `ΔΕ01` |

This eliminates repeated dual-key rules such as `ΠΕ06` + `PE06` in the language engine.

## Canonical scoring-result API

The main reusable scoring engines now expose at least:

```js
{
  rawPoints,
  points,
  details,
  warnings
}
```

Applied to:

- `EducationAcademic`
- `EducationLanguages.calculate()`
- `EducationLanguages.calculatePair()`
- `EducationService.calculateAsepService()`
- `EducationService.cappedTotal()`
- `EducationSocial`
- `TEAcademic`

### Backwards compatibility

Old aliases are retained where they already existed:

- `raw` remains an alias of `rawPoints` for languages/service/TE and is also exposed by PE academic.
- `total` remains an alias of `points` for `EducationSocial`.

The six main ASEP calculator pages were migrated to consume canonical `points` / `rawPoints` for shared Service/Social results.

## Specialty-aware modules migrated

Canonical normalization is now used by:

- `academic-calculations.js`
- `language-calculations.js`
- `asep-pe-academic.js`
- `asep-language-selector.js`
- `eae-table-eligibility.js`
- `asep-te-academic.js`
- `onaseia-calculations.js`
- `sde-calculations.js`
- `sde-registry-calculations.js`

The SDE public specialty lists may still expose Latin legacy values (`PE06`, `TE16`) for UI/backwards compatibility, but scoring/eligibility comparisons normalize at the calculation boundary.

## Important edge cases locked

- `PE06` and `ΠΕ06` both exclude English in PE/SDE language scoring.
- `PE86` and `ΠΕ86` both trigger the correct computer-knowledge rule.
- `PE61` / `PE71` normalize before the 3EA special EAE academic rule.
- `PE11` normalizes before its 3EA +8 specialization rule.
- `TE16` / `ΤΕ16` normalize before Onaseia manual minimum and TE academic UI family detection.
- Mixed Greek/Latin prefix input is canonicalized.

## Cache-bust

All modified calculation/controller assets referenced by production PHP pages were moved to `v=3.20.31` so browsers do not retain older incompatible controller/engine code.

## Verification

### Regression / contract tests

- PE academic regression: 20 PASS
- TE academic regression: 19 PASS
- Service/Social regression: 14 PASS
- EducationCore + result-contract tests: 43 PASS
- PE controller specialty runtime stub: 5 PASS
- Specialty normalization regression: 9 PASS
- PE academic architecture: 24 PASS
- TE academic architecture: 42 PASS
- Naming contract: 66 PASS
- Service/Social architecture: 67 PASS

**Total: 309 PASS / 0 FAIL**

### Syntax / render / dependencies

- PHP lint: 36/36 PASS
- JavaScript syntax: 25/25 PASS
- Production PHP render: 24/24 PASS
- Rendered local asset references checked: 155
- Missing local dependencies: 0
- Core load-order failures: 0
- Rendered pages with duplicate real HTML ids: 0

### Browser note

A Chromium E2E pass is not claimed in this environment: even a minimal headless Chromium invocation currently times out because the container has no usable DBus/system-bus runtime. Instead, controller runtime behavior is covered with Node DOM stubs plus full PHP render/load-order checks. The previous SDE runtime hotfix remains preserved in this master.

## Deliberately not changed

- `branch` vs `specialty` DOM naming where page-specific logic still makes the distinction useful.
- Domain-specific result shapes in unrelated calculators (European Schools, SDE leadership, abroad, etc.). These can adopt the core contract incrementally without forcing a risky repository-wide rewrite.
- `sde-calculations-v3.16.js` remains obsolete/unreferenced and can be removed in a dedicated cleanup step.
