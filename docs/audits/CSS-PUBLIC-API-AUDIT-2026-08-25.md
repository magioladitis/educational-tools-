# CSS Public API & Compatibility Audit — 25/08/2026

## Baseline

Audit/refactor performed on the clean canonical master **3.20.50**, after the repository, ID-normalization, UI-component and CSS-consolidation passes.

Goal: distinguish deliberately stable CSS/design-system API from genuinely dead compatibility/legacy CSS, remove only selectors with no production consumer, and normalize internal runtime usage toward the canonical API without breaking older markup.

New cache version after this pass: **3.20.51**.

## 1. Design-token audit

`assets/common.css` now has a clear three-level contract.

### Canonical `--edu-*` design tokens

Core shared tokens remain public and stable, including:

- surfaces/background/text: `--edu-bg`, `--edu-surface`, `--edu-surface-soft`, `--edu-text`, `--edu-muted`
- borders: `--edu-border`, `--edu-border-strong`
- primary palette: `--edu-primary`, `--edu-primary-dark`, `--edu-primary-soft`
- success/warning/danger palettes and borders
- shadows: `--edu-shadow-sm`, `--edu-shadow`
- radius/control scale: `--edu-radius-sm`, `--edu-radius`, `--edu-radius-lg`, `--edu-control-height`

Two public radius tokens currently have no internal consumer:

- `--edu-radius-sm`
- `--edu-radius`

They are retained intentionally as design-system scale tokens. A public token does not need to be deleted merely because the current page set does not consume every scale step.

### Compatibility tokens retained

The following aliases have no current internal consumer, but are explicitly retained as backwards-compatible API for older pages/extensions:

- `--edu-tools-blue` → `--edu-primary`
- `--edu-tools-blue-dark` → `--edu-primary-dark`
- `--edu-tools-muted` → `--edu-muted`
- `--edu-tools-border` → `--edu-border`

The older mature-calculator variables (`--bg`, `--card`, `--text`, `--muted`, `--border`, `--blue`, `--green`, etc.) remain because they are still actively used and/or overridden by existing calculator families.

### Theme hooks

Exactly two variables are intentionally referenced without a global declaration:

- `--edu-page-accent`
- `--edu-page-accent-dark`

They are optional page-level theme hooks and every shared use has a fallback to the core primary palette.

## 2. Canonical CSS component API

The following `.edu-*` selectors are intentionally kept as the stable API for current/future tools even when the present page set does not yet use all of them:

- `.edu-card`
- `.edu-field-grid`
- `.edu-field`
- `.edu-field--full`
- `.edu-help`
- `.edu-actions`
- `.edu-message`
- `.edu-message--info`
- `.edu-message--warning`
- `.edu-message--success`
- `.edu-message--danger`

After this pass, these **11 documented canonical API selectors are the only CSS selector classes without a current PHP/JS production consumer**.

This is deliberate API surface, not dead CSS.

## 3. Button API normalization

Before this pass, `common.js` dynamically emitted the older classes:

- `edu-btn-primary`
- `edu-btn-secondary`

while the CSS already exposed the canonical BEM-style API:

- `.edu-btn`
- `.edu-btn--primary`
- `.edu-btn--secondary`

Internal runtime enhancement now emits:

- `edu-btn edu-btn--primary`
- `edu-btn edu-btn--secondary`

The old single-dash selectors remain in CSS and are still recognized by `common.js` as compatibility aliases. This gives us canonical internal behavior without breaking older authored markup/extensions.

Browser runtime verification confirmed:

- ordinary primary action → canonical base + primary modifier
- reset/clear action → canonical base + secondary modifier
- legacy `edu-btn-primary` markup remains visually identical and is not rewritten

## 4. Dead legacy selectors removed

Fourteen selector classes were proven to have no consumer in:

- production PHP source,
- shared/component PHP,
- production JavaScript,
- dynamically generated class strings,
- rendered page output.

Removed:

1. `back`
2. `back-tools`
3. `calc-actions`
4. `edu-mt-18`
5. `edu-source-compact`
6. `edu-tools-global-footer__disclaimer`
7. `edu-tools-global-footer__meta`
8. `edu-tools-global-header__brand`
9. `edu-tools-global-header__brand-text`
10. `inline-result`
11. `official-note`
12. `result-box`
13. `sde-language-level`
14. `source-note`

No active compatibility alias was removed.

## 5. Header comment cleanup

`includes/header.php` still carried a historical source-code comment `UI v3.20.1` although asset versioning had long since become centralized.

It is now versionless:

`Common header / navigation for Educational Tools — shared UI.`

This prevents harmless comments from drifting out of sync with the central release version again.

## 6. Test contract cleanup

Two older tests were unnecessarily coupled to a specific cache release:

- `tests/css-consolidation-contract.py`
- `tests/sivitanidios-polish-contract.py`

They were made release-independent:

- the CSS consolidation test now requires the consolidated rule count to remain **at or below** the established 811-rule baseline rather than exactly 811;
- the Sivitanidios polish test verifies use of the central `edu_asset_url()` helper rather than a hard-coded version number.

A new test was added:

- `tests/css-public-api-contract.py`

It locks the intended token/API/compatibility boundary and prevents dead legacy selectors from creeping back in.

## 7. Metrics

| Metric | Before (3.20.50) | After (3.20.51) | Change |
|---|---:|---:|---:|
| `common.css` bytes | 127,310 | 125,589 | **−1,721 (−1.35%)** |
| top-level qualified CSS rules | 811 | 798 | **−13** |
| all qualified rules incl. media blocks | 941 | 927 | **−14** |
| selector classes with no production consumer | 28 | 11 | **−17** |
| genuinely dead selector classes | 14 | 0 | **−14** |
| canonical button classes previously unused | 3 | 0 | now internally consumed |
| custom-property declarations without internal use | 6 | 6 | intentionally retained |

The remaining 11 unused selector classes and 6 unconsumed custom properties are all documented public/compatibility API.

## 8. Regression and parity verification

After the cleanup:

- **20/20 executable test suites PASS**
- **15/15 CSS public API contract checks PASS**
- **43/43 PHP lint PASS**
- **30/30 production JS syntax PASS**
- **26/26 root PHP runtime renders PASS**
- **26/26 static render parity PASS** after normalizing only the cache-version query string
- **12/12 computed-style parity PASS** across 6 representative pages at desktop and mobile widths
- canonical/legacy button runtime compatibility test: **PASS**

Representative computed-style parity pages covered:

- Tools directory
- General secondment calculator
- European Schools
- 3EA/2025
- SDE Registry
- Sivitanidios SAEK

## 9. Production files changed

Only four production files changed:

1. `assets/common.css`
2. `assets/common.js`
3. `includes/config.php`
4. `includes/header.php`

`EDU_TOOLS_VERSION` was bumped from **3.20.50** to **3.20.51** because both shared CSS and shared JS changed.

## Conclusion

The CSS public boundary is now explicit rather than inferred.

- Active legacy aliases remain where they are actually needed.
- Public canonical `edu-*` APIs are retained even when not yet fully adopted.
- Dead historical selectors have been removed.
- Runtime button enhancement now uses the canonical API.
- Release tests no longer fail merely because the cache version changes.

Further aggressive removal of the remaining `edu-*` API or compatibility tokens is **not recommended**. The next useful cleanup should target active legacy markup only when there is a concrete migration benefit, rather than shrinking CSS for its own sake.
