# Training-proof component audit — v3.20.11-rc1

## Scope
This change centralizes only the repeated HTML presentation of the seminar-certificate proof widget.
It does **not** centralize scoring, eligibility, hour thresholds, duration thresholds, specialties, or programme rules.

## Pages migrated
1. `ypologismos-morion.php`
2. `ypologismos-morion-1gt-2024.php`
3. `ypologismos-morion-1ea-2025.php`
4. `ypologismos-morion-2ea-2025.php`
5. `ypologismos-morion-3ea-2025.php`
6. `ypologismos-morion-4ea-2025.php`

Shared renderer:
- `includes/components/training-proof.php`

## Explicit safety boundary
The component itself contains no `300`, `400`, `7 μην`, scoring points, EAE identifiers, or calculator-specific rules.
Each page explicitly passes its DOM contract IDs and its own legal/explanatory copy.

### General / ASEP training contexts
- `ypologismos-morion.php`: general ≥300h / ≥7 months training logic remains on the page.
- `ypologismos-morion-1gt-2024.php`: page-specific TE training logic remains on the page.
- `ypologismos-morion-1ea-2025.php`: page-specific training logic remains on the page.
- `ypologismos-morion-2ea-2025.php`: page-specific training logic remains on the page.

### Special Education guard
The EAE 400-hour criteria are **not moved into the shared component**.

- 3EA/2025 keeps `seminar400` as a distinct EAE auxiliary-list criterion and keeps the existing activation rule: the proof is active if either the general training qualification or the EAE 400h seminar is selected.
- 4EA/2025 keeps `auxSeminar400` as a distinct EAE auxiliary-list criterion and keeps the existing activation rule: the proof is active if either the general training qualification or the EAE 400h seminar is selected.

The component receives only a non-behavioral `data-training-context` string so tests can audit the intended context.

## Deliberately excluded
- `language-proof`: not centralized because language rules differ materially between calls/calculators.
- `ypologismos-morion-mitroo-sde.php` → `educatorTrainingProof`: stays separate because it is a different proof/warning with SDE-specific evidence requirements.

## What did not change
- Inline JavaScript in all six migrated pages: byte-for-byte identical to v3.20.10.
- `assets/common.css`: byte-for-byte identical to v3.20.10.
- `assets/common.js`: byte-for-byte identical to v3.20.10.
- Calculation modules: unchanged.
- User-visible rendered text and training-proof DOM (except the new audit-only `data-training-context` attribute): semantically identical.
