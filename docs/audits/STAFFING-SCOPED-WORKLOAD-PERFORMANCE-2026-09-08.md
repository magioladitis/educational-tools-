# School staffing simulator — scoped workload loading (2026-09-08)

## Problem

The staffing simulator previously called `teachingWorkloadModel()` on every explicit calculation. That API intentionally builds the complete regulatory workload catalogue for all supported secondary-school structures, even when the current profile is only a Gymnasium or GEL. The profile layer filtered unrelated structures afterwards, so the result was correct but peak memory/time were unnecessarily high on shared hosting.

## Production path

`ypologismos-didaktikon-anagkon.php` now calls `teachingWorkloadModelForProfile($profile)`.

For the school types currently supported by the UI, the workload layer uses generated school-scoped snapshots:

- `gymnasio`
- `gel`
- `esperino_gymnasio`
- `esperino_gel`

`gymnasio_lt` is a composite profile and therefore loads only `gymnasio + gel`.

The canonical APIs `weeklyTimetableRows()` and `teachingAssignmentsData()` remain unchanged for audits and tools that genuinely need the complete catalogue. Unknown school types fall back safely to the canonical dataset.

## Snapshot maintenance

The canonical regulatory sources remain:

- `includes/weekly-timetable-data.php`
- `includes/teaching-assignments-data.php`

After changing either dataset, rebuild the scoped snapshots with:

```bash
php tests/rebuild-scoped-workload-snapshots.php
```

Then run:

```bash
php tests/staffing-scoped-workload-contract.php
```

The contract compares timetable rows, assignment rows and the final workload model against the canonical full dataset for every scoped school type and for the Gymnasium + Lyceum Classes composite.

## Reference measurement

On the 2026-09-08 development snapshot:

- full workload catalogue: 2,197 instances, ~12 MiB PHP peak, ~0.9 s model build;
- Gymnasium scoped workload: 61 instances, ~4 MiB PHP peak, ~0.02 s;
- Gymnasium + Lyceum Classes: 112 instances, ~4 MiB PHP peak, ~0.01 s.

These figures are environment-dependent; the contract guarantees equivalence of the data, not a fixed performance number.
