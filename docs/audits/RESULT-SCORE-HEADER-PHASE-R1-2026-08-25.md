# Result Score Header — Phase R1 Audit — 25/08/2026

## Scope

Safe first phase of the Result Box UI audit. Presentation only: no scoring, eligibility, cap, priority, evidence, or calculation-timing rules were changed.

## Change

Added the shared presentation helper `calculatorScoreHeader()` to `includes/components/calculator-layout.php`.

The helper exposes semantic slots for:

- score value (`value_id`, `value_html`),
- label,
- optional context,
- optional cap,
- variant,
- accessible group label.

The standard variant renders the canonical classes:

- `result-score-header`,
- `result-score`,
- `result-score-label`,
- optional `result-score-context`,
- optional `result-score-cap`.

The legacy `calculatorTotalBlock()` helper remains in the shared component as a compatibility primitive, but no production calculator uses it after R1.

## Migrated standard aggregate calculators

1. `ypologismos-morion.php`
2. `ypologismos-morion-1ea-2025.php`
3. `ypologismos-morion-1gt-2024.php`
4. `ypologismos-morion-2ea-2025.php`
5. `ypologismos-morion-3ea-2025.php`
6. `ypologismos-morion-4ea-2025.php`
7. `ypologismos-morion-5ea-2022.php`
8. `ypologismos-morion-sivitanidios-saek.php`

## Intentional visual normalization

The redundant standalone visible heading `Αποτέλεσμα` was removed from:

- 1EA/2025,
- 2EA/2025,
- 3EA/2025.

Accessibility is preserved through `role="group" aria-label="Αποτέλεσμα"` on the shared score header.

Seven of the eight pages already used the canonical standard score styling. Their previous declarations are exactly equal to the new shared declarations:

- wrapper: `text-align:center; padding:10px 0 18px`,
- score: `54px`, line-height `1`, weight `850`, page accent, `-.04em`, tabular numerals,
- label: muted color, `5px` top spacing.

3EA/2025 was the historical exception. Its principal score changes intentionally from the old local `44px / weight 800` style to the canonical `54px / weight 850` standard score hierarchy. Its separate `total-label` presentation is also replaced by the common label slot.

## Preserved hooks and IDs

Rendered before/after ID-set comparison on all eight migrated pages: **8/8 PASS**.

Exact page ID counts preserved:

- main ASEP calculator: 50 → 50,
- 1EA/2025: 48 → 48,
- 1GT/2024: 56 → 56,
- 2EA/2025: 70 → 70,
- 3EA/2025: 65 → 65,
- 4EA/2025: 70 → 70,
- 5EA/2022: 66 → 66,
- Sivitanidios SAEK: 51 → 51.

Total: **476 IDs preserved exactly**. Score IDs remain unchanged (`grandTotal`, `totalPoints`, `finalTotal` as applicable).

## CSS cleanup

Removed obsolete standard-family score selectors after migration:

- `body.edu-ui.edu-calc-standard .total`,
- `body.edu-ui.edu-calc-standard .total .num`,
- `body.edu-ui.edu-calc-standard .total .label`,
- old 3EA `.total` and `.total-label` score rules,
- the now-unused `.edu-text-center` utility that existed only for the removed 3EA result heading.

The shared score header now owns the presentation contract.

## Regression results

- Result score-header contract: **91/91 PASS**
- Full executable test suites: **22/22 PASS**
- PHP lint: **43/43 PASS**
- Production JavaScript syntax: **30/30 PASS**
- PHP page runtime renders: **26/26 PASS**
- Decimal-format contract: **6/6 PASS**
- ID-normalization contract: **239/239 PASS**
- Standard-family rendered ID parity: **8/8 PASS**
- Existing standard score CSS declaration parity: **3/3 declaration groups identical**

## Version / deployment

`common.css` changed, so the central asset version is bumped:

- `3.20.51` → **`3.20.52`**

No JavaScript production file changed in this phase.

## Out of scope for R1

Deliberately unchanged:

- summary/result rows,
- status / eligibility / priority boxes,
- action hierarchy,
- disclaimers,
- progress bars,
- staged / conditional result headers,
- calculation logic.

These remain for later Result Box phases.
