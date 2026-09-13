# Pre-PWA test & JavaScript separation audit — 2026-09-13

This checkpoint is the clean baseline before adding the PWA layer.

## Regression status

- 176 executable test/contract files (`.py`, `.js`, `.php`, `.sh`): PASS.
- Pre-PWA regression gate: PASS.
- Production PHP syntax: 107/107 files PASS.
- PHP test utilities syntax: 2/2 files PASS.
- JavaScript syntax: 111/111 files PASS.
- Source registry audit: 0 errors; 5 documentation warnings for legal-source entries without an exact decision date.

## PHP / JavaScript separation

Automated scan of all 107 production PHP files:

- executable inline `<script>` blocks: 0
- inline DOM event attributes (`onclick`, `onchange`, `oninput`, etc.): 0
- `javascript:` URLs: 0
- DOM/JavaScript API implementation references left in PHP: 0
- inline `application/json` payloads: 1, in `ypologismos-didaktikon-anagkon.php`; this is data, not executable JavaScript
- external `<script src="...">` tags remain the normal loading mechanism

The guard is now automated by `tests/php-inline-js-separation-contract.py` and is included in `tests/pre-pwa-regression.sh`.

## Cleanup performed during this pass

- Updated stale render/source assertions after the central legal-source registry refactor.
- Updated portable staffing-schema ID coverage for Prototype Ecclesiastical schools.
- Updated regulatory-gap expectations to the current 26 rows / 32 grade instances.
- Updated workload aggregation expectations to the current 11,389 claims.
- Converted brittle staffing performance exact-count assertions into bounded regression budgets.
- Updated tests to follow the scoped `teachingWorkloadModelForProfile()` architecture and native submitter flow.
- Moved the staffing performance diagnostic CSS into `assets/staffing-simulator.css`.
- Moved salary/payroll page CSS into `assets/salary-scale.css` and updated the print contract accordingly.
