# R4B — Utility Action Hierarchy — 25/08/2026

## Scope
Presentation-only normalization of result-box utility actions after R4A.

## Decision
The following actions are utilities, not primary calculation CTAs:
- Αντιγραφή / Αντιγραφή αποτελέσματος / Αντιγραφή σύνοψης
- Μηδενισμός / Καθαρισμός
- Εκτύπωση
- Φόρτωση παραδείγματος

All result-box copy actions now explicitly use the existing `secondary` presentation class. Reset/print/sample actions were already secondary and remain so.

No global default in `calculatorActions()` was changed, because hybrid/manual-value actions must retain their own hierarchy.

## Pages normalized
1. `ypologismos-morion.php`
2. `ypologismos-morion-1ea-2025.php`
3. `ypologismos-morion-1gt-2024.php`
4. `ypologismos-morion-2ea-2025.php`
5. `ypologismos-morion-3ea-2025.php`
6. `ypologismos-morion-4ea-2025.php`
7. `ypologismos-morion-5ea-2022.php`
8. `ypologismos-morion-apospasis-dimos.php`
9. `ypologismos-morion-apospasis-evropaika-scholeia.php`
10. `ypologismos-morion-apospasis-exoteriko.php`
11. `ypologismos-morion-diefthynton-ypodiefthynton-sde.php`
12. `ypologismos-morion-mitroo-sde.php`
13. `ypologismos-morion-sivitanidios-saek.php`

## Intentionally unchanged
- `ypologismos-morion-apospasis.php`: `Υπολογισμός μορίων` remains `primary-btn`.
- `ypologismos-morion-onaseia.php`: `Υπολογισμός μορίων` remains present for the hybrid flow.
- R4A removals in Ψηφιακό Φροντιστήριο and Απόσπαση ΣΔΕ remain unchanged.

## Regression protection
Added `tests/result-actions-r4b-contract.py`.

Contract result: **31/31 PASS**.

## Verification
- PHP lint: **43/43 PASS**
- production JS syntax: **30/30 PASS**
- executable test suites: **26/26 PASS**
- affected-page ID parity: **920/920 IDs identical**
- rendered HTML parity excluding `class` attributes: **13/13 PASS**

## Version / cache
No CSS or JS asset changed. `EDU_TOOLS_VERSION` remains **3.20.54**; no cache bump is required.
