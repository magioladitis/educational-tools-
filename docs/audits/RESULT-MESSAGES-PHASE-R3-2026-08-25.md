# Result Box Phase R3 — Semantic messages

Date: 25/08/2026  
Baseline: Educational Tools v3.20.53 (Phase R2)  
Result: v3.20.54

## Scope

Presentation-only normalization of messages inside Variant A/B result sidebars. No scoring, eligibility, cap, ID, legal-text, or calculation-timing rules were changed.

## Canonical semantics

A new shared `calculatorResultMessage()` presentation helper was added to `includes/components/calculator-layout.php`.

Supported result-message variants:

- `status` — neutral current state, missing selection, pending input;
- `success` — confirmed positive state, eligibility, priority, or passed threshold;
- `warning` — incomplete requirement, failed threshold, missing evidence, or condition needing attention;
- `disclaimer` — explanatory/legal limitation or non-actionable informational context.

The helper reuses the existing `.edu-message` design system. A new neutral `.edu-message--status` style was added; success/warning/disclaimer reuse existing design tokens.

## Pages migrated

Primary result-sidebars were normalized in:

- `ypologismos-morion.php`
- `ypologismos-morion-1ea-2025.php`
- `ypologismos-morion-1gt-2024.php`
- `ypologismos-morion-2ea-2025.php`
- `ypologismos-morion-3ea-2025.php`
- `ypologismos-morion-4ea-2025.php`
- `ypologismos-morion-5ea-2022.php`
- `ypologismos-morion-sivitanidios-saek.php`
- `ypologismos-morion-apospasis-dimos.php`
- `ypologismos-morion-apospasis-sde.php`
- `ypologismos-morion-apospasis-psifiako-frontistirio.php`

Dynamic messages in SDE secondment and Digital Tutoring were mapped to the same semantic classes. Red/danger presentation was intentionally collapsed into the canonical `warning` role inside result sidebars, because these messages communicate a failed/incomplete condition rather than an application-level fatal error.

## Deliberately not migrated

Messages outside the result sidebar remain page/domain-specific in this phase. Examples include form validation messages, evidence/proof components, and eligibility panels in the main form. They may use the existing `.info`, `.warning`, `.success`, `.danger`, `.priority`, etc. classes because R3 is scoped specifically to result-box semantics.

## Cleanup

Two CSS rules became unused after the migration and were removed:

- `.priority.special`
- the 3EA-specific `.eligibility-box` rules

The Digital Tutoring `statusBox` wrapper margin was reset to avoid double vertical spacing now that each semantic message owns its result-section gap.

## Test debt fixed

- `css-public-api-contract.py` now accepts a subset of documented public API classes as unused, rather than requiring every public class to be unused.
- `result-summary-r2-contract.py` no longer hard-codes release `3.20.53`.
- New `result-message-r3-contract.py` verifies the semantic helper, variants, page migrations, and dynamic message families.

## Verification

- 24/24 executable test suites PASS
- 43/43 PHP lint PASS
- 30/30 production JavaScript syntax PASS
- R3 semantic contract: 35/35 PASS
- ID normalization contract: 239/239 PASS
- decimal-format contract: 6/6 PASS
- 11/11 affected pages preserve static rendered text
- 759/759 rendered IDs preserve exact identity and order versus v3.20.53
- 0 scoring/business-rule changes

## Intentional visual change

Unlike R1/R2, full computed-style parity is not an acceptance target for R3: the purpose of the phase is to make message color and treatment correspond to meaning. The page text, IDs, and calculation behavior remain unchanged.
