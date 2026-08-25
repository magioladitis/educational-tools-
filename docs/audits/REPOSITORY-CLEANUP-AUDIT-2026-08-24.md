# Repository cleanup audit — 24/08/2026

## Scope

Baseline: Educational Tools `3.20.49`, after the field-ID normalization pass.

Goal: remove repository clutter without changing any production behavior or business rule.

## Result

The canonical master is now split cleanly into:

- root: production PHP pages/hubs only,
- `assets/`: shared public CSS/JS,
- `includes/`: shared PHP components/controllers/calculation engines,
- `tests/`: executable test source only,
- `docs/audits/`: only current architecture/audit documentation.

## Cleanup performed

### 1. Historical artifacts removed from repository root

The previous master had 71 non-PHP files in the root, including:

- old `*-AUDIT*.md` reports,
- old `*-TEST-RESULTS*.txt` outputs,
- old `FILES-TO-UPLOAD-*.txt` manifests,
- historical change/result files,
- one CSV audit inventory.

These no longer live in the canonical root. The current ID normalization audit was retained under `docs/audits/`.

### 2. Generated render snapshots removed

Removed 12 generated files from `tests/rendered/`:

- 6 HTML render snapshots,
- 6 corresponding `.err` files.

They are build/test artifacts, not source files.

### 3. Architecture test made self-contained

`tests/service-social-architecture.py` previously depended on the stored render snapshots. It now runs `php <page>` itself and validates the generated HTML in memory.

This means the test suite can run from a clean checkout/master without any pre-generated files.

### 4. Production dead-file audit

Production dependency audit covered all files under `assets/` and `includes/`.

Result:

- 48 production shared files checked,
- 48/48 referenced,
- 0 unreferenced production modules/components,
- 0 stale versioned/backup/legacy production files found.

No production JS/PHP/CSS file was removed.

### 5. Local-link dependency audit

Rendered all 26 root PHP pages and checked local `.php`, `.js`, and `.css` references.

Result:

- 263 local rendered references checked,
- 0 missing targets.

### 6. Duplicate-helper review

Exact duplicate private helper bodies were scanned across production PHP/JS.

The remaining duplicates are small, scoped utilities such as numeric coercion, `idOf()`, `checked()`, tiny render/reset wrappers, or one-line adapters to shared controllers. They are not independent business-rule engines.

Recommendation: **do not centralize these micro-helpers merely to reduce line count**. Moving them into a global utility layer would add coupling without meaningful reduction in business-rule duplication.

The important shared logic is already centralized in the dedicated engines/controllers.

## Before / after

| Metric | Before | Clean master |
|---|---:|---:|
| Total files | 175 | 92 |
| Root files | 97 | 26 |
| Root non-PHP artifacts | 71 | 0 |
| Stored rendered test artifacts | 12 | 0 |
| Executable test suites | 17 | 17 |
| Production shared files (`assets/` + `includes/`) | 48 | 48 |
| Approx. unpacked size | 1.66 MB | 1.23 MB |

83 files were removed from the canonical master net. The removed historical material is preserved separately in the audit archive.

## Production parity

All 74 production files (root PHP + `assets/` + `includes/`) were SHA-256 compared before and after cleanup.

**74/74 are byte-identical.**

Therefore:

- no scoring rule changed,
- no UI behavior changed,
- no field ID changed in this repository-cleanup pass,
- no asset cache bump is required,
- production version remains **3.20.49**.

## Regression after cleanup

- 17/17 executable test suites PASS,
- 43/43 PHP lint PASS,
- 30/30 production JavaScript syntax PASS,
- 26/26 root PHP runtime renders PASS,
- 0 runtime PHP stderr in the render sweep.

## Server impact

There is **no required production upload** for this pass because the production files are byte-identical to `3.20.49`.

If historical audit/test/render files were ever uploaded to the web server, they may safely be deleted there. Normal small deployment packages used in the project did not require these files.
