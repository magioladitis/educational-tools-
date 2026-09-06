# Staffing simulator — orange-priority pass (2026-09-06)

Scope: `ypologismos-didaktikon-anagkon.php` and `school_registry_v1` only. No changes to the regulatory assignment tables or timetable rules.

## Changes

- Tab 5 (`Κενά μαθημάτων`) is available after a valid school-profile calculation even when no personnel have been entered.
- Printable report includes the current vacancies table and is synchronized by the live allocation view.
- Vacancy staff status distinguishes personnel that can cover a slot normally from personnel whose only available route is a B-assignment beyond the 10-hour threshold (warning/exception, never a hard block).
- `school_registry_v1` supports an optional Ministry/myschool school code separately from its portable internal registry id.
- The Ministry/myschool code is loaded into Card 1, displayed in the registry preview and print report, and can become the fallback internal registry id when no explicit registry id is supplied.
- Duplicate school identity (same code/id in one imported registry) is rejected client-side.
- No live external lookup was added; the tool remains request-free for CSV import and local registry switching.

## Verification

- PHP lint: 78/78 PASS.
- JavaScript syntax: 47/47 PASS.
- JavaScript regression suites: 10/10 PASS.
- Python contract files: 63/65 PASS; the two failures are the pre-existing `id-normalization-contract.py` and `service-tools-r12-contract.py` baseline issues.
- Staffing vacancy contract: 20/20 PASS.
- School registry contract: 22/22 PASS.
- Print contract: 14/14 PASS.
