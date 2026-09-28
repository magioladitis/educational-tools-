# Production / Real-device audit

Use this checklist after each release that changes layout, PWA shell, service worker, or major interactive workflows.

## Automated gate

- `python3 tests/accessibility-production-contract.py`
- `python3 tests/pwa-manifest-contract.py`
- `bash tests/pre-pwa-regression.sh`
- `python3 tests/public-copy-audit-v32241-contract.py` — ελέγχει ότι βασική developer/internal ορολογία δεν επανεμφανίζεται στο δημόσιο UI.

The accessibility contract renders representative PHP pages and checks that visible form controls have an accessible name, the shared skip link is present, Greek document language is declared, focus styling is visible, reduced-motion is respected, and the PWA registration/update policy is intact.

## Android Chrome — production HTTPS

1. Open `ergaleia.php` in a normal tab and confirm the browser tab/icon and theme color.
2. DevTools/Application (desktop remote debugging if available): Manifest must load with 192, 512 and maskable icons; `start_url` must resolve inside scope.
3. Service Worker must be `activated and running`, with scope covering the toolbox root.
4. Install/Add to Home Screen; launch from the icon and confirm standalone display and correct start page.
5. Navigate to Διδακτικές Ανάγκες, Αναθέσεις, Προθεσμίες and one calculator. Confirm back navigation and no stale page after a new deploy.
6. With network temporarily unavailable, static assets may fall back from cache; dynamic PHP/navigation is intentionally not promised offline.

## iPhone Safari

1. Verify favicon / Apple touch icon and Add to Home Screen icon.
2. Launch from Home Screen and verify controls are not obscured by browser safe areas.
3. Check 200% text zoom and portrait/landscape on the main workflows.

## Keyboard / assistive technology

- First Tab exposes “Μετάβαση στο κύριο περιεχόμενο”.
- Focus indicator remains clearly visible on links, buttons, inputs, selects, summaries and tabs.
- Labels announce the purpose of every visible form control.
- Dynamic results use polite status/live regions where appropriate; blocking validation errors use alert semantics.
- Tabs can be reached by keyboard and expose `role=tab`, `aria-selected`, `aria-controls` consistently.
- At 200% zoom there is no two-dimensional page scrolling for ordinary content; intentionally wide data tables may scroll in their own container.
- With Reduce Motion enabled, smooth scrolling and transitions are effectively disabled.

## Release-update smoke test

After deploying a new version, reload once and confirm that `service-worker.js` and `manifest.webmanifest` revalidate. The worker is registered with `updateViaCache: none`; static asset cache names must match `EDU_TOOLS_VERSION`. Never cache PHP/navigation responses in the service worker without a separate explicit design review.

## Mobile UX phase 1 smoke test

1. Σε οδηγό με `data-edu-primary-action`, το sticky CTA δεν εμφανίζεται πριν ο χρήστης αλληλεπιδράσει/κάνει ουσιαστικό scroll και κρύβεται όταν το κανονικό κουμπί είναι ορατό.
2. Με focus σε input/select/textarea το sticky CTA δεν πρέπει να καλύπτει το πληκτρολόγιο.
3. Μετά τον τελικό έλεγχο, το viewport μετακινείται στο αποτέλεσμα· το «Επεξεργασία στοιχείων ↑» επιστρέφει στο τελευταίο πεδίο.
4. Σε ελλιπείς απαντήσεις eligibility, εμφανίζεται κοινή περίληψη validation, `aria-invalid=true` και focus στο πρώτο αναπάντητο πεδίο αντί για scroll στο αποτέλεσμα.
5. Στο mobile hero εμφανίζεται μόνο ο τίτλος και το διακριτικό `i`. Με πάτημα στο `i` εμφανίζονται εισαγωγή/meta/badges, το `aria-expanded` ενημερώνεται σωστά και στο desktop όλο το περιεχόμενο παραμένει μόνιμα ορατό.

## 2026-09-28 — Service timeline EEP-EVP detachment source completion (v3.22.43)

- Completed per-year official-source coverage for `eep-ebp-detachments` across 2020–2026.
- Corrected the 2024 central application window from the stale local-process value `15–17/07/2024` to the official central invitation window `22/04–01/05/2024` (41510/Ε4/19-04-2024).
- Added the previously missing 2020 and 2021 central windows: `19–26/05/2020` and `20–27/05/2021`.
- Added full `historical_sources` and `verified_history_indices` coverage and a regression guard preventing the stale July 2024 value from returning to this card.
- Release/cache version: `3.22.43`.
