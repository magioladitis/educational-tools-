# European Schools calculation-module audit

The uploaded PHP references `includes/european-schools-calculations.js` and its UI uses these exported symbols:

- `EuropeanSchools.HOST_LANGUAGES`
- `EuropeanSchools.LANGUAGE_LABELS`
- `EuropeanSchools.OTHER_EU_LANGUAGES`
- `EuropeanSchools.WORKING_LANGUAGES`
- `EuropeanSchools.POSITIONS_2026`
- `EuropeanSchools.calculate`

The unversioned Library copy of `european-schools-calculations.js` does **not** export the first four language collections, and when paired with the uploaded PHP it raises runtime errors. This same mismatch occurs with the original uploaded PHP, so it is not caused by the CSS migration.

The frozen/versioned `european-schools-calculations-v3.6.js` does export those symbols and the page passes the browser interaction smoke test with that version.

**Safety decision:** this CSS batch does not deploy or replace the calculation module. The module-version mismatch is documented separately so it can be resolved through the calculation-module provenance/parity workflow rather than silently changing business logic during a CSS-only cleanup.
