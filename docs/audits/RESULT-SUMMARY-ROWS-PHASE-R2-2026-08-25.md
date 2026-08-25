# Result Box Phase R2 — Summary rows & vertical rhythm

Date: 25/08/2026  
Baseline: 3.20.52  
Release: 3.20.53

## Scope

Phase R2 applies only to result-box **Variant A (standard aggregate)** and **Variant B (fixed-cap)** summary rows. It is presentation-only.

No scoring, eligibility, priority, cap, legal text, calculation timing, field IDs, or JS hooks are changed.

Affected visual families:

- Variant A: `ypologismos-morion.php`, 1EA, 1GT, 2EA, 3EA, 4EA, 5EA, Sivitanidios SAEK.
- Variant B: DIM.OS detachment, SDE detachment, Digital Tutoring detachment.

Status/warning/disclaimer semantics are explicitly deferred to Phase R3.

## Canonical row grammar

New shared result-box rhythm tokens in `assets/common.css`:

- `--edu-result-row-gap: 12px`
- `--edu-result-row-pad-y: 9px`
- `--edu-result-row-separator: #edf1f5`
- `--edu-result-section-gap: 14px`

Canonical Variant A/B summary row:

- flex row;
- label left / value right;
- 12px gap;
- 9px vertical padding;
- 14px font size;
- 1px top separator `#edf1f5`;
- right-aligned tabular numeric value.

Action sections use the shared 14px result-section gap where this phase applies.

## 3EA cleanup

3EA/2025 was the last standard aggregate calculator that rendered its three score categories in a dedicated `<table class="table">`.

It now uses the existing shared `calculatorResultRow()` primitive for:

- `resAcademic`
- `resService`
- `resSocial`

The obsolete 3EA-only result-table CSS was removed.

This is an intentional visual normalization. The three IDs and their JS update hooks are unchanged.

## Intentional visual differences

### Already-standard Variant A pages

The seven Variant A pages already using `.edu-calc-standard` retain the same effective row dimensions used before R2:

- gap 12px;
- padding 9px 0;
- font 14px;
- right/tabular value;
- action gap 14px.

The only source-level change is that these values are now expressed through the shared result rhythm tokens. The canonical separator keeps the existing standard value `#edf1f5`.

### 3EA/2025

Old: table cells, `8px 4px`, separator `#edf0f4`.  
New: canonical result rows, `9px 0`, 12px flex gap, separator `#edf1f5`.

### DIM.OS detachment

Old row: gap 10px, padding 9px, separator `#eef1f6`, font `.96rem`; actions margin 12px.  
New row: gap 12px, padding 9px, separator `#edf1f5`, font 14px; actions margin 14px.

### SDE detachment / Digital Tutoring detachment

Gap, padding and font size were already canonical. Only the separator changes from `#edf0f4` to `#edf1f5`; action gap remains 14px.

## Architecture/test cleanup

`tests/layout-phase4a-contract.py` previously required exactly 77 static `calculatorResultRow()` calls. R2 correctly adds three more shared calls in 3EA, so the test was made architecture-safe: it now requires at least the established baseline and still requires zero literal static result-row blocks.

New test:

- `tests/result-summary-r2-contract.py`

It verifies the shared rhythm tokens, Variant A/B selector coverage, 3EA table removal, preservation of the three 3EA result IDs, and DIM.OS action rhythm.

## Validation

- PHP lint: **43/43 PASS**
- production JS syntax: **30/30 PASS**
- executable test suites: **23/23 PASS**
- R2 contract: **20/20 PASS**
- PHP runtime renders: **26/26 PASS**
- duplicate rendered IDs: **0**
- affected-page ID parity before/after: **11/11 pages, 594/594 IDs preserved**
- result-row count in 3EA: **0 → 3**, intentionally

A Chromium computed-style comparison was attempted, but the current container policy blocks both local HTTP and `file://` browser navigation. Therefore no browser computed-style parity claim is made for this phase. Instead, rendered DOM/ID parity and direct CSS declaration comparison were used.

## Deferred to R3

Do not change yet:

- `priority`, `status`, `status-box`, `eligibility-box` semantics;
- info/warning/disclaimer color meaning;
- ordering of complex 3EA eligibility/status blocks;
- action-mode decisions (`live` vs `manual`).

These require semantic review rather than mechanical CSS normalization.
