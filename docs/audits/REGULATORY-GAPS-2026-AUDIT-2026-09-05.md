# Regulatory gaps 2026 — audit checkpoint

Current server-side timetable/assignment cross-audit preserves **26 regulatory-gap rows / 32 grade instances** as deliberate hard stops. These rows are not converted to assignments by inference.

Breakdown:

- EN.Ε.Ε.ΓΥ.-Λ.: 17 rows / 17 grade instances.
- Music Gymnasium: 2 rows / 4 grade instances.
- Music Lyceum: 6 rows / 8 grade instances.
- Prototype Ecclesiastical Gymnasium second foreign language: 1 row / 3 grade instances.

The Ecclesiastical Gymnasium language row keeps French/German known branches separate from the unresolved Russian/Arabic/Turkish branches and uses the inference guard `no_unpublished_language_specialty_inference`. Music-school and EN.Ε.Ε.ΓΥ.-Λ. gaps retain their existing source pairs and guards. Public timetable payloads strip the internal `assignment_*` audit metadata.

This file documents the state protected by `tests/regulatory-gaps-2026-contract.py`; the executable datasets remain the source of truth.
