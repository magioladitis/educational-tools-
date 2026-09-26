# Production / Real-device audit

Use this checklist after each release that changes layout, PWA shell, service worker, or major interactive workflows.

## Automated gate

- `python3 tests/accessibility-production-contract.py`
- `python3 tests/pwa-manifest-contract.py`
- `bash tests/pre-pwa-regression.sh`

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
