# UI pattern / shared-component audit — 24/08/2026

## Scope

Audit performed on the clean canonical master 3.20.49 after ID normalization and repository cleanup.
The goal was to identify repeated **presentation-only** markup that can safely move into shared components without changing scoring, eligibility, legal rules, field IDs, or JavaScript state hooks.

## Baseline architecture

The repository already has strong shared presentation coverage:

- 17 calculator pages under `ypologismos-morion*.php`.
- `calculator-layout.php` already owns calculator containers, columns, cards, results shells, result rows and actions.
- 24 pages use the shared source/legal-basis card.
- 11 pages use the shared deadline card.
- 21 section headers are already declarative through `calculatorCardStart(... header_variant => section-head ...)`.
- 77 static result rows are already rendered through `calculatorResultRow()`.
- 18 action groups are already rendered through `calculatorActions()`.

## Safe Phase 4B changes completed

### 1. Declarative calculator heroes

Before this pass, 13 calculator pages still used `calculatorHeroStart()` / `calculatorHeroEnd()` and manually wrote the inner `h1`, intro paragraph and metadata chips.

Changes:

- Extended `calculatorHero()` with `intro_attrs` so legacy intro classes can be preserved exactly.
- Migrated 12 manual hero blocks to declarative `calculatorHero()` calls.
- There are now **16 declarative hero calls across 17 calculator pages**.
- Only `ypologismos-morion-apospasis-dimos.php` keeps `calculatorHeroStart()` because its hero intentionally contains two independent paragraphs; forcing it into the one-intro helper would make the API less clean.

No visible content, class, ID or hierarchy changed.

### 2. Shared total block

Added `calculatorTotalBlock()` for the standard static result headline:

```html
<div class="total">
  <div class="num" id="...">...</div>
  <div class="label">...</div>
</div>
```

Migrated 7 identical total blocks.

Intentionally left local:

- DΗM.Ω.Σ. total: includes `outof` plus progress bar.
- 3EA total: uses a different result-card structure (`.total` directly on the numeric node).
- SDE calculators: use the intentionally different `.big-total` pattern.

### 3. Shared subtotal row

Added `calculatorSubtotalRow()` for the repeated structure:

```html
<div class="subtot">
  <span>...</span>
  <span class="pill" id="...">...</span>
</div>
```

Migrated **14/14 literal subtotal rows**. There are now **0 literal `<div class="subtot">` blocks** in calculator pages.

## Render parity

The old clean master and the refactored master were PHP-rendered and compared as normalized DOM trees.

- **26/26 root PHP pages: semantic DOM parity**.
- Hero structure/text/attributes preserved.
- Total structure/text/IDs preserved.
- Subtotal structure/text/IDs preserved.
- Whitespace-only differences are ignored by the DOM comparison.

## Regression status

After the refactor:

- PHP lint: **43/43 PASS**.
- Production JavaScript syntax: **30/30 PASS**.
- Executable test suites: **18/18 PASS**.
- New UI-pattern contract: **84/84 PASS**.
- Runtime PHP renders: **26/26 PASS**.
- Duplicate rendered IDs: **0**.

## Patterns that should remain page-local

### Field / field-grid markup

There are many `.field` / `.field-grid` occurrences, but they are not good candidates for PHP componentization. Labels, controls, help text, nested proof components, dynamic containers and JS hooks vary materially by page. A generic `renderField()` API would become more complex than the markup it replaces.

Recommendation: keep field markup local; keep styling shared in `common.css`.

### Status / message boxes

The project uses `.note`, `.info`, `.warning`, `.success`, `.danger` and related aliases. `common.css` already normalizes their visual language. Several scripts dynamically change these classes at runtime.

Recommendation: do not wrap every message in a PHP component. Migrate toward canonical `.edu-message` classes opportunistically when individual tools are edited, not through a mass replacement.

### Rich result variants

`total`, `big-total`, the 3EA direct total, DΗM.Ω.Σ. total+bar and other result presentations communicate different information and should not be forced into one universal markup API.

## Remaining UI naming debt worth a future pass

### `checkrow` vs `check-row`

The repository still contains two class families for the same broad visual idea:

- `checkrow`: 18 occurrences.
- `check-row`: 41 occurrences.

The family-scoped CSS is not identical, so a blind rename is not recommended.

Safe future migration:

1. introduce/add `edu-check-row` as a canonical base class,
2. keep legacy classes as compatibility aliases,
3. move only genuinely common flex/alignment rules to the base class,
4. preserve page-family overrides,
5. remove aliases only after browser parity tests.

### Guide-page shell

Seven guide pages repeat:

- `app-box edu-modernized`,
- `hero edu-legacy-hero`,
- question/result flow.

A dedicated `guide-layout.php` could centralize the shell and hero. The benefit is moderate rather than urgent because the repeated question markup is intentionally simple and readable. Do not componentize the 50+ question blocks themselves.

### CSS family duplication

The largest remaining presentation debt is now CSS rather than PHP markup. Several page families repeat similar rules for:

- hero metadata chips,
- two-column field grids,
- bordered `.field` cards,
- info/warning message spacing,
- responsive collapse.

Examples include European Schools, Abroad, Digital Tutoring and the SDE family.

This is the best candidate for the next audit: extract a small canonical calculator-family base while retaining family-specific colors, dimensions and exceptions. It has higher visual-regression risk than the Phase 4B PHP refactor, so it should be done with rendered/browser parity checks.

### Local credits

Five calculators still have local `.credits` text in addition to the global footer. Some are contextual tool labels, some duplicate authorship information. This is low-value cleanup and should be reviewed editorially rather than removed automatically.

## Version / deployment

No CSS or JavaScript asset changed in this pass, so **no cache-version bump is required**. The canonical asset version remains **3.20.49**.

Production upload consists only of the changed PHP pages plus `includes/components/calculator-layout.php`.
