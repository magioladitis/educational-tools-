# CSS Consolidation Audit — 24/08/2026

## Baseline

Audit/refactor performed on the clean canonical master **3.20.49**, after the ID-normalization, repository-cleanup and UI-pattern/component passes.

Goal: reduce duplicated presentation rules in `assets/common.css` without changing markup, scoring logic, IDs, JavaScript hooks or visible layout.

New cache version after the CSS change: **3.20.50**.

## Scope

The highest-value exact duplication was concentrated in four modern calculator families:

- European Schools (`edu-page-european-schools`)
- Secondment Abroad (`edu-page-abroad`)
- Digital Tutoring (`edu-page-digital-tutoring`)
- SDE Registry / Leadership (`edu-calc-sde`)

The audit compared normalized declaration sets, not merely similar selector names.

## Findings before cleanup

`common.css` contained **31 exact cross-family duplicate declaration sets** in the families above. They represented **65 redundant qualified rules**.

Repeated patterns included:

- `field-grid`
- labels and label help text
- form controls
- card headings/subtitles
- warning/danger/info states
- sticky result panels
- action button grids
- primary/secondary buttons
- result rows
- progress bars
- source notes
- criteria lists
- shared body/page-shell declarations for Abroad + Digital Tutoring

These were presentation-only duplicates; no business-rule selectors were involved.

## Refactor performed

Rules with exactly the same normalized declarations were consolidated into grouped selectors. Page-specific differences were deliberately left untouched, including:

- hero gradients and page accent colors
- sidebar widths and responsive breakpoints
- total-number sizes/colors
- SDE-specific green theme
- European Schools stage/result presentation
- Abroad stipend panels
- Digital Tutoring interview/status panels
- SDE role-specific blocks

No production PHP markup was changed.

### Metrics

| Metric | Before | After | Change |
|---|---:|---:|---:|
| `common.css` bytes | 131,507 | 127,310 | **−4,197 (−3.19%)** |
| qualified CSS rules | 876 | 811 | **−65 (−7.42%)** |
| exact cross-family duplicate sets in target families | 31 | 0 | **−31** |

The source line count remains 2,792 because grouped selectors are intentionally kept readable instead of minified.

## Visual parity verification

A computed-style parity test compared the old and new CSS on all DOM elements of the five affected pages:

1. European Schools
2. Secondment Abroad
3. Digital Tutoring
4. SDE Registry
5. SDE Leadership

The test ran at both desktop (1280 px) and mobile (600 px) widths and compared key computed presentation properties including display/position, dimensions, margins, padding, borders, backgrounds, typography, grid/flex layout, sticky positioning and overflow.

Result: **10/10 page+viewport combinations PASS**, with no computed-style differences.

## `checkrow` vs `check-row`

The audit also revisited the two checkbox-row naming families.

Current source usage:

- `checkrow`: **27** markup occurrences
- `check-row`: **41** markup occurrences

They are **not equivalent visual components** today:

- standard ASEP `checkrow`: flex row, 19 px checkbox, vertical padding
- DΗM.Ω.Σ. `checkrow`: two-column grid with row borders
- general secondment `check-row`: flex row with scaled checkbox and different typography
- SDE `check-row`: compact flex row with 18 px checkbox
- Sivitanidios language proof `check-row`: nested flex row with its own spacing

Therefore a blind rename would provide little CSS reduction and would increase regression risk. They remain intentionally separate. A future shared checkbox-row component would need explicit variants/modifiers rather than one universal rule.

## Compatibility tokens

Six custom-property definitions are currently not referenced directly inside this repository:

- `--edu-radius-sm`
- `--edu-radius`
- `--edu-tools-blue`
- `--edu-tools-blue-dark`
- `--edu-tools-muted`
- `--edu-tools-border`

They were **retained intentionally** as design-system / backwards-compatibility tokens. Removing them would be an API cleanup, not a visual CSS consolidation, and is better handled separately if desired.

## Regression results

After the refactor:

- CSS consolidation contract: **7/7 PASS**
- all executable test suites: **19/19 PASS**
- PHP lint: **43/43 PASS**
- production JavaScript syntax: **30/30 PASS**
- root PHP runtime renders: **26/26 PASS**
- computed-style parity: **10/10 PASS**

## Production impact

Only **2 production files** changed:

1. `assets/common.css`
2. `includes/config.php`

`includes/config.php` was bumped from `3.20.49` to **`3.20.50`** because the shared CSS asset changed.

No calculation engine, PHP calculator page, field ID, form value or business rule changed.

## Conclusion

The high-value exact CSS duplication across the modern split-calculator families is now removed. Further consolidation should be selective: the remaining repeated-looking rules mostly belong to genuinely different UI variants or shared component state classes, where aggressive merging would offer little benefit relative to coupling/regression risk.
