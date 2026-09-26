# PWA manifest foundation — 2026-09-13

First additive PWA step on top of the clean pre-PWA baseline.

## Added

- `manifest.webmanifest` at the application root.
- Stable app identity (`id: ./`) and Greek app metadata.
- `start_url: ./ergaleia.php` with `scope: ./`.
- `display: standalone`, without forcing an orientation.
- Theme/background colors aligned with `assets/common.css`.
- Manifest discovery link in all 35 public PHP pages with a `<head>` and in the root `index.html` redirect page.
- `tests/pwa-manifest-contract.py`, included in `tests/pre-pwa-regression.sh`.

## Deliberately not added yet

- App icons / maskable icons.
- Service worker or caching.
- Offline fallback.
- Install prompt UI.

The manifest intentionally contains no `icons` array until the real files exist, so there are no broken PWA resources.
## Update — 2026-09-26

The icon step is now complete in v3.22.4: the manifest references 192×192 and 512×512 PNG app icons plus a dedicated 512×512 maskable icon. The remaining deferred PWA work is service-worker/offline behavior and any optional custom install-prompt UI.
