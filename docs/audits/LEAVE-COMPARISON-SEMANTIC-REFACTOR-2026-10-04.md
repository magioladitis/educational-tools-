# Leave comparison semantic refactor — 2026-10-04

## Goal

Stop deriving legal comparison outcomes from user-facing duration strings. Comparable leave cards now expose structured comparison metadata and the browser computes the comparison after the initial page load.

## Data model

Mapped leave records may include a `comparison` object with:

- `duration.kind` (`fixed`, `maximum`, `range`, `qualitative`, `variable`, `formula`, `parts`, `mixed_period`)
- `duration.unit`
- `duration.base`, `min`, `max`
- `duration.extended_max`
- `duration.variants` / `variant_key`
- `duration.frequency_limit` / `frequency_period`
- optional `scope.key` / `scope.label`

Existing `pay`, `service`, and `proportional` fields remain authoritative for those dimensions.

## Client-side behavior

`includes/leave-comparison-ui.js` computes, without network requests:

- basic duration equality,
- special-case equality,
- frequency equality,
- scope equality,
- pay/service equality,
- proportionality equality,
- an overall human-readable status.

The client also provides filters for substantive differences and equal basic duration.

## Important UX examples

- Marriage: `5 working days` is treated as equal even if explanatory prose differs elsewhere.
- Blood donation: `2 working days` is the basic duration; `up to 6 times/year` is compared separately as frequency.
- Special illness/disability: the basic entitlement and the higher special-case ceiling are separate dimensions.
- Bereavement: duration and covered-relative scope are separate dimensions.

## Regression protection

`tests/leave-comparison-contract.py` verifies that every explicitly mapped pair has structured duration semantics, that the comparison is rendered client-side, that no post-load request is introduced, and that the new comparison dimensions remain available.
