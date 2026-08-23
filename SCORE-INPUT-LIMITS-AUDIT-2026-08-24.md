# Score input limits audit — 2026-08-24

## Why the previous audit missed these fields

The affected inputs already had HTML `min`/`max` attributes and the calculation layer already clamped the values. The previous audit treated that combination as sufficient protection. Native HTML `max`, however, does not prevent a user from typing an out-of-range value; it only marks the control invalid. Therefore the visible input could remain above the rubric maximum even though the calculation used the capped value.

The missing acceptance criterion was **live input normalization**.

## Fixed fields

### `ypologismos-morion-apospasis-psifiako-frontistirio.php`
- A1. Συγκρότηση σκέψης – λόγου: 0–20
- A2. Επικοινωνιακές δεξιότητες: 0–15
- Γ1. Βαθμολογία βιντεοσκοπημένου μαθήματος: 0–35

All three now call `normalizeBoundedNumber(this)` before `calculate()`. The existing calculation clamps remain in place as a second defensive layer.

### `ypologismos-morion-diefthynton-ypodiefthynton-sde.php`
- Βαθμολογία συνέντευξης Διευθυντή: 0–25

The input now calls `normalizeBoundedScore(this)` before `calculate()`. The shared `SDELeadership` engine continues to cap interview points at 25.

## Protection model

For rubric score fields with an explicit maximum, the required pattern is now:
1. HTML `min` / `max`
2. live normalization of the visible input
3. calculation-engine clamp

## Tests

- Exact normalizer behavior: 8/8 PASS
- Static input + engine contract checks: 11/11 PASS
- SDE engine interview clamp: PASS (`99 -> 25`)
- PHP syntax: 37/37 PASS
- JavaScript syntax: 31/31 PASS

A Chromium integration attempt was made but the container Chromium process stalled, so no browser-pass claim is made for this patch.
