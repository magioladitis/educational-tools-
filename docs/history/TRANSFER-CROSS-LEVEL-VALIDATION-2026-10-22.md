# Cross-level transfer validation — 22/10/2026

Base: 3.22.198-VALIDATED-FULL. This is a validation update, not a new functional release.

## New test
`tests/transfer-both-levels-rendered-contract.py` renders both PHP calculators via PHP CLI and checks:
- valid generated HTML shell and `lang=el` / viewport;
- no duplicate IDs in either document;
- all explicit `label for` references point to existing elements;
- key checklist, print, mail-compose, score, MSD elements per education level.

Results: PRIMARY PASS (150 unique IDs), SECONDARY PASS (149 unique IDs).

## Regression suite
84 scripts whose names contain `transfer`: 84/84 pass after separately rerunning 5 tests that timed out during a parallel run with an 8-second limit. These timeouts were not assertion failures. The 5 reruns used a 22-second process limit and all exited successfully.

## Important limitations
- Chromium HTTP loopback navigation returned `ERR_BLOCKED_BY_ADMINISTRATOR`. No interactive browser E2E, layout, actual click-through, or JavaScript runtime behavior in a browser could be verified.
- The CLI uses PHP 8.4.24, not production PHP 7.4. Compatibility with PHP 7.4 remains unverified.
- These tests do not constitute legal validation of the underlying transfer eligibility or documents rules.
- Application logic, scoring and UI runtime files are unchanged; only the new test and this report have been added.
