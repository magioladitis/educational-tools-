# ASEP public educational service cap — v3.20.14-rc2

Scope: the six ASEP calculators only (1ΓΕ/2ΓΕ, 1ΓΤ/2024, 1ΕΑ/2025, 2ΕΑ/2025, 3ΕΑ/2025, 4ΕΑ/2025).

## Rule locked
- Regular/public educational service: 1 point per month.
- Maximum recognized duration in this field: 120 months.
- The shared `EducationService.regularPublic()` now clamps to 120 months, so values above 120 cannot leak into service month summaries/calculation internals.
- Every relevant input now declares `max="120"` and the UI text explicitly says `έως 120 μήνες`.
- Existing page-side integer sanitizers were updated where needed so typed values are clamped to the HTML max; 3EA/4EA already used max-aware sanitizers.
- `service-calculations.js` is cache-busted as `?v=3.20.14-rc2` in all six pages.

## Out of scope
No SDE, Onaseia, secondment or other non-ASEP calculator was changed.

## Official verification
The 1ΓΤ/2024 official ASEP FEK states educational service up to 120 months, 1 point/month and 120 points maximum. The 2026 1ΓΕ FEK uses the same 120-point service category and public educational service criterion. The 2025 2ΕΑ FEK explicitly states educational service up to 120 months at 1 point/month; 1ΕΑ/2025 is governed by the same statutory 120-month service ceiling.
