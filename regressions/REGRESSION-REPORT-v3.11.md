# Regression report — ypologismos-morion.php v3.11

The v3.11 change is a presentation-only refactor of the central 1ΓΕ/2026 & 2ΓΕ/2026 calculator.

## Safety checks

- Kept all 34 HTML IDs unchanged.
- Kept all `onclick` and `oninput` handlers unchanged.
- Kept the calculator's inline JavaScript byte-for-byte identical to v3.10.
- Kept the same calculation modules:
  - `includes/academic-calculations.js`
  - `includes/service-calculations.js`
  - `includes/social-calculations.js`
- Calculator inline JS SHA-256: `196f653c0517186e534702cc2879782e34a88562979e2365aa8851a4a5784631` in both v3.10 and v3.11.
- Preserved a rollback copy at `backups/ypologismos-morion-v3.10.php.bak`.

## Golden regression scenarios

All passed:

1. Basic degree 7.50 → 18.75 academic points.
2. Rich academic profile → 115 points.
3. Maximum academic profile → 120 points.
4. ΠΕ06 language exclusion: English excluded, French C1 counted → 5 language points.
5. ΠΕ86 computer exclusion → 0 computer points.
6. Service raw 149 → cap applied at 120.
7. Three-month contract annual caps: regular 2020 → 10, difficult 2021 → 20.
8. Social criteria: 2 children + 67% highest eligible disability → 32.8 points.

## UI-only changes

- Reference layout aligned with `ypologismos-morion-1gt-2024.php`.
- Hero at top.
- Six separate cards for the input sections.
- Sticky result card on the right on desktop.
- Copy/reset actions grouped in the result card.
- Detailed breakdown remains below the form.
- Sources/legal basis remains inside the same tool container.
- Mobile layout collapses to one column.
