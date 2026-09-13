# Mobile-browser usability audit — 2026-09-13

Scope: browser use on narrow phones, with the practical target widths 360px, 390px and 430px. No service worker, caching policy or native wrapper was introduced in this pass.

## Changes

- Mobile tools directory cards no longer inherit the desktop minimum height; spacing and typography are tightened without hiding descriptions.
- Directory filters use a horizontal touch-scroll strip on phones instead of wrapping into several rows.
- Directory filter chips and hero actions retain at least 44px touch height.
- Staffing workflow tabs use a horizontal touch-scroll strip below 760px and retain 44px touch height.
- Shared checkbox/check-row and segmented-choice controls expose larger phone touch areas.
- Existing safeguards remain: 16px phone form controls, controlled horizontal table scrolling, long-label wrapping, dynamic-viewport help panel, safe-area-aware back-to-top where common.js is loaded.
- No `overflow-x:hidden` was introduced; wide tables remain discoverable instead of being clipped.

## Regression evidence

- `tests/mobile-browser-usability-contract.py`: 86/86 PASS.
- `tests/pre-pwa-regression.sh`: PASS.
- Full executable test inventory (`.py`, `.js`, `.php`) in an isolated copy: 177/177 PASS.
- Production PHP syntax: 107/107 PASS.
- JavaScript syntax under `assets/` + `includes/`: 76/76 PASS.
- PHP/JS separation contract: 6/6 PASS; the sole inline script payload remains `application/json` runtime data in `ypologismos-didaktikon-anagkon.php`.

## Architecture note

This remains a browser-first release. The manifest foundation is present, but installability features do not change the mobile browser workflow. Service-worker caching remains intentionally absent at this checkpoint.
