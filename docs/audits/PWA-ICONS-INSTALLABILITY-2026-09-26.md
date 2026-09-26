# PWA icons & installability — 2026-09-26

Release: v3.22.4

## Added

- `assets/icons/icon-192.png` — 192×192 PNG, normal app icon.
- `assets/icons/icon-512.png` — 512×512 PNG, normal app icon.
- `assets/icons/icon-maskable-512.png` — 512×512 full-bleed PNG for Android adaptive masks.
- `assets/icons/icon-source.svg` — editable/source artwork retained for future exports.
- Manifest `icons` entries for the required 192×192 and 512×512 sizes and the dedicated maskable 512×512 variant.
- Manifest app shortcuts for Διδακτικές Ανάγκες, Μισθοδοσία and Προθεσμίες.

## Regression protection

`tests/pwa-manifest-contract.py` now verifies that:

- the manifest contains normal 192×192 and 512×512 PNG icons;
- a 512×512 maskable PNG is declared;
- every required icon file exists;
- the actual PNG dimensions match the manifest declarations;
- local shortcut targets exist;
- every public PHP page and the root redirect continue to discover the manifest.

## Deployment note

Browser install promotion still depends on the deployed site being served in a secure context (HTTPS) and on browser/user-engagement rules. This repository change covers the manifest/icon side; deployment HTTPS must be validated on the live host.
