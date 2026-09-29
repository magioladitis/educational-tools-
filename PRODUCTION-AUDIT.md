## v3.22.51 — EEP/EVP audit corrections

- Regression verification: all service-timeline contracts PASS; shared/mobile/UI regression chunks PASS; optimizer/performance/staffing/legal-source/source-registry/PHP-lint tail PASS.
- Added `eep-evp-detachment-functional-gaps` with verified 2020–2026 history.
- Corrected level metadata for all three metataksi result destination cards (`to-primary`, `to-secondary`, `to-eep`).
- Kept EEP/EVP services/bodies detachments out of the public recurring timeline: official evidence confirms a historical 2019–2023 process, but no equivalent recurring central call was identified for 2024–2026.
- Added regression coverage for the new event, level semantics, and historical-only services/bodies decision.


## v3.22.50 — EEP/EVP coverage pass

- Προστέθηκαν ξεχωριστές σειρές για εγκύκλιο και αιτήσεις μεταθέσεων ΕΕΠ-ΕΒΠ, αμοιβαίες μεταθέσεις ΕΕΠ-ΕΒΠ, ανακοινώσεις αποσπάσεων ΕΕΠ-ΕΒΠ και κατ’ εξαίρεση αποσπάσεις νεοδιόριστων ΕΕΠ-ΕΒΠ.
- Η κοινή σειρά «Αιτήσεις μετάθεσης εκπαιδευτικών» περιορίστηκε σωστά σε Π.Ε./Δ.Ε.
- Η κάλυψη του φίλτρου ΕΕΠ-ΕΒΠ αυξήθηκε από 9 σε 15 διαδικασίες· σύνολο χρονοδιαγράμματος 39 διαδικασίες.
- Η πιθανή ξεχωριστή σειρά αποσπάσεων ΕΕΠ-ΕΒΠ σε υπηρεσίες/φορείς παραμένει εκτός δημόσιου dataset μέχρι να τεκμηριωθεί ομοιόμορφη συνέχεια μετά το 2023.
- Τα service-timeline contracts, shared hero, mobile/PWA, accessibility, optimizer/parity, performance, staffing, legal-source registry, source registry και PHP lint ολοκληρώθηκαν χωρίς αποτυχία σε διαδοχικά regression chunks.
- Το πραγματικό XLS export επαληθεύτηκε με `artifact_tool`: φύλλα `Χρονολόγιο` (A1:L40) και `Πηγές` (A1:F285), οι νέες σειρές ΕΕΠ-ΕΒΠ είναι παρούσες και ο έλεγχος για formula errors επέστρεψε 0 ευρήματα.

# v3.22.50 — EEP-EVP service-timeline coverage

- Added dedicated EEP-EVP transfer circular/application cycles, mutual-transfer deadline rule, detachment results and newly-appointed exceptional-detachment stages.
- EEP-EVP level-filter coverage: 15 events (previously 9); total timeline events: 39.
- Educator transfer applications no longer incorrectly carry EEP-EVP historical dates.
- Every newly stored date has a Ministry or official education-authority source; services/bodies detachments after 2023 remain intentionally unmodeled pending continuous evidence.

# v3.22.49 — Shared page hero template / mobile info consistency

- Canonical shared hero: `includes/components/page-hero.php`.
- Timeline migrated from ad-hoc `.timeline-hero` markup to `eduPageHero()`.
- Calculator hero API delegates to the same component.
- Mobile disclosure contract: `data-edu-hero` + `data-edu-hero-info`, with resilient legacy/custom hero fallback.
- Goal: no page-specific implementation is required to obtain the standard mobile `i` behaviour.

# v3.22.48 — Level/personnel filters + complete level metadata audit

- Regression: targeted v3.22.47 compatibility + v3.22.48 contract PASS; pre-PWA gate passed through the allocation optimizer section before the command timeout; the complete optimizer/performance/staffing/legal-source/source-registry/PHP-lint tail passed separately with no failures.

- Added independent `Π.Ε. / Δ.Ε. / ΕΕΠ-ΕΒΠ` filtering, composed with the existing process-category filter.
- All 32 service-timeline events now declare explicit canonical `levels`; no event relies on dataset fallback `all`.
- Audit corrected `organic-gaps-circular` to D.E. and normalized educator-only, EEP-EVP-only and cross-personnel processes.
- XLS export now includes the same level/personnel dimension in both `Χρονολόγιο` and `Πηγές`.
- Added `SERVICE-TIMELINE-LEVEL-AUDIT-2026-09-29.md` and regression contract `service-timeline-v32248-contract.py`.

# v3.22.47 — Transfer announcements PE/DE split + level metadata groundwork

- Split educator transfer announcements into independent P.E. and D.E. timeline events with separate histories/sources.
- Added optional `levels` metadata and rendered `data-levels` for clearly level-specific events, preparing a future P.E./D.E./EEP-EVP filter without changing current UI behavior.
- XLS export inherits the split automatically from the timeline dataset.

# v3.22.46 — Mutual-transfer application split + resignation withdrawal horizon

- Date: 2026-09-29
- Split the combined mutual-transfer application deadline into separate P.E. and D.E. timeline rows, with independent histories and source trails.
- Current cycle: P.E. 03/04/2026, D.E. 07/04/2026. The P.E. card also records the Ministry's later 14/04/2026 protocol-submission date without changing the statutory 15-day deadline.
- Restored the operational horizon for resignation-withdrawal: each year displays the latest possible withdrawal date for an application submitted on the final application day, while the note preserves the legal one-month-per-application rule.
- XLS export automatically includes both new mutual-transfer rows and the derived resignation-withdrawal horizon.
- Regression: all service-timeline contracts PASS including new v3.22.46; pre-PWA gate passed through the personnel workload section before the single-command timeout; the entire remaining optimizer/performance/staffing/legal-source/source-registry/PHP-lint tail passed separately with no failures.

# v3.22.45 — Service timeline source completion + XLS export

- Date: 2026-09-29
- Completed the source trail for every stored mutual-transfer result date (P.E. and D.E.). Years with no stored result remain blank rather than inferred.
- Removed the process-count pill from the service-timeline hero.
- Added `Εξαγωγή σε XLS`, backed by `xronodiagramma-ypiresiakon-metavolon-export.php` and a dependency-free XLSX writer.
- Export workbook contains `Χρονολόγιο` (matrix by school year) and `Πηγές` (row-wise source URLs), with frozen panes, filters, wrapping and bounded widths.
- Exported workbook was opened with `artifact_tool`; key ranges were inspected and the formula/error scan returned no spreadsheet errors.
- Regression: service-timeline v3.22.44 compatibility PASS; v3.22.45 export contract PASS; optimizer/performance/staffing PASS; legal-source registry PASS; source-registry audit 0 errors/0 warnings; top-level PHP syntax PASS. The full shell gate exceeded a single command time budget only during the optimizer section and was completed in subsequent chunks with no failures.

## v3.22.44 — Service timeline final UX/content polish

PASS target: remove repeated verification chrome while retaining source transparency; partial historical sourcing remains visible only as a single subtle card-level cue.

# Production / Real-device audit

Use this checklist after each release that changes layout, PWA shell, service worker, or major interactive workflows.

## Automated gate

- `python3 tests/accessibility-production-contract.py`
- `python3 tests/pwa-manifest-contract.py`
- `bash tests/pre-pwa-regression.sh`
- `python3 tests/public-copy-audit-v32241-contract.py` — ελέγχει ότι βασική developer/internal ορολογία δεν επανεμφανίζεται στο δημόσιο UI.

The accessibility contract renders representative PHP pages and checks that visible form controls have an accessible name, the shared skip link is present, Greek document language is declared, focus styling is visible, reduced-motion is respected, and the PWA registration/update policy is intact.

## Android Chrome — production HTTPS

1. Open `ergaleia.php` in a normal tab and confirm the browser tab/icon and theme color.
2. DevTools/Application (desktop remote debugging if available): Manifest must load with 192, 512 and maskable icons; `start_url` must resolve inside scope.
3. Service Worker must be `activated and running`, with scope covering the toolbox root.
4. Install/Add to Home Screen; launch from the icon and confirm standalone display and correct start page.
5. Navigate to Διδακτικές Ανάγκες, Αναθέσεις, Προθεσμίες and one calculator. Confirm back navigation and no stale page after a new deploy.
6. With network temporarily unavailable, static assets may fall back from cache; dynamic PHP/navigation is intentionally not promised offline.

## iPhone Safari

1. Verify favicon / Apple touch icon and Add to Home Screen icon.
2. Launch from Home Screen and verify controls are not obscured by browser safe areas.
3. Check 200% text zoom and portrait/landscape on the main workflows.

## Keyboard / assistive technology

- First Tab exposes “Μετάβαση στο κύριο περιεχόμενο”.
- Focus indicator remains clearly visible on links, buttons, inputs, selects, summaries and tabs.
- Labels announce the purpose of every visible form control.
- Dynamic results use polite status/live regions where appropriate; blocking validation errors use alert semantics.
- Tabs can be reached by keyboard and expose `role=tab`, `aria-selected`, `aria-controls` consistently.
- At 200% zoom there is no two-dimensional page scrolling for ordinary content; intentionally wide data tables may scroll in their own container.
- With Reduce Motion enabled, smooth scrolling and transitions are effectively disabled.

## Release-update smoke test

After deploying a new version, reload once and confirm that `service-worker.js` and `manifest.webmanifest` revalidate. The worker is registered with `updateViaCache: none`; static asset cache names must match `EDU_TOOLS_VERSION`. Never cache PHP/navigation responses in the service worker without a separate explicit design review.

## Mobile UX phase 1 smoke test

1. Σε οδηγό με `data-edu-primary-action`, το sticky CTA δεν εμφανίζεται πριν ο χρήστης αλληλεπιδράσει/κάνει ουσιαστικό scroll και κρύβεται όταν το κανονικό κουμπί είναι ορατό.
2. Με focus σε input/select/textarea το sticky CTA δεν πρέπει να καλύπτει το πληκτρολόγιο.
3. Μετά τον τελικό έλεγχο, το viewport μετακινείται στο αποτέλεσμα· το «Επεξεργασία στοιχείων ↑» επιστρέφει στο τελευταίο πεδίο.
4. Σε ελλιπείς απαντήσεις eligibility, εμφανίζεται κοινή περίληψη validation, `aria-invalid=true` και focus στο πρώτο αναπάντητο πεδίο αντί για scroll στο αποτέλεσμα.
5. Στο mobile hero εμφανίζεται μόνο ο τίτλος και το διακριτικό `i`. Με πάτημα στο `i` εμφανίζονται εισαγωγή/meta/badges, το `aria-expanded` ενημερώνεται σωστά και στο desktop όλο το περιεχόμενο παραμένει μόνιμα ορατό.

## 2026-09-28 — Service timeline EEP-EVP detachment source completion (v3.22.43)

- Completed per-year official-source coverage for `eep-ebp-detachments` across 2020–2026.
- Corrected the 2024 central application window from the stale local-process value `15–17/07/2024` to the official central invitation window `22/04–01/05/2024` (41510/Ε4/19-04-2024).
- Added the previously missing 2020 and 2021 central windows: `19–26/05/2020` and `20–27/05/2021`.
- Added full `historical_sources` and `verified_history_indices` coverage and a regression guard preventing the stale July 2024 value from returning to this card.
- Release/cache version: `3.22.43`.


## v3.22.52 — Newly appointed exceptional detachment results

- Renamed the PE/DE newly-appointed invitation and application rows to explicitly state the exceptional-detachment scope.
- Added separate PE and DE result/name-announcement rows for 2021–2026.
- Kept result dates level-specific; no synthetic common result date is used.
- Added regression coverage for the new rows and their complete historical source trail.

### Regression status v3.22.52
- Service-timeline contracts through v3.22.52: PASS.
- Mobile/shared-hero, PWA/accessibility and layout regression chunks: PASS.
- Allocation/optimizer parity, performance and staffing-action chunks: PASS.
- Legal-source registry, source audit and PHP syntax: PASS.
- XLS export smoke test: PASS (`Χρονολόγιο` + `Πηγές`, separate Π.Ε./Δ.Ε. result rows present).

## v3.22.53 — Service timeline completion audit

- Canonical timeline: 46 processes (Π.Ε. 26 / Δ.Ε. 27 / ΕΕΠ-ΕΒΠ 22).
- Added three source-backed EEP-EVP milestones and one shared post-results five-day rule without inventing an absolute date.
- Filled four previously missing historical values with Ministry-backed evidence.
- Preserved the semantic distinction between decision date (15/06/2020) and Ministry announcement date (16/06/2020) for DE mutual transfers 2020.
- Release/cache version: `3.22.53`.

## v3.22.54 — EEP-EVP transfer-withdrawal history completion

- Completed all seven history slots for `transfer-application-withdrawal-eep-evp` from official Ministry/Diavgeia circulars.
- Preserved the actual historical rule changes: relative KYSSEEP deadline in 2019-20, 05/04/2021 in 2020-21, 08/03/2022 23:59 in 2021-22, then 31/12 15:00 from 2022-23 onward.
- Marked all seven history indices verified and attached one source per year.
- Release/cache version: `3.22.54`.

## v3.22.55 — EEP-EVP detachment-application withdrawal history

- Completed the source-backed history of `eep-evp-detachment-application-withdrawal` for the five years in which the central circular provides a distinct pre-results withdrawal/deactivation deadline: 15/06/2022, 02/06/2023, 22/05/2024, 03/06/2025 and 08/06/2026.
- Kept 2019-20 and 2020-21 intentionally empty: those circulars used service/email submission and do not state an equivalent standalone pre-results application-withdrawal deadline.
- Explicitly separated withdrawal/deactivation of the pending application from withdrawal of an already approved detachment after results.
- Release/cache version: `3.22.55`.
